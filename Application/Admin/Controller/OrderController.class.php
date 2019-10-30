<?php
namespace Admin\Controller;
use Think\Controller;
class OrderController extends HeadsController {
    public function index(){  //电话叫单
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
        $phone = I("phone");
        if(empty($phone)){
            $msg['error_code'] = -404;
            $msg['message'] = "phone is null";
            $this->ajaxReturn($msg);
            exit();
        }
        $s_lat = I("s_lat");  //此处接收百度地图的坐标
        $s_lng = I("s_lng");
        $s_cn_local = I("s_local");  //"慧谷大厦";//
        $user = M("s_user")->where(array("phone"=>$phone))->field("id")->find();
        if(empty($user)){  //这人没关注过公众号
            $data['phone'] = $phone;
            $data['sdate'] = date("Y-m-d H:i:s",NOW_TIME);
            $data['unionid'] = rand(10,99)."".NOW_TIME;
            $userid = M("s_user")->add($data);
        }else{
            $userid = $user['id'];
        }
        $s_site = Convert_BD09_To_GCJ02($s_lat, $s_lng);  //百度转腾信坐标   //维度，经度
        $d_id = select_drive($s_site['lat'],$s_site['lng']); //维度，经度
        $driveregist = M("s_driver")->where(array("id"=>$d_id))->field("registered_id")->find();
        if(!$d_id){
            $msg['error_code'] = 1;
            $msg['message'] = "当前地段无司机可接单，请稍后再试，或更换上车地点！";
            $this->ajaxReturn($msg);
            exit();
        }
        $m = M();
        $m->startTrans();   //开启事务
        
        $id = creat_order_offline($userid, $d_id, $s_cn_local, $s_site['lat'], $s_site['lng'],NULL,NULL,NULL,"Out_line");
        if($id){
            $d_site = S("drive_{$d_id}");
            $addsite = thorough_order($id, $s_lat, $s_lng, $d_site['lat'], $d_site['lng']);
            //派单成功，更改司机状态
            $up = driver_state_up($d_id,"running");
            if($up>0&&$addsite>0){
                $m->commit();
            }  else {
                $m->rollback();
                $msg['error_code'] = -1;
                $msg['message'] = "司机状态更改失败";
                $this->ajaxReturn($msg);
                exit();
            }
            $order['state'] = "new";    //订单状态
            S("order_fee_{$id}",$order);   //缓存订单计价

            $site = S("drive_{$d_id}");
            $dist = round(getdistance($s_site['lng'], $s_site['lat'], $site['lng'], $site['lat']),2);
            $kilometer = round(($dist/1000),2);
            $msg = get_drive_info($d_id);
            $msg['o_id'] = $id;
            $msg['distance'] = $kilometer;
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $info = GetOrderInfo($id);
            S("seek_order_{$info['d_id']}",$info);
            $this->ajaxReturn($msg);
        }else{
            //已有活动中的订单，给用户反馈
            $m->rollback();
            $msg['error_code'] = 2;
            $msg['message'] = "该用户已叫车，请不要重复叫车！如需重叫，请先取消订单！";
            $this->ajaxReturn($msg);
            exit();
        }
    }
    public function orderlist() {
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
            $where["CONCAT(b_order.order_num,s_driver.name,s_driver.phone,s_user.phone)"] = array("like","%{$search}%");
        }
        $state = I("state");
        if(!empty($state)){
            $where['b_order.state'] = $state;
        }
        $sear_time_s = I("sear_time_s");
        $sear_time_e = I("sear_time_e");
        if(empty($sear_time_s)){
            $sear_time_s = date("Y-m-d H:i:s",0);
        }
        if(empty($sear_time_e)){
            $sear_time_e = date("Y-m-d H:i:s",NOW_TIME);
        }  else {
            $sear_time_e = $sear_time_e." 23:59:59";
        }
        $where['b_order.sdate'] = array("between","{$sear_time_s},{$sear_time_e}");
        
