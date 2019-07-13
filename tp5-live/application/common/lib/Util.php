<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/5/27
 * Time: 16:47
 */
namespace app\common\lib;

class Util
{
    public static function show($status, $message = '', $data = []){
        $result = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];
        return json_encode($result);
    }
}