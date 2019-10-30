var url=window.globalConfig.api;//接口地址
layui.use(['form', 'layer', 'jquery'], function() {
	var form = layui.form,
		layer = parent.layer === undefined ? layui.layer : top.layer
	$ = layui.jquery;

	$(".loginBody .seraph").click(function() {
		layer.msg("这只是做个样式，至于功能，你见过哪个后台能这样登录的？还是老老实实的找管理员去注册吧", {
			time: 5000
		});
	})

	//登录按钮
	form.on("submit(login)", function(data) {
		var userName = $('#userName').val();
		var password = $('#password').val();
		$(this).text("登录中...").attr("disabled", "disabled").addClass("layui-disabled");
		setTimeout(function() {
			$.ajax({
				url:url+'/admin/Index/login',
				type: "POST",
				data: {
					account: userName,
					password: password
				},
				dataType: "json",
				success: function(data) {
					console.log(data)
					if (data.error_code == 0) {
						var account=data.account;
						var userid=data.id;
						var token=data.token;
						var address_id=data.address_id;
						localStorage.setItem("account",account)
						localStorage.setItem("userid",userid)
						localStorage.setItem("token",token)
						localStorage.setItem("address_id",address_id)
						window.location.href = "/Eadmin";
					} else if (data.error_code == -403) {
						alert('账号密码错误');
					}
				}
			});
		},500);
		return false;
	})

	//表单输入效果
	$(".loginBody .input-item").click(function(e) {
		e.stopPropagation();
		$(this).addClass("layui-input-focus").find(".layui-input").focus();
	})
	$(".loginBody .layui-form-item .layui-input").focus(function() {
		$(this).parent().addClass("layui-input-focus");
	})
	$(".loginBody .layui-form-item .layui-input").blur(function() {
		$(this).parent().removeClass("layui-input-focus");
		if ($(this).val() != '') {
			$(this).parent().addClass("layui-input-active");
		} else {
			$(this).parent().removeClass("layui-input-active");
		}
	})
})
