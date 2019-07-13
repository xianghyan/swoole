<?php
/**
 * 必须在类型onRequest中使用，下面的运行方式无效
 * 如在http服务的onRequest中使用协程
 * User: Admin
 * Date: 2019/5/24
 * Time: 9:37
 */
$redis = new Swoole\conroutine\Rddis();
$redis->connect('127.0.0.1', 6379);
$redis->get();