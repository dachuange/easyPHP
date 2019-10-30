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
		url: url + '/admin/Money/withdraw_list',
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
			statusName: 'error_code', //数据状态的字段名称，默认：code
			countName: 'count', //数据总数的字段名称，默认：count
			dataName: 'list', //默数据列表的字段名称，认：data        //我返回的datas集合 
		},
		id: "driverListTable",
		done: function(res) {
			layer.close(index); //返回数据关闭loading
			var status = res.error_code;
			console.log(status)
			if (status == -101) {
				top.location.href = '../login/login.html';
			}
			for (var i = 0; i < res.list.length; i++) //遍历返回数据
			{
				if (res.list[i].state == 'O') //设置条件
				{
					$("table tbody tr").eq(i).css('background', '#FFC0CB') //改变满足条件行的颜色
				}
			}
		},
		cols: [
			[
				// 				{
				// 					type: "checkbox",
				// 					fixed: "left",
				// 					width: 50
				// 				},
				/* {
					field: 'withdrawid',
					title: '提现编号',
					minWidth: 200,
					align: "center",
					hide: true
				}, */
				/* {
					field: 'd_id',
					title: '司机ID',
					minWidth: 200,
					align: "center",
					hide: true
				}, */
				{
					field: 'card',
					title: '司机工号',
					minWidth: 80,
					align: "center"
				},
				{
					field: 'name',
					title: '司机姓名',
					minWidth: 100,
					align: "center"
				},
				{
					field: 'banknum',
					title: '银行卡号',
					minWidth: 200,
					align: "center"
				},
				{
					field: 'bankaddress',
					title: '开户行',
					minWidth: 150,
					align: "center"
				},
				{
					field: 'banktype',
					title: '银行卡类型',
					minWidth: 150,
					align: "center"
				},
				{
					field: 'pename',
					title: '卡所属名称',
					minWidth: 130,
					align: "center"
				},
				{
					field: 'bankphone',
					title: '银行预留手机号',
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
					field: 'date',
					title: '提现时间',
					minWidth: 200,
					align: "center"
				},
				{
					field: 'fuse_reason',
					title: '拒绝原因',
					minWidth: 200,
					align: "center"
				},
				{
					field: 'state_c',
					title: '状态',
					minWidth: 100,
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
				state:''
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
		window.location.href = url +'/admin/Exportdata/export_withdraw/state/' + state+'/address_id/' + address_id;
	});
	//列表操作
	table.on('tool(driverList)', function(obj) {
		var layEvent = obj.event,
			data = obj.data;
		var withdrawid = data.withdrawid;
		//审核成功
		if (layEvent === 'usable') {
			layer.confirm('确定通过提现审核？', {
				icon: 3,
				title: '提示信息'
			}, function(index) {
				var reviewed = 'Y';
				console.log('reviewed', reviewed)
				/* 提现审核 */
				$.ajax({
					url: url + '/admin/Money/withdraw_submit',
					type: "POST",
					data: {
						id: id,
						token: token,
						address_id:address_id,
						withdrawid: withdrawid,
						pd: reviewed
					},
					dataType: "json",
					success: function(data) {
						console.log('提现审核', data)
						var code = data.error_code;
						if (code == 0) {
							alert("审核成功")
							layer.close(index);
							location.reload()
						} else if (code == -402) {
							top.location.href = '../login/login.html';
						} else if (code == -101) {
							top.location.href = '../login/login.html';
						} else if (code == -405) {
							alert("审核失败")
							layer.close(index);
							location.reload()
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
			})
		}
		//审核失败
		if (layEvent === 'usable1') {
			var d_id = data.d_id;
			var withdrawid = data.withdrawid;
			lockPage(d_id,withdrawid)
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
function lockPage(d_id,withdrawid) {
	var d_id = d_id;
	var withdrawid=withdrawid;
	layer.open({
		title: false,
		type: 1,
		content: '<div class="admin-header-lock" id="lock-box">' +
			'<span class="layui-layer-setwin"><a class="layui-layer-ico layui-layer-close layui-layer-close1" href="javascript:;"></a></span>' +
			'<div id="d_id" style="display:none;">' + d_id + '</div>' +
			'<div id="withdrawid" style="display:none;">' + withdrawid + '</div>' +
			'<div style="font-size:15px;margin-top:20px;">请填写审核失败的原因:</div>' +
			'<input id="fuse_reason" value="" class="input_btn">' +
			'<button class="layui-btn1" id="unlock">确定</button>' +
			'</div>',
		closeBtn: 0,
		shade: 0,
		success: function() {

		}
	})
	$(".admin-header-lock-input").focus();
}
//拒绝审核
$("body").on("click", "#unlock", function() {
	var reviewed = 'N';
	console.log('reviewed', reviewed)
	var fuse_reason=$("#fuse_reason").val();
	console.log('fuse_reason',fuse_reason)
	var withdrawid=$("#withdrawid").html();
	console.log('withdrawid',withdrawid)
	if(fuse_reason==""){
		alert("请填写拒绝原因~");
		return  false;
	}
	/* 拒绝提现审核 */
	$.ajax({
		url: url + '/admin/Money/withdraw_submit',
		type: "POST",
		data: {
			id: id,
			token: token,
			address_id:address_id,
			withdrawid: withdrawid,
			pd: reviewed,
			fuse_reason:fuse_reason
		},
		dataType: "json",
		success: function(data) {
			console.log('提现审核', data)
			var code = data.error_code;
			if (code == 0) {
				alert("拒绝成功")
				window.location.reload();
			} else if (code == -402) {
				top.location.href = '../login/login.html';
			} else if (code == -405) {
				alert("拒绝失败")
				window.location.reload();
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
