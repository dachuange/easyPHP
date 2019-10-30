<?php

/**
 * 派单文件
 * @desc 派单业务处理，包括：
 * 1.轨迹上报，实时更新
 * 2.监听者
 * 3.获取乘客所在行政区
 * 4.乘客检查是否存在未完成订单
 * 5.固化信息到Mysql
 * 6.司机获取大厅中的订单列表
 * 7.司机在大厅接单
 * 8.司机接系统指派单
 * 9.拒派单时司机转入5分钟黑名单
 * 10.司机检查是否存在未完成订单
 * 11.根据订单号获取未完成订单信息
 * 12.乘客下单
 * 13.司机听单
 * 14.根据司机ID获取司机所属运营区域
 * 15.接口3s限速
 * 16.司机取消听单
 * 17.乘客取消预约单
 */
namespace Home_V2\Controller;

use Think\Controller;
use Home_V2\Controller\DispatchInterface;

/**
 * 派单控制器设计
 *
 * @author Jason <2358357748@qq.com>
 * @version 1.0
 */
class DispatchController extends Controller implements DispatchInterface {
	const DRIVER_POINTS_DB = 0;
	const AWAIT_DRIVER_POINTS = "await_driver_points";
	const AWAIT_ORDER_POINTS = "await_order_points";
	const MASTER_DRIVER_POINTS = "master_driver_points";
	const ORDER_CACHE_15 = 1;
	const ORDER_CACHE_90 = 2;
	const BLACKLIST_DRIVER_DB = 3;
	const DELAY_TIME = 5; // 当前假设间隔为5
	const ORDER_PUBLIC_CACHE = 4; // 订单全生命周期缓冲区
	const MESSAGE_PUBLIC_CACHE = 5; // 15s消息缓冲区
	const API_SPEED_CACHE = 6; // API接口速率控制缓冲区
	const BROADCASTING_LIST_COUNT = 11; // 广播获取条数
	const DRIVER_HEART_ALIVE_AREA = 7;//司机心跳区域
	const DRIVER_HEART_ALIVE_TIME = 120;//司机心跳生命时间
	private $redis = null;
	private $randomInt = 0,$concurrentTtl = 6;
	public function __construct() {
		parent::__construct();
		$this->redis = new \Redis ();
		$this->redis->connect ( C ( 'redisIp' ), C ( 'redisPort' ) );
		$this->redis->auth ( C ( 'redisAuth' ) );
		$this->randomInt = random_int(1, 999999);
	}
	public function __destruct() {
		parent::__destruct();
		$this->redis->close ();
	}

	/**
	 * 司机取消听单
	 *
	 * @param int $driverId
	 *        	司机ID
	 * @return string
	 */
	public function driverCancelPrepare() {
		$driverId = I("post.driverId");
                $token = I("token");  //司机端的token
                if(empty($driverId)){
                    $this->ajaxReturn ( [ 
                        "message" => "driverId为空",
                        "message_code" => -1
                    ] );
                }
                if($token != S("drive_token_{$driverId}")){
                    $msg['message_code'] = -101;
                    $msg['message'] = "token错误";
                    $this->ajaxReturn($msg);
                    exit();
                }
                $stroke_id = I("post.stroke_id");
                if(empty($stroke_id)){
                    $this->ajaxReturn ( [ 
                        "message" => "stroke_id",
                        "message_code" => -1
                    ] );
                }
                
                //干掉生存计时缓存
                S("driverid_survive_{$driverId}",NULL);

                $m = M();
                $m->startTrans();   //开启事务
		$areaId = $this->getDriverBelongs2AreaId ( $driverId );
		$this->redis->select ( self::DRIVER_POINTS_DB );
                $up = driver_state_up($driverId,"off");
                $now = NOW_TIME;
                $sql0 = <<<SQL
                    update d_online_record set edate=$now,line_time=($now-sdate) 
                    where  id = {$stroke_id} 
SQL;
                $up1 = M()->execute($sql0);
                $this->redis->zRem ( self::AWAIT_DRIVER_POINTS . "_$areaId", $driverId);
                $result = $this->redis->zScore ( self::AWAIT_DRIVER_POINTS . "_$areaId", $driverId );
                #if (!$result && $up>0&&$up1>0){
                if ($up1>0){
                    $m->commit();
                    $this->ajaxReturn ( [ 
                        "message" => "成功取消听单",
                        "message_code" => 0
                    ] );
                }  else {
                    $m->rollback();
                    $this->ajaxReturn ( [
                        "message" => "取消听单失败",
                        "message_code" => -1
                    ] );
                }
	}
	/**
	 * 乘客取消预约单
	 *
	 * @param int $uid
	 *        	乘客ID
	 * @return string
	 */
	public function passengerCancelOrder() {

		$userinfo = session ( "userinfo" );
		$uid = $userinfo ['u_id'];
                if(empty($uid)){
                    $this->ajaxReturn([
                        "message" => "身份丢失",
                        "message_code" => -1,
                    ]);
                }
		
        $this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
        if($this->redis->set("concurrent",$this->randomInt,['nx','ex'=>$this->concurrentTtl])){
            // 2.检查正式单情势
            if ($order = $this->passengerCheckExistOrders ( $uid )) {
                switch ($order ['state']) {
                    case "on" :
                        $orderfee = S ( "order_fee_{$order['o_id']}" );
                        $start_time = NOW_TIME - $orderfee ['on_time'];
                        $msg ['message_code'] = 1; // 等待接驾
                        $msg ['message'] = "on";
                        $msg ['link'] = "/Orderauto/order_on/o_id/{$order['o_id']}/start_time/{$start_time}";
                        $this->ajaxReturn ( $msg );
                        break;
                    case "active" :
                        $orderfee = S ( "order_fee_{$order['o_id']}" );
                        $start_time = NOW_TIME - $orderfee ['active_time'];
                        $msg ['message_code'] = 2; // 行程中
                        $msg ['message'] = "active";
                        $msg ['link'] = "/Orderauto/order_active/o_id/{$order['o_id']}/start_time/{$start_time}";
                        $this->ajaxReturn ( $msg );
                        break;
                    case "wait_pay" :
                        $msg ['message_code'] = 3; // 等待支付
                        $msg ['message'] = "wait_pay";
                        $msg ['link'] = "/Orderauto/order_deatil?o_id={$order['o_id']}";
                        $this->ajaxReturn ( $msg );
                        break;
                }
            }
            
            // 1.检查15s,90s预制单情势
            try {                 
                $this->redis->select ( self::ORDER_CACHE_15 );
                $iterator = null;
                
                while ( true ) {
                    
                    $data = $this->redis->scan( $iterator, '*:' . $uid . ':*', 100 );
                    
                    if ($data == false && $iterator == 0)
                        break;
                    foreach ( $data as $key ) {
                        $this->redis->expire ( $key, 0 );
                        list($driverId,$uid,$no) = explode(":", $key);
                        // 系统派单之后的取消固化
                        save2reservation_cancel ( $key );
                        $drivers[] = $driverId;
                    }
                }
                
                
                //这里执行90s取消递归
                $this->recursion_del_90($uid);
                
                $this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
                if($this->redis->get("concurrent") == $this->randomInt)
                    $this->redis->del("concurrent");
                
                //将乘客取消系统派单消息推送给司机
                $message = [
                    "type" => "passenger_cancel_reservation_order",
                    "message" => "乘客已取消预约行程",
                    "message_code" => "-1",
                    "cont" => [
                        "time" => date('Y-m-d H:i:s',time()),
                    ],
                ];
                foreach($drivers as $v){
                    redis_publish($v, $message, 2, 3);
                }
                    
                $this->ajaxReturn ( [
                    "message" => "成功取消预约单",
                    "message_code" => 0
                ] );
                
            } catch ( \Exception $ex ) {
                $this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
                if($this->redis->get("concurrent") == $this->randomInt)
                    $this->redis->del("concurrent");
                $this->ajaxReturn ( [
                    "message" => "取消预约单失败",
                    "message_code" => - 1
                ] );
            }
        }else
            $this->ajaxReturn([
                "message" => "并发冲突",
                "message_code" => -1,
            ]);	
                    
	}

