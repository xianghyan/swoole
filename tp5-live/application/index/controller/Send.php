<?php
namespace app\index\controller;
use app\common\lib\tencent\Sms;
use app\common\lib\Util;
use app\common\lib\Redis;

class Send
{
    /**
     * 短信验证码发送
     */
    public function index()
    {
        //$phoneNum = input('phone_num',0,"intval");
        $phoneNum = intval($_GET['phone_num']);
        if(empty($phoneNum)){
            return Util::show(0, '手机号不能为空');
        }
        //生成一个随机数
        $code = rand(1000,9999);

        $taskData = [
            'method' => 'sendSms',
            'data' => [
                'phone' => $phoneNum,
                'code' => $code,
            ],
        ];
        $_POST['http_server']->task($taskData);
        return Util::show(1, '验证码发送成功');

        /*if ($response['result'] == 0){
            //redis
            $redis = new \Swoole\Coroutine\Redis();
            $redis->connect(config('redis.host'),config('redis.port'));
            $redis->set(Redis::smsKey($phoneNum), $code);
            return Util::show(1, '验证码发送成功');
        }else{
            return Util::show(0, '验证码发送失败');
        }*/
    }
}
