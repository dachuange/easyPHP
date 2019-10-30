<?php
namespace Admin\Model;
use Think\Model;
class MemberChkModel{
	//操作
	public function check($data){
		// echo sizeof($data["yx"]);
		// echo sizeof($data["wx"]);
		// dump($data);die;
		//数据处理部分
		//为2删除  为1并且新增字段在between内新增
		if (sizeof($data["yx"])>0) {
			$i = 0;
			foreach ($data["yx"] as $key => $value) {
				// if ($value['addtime']>=$time_begin && $value['addtime']<=$time_end) {
					$data_add[$i]['username'] = $value['username'];
					$data_add[$i]['sex'] = $value['sex'];
					$data_add[$i]['astate'] = "yes";
					$data_add[$i]['level'] = $value['level_min'];
					$data_add[$i]['mobile'] = $value['mobile'];
					$data_add[$i]['position'] = $data_add[$i]['department'] = $value['position'];
					$data_add[$i]['sdate'] = date("Y-m-d H:i:s");
					$data_add[$i]['udate'] = date("Y-m-d H:i:s");
					$data_add[$i]['id_min'] = $value['id'];
					switch ($value['level_min']) {
						case 'shoppers':
							$data_add[$i]['departmentid'] = '1000';
							break;
						case 'dealers':
							$data_add[$i]['departmentid'] = '1001';
							break;
						case 'suppliers':
							$data_add[$i]['departmentid'] = '1002';
							break;
						case 'provinces':
							$data_add[$i]['departmentid'] = '1004';
							break;
					}
					$i++;
				// }
			}
			$num1 = M('sUser')->addAll($data_add,array(),'username,mobile,udate');
			// echo M('sUser')->_sql();
// 			$sql = <<<SQL
// 				delete from s_user where (level,id_min) in (
// 					select level,id_min from(
// 						select level,id_min from s_user where id_min is not null group by level,id_min having count(*)>1
// 					) as new_sql
// 				) and id not in (
// 					select id from (
// 						select max(id) id from s_user where id_min is not null group by level,id_min having count(*)>1
// 					) as new_sql2
// 				)
// SQL;
// 			$num = M('')->execute($sql);
		}
		if (sizeof($data["wx"])>0) {
			$ii = 0;
			foreach ($data["wx"] as $k => $v) {
				$data_del[$ii]['id_min'] = $v['id'];
				$data_del[$ii]['level'] = $v['level_min'];
				$ii++;
			}
			$data_del['_logic'] = "or";
			$num2 = M('sUser')->where($data_del)->delete();
		}
		echo $num1.'+'.$num2;
	}

}