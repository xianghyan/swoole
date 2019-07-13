<?php
/**
 * Created by PhpStorm.
 * User: yxh
 * Date: 2019/6/22
 * Time: 16:27
 */

namespace app\index\controller;


use app\common\controller\Common;
use app\common\lib\redis\Predis;
use app\common\lib\Util;
use think\Db;

class Chart extends Common
{
    public function index(){
        if(!empty($_POST['game_id'])){
            //聊天消息入库
            $info = [
                'game_id' => $_POST['game_id'],
                'user_id' => 0,
                'content' => $_POST['content'],
                'status' => 1,
                'create_time' => time()
            ];
            try{
                Db::name('chart')->insert($info);
            }catch (\Exception $e){
                return json($e->getMessage());
            }

            //组织数据，push到聊天室
            $data = [
                'game_id' => $_POST['game_id'],
                'user' => $_POST['user'].rand(0, 999),
                'content' => $_POST['content']
            ];
            $taskData = [
                'method' => 'pushChart',
                'data' => $data
            ];
            $_POST['http_server']->task($taskData);
            return Util::show(1, 'success');
        }
        return Util::show(0, 'failed');
    }
}