<?php
/**
 * 监控服务 ws http 8811
 * Created by PhpStorm.
 * User: Admin
 * Date: 2019/6/24
 * Time: 14:29
 */

class Server
{
    const PORT = 8811;

    public function port(){
        $shell = "netstat -anp 2>/dev/null | grep ".self::PORT." | grep LISTEN | wc -l";
        $result = shell_exec($shell);
        if($result != 1){
            //发送报警 邮件 短信 -- 调用框架方法，同样需要加载框架项目文件
            ///todo
            echo date("Ymd H:i:s")." error".PHP_EOL;
        }else{
            echo date("Ymd H:i:s")." success".PHP_EOL;
        }
    }
}
swoole_timer_tick(2000, function ($timer_id){
    (new Server())->port();
    echo "timer start:".PHP_EOL;
});