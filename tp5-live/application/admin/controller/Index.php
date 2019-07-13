<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/6/14
 * Time: 16:59
 */

namespace app\admin\controller;


use think\Controller;

class Index extends Controller
{
    public function index(){
        return $this->fetch();
    }

}