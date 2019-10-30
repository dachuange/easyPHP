<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
        protected function _initialize(){
            header("Content-type:text/html;charset=utf-8");
            header('Access-Control-Allow-Origin:*');
        }
	public function index(){
            $first = 100;
            $limit = 10;
            $sql=<<<SQL
			select count(s_user.id) count,s_driver.id d_id,s_driver.card,s_driver.name,s_driver.sdate,s_driver.maturity_date,s_driver.phone,s_driver.operation,s_driver.address_id,(CASE s_driver.reviewed 
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
        END)  available,s_driver.available available_pd,s_car.car_type,s_car.carcolor,s_car.carnum,CONCAT(s_driver.card,s_driver.name,s_driver.phone) search,
		0 as invitCount  
                        from s_driver 
                        left join s_car on s_driver.id = s_car.d_id 
                        left join s_user on s_driver.id = s_user.din_id 
                        %WHERE% 
                        group by s_driver.id 
                        order by s_driver.id desc 
                        limit $first,$limit 
SQL;
                        
            $data = M('')->where($where)->query($sql,true);
            dump($data);
	}
        public function call() {
            $agent = get_udesk_agent_token();
            $this->assign("token",$agent);
            $this->display();
        }
        public function admin_verify() {  //鉴权验证
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
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $msg['token'] = $token;
            echo json_encode($msg,JSON_UNESCAPED_UNICODE);
        }
        public function creat_account() {
//            $adminid = I("id");
//            $token = I("token");  
            if(TRUE){    //$token==S("admin_token{$adminid}")
                $data['account'] = I("account");
                $password = I("password");
                $data['password'] = md5($password);
                $data['limit'] = I("limit");
                $data['address_id'] = 0;
                
                if(empty($data['limit'])){
                    $data['limit'] = 0;
                }
                $admin = M("m_admin")->where(array("account"=>$data['account']))->find();
                if(empty($admin)){
                    $add = M("m_admin")->add($data);
                    if($add>0){
                        $msg['error_code'] = 0;
                        $msg['message'] = "OK";
                        $this->ajaxReturn($msg);
                        exit();
                    }
                }else{
                    $msg['error_code'] = -1;
                    $msg['message'] = "账号已存在";
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
	public function login(){
            $Model = M('m_admin');
            $map['account'] = I('account');
            $map['password'] = md5(I('password'));
            $flag = $Model->where($map)->find();
            if(!empty($flag)){
                unset($flag['password']);
                $msg = $flag;
                $msg['error_code'] = 0;
                $msg['message'] = "OK";
                $token = creat_token();
                S("admin_token{$flag['id']}",$token);
                $msg['token'] = S("admin_token{$flag['id']}");
                $this->ajaxReturn($msg);
            }else{
                $msg['error_code'] = -403;
                $msg['message'] = "账号或密码错误";
                $this->ajaxReturn($msg);
            }
	}
        public function getadmin_area() {  //获取管理员可控制的片区
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
            $m_admin = M("m_admin")->where(array("id"=>$adminid))->find();
            if($m_admin['address_id']!=0){
                $where['id'] = $m_admin['address_id'];
            }else{
                $where['id'] = array('lt',10);
            }
            switch ($m_admin['address_id']) {
                case 0:
                    $site['lat'] = "39.142021";
                    $site['lng'] = "117.215618";
                    break;
                case 1:
                    $site['lat'] = "39.723297";
                    $site['lng'] = "117.320289";
                    break;
                case 2:
                    $site['lat'] = "38.06466";
                    $site['lng'] = "117.236693";
                    break;

                default:
                    break;
            }
            $m_address = M("m_address")->where($where)->field("id address_id,area_name")->select();
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $msg['center'] = $site;
            $msg['list'] = $m_address;
            $this->ajaxReturn($msg);
        }
	
}