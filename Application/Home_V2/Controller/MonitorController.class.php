<?php

namespace Home_V2\Controller;

use Think\Controller;

class MonitorController extends Controller {
	
	const DRIVER_POINTS_DB = 0;
	const AWAIT_DRIVER_POINTS = "await_driver_points";
	const AWAIT_ORDER_POINTS = "await_order_points";
	const MASTER_DRIVER_POINTS = "master_driver_points";
	const ORDER_CACHE_15 = 1;
	const ORDER_CACHE_90 = 2;
	const BLACKLIST_DRIVER_DB = 3;
	const DELAY_TIME = 5;//当前假设间隔为5
	const ORDER_PUBLIC_CACHE = 4;//订单全生命周期缓冲区
	const MESSAGE_PUBLIC_CACHE = 5;//15s消息缓冲区
	const API_SPEED_CACHE = 6;//API接口速率控制缓冲区
	const BROADCASTING_LIST_COUNT = 10;//广播获取条数
	
	private $redis = null;
	private $concurrentTtl = 6;
	
	public function __construct(){
		$this->redis = new \Redis();
		$this->redis->connect(C('redisIp'),C('redisPort'));
		$this->redis->auth(C('redisAuth'));
	}
	
	public function __destruct(){
		$this->redis->close ();
	}
	
	/**
	 * 命令行运行环境
	 * php index.php Home_V2/cli
	 */
	public function runCli() {
		ini_set('default_socket_timeout', -1);
		try {
			$this->redis->setOption(\Redis::OPT_READ_TIMEOUT,-1);
			$this->redis->psubscribe(['__keyevent@1__:*','__keyevent@2__:*','__keyevent@3__:*','__keyevent@7__:*','__keyevent@15__:*'],[$this,'listener']);
		} catch(\Exception $ex){
			echo $ex->getMessage ();
		}
	}
	
