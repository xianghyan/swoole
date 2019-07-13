<?php
namespace app\common\lib\task;
use app\common\lib\redis\Predis;
use app\common\lib\tencent\Sms;
use app\common\lib\Redis;
/**
 * swoole里面task异步任务 统一处理
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/5/28
 * Time: 23:14
 */
class Task
{
	/**
	 * 异步发送验证码
	 * @param  [type] $data [description]
	 * @param  [type] $serv [swoole serve对象]
	 * @return [type]       [description]
	 */
    public function sendSms($data, $serv)
    {
        try {
            $response = Sms::smsSend($data['phone'], $data['code']);
        } catch (\Exception $e) {
            return false;
        }
        //发送成功，写入redis(同步)
        if ($response['result'] == 0){
            Predis::getInstance()->set(Redis::smsKey($data['phone']), $data['code']);
        }else{
            return false;
        }

        return true;
    }

    /**
     * 通过task发送赛况实时数据给客户端
     * @param $info
     * @param  [type] $serv [swoole serve对象]
     */
    public function pushLive($info, $serv){
        if(!empty($info['game_id'])){
            $clients = Predis::getInstance()->zRangeByScore(config('redis.live_game_key'), $info['game_id'], $info['game_id']);
            foreach($clients as $fd){
                $serv->push($fd, json_encode($info));
            }
        }
    }

    /**
     * 通过task发送聊天实时数据给客户端
     * @param $info
     * @param  [type] $serv [swoole serve对象]
     */
    public function pushChart($info, $serv){
        if(!empty($info['game_id'])){
            $clients = Predis::getInstance()->zRangeByScore(config('redis.chart_game_key'), $info['game_id'], $info['game_id']);
            foreach($clients as $fd){
                $serv->push($fd, json_encode($info));
            }
        }
    }
}