	/**
	 * 根据乘客坐标，获取相应片区
	 *
	 * @param double $longitude
	 *        	经度
	 * @param double $latitude
	 *        	纬度
	 * @return int id
	 */
	public function fetchAddress($longitude, $latitude) {
		$address_id = remote_addressid ( $latitude, $longitude );
		if ($address_id === FALSE) {
			$address_id = 10; // 其他
		}
		return $address_id;
	}
	/**
	 * 乘客检查正式订单中是否包含已存在订单
	 *
	 * {@inheritdoc}
	 * @see \Home_V2\Controller\DispatchInterface::passengerCheckExistOrder()
	 */
	public function passengerCheckExistOrders($uid) {
		$order = M ( "b_order" );
		$data = $order->where ( "u_id = $uid and state not in ('end','cannal')" )->find ();
		if (empty ( $data ))
			return null;
		else
			$data ['o_id'] = $data ['id'];
		return $data;
	}
	/**
	 * 固化入Mysql数据库
	 *
	 * {@inheritdoc}
	 * @see \Home_V2\Controller\DispatchInterface::save2Mysql()
	 */
	public function save2Mysql($data, $d_id,$source) {
		$m = M ();
		$m->startTrans (); // 开启事务
                $site = S("drive_{$d_id}"); //司机位置
                $id = creat_order ( $data ['u_id'], $d_id, $data ['appellation_origin'], $data ['latitude_origin'], $data ['longitude_origin'], $data ['appellation_destination'], $data ['latitude_destination'], $data ['longitude_destination'], $source, $data ['address_id'] );
		if ($id > 0) {
			// 开始跳过NEW，直接到ON
			$orderx ['on_time'] = NOW_TIME;
			$orderx ['state'] = 'on';
			S ("order_fee_{$id}", $orderx ); // 更新缓存
                        S ("order_driver_{$d_id}",1);  //一个用于判断是否有单的并发判断，在pull15里删除
			$o_up = order_state_up ( $id, 'on' );

			$addsite = thorough_order ( $id, $data ['latitude_origin'], $data ['longitude_origin'], $site['lat'], $site['lng'] );
			if ($o_up > 0 && $addsite > 0) {
				$info = GetOrderInfo ( $id );
				$info ['sdate'] = strtotime ( $info ['sdate'] );
				$s_site = Convert_GCJ02_To_BD09 ( $info ['s_lat'], $info ['s_lng'] ); // 腾讯转百度
				$e_site = Convert_GCJ02_To_BD09 ( $info ['e_lat'], $info ['e_lng'] );
				$info ['s_lat'] = $s_site ['lat'];
				$info ['s_lng'] = $s_site ['lng'];
				$info ['e_lat'] = $e_site ['lat'];
				$info ['e_lng'] = $e_site ['lng'];

				$m->commit ();
				save2reservation ( $data ['reservation_no'] );
				return $info;
				return;
			} else {
				$m->rollback ();
				return FALSE;
			}
		} else {

			$m->rollback ();
			return FALSE;
		}
	}
	public function doTest() {
	    //支付成功同时推送给司机
	    $message = [
	        "type" => "passenger_wxpay_success",
	        "message" => "乘客已成功支付",
	        "message_code" => "0",
	        "cont" => [
	            "orderId" => '11116156289391659',
	            "passengerPhone" => 13840753831,
	            "amount" => 12,
	            "time" => date('Y-m-d H:i:s',time()),
	        ],
	    ];
	    
	    redis_publish(13, $message, 2, 3);
	    /* $msg = [
	        "type" => "passenger_order",
	        "driverId" => 33,
	        "cont" => [
	            "appellation_origin" => 1,
	            "appellation_destination" => 2,
	            "distance" => 1.2,
	            "time" => date('Y-m-d H:i:s',time()),
	            "key" => 'x:y:z',
	        ],
	        "message" => "乘客以下单，请及时接单",
	        "message_code" => "0",
	    ];
	    redis_publish($msg['driverId'], $msg, 2, 5); */
/* 	    $longitude_origin = I('post.longitude');
	    $latitude_origin = I('post.latitude');
	    
	    $pos = Convert_BD09_To_GCJ02($latitude_origin,$longitude_origin);
	    $longitude_origin = $pos['lng'];
	    $latitude_origin = $pos['lat'];
	    
	    $this->redis->select(self::DRIVER_POINTS_DB);
	    print_r($this->redis->rawCommand ( 'georadius', 'await_driver_points_3', $longitude_origin, $latitude_origin, 2, 'km', 'WITHDIST', 'WITHCOORD', 'count', 1, 'asc' ));
 */	}
	
	
	/**
	 * 司机大厅获取订单列表
	 * 返回大厅中适合该司机的列表
	 *
	 * @param integer $driverId
	 *        	司机ID
	 * @param double $longitude
	 *        	经度
	 * @param double $latitude
	 *        	纬度
	 * @return string
	 */
	public function driverGrabOrderList() {
		$driverId = I ( "post.driverId" );
		/* $longitude = I ( 'post.longitude' );
		$latitude = I ( 'post.latitude' ); */
		
		$this->redis->select(self::DRIVER_POINTS_DB);
		$pos = $this->redis->rawCommand('geopos', self::MASTER_DRIVER_POINTS, $driverId)[0];
		$longitude = $pos[0];
		$latitude = $pos[1];
		
		$areaId = $this->getDriverBelongs2AreaId ( $driverId );
		$broadcastingKilometers = M ( "m_address_config" )->where ( "address_id = " . $areaId )->field ( "broadcasting_kilometers" )->find () ['broadcasting_kilometers'];
		
		
		$this->redis->select ( self::ORDER_CACHE_90 );
		$data = $this->redis->rawCommand ( 'georadius', self::AWAIT_ORDER_POINTS . "_$areaId", $longitude, $latitude, $broadcastingKilometers, 'km', 'WITHDIST', 'WITHCOORD', 'count', self::BROADCASTING_LIST_COUNT, 'asc' );
		$this->redis->select ( self::ORDER_PUBLIC_CACHE );
		if (! empty ( $data )) {
			$r = [];
			foreach ( $data as $v ) {
				if(substr($v [0],0,strpos($v [0],":")) == $driverId)//排除掉自己拒绝的单子
					continue;
				$result ['key'] = $v [0];
				$data2 = json_decode ( $this->redis->get ( $v [0] ), true, JSON_UNESCAPED_UNICODE );
				if(empty($data2))
					continue;
				$result ['appellation_destination'] = $data2 ['appellation_destination'];
				$result ['appellation_origin'] = $data2 ['appellation_origin'];
				$result ['distance'] = round($v [1],1);
				// $result['longitude'] = $v[2][0];
				// $result['latitude'] = $v[2][1];
				$r [] = $result;
			}
			$this->ajaxReturn ( [ 
					"message" => "成功获取广播列表",
					"message_code" => "0",
					"cont" => $r
			] );
		} else
			$this->ajaxReturn ( [ 
					"message" => "未找到适宜的订单列表",
					"message_code" => "-1"
			] );
	}

