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
/**
 * sudo yum install php74-php-posix
 * php74-php-process
 * curl 'http://localhost:8083'
 */
require_once __DIR__ . '/vendor/autoload.php';
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;
use Amp\Artax\Response;

$http_worker = new Worker("http://0.0.0.0:8083");

$http_worker->count = 1; // 只开一个进程

/*$http_worker->onMessage = function($connection, $host) {
    echo 1;
    $data = file_get_contents('http://localhost:8082');
    $connection->send($data);
};*/

$http_worker->onMessage = function($connection, $host) {
    echo 1;
    $loop    = Worker::getEventLoop();
    $client  = new \React\HttpClient\Client($loop);
    $request = $client->request('GET', 'http://localhost:8081');
    $request->on('error', function(Exception $e) use ($connection) {
        $connection->send($e);
    });
    $request->on('response', function ($response) use ($connection) {
        $response->on('data', function ($data) use ($connection) {
            $connection->send($data);
        });
    });
    $request->end();
};

Worker::runAll();