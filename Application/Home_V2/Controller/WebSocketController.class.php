<?php

namespace Home_V2\Controller;

use Think\Controller;
use Workerman\Worker;
use Workerman\Lib\Timer;
use Clue\React\Redis\Factory;
use Clue\React\Redis\Client;
use React\Promise\Timer\TimeoutException;

class WebSocketController extends Controller {
	const HEARTBEAT_TIME = 50;
	public function runCli() {
		$context = array (
				'ssl' => array (
						'local_cert' => '/cert/testtaxisan.com_public.crt',
						'local_pk' => '/cert/testtaxisan.com.key',
						'verify_peer' => false
				)
		);

		// 创建websocket进程
		$worker = new Worker ( "websocket://0.0.0.0:2020", $context );
		$worker->count = 3;
		$worker->transport = 'ssl';
			
		$worker->onWorkerStart = function ($w) {
			Timer::add ( 1, function () use ($w) {
				$time_now = time ();
				foreach ( $w->connections as $connection ) {
					if (empty ( $connection->lastVisitTime )) {
						$connection->lastVisitTime = $time_now;
						continue;
					}
					if ($time_now - $connection->lastVisitTime > self::HEARTBEAT_TIME) {
						$connection->close ();
					}
				}
			} );
		};

		$worker->onConnect = function ($conn) {
			$conn->onWebSocketConnect = function ($conn) {
				$conn->requestUri = $_SERVER ['REQUEST_URI'];
				$conn->queryString = $_SERVER ['QUERY_STRING'];
			};
		};
		// 约定：司机发送数据格式为：{"driverId":"1"}
		$worker->onMessage = function ($conn, $data) {			
			$conn->lastVisitTime = time ();
			if ($data != 1) {
				switch (explode ( "?", $conn->requestUri ) [0]) {
					case "/cache" :
						$loop = Worker::getEventLoop ();
						$factory = new Factory ( $loop );
						$factory->createClient ( "redis://:" . C ( 'redisAuth' ) . "@" . C ( "redisIp" ) . ":" . C ( "redisPort" ) )->then ( function (Client $client) use ($conn,$data) {
							$data = json_decode($data,true,JSON_UNESCAPED_UNICODE);
							$driverId = $data['driverId'];
							$conn->uid = $driverId;
							$conn->userData = $data;
							$conn->client = $client;
							$client->subscribe ( 'channel_' . $driverId )->then ( function () use ($driverId, $conn,$data) {
								$conn->send('{"type":"start","status":"success","msg":"司机' . $driverId . '构建信道成功"}');
							    $log = PHP_EOL . '/************************************************************************' . PHP_EOL . "时间：\t\t" . date ( "Y-m-d H:i:s", time () ) . PHP_EOL . "访问URI：\t\t" . $conn->requestUri . PHP_EOL . "查询参数：\t\t" . urldecode ( $conn->queryString ) . PHP_EOL . "传入数据：\t\t" . json_encode ( $data, JSON_UNESCAPED_UNICODE ) . PHP_EOL . "传出：\t\t" . "司机:" . $driverId . "成功订阅通道" . PHP_EOL . '*************************************************************************/' . PHP_EOL;
								$this->log ( $log );
							}, function (\Exception $e) use ($driverId, $client,$data) {
								$client->close ();
								$log = PHP_EOL . '/************************************************************************' . PHP_EOL . "时间：\t\t" . date ( "Y-m-d H:i:s", time () ) . PHP_EOL . "访问URI：\t\t" . $conn->requestUri . PHP_EOL . "查询参数：\t\t" . urldecode ( $conn->queryString ) . PHP_EOL . "传入数据：\t\t" . json_encode ( $data, JSON_UNESCAPED_UNICODE ) . PHP_EOL . "传出：\t\t" . "司机:" . $driverId . '监听通道失败：' . PHP_EOL . "原因:\t\t" . $e->getMessage () . PHP_EOL . '*************************************************************************/' . PHP_EOL;
								$this->log ( $log );
							} );
							$client->on ( 'message', function ($channel, $message) use ($conn, $data) {
								$conn->send ($message);
								$msg = json_decode($message,true,JSON_UNESCAPED_UNICODE);								
								$log = PHP_EOL . '/************************************************************************' . PHP_EOL . "时间：\t\t" . date ( "Y-m-d H:i:s", time () ) . PHP_EOL . "访问URI：\t\t" . $conn->requestUri . PHP_EOL . "查询参数：\t\t" . urldecode ( $conn->queryString ) . PHP_EOL . "传入数据：\t\t" . json_encode ( $data, JSON_UNESCAPED_UNICODE ) . PHP_EOL . "传出：\t\t" . $message . PHP_EOL . '*************************************************************************/' . PHP_EOL;								
								$this->log ( $log );
							} );
						}, function (\Exception $e)use($data){
							$log = PHP_EOL . '/************************************************************************' . PHP_EOL . "时间：\t\t" . date ( "Y-m-d H:i:s", time () ) . PHP_EOL . "访问URI：\t\t" . $conn->requestUri . PHP_EOL . "查询参数：\t\t" . urldecode ( $conn->queryString ) . PHP_EOL . "传入数据：\t\t" . json_encode ( $data, JSON_UNESCAPED_UNICODE ) . PHP_EOL . "传出：\t\t" . 'Init Redis CLI Failed：' . PHP_EOL . "原因:\t\t" . $e->getMessage () . PHP_EOL . '*************************************************************************/' . PHP_EOL;
							$this->log ( $log );
						} );
						return;
						break;
					default :
						break;
				}
			}
			$conn->send ( '{"type":"heartbeat","status":"success","msg":"心跳包收到成功"}' );
		};

		$worker->onClose = function ($conn) {
			$conn->client->unsubscribe ( 'channel_' . $conn->uid );
			$conn->client->close();
			$log = PHP_EOL . '/************************************************************************' . PHP_EOL . "时间：\t\t" . date ( "Y-m-d H:i:s", time () ) . PHP_EOL . "访问URI：\t\t" . $conn->requestUri . PHP_EOL . "查询参数：\t\t" . urldecode ( $conn->queryString ) . PHP_EOL . "传入数据：\t\t" . json_encode ( $conn->userData, JSON_UNESCAPED_UNICODE ) . PHP_EOL . "传出：\t\t" . "司机{$conn->uid}号断开连接" . PHP_EOL . '*************************************************************************/' . PHP_EOL;
			$this->log ( $log );
		};

		$worker->onError = function ($conn, $code, $msg) {
			$log = PHP_EOL . '/************************************************************************' . PHP_EOL . "时间：\t\t" . date ( "Y-m-d H:i:s", time () ) . PHP_EOL . "访问URI：\t\t" . $conn->requestUri . PHP_EOL . "查询参数：\t\t" . urldecode ( $conn->queryString ) . PHP_EOL . "传入数据：\t\t" . json_encode ( $conn->userData, JSON_UNESCAPED_UNICODE ) . PHP_EOL . "传出：\t\t" . "司机{$conn->uid}通信连接出现问题(${code})：" . $msg . PHP_EOL . '*************************************************************************/' . PHP_EOL;
			$this->log ( $log );
		};
        
		//定时器
		$worker2 = new Worker("http://0.0.0.0:2121");
		$worker2->count = 3;
		$worker2->onMessage = function($conn,$data){
		    extract($_POST);
		    $redis = new \Redis();
		    $redis->connect(C("redisIp"),C("redisPort"));
		    $redis->auth(C('redisAuth'));
		    if(intval($redis->publish("channel_" . $driverId,$message)) == 0){
		        $times = 1;
		        $insertId = $this->save2db($driverId, $type, $message, date("Y-m-d H:i:s",time()),$interval);		        
		        $timer = Timer::add($interval, function()use($redis,$driverId,&$times,$counts,$message,&$timer,$insertId){
		            $this->update2db($insertId);
		            if(($times >= $counts) || intval($redis->publish("channel_" . $driverId,$message)) > 0){		                
		                $redis->close();
		                Timer::del($timer);		                
		            }
		            $times++;
		        });
		        $conn->send("failed");
			return;
		    }else{
		        $this->save2db($driverId, $type, $message, date("Y-m-d H:i:s",time()),$interval);
		        $conn->send("success");
		    }
		    $redis->close();
		};
	
		Worker::runAll ();
	}
	private function log($log) {
		$directory = 'websocket_log';
		if (! is_dir ( $directory ))
			mkdir ( $directory );
		$fileName = $directory . DIRECTORY_SEPARATOR . date ( "Y-m-d", time () ) . ".log";
		$fd = fopen ( $fileName, "a" );
		if (flock ( $fd, LOCK_EX )) {
			fwrite ( $fd, $log );
			fflush ( $fd );
			flock ( $fd, LOCK_UN );
		}
		fclose ( $fd );
	}
	
	private function save2db($driverId,$type,$message,$sendTime,$resentInterval){
	    $msg = M('b_push_message');
	    $msg->driverId = $driverId;
	    $msg->type = $type;
	    $msg->message = $message;
	    $msg->lastSendTime = $sendTime;
	    $msg->resentInterval = $resentInterval;
	    return $msg->add();
	}
	
	private function update2db($insertId){
	    $msg = M('b_push_message')->where("id = $insertId");
	    $msg->lastSendTime = date("Y-m-d H:i:s",time());
	    $msg->setInc('resentTimes',1);
	    $msg->save();
	    $msg->clearData();
	}
}