	/**
	 * 司机90s内于大厅接单
	 *
	 * @param integer $driverId
	 *        	司机ID
	 * @param string $key
	 *        	预制单键名，如："8:9:1c1d433ed5c799768fce668cd4094d92"
	 * @return string
	 */
	public function driverAcceptOrder_90() {
		$driverId = I ( "post.driverId" );
		$key = I ( "post.key" );
                $token = I("token");  //司机端的token
		if(empty($driverId)){
            $msg['message_code'] = -402;
            $msg['message'] = "d_id为空";
            $this->ajaxReturn($msg);
            exit();
        }
        if($token != S("drive_token_{$driverId}")){
            $msg['message_code'] = -101;
            $msg['message'] = "token错误";
            $this->ajaxReturn($msg);
            exit();
        }
        $this->redis->select ( self::MESSAGE_PUBLIC_CACHE );       
        
        if($this->redis->set("concurrent",$this->randomInt,['nx','ex'=>$this->concurrentTtl])){
            //检查15缓冲区，如果存在预约单，即不允许进行大厅抢单
            $this->redis->select(self::ORDER_CACHE_15);
            $iterator = null;
            while ( true ) {
                $data = $this->redis->scan ( $iterator, $driverId . ":*:*", 100 );
                if ($data == false && $iterator == 0)
                    break;
                elseif($data){
                    $this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
                    if($this->redis->get("concurrent") == $this->randomInt)
                        $this->redis->del("concurrent");
                    $this->ajaxReturn([
                        "message" => "系统繁忙,请重试",
                        "message_code" => -1,
                    ]);
                }
            }           
            
            $this->redis->select(self::ORDER_CACHE_90);
            
            $iterator = null;
            while ( true ) {
                $data = $this->redis->scan ( $iterator, $key, 100 );
                if ($data == false && $iterator == 0){
                    $this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
                    if($this->redis->get("concurrent") == $this->randomInt)
                        $this->redis->del("concurrent");
                    $this->ajaxReturn([
                        "message" => "订单已被抢",
                        "message_code" => -1,
                    ]);
                }elseif($data)
                    break;
            }
            
            $s_driver = M ( 's_driver' );
            $s_driver->where ( "id = " . $driverId )->save (["state" => "running"]);
            
            
            $areaId = $this->getDriverBelongs2AreaId ( $driverId );
            
            // 从AWAIT_DRIVER_POINTS中移除
            $this->redis->select ( self::DRIVER_POINTS_DB );
            $this->redis->zRem ( self::AWAIT_DRIVER_POINTS . "_$areaId", $driverId );
            
            $this->redis->select ( self::ORDER_PUBLIC_CACHE );
            $data = json_decode ( $this->redis->get ( $key ), true, JSON_UNESCAPED_UNICODE );
            
            // 清理工作开始：
            // 1.清理ORDER_PUBLIC_CACHE区域
            $this->redis->del ( $key );
            // 2.清理MESSAGE_PUBLIC_CACHE区域
            $this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
            $this->redis->del ( $key );
            // 3.删除90s缓冲区数据
            $this->redis->select ( self::ORDER_CACHE_90 );
            $this->redis->zRem ( self::AWAIT_ORDER_POINTS . "_$areaId", $key );
            $this->redis->del ( $key );                        
            // 4.这里至关重要，必须要记得清除并发锁
            $this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
            if($this->redis->get("concurrent") == $this->randomInt)
                $this->redis->del("concurrent");

            // 固化Mysql数据库信息,将预制单信息转入正式订单
            if ($return = $this->save2Mysql ( $data, $driverId,$data ['source'] )) {
                $this->redis->select(self::ORDER_CACHE_15);
                $this->redis->del($key);

                $this->ajaxReturn ( [
                    "message" => "系统成功指派订单",
                    "message_code" => "0",
                    "cont" => $return
                ] );
            } else {
                $this->ajaxReturn ( [
                    "message" => "订单已经被抢",
                    "message_code" => "-1"
                ] );
            }
        }else{
            $this->ajaxReturn([
                "message" => "并发冲突",
                "message_code" => -1,
            ]);
        }            
	}

