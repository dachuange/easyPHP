<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
		<meta content="yes" name="apple-mobile-web-app-capable">
		<meta content="black" name="apple-mobile-web-app-status-bar-style">
		<meta name="format-detection" content="telephone=yes"/>
		<meta http-equiv="pragma" content="no-cache">
		<meta http-equiv="cache-control" content="no-cache">
		<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="0" />
		<link rel="stylesheet" href="/Public/home/css/wait.css" media="all">
	    <link rel="stylesheet" href="/Public/home/css/alert.css" media="all">
		<script type="text/javascript" src="/Public/home/js/jquery.min.js"></script>
		<title>等待司机</title>
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
				height: 100%;
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
          complete : function(response){
              var start = response.detail.start,
                  end = response.detail.end;
                  start_icon = new qq.maps.MarkerImage(
                    '/Public/home/images/car1.png', 
//                       new qq.maps.Size(40, 60),
//                       new qq.maps.Point(0, 0),
                  ),
                  end_icon = new qq.maps.MarkerImage(
					 '/Public/home/images/loc1.png', 
// 				      new qq.maps.Size(35, 55),
//                       new qq.maps.Point(0, 0),
                  );
              start_marker && start_marker.setMap(null); 
              end_marker && end_marker.setMap(null);
              clearOverlay(route_lines);
                start_marker = new qq.maps.Marker({
                      icon: end_icon,
                      position: start.latLng,
                      map: map,
                      //zIndex:1
                });
                end_marker = new qq.maps.Marker({
					  icon: start_icon,
                      position: end.latLng,
                      map: map,
                      //zIndex:1
                });
               directions_routes = response.detail.routes;
               var routes_desc=[];
               //所有可选路线方案
               for(var i = 0;i < directions_routes.length; i++){
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
               var routes=document.getElementById('routes');
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
        center: new qq.maps.LatLng(39.083172,117.199402),
        zoom: 15,
        });
        calcRoute();
    }
    function calcRoute() {
       var userweidu=$('#userweidu').val();
       var userjingdu=$('#userjingdu').val();
       var sijiweidu=$('#sijiweidu').val();
       var sijijingdu=$('#sijijingdu').val();
	   var policy ='LEAST_DISTANCE';//最短距离
	   route_steps = [];
       directionsService.setLocation("天津");
	   directionsService.setPolicy(qq.maps.DrivingPolicy[policy]);
       directionsService.search(new qq.maps.LatLng(userweidu, userjingdu),//中心点
	   new qq.maps.LatLng(sijiweidu, sijijingdu));
    }
    //清除地图上的marker
    function clearOverlay(overlays){
        var overlay;
        while(overlay = overlays.pop()){
            overlay.setMap(null);
        }
    }
    function renderStep(index){   
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
    function showP(){
        var showPlacemark  = document.getElementById('sp');
        if(showPlacemark.checked){
            for(var i=0;i<directions_placemarks.length;i++){
                var placemarks = directions_placemarks[i];
                for(var j=0;j<placemarks.length;j++){
                    var placemark = placemarks[j];
                    var label = new qq.maps.Label({
                        map: map,
                        position: placemark.latLng,
                        content:placemark.name
                    });
                    directions_labels.push(label);
                }
            }
        }else{
            clearOverlay(directions_labels);
        }
    } 
    //获取司机位置
	function driver(){
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
	var o_id = $('#o_id').val();
	var d_id = $('#d_id').val();
	$.ajax({
		url: '/index.php/Orderauto/drive_dynamic',
		type: "POST",
		  data: {
			drive_id: d_id,
			orderid: o_id,
			address_id:"<?php echo ($order['address_id']); ?>"
		  },
		  dataType: "json",
		  async:'false', 
		  success: function(data) {
			console.log('data',data);
			// toast({
			// 	message: "正在运行",
			// 	time: 1500
			// });
			var driverLatitude = data.latitude;
			var driverLongitude = data.longitude;
			$('#sijiweidu').val(driverLatitude);
			$('#sijijingdu').val(driverLongitude);
			 calcRoute();//重新画地图
			var psd = data.psd;
			console.log('psd',psd)
			var total = data.total;
			var wait_reward=data.wait_reward;
			$('#wait_reward').html(data.wait_reward);
			$('#timer').val(data.start_time);
		    if (psd == 'arrived') {
				window.location.href ="/Orderauto/order_arrived?o_id="+o_id
		    }else if (psd == 'active') {
		    	window.location.href ="/Orderauto/order_active?o_id="+o_id
		    }else if (psd == 'wait_pay' ||psd=='end') {
		    	window.location.href ="/Orderauto/order_deatil?o_id="+o_id
		    }
			setTimeout(driver,4000); 
		  }
		})
	}
	//取消订单
	function cancel(){
		var o_id = $('#o_id').val();
		window.location.href ="/index.php/Orderauto/canecl_order?o_id="+o_id
	}
	
	//计时器
	function two_char(n) {
		return n >= 10 ? n : "0" + n;
	}
	function time_fun() {
		//var sec=0;
		var sec=$('#mytime').html();
		var timer1=$('#timer1').val(0);
		var t1=setInterval(function () {
			var timer1=$('#timer1').val();//前端计时器
			var timer=$('#timer').val();//后台
			var jianzhi=timer-timer1;
			if(Math.abs(jianzhi)<5){
			
			}else{
			  sec=timer;//差值大于5,用后端
			}
			sec++;
			var timer1=$('#timer1').val(sec);//前端计时器
			var date = new Date(0, 0)
			date.setSeconds(sec);
			var h = date.getHours(), m = date.getMinutes(), s = date.getSeconds();
			//document.getElementById("mytime").innerText = two_char(h) + ":" + two_char(m) + ":" + two_char(s);
		    document.getElementById("mytime").innerText = two_char(m) + ":" + two_char(s);
		}, 1000);
		
	}
	//每隔3000执行一次
	$(function() {
		init();//重新画地图
		time_fun();//计时器
		driver(); 
	})
	//屏蔽按钮返回键
	$(document).ready(function() {
		if (window.history && window.history.pushState) {
			$(window).on('popstate', function() {
				window.history.pushState('forward', null, '#');
				window.history.forward(1);
			});
		}
		window.history.pushState('forward', null, '#'); //在IE中必须得有这两行
		window.history.forward(1);
	});
	//解决页面锁屏问题
	document.addEventListener('visibilitychange', function() {
		if (document.visibilityState == 'visible') {
			location.reload(true);
		}
	})
	document.addEventListener('webkitvisibilitychange', function() {
		if (document.webkitVisibilityState == 'visible') {
			location.reload(true);
		}
	})
	document.addEventListener('mozvisibilitychange', function() {
		if (document.mozVisibilityState == 'visible') {
			location.reload(true);
		}
	})
	document.addEventListener('msvisibilitychange', function() {
		if (document.msVisibilityState == 'visible') {
			location.reload(true);
		}
	})
</script>
	</head>
	<body>
		<div class="total1" style="margin-top:5px;">
			<div class="total-driver">
				<div class="driver-info">
						
					<?php if($order['avatar'] != '' ): ?><div class="driver-img">
							<image style="width: 75px;height: 75px;border-radius: 50%;" src="<?php echo ($order["avatar"]); ?>" />
						</div>
						<?php else: ?>
						<div class="driver-img">
							<image style="width: 75px;height: 75px;border-radius: 50%;" src="/Public/home/images/driver.png" />
						</div><?php endif; ?>
					<div class="driver-detailed">
						<div class="detailed-name"><?php echo ($order["nickname"]); ?>
							<img class="star" style="width: 12px;height: 12px;display: inline-block;" src="/Public/home/images/star.png"></img>
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
					<a href="tel:<?php echo ($order["d_phone"]); ?>">
						<div class="phone">
							<image style="width: 35px;height: 35px;" src="/Public/home/images/phone.png" />
						</div>
					</a>
				</div>
			</div>
		  <!-- 司机位置和等待金额-->
			<div class="driverlocal">司机已出发，请您耐心等待...</div>
			<div class="jianglijin">
				<div class="left">
					等待时长<span class="green1" id="mytime"><?php echo ($order["start_time"]); ?></span>s
				</div>
				<div class="right">
					<div class="right-a"><img style="width:100%;float: left;margin-top:-10px;" src="/Public/home/images/money.gif"></img></div>
					<div class="right-b">等待时长<span class="green1">奖励金<span id="wait_reward"></span></span>元</div>
				</div>
			</div>
			<div class="shuoming">
				本奖励金限此订单<span class="green">线上支付</span>使用
			</div>
		</div>
		<!-- end#### -->
		<div style="height:820px">
			<div id="container"></div>
		</div>
		<div id="up-map-div">
			<div class="zong">
				<div class="zuo">
					<a href="tel:110">
						<button class="wode_out1">一键报警</button>
					</a>
				</div>
				<div class="you">
					<button class="wode_out" onclick="cancel()" id="zhifu">取消订单</button>
				</div>
			</div>
		</div>
		<div id="routes"></div>
		<input id="sijijingdu" name="sijijingdu" type="hidden" value="<?php echo ($order["e_lng"]); ?>" onchange="calcRoute();">
		<input id="userweidu" name="userweidu" type="hidden" value="<?php echo ($order["s_lat"]); ?>" onchange="calcRoute();">
		<input id="userjingdu" name="userjingdu" type="hidden" value="<?php echo ($order["s_lng"]); ?>" onchange="calcRoute();">
		<input id="sijiweidu" name="sijiweidu" type="hidden" value="<?php echo ($order["e_lat"]); ?>" onchange="calcRoute();">
		<input id="d_id" name="d_id" type="hidden" value="<?php echo ($order["d_id"]); ?>">
		<input id="o_id" name="o_id" type="hidden" value="<?php echo ($order["o_id"]); ?>">
		<input id="timer" name="timer" type="hidden" value="">
		<input id="timer1" name="timer1" type="hidden" value="">
	</body>
</html>