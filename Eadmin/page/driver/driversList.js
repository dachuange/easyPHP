var url = window.globalConfig.api; //接口地址
var limit=window.globalConfig.limit;//每页显示的条数
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
		url: url + '/admin/Driver/driver_list',
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
			reviewed: 'O'
		},
		response: {
			statusName: 'error_code', //数据状态的字段名称，默认：code
			countName: 'count', //数据总数的字段名称，默认：count
			dataName: 'list', //默数据列表的字段名称，认：data        //我返回的datas集合 
		},
		page: true,
		/* height: "400", */
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
					field: 'carnum',
					title: '车牌号',
					minWidth: 120,
					align: 'center'
				},
				{
					field: 'car_type',
					title: '车辆类型',
					minWidth: 120,
					align: 'center'
				},
				{
					field: 'carcolor',
					title: '车辆颜色',
					minWidth: 100,
					align: 'center'
				},
				{
					field: 'sdate',
					title: '注册时间',
					minWidth: 200,
					align: 'center'
				},
				{
					field: 'maturity_date',
					title: '到期时间',
					minWidth: 150,
					align: 'center'
				},
				{
					field: 'reviewed_cn',
					title: '审核状态',
					minWidth: 100,
					align: 'center'
				},
				{
					field: 'state_cn',
					title: '司机行车状态',
					minWidth: 120,
					align: 'center'
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
	//司机搜索【此功能需要后台配合，所以暂时没有动态效果演示】
	$(".search_btn").on("click", function() {
		/* if ($(".searchVal").val() != '') { */
		    var index = layer.load(2); //添加laoding,0-2两种方式
			table.reload("driverListTable", {
				page: {
					curr: 1 //重新从第 1 页开始
				},
				where: {
					search: $(".searchVal").val() //搜索的关键字
				},
			})
			layer.close(index);    //返回数据关闭loading
		/* } else {
			layer.msg("请输入搜索的内容");
		} */
	});
	//重置按钮【此功能需要后台配合，所以暂时没有动态效果演示】
	$(".reset").on("click", function() {
		/* if ($(".searchVal").val() != '') { */
		    var index = layer.load(2); //添加laoding,0-2两种方式
			table.reload("driverListTable", {
				page: {
					curr: 1 //重新从第 1 页开始
				},
				where: {
					search: '' //搜索的关键字
				},
			})
			layer.close(index);    //返回数据关闭loading
		/* } else {
			layer.msg("请输入搜索的内容");
		} */
	});
	//列表操作
	table.on('tool(driverList)', function(obj) {
		var layEvent = obj.event,
			data = obj.data;
		console.log(data)
		if (layEvent === 'detail') { //查看司机详情（用）
			var d_id = data.d_id;
			var Url = "driverInfo.html?d_id=" + d_id
			window.location.href = Url;
		}
	});
    //回车事件
    $(document).on('keydown', function(event) {
    	var event = event || window.event;
    	if (event.keyCode == 13) {
    		/* if ($(".searchVal").val() != '') { */
			    var index = layer.load(2); //添加laoding,0-2两种方式
    			table.reload("driverListTable", {
    				page: {
    					curr: 1 //重新从第 1 页开始
    				},
    				where: {
    					search: $(".searchVal").val() //搜索的关键字
    				},
    			})
				layer.close(index);    //返回数据关闭loading
    		/* } else {
    			layer.msg("请输入搜索的内容");
    		} */
    	}
    });
})
