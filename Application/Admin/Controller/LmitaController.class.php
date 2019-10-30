<?php
namespace Admin\Controller;
use Think\Controller;
class LmitaController extends HeadsController {
	public function index(){
            dump(S("admin_token{$adminid}"));
	}
        public function driver_state_list() {
            $address_id = I("address_id");
            if(!empty($address_id)){
                $where['s_driver.address_id'] = $address_id;
            }
            $where['reviewed']='Y';
            $sql=<<<SQL
                select count(s_driver.id) count,s_driver.state,(CASE s_driver.state 
        WHEN 'off' THEN '未在岗' 
        WHEN 'stand' THEN '待接单' 
        WHEN 'running' THEN '行程中' 
        END)  state_cn from s_driver 
                    %WHERE% 
                group by s_driver.state 
SQL;
            $data = M('')->where($where)->query($sql,true);
            
            $all = M("s_driver")->where($where)->field("count(id) count")->find();
            $all['state'] = '';
            $all['state_cn'] = '全部';
            array_unshift($data,$all); 
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $msg['list'] = $data;
            $this->ajaxReturn($msg);
        }
        public function driver_list_con() {
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
            $address_id = I("address_id");
            if(!empty($address_id)){
                $where['s_driver.address_id'] = $address_id;
            }
            $state = I("state");
            if(!empty($state)){
                $where['state'] = $state;
            }
            $reviewed = 'Y';
            if(!empty($reviewed)){
                $where['reviewed'] = $reviewed;
            }
            $search = I("search");
            if(!empty($search)){
                $where["CONCAT(s_driver.card,s_driver.name,s_driver.phone)"] = array("like","%{$search}%");
            }
            $today['start'] = date("Y-m-d 00:00:00",NOW_TIME);
            $today['end'] = date("Y-m-d 23:59:59",NOW_TIME);
            $sql=<<<SQL
			select s_driver.id d_id,s_driver.card,s_driver.name,s_driver.phone,s_driver.lits,
                        (select count(id) from b_order where b_order.d_id = s_driver.id) counts,(select count(id) from b_order where sdate between '{$today['start']}' and '{$today['end']}' and b_order.state<>'cannal' and b_order.d_id = s_driver.id ) daylits,(select count(id) from b_order where sdate between '{$today['start']}' and '{$today['end']}' and b_order.d_id = s_driver.id) daycounts 
                        from s_driver 
                        %WHERE% 
                        order by daylits desc 
SQL;
            $data = M('')->where($where)->query($sql,true);
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $msg['list'] = $data;
            $this->ajaxReturn($msg);
        }
        public function order_state_list() {
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
            $where['b_order.d_id']=I("d_id");
            $sql=<<<SQL
                select count(b_order.id) count,b_order.state,(CASE b_order.state 
    WHEN 'on' THEN '尚未接驾' 
    WHEN 'active' THEN '行程中' 
    WHEN 'wait_pay' THEN '待支付' 
    WHEN 'end' THEN '订单结束' 
    WHEN 'cannal' THEN '订单被取消' 
    END)  state_cn from b_order 
                    %WHERE% 
                group by b_order.state 
SQL;
            $data = M('')->where($where)->query($sql,true);

            $all = M("b_order")->where($where)->field("count(id) count")->find();
            $all['state'] = '';
            $all['state_cn'] = '全部';
            array_unshift($data,$all);
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $msg['list'] = $data;
            $this->ajaxReturn($msg);
        }
        public function driver_order_list_con() {
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
        $d_id = I("d_id");
        $where['b_order.d_id'] = $d_id;
        $state = I("state");
        if(!empty($state)){
            $where['b_order.state'] = $state;
        }
        $sql=<<<SQL
                    select b_order.id o_id,b_order.order_num,b_order.saddress,b_order.eaddress,b_order.warning,b_order.amount,b_order.sdate,
        (CASE b_order.state 
    WHEN 'on' THEN '尚未接驾' 
    WHEN 'active' THEN '行程中' 
    WHEN 'wait_pay' THEN '待支付' 
    WHEN 'end' THEN '订单结束' 
    WHEN 'cannal' THEN '订单被取消' 
    END)  state_cn,(CASE b_order.source 
    WHEN 'Applets' THEN '小程序' 
    WHEN 'No_public' THEN '公众号' 
    WHEN 'Out_line' THEN '线下' 
    END) source,(CASE b_order_funding.method 
    WHEN 'nopublic' THEN '公众号支付' 
    WHEN 'offline' THEN '线下支付' 
    END) paymethod,s_driver.phone d_phone,s_driver.name d_name,s_user.phone u_phone,s_user.nickname,
        CONCAT(b_order.order_num) search 
                    from b_order 
                left join s_driver on s_driver.id = b_order.d_id 
                left join s_user on s_user.id = b_order.u_id 
                left join b_order_funding on b_order.order_num = b_order_funding.order_num 
                    %WHERE% 
                    order by b_order.id desc 
SQL;
        $data = M('')->where($where)->query($sql,true);
        //还需返回页数
        $msg['error_code'] = 0;
        $msg['message'] = "OK";
        $msg['list'] = $data;
//            dump($msg);
        $this->ajaxReturn($msg);
        }
	
}