<?php

/* 
 * 现有所有函数皆为自建工具函数
 * Commom下的function，只允许存放工具类公共函数
 * and open the template in the editor.
 */
function encrypt($pw, $secureKey) {   //加密
	// You need to implement
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB); 
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND); 
	$pw = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $secureKey, $pw, MCRYPT_MODE_ECB, $iv);
	$pw = base64_encode($pw);
	return $pw;
}
function decrypt($pw, $secureKey) {  //解密
	// You need to implement
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB); 
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	$pw = base64_decode($pw);
	$pw = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $secureKey, $pw, MCRYPT_MODE_ECB, $iv);
	return trim($pw);
}
function curl_get($url) {   //能够调用HTTPS的 CURL模板
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);  
    curl_setopt($ch, CURLOPT_HEADER, 0);  
//    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
    curl_setopt($ch, CURL_SSLVERSION_SSL, 2);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return $file_contents;
    
}
function curl_post($url,$post_data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);  
    curl_setopt($ch, CURLOPT_HEADER, 0);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
    curl_setopt($ch, CURL_SSLVERSION_SSL, 2);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return $file_contents;
}
function curl_post_setHeader($url,$post_data,$header) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);  
    curl_setopt($ch, CURLOPT_HEADER, 0);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
    curl_setopt($ch, CURL_SSLVERSION_SSL, 2);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return $file_contents;
}
function curl_post_certificate($url,$post_data,$cert) {   //带有证书检查的   $cert 为文件夹前缀
    //return dirname(__FILE__).'/cert/apiclient_cert.pem';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);  
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
    curl_setopt($ch,CURLOPT_SSLCERTTYPE,'pem');
    curl_setopt($ch,CURLOPT_SSLCERT,'/phpstudy/www/app/Application/Common/Common/'.$cert.'cert/apiclient_cert.pem');
    curl_setopt($ch,CURLOPT_SSLCERTTYPE,'pem');
    curl_setopt($ch,CURLOPT_SSLKEY,'/phpstudy/www/app/Application/Common/Common/'.$cert.'cert/apiclient_key.pem');
    curl_setopt($ch,CURLOPT_SSLCERTTYPE,'pem');
    curl_setopt($ch,CURLOPT_CAINFO,'/phpstudy/www/app/Application/Common/Common/'.$cert.'cert/rootca.pem');

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return $file_contents;
}
function arrayToXml($arr){
    $xml = "<root>"; 
    foreach ($arr as $key=>$val){ 
    if(is_array($val)){ 
    $xml.="<".$key.">".arrayToXml($val)."</".$key.">"; 
    }else{ 
    $xml.="<".$key.">".$val."</".$key.">"; 
    } 
    } 
    $xml.="</root>"; 
return $xml; 
}
function xmlToArray($xml){
 
 //禁止引用外部xml实体 
 
libxml_disable_entity_loader(true); 
 
$xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA); 
 
$val = json_decode(json_encode($xmlstring),true); 
 
return $val; 
 
}
function objectToArray($e){
  $e=(array)$e;
  foreach($e as $k=>$v){
    if( gettype($v)=='resource' ) return;
    if( gettype($v)=='object' || gettype($v)=='array' )
      $e[$k]=(array)objectToArray($v);
  }
  return $e;
}
function generate_num($length) {
// 密码字符集，可任意添加你需要的字符  
$chars ="0123456789";  
$password = "";  
for ( $i = 0; $i < $length; $i++ )  {  
// 这里提供两种字符获取方式  
// 第一种是使用 substr 截取$chars中的任意一位字符；  
// 第二种是取字符数组 $chars 的任意元素  
// $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);  
$password .= $chars[ mt_rand(0, strlen($chars) - 1) ];  
}
return $password;  
}
function generate_password($length) {
// 密码字符集，可任意添加你需要的字符  
$chars ="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";  
$password = "";  
for ( $i = 0; $i < $length; $i++ )  {  
// 这里提供两种字符获取方式  
// 第一种是使用 substr 截取$chars中的任意一位字符；  
// 第二种是取字符数组 $chars 的任意元素  
// $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);  
$password .= $chars[ mt_rand(0, strlen($chars) - 1) ];  
}
return $password;  
}
function generate_password_zm($length) {
// 密码字符集，可任意添加你需要的字符  
$chars ="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$password = "";  
for ( $i = 0; $i < $length; $i++ )  {  
// 这里提供两种字符获取方式  
// 第一种是使用 substr 截取$chars中的任意一位字符；  
// 第二种是取字符数组 $chars 的任意元素  
// $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);  
$password .= $chars[ mt_rand(0, strlen($chars) - 1) ];  
}
return $password;  
}
function i_array_column($input, $columnKey, $indexKey=null){   //二位数组转一维  （二维数组，"要被当做值得键名"，"要被当做键的键名"）
    if(!function_exists('array_column')){ 
        $columnKeyIsNumber  = (is_numeric($columnKey))?true:false; 
        $indexKeyIsNull            = (is_null($indexKey))?true :false; 
        $indexKeyIsNumber     = (is_numeric($indexKey))?true:false; 
        $result                         = array(); 
        foreach((array)$input as $key=>$row){ 
            if($columnKeyIsNumber){ 
                $tmp= array_slice($row, $columnKey, 1); 
                $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null; 
            }else{ 
                $tmp= isset($row[$columnKey])?$row[$columnKey]:null; 
            } 
            if(!$indexKeyIsNull){ 
                if($indexKeyIsNumber){ 
                  $key = array_slice($row, $indexKey, 1); 
                  $key = (is_array($key) && !empty($key))?current($key):null; 
                  $key = is_null($key)?0:$key; 
                }else{ 
                  $key = isset($row[$indexKey])?$row[$indexKey]:0; 
                } 
            } 
            $result[$key] = $tmp; 
        } 
        return $result; 
    }else{
        return array_column($input, $columnKey, $indexKey);
    }
}
function get_client_ip_own($type = 0) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($_SERVER['HTTP_X_REAL_IP']){//nginx 代理模式下，获取客户端真实IP
        $ip=$_SERVER['HTTP_X_REAL_IP'];     
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {//客户端的ip
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {//浏览当前页面的用户计算机的网关
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];//浏览当前页面的用户计算机的ip地址
    }else{
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}
function NoRand($begin=1,$end=9,$limit=4){
    $rand_array=range($begin,$end); 
    shuffle($rand_array);//调用现成的数组随机排列函数 
    return array_slice($rand_array,0,$limit);//截取前$limit个 
}
function creat_token() {
    $token = md5(generate_password(16)."".NOW_TIME);  //生成token
    return $token;
}
function get_wx_token(){  //这是获取服务号的token
    $appid = C('FW_appid');
    $secret = C('FW_secret');
    $wxinfo = auto2_Entrance($state);
    if(S("WXAccessToken")){
        return S("WXAccessToken");
    }else{
    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
    
    $json = curl_get($url);
    $tokenarr = json_decode($json,TRUE);
    S("WXAccessToken",$tokenarr['access_token'],array('type'=>'file','expire'=>7200));
    return $tokenarr['access_token'];
    }
}
function get_wx_token_dyh($publicid){  //这里是不同订阅号的token  不同的好，返回不同的token
    $wxinfo = auto2_Entrance($publicid);

    $appid = $wxinfo['appid'];
    $secret = $wxinfo['secret'];
    if(S("WXAccessToken_{$publicid}")){
        return S("WXAccessToken_{$publicid}");
    }else{
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
        $json = curl_get($url);
        $tokenarr = json_decode($json,TRUE);
        S("WXAccessToken_{$publicid}",$tokenarr['access_token'],array('type'=>'file','expire'=>7200));
        return $tokenarr['access_token'];
    }
}
function get_wx_token_ceshi($publicid){  //这里是不同订阅号的token  不同的好，返回不同的token
    $wxinfo = auto2_Entrance($publicid);

    $appid = $wxinfo['appid'];
    $secret = $wxinfo['secret'];
    if(S("WXAccessToken_{$publicid}")){
        return S("WXAccessToken_{$publicid}");
    }else{
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
        $json = curl_get($url);
        $tokenarr = json_decode($json,TRUE);
        S("WXAccessToken_{$publicid}",$tokenarr['access_token'],array('type'=>'file','expire'=>7200));
        return $tokenarr['access_token'];
    }
}
function get_ticket($publicid) {
    if(S("WXticket_{$publicid}")){
        return S("WXticket_{$publicid}");
    }else{
        if($publicid=="gh_78052d300081"){  //服务号
            $token = get_wx_token();
        }elseif ($publicid=="gh_e7e54e5ab903") {  //订阅号
            $token = get_wx_token_dyh($publicid);
        }elseif ($publicid=="gh_008a50db02b8") {
            $token = get_wx_token_ceshi($publicid);
        }

    $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$token}&type=jsapi";
    $json = curl_get($url);
    $tokenarr = json_decode($json,TRUE);
    S("WXticket_{$publicid}",$tokenarr['ticket'],array('type'=>'file','expire'=>7200));
        return $tokenarr['ticket'];
    }
}
function Get_wx_information($id){
    $where['id']=$id;
    $wxinfo = M("wx_info")->where($where)->find();
    return $wxinfo;
}
function openid_userin($openid,$codetoken) {  //网页开发 获取用户信息
     //此处token不是 基础的token 而是  code换来的
    $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$codetoken}&openid={$openid}&lang=zh_CN";
    $json = curl_get($url);
    $arr = json_decode($json,TRUE);
    return $arr;
}
function openid_userinfo($openid) {  //这是服务号的获取身份信息
    $token = get_wx_token();
    $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$token}&openid={$openid}&lang=zh_CN";
    $json = curl_get($url);
    $arr = json_decode($json,TRUE);
    return $arr;
}
function message_void($openid,$publicid) {  //用户openid，公众号原始ID
    $where["{$publicid}_open"] = $openid;
    $s_user = M("s_user")->where($where)->find();
    if(!empty($s_user['phone'])){  //这个人绑了电话
        return $s_user;
    }else{   //这个人没邦手机号
        return FALSE;
    }
}
function addUserF($d_openid,$publicid) {
    $wxinfo = auto2_Entrance($publicid);
    if($publicid=="gh_78052d300081"){  //服务号
        $token = get_wx_token();
    }elseif ($publicid=="gh_e7e54e5ab903") {  //订阅号
        $token = get_wx_token_dyh($publicid);
    }elseif ($publicid=="gh_008a50db02b8") {
        $token = get_wx_token_ceshi($publicid);
    }else{
        exit();
    }
    $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$token}&openid={$d_openid}&lang=zh_CN";
    $json = curl_get($url);
    $arr = json_decode($json,TRUE);
    
    if($arr['subscribe']==1){  //关注，而不是退关注
        $data[0]["{$publicid}_open"] = $arr['openid'];
        $data[0]['nickname'] = $arr['nickname'];
        $data[0]['headimgurl'] = $arr['headimgurl'];
        $data[0]['sdate'] = date("Y-m-d H:i:s",$arr['subscribe_time']);
        $data[0]['unionid'] = $arr['unionid'];
        $data[0]['invitcode'] = strtoupper(generate_password(6));
        if(!empty($data[0]['unionid'])){  //
            $add = M("s_user")->addAll($data,array(),"nickname,headimgurl,{$publicid}_open"); 
        }else{
            $add = 0;
        }
        
//unionid是唯一字段
        if($add>0){
            $ret['unionid'] = $arr['unionid'];
            return $ret;
        }else{
            return FALSE;
        }
    }  else {
        return FALSE;
    }
}
function getdistance($lng1, $lat1, $lng2, $lat2) {  //计算两经纬度之间的距离
    // 将角度转为狐度
    $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
    $radLat2 = deg2rad($lat2);
    $radLng1 = deg2rad($lng1);
    $radLng2 = deg2rad($lng2);
    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;
    $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
    return $s;
}
function getDistance_n($longitude1, $latitude1, $longitude2, $latitude2, $unit=2, $decimal=2){
 
  $EARTH_RADIUS = 6370.996; // 地球半径系数
  $PI = 3.1415926;
 
  $radLat1 = $latitude1 * $PI / 180.0;
  $radLat2 = $latitude2 * $PI / 180.0;
 
  $radLng1 = $longitude1 * $PI / 180.0;
  $radLng2 = $longitude2 * $PI /180.0;
 
  $a = $radLat1 - $radLat2;
  $b = $radLng1 - $radLng2;
 
  $distance = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
  $distance = $distance * $EARTH_RADIUS * 1000;
 
  if($unit==2){
    $distance = $distance / 1000;
  }
 
  return round($distance, $decimal);
 
}
/*
* 39.1144787549,117.2157096863   前面是纬度，后面是经度
* 中国正常GCJ02坐标---->百度地图BD09坐标
* 腾讯地图用的也是GCJ02坐标
* @param double $lat 纬度
* @param double $lng 经度
* @return array();
*/
function Convert_GCJ02_To_BD09($lat,$lng){
    $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    $x = $lng;
    $y = $lat;
    $z =sqrt($x * $x + $y * $y) + 0.00002 * sin($y * $x_pi);
    $theta = atan2($y, $x) + 0.000003 * cos($x * $x_pi);
    $lng = $z * cos($theta) + 0.0065;
    $lat = $z * sin($theta) + 0.006;
    return array('lat'=>$lat,'lng'=>$lng);
}


