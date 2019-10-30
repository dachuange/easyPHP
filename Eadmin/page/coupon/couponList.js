var url = window.globalConfig.api; //接口地址
//获取红包设置信息
var id = localStorage.getItem("userid");
var token = localStorage.getItem("token");
var address_id = localStorage.getItem("address_id");
$.ajax({
	url: url + '/admin/Coup/getcoup',
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
		console.log('红包设置', data)
		if (data.error_code == 0) {
			$('#dwithdrawSet').empty();
			var result = data.list
			var size = result.length;
			var num = 'num';
			var wxnum = 'wxnum';
			var numa = 'numa';
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
						'<td>首扣优惠</td>' +
						'<td><input type="number" step="0.01" class="layui-input cmsName first_amount" id="' + address.address_id + '' + numa +
						'" value="' + address.first_amount + '"></td>' +
						'<td></td>' +
						'<td></td>' +
						'</tr>' +
						'<tr>' +
						'<td>红包优惠</td>' +
						'<td><input type="number" step="0.01" class="layui-input cmsName amount" id="' + address.address_id + '' + num +
						'" value="' + address.amount + '"></td>' +
						'<td>有效期限</td>' +
						'<td><input type="number" class="layui-input cmsName period" id="' + address.address_id + '' + wxnum +
						'" value="' + address.period + '"></td>' +
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

//红包设置
function submenu() {
	var setArray = [];
	$(".quyulist").each(function(i){
		var amount=$(this).find(".amount").val();
		var period=$(this).find(".period").val();
		var first_amount=$(this).find(".first_amount").val();
		var address_id=$(this).find(".address_id").val();
		setArray.push({amount:amount,period:period,first_amount:first_amount,address_id:address_id});
	})
	console.log("setArray",setArray);
	/* 红包设置 */
	$.ajax({
		url: url + '/admin/Coup/setcoup',
		type: "POST",
		data: {
			id: id,
			token: token,
			address_id: address_id,
			setArray: setArray
		},
		dataType: "json",
		success: function(data) {
			console.log('红包设置', data)
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
