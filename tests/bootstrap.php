<?php

if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', realpath(dirname(__FILE__) . '/../public') . '/');
    define('INCLUDE_PATH', realpath(dirname(__FILE__) . '/../include') . '/');

    define('ENVIRONMENT', 'dev');                                                                                                                                                       
    require INCLUDE_PATH . '/bootstrap.php';
    require __DIR__ . '/testbase.php';
    require __DIR__ . '/suitebase.php';

    if (!include_once(INCLUDE_PATH . "config.php"))
    {
        throw new Exception("Failed to load config.php");
    }

    // sets up exception handler and base exception class
    require_once FRAMEWORK_FOLDER . 'exception.php';

    // sets up autoloader
    require_once FRAMEWORK_FOLDER . 'autoload.php';
}
