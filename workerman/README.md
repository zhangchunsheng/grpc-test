### php异步编程
```
内容简介：传统的 php-fpm 一个进程执行一个请求，要达到多少并发，就要生成多少个进程。更糟糕的是每次请求都需要重新编译执行，导致并发一直上不来。因此出现了 Swoole 和 WorkerMan 两个国内流行的常驻内存框架[1]。这两个框架原理都是通过事件循环，让程序一直停留在内存，等待外部请求，达到高并发。在工作目录下新建文件 slowServer.php
本文转载自：https://segmentfault.com/a/1190000017982225，本站转载出于传递更多信息之目的，版权归原作者或者来源机构所有。
前言

传统的 php-fpm 一个进程执行一个请求，要达到多少并发，就要生成多少个进程。更糟糕的是每次请求都需要重新编译执行，导致并发一直上不来。因此出现了 Swoole 和 WorkerMan 两个国内流行的常驻内存框架[1]。这两个框架原理都是通过事件循环，让程序一直停留在内存，等待外部请求，达到高并发。

为什么需要异步
先来看一个例子
在工作目录下新建文件 slowServer.php

<?php
sleep(5); // 5秒后才能返回请求
echo 'done';
开启服务

$ php -S localhost:8081 slowServer.php
开另一个终端，安装依赖

$ pecl install event # 安装 event 扩展
$ composer require workerman/workerman
$ composer require react/http-client:^0.5.9
新建文件 worker.php

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
开启服务器

php worker.php start
在浏览器开启两个标签，都打开网址 http://localhost :8082 。这时可以看到终端输出“1”，过了一会儿又输出“1”，原因是8081 服务器 在处理第一个请求的时候阻塞在了等待8081返回之中，等第一个请求结束后，才开始处理第二个请求。也就是说请求是一个一个执行的，要达到多少个并发，就要建立多少个进程，跟 php-fpm 一样。现在修改一下代码

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
现在打开服务，再在浏览器发起请求，发现第二个“1”在请求后就马上输出了，而这时第一个请求还没结束。这表明进程不再阻塞，并发量取决于 cpu 和 内存，而不是进程数。

为什么需要异步
通过上面的例子已经很明白了，reactphp 框架通过把 http 请求变成异步，让 onMessage 函数变成非阻塞，cpu 可以去处理下一个请求。即从 cpu 循环等待 8081 返回，变成了 epoll 等待。

异步的意义在于把 cpu 从 io 等待中解放出来，可以处理其他计算任务。如果你想知道怎么用框架实现异步，看到这里就可以了。WorkerMan 配合 ReactPHP 或者自身的 AsyncTcpConnection 已经可以满足很多 io 请求异步化的需求。下面继续讨论这些框架是怎么做到异步的。

哪些地方应该被做成异步
通过上面的例子已经知道一旦执行到不需要 cpu，但是要等待 io 的时候，应该把 io 的过程做成异步。

实现事件循环
上面的例子是通过 reactphp 把 http 请求变成了异步，其实 WorkerMan 框架本身也是异步的，下面来看看 WorkerMan 是怎么使 onMessage 函数可以异步接受请求。先来新建下面这个文件 react.php

<?php
$context = stream_context_create();
$socket = stream_socket_server('tcp://0.0.0.0:8081', $errno, $errmsg, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN,$context); // 注册一个 fd（file descriptor)

function react($socket){
    $new_socket = stream_socket_accept($socket, 0, $remote_address);
    echo 1;
}

$eventBase = new EventBase();
$event = new Event($eventBase, $socket, Event::READ | Event::PERSIST, 'react', $socket); // 注册一个事件，检测 fd 有没有写入内容
$event->add();
$eventBase->loop(); // 开始循环
开始执行

$ php react.php
在另一个终端执行

telnet 127.0.0.1 8081
这时就会看到第一个终端输出'1'。

我之前写过一篇文章 《php使用epoll》 ，是这篇文章的基础。那篇文章里事件回调是通过定时来实现，即

$event->add($seconds);
而这里，事件回调是通过检测 fd 是否有写入内容来实现，这个过程不需要 cpu 参与。当 fd 有内容写入时，会调函数 'react'，这时开始使用 cpu。如果这时候进程执行另一个异步请求，比如用 reactphp 框架请求一个网页，那么程序会让出 cpu，此时如果有另一个请求进来，就可以回调执行另一个 'react' 函数。由此提高了并发量。

协程
生成器 Generater
这是生成器的 PHP 官方文档 http://php.net/manual/zh/lang...

<?php
function gen_one_to_three() {
    for ($i = 1; $i <= 3; $i++) {
        //注意变量$i的值在不同的yield之间是保持传递的。
        yield $i;
    }
}

$generator = gen_one_to_three();
foreach ($generator as $value) {
    echo "$value\n";
}
生成器就是每次程序执行到 yield 的时候保存状态，然后返回 $i，是否继续执行 gen_one_to_three 里的循环，取决于主程序是否继续调用

什么是协程
上面的程序另一种写法是

<?php
$i = 1;
function gen_one_to_three() {
    global $i;
    if ($i<=3){
        return $i++;
    }
}

while ($value = gen_one_to_three()) {
    echo "$value\n";
}
由此可见，协程就是一种对函数的封装，使其变成一种可以被中断的函数，行为更像是子进程或子线程，而不是函数。协程的具体写法这里不细写，因为协程的写法十分复杂，可能需要再做一层封装才能好用。

协程与异步
既然协程可以被中断，那么只要在程序发起请求后发起事件循环，然后用 yield 返回，然后程序继续执行主程序部分，等事件返回后触发函数，执行 Generatot::next() 或 Generator::send() 来继续执行协程部分。封装好后就好像没有异步回调函数一样，和同步函数很像。

现在已经有 ampphp 和 swoole 两个框架封装了协程，有兴趣可以了解一下。

国外还有 https://amphp.org 和 https://reactphp.org 这两个框架
博客地址： http://b.ljj.pub
```