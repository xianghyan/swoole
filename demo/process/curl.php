<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/5/23
 * Time: 18:46
 */
echo "start time".date("Ymd H:i:s").PHP_EOL;
$workers = [];
$urls = [
    'http://baidu.com',
    'http://sina.com.cn',
    'http://qq.com',
    'http://baidu.com?serarch=yxh',
    'http://baidu.com?serarch=yxh2',
    'http://baidu.com?serarch=yxh3',
];

for ($i=0;$i<6;$i++){
    //子进程
    $process = new swoole_process(function (swoole_process $worker) use ($i, $urls){
        //curl
        $content = curlData($urls[$i]);
        //echo $content.PHP_EOL;
        $worker->write($content.PHP_EOL);
    },true);
    $pid = $process->start();
    $workers[$pid] = $process;
}

foreach ($workers as $process){
    echo $process->read();
}
function curlData($url){
    sleep(1);
    return $url." success".PHP_EOL;
}
echo "end time".date("Ymd H:i:s").PHP_EOL;