/*
* 
* 百度地图BD09坐标---->中国正常GCJ02坐标
* 腾讯地图用的也是GCJ02坐标
* @param double $lat 纬度
* @param double $lng 经度
* @return array();
*/
function Convert_BD09_To_GCJ02($lat,$lng){
    $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    $x = $lng - 0.0065;
    $y = $lat - 0.006;
    $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
    $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
    $lng = $z * cos($theta);
    $lat = $z * sin($theta);
    return array('lat'=>$lat,'lng'=>$lng);
}
/*
* 
* 选择司机
* @param double $lat 纬度
* @param double $lng 经度
* @return array();
*/
function select_drive($lat_user,$lng_user) {  //用户坐标是 腾讯经纬度。    39.1164266293,117.1494376659  
    $limit=10000;
    $where['state'] = "stand"; //当前待机的司机。
    $s_driver = M("s_driver")->where($where)->field("id,latitude,longitude")->select();
    if(empty($s_driver)){
        return FALSE;
    }else{
        foreach ($s_driver as $key => $value) {
            $site = S("drive_{$value['id']}");
            $s = round(getdistance($lng_user, $lat_user, $site['lng'], $site['lat']),2);
            $data[$value['id']] = $s;
        }
        $drive_id = array_keys($data, min($data));
        if($drive_id[0]){
            if($data[$drive_id[0]]<$limit){
                return $drive_id[0];
            }else{
                return FALSE;
            }
        }  else {
            return FALSE;
        }
        
        
    }
}
/*
* 
* 创建订单
* @param double $lat 纬度
* @param double $lng 经度
 * * @param double $local 中文地点
* @return array();
*/
function creat_order($userid,$d_id,$local,$lat,$lng,$e_local,$e_lat,$e_lng,$source,$address_id=1) {
   $where['u_id'] = $userid;
   $where['state'] = array("in","new,on,active,wait_pay");
   $order = M("b_order")->where($where)->find();
   if(empty($order)){ //没有找到正在进行的订单
       $publicid = M("wx_info")->where(array("public_id"=>session("publicid")))->getField("id");
        $data['u_id'] = $userid;
        $data['d_id'] = $d_id;
        $data['order_num'] = $userid."".$d_id."".NOW_TIME."".rand(10,99);
        $data['amount'] = 0;
        $data['sdate'] = date("Y-m-d H:i:s",NOW_TIME);
        $data['s_lat'] = $lat;
        $data['s_lng'] = $lng;
        $data['saddress'] = $local;
        $data['e_lat'] = $e_lat;
        $data['e_lng'] = $e_lng;
        $data['eaddress'] = $e_local;
        $data['distance'] = 0;
        $data['state'] = "new";
        $data['source'] = $source;
        $data['publicid'] = $publicid;
        $data['address_id'] = $address_id;
        $data['operation'] = M("s_driver")->where(array("id"=>$d_id))->getField("operation");
        $id = M("b_order")->add($data);
        
        if($id>0){
            return $id;
        }
   }else{
       return FALSE;
   }
}
function creat_order_offline($userid,$d_id,$local,$lat,$lng,$e_local,$e_lat,$e_lng,$source) {  //创建线下订单
   $where['u_id'] = $userid;
   $where['state'] = array("in","new,on,active,wait_pay");
   $order = M("b_order")->where($where)->find();
   if(empty($order)){ //没有找到正在进行的订单
       $publicid = M("wx_info")->where(array("public_id"=>"gh_78052d300081"))->getField("id");
       $data['u_id'] = $userid;
        $data['d_id'] = $d_id;
        $data['order_num'] = $userid."".$d_id."".NOW_TIME."".rand(10,99);
        $data['amount'] = 0;
        $data['sdate'] = date("Y-m-d H:i:s",NOW_TIME);
        $data['s_lat'] = $lat;
        $data['s_lng'] = $lng;
        $data['saddress'] = $local;
        $data['e_lat'] = $e_lat;
        $data['e_lng'] = $e_lng;
        $data['eaddress'] = $e_local;
        $data['distance'] = 0;
        $data['state'] = "new";
        $data['source'] = $source;
        $data['publicid'] = $publicid;
        $id = M("b_order")->add($data);
        if($id>0){
            return $id;
        }
   }else{
       return FALSE;
   }
}
function get_drive_info($d_id) {
    $sql = <<<SQL
    select s_driver.id d_id,s_driver.name,s_driver.nickname,s_driver.card,s_driver.phone,s_car.car_type,s_car.carcolor,s_car.carnum,if(s_avatar_verify.verify='Y',s_avatar_verify.file,'') avatar,ifnull((select FORMAT(AVG(point),1) from d_point where d_id={$d_id}),5) point,s_driver.lits 
        from s_driver 
        left join s_car on s_driver.id = s_car.d_id 
        left join s_avatar_verify on s_avatar_verify.d_id = s_driver.id 
        where s_driver.id={$d_id} 
SQL;
    $res = M()->query($sql);
    return $res[0];
}
//客服消息（系统向用户发送消息）
function Customer_Service($openid,$cont,$publicid) {  //$publicid为原始ID
    
    $data['touser'] = $openid;
    $data['msgtype'] = "text";
    $data['text'] = array("content"=>$cont);
    $post_data = json_encode($data,JSON_UNESCAPED_UNICODE);
    
    $wxinfo = auto2_Entrance($publicid);
    if($publicid=="gh_78052d300081"){  //服务号
        $token = get_wx_token();
    }elseif ($publicid=="gh_e7e54e5ab903") {  //订阅号
        $token = get_wx_token_dyh($publicid);
    }elseif ($publicid=="gh_008a50db02b8") {
        $token = get_wx_token_ceshi($publicid);
    }else{
        exit();
    }
    
    $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$token}";
    $json = curl_post($url, $post_data);
    $res = json_decode($json,TRUE);
    return $res;
}
function Customer_Service_image($openid,$cont,$publicid) {  //$publicid为原始ID
    
    $data['touser'] = $openid;
    $data['msgtype'] = "image";
    $data['image'] = array("media_id"=>$cont);
    $post_data = json_encode($data,JSON_UNESCAPED_UNICODE);
    
    $wxinfo = auto2_Entrance($publicid);
    if($publicid=="gh_78052d300081"){  //服务号
        $token = get_wx_token();
    }elseif ($publicid=="gh_e7e54e5ab903") {  //订阅号
        $token = get_wx_token_dyh($publicid);
    }elseif ($publicid=="gh_008a50db02b8") {
        $token = get_wx_token_ceshi($publicid);
    }else{
        exit();
    }
    
    $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$token}";
    $json = curl_post($url, $post_data);
    $res = json_decode($json,TRUE);
    return $res;
}
function Temp_Service($openid,$template_id) {  //发送模板消息
    $token = get_wx_token();
    $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$token}";
    $date = date("Y-m-d H:i:s",NOW_TIME);
    $post = array(
        "touser"=>$openid,
        "template_id"=>$template_id,
        "url"=>"https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx73e0cea3e01d21f6&redirect_uri=https%3A%2F%2Fwww.taxisanjiayi.com%2FUser%2Fcoupon&response_type=code&scope=snsapi_userinfo&state=gh_78052d300081#wechat_redirect",
        "data"=>array(
            "first"=>array("value"=>"恭喜您，您的好友响应了您的邀请，为您发放一张2元优惠券","color"=>"#FF0000"),
            "keyword1"=>array("value"=>"优惠券发放","color"=>"#173177"),
            "keyword2"=>array("value"=>"邀请有礼","color"=>"#173177"),
            "keyword3"=>array("value"=>$date,"color"=>"#173177"),
            "remark"=>array("value"=>"您可点击下方详情，查看优惠券","color"=>"#FF0000"),
        ),
    );
    $post_data = json_encode($post,JSON_UNESCAPED_UNICODE);
    $res = curl_post($url, $post_data);
    $arr = json_decode($res,TRUE);
    return $arr;
}
function Temp_Service_pay($openid,$template_id) {  //发送支付完成模板消息
    $token = get_wx_token();
    $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$token}";
    $date = date("Y-m-d H:i:s",NOW_TIME);
    $post = array(
        "touser"=>$openid,
        "template_id"=>$template_id,
        "url"=>"",
        "data"=>array(
            "first"=>array("value"=>"您已进行线下支付，感谢您的使用。","color"=>"#FF0000"),
            "keyword1"=>array("value"=>"行程支付","color"=>"#173177"),
            "keyword2"=>array("value"=>"线下支付","color"=>"#173177"),
            "keyword3"=>array("value"=>$date,"color"=>"#173177"),
//            "remark"=>array("value"=>"您可点击下方详情，查看优惠券","color"=>"#FF0000"),
        ),
    );
    $post_data = json_encode($post,JSON_UNESCAPED_UNICODE);
    $res = curl_post($url, $post_data);
    $arr = json_decode($res,TRUE);
    return $arr;
}
function code_openid($code) {  //服务号
    $appid = C('FW_appid');
    $secret = C('FW_secret');
    $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
    $res = curl_get($url);
    $arrs = json_decode($res,TRUE);
    return $arrs;
}
function code_openid_CS($code) {  //服务号
    $appid = C('CS_appid');
    $secret = C('CS_secret');
    $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
    $res = curl_get($url);
    $arrs = json_decode($res,TRUE);
    return $arrs;
}
function sendSms($phone,$yzm) {  //e达生活验证码
    Vendor('SendSMS.SignatureHelper');
    date_default_timezone_set("Asia/Shanghai");
    $params = array ();
    // *** 需用户填写部分 ***
    // fixme 必填：是否启用https
    $security = false;
    // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
    $accessKeyId = "LTAIWoCbapilmnna";
    $accessKeySecret = "sC5zFEdgSPXFI1lfPUWN9H51Cj18kW";
    // fixme 必填: 短信接收号码
    $params["PhoneNumbers"] = $phone;
    // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
    $params["SignName"] = "e达生活";
    // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
    $params["TemplateCode"] = "SMS_164279778";
    // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
    $params['TemplateParam'] = Array (
        "code" => $yzm,
//        "product" => "阿里通信"
    );
    // fixme 可选: 设置发送短信流水号
    $params['OutId'] = time();
    // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
//    $params['SmsUpExtendCode'] = "1234567";
    // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
    if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
        $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
    }
    // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
    $helper = new SignatureHelper();
    // 此处可能会抛出异常，注意catch
    $content = $helper->request(
        $accessKeyId,
        $accessKeySecret,
        "dysmsapi.aliyuncs.com",
        array_merge($params, array(
            "RegionId" => "cn-hangzhou",
            "Action" => "SendSms",
            "Version" => "2017-05-25",
        )),
        $security
    );
    return $content;
}
function sendSms_passed($phone) {  //审核通过
    Vendor('SendSMS.SignatureHelper');
    date_default_timezone_set("Asia/Shanghai");
    $params = array ();
    // *** 需用户填写部分 ***
    // fixme 必填：是否启用https
    $security = false;
    // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
    $accessKeyId = "LTAIWoCbapilmnna";
    $accessKeySecret = "sC5zFEdgSPXFI1lfPUWN9H51Cj18kW";
    // fixme 必填: 短信接收号码
    $params["PhoneNumbers"] = $phone;
    // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
    $params["SignName"] = "e达生活";
    // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
    $params["TemplateCode"] = "SMS_166370664";
    // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
    $params['TemplateParam'] = Array (
        "timestr" => "每周三下午1点",
        "phone" => "022-83605860",
        "address" => "天津市宝坻区钰华街真爱医院西美团专送",
    );
    // fixme 可选: 设置发送短信流水号
    $params['OutId'] = time();
    // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
//    $params['SmsUpExtendCode'] = "1234567";
    // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
    if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
        $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
    }
    // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
    $helper = new SignatureHelper();
    // 此处可能会抛出异常，注意catch
    $content = $helper->request(
        $accessKeyId,
        $accessKeySecret,
        "dysmsapi.aliyuncs.com",
        array_merge($params, array(
            "RegionId" => "cn-hangzhou",
            "Action" => "SendSms",
            "Version" => "2017-05-25",
        )),
        $security
    );
    return $content;
}
function sendSms_fail($phone) {  //审核失败
    Vendor('SendSMS.SignatureHelper');
    date_default_timezone_set("Asia/Shanghai");
    $params = array ();
    // *** 需用户填写部分 ***
    // fixme 必填：是否启用https
    $security = false;
    // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
    $accessKeyId = "LTAIWoCbapilmnna";
    $accessKeySecret = "sC5zFEdgSPXFI1lfPUWN9H51Cj18kW";
    // fixme 必填: 短信接收号码
    $params["PhoneNumbers"] = $phone;
    // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
    $params["SignName"] = "e达生活";
    // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
    $params["TemplateCode"] = "SMS_166370471";
    // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
    $params['TemplateParam'] = Array (
        "phone" => "022-83605860",
//        "product" => "阿里通信"
    );
    // fixme 可选: 设置发送短信流水号
    $params['OutId'] = time();
    // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
//    $params['SmsUpExtendCode'] = "1234567";
    // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
    if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
        $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
    }
    // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
    $helper = new SignatureHelper();
    // 此处可能会抛出异常，注意catch
    $content = $helper->request(
        $accessKeyId,
        $accessKeySecret,
        "dysmsapi.aliyuncs.com",
        array_merge($params, array(
            "RegionId" => "cn-hangzhou",
            "Action" => "SendSms",
            "Version" => "2017-05-25",
        )),
        $security
    );
    return $content;
}
function sendSms_text($phone,$yzm) {  //三潭龙账号
    Vendor('SendSMS.SignatureHelper');
    date_default_timezone_set("Asia/Shanghai");
    $params = array ();

    // *** 需用户填写部分 ***
    // fixme 必填：是否启用https
    $security = false;

    // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
    $accessKeyId = "LTAIWoCbapilmnna";
    $accessKeySecret = "sC5zFEdgSPXFI1lfPUWN9H51Cj18kW";

    // fixme 必填: 短信接收号码
    $params["PhoneNumbers"] = $phone;

    // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
    $params["SignName"] = "三潭龙";

    // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
    $params["TemplateCode"] = "SMS_164279778";

    // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
    $params['TemplateParam'] = Array (
        "code" => $yzm,
//        "product" => "阿里通信"
    );

    // fixme 可选: 设置发送短信流水号
    
    $params['OutId'] = time();

    // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
//    $params['SmsUpExtendCode'] = "1234567";


    // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
    if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
        $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
    }

    // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
    $helper = new SignatureHelper();

    // 此处可能会抛出异常，注意catch
    $content = $helper->request(
        $accessKeyId,
        $accessKeySecret,
        "dysmsapi.aliyuncs.com",
        array_merge($params, array(
            "RegionId" => "cn-hangzhou",
            "Action" => "SendSms",
            "Version" => "2017-05-25",
        )),
        $security
    );

    return $content;
}
function search_order_userid($userid) {   //通过userid检测还未结束的订单
    $where['b_order.u_id'] = $userid;
    $where['b_order.state'] = array("in","new,on,arrived,active,wait_pay");  
    $sql = <<<SQL
    select b_order.id o_id,b_order.u_id,b_order.d_id,b_order.state 
        from b_order 
        %WHERE%
        for update
SQL;
    $res = M()->where($where)->query($sql,TRUE);
    if(empty($res)){  //没订单取消
        return FALSE;
    }else{
        return $res[0];
    }
}
function search_order($openid) {   //通过openid检测可取消的订单
    $where['s_user.openid'] = $openid;
    $where['b_order.state'] = array("in","new,on,active");  //不能带wait_pay。 因为wait_pay不能取消
    $sql = <<<SQL
    select b_order.id o_id,b_order.u_id,b_order.d_id 
        from b_order 
        left join s_user on s_user.id = b_order.u_id 
        %WHERE%
SQL;
    $res = M()->where($where)->query($sql,TRUE);
    if(empty($res)){  //没订单取消
        return FALSE;
    }else{
        return $res[0];
    }
}
function search_order_drive($d_id) {
    $data['b_order.d_id'] = $d_id;
    $data['b_order.d_confirmation'] = 'N';
    $data['b_order.state'] = array("in","new,on,arrived,active,wait_pay");
    $sql = <<<SQL
    select b_order.id o_id,b_order.u_id,b_order.d_id,b_order.s_lat,b_order.s_lng,b_order.saddress,
            b_order.e_lat,b_order.e_lng,b_order.eaddress,b_order.state,s_user.phone,b_order.sdate,b_order.operation,s_user.headimgurl,
            b_order.mileage_fee,b_order.duration_fee,b_order.amount,b_order.distance,b_order.duration 
        from b_order 
        left join s_user on s_user.id = b_order.u_id 
        %WHERE% 
SQL;
    $res = M()->where($data)->query($sql,TRUE);
    if(empty($res)){  //没订单取消
        return FALSE;
    }else{
        $s_site = Convert_GCJ02_To_BD09($res[0]['s_lat'], $res[0]['s_lng']);
        $res[0]['s_lat'] = $s_site['lat'];
        $res[0]['s_lng'] = $s_site['lng'];
        $e_site = Convert_GCJ02_To_BD09($res[0]['e_lat'], $res[0]['e_lng']);
        $res[0]['e_lat'] = $e_site['lat'];
        $res[0]['e_lng'] = $e_site['lng'];
        return $res[0];
    }
}

