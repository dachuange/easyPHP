<?php if (!defined('THINK_PATH')) exit();?><html>

<head>
	<title>等待司机接单</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport"
		content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta content="yes" name="apple-mobile-web-app-capable">
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="cache-control" content="no-cache">
	<link rel="stylesheet" href="/Public/home/css/waiting.css" media="all">
	<link rel="stylesheet" href="/Public/home/css/alert.css" media="all">
	<script charset="utf-8" src="https://map.qq.com/api/js?v=2.exp&key=X5JBZ-YS53I-5FFG2-5YZ72-HNS46-CQBBW"></script>
	<script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js" type="text/javascript" charset="utf-8"></script>
	<!-- <script type="text/javascript" src="https://3gimg.qq.com/lightmap/components/geolocation/geolocation.min.js"></script> -->
	<style>
	</style>
</head>

<body>
	<div id="content" class="content">
		<!-- 遮罩 -->
		<div class="zhezhao" id="zhezhao">


			<div class="wait-gif"><img src="/Public/home/images/wait3.gif"></img></div>

			<div style="color: #666666 ;text-align: center;font-size: 18px;">正为您努力呼叫车辆</div>

			<div class="kong">
				<div class='gray-text'>已等待</div>
				<div class="jishi" id="mytime">

					00:00:00</div>
			</div>
			<div class="cancel" onclick="end()">
				<div class='text'>取消叫车</div>

				</button>
			</div>
		</div>
		<script type="text/javascript" src="/Public/home/js/jquery.min.js"></script>
		<script>
			var sec = 0
			var start = '<?php echo $_GET['timestap'];?>';
			var now = new Date().getTime()

			start != 0 && (sec = now / 1000 - start)
			console.log('xxxx', now, start, sec)

			$(function () {
				$('#zhezhao').show();
				time_fun(sec); //计时器
				save(); //叫单
			})
			//叫单
			function save() {
				//弹框
				var toast = function (params) {
					var el = document.createElement("div");
					el.setAttribute("id", "toast");
					el.innerHTML = params.message;
					document.body.appendChild(el);
					el.classList.add("fadeIn");
					setTimeout(function () {
						el.classList.remove("fadeIn");
						el.classList.add("fadeOut");
						el.addEventListener("animationend", function () {
							el.classList.add("hide");
						});
					}, params.time);
				};
				var uid = localStorage.getItem("u_id")
				var s_lat = localStorage.getItem("s_lat")
				var s_lng = localStorage.getItem("s_lng")
				var s_local = localStorage.getItem("s_local")
				var e_lat = localStorage.getItem("e_lat")
				var e_lng = localStorage.getItem("e_lng")
				var e_local = localStorage.getItem("e_local")
				$.ajax({
					url: '/Home_V2/po', //不跨域调取url
					type: 'post',
					data: {
						uid: uid,
						latitude_origin: s_lat,
						longitude_origin: s_lng,
						appellation_origin: s_local,
						latitude_destination: e_lat,
						longitude_destination: e_lng,
						appellation_destination: e_local,
					},
					async: 'false',
					success: function (data) {
						console.log('data', data)
						// toast({
						// 	message: data.message,
						// 	time: 3000
						// });
						if (data.message_code == 0) {
							setInterval(check_order, 6000);
						} else if (data.message_code == -1) {
							//退出到公众号
							var sec = localStorage.getItem("sec")
							//没司机接单跳转到公众号
							if (sec > 120) {
								//window.location.href = "/Index/home";
								WeixinJSBridge.call('closeWindow');
							}
							//循环叫单
							setTimeout(save, 6000);
						}
					}
				});

			}

			function check_order() {
				//弹框
				var toast = function (params) {
					var el = document.createElement("div");
					el.setAttribute("id", "toast");
					el.innerHTML = params.message;
					document.body.appendChild(el);
					el.classList.add("fadeIn");
					setTimeout(function () {
						el.classList.remove("fadeIn");
						el.classList.add("fadeOut");
						el.addEventListener("animationend", function () {
							el.classList.add("hide");
						});
					}, params.time);
				};
				//校验订单
				$.ajax({
					url: '/Home_V2/pceo', //不跨域调取url
					type: 'post',
					data: {

					},
					async: 'false',
					success: function (data) {
						console.log('data', data)
						// toast({
						// 	message: data.message,
						// 	time: 3000
						// });
						if (data.message_code == 0) {
							//啥都没有
							WeixinJSBridge.call('closeWindow');
						} else if (data.message_code == -1) {
							//有预约订单
						} else {
							//有正式单子
							var APP = '';
							var URL = data.link;
							window.location.href = APP + URL;
						}
					},
				})
			}
			//计时器
			function two_char(n) {
				return n >= 10 ? n : "0" + n;
			}

			function time_fun() {
				setInterval(function () {
					sec++;
					var date = new Date(0, 0)
					date.setSeconds(sec);
					localStorage.setItem("sec", sec)
					var h = date.getHours(),
						m = date.getMinutes(),
						s = date.getSeconds();
					document.getElementById("mytime").innerText = two_char(h) + ":" + two_char(m) + ":" + two_char(s);
				}, 1000);
			}
			//取消订单
			function end() {
				//校验订单
				$.ajax({
					url: '/Home_V2/pco', //不跨域调取url
					type: 'post',
					data: {

					},
					async: 'false',
					success: function (data) {
						console.log('data', data)
						if (data.message_code == 0) {
							console.log('message', data.message)
							// window.location.href = "/Index/index";
							var history = window.history;
							history.back();
						} else if (data.message_code == -1) {
							console.log('message', data.message)
						} else {
							console.log('message', data.message)
						}
					},
				})
			}
		</script>
</body>

</html>