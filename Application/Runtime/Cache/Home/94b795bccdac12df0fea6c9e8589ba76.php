<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta content="yes" name="apple-mobile-web-app-capable">
		<meta content="black" name="apple-mobile-web-app-status-bar-style">
		<meta http-equiv="pragma" content="no-cache">
		<meta http-equiv="cache-control" content="no-cache">
		<link rel="stylesheet" href="/Public/home/css/cancel.css" media="all">
		<link rel="stylesheet" href="/Public/dist/css/mui.min.css" media="all">
		<link rel="stylesheet" href="/Public/home/css/alert.css" media="all">
		<script type="text/javascript" src="/Public/home/js/jquery.min.js"></script>
		<script type="text/javascript" src="/Public/dist/js/mui.min.js"></script>
		<script type="text/javascript">
		</script>
		<title>取消订单</title>
	</head>
	<body>
		<div class="details">
			<text class="details-titlet">你为什么取消？</text>
			<text class="details-titlef">请告知我们，我们可以改善</text>
		</div>
		<ul class="mui-table-view" style="color:#666;">
			<?php if(is_array($reson)): $i = 0; $__LIST__ = $reson;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="mui-table-view-cell mui-collapse">
					<a class="mui-navigate-right" href="#" style="border-bottom: 1px solid #f4f5f6;padding:18px 20px;background: #fff;"><?php echo ($vo["text"]); ?></a>
					<?php if(is_array($vo["cont"])): $i = 0; $__LIST__ = $vo["cont"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cont): $mod = ($i % 2 );++$i;?><div class="mui-collapse-content" style="border-bottom: 1px solid #f4f5f6; padding:5px 10px;">
							<!-- <p><?php echo ($cont["text"]); ?></p> -->
							<div class="mui-input-row mui-radio">
								<label><?php echo ($cont["text"]); ?></label>
								<input name="r_id" type="radio" value="<?php echo ($cont["id"]); ?>">
							</div>
						</div><?php endforeach; endif; else: echo "" ;endif; ?>
				</li><?php endforeach; endif; else: echo "" ;endif; ?>
		</ul>
		<div class="wode_out" onclick="tijiao()">确定</div>
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
			var r_id =$("input[name='r_id']:checked").val();
// 			if(r_id==undefined){
// 				//alert("请选择取消订单原因");
// 				toast({
// 					message: "请选择取消订单原因",
// 					time: 1500
// 				});
// 				return false;
// 			}
			$.ajax({
				url: '/index.php/Orderauto/canecl_order_chk',
				type: "POST",
				async:'false', 
				data: {
					r_id: r_id,
					o_id: o_id
				},
				dataType: "json",
				success: function(data) {
					console.log('data', data);
					var code = data.error_code;
					console.log('code', code);
					if (code == 0) {
						//alert("取消成功")
						toast({
							message: "取消成功",
							time: 1100
						});
						setTimeout(function() {
							window.location.href ="/index.php/Orderauto/cencel_success"
						}, 1200);
					} else if (code == 1) {
						//alert("该订单不能取消")
						toast({
							message: "该订单不能取消",
							time: 1500
						});
					}
				},
			})
		}
	</script>
</html>