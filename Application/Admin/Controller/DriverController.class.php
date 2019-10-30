<?php
namespace Admin\Controller;
use Think\Controller;
class DriverController extends HeadsController {
        public function index() {
            $sql=<<<SQL
			select datediff('2019-07-18',s_driver.sdate) 
                        from s_driver 
                        where s_driver.id='8'
SQL;
            $data = M('')->query($sql);
            dump($data);
        }
	public function driver_list(){
            
            $adminid = I("id");  //管理员的ID
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
            }
            $address_id = I("address_id");
            if(!empty($address_id)){
                $where['s_driver.address_id'] = $address_id;
            }
            
            $state = I("state");
            if(!empty($state)){
                $where['s_driver.state'] = $state;
            }
            $reviewed = I("reviewed");
            if(!empty($reviewed)){
                $where['reviewed'] = $reviewed;
            }
            $search = I("search");
            if(!empty($search)){
                $where["CONCAT(s_driver.card,s_driver.name,s_driver.phone)"] = array("like","%{$search}%");
            }
            
            $count_sql = <<<SQL
			select count(s_driver.id) num from s_driver  
                        %WHERE% 
SQL;
            $count = M('')->where($where)->query($count_sql,true);
            $count = $count[0]['num'];
            $page = I("page");
            if(empty($page)){
               $page = 1;
            }
            $limit = C("PAGE_LIMIT");
            $first = ($page-1)*$limit;
            $lastpage = ceil($count/$limit);
            $sql=<<<SQL
			select count(s_user.id) as invitCount,s_driver.id d_id,s_driver.card,s_driver.name,s_driver.sdate,s_driver.maturity_date,s_driver.phone,s_driver.operation,s_driver.address_id,(CASE s_driver.reviewed 
        WHEN 'Y' THEN '审核通过' 
        WHEN 'O' THEN '审核中' 
        WHEN 'N' THEN '审核未通过' 
        END)  reviewed_cn,
            (CASE s_driver.state 
        WHEN 'off' THEN '关闭' 
        WHEN 'stand' THEN '待接单' 
        WHEN 'running' THEN '行程中' 
        END)  state_cn,
            (CASE s_driver.available 
        WHEN 'Y' THEN '正常' 
        WHEN 'snap_prohibited' THEN '临时封禁' 
        WHEN 'lasting_prohibited' THEN '永久分封禁' 
        END)  available,s_driver.available available_pd,s_car.car_type,s_car.carcolor,s_car.carnum,CONCAT(s_driver.card,s_driver.name,s_driver.phone) search 
                        from s_driver 
                        left join s_car on s_driver.id = s_car.d_id 
						left join s_user on s_driver.id = s_user.din_id 
                        %WHERE% 
                        group by s_driver.id 
                        order by s_driver.id desc 
                        limit $first,$limit 
SQL;
                        
