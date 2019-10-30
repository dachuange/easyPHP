var url = window.globalConfig.api; //接口地址
//获取等待奖励金设置信息
var id = localStorage.getItem("userid");
var token = localStorage.getItem("token");
var address_id = localStorage.getItem("address_id");
$.ajax({
	url: url + '/admin/Setting/userreward_get',
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
		console.log('等待奖励设置', data)
		if (data.error_code == 0) {
			$('#dwithdrawSet').empty();
			var result = data.list
			var size = result.length;
			var num = 'num';
			var wxnum = 'wxnum';
			var numa = 'numa';
			var wxnuma = 'wxnuma';
			var address_id = 'address_id';
			if (size > 0) {
				var str = "";
				for (var i = 0; i < size; i++) {
					var address = result[i];
					str +='<div class="quyulist">'+
					   '<div class="diqu">' + address.area_name + '</div>' +
						'<input type="hidden" class="address_id" id="' + address.address_id + '' + address_id + '" value="' + address.address_id + '">' +
						'<table class="layui-table mag0">' +
						'<colgroup>' +
						'<col width="25%">' +
						'<col width="25%">' +
						'<col width="25%">' +
						'<col width="25%">' +
						'</colgroup>' +
						'<tbody>' +
						'<tr>' +
						'<td>免费秒数</td>' +
						'<td><input type="number" step="0.01" class="layui-input cmsName free_time" id="' + address.address_id + '' + num +
						'" value="' + address.free_time + '"></td>' +
						'<td>超时奖励秒数段</td>' +
						'<td><input type="number" step="0.01" class="layui-input cmsName cost_every" id="' + address.address_id + '' + wxnum +
						'" value="' + address.cost_every + '"></td>' +
						'</tr>' +
						'<tr>' +
						'<td>每时段奖励金</td>' +
						'<td><input type="number" step="0.01" class="layui-input cmsName every_amount" id="' + address.address_id + '' + numa +
						'" value="' + address.every_amount + '"></td>' +
						'<td>最高限制奖励金</td>' +
						'<td><input type="number" step="0.01" class="layui-input cmsName limit_amount" id="' + address.address_id + '' + wxnuma +
						'" value="' + address.limit_amount + '"></td>' +
						'</tr>' +
						'</tbody>' +
						'</table>'+
						'<div>'
				}
				$('#dwithdrawSet').append(str);
				$('#dwithdrawSet').show();
			} else {
				$('#dwithdrawSet').hide();
			}
		} else if (data.error_code == -101) {
			top.location.href = '../login/login.html';
		} else if (data.error_code == -402) {
			top.location.href = '../login/login.html';
		}
	},
})

//等待奖励设置
function submenu() {
	var setArray = [];
	$(".quyulist").each(function(i){
		var free_time=$(this).find(".free_time").val();
		var cost_every=$(this).find(".cost_every").val();
		var every_amount=$(this).find(".every_amount").val();
		var limit_amount=$(this).find(".limit_amount").val();
		var address_id=$(this).find(".address_id").val();
		setArray.push({free_time:free_time,cost_every:cost_every,every_amount:every_amount,limit_amount:limit_amount,address_id:address_id});
	})
	console.log("setArray",setArray);
	/* 等待奖励设置 */
	$.ajax({
		url: url + '/admin/Setting/userreward_set',
		type: "POST",
		data: {
			id: id,
			token: token,
			address_id: address_id,
			setArray: setArray
		},
		dataType: "json",
		success: function(data) {
			console.log('等待奖励设置', data)
			var code = data.error_code;
			if (code == 0) {
				alert("设置成功")
				location.reload()
			} else if (code == -402) {
				top.location.href = '../login/login.html';
			} else if (code == -101) {
				top.location.href = '../login/login.html';
			} else if (code == -1) {
				alert(data.message);
			}
		},
	})
}
