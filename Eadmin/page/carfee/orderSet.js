var url = window.globalConfig.api; //接口地址
//获取派单设置信息
var id = localStorage.getItem("userid");
var token = localStorage.getItem("token");
var address_id = localStorage.getItem("address_id");
$.ajax({
	url: url + '/admin/Setting/assign_driver_get',
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
		console.log('派单设置信息', data)
		if (data.error_code == 0) {
			$('#orderSet').empty();
			var result = data.list
			var size = result.length;
			var num = 'num';
			var wxnum = 'wxnum';
			var address_id = 'address_id';
			if (size > 0) {
				var str = "";
				for (var i = 0; i < size; i++) {
					var address = result[i];
					str += '<div class="quyulist">'+
					    '<div class="diqu">' + address.area_name + '</div>' +
						'<input type="hidden" class="address_id" value="' + address.address_id + '">' +
						'<table class="layui-table mag0">' +
						'<colgroup>' +
						'<col width="25%">' +
						'<col width="25%">' +
						'<col width="25%">' +
						'<col width="25%">' +
						'</colgroup>' +
						'<tbody>' +
						'<tr>' +
						'<td>派单公里数(公里)</td>' +
						'<td><input type="number" step="0.01" class="layui-input cmsName nearby_kilometers" value="' + address.nearby_kilometers + '"></td>' +
						'<td>播报公里数（公里）</td>' +
						'<td><input type="number" step="0.01" class="layui-input cmsName broadcasting_kilometers"  value="' + address.broadcasting_kilometers + '"></td>' +
						'</tr>' +
						'<tr>' +
						'<td>司机可接单时间(秒)</td>' +
						'<td><input type="number" step="0.01" class="layui-input cmsName reservation_time" value="' + address.reservation_time + '"></td>' +
						'<td>播报时间（秒）</td>' +
						'<td><input type="number" step="0.01" class="layui-input cmsName hall_time"  value="' + address.hall_time + '"></td>' +
						'</tr>' +
						'<tr>' +
						'<td>司机拒绝无单时间(秒)</td>' +
						'<td><input type="number" step="0.01" class="layui-input cmsName blacklist_time" value="' + address.blacklist_time + '"></td>' +
						'</tr>' +
						'</tbody>' +
						'</table>'+
						'</div>'
				}
				$('#orderSet').append(str);
				$('#orderSet').show();
			} else {
				$('#orderSet').hide();
			}
		} else if (data.error_code == -101) {
			top.location.href = '../login/login.html';
		} else if (data.error_code == -402) {
			top.location.href = '../login/login.html';
		} else if (code == -1) {
			alert("请输入修改字段")
			location.reload()
		}
	},
})

//司机提现金额设置
function submenu() {
	var setArray = [];
	$(".quyulist").each(function(i){
		var nearby_kilometers=$(this).find(".nearby_kilometers").val();
		var broadcasting_kilometers=$(this).find(".broadcasting_kilometers").val();
		var reservation_time=$(this).find(".reservation_time").val();
		var hall_time=$(this).find(".hall_time").val();
		var blacklist_time=$(this).find(".blacklist_time").val();
		var address_id=$(this).find(".address_id").val();
		setArray.push({nearby_kilometers:nearby_kilometers,broadcasting_kilometers:broadcasting_kilometers,reservation_time:reservation_time,hall_time:hall_time,blacklist_time:blacklist_time,address_id:address_id});
	})
	console.log("setArray",setArray);
	/* 司机提现金额设置 */
	$.ajax({
		url: url + '/admin/Setting/assign_driver_set',
		type: "POST",
		data: {
			id: id,
			token: token,
			address_id: address_id,
			setArray: setArray
		},
		dataType: "json",
		success: function(data) {
			console.log('司机提现金额设置', data)
			var code = data.error_code;
			if (code == 0) {
				alert("设置成功")
				location.reload()
			} else if (code == -1) {
				alert(data.message)
			} else if (code == -402) {
				top.location.href = '../login/login.html';
			} else if (code == -101) {
				top.location.href = '../login/login.html';
			}
		},
	})
}