	/**
	 * 司机转入5分钟黑名单
	 *
	 * @param integer $driverId
	 *        	司机ID
	 * @param string $key
	 *        	key键名
	 * @return string
	 */
	public function switch2Blacklist() {
		$driverId = I ( "post.driverId" );
		$key = I ( "post.key" );
		$areaId = $this->getDriverBelongs2AreaId ( $driverId );
		$blacklistTime = M ( "m_address_config" )->where ( "address_id = " . $areaId )->field ( "blacklist_time" )->find () ['blacklist_time'];
		

		$this->redis->select(self::ORDER_CACHE_15);
		
		$iterator = null;
		while ( true ) {
			$data = $this->redis->scan ( $iterator, $key, 100 );
			if ($data == false && $iterator == 0)
				$this->ajaxReturn([
						"message" => "订单已消失，无需加入黑名单",
						"message_code" => 0,
				]);
			elseif($data)
				break;
		}
		

		$this->recursion_switch2black($driverId, $blacklistTime, $key);
	}
	/**
	 * 司机15s内接单
	 *
	 * @param int $driverId
	 *        	司机ID
	 * @param
	 *        	string key 键名，由assign_d_id:u_id:reservation_no组成
	 * @return string
	 */
	public function driverAcceptOrder_15() {
		
		$key = I ( 'post.key' );
		$no = $key;
		
		$driverId = I ( 'post.driverId' );		
		$token = I("token");  //司机端的token
		if(empty($driverId)){
                    $msg['message_code'] = -402;
                    $msg['message'] = "d_id为空";
                    $this->ajaxReturn($msg);
                    exit();
                }
                if($token != S("drive_token_{$driverId}")){
                    $msg['message_code'] = -101;
                    $msg['message'] = "token错误";
                    $this->ajaxReturn($msg);
                    exit();
                }
        
        $this->redis->select ( self::MESSAGE_PUBLIC_CACHE );        
        if($this->redis->set("concurrent",$this->randomInt,['nx','ex'=>$this->concurrentTtl])){
            $orderReservation = M('b_order_reservation');
            $orderReservation->where("reservation_no = '$no'")->save([
                "state" => "success",
            ]);
            
            $s_driver = M ( 's_driver' );
            $s_driver->where ( "id = " . $driverId )->save (["state" => "running"]);       
            
            $areaId = $this->getDriverBelongs2AreaId($driverId);
            $this->redis->select ( self::ORDER_CACHE_15 );
            $data = json_decode ( $this->redis->get ( $key ), true, JSON_UNESCAPED_UNICODE );            
            
            // 清理15s缓冲区
            $this->redis->pexpire( $key,100 );           
            
            $this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
            
            if($this->redis->get("concurrent") == $this->randomInt)
                $this->redis->del("concurrent");
            
            if (!empty($data)){
                if ($return = $this->save2Mysql ( $data, $data ['assign_d_id'],$data ['source'] )) {
                    $this->ajaxReturn ( [
                        "message" => "系统成功指派订单",
                        "message_code" => "0",
                        "cont" => $return
                    ] );
                } else {
                    $this->ajaxReturn ( [
                        "message" => "接单失败",
                        "message_code" => "-1"
                    ] );
                }
            }else{
                $this->ajaxReturn ( [
                    "message" => "订单已被取消或超时",
                    "message_code" => "-1"
                ] );
            }                
        }else
            $this->ajaxReturn([
                "message" => "并发冲突",
                "message_code" => -1,
            ]);	
	}
	
	
	/**
	 * 司机15s内拉单(轮询接口)
	 *
	 * @param int $driverId
	 *        	司机的ID号
	 * @return string
	 */
	public function driverPullOrder_15() {
		$driverId = I('post.driverId');
                if(S("order_driver_{$driverId}")){
                    S("order_driver_{$driverId}",NULL);
                    $this->ajaxReturn ( [ 
                        "message" => "有预约单情况下,手动快速抢单,预约单静默处理,返回没拉取到",
                        "message_code" => "0",
                        'is_order' => 'N',
                        'driverId' => $driverId,
                    ] );
                }
		if(!$this->speedControll($driverId,5))
			$this->ajaxReturn([
					"message" => "请不要高频访问",
					"message_code" => -1,
			]);
		$this->redis->select ( self::ORDER_CACHE_15 );
		$iterator = null;
		while ( true ) {
			$data = $this->redis->scan ( $iterator, $driverId . ":*:*", 100 );
			if ($data == false)
				break;
			foreach ( $data as $value ) {
				$result [] = $value;
			}
		}
		// 考虑到 driverId:uid:no的处理条件为一旦有一单派给司机，在司机接单前，就不会再次派给司机，于是
		// 可以确定，这个pattern一定只有一个值。故取出【0】，即可。
		if (! empty ( $result )) {
			$data = json_decode ( $this->redis->get ( $result [0] ), true );
			$data['distance'] = round($data['distance'],1);
			$data ["driverId"] = $data ['assign_d_id'];
			$data ["key"] = $data ['reservation_no'];
			$this->ajaxReturn ( [ 
					"message" => "成功获取系统指派预约单",
					"message_code" => "0",
					"is_order" => 'Y',
					"cont" => $data,
					'driverId' => $driverId,
			] );
		} else {
			$this->ajaxReturn ( [ 
					"message" => "指派单不存在",
					"message_code" => "0",
					'is_order' => 'N',
					'driverId' => $driverId,
			] );
		}
	}
	/**
	 * 乘客检查单
	 * api:
	 *
	 * @param integer $uid
	 *        	乘客ID
	 * @return string
	 */
	public function passengerCheckExistOrder() {
		// $uid = I("post.uid");
		$userinfo = session ( "userinfo" );
		$uid = $userinfo ['u_id'];
		
		if(empty($uid)){
			$this->ajaxReturn([
					"message" => "uid不能为空",
					"message_code" => -1,
			]);
		}
		
		// 1.检查15s,90s预制单情势
		$this->redis->select ( self::ORDER_CACHE_15 );
		$iterator = null;
		while ( true ) {
			$data = $this->redis->scan ( $iterator, "*:" . $uid . ":*", 100 );
			if ($data == false)
				break;
			foreach ( $data as $value ) {
				$result [] = $value;
			}
		}
		if (! empty ( $result )) {
                    $cont = json_decode ( $this->redis->get ( $result [0] ), true, JSON_UNESCAPED_UNICODE );
                    $cont['initiation_time_int'] = strtotime($cont['initiation_time']);
			$this->ajaxReturn ( [ 
					"message" => "系统之前有派单，不可再呼叫",
					"message_code" => "-1",
					"cont" => $cont
			] );
		}

		$this->redis->select ( self::ORDER_CACHE_90 );
		$iterator = null;
		while ( true ) {
			$data = $this->redis->scan ( $iterator, "*:" . $uid . ":*", 100 );
			if ($data == false)
				break;
			foreach ( $data as $value ) {
				$result [] = $value;
			}
		}
		if (! empty ( $result )) {
		                $this->redis->select(self::ORDER_PUBLIC_CACHE);
                        $cont = json_decode ( $this->redis->get ( $result [0] ), true, JSON_UNESCAPED_UNICODE );
                        $cont['initiation_time_int'] = strtotime($cont['initiation_time']);
			$this->ajaxReturn ( [ 
					"message" => "系统之前有派单，不可再呼叫",
					"message_code" => "-1",
					"cont" => $cont
			] );
		}

		// 2.检查正式单情势
		if ($order = $this->passengerCheckExistOrders ( $uid )) {
			switch ($order ['state']) {
				case "on" :
					$orderfee = S ( "order_fee_{$order['o_id']}" );
					$start_time = NOW_TIME - $orderfee ['on_time'];
					$msg ['message_code'] = 1; // 等待接驾
					$msg ['message'] = "on";
					$msg ['link'] = "/Orderauto/order_on/o_id/{$order['o_id']}/start_time/{$start_time}";
					$this->ajaxReturn ( $msg );
					break;
				case "active" :
					$orderfee = S ( "order_fee_{$order['o_id']}" );
					$start_time = NOW_TIME - $orderfee ['active_time'];
					$msg ['message_code'] = 2; // 行程中
					$msg ['message'] = "active";
					$msg ['link'] = "/Orderauto/order_active/o_id/{$order['o_id']}/start_time/{$start_time}";
					$this->ajaxReturn ( $msg );
					break;
				case "wait_pay" :
					$msg ['message_code'] = 3; // 等待支付
					$msg ['message'] = "wait_pay";
					$msg ['link'] = "/Orderauto/order_deatil?o_id={$order['o_id']}";
					$this->ajaxReturn ( $msg );
					break;
			}
		}
		$this->ajaxReturn ( [ 
				"message" => "校验成功",
				"message_code" => "0"
		] );
	}
	public function passengerCheckExistOrder2() {
		$userinfo = session ( "userinfo" );
		$uid = $userinfo ['u_id'];
		
		if(empty($uid))
			$this->ajaxReturn([
					"message_code" => -1,
					"message" => "uid不能为空",
			]);
		
		
		// $uid = I("post.uid");
		// 1.检查15s,90s预制单情势
		$this->redis->select ( self::ORDER_CACHE_15 );
		$iterator = null;
		while ( true ) {
			$data = $this->redis->scan ( $iterator, "*:" . $uid . ":*", 100 );
			if ($data == false)
				break;
			foreach ( $data as $value ) {
				$result [] = $value;
			}
		}
		if (! empty ( $result )) {
			return [ 
					"message" => "系统之前有派单，不可再呼叫",
					"message_code" => "-1",
					"cont" => json_decode ( $this->redis->get ( $result [0] ), true, JSON_UNESCAPED_UNICODE )
			];
		}

		$this->redis->select ( self::ORDER_CACHE_90 );
		$iterator = null;
		while ( true ) {
			$data = $this->redis->scan ( $iterator, "*:" . $uid . ":*", 100 );
			if ($data == false)
				break;
			foreach ( $data as $value ) {
				$result [] = $value;
			}
		}
		if (! empty ( $result )) {
			return [ 
					"message" => "系统之前有派单，不可再呼叫",
					"message_code" => "-1",
					"cont" => json_decode ( $this->redis->get ( $result [0] ), true, JSON_UNESCAPED_UNICODE )
			];
		}

		// 2.检查正式单情势
		if ($order = $this->passengerCheckExistOrders ( $uid )) {
			return [ 
					"message" => "系统之前有派单，不可再呼叫",
					"message_code" => "-2",
					"cont" => $order
			];
		}
		return [ 
				"message" => "校验成功",
				"message_code" => "0"
		];
	}

	/**
	 * 检查正式订单中是否有未完成订单
	 *
	 * {@inheritdoc}
	 * @see \Home_V2\Controller\DispatchInterface::checkExistOrder()
	 */
	public function driverCheckExistOrders($driverId) {
	}
	/**
	 * 获取未完成正式订单信息
	 *
	 * {@inheritdoc}
	 * @see \Home_V2\Controller\DispatchInterface::getUnFinishedOrder()
	 */
	public function getUnFinishedOrder($orderId) {
	}

