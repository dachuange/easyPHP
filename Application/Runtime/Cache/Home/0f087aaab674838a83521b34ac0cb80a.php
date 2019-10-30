<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
		<meta content="yes" name="apple-mobile-web-app-capable">
		<meta content="black" name="apple-mobile-web-app-status-bar-style">
		<meta http-equiv="pragma" content="no-cache">
		<meta http-equiv="cache-control" content="no-cache">
		<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="0" />
		<link rel="stylesheet" href="/Public/home/css/complain.css" media="all">
		<link rel="stylesheet" href="/Public/dist/css/mui.min.css" media="all">
		<link rel="stylesheet" href="/Public/home/css/alert.css" media="all">
		<script type="text/javascript" src="/Public/home/js/jquery.min.js"></script>
		<script type="text/javascript" src="/Public/dist/js/mui.min.js"></script>
		<title>投诉</title>
	</head>
	<body>
		<div class="title">请选择原因</div>
		<div style="background:#fff;">
			<?php if(is_array($reson)): $i = 0; $__LIST__ = $reson;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="mui-collapse-content" style="border-bottom: 1px solid #f4f5f6; font-size: 1.0rem;color: #333;padding: 8px 0;">
					<div class="mui-input-row mui-radio">
						<label><?php echo ($vo["text"]); ?></label>
						<input name="complaint_id" type="radio" value="<?php echo ($vo["id"]); ?>">
					</div>
				</div><?php endforeach; endif; else: echo "" ;endif; ?>
		</div>
		<div class="content">
			<div class="title">请具体描述问题</div>
			<div style="background:#fff;">
				<textarea class="textarea" id="textarea" placeholder="请描述具体原因..." maxlength="50" /></textarea>
			</div>
			<div class="wode_out" onclick="tijiao()">提交</div>
		</div>
	</body>
	<script>
		function tijiao() {
			//弹框
			var toast = function(params) {
				var el = document.createElement("div");
				el.setAttribute("id", "toast");
				el.innerHTML = params.message;
				document.body.appendChild(el);
				el.classList.add("fadeIn");
				setTimeout(function() {
					el.classList.remove("fadeIn");
					el.classList.add("fadeOut");
					el.addEventListener("animationend", function() {
						el.classList.add("hide");
					});
				}, params.time);
			};
			//var o_id = localStorage.getItem("o_id");
			//解析URL获取参数
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
			var o_id = theRequest.o_id;
			console.log('o_id', o_id)
			var complaint_id = $("input[name='complaint_id']:checked").val();
			var text = $("#textarea").val()
			$.ajax({
				url: '/index.php/Orderauto/complaint_list_chk',
				type: "POST",
				data: {
					o_id: o_id,
					complaint_id: complaint_id,
					text: text
				},
				dataType: "json",
				success: function(data) {
					console.log('data', data);
					var code = data.error_code;
					console.log('code', code);
					if (code == 0) {
						//alert("投诉成功")
						toast({
							message: "投诉成功",
							time: 1100
						});
						setTimeout(function() {
							window.location.href = "/index.php/User/usercenter"
						}, 1200);
					} else if (code == 1) {
						//alert("投诉失败")
						toast({
							message: "投诉失败",
							time: 1500
						});
					}
				},
			})
		}
	</script>
</html>