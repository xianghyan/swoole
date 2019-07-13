<?php
$http = new swoole_http_server("0.0.0.0",8811);

$http->set([
    'enable_static_handler' => true,
    'document_root' => '/var/www/swoole/data'
]);

$http->on('request',function ($request, $response){
    //print_r($request->get);
    $response->cookie("singwa","xsssss",time()+1800);
    $response->end("sss".json_encode($request->get));
});
$http->start();