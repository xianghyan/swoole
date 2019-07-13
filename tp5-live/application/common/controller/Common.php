<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/6/17
 * Time: 13:16
 */
namespace app\common\controller;

use think\Controller;
use think\Db;

class Common extends Controller
{
    public function initialize(){
        //初始化时清除模板变量
        $this->view->clear();
    }

    /**
     * 获取球队列表
     */
    public function getTeamList(){
        return Db::name('team')->select();
    }
}