<?php
namespace Admin\Controller;
use Think\Controller;

class PhoneController extends HeadsController {

    public function getudsekinfo(){
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
        $data['error_code'] = 0;
        $data['message'] = "OK";
        $m_admin = M("m_admin")->where(array("id"=>$adminid))->find();
        $data['token'] = get_udesk_agent_token($m_admin['udesk_adminmail'],$m_admin['udesk_adminpwd'],$m_admin['udesk_openapi_url'],$m_admin['udesk_agent_url'],$m_admin['udesk_cusmail']);
        $data['subDomain'] = $m_admin['subdomain'];
        $this->ajaxReturn($data);
    }
    
}
