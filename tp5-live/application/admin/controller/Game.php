<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/6/14
 * Time: 18:12
 */

namespace app\admin\controller;


use app\common\controller\Common;
use app\common\lib\Util;
use think\Db;

class Game extends Common
{
    public function index(){
        $map = array();
        /*if(!empty($_GET['searchname'])){
            $search_name = $_GET['searchname'];
            //$map['name'] = $search_name;
            $map[] = ['name', 'like', '%'.$search_name.'%'];
            $this->assign('search_name',$search_name);
        }*/
        $list = \app\common\model\Game::where($map)->paginate(config('template.admin_page_num'))->appends('s','admin/game/index');
        //echo Db::name('team')->getLastSql();
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 增加修改直播信息
     */
    public function edit(){
        //直播信息中都要用到所有球队列表
        $team_list = $this->getTeamList();
        $this->assign('team_list', $team_list);

        if(!empty($_GET['id'])){
            $game_id = $_GET['id'];
        }
        //修改添加
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            //修改
            if(!empty($game_id)){
                $game = \app\common\model\Game::get($game_id);
                $game->a_id = $_POST['a_id'];
                $game->b_id = $_POST['b_id'];
                $game->narrator = $_POST['narrator'];
                $game->start_date = strtotime(substr($_POST['start_time'], 0, 10));
                $game->start_time = strtotime($_POST['start_time']);
                $game->status = intval($_POST['status']);
                $game->image = $_POST['image'];
                $game->update_time = time();
                $result = $game->save();
            }else{
                //添加
                $data = [
                    'a_id' => $_POST['a_id'],
                    'b_id' => $_POST['b_id'],
                    'narrator' => $_POST['narrator'],
                    'start_date' => strtotime(substr($_POST['start_time'], 0, 10)),
                    'start_time' => strtotime($_POST['start_time']),
                    'status' => intval($_POST['status']),
                    'image' => $_POST['image'],
                    'create_time' => time()
                ];
                $result = Db::name('game')->insert($data);
            }
            if ($result){
                return Util::show(1,"提交成功");
            }
            return Util::show(0,"提交失败");
        }
        if(!empty($game_id)){
            $game_info = \app\common\model\Game::where('id', $game_id)->find();
            $this->assign('game_info', $game_info);
        }
        return $this->fetch();
    }

    public function delete(){
        if(!empty($_GET['id'])){
            $game_id = $_GET['id'];
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            //批量删除
            $ids = $_POST['ids'];
            $result = Db::name('game')->delete($ids);
        }else{
            //单个删除
            $result = Db::name('game')->delete($game_id);//直接删除
        }

        if($result){
            return Util::show(1,"success delete");
        }
        return Util::show(0,"failed delete");
    }

    //发布赛况界面
    public function out(){
        if(!empty($_GET['id'])){
            $game_id = $_GET['id'];
            $game_info = \app\common\model\Game::where('id', $game_id)->find();
            $this->assign('game_info', $game_info);
            return $this->fetch();
        }
        return false;
    }
}