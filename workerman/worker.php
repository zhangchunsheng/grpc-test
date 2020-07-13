<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/13
 * Time: 3:03 PM
 */
//composer require workerman/workerman
//composer require react/http-client:^0.5.9

//php worker.php start
require_once __DIR__ . '/vendor/autoload.php';
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;
use Amp\Artax\Response;

$http_worker = new Worker("http://0.0.0.0:8082");

$http_worker->count = 1; // 只开一个进程

$http_worker->onMessage = function($connection, $host) {
    echo 1;
    $data = file_get_contents('http://localhost:8081');
    $connection->send($data);
};

Worker::runAll();