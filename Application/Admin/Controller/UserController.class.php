<?php
namespace Admin\Controller;
use Think\Controller;
class UserController extends HeadsController {
	public function index(){  //用户列表
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
            $sear_time_s = I("sear_time_s");
            $sear_time_e = I("sear_time_e");
            if(empty($sear_time_s)){
                $sear_time_s = date("Y-m-d H:i:s",0);
            }
            if(empty($sear_time_e)){
                $sear_time_e = date("Y-m-d H:i:s",NOW_TIME);
            }
            $where["s_user.sdate"] = array("between","{$sear_time_s},{$sear_time_e}");
            $search = I("search");
            if(!empty($search)){
                $where["_string"] = "CONCAT(IFNULL(s_user.nickname,'|'),'|',IFNULL(s_user.phone,'|')) like '%{$search}%'";
            }
            $state = I("state");
            if(!empty($state)){
                $where["state"] = $state;
            }
            $attend = I("attend");
            if($attend=="Y"){
                $where["phone"] = array('exp','is not null');
            }elseif ($attend=="N") {
                $where["phone"] = array('exp','is null');
            }
            $is_order = I("is_order");
            if($is_order=="Y"){  //打过车
                $where["_string"] = "(select count(b_order.id) from b_order where b_order.u_id=s_user.id)>0";
            }elseif ($is_order=="N") {  //没打过车
                $where["_string"] = "(select count(b_order.id) from b_order where b_order.u_id=s_user.id)=0";
            }
            $address_id = M("m_admin")->where(array("id"=>$adminid))->getField("address_id");
            if(!empty($address_id)){
                $where['s_user.address_id'] = $address_id;
            }
            $count_sql = <<<SQL
			select count(s_user.id) num from s_user  
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
			select s_user.id u_id,s_user.phone,s_user.nickname,s_user.headimgurl,s_user.sdate,s_user.state,(select count(s_coupon.id) from s_coupon where s_coupon.u_id = s_user.id and s_coupon.user='N') coup_nums_n,(select count(s_coupon.id) from s_coupon where s_coupon.u_id = s_user.id and s_coupon.user='Y') coup_nums_y,CONCAT(s_user.phone,s_user.nickname),(CASE s_user.state 
        WHEN 'Y' THEN '行程中' 
        WHEN 'N' THEN '未用车' 
        WHEN 'O' THEN '等待派单中' 
        END)  state_cn 
                        from s_user 
                        %WHERE% 
                        order by s_user.id desc 
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
        public function coup_user(){   //红包发放的用户列表
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
       
            $search = I("search");
            if(!empty($search)){
                $where["_string"] = "CONCAT(IFNULL(s_user.nickname,'|'),'|',IFNULL(s_user.phone,'|')) like '%{$search}%'";
}
            $state = I("state");
            if(!empty($state)){
                $where["state"] = $state;
            }
            $attend = I("attend");
            if($attend=="Y"){
                $where["phone"] = array('exp','is not null');
            }elseif ($attend=="N") {
                $where["phone"] = array('exp','is null');
            }
            //红包筛选//
            $a = I("aa");$b = I("bb");$c = I("cc");$d = I("dd");$e = I("ee");
            $f = I("ff");$g = I("gg");
            
            if($a=="Y"){  //N天内，订单数大于X
                $daya = I("day_a");
                $osa = I("os_a");
                $where["datediff(now(),b_order.sdate)"] = array('elt',$daya);
                if(empty($haveing)){
                    $haveing = "having count(b_order.id)>=$osa";
                }else{
                    $haveing = $haveing."count(b_order.id)>=$osa";
                }
            }
            if($b=="Y"){  //N天内，订单数小于X
                $dayb = I("day_b");
                $osb = I("os_b");
                if(empty($osb)){
                    $osb = 0;
                }
                $where["datediff(now(),b_order.sdate)"] = array('elt',$dayb);
                if(empty($haveing)){
                    $haveing = "having count(b_order.id)<=$osb";
                }else{
                    $haveing = $haveing."count(b_order.id)<=$osb";
                }
            }
            if($c=="Y"){  //已注册
                $where["s_user.phone"] = array('exp','is not null');
            }
            if($d=="Y"){  //无订单
                if(empty($haveing)){
                    $haveing = "having count(b_order.id)=0";
                }else{
                    $haveing = $haveing."count(b_order.id)=0";
                }
            }
            if($e=="Y"){  //N天内注册
                $daye = I("day_e");
                if(empty($daye)){
                    $daye = 0;
                }
                $where["datediff(now(),s_user.sdate)"] = array('elt',$daye);
                $where["s_user.phone"] = array('exp','is not null');
            }
            if($f=="Y"){  //M个差评
                $where["s_user.id"] = array('eq',0);
            }
            if($g=="Y"){  //>F个取消单
                $fuse = I("fuse_g");
                $where["b_order.state"] = array('eq','cannal');
                if(empty($haveing)){
                    $haveing = "having count(b_order.id)>=$fuse";
                }else{
                    $haveing = $haveing."count(b_order.id)>=$fuse";
                }
            }
            $address_id = M("m_admin")->where(array("id"=>$adminid))->getField("address_id");
            if(!empty($address_id)){
                $where['s_user.address_id'] = $address_id;
            }
            
            $sql0=<<<SQL
			select s_user.id u_id 
                        from s_user 
                        left join b_order on b_order.u_id = s_user.id 
                        %WHERE% 
                        group by s_user.id 
                        $haveing
                        order by s_user.id desc 
SQL;
            $datas = M('')->where($where)->query($sql0,true);
            $count = count($datas);
            $page = I("page");
            if(empty($page)){
               $page = 1;
            }
            $limit = C("PAGE_LIMIT");
            $first = ($page-1)*$limit;
            $lastpage = ceil($count/$limit);
            
//            $data = M('')->_sql();
            foreach ($datas as $key => $value) {
                $idarr[] = $value['u_id'];
            }
            $sql=<<<SQL
			select s_user.id u_id,s_user.phone,s_user.nickname,(select count(s_coupon.id) from s_coupon where s_coupon.u_id = s_user.id and s_coupon.user='N') coup_nums_n,(select count(s_coupon.id) from s_coupon where s_coupon.u_id = s_user.id and s_coupon.user='Y') coup_nums_y,s_user.sdate 
                        from s_user 
                        left join b_order on b_order.u_id = s_user.id 
                        %WHERE% 
                        group by s_user.id 
                        $haveing
                        order by s_user.id desc 
                        limit $first,$limit 
SQL;
            $data = M('')->where($where)->query($sql,true);
            //还需返回页数
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $msg['list'] = $data;
            $msg['liststr'] = join(",", $idarr);
            $msg['count'] = $count;
            $msg['lastpage'] = $lastpage;
//            dump($msg);
            $this->ajaxReturn($msg);
	}
	
	public function coup_usernew(){   //红包发放的用户列表
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
	    $search = I("search");
	    if(!empty($search)){
	        $where["_string"] = "CONCAT(IFNULL(s_user.nickname,'|'),'|',IFNULL(s_user.phone,'|')) like '%{$search}%'";
	        //dump($where["_string"]);exit;
	    }
	    //红包筛选//
	    $a = I("aa");$b = I("bb");$c = I("cc");$d = I("dd");
	    
	    $where["s_user.phone"] = array('exp','is not null');
	    if($a =="Y"){//是否下过单
	        if(empty($haveing)){
	            $haveing = "having count(b_order.id)=0";
	        }else{
	            $haveing = $haveing."count(b_order.id)=0";
	        }
	        
	    }
	    if($b=="Y"){  //N天内注册
	        $daye = I("day_e");//day_e N
	        if(empty($daye)){
	            $daye = 0;
	        }
	        $where["datediff(now(),s_user.sdate)"] = array('elt',$daye);
	    }
	    //若选择N天内无订单用户，
	    if(empty($a) && $c=="Y"){  //N天内无订单
	        $dayb = I("day_b");
	        $where[] = "s_user.id not in (SELECT DISTINCT(u_id) from b_order WHERE datediff(now(),b_order.sdate) <= '$dayb')";
	        //                $where["datediff(now(),b_order.sdate)"] = array('gt',$dayb);
	        //                if(empty($haveing)){
	        //                    $haveing = "having count(b_order.id)=0";
	        //                }else{
	        //                    $haveing = $haveing."count(b_order.id)=0";
	        //                }
	    }
	    if($d=="Y"){//取两个日期间的值
	        $begin = I("begin");
	        $end = I("end");
	        $where['s_user.sdate'] = ['between',[$begin,$end]];
	    }
	    
	    $address_id = M("m_admin")->where(array("id"=>$adminid))->getField("address_id");
	    if(!empty($address_id)){
	        $where['s_user.address_id'] = $address_id;
	    }
	    
	    $sql0=<<<SQL
			select s_user.id u_id
                        from s_user
                        left join b_order on b_order.u_id = s_user.id
                        %WHERE%
                        group by s_user.id
                        $haveing
                        order by s_user.id desc
SQL;
                        
                        $datas = M('')->where($where)->query($sql0,true);
                        //dump(M('')->getLastSql());exit;
                        $count = count($datas);
                        //dump($datas);exit;
                        $page = I("page");
                        if(empty($page)){
                            $page = 1;
                        }
                        $limit = C("PAGE_LIMIT");
                        $first = ($page-1)*$limit;
                        $lastpage = ceil($count/$limit);
                        
                        //            $data = M('')->_sql();
                        foreach ($datas as $key => $value) {
                            $idarr[] = $value['u_id'];
                        }
                        $sql=<<<SQL
			select s_user.id u_id,s_user.phone,s_user.nickname,(select count(s_coupon.id) from s_coupon where s_coupon.u_id = s_user.id and s_coupon.user='N') coup_nums_n,(select count(s_coupon.id) from s_coupon where s_coupon.u_id = s_user.id and s_coupon.user='Y') coup_nums_y,s_user.sdate
                        from s_user
                        left join b_order on b_order.u_id = s_user.id
                        %WHERE%
                        group by s_user.id
                        $haveing
                        order by s_user.id desc
                        limit $first,$limit
SQL;
                        $data = M('')->where($where)->query($sql,true);
                        //还需返回页数
                        $msg['error_code'] = 0;
                        $msg['message'] = "OK";
                        $msg['list'] = $data;
                        $msg['liststr'] = join(",", $idarr);
                        $msg['count'] = $count;
                        $msg['lastpage'] = $lastpage;
                        //          dump($msg);exit;
                        $this->ajaxReturn($msg);
	}
	
}