            $data = M('')->where($where)->query($sql,true);
//            dump($data);
            //还需返回页数
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $msg['list'] = $data;
            $msg['lastpage'] = $lastpage;
            $msg['count'] = $count;
//            dump($msg);
            $this->ajaxReturn($msg);
	}
        public function up_operation() {
            $d_id = I("d_id");
            $operation = I("operation");
            if(!empty($operation)){
                $data['operation'] = $operation;
            }
            $address_id = I("address_id");
            if(!empty($address_id)){
                $data['address_id'] = $address_id;
            }
            $up = M("s_driver")->where(array("id"=>$d_id))->save($data);
            if($up>0){
                $msg['error_code'] = 0;
                $this->ajaxReturn($msg);
            }else{
                $msg['error_code'] = -1;
                $this->ajaxReturn($msg);
            }
        }
        public function driver_deatil() {
            $adminid = I("id");  //管理员的ID
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
            }
            $d_id = I("d_id");
            if(empty($d_id)){
                $msg['error_code'] = -402;
                $msg['message'] = "d_id为空";
                $this->ajaxReturn($msg);
                exit();
            }
            $where['s_driver.id'] = $d_id;
            $sql=<<<SQL
            select s_driver.id d_id,s_driver.card,s_driver.name,s_driver.sdate,s_driver.maturity_date,s_driver.phone,s_driver.invite_card,s_driver.invite_card,s_driver.idcard,s_driver.amount,s_driver.all_amount,s_driver.urgent_phone,s_driver.urgent_phone,s_driver.lits,s_driver.service_fee_now,
        (CASE s_driver.reviewed 
        WHEN 'Y' THEN '审核通过' 
        WHEN 'O' THEN '审核中' 
        WHEN 'N' THEN '审核未通过' 
        END)  reviewed_cn,(CASE s_driver.state 
        WHEN 'off' THEN '关闭' 
        WHEN 'stand' THEN '待接单' 
        WHEN 'running' THEN '行程中' 
        
        END)  state_cn,
                    (CASE s_driver.available 
        WHEN 'Y' THEN '可用' 
        WHEN 'snap_prohibited' THEN '临时封禁' 
        WHEN 'lasting_prohibited' THEN '永久分封禁' 
        END)  available,
            s_car.car_type,s_car.carcolor,s_car.carnum,ifnull(s_avatar_verify.file,'') avatar,s_avatar_verify.verify,s_avatar_verify.card_img_z,s_avatar_verify.card_img_f,s_avatar_verify.xs_img,s_avatar_verify.js_img,s_avatar_verify.insurance_img,s_avatar_verify.car_pe_img,s_avatar_verify.urgent_img,s_avatar_verify.account_book_img,
                ifnull((select FORMAT(AVG(point),1) from d_point where d_id={$d_id}),5) point,
                ifnull((select COUNT(s_complaint.id) from s_complaint where d_id={$d_id}),0) complaint,
            CONCAT(s_driver.card,s_driver.name,s_driver.phone) search 
                        from s_driver 
                        left join s_car on s_driver.id = s_car.d_id 
                        left join s_avatar_verify on s_driver.id = s_avatar_verify.d_id 
                        %WHERE% 
