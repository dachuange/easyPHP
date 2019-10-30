<?php
namespace Admin\Model;
use Think\Model;
class AdminModel extends Model {
	protected $_validate = array(
		array('username','require','请填写分销商名'),
		array('username','','分销商名已经存在！',0,'unique',1),
		array('password','require','请输入密码',0,'',1),
		array('password','6,20','密码不能小于6位',0,'length',1), 
		array('repassword','password','确认密码不正确',0,'confirm'), 
		array('email','require','请填写邮箱'),	
	);
	protected $_auto = array (   
	    array('password','getpass',3,'callback'),       
	);
	function getpass(){
		$login_pass=I('post.password');
		if(empty($login_pass))
		{
			unset($_POST['password']);
			return false;
		}
		return md5($login_pass);	
	}

}