function GetOrderInfo($o_id) {  //获取订单信息
    $data['b_order.id'] = $o_id;
        $sql = <<<SQL
    select b_order.id o_id,b_order.u_id,b_order.d_id,b_order.s_lat,b_order.s_lng,b_order.saddress,
            b_order.e_lat,b_order.e_lng,b_order.eaddress,s_user.phone u_phone,b_order.sdate,b_order.operation,s_user.headimgurl,b_order.address_id,
            b_order.mileage_fee,b_order.duration_fee,b_order.amount,b_order.distance,b_order.duration,
            s_car.car_type,s_car.carcolor,s_car.carnum,if(s_avatar_verify.verify="Y",s_avatar_verify.file,'') avatar,s_driver.card,s_driver.name,s_driver.nickname,s_driver.phone d_phone,s_driver.lits,
                ifnull((select FORMAT(AVG(point),1) from d_point where d_id=b_order.d_id),5) point,
                ifnull((select COUNT(s_complaint.id) from s_complaint where d_id=b_order.d_id),0) complaint,
                (CASE b_order.state 
    WHEN 'on' THEN '尚未接驾' 
    WHEN 'active' THEN '行程中' 
    WHEN 'wait_pay' THEN '待支付' 
    WHEN 'end' THEN '订单结束' 
    WHEN 'cannal' THEN '订单被取消' 
    END)  state_cn,b_order.state 
        from b_order 
        left join s_user on s_user.id = b_order.u_id 
        left join s_driver on s_driver.id = b_order.d_id 
        left join s_car on b_order.d_id = s_car.d_id 
        left join s_avatar_verify on s_avatar_verify.d_id = b_order.d_id 
        %WHERE% 
SQL;
    $res = M()->where($data)->query($sql,TRUE);
    return $res[0];
}
function Drive_token($d_id) {
    $token = creat_token();
    return $token;
}
function order_state_up($o_id,$state) {
    $up = M("b_order")->where(array("id"=>$o_id))->save(array("state"=>$state));
    return $up;
}
function driver_state_up($d_id,$state) {
    $up = M("s_driver")->where(array("id"=>$d_id))->save(array("state"=>$state));
    return $up;
}
function driver_order_confirmation($o_id,$state) {
    $up = M("b_order")->where(array("id"=>$o_id))->save(array("d_confirmation"=>$state));
    return $up;
}
function driver_amount_recoed_add($o_id,$d_id,$amount) {  //司机余额增加记录
    $mstx['d_id'] = $d_id;
    $mstx['o_id'] = $o_id;
    $mstx['date'] = date("Y-m-d H:i:s",NOW_TIME);
    $mstx['amount'] = $amount;
    $add = M("d_amount_record")->add($mstx);
    return $add;
}
function applets_token() {
    $wxinfo = auto2_Entrance(session("publicid"));
    $appid = $wxinfo['appid'];
    $secret = $wxinfo['secret'];
    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
    $json = curl_get($url);
    $arr = json_decode($json,TRUE);
    return $arr['access_token'];
}
function jg_send_jpush($registration_id,$content,$psd=TRUE) {  //极光推送
    Vendor('JPushZDY.JPushZDY');
    $push = new \JPushZDY();
    $m_type = 'http';//推送附加字段的类型
    $m_txt = 'http://www.groex.cn/';//推送附加字段的类型对应的内容(可不填) 可能是url,可能是一段文字。
    $m_time = 86400;//离线保留时间
    $receive = array("registration_id"=>array($registration_id));  //目标用户  1507bfd3f788b28f010
//    $content = '这是什么那';
    $message="";//存储推送状态
    $result = $push->push($receive,$content,$m_type,$m_txt,$m_time,$psd);
    $res_arr = json_decode($result, true);
    return $res_arr;
}
function aliyun_pushmessage($DEVICEID,$bodyjson) {  //阿里云消息推送
    date_default_timezone_set('Etc/GMT');
    $data['Action'] = "PushMessageToAndroid";
    $data['AppKey'] = "27577922";
    $data['Target'] = "DEVICE";
    $data['TargetValue'] = $DEVICEID; //"fabce271c6ab4ccb9ff385f90a1e7262";  //DEVICEID  设备ID
    $data['Title'] = "1";
    $data['Body'] = urlencode($bodyjson);   //$bodyjson
    $data['RegionId'] = "cn-hangzhou";
    $data['Version'] = "2016-08-01";
    $data['AccessKeyId'] = "LTAIIdxaxAnJkHD0";
    $data['SignatureMethod'] = "HMAC-SHA1";
    $time = date("Y-m-d H:i:s|",NOW_TIME);
    $time = str_replace(" ","T", $time);
    
    $data['Timestamp']=str_replace("|","Z",$time);
    $data['SignatureVersion'] = "1.0";
    $data['SignatureNonce'] = generate_password(4).NOW_TIME;
    ksort($data);
    $signx = "";
    $accessSecret = "1c0ReAFzreO722fAqfnK7SUgdG4bZ5&";
    foreach ($data as $key => $value) {
        if(empty($signx)){
            $signx = $key."=".$value;
        }  else {
            $signx = $signx."&".$key."=".$value;
        }
        
    }
    $source = "GET&%2F&".urlencode($signx);
    $source = str_replace("%3A","%253A", $source);  //:的编码方式不一样
    $data['Signature'] = urlencode(base64_encode(hash_hmac('sha1', $source, $accessSecret, true)));
    $Signature_y = base64_encode(hash_hmac('sha1', $source, $accessSecret, true));
    foreach ($data as $key => $value) {
        if(empty($url)){
            $url = $key."=".$value;
        }else{
            $url = $url."&".$key."=".$value;
        }
    }

    $url = "http://cloudpush.aliyuncs.com/?".$url;
    $json = curl_get($url);
    $arr = xmlToArray($json);
    return $arr;
}
function aliyun_pushNotice($DEVICEID,$bodyjson,$Title='2') {  //阿里云通知推送
    date_default_timezone_set('Etc/GMT');
    $data['Action'] = "PushNoticeToAndroid";
    $data['AppKey'] = "27577922";
    $data['Target'] = "DEVICE";
    $data['TargetValue'] = $DEVICEID;   //"fabce271c6ab4ccb9ff385f90a1e7262";  //DEVICEID  设备ID
    $data['Title'] = urlencode($Title);
    $data['Body'] = urlencode($bodyjson);
    $data['RegionId'] = "cn-hangzhou";
    $data['Version'] = "2016-08-01";
    $data['AccessKeyId'] = "LTAIIdxaxAnJkHD0";
    $data['SignatureMethod'] = "HMAC-SHA1";
    $time = date("Y-m-d H:i:s|",NOW_TIME);
    $time = str_replace(" ","T", $time);
    
    $data['Timestamp']=str_replace("|","Z",$time);
    $data['SignatureVersion'] = "1.0";
    $data['SignatureNonce'] = generate_password(4).NOW_TIME;
    ksort($data);
    $signx = "";
    $accessSecret = "1c0ReAFzreO722fAqfnK7SUgdG4bZ5&";
    foreach ($data as $key => $value) {
        if(empty($signx)){
            $signx = $key."=".$value;
        }  else {
            $signx = $signx."&".$key."=".$value;
        }
        
    }
    $source = "GET&%2F&".urlencode($signx);
    $source = str_replace("%3A","%253A", $source);  //:的编码方式不一样
    $data['Signature'] = urlencode(base64_encode(hash_hmac('sha1', $source, $accessSecret, true)));
    foreach ($data as $key => $value) {
        if(empty($url)){
            $url = $key."=".$value;
        }else{
            $url = $url."&".$key."=".$value;
        }
    }
    $url = "http://cloudpush.aliyuncs.com/?".$url;
    $json = curl_get($url);
    $arr = xmlToArray($json);
    return $arr;
}
function secToTime($times){
    $result = '00:00:00';  
    if ($times>0) {
            $hour = floor($times/3600);  
            $minute = floor(($times-3600 * $hour)/60);  
            $second = floor((($times-3600 * $hour) - 60 * $minute) % 60);  
            $result = $hour.':'.$minute.':'.$second;  
    }  
    return $result;  
}
function get_online_record($d_id) {
    $data['sdate'] = NOW_TIME;
    $data['edate'] = 0;
    $data['d_id'] = $d_id;
    $data['line_time'] = 0;
    $add = M("d_online_record")->add($data);
    if($add>0){
        return $add;
    }else{
        return FALSE;
    }
}
function getarea() {  //获取宝坻各加价区域
        $area = array(
		//   +2区 117.288058,39.751594
		12 => array(
			array('x'=>117.272822, 'y'=>39.695654),
			array('x'=>117.272822, 'y'=>39.746269),
			array('x'=>117.288058,'y'=>39.746269),
			array('x'=>117.288058,'y'=>39.695654),
                        array('x'=>117.272822,'y'=>39.695654),
		),
                // +1区 
		3 => array(
			array('x'=>117.288058, 'y'=>39.695654),
			array('x'=>117.288058, 'y'=>39.746269),
			array('x'=>117.300778,'y'=>39.746269),
			array('x'=>117.300778,'y'=>39.695654),
                        array('x'=>117.288058,'y'=>39.695654),
		),
		//  正常区  117.319175,39.748266
		1 => array(
			array('x'=>117.300778,'y'=>39.744993),
			array('x'=>117.318716, 'y'=>39.744993),
			array('x'=>117.318716, 'y'=>39.695654),
			array('x'=>117.300778,'y'=>39.695654),
			array('x'=>117.300778,'y'=>39.744993),
		),
                //  正常区  117.341884,39.745215   117.318888,39.744993
		2 => array(
			array('x'=>117.318716,'y'=>39.744993),
			array('x'=>117.342675, 'y'=>39.744993),
			array('x'=>117.342675, 'y'=>39.714084),
			array('x'=>117.318716,'y'=>39.714084),
			array('x'=>117.318716,'y'=>39.744993),
		),
                //  +1区  
		6 => array(
			array('x'=>117.342675,'y'=>39.714084),
			array('x'=>117.342675, 'y'=>39.748266),
			array('x'=>117.349574, 'y'=>39.748266),
			array('x'=>117.349574,'y'=>39.714084),
			array('x'=>117.342675,'y'=>39.714084),
		),
                //  +2区  117.349574,39.738279
		15 => array(
			array('x'=>117.349574,'y'=>39.714084),
			array('x'=>117.349574, 'y'=>39.748266),
			array('x'=>117.358772, 'y'=>39.748266),
			array('x'=>117.358772,'y'=>39.714084),
			array('x'=>117.349574,'y'=>39.714084),
		),
                // +1区  117.28662,39.691434
		10 => array(
			array('x'=>117.290429,'y'=>39.695654),
			array('x'=>117.321187, 'y'=>39.695654),
			array('x'=>117.321187, 'y'=>39.691434),
			array('x'=>117.290429,'y'=>39.691434),
			array('x'=>117.290429,'y'=>39.695654),
		),
                // +2区  117.28662,39.691434
		11 => array(
			array('x'=>117.290429,'y'=>39.691434),
			array('x'=>117.321187, 'y'=>39.691434),
			array('x'=>117.321187, 'y'=>39.686326),
			array('x'=>117.290429,'y'=>39.686326),
			array('x'=>117.290429,'y'=>39.691434),
		),
                //+1区  117.317953,39.748488
		5 => array(
			array('x'=>117.318716,'y'=>39.744993),
			array('x'=>117.318716, 'y'=>39.748488),
			array('x'=>117.342675, 'y'=>39.748488),
			array('x'=>117.342675,'y'=>39.744993),
			array('x'=>117.318716,'y'=>39.744993),
		),
                //+2区  117.319175,39.752537
		14 => array(
			array('x'=>117.318716,'y'=>39.748488),
			array('x'=>117.318716, 'y'=>39.752537),
			array('x'=>117.342675, 'y'=>39.752537),
			array('x'=>117.342675,'y'=>39.748488),
			array('x'=>117.318716,'y'=>39.748488),
		),
                //+1区  117.300778  117.318816
		4 => array(
			array('x'=>117.300778,'y'=>39.744993),
			array('x'=>117.300778, 'y'=>39.748488),
			array('x'=>117.318716, 'y'=>39.748488),
			array('x'=>117.318716,'y'=>39.744993),
			array('x'=>117.300778,'y'=>39.744993),
		),
                //+2区  117.300778  117.318816
		13 => array(
			array('x'=>117.300778,'y'=>39.748488),
			array('x'=>117.300778, 'y'=>39.752537),
			array('x'=>117.318716, 'y'=>39.752537),
			array('x'=>117.318716,'y'=>39.748488),
			array('x'=>117.300778,'y'=>39.748488),
		),
            //117.318716  117.342675  横坐标   
            //39.714084   39.695654
                //+1区    117.330745   39.704537
		8 => array(
			array('x'=>117.318716,'y'=>39.695654),
			array('x'=>117.330745, 'y'=>39.695654),
			array('x'=>117.330745, 'y'=>39.704537),
			array('x'=>117.318716,'y'=>39.704537),
			array('x'=>117.318716,'y'=>39.695654),
		),
//                //+1 区
//                16 => array(
//			array('x'=>117.330745,'y'=>39.695654),
//			array('x'=>117.342675, 'y'=>39.695654),
//			array('x'=>117.342675, 'y'=>39.704537),
//			array('x'=>117.330745,'y'=>39.704537),
//			array('x'=>117.330745,'y'=>39.695654),
//		),
                //+1 区
                9 => array(
			array('x'=>117.318716,'y'=>39.704537),
			array('x'=>117.330745, 'y'=>39.704537),
			array('x'=>117.330745, 'y'=>39.714084),
			array('x'=>117.318716,'y'=>39.714084),
			array('x'=>117.318716,'y'=>39.704537),
		),
                //+2 区
                7 => array(
			array('x'=>117.330745,'y'=>39.704537),
			array('x'=>117.342675, 'y'=>39.704537),
			array('x'=>117.342675, 'y'=>39.714084),
			array('x'=>117.330745,'y'=>39.714084),
			array('x'=>117.330745,'y'=>39.704537),
		),
    );
    return $area;
}
function get_addressid() {  //根据经纬度获取片区
        $area = array(
		//宝坻行政区
            //116.897546,39.333383
                1 => array(
			array('x'=>117.128601,'y'=>39.875308),
			array('x'=>117.614979, 'y'=>39.875308),
			array('x'=>117.614979, 'y'=>39.569852),
			array('x'=>117.128601,'y'=>39.569852),
			array('x'=>117.128601,'y'=>39.875308),
		),
            
                //宝坻城区
//                1 => array(
//			array('x'=>117.268942,'y'=>39.764685),
//			array('x'=>117.359778, 'y'=>39.764685),
//			array('x'=>117.359778, 'y'=>39.694099),
//			array('x'=>117.268942,'y'=>39.694099),
//			array('x'=>117.268942,'y'=>39.764685),
//		),
                //沧州盐山  117.134502,38.150633     117.38574,38.112486
		2 => array(
			array('x'=>117.134502,'y'=>38.150633),
			array('x'=>117.38574, 'y'=>38.150633),
			array('x'=>117.38574, 'y'=>37.954689),
			array('x'=>117.134502,'y'=>37.954689),
			array('x'=>117.134502,'y'=>38.150633),
		),
                //大城  （现在测试是用天津）
		3 => array(
			array('x'=>116.897546,'y'=>39.333383),
			array('x'=>117.444866, 'y'=>39.333383),
			array('x'=>117.444866, 'y'=>38.917801),
			array('x'=>116.897546,'y'=>38.917801),
			array('x'=>116.897546,'y'=>39.333383),
		),
                //慧谷大厦周边
                //117.151048,39.125118   117.157121,39.124614
//                2 => array(
//			array('x'=>117.151048,'y'=>39.125118),
//			array('x'=>117.157121, 'y'=>39.125118),
//			array('x'=>117.157121, 'y'=>39.117252),
//			array('x'=>117.151048,'y'=>39.117252),
//			array('x'=>117.151048,'y'=>39.125118),
//		),
                //青县
                //116.683678,38.670557   116.979759,38.64982    116.971423,38.463822
//                2 => array(
//			array('x'=>116.683678,'y'=>38.670557),
//			array('x'=>116.979759, 'y'=>38.670557),
//			array('x'=>116.979759, 'y'=>38.463822),
//			array('x'=>116.683678,'y'=>38.463822),
//			array('x'=>116.683678,'y'=>38.670557),
//		),
                
    );
    return $area;
}
function remote_order($lat,$lng) {  //默认输入的是标准坐标系   //坐标点必须顺时针转。
    Vendor('AreaCheck.AreaCheck'); 
    $site = Convert_GCJ02_To_BD09($lat, $lng);
            
        $area = getarea();
        $area = new \Area($area);
        return $area->checkPoint($site['lng'],$site['lat']);
//        return $area;
}
function remote_addressid($lat,$lng) {  //默认输入的是标准坐标系   //坐标点必须顺时针转。
    Vendor('AreaCheck.AreaCheck'); 
    $site = Convert_GCJ02_To_BD09($lat, $lng);
            
        $area = get_addressid();
        $area = new \Area($area);
        return $area->checkPoint($site['lng'],$site['lat']);
//        return $area;
}
function media_to_img($media_id,$filename) {
    $token = get_wx_token();
    $url = "https://api.weixin.qq.com/cgi-bin/media/get?access_token={$token}&media_id={$media_id}";
    $json = curl_get($url);
    
    $path = "./Upload/drive_ver_img/{$filename}";
    $aaa = file_put_contents($path,$json);
    if($aaa>0){
        $pathsave = C("domin")."/Upload/drive_ver_img/{$filename}";
        return $pathsave;
    }else{
        return FALSE;
    }
}
//搜索某坐标 附近X公里内的司机坐标与ID
function limit_dviver_onsite($lat,$lng,$distance) { //用户纬度，经度，搜索范围 米。
    $where['state'] = "stand"; //当前待机的司机。
    $s_driver = M("s_driver")->where($where)->field("id,latitude,longitude")->select();
    
    foreach ($s_driver as $key => $value) {
        $site = S("drive_{$value['id']}");
        
        $s = round(getdistance($lng, $lat, $site['lng'], $site['lat']),2);
//        dump($s);
        if($s<3000){
            $data[$value['id']]['d_id'] = $value['id'];
            $data[$value['id']]['lat'] = $site['lat'];
            $data[$value['id']]['lng'] = $site['lng'];
            $data[$value['id']]['distance'] = $s;
        }
    }
    return $data;
    
}
/* 获取起步价
 * $time  司机接客已用秒数

 */
