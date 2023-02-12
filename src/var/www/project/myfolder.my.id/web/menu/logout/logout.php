<?php
$domain = 'myfolder.my.id';
$host = $_SERVER['HTTP_HOST'];
$user = empty($_SERVER['PHP_AUTH_USER']) ? null : $_SERVER['PHP_AUTH_USER'];
preg_match('/^(?<user>.+)-(?<scope>.+)\.'.preg_quote($domain).'$/', $_SERVER['HTTP_HOST'], $matches);
$scope = empty($matches['scope']) ? 'self' : $matches['scope'];
?>
<?php if (empty($user)): ?>
<script>
    window.location = "<?php echo 'https://'.$domain.'/menu/logout/route.php?from='.$scope; ?>";
</script>
<?php else: ?>
<script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
<script>
// Reference: https://tuhrig.de/basic-auth-log-out-with-javascript/
(function () {
    jQuery.ajax({
            type: "GET",
            url: window.location.origin,
            async: false,
            username: "logout",
            password: "logout"
    })
    .done(function(){
        // If we don't get an error, we actually got an error as we expect an 401!
    })
    .fail(function(){
        // We expect to get an 401 Unauthorized error! In this case we are successfully 
        // logged out and we redirect the user.
        window.location = "<?php echo 'https://'.$host.'/menu/logout/logout.php'; ?>";
    });
})()
</script>
<?php endif; ?>
