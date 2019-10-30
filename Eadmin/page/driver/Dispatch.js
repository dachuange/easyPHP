var url = window.globalConfig.api; //接口地址
var limit = window.globalConfig.limit; //每页显示的条数
var pageSize = 20; //每页显示数据条数
var page = 1; //当前页数
var dataLength = 0; //数据总条数
var search = $('#search').val();
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
		url: url + '/admin/Lmita/driver_list_con',
		cellMinWidth: 95,
		method: 'POST', //laui 修改请求方式
		request: {
			page: 'page' //页码的参数名称，默认：page,每页数据量的参数名，默认：limit
		},
		where: {

			id: id,
			token: token,
			address_id:address_id,
			state: $('#state option:selected').val()
		},
		response: {
			statusName: 'error_code', //数据状态的字段名称，默认：code
			countName: 'count', //数据总数的字段名称，默认：count
			dataName: 'list', //默数据列表的字段名称，认：data        //我返回的datas集合 
		},
		page: false,
		limit: limit,
		id: "driverListTable",
		done: function(res) {
			layer.close(index);    //返回数据关闭loading
			$('table.layui-table thead tr th:eq(0)').addClass('layui-hide');
			var status = res.error_code;
			console.log(status)
			if (status == -101) {
				top.location.href = '../login/login.html';
			}
		},
		cols: [
			[{
					field: 'd_id',
					title: '司机ID',
					minWidth: 100,
					align: "center",
					style: 'display:none;'
				},
				{
					field: 'name',
					title: '姓名/电话',
					minWidth: 100,
					align: 'center',
					templet: function(d) {
						return '' + d.name + '/' + d.phone + ''
					}
				},
				{
					field: 'daylits',
					title: '日成交/日总',
					minWidth: 120,
					align: 'center',
					/* sort: true, */
					templet: function(d) {
						return '' + d.daylits + '/' + d.daycounts + ''
					}
				},
				{
					field: 'lits',
					title: '总成交/总订单',
					minWidth: 120,
					align: 'center',
					/* sort: true, */
					templet: function(d) {
						return '' + d.lits + '/' + d.counts + ''
					},
				},
				{
					title: '操作',
					minWidth: 100,
					templet: '#driverListBar',
					fixed: "right",
					align: "center"
				}
			]
		]
	});
	//搜索
	$(".search_btn").on("click", function() {
		$('#you').hide();
		// if ($(".searchVal").val() != '' || $('#state option:selected').val()!='') {
		table.reload("driverListTable", {
			page: {
				curr: 1 //重新从第 1 页开始
			},
			where: {
				search: $(".searchVal").val(), //搜索的关键字
				start_time: $("#test1").val(),
				end_time: $("#test2").val()
			},
		})
		// 		} else {
		// 			layer.msg("请输入搜索的内容");
		// 		}
	});
	//重置按钮
	$(".reset").on("click", function() {
		$('#you').hide();
		// if ($(".searchVal").val() != '' || $('#state option:selected').val()!='') {
		table.reload("driverListTable", {
			page: {
				curr: 1 //重新从第 1 页开始
			},
			where: {
				search: '', //搜索的关键字
				start_time: '',
				end_time: ''
			},
		})
		// 		} else {
		// 			layer.msg("请输入搜索的内容");
		// 		}
	});
	//列表操作
	table.on('tool(driverList)', function(obj) {
		var layEvent = obj.event,
			data = obj.data;
		console.log(data)
		if (layEvent === 'detail') { //查看订单详情
			var d_id = data.d_id;
			console.log('d_id', d_id);
			localStorage.setItem("driverid",d_id)
			$('#you').show();
			//获取订单的筛选条件+数量
			$.ajax({
				url: url + '/admin/Lmita/order_state_list',
				type: "POST",
				data: {
					id: id,
					token: token,
					address_id:address_id,
					d_id: d_id
				},
				dataType: "json",
				success: function(data) {
					console.log('state', data)
					if (data.error_code == 0) {
						$('#orderState').empty();
						var result = data.list
						var size = result.length;
						if (size > 0) {
							var str = "";
							for (var i = 0; i < size; i++) {
								var address = result[i];
								if (i == 0) {
									str += '<li lay-id="" class="layui-this">' +
										address.state_cn + '(' + address.count + ')</li>'
								} else {
									str += '<li lay-id=' + address.state + '>' +
										address.state_cn + '(' + address.count + ')</li>'
								}
							}
							$('#orderState').append(str);
							$('#orderState').show();
						} else {
							$('#orderState').hide();
						}
					} else if (data.error_code == -101) {
						top.location.href = '../login/login.html';
					} else if (data.error_code == -402) {
						top.location.href = '../login/login.html';
					} else if (data.error_code == -1) {
						alert(data.message)
					}
				},
			})
			//end

			//司机关联订单列表
			var tableIns = table.render({
				elem: '#orderList',
				url: url + '/admin/Lmita/driver_order_list_con',
				cellMinWidth: 95,
				method: 'POST', //laui 修改请求方式
				request: {
					page: 'page' //页码的参数名称，默认：page,每页数据量的参数名，默认：limit
				},
				where: {

					id: id,
					token: token,
					address_id:address_id,
					state: "",
					d_id: d_id
				},
				response: {
					statusName: 'error_code', //数据状态的字段名称，默认：code
					countName: 'count', //数据总数的字段名称，默认：count
					dataName: 'list', //默数据列表的字段名称，认：data        //我返回的datas集合 
				},
				page: false,
				limit: limit,
				id: "orderListTable",
				done: function(res) {
					layer.close(index);    //返回数据关闭loading
					//$('table.layui-table thead tr th:eq(0)').addClass('layui-hide');
					var status = res.error_code;
					console.log(status)
					if (status == -101) {
						top.location.href = '../login/login.html';
					}
				},
				cols: [
					[{
							field: 'order_num',
							title: '订单编号',
							minWidth: 60,
							align: "center"
						},
						{
							field: 'd_name',
							title: '司机姓名',
							minWidth: 80,
							align: "center",
							hide: true
						},
						{
							field: 'd_phone',
							title: '司机电话',
							minWidth: 80,
							align: "center",
							hide: true
						},
						{
							field: 'u_phone',
							title: '用户电话',
							minWidth: 80,
							align: "center",
							hide: true
						},
						{
							field: 'amount',
							title: '金额',
							minWidth: 70,
							align: "center"
						},
						{
							field: 'state_cn',
							title: '状态',
							minWidth: 80,
							align: "center",
							hide: true
						},
						{
							field: 'saddress',
							title: '起点',
							minWidth: 80,
							align: "center",
							hide: true
						},
						{
							field: 'eaddress',
							title: '终点',
							minWidth: 80,
							align: "center",
							hide: true
						},
						{
							field: 'paymethod',
							title: '支付方式',
							minWidth: 80,
							align: "center",
							hide: true
						},
						{
							field: 'sdate',
							title: '订单开始时间',
							minWidth: 80,
							align: "center"
						},
						{
							field: 'nickname',
							title: '用户昵称',
							minWidth: 80,
							align: "center",
							hide: true
						},
						{
							field: 'source',
							title: '来源',
							minWidth: 80,
							align: "center"
						},
						{
							field: 'warning',
							title: '报警',
							minWidth: 80,
							align: "center",
							hide: true,
							templet: function(d) {
								if (d.warning == 'Y') {
									return "超区订单";
								} else if (d.warning == 'N') {
									return "正常订单";
								}
							}
						},
						{
							field: 'o_id',
							title: '订单ID',
							minWidth: 40,
							align: "center"
							/* style: 'display:none;' */
						},
						{
							title: '操作',
							minWidth: 80,
							templet: '#orderListBar',
							fixed: "right",
							align: "center"
						}
					]
				]
			});
			//订单列表操作
			table.on('tool(orderList)', function(obj) {
				var layEvent = obj.event,
					data = obj.data;
				console.log(data)
				if (layEvent === 'detail1') { //查看订单详情
					var o_id = data.o_id;
					console.log('o_id', o_id);
					window.location.href = "../order/orderInfo.html?o_id=" + o_id;

				}
			});
			//////////////////end
		}
	});
	//回车事件
	$(document).on('keydown', function(event) {
		var event = event || window.event;
		if (event.keyCode == 13) {
			$('#you').hide();
			// if ($(".searchVal").val() != '' || $('#state option:selected').val()!='') {
			table.reload("driverListTable", {
				page: {
					curr: 1 //重新从第 1 页开始
				},
				where: {
					search: $(".searchVal").val(), //搜索的关键字
					start_time: $("#test1").val(),
					end_time: $("#test2").val()
				},
			})
			// 		} else {
			// 			layer.msg("请输入搜索的内容");
			// 		}
		}
	});
})
//获取司机筛选条件+数量
$.ajax({
	url: url + '/admin/Lmita/driver_state_list',
	type: "POST",
	data: {
		id: id,
		token: token,
		address_id:address_id
	},
	dataType: "json",
	success: function(data) {
		console.log('state', data)
		if (data.error_code == 0) {
			$('#state').empty();
			var result = data.list
			var size = result.length;
			if (size > 0) {
				var str = "";
				for (var i = 0; i < size; i++) {
					var address = result[i];
					str += '<option value=' + address.state + '>' + address.state_cn + '(' + address.count + ')</option>'
				}
				$('#state').append(str);
				$('#state').show();
			} else {
				$('#state').hide();
			}
		} else if (data.error_code == -101) {
			top.location.href = '../login/login.html';
		} else if (data.error_code == -402) {
			top.location.href = '../login/login.html';
		} else if (data.error_code == -1) {
			alert(data.message)
		}
	},
})
//监听select
$("#state").change(function() {
	$('#you').hide();
	var opt = $("#state").val();
	console.log('opt', opt)
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
			url: url + '/admin/Lmita/driver_list_con',
			cellMinWidth: 95,
			method: 'POST', //laui 修改请求方式
			request: {
				page: 'page' //页码的参数名称，默认：page,每页数据量的参数名，默认：limit
			},
			where: {

				id: id,
				token: token,
				address_id:address_id,
				state: opt
			},
			response: {
				statusName: 'error_code', //数据状态的字段名称，默认：code
				countName: 'count', //数据总数的字段名称，默认：count
				dataName: 'list', //默数据列表的字段名称，认：data        //我返回的datas集合 
			},
			page: false,
			limit: limit,
			id: "driverListTable",
			done: function(res) {
				layer.close(index);    //返回数据关闭loading
				$('table.layui-table thead tr th:eq(0)').addClass('layui-hide');
				var status = res.error_code;
				console.log(status)
				if (status == -101) {
					top.location.href = '../login/login.html';
				}
			},
			cols: [
				[{
						field: 'd_id',
						title: '司机ID',
						minWidth: 100,
						align: "center",
						style: 'display:none;'
					},
					{
						field: 'name',
						title: '姓名/电话',
						minWidth: 100,
						align: 'center',
						templet: function(d) {
							return '' + d.name + '/' + d.phone + ''
						}
					},
					{
						field: 'daylits',
						title: '日成交/日总',
						minWidth: 120,
						align: 'center',
						templet: function(d) {
							return '' + d.daylits + '/' + d.daycounts + ''
						}
					},
					{
						field: 'lits',
						title: '总成交/总订单',
						minWidth: 120,
						align: 'center',
						/* sort: true, */
						templet: function(d) {
							return '' + d.lits + '/' + d.counts + ''
						},
					},
					{
						title: '操作',
						minWidth: 100,
						templet: '#driverListBar',
						fixed: "right",
						align: "center"
					}
				]
			]
		});
	
	});
});
//tab切换
layui.use('element', function() {
	var element = layui.element;
	//获取hash来切换选项卡，假设当前地址的hash为lay-id对应的值
	var layid = location.hash.replace(/^#test1=/, '');
	console.log('layid', layid)
	element.tabChange('test1', layid); //假设当前地址为：http://a.com#test1=222，那么选项卡会自动切换到“发送消息”这一项

	//监听Tab切换，以改变地址hash值
	element.on('tab(test1)', function() {
		location.hash = 'test1=' + this.getAttribute('lay-id');
		var layid = this.getAttribute('lay-id');
		console.log('layid', layid)
		var driverid = localStorage.getItem("driverid");
		console.log("driverid",driverid)
		/* tab切换 */
		//司机关联订单列表
		layui.use(['form', 'layer', 'table', 'laytpl'], function() {
			var form = layui.form,
				layer = parent.layer === undefined ? layui.layer : top.layer,
				$ = layui.jquery,
				laytpl = layui.laytpl,
				table = layui.table;
			var index = layer.load(2); //添加laoding,0-2两种方式
			var tableIns = table.render({
				elem: '#orderList',
				url: url + '/admin/Lmita/driver_order_list_con',
				cellMinWidth: 95,
				method: 'POST', //laui 修改请求方式
				request: {
					page: 'page' //页码的参数名称，默认：page,每页数据量的参数名，默认：limit
				},
				where: {

					id: id,
					token: token,
					address_id:address_id,
					state: layid,
					d_id: driverid
				},
				response: {
					statusName: 'error_code', //数据状态的字段名称，默认：code
					countName: 'count', //数据总数的字段名称，默认：count
					dataName: 'list', //默数据列表的字段名称，认：data        //我返回的datas集合 
				},
				page: false,
				limit: limit,
				id: "orderListTable",
				done: function(res) {
					layer.close(index);    //返回数据关闭loading
					//$('table.layui-table thead tr th:eq(0)').addClass('layui-hide');
					var status = res.error_code;
					console.log(status)
					if (status == -101) {
						top.location.href = '../login/login.html';
					}
				},
				cols: [
					[{
							field: 'order_num',
							title: '订单编号',
							minWidth: 60,
							align: "center"
						},
						{
							field: 'd_name',
							title: '司机姓名',
							minWidth: 80,
							align: "center",
							hide: true
						},
						{
							field: 'd_phone',
							title: '司机电话',
							minWidth: 80,
							align: "center",
							hide: true
						},
						{
							field: 'u_phone',
							title: '用户电话',
							minWidth: 80,
							align: "center",
							hide: true
						},
						{
							field: 'amount',
							title: '金额',
							minWidth: 70,
							align: "center"
						},
						{
							field: 'state_cn',
							title: '状态',
							minWidth: 80,
							align: "center",
							hide: true
						},
						{
							field: 'saddress',
							title: '起点',
							minWidth: 80,
							align: "center",
							hide: true
						},
						{
							field: 'eaddress',
							title: '终点',
							minWidth: 80,
							align: "center",
							hide: true
						},
						{
							field: 'paymethod',
							title: '支付方式',
							minWidth: 80,
							align: "center",
							hide: true
						},
						{
							field: 'sdate',
							title: '订单开始时间',
							minWidth: 80,
							align: "center"
						},
						{
							field: 'nickname',
							title: '用户昵称',
							minWidth: 80,
							align: "center",
							hide: true
						},
						{
							field: 'source',
							title: '来源',
							minWidth: 80,
							align: "center"
						},
						{
							field: 'warning',
							title: '报警',
							minWidth: 80,
							align: "center",
							hide: true,
							templet: function(d) {
								if (d.warning == 'Y') {
									return "超区订单";
								} else if (d.warning == 'N') {
									return "正常订单";
								}
							}
						},
						{
							field: 'o_id',
							title: '订单ID',
							minWidth: 40,
							align: "center"
							/* style: 'display:none;' */
						},
						{
							title: '操作',
							minWidth: 80,
							templet: '#orderListBar',
							fixed: "right",
							align: "center"
						}
					]
				]
			});
			//订单列表操作
			table.on('tool(orderList)', function(obj) {
				var layEvent = obj.event,
					data = obj.data;
				console.log(data)
				if (layEvent === 'detail1') { //查看订单详情
					var o_id = data.o_id;
					console.log('o_id', o_id);
					window.location.href = "../order/orderInfo.html?o_id=" + o_id;

				}
			});
		});
		//////////////////end
		/* tab切换 */
	});
});
//右边联动效果
$(function() {
	$('#you').hide();
})