function getUserWaitReward($time,$address_id=1) {  //获取用户所得奖励金
    $wait_reward = M("m_user_wait_reward_set")->where(array("address_id"=>$address_id))->find();
    if($time<$wait_reward['free_time']){
        $amount = 0;
    }else{
        $over = $time-$wait_reward['free_time']; //超出免费的时长
        $eklps = ceil($over/$wait_reward['cost_every']);  //超出的时长 /得钱间隔时长  倍数。向上取整
        $amount = $eklps*$wait_reward['every_amount'];
    }
    if($amount>=$wait_reward['limit_amount']){
        $amount = $wait_reward['limit_amount'];
    }
    return $amount;
}
/* 获取起步价
 * $lat 维度， $lng  经度
 * $orderstart  开始计费的时间戳
 * $all_kilometer  当前总公里数  (6.54)
 * $start_fee_list   上一次访问的起步价数组
 */
function getStartFee($lat,$lng,$orderstart,$all_kilometer,$start_fee_list) {   //$orderstart  上车时间    //上一次访问时的加价项
    $fee = S("billing_n");
    $STAR_FEE['fee'] = $fee['start_fee'];
    $STAR_FEE['start_fee'] = $fee['start_fee'];
    
    $time = date("H:i:s",$orderstart);
    if($time>$fee['early_peak_time_start']&&$time<$fee['early_peak_time_end']){  //早高峰  06:30:00-08:00:00
        $STAR_FEE['fee'] = $STAR_FEE['fee']+$fee['early_peak'];
        $STAR_FEE['early_peak'] = $fee['early_peak'];
    }
    if($time>$fee['late_peak_time_start']&&$time<$fee['late_peak_time_end']){  //晚高峰  17:30:00-19:00:00
        $STAR_FEE['fee'] = $STAR_FEE['fee']+$fee['late_peak'];
        $STAR_FEE['late_peak'] = $fee['late_peak'];
    }
    if($fee['night_driving_first_time_start']>$fee['night_driving_first_time_end']){//如果 第一时段的起始是深夜（22:00:00,23:00:00一类的）  结束时间是 02:00:00一类的
        if($time<$fee['night_driving_first_time_end']||$time>$fee['night_driving_first_time_start']){  //夜间行车第一时段  23:00:00-02:00:00
            $STAR_FEE['fee'] = $STAR_FEE['fee']+$fee['night_driving_first'];
            $STAR_FEE['night_driving_first'] = $fee['night_driving_first'];
        }
    }else{  //起始是 凌晨（01:00:00, 04:00:00）一类的。
        if($time>$fee['night_driving_first_time_start']&&$time<$fee['night_driving_first_time_end']){  //夜间行车第一时段  00:00:00-03:00:00
            $STAR_FEE['fee'] = $STAR_FEE['fee']+$fee['night_driving_first'];
            $STAR_FEE['night_driving_first'] = $fee['night_driving_first'];
        }
    }
    if($time>$fee['night_driving_second_time_start']&&$time<$fee['night_driving_second_time_end']){  //夜间行车第一时段  02:00:00-06:00:00
        $STAR_FEE['fee'] = $STAR_FEE['fee']+$fee['night_driving_second'];
        $STAR_FEE['night_driving_second'] = $fee['night_driving_second'];
    }
    $x = remote_order($lat,$lng);
    if($x===FALSE){ //不在里面了，出城了
        //标准时段出城   +3
        $STAR_FEE['fee'] = $STAR_FEE['fee']+$fee['out_town'];
        $STAR_FEE['out_town'] = $fee['out_town'];
        //第一时段出城   23:00 - 02:00  +1
        if($fee['out_town_first_time_start']>$fee['out_town_first_time_end']){
            if($time<$fee['out_town_first_time_end']||$time>$fee['out_town_first_time_start']){
                $STAR_FEE['fee'] = $STAR_FEE['fee']+$fee['out_town_first_add'];
                $STAR_FEE['out_town'] = $STAR_FEE['out_town']+$fee['out_town_first_add'];
            }
        }else{
            if($time>$fee['out_town_first_time_start']&&$time<$fee['out_town_first_time_end']){
                $STAR_FEE['fee'] = $STAR_FEE['fee']+$fee['out_town_first_add'];
                $STAR_FEE['out_town'] = $STAR_FEE['out_town']+$fee['out_town_first_add'];
            }
        }
        //第二时段出城   02:00 - 06:00   +2
        if($time>$fee['out_town_second_time_start']&&$time<$fee['out_town_second_time_end']){
            $STAR_FEE['fee'] = $STAR_FEE['fee']+$fee['out_town_second_add'];
            $STAR_FEE['out_town'] = $STAR_FEE['out_town']+$fee['out_town_second_add'];
        }
        //公里数超过N公里的出城+价   23:00 - 06:00  8公里  +5
        if($all_kilometer>$fee['out_town_killmeter']){
            if($fee['out_town_killmeter_time_start']>$fee['out_town_killmeter_time_end']){
                if($time<$fee['out_town_killmeter_time_end']||$time>$fee['out_town_killmeter_time_start']){  
                    $STAR_FEE['fee'] = $STAR_FEE['fee']+$fee['out_town_killmeter_add'];
                    $STAR_FEE['out_town'] = $STAR_FEE['out_town']+$fee['out_town_killmeter_add'];
                }
            }else{
                if($time>$fee['out_town_killmeter_time_start']&&$time<$fee['out_town_killmeter_time_end']){ 
                    $STAR_FEE['fee'] = $STAR_FEE['fee']+$fee['out_town_killmeter_add'];
                    $STAR_FEE['out_town'] = $STAR_FEE['out_town']+$fee['out_town_killmeter_add'];
                }
            }
        }
        
    }else{  //加钱区
       $addarr1 = array(3,4,5,10,14);  //第一段区域
       $addarr2 = array(11,12,13,15);  //第二段区域
       if(in_array($x, $addarr1)){
            $STAR_FEE['fee'] = $STAR_FEE['fee']+$fee['edge_town'];
            $STAR_FEE['edge_town'] = $fee['edge_town'];
       }elseif (in_array($x, $addarr2)) {
            $STAR_FEE['fee'] = $STAR_FEE['fee']+$fee['edge_town_second'];
            $STAR_FEE['edge_town'] = $fee['edge_town_second'];
        }
    }
    if($fee['bad_weather']!="0.00"){  //恶劣天气加价
        $STAR_FEE['fee'] = $STAR_FEE['fee']+$fee['bad_weather'];
        $STAR_FEE['bad_weather'] = $fee['bad_weather'];
    }
    if($fee['other']!="0.00"){  //其他类型加价
        $STAR_FEE['fee'] = $STAR_FEE['fee']+$fee['other'];
        $STAR_FEE['other'] = $fee['other'];
    }
    //将上次的加钱区与本次区分别与上一次做对比。  哪个大，用哪个。
    if($start_fee_list['fee']>$STAR_FEE['fee']){
        $STAR_FEE = $start_fee_list;
    }
    return $STAR_FEE;
}
function getStartFee_cangzhou($lat,$lng,$orderstart,$all_kilometer,$start_fee_list) {   //$orderstart  上车时间    //上一次访问时的加价项
    $fee = S("billing_c");
    $STAR_FEE['fee'] = $fee['start_fee'];
    $STAR_FEE['start_fee'] = $fee['start_fee'];
    
    $time = date("H:i:s",$orderstart);
    if($time>$fee['early_peak_time_start']&&$time<$fee['early_peak_time_end']){  //早高峰  06:30:00-08:00:00
        $STAR_FEE['fee'] = $STAR_FEE['fee']+$fee['early_peak'];
        $STAR_FEE['early_peak'] = $fee['early_peak'];
    }
    if($time>$fee['late_peak_time_start']&&$time<$fee['late_peak_time_end']){  //晚高峰  17:30:00-19:00:00
        $STAR_FEE['fee'] = $STAR_FEE['fee']+$fee['late_peak'];
        $STAR_FEE['late_peak'] = $fee['late_peak'];
    }
    if($fee['bad_weather']!="0.00"){  //恶劣天气加价
        $STAR_FEE['fee'] = $STAR_FEE['fee']+$fee['bad_weather'];
        $STAR_FEE['bad_weather'] = $fee['bad_weather'];
    }
    if($fee['other']!="0.00"){  //其他类型加价
        $STAR_FEE['fee'] = $STAR_FEE['fee']+$fee['other'];
        $STAR_FEE['other'] = $fee['other'];
    }
    return $STAR_FEE;
}
function nopublic_orderpay_info_creat($start_fee_list,$openid) {
    $info = "";
    foreach ($start_fee_list as $key => $value) {
        switch ($key) {
            case "fee":
                $kname = "总计";
                break;
            case "start_fee":
                $kname = "起步价";
                break;
            case "early_peak":
                $kname = "早高峰加价";
                break;
            case "late_peak":
                $kname = "晚高峰加价";
                break;
            case "night_driving_first":
                $kname = "夜间加价";
                break;
            case "night_driving_second":
                $kname = "夜间加价";
                break;
            case "out_town":
                $kname = "出城加价";
                break;
            case "edge_town":
                $kname = "加钱区加价";
                break;
            case "bad_weather":
                $kname = "恶劣天气加价";
                break;
            case "other":
                $kname = "其他加价";
                break;
            
            default:
                break;
        }
        if($key!="fee"){
            $info = $info."".$kname."".$value."元，";
        }
    }
    return $info;
}
function getMileage($kilometer) {
    $fee = S("billing_n");
    if($kilometer<=2){
        $MILEAGE_FEE = 0;
    }
    if($kilometer>2&&$kilometer<=5){
        $MILEAGE_FEE = ceil(($kilometer-2))*$fee['first_mileage'];
    }
    if($kilometer>5){
        $first = (5-2)*$fee['first_mileage'];
        $MILEAGE_FEE = $first + ceil(($kilometer-5))*$fee['second_mileage'];
    }
    return $MILEAGE_FEE;
}
function getMileage_cangzhou($kilometer) {
    $fee = S("billing_c");
    $time = date("H:i:s",NOW_TIME);
    if($fee['night_driving_first_time_start']>$fee['night_driving_first_time_end']){//如果 第一时段的起始是深夜（22:00:00,23:00:00一类的）  结束时间是 05:00:00一类的
        if($time<$fee['night_driving_first_time_end']||$time>$fee['night_driving_first_time_start']){  //夜间行车第一时段  23:00:00-02:00:00
            $fee['first_mileage'] = $fee['first_mileage']+$fee['night_driving_milladd'];
            $fee['second_mileage'] = $fee['second_mileage']+$fee['night_driving_milladd'];
        }
    }else{  //起始是 凌晨（01:00:00, 04:00:00）一类的。
        if($time>$fee['night_driving_first_time_start']&&$time<$fee['night_driving_first_time_end']){  //夜间行车第一时段  00:00:00-03:00:00
            $fee['first_mileage'] = $fee['first_mileage']+$fee['night_driving_milladd'];
            $fee['second_mileage'] = $fee['second_mileage']+$fee['night_driving_milladd'];
        }
    }
    if($kilometer<=2){
        $MILEAGE_FEE = 0;
    }
    if($kilometer>2&&$kilometer<=6){
        $MILEAGE_FEE = round((($kilometer-2)*$fee['first_mileage']),1);
    }
    if($kilometer>6){
        $first = round(((6-2)*$fee['first_mileage']),1);
        $MILEAGE_FEE = $first + round((($kilometer-6)*$fee['second_mileage']),1);
    }
    return $MILEAGE_FEE;
}
function getTimeFee_cangzhou($Fee_starttime) {
    $fee = S("billing_c");
    $spilt_time = NOW_TIME-$Fee_starttime;
    if($spilt_time>300){
        $TimeFee = ceil(($spilt_time-300)/300)*$fee['duration']*5;
    }else{
        $TimeFee = 0;
    }
    return $TimeFee;
}
function upuser_state($u_id,$state) {
    $where['id'] = $u_id;
    $up = M("s_user")->where($where)->save(array("state"=>$state));
    return $up;
}
function thorough_order($o_id,$s_lat,$s_lng,$d_lat,$d_lng) {
    $data['o_id'] = $o_id;
    $data['s_lat'] = $s_lat;
    $data['s_lng'] = $s_lng;
    $data['d_lat'] = $d_lat;
    $data['d_lng'] = $d_lng;
    $data['distance'] = round((getdistance($s_lng, $s_lat, $d_lng, $d_lat)/1000),2);
    $add = M("b_order_site")->add($data);
    return $add;
}
function thorough_ordertime_up($o_id,$on_start_time,$on_end_time) {
    
    if(!empty($on_start_time)){
        $data['on_start_time'] = $on_start_time;
    }
    if(!empty($on_end_time)){
        $data['on_end_time'] = $on_end_time;
    }
    $data['wait_time'] = $data['on_end_time']-$data['on_start_time'];
    $add = M("b_order_site")->where(array("o_id"=>$o_id))->save($data);
    return $add; 
}
function thorough_ordersite_up($o_id,$fee_lat,$fee_lng,$billing_start_time) {
    if(!empty($fee_lat)){
        $data['start_lat'] = $fee_lat;
    }
    if(!empty($fee_lng)){
        $data['start_lng'] = $fee_lng;
    }
    if(!empty($billing_start_time)){
        $data['billing_start_time'] = $billing_start_time;
    }
    $add = M("b_order_site")->where(array("o_id"=>$o_id))->save($data);
    return $add;
}
function thorough_ordersite_end_up($o_id,$end_lat,$end_lng,$billing_start_time,$billing_end_time) {
    if(!empty($end_lat)){
        $data['end_lat'] = $end_lat;
    }
    if(!empty($end_lng)){
        $data['end_lng'] = $end_lng;
    }
    if(!empty($billing_start_time)){
        $data['billing_start_time'] = $billing_start_time;
    }
    if(!empty($billing_end_time)){
        $data['billing_end_time'] = $billing_end_time;
    }
    $add = M("b_order_site")->where(array("o_id"=>$o_id))->save($data);
    return $add;
}
function yingyan_getdistance($entity_name,$start_time,$end_time) {
    $data['ak'] = "ocEMU4U8fte3ysyRuTwzYoy6QACcF3sY";
    $data['service_id'] = "215060";
    $data['entity_name'] = $entity_name;
    $data['start_time'] = $start_time;
    $data['end_time'] = $end_time;
    $data['is_processed'] = "1";
    $data['process_option'] = "need_denoise=1,radius_threshold=100,need_mapmatch=1,transport_mode=driving";
    $data['supplement_mode'] = "driving";
    $url = "http://yingyan.baidu.com/api/v3/track/getdistance";
    foreach ($data as $key => $value) {
        if($key=="ak"){
            $url = $url."?{$key}={$value}";
        }  else {
            $url = $url."&{$key}={$value}";
        }
    }
    $json = curl_get($url);
    $arr = json_decode($json,TRUE);
    return $arr;
}
function yingyan_gettrack($entity_name,$start_time,$end_time) {
    $data['ak'] = "ocEMU4U8fte3ysyRuTwzYoy6QACcF3sY";
    $data['service_id'] = "215060";
    $data['entity_name'] = $entity_name;
    $data['start_time'] = $start_time;
    $data['end_time'] = $end_time;
    $data['is_processed'] = "1";
    $data['process_option'] = "need_denoise=1,radius_threshold=100,need_mapmatch=1,transport_mode=driving,vacuate_precision=0";
    $data['supplement_mode'] = "driving";
//    $data['vacuate_precision'] = 500;
    $url = "http://yingyan.baidu.com/api/v3/track/gettrack";
    foreach ($data as $key => $value) {
        if($key=="ak"){
            $url = $url."?{$key}={$value}";
        }  else {
            $url = $url."&{$key}={$value}";
        }
    }
    $json = curl_get($url);
    $arr = json_decode($json,TRUE);
    return $arr;
}
function yingyan_nowpoint($entity_name,$coord_type_output) {  //拉取实时位置 bd09ll  gcj02
    $data['ak'] = "ocEMU4U8fte3ysyRuTwzYoy6QACcF3sY";
    $data['service_id'] = "215060";
    $data['entity_name'] = $entity_name;
    $data['process_option'] = "need_denoise=1,radius_threshold=100,need_mapmatch=1,transport_mode=driving";
    $data['coord_type_output'] = $coord_type_output;
    
    $url = "http://yingyan.baidu.com/api/v3/track/getlatestpoint";
    foreach ($data as $key => $value) {
        if($key=="ak"){
            $url = $url."?{$key}={$value}";
        }  else {
            $url = $url."&{$key}={$value}";
        }
    }
    $json = curl_get($url);
    $arr = json_decode($json,TRUE);
    return $arr;
}
function yingyan_Upload($point_list) {//上传坐标点
    $data['ak'] = "ocEMU4U8fte3ysyRuTwzYoy6QACcF3sY";
    $data['service_id'] = "215060";
    $data['point_list'] = $point_list;
    $url = "http://yingyan.baidu.com/api/v3/track/addpoints";
    foreach ($data as $key => $value) {
        if($key=="ak"){
            $url = $url."?{$key}={$value}";
        }  else {
            $url = $url."&{$key}={$value}";
        }
    }
//    dump($data);
//        exit();
    $json = curl_post($url,$data);
    $arr = json_decode($json,TRUE);
    return $arr;
}
function colfee_ret_fuc($o_id,$d_id,$entity_name) {
    $order_time = S("order_fee_{$o_id}");
    $site = S("drive_{$d_id}");  //这个是当前位置
    $fee = S("billing_n");
    $res = yingyan_getdistance($entity_name, $order_time['active_time'], NOW_TIME);
    if($res['status']==0){  //成功了
        $ALL_KILOMETER = round(($res['distance']/1000),1);
    }  else {
        $ALL_KILOMETER = $order_time['distan'];  //接口距离失败，用上一个距离
    }
    $STAR_FEE = getStartFee($site['lat'],$site['lng'],$order_time['active_time'],$ALL_KILOMETER,$order_time['start_fee_list']);  //获取起步价  $STAR_FEE['fee']
    $MILEAGE_FEE = getMileage($ALL_KILOMETER);        
    
    $cost['start_fee_list'] = $STAR_FEE;
    $cost['start_fee'] = $STAR_FEE['fee'];
    $cost['distan'] = $ALL_KILOMETER;
    $cost['mileage_fee'] = $MILEAGE_FEE;
    $cost['duration_fee'] = 0;
    $cost['total'] = $STAR_FEE['fee']+$MILEAGE_FEE;
    
    $order_time['start_fee_list'] = $cost['start_fee_list'];   //更新订单缓存的距离字段
    $order_time['distan'] = $cost['distan'];   //更新订单缓存的距离字段
    $order_time['mileage_fee'] = $cost['mileage_fee'];   //更新订单缓存的距离字段
    $order_time['duration_fee'] = $cost['duration_fee'];   //更新订单缓存的距离字段
    $order_time['total'] = $cost['total'];   //更新订单缓存的距离字段
    S("order_fee_{$o_id}",$order_time);
    return $cost;
}
function colfee_ret_cangzhou($o_id,$d_id,$entity_name) {
    $order_time = S("order_fee_{$o_id}");
    $site = S("drive_{$d_id}");  //这个是当前位置
    $fee = S("billing_c");
    $res = yingyan_getdistance($entity_name, $order_time['active_time'], NOW_TIME);
    if($res['status']==0){  //成功了
        $ALL_KILOMETER = round(($res['distance']/1000),1);
    }  else {
        $ALL_KILOMETER = $order_time['distan'];  //接口距离失败，用上一个距离
    }
    $STAR_FEE = getStartFee_cangzhou($site['lat'],$site['lng'],$order_time['active_time'],$ALL_KILOMETER,$order_time['start_fee_list']);  //获取起步价  $STAR_FEE['fee']
    $MILEAGE_FEE = getMileage_cangzhou($ALL_KILOMETER);
    $TimeFee = getTimeFee_cangzhou($order_time['active_time']);
    
    $cost['start_fee_list'] = $STAR_FEE;
    $cost['start_fee'] = $STAR_FEE['fee'];
    $cost['distan'] = $ALL_KILOMETER;
    $cost['mileage_fee'] = $MILEAGE_FEE;
    $cost['duration_fee'] = $TimeFee;
    $cost['total'] = $STAR_FEE['fee']+$MILEAGE_FEE+$TimeFee;
    
    $order_time['start_fee_list'] = $cost['start_fee_list'];   //更新订单缓存的距离字段
    $order_time['distan'] = $cost['distan'];   //更新订单缓存的距离字段
    $order_time['mileage_fee'] = $cost['mileage_fee'];   //更新订单缓存的距离字段
    $order_time['duration_fee'] = $cost['duration_fee'];   //更新订单缓存的距离字段
    $order_time['total'] = $cost['total'];   //更新订单缓存的距离字段
    S("order_fee_{$o_id}",$order_time);
    return $cost;
}
/*
 *订单ID
 *司机ID 
 *entityID
 *开始计费的时间
 *  */
