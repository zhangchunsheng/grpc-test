<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2020/7/13
 * Time: 3:22 PM
 */
//scl enable php74 bash
//php react.php
//telnet 127.0.0.1 8082
$context = stream_context_create();
$socket = stream_socket_server('tcp://0.0.0.0:8082', $errno, $errmsg, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $context); // 注册一个 fd（file descriptor)

function react($socket) {
    $new_socket = stream_socket_accept($socket, 0, $remote_address);
    echo 1;
}

$eventBase = new EventBase();
$event = new Event($eventBase, $socket, Event::READ | Event::PERSIST, 'react', $socket); // 注册一个事件，检测 fd 有没有写入内容
$event->add();
$eventBase->loop(); // 开始循环

//https://amphp.org/
//https://reactphp.org/
//https://b.ljj.pub/