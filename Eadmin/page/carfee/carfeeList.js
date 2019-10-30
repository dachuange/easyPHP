var url1 = window.globalConfig.api; //接口地址
var id = localStorage.getItem("userid");
var token = localStorage.getItem("token");
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
	var address_id = theRequest.address_id;
	console.log('address_id', address_id)
	var area_name = theRequest.area_name;
	console.log('area_name', area_name)
	//ajax开始	
	//获取车费设置信息
	$.ajax({
		url: url1 + '/Admin/Setting/d_fee_list',
		type: "POST",
		data: {
			id: id,
			token: token,
			address_id: address_id
		},
		dataType: "json",
		success: function(data) {
			var code = data.error_code;
			if (code == -402) {
				top.location.href = '../login/login.html';
			} else if (code == -101) {
				top.location.href = '../login/login.html';
			}
			console.log('车费设置', data)
			var data = data.list;
			$('#start_fee').val(data.start_fee);
			$('#first_mileage').val(data.first_mileage);
			$('#second_mileage').val(data.second_mileage);
			$('#early_peak').val(data.early_peak);
			$('#test4').val(data.early_peak_time_start);
			$('#test5').val(data.early_peak_time_end);
			$('#late_peak').val(data.late_peak);
			$('#test6').val(data.late_peak_time_start);
			$('#test7').val(data.late_peak_time_end);
			$('#out_town').val(data.out_town);
			$('#edge_town').val(data.edge_town);
			$('#edge_town_second').val(data.edge_town_second);
			$('#night_driving_first').val(data.night_driving_first);
			$('#test8').val(data.night_driving_first_time_start);
			$('#test9').val(data.night_driving_first_time_end);
			$('#night_driving_second').val(data.night_driving_second);
			$('#test10').val(data.night_driving_second_time_start);
			$('#test11').val(data.night_driving_second_time_end);
			$('#bad_weather').val(data.bad_weather);
			$('#test12').val(data.out_town_first_time_start);
			$('#test13').val(data.out_town_first_time_end);
			$('#out_town_first_add').val(data.out_town_first_add);
			$('#test14').val(data.out_town_second_time_start);
			$('#test15').val(data.out_town_second_time_end);
			$('#out_town_second_add').val(data.out_town_second_add);
			$('#test16').val(data.out_town_killmeter_time_start);
			$('#test17').val(data.out_town_killmeter_time_end);
			$('#out_town_killmeter').val(data.out_town_killmeter);
			$('#out_town_killmeter_add').val(data.out_town_killmeter_add);
			$('#other').val(data.other);
			$('#othertext').val(data.othertext);
		},
	})
}
//车费设置
function submenu() {
	//获取address_id
	var url = window.location.href;
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
	var address_id = theRequest.address_id;
	console.log('address_id', address_id)
	var area_name = theRequest.area_name;
	console.log('area_name', area_name)
	//提交设置
	var start_fee = $('#start_fee').val();
	var first_mileage = $('#first_mileage').val();
	var second_mileage = $('#second_mileage').val();
	var early_peak = $('#early_peak').val();
	var early_peak_time_start = $('#test4').val();
	var early_peak_time_end = $('#test5').val();
	var late_peak = $('#late_peak').val();
	var late_peak_time_start = $('#test6').val();
	var late_peak_time_end = $('#test7').val();
	var night_driving_first = $('#night_driving_first').val();
	var night_driving_first_time_start = $('#test8').val();
	var night_driving_first_time_end = $('#test9').val();
	var night_driving_second = $('#night_driving_second').val();
	var night_driving_second_time_start = $('#test10').val();
	var night_driving_second_time_end = $('#test11').val();
	var out_town = $('#out_town').val();
	var edge_town = $('#edge_town').val();
	var edge_town_second = $('#edge_town_second').val();
	var bad_weather = $('#bad_weather').val();
	var out_town_first_time_start = $('#test12').val();
	var out_town_first_time_end = $('#test13').val();
	var out_town_first_add = $('#out_town_first_add').val();
	var out_town_second_time_start = $('#test14').val();
	var out_town_second_time_end = $('#test15').val();
	var out_town_second_add = $('#out_town_second_add').val();
	var out_town_killmeter_time_start = $('#test16').val();
	var out_town_killmeter_time_end = $('#test17').val();
	var out_town_killmeter = $('#out_town_killmeter').val();
	var out_town_killmeter_add = $('#out_town_killmeter_add').val();
	var other = $('#other').val();
	var othertext = $('#othertext').val();
	if (start_fee == '') {
		alert('起步价不能为空');
		return false
	}
	if (first_mileage == '') {
		alert('2-5公里，每公里里程费不能为空');
		return false
	}
	if (second_mileage == '') {
		alert('5公里以上，每公里里程费不能为空');
		return false
	}
	if (early_peak == '') {
		alert('早高峰加价不能为空');
		return false
	}
	if (late_peak == '') {
		alert('晚高峰加价不能为空');
		return false
	}
	if (out_town == '') {
		alert('出城加价不能为空');
		return false
	}
	if (edge_town == '') {
		alert('红色边缘加价不能为空');
		return false
	}
	if (bad_weather == '') {
		alert('恶劣天气加价不能为空');
		return false
	}
	if (other == '') {
		alert('其他类型加价不能为空');
		return false
	}
	/* 车费设置 */
	$.ajax({
		url: url1 + '/admin/Setting/d_fee_set',
		type: "POST",
		data: {
			id: id,
			token: token,
			address_id: address_id,
			start_fee: start_fee,
			first_mileage: first_mileage,
			second_mileage: second_mileage,
			early_peak: early_peak,
			early_peak_time_start: early_peak_time_start,
			early_peak_time_end: early_peak_time_end,
			late_peak: late_peak,
			late_peak_time_start: late_peak_time_start,
			late_peak_time_end: late_peak_time_end,
			night_driving_first: night_driving_first,
			night_driving_first_time_start: night_driving_first_time_start,
			night_driving_first_time_end: night_driving_first_time_end,
			night_driving_second: night_driving_second,
			night_driving_second_time_start: night_driving_second_time_start,
			night_driving_second_time_end: night_driving_second_time_end,
			out_town: out_town,
			edge_town: edge_town,
			edge_town_second: edge_town_second,
			bad_weather: bad_weather,
			out_town_first_time_start: out_town_first_time_start,
			out_town_first_time_end: out_town_first_time_end,
			out_town_first_add: out_town_first_add,
			out_town_second_time_start: out_town_second_time_start,
			out_town_second_time_end: out_town_second_time_end,
			out_town_second_add: out_town_second_add,
			out_town_killmeter_time_start: out_town_killmeter_time_start,
			out_town_killmeter_time_end: out_town_killmeter_time_end,
			out_town_killmeter: out_town_killmeter,
			out_town_killmeter_add: out_town_killmeter_add,
			other: other,
			othertext: othertext
		},
		dataType: "json",
		success: function(data) {
			console.log('车费设置', data)
			var code = data.error_code;
			if (code == 0) {
				alert("设置成功")
				location.reload()
			} else if (code == -402) {
				top.location.href = '../login/login.html';
			} else if (code == -101) {
				top.location.href = '../login/login.html';
			} else if (code == -1) {
				alert("请选择您要修改的数值")
				location.reload()
			}
		},
	})
}
