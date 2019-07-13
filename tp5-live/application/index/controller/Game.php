<?php
/**
 * Created by PhpStorm.
 * User: yxh
 * Date: 2019/6/18
 * Time: 21:28
 */

namespace app\index\controller;


use app\common\controller\Common;
use app\common\model\Outs;
use think\Db;
use think\facade\View;

class Game extends Common
{
    /**
     *所有赛事
     */
    public function index(){
        $map = array();
        //根据日期分组
        $start_date_list = \app\common\model\Game::field('GROUP_CONCAT(id) as ids,start_date')->group('start_date')->select();
        //查询日期星期
        $weekarray=array("日","一","二","三","四","五","六"); //先定义一个数组
        foreach ($start_date_list as $k => $v){
            $week = $weekarray[date("w",$v->getData('start_date'))];
            $start_date_list[$k]['week'] = $week;
        }

        //赛事列表
        $game_info = \app\common\model\Game::where($map)->select();

        $this->assign('game_info', $game_info);
        $this->assign('start_date_list', $start_date_list);
        $this->assign('big-title', '图文赛事直播');
        return $this->fetch();
    }

    /**
     * 赛事直播详情
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function live(){
        $map = array();
        $game_id = $_GET['id'];

        $map = [
            'game_id' => $game_id,
            'status' => 1
        ];
        $game_info = \app\common\model\Game::where('id', $game_id)->find();
        $out_list = Outs::where($map)->order('create_time', 'desc')->select();

        //聊天室消息列表
        $chart_list = Db::name('chart')->where($map)->select();

        $this->assign('game_info', $game_info);
        $this->assign('chart_list', $chart_list);
        $this->assign('out_list', $out_list);
        return $this->fetch();
    }
}