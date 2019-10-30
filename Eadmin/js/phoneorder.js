//常用地址列表
var id = localStorage.getItem("userid");
var token = localStorage.getItem("token");
var url = window.globalConfig.api; //接口地址
$.ajax({
	url: url + '/admin/Order/address_view_list',
	type: "POST",
	data: {
		id: id,
		token: token
	},
	dataType: "json",
	success: function(data) {
		console.log('addresslist', data)
		if (data.error_code == 0) {
			$('#addressList').empty();
			var result = data.list
			var size = result.length;
			if (size > 0) {
				var str = "";
				for (var i = 0; i < size; i++) {
					var address = result[i];
					str += '<li data-viewid=' + address.viewid + ' data-lng=' + address.lng + ' data-lat=' + address.lat + '>' +
						address.view + '</li>'
				}
				$('#addressList').append(str);
				$('#addressList').show();
			} else {
				$('#addressList').hide();
			}
		} else if (data.error_code == -101) {
			top.location.href = '../login/login.html';
		} else if (data.error_code == -402) {
			top.location.href = '../login/login.html';
		}
	},
})
//删除常用地址
function delPlace() {
	if (confirm('确定要删除吗') == true) {
		var id = localStorage.getItem("userid");
		var token = localStorage.getItem("token");
		var viewid = localStorage.getItem("viewid");
		var url = window.globalConfig.api; //接口地址
		if (viewid == undefined) {
			alert("请选择要删除的常用地址")
		}
		$.ajax({
			url: url + '/admin/Order/del_address_view',
			type: "POST",
			data: {
				id: id,
				token: token,
				viewid: viewid
			},
			dataType: "json",
			success: function(data) {
				console.log(data)
				if (data.error_code == 0) {
					alert("常用地址删除成功~")
					localStorage.removeItem("viewid"); //移除所有的缓存数据
					localStorage.removeItem("lng"); //移除所有的缓存数据
					localStorage.removeItem("lat"); //移除所有的缓存数据
					window.location.reload();
				} else if (data.error_code == -101) {
					top.location.href = '../login/login.html';
				} else if (data.error_code == -402) {
					top.location.href = '../login/login.html';
				}
			},
		})
		return true;
	} else {
		return false;
	}
}
