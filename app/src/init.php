<?php
require_once '../vendor/autoload.php';
require_once 'Configs/default.php';
require_once 'Configs/monolog.php';

ini_set('zlib.output_compression', 'On');
ini_set('zlib.output_compression_level', '6');

// 載入環境變量
if (file_exists($ROOT . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($ROOT);
    $dotenv->load();

    $ENV = $_ENV['ENV'];
};

$REDIS_CLIENT   = new Models\RedisClient(0);
$CACHE_CLIENT   = new Models\CacheClinet($ROOT, $REDIS_CLIENT);
$SESSION_CLIENT = new Models\SessionClient($ROOT, $REDIS_CLIENT);
// 設置會話初始資料
if ($SESSION_CLIENT->get('session_id') === null) {
    $SESSION_CLIENT->set('created_time', date('Y-m-d H:i:s'));
    $SESSION_CLIENT->set('session_id', $SESSION_CLIENT->getId());
};


$Router = new App\Router();
$Router->init();