        $count_sql = <<<SQL
                    select count(b_order.id) num from b_order  
                    left join s_driver on s_driver.id = b_order.d_id 
                    left join s_user on s_user.id = b_order.u_id 
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
               select b_order.id o_id,b_order.order_num,b_order.saddress,b_order.eaddress,b_order.warning,b_order.amount,b_order.sdate,b_order.wait_reward,
        (CASE b_order.state 
    WHEN 'new' THEN '未出发' 
    WHEN 'on' THEN '尚未接驾' 
    WHEN 'active' THEN '行程中' 
    WHEN 'wait_pay' THEN '待支付' 
    WHEN 'end' THEN '订单结束' 
    WHEN 'cannal' THEN '订单被取消' 
    END)  state_cn,(CASE b_order.source 
    WHEN 'Applets' THEN '小程序' 
    WHEN 'No_public' THEN '公众号' 
    WHEN 'Out_line' THEN '线下' 
    END) source,(CASE  
    WHEN  b_order_funding.method is null THEN ifnull(b_cancel_order_reason.text,b_order.cancel_text) 
    WHEN b_order_funding.method='nopublic' THEN '公众号支付' 
    WHEN b_order_funding.method='offline' THEN '线下支付' 
    END) paymethod,s_driver.phone d_phone,s_driver.name d_name,s_user.phone u_phone 
                    from b_order 
                left join s_driver on s_driver.id = b_order.d_id 
                left join s_user on s_user.id = b_order.u_id 
                left join b_order_funding on b_order.order_num = b_order_funding.order_num 
                left join b_cancel_order_reason on b_cancel_order_reason.id = b_order.cancel_id 
                
