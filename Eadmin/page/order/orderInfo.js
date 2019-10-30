var url1=window.globalConfig.api;//接口地址
var token = localStorage.getItem("token");
var id = localStorage.getItem("userid");
layui.use('form', function() {
	var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
	form.render();
});

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
	var o_id = theRequest.o_id;
	console.log('o_id', o_id)
			$.ajax({
				url: url1+'/admin/Order/orderdetil',
				type: "POST",
				data: {
					id: id,
					token:token,
					o_id:o_id
				},
				dataType: "json",
				success: function(data) {
					console.log('orderdetail', data)
					$('#o_id').val(data.o_id);
					$('#order_num').val(data.order_num);
					$('#mileage_fee').val(data.mileage_fee);
					$('#duration_fee').val(data.duration_fee);
					$('#amount').val(data.amount);
					$('#sdate').val(data.sdate);
					$('#edate').val(data.edate);
					$('#duration_s').val(data.duration_s);
					$('#saddress').val(data.saddress);
					$('#eaddress').val(data.eaddress);
					$('#distance').val(data.distance);
					$('#warning_cn').val(data.warning_cn);
					$('#state_cn').val(data.state_cn);
					$('#name').val(data.name);
					$('#nickname').val(data.nickname);
					$('#d_phone').val(data.d_phone);
					$('#u_phone').val(data.u_phone);
					$('#source').val(data.source);
					$('#s_coupon_amount').val(data.s_coupon_amount);
					$('#start_fee').val(data.start_fee);
					$('#early_peak').val(data.early_peak);
					$('#late_peak').val(data.late_peak);
					$('#out_town').val(data.out_town);
					$('#edge_town').val(data.edge_town);
					$('#night_driving').val(data.night_driving);
					$('#bad_weather').val(data.bad_weather);
					$('#other').val(data.other);
					$('#user_cannel').val(data.user_cannel);
					$('#admin_cannel').val(data.admin_cannel);
					$('#wait_reward').val(data.wait_reward);
					$('#wait_time').val(data.wait_time);
					$('#pd_distance').val(data.pd_distance);
					var code = data.error_code;
					if (code == -101) {
						top.location.href= '../login/login.html';
					} 
				}
			});
	//return theRequest;
}
//获取当前url
var url = window.location.href;
GetRequest(url);
