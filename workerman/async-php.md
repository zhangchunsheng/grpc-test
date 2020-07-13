async-php

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;
use Amp\Artax\Response;

$http_worker = new Worker("http://0.0.0.0:8083");

$http_worker->count = 1; // 只开一个进程

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
```