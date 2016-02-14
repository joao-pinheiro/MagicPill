<?php

define('START_TIME', microtime(true));

// Define path to application directory
define('APPLICATION_PATH', realpath(__DIR__ . '/../'));
define('APPLICATION_ENV', 'testing');

require_once APPLICATION_PATH . '/vendor/autoload.php';

set_include_path(
    realpath(APPLICATION_PATH . '/library/')
    . PATH_SEPARATOR
    . get_include_path()
);

/*
$app = new \Weduc\Platform\Application\MvcApplication(
    \Weduc\Core\Registry::getInstance()
);

$app->start();
*/
