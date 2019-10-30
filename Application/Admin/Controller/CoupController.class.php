<?php
namespace Admin\Controller;
use Think\Controller;
class CoupController extends HeadsController {
        public function getcoup() {  //获取红包设置
            $adminid = I("id");  //管理员的ID
            if(empty($adminid)){
                $msg['error_code'] = -402;
                $msg['message'] = "管理员ID为空";
                $this->ajaxReturn($msg);
                exit();
            }
            $address_id = I("address_id");
            if(!empty($address_id)){
                $where['m_coupon_set.address_id'] = $address_id;
        }
            $sql=<<<SQL
                select m_coupon_set.first_amount,m_coupon_set.amount,m_coupon_set.period,
            m_coupon_set.address_id,m_address.area_name 
                from m_coupon_set 
                left join m_address on m_address.id = m_coupon_set.address_id 
                    %WHERE% 
SQL;
            $info = M('')->where($where)->query($sql,true);
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $msg['list'] = $info;
            $this->ajaxReturn($msg);
        }
        //获取城市列表
        public function getcity(){
            $adminid = I("id");  //管理员的ID
            if(empty($adminid)){
                $msg['error_code'] = -402;
                $msg['message'] = "管理员ID为空";
                $this->ajaxReturn($msg);
                exit();
            }
            $where = array(
                'id' => $adminid,
            );
            $address_id = M('m_admin') -> where($where) -> getField('address_id');
            if($address_id == 0){
                $info = M('m_address') -> select();
                $msg['error_code'] = 0;
                $msg['message'] = "OK";
                $msg['list'] = $info;
                $this->ajaxReturn($msg);
            }else{
                $where = array(
                    'address_id' => $address_id,
                );
                $info = M('m_address') ->where($where) -> find();
                $msg['error_code'] = 1;
                $msg['message'] = "OK";
                $msg['list'] = $info;
                $this->ajaxReturn($msg);
            }
            //dump($address_id);exit;
        }
        //新版获取红包设置
        public function getcoupnew() {  //获取红包设置
            $adminid = I("id");  //管理员的ID
            if(empty($adminid)){
                $msg['error_code'] = -402;
                $msg['message'] = "管理员ID为空";
                $this->ajaxReturn($msg);
                exit();
            }
            $address_id = I("address_id");
            if(!empty($address_id)){
                $where['m_coupon_set.address_id'] = $address_id;
            }
            $where = array(
                'address_id' => $address_id,
            );
            $info = M('m_coupon_set')->where($where)->find();
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $msg['list'] = $info;
            $this->ajaxReturn($msg);
        }
	public function setcoup(){  //设置红包金额
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
            $setArray = I("setArray");
            foreach ($setArray as $key => $value) {
                foreach ($value as $k => $v) {
                    $data[$key][$k] = $v;
                }
            }
            $up = M("m_coupon_set")->addAll($data,array(),"first_amount,amount,period");
            if($up>0){
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $this->ajaxReturn($msg);
            }else{
                $msg['error_code'] = -1;
                $msg['message'] = "请修改需更新值";
                $this->ajaxReturn($msg);
	}
	}
	//新版红包设置功能，单个地区设置（之前是一个页面多个地区批量设置）
	public function setcoupnew(){
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
	        $where['m_coupon_set.address_id'] = $address_id;
	    }
	    $setArray = I("setArray");
	    //dump($setArray);exit;
	    ($setArray['first_amount']<0.01)?$setArray['first_amount']=0.01:$setArray['first_amount']=$setArray['first_amount'];
	    if($setArray['first_amount'] > 100){
	        $msg['error_code'] = -501;
	        $msg['message'] = "新注册发放金额不能大于100";
	        $this->ajaxReturn($msg);
	        exit();
	    }
	    $data['first_amount'] = $setArray['first_amount'];
	    ($setArray['amount']<0.01)?$setArray['amount']=0.01:$setArray['amount']=$setArray['amount'];
	    if($setArray['amount'] > 100){
	        $msg['error_code'] = -502;
	        $msg['message'] = "拉新发放金额不能大于100";
	        $this->ajaxReturn($msg);
	        exit();
	    }
	    $data['amount'] = $setArray['amount'];
	    $data['period'] = $setArray['period'];
	    $data['invite_period'] = $setArray['invite_period'];
	    $data['new_state'] = $setArray['new_state'];
	    $data['invite_state'] = $setArray['invite_state'];
	    $data['switch_state'] = $setArray['switch_state'];
	    //dump($data);exit;
	    $where = array(
	        'address_id' => $address_id,
	    );
	    $up = M("m_coupon_set")->where($where)->save($data);
	    if($up>0){
	        $msg['error_code'] = 0;
	        $msg['message'] = "OK";
	        $this->ajaxReturn($msg);
	    }else{
	        $msg['error_code'] = -1;
	        $msg['message'] = "请修改需更新值";
	        $this->ajaxReturn($msg);
	    }
	}
	//新红包发放
	public function giftcoupnew() {//发送优惠券
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
	    $useridstr = I("idstr");
	    if(empty($useridstr)){
	        $msg['error_code'] = -501;
	        $msg['message'] = "发送人数为0";
	        $this->ajaxReturn($msg);
	        exit();
	    }
	    $useropen = M("s_user")->where(array("id"=>array("in","$useridstr")))->field("id,openid")->select();
	    //dump($useropen);exit;
	    foreach ($useropen as $key => $value) {
	        $open[$value['id']] = $value['openid'];
	    }
	    if($useridstr=="all"){
	        $sql = <<<SQL
                    select group_concat(s_user.id separator ',') idstr
                    from s_user
SQL;
	        $data = M('')->query($sql,true);
	        $useridstr = $data[0]['idstr'];
	    }
	    $amount = I("amount");
	    ($amount<0.01)?$amount=0.01:$amount=$amount;
	    if(empty($amount) || $amount > 99){
	        $msg['error_code'] = -501;
	        $msg['message'] = "发放金额不能为空或者大于99";
	        $this->ajaxReturn($msg);
	        exit();
	    }
	    //红包类型
	    $typeid = I("typeid");
	    $sdate = date('Y-m-d',time());
	    //到期时间 需要改 现在时间加输入的天数
	    $edate1 = I("edate");
	    if(empty($edate1)){
	        $edate1 = 0;
	    }
	    $edate = date("Y-m-d",strtotime("+$edate1 day"));
	    //推送内容
	    $send_text = I("send_text");
	    $coup_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx73e0cea3e01d21f6&redirect_uri=https%3A%2F%2Fwww.taxisanjiayi.com%2FUser%2Fcoupon&response_type=code&scope=snsapi_userinfo&state=gh_78052d300081#wechat_redirect";
	    $send_text = $send_text.'<a href="'.$coup_url.'">点击查看</a>';
	    $arr = explode(",", $useridstr);
	    
	    foreach ($arr as $key => $value) {
	        $coups[$key]['u_id'] = $value;
	        $coups[$key]['amount'] = $amount;
	        $coups[$key]['sdate'] = $sdate;
	        $coups[$key]['edate'] = $edate;
	        $coups[$key]['typeid'] = $typeid;
	        $coups[$key]['use_date'] =$coups[$key]['sdate']."~".$coups[$key]['edate'];
	        $coups[$key]['user'] = "N";
	        if(!empty($send_text)){
	            
	            Customer_Service($open[$value], $send_text,"gh_78052d300081");
	        }
	    }
	    $add = M("s_coupon")->addAll($coups);
	    if($add>0){
	        $xs['adminid'] = $adminid;
	        $xs['userstr'] = $useridstr;
	        $xs['date'] = date("Y-m-d H:i:s",NOW_TIME);
	        $xs['amount'] = $amount;
	        $xs['nums'] = count($arr);
	        $codadd = M("s_coupon_recode")->add($xs);
	        
	        $msg['error_code'] = 0;
	        $msg['message'] = "OK";
	        $this->ajaxReturn($msg);
	    }else{
	        $msg['error_code'] = -501;
	        $msg['message'] = "发送优惠券失败";
	        $this->ajaxReturn($msg);
	    }
	}
        public function giftcoup() {//发送优惠券
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
            $useridstr = I("idstr");
            if(empty($useridstr)){
                $msg['error_code'] = -501;
                $msg['message'] = "发送人数为0";
                $this->ajaxReturn($msg);
                exit();
            }
            $useropen = M("s_user")->where(array("id"=>array("in","$useridstr")))->field("id,openid")->select();
            foreach ($useropen as $key => $value) {
                $open[$value['id']] = $value['openid'];
            }
            if($useridstr=="all"){
                $sql = <<<SQL
                    select group_concat(s_user.id separator ',') idstr 
                    from s_user 
SQL;
		$data = M('')->query($sql,true);
                $useridstr = $data[0]['idstr'];
            }
            $amount = I("amount");
            if(empty($amount)){
                $msg['error_code'] = -501;
                $msg['message'] = "发放金额不能为空";
                $this->ajaxReturn($msg);
                exit();
            }
            $typeid = I("typeid");
            $sdate = I("sdate");
            $edate = I("edate");
            $send_text = I("send_text");
            $coup_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx73e0cea3e01d21f6&redirect_uri=https%3A%2F%2Fwww.taxisanjiayi.com%2FUser%2Fcoupon&response_type=code&scope=snsapi_userinfo&state=gh_78052d300081#wechat_redirect";
            $send_text = $send_text.'<a href="'.$coup_url.'">点击查看</a>';
            $arr = explode(",", $useridstr);
            
            foreach ($arr as $key => $value) {
                $coups[$key]['u_id'] = $value;
                $coups[$key]['amount'] = $amount;
                $coups[$key]['sdate'] = $sdate;
                $coups[$key]['edate'] = $edate;
                $coups[$key]['typeid'] = $typeid;
                $coups[$key]['use_date'] =$coups[$key]['sdate']."~".$coups[$key]['edate'];
                $coups[$key]['user'] = "N";
                if(!empty($send_text)){
                    
                    Customer_Service($open[$value], $send_text,"gh_78052d300081");
                } 
            }
            $add = M("s_coupon")->addAll($coups);
            if($add>0){
                $xs['adminid'] = $adminid;
                $xs['userstr'] = $useridstr;
                $xs['date'] = date("Y-m-d H:i:s",NOW_TIME);
                $xs['amount'] = $amount;
                $xs['nums'] = count($arr);
                $codadd = M("s_coupon_recode")->add($xs);
                
                $msg['error_code'] = 0;
                $msg['message'] = "OK";
                $this->ajaxReturn($msg);
            }else{
                $msg['error_code'] = -501;
                $msg['message'] = "发送优惠券失败";
                $this->ajaxReturn($msg);
            }
        }
        public function coup_type() {
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
            $type = M("s_coupon_type")->field("id couid,val")->select();
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $msg['type'] = $type;
            $this->ajaxReturn($msg);
            exit();
        }
        public function incentive_info() {  //获取奖励金
            $adminid = I("id");  //管理员的ID
            if(empty($adminid)){
                $msg['error_code'] = -402;
                $msg['message'] = "管理员ID为空";
                $this->ajaxReturn($msg);
                exit();
            }
            $address_id = I("address_id");
            if(!empty($address_id)){
                $where['address_id'] = $address_id;
        }
            $sql=<<<SQL
        select m_service_fee.mon_fee,m_service_fee.invite_driver_reward,m_service_fee.invite_attention_reward,m_service_fee.invite_userr_reward,
            m_service_fee.address_id,m_address.area_name 
                from m_service_fee 
                left join m_address on m_address.id = m_service_fee.address_id 
                    %WHERE% 
SQL;
            $info = M('')->where($where)->query($sql,true);

            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $msg['list'] = $info;
            $this->ajaxReturn($msg);
        }
        public function driver_incentive() { //司机奖励金设置
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
            $setArray = I("setArray");
            foreach ($setArray as $key => $value) {
                foreach ($value as $k => $v) {
                    $data[$key][$k] = $v;
                }
            }
            $up = M("m_service_fee")->addAll($data,array(),"mon_fee,invite_driver_reward,invite_userr_reward,invite_attention_reward");
            if($up>0){
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $this->ajaxReturn($msg);
            }else{
                $msg['error_code'] = -1;
                $msg['message'] = "请修改需更新值";
                $this->ajaxReturn($msg);
        }
        }
        public function giftcoup_record() {
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
                $where['m_admin.address_id'] = $address_id;
            }
            $sear_time_s = I("sear_time_s");
            $sear_time_e = I("sear_time_e");
            if(empty($sear_time_s)){
                $sear_time_s = date("Y-m-d H:i:s",0);
            }
            if(empty($sear_time_e)){
                $sear_time_e = date("Y-m-d H:i:s",NOW_TIME);
            }
            $where['s_coupon_recode.date'] = array("between","{$sear_time_s},{$sear_time_e}");
            $count_sql = <<<SQL
			select count(s_coupon_recode.id) num from s_coupon_recode  
                        left join m_admin on m_admin.id = s_coupon_recode.adminid 
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
       
            $sql = <<<SQL
                    select s_coupon_recode.id record_id,m_admin.account,s_coupon_recode.adminid,s_coupon_recode.date,s_coupon_recode.amount,s_coupon_recode.nums 
                    from s_coupon_recode 
                    left join m_admin on m_admin.id = s_coupon_recode.adminid 
                    order by s_coupon_recode.id desc 
                    limit $first,$limit
                    
