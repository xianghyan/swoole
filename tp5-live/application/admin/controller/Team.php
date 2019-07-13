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

class Team extends Common
{
    public function index(){
        $map = array();
        if(!empty($_GET['searchname'])){
            $search_name = $_GET['searchname'];
            //$map['name'] = $search_name;
            $map[] = ['name', 'like', '%'.$search_name.'%'];
            $this->assign('search_name',$search_name);
        }
        $list = Db::name("team")->where($map)->paginate(config('template.admin_page_num'))->appends('s','admin/team/index');
        //echo Db::name('team')->getLastSql();
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 增加修改球队
     */
    public function edit(){
        if(!empty($_GET['id'])){
            $team_id = $_GET['id'];
        }
        //修改添加
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            //修改
            if(!empty($team_id)){
                $team = \app\common\model\Team::get($team_id);
                $team->name = $_POST['name'];
                $team->type = intval($_POST['type']);
                $team->image = $_POST['image'];
                $team->update_time = time();
                $result = $team->save();
            }else{
                //添加
                $data = [
                    'name' => $_POST['name'],
                    'type' => intval($_POST['type']),
                    'image' => $_POST['image'],
                    'create_time' => time()
                ];
                $result = Db::name('team')->insert($data);
            }
            if ($result){
                return Util::show(1,"提交成功");
            }
            return Util::show(0,"提交失败");
        }
        if(!empty($team_id)){
            $team_info = Db::name('team')->where('id', $team_id)->find();
            $this->assign('info', $team_info);
        }
        return $this->fetch();
    }

    public function delete(){
        if(!empty($_GET['id'])){
            $team_id = $_GET['id'];
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            //批量删除
            $ids = $_POST['ids'];
            $result = Db::name('team')->delete($ids);
        }else{
            //单个删除
            $result = Db::name('team')->delete($team_id);//直接删除
        }

        if($result){
            return Util::show(1,"success delete");
        }
        return Util::show(0,"failed delete");
    }
}