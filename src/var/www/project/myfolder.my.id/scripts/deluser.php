<?php
include(__DIR__.'/config.php');
date_default_timezone_set($default_timezone);
$database = $installation_directory.'/.htpasswd';
$directory_database = dirname($database);
$mode = 'default';
if (!is_writable($directory_database)) {
    die('Directory of database cannot be writing.');
}
if (!is_writable($database)) {
    die('Database cannot be writing.');
}
// Reference: https://stackoverflow.com/a/36884059
function WhileLocked($pathname, callable $function, $proj = ' ') {
    // create a semaphore for a given pathname and optional project id
    $semaphore = sem_get(ftok($pathname, $proj)); // see ftok for details
    sem_acquire($semaphore);
    try {
        // capture result
        $result = call_user_func($function);
    } catch (Exception $e) {
        // release lock and pass on all errors
        sem_release($semaphore);
        throw $e;
    }
    // also release lock if all is good
    sem_release($semaphore);
    return $result;
}
if (isset($_POST['username'])) {
    $username = $_POST['username'];
    $result = WhileLocked($database, function () use ($database, $username) {
        // Reference: https://stackoverflow.com/a/3004080
        $reading = fopen($database, 'r');
        $writing = fopen($database.'.tmp', 'w');
        $replaced = false;
        while (!feof($reading)) {
            $line = fgets($reading);
            $line = preg_replace_callback(
                '/^'.preg_quote($username.':').'(.+)/',
                function ($matches) use (&$replaced) {
                    $replaced = true;
                    return '';
                },
                $line
            );
            // $line is still containes \n, clear it with rtrim.
            if (rtrim($line) != '') {
                fputs($writing, $line);
            }
        }
        fclose($reading); fclose($writing);
        // might as well not overwrite the file if we didn't replace anything
        if ($replaced) {
            rename($database.'.tmp', $database);
            return true;
        } else {
            unlink($database.'.tmp');
            return false;
        }
    });
    if ($result) {
        $mode = 'success';
        $olddir = $installation_directory.'/storage/'.$username;
        if (is_link($olddir.'/scripts/tinyfilemanager.php')) {
            unlink($olddir.'/scripts/tinyfilemanager.php');
        }
        if (is_link($olddir.'/scripts/translation.json')) {
            unlink($olddir.'/scripts/translation.json');
        }
        if (is_file($olddir.'/scripts/config.php')) {
            unlink($olddir.'/scripts/config.php');
        }
        if (is_dir($olddir.'/scripts')) {
            rmdir($olddir.'/scripts');
        }
        $newdir = $installation_directory.'/storage/'.$username.'_'.date('Ymd_His');
        if (is_dir($olddir)) {
            rename($olddir, $newdir);
        }
    }
    else {
        $mode = 'failed';
    }
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
</head>
<body>
<?php switch ($mode): ?>
<?php case 'success': ?>
<script>
(function () {
    alert('User removed successfully.');
    window.location = "<?php echo 'https://admin.'.$domain.'/'; ?>";
})()
</script>
<?php case 'failed': ?>
<script>
(function () {
    alert('User does not exists.');
    window.location = "<?php echo 'https://admin.'.$domain.'/scripts/deluser.php'; ?>";
})()
</script>
<?php default: ?>
<script>
// Reference: https://stackoverflow.com/a/133997
function post(path, params, method='post') {
  const form = document.createElement('form');
  form.method = method;
  form.action = path;
  for (const key in params) {
    if (params.hasOwnProperty(key)) {
      const hiddenField = document.createElement('input');
      hiddenField.type = 'hidden';
      hiddenField.name = key;
      hiddenField.value = params[key];
      form.appendChild(hiddenField);
    }
  }
  document.body.appendChild(form);
  form.submit();
}
(function () {
    do {
        newname = prompt("Enter a user name to remove:");
        if (newname === null) {
            window.location = "<?php echo 'https://admin.'.$domain.'/'; ?>";
            break;
        }
        if (!confirm("Are you sure, delete user "+newname+" ?")) {
            window.location = "<?php echo 'https://admin.'.$domain.'/'; ?>";
            break;
        }
        post('/scripts/deluser.php', {'username': newname});
    }
    while (false);
})()
</script>
<?php endswitch ?>
</body>
</html>
