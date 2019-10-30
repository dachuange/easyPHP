<?php
namespace Admin\Controller;
use Think\Controller;
class MoneyController extends HeadsController {
	public function order_flow(){  //订单流水
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
            $search = I("search");
            if(!empty($search)){
                $where["CONCAT(b_order_funding.order_num)"] = array("like","%{$search}%");
            }
            $count_sql = <<<SQL
			select count(b_order_funding.id) num from b_order_funding 
                        left join b_order on b_order.order_num = b_order_funding.order_num 
                        left join s_driver on s_driver.id = b_order.d_id 
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
			select b_order_funding.order_num,b_order_funding.date,b_order.amount,
                    if(b_order.coupon_id=0,0,(select amount from s_coupon where id = b_order.coupon_id)) s_coupon_amount,(CASE b_order_funding.method 
    WHEN 'nopublic' THEN '公众号支付' 
    WHEN 'offline' THEN '线下支付' 
    WHEN 'applets' THEN '小程序支付' 
    END)  method 
                        from b_order_funding 
                        left join b_order on b_order.order_num = b_order_funding.order_num 
                        left join s_driver on s_driver.id = b_order.d_id 
                        %WHERE% 
                        order by b_order_funding.id desc 
                        limit $first,$limit 
SQL;
            $data = M('')->where($where)->query($sql,true);
            //还需返回页数
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $msg['list'] = $data;
            $msg['lastpage'] = $lastpage;
            $msg['count'] = $count;
//            dump($msg);
            $this->ajaxReturn($msg);
	}
        public function platform_flow(){  //平台流水
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
            if($address_id!=0){
                $msg['error_code'] = -402;
                $msg['message'] = "您无访问平台流水的权限";
                $this->ajaxReturn($msg);
                exit();
            }
            
            $search = I("search");
            if(!empty($search)){
                $where["CONCAT(b_platform_funding.full_num)"] = array("like","%{$search}%");
            }
            $count_sql = <<<SQL
			select count(b_platform_funding.id) num from b_platform_funding 
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
			select b_platform_funding.full_num,b_platform_funding.date,b_platform_funding.amount,(CASE b_platform_funding.type 
    WHEN 'order' THEN '订单收入' 
    WHEN 'payment' THEN '司机月费' 
    WHEN 'envelope' THEN '优惠券支出' 
    END)  type 
                        from b_platform_funding 
                        %WHERE% 
                        order by b_platform_funding.id desc 
                        limit $first,$limit 
SQL;
            $data = M('')->where($where)->query($sql,true);
            //还需返回页数
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $msg['list'] = $data;
            $msg['lastpage'] = $lastpage;
            $msg['count'] = $count;
//            dump($msg);
            $this->ajaxReturn($msg);
	}
        public function withdraw_list() {  //提现列表
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
            $search = I("search");
            if(!empty($search)){
                $where["CONCAT(d_withdraw.banknum)"] = array("like","%{$search}%");
            }
            $state = I("state");
            if(!empty($state)){
                $where['d_withdraw.state'] = $state;
            }
            $count_sql = <<<SQL
			select count(d_withdraw.id) num from d_withdraw 
                        left join s_driver on s_driver.id = d_withdraw.d_id 
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
			select s_driver.card,s_driver.name,d_withdraw.id withdrawid,d_withdraw.d_id,d_withdraw.banknum,d_withdraw.bankaddress,d_withdraw.banktype,d_withdraw.pename,d_withdraw.bankphone,d_withdraw.amount,d_withdraw.date,d_withdraw.state,d_withdraw.fuse_reason,(case d_withdraw.state 
        WHEN 'Y' THEN '已打款' 
        WHEN 'N' THEN '打款失败' 
        WHEN 'O' THEN '正在处理' 
        end) state_c  
                        from d_withdraw 
                        left join s_driver on s_driver.id = d_withdraw.d_id 
                        %WHERE% 
                        order by d_withdraw.id desc 
                        limit $first,$limit 
SQL;
            $data = M('')->where($where)->query($sql,true);
            //还需返回页数
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $msg['list'] = $data;
            $msg['lastpage'] = $lastpage;
            $msg['count'] = $count;
//            dump($msg);
            $this->ajaxReturn($msg);
        }
        public function withdraw_submit() {  //提现审核通过
            
            
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
            $m = M();
            $m->startTrans();   //开启事务
            $withdrawid = I("withdrawid");
            $pd = I("pd");
            $info = M("d_withdraw")->where(array("id"=>$withdrawid))->field("amount,d_id,withdram_num")->find();
            if($pd=="N"){  //审核不通过
                $fuse_reason = I("fuse_reason");
                $up = M("d_withdraw")->where(array("id"=>$withdrawid))->save(array("state"=>"N","fuse_reason"=>$fuse_reason));
                
                $registered_id = M("s_driver")->where(array("id"=>$info['d_id']))->getField("registered_id");
                $bodyjson = "您的提现申请未通过，请联系客服尽快确认信息正确性。";
                $title = "提现申请通知";
                $sd = aliyun_pushNotice($registered_id,$bodyjson,$title);
                $sql0 = <<<SQL
            update s_driver set amount=amount+{$info['amount']} 
                where  id = {$info['d_id']} 
SQL;
                $up1 = M()->execute($sql0);
                $m->commit();
                $msg['error_code'] = 0;
                $msg['message'] = "OK";
                $this->ajaxReturn($msg);
                exit();
            }

            $up = M("d_withdraw")->where(array("id"=>$withdrawid))->save(array("state"=>"Y"));
            if($up>0){
                
                $palfrom['full_num'] = $info['withdram_num'];
                $palfrom['date'] = date("Y-m-d H:i:s",NOW_TIME);
                $palfrom['amount'] = 0-$info['amount'];
                $palfrom['type'] = "withdraw";
                $add = M("b_platform_funding")->add($palfrom);
                if($add>0){
                    $m->commit();
                    //提醒通知
                    $registered_id = M("s_driver")->where(array("id"=>$info['d_id']))->getField("registered_id");
                    $bodyjson = "您的提现申请已通过";
                    $title = "提现申请通知";
                    $sd = aliyun_pushNotice($registered_id,$bodyjson,$title);
                    
                    $msg['error_code'] = 0;
                    $msg['message'] = "OK";
                    $this->ajaxReturn($msg);
                    exit();
                }else{
                    $m->rollback();
                    //还需返回页数
                    $msg['error_code'] = -2;
                    $msg['message'] = "该次审核异常";
                    $this->ajaxReturn($msg);
                    exit();
                }
            }else{
                $m->rollback();
                //还需返回页数
                $msg['error_code'] = -1;
                $msg['message'] = "重复审核";
                $this->ajaxReturn($msg);
                exit();
                
            }
                
        
            
        }
       
}