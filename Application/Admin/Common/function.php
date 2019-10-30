<?php

function get_attribute($id){
	$name = M('cAttribute')->where('id='.$id)->getfield('name');
	return $name;
}
function destroy_cahe($orderid) {
    S("order_fee_{$orderid}",NULL);
}
function getUserCountToday($deviation=0) {  //偏移量
    $date = date("Y-m-d",strtotime("-{$deviation} day"));
    dump($date);
    $count_sql = <<<SQL
        SELECT HOUR(e.time) as Hour,count(*) as Count 
	FROM s_user e 
	WHERE e.sdate = {$date} 
	GROUP BY HOUR(e.time) ORDER BY Hour(e.time)
SQL;
    $count = M('')->where($where)->query($count_sql,true);
    dump($count);
}
function get_udesk_agent_token($udesk_adminmail,$udesk_adminpwd,$udesk_openapi_url,$udesk_agent_url,$udesk_cusmail) {
    $apitoken = get_udesk_openapi_token($udesk_adminmail,$udesk_adminpwd,$udesk_openapi_url);
    $header = array('open_api_token:'.$apitoken,'content_type:application/json');
    $time = NOW_TIME;
    $post = array(
        "email"=>$udesk_adminmail,
        "agent_email"=>$udesk_cusmail,
        "timestamp"=>$time,
        "sign"=>  sha1("{$udesk_adminmail}&{$apitoken}&{$time}"),
    );
    $url = $udesk_agent_url;
    $res = curl_post_setHeader($url, $post, $header);
    $msg = json_decode($res,TRUE);
//    dump($res);
    return $msg['agent_api_token'];
}
function get_udesk_openapi_token($udesk_adminmail,$udesk_adminpwd,$udesk_openapi_url) {

    if(S("udesk_openapi_token")){
        return S("udesk_openapi_token");
    }else{
        $post = array(
            "email"=>$udesk_adminmail,
            "password"=>$udesk_adminpwd
        );
        $post_data = $post;
        $url = $$udesk_openapi_url;
        $res = curl_post($url, $post_data);
        $resarr = json_decode($res,TRUE);
        if($resarr['code']==1000){
            S("udesk_openapi_token",$resarr['open_api_auth_token']);
            return $resarr['open_api_auth_token'];
        }else{
            S("udesk_openapi_token",NULL);
            return NULL;
        }
    }
//    {"code":1000,"open_api_auth_token":"8c45b1c8-5b43-49e1-a293-9694f7789669"}
}





