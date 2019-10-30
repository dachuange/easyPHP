var url = window.globalConfig.api; //接口地址
var id = localStorage.getItem("userid");
var token = localStorage.getItem("token");
//司机详情
function GetRequest(url) {
	// var url = location.search; //获取url中"?"符后的字串 
	var theRequest = {};
	if (url.indexOf("?") != -1) {
		var str = url.substring(url.indexOf("?") + 1);
		console.log(str);
		strs = str.split("&");
		console.log(strs);
		for (var i = 0; i < strs.length; i++) {
			theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
		}
	}
	var d_id = theRequest.d_id;
	localStorage.setItem("driver_id", d_id)
}
//获取当前url
var url1 = window.location.href;
GetRequest(url1);
//时间搜索
$(".search_btn").on("click", function() {
	layui.use(['form', 'layer', 'table', 'laytpl'], function() {
		var form = layui.form,
			layer = parent.layer === undefined ? layui.layer : top.layer,
			$ = layui.jquery,
			laytpl = layui.laytpl,
			table = layui.table;
		var index = layer.load(2); //添加laoding,0-2两种方式
		var d_id = localStorage.getItem("driver_id");
		console.log('driver_id', d_id)
		$.ajax({
			url: url + '/admin/Analysis/driver_onlinetime_detil',
			type: "POST",
			data: {
				id: id,
				token: token,
				d_id: d_id,
				day: $("#test1").val()
			},
			dataType: "json",
			success: function(data) {
				console.log("data", data)
				layer.close(index);    //返回数据关闭loading
				//请求成功时执行该函数内容，result即为服务器返回的json对象
				if (data.error_code == 0) {
					$("#name").html(data.name);
					$("#phone").html(data.phone);
					$("#count").html(data.count);
					var time = [];
					time.push(data.time);
					//图标显示
					var myChart = echarts.init(document.getElementById('Main'));
					// 显示标题，图例和空的坐标轴
					myChart.setOption({
						title: {},
						tooltip: {
							trigger: 'axis', //坐标轴触发提示框，多用于柱状、折线图中
						},
						legend: {
							data: ['时长']
						},
						toolbox: {
							feature: {
								dataView: {
									show: false,
									readOnly: false
								},
								magicType: {
									show: false,
									type: ['bar']
								},
								restore: {
									show: false
								},
								saveAsImage: {
									show: false
								}
							}
						},
						xAxis: {
							type: 'category',
							data: ['0-6点', '6-8点', '8-11点', '11-13点', '13-17点', '17-19点', '19-24点']
						},
						yAxis: {},
						series: [{
							name: '时长',
							type: 'bar',
							itemStyle: {
								normal: {
									color: '#4ad2ff'
								}
							},
							data: data.time
						}]
					});
				} else if (data.error_code == -101) {
					top.location.href = '../login/login.html';
				} else if (data.error_code == -402) {
					top.location.href = '../login/login.html';
				} else if (data.error_code == -1) {
					alert(data.message)
				}
			},
		})
	});
});
//页面加载
$(function() {
	layui.use(['form', 'layer', 'table', 'laytpl'], function() {
		var form = layui.form,
			layer = parent.layer === undefined ? layui.layer : top.layer,
			$ = layui.jquery,
			laytpl = layui.laytpl,
			table = layui.table;
		var index = layer.load(2); //添加laoding,0-2两种方式
		var d_id = localStorage.getItem("driver_id");
		console.log('driver_id', d_id)
		$.ajax({
			url: url + '/admin/Analysis/driver_onlinetime_detil',
			type: "POST",
			data: {
				id: id,
				token: token,
				d_id: d_id,
				day: $("#test1").val()
			},
			dataType: "json",
			success: function(data) {
				console.log("data", data)
				//请求成功时执行该函数内容，result即为服务器返回的json对象
				if (data.error_code == 0) {
					layer.close(index);    //返回数据关闭loading
					$("#name").html(data.name);
					$("#phone").html(data.phone);
					$("#count").html(data.count);
					var time = [];
					time.push(data.time);
					//图标显示
					var myChart = echarts.init(document.getElementById('Main'));
					// 显示标题，图例和空的坐标轴
					myChart.setOption({
						title: {},
						tooltip: {
							trigger: 'axis', //坐标轴触发提示框，多用于柱状、折线图中
						},
						legend: {
							data: ['时长']
						},
						toolbox: {
							feature: {
								dataView: {
									show: false,
									readOnly: false
								},
								magicType: {
									show: false,
									type: ['bar']
								},
								restore: {
									show: false
								},
								saveAsImage: {
									show: false
								}
							}
						},
						xAxis: {
							type: 'category',
					        data: ['0-6点', '6-8点', '8-11点', '11-13点', '13-17点', '17-19点', '19-24点']
						},
						yAxis: {},
						series: [{
							name: '时长',
							type: 'bar',
							itemStyle: {
								normal: {
									color: '#4ad2ff'
								}
							},
							data: data.time
						}]
					});
				} else if (data.error_code == -101) {
					top.location.href = '../login/login.html';
				} else if (data.error_code == -402) {
					top.location.href = '../login/login.html';
				} else if (data.error_code == -1) {
					alert(data.message)
				}
			},
		})
	})
})
