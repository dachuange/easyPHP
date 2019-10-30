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
		url: url + '/admin/Analysis/driver_onlinetime',
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
			start_time: $("#test1").val(),
			end_time: $("#test2").val()
		},
		response: {
			statusName: 'error_code', //数据状态的字段名称，默认：code
			countName: 'count', //数据总数的字段名称，默认：count
			dataName: 'list', //默数据列表的字段名称，认：data        //我返回的datas集合 
		},
		page: true,
		// height: "400",
		//limits: [10, 15, 20, 25],
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
					style: 'display:none;'
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
					minWidth: 120,
					align: 'center'
				},
				{
					field: 'phone',
					title: '司机电话',
					minWidth: 150,
					align: 'center'
				},
				{
					field: 'line_time',
					title: '在线时长',
					minWidth: 100,
					align: 'center',
					sort: true
				},
				{
					title: '操作',
					minWidth: 80,
					templet: '#driverListBar',
					fixed: "right",
					align: "center"
				}
			]
		]
	});
	//司机搜索【此功能需要后台配合，所以暂时没有动态效果演示】
	$(".search_btn").on("click", function() {
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
	//重置按钮【此功能需要后台配合，所以暂时没有动态效果演示】
	$(".reset").on("click", function() {
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
	//导出表格
	$(".daoAll_btn").on("click", function() {
		var start_time = $("#test1").val();
		var end_time = $("#test2").val();
		if (start_time == "") {
			alert("请筛选要导出的时间段");
			return false;
		}
		if (end_time == "") {
			alert("请筛选要导出的时间段");
			return false;
		}

		window.location.href = url +'/admin/Exportdata/export_driveronline/start_time/' +start_time +'/' + 'end_time/' + end_time+'/address_id/' + address_id;
	});
	//列表操作
	table.on('tool(driverList)', function(obj) {
		var layEvent = obj.event,
			data = obj.data;
		console.log(data)
		if (layEvent === 'detail') { //查看司机详情（用）
			var d_id = data.d_id;
			var Url = "driverZgtj.html?d_id=" + d_id
			//window.location.href = Url;
			window.open(Url);//打开新的页面
			
		} else if (layEvent === 'usable') {
			var d_id = data.d_id;
			lockPage(d_id)
		} else if (layEvent === 'del') {
			var d_id = data.d_id;
			var available_pd = data.available_pd;
			if (available_pd == 'Y') {
				alert("正常状态，无需解封")
				return false;
			}
			layer.confirm('确定解除禁封？', {
				icon: 3,
				title: '提示信息'
			}, function(index) {
				$.ajax({
					url: url + '/admin/Driver/unbanned_driver',
					type: "POST",
					data: {
						id: id,
						token: token,
						address_id:address_id,
						d_id: d_id
					},
					dataType: "json",
					success: function(data) {
						console.log('cancel', data)
						var code = data.error_code;
						if (code == 0) {
							alert("解封成功")
							layer.close(index);
							window.location.reload();
						} else if (code == -402) {
							top.location.href = '../login/login.html';
						} else if (code == -101) {
							top.location.href = '../login/login.html';
						}
					},
				})
			})
		}
	});
	//回车事件
	$(document).on('keydown', function(event) {
		var event = event || window.event;
		if (event.keyCode == 13) {
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


//禁封司机
function lockPage(d_id) {
	var d_id = d_id;
	layer.open({
		title: false,
		type: 1,
		content: '<div class="admin-header-lock" id="lock-box">' +
			'<span class="layui-layer-setwin"><a class="layui-layer-ico layui-layer-close layui-layer-close1" href="javascript:;"></a></span>' +
			'<div id="d_id" style="display:none;">' + d_id + '</div>' +
			'<div style="font-size:15px;margin-top:20px;">请选择封禁天数:</div>' +
			'<select name="state" id="day" class="input_btn">' +
			'<option value="1">1天</option>' +
			'<option value="2">2天</option>' +
			'<option value="3">3天</option>' +
			'<option value="7">7天</option>' +
			'<option value="15">15天</option>' +
			'<option value="forever">永久封禁</option>' +
			'</select>' +
			'<button class="layui-btn1" id="unlock">确定</button>' +
			'</div>',
		closeBtn: 0,
		shade: 0,
		success: function() {

		}
	})
	$(".admin-header-lock-input").focus();
}
//确定按钮
$("body").on("click", "#unlock", function() {
	var d_id = $('#d_id').html();
	var day = $('#day option:selected').val(); //选中的值
	$.ajax({
		url: url + '/admin/Driver/banned_driver',
		type: "POST",
		data: {
			d_id: d_id,
			id: id,
			token: token,
			address_id:address_id,
			day: day
		},
		dataType: "json",
		success: function(data) {
			console.log('jinfeng', data)
			var code = data.error_code;
			if (code == 0) {
				alert("禁封成功")
				window.location.reload();
			} else if (code == -402) {
				top.location.href = '../login/login.html';
			} else if (code == -101) {
				top.location.href = '../login/login.html';
			}
		},
	})
});
$(document).on('keydown', function(event) {
	var event = event || window.event;
	if (event.keyCode == 13) {
		$("#unlock").click();
	}
});
