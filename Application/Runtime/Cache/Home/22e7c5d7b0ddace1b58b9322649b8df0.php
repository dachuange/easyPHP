<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport"
		content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<meta content="yes" name="apple-mobile-web-app-capable">
	<meta name="format-detection" content="telephone=yes" />
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	<title>服务中</title>
	<link rel="stylesheet" href="/Public/home/css/alert.css" media="all">
	<link rel="stylesheet" href="/Public/home/css/service.css" media="all">
	<script type="text/javascript" src="/Public/home/js/jquery.min.js"></script>
	<style type="text/css">
		* {
			margin: 0px;
			padding: 0px;
		}

		body,
		button,
		input,
		select,
		textarea {
			font: 12px/16px Verdana, Helvetica, Arial, sans-serif;
		}

		#container {
			width: 100%;
			min-height: 820px;
		}

		#up-map-div {
			width: 100%;
			height: 50px;
			position: fixed;
			z-index: 9999;
			bottom: 5%;
		}
	</style>
	<script charset="utf-8" src="https://map.qq.com/api/js?v=2.exp&key=X5JBZ-YS53I-5FFG2-5YZ72-HNS46-CQBBW"></script>
	<script>
		var map,
			directionsService = new qq.maps.DrivingService({
				complete: function (response) {
					var start = response.detail.start,
						end = response.detail.end;
					start_icon = new qq.maps.MarkerImage(
						'/Public/home/images/car1.png',
						//                       new qq.maps.Size(40, 60),
						//                       new qq.maps.Point(0, 0),
					),
						end_icon = new qq.maps.MarkerImage(
							'/Public/home/images/loc1.png',
							// 					  new qq.maps.Size(35, 55),
							//                       new qq.maps.Point(0, 0),
						);
					start_marker && start_marker.setMap(null);
					end_marker && end_marker.setMap(null);
					clearOverlay(route_lines);

					start_marker = new qq.maps.Marker({
						icon: start_icon,
						position: start.latLng,
						map: map,
						zIndex: 1
					});
					end_marker = new qq.maps.Marker({
						icon: end_icon,
						position: end.latLng,
						map: map,
						zIndex: 1
					});
					directions_routes = response.detail.routes;
					var routes_desc = [];
					//所有可选路线方案
					for (var i = 0; i < directions_routes.length; i++) {
						var route = directions_routes[i],
							legs = route;
						//调整地图窗口显示所有路线    
						map.fitBounds(response.detail.bounds);
						var steps = legs.steps;
						route_steps = steps;
						polyline = new qq.maps.Polyline(
							{
								path: route.path,
								strokeColor: '#3893F9',
								strokeWeight: 6,
								map: map
							}
						)
						route_lines.push(polyline);
					}
					//方案文本描述
					var routes = document.getElementById('routes');
					routes.innerHTML = routes_desc.join('<br>');
				}
			}),
			directions_routes,
			directions_placemarks = [],
			directions_labels = [],
			start_marker,
			end_marker,
			route_lines = [],
			step_line,
			route_steps = [];

		function init() {
			map = new qq.maps.Map(document.getElementById("container"), {
				// 地图的中心地理坐标。
				center: new qq.maps.LatLng(39.717490, 117.315690),
				zoom: 15,
				mapTypeControlOptions: {
								//设置控件的地图类型ID，ROADMAP显示普通街道地图，SATELLITE显示卫星图像，HYBRID显示卫星图像上的主要街道透明层
								mapTypeIds: [
								],
								//设置控件位置相对上方中间位置对齐
								position: qq.maps.ControlPosition.TOP_CENTER
							}
			});
			calcRoute();
		}
		function calcRoute() {
			var startweidu = $('#startweidu').val();
			var startjingdu = $('#startjingdu').val();
			var endweidu = $('#endweidu').val();
			var endjingdu = $('#endjingdu').val();
			var policy = 'LEAST_DISTANCE';//最短距离
			route_steps = [];
			directionsService.setLocation("天津");
			directionsService.setPolicy(qq.maps.DrivingPolicy[policy]);
			directionsService.search(new qq.maps.LatLng(startweidu, startjingdu),//中心点
				new qq.maps.LatLng(endweidu, endjingdu));
		}
		//清除地图上的marker
		function clearOverlay(overlays) {
			var overlay;
			while (overlay = overlays.pop()) {
				overlay.setMap(null);
			}
		}
		function renderStep(index) {
			var step = route_steps[index];
			//clear overlays;
			step_line && step_line.setMap(null);
			//draw setp line      
			step_line = new qq.maps.Polyline(
				{
					path: step.path,
					strokeColor: '#ff0000',
					strokeWeight: 6,
					map: map
				}
			)
		}
		//显示路段路标
		function showP() {
			var showPlacemark = document.getElementById('sp');
			if (showPlacemark.checked) {
				for (var i = 0; i < directions_placemarks.length; i++) {
					var placemarks = directions_placemarks[i];
					for (var j = 0; j < placemarks.length; j++) {
						var placemark = placemarks[j];
						var label = new qq.maps.Label({
							map: map,
							position: placemark.latLng,
							content: placemark.name
						});
						directions_labels.push(label);
					}
				}
			} else {
				clearOverlay(directions_labels);
			}
		}
		//获取司机位置
		function driver() {
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
			var o_id = $('#o_id').val();
			var d_id = $('#d_id').val();
			$.ajax({
				url: '/index.php/Orderauto/drive_dynamic',
				type: "POST",
				data: {
					drive_id: d_id,
					orderid: o_id
				},
				dataType: "json",
				async: 'false',
				success: function (data) {
					toast({
						message: "正在运行",
						time: 1500
					});
					console.log('data', data);
					var driverLatitude = data.latitude;
					var driverLongitude = data.longitude;
					$('#startweidu').val(driverLatitude);
					$('#startjingdu').val(driverLongitude);
					$('#distance').html(data.distan);
					$('#amount').html(data.total);
					$('#timer').val(data.start_time);
					calcRoute();//重新画地图
					var psd = data.psd;
					console.log('psd', psd)
					if (psd == 'wait_pay' || psd == 'end') {
						window.location.href = "/Orderauto/order_deatil?o_id=" + o_id
					}
					setTimeout(driver, 4000);
				}
			})
		}
		//计时器
		function two_char(n) {
			return n >= 10 ? n : "0" + n;
		}
		function time_fun() {
			//var sec=0;
			var sec = $('#mytime').html();
			var timer1 = $('#timer1').val(0);
			setInterval(function () {
				var timer1 = $('#timer1').val();//前端计时器
				var timer = $('#timer').val();//后台
				var jianzhi = timer - timer1;
				if (Math.abs(jianzhi) < 5) {

				} else {
					sec = timer;//差值大于5,用后端
				}
				sec++;
				var timer1 = $('#timer1').val(sec);//前端计时器
				var date = new Date(0, 0)
				date.setSeconds(sec);
				var h = date.getHours(), m = date.getMinutes(), s = date.getSeconds();
				//document.getElementById("mytime").innerText = two_char(h) + ":" + two_char(m) + ":" + two_char(s);
				document.getElementById("mytime").innerText = two_char(m) + ":" + two_char(s);
			}, 1000);
		}

		//每隔3000执行一次
		$(function () {
			time_fun();//计时器
			init();//重新画地图
			driver();
		})
		//屏蔽按钮返回键
		$(document).ready(function () {
			if (window.history && window.history.pushState) {
				$(window).on('popstate', function () {
					window.history.pushState('forward', null, '#');
					window.history.forward(1);
				});
			}
			window.history.pushState('forward', null, '#'); //在IE中必须得有这两行
			window.history.forward(1);
		});
	</script>