	/**
	 * 事件监听器
	 * @param object $instance
	 * @param string $pattern
	 * @param string $chan
	 * @param string $message
	 */
	public function listener($instance, $patern,$chan, $message) {
		try{
			$redis = new \Redis();
			$redis->connect(C('redisIp'),C('redisPort'));
			$redis->auth(C('redisAuth'));
			switch ($chan){
				case "__keyevent@1__:del"://乘客取消订单后，司机立即重新回到await等待区
					//移入大厅
					$driverId = explode(":", $message)[0];
					$addrId = $this->getDriverBelongs2AreaId($driverId);
					
					$driver = M("s_driver");
					if($driver->where("id = $driverId")->getField("state") == "off")
						break;
					
					$randomInt = random_int(0, 999999);
					while(true){				
						$redis->select ( self::MESSAGE_PUBLIC_CACHE );
						if($redis->set("concurrent6",$randomInt,['nx','ex'=>$this->concurrentTtl])){
							$redis->select(self::BLACKLIST_DRIVER_DB);
							if($redis->exists($driverId)){
								$redis->select ( self::MESSAGE_PUBLIC_CACHE );
								if($redis->get("concurrent6") == $randomInt)
								    $redis->del("concurrent6");
								break;
							}
					$redis->select(self::DRIVER_POINTS_DB);
					$score = $redis->zScore(self::MASTER_DRIVER_POINTS,$driverId);
					$redis->zAdd(self::AWAIT_DRIVER_POINTS . "_$addrId",$score,$driverId);
							$redis->select ( self::MESSAGE_PUBLIC_CACHE );
							if($redis->get("concurrent6") == $randomInt)
							    $redis->del("concurrent6");
							break;
						}
					}
			
					break;
				case "__keyevent@7__:expired"://司机心跳区事件
				    list($driverId,$areaId) = explode(":", $message);
				    $redis->select(self::DRIVER_POINTS_DB);
				    $redis->zRem(self::AWAIT_DRIVER_POINTS . "_$areaId",$driverId);
				    $driver = M("s_driver")->where("id = $driverId");
				    $data = $driver->find();
				    if($data['state'] == "stand"){
				        $driver->state = "off";
				        $driver->save();
				    }
				    $driver->clearData();
				    break;
				case "__keyevent@1__:expired"://15s缓冲区事件
					
					$driverId = explode(":", $message)[0];
					
					$order = M("b_order");
					$isOrder = $order->where("d_id = $driverId and state in ('new','on','arrived','active','wait_pay') and d_confirmation = 'N'")->find();
					if(!empty($isOrder))
						break;	
					
					$driver = M("s_driver");
					$driver->where("id = $driverId")->save([state => 'stand']);
					
						
					
					$addrId = $this->getDriverBelongs2AreaId($driverId);
					$blacklistTime = M("m_address_config")->where("address_id = " . $addrId)->field("blacklist_time")->find()['blacklist_time'];
					$hallTime = M("m_address_config")->where("address_id = " . $addrId)->field("hall_time")->find()['hall_time'];
					
					
					$this->recursion_switch2black($redis, $driverId, $blacklistTime);
					
					$redis->select(self::ORDER_PUBLIC_CACHE);
					$data = json_decode($redis->get($message),true,JSON_UNESCAPED_UNICODE);
					
					$redis->select(self::ORDER_CACHE_90);
					$redis->setex($message,$hallTime + self::DELAY_TIME,$data['address_id']);
					$redis->rawCommand('geoadd', self::AWAIT_ORDER_POINTS . "_${data['address_id']}", $data['longitude_origin'],$data['latitude_origin'], $message);
					
					
					$redis->select(self::MESSAGE_PUBLIC_CACHE);
					$redis->setex($message,1800,$data['address_id']);
					
					break;
				case "__keyevent@2__:expired"://90s缓冲区事件					
				    @list($driverId,$passengerId,$no,$addrId) = explode(":", $message);
				    if($driverId == "passenger"){
				        $redis->select(self::ORDER_CACHE_90);
				        $redis->zRem(self::AWAIT_ORDER_POINTS . "_$addrId",$message);
				        $openid = M("s_user")->where("id = " . $passengerId)->find()['openid'];
				        Customer_Service($openid,"附近没有司机接单，请换地点或稍后重试。",'gh_008a50db02b8');//gh_78052d300081
				    }else{
				        $redis->select(self::ORDER_PUBLIC_CACHE);
				        $data = json_decode($redis->get($message),true,JSON_UNESCAPED_UNICODE);
				        
				        $redis->select(self::MESSAGE_PUBLIC_CACHE);
				        $addrId = $redis->get($message);
				        $redis->select(self::ORDER_CACHE_90);
				        $redis->zRem(self::AWAIT_ORDER_POINTS . "_$addrId",$message);
				        
				        $arr = explode(":", $message);
				        save2reservation_exit($arr[2]);
				        $openid = M("s_user")->where("id = " . $data['u_id'])->find()['openid'];
				        
				        Customer_Service($openid,"附近没有司机接单，请换地点或稍后重试。",'gh_008a50db02b8');//gh_78052d300081
				        //固化订单失效状态到Mysql
				        //删除ORDER_PUBLIC_CACHE订单信息
				        //删除MESSAGE_PUBLIC_CACHE信息
				    }				    				    			
					break;
				case "__keyevent@3__:expired"://5分钟黑名单事件
					$driver = M("s_driver");
					$data = $driver->where("id = $message and state = 'stand'")->find();
					if(empty($data))
						break;
					$areaId = $this->getDriverBelongs2AreaId($message);
					$redis->select(self::DRIVER_POINTS_DB);
					$pos = $redis->rawCommand('geopos', self::MASTER_DRIVER_POINTS, $message)[0];
					//由黑名单重新进入到等待区，会被重置为master坐标
					$redis->rawCommand('geoadd', self::AWAIT_DRIVER_POINTS . "_$areaId", $pos[0],$pos[1], $message);
					break;
				case '__keyevent@15__:expired'://db15测试服务不中断
					$monitor = M("m_monitor_list");
					$monitor->date = date("Y-m-d H:i:s",time());
					$monitor->add();
				default:
					break;
			}
			$redis->close();
		}catch(\Exception $ex){
			$fd = fopen(getcwd() . DIRECTORY_SEPARATOR . "listener.log", "a");
			$log = date("Y-m-d H:i:s",time()) . "=>" . $ex->getMessage() . PHP_EOL;
			fwrite($fd,$log);
			fclose($fd);
		}
	}  
	/**
	 * 获取司机运营所归属的运营区域ID号
	 * @param integer $driverId 司机ID
	 * @return int
	 */
	private function getDriverBelongs2AreaId($driverId){
		$driver = M("s_driver");
		return $driver->where("id = " . $driverId)->field("address_id")->find()['address_id'];
	}
	
	private function recursion_switch2black($redis,$driverId,$blacklistTime){
		
		$redis->select ( self::MESSAGE_PUBLIC_CACHE );
		$randomInt = random_int(0, 999999);
		if($redis->set("concurrent6",$randomInt,['nx','ex'=>$this->concurrentTtl])){
			$redis->select(self::BLACKLIST_DRIVER_DB);
			$redis->setex($driverId,$blacklistTime + self::DELAY_TIME,0);
			$redis->select ( self::MESSAGE_PUBLIC_CACHE );
			if($redis->get("concurrent6") == $randomInt)
			    $redis->del("concurrent6");
		}else {
			$redis->select ( self::MESSAGE_PUBLIC_CACHE );
			if($redis->get("concurrent6") == $randomInt)
			    $redis->del("concurrent6");
			$this->recursion_switch2black($redis, $driverId, $blacklistTime);
		}
	}
}