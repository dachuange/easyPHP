<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<meta content="yes" name="apple-mobile-web-app-capable">
		<meta content="black" name="apple-mobile-web-app-status-bar-style">
		<meta name="format-detection" content="telephone=yes" />
		<meta http-equiv="cache-control" content="no-cache">
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="0" />
		<link rel="stylesheet" href="/Public/home/css/orderinfo_detil.css" media="all">
		<link rel="stylesheet" href="/Public/dist/css/mui.min1.css" media="all">
		<link rel="stylesheet" href="/Public/home/css/alert.css" media="all">
		<title>订单支付</title>
	</head>

	<body>
		<div style="margin-bottom:80px">
			<!-- 司机信息 -->
			<input id="appId" name="appId" type="hidden" value="">
			<input id="nonceStr" name="nonceStr" type="hidden" value="">
			<input id="package" name="package" type="hidden" value="">
			<input id="signType" name="signType" type="hidden" value="">
			<input id="timeStamp" name="timeStamp" type="hidden" value="">
			<input id="paySign" name="paySign" type="hidden" value="">
			<input id="state" name="state" type="hidden" value="<?php echo ($orderdetil["state"]); ?>">
			
			<div class="total1" style="margin-top:5px;">
				<div class="total-driver">
					<div class="driver-info">
						<?php if($orderdetil['avatar'] != '' ): ?><div class="driver-img">
								<image style="width: 75px;height: 75px;border-radius: 50%;" src="<?php echo ($orderdetil["avatar"]); ?>" />
							</div>
							<?php else: ?>
							<div class="driver-img">
								<image style="width: 75px;height: 75px;border-radius: 50%;" src="/Public/home/images/driver.png" />
							</div><?php endif; ?>
						<div class="driver-detailed">
							<div class="detailed-name"><?php echo ($orderdetil["nickname"]); ?>
								<!-- <image class="star" style="width: 18px;height: 18px;display: inline-block;" src="/Public/home/images/star.png">
							<div class="orderTotal">5</div> -->
							</div>
							<div class="detailed-Cartnumber1"><?php echo ($orderdetil["carnum"]); ?></div>
							<div class="detailed-cart"><?php echo ($orderdetil["car_type"]); ?></div>
						</div>
					</div>
					<div class="driver-message">
						<!-- <a href="sms:<?php echo ($orderdetil["phone"]); ?>">
							<div class="mas">
								<image style="width: 35px;height: 35px;" src="/Public/home/images/msm.png" />
							</div>
						</a> -->
						<a href="tel:<?php echo ($orderdetil["phone"]); ?>">
							<div class="phone">
								<image style="width: 35px;height: 35px;" src="/Public/home/images/phone.png" />
							</div>
						</a>
					</div>
				</div>
			</div>

			<div class="recommend">
				<span>订单详情</span>
			</div>
			<div class="orderDetail">
				<div class="money">
					<span class="price"><?php echo ($orderdetil["feeamount"]); ?></span>
					<span clss="danwei">元</span>
					<!-- <span clss="danwei" style="padding-left:25px;">元</span> -->
				</div>
				<div class="detail">
					<div class="left">
						订单状态
					</div>
					<div class="right">
						<?php echo ($orderdetil["state_c"]); ?>
					</div>
				</div>
				<div class="detail">
					<div class="left">
						订单编号
					</div>
					<div class="right">
						<?php echo ($orderdetil["order_num"]); ?>
					</div>
				</div>
				<div class="detail">
					<div class="left1">
						起点
					</div>
					<div class="right1">
						<?php echo ($orderdetil["saddress"]); ?>
					</div>
				</div>

				<div class="detail">
						<div class="left1">
							终点
						</div>
						<div class="right1">
							<?php echo ($orderdetil["eaddress"]); ?>
						</div>
					</div>
				<?php if($orderdetil['operation'] != 'taxi' ): ?><div class="detail">
						<div class="left">
							里程
						</div>
						<div class="right">
							<?php echo ($orderdetil["distance"]); ?>公里
						</div>
					</div>
					<div class="detail">
						<div class="left">
							里程费
						</div>
						<div class="right">
							<?php echo ($orderdetil["mileage_fee"]); ?>元
						</div>
					</div>
					<?php if($orderdetil['duration_fee'] != '0.00' ): ?><div class="detail">
							<div class="left">
								时长费
							</div>
							<div class="right">
								<?php echo ($orderdetil["duration_fee"]); ?>元
							</div>
						</div><?php endif; ?>
					<?php if($orderdetil['start_fee'] != '0.00' ): ?><div class="detail">
								<div class="left">
									起步价
								</div>
								<div class="right">
									<?php echo ($orderdetil["start_fee"]); ?>元
								</div>
							</div><?php endif; endif; ?>
		
			


				
			

				<div class="detail">
					<div class="left">
						时长
					</div>
					<div class="right">
						<?php echo ($orderdetil["duration"]); ?>
					</div>
				</div>
		
		
				<?php if($orderdetil['early_peak'] != '0.00' ): ?><div class="detail">
						<div class="left">
							早高峰加价
						</div>
						<div class="right">
							<?php echo ($orderdetil["early_peak"]); ?>元
						</div>
					</div><?php endif; ?>
				<?php if($orderdetil['late_peak'] != '0.00' ): ?><div class="detail">
						<div class="left">
							晚高峰加价
						</div>
						<div class="right">
							<?php echo ($orderdetil["late_peak"]); ?>元
						</div>
					</div><?php endif; ?>
				<?php if($orderdetil['out_town'] != '0.00' ): ?><div class="detail">
						<div class="left">
							出城加价
						</div>
						<div class="right">
							<?php echo ($orderdetil["out_town"]); ?>元
						</div>
					</div><?php endif; ?>
				<?php if($orderdetil['edge_town'] != '0.00' ): ?><div class="detail">
						<div class="left">
							边缘区加价
						</div>
						<div class="right">
							<?php echo ($orderdetil["edge_town"]); ?>元
						</div>
					</div><?php endif; ?>
				<?php if($orderdetil['night_driving_first'] != '0.00' ): ?><div class="detail">
						<div class="left2">
							夜间行车第一时段加价
						</div>
						<div class="right2">
							<?php echo ($orderdetil["night_driving_first"]); ?>元
						</div>
					</div><?php endif; ?>
				<?php if($orderdetil['night_driving_second'] != '0.00' ): ?><div class="detail">
						<div class="left2">
							夜间行车第二时段加价
						</div>
						<div class="right2">
							<?php echo ($orderdetil["night_driving_second"]); ?>元
						</div>
					</div><?php endif; ?>
				<?php if($orderdetil['bad_weather'] != '0.00' ): ?><div class="detail">
						<div class="left">
							恶劣天气加价
						</div>
						<div class="right">
							<?php echo ($orderdetil["bad_weather"]); ?>元
						</div>
					</div><?php endif; ?>
				<?php if($orderdetil['other'] != '0.00' ): ?><div class="detail">
						<div class="left">
							其他类型加价
						</div>
						<div class="right">
							<?php echo ($orderdetil["other"]); ?>元
						</div>
					</div><?php endif; ?>
				<div class="detail">
					<div class="left">
						总价
					</div>
					<div class="right">
						<?php echo ($orderdetil["amount"]); ?>元
					</div>
				</div>
				<?php if($orderdetil['wait_reward'] != '0.00' ): ?><div class="detail" style="color:#05CEA7;">
						<div class="left">
							等待奖励金
						</div>
						<div class="right">
							<?php echo ($orderdetil["wait_reward"]); ?>元
						</div>
					</div><?php endif; ?>
				<div class="detail" style="color:#05CEA7;" onclick="tankuang();">
					<div class="left">
						选择优惠券
					</div>
					<div class="right">
						-<span id="youhui"><?php echo ($orderdetil["s_coupon_amount"]); ?></span>元
					</div>
				</div>
			</div>
			<div class="wode_out1" onclick="pay()" id="zhifu">支付</div>
			<!-- <div class="wode_out" onclick="tousu()">投诉</div> -->
		</div>
		<input id="state" name="state" type="hidden" value="<?php echo ($orderdetil["state"]); ?>">
		<input id="d_id" name="d_id" type="hidden" value="<?php echo ($orderdetil["d_id"]); ?>">
		<input id="o_id" name="o_id" type="hidden" value="">
		<!-- 遮罩 -->
		<div class="zhezhao" id="couponlist" onclick="close();"></div>
	</body>
	<script type="text/javascript" src="/Public/home/js/jquery.min.js"></script>
	<script type="text/javascript" src="/Public/dist/js/mui.min.js"></script>
	<script type="text/javascript">
		var url = window.location.href;

		//订单详情
		$(function() {
			$('.zhezhao').hide(); //隐藏选择优惠券弹框
			//判断是否显示支付按钮
			var state = $('#state').val();
			console.log(state);
			if (state == "wait_pay") {
				$('#zhifu').show()
			} else {
				$('#zhifu').hide()
			}
			//页面加载时判断订单状态
			GetRequest(url);
			panduan();
		})
		//准备支付
		function setpay() {
			var o_id = $('#o_id').val();
			$.ajax({
				//url:' http://39.98.43.249/User/token',//跨域调取URL
				url: '/index.php/Orderauto/ready_pay', //不跨域调取url
				type: 'post',
				data: {
					o_id: o_id
				},
				dataType: 'json',
				success: function(data) {
					console.log('wechat', data)
					$('#appId').val(data.appId), //公众号名称，由商户传入     
					$('#timeStamp').val(data.timeStamp), //时间戳，自1970年以来的秒数     
					$('#nonceStr').val(data.nonceStr), //随机串     
					$('#package').val(data.package),
					$('#signType').val(data.signType), //微信签名方式：     
					$('#paySign').val(data.paySign) //微信签名 
					//微信支付
					$.ajax({
						url: '/index.php/Index/order_viefy', //不跨域调取url
						type: 'post',
						success: function(data) {
							console.log('data', data)
							var code = data.error_code;
							console.log('code', code);
							if (code == 0) {
								//跳转大红包
								window.location.href = "/Orderauto/payoff_coupondeception?o_id=" + o_id
							} else if (code == 3) {
								//未支付调取支付接口
								if (typeof WeixinJSBridge == "undefined") {
									if (document.addEventListener) {
										document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
									} else if (document.attachEvent) {
										document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
										document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
									}
								} else {
									onBridgeReady();
								}
							}
						}
					});
				},
			})
		}
		//支付
		function onBridgeReady() {
			WeixinJSBridge.invoke(
				'getBrandWCPayRequest', {
					"appId": $('#appId').val(), //公众号名称，由商户传入     
					"timeStamp": $('#timeStamp').val(), //时间戳，自1970年以来的秒数     
					"nonceStr": $('#nonceStr').val(), //随机串     
					"package": $('#package').val(),
					"signType": $('#signType').val(), //微信签名方式：     
					"paySign": $('#paySign').val() //微信签名 
				},
				function(res) {
					if (res.err_msg == "get_brand_wcpay_request:ok") {
						//WeixinJSBridge.call('closeWindow');
						window.location.href = "/Orderauto/payoff_coupondeception?o_id=" + o_id;
					}
				});
		}
		//支付按钮
		function pay() {
			setpay();
		}
		//解析url参数
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
			var o_id = theRequest.o_id;
			console.log('o_id', o_id)
			$('#o_id').val(o_id);
		}
		//获得URL
		
		//跳转投诉页面
		function tousu() {
			var o_id = $('#o_id').val();
			window.location.href = "/index.php/Orderauto/complaint_list?o_id=" + o_id;
		}
		//判断跳转评价页面
		function panduan() {
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
				success: function(data) {
					console.log('data', data);
					var psd = data.psd;
					console.log('psd', psd)
					if (psd == 'end') {
						window.location.href = "/Orderauto/payoff_coupondeception?o_id=" + o_id
					}
					setTimeout(panduan, 3000);
				}
			})
		}
		//选择优惠券列表弹框
		function tankuang() {
			//可选优惠券列表
			var o_id = $('#o_id').val();
			$.ajax({
				url: '/index.php/Ucoupon/getavalibcoup',
				type: "POST",
				data: {
					o_id: o_id
				},
				success: function(data) {
					console.log('addresslist', data)
					if (data.error_code == 0) {
						$('.zhezhao').show();//弹出优惠券弹框
						window.scrollTo(0, 0);
						$('#couponlist').empty();
						var result = data.list
						var size = result.length;
						if (size > 0) {
							var str = "";
							for (var i = 0; i < size; i++) {
								var address = result[i];
								str +=
									'<div class="mui-collapse-content" style="border-bottom: 10px solid #f4f5f6; font-size: 1.0rem;color: #333;padding: 8px 0;" onclick="choose();">' +
									'<div class="mui-input-row mui-radio">' +
									'<label>' +
									'<div class="list">' +
									'<div class="couponlist" style="background:url(/Public/home/images/cbj.png);background-size:100% 100%;">' +
									'<div class="content">' + address.val + '</div>' +
									'<div class="content1">' +
									'<div class="zuo">' +
									'<image src="/Public/home/images/che.png" class="che"></image>' +
									'</div>' +
									'<div class="zhong">' +
									'<span class="small"> ¥</span>' +
									'<span class="big">' + address.amount + '</span>' +
									'</div>' +
									'<div class="you">现金券</div>' +
									'</div>' +
									'</div>' +
									'<div class="content2">' +
									'<div class="leftt">' +
									'<div class="commit">' + address.sdate + '至' + address.edate + '</div>' +
									'</div>' +
									'<div class="rightt">未使用</div>' +
									'</div>' +
									'</div>' +
									'</label>' +
									'<input name="coupon" type="radio" value="' + address.coupid + '" style="margin-top:50px;">' +
									'</div>' +
									'</div>'
							}
							$('#couponlist').append(str);
							$('#couponlist').show();
						} else {
							$('#couponlist').hide();
						}
					} else if(data.error_code ==1){
						//alert(data.message);
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
						toast({
							message: data.message,
							time: 1500
						});
						return false;
					}else if (data.error_code == -101) {
						top.location.href = '../login/login.html';
					} else if (data.error_code == -402) {
						top.location.href = '../login/login.html';
					}
				},
				error: function(data) {
					console.log("errdata", data);
				}
			})
		}
		//优惠券选择结果
		function choose() {
			var o_id = $('#o_id').val();
			var coupid = $("input[name='coupon']:checked").val();
			$.ajax({
				url: '/index.php/Ucoupon/coup_order_select', //不跨域调取url
				type: 'post',
				data: {
					o_id: o_id,
					coupid: coupid
				},
				dataType: 'json',
				success: function(data) {
					console.log('couxuanze', data)
					if (data.error_code == 0) {
	                    location.reload()
						$('.zhezhao').hide();
					}
					if (data.error_code == -1) {
						alert(data.message);
					}
				},
			})
		}
	</script>
</html>