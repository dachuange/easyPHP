<?php
namespace Admin\Model;
use Think\Model;
class AdminGroupModel extends Model {
	protected $_validate = array(
		array('title','require','请填写分销商组',0,'',1),
		array('title','','分销商组已经存在！',0,'unique',1), 
	);
	protected $_auto = array (          
        array('rules','getrules',3,'callback'),
        array('r_p','gettitle',3,'callback'),
	);
	function getrules(){
		$data = implode(',',I('post.rules'));
		if ($data) {
			return $data;
		}else{
			return false;
		}
	}
	function gettitle(){
		$data = implode(',',I('post.title'));
		if ($data) {
			return $data;
		}else{
			return false;
		}
	}
}