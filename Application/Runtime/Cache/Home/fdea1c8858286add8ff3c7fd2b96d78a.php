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
		<link rel="stylesheet" href="/Public/home/css/fenxiang.css" media="all">
		<script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
		<script type="text/javascript" src="/Public/home/js/jquery.min.js"></script>
		<title>分享有礼</title>
		<style>
		</style>
	</head>
	<body>
     <!--红包 遮罩 -->
     <div class="cont" id="cont">
     	<div class="beijing">
			<a href="/index.php/Orderauto/assess_list?o_id=<?php echo ($o_id); ?>">
     		<div class="close">
     			<img src="/Public/home/images/close.png" class="guanbi" />
     		</div>
			</a>
     		<div class="btn" onclick="fenxiang()">
     			<img src="/Public/home/images/fenxiang.png" class="anniu" />
     		</div>
     	</div>
     </div>
	 <!-- 遮罩 -->
	 <div class="zhezhao" id="zhezhao" onclick="zhezhao()">
	 	<img src="/Public/home/images/top.png" class="top" />
	 </div>
	 <input id="appId" name="appId" type="hidden" value="<?php echo ($config["appId"]); ?>">
	 <input id="timestamp" name="timestamp" type="hidden" value="<?php echo ($config["timestamp"]); ?>">
	 <input id="nonceStr" name="nonceStr" type="hidden" value="<?php echo ($config["nonceStr"]); ?>">
	 <input id="signature" name="signature" type="hidden" value="<?php echo ($config["signature"]); ?>">
	 <input id="title" name="timestamp" type="hidden" value="<?php echo ($qrinfo["title"]); ?>">
	 <input id="link" name="nonceStr" type="hidden" value="<?php echo ($qrinfo["link"]); ?>">
	 <input id="imgUrl" name="imgUrl" type="hidden" value="<?php echo ($qrinfo["imgUrl"]); ?>">
	</body>
	<script>
		$('#zhezhao').show();
		function fenxiang() {
			$('#zhezhao').show();
		}
		function zhezhao() {
			$('#zhezhao').hide();
			$('#cont').show();
		}
		/* 微信配置 */
		var appId = $('#appId').val();
		var timestamp = $('#timestamp').val();
		var nonceStr = $('#nonceStr').val();
		var signature = $('#signature').val();
		var title = $('#title').val();
		var link = $('#link').val();
		var imgUrl = $('#imgUrl').val();
		wx.config({
			debug: false, //调试模式   当为tru时，开启调试模式 
			appId: appId,
			timestamp: timestamp.toString(), //签名时间戳
			nonceStr: nonceStr, //生成签名的随机串 
			signature: signature, //签名                    
			jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage'],
			success: function() {
				alert("配置成功")
			},
			fail: function() {
				alert("配置失败")
			}
		});
		wx.ready(function() { //需在用户可能点击分享按钮前就先调用
			//老版
			wx.onMenuShareTimeline({
				title: title, // 分享标题
				link: link, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
				imgUrl: imgUrl, // 分享图标
				success: function() {
					// 设置成功
					//alert(link)
				}
			})
			//分享朋友圈老版
			wx.onMenuShareAppMessage({
				title: title, // 分享标题
				desc: '', // 分享描述
				link: link, // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
				imgUrl: imgUrl, // 分享图标
				type: '', // 分享类型,music、video或link，不填默认为link
				dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
				success: function() {
					// 用户点击了分享后执行的回调函数
				}
			});
		});
	</script>
</html>