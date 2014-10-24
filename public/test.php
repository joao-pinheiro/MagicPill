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

$configArray = array(
    'part1' => array(
        'master' => array(
            'host' => 'localhost',
            'schema' => 'live_db',
            'password' => 'password_live',
            'encoding' => 'utf8'
        )),

    'part3' => array(
        'key3' => array(
            'key1' => 'value2'
        )
    ),
);

$newConfigArray = array(
    'part1' => array(
        'master' => array(
            'host' => 'localhost',
            'schema' => 'test_db',
            'password' => 'password_stg'
        )),

    'part3' => array(
        'key3' => array(
            'key1' => 'value2'
        )
    ),
);

$config = new MagicPill\Util\Config\IniFile('teste.ini');
$x = $config->db->adapter;
var_dump($x());

$config = new MagicPill\Util\Config\PhpFile('config.php', 'production');
var_dump($config->toArray());

$time = microtime(true);

//---------------
$array = array(
    'writerName' => 'Stream',
    'writerParams' => array(
        'stream' => '/tmp/zf.log',
        'mode' => 'a',
        ),
    'filterName' => 'Priority',
    'filterParams' => array(
        'priority' => 5,
    ) 
);


$time = microtime(true);
$result = \Zend_Log::factory(array('log' => $array));
for ($i=0; $i < 10; $i++) {
    $result->log('XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', 4);
}
echo "ZF Log stuff: ";
echo microtime(true) - $time;


$time = microtime(true);
$array = array(
    'default' => array(
        'writers' => array(
            array(
                'writerName' => 'WriterStream',
                'streamName' => 'xpto.log',
                'streamMode' => 'a',
                'logLevel' => 5
            )
        )
    )
);

$logManager = new MagicPill\Util\Log\LogManager($array);
for ($i=0; $i < 10; $i++) {
    $logManager->log(5, 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
}    
echo "Log stuff: ";
echo microtime(true) - $time;
        
$execTime = microtime(true) - START_TIME;
echo $execTime;


echo "<br><br>";
$time = microtime(true);
$dict = new \MagicPill\Collection\Dictionary();
for($i = 0; $i < 1000; $i++) {
    $dict->add('key_' . $i, $i);
}
$execTime = microtime(true) - START_TIME;
echo $execTime;

echo "<br><br>";
$time = microtime(true);
$array = array();

for($i = 0; $i < 1000; $i++) {
    $array['key_' . $i] = $i;
}
$execTime = microtime(true) - START_TIME;
echo $execTime;
var_export($_SERVER);