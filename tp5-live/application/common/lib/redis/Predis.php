<?php
namespace app\common\lib\redis;

class Predis
{
	public $redis = "";
	/**
	 * 定义单例模式的变量
	 * @var null
	 */
	private static $_instance = null;

	private function __construct(){
		$this->redis = new \Redis();
		$result = $this->redis->connect(config('redis.host'), config('redis.port'), config('redis.timeout'));
		if ($result === false) {
			throw new \Exception("redis connect error");
		}
	}

    /**
     * @return Predis|null
     */
	public static function getInstance(){
		if (empty(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

    /**
     * @param $key
     * @param $value
     * @param int $time
     * @return bool|string
     * @throws \Exception
     */
	public function set($key, $value, $time=0){
        try{
            if(!$key){
                return '';
            }
            if(is_array($value)){
                $value = json_encode($value);
            }
            if(!$time){
                return $this->redis->set($key, $value);
            }
            return $this->redis->setex($key, $time, $value);
        }catch (\Exception $e){
            throw new \Exception("redis set error");
        }
	}

    /**
     * @param $key
     * @return bool|string
     */
	public function get($key){
		if (!$key) {
			return "";
		}
		return $this->redis->get($key);
	}

    /**
     * @param $name
     * @param $arguments
     * @return string
     */
    public function __call($name, $arguments)
    {
        if ($arguments){
            if (isset($arguments[1]) && is_array($arguments[1])){
                return $this->redis->$name($arguments[0], ...$arguments[1]);
            }
            return $this->redis->$name(...$arguments);
        }
        return "without arguments";
    }

}