	/**
	 * 司机检查单
	 *
	 * @param integer $driverId
	 *        	司机ID
	 * @return string
	 */
	public function driverCheckExistOrder() {
		$driverId = I ( "post.driverId" );
		// 1.检查15s派单中的预制单
		$this->redis->select ( self::ORDER_CACHE_15 );
		$iterator = null;
		while ( true ) {
			$data = $this->redis->scan ( $iterator, $driverId . ":*:*", 100 );
			if ($data == false)
				break;
			foreach ( $data as $value ) {
				$result [] = $value;
			}
		}
		if (! empty ( $result )) {

			$order = json_decode ( $this->redis->get ( $result [0] ), true, JSON_UNESCAPED_UNICODE );

			$this->ajaxReturn ( [ 
					"message" => "系统之前有派单，不可再听单或接单",
					"message_code" => "-1",
					"cont" => $order
			] );
		} else
			$this->ajaxReturn ( [ 
					"message" => "不存在预约单",
					"message_code" => "0"
			] );
	}
	
	/**
	 * 乘客电话叫单
	 * @param string $phone 电话
	 * @param string $s_lat 纬度
	 * @param string $s_lng 经度
	 * @param string $s_local 所在地名
	 * @return string
	 */
	public function passengerOrderByPhone(){
		$adminid = I("id");  //管理员的ID
                if(empty($adminid)){
                    $msg['message_code'] = -402;
                    $msg['message'] = "管理员ID为空";
                    $this->ajaxReturn($msg);
                    exit();
                }
                $token = I("token");
                if($token!=S("admin_token{$adminid}")){
                    $msg['message_code'] = -101;
                    $msg['message'] = "token错误";
                    $this->ajaxReturn($msg);
                    exit();
                }
                $address_id = M("m_admin")->where(array("id"=>$adminid))->getField("address_id");
		
		//从电话叫单处获取
		$phone = I("phone");
		if(empty($phone)){
			$msg['message_code'] = -404;
			$msg['message'] = "phone is null";
			$this->ajaxReturn($msg);
			exit();
		}
		$s_lat = I("s_lat");  //此处接收百度地图的坐标
		$s_lng = I("s_lng");
		$s_cn_local = I("s_local");  //"慧谷大厦";//
		$user = M("s_user")->where(array("phone"=>$phone))->field("id")->find();
		if(empty($user)){  //这人没关注过公众号
			$data['phone'] = $phone;
			$data['sdate'] = date("Y-m-d H:i:s",NOW_TIME);
			$data['unionid'] = rand(10,99)."".NOW_TIME;
			$userid = M("s_user")->add($data);
		}else{
			$userid = $user['id'];
		}
		$s_site = Convert_BD09_To_GCJ02($s_lat, $s_lng);  //百度转腾信坐标   //维度，经度
		$addrId = $this->fetchAddress ( $s_site['lng'], $s_site['lat'] );
                if($address_id==$addrId||$address_id==0){
                    
                }else{
                    $msg['message_code'] = -1;
                    $msg['message'] = $address_id."叫单区域不在管辖范围内".$addrId;
                    $this->ajaxReturn($msg);
                    exit();
                }
		//提取必要的信息出来
		$uid = $userid;
		$longitude_origin = $s_site['lng'];
		$latitude_origin = $s_site['lat'];
		$appellation_origin = $s_cn_local;
		
		// 1.检查15s,90s预制单情势
		$this->redis->select ( self::ORDER_CACHE_15 );
		$iterator = null;
		while ( true ) {
			$data = $this->redis->scan ( $iterator, "*:" . $uid . ":*", 100 );
			if ($data == false)
				break;
				foreach ( $data as $value ) {
					$result [] = $value;
				}
		}
		if (! empty ( $result )) {
			$this->ajaxReturn([
					"message" => "系统之前有派单，不可再呼叫",
					"message_code" => "-1",
					"cont" => json_decode ( $this->redis->get ( $result [0] ), true, JSON_UNESCAPED_UNICODE )
			]); 
		}
		
		$this->redis->select ( self::ORDER_CACHE_90 );
		$iterator = null;
		while ( true ) {
			$data = $this->redis->scan ( $iterator, "*:" . $uid . ":*", 100 );
			if ($data == false)
				break;
				foreach ( $data as $value ) {
					$result [] = $value;
				}
		}
		if (! empty ( $result )) {
			$this->ajaxReturn([
					"message" => "系统之前有派单，不可再呼叫",
					"message_code" => "-1",
					"cont" => json_decode ( $this->redis->get ( $result [0] ), true, JSON_UNESCAPED_UNICODE )
			]);
		}
		
		// 2.检查正式单情势
		$order = $this->passengerCheckExistOrders ( $uid );
		if(!empty($order)){
			$this->ajaxReturn([
					"message" => "系统之前有派单，不可再呼叫",
					"message_code" => "-1",
					"cont" => $order
			]);
		}
		
		// 1.获取乘客起始点所在运营片区ID
		$addrId = $this->fetchAddress ( $longitude_origin, $latitude_origin );
		
		// 2.获取该片区的配置内容，如：派单半径10Km
		$nearByKilometers = M ( "m_address_config" )->where ( "address_id = " . $addrId )->field ( "nearby_kilometers" )->find () ['nearby_kilometers'];
		// 获取预约单时长
		$reservationTime = M ( "m_address_config" )->where ( "address_id = " . $addrId )->field ( "reservation_time" )->find () ['reservation_time'];
		// 3.将乘客坐标、派单半径放入AWAIT_DRIVER_POINTS缓冲区，取最近司机id
		$this->redis->select ( self::DRIVER_POINTS_DB );
		
		// 4.选择最近的且具备运营资质的司机1人
		if (! empty ( $data = $this->redis->rawCommand ( 'georadius', self::AWAIT_DRIVER_POINTS . "_$addrId", $longitude_origin, $latitude_origin, $nearByKilometers, 'km', 'WITHDIST', 'WITHCOORD', 'count', 1, 'asc' ) )) {
			$result = [
					"u_id" => $uid,
			        "source" => "Out_line",
					"assign_d_id" => $data [0] [0],
					"distance" => $data [0] [1],
					"driverPoints" => $data [0] [2],
					"reservation_no" => md5 ( http_build_query ( [
							"assign_d_id" => $data [0] [0],
							"u_id" => $uid,
							"timestamp" => time ()
					] ) ), // 32bit md5
					"address_id" => $addrId,
					"longitude_destination" => null,
					"latitude_destination" => null,
					"appellation_destination" => null,
					"longitude_origin" => $longitude_origin,
					"latitude_origin" => $latitude_origin,
					"appellation_origin" => $appellation_origin,
					"initiation_time" => date ( 'Y-m-d H:i:s', time () ),
					"state" => "active"
			];
			// 4.将指定司机从AWAIT_DRIVER_POINTS里移除
			$this->redis->zRem ( self::AWAIT_DRIVER_POINTS . "_$addrId", $data [0] [0] );
			// 5.将该笔预约单加入15s缓冲区：ORDER_CACHE_15
			$this->redis->select ( self::ORDER_CACHE_15 );
			$this->redis->setex ( $result ['assign_d_id'] . ':' . $result ['u_id'] . ':' . $result ['reservation_no'], $reservationTime + self::DELAY_TIME, json_encode ( $result ) );
			// 6.加入公共订单缓冲区
			$this->redis->select ( self::ORDER_PUBLIC_CACHE );
			$this->redis->setex ( $result ['assign_d_id'] . ':' . $result ['u_id'] . ':' . $result ['reservation_no'], 1800,json_encode ( $result ) );
			// 7.将信息预存入mysql预约订单表
			$orderReservation = M ( "b_order_reservation" );
			$bool = $orderReservation->add ( $result );
			// 7.1将司机的状态修改为running
			#$s_driver = M ( 's_driver' );
			#$s_driver->state = "running";
			#$s_driver->where ( "id = " . $result ['assign_d_id'] )->save (["state" => "running"]);
			
			$info = get_drive_info($result ['assign_d_id']);
			$info["distance"] = $result['distance'];
			// 8.返回消息，不通知乘客匹配到的司机
			if ($bool)
				$this->ajaxReturn ( [
						"message" => "成功收到呼叫申请",
						"message_code" => "0",
						"cont" => $info,
				] );
				else
					$this->ajaxReturn ( [
							"message" => "申请呼叫失败",
							"message_code" => "-1"
					] );
		} else {
			$this->ajaxReturn ( [
					"message_code" => "-1",
					"message" => $nearByKilometers . "公里内未找到适合的司机",
			] );
		}

	}
	
