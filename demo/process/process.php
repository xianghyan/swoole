<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/5/23
 * Time: 17:51
 */
$process = new swoole_process(function (swoole_process $pro){
    //todo
    $pro->exec("/usr/bin/php",[__DIR__."/../server/http_server.php"]);
},false);
$pid = $process->start();
echo $pid.PHP_EOL;

swoole_process::wait();