function end_order_fee($o_id,$d_id,$entity_name,$start_fee_time) {
    $fee = M("m_billing_n")->find();
    $entity_name = "entity_name_".$d_id;
    $res = yingyan_getdistance($entity_name,$start_fee_time, NOW_TIME);
}
function jsapi_pay_nopublic($openId,$ordernum,$fee) {   //统一下单api接口
    Vendor('WxAlipay.Wx_pay_Nopublic.WxPayJsApiPay');
    Vendor('WxAlipay.Wx_pay_Nopublic.WxPayConfig');
    Vendor('WxAlipay.Wx_pay_Nopublic.lib.WxPayApi');
    $tools = new JsApiPay();
    $openId = $openId;
    //②、统一下单
    $input = new WxPayUnifiedOrder();
//    $input->SetNonce_str("test");  //随机串  系统帮助生成了  
//    $input->SetSpbill_create_ip("sign");  //ip，系统帮助生成了
//    $input->SetTime_start(date("YmdHis"));   //交易起始时间
//    $input->SetTime_expire(date("YmdHis", time() + 600));  //交易失效时间
//    $input->SetGoods_tag("test");     //订单优惠标记
    
//    $input->SetAppid("wx73e0cea3e01d21f6");  //APPID    在WxPayConfig.php里
//    $input->SetMch_id("1533419931");  //商户号      在WxPayConfig.php里

//    $input->SetSign("sign");  //签名  系统帮助生成了

    $input->SetBody("打车车费");  //商品简单描述
    $input->SetAttach("test");   //附加的自定义参数，会在回调中原样返回
    $input->SetOut_trade_no($ordernum);   //订单号  测试时先用时间戳代替  $ordernum
    $input->SetTotal_fee($fee);      //金额 分
    $input->SetNotify_url("https://www.taxisanjiayi.com/User/order_callback");
    $input->SetTrade_type("JSAPI");   //固定的
    $input->SetOpenid($openId);

    $config = new WxPayConfig();
    $order = WxPayApi::unifiedOrder($config, $input);
//    return $order;
//    exit();
    $jsApiParameters = $tools->GetJsApiParameters($order);   //这个可以直接返回给前台
    return $jsApiParameters;
    
//    //获取共享收货地址js函数参数
//    $editAddress = $tools->GetEditAddressParameters();
}
function jsapi_pay_app($notify,$ordernum,$fee) {   //统一下单api接口,司机端
    Vendor('WxAlipay.Wx_pay_App.WxPayJsApiPay');  //需要改信息
    Vendor('WxAlipay.Wx_pay_App.WxPayConfig');    //需要改信息
    Vendor('WxAlipay.Wx_pay_App.lib.WxPayApi');   //需要改信息
    $tools = new JsApiPay();
    $openId = $openId;
    //②、统一下单
    $input = new WxPayUnifiedOrder();
//    $input->SetNonce_str("test");  //随机串  系统帮助生成了  
//    $input->SetSpbill_create_ip("sign");  //ip，系统帮助生成了
//    $input->SetTime_start(date("YmdHis"));   //交易起始时间
//    $input->SetTime_expire(date("YmdHis", time() + 600));  //交易失效时间
//    $input->SetGoods_tag("test");     //订单优惠标记
    
//    $input->SetAppid("wx73e0cea3e01d21f6");  //APPID    在WxPayConfig.php里
//    $input->SetMch_id("1533419931");  //商户号      在WxPayConfig.php里

//    $input->SetSign("sign");  //签名  系统帮助生成了

    $input->SetBody("司机续费");  //商品简单描述
    $input->SetAttach($notify);   //附加的自定义参数，会在回调中原样返回  //这里 附加的是d_id
    $input->SetOut_trade_no($ordernum);   //订单号  测试时先用时间戳代替  $ordernum
    $input->SetTotal_fee($fee);      //金额 分
    $input->SetNotify_url("https://www.taxisanjiayi.com/Appdata/app_notify");
    $input->SetTrade_type("APP");   //固定的
//    $input->SetOpenid($openId);

    $config = new WxPayConfig();
    $order = WxPayApi::unifiedOrder($config, $input);
    
    $jsApiParameters = $tools->GetJsApiParameters($order);   //这个可以直接返回给前台
    return $jsApiParameters;
}
function jsapi_pay_applets($openId,$ordernum,$fee) {
    Vendor('WxAlipay.Wx_pay_Applets.WxPayJsApiPay');
    Vendor('WxAlipay.Wx_pay_Applets.WxPayConfig');
    Vendor('WxAlipay.Wx_pay_Applets.lib.WxPayApi');
    $tools = new JsApiPay();
    $openId = $openId;
    //②、统一下单
    $input = new WxPayUnifiedOrder();
//    $input->SetNonce_str("test");  //随机串  系统帮助生成了  
//    $input->SetSpbill_create_ip("sign");  //ip，系统帮助生成了
//    $input->SetTime_start(date("YmdHis"));   //交易起始时间
//    $input->SetTime_expire(date("YmdHis", time() + 600));  //交易失效时间
//    $input->SetGoods_tag("test");     //订单优惠标记
    
//    $input->SetAppid("wx73e0cea3e01d21f6");  //APPID    在WxPayConfig.php里
//    $input->SetMch_id("1533419931");  //商户号      在WxPayConfig.php里

//    $input->SetSign("sign");  //签名  系统帮助生成了

    $input->SetBody("打车车费");  //商品简单描述
    $input->SetAttach("test");   //附加的自定义参数，会在回调中原样返回
    $input->SetOut_trade_no($ordernum);   //订单号  测试时先用时间戳代替  $ordernum
    $input->SetTotal_fee($fee);      //金额 分
    $input->SetNotify_url("https://www.taxisanjiayi.com/Order/order_callback");
    $input->SetTrade_type("JSAPI");   //固定的
    $input->SetOpenid($openId);

    $config = new WxPayConfig();
    $order = WxPayApi::unifiedOrder($config, $input);
    $jsApiParameters = $tools->GetJsApiParameters($order);   //这个可以直接返回给前台
    return $jsApiParameters;
    
//    //获取共享收货地址js函数参数
//    $editAddress = $tools->GetEditAddressParameters();
}
function auto2_Entrance($publicid){   //判断 用户是从哪个号进的
    switch ($publicid) {
        case "gh_78052d300081":  //支付服务号
            $wxinfo['appid'] = C("FW_appid");
            $wxinfo['secret'] = C("FW_secret");
            break;
        case "gh_e7e54e5ab903":   //宝坻订阅号
            $wxinfo['appid'] = C("DY1_appid");
            $wxinfo['secret'] = C("DY1_secret");
            break;
        case "gh_008a50db02b8":   //测试服务号
            $wxinfo['appid'] = C("CS_appid");
            $wxinfo['secret'] = C("CS_secret");
            break;
        default:
            break;
    }
    return $wxinfo;
}
/**
 * 从15s司机待接单缓冲区中移除司机
 * @param integer $driverId 司机ID
 * @return boolean 成功true,失败
 */