	/**
	 * 乘客下单
	 * api:
	 * 需要说明的事，派单的时候，是根据乘客的坐标区域，选定运营证覆盖当前区域的司机范围。
	 *
	 * @param int $uid
	 *        	乘客ID
	 * @param double $longitude_origin
	 *        	起始点经度
	 * @param double $latitude_origin
	 *        	起始点纬度
	 * @param string $appellation_origin
	 *        	起始点称呼
	 * @param double $longitude_destination
	 *        	目的地经度
	 * @param double $latitude_destination
	 *        	目的地纬度
	 * @param string $appellation_destination
	 *        	目的地称呼
	 * @return string
	 */
	public function passengerOrder() {

		// $uid = I('post.uid');
		$userinfo = session ( "userinfo" );
		$uid = $userinfo ['u_id'];

		if(empty($uid))
			$this->ajaxReturn([
					"message" => "UID不能为空",
					"message_code" => -1,
			]);
		
		$temp = $this->passengerCheckExistOrder2 ();

		switch ($temp ['message_code']) {
			case '-1' :
				$this->ajaxReturn ( [ 
						"message" => "成功收到呼叫申请(重进)",
						"message_code" => "0"
				] );
				break;
			case '-2' :
				$this->ajaxReturn ( [ 
						"message" => "成功收到呼叫申请(重进)",
						"message_code" => "0"
				] );
				break;
			default :
				break;
		}
		
		$pos = Convert_BD09_To_GCJ02(I ( 'post.latitude_origin' ),I ( 'post.longitude_origin' ));
		$longitude_origin = $pos['lng'];
		$latitude_origin = $pos['lat'];
		$appellation_origin = I ( 'post.appellation_origin', NULL );

		$longitude_destination = I ( 'post.longitude_destination', NULL );
		$latitude_destination = I ( 'post.latitude_destination', NULL );
		$appellation_destination = I ( 'post.appellation_destination', NULL );

		// 1.获取乘客起始点所在运营片区ID
		$addrId = $this->fetchAddress ( $longitude_origin, $latitude_origin );

		// 2.获取该片区的配置内容，如：派单半径10Km
		$nearByKilometers = M ( "m_address_config" )->where ( "address_id = " . $addrId )->field ( "nearby_kilometers" )->find () ['nearby_kilometers'];
		// 获取预约单时长
		$reservationTime = M ( "m_address_config" )->where ( "address_id = " . $addrId )->field ( "reservation_time" )->find () ['reservation_time'];
		
		//这里添加并发锁
		$this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
		if ($this->redis->set("concurrent5",$this->randomInt,['nx','ex'=>$this->concurrentTtl])) {
						
			// 3.将乘客坐标、派单半径放入AWAIT_DRIVER_POINTS缓冲区，取最近司机id
			$this->redis->select ( self::DRIVER_POINTS_DB );
			
			// 4.选择最近的且具备运营资质的司机1人
			if (! empty ( $data = $this->redis->rawCommand ( 'georadius', self::AWAIT_DRIVER_POINTS . "_$addrId", $longitude_origin, $latitude_origin, $nearByKilometers, 'km', 'WITHDIST', 'WITHCOORD', 'count', 10, 'asc' ) )) {
				$result = [
						"u_id" => $uid,
				        "source" => "No_public",
				        "driversDataIn10" => json_encode($data,JSON_UNESCAPED_UNICODE),
						"assign_d_id" => $data [0] [0],
						"distance" => $data [0] [1],
						"driverPoints" => $data [0] [2],
				        "reservation_no" => $data[0][0] . ":" . $uid . ":" . md5 ( http_build_query ( [
								"assign_d_id" => $data [0] [0],
								"u_id" => $uid,
								"timestamp" => time ()
						] ) ), // 32bit md5
						"address_id" => $addrId,
						"longitude_destination" => $longitude_destination,
						"latitude_destination" => $latitude_destination,
						"appellation_destination" => $appellation_destination,
						"longitude_origin" => $longitude_origin,
						"latitude_origin" => $latitude_origin,
						"appellation_origin" => $appellation_origin,
						"initiation_time" => date ( 'Y-m-d H:i:s', time () ),
						"state" => "active"
				];
				// 4.将指定司机从AWAIT_DRIVER_POINTS里移除
				$this->redis->zRem ( self::AWAIT_DRIVER_POINTS . "_$addrId", $data [0] [0] );
				// 5.将该笔预约单加入15s缓冲区：ORDER_CACHE_15
				$this->redis->select ( self::ORDER_CACHE_15 );
				$this->redis->setex ( $result ['reservation_no'], $reservationTime + self::DELAY_TIME, json_encode ( $result ) );
				// 6.加入公共订单缓冲区
				$this->redis->select ( self::ORDER_PUBLIC_CACHE );
				$this->redis->setex ( $result ['reservation_no'], 1800,json_encode ( $result ) );
				// 7.将信息预存入mysql预约订单表
				$orderReservation = M ( "b_order_reservation" );
				$bool = $orderReservation->add ( $result );
				
				
				$this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
				if($this->redis->get("concurrent5") == $this->randomInt)
				    $this->redis->del("concurrent5");
				
				
				// 8.返回消息，不通知乘客匹配到的司机
				if ($bool){
				    $msg = [
				        "type" => "passenger_order",
				        "driverId" => $result['assign_d_id'],
				        "cont" => [
				            "appellation_origin" => $result["appellation_origin"],
				            "appellation_destination" => $result["appellation_destination"],
				            "distance" => sprintf("%.1f",$result["distance"]),
				            "time" => date('Y-m-d H:i:s',time()),
				            "key" => $result['reservation_no'],
				        ],
				        "message" => "乘客以下单，请及时接单",
				        "message_code" => "0",
				    ];
				    redis_publish($msg['driverId'], $msg, 2, 5);
					$this->ajaxReturn ( [
							"message" => "成功收到呼叫申请(原始)",
							"message_code" => "0"
					] );
				}else
						$this->ajaxReturn ( [
								"message" => "申请呼叫失败",
								"message_code" => "-1"
						] );
			} else {
				
				$this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
				if($this->redis->get("concurrent5") == $this->randomInt)
				    $this->redis->del("concurrent5");
				
				//乘客呼叫号
				$call_no = md5 ( http_build_query ( [
						"u_id" => $uid,
						"timestamp" => time ()
				] ) );
				$key = "passenger:" . $uid . ":$call_no:" . $addrId;
				$hallTime = M("m_address_config")->where("address_id = " . $addrId)->field("hall_time")->find()['hall_time'];
				$this->redis->select(self::ORDER_CACHE_90);
				$this->redis->setex($key,$hallTime + self::DELAY_TIME,$addrId);
				$this->redis->rawCommand('geoadd', self::AWAIT_ORDER_POINTS . "_$addrId", $longitude_origin,$latitude_origin, $key);

				
				$result = [
				    "u_id" => $uid,
				    "source" => "No_public",
				    "address_id" => $addrId,
				    "longitude_destination" => $longitude_destination,
				    "latitude_destination" => $latitude_destination,
				    "appellation_destination" => $appellation_destination,
				    "longitude_origin" => $longitude_origin,
				    "latitude_origin" => $latitude_origin,
				    "appellation_origin" => $appellation_origin,
				    "initiation_time" => date ( 'Y-m-d H:i:s', time () ),
				];
				// 6.加入公共订单缓冲区
				$this->redis->select ( self::ORDER_PUBLIC_CACHE );
				$this->redis->setex ( $key, 1800,json_encode ( $result ) );
				
				$this->ajaxReturn ( [
						"message_code" => "-1",
						"message" => $nearByKilometers . "公里内未找到适合的司机,呼叫已转入大厅。",
						"areaId" => $addrId
				] );
			}
			
		}else 
			$this->ajaxReturn ( [
					"message_code" => "-1",
					"message" => "并发冲突",
			] );
	}

	/**
	 * 准备接单
	 * api:http://xxx.net/Home_V2/pr
	 *
	 * @param int $driverId
	 *        	司机ID
	 * @param double $longitude
	 *        	经度
	 * @param double $latitude
	 *        	纬度
	 * @return string
	 */
	public function prepare() {
                
		$longitude = I ( 'post.longitude' );
		$latitude = I ( 'post.latitude' );
		$pos = Convert_BD09_To_GCJ02($latitude,$longitude);
		$longitude = $pos['lng'];
		$latitude = $pos['lat'];
		$token = I("token");  //司机端的token
		$driverId = I ( 'post.driverId' );
		if(empty($driverId)){
            $msg['message_code'] = -402;
            $msg['message'] = "d_id为空";
            $this->ajaxReturn($msg);
            exit();
        }
        if($token != S("drive_token_{$driverId}")){
            $msg['message_code'] = -101;
            $msg['message'] = "token错误";
            $this->ajaxReturn($msg);
            exit();
        }
        S("driverid_survive_{$driverId}",NOW_TIME,array('type'=>'file','expire'=>180));
		//判断是否被禁
		$driver_info = M("s_driver")->where(array("id"=>$driverId))->field("id,state,available,address_id")->find();
		if($driver_info['available']=="snap_prohibited"||$driver_info['available']=="lasting_prohibited"){
			$msg['message_code'] = 1;
			$msg['message'] = "该司机已被封禁！";
			$this->ajaxReturn($msg);
			exit();
		}
		$m = M();
		$m->startTrans();   //开启事务
		
		//结束未结束的行程
		$online_record = M("d_online_record")->where(array("d_id"=>$driverId,"edate"=>0))->find();
		if(!empty($online_record)){
			$line_time = NOW_TIME-$online_record['sdate'];
			$upline = M("d_online_record")->where(array("id"=>$online_record['id']))->save(array("edate"=>NOW_TIME,"line_time"=>$line_time));
		}  else {
			$upline = 1;
		}
		
		$this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
		if($this->redis->set("concurrent6",$this->randomInt,['nx','ex'=>$this->concurrentTtl]) && $this->redis->set ( "concurrent5", $this->randomInt,['nx','ex'=>$this->concurrentTtl] )){
			$this->redis->select ( self::BLACKLIST_DRIVER_DB );
			$a = $this->redis->exists ( $driverId );
			$this->redis->select ( self::ORDER_CACHE_15 );
			$iterator = null;
			while ( true ) {
				$data = $this->redis->scan ( $iterator, $driverId . ":*:*", 100 );
				if ($data == false)
					break;
					foreach ( $data as $value ) {
						$result [] = $value;
					}
			}
			
			if (!$a && empty ( $result )) {
				// 获取司机的运营区域限制标识
				$areaId = $this->getDriverBelongs2AreaId ( $driverId );
				#$await = $this->internalTrackReport ( self::DRIVER_POINTS_DB, self::AWAIT_DRIVER_POINTS . "_$areaId", $driverId, $longitude, $latitude );
				$this->recursion_temp($areaId,$driverId, $longitude, $latitude);
				$master = $this->internalTrackReport ( self::DRIVER_POINTS_DB, self::MASTER_DRIVER_POINTS, $driverId, $longitude, $latitude );
				
				// 修改司机状态为听单中
				$driver = M ( "s_driver" );
				#$driver->state = "stand";
				$state = $driver->where ( "id = " . $driverId )->save (["state" => "stand"]);
				$onlinadd = get_online_record($driverId);
				if ($onlinadd&&$state){
					$m->commit();
					$this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
					if($this->redis->get("concurrent6") == $this->randomInt)
					   $this->redis->del("concurrent6");
					if($this->redis->get("concurrent5") == $this->randomInt)
					   $this->redis->del("concurrent5");
					$this->ajaxReturn ( [
							"message_code" => "0",
							"message" => "听单成功",
							"stroke_id" => $onlinadd,
							"address_id" => $driver_info['address_id'],
							"cont" => [
									"longitude" => $longitude,
									"latitude" => $latitude,
									"driverId" => $driverId
							]
					] );
				}else{
					$m->rollback();
					$this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
					if($this->redis->get("concurrent6") == $this->randomInt)
					    $this->redis->del("concurrent6");
				    if($this->redis->get("concurrent5") == $this->randomInt)
				        $this->redis->del("concurrent5");
					$this->ajaxReturn ( [
							"message_code" => "-1",
							"message" => "听单失败",
					] );
				}
				
			} else {
				// 修改司机状态为听单中
				$driver = M ( "s_driver" );
				#$driver->state = "stand";
				$state = $driver->where ( "id = " . $driverId )->save (["state" => "stand"]);
				$onlinadd = get_online_record($driverId);
				if ($onlinadd&&$state){
					$m->commit();
					$this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
					if($this->redis->get("concurrent6") == $this->randomInt)
					    $this->redis->del("concurrent6");
				    if($this->redis->get("concurrent5") == $this->randomInt)
				        $this->redis->del("concurrent5");
					$this->ajaxReturn ( [
							"message_code" => "0",
							"message" => "听单成功",
							"stroke_id" => $onlinadd,
							"address_id" => $driver_info['address_id'],
							"cont" => [
									"longitude" => $longitude,
									"latitude" => $latitude,
									"driverId" => $driverId
							]
					] );
				}else{
					$this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
					if($this->redis->get("concurrent6") == $this->randomInt)
					    $this->redis->del("concurrent6");
				    if($this->redis->get("concurrent5") == $this->randomInt)
				        $this->redis->del("concurrent5");
					$m->rollback();
					$this->ajaxReturn ( [
							"message_code" => "-1",
							"message" => "请回到首页重新听单",
					] );
				}
				
			}
		}else{
			$this->ajaxReturn ( [
					"message_code" => "-1",
					"message" => "听单失败",
			] );
		}
		
	}

