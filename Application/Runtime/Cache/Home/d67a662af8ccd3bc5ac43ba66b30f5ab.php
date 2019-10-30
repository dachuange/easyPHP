<?php if (!defined('THINK_PATH')) exit();?><html>
	<head>
		<title>个人中心</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
		<meta content="yes" name="apple-mobile-web-app-capable">
		<meta content="black" name="apple-mobile-web-app-status-bar-style">
		<meta http-equiv="pragma" content="no-cache">
		<meta http-equiv="cache-control" content="no-cache">
		<link rel="stylesheet" href="/Public/home/css/my.css" media="all">
		<script type="text/javascript" src="/Public/home/js/jquery.min.js"></script>
		<style>
		</style>
	</head>
	<body>
		<div class="personal_info">
			<a href="/index.php/User/usercenter_detil">
				<div class="photo_wrap">
					<?php if($user['headimgurl'] != '' ): ?><div class="left">
							<image src="<?php echo ($user["headimgurl"]); ?>" class="photo"></image>
						</div>
						<?php else: ?>
						<div class="driver-img">
							<image src="/Public/home/images/driver.png" class="photo" />
							</image>
						</div><?php endif; ?>
					<div class="right">
						<div class="nickname">
							<?php echo ($user["nickname"]); ?>
						</div>
						<div class="mobile"><?php echo ($user["phone"]); ?></div>
					</div>
				</div>
			</a>
		</div>
		<a href="/index.php/Orderauto/order_list">
			<div class="weui_cell">
				<div class="weui_cell_hd">
					<image src="/Public/home/images/order.png"></image>
				</div>
				<div class="weui_cell_bd">
					<div class="weui_cell_bd_p">我的订单</div>
				</div>
				<div>
					<image src="/Public/home/images/icon-arrow.png" class="with_arrow"></image>
				</div>
			</div>
		</a>
		<a href="/index.php/User/coupon">
			<div class="weui_cell">
				<div class="weui_cell_hd">
					<image src="/Public/home/images/youhuiquan.png"></image>
				</div>
				<div class="weui_cell_bd">
					<div class="weui_cell_bd_p">优惠券</div>
				</div>
				<div>
					<image src="/Public/home/images/icon-arrow.png" class="with_arrow"></image>
				</div>
			</div>
		</a>
		<a href="/index.php/Index/user_protocol">
			<div class="weui_cell">
				<div class="weui_cell_hd">
					<image src="/Public/home/images/xiaoxi.png"></image>
				</div>
				<div class="weui_cell_bd">
					<div class="weui_cell_bd_p">服务协议</div>
				</div>
				<div>
					<image src="/Public/home/images/icon-arrow.png" class="with_arrow"></image>
				</div>
			</div>
		</a>
<!--		<a href="/index.php/Index/pay_info">
			<div class="weui_cell" style="margin-top:10px;">
				<div class="weui_cell_hd">
					<image src="/Public/home/images/jijiashuoming.png"></image>
				</div>
				<div class="weui_cell_bd">
					<div class="weui_cell_bd_p">计价规则</div>
				</div>
				<div>
					<image src="/Public/home/images/icon-arrow.png" class="with_arrow"></image>
				</div>
			</div>
		</a>-->
		<a href="/index.php/User/bind_invite_code">
			<div class="weui_cell" id="yaoqing">
				<div class="weui_cell_hd">
					<image src="/Public/home/images/xinxin.png" style="width:100%;"></image>
				</div>
				<div class="weui_cell_bd">
					<div class="weui_cell_bd_p">输入邀请码</div>
				</div>
				<div>
					<image src="/Public/home/images/icon-arrow.png" class="with_arrow"></image>
				</div>
			</div>
		</a>
		<a href="tel:022-29228262">
			<div class="weui_cell" bindtap='tel'>
				<div class="weui_cell_hd">
					<image src="/Public/home/images/dianhua.png" style="width:100%;"></image>
				</div>
				<div class="weui_cell_bd">
					<div class="weui_cell_bd_p">客服电话</div>
				</div>
				<div class="with_arrow1">
					022-29228262
				</div>
			</div>
		</a>
		<!-- <div class="wode_out" onclick="loginout()">退出</div> -->
		<input id="invit_code" name="invit_code" type="hidden" value="<?php echo ($user["invit_code"]); ?>">
	</body>
	<script>
		function loginout() {
			window.location.href = "/index.php/User/logout"
		}
		var invit_code = $('#invit_code').val();
		if (invit_code == 'Y') {
			$('#yaoqing').show();
		} else {
			$('#yaoqing').hide();
		}
	</script>
</htmL>