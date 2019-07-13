<?php
/**
 * 异步读取文件
 * User: yxh
 * Date: 2019/5/23
 * Time: 14:05
 */
$result = swoole_async_readfile(__DIR__."/1.txt", function ($filename, $fileContent){
    echo "filename:".$filename.PHP_EOL;
    echo "content:".$fileContent.PHP_EOL;
});

var_dump($result);
echo "start".PHP_EOL;