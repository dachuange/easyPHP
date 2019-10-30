<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
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
		<link rel="stylesheet" href="/Public/home/css/alert.css" media="all">
		<link rel="stylesheet" href="/Public/home/css/success.css" media="all">
		<script type="text/javascript" src="/Public/home/js/jquery.min.js"></script>
		<title>支付成功</title>
	</head>
	<body>
		<div class="kong">
			<image src="/Public/home/images/success.png" class="null"></image>
			<div class="sorry">支付成功</div>
		</div>
		<div class="wode_out" id="myBtn" style="display: none;">提交</div>
		<!-- 弹窗 -->
		<div id="myModal" class="modal">
			<!-- 弹窗内容 -->
			<div class="modal-content">
				<div class="close">&times;</div>
				<div class="you" onclick="complain()">投诉</div>
				<div class="starts">
					<ul id="pingStar">
						<li rel="1" title="非常不满,意各方面都很差"></li>
						<li rel="2" title="不满意,比较差"></li>
						<li rel="3" title="一般,需要改善"></li>
						<li rel="4" title="比较满意,但仍可改善"></li>
						<li rel="5" title="非常满意,无可挑剔"></li>
					</ul>
					<div id="dir" class="wenzi"></div>
					<input type="hidden" value="" id="startP">
					<input type="hidden" value="" id="point">
				</div>
				<div class="center">
					<div class="yuanyin" id="yuanyin">
						<ul style="width:100%;height:140px;" class="tab">
							<?php if(is_array($reson)): $i = 0; $__LIST__ = $reson;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li data-value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["text"]); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
						</ul>
					</div>
				</div>
				<div class="wode_out1" onclick="tijiao()">提交</div>
			</div>
		</div>
		<script>
			window.onload = function() {
				var s = document.getElementById("pingStar"),
					m = document.getElementById('dir'),
					n = s.getElementsByTagName("li"),
					input = document.getElementById('startP'); //保存所选值
				clearAll = function() {
					for (var i = 0; i < n.length; i++) {
						n[i].className = '';
					}
				}
				for (var i = 0; i < n.length; i++) {
					n[i].onclick = function() {
						var q = this.getAttribute("rel");
						clearAll();
						input.value = q;
						for (var i = 0; i < q; i++) {
							n[i].className = 'on';
						}
						m.innerHTML = this.getAttribute("title");
						$('#point').val(q);
					}
					n[i].onmouseover = function() {
						var q = this.getAttribute("rel");
						clearAll();
						for (var i = 0; i < q; i++) {
							n[i].className = 'on';
						}
					}
					n[i].onmouseout = function() {
						clearAll();
						for (var i = 0; i < input.value; i++) {
							n[i].className = 'on';
						}
					}
				}
			}
			$('.tab').on('click', 'li', function() {
				//$(this).addClass("on1").siblings().removeClass("on1");
				$(this).siblings('li').removeClass('on1'); // 删除其他li的边框样式
				$(this).addClass('on1'); // 为当前li添加边框样式
				var type = $(this).data("value");
				console.log(type);
				localStorage.setItem("assess_id", type)
			});
		</script>
		<script>			
			function tijiao() {
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
				var o_id = "<?php echo ($o_id); ?>";
				var point = $('#point').val();
				var assess_id = localStorage.getItem('assess_id')
				$.ajax({
					url: '/index.php/Orderauto/assess_list_chk',
					type: "POST",
					data: {
						o_id: o_id,
						assess_id: assess_id,
						point: point
					},
					dataType: "json",
					success: function(data) {
						console.log('data', data);
						var code = data.error_code;
						console.log('code', code);
						if (code == 0) {
							//alert("评价成功")
							toast({
								message: "评价成功",
								time: 1100
							});
							setTimeout(function() {
								window.location.href = "/Index/invite_user"
							}, 1200);
						} else if (code == 1) {
							//alert("评价失败")
							toast({
								message: data.message,
								time: 1500
							});
						}
					},
				})
			}

			function complain() {
				var o_id = "<?php echo ($o_id); ?>";
				window.location.href = "/index.php/Orderauto/complaint_list?o_id="+o_id;
			}
		</script>
		<script>
			setTimeout(function() {
				$('#myModal').show();
			}, 1000);
			// 获取弹窗
			var modal = document.getElementById('myModal');

			// 打开弹窗的按钮对象
			var btn = document.getElementById("myBtn");

			// 获取 <span> 元素，用于关闭弹窗
			var span = document.querySelector('.close');

			// 点击按钮打开弹窗
			btn.onclick = function() {
				modal.style.display = "block";
			}

			// 点击 <span> (x), 关闭弹窗
			span.onclick = function() {
				modal.style.display = "none";
			}

			// 在用户点击其他地方时，关闭弹窗
			window.onclick = function(event) {
				if (event.target == modal) {
					modal.style.display = "none";
				}
			}
		</script>
	</body>
</html>