SQL;
		$data = M('')->query($sql,true);
                $msg['error_code'] = 0;
                $msg['message'] = "OK";
                $msg['list'] = $data;
                $msg['lastpage'] = $lastpage;
                $msg['count'] = $count;
                $this->ajaxReturn($msg);
}
        public function getgiftcoup_user() {  //查看发放记录里的 收红包人
//            $adminid = I("id");  //管理员的ID
//            if(empty($adminid)){
//                $msg['error_code'] = -402;
//                $msg['message'] = "管理员ID为空";
//                $this->ajaxReturn($msg);
//                exit();
//            }
//            $token = I("token");
//            if($token!=S("admin_token{$adminid}")){
//                $msg['error_code'] = -101;
//                $msg['message'] = "token错误";
//                $this->ajaxReturn($msg);
//                exit();
//            }
            $record_id = I("record_id");
            $userstr = M("s_coupon_recode")->where(array("id"=>$record_id))->getField("userstr");
            $sql = <<<SQL
                    select s_user.nickname,s_user.phone 
                    from s_user 
                    where s_user.id in ($userstr)
       
SQL;
		$data = M('')->query($sql,true);
                $msg['error_code'] = 0;
                $msg['message'] = "OK";
                $msg['list'] = $data;
                $this->ajaxReturn($msg);
}
       
}