var url1=window.globalConfig.api;//接口地址
var id = localStorage.getItem("userid");
var token = localStorage.getItem("token");
layui.use('form', function() {
	var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
	form.render();
});

//司机详情
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
	var d_id = theRequest.d_id;
	console.log('d_id', d_id)
	$.ajax({
		url: url1+'/admin/Driver/driver_deatil',
		type: "POST",
		data: {
			d_id: d_id,
			id:id,
			token:token
		},
		dataType: "json",
		success: function(data) {
			console.log('driverdetail', data)
			if (data.verify == 'Y') {
				$('#verify').val('审核通过');
			} else if (data.verify == 'N') {
				$('#verify').val('审核未通过');
			} else if (data.verify == 'O') {
				$('#verify').val('审核中');
			}
			if(data.reviewed_cn == '审核通过'){
				$('#sub').hide();
			}
			$('#d_id').val(data.d_id);
			$('#name').val(data.name);
			$('#card').val(data.card);
			$('#phone').val(data.phone);
			$('#car_type').val(data.car_type);
			$('#carcolor').val(data.carcolor);
			$('#carnum').val(data.carnum);
			$('#sdate').val(data.sdate);
			$('#maturity_date').val(data.maturity_date);
			$('#reviewed_cn').val(data.reviewed_cn);
			$('#state_cn').val(data.state_cn);
			$('#invite_card').val(data.invite_card);
			$('#idcard').val(data.idcard);
			$('#amount').val(data.amount);
			$('#all_amount').val(data.all_amount);
			$('#urgent_phone').val(data.urgent_phone);
			$('#lits').val(data.lits);
			$('#service_fee_now').val(data.service_fee_now);
			$('#point').val(data.point);
			$('#complaint').val(data.complaint);
			$('#avatar').attr('src', data.avatar);
			$('#card_img_z').attr('src', data.card_img_z);
			$('#card_img_f').attr('src', data.card_img_f);
			$('#xs_img').attr('src', data.xs_img);
			$('#js_img').attr('src', data.js_img);
			$('#insurance_img').attr('src', data.insurance_img);
			$('#car_pe_img').attr('src', data.car_pe_img);
			$('#urgent_img').attr('src', data.urgent_img);
			$('#account_book_img').attr('src', data.account_book_img);
			localStorage.setItem("card_img_z",data.card_img_z)
			localStorage.setItem("card_img_f",data.card_img_f)
			localStorage.setItem("xs_img",data.xs_img)
			localStorage.setItem("js_img",data.js_img)
			localStorage.setItem("insurance_img",data.insurance_img)
			localStorage.setItem("car_pe_img",data.car_pe_img)
			localStorage.setItem("urgent_img",data.urgent_img)
			localStorage.setItem("account_book_img",data.account_book_img)
		}
	});
	//return theRequest;
}
//获取当前url
var url = window.location.href;
GetRequest(url);
//司机入驻审核
function submenu() {
	var d_id=$('#d_id').val();
	var reviewed=$('#state option:selected').val();//选中的值
	/* 审核 */
	$.ajax({
		url: url1+'/admin/Driver/drive_reviewed',
		type: "POST",
		data: {
			id: id,
			token: token,
			d_id: d_id,
			reviewed: reviewed
		},
		dataType: "json",
		success: function(data) {
			console.log('司机入驻审核', data)
			var code = data.error_code;
			if (code == 0) {
				alert("审核成功")
				window.location.href = '../driver/driverList.html';
			} else if (code == -402) {
				top.location.href= '../login/login.html';
			} else if (code == -101) {
				top.location.href= '../login/login.html';
			} else if (code == -403) {
				alert("已经审核过了")
			}
		},
	})
}
