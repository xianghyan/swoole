<?php
/**
 * Created by PhpStorm.
 * User: yxh
 * Date: 2019/6/14
 * Time: 23:56
 */

namespace app\common\model;


use think\Db;
use think\Model;

class Outs extends Model
{
    //获取器-状态
    public function getTypeAttr($value)
    {
        $types = [1=>'第一节',2=>'第二节',3=>'第三节',4=>'第四节'];
        return $types[$value];
    }

    //获取器-倒计时
    public function getCountdownAttr($value)
    {
        $countdown = substr(date("H:i:s", $value),-5);
        return $countdown;
    }

    //获取器增加team_name字段
    public function getTeamNameAttr($value, $data)
    {
        $team_array = array();
        //team_id为0
        $team_array[0] = "直播员";

        $team_list = Db::name('team')->select();
        //处理球队表为数组
        foreach ($team_list as $team){
            $team_array[$team['id']] = $team['name'];
        }
        return $team_array[$data['team_id']];
    }

    //获取器增加team_logo字段
    public function getTeamLogoAttr($value, $data)
    {
        $team_array = array();
        //team_id为0
        $team_array[0] = "";

        $team_list = Db::name('team')->select();
        //处理球队表为数组
        foreach ($team_list as $team){
            $team_array[$team['id']] = $team['image'];
        }
        return $team_array[$data['team_id']];
    }
}