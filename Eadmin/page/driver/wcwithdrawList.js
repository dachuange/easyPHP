var url = window.globalConfig.api; //接口地址
var limit = window.globalConfig.limit; //每页显示的条数
var pageSize = 20; //每页显示数据条数
var page = 1; //当前页数
var dataLength = 0; //数据总条数
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
		url: url + '/Admin/list',
		cellMinWidth: 95,
		method: 'POST', //laui 修改请求方式
		request: {
			//search: 'search', //搜索
			page: 'page' //页码的参数名称，默认：page,每页数据量的参数名，默认：limit
		},
		where: {
			id: id,
			token: token,
			address_id:address_id,
			state: $('#state option:selected').val()
		},
		page: true,
		/* 	height: "400", */
		//limits: [10, 15, 20, 25],
		limit: limit,
		response: {
			statusName: 'status', //数据状态的字段名称，默认：code
			statusCode: 'Success', //成功的状态码，默认：0
			countName: 'count', //数据总数的字段名称，默认：count
			dataName: 'list', //默数据列表的字段名称，认：data        //我返回的datas集合 
		},
		id: "driverListTable",
		done: function(res) {
			layer.close(index); //返回数据关闭loading
			console.log('res',res)
			var status = res.status;
			console.log('status',status)
			if (status == -101) {
				top.location.href = '../login/login.html';
			}
			/* for (var i = 0; i < res.data.length; i++) //遍历返回数据
			{
				if (res.data[i].state == 'O') //设置条件
				{
					$("table tbody tr").eq(i).css('background', '#FFC0CB') //改变满足条件行的颜色
				}
			} */
		},
		cols: [
			[
				{
					field: 'withdram_num',
					title: '申请单号',
					minWidth: 100,
					align: "center",
					hide: true
				},
				{
					field: 'payment_no',
					title: '微信订单号',
					minWidth: 100,
					align: "center",
					hide: true
				},
				{
					field: 'd_id',
					title: '司机ID',
					minWidth: 100,
					align: "center",
					hide: true
				},
				{
					field: 'openid',
					title: '司机openid',
					minWidth: 100,
					align: "center"
				},
				{
					field: 'realname',
					title: '司机姓名',
					minWidth: 100,
					align: "center"
				},
				{
					field: 'phone',
					title: '司机电话',
					minWidth: 130,
					align: "center"
				},
				{
					field: 'amount',
					title: '提现金额',
					minWidth: 100,
					align: "center"
				},
				{
					field: 'askfordate',
					title: '申请的日期',
					minWidth: 200,
					align: "center"
				},
				{
					field: 'paydate',
					title: '成功支付的日期',
					minWidth: 200,
					align: "center"
				},
				{
					field: 'state',
					title: '状态',
					minWidth: 100,
					align: "center",
					templet: function(d) {
						if (d.state == 'Y') {
							return "通过";
						} else if (d.state == 'N') {
							return "不通过";
						} else if (d.state == 'O') {
							return "待审核";
						}
					}
				},
				{
					field: 'refuse_reason',
					title: '拒绝原因',
					minWidth: 200,
					align: "center"
				},
				{
					// field: '', 
					title: '操作',
					minWidth: 175,
					templet: '#driverListBar',
					fixed: "right",
					align: "center"
				}
			]
		]
	});
	//提现搜索【此功能需要后台配合，所以暂时没有动态效果演示】
	$(".search_btn").on("click", function() {
		// if ($('#state option:selected').val()!='') {
		var index = layer.load(2); //添加laoding,0-2两种方式
		table.reload("driverListTable", {
			page: {
				curr: 1 //重新从第 1 页开始
			},
			where: {
				state: $('#state option:selected').val()
			},
		})
		layer.close(index); //返回数据关闭loading
		//     	} else {
		//     		layer.msg("请输入搜索的内容");
		//     	}
	});
	//重置按钮【此功能需要后台配合，所以暂时没有动态效果演示】
	$(".reset").on("click", function() {
		// if ($('#state option:selected').val()!='') {
		var index = layer.load(2); //添加laoding,0-2两种方式
		table.reload("driverListTable", {
			page: {
				curr: 1 //重新从第 1 页开始
			},
			where: {
				state: ''
			},
		})
		layer.close(index); //返回数据关闭loading
		//     	} else {
		//     		layer.msg("请输入搜索的内容");
		//     	}
	});
	//导出表格
	$(".daoAll_btn").on("click", function() {
		var state = $('#state option:selected').val();
		if (state == '') {
			alert("请选择要导出的提现状态");
			return false;
		}
		window.location.href = url +'/Admin/export_withdraw/state/' + state+'/address_id/' + address_id;
	});
	//列表操作
	table.on('tool(driverList)', function(obj) {
		var layEvent = obj.event,
			data = obj.data;
			console.log("data",data)
		/* var withdram_num = data.withdram_num;
		console.log("withdram_num",withdram_num) */
		//审核成功
		if (layEvent === 'usable') {
			//var index1 = layer.load(2);//弹出等待框
			var withdram_num = data.withdram_num;
			console.log("withdram_num",withdram_num);
			layer.confirm('确定通过提现审核？', {
				icon: 3,
				title: '提示信息'
			}, function(index) {
				var index1 = layer.load(2)
				/* 提现审核 */
				$.ajax({
					url: url + '/Admin/c2i',
					type: "POST",
					async: 'true',
					data: {
						id: id,
						token: token,
						address_id:address_id,
						withdram_num: withdram_num,
					},
					dataType: "json",
					success: function(data) {
						layer.close(index1);
						console.log('提现审核', data)
						var code = data.status;
						if (code == "Success") {
							alert("审核成功")
							layer.close(index);
							location.reload()
						} else if (code == "Fail") {
							alert(data.msg);
							layer.close(index);
							location.reload()
						} else if (code == -402) {
							top.location.href = '../login/login.html';
						} else if (code == -101) {
							top.location.href = '../login/login.html';
						} else if (code == -1) {
							alert("重复审核")
							layer.close(index);
							location.reload()
						} else if (code == -2) {
							alert("审核异常")
							layer.close(index);
							location.reload()
						}
					},
				})
				/* ajax结束 */
			})
			//layer.close(index1);//关闭等待框
		}
		//审核失败（无用）
		if (layEvent === 'usable1') {
			var withdram_num = data.withdram_num;
			lockPage(withdram_num)
		}
	})
	//回车事件
	$(document).on('keydown', function(event) {
		var event = event || window.event;
		if (event.keyCode == 13) {
			// if ($('#state option:selected').val()!='') {
			var index = layer.load(2); //添加laoding,0-2两种方式
			table.reload("driverListTable", {
				page: {
					curr: 1 //重新从第 1 页开始
				},
				where: {
					state: $('#state option:selected').val()
				},
			})
			layer.close(index); //返回数据关闭loading
			//     	} else {
			//     		layer.msg("请输入搜索的内容");
			//     	}
		}
	});
})
//拒绝原因
function lockPage(withdram_num) {
	layer.open({
		title: false,
		type: 1,
		content: '<div class="admin-header-lock" id="lock-box">' +
			'<span class="layui-layer-setwin"><a class="layui-layer-ico layui-layer-close layui-layer-close1" href="javascript:;"></a></span>' +
			'<div id="withdram_num" style="display:none;">' + withdram_num + '</div>' +
			'<div style="font-size:15px;margin-top:20px;">请填写审核失败的原因:</div>' +
			'<input id="reason" value="" class="input_btn">' +
			'<button class="layui-btn1" id="unlock">确定</button>' +
			'</div>',
		closeBtn: 0,
		shade: 0,
		success: function() {

		}
	})
	$(".admin-header-lock-input").focus();
}
//拒绝审核（无用）
$("body").on("click", "#unlock", function() {
	var reason=$("#reason").val();
	console.log('reason',reason)
	var withdram_num=$("#withdram_num").html();
	console.log("withdram_num",withdram_num);
	if(reason==""){
		alert("请填写拒绝原因~");
		return  false;
	}
	/* 拒绝提现审核 */
	$.ajax({
		url: url + '/Admin/refuse',
		type: "POST",
		data: {
			id: id,
			token: token,
			address_id:address_id,
			withdraw_num:$("#withdram_num").html(),
			reason:reason
		},
		dataType: "json",
		success: function(data) {
			console.log('提现审核', data)
			var code = data.status;
			window.localStorage.removeItem('withdraw_num')
			if (code == "Success") {
				alert("拒绝成功")
				window.location.reload();
			} else if (code == "Fail") {
				alert("拒绝失败")
				window.location.reload();
			} else if (code == -402) {
				top.location.href = '../login/login.html';
			} else if (code == -1) {
				alert("重复审核")
				window.location.reload();
			} else if (code == -2) {
				alert("审核异常")
				window.location.reload();
			}
		},
	})
});
