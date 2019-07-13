<?php
$http = new swoole_http_server("0.0.0.0",8811);

$http->set([
    'enable_static_handler' => true,
    'document_root' => '/var/www/swoole/tp5-live/public/static',
    'worker_num' => 3,
]);
$http->on('WorkerStart',function (swoole_server $server, $worker_id){
    // 加载基础文件
    require __DIR__ . '/../thinkphp/base.php';
});
$http->on('request',function ($request, $response) use($http){
    //print_r($request->get)
    if(isset($request->server)){
        foreach ($request->server as $k => $v){
            $_SERVER[strtoupper($k)] = $v;
        }
    }
    if(isset($request->header)){
        foreach ($request->header as $k => $v){
            $_SERVER[strtoupper($k)] = $v;
        }
    }
    if (count($_GET) > 0) {
        $_GET = array();
    }
    if(isset($request->get)){
        foreach ($request->get as $k => $v){
            $_GET[$k] = $v;
        }
    }
    if (count($_POST) > 0) {
        $_POST = array();
    }
    if(isset($request->post)){
        foreach ($request->post as $k => $v){
            $_POST[$k] = $v;
        }
    }
    //$response->cookie("singwa","xsssss",time()+1800);

    ob_start();
    // 执行应用并响应
    try{
        think\Container::get('app')->run()->send();
    }catch (\Exception $e){
        //todo
    }
    //echo "-action-".request()->action().PHP_EOL;
    $result = ob_get_contents();
    @ob_end_clean();
    //$response->end("sss".json_encode($request->get));
    $response->end($result);
    //$http->close($http->fd);
});
$http->start();