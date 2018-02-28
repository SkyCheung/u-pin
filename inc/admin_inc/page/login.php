<?php
if (!defined('in_mx')) {exit('Access Denied');}

require("./inc/lib/system.php");

$res = array('err' => '', 'res' => '', 'data' => array()); 
$c_user = isset($_GET["c_user"]) ? trim($_GET["c_user"]) :'';
$c_pwd = isset($_GET["c_pwd"]) ? trim($_GET["c_pwd"]) :'';
$vcode = isset($_GET["vcode"]) ? trim($_GET["vcode"]) :'';

if ($c_user==""){
	$res['err'] = "请输入用户名";
	die(json_encode($res));
}
if ($login_yes=="1") {
	SetCookie("checked",'checked="checked"',time()+31536000);
	SetCookie("adminname",$c_user,time()+31536000);
}else{
	SetCookie("checked","");
	SetCookie("adminname","");
}
if ($c_pwd==""){
	$res['err'] = "请输入密码";
	die(json_encode($res));
}
elseif (strlen($c_pwd)<3){
	$res['err'] = "密码不正确";
	die(json_encode($res));
}
if ($vcode==""){
	$res['err'] = "请输入验证码";
	die(json_encode($res));
}
 
if (strtolower($_SESSION['vcode'])!=trim(strtolower($vcode))){
	$_SESSION['vcode']=='';
 	$res['err'] = "验证码不正确！";
	die(json_encode($res));
}

$row = $db->fetch('user', '*', array('username' => trim(addslashes($c_user))), 'id DESC');
if (!$row){
	$res['err'] = "用户不存在或者密码错误";
	 
	die(json_encode($res));
}

$count = get_login_count($row['id'], role_admin);	
if($count>=6)
{
	$res['err']= "密码错误6次，请1小时后再试";
	die(json_encode($res));
}

if(encryptStr($c_pwd, $row["salt"]) !=trim($row["pwd"])){		
	
	$count= $count + 1;
	add_login_log($row['id'], role_admin, login_fail);
	if($count >=2)
	{
		$res['data']= array('authcode'=>'1'); 
	}
	if($count == 4 || $count == 5)
	{
		$err= "账户名与密码不匹配，您还可以尝试".(6-$count)."次";
	}
	elseif($count>=6)
	{
		$err= "密码错误6次，请1小时后再试";
	}
	else {
		$err= "用户不存在或者密码错误";
	}
	$res['err']= $err;
	die(json_encode($res));
}

session_cache_limiter('private');

function start_session($expire = 0) 
{ 
	if ($expire == 0) {
		$expire = ini_get('session.gc_maxlifetime');
	} else { 
		ini_set('session.gc_maxlifetime', $expire); 
	} 
	if (empty($_COOKIE['PHPSESSID'])) {
		session_set_cookie_params($expire); 
		session_start();
	} else {
		session_start();
		setcookie('PHPSESSID', session_id(), time() + $expire);
	} 
}
start_session(10800);

add_login_log($row['id'], role_admin, login_success);//登录日志
$db->update('user', array('lastlogintime'=>time(),'lastip'=>getip()), array('id'=>$row["id"]));//更新登录信息

$_SESSION['lg_id']= $row["id"];
SetCookie("adminname",$row["username"],time()+2678400,"/");
SetCookie("lg_id",$row["id"], 0 ,"/");
die(json_encode($res));

?>