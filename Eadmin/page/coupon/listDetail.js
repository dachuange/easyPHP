var url1 = window.globalConfig.api; //接口地址
var limit = window.globalConfig.limit; //每页显示的条数
var pageSize = 20; //每页显示数据条数
var page = 1; //当前页数
var dataLength = 0; //数据总条数
var id = localStorage.getItem("userid");
var token = localStorage.getItem("token");
var address_id = localStorage.getItem("address_id");
var search = $('#search').val();
//获取当前url
var url = window.location.href;
GetRequest(url);
//订单详情
function GetRequest(url) {
	// var url = location.search; //获取url中"?"符后的字串 
	var theRequest = {};
	if (url.indexOf("?") != -1) {
		var str = url.substring(url.indexOf("?") + 1);
		// var str = str.substr(1); 
		console.log(str);
		strs = str.split("&");
		console.log(strs);
		for (var i = 0; i < strs.length; i++) {
			theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
		}
	}
	//console.log(theRequest);
	var record_id = theRequest.record_id;
	console.log('record_id', record_id)
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
			url: url1 + '/admin/Coup/getgiftcoup_user',
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
				record_id:record_id
			},
			page: false,
			/* height: "400", */
			//limits: [10, 15, 20, 25],
			limit: limit,
			id: "driverListTable",
			done: function(res) {
				layer.close(index); //返回数据关闭loading
				var status = res.error_code;
				console.log(status)
				if (status == -101) {
					top.location.href = '../login/login.html';
				}
			},
			cols: [
				[
					{
						field: 'nickname',
						title: '用户昵称',
						minWidth: 200,
						align: "center"
					},
					{
						field: 'phone',
						title: '用户电话',
						minWidth: 200,
						align: "center"
					}
				]
			]
		});
	})
}