function removeDriverFromAwaitPoints($driverId){
	$areaId = M("s_driver")->where("id = $driverId")->field("address_id")->find()["address_id"];
	$redis = new Redis();
	$redis->connect("127.0.0.1","6379");
	$redis->auth("sanjiayi@3+1.com");
	$redis->select (0);
	if($redis->zRem ("await_driver_points" . "_$areaId", $driverId )){
		$redis->close();
		return true;
	}
	$redis->close();
	return false;
}
/**
 * 取消正式订单
 * $d_id 司机ID
 * $ascription  取消者（user/admin） 
 * $o_id 订单ID
 * $r_id 用户取消原因ID  不传默认为0
 * $canceltext 后台管理员取消原因  不传默认为''
 */
function cancel_order($d_id,$ascription,$o_id,$r_id=0,$canceltext=''){
        $m = M();
        $m->startTrans();   //开启事务
        $Rea_id = $r_id;
        switch ($ascription) {
            case "admin":
                $where['b_order.id'] = $o_id;
                $where['b_order.state'] = array("in","new,on,active,wait_pay");
                break;
            case "user":
                $where['b_order.id'] = $o_id;
                $where['b_order.state'] = array("in","new,on,arrived");  //不能带wait_pay。 因为wait_pay不能取消
                break;
            default:
                break;
        }
        $sql = <<<SQL
            select b_order.id o_id,b_order.u_id,b_order.d_id 
                from b_order 
                %WHERE%
SQL;
        $res = M()->where($where)->query($sql,TRUE);
        if(!empty($res)){  //取消
            $upo = M("b_order")->where(array("id"=>$res[0]['o_id']))->save(array("state"=>"cannal","cancel_id"=>$Rea_id,"d_confirmation"=>'Y','cancel_text'=>$canceltext));
            $up = driver_state_up($res[0]['d_id'],"off");  //这个可以不验证
            if($upo>0){
                destroy_cahe($res[0]['o_id']);//清理订单缓存
                S("seek_order_{$res[0]['d_id']}",NULL);  //清除司机叫单缓存
                $state = 'N';
                upuser_state($res[0]['u_id'], $state);
                $m->commit();
                $msg['error_code'] = 0;
                $msg['message'] = "OK";
            }else{
                $m->rollback();
                $msg['error_code'] = -1;
                $msg['message'] = "司机";
            }
            return $msg;
        }else{  //不允许取消
            $msg['error_code'] = 1;
            $msg['message'] = "该订单不允许取消";
            return $msg;
        }
}

/**
 * 缓存发布
 * @param integer $driverId 司机ID
 * @param array $message 消息数组
 * @param integer $interval 重发间隔
 * @param integer $counts 重发次数
 */
function redis_publish($driverId,$message,$interval,$counts){
    $msg = [
        "driverId" => $driverId,
        "type" => $message['type'],
        "message" => json_encode($message),
        "interval" => $interval,
        "counts" => $counts,
    ];
    curl_post("http://127.0.0.1:2121",$msg);
    
}
