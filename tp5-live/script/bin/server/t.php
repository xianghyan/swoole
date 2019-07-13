<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/6/13
 * Time: 13:45
 */
// 加载基础文件
require __DIR__ . '/../thinkphp/start.php';

//重启时删除redis中的client
$clients = [7, 8];
if (!empty($clients)){
    \app\common\lib\redis\Predis::getInstance()->sRem(config('redis.live_game_key'), $clients);
}