<?php

define('ROOT_SRC', __DIR__ . DIRECTORY_SEPARATOR);
define('ROOT_DIR', __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR);
define('URL_BASE', '/testing/');

spl_autoload_register('autoloader');

function autoloader($class)
{
    $file = str_replace("\\", DIRECTORY_SEPARATOR, $class);
    $file = preg_replace("/plywood\//i", ROOT_SRC, $file, 1);
    $file .=  ".php";

    require $file;
}
