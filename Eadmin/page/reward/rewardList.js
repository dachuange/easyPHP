var url = window.globalConfig.api; //接口地址
var id = localStorage.getItem("userid");
var token = localStorage.getItem("token");
var address_id = localStorage.getItem("address_id");
//获取红包设置信息
var id = localStorage.getItem("userid");
$.ajax({
	url: url + '/Admin/Coup/incentive_info',
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
		console.log('奖励金设置', data)
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
					str += '<div class="quyulist">'+
					    '<div class="diqu">' + address.area_name + '</div>' +
						'<input type="hidden"  class="address_id" id="' + address.address_id + '' + address_id + '" value="' + address.address_id + '">' +
						'<table class="layui-table mag0">' +
						'<colgroup>' +
						'<col width="25%">' +
						'<col width="25%">' +
						'<col width="25%">' +
						'<col width="25%">' +
						'</colgroup>' +
						'<tbody>' +
						'<tr>' +
						'<td>月费</td>' +
						'<td><input type="number" step="0.01" class="layui-input cmsName mon_fee" id="' + address.address_id + '' + num +
						'" value="' + address.mon_fee + '"></td>' +
						'<td>邀请司机获得奖励</td>' +
						'<td><input type="number" step="0.01" class="layui-input cmsName invite_driver_reward" id="' + address.address_id + '' + wxnum +
						'" value="' + address.invite_driver_reward + '"></td>' +
						'</tr>' +
						'<tr>' +
						'<td>邀请用户注册获得奖励</td>' +
						'<td><input type="number" step="0.01" class="layui-input cmsName invite_userr_reward" id="' + address.address_id + '' + numa +
						'" value="' + address.invite_userr_reward + '"></td>' +
						'<td>邀请用户关注获得奖励</td>' +
						'<td><input type="number" step="0.01" class="layui-input cmsName invite_attention_reward" id="' + address.address_id + '' + wxnuma +
						'" value="' + address.invite_attention_reward + '"></td>' +
						'</tr>' +
						'</tbody>' +
						'</table>'+
						'</div>'
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

//奖励金设置
function submenu() {
	var setArray = [];
	$(".quyulist").each(function(i){
		var mon_fee=$(this).find(".mon_fee").val();
		var invite_driver_reward=$(this).find(".invite_driver_reward").val();
		var invite_userr_reward=$(this).find(".invite_userr_reward").val();
		var invite_attention_reward=$(this).find(".invite_attention_reward").val();
		var address_id=$(this).find(".address_id").val();
		setArray.push({mon_fee:mon_fee,invite_driver_reward:invite_driver_reward,invite_userr_reward:invite_userr_reward,invite_attention_reward:invite_attention_reward,address_id:address_id});
	})
	console.log("setArray",setArray);
	/* 奖励金设置 */
	$.ajax({
		url: url + '/admin/Coup/driver_incentive',
		type: "POST",
		data: {
			id: id,
			token: token,
			address_id: address_id,
			setArray: setArray
		},
		dataType: "json",
		success: function(data) {
			console.log('奖励金设置', data)
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
				location.reload()
			}
		},
	})
}
