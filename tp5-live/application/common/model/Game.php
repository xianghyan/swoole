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

class Game extends Model
{
    //获取器-状态
    public function getStatusAttr($value)
    {
        $status = [0=>'未开始',1=>'已开始',2=>'已结束'];
        return $status[$value];
    }

    //获取器-日期
    public function getStartDateAttr($value)
    {
        return date("Y-m-d", $value);
    }

    //获取器-时间
    public function getStartTimeAttr($value)
    {
        $start_time = date("Y-m-d H:i:s", $value);
        return $start_time;
    }

    //获取器增加a_name字段
    public function getANameAttr($value, $data)
    {
        $team_array = array();
        $team_list = Db::name('team')->select();
        //处理球队表为数组
        foreach ($team_list as $team){
            $team_array[$team['id']] = $team['name'];
        }
        return $team_array[$data['a_id']];
    }

    //获取器增加b_name字段
    public function getBNameAttr($value, $data)
    {
        $team_array = array();
        $team_list = Db::name('team')->select();
        //处理球队表为数组
        foreach ($team_list as $team){
            $team_array[$team['id']] = $team['name'];
        }
        return $team_array[$data['b_id']];
    }

    //获取器增加a_logo字段
    public function getALogoAttr($value, $data)
    {
        $team_array = array();
        $team_list = Db::name('team')->select();
        //处理球队表为数组
        foreach ($team_list as $team){
            $team_array[$team['id']] = $team['image'];
        }
        return $team_array[$data['a_id']];
    }

    //获取器增加b_logo字段
    public function getBLogoAttr($value, $data)
    {
        $team_array = array();
        $team_list = Db::name('team')->select();
        //处理球队表为数组
        foreach ($team_list as $team){
            $team_array[$team['id']] = $team['image'];
        }
        return $team_array[$data['b_id']];
    }

}