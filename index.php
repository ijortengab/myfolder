<?php

require 'vendor/autoload.php';

use IjorTengab\MyFolder\Core\Application;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Credit: https://github.com/symfony/polyfill-php80/blob/1.x/Php80.php
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return false !== strpos($haystack, $needle);
    }
}
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return 0 === strncmp($haystack, $needle, \strlen($needle));
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle) {
        $needleLength = \strlen($needle);
        return 0 === substr_compare($haystack, $needle, -$needleLength);
    }
}
$app = new Application(__DIR__);
$app->run();
