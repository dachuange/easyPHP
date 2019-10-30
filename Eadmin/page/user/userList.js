var url = window.globalConfig.api; //接口地址
var limit = window.globalConfig.limit; //每页显示的条数
var pageSize = 20; //每页显示数据条数
var page = 1; //当前页数
var dataLength = 0; //数据总条数
var search = $('#search').val();
var id = localStorage.getItem("userid");
var token = localStorage.getItem("token");
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
			state: $('#state option:selected').val(),
			attend: $('#attend option:selected').val(),
			is_order:$('#is_order option:selected').val(),
			sear_time_s: $("#test1").val(),
			sear_time_e: $("#test2").val()
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
		id: "userListTable",
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
				/* {
					field: 'u_id',
					title: '用户ID',
					minWidth: 100,
					align: "center",
					hide: true
				}, */
				{
					field: 'headimgurl',
					title: '用户头像',
					width: 180,
					align: "center",
					templet: function(d) {
						return '<img src=' + d.headimgurl + ' height="100px;width:100px;" />';
					}
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
		/* 	if ($(".searchVal").val() != '') { */
		var index = layer.load(2); //添加laoding,0-2两种方式
		table.reload("userListTable", {
			page: {
				curr: 1 //重新从第 1 页开始
			},
			where: {
				search: $(".searchVal").val(), //搜索的关键字
				state: $('#state option:selected').val(),
				attend: $('#attend option:selected').val(),
				is_order:$('#is_order option:selected').val(),
				sear_time_s: $("#test1").val(),
				sear_time_e: $("#test2").val()
			}
		})
		layer.close(index); //返回数据关闭loading
		/* } else {
			layer.msg("请输入搜索的内容");
		} */
	});
    //重置按钮【此功能需要后台配合，所以暂时没有动态效果演示】
    $(".reset").on("click", function() {
    	/* 	if ($(".searchVal").val() != '') { */
    	var index = layer.load(2); //添加laoding,0-2两种方式
    	table.reload("userListTable", {
    		page: {
    			curr: 1 //重新从第 1 页开始
    		},
    		where: {
    			search: '', //搜索的关键字
    			state:'',
    			attend: '',
    			is_order:'',
    			sear_time_s: '',
    			sear_time_e: ''
    		}
    	})
    	layer.close(index); //返回数据关闭loading
    	/* } else {
    		layer.msg("请输入搜索的内容");
    	} */
    });
	//添加用户
	function addUser(edit) {
		var index = layui.layer.open({
			title: "添加用户",
			type: 2,
			content: "driverAdd.html",
			success: function(layero, index) {
				var body = layui.layer.getChildFrame('body', index);
				if (edit) {
					body.find(".userName").val(edit.userName); //登录名
					body.find(".userEmail").val(edit.userEmail); //邮箱
					body.find(".userSex input[value=" + edit.userSex + "]").prop("checked", "checked"); //性别
					body.find(".userGrade").val(edit.userGrade); //会员等级
					body.find(".userStatus").val(edit.userStatus); //用户状态
					body.find(".userDesc").text(edit.userDesc); //用户简介
					form.render();
				}
				setTimeout(function() {
					layui.layer.tips('点击此处返回用户列表', '.layui-layer-setwin .layui-layer-close', {
						tips: 3
					});
				}, 500)
			}
		})
		layui.layer.full(index);
		window.sessionStorage.setItem("index", index);
		//改变窗口大小时，重置弹窗的宽高，防止超出可视区域（如F12调出debug的操作）
		$(window).on("resize", function() {
			layui.layer.full(window.sessionStorage.getItem("index"));
		})
	}
	$(".addNews_btn").click(function() {
		addUser();
	})

	//批量删除
	$(".delAll_btn").click(function() {
		var checkStatus = table.checkStatus('userListTable'),
			data = checkStatus.data,
			newsId = [];
		if (data.length > 0) {
			for (var i in data) {
				newsId.push(data[i].newsId);
			}
			layer.confirm('确定删除选中的用户？', {
				icon: 3,
				title: '提示信息'
			}, function(index) {
				// $.get("删除文章接口",{
				//     newsId : newsId  //将需要删除的newsId作为参数传入
				// },function(data){
				tableIns.reload();
				layer.close(index);
				// })
			})
		} else {
			layer.msg("请选择需要删除的用户");
		}
	})

	//列表操作
	table.on('tool(driverList)', function(obj) {
		var layEvent = obj.event,
			data = obj.data;

		if (layEvent === 'edit') { //编辑
			addUser(data);
		} else if (layEvent === 'usable') { //启用禁用
			var _this = $(this),
				usableText = "是否确定禁用此用户？",
				btnText = "已禁用";
			if (_this.text() == "已禁用") {
				usableText = "是否确定启用此用户？",
					btnText = "已启用";
			}
			layer.confirm(usableText, {
				icon: 3,
				title: '系统提示',
				cancel: function(index) {
					layer.close(index);
				}
			}, function(index) {
				_this.text(btnText);
				layer.close(index);
			}, function(index) {
				layer.close(index);
			});
		} else if (layEvent === 'del') { //删除
			layer.confirm('确定删除此用户？', {
				icon: 3,
				title: '提示信息'
			}, function(index) {
				// $.get("删除文章接口",{
				//     newsId : data.newsId  //将需要删除的newsId作为参数传入
				// },function(data){
				tableIns.reload();
				layer.close(index);
				// })
			});
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
					attend: $('#attend option:selected').val(),
					is_order:$('#is_order option:selected').val(),
					sear_time_s: $("#test1").val(),
					sear_time_e: $("#test2").val()
				}
			})
			layer.close(index); //返回数据关闭loading
			/* } else {
				layer.msg("请输入搜索的内容");
			} */
		}
	});
})
