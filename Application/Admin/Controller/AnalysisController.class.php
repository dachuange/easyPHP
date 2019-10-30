<?php
namespace Admin\Controller;
use Think\Controller;
class AnalysisController extends HeadsController {
        public function index() {
            $sql=<<<SQL
			select datediff('2019-07-18',s_driver.sdate) 
                        from s_driver 
                        where s_driver.id='8'
SQL;
            $data = M('')->query($sql);
            dump($data);
        }
	public function driver_onlinetime(){ //司机在岗统计列表
            
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
            
            $starttime = I("start_time");
            if(empty($starttime)){
                $starttime = 0;
            }else{
                $starttime = strtotime($starttime);
            }
            $endtime = I("end_time");
            if(empty($endtime)){
                $endtime = NOW_TIME;
            }else{
                $endtime = $endtime." 23:59:59";
                
                $endtime = strtotime($endtime);
            }
            $sql=<<<SQL
			select s_driver.id d_id,s_driver.card,s_driver.name,s_driver.phone,
                    
                    SEC_TO_TIME(
                        ifnull((select ($endtime-$starttime) from d_online_record where d_online_record.d_id=s_driver.id and d_online_record.sdate < {$starttime}  and d_online_record.edate > {$endtime}),0)
                            +
                        ifnull((select SUM(d_online_record.line_time) from d_online_record where d_online_record.d_id=s_driver.id and d_online_record.sdate between {$starttime} and {$endtime} and d_online_record.edate between {$starttime} and {$endtime}),0)
                            +
                        ifnull((select ({$endtime}-d_online_record.sdate) from d_online_record where d_online_record.d_id=s_driver.id and d_online_record.sdate between {$starttime} and {$endtime} and d_online_record.edate > {$endtime}),0)
                            +
                        ifnull((select (d_online_record.edate-{$starttime}) from d_online_record where d_online_record.d_id=s_driver.id and d_online_record.edate between {$starttime} and {$endtime} and d_online_record.sdate < {$starttime}),0)
                                ) line_time 
                        from s_driver 
                        %WHERE% 
                        order by line_time desc 
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
        public function driver_onlinetime_detil() { //司机在岗统计详情
            $d_id = I("d_id");
//            $this->ajaxReturn($d_id);
            $adminid = I("id");  //管理员的ID
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
            $where['s_driver.id'] = $d_id;
            $day = I("day");
            if(empty($day)){
                $day = date("Y-m-d",NOW_TIME);
            }
//            dump($day);
            $counts = 0;
            $limit[0] = '0-6';
            $limit[1] = '6-8';
            $limit[2] = '8-11';
            $limit[3] = '11-13';
            $limit[4] = '13-17';
            $limit[5] = '17-19';
            $limit[6] = '19-24';
            for ($index = 0; $index < sizeof($limit); $index++) {
                $arr = explode("-", $limit[$index]);
                
                if($arr[0]<10){
                    $time = "0".$arr[0].":00:00";
                }else{
                    $time = $arr[0].":00:00";
                }
                $times = $day." ".$time;
//                dump($times);
                $starttime = strtotime($times);
                if($arr[1]<10){
                    $time2 = "0".$arr[1].":00:00";
                }else{
                    if($arr[1]==24){
                        $time2 = "23:59:59";
                    }else{
                        $time2 = $arr[1].":00:00";
                    }
                }
                $timee = $day." ".$time2;
//                dump($timee);
                $endtime = strtotime($timee);
                $sql=<<<SQL
                    select s_driver.id d_id,s_driver.card,s_driver.name,s_driver.phone,
                    (   ifnull((select ($endtime-$starttime) from d_online_record where d_online_record.d_id=s_driver.id and d_online_record.sdate < {$starttime}  and d_online_record.edate > {$endtime}),0)
                            +
                        ifnull((select SUM(d_online_record.line_time) from d_online_record where d_online_record.d_id=s_driver.id and d_online_record.sdate between {$starttime} and {$endtime} and d_online_record.edate between {$starttime} and {$endtime}),0)
                            +
                        ifnull((select (d_online_record.edate-{$starttime}) from d_online_record where d_online_record.d_id=s_driver.id and d_online_record.edate between {$starttime} and {$endtime} and d_online_record.sdate < {$starttime} limit 0,1),0)
                          +
                        ifnull((select ({$endtime}-d_online_record.sdate) from d_online_record where d_online_record.d_id=s_driver.id and d_online_record.sdate between {$starttime} and {$endtime} and d_online_record.edate > {$endtime} limit 0,1),0)
                                ) line_time from s_driver 
                        %WHERE% 
SQL;
            
                $data = M('')->where($where)->query($sql,true);
//                dump($data);
                $counts = $counts+$data[0]['line_time'];
                $tongji[$index]=  round(($data[0]['line_time']/60));
            }
            $counts = secToTime($counts);
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $msg['time'] = $tongji;
            $msg['name'] = $data[0]['name'];
            $msg['phone'] = $data[0]['phone'];
            $msg['count'] = $counts;
            $this->ajaxReturn($msg);
        }
        public function areafeedisplay() {  //宝坻车费展示
            $address_id = I("address_id");
            if($address_id==0||$address_id==1){
            $area = getarea();
            $this->ajaxReturn($area);
            }else{
                $msg['error_code'] = -1;
                $msg['message'] = "您无访问宝坻设置的权限";
                $this->ajaxReturn($msg);
                exit();
        }
            
        }
        public function user_wait_reward_tj() { //用户等待奖励金统计
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
                $where = " AND s_driver.address_id={$address_id} ";
            }else{
                $where="";
            }
            
            $sear_time_s = I("sear_time_s");
            $sear_time_e = I("sear_time_e");
            if(empty($sear_time_s)){
                $sear_time_s = date("Y-m-d H:i:s",0);
            }
            if(empty($sear_time_e)){
                $sear_time_e = date("Y-m-d H:i:s",NOW_TIME);
            }
            $count_sql = <<<SQL
            SELECT SUM(b_order.wait_reward) num,b_order_funding.method 
        FROM  b_order
        LEFT JOIN b_order_funding ON b_order_funding.order_num = b_order.order_num 
        LEFT JOIN s_driver ON s_driver.id = b_order.d_id 
        WHERE b_order.wait_reward <>0 AND b_order_funding.method is not NULL 
        AND b_order.sdate between "$sear_time_s" and "$sear_time_e" {$where}
        GROUP BY b_order_funding.method
SQL;
            $count = M('')->where($where)->query($count_sql,true);
            $data['count'] = 0;
            foreach ($count as $key => $value) {
                $data['count'] = $data['count']+$value['num'];
                if($value['method']=='offline'){
                    $data['offline'] = $value['num'];
                }elseif($value['method']=='nopublic'){
                    $data['nopublic'] = $value['num'];
                }
            }
            if(empty($data['nopublic'])){
                $data['nopublic'] = 0;
            }
            if(empty($data['offline'])){
                $data['offline'] = 0;
            }
//            dump($value);
            $this->ajaxReturn($data);
        }
        public function user_growth() {
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
            getUserCountToday(0);
        }
        
        
}