</head>

<body>
	<div style="height:80px;"></div>
	<?php if(($order["operation"] == 'taxi')): ?><div class='center-position'>
				<div class='taxi'>

						<div style="font-size: 3.8vw">行程时长:</div>
				<div style="font-size: 4.8vw;font-weight:500;
				color:rgba(51,51,51,1);" id="mytime"><?php echo ($order["start_time"]); ?></div>

				</div>
			
			</div><?php endif; ?>
	<div class="total1">
		<div class="total-driver">
			<div class="driver-info">
				<?php if($order['avatar'] != '' ): ?><div class="driver-img">

						<image style="width: 75px;height: 75px;border-radius: 50%;" src="<?php echo ($order["avatar"]); ?>" />
					</div>
					<?php else: ?>
					<div class="driver-img">
						<image style="width: 75px;height: 75px;border-radius: 50%;"
							src="/Public/home/images/driver.png" />
					</div><?php endif; ?>
				<div class="driver-detailed">
					<div class="detailed-name"><?php echo ($order["nickname"]); ?>
						<img class="star" style="width: 12px;height: 12px;display: inline-block;"
							src="/Public/home/images/star.png"></img>
						<div class="orderTotal"><?php echo ($order["point"]); ?></div>
						<div class="orderTotal1"><?php echo ($order["lits"]); ?>单</div>
					</div>
					<div class="detailed-Cartnumber"><?php echo ($order["carnum"]); ?></div>
					<div class="detailed-cart"><?php echo ($order["carcolor"]); ?>-<?php echo ($order["car_type"]); ?></div>
				</div>
			</div>
			<div class="driver-message">
				<!-- <a href="sms:<?php echo ($order["d_phone"]); ?>">
						<div class="mas">
							<image style="width: 35px;height: 35px;" src="/Public/home/images/msm.png" />
						</div>
					</a> -->
				<div class="mas" onclick="reload()">
					<image style="width: 35px;height: 35px;" src="/Public/home/images/shuaxin.png" />
				</div>
				<a href="tel:<?php echo ($order["d_phone"]); ?>">
					<div class="phone">
						<image style="width: 35px;height: 35px;" src="/Public/home/images/phone.png" />
					</div>
				</a>
			</div>
		</div>
		<!-- <div class="total-prompt">服务中</div> -->







		<?php if(($order["operation"] == 'taxi')): else: ?>
			<div class="total-looking">
				<div class="looking-desc">
					<div class="left1">
						<div class="right-text1" id="mytime"><?php echo ($order["start_time"]); ?></div>
					</div>
					<div class="center">

						<div class="right-text1"><span id="distance"><?php echo ($order["distance"]); ?>


							</span>KM </div>
					</div>
					<div class="right1">
						<div class="right-text"><span>¥</span><span id="amount"><?php echo ($order["amount"]); ?></span></div>
					</div>
				</div>
			</div><?php endif; ?>







		<?php if(($order["operation"] == 'taxi')): else: ?>
			<div class="total-looking">
				<div class="looking-desc1">

					<div class="left1">行程时长</div>
					<div class="center">行驶距离</div>
					<div class="right1">价格</div>
				</div>
			</div><?php endif; ?>



	</div>
	<div id="container"></div>
	<!-- <div id="up-map-div">
			<button class="wode_out" onclick="pay()" id="zhifu">支付</button>
		</div> -->
	<div id="routes"></div>

	<input id="startweidu" name="startweidu" type="hidden" value="<?php echo ($order["s_lat"]); ?>" onchange="calcRoute();">
	<input id="startjingdu" name="startjingdu" type="hidden" value="<?php echo ($order["s_lng"]); ?>" onchange="calcRoute();">
	<input id="endweidu" name="endweidu" type="hidden" value="<?php echo ($order["e_lat"]); ?>" onchange="calcRoute();">
	<input id="endjingdu" name="endjingdu" type="hidden" value="<?php echo ($order["e_lng"]); ?>" onchange="calcRoute();">
	<input id="d_id" name="d_id" type="hidden" value="<?php echo ($order["d_id"]); ?>">
	<input id="o_id" name="o_id" type="hidden" value="<?php echo ($order["o_id"]); ?>">
	<input id="timer" name="timer" type="hidden" value="">
	<input id="timer1" name="timer1" type="hidden" value="">
</body>
<script>
	function reload() {
		window.location.reload();
	}
	//解决页面锁屏问题
	document.addEventListener('visibilitychange', function () {
		if (document.visibilityState == 'visible') {
			location.reload(true);
		}
	})
	document.addEventListener('webkitvisibilitychange', function () {
		if (document.webkitVisibilityState == 'visible') {
			location.reload(true);
		}
	})
	document.addEventListener('mozvisibilitychange', function () {
		if (document.mozVisibilityState == 'visible') {
			location.reload(true);
		}
	})
	document.addEventListener('msvisibilitychange', function () {
		if (document.msVisibilityState == 'visible') {
			location.reload(true);
		}
	})
</script>

</html>