                    %WHERE% 
                    group by b_order.id 
                    order by b_order.id desc 
                    limit $first,$limit 
SQL;
        $data = M('')->where($where)->query($sql,true);
        //还需返回页数
//        $this->ajaxReturn($data);
        $msg['error_code'] = 0;
        $msg['message'] = "OK";
        $msg['list'] = $data;
        $msg['lastpage'] = $lastpage;
        $msg['count'] = $count;
//            dump($msg);
        $this->ajaxReturn($msg);
    }
    public function orderdetil() {
        $o_id = I("o_id");
        if(empty($o_id)){
            $msg['error_code'] = -403;
            $msg['message'] = "o_id为空";
            $this->ajaxReturn($msg);
            exit();
        }
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
        $where['b_order.id'] = $o_id;
        $sql=<<<SQL
        select b_order.id o_id,b_order.order_num,b_order.mileage_fee,b_order.duration_fee,b_order.amount,b_order.sdate,b_order.edate,b_order.duration,b_order.saddress,b_order.eaddress,b_order.distance,if(b_order.warning='Y','超区订单','正常订单') warning_cn,s_driver.name,s_user.nickname,s_driver.phone d_phone,s_user.phone u_phone,if(b_order.coupon_id=0,0,(select amount from s_coupon where id = b_order.coupon_id)) s_coupon_amount,b_order.wait_reward,
            b_order.start_fee,b_order.early_peak,b_order.late_peak,b_order.out_town,b_order.edge_town,(b_order.night_driving_first+b_order.night_driving_second) night_driving,b_order.bad_weather,b_order.other,b_cancel_order_reason.text user_cannel,b_order.cancel_text admin_cannel,b_order_site.distance pd_distance,b_order_site.wait_time,b_order_site.billing_start_time,b_order_site.billing_end_time,
            (CASE b_order.source 
    WHEN 'Applets' THEN '小程序' 
    WHEN 'No_public' THEN '公众号' 
    WHEN 'Out_line' THEN '线下' 
    END) source,
        (CASE b_order.state 
    WHEN 'new' THEN '未出发' 
    WHEN 'on' THEN '尚未接驾' 
    WHEN 'active' THEN '行程中' 
    WHEN 'wait_pay' THEN '待支付' 
    WHEN 'end' THEN '订单结束' 
    WHEN 'cannal' THEN '订单被取消' 
    END)  state_cn,
        CONCAT(b_order.order_num) search 
                    from b_order 
                    left join s_user on s_user.id = b_order.u_id 
                    left join s_driver on s_driver.id = b_order.d_id 
                    left join b_cancel_order_reason on b_cancel_order_reason.id = b_order.cancel_id 
                    left join b_order_site on b_order_site.o_id = b_order.id 
                    %WHERE% 
SQL;
        $data = M('')->where($where)->query($sql,true);
        $data[0]['duration_s'] = secToTime($data[0]['duration']);
        $msg = $data[0];
        $msg['error_code'] = 0;
        $msg['message'] = "OK";
        $this->ajaxReturn($msg);
//            dump($msg);
    }
    public function order_trajectory() {  //订单轨迹
        $o_id = I("o_id");
        $where['b_order_site.o_id'] = $o_id;
        $sql=<<<SQL
        select a.d_id,b_order_site.billing_start_time,b_order_site.billing_end_time 
                from b_order_site 
                left join (select id,d_id from b_order where id = {$o_id}) as a on a.id=b_order_site.o_id 
                    %WHERE% 
SQL;
        $data = M('')->where($where)->query($sql,true);
        $entity_name = "entity_name_".$data[0]['d_id'];
        $status = yingyan_gettrack($entity_name, $data[0]['billing_start_time'], $data[0]['billing_end_time']);
        if($status['status']==0){
            foreach ($status['points'] as $key => $value) {
                $point[$key]['Long'] = $value['longitude'];
                $point[$key]['Lat'] = $value['latitude'];
            }
        }
//        dump($point);
        $this->ajaxReturn($point);
    }
    public function cancelorder() {
        $o_id = I("o_id");
        if(empty($o_id)){
            $msg['error_code'] = -403;
            $msg['message'] = "o_id为空";
            $this->ajaxReturn($msg);
            exit();
        }
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
        $canceltext = I("canceltext");
        $where['b_order.id'] = $o_id;
        $where['b_order.state'] = array("in","new,on,active,wait_pay");
        $sql = <<<SQL
    select b_order.id o_id,b_order.u_id,b_order.d_id,s_driver.registered_id 
        from b_order 
        left join s_driver on s_driver.id = b_order.d_id 
        %WHERE%
SQL;
    $res = M()->where($where)->query($sql,TRUE);
    if(empty($res)){
        $msg['error_code'] = 1;
        $msg['message'] = "该订单不允许取消";
        $this->ajaxReturn($msg);
        exit();
    }
    destroy_cahe($res[0]['o_id']);//清理订单缓存
    S("seek_order_{$res[0]['d_id']}",NULL);  //清除司机叫单缓存
    M("b_order")->where(array("id"=>$res[0]['o_id']))->save(array("state"=>"cannal","d_confirmation"=>'Y','cancel_text'=>$canceltext));
    M("s_driver")->where(array("id"=>$res[0]['d_id']))->save(array("state"=>"off"));
    //极光推送取消订单
    $js_k['result'] = 3;
    $js_k['o_id'] = $res[0]['o_id'];
    $content = json_encode($js_k);
    aliyun_pushmessage($res[0]['registered_id'], $content);
    $msg['error_code'] = 0;
    $msg['message'] = "OK";
    
    $msg = [
        "type" => "admin_cancel_order",
        "driverId" => $res[0]['d_id'],
        "orderId" => $res[0]['o_id'],
        "cont" => [
            "time" => date('Y-m-d H:i:s',time()),
        ],
        "message" => "您好，订单{$res[0]['o_id']}已被运营管理员取消。",
        "message_code" => "0",
    ];
    redis_publish($msg['driverId'], $msg, 2, 5);
    
    $this->ajaxReturn($msg);
    }
    public function order_complaint() {  //订单结束后的投诉  //这似乎没啥用，投诉放进feedback里了。
        $count = M("s_complaint")->count("id");
        $page = I("page");
        if(empty($page)){
           $page = 1;
        }
        $limit = C("PAGE_LIMIT");
        $first = ($page-1)*$limit;
        $lastpage = ceil($count/$limit);
        $sql = <<<SQL
    select s_complaint.o_id,s_complaint_list.text reson,s_complaint.text,b_order.edate,b_order.order_num 
        from s_complaint 
        left join b_order on b_order.id = s_complaint.o_id 
        left join s_complaint_list on s_complaint_list.id = s_complaint.complaint_id 
        %WHERE%
        order by s_complaint.o_id desc 
        limit $first,$limit 
SQL;
        $res = M()->where($where)->query($sql,TRUE);
        $msg['error_code'] = 0;
        $msg['message'] = "OK";
        $msg['list'] = $res;
        $msg['lastpage'] = $lastpage;
        $msg['count'] = $count;
        $this->ajaxReturn($msg);
//        dump($msg);
    }
    public function add_address_view() {  //添加常用标签栏。
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
        $data['m_id'] = $adminid;
        $data['view'] = I("view");
        $data['lat'] = I("lat");
        $data['lng'] = I("lng");
        $data['os'] = $adminid."-".$data['lat'].",".$data['lng'];
        foreach ($data as $key => $value) {
            if(empty($value)){
                $msg['error_code'] = -402;
                $msg['message'] = $key;
                $this->ajaxReturn($msg);
                exit();
            }
        }
        $add = M("m_view")->add($data,array(),"view");
        if($add>0){
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $this->ajaxReturn($msg);
            exit();
        }  else {
            $msg['error_code'] = -1;
            $msg['message'] = "重复了";
            $this->ajaxReturn($msg);
            exit();
        }
    }
    public function address_view_list() {  //常用标签栏列表
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
        $view = M("m_view")->where(array("m_id"=>$adminid))->field("id viewid,view,lat,lng")->select();
        $msg['error_code'] = 0;
        $msg['message'] = "OK";
        $msg['list'] = $view;
        $this->ajaxReturn($msg);
        exit();
    }
    public function del_address_view() {  //删除常用标签栏
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
        $viewid = I("viewid");
        $del = M("m_view")->where(array("id"=>$viewid))->delete();
        if($del>0){
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
        }else{
            $msg['error_code'] = -1;
            $msg['message'] = "error";
        }
        $this->ajaxReturn($msg);
        exit();
    }
}