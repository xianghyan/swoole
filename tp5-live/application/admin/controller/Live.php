<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/6/10
 * Time: 19:10
 */
namespace app\admin\controller;

use app\common\controller\Common;
use app\common\lib\Util;
use think\Db;

class Live extends Common
{
    public function push(){
        $game_id = $_GET['id'];
        $data = [
            'game_id' => $game_id,
            'team_id' => intval($_POST['team_id']),
            'content' => $_POST['content'],
            'image' => $_POST['image'],
            'type' => intval($_POST['type']),
            'countdown' => strtotime($_POST['countdown']),
            'status' => 1,
            'create_time' => time()
        ];

        try{
            //赛况信息入库
            Db::name('outs')->insert($data);
            //修改比分
            $game = \app\common\model\Game::get($game_id);
            $game->a_score = intval($_POST['a_score']);
            $game->b_score = intval($_POST['b_score']);
            $game->save();
        }catch (\Exception $e){
            return json($e->getMessage());
        }
        //赛事信息
        $game_info = \app\common\model\Game::where('id', $game_id)->find();

        $teams = [
            $game_info['a_id'] => [
                'name' => $game_info['a_name'],
                'logo' => $game_info['a_logo']
            ],
            $game_info['b_id'] => [
                'name' => $game_info['b_name'],
                'logo' => $game_info['b_logo']
            ]
        ];
        //组织数据，push到直播页面
        $info = [
            'game_id' => intval($game_id),
            'type' => intval($_POST['type']),
            'countdown' => substr($_POST['countdown'],-5),
            'title' => $teams[$_POST['team_id']]['name'] ?? "直播员",
            'logo' => $teams[$_POST['team_id']]['logo']??"",
            'content' => $_POST['content']??"",
            'image' => $_POST['image']??"",
            'a_score' => intval($_POST['a_score']),
            'b_score' => intval($_POST['b_score']),
        ];

        $taskData = [
            'method' => 'pushLive',
            'data' => $info
        ];
        $_POST['http_server']->task($taskData);
        return Util::show(1, '发布成功');
    }
}