<?php

define('START_TIME', microtime(true));
register_shutdown_function('session_write_close');

// Define path to application directory
define('APPLICATION_PATH', realpath(__DIR__ . '/../'));
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'live'));

require_once APPLICATION_PATH . '/autoloader.php';

set_include_path(
        realpath(APPLICATION_PATH . '/library/')
        . PATH_SEPARATOR
        . get_include_path()
        );

$app = new \MagicPill\Application(array(
    'environment' => getenv('APPLICATION_ENV'),
    'configFile' => APPLICATION_PATH . '/config/application.ini',
    'resourceNamespace' => array(
        '\Namespace\One',
        '\Namespace\Two'
        ),
    'developmentEnvironment', in_array(APPLICATION_ENV, array('development', 'testing', 'staging'))
));

throw  MagicPill\Exception\ExceptionFactory::ExceptionTestNotFound('This is the message');

$execTime = microtime(true) - START_TIME;
echo $execTime;

