<?php
$domain = 'myfolder.my.id';
$from = empty($_GET['from']) ? 'init' : $_GET['from'];
$user = $_SERVER['PHP_AUTH_USER'];
switch ($from) {
    case 'init' :
        $redirect = 'https://'.$user.'-private.'.$domain.'/menu/logout/logout.php';
        break;

    case 'private' :
        $redirect = 'https://'.$user.'-public.'.$domain.'/menu/logout/logout.php';
        break;

    case 'public' :
        $redirect = 'https://'.$domain.'/menu/logout/logout.php';
        break;

    case 'self' :
        $redirect = 'https://'.$domain.'/login/';
        break;
}
?>
<script>
    window.location = "<?php echo $redirect; ?>";
</script>
