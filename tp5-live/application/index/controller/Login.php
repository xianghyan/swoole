<?php

namespace app\index\controller;
use app\common\lib\Util;
use app\common\lib\Redis;
use app\common\lib\redis\Predis;

class Login
{
	public function index(){
		//phone code
		$phoneNum = intval($_GET['phone_num']);
		$code = intval($_GET['code']);
		if (empty($phoneNum) || empty($code)) {
			return Util::show(0, 'phone or code is error');
		}
		//redis code
		$redisCode = Predis::getInstance()->get(Redis::smsKey($phoneNum));
		if ($redisCode == $code){
		    // 写入redis
            $data = [
                'user' => $phoneNum,
                'srcKey' => md5(Redis::userKey($phoneNum)),
                'time' => time(),
                'isLogin' => true,
            ];
            Predis::getInstance()->set(Redis::userKey($phoneNum), $data);

            return Util::show(1, "ok", $data);
        } else {
            return Util::show(0, "login error");
        }
		//echo $redisCode;
		//redis.so

	}
}