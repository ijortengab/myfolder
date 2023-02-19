<?php
// Common config.
$domain = 'myfolder.my.id';
$installation_directory = realpath(__DIR__.'/..');
$user_storage_directory = $installation_directory.'/storage';
$public_storage_directory = $installation_directory.'/public';
$default_timezone = 'Asia/Jakarta';
// Override config per environment.
if (is_file(__DIR__.'/config.local.php')) {
    include(__DIR__.'/config.local.php');
}
