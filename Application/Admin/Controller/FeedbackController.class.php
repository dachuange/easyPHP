<?php
namespace Admin\Controller;
use Think\Controller;
class FeedbackController extends HeadsController {
	public function index(){
            
	}
        public function complaint_list() {  //获取投诉信息
            
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
            $count_sql = <<<SQL
                    select count(s_complaint.id) num from s_complaint 
                    left join s_driver on s_complaint.d_id = s_driver.id 
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
                        select s_complaint.o_id,s_complaint_list.text reson,s_complaint.text,s_complaint.date,s_complaint.d_id,s_complaint.u_id,s_driver.name d_name,s_driver.phone d_phone,s_user.nickname,s_user.phone u_phone 
                        from s_complaint 
                    left join s_complaint_list on s_complaint_list.id = s_complaint.complaint_id 
                    left join s_driver on s_complaint.d_id = s_driver.id 
                    left join s_user on s_complaint.u_id = s_user.id 
                        %WHERE% 
                        order by s_complaint.id desc 
                        limit $first,$limit 
SQL;
            $data = M('')->where($where)->query($sql,true);
            //还需返回页数
            $msg['error_code'] = 0;
            $msg['message'] = "OK";
            $msg['list'] = $data;
            $msg['lastpage'] = $lastpage;
            $msg['count'] = $count;
//                dump($msg);
            $this->ajaxReturn($msg);
        }
       
}