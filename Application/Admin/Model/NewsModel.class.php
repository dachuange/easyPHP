<?php
namespace Admin\Model;
use Think\Model;
class NewsModel extends Model {
	protected $_validate = array(
		array('title','require','请填写标题',0,'',1),
	);
	protected $_auto = array (          
        array('create_time','gettime',3,'callback'),
        array('deadline','gettime2',3,'callback'),  
	);

	function gettime(){
		$create_time=I('post.create_time');
		if(empty($create_time))
		{
			unset($_POST['create_time']);
			return false;
		}
		return strtotime($create_time);	
	}
	function gettime2(){
		$deadline=I('post.deadline');
		if(empty($deadline))
		{
			unset($_POST['deadline']);
			return false;
		}
		if ($deadline == '0') {
			return '0';
		}else{
			return strtotime($deadline);
		}
			
	}
		
}