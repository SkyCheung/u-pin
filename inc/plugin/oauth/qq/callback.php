<?php
if (!defined('in_mx')) {exit('Access Denied');}

require_once(plugin."oauth/qq/API/qqConnectAPI.php");
$oauth_c = new QC();
$access_token = $oauth_c->qq_callback();
$openid = $oauth_c->get_openid(); 
if(empty($openid))
{
	session_start();
	unset($_SESSION['oauth_userinfo']);
	redirect("login.html");
}

dbc();

$oauth_c = new QC($access_token, $openid);
$res = oauth_login($oauth_c, 'qq', $openid);	
		
if($res['err'] !='')
{
	unset($_SESSION['oauth_userinfo']);
	$_SESSION['wx_autologin_fail'] = 1;
}
redirect("index.html");die();



/*$user = get_member_oauth('qq', $openid); 
if($user && count($user)>0 && $user['mobile'] !='') //已经绑定过，直接登录
{
	if($user['status'] == locked)
	{
	 	message("出于安全原因，系统已冻结您的账户，请与客服联系。", "login.html");
	}
	set_login_session($user['uid'], $user['uname'], 0);
	redirect("user.html");
}
else {
	session_start();
	$oauth_c = new QC($access_token, $openid);
	$res = $oauth_c->get_user_info(); //获取用户QQ信息
	$oauth_userinfo = array('openid'=>$openid);
	if($res['ret'] == 0){
		$oauth_userinfo['type'] ='qq';
		$oauth_userinfo['nickname']= $res['nickname'];
		$oauth_userinfo['avatar']= $res['figureurl_qq_1']; //头像 40x40
	}
	$_SESSION['oauth_userinfo'] = json_encode($oauth_userinfo);
	redirect("login.html");
}*/


?>	
