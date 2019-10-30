var url = window.globalConfig.api; //接口地址
var limit = window.globalConfig.limit; //每页显示的条数
var pageSize = 20; //每页显示数据条数
var page = 1; //当前页数
var dataLength = 0; //数据总条数
var search = $('#search').val();
var id = localStorage.getItem("userid");
var token = localStorage.getItem("token");
var address_id = localStorage.getItem("address_id");
layui.use(['form', 'layer', 'table', 'laytpl'], function() {
	var form = layui.form,
		layer = parent.layer === undefined ? layui.layer : top.layer,
		$ = layui.jquery,
		laytpl = layui.laytpl,
		table = layui.table;
	var index = layer.load(2); //添加laoding,0-2两种方式
	//用户列表
	var tableIns = table.render({
		elem: '#userList',
		url: url + '/admin/User/index',
		cellMinWidth: 95,
		method: 'POST', //laui 修改请求方式
		request: {
			page: 'page' //页码的参数名称，默认：page,每页数据量的参数名，默认：limit
		},
		where: {
			search: search, //搜索
			id: id,
			token: token,
			address_id:address_id,
			state: $('#state option:selected').val(),
			attend: $('#attend option:selected').val()
		},
		response: {
			statusName: 'error_code', //数据状态的字段名称，默认：code
			countName: 'count', //数据总数的字段名称，默认：count
			dataName: 'list', //默数据列表的字段名称，认：data        //我返回的datas集合 
		},
		page: true,
		limits: [10, 15, 20, 25],
		limit: limit,
		id: "userListTable",
		done: function(res) {
			layer.close(index); //返回数据关闭loading
			$('table.layui-table thead tr th:eq(0)').addClass('layui-hide');
			var status = res.error_code;
			console.log(status)
			if (status == -101) {
				top.location.href = '../login/login.html';
			}
		},
		cols: [
			[
				/* {
					type: "checkbox",
					fixed: "left",
					width: 50
				}, */
				{
					field: 'u_id',
					title: '用户ID',
					minWidth: 100,
					align: "center",
					style: 'display:none;'
				},
				{
					field: 'nickname',
					title: '用户昵称',
					minWidth: 100,
					align: "center"
				},
				{
					field: 'phone',
					title: '用户电话',
					minWidth: 100,
					align: "center"
				},
				{
					field: 'state_cn',
					title: '用车状态',
					minWidth: 100,
					align: "center"
				},
				{
					field: 'coup_nums_y',
					title: '已使用/未使用',
					minWidth: 100,
					align: 'center',
					templet: function(d) {
						return '' + d.coup_nums_y + '/' + d.coup_nums_n + ''
					}
				},
				{
					field: 'sdate',
					title: '注册时间',
					minWidth: 150,
					align: 'center'
				}
			]
		]
	});

	//搜索【此功能需要后台配合，所以暂时没有动态效果演示】
	$(".search_btn").on("click", function() {
		/* if ($(".searchVal").val() != '') { */
		var index = layer.load(2); //添加laoding,0-2两种方式
		table.reload("userListTable", {
			page: {
				curr: 1 //重新从第 1 页开始
			},
			where: {
				search: $(".searchVal").val(), //搜索的关键字
				state: $('#state option:selected').val(),
				attend: $('#attend option:selected').val()
			}
		})
		layer.close(index); //返回数据关闭loading
		/* } else {
			layer.msg("请输入搜索的内容");
		} */
	});
	//筛选[多条件筛选ajax]
	$(".select_btn").on("click", function() {
		lockPage()
	});
	//批量发送弹框（新版）
	$(".saveAll_btn1").click(function() {
		lockPage1();
	})
	//批量发送（旧版无用）
	$(".saveAll_btn").click(function() {
		var checkStatus = table.checkStatus('userListTable'),
			data = checkStatus.data,
			//newsId = [];
			newsId = '';
		if (data.length > 0) {
			for (var i in data) {
				//newsId.push(data[i].u_id);
				newsId += data[i].u_id;
			}
			console.log('u_id', newsId)
			$.ajax({
				url: url + '/admin/Coup/giftcoup',
				type: "POST",
				data: {
					id: id,
					token: token,
					idstr: newsId //将需要发送的newsId作为参数传入
				},
				dataType: "json",
				success: function(data) {
					console.log('发送红包', data)
					var code = data.error_code;
					if (code == 0) {
						layer.msg("发送成功");
						location.reload()
					} else if (code == -402) {
						top.location.href = '../login/login.html';
					} else if (code == -101) {
						top.location.href = '../login/login.html';
					} else if (code == -501) {
						layer.msg("发送失败");
					}
				}
			})
		} else {
			layer.msg("请选择需要发送红包的用户");
		}
	})
	//单个发送（旧版无用）
	table.on('tool(userListBar)', function(obj) {
		var token = $('#token').val();
		var id = localStorage.getItem("userid");
		var layEvent = obj.event,
			data = obj.data;
		var newsId = data.u_id;
		console.log('u_id', newsId)
		if (layEvent === 'edit') {
			$.ajax({
				url: url + '/admin/Coup/giftcoup',
				type: "POST",
				data: {
					id: id,
					token: token,
					idstr: newsId //将需要发送的newsId作为参数传入
				},
				dataType: "json",
				success: function(data) {
					console.log('发送红包', data)
					var code = data.error_code;
					if (code == 0) {
						layer.msg("发送成功");
						location.reload()
					} else if (code == -402) {
						top.location.href = '../login/login.html';
					} else if (code == -101) {
						top.location.href = '../login/login.html';
					} else if (code == -501) {
						layer.msg("发送失败");
					}
				}
			})
		}
	});
	//回车事件
	$(document).on('keydown', function(event) {
		var event = event || window.event;
		if (event.keyCode == 13) {
			/* 	if ($(".searchVal").val() != '') { */
			var index = layer.load(2); //添加laoding,0-2两种方式
			table.reload("userListTable", {
				page: {
					curr: 1 //重新从第 1 页开始
				},
				where: {
					search: $(".searchVal").val(), //搜索的关键字
					state: $('#state option:selected').val(),
					attend: $('#attend option:selected').val()
				}
			})
			layer.close(index); //返回数据关闭loading
			/* } else {
				layer.msg("请输入搜索的内容");
			} */
		}
	});
})
//红包筛选条件
function lockPage() {
	layer.open({
		title: false,
		type: 1,
		/* area: '300px', */
		content: '<div class="admin-header-lock1" id="lock-box">' +
			'<span class="layui-layer-setwin"><a class="layui-layer-ico layui-layer-close layui-layer-close1" href="javascript:;"></a></span>' +
			'<div style="font-size:15px;margin-bottom:40px;">请选择筛选条件</div>' +
			'<div class="layui-form-item" style="margin-top: 10px;">' +
			'<div class="layui-input-block1" id="a">' +
			'<input type="checkbox" name="a" value="Y" style="width: 16px;height: 16px;">' +
			'<input type="number" name="" value="" id="day_a" style="width: 50px;height: 25px;margin-left: 10px;">天内有' +
			'<input type="number" name="" value="" id="os_a" style="width: 50px;height: 25px;margin-left: 10px;">单' +
			'</div>' +
			'</div>' +
			'<div class="layui-form-item" style="margin-top: 10px;margin-left: -50px;">' +
			'<div class="layui-input-block1" id="b">' +
			'<input type="checkbox" name="b" value="Y" style="width: 16px;height: 16px;">' +
			'<input type="number" name="" value="" id="day_b" style="width: 50px;height: 25px;margin-left: 10px;">天内无订单' +
			'</div>' +
			'</div>' +
			'<div class="layui-form-item" style="margin-top: 10px;margin-left: -130px;">' +
			'<div class="layui-input-block1" id="c">' +
			'<input type="checkbox" name="c" value="Y" style="width: 16px;height: 16px;">' +
			'<span style="width: 50px;height: 25px;margin-left: 10px;">已注册</span>' +
			'</div>' +
			'</div>' +
			'<div class="layui-form-item" style="margin-top: 10px;margin-left: -130px;">' +
			'<div class="layui-input-block1" id="d">' +
			'<input type="checkbox" name="d" value="Y" style="width: 16px;height: 16px;">' +
			'<span style="width: 50px;height: 25px;margin-left: 10px;">无订单</span>' +
			'</div>' +
			'</div>' +
			'<div class="layui-form-item" style="margin-top: 10px;margin-left: -60px;">' +
			'<div class="layui-input-block1" id="e">' +
			'<input type="checkbox" name="e" value="Y" style="width: 16px;height: 16px;">' +
			'<input type="number" name="" value="" id="day_e" style="width: 50px;height: 25px;margin-left: 10px;">天内注册' +
			'</div>' +
			'</div>' +
			'<div class="layui-form-item" style="margin-top: 10px;margin-left: -75px;">' +
			'<div class="layui-input-block1" id="f">' +
			'<input type="checkbox" name="f" value="Y" style="width: 16px;height: 16px;">' +
			'<input type="number" name="" value="" id="bad_f" style="width: 50px;height: 25px;margin-left: 10px;">个差评' +
			'</div>' +
			'</div>' +
			'<div class="layui-form-item" style="margin-top: 10px;margin-left: -60px;">' +
			'<div class="layui-input-block1" id="g">' +
			'<input type="checkbox" name="g" value="Y" style="width: 16px;height: 16px;">' +
			'<input type="number" name="" value="" id="fuse_g" style="width: 50px;height: 25px;margin-left: 10px;">个取消单' +
			'</div>' +
			'</div>' +
			'<button class="layui-btn1" id="unlock" style="width:300px;margin:10% 0;">确定</button>' +
			'</div>',
		closeBtn: 0,
		shade: 0,
		success: function() {

		},
	})
	$(".admin-header-lock-input").focus();
}
//红包筛选确定按钮
$("body").on("click", "#unlock", function() {
	var a = $('#a input[name="a"]:checked ').val(); //获取选中的值
	var b = $('#b input[name="b"]:checked ').val(); //获取选中的值
	var c = $('#c input[name="c"]:checked ').val(); //获取选中的值
	var d = $('#d input[name="d"]:checked ').val(); //获取选中的值
	var e = $('#e input[name="e"]:checked ').val(); //获取选中的值
	var f = $('#f input[name="f"]:checked ').val(); //获取选中的值
	var g = $('#g input[name="g"]:checked ').val(); //获取选中的值
	var day_a = $('#day_a').val();
	var day_b = $('#day_b').val();
	var day_e = $('#day_e').val();
	var os_a = $('#os_a').val();
	var bad_f = $('#bad_f').val();
	var fuse_g = $('#fuse_g').val();
	//alert("123");
	layui.use(['form', 'layer', 'table', 'laytpl'], function() {
		var form = layui.form,
			layer = parent.layer === undefined ? layui.layer : top.layer,
			$ = layui.jquery,
			laytpl = layui.laytpl,
			table = layui.table;
		var index = layer.load(2); //添加laoding,0-2两种方式
		//用户列表
		var tableIns = table.render({
			elem: '#userList',
			url: url + '/admin/User/coup_user',
			cellMinWidth: 95,
			method: 'POST', //laui 修改请求方式
			request: {
				page: 'page' //页码的参数名称，默认：page,每页数据量的参数名，默认：limit
			},
			where: {
				id: id,
				token: token,
				search: $('.searchVal').val(),
				state: $('#state option:selected').val(),
				attend: $('#attend option:selected').val(),
				aa: a,
				bb: b,
				cc: c,
				dd: d,
				ee: e,
				ff: f,
				gg: g,
				day_a: day_a,
				day_b: day_b,
				day_e:day_e,
				os_a: os_a,
				bad_f: bad_f,
				fuse_g: fuse_g
			},
			response: {
				statusName: 'error_code', //数据状态的字段名称，默认：code
				countName: 'count', //数据总数的字段名称，默认：count
				dataName: 'list', //默数据列表的字段名称，认：data        //我返回的datas集合 
			},
			page: true,
			/* height: "400", */
			limits: [10, 15, 20, 25],
			limit: limit,
			id: "userListTable",
			done: function(res) {
				layer.close(index); //返回数据关闭loading
				$('table.layui-table thead tr th:eq(1)').addClass('layui-hide');
				var status = res.error_code;
				console.log(status)
				var liststr = res.liststr;
				console.log("liststr", liststr)
				localStorage.setItem("liststr", liststr)
				if (status == -101) {
					top.location.href = '../login/login.html';
				}
			},
			cols: [
				[{
						type: "checkbox",
						fixed: "left",
						width: 50
					},
					{
						field: 'u_id',
						title: '用户ID',
						/* minWidth: 100, */
						align: "center",
						style: 'display:none;'
					},
					// 				{
					// 					field: 'headimgurl',
					// 					title: '用户头像',
					// 					width: 180,
					// 					align: "center",
					// 					templet: function(d) {
					// 						return '<img src=' + d.headimgurl + ' height="100px;width:100px;" />';
					// 					}
					// 				},
					{
						field: 'nickname',
						title: '用户昵称',
						minWidth: 100,
						align: "center"
					},
					{
						field: 'phone',
						title: '用户电话',
						minWidth: 100,
						align: "center"
					},
					{
						field: 'state_cn',
						title: '用车状态',
						minWidth: 100,
						align: "center"
					},
					{
						field: 'sdate',
						title: '注册时间',
						minWidth: 150,
						align: 'center'
					}
				]
			]
		});
	});
})
//红包设置弹框
function lockPage1() {
	type();
	layer.open({
		title: false,
		type: 1,
		/* area: '300px', */
		content: '<div class="admin-header-lock1" id="lock-box">' +
			'<span class="layui-layer-setwin"><a class="layui-layer-ico layui-layer-close layui-layer-close1" href="javascript:;"></a></span>' +
			'<div style="font-size:15px;margin-bottom:40px;">设置红包发放条件:</div>' +
			'<div class="layui-inline" style="margin-bottom: 20px;">' +
			'<label class="layui-form-label">红包金额</label>' +
			'<div class="layui-input-inline">' +
			'<input type="text" name="" id="money" class="layui-input">' +
			'</div>' +
			'</div>' +
			'<div class="layui-inline" style="margin-bottom: 20px;">' +
			'<label class="layui-form-label">红包类型</label>' +
			'<div class="layui-input-inline">' +
			'<select name="state" id="type" style="width: 182px;height: 38px;">' +
			'</select>' +
			'</div>' +
			'</div>' +
			'<div class="layui-inline" style="margin-bottom: 20px;">' +
			'<label class="layui-form-label">开始时间</label>' +
			'<div class="layui-input-inline">' +
			'<input type="text" class="layui-input" id="test5" placeholder="yyyy-MM-dd HH:mm:ss" name="createtime" style="height:38px;width:180px;">' +
			'</div>' +
			'</div>' +
			'<div class="layui-inline" style="margin-bottom: 20px;">' +
			'<label class="layui-form-label">结束时间</label>' +
			'<div class="layui-input-inline">' +
			'<input type="text" class="layui-input" id="test6" placeholder="yyyy-MM-dd HH:mm:ss" name="createtime" style="height:38px;width:180px;">' +
			'</div>' +
			'</div>' +
			'<div class="layui-inline" style="margin-bottom: 20px;">' +
			'<label class="layui-form-label">推送内容</label>' +
			'<div class="layui-input-inline">' +
			'<textarea type="text" name="" id="send_text" class="layui-input" style="height: 100px;"></textarea>' +
			'</div>' +
			'</div>' +
			'<button class="layui-btn" id="unlock1" style="width:300px;margin:5% 0;">确定</button>' +
			'</div>',
		closeBtn: 0,
		shade: 0,
		success: function(layero, index) {
			layui.use('laydate', function() {
				var laydate = layui.laydate;
				//时间选择器
				laydate.render({
					elem: '#test5',
					type: 'datetime',
					trigger: 'click',
					format: "yyyy-MM-dd HH:mm:ss",
					value: new Date()
				});
				//时间选择器
				laydate.render({
					elem: '#test6',
					type: 'datetime',
					trigger: 'click',
					format: "yyyy-MM-dd HH:mm:ss",
					value: new Date()
				});
			})
		}
	})
	$(".admin-header-lock-input").focus();
}
//红包设置确认按钮
$("body").on("click", "#unlock1", function() {
	//alert(456);

	layui.use(['form', 'layer', 'table', 'laytpl'], function() {
		var form = layui.form,
			layer = parent.layer === undefined ? layui.layer : top.layer,
			$ = layui.jquery,
			laytpl = layui.laytpl,
			table = layui.table;
			if ($("#money").val() == '') {
				layer.msg("请设置红包金额");
				return false;
			}
		layer.confirm('确定要发放红包？', {
			icon: 3,
			title: '提示信息'
		}, function(index) {
			var idstr = localStorage.getItem("liststr");
			console.log("idstr", idstr);
			var amount = $("#money").val();
			var typeid = $('#type option:selected').val();
			var sdate = $("#test5").val();
			var edate = $("#test6").val();
			var send_text = $("#send_text").val();
			$.ajax({
				url: url + '/admin/Coup/giftcoup',
				type: "POST",
				data: {
					id: id,
					token: token,
					idstr: idstr, //将需要发送的newsId作为参数传入
					amount: amount,
					sdate: sdate,
					edate: edate,
					typeid: typeid,
					send_text:send_text
				},
				dataType: "json",
				success: function(data) {
					console.log('发送红包', data)
					var code = data.error_code;
					if (code == 0) {
						window.localStorage.removeItem('liststr')
						layer.msg("发送成功");
						layer.close(index);
						window.location.reload();
					} else if (code == -402) {
						top.location.href = '../login/login.html';
					} else if (code == -101) {
						top.location.href = '../login/login.html';
					} else if (code == -501) {
						layer.msg(data.message);
						layer.close(index);
					}
				}
			});
		})
	});
})
//获取红包类型
function type() {
	$.ajax({
		url: url + '/admin/Coup/coup_type',
		type: "POST",
		data: {
			id: id,
			token: token
		},
		dataType: "json",
		success: function(data) {
			console.log('type', data)
			if (data.error_code == 0) {
				$('#type').empty();
				var result = data.type;
				var size = result.length;
				if (size > 0) {
					var str = "";
					for (var i = 0; i < size; i++) {
						var address = result[i];
						str += '<option value=' + address.couid + '>' + address.val + '</option>'
					}
					$('#type').append(str);
					$('#type').show();
				} else {
					$('#type').hide();
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
}
