	// 百度地图API功能
	var map = new BMap.Map("allmap");
	map.centerAndZoom(new BMap.Point(117.318035,39.721973), 14);
	map.enableScrollWheelZoom();
    //ajax开始	
	var url = window.globalConfig.api; //接口地址
	var id = localStorage.getItem("userid");
	var token = localStorage.getItem("token");
	var address_id= localStorage.getItem("address_id");
	$.ajax({
		url: url + '/admin/Analysis/areafeedisplay',
		type: "POST",
		data: {
			id: id,
			token: token,
			address_id:address_id
		},
		dataType: "json",
		success: function(data) {
			console.log('map', data)
			var obj = eval(data);
			//console.log('obj1', obj)
			var path = [];
			//第一个
			var path = obj[1];
			console.log("path", path);
			var polygon = new BMap.Polygon([
				new BMap.Point(path[0].x, path[0].y),
				new BMap.Point(path[1].x, path[1].y),
				new BMap.Point(path[2].x, path[2].y),
				new BMap.Point(path[3].x, path[3].y),
				new BMap.Point(path[4].x, path[4].y)
			], {strokeColor:"blue", strokeWeight:2, strokeOpacity:0.3});  //创建多边形
			map.addOverlay(polygon);   //增加多边形
			
			//第二个
			var path1 = obj[2];
			console.log("path1", path1);
			var polygon1 = new BMap.Polygon([
				new BMap.Point(path1[0].x, path1[0].y),
				new BMap.Point(path1[1].x, path1[1].y),
				new BMap.Point(path1[2].x, path1[2].y),
				new BMap.Point(path1[3].x, path1[3].y),
				new BMap.Point(path1[4].x, path1[4].y)
			],{strokeColor:"blue", strokeWeight:2, strokeOpacity:0.3});  //创建多边形
			map.addOverlay(polygon1);   //增加多边形
		
	        //第三个
	        var path2 = obj[3];
	        console.log("path2", path2);
			var polygon2 = new BMap.Polygon([
	        	new BMap.Point(path2[0].x, path2[0].y),
	        	new BMap.Point(path2[1].x, path2[1].y),
	        	new BMap.Point(path2[2].x, path2[2].y),
	        	new BMap.Point(path2[3].x, path2[3].y),
	        	new BMap.Point(path2[4].x, path2[4].y)
	        ],{strokeColor:"blue", strokeWeight:2, strokeOpacity:0.3});  //创建多边形
			map.addOverlay(polygon2);   //增加多边形
	       
			//第四个
	        var path3 = obj[4];
	        console.log("path3", path3);
	        var polygon3 = new BMap.Polygon([
				new BMap.Point(path3[0].x, path3[0].y),
				new BMap.Point(path3[1].x, path3[1].y),
				new BMap.Point(path3[2].x, path3[2].y),
				new BMap.Point(path3[3].x, path3[3].y),
				new BMap.Point(path3[4].x, path3[4].y)
	        ], {strokeColor:"blue", strokeWeight:2, strokeOpacity:0.3});  //创建多边形
			map.addOverlay(polygon3);   //增加多边形
	        
			 //第五个
			var path4 = obj[5];
			console.log("path4", path4);
			var polygon4 = new BMap.Polygon([
				new BMap.Point(path4[0].x, path4[0].y),
				new BMap.Point(path4[1].x, path4[1].y),
				new BMap.Point(path4[2].x, path4[2].y),
				new BMap.Point(path4[3].x, path4[3].y),
				new BMap.Point(path4[4].x, path4[4].y)
			], {strokeColor:"blue", strokeWeight:2, strokeOpacity:0.3});  //创建多边形
			map.addOverlay(polygon4);   //增加多边形

			 //第六个
			var path5 = obj[6];
			console.log("path5", path5);
			var polygon5 = new BMap.Polygon([
				new BMap.Point(path5[0].x, path5[0].y),
				new BMap.Point(path5[1].x, path5[1].y),
				new BMap.Point(path5[2].x, path5[2].y),
				new BMap.Point(path5[3].x, path5[3].y),
				new BMap.Point(path5[4].x, path5[4].y)
			], {strokeColor:"blue", strokeWeight:2, strokeOpacity:0.3});  //创建多边形
			map.addOverlay(polygon5);   //增加多边形
			
			 //第七个
			var path6 = obj[7];
			console.log("path6", path6);
			var polygon6 = new BMap.Polygon([
				new BMap.Point(path6[0].x, path6[0].y),
				new BMap.Point(path6[1].x, path6[1].y),
				new BMap.Point(path6[2].x, path6[2].y),
				new BMap.Point(path6[3].x, path6[3].y),
				new BMap.Point(path6[4].x, path6[4].y)
			], {strokeColor:"blue", strokeWeight:2, strokeOpacity:0.3});  //创建多边形
			map.addOverlay(polygon6);   //增加多边形
			
			 //第八个
			var path7 = obj[8];
			console.log("path7", path7);
			var polygon7 = new BMap.Polygon([
				new BMap.Point(path7[0].x, path7[0].y),
				new BMap.Point(path7[1].x, path7[1].y),
				new BMap.Point(path7[2].x, path7[2].y),
				new BMap.Point(path7[3].x, path7[3].y),
				new BMap.Point(path7[4].x, path7[4].y)
			], {strokeColor:"blue", strokeWeight:2, strokeOpacity:0.3});  //创建多边形
			map.addOverlay(polygon7);   //增加多边形
			
			 //第九个
			var path8 = obj[9];
			console.log("path8", path8);
			var polygon8 = new BMap.Polygon([
				new BMap.Point(path8[0].x, path8[0].y),
				new BMap.Point(path8[1].x, path8[1].y),
				new BMap.Point(path8[2].x, path8[2].y),
				new BMap.Point(path8[3].x, path8[3].y),
				new BMap.Point(path8[4].x, path8[4].y)
			], {strokeColor:"blue", strokeWeight:2, strokeOpacity:0.3});  //创建多边形
			map.addOverlay(polygon8);   //增加多边形
			
			 //第十个
			var path9 = obj[10];
			console.log("path9", path9);
			var polygon9 = new BMap.Polygon([
				new BMap.Point(path9[0].x, path9[0].y),
				new BMap.Point(path9[1].x, path9[1].y),
				new BMap.Point(path9[2].x, path9[2].y),
				new BMap.Point(path9[3].x, path9[3].y),
				new BMap.Point(path9[4].x, path9[4].y)
			], {strokeColor:"blue", strokeWeight:2, strokeOpacity:0.3});  //创建多边形
			map.addOverlay(polygon9);   //增加多边形
		
			 //第十一个
			var path10 = obj[11];
			console.log("path10", path10);
			var polygon10 = new BMap.Polygon([
				new BMap.Point(path10[0].x, path10[0].y),
				new BMap.Point(path10[1].x, path10[1].y),
				new BMap.Point(path10[2].x, path10[2].y),
				new BMap.Point(path10[3].x, path10[3].y),
				new BMap.Point(path10[4].x, path10[4].y)
			], {strokeColor:"blue", strokeWeight:2, strokeOpacity:0.3});  //创建多边形
			map.addOverlay(polygon10);   //增加多边形
			
			 //第十二个
			var path11 = obj[12];
			console.log("path11", path11);
			var polygon11 = new BMap.Polygon([
				new BMap.Point(path11[0].x, path11[0].y),
				new BMap.Point(path11[1].x, path11[1].y),
				new BMap.Point(path11[2].x, path11[2].y),
				new BMap.Point(path11[3].x, path11[3].y),
				new BMap.Point(path11[4].x, path11[4].y)
			], {strokeColor:"blue", strokeWeight:2, strokeOpacity:0.3});  //创建多边形
			map.addOverlay(polygon11);   //增加多边形
			
			 //第十三个
			var path12 = obj[13];
			console.log("path12", path12);
			var polygon12 = new BMap.Polygon([
				new BMap.Point(path12[0].x, path12[0].y),
				new BMap.Point(path12[1].x, path12[1].y),
				new BMap.Point(path12[2].x, path12[2].y),
				new BMap.Point(path12[3].x, path12[3].y),
				new BMap.Point(path12[4].x, path12[4].y)
			], {strokeColor:"blue", strokeWeight:2, strokeOpacity:0.3});  //创建多边形
			map.addOverlay(polygon12);   //增加多边形
			
			 //第十四个
			var path13 = obj[14];
			console.log("path13", path13);
			var polygon13 = new BMap.Polygon([
				new BMap.Point(path13[0].x, path13[0].y),
				new BMap.Point(path13[1].x, path13[1].y),
				new BMap.Point(path13[2].x, path13[2].y),
				new BMap.Point(path13[3].x, path13[3].y),
				new BMap.Point(path13[4].x, path13[4].y)
			], {strokeColor:"blue", strokeWeight:2, strokeOpacity:0.3});  //创建多边形
			map.addOverlay(polygon13);   //增加多边形
			
			 //第十五个
			var path14 = obj[15];
			console.log("path14", path14);
			var polygon14 = new BMap.Polygon([
				new BMap.Point(path14[0].x, path14[0].y),
				new BMap.Point(path14[1].x, path14[1].y),
				new BMap.Point(path14[2].x, path14[2].y),
				new BMap.Point(path14[3].x, path14[3].y),
				new BMap.Point(path14[4].x, path14[4].y)
			], {strokeColor:"blue", strokeWeight:2, strokeOpacity:0.3});  //创建多边形
			map.addOverlay(polygon14);   //增加多边形
			
			 //第十六个
			/* var path15 = obj[16];
			console.log("path15", path15);
			var polygon15 = new BMap.Polygon([
				new BMap.Point(path15[0].x, path15[0].y),
				new BMap.Point(path15[1].x, path15[1].y),
				new BMap.Point(path15[2].x, path15[2].y),
				new BMap.Point(path15[3].x, path15[3].y),
				new BMap.Point(path15[4].x, path15[4].y)
			], {strokeColor:"blue", strokeWeight:2, strokeOpacity:0.3});  //创建多边形
			map.addOverlay(polygon15);   //增加多边形 */
		},
	})
	
	//单击获取点击的经纬度
	map.addEventListener("click",function(e){
		alert(e.point.lng + "," + e.point.lat);
	});