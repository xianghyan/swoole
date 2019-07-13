<?php
/**
 *
 */
$server = new Swoole\WebSocket\Server("0.0.0.0", 8812);

$server->set([
    'enable_static_handler' => true,
    'document_root' => '/var/www/swoole/data'
]);

$server->on('open', 'onOpen');

function  onOpen($server, $request){
    print_r($request->fd."\n");
    //echo "{$request->fd}\n";
}

$server->on('message', function (Swoole\WebSocket\Server $server, $frame) {
    echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
    $server->push($frame->fd, "yxh ws server success");
});

$server->on('close', function ($ser, $fd) {
    echo "client {$fd} closed\n";
});

$server->start();