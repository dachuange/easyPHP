var url = window.globalConfig.api; //接口地址
//获取司机提现金额信息
var id = localStorage.getItem("userid");
var token = localStorage.getItem("token");
var address_id = localStorage.getItem("address_id");
$.ajax({
	url: url + '/admin/Setting/withdrawal_limit_get',
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
		console.log('司机可提现金额', data)
		if (data.error_code == 0) {
			$('#dwithdrawSet').empty();
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
						'<td>银行卡可提现金额设置</td>' +
						'<td><input type="number" step="0.01" class="layui-input cmsName withdraw_set" id="' + address.address_id + '' + num +
						'" value="' + address.withdraw_set + '"></td>' +
						'<td>微信可提现金额设置</td>' +
						'<td><input type="number" step="0.01" class="layui-input cmsName wx_withdraw_set" id="' + address.address_id + '' + wxnum +
						'" value="' + address.wx_withdraw_set + '"></td>' +
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
		var withdraw_set=$(this).find(".withdraw_set").val();
		var wx_withdraw_set=$(this).find(".wx_withdraw_set").val();
		var address_id=$(this).find(".address_id").val();
		setArray.push({withdraw_set:withdraw_set,wx_withdraw_set:wx_withdraw_set,address_id:address_id});
	})
	console.log("setArray",setArray);
	/* 司机提现金额设置 */
	$.ajax({
		url: url + '/admin/Setting/withdrawal_limit_set',
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
