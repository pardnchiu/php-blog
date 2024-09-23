<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FilterHandler;
use Monolog\Formatter\LineFormatter;

// 建立日誌紀錄器
$log = new Logger('app');

// 設定日誌記錄的時間為台灣時區
$log->pushProcessor(function ($record) {
    $date = new DateTime('now', new DateTimeZone('Asia/Taipei'));
    $time = $date->format("Y-m-d H:i:s");
    $record['extra']['datetime'] = $time;
    return $record;
});

// 自定義日誌格式
$output = "%extra.datetime%: %message%\n";
$formatter = new LineFormatter($output);

// 設置處理器並應用格式化器
$infoStream = new StreamHandler($_SERVER['DOCUMENT_ROOT'] . '/storage/logs/info.log', Logger::INFO);
$infoStream->setFormatter($formatter);
$debugStream = new StreamHandler($_SERVER['DOCUMENT_ROOT'] . '/storage/logs/debug.log', Logger::DEBUG);
$debugStream->setFormatter($formatter);
$errorStream = new StreamHandler($_SERVER['DOCUMENT_ROOT'] . '/storage/logs/error.log', Logger::ERROR);
$errorStream->setFormatter($formatter);

// 設置過濾處理器
$infoHandler = new FilterHandler($infoStream, Logger::INFO, Logger::INFO);
$debugHandler = new FilterHandler($debugStream, Logger::DEBUG, Logger::DEBUG);
$errorHandler = new FilterHandler($errorStream, Logger::ERROR, Logger::ERROR);

// 將處理器推送到日誌紀錄器中
$log->pushHandler($infoHandler);
$log->pushHandler($debugHandler);
$log->pushHandler($errorHandler);

function print_info($str)
{
    global $log;
    $log->info($str);
}

function print_dev($str)
{
    global $log;
    $log->debug($str);
}

function print_err($str)
{
    global $log;
    $log->error($str);
}
