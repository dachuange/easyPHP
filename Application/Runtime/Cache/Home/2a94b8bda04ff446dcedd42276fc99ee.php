<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>e达出行</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="yes" name="apple-mobile-web-app-capable">
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="Expires" content="0" />
	<link rel="stylesheet" href="/Public/home/css/index.css" media="all">
	<link rel="stylesheet" href="/Public/dist/css/mui.min.css" media="all">

	<link rel="stylesheet" href="/Public/home/css/base.css" media="all">

	<link rel="stylesheet" href="/Public/home/css/footer.css" media="all">
	<script type="text/javascript" src="/Public/dist/js/mui.min.js"></script>
	<script type="text/javascript" src="/Public/home/js/jquery.min.js"></script>
	<script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js" type="text/javascript" charset="utf-8"></script>
	<!-- 	<script type="text/javascript" src="https://3gimg.qq.com/lightmap/components/geolocation/geolocation.min.js"></script> -->
	<script charset="utf-8" src="https://map.qq.com/api/js?v=2.exp&key=X5JBZ-YS53I-5FFG2-5YZ72-HNS46-CQBBW"></script>
	<style type="text/css">
		html {
			font-size: 10px
		}

		* {
			margin: 0px;
			padding: 0px;
		}

		body {
			background: #f4f5f6;
			background-size: cover;
			overflow-x: hidden;
			overflow-y: hidden;
			font-family: 'Microsoft YaHei';
		}

		.first-button {
			border-radius: 26px;
			height: 7vh;
			line-height: 7vh;
			text-align: center;
			margin: 3vh 3vw;
			position: absolute;
			bottom: 2vh;
			width: 92vw;
			background: #02CBAB;
			color: #fff;
			font-size: 4.3vw;
		}

		.img-box {
			display: flex;
			justify-content: center;
		}

		.img-box img {
			width: 4.3vw;
			height: 4.3vw;
		}






		#qqMap {
			top: 0;
			width: 100vw;
			height: 100vh;
			position: absolute;
		}

		#map {
			top: 0;
			width: 100vw;
			height: 100vh;
			position: absolute;
		}

		.center-box {
			display: flex;
			justify-content: center;
		}

		.left-box {
			width: 12vw;
			height: 24vw;
			background: #fff;
			position: absolute;
			bottom: 35vh;
			margin-left: 3vw;
			border-radius: 4px;
		}

		.left-box-item {
			margin: 0 2vw;
			padding: 2vw 0;
			display: flex;
			height: 50%;
			flex-direction: column;
			justify-content: space-between;
			align-items: center;
		}

		.input-area {
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			padding-left: 4.8vw;
			background: #fff;
			width: 92vw;
			margin: 3vh 3vw;

			height: 19.5vh;
			box-shadow: 0px 5px 10px 0px rgba(0, 0, 0, 0.18);
			border-radius: 10px;
			position: absolute;
			bottom: 11vh;
		}

		.coupon-tip {
			box-shadow: 0px 5px 10px 0px rgba(0, 0, 0, 0.18);
			font-size: 3.8vw;
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding: 3.6vw;
			margin-top: 3vh;
			position: absolute;
			width: 92vw;
			height: 6vh;
			background: rgba(255, 255, 255, 1);
			border-radius: 4px;
		}

		.black-title {
			margin-left: 2.4vw;
			color: #333;
			line-height: 100%;
			font-weight: 500;

		}

		.gray-text {
			color: #666;
			font-weight: 500;
			font-size: 2.4vw;
			line-height: 2.4vw;
			white-space: nowrap;
		}

		.icon-holder {
			width: 5.5vw;
			height: 4.5vw;
		}

		.icon-holder img {
			width: 5.5vw;
			height: 4.5vw;
		}



		.go-coupon-text {
			line-height: 100%;
			text-align: right;
			font-weight: 500;

		}

		.go-coupon-text a {
			color: rgba(0, 200, 172, 1) !important;

		}

		#up-map-div {
			width: 100%;
			height: 60px;
			position: absolute;
			bottom: 0;
		}
	</style>
	<script>
		$(function () {
			console.log('test')
			//内测的版本
			var check = "<?php echo ($check); ?>";
			if (check == "Y") {
			} else {
				$('#lalala').hide();
				$('#con').html('系统正在内测，新功能稍后上线，敬请期待！')
			}
			//geolocation.getLocation(showPosition, showErr, options);
			//geolocation.watchPosition(showPosition, showErr, options);
		})
		//当前定位
		/* var geolocation = new qq.maps.Geolocation("X5JBZ-YS53I-5FFG2-5YZ72-HNS46-CQBBW", "myapp");
		var positionNum = 0;
		var options = {
			timeout: 8000
		};
		function showPosition(position) {
			var adCode = position.adCode; //邮政编码
			var nation = position.nation; //中国
			var city = position.city; //城市
			var addr = position.addr; //详细地址
			var lat = position.lat; //
			var lng = position.lng; //火星坐标 //TODO 实现业务代码逻辑 
			$('#s_lat').val(lat);
			$('#s_lng').val(lng);
			if(lat!='' && lng!=''){
				init()
			}
		};
		function showErr() {
			//TODO 如果出错了调用此方法 
		}; */
		function init() {
			$('#e_local').val('');
			console.log('初始化')
			checkStatus(true)
			/* 微信配置 */
			// var appId = $('#appId').val();
			var appId = "<?php echo ($config["appId"]); ?>"
			var timestamp = $('#timestamp').val();
			var nonceStr = $('#nonceStr').val();
			var signature = $('#signature').val();
			wx.config({
				debug: false, //调试模式   当为tru时，开启调试模式 
				appId: appId,
				timestamp: timestamp.toString(), //签名时间戳
				nonceStr: nonceStr, //生成签名的随机串 
				signature: signature, //签名                    
				jsApiList: ['openLocation', 'getLocation'],
				success: function (res) {
					if (res.checkResult.getLocation == false) {
						alert('你的微信版本太低，不支持微信JS接口，请升级到最新的微信版本！');
						return;
					}
					alert("配置成功")
				},
				fail: function () {
					alert("配置失败")
				}
			});
			wx.ready(function () {
				wx.getLocation({
					type: "gcj02",
					success: function (res) {
						console.log('wxGetLocation', res)
						latitude = res.latitude;
						longitude = res.longitude;
						document.getElementById("map").innerHTML =
							'<iframe id="mapPage" width="100%" height="100%" frameborder=0 scrolling="no" src="https://apis.map.qq.com/tools/locpicker?search=1&type=1&key=X5JBZ-YS53I-5FFG2-5YZ72-HNS46-CQBBW&referer=myapp&coordtype=5&coord=' +
							latitude + ',' + longitude + '"></iframe>'
						geocoder(latitude, longitude);

						$('#s_lat').val(latitude);
						$('#s_lng').val(longitude);

						//地图显示
						var s_lat = $('#s_lat').val();
						var s_lng = $('#s_lng').val();
						var center = new qq.maps.LatLng(s_lat, s_lng);
						var map = new qq.maps.Map(document.getElementById("qqMap"), {
							center: center,
							zoom: 15,
							zoomControl: false,
							//设置控件的地图类型和位置
							mapTypeControlOptions: {
								//设置控件的地图类型ID，ROADMAP显示普通街道地图，SATELLITE显示卫星图像，HYBRID显示卫星图像上的主要街道透明层
								mapTypeIds: [
								],
								//设置控件位置相对上方中间位置对齐
								position: qq.maps.ControlPosition.TOP_CENTER
							}
						});
						//添加监听事件   获取鼠标单击事件
						qq.maps.event.addListener(map, 'click', function (event) {
							var marker = new qq.maps.Marker({
								position: event.latLng,
								map: map
							});
							qq.maps.event.addListener(map, 'click', function (event) {
								marker.setMap(null);
							});
							var result = event.latLng
							console.log("result", result)
							var lng = result.lng;
							console.log("lng", lng)
							var lat = result.lat;
							console.log("lat", lat)
							/*if(lat!='' && lng!=''){
								$.ajax({
									url: '/index.php/Message/dviver_onsite',
									type: "POST",
									data: {
										lat: lat,
										lng: lng,
										limit:'3000'
									},
									dataType: "json",
									success: function(data) {
										console.log(data)
										if(data.error_code==0){
											var obj = eval(data.list);  
											console.log('obj1',obj)
											for(var i in obj){
											   var latlngs = [new qq.maps.LatLng(obj[i].lat,obj[i].lng)];
											   for (var i = 0; i < latlngs.length; i++) {
														(function(n) {
															var end_icon = new qq.maps.MarkerImage(
																'/Public/home/images/car3.png',
															);
															var marker = new qq.maps.Marker({
																icon: end_icon,
																position: latlngs[n],
																map: map
															});
															qq.maps.event.addListener(marker, 'click', function() {
																infoWin.open();
																infoWin.setContent('<div style="text-align:center;white-space:' +
																	'nowrap;margin:10px;">这是第 ' +
																	n + ' 个标注</div>');
																infoWin.setPosition(latlngs[n]);
															});
														})(i);
												}
											}
										}else{
												layer.open({ content: "获取附近商铺失败", skin: 'msg', time: 2 });
										}
									},
								})
							} */
						});
						//设置marker标记
						var marker = new qq.maps.Marker({
							/* icon:'/Public/home/images/loc1.png', */
							map: map,
							position: center
						});
						var infoWin = new qq.maps.InfoWindow({
							map: map
						});
						/* if(s_lat!='' && s_lng!=''){
							$.ajax({
								url: '/index.php/Message/dviver_onsite',
								type: "POST",
								data: {
									lat: s_lat,
									lng: s_lng,
									limit:'3000'
								},
								dataType: "json",
								success: function(data) {
									console.log(data)
									if(data.error_code==0){
										var obj = eval(data.list);  
										console.log('obj',obj)
										for(var i in obj){
										   var latlngs = [new qq.maps.LatLng(obj[i].lat,obj[i].lng)];
										   for (var i = 0; i < latlngs.length; i++) {
													(function(n) {
														var end_icon = new qq.maps.MarkerImage(
															'/Public/home/images/car3.png',
														);
														var marker = new qq.maps.Marker({
															icon: end_icon,
															position: latlngs[n],
															map: map
														});
														qq.maps.event.addListener(marker, 'click', function() {
															infoWin.open();
															infoWin.setContent('<div style="text-align:center;white-space:' +
																'nowrap;margin:10px;">这是第 ' +
																n + ' 个标注</div>');
															infoWin.setPosition(latlngs[n]);
														});
													})(i);
											}
										}
									}else{
											//layer.open({ content: "获取附近商铺失败", skin: 'msg', time: 2 });
									}
								},
							})
						} */
						//地图显示结束
					},
					cancel: function (res) {
						alert('用户拒绝授权获取地理位置'); //用户拒绝授权
					},
				});
			});
		}


		function geocoder(latitude, longitude) {
			var ll = latitude + "," + longitude;
			$.ajax({
				type: 'get',
				url: 'https://apis.map.qq.com/ws/geocoder/v1',
				dataType: 'jsonp',
				data: {
					key: "X5JBZ-YS53I-5FFG2-5YZ72-HNS46-CQBBW", //开发密钥
					location: ll,
					//位置坐标
					get_poi: "0", //是否返回周边POI列表：1.返回；0不返回(默认)
					//coord_type: "5", //输入的locations的坐标类型,1 火星坐标
					parameter: {
						"scene_type": "tohome",
						"poi_num": 20
					}, //附加控制功能
					output: "jsonp"
				},
				success: function (data, textStatus) {
					console.log("locationData", data)
					var formatted_addresses = data.result.formatted_addresses;
					console.log("formatted_addresses", formatted_addresses);
					var recommend = formatted_addresses.recommend;
					console.log("recommend", recommend);
					if (data.status == 0) {
						var address = data.result.address;
						//$('#s_local').val(address);//显示街道
						$('#s_local').val(recommend); //显示名称
					} else {
						alert("系统错误，请联系管理员！")
					}
				},
				error: function () {
					alert("系统错误，请联系管理员！")
				}
			});
		}
	</script>
