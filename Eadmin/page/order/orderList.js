var url = window.globalConfig.api; //接口地址
var limit = window.globalConfig.limit; //每页显示的条数
var pageSize = 20; //每页显示数据条数
var page = 1; //当前页数
var dataLength = 0; //数据总条数
var id = localStorage.getItem("userid");
var token = localStorage.getItem("token");
var address_id= localStorage.getItem("address_id");
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
		url: url + '/admin/Order/orderlist',
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
			state: $('#state option:selected').val(),
			sear_time_s: $("#test1").val(),
			sear_time_e: $("#test2").val()
		},
		page: true,
		/* height: "400", */
		//limits: [10, 15, 20, 25],
		limit: limit,
		id: "driverListTable",
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
				// 				{
				// 					type: "checkbox",
				// 					fixed: "left",
				// 					width: 50
				// 				},
				{
					field: 'o_id',
					title: '订单ID',
					minWidth: 100,
					align: "center",
					style: 'display:none;'
				},
				{
					field: 'order_num',
					title: '订单编号',
					minWidth: 100,
					align: "center"
				},
				{
					field: 'd_name',
					title: '司机姓名',
					minWidth: 100,
					align: "center",
					hide: true
				},
				{
					field: 'd_phone',
					title: '司机电话',
					minWidth: 130,
					align: "center",
					hide: true
				},
				{
					field: 'u_phone',
					title: '用户电话',
					minWidth: 130,
					align: "center",
					hide: true
				},
				{
					field: 'amount',
					title: '金额',
					minWidth: 100,
					align: "center"
				},
				{
					field: 'state_cn',
					title: '状态',
					minWidth: 130,
					align: "center",
					hide: true
				},
				{
					field: 'saddress',
					title: '起点',
					minWidth: 200,
					align: "center",
					hide: true
				},
				{
					field: 'eaddress',
					title: '终点',
					minWidth: 150,
					align: "center",
					hide: true
				},
				{
					field: 'paymethod',
					title: '支付方式',
					minWidth: 130,
					align: "center",
					hide: true
				},
				{
					field: 'sdate',
					title: '订单开始时间',
					minWidth: 180,
					align: "center"
				},
				/* {
					field: 'nickname',
					title: '用户昵称',
					minWidth: 150,
					align: "center",
					hide: true
				}, */
				/* {
					field: 'source',
					title: '来源',
					minWidth: 100,
					align: "center"
				}, */
				/* {
					field: 'warning',
					title: '报警',
					minWidth: 100,
					align: "center",
					hide: true,
					templet: function(d) {
						if (d.warning == 'Y') {
							return "超区订单";
						} else if (d.warning == 'N') {
							return "正常订单";
						}
					}
				}, */
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
	//搜索【此功能需要后台配合，所以暂时没有动态效果演示】
	$(".search_btn").on("click", function() {
		var index = layer.load(2); //添加laoding,0-2两种方式
		// if ($(".searchVal").val() != ''||$("#test1").val()!=''||$("#test2").val()!='') {
		table.reload("driverListTable", {
			page: {
				curr: 1 //重新从第 1 页开始
			},
			where: {
				search: $(".searchVal").val(), //搜索的关键字
				state: $('#state option:selected').val(),
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
				search: '', //搜索的关键字
				state: '',
				sear_time_s: '',
				sear_time_e: ''
			}
		})
		layer.close(index); //返回数据关闭loading
		// 		} else {
		// 			layer.msg("请输入搜索的内容");
		// 		}
	});
	//导出表格
	$(".daoAll_btn").on("click", function() {
		var state = $('#state option:selected').val();
		var start_time = $("#test1").val();
		var end_time = $("#test2").val();
		var search = $(".searchVal").val();
		/* var arr=[];
		arr. push(state,start_time,end_time,search);
		console.log('arr',arr) */
		if (state == '' && search!='' && start_time!=''&& end_time!='') {
			window.location.href = url + '/admin/Exportdata/orderexporde/start_time/' + start_time + '/' + 'end_time/' + end_time + '/' + 'phone/' + search+'/address_id/' + address_id;
		}
		if(search=='' && state != '' && start_time!=''&& end_time!=''){
			window.location.href = url + '/admin/Exportdata/orderexporde/state/' + state + '/' +
				'start_time/' + start_time + '/' + 'end_time/' + end_time+'/address_id/' + address_id;
		}
		if(start_time==''&& search!='' && state != ''&& end_time!=''){
				window.location.href = url + '/admin/Exportdata/orderexporde/state/' + state + '/'+ 'end_time/' + end_time + '/' + 'phone/' + search+'/address_id/' + address_id;
		}
		if(end_time=='' && search!='' && state != '' && start_time!=''){
			window.location.href = url + '/admin/Exportdata/orderexporde/state/' + state + '/' +
				'start_time/' + start_time + '/'+ 'phone/' + search+'/address_id/' + address_id;
		}
		if(state == '' && search==''&& start_time != '' && end_time!=''){
			window.location.href = url + '/admin/Exportdata/orderexporde/start_time/'+start_time+'/'+'end_time/'+end_time+'/address_id/' + address_id;
		}
		if(state == '' && start_time==''&& search != '' && end_time!=''){
			window.location.href = url + '/admin/Exportdata/orderexporde/end_time/' + end_time + '/' + 'phone/' + search+'/address_id/' + address_id;
		}
		if(state == '' && end_time==''&& search != '' && start_time!=''){
			window.location.href = url + '/admin/Exportdata/orderexporde/start_time/' + start_time + '/' + 'phone/' + search+'/address_id/' + address_id;
		}
		if(search == '' && start_time=='' && state != '' && end_time!=''){
			window.location.href = url + '/admin/Exportdata/orderexporde/state/' + state + '/'+ 'end_time/' + end_time+'/address_id/' + address_id;
		}
		if(search == '' && end_time=='' && state != '' && start_time!=''){
			window.location.href = url + '/admin/Exportdata/orderexporde/state/' + state + '/' +'start_time/' + start_time+'/address_id/' + address_id;
		}
		if(start_time == '' && end_time=='' && search != '' && state!=''){
			window.location.href = url + '/admin/Exportdata/orderexporde/state/' + state + '/' + 'phone/' + search+'/address_id/' + address_id;
		}
		if(start_time == '' && end_time=='' && search==''){
			window.location.href = url + '/admin/Exportdata/orderexporde/state/' + state+'/address_id/' + address_id;
		}
		if(state == '' && end_time=='' && start_time==''){
			window.location.href = url + '/admin/Exportdata/orderexporde/phone/' + search+'/address_id/' + address_id;
		}
		if(end_time == '' && state=='' && search==''){
			window.location.href = url + '/admin/Exportdata/orderexporde/start_time/' + start_time+'/address_id/' + address_id;
		}
		if(state == '' && search=='' && start_time==''){
			window.location.href = url + '/admin/Exportdata/orderexporde/end_time/' + end_time+'/address_id/' + address_id;
		}
		if(end_time!='' && search!='' && state != '' && start_time!=''){
			window.location.href = url + '/admin/Exportdata/orderexporde/state/' + state + '/' +
				'start_time/' + start_time + '/' + 'end_time/' + end_time + '/' + 'phone/' + search+'/address_id/' + address_id;
		}
		if(end_time=='' && search=='' && state == '' && start_time==''){
			window.location.href = url + '/admin/Exportdata/orderexporde/address_id/' + address_id;
		}
	});
	
	//列表操作
	table.on('tool(driverList)', function(obj) {
		var layEvent = obj.event,
			data = obj.data;
		var o_id = data.o_id;
		//订单详情
		if (layEvent === 'usable') {
			var o_id = data.o_id;
			var url = "orderInfo.html?o_id=" + o_id
			//window.location.href = url;
			window.open(url);//打开新的页面
		}
		//取消订单
		if (layEvent === 'usable1') {
			var o_id = data.o_id;
			lockPage(o_id)
		}
		//查看轨迹
		if (layEvent === 'usable2') {
			var o_id = data.o_id;
			var url = "guiji.html?o_id=" + o_id
			//window.location.href = url;
			window.open(url);//打开新的页面
		}
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
					search: $(".searchVal").val(), //搜索的关键字
					state: $('#state option:selected').val(),
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

//取消原因
function lockPage(o_id) {
	layer.open({
		title: false,
		type: 1,
		content: '<div class="admin-header-lock" id="lock-box">' +
			'<span class="layui-layer-setwin"><a class="layui-layer-ico layui-layer-close layui-layer-close1" href="javascript:;"></a></span>' +
			'<div id="o_id" style="display:none;">' + o_id + '</div>' +
			'<div style="font-size:15px;margin-top:20px;">请填取消订单的原因:</div>' +
			'<input id="canceltext" value="" class="input_btn">' +
			'<button class="layui-btn1" id="unlock">确定</button>' +
			'</div>',
		closeBtn: 0,
		shade: 0,
		success: function() {

		}
	})
	$(".admin-header-lock-input").focus();
}
//确定
$("body").on("click", "#unlock", function() {
	var o_id=$("#o_id").html();
	var canceltext=$("#canceltext").val();
	console.log("canceltext",canceltext);
	if(canceltext==""){
		alert("请填写取消订单原因~");
		return  false;
	}
	//取消订单
	$.ajax({
		url: url + '/admin/Order/cancelorder',
		type: "POST",
		data: {
			id: id,
			token: token,
			address_id:address_id,
			o_id: o_id,
			canceltext:canceltext
		},
		dataType: "json",
		success: function(data) {
			console.log('cancel', data)
			var code = data.error_code;
			if (code == 0) {
				alert("取消成功")
				window.location.reload();
			} else if (code == 1) {
				alert("此订单不能取消")
				window.location.reload();
			} else if (code == -402) {
				top.location.href = '../login/login.html';
			} else if (code == -101) {
				top.location.href = '../login/login.html';
			} else {
				alert("取消失败")
				window.location.reload();
			}
		},
	})
})