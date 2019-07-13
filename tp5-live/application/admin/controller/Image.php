<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/6/10
 * Time: 19:10
 */
namespace app\admin\controller;

use app\common\lib\Util;

class Image
{
    public function index(){
        $file = request()->file('file');
        if ($file){
            $info = $file->move(APP_PATH.'/../public/static/upload');
        }else{
            return Util::show(0,"no file to upload");
        }
        if($info){
            $data = [
                'image' => "/".$info->getSaveName(),
            ];
            return Util::show(1,"success upload",$data);
        }else{
            return Util::show(0,"fail upload");
        }
    }
}