<?php

namespace Home_V2\Controller;

interface DispatchInterface {
	//获取所在片区
	public function fetchAddress($longitude,$latitude);
	//15s预制单正式固化入数据库
	public function save2Mysql($data,$d_id,$source);
	//司机检查正式订单中是否包含已存在订单
	public function driverCheckExistOrders($driverId);
	//乘客检查正式订单中是否包含已存在订单
	public function passengerCheckExistOrders($uid);
	//根据订单号获取正式订单信息
	public function getUnFinishedOrder($orderId);
}