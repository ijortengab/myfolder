<?php
$domain = 'myfolder.my.id';
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
    $username = $_SERVER['PHP_AUTH_USER'];
    $result = WhileLocked($database, function () use ($database, $username, $password) {
        // Reference: https://stackoverflow.com/a/3004080
        $reading = fopen($database, 'r');
        $writing = fopen($database.'.tmp', 'w');
        $replaced = false;
        while (!feof($reading)) {
            $line = fgets($reading);
            $line = preg_replace_callback(
                '/^'.preg_quote($username.':').'(.+)/',
                function ($matches) use ($username, $password, &$replaced) {
                    $replaced = true;
                    // Reference: 
                    // https://www.virendrachandak.com/techtalk/using-php-bcrypt-algorithm-for-htpasswd-generation/
                    $encrypted_password = password_hash($password, PASSWORD_BCRYPT);
                    return $username.':'.$encrypted_password;
                },
                $line
            );
            fputs($writing, $line);
        }
        fclose($reading); fclose($writing);
        // might as well not overwrite the file if we didn't replace anything
        if ($replaced) {
            rename($database.'.tmp', $database);
        } else {
            unlink($database.'.tmp');
        }
        // User pasti exists, return true.
        return true;
    });
    if ($result) {
        $mode = 'success';
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
    alert('Password Change successfully.');
    window.location = "<?php echo 'https://'.$username.':'.$password.'@'.$domain.'/'; ?>";
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
        newpass = prompt("Please enter new password.");
        if (newpass === null) {
            window.location = "<?php echo 'https://'.$domain.'/menu/'; ?>";
            break;
        }
        if (!confirm("Are you sure, change password to "+newpass+"?")) {
            window.location = "<?php echo 'https://'.$domain.'/menu/'; ?>";
            break;
        }
        post('/menu/password/', {'password': newpass});
    }
    while (false);
})()
</script>
<?php endswitch ?>
</body>
</html>
