<?php
namespace Admin\Model;
use Think\Model;
class MailModel extends Model {
	protected $_validate = array(
		array('title','require','请填写标题',0,'',1),
		array('user_id','require','请填收件人',0,'',1),
	);
	protected $_auto = array (       
	     array('add_time','time',1,'function'), 
	     array('reply_time','time',2,'function'), 
	);

}