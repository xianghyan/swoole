<?php
/**
 * ws 优化 基础类库
 * User: yxh
 * Date: 2019/5/23
 * Time: 11:50
 */
class Http
{
    const HOST = "0.0.0.0";
    const PORT = 8811;

    public $http = null;
    public function __construct()
    {
        $this->http = new Swoole\Http\Server(self::HOST, self::PORT);

        $this->http->set([
            'enable_static_handler' => true,
            'document_root' => '/var/www/swoole/tp5-live/public/static',
            'worker_num' => 3,
            'task_worker_num' => 3,
        ]);
        $this->http->on("workerstart",[$this,"onWorkerStart"]);
        $this->http->on("request",[$this,"onRequest"]);
        $this->http->on("task",[$this,"onTask"]);
        $this->http->on("finish",[$this,"onfinish"]);
        $this->http->on("close",[$this,"onClose"]);

        $this->http->start();
    }

    /**
     * @param $server
     * @param $worker_id
     */
    public function onWorkerStart($server, $worker_id){
        // 加载基础文件
        require __DIR__ . '/../thinkphp/start.php';
    }

    /**
     * request回调
     * @param $request
     * @param $response
     */
    public function onRequest($request, $response){
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
        if (count($_POST) > 0) {
            $_POST = array();
        }
        if(isset($request->post)){
            foreach ($request->post as $k => $v){
                $_POST[$k] = $v;
            }
        }
        //http服务赋值到$_POST，用作其他位置调用
        $_POST['http_server'] = $this->http;
        //$response->cookie("singwa","xsssss",time()+1800);

        ob_start();
        // 执行应用并响应
        try{
            think\Container::get('app')->run()->send();
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
        $flog = $obj->$method($data['data']);

        return $flog; //告诉worker
    }

    public function onFinish($serv, $taskId, $data){
        echo "taskId:{$taskId}\n";
        echo "finish-data-success:{$data}\n";
    }

    /**
     * close
     * @param $ws
     * @param $fd
     */
    public function onClose($ws, $fd){
        echo "clientid:{$fd} closed\n";
    }
}

$obj = new Http();