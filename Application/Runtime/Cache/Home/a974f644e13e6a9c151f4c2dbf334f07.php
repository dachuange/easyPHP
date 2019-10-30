<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta content="yes" name="apple-mobile-web-app-capable">
		<meta content="black" name="apple-mobile-web-app-status-bar-style">
		<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
		<meta http-equiv="pragma" content="no-cache">
		<meta http-equiv="cache-control" content="no-cache">
		<link rel="stylesheet" href="/Public/home/css/cancel-success.css" media="all">
		<script type="text/javascript" src="/Public/home/js/jquery.min.js"></script>
		<body>
			<div class="kong">
				<image src="/Public/home/images/success.png" class="null"></image>
				<div class="sorry">取消成功</div>
				<div class="wode_out" onclick="toIndex()">返回首页</div>
			</div>
		</body>
		<script>
			function toIndex() {
				window.location.href = "/Index/index"
			}
		</script>
</html>