</head>

<body onload="init()" id="con">
	<div id="lalala">
		<!-- <input id="appId" name="appId" type="hidden" value="<?php echo ($config["appId"]); ?>"> -->
		<input id="timestamp" name="timestamp" type="hidden" value="<?php echo ($config["timestamp"]); ?>">
		<input id="nonceStr" name="nonceStr" type="hidden" value="<?php echo ($config["nonceStr"]); ?>">
		<input id="signature" name="signature" type="hidden" value="<?php echo ($config["signature"]); ?>">
		<input id="s_lat" name="s_lat" type="hidden" value="">
		<input id="s_lng" name="s_lng" type="hidden" value="">
		<input id="e_lat" name="e_lat" type="hidden" value="">
		<input id="e_lng" name="e_lng" type="hidden" value="">

		<div id="qqMap"></div>
		<div id="map"></div>
		<div id='ui'>
			<div class="center-box">
				<div id='coupon-tip' class="coupon-tip">
					<div style="display: flex;align-items: center;">
						<div class='icon-holder'>

							<img src="/Public/home/images/coupon.png" alt="">
						</div>
						<div class="black-title">您有优惠卷待使用</div>
					</div>
					<div class="go-coupon-text">
						<a href="/User/coupon.html">去查看</a>
					</div>
				</div>

			</div>
			<div class="left-box">

				<a href="/Index/invite_user_check">

					<div class='left-box-item' style="border-bottom: 1px solid #eee">


						<div class="img-box">
							<img src="/Public/home/images/go-gift.png" />
						</div>

						<div class="gray-text">邀请有礼</div>


					</div>

				</a>

				<a href="/User/usercenter">

					<div class='left-box-item'>


						<div class="img-box">
							<img src="/Public/home/images/wode1.png" />
						</div>

						<div class="gray-text">个人中心</div>
					</div>
				</a>
			</div>

			<div class="input-area">
				<div class='map-input-item' style='border-bottom:1px solid #eee;
									'>

					<div class='point' style='background: #FA8C4A;'>

					</div>
					<input type="text" id="s_local" placeholder="正在获取上车地点..." readonly="true" onclick="chooseStart()">
				</div>
				<div class='map-input-item'>

					<div class='point'>
					</div>
					<input type="text" id="e_local" placeholder="终点(选填,建议输入方便司机导航)" onclick="chooseEnd()"
						readonly="true">

				</div>


			</div>
			<div class='first-button' onclick="checkStatus()">
				一键叫车
			</div>

		</div>











		<!--红包 遮罩 -->
		<div class="zhezhao" id="zhezhao">
			<div class="beijing">
				<div class="close" onclick="zhezhao()">
					<img src="/Public/home/images/close.png" class="guanbi" />
				</div>
				<div class="jine">
					¥<?php echo ($login["amount"]); ?>
				</div>
				<div class="btn" onclick="fenxiang()">
					<img src="/Public/home/images/fenxiang.png" class="anniu" />
				</div>
			</div>
		</div>
	</div>




</body>
<script>
	window.addEventListener('message', function (event) {
		// 接收位置信息，用户选择确认位置点后选点组件会触发该事件，回传用户的位置信息
		var loc = event.data;
		if (loc && loc.module == 'locationPicker') {
			console.log('location', loc);
			if (loc != null && loc != undefined) {
				setTimeout(function () {
					$('#ui').show();
					$('#map').hide();
					$('#mapPage').hide();
					var biaoshi = localStorage.getItem("name")
					console.log('biaoshi', biaoshi);
					if (biaoshi == 1) {
						var s_lat = loc.latlng.lat;
						var s_lng = loc.latlng.lng;

						var s_local = loc.poiaddress;
						var poiname = loc.poiname;
						if (poiname == '我的位置') {
							$('#s_local').val(s_local);
						} else {
							$('#s_local').val(poiname);
						}
						$('#s_lat').val(s_lat);
						$('#s_lng').val(s_lng);

					} else if (biaoshi == 2) {
						console.log(loc.latlng)
						var e_lat = loc.latlng.lat;
						var e_lng = loc.latlng.lng;
						var e_local = loc.poiaddress;

						var poiname = loc.poiname;
						if (poiname == '我的位置') {
							$('#e_local').val(e_local);
						} else {
							$('#e_local').val(poiname);
						}
						$('#e_lat').val(e_lat);
						$('#e_lng').val(e_lng);
					}
				}, 300); //延迟5000毫米
			}
		}
	}, false);
	function chooseStart() {
		console.log('起点')
		$('#ui').hide();

		$('#mapPage').attr('src', $('#mapPage').attr('src'));
		localStorage.setItem("name", "1")
		$('#map').show();
		$('#mapPage').show();
	}
	//结束
	function chooseEnd() {
		$('#ui').hide();

		$('#mapPage').attr('src', $('#mapPage').attr('src'));
		localStorage.setItem("name", "2")
		$('#map').show();
		$('#mapPage').show();
		$('#ui').hide();
	}
	function tankuang() {
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
		//$("#jcwy").removeAttr("onclick");
		//建立接单参数
		var s_local = $('#s_local').val();
		if (s_local == '') {
			toast({
				message: '正在定位起点,请稍等~',
				time: 1500
			});
			return false;
			//$("#jcwy").attr("onclick", "tankuang()");
		}
		var s_lat = $('#s_lat').val();
		var s_lng = $('#s_lng').val();
		var e_local = $('#e_local').val();
		var e_lat = $('#e_lat').val();
		var e_lng = $('#e_lng').val();
		var u_id = $('#u_id').val();
		if (s_local == e_local) {
			toast({
				message: '起点和终点不能相同',
				time: 1500
			});
			return false;
		}
		localStorage.setItem("u_id", u_id)
		localStorage.setItem("s_local", s_local)
		localStorage.setItem("s_lat", s_lat)
		localStorage.setItem("s_lng", s_lng)
		localStorage.setItem("e_lat", e_lat)
		localStorage.setItem("e_lng", e_lng)
		localStorage.setItem("e_local", e_local)

		//是否在区域内
		$.ajax({
			url: '/User/addressid_check', //不跨域调取url
			type: 'post',
			data: {
				lat: s_lat,
				lng: s_lng
			},

			success: function (data) {
				console.log('data', data)
				var code = data.error_code;
				if (code == 0) {
					//在区域内
					window.location.href = '/index.php/Index/wait'
				} else if (code == -1) {
					//不在区内
					toast({
						message: data.message,
						time: 1500
					});
				}
			},
		})
	}
	function checkStatus(start = false) {
		console.log('状态检查')
		$.ajax({
			url: '/index.php/Home_V2/pceo', //不跨域调取url
			type: 'post',
			success: function (data) {
				var code = data.message_code;
				var timestap = data.cont ? data.cont.initiation_time_int : 0

				console.log('code', data, code);
				if (code == 0 && start !== true) {
					// var APP = '';
					// var URL = data.link;
					// window.location.href = "/Index/home";
					tankuang()
				} else if (data.message_code == -1) {
					//有预约订单
					window.location.href = "/Index/wait" + '?timestap=' + timestap
				} else if (code == 1) {
					var btnArray = ['进入'];
					mui.confirm('您有未完成的订单，点此进入?', 'e达生活', btnArray, function (e) {
						console.log('checkStatus', e)

						if (e.index == 0) {
							var APP = '';
							var URL = data.link;
							window.location.href = APP + URL;
							return true;
						} else {

						}
					})
				} else if (code == 2) {
					var btnArray = ['进入'];
					mui.confirm('您有未结束的订单，点此进入?', 'e达生活', btnArray, function (e) {
						if (e.index == 0) {
							var APP = '';
							var URL = data.link;
							window.location.href = APP + URL;
							return true;
						} else {

						}
					})
				} else if (code == 3) {
					var btnArray = ['去支付'];
					mui.confirm('您有未支付的订单，点此进入?', 'e达生活', btnArray, function (e) {
						if (e.index == 0) {
							var APP = '';
							var URL = data.link;
							window.location.href = APP + URL;
							return true;
						} else {

						}
					})
				}
			}
		});
	}
	var browserRule = /^.*((iPhone)|(iPad)|(Safari))+.*$/;
	if (browserRule.test(navigator.userAgent)) {
		window.onpageshow = function (event) {
			if (event.persisted) {
				window.location.reload()
			}
		};
	}
</script>
<script type="text/javascript">
	history.pushState(null, null, document.URL);
	window.addEventListener('popstate', function () {
		history.pushState(null, null, document.URL);
	});
	$('#map').hide();


	var login = '<?php echo ($login["login"]); ?>'; //判断是否显示遮罩层
	var showCoupon = '<?php echo ($login["coup"]); ?>'; //判断是否显示遮罩层
	console.log('showCoupon', showCoupon)
	$('#coupon-tip').hide();

	if (showCoupon) {
		$('#coupon-tip').show();

	}

	if (login == 1) {
		$('#zhezhao').show();
		$('#qqMap').hide();
	} else {
		$('#zhezhao').hide();
		$('#qqMap').show();
	}

	function zhezhao() {
		$('#zhezhao').hide();
		$('#qqMap').show();
	}

	function fenxiang() {
		window.location.href = "/index.php/Index/invite_user_check"
	}
</script>

</html>