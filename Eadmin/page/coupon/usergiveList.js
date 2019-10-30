var url = window.globalConfig.api; //接口地址
var limit = window.globalConfig.limit; //每页显示的条数
var pageSize = 20; //每页显示数据条数
var page = 1; //当前页数
var dataLength = 0; //数据总条数
var id = localStorage.getItem("userid");
var token = localStorage.getItem("token");
var address_id = localStorage.getItem("address_id");
var search = $('#search').val();
layui.use(['form', 'layer', 'table', 'laytpl'], function() {
	var token = localStorage.getItem("token");
	var form = layui.form,
		layer = parent.layer === undefined ? layui.layer : top.layer,
		$ = layui.jquery,
		laytpl = layui.laytpl,
		table = layui.table;
	var index = layer.load(2); //添加laoding,0-2两种方式
	//司机列表
	var tableIns = table.render({
		elem: '#driverList',
		url: url + '/admin/Coup/giftcoup_record',
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
			address_id:address_id,
			sear_time_s: $("#test1").val(),
			sear_time_e: $("#test2").val()
		},
		page: true,
		/* height: "400", */
		//limits: [10, 15, 20, 25],
		limit: limit,
		id: "driverListTable",
		done: function(res) {
			$('table.layui-table thead tr th:eq(0)').addClass('layui-hide');
			layer.close(index); //返回数据关闭loading
			var status = res.error_code;
			console.log(status)
			if (status == -101) {
				top.location.href = '../login/login.html';
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
					field: 'record_id',
					title: '记录ID',
					minWidth: 100,
					align: "center",
					style: 'display:none;'
				},
				{
					field: 'account',
					title: '管理员账号',
					minWidth: 100,
					align: "center"
				},
				{
					field: 'date',
					title: '发放时间',
					minWidth: 100,
					align: "center"
				},
				{
					field: 'amount',
					title: '发放金额',
					minWidth: 100,
					align: "center",
					hide: true
				},
				{
					field: 'nums',
					title: '发放记录',
					minWidth: 130,
					align: "center",
					hide: true
				},
				{
					title: '操作',
					minWidth: 180,
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
		var o_id = data.o_id;
		//查看
		if (layEvent === 'usable') {
			var record_id = data.record_id;
			var url = "listDetail.html?record_id=" + record_id
			window.open(url);//打开新的页面
		}
	});
	//搜索【此功能需要后台配合，所以暂时没有动态效果演示】
	$(".search_btn").on("click", function() {
		var index = layer.load(2); //添加laoding,0-2两种方式
		// if ($(".searchVal").val() != ''||$("#test1").val()!=''||$("#test2").val()!='') {
		table.reload("driverListTable", {
			page: {
				curr: 1 //重新从第 1 页开始
			},
			where: {
				sear_time_s: $("#test1").val(),
				sear_time_e: $("#test2").val()
			}
		})
		layer.close(index); //返回数据关闭loading
		// 		} else {
		// 			layer.msg("请输入搜索的内容");
		// 		}
	});
	//重置按钮【此功能需要后台配合，所以暂时没有动态效果演示】
	$(".reset").on("click", function() {
		var index = layer.load(2); //添加laoding,0-2两种方式
		// if ($(".searchVal").val() != ''||$("#test1").val()!=''||$("#test2").val()!='') {
		table.reload("driverListTable", {
			page: {
				curr: 1 //重新从第 1 页开始
			},
			where: {
				sear_time_s: '',
				sear_time_e: ''
			}
		})
		layer.close(index); //返回数据关闭loading
		// 		} else {
		// 			layer.msg("请输入搜索的内容");
		// 		}
	});
	//回车事件
	$(document).on('keydown', function(event) {
		var event = event || window.event;
		if (event.keyCode == 13) {
			//搜索
			// if ($(".searchVal").val() != ''||$("#test1").val()!=''||$("#test2").val()!='') {
			var index = layer.load(2); //添加laoding,0-2两种方式
			table.reload("driverListTable", {
				page: {
					curr: 1 //重新从第 1 页开始
				},
				where: {
					sear_time_s: $("#test1").val(),
					sear_time_e: $("#test2").val()
				}
			})
			layer.close(index); //返回数据关闭loading
			// 		} else {
			// 			layer.msg("请输入搜索的内容");
			// 		}
		}
	});
})