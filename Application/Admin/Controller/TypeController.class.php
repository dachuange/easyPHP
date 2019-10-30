<?php
namespace Admin\Controller;
use Think\Controller;
class TypeController extends HeadsController {
	public function index(){
            
            $this->assign('pid',0);
            $this->display();
	}
        public function tree_json(){
		$arr=array();
		$count = -1;
		$parentid = I('id')?I('id'):I('pid');
		$sql_data = <<<SQL
			select org.id id,org.name name,org.fatherid pd,org.state from c_type org where org.fatherid = {$parentid} 

SQL;
		$data = M('')->query($sql_data,true);
//		 dump($data);die;
		// $data = M('organizational')->where('parentid='.$parentid)->select();
		foreach ($data as $key => $value) {
			// $count = M('organizational')->where('parentid='.$value['id'])->count();
			 if ($value['pd']=="0") {
				$isParent = 'true';
			 }else{
			 	$isParent = 'false';
			 }
			$arr_str = array("name" =>$value['name'],'id'=>$value['id'],'count'=>$count,'times'=>'1','isParent'=>$isParent,"state"=>$value['state'],"open"=>true);
        	array_push($arr, $arr_str);
		}
        $arr2 = json_encode($arr);
        echo $arr2;
	}
        public function add_class(){
            $classify = M("c_type")->where(array("fatherid"=>0))->select();
            $this->assign('classify',$classify);
            $this->display();
	}
        public function close() {
            $id=I("id");
            $fatherid = M("c_type")->where(array("id"=>$id))->getField("fatherid");
            if($fatherid=="0"){  //本身是父级ID  连子级一起禁用
                M("c_type")->where(array("id"=>$id))->save(array("state"=>"N"));   
                M("c_type")->where(array("fatherid"=>$id))->save(array("state"=>"N"));
            } else {  //本身就是子集ID   
                M("c_type")->where(array("id"=>$id))->save(array("state"=>"N"));
            }
            $this->ajaxReturn("ok");
           
        }
        public function open() {
            $id=I("id");
            $fatherid = M("c_type")->where(array("id"=>$id))->getField("fatherid");
            if($fatherid=="0"){  //本身是父级ID  只启用自己
                M("c_type")->where(array("id"=>$id))->save(array("state"=>"Y"));   

            } else {  //本身是子集ID  父级ID也要启用   
                M("c_type")->where(array("id"=>$id))->save(array("state"=>"Y"));
                M("c_type")->where(array("id"=>$fatherid))->save(array("state"=>"Y"));
            }
            $this->ajaxReturn("ok");
        }
	
}