	/**
	 * 实时轨迹上报
	 * 默认Home模块
	 * api:http://xxx.net/Home_V2/tr
	 *
	 * @param int $driverId
	 *        	司机ID
	 * @return string
	 */
	public function trackReport() {

		$driverId = I ( 'post.driverId' );
                $token = I("token");  //司机端的token
		if(empty($driverId)){
                    $msg['message_code'] = -402;
                    $msg['message'] = "d_id为空";
                    $this->ajaxReturn($msg);
                    exit();
                }
                if($token != S("drive_token_{$driverId}")){
                    $msg['message_code'] = -101;
                    $msg['message'] = "token错误";
                    $this->ajaxReturn($msg);
                    exit();
                }
                S("driverid_survive_{$driverId}",NOW_TIME,array('type'=>'file','expire'=>90));
                
		$point_list = I("point_list");
                $point_list = str_replace("&quot;",'"', $point_list);
                //记录所有数据
                $record['date'] = date("Y-m-d H:i:s",NOW_TIME);
                $record['d_id'] = $driverId;
                $record['point_list'] = $point_list;
//                M("d_upload_point_record")->add($record);
                
                $res = yingyan_Upload($point_list);
                if($res['status']==0){
                    $resx['message_code'] = 0;
                    $resx['message'] = "OK";
                }else{
                    //这里使用推送，告诉司机你的网络不好  
                    $msg = [
                        "type" => "sys_net_error",
                        "message" => "您的网络信号故障，请重新开启网络",
                        "message_code" => "-1",
                        "cont" => [
                            "time" => date('Y-m-d H:i:s',time()),
                        ],
                    ];
                    #redis_publish($driverId, $msg, 5, 6);
                }

                $pointarr = json_decode($point_list,TRUE);
                $lastpoint = end($pointarr);
                $site = Convert_BD09_To_GCJ02($lastpoint["latitude"], $lastpoint["longitude"]);
                S("drive_{$driverId}",$site);
		$longitude = $site['lng'];
		$latitude = $site['lat'];
        
		// 获取司机的运营区域限制标识
		$areaId = $this->getDriverBelongs2AreaId ( $driverId );	
		
		//这里增加了心跳设计，更新DRIVER_HEART_ALIVE_AREA中的生命周期
		$this->redis->select(self::DRIVER_HEART_ALIVE_AREA);
		$r = $this->redis->expire("$driverId:$areaId",self::DRIVER_HEART_ALIVE_TIME);
		
		// 验证司机待驾状态，如果是await
		$this->redis->select ( self::DRIVER_POINTS_DB );	
		
		if (!is_bool ( $this->redis->zScore ( self::AWAIT_DRIVER_POINTS . "_$areaId", $driverId ) )) {		    		    		    
			$await = $this->internalTrackReport ( self::DRIVER_POINTS_DB, self::AWAIT_DRIVER_POINTS . "_$areaId", $driverId, $longitude, $latitude );
			$master = $this->internalTrackReport ( self::DRIVER_POINTS_DB, self::MASTER_DRIVER_POINTS, $driverId, $longitude, $latitude );
			if ($await && $master)
				$this->ajaxReturn ( [ 
						"message_code" => "0",
						"message" => "上报成功",
						"cont" => [ 
								"longitude" => $longitude,
								"latitude" => $latitude,
								"driverId" => $driverId
						]
				] );
			else
				$this->ajaxReturn ( [ 
						"message_code" => "-1",
						"message" => "上报失败"
				] );
		} else {
			$master = $this->internalTrackReport ( self::DRIVER_POINTS_DB, self::MASTER_DRIVER_POINTS, $driverId, $longitude, $latitude );
			if ($master)
				$this->ajaxReturn ( [ 
						"message_code" => "0",
						"message" => "上报成功",
						"cont" => [ 
								"longitude" => $longitude,
								"latitude" => $latitude,
								"driverId" => $driverId
						]
				] );
			else
				$this->ajaxReturn ( [ 
						"message_code" => "Fail",
						"message" => "上报失败"
				] );
		}
	}

	/**
	 * 控制接口提交速率简易设计
	 * 间隔N秒调用
	 */
	private function speedControll($id,$seconds) {
		$key = md5 ( http_build_query ( [ 
				"id" => $id,
				"uri" => I ( 'server.REQUEST_URI' )
		] ) );
		$this->redis->select ( self::API_SPEED_CACHE );
		if (! $this->redis->exists ( $key )) {
			$this->redis->setex ( $key, $seconds, 1 );
			return true;
		} else
			return false;
	}

	/**
	 * 轨迹上报内部入口
	 *
	 * @param int $db
	 *        	库位号
	 * @param string $zName
	 *        	Z集合名
	 * @param int $driverId
	 *        	司机ID
	 * @param double $longitude
	 *        	经度
	 * @param double $latitude
	 *        	纬度
	 * @return string
	 */
	private function internalTrackReport($db, $zName, $driverId, $longitude, $latitude) {
		$this->redis->select ( $db );
		if ($this->redis->zScore ( $zName, $driverId )) {
			if ($this->redis->zRem ( $zName, $driverId ))
				if ($this->redis->rawCommand ( 'geoadd', $zName, $longitude, $latitude, $driverId ))
					return true;
				else
					return false;
		} else if ($this->redis->rawCommand ( 'geoadd', $zName, $longitude, $latitude, $driverId ))
			return true;
		else
			return false;
	}
	/**
	 * 获取司机运营所归属的运营区域ID号
	 *
	 * @param integer $driverId
	 *        	司机ID
	 * @return int
	 */
	private function getDriverBelongs2AreaId($driverId) {
		$driver = M ( "s_driver" );
		return $driver->where ( "id = " . $driverId )->field ( "address_id")->find()['address_id'];
	}
	
	private function recursion_temp($areaId,$driverId, $longitude, $latitude){
		$await = $this->internalTrackReport ( self::DRIVER_POINTS_DB, self::AWAIT_DRIVER_POINTS . "_$areaId", $driverId, $longitude, $latitude );
		$this->redis->select(self::DRIVER_HEART_ALIVE_AREA);
		$this->redis->setex("$driverId:$areaId",self::DRIVER_HEART_ALIVE_TIME,1);
		if(!$await)
			$this->recursion_temp($areaId,$driverId, $longitude, $latitude);
		else
			return;
	}
	
	private function recursion_switch2black($driverId,$blacklistTime,$key){
		$this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
		if($this->redis->set("concurrent6",$this->randomInt,['nx','ex'=>$this->concurrentTtl])){
			$this->redis->select ( self::BLACKLIST_DRIVER_DB );
			if ($this->redis->setex ( $driverId, $blacklistTime + self::DELAY_TIME, 0 )) {
				// 执行立即转入大厅
				$this->redis->select ( self::ORDER_CACHE_15 );
				//这里做了伪删除，不适用0，暂定为1
				$this->redis->pexpire( $key,100 );
				$this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
				if($this->redis->get("concurrent6") == $this->randomInt)
				    $this->redis->del("concurrent6");
				$this->ajaxReturn ( [
						"message" => "成功",
						"message_code" => "0"
				] );
			}
			$this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
			if($this->redis->get("concurrent6") == $this->randomInt)
			    $this->redis->del("concurrent6");
		}
		$this->recursion_switch2black($driverId,$blacklistTime,$key);
	}
	
	private function recursion_del_90($uid){
		$this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
		if ($this->redis->setnx ( "concurrent2", 5 )) {
			$this->redis->select ( self::ORDER_CACHE_90 );
			$iterator = null;
			while ( true ) {
				$data = $this->redis->scan ( $iterator, "*:" . $uid . ":*", 100 );
				if ($data == false && $iterator == 0)
					break;
					foreach ( $data as $key ) {
						$addrId = $this->redis->get ( $key );
						$this->redis->zRem ( self::AWAIT_ORDER_POINTS . "_$addrId", $key );
						$this->redis->expire ( $key, 0 );
						// 系统派单之后的取消固化
						save2reservation ( $key );
					}
			}
			$this->redis->select ( self::MESSAGE_PUBLIC_CACHE );
			$this->redis->expire ( "concurrent2", 0 );
		}
		else
			$this->recursion_del_90($uid);
	}

}

