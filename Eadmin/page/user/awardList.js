var url = window.globalConfig.api; //接口地址
var limit = window.globalConfig.limit; //每页显示的条数
var pageSize = 20; //每页显示数据条数
var page = 1; //当前页数
var dataLength = 0; //数据总条数
var search = $('#search').val();
var id = localStorage.getItem("userid");
var token = localStorage.getItem("token");
var address_id= localStorage.getItem("address_id");
$.ajax({
	url: url + '/admin/Analysis/user_wait_reward_tj',
	type: "POST",
	data: {
		id: id,
		token: token,
		address_id:address_id,
		sear_time_s: $("#test1").val(),
		sear_time_e: $("#test2").val()
	},
	dataType: "json",
	success: function(data) {
		$("#count").val(data.count);
		$("#offline").val(data.offline);
		$("#nopublic").val(data.nopublic);
	},
})
var windowComeback = document.getElementById('search');
windowComeback.addEventListener('click', function() {
	 var id = localStorage.getItem("userid");
	 var token = localStorage.getItem("token");
	 $.ajax({
	 	url: url + '/admin/Analysis/user_wait_reward_tj',
	 	type: "POST",
	 	data: {
	 		id: id,
	 		token: token,
			address_id:address_id,
	 		sear_time_s: $("#test1").val(),
	 		sear_time_e: $("#test2").val()
	 	},
	 	dataType: "json",
	 	success: function(data) {
	 		$("#count").val(data.count);
	 		$("#offline").val(data.offline);
	 		$("#nopublic").val(data.nopublic);
	 	},
	 })
})
//重置按钮
var windowComeback = document.getElementById('reset');
windowComeback.addEventListener('click', function() {
	 var id = localStorage.getItem("userid");
	 var token = localStorage.getItem("token");
	 $.ajax({
	 	url: url + '/admin/Analysis/user_wait_reward_tj',
	 	type: "POST",
	 	data: {
	 		id: id,
	 		token: token,
			address_id:address_id,
	 		sear_time_s: '',
	 		sear_time_e: ''
	 	},
	 	dataType: "json",
	 	success: function(data) {
	 		$("#count").val(data.count);
	 		$("#offline").val(data.offline);
	 		$("#nopublic").val(data.nopublic);
	 	},
	 })
})
