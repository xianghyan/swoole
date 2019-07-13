<?php
/**
 * ws 优化 基础类库
 * User: yxh
 * Date: 2019/5/23
 * Time: 11:50
 */
class Ws
{
    const HOST = "0.0.0.0";
    const PORT = 8811;
    const CHART_PORT = 8812;

    public $ws = null;
    public function __construct()
    {
        $this->ws = new Swoole\WebSocket\Server(self::HOST, self::PORT);
        $port_chart = $this->ws->listen(self::HOST, self::CHART_PORT, SWOOLE_SOCK_TCP);

        $this->ws->set([
            'enable_static_handler' => true,
            'document_root' => '/var/www/swoole/tp5-live/public/static',
            'worker_num' => 3,
            'task_worker_num' => 3,
        ]);
        $this->ws->on("start",[$this,"onStart"]);
        $this->ws->on("open",[$this,"onOpen"]);
        $port_chart->on("open",[$this,"onChartOpen"]);
        $this->ws->on("message",[$this,"onMessage"]);
        $this->ws->on("workerstart",[$this,"onWorkerStart"]);
        $this->ws->on("request",[$this,"onRequest"]);
        $this->ws->on("task",[$this,"onTask"]);
        $this->ws->on("finish",[$this,"onfinish"]);
        $this->ws->on("close",[$this,"onClose"]);
        $port_chart->on("close",[$this,"onChartClose"]);

        $this->ws->start();
    }

    public function onStart($server){
        swoole_set_process_name("live_master");
    }

    /**
     * @param $server
     * @param $worker_id
     */
    public function onWorkerStart($server, $worker_id){
        //定义应用目录
        define('APP_PATH', __DIR__ . '/../../../application' . DIRECTORY_SEPARATOR);
        // 加载基础文件
        require __DIR__ . '/../../../thinkphp/start.php';

        //重启时删除redis中的client
        \app\common\lib\redis\Predis::getInstance()->delete(config('redis.live_game_key'));
        \app\common\lib\redis\Predis::getInstance()->delete(config('redis.chart_game_key'));
    }

    /**
     * request回调
     * @param $request
     * @param $response
     */
    public function onRequest($request, $response){
        if($request->server['request_uri'] == '/favicon.ico'){
            $response->status(404);
            $response->end();
            return;
        }

        if (count($_SERVER) > 0) {
            $_SERVER = array();
        }
        if(isset($request->server)){
            foreach ($request->server as $k => $v){
                $_SERVER[strtoupper($k)] = $v;
            }
        }
        if(isset($request->header)){
            foreach ($request->header as $k => $v){
                $_SERVER[strtoupper($k)] = $v;
            }
        }
        if (count($_GET) > 0) {
            $_GET = array();
        }
        if(isset($request->get)){
            foreach ($request->get as $k => $v){
                $_GET[$k] = $v;
            }
        }
        if (count($_FILES) > 0) {
            $_FILES = array();
        }
        if(isset($request->files)){
            foreach ($request->files as $k => $v){
                $_FILES[$k] = $v;
            }
        }
        if (count($_POST) > 0) {
            $_POST = array();
        }
        if(isset($request->post)){
            foreach ($request->post as $k => $v){
                $_POST[$k] = $v;
            }
        }

        //写入access日志
        $this->writeLog();
        //http服务赋值到$_POST，用作其他位置调用
        $_POST['http_server'] = $this->ws;
        //$response->cookie("singwa","xsssss",time()+1800);

        ob_start();
        // 执行应用并响应
        try{
            think\Container::get('app')->path(APP_PATH)->run()->send();
        }catch (\Exception $e){
            //todo
        }
        //echo "-action-".request()->action().PHP_EOL;
        $result = ob_get_contents();
        @ob_end_clean();
        //$response->end("sss".json_encode($request->get));
        $response->end($result);
    }

    public function onTask($serv, $taskId, $workerId, $data){
        $obj = new app\common\lib\task\Task();
        
        $method = $data['method'];
        $flog = $obj->$method($data['data'], $serv);

        return $flog; //告诉worker
    }

    public function onFinish($serv, $taskId, $data){
        echo "taskId:{$taskId}\n";
        echo "finish-data-success:{$data}\n";
    }

    /**
     * 监听ws连接事件
     * @param $ws
     * @param $request
     */
    public function onOpen($ws, $request){
        \app\common\lib\redis\Predis::getInstance()->zAdd(config('redis.live_game_key'), intval($request->get['game_id']), $request->fd);
        var_dump($request->fd);
    }

    /**
     * 监听ws-chart-port连接事件
     * @param $ws
     * @param $request
     */
    public function onChartOpen($ws, $request){
        \app\common\lib\redis\Predis::getInstance()->zAdd(config('redis.chart_game_key'), intval($request->get['game_id']), $request->fd);
        var_dump($request->fd);
    }

    /**
     * 监听ws消息事件
     * @param $ws
     * @param $frame
     */
    public function onMessage($ws, $frame){
        echo "ser-push-message:{$frame->data}\n";
        $ws->push($frame->fd,"server-push:".date("Y-m-d H:i:s"));
    }

    /**
     * close
     * @param $ws
     * @param $fd
     */
    public function onClose($ws, $fd){
        \app\common\lib\redis\Predis::getInstance()->zRem(config('redis.live_game_key'), $fd);
        echo "clientid:{$fd} closed\n";
    }

    /**
     * close
     * @param $ws
     * @param $fd
     */
    public function onChartClose($ws, $fd){
        \app\common\lib\redis\Predis::getInstance()->zRem(config('redis.chart_game_key'), $fd);
        echo "clientid:{$fd} closed\n";
    }

    /**
     * 写入请求日志
     */
    public function writeLog(){
        $data = array_merge(['date' => date('Ymd H:i:s')], $_GET, $_POST, $_SERVER);
        $logs = "";
        foreach ($data as $key => $value){
            $logs .= $key.": ".$value." ";
        }
        \Swoole\Coroutine::writeFile(APP_PATH.'../runtime/log/'.date('Ym').'/'.date('d').'_access.log', $logs.PHP_EOL, FILE_APPEND);
    }
}

new Ws();