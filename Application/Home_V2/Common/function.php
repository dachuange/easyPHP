<?php
/**
 * 固化到预约单为：Success
 * @param string $no 预约单号
 */
function save2reservation($no){
	$reservation = M("b_order_reservation");
	$reservation->state = "success";
	$reservation->where("reservation_no = '$no'");
	$reservation->save();
}
/**
 * 固化到预约单为：exit
 * @param string $no 预约单号
 */
function save2reservation_exit($no){
	$reservation = M("b_order_reservation");
	$reservation->state = "exit";
	$reservation->where("reservation_no = '$no'");
	$reservation->save();
}

/**
 * 固化到预约单为：Cancel
 * @param string $no 预约单号
 */
function save2reservation_cancel($no){
	$reservation = M("b_order_reservation");
	$reservation->state = "cancel";
	$reservation->where("reservation_no = '$no'");
	$reservation->save();
}