<?php
return array(
		'URL_ROUTER_ON'   => true, 
		'URL_ROUTE_RULES'=>array(
				//企业微信打款给司机微信账户
				'c2i' => 'WxWithDraw/cashToIndividual',
				'ask4' => 'WxWithDraw/addAskForCashToIndividual',
				'list' => 'WxWithDraw/getAskForList',
				"export_withdraw" => "WxWithDraw/exportExcel",
				"refuse" => "WxWithDraw/refuseWithDraw",
				"t" => "WxWithDraw/test",
				//RBAC处理
				"addrole" => "Rbac/addRole",
				"delrole" => "Rbac/delRole",
				"listrole" => "Rbac/listRole",
				"r2u" => "Rbac/role2User",
				"addAuthority" => "Rbac/addAuthority",
				"delAuthority" => "Rbac/delAuthority",
				"listAuthority" => "Rbac/listAuthority",
				"addrole2authority" => "Rbac/addrole2authority",
				"delrole2authority" => "Rbac/delrole2authority",
				"getRA" => "Rbac/getRoleAuthorityByUser",
				"getMenu" => "Rbac/getAuthoritiesMenu",
				"getAF" => "Rbac/getAuthoritiesFunctions",
				"addUser" => "Rbac/addUser",
				"listUser" => "Rbac/listUser",
				"delUser" => "Rbac/delUser",
				#"getRA2/id/:id" => ["Rbac/getRoleAuthorityByUserByGet",null,['method'=>'get']],
		
				
				//PowerRest管理				
				array('users','PowerRest/add',null,array('method'=>'post')),
				array('users/:id','PowerRest/update',null,array('method'=>'put')),
				array('users/:id','PowerRest/delete',null,array('method'=>'delete')),
				array('users/[:id]','PowerRest/read',null,array('ext' => 'json','method'=>'get')),
				
				array('powers','PowerRest/readPowers',null,array('ext' => 'json','method'=>'get')),
				array('roles','PowerRest/addRole',null,array('method'=>'post')),
				array('roles/:id','PowerRest/updateRole',null,array('method'=>'put')),
				array('roles/[:id]','PowerRest/readRole',null,array('ext' => 'json','method'=>'get')),
				array('roles/:id','PowerRest/deleteRole',null,array('method'=>'delete')),
				
				array('menus/:id','PowerRest/menu',null,array('ext' => 'json','method'=>'get')),
			
		),
        'PAGE_LIMIT'  => 10,
      'TMPL_ACTION_ERROR'   => 'Public:error',
      'TMPL_ACTION_SUCCESS' => 'Public:success',
	'PAGE_NUMBER'  => 6,
	'PAGE_CONFIG'  => array(
		    'header' => '共 %TOTAL_ROW% 条记录',
            'prev'   => '<<',
            'next'   => '>>',
            'last'   => '尾页',
            'first'  => '首页',    
            'theme'  => '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %HEADER% %END%',
            ),
	'PAGE_LASTSUFFIX' => false,
	'PAGE_ROLLPAGE' => 6,
      'UPLOAD_CONFIG' => array(
                    'maxSize'  => 5242880,
                'savePath' => '/Uploads/',
                'saveName' => array('uniqid',''),
                'autoSub'  => true,
                'subName'  => array('date','Ymd'),
            ),
    'TMPL_PARSE_STRING' => array(
    '__PUBLIC__' => __ROOT__.'/Public',
                    ),
);