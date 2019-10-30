<?php
namespace Admin\Model;
use Think\Model;
class MemberActiveModel extends Model {
	public function users($id){


		M('UcenterMember')->where('id='.$id)->save();
	}
}