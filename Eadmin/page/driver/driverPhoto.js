var url=window.globalConfig.api;//接口地址
var id = localStorage.getItem("userid");
var token = localStorage.getItem("token");
var address_id= localStorage.getItem("address_id");
layui.use(['form', 'layer', 'table', 'laytpl'], function() {
	var form = layui.form,
		layer = parent.layer === undefined ? layui.layer : top.layer,
		$ = layui.jquery,
		laytpl = layui.laytpl,
		table = layui.table;
		var index = layer.load(2); //添加laoding,0-2两种方式
	//司机列表
	var tableIns = table.render({
		elem: '#driverList',
		url: url+'/admin/Driver/get_avater_list',
		cellMinWidth: 95,
		method: 'POST', //laui 修改请求方式
		height: "400",
		request: {
			page: 'page' //页码的参数名称，默认：page,每页数据量的参数名，默认：limit
		},
		where: {
			id: id,
			token:token,
			address_id:address_id
		},
		response: {
			statusName: 'error_code', //数据状态的字段名称，默认：code
			countName: 'count', //数据总数的字段名称，默认：count
			dataName: 'list', //默数据列表的字段名称，认：data        //我返回的datas集合 
		},
		page: false,
		id: "driverListTable",
		done: function(res) {
			layer.close(index);    //返回数据关闭loading
			$('table.layui-table thead tr th:eq(0)').addClass('layui-hide');
			var status = res.error_code;
			console.log(status)
			if (status == -101) {
				top.location.href= '../login/login.html';
			}
		},
		cols: [
			[
				// 				{
				// 					type: "checkbox",
				// 					fixed: "left",
				// 					width: 50
				// 				},
				{
					field: 'd_id',
					title: '司机ID',
					minWidth: 100,
					align: "center",
					style:'display:none;'
				},
				{
					field: 'card',
					title: '司机工号',
					minWidth: 100,
					align: "center"
				},
				{
					field: 'name',
					title: '司机姓名',
					minWidth: 100,
					align: "center",
					hide: true
				},
				{
					field: 'avater',
					title: '司机头像',
					minWidth: 100,
					align: "center",
					templet: function(d) {
						return '<img src=' + d.avater + ' height="100px;width:100px;" />';
					}
				},
				{
					title: '操作',
					minWidth: 175,
					templet: '#driverListBar',
					fixed: "right",
					align: "center"
				}
			]
		]
	});

	//列表操作
	table.on('tool(driverList)', function(obj) {
		var layEvent = obj.event,
			data = obj.data;
		var d_id = data.d_id;
		//审核成功
		if (layEvent === 'usable') {
			var reviewed = 'Y';
			console.log('reviewed', reviewed)

			/* 司机审核 */
			$.ajax({
				url: url+'/admin/Driver/driver_avater_reviewed',
				type: "POST",
				data: {
					id: id,
					token: token,
					d_id: d_id,
					address_id:address_id,
					reviewed: reviewed
				},
				dataType: "json",
				success: function(data) {
					console.log('司机头像', data)
					var code = data.error_code;
					if (code == 0) {
						alert("审核成功")
						location.reload()
					} else if (code == -402) {
						top.location.href= '../login/login.html';
					} else if (code == -101) {
						top.location.href= '../login/login.html';
					} else if (code == -405) {
						alert("审核失败")
						location.reload()
					}
				},
			})
		}
		//审核失败
		if (layEvent === 'usable1') {
			var reviewed = 'N';
			console.log('reviewed', reviewed)

			/* 司机审核 */
			$.ajax({
				url: url+'/admin/Driver/driver_avater_reviewed',
				type: "POST",
				data: {
					id: id,
					token: token,
					address_id:address_id,
					d_id: d_id,
					reviewed: reviewed
				},
				dataType: "json",
				success: function(data) {
					console.log('司机头像', data)
					var code = data.error_code;
					if (code == 0) {
						alert("审核成功")
						location.reload()
					} else if (code == -402) {
						window.open = '../login/login.html';
					} else if (code == -405) {
						alert("审核失败")
						location.reload()
					}
				},
			})
		}
	});

})