SQL;
            $data = M('')->where($where)->query($sql,true);
            $msg = $data[0];
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $this->ajaxReturn($msg);
//            dump($msg);
        }
        public function driver_trajectory() {  //司机轨迹
              //订单轨迹
        $d_id = I("d_id");
        if(empty($d_id)){
            $msg['error_code'] = -402;
            $msg['message'] = "d_id为空";
            $this->ajaxReturn($msg);
            exit();
        }
        $sear_time_s = I("sear_time_s");
        if(empty($sear_time_s)){
            $str = date("Y-m-d",NOW_TIME);
            $sear_time_s = strtotime($str);
        }else{
            $sear_time_s = strtotime($sear_time_s);
        }
        
        $sear_time_e = I("sear_time_e");
        if(empty($sear_time_e)){
            $sear_time_e = NOW_TIME;
        }else{
            $sear_time_e = strtotime($sear_time_e);
        }

        $entity_name = "entity_name_".$d_id;
        $status = yingyan_gettrack($entity_name, $sear_time_s,$sear_time_e);
        if($status['status']==0){
            foreach ($status['points'] as $key => $value) {
                $point[$key]['Long'] = $value['longitude'];
                $point[$key]['Lat'] = $value['latitude'];
            }
        }
//        dump($point);
        $this->ajaxReturn($point); 
        }
        public function drive_reviewed() {  //司机审核
            $adminid = I("id");  //管理员的ID
            if(empty($adminid)){
                $msg['error_code'] = -402;
                $msg['message'] = "管理员ID为空";
                $this->ajaxReturn($msg);
                exit();
            }
            $token = I("token");
            if($token==S("admin_token{$adminid}")){
                $d_id = I("d_id");
                $reviewed = I("reviewed");  //Y或者N
                $driver = M("s_driver")->where(array("id"=>$d_id))->field("id,registered_id,unionid,invite_card,reviewed,if(datediff('2019-07-18',s_driver.sdate)>0,datediff('2019-07-18',s_driver.sdate),0) adddate")->find();
                if(!empty($reviewed)&&$driver['reviewed']!=$reviewed){
                    $up = M("s_driver")->where(array("id"=>$d_id))->save(array("reviewed"=>$reviewed));
                    if($up>0){  //审核通过，,结算司机邀请奖励  //并通知提醒
                        //后期改成短信提醒
                        $obj = sendSms_passed($phone);
                        if($reviewed=='Y'){
                            if(!empty($driver['invite_card'])){//结算司机奖励
                                
                                $fee = M("m_service_fee")->where(array("id"=>1))->getField("invite_driver_reward");
                                $sql0 = <<<SQL
                                update s_driver set service_fee_now=service_fee_now+$fee 
                                where  card = {$driver['invite_card']} 
SQL;
                                $up1 = M()->execute($sql0);
                                if($up1>0){
                                    $registered_id = M("s_driver")->where(array("card"=>$driver['invite_card']))->getField("registered_id");
                                    $bodyjson = "您获得{$fee}元奖励金";
                                    $title = "奖励金入账";
                                    $sd = aliyun_pushNotice($registered_id,$bodyjson,$title);
                                    S("aaaa",$sd);
                                }
                            }
                            //这里进行免费天数发放。
                            $adddate = ($driver['adddate']*2)+30;
                            $sql0 = <<<SQL
            UPDATE s_driver SET maturity_date=DATE_ADD(maturity_date,INTERVAL {$adddate} DAY) where id = {$d_id} 
SQL;
                            $up1 = M()->execute($sql0);
                            
                            //这里增加给司机推送审核通过的微信模板消息
                            #Customer_Service($driver['unionid'],'恭喜您成功注册成为e达生活司机，请及时开工接单',"gh_78052d300081");
                        }elseif($reviewed=='N'){
                            $obj = sendSms_fail($phone);
                            //这里增加给司机推送审核驳回的微信模板消息
                            #Customer_Service($driver['unionid'],'很抱歉您的审核信息没有通过，请修改您的信息重新提交',"gh_78052d300081");
                        }
                        $msg['error_code'] = 0;
                        $msg['message'] = "OK";
                        $this->ajaxReturn($msg);
                        exit();
                    }  else {
                        $msg['error_code'] = -405;
                        $msg['message'] = "失败";
                        $this->ajaxReturn($msg);
                        exit();
                    }
                }  else {
                    $msg['error_code'] = -403;
                    $msg['message'] = "审核状态为空";
                    $this->ajaxReturn($msg);
                    exit();
                }
            }else{
                $msg['error_code'] = -101;
                $msg['message'] = "token错误";
                $this->ajaxReturn($msg);
                    exit();
            }
        }
        public function get_avater_list() {  //司机头像审核
            $adminid = I("id");  //管理员的ID
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
            }
            $address_id = I("address_id");
            if(!empty($address_id)){
                $where['s_driver.address_id'] = $address_id;
            }
            $data['s_avatar_verify.verify'] = "O";
    $sql = <<<SQL
    select s_driver.id d_id,s_avatar_verify.file avater,s_driver.card,s_driver.name 
        from s_driver
        left join s_avatar_verify on s_avatar_verify.d_id = s_driver.id 
        %WHERE% 
SQL;
        $res = M()->where($data)->query($sql,TRUE);
        $msg['list'] = $res;
        $msg['error_code'] = 0;
        $msg['message'] = "OK";
        $this->ajaxReturn($msg);
        }
        public function driver_avater_reviewed() {
            $adminid = I("id");  //管理员的ID
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
            }
            $d_id = I("d_id");
            $reviewed = I("reviewed");  //Y或者N
            if(!empty($reviewed)){
                $up = M("s_avatar_verify")->where(array("d_id"=>$d_id))->save(array("verify"=>$reviewed));
                if($up>0){
                    $msg['error_code'] = 0;
                    $msg['message'] = "OK";
                    $this->ajaxReturn($msg);
                    exit();
                }  else {
                    $msg['error_code'] = -405;
                    $msg['message'] = "失败";
                    $this->ajaxReturn($msg);
                    exit();
                }
            }  else {
                $msg['error_code'] = -403;
                $msg['message'] = "审核状态为空";
                $this->ajaxReturn($msg);
                exit();
            }
        }
        public function banned_driver() {
            $adminid = I("id");  //管理员的ID
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
            }
            $d_id = I("d_id");
            if(empty($d_id)){
                $msg['error_code'] = -402;
                $msg['message'] = "d_id为空";
                $this->ajaxReturn($msg);
                exit();
            }
            $day = I("day");
            if(empty($day)){
                $day = 1;
            }
            if($day=="forever"){
                M("s_driver")->where(array("id"=>$d_id))->save(array("available"=>"lasting_prohibited","state"=>"off"));  //被永久封了
                S("drive_token_{$d_id}",NULL);
                $msg['error_code'] = 0;
                $msg['message'] = "OK";
                $this->ajaxReturn($msg);
            }else{
                $time = NOW_TIME + ($day*24*3600);
                $data[0]['unblock'] = date("Y-m-d",$time);
                $data[0]['d_id'] = $d_id;
                $data[0]['current_time'] = date("Y-m-d H:i:s",NOW_TIME);
                M("d_seal")->addAll($data,array(),"unblock,current_time");
                M("s_driver")->where(array("id"=>$d_id))->save(array("available"=>"snap_prohibited","state"=>"off"));  //被封了
                S("drive_token_{$d_id}",NULL);
                S("drive_{$d_id}",NULL);
                $msg['error_code'] = 0;
                $msg['message'] = "OK";
                $this->ajaxReturn($msg);
                exit();
            }
            
        }
        public function unbanned_driver() {  //解封司机
            $adminid = I("id");  //管理员的ID
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
            }
            $d_id = I("d_id");
            if(empty($d_id)){
                $msg['error_code'] = -402;
                $msg['message'] = "d_id为空";
                $this->ajaxReturn($msg);
                exit();
            }
            $s_driver = M("s_driver")->where(array("id"=>$d_id))->field("id,available")->find();
            
            if($s_driver['available']=="lasting_prohibited"){  //永久封禁
                M("s_driver")->where(array("id"=>$d_id))->save(array("available"=>"Y")); 
                $msg['error_code'] = 0;
                $msg['message'] = "OK";
                $this->ajaxReturn($msg);
            }elseif($s_driver['available']=="snap_prohibited"){
                $time = NOW_TIME;
                $data[0]['unblock'] = date("Y-m-d",$time);
                $data[0]['d_id'] = $d_id;
                M("d_seal")->addAll($data,array(),"unblock,current_time");
                M("s_driver")->where(array("id"=>$d_id))->save(array("available"=>"Y"));  //解封
                $msg['error_code'] = 0;
                $msg['message'] = "OK";
                $this->ajaxReturn($msg);
                exit();
            }
            
        }
        
        public function get_invitUser_list(){
        	$page = I("page",1);
        	$limit = C("PAGE_LIMIT");
        	$first = ($page-1)*$limit;
        	$driverId = I("driverId");
        	$startTime = I("startTime");
        	$endTime = I("endTime");
        	$count_sql = "select count(id) as counts from s_user where din_id = $driverId";   
        	$sql = "select `headimgurl`,`nickname`,`phone`,`sdate` from s_user where din_id = $driverId";
        	if(!empty($startTime)){
        		$sql .= " and sdate >= '$startTime'";
        		$count_sql .= " and sdate >= '$startTime'";
        	}
        	if(!empty($startTime)){
        		$sql .= " and sdate <= '$endTime'";
        		$count_sql .= " and sdate <= '$endTime'";
        	}
        		
        	$sql .= " limit $first,$limit";
        	$count = M()->query($count_sql)[0]['counts'];
        	$lastpage = ceil($count/$limit);
        	$data = M()->query($sql);
        	
        	$msg['error_code'] = 0;
        	$msg['message'] = "OK";
        	$msg['list'] = $data;
        	$msg['lastpage'] = $lastpage;
        	$msg['count'] = $count;
        	$this->ajaxReturn($msg);
        }
        
}