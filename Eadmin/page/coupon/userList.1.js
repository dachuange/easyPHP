var url=window.globalConfig.api;//接口地址
var limit=window.globalConfig.limit;//每页显示的条数
var pageSize = 20; //每页显示数据条数
var page = 1; //当前页数
var dataLength = 0; //数据总条数
var search = $('#search').val();
var id = localStorage.getItem("userid");
var token= localStorage.getItem("token");
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
		url: url+'/admin/User/index',
		cellMinWidth: 95,
		method: 'POST', //laui 修改请求方式
		request: {
			page: 'page' //页码的参数名称，默认：page,每页数据量的参数名，默认：limit
		},
		where: {
			search: search, //搜索
			id:id,
			token:token,
			state:$('#state option:selected').val(),
			attend:$('#attend option:selected').val()
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
			layer.close(index);    //返回数据关闭loading
			$('table.layui-table thead tr th:eq(1)').addClass('layui-hide');
			var status = res.error_code;
			console.log(status)
			if (status ==-101) {
				top.location.href= '../login/login.html';
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
					style:'display:none;'
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

	//搜索【此功能需要后台配合，所以暂时没有动态效果演示】
	$(".search_btn").on("click", function() {
		/* if ($(".searchVal").val() != '') { */
		    var index = layer.load(2); //添加laoding,0-2两种方式
			table.reload("userListTable", {
				page: {
					curr: 1 //重新从第 1 页开始
				},
				where: {
					search: $(".searchVal").val() ,//搜索的关键字
					state:$('#state option:selected').val(),
					attend:$('#attend option:selected').val()
				}
			})
			layer.close(index); //返回数据关闭loading
		/* } else {
			layer.msg("请输入搜索的内容");
		} */
	});

	//批量发送
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
			console.log('u_id',newsId)
			$.ajax({
					url: url+'/admin/Coup/giftcoup',
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
							top.location.href= '../login/login.html';
						}else if (code == -101) {
							top.location.href= '../login/login.html';
						} else if (code == -501) {
							layer.msg("发送失败");
						}
					}
			})
		} else {
			layer.msg("请选择需要发送红包的用户");
		}
	})

	//单个发送
	table.on('tool(userListBar)', function(obj) {
			var token = $('#token').val();
			var id = localStorage.getItem("userid");
			var layEvent = obj.event,
				data = obj.data;
			var newsId = data.u_id;
			console.log('u_id',newsId)
			if (layEvent === 'edit') {
				$.ajax({
						url: url+'/admin/Coup/giftcoup',
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
								top.location.href= '../login/login.html';
							}else if (code == -101) {
								top.location.href= '../login/login.html';
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
