<?php
if (!defined('in_mx')) {exit('Access Denied');}

if(intval($_SESSION['uid']) !=0){
	redirect("index.html");
}

$nav_footer = get_nav('bot');

$db=dbc();
$row = get_oauth(); //第三方登录列表
$oauth_userinfo = json_decode($_SESSION['oauth_userinfo'], true);

if($return_url){$return_url_encode=urlencode($return_url);}

@include template("login",$ym_tpl."/");
exit();
?>