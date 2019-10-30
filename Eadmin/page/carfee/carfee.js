var url = window.globalConfig.api; //接口地址
var id = localStorage.getItem("userid");
var token = localStorage.getItem("token");
var address_id= localStorage.getItem("address_id");
$.ajax({
	url: url + '/admin/Index/getadmin_area',
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
					str += '<li data-address_id=' + address.address_id + ' data-area_name=' + address.area_name + '>' +
						address.area_name + '</li>'
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