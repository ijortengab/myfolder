<?php
$uri = $_SERVER['REQUEST_URI'];
$parts = parse_url($uri);
$path = '';
if (isset($parts['path'])) {
    $path = trim($parts['path'],'/');
}
$dirs = explode('/', $path);
$first = array_shift($dirs);
$parent_directory = implode('/', $dirs);
header('Location: https://'.$_SERVER['PHP_AUTH_USER'].':'.$_SERVER['PHP_AUTH_PW'].'@'.$_SERVER['REMOTE_USER'].'-'.$first.'.'.$_SERVER['HTTP_HOST'].'/'.$parent_directory);
