<?php
return array(
		//'配置项'=>'配置值'
		'DEFAULT_MODULE'     => 'Home',
		'MODULE_ALLOW_LIST'  => array('Home',"Admin","Home_V2"),
		//    'DEFAULT_TIMEZONE'=>'Asia/Shanghai',
		
		'DB_TYPE'      => 'mysql',//数据库类型
		//	'DB_HOST'      => 'rm-8vb69816825js9l47rw.mysql.zhangbei.rds.aliyuncs.com',//服务器地址
		'DB_HOST'      => '127.0.0.1',//服务器地址
		'DB_NAME'      => 'taxi',//数据库名
		//	'DB_USER'      => 'utnsd1',//用户名
		'DB_USER'      => 'root',//用户名
		//	'DB_PWD'       => 'Hfds95647f@dJK',//密码  Yesndm965@deHPAN
		'DB_PWD'       => 'Yesndm965@deHPAN',//密码
		#'DB_PWD'       => 'root',//密码
		'DB_PORT'      => 3306,//端口
		'DB_PREFIX'    => '',//数据库表前缀
		'DB_CHARSET'   => 'utf8',//字符集
		'DB_PARAMS'    =>    array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL),   //查询出的结果集，字段强制转换为小写
		
		"domin" => "https://www.testtaxisan.com",
		"DY1_appid" => "wx9fe615e38d423ac7",      //第一个订阅号
		"DY1_secret" => "0d49d80d54e2b1ac247083e91b725f6c",
		"FW_appid" => "wx73e0cea3e01d21f6",       //支付主服务号
		"FW_secret" => "c5608a60f21d4a016fea7fb1d9ecd1f6",
		"SJ_appid" => "wxd03384b4ccd5bf33",       //司机端支付ID
		"SJ_secret" => "40faf16bc7ce552180cf241a18377050",
		"XCX_appid" => "wxdfe623346f3e66ba",      //司机ID
		"XCX_secret" => "36e8647f495a8448129213b85527a23e",
		"CS_appid" =>"wx9229b79c216d5f50",
		"CS_secret" =>"f55c5e917f531aa5b3ddac5eb512d294",
		
		"hb_tempid" => "MtOlQSGt3oSImN9NW-w71pBYNXApKC3oxY9WsyHAG2k",
);