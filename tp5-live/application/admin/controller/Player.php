<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/6/14
 * Time: 18:12
 */

namespace app\admin\controller;


use think\Controller;
use think\Db;

class Player extends Controller
{
    public function index(){
        $list = Db::name("player")->paginate(config('template.admin_page_num'));
        $this->assign('list',$list);
        return $this->fetch();
    }
}