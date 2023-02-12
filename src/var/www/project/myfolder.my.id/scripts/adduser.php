<?php
include(__DIR__.'/config.php');
date_default_timezone_set($default_timezone);
$database = '/var/www/project/'.$domain.'/.htpasswd';
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
if (isset($_POST['password'])) {
    $password = $_POST['password'];
    $username = $_POST['username'];
    $result = WhileLocked($database, function () use ($database, $username, $password) {
        // Reference: https://stackoverflow.com/a/3004080
        $reading = fopen($database, 'r');
        $found = false;
        while (!feof($reading)) {
            $line = fgets($reading);
            if (preg_match('/^'.preg_quote($username.':').'(.+)/', $line, $matches)) {
                $found = true;
                break;
            }
        }
        fclose($reading);
        if (!$found) {
            $encrypted_password = password_hash($password, PASSWORD_BCRYPT);
            $content = $username.':'.$encrypted_password;
            file_put_contents($database, $content.PHP_EOL, FILE_APPEND);
            return true;
        }
    });
    if ($result) {
        $mode = 'success';
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
    alert('User addedd successfully.');
    window.location = "<?php echo 'https://admin.'.$domain.'/'; ?>";
})()
</script>
<?php case 'failed': ?>
<script>
(function () {
    alert('User has been exists.');
    window.location = "<?php echo 'https://admin.'.$domain.'/scripts/adduser.php'; ?>";
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
        newname = prompt("Input new username.");
        if (newname === null) {
            window.location = "<?php echo 'https://admin.'.$domain.'/'; ?>";
            break;
        }
        newpass = prompt("Set password for "+newname+'.');
        if (newpass === null) {
            window.location = "<?php echo 'https://admin.'.$domain.'/'; ?>";
            break;
        }
        if (!confirm("Are you sure, add user "+newname+" with password "+newpass+"?")) {
            window.location = "<?php echo 'https://admin.'.$domain.'/'; ?>";
            break;
        }
        post('/scripts/adduser.php', {'username': newname, 'password': newpass});
    }
    while (false);
})()
</script>
<?php endswitch ?>
</body>
</html>
