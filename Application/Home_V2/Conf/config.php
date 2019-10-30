<?php
return array(
		"redisIp" => "127.0.0.1",
		"redisPort" => "6379",
		"redisAuth" => "sanjiayi@3+1.com",
		'URL_ROUTER_ON'   => true,
		'URL_ROUTE_RULES'=> [
				'tr' => 'Dispatch/trackReport',//实时轨迹上报
				'pr' => 'Dispatch/prepare',//听单
				'pceo'=>'Dispatch/passengerCheckExistOrder',//乘客检查是否存在未完成订单
				'dceo' => 'Dispatch/driverCheckExistOrder',//司机检查是否符合听单条件
				'po' => 'Dispatch/passengerOrder',//乘客下单
				'dpo15' => 'Dispatch/driverPullOrder_15',//司机拉取15s系统匹配单
				'dao15' => 'Dispatch/driverAcceptOrder_15',//司机15s内接单
				'dao90' => 'Dispatch/driverAcceptOrder_90',//司机90s内大厅接单
				's2b' => 'Dispatch/switch2Blacklist',//转入5分钟黑名单
				'pco' => "Dispatch/passengerCancelOrder",//乘客取消订单
				'dcp' => 'Dispatch/driverCancelPrepare',//司机取消听单 
				'dgol' => 'Dispatch/driverGrabOrderList',//司机获取大厅中适合他的闲置单列表
				'pobp' => 'Dispatch/passengerOrderByPhone',//乘客电话呼单
				'cli' => 'Monitor/runCli',//Cli运行
				'test' => 'Dispatch/doTest',//司机15s内接单
				'wscli' => 'WebSocket/runCli',
		],
		
);