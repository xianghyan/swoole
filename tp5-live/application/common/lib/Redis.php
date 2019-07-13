<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/5/27
 * Time: 19:31
 */
namespace app\common\lib;

class Redis
{
    /**
     * 验证码redis key的前缀
     * @var string
     */
    public static $pre = "sms_";
    /**
     * 用户user pre前缀
     * @var string
     */
    public static $userpre = "user_";

    /**
     * 存储验证码 redis key
     * @param $phone
     * @return string
     */
    public static function smsKey($phone){
        return self::$pre.$phone;
    }

    /**
     * 用户key
     * @param $phone
     * @return string
     */
    public static function userKey($phone){
        return self::$userpre.$phone;
    }
}