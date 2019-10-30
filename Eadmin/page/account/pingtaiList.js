var url=window.globalConfig.api;//接口地址
var limit=window.globalConfig.limit;//每页显示的条数
var pageSize = 20; //每页显示数据条数
var page = 1; //当前页数
var dataLength = 0; //数据总条数
var id = localStorage.getItem("userid");
var token = localStorage.getItem("token");
var address_id= localStorage.getItem("address_id");
layui.use(['form', 'layer', 'table', 'laytpl'], function() {
	var token = localStorage.getItem("token");
	var form = layui.form,
		layer = parent.layer === undefined ? layui.layer : top.layer,
		$ = layui.jquery,
		laytpl = layui.laytpl,
		table = layui.table;
		var index = layer.load(2); //添加laoding,0-2两种方式
	//平台流水列表
	var tableIns = table.render({
		elem: '#driverList',
		url: url+'/admin/Money/platform_flow',
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
		/* height: "450", */
		//limits: [10, 15, 20, 25],
		limit: limit,
		id: "driverListTable",
		done: function(res) {
			layer.close(index);    //返回数据关闭loading
			var status = res.error_code;
			console.log(status)
			if (status == -101) {
				top.location.href= '../login/login.html';
			}
			var message=res.message;
			alert(message);
		},
		cols: [
			[{
					field: 'full_num',
					title: '订单编号',
					minWidth: 150,
					align: "center"
				},
				{
					field: 'date',
					title: '日期',
					minWidth: 200,
					align: "center",
					hide: true
				},
				{
					field: 'amount',
					title: '金额',
					minWidth: 150,
					align: "center",
					hide: true
				},
				{
					field: 'type',
					title: '支付方式',
					minWidth: 200,
					align: "center",
					hide: true,
				}
			]
		]
	});
})
