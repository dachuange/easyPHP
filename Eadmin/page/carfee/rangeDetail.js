	//获取当前url
	var url = window.location.href;
	GetRequest(url);
	//订单详情
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
		var address_id = theRequest.address_id;
		console.log('address_id', address_id)
		var area_name = theRequest.area_name;
		console.log('area_name', area_name)
		//ajax开始	
		var url1 = window.globalConfig.api; //接口地址
		var id = localStorage.getItem("userid");
		var token = localStorage.getItem("token");
		$.ajax({
			url: url1 + '/admin/Setting/gettaxilimitarea',
			type: "POST",
			data: {
				id: id,
				token: token,
				address_id: address_id
			},
			dataType: "json",
			success: function(data) {
				console.log('map', data)
				var path = eval(data);
				// 百度地图API功能
				var map = new BMap.Map("allmap");
				map.centerAndZoom(new BMap.Point(path[0].x, path[0].y), 12);
				map.enableScrollWheelZoom();
				var polygon = new BMap.Polygon([
					new BMap.Point(path[0].x, path[0].y),
					new BMap.Point(path[1].x, path[1].y),
					new BMap.Point(path[2].x, path[2].y),
					new BMap.Point(path[3].x, path[3].y),
					new BMap.Point(path[4].x, path[4].y)
				], {
					strokeColor: "blue",
					strokeWeight: 2,
					strokeOpacity: 0.3
				}); //创建多边形
				map.addOverlay(polygon); //增加多边形
			}
		})
		//单击获取点击的经纬度
		map.addEventListener("click", function(e) {
			alert(e.point.lng + "," + e.point.lat);
		});
	}
