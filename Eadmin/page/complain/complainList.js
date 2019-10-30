var url=window.globalConfig.api;//接口地址
var limit=window.globalConfig.limit;//每页显示的条数
var pageSize = 20; //每页显示数据条数
var page = 1; //当前页数
var dataLength = 0; //数据总条数
var id = localStorage.getItem("userid");
var token= localStorage.getItem("token");
var address_id= localStorage.getItem("address_id");
layui.use(['form', 'layer', 'table', 'laytpl'], function() {
	var token= localStorage.getItem("token");
	var form = layui.form,
		layer = parent.layer === undefined ? layui.layer : top.layer,
		$ = layui.jquery,
		laytpl = layui.laytpl,
		table = layui.table;
		var index = layer.load(2); //添加laoding,0-2两种方式
	//投诉列表
	var tableIns = table.render({
		elem: '#driverList',
		url: url+'/admin/Feedback/complaint_list',
		cellMinWidth: 95,
		method: 'POST', //laui 修改请求方式
		request: {
			// 			search: 'search', //搜索
						page: 'page' //页码的参数名称，默认：page,每页数据量的参数名，默认：limit
		},
		response: {
			statusName: 'error_code', //数据状态的字段名称，默认：code
			countName: 'count', //数据总数的字段名称，默认：count
			dataName: 'list', //默数据列表的字段名称，认：data        //我返回的datas集合 
		},
		where: {
			id: id,
			token: token,
			address_id:address_id
		},
		page: true,
		// height: "450",
		//limits: [10, 15, 20, 25],
		limit: limit,
		id: "driverListTable",
		done: function(res) {
			layer.close(index);    //返回数据关闭loading
			var status = res.error_code;
			console.log(status)
			if (status ==-101) {
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
					field: 'o_id',
					title: '订单ID',
					minWidth: 150,
					align: "center",
					hide: true
				},
				{
					field: 'date',
					title: '投诉时间',
					minWidth: 200,
					align: "center"
				},
				{
					field: 'reson',
					title: '投诉类型',
					minWidth: 250,
					align: "center",
					hide: true
				},
				{
					field: 'text',
					title: '具体投诉原因',
					minWidth: 300,
					align: "center",
					hide: true
				},
				{
					field: 'd_name',
					title: '被投诉者',
					minWidth: 200,
					align: "center",
					hide: true,
				},
				{
					field: 'd_phone',
					title: '被投诉者电话',
					minWidth: 150,
					align: "center",
					hide: true
				},
				{
					field: 'nickname',
					title: '投诉者昵称',
					minWidth: 200,
					align: "center",
					hide: true,
				},
				{
					field: 'u_phone',
					title: '投诉者电话',
					minWidth: 150,
					align: "center",
					hide: true
				}
			]
		]
	});
})
