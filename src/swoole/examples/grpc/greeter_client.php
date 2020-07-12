<?php

use Helloworld\GreeterClient;
use Helloworld\HelloRequest;

require __DIR__ . '/../../vendor/autoload.php';

$name = !empty($argv[1]) ? $argv[1] : 'Swoole';

Swoole\Coroutine::create(function () use ($name) {
    $greeterClient = new GreeterClient('127.0.0.1:50051');
    $request = new HelloRequest();
    $request->setName($name);
    [$reply] = $greeterClient->SayHello($request);
    $message = $reply->getMessage();
    echo "{$message}\n";
    $greeterClient->close();
});
