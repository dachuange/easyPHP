<?php
namespace Admin\Controller;
use Think\Controller;
class SettingController extends Controller {
        protected function _initialize(){
            header("Content-type:text/html;charset=utf-8");
            header('Access-Control-Allow-Origin:*');
        }
	public function d_fee_list() {  //车费展示
            $adminid = I("id");  //管理员的ID
            if(empty($adminid)){
                $msg['error_code'] = -402;
                $msg['message'] = "管理员ID为空";
                $this->ajaxReturn($msg);
                exit();
            }
//            $token = I("token");
//            if($token!=S("admin_token{$adminid}")){
//                $msg['error_code'] = -101;
//                $msg['message'] = "token错误";
//                $this->ajaxReturn($msg);
//                exit();
//            }
            $address_id = I("address_id");
            switch ($address_id) {
                case '1':
                    $info = M("m_billing_n")->where(array("id"=>1))->find();
                    break;
                case '2':
                    $info = M("m_billing_c")->where(array("id"=>1))->find();
                    break;
                default:
                    break;
            }
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $msg['list'] = $info;
            $this->ajaxReturn($msg);
        }
        public function d_fee_set() {  //设置计价规则
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
            
            $data = $_POST;
            unset($data['address_id']);
            unset($data['id']);
            foreach ($data as $key => $value) {
                if(empty($value)){
                    $data[$key] = 0;
                }
            }
            switch ($address_id) {
                case '1':
            $id = M("m_billing_n")->where(array("id"=>1))->save($data);
                    break;
                case '2':
                    $id = M("m_billing_c")->where(array("id"=>1))->save($data);
                    break;
                default:
                    break;
            }
            if($id>0){
                S("billing_n",NULL);
                S("billing_c",NULL);
                $msg['error_code'] = 0;
                $msg['message'] = "OK";
            }  else {
                $msg['error_code'] = -1;
                $msg['message'] = "无可更新值";
            }
            $this->ajaxReturn($msg);
            exit();
        }
        public function withdrawal_limit_get() { //提现设置获取
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
                $where['m_service_fee.address_id'] = $address_id;
            }
            $sql=<<<SQL
        select m_service_fee.withdraw_set,m_service_fee.wx_withdraw_set,m_service_fee.address_id,m_address.area_name 
                from m_service_fee 
                left join m_address on m_address.id = m_service_fee.address_id 
                    %WHERE% 
SQL;
            $m_service_fee = M('')->where($where)->query($sql,true);
         
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $msg['list'] = $m_service_fee;

            $this->ajaxReturn($msg);
        }
        public function withdrawal_limit_set() {  //提现设置
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
            $up = M("m_service_fee")->addAll($data,array(),"wx_withdraw_set,withdraw_set");
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
        public function userreward_get() {  //用户等待金
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
                $where['m_user_wait_reward_set.address_id'] = $address_id;
            }
            $sql=<<<SQL
                select m_user_wait_reward_set.free_time,m_user_wait_reward_set.cost_every,m_user_wait_reward_set.every_amount,m_user_wait_reward_set.limit_amount,
            m_user_wait_reward_set.address_id,m_address.area_name 
                from m_user_wait_reward_set 
                left join m_address on m_address.id = m_user_wait_reward_set.address_id 
                    %WHERE% 
SQL;
            $info = M('')->where($where)->query($sql,true);
            
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $msg['list'] = $info;
            $this->ajaxReturn($msg);
        }
        public function userreward_set() {
            $adminid = I("id");  //管理员的ID
            S("wait_reward",NULL);
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
            $up = M("m_user_wait_reward_set")->addAll($data,array(),"free_time,cost_every,every_amount,limit_amount");
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
        public function gettaxilimitarea() {  //获取各区的打车范围
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
            $arr = get_addressid();
            $this->ajaxReturn($arr[$address_id]);
        }
	public function assign_driver_get() {  //派单设置
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
                $where['m_address_config.address_id'] = $address_id;
            }
            $sql=<<<SQL
                select m_address_config.nearby_kilometers,m_address_config.broadcasting_kilometers,m_address_config.reservation_time,m_address_config.hall_time,m_address_config.blacklist_time,
                m_address.area_name,m_address.id address_id 
                from m_address_config 
                left join m_address on m_address.id = m_address_config.address_id 
                    %WHERE% 
SQL;
            $info = M('')->where($where)->query($sql,true);
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $msg['list'] = $info;
            $this->ajaxReturn($msg);
        }
        public function assign_driver_set() {  //派单设置提交
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
            $up = M("m_address_config")->addAll($data,array(),"nearby_kilometers,broadcasting_kilometers,reservation_time,hall_time,blacklist_time");
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
}