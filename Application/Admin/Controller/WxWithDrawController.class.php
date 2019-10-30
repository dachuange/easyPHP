<?php

namespace Admin\Controller;

use Think\Controller;

class WxWithDrawController extends Controller {
	
	/**
	 * 拒绝提现操作
	 * api:http://xxx.net/Admin/refuse
	 * @param string $withdraw_num 申请单号
	 * @param string $reason 拒绝原因
	 * @return string
	 */
	public function refuseWithDraw(){
		
		$draw = M("d_wxwithdraw");
		$data_['state'] = 'N';
		$data_['refuse_reason'] = I("reason");

		$draw->where(['withdram_num' => I("withdraw_num")])->find();
		$data = $draw->where(['withdram_num' => I("withdraw_num")])->find();

		if($draw->where('withdram_num = ' . I("withdraw_num"))->save($data_)){
			
			$amount = $data["amount"];
			$driverId = $data['d_id'];
			
			$driver = M("s_driver");
			$p = $driver->where("id = " . $driverId)->find();
			$driver->amount = $p["amount"] + $amount;
			$driver->where("id = " . $driverId)->save();
			
			$this->ajaxReturn([
					"status" => "Success",
					"msg" => "操作成功",
			]);
		}else{
			$this->ajaxReturn([
					"status" => "Fail",
					"msg" => "操作失败",
			]);
		}
	}
	
	
	/**
	 * 导出Excel
	 * api http://xxx.net/Admin/export_withdraw
	 * @param string $state 状态Y,N,O
	 * @return void
	 */
	public function exportExcel(){
		$state = I("state",null);
		$address_id = I("address_id",null);
		$map = empty($state) ? null : [
				"d_wxwithdraw.state" => $state,
		];
		if($address_id != 0)
			$map['s_driver.address_id'] = $address_id;
		$draw = M("d_wxwithdraw");
		$user = $draw->join("s_driver on s_driver.id = d_wxwithdraw.d_id")->where($map)->order('askfordate')->field(['withdram_num','d_id','openid','realname','d_wxwithdraw.phone','d_wxwithdraw.amount','askfordate','paydate','d_wxwithdraw.state'=>'status','refuse_reason','return_msg','err_code','payment_no'])->select();
		
		import('Library/PHPExcel/PHPExcel', APP_PATH);
		$filename_xia='./Public/exceltmpl/print_tmpl_wxwithdraw.xls';
		$inputFileType_xia = \PHPExcel_IOFactory::identify($filename_xia);
		$objReader_xia = \PHPExcel_IOFactory::createReader($inputFileType_xia);
		$objPHPExcel_xia = $objReader_xia->load($filename_xia);
		$objWorksheet_xia = $objPHPExcel_xia->setActiveSheetIndex(0);
		$contentStart = 2;
		
		foreach($user as $key => $val){
			$objPHPExcel_xia->setActiveSheetIndex(0)
			->setCellValue('A'.$contentStart, $val['id'])
			->setCellValueExplicit('B'.$contentStart, $val['withdram_num'],\PHPExcel_Cell_DataType::TYPE_STRING)  //不转义数字的输出
			->setCellValueExplicit('C'.$contentStart, $val['d_id'],\PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit('D'.$contentStart,$val['openid'],\PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValue('E'.$contentStart, $val['realname'])
			->setCellValueExplicit('F'.$contentStart, $val['phone'],\PHPExcel_Cell_DataType::TYPE_STRING)  //不转义数字的输出
			->setCellValueExplicit('G'.$contentStart, number_format(($val['amount']*0.994), 2, '.', ''),\PHPExcel_Cell_DataType::TYPE_STRING)  //不转义数字的输出
			->setCellValueExplicit('H'.$contentStart, $val['askfordate'],\PHPExcel_Cell_DataType::TYPE_STRING)  //不转义数字的输出
			->setCellValue('I'.$contentStart,$val['paydate'])
			->setCellValue('J'.$contentStart,$val['status']=='Y'?'已打款':($val['status']=='O'?"待打款":"打款失败"))
			->setCellValue('K'.$contentStart,$val['refuse_reason'])
			->setCellValue('L'.$contentStart,$val['return_msg'])
			->setCellValue('M'.$contentStart,$val['err_code'])
			->setCellValue('N'.$contentStart,$val['payment_no'])
			
			;
			$contentStart++;
		}
		$objWriter_xia = \PHPExcel_IOFactory::createWriter($objPHPExcel_xia, 'Excel5');  //引用 Excel5  是 .xls文件   Excel2007 是 .xlsx文件
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="print_'.$state.'.xls"');
		header('Cache-Control: max-age=0');
		
		ob_clean();
		flush();
		
		$objWriter_xia->save('php://output');
		return;
	}
	
	
	/**
	 * 获取司机提现申请列表
	 * Post Form Data 如：page=1&state=N
	 *api:http://xxx.net/Admin/list
	 *http://xxx.net/Admin/WxWithDraw/getAskForList
	 *@param String $page  当前页
	 *@param String $state 状态 Y,N,O
	 *@return String
	 */
	public function getAskForList(){
		$page = I("page",1);
		$state = I("state",null);
		$address_id = I("address_id",null);
		
		$map = empty($state) ? null : [
				"d_wxwithdraw.state" => $state,
		];
		if($address_id != 0)
			$map['s_driver.address_id'] = $address_id;
		$draw = M("d_wxwithdraw");
				
		$count = $draw->join("left join s_driver on d_wxwithdraw.d_id=s_driver.id")->where($map)->count();
		
		$list = $draw->join("left join s_driver on d_wxwithdraw.d_id=s_driver.id")->where($map)->order('askfordate desc')->field(['withdram_num','d_id','openid','realname','d_wxwithdraw.phone','d_wxwithdraw.state','d_wxwithdraw.amount','askfordate','paydate','s_driver.state'=>'status','refuse_reason','return_msg','err_code','payment_no'])->page($page .',10')->select();
		$this->ajaxReturn([
				'count' => $count,
				"status" => "Success",
				"msg" => "操作成功",
				'list' => $list,
		]);
	}
	
	
	/**
	 * 司机增加微信提现申请
	 * POST Form Data 调用示例：
	 * api:http://xxx.net/Admin/ask4
	 * http://xxx.net/Admin/WxWithDraw/addAskForCashToIndividual
	 * @param string $d_Id 司机ID
	 * @param string $openid 司机的openId
	 * @param string $phone 司机的联系电话
	 * @param double $amount 申请的金额
	 */
	public function addAskForCashToIndividual(){
		$data = I('post.');
		$data['askfordate'] = date('Y-m-d H:i:s',NOW_TIME);
		$addrId = M("s_driver")->where("id = " . $data['d_id'])->field('address_id')->find()['address_id'];
		$wx_withdraw_set = M('m_service_fee')->where("address_id = " . $addrId)->field('wx_withdraw_set')->find()['wx_withdraw_set'];
		
		
		if($data['amount'] < $wx_withdraw_set){
			$this->ajaxReturn([
					"message_code" => -1,
					"message" => "提现金额不能低于" . $wx_withdraw_set . "元",
			]);
		}
		
		$driver = M("s_driver");
		$data2 = $driver->where("id=" . $data['d_id'])->find();
		if($data2['amount'] >= $data['amount']){
			$data2['amount'] = $data2['amount'] - $data['amount'];
			$driver->where("id=" . $data['d_id'])->save($data2);
		}
		
		$data['withdram_num'] = rand(10,99)."".NOW_TIME;
		$draw = M("d_wxwithdraw");
		$insertId = $draw->add($data);
		if(empty($insertId))
			$this->ajaxReturn([
					"message_code" => -1,
					"message" => "fail",
			]);
			$this->ajaxReturn([
					"cont" => [
						"id" => $insertId,
					],
					"message_code" => 0,
					"message" => "success",
			]);
	}
	
	/**
	 * 提现到个人微信账户
	 * POST Form Data调用示例：
	 * api调用：http://xxx.net/Admin/c2i
	 * http://xxx.net/Admin/WxWithDraw/cashToIndividual
	 * @param string $withdram_num 申请号
	 * @return string 返回执行的JSON结果
	 */
	public function cashToIndividual(){
		

		/* $draw = M("d_wxwithdraw");
		$draw->state = "Y";
		$draw->paydate = '2019-08-16 10:06:29';
		$draw->payment_no = '10101045204011908160019202460179';
		$data[0]['withdram_num'] = I('post.withdram_num');
		$draw->where("withdram_num = " . $data[0]['withdram_num'])->save();
		exit(); */
		
		#$data = json_decode(file_get_contents("php://input"),true);	
		
		/* $adminid = I("id");  //管理员的ID
		if(empty($adminid)){
			$msg['error_code'] = -402;
			$msg['message'] = "管理员ID为空";
			$this->ajaxReturn($msg);
			exit();
		}
		$token = I("token");
		if($token!=S("admin_token{$adminid}")){
			$msg['error_code'] = -101;
			$msg['message'] = "token错误";
			$this->ajaxReturn($msg);
			exit();
		} */
		
		
		$data = I("post.");
		
		
		Vendor('wx.WxTransfer');
		#$wxPay = new \WxTransfer(C('mchId'),C('FW_appid'),C('FW_secret'),C('apiKey'));
		
		$wxPay = new \WxTransfer('1533419931','wxc0166ed61a85294d','f59fa021e43a2eabdd81cc3a15a44aba','B8BA05E3358E840576A05270A84CD1E2');
		
		
		$draw = M("d_wxwithdraw");
		$data = $draw->where("`withdram_num` = " . $data['withdram_num'])->select();
		
		if(empty($data))
			$this->ajaxReturn([
					"msg"=>"支付单号不存在",
					"status" => "Fail",
			]);
		
		$outTradeNo = $data[0]['withdram_num'];     //订单号
		$payAmount = round($data[0]['amount'] * 0.994,2);             //转账金额
		//$realName = $data[0]['realname'];         //收款人真实姓名
		$openId = $data[0]['openid'];
		
		$unifiedOrder = $wxPay->createJsBizPackage($openId,$payAmount,$outTradeNo,"");
                
		if($unifiedOrder->return_code != 'SUCCESS'){
			$draw->state = "N";
			$draw->return_msg = $unifiedOrder->return_msg;
			#$draw->where("withdram_num = " . $data[0]['withdram_num'])->save();
			$this->ajaxReturn([
					"msg"=>"提现失败",
					"status" => "Fail",
					"data" => [
							"return_msg" => $unifiedOrder->return_msg,
					],
			]);
		}
		if ($unifiedOrder->result_code != 'SUCCESS') {
			$draw->state = "N";
			$draw->err_code = $unifiedOrder->err_code;
			#$draw->where("withdram_num = " . $data[0]['withdram_num'])->save();
                        $unifiedarr = objectToArray($unifiedOrder);
//                        $this->ajaxReturn($unifiedarr['err_code_des']);
			$this->ajaxReturn([
					"msg"=>$unifiedarr['err_code_des'],
					"status" => "Fail",
					"data" => [
							"err_code" => $unifiedOrder->err_code,
					],
			]);
		}
                
		$draw->state = "Y";
		$draw->paydate = strval($unifiedOrder->payment_time);
		$draw->payment_no = strval($unifiedOrder->payment_no);
		
		$draw->where("withdram_num = " . $data[0]['withdram_num'])->save();
		
		$funding = M('b_platform_funding');
		$funding->full_num = $data[0]['withdram_num'];
		$funding->date = strval($unifiedOrder->payment_time);
		$funding->amount = $data[0]['amount'];
		$funding->type = 'withdraw';
		$funding->add();
		
		$this->ajaxReturn([
				"msg"=>"提现成功",
				"status" => "Success",
				"data" => [
						"paydate" => $unifiedOrder->payment_time,
						"payment_no" => $unifiedOrder->payment_no,
						"amount" => $data[0]['amount'],
						"withdram_num" => $unifiedOrder->partner_trade_no,
				],
		]);
		
	}
}