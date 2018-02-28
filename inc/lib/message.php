<?php
if (!defined('in_mx')) {exit('Access Denied');}
session_cache_limiter('private');
session_start();

$code='message';
$subcode='message';



if ($_SESSION['vcode']==''){
	$con='<center style="font-size:14px;color:red;"><a href="javascript:history.go(-1)" style="color:red;font-size:16px;">'.$ln_message_err06.'</a></center>';
	@include template("page",$ym_tpl."/");
	die();
}

if (trim($vcode)==""){
	$con='<center style="font-size:14px;color:red;"><a href="javascript:history.go(-1)" style="color:red;font-size:16px;">'.$ln_message_err04.'</a></center>';
	@include template("page",$ym_tpl."/");
	die();
}


if (trim($yourname)==""){
	$con='<center style="font-size:14px;color:red;"><a href="javascript:history.go(-1)" style="color:red;font-size:16px;">'.$ln_errorcon.'</a></center>';
	@include template("page",$ym_tpl."/");
	die();
}



if (strtolower($_SESSION['vcode'])==trim(strtolower($vcode))){
	dbc();
	$ipsss=getip();
	$ipaddress=iconv("GBK", "UTF-8", showip($ipsss));

	$db->insert('message', array('c_id' => intval($user_id),'c_sid' => intval($sid),'c_username' => trim($yourname),'c_company' => trim($yourcompany), 'c_phone' => ptitle($yourphone) , 'c_email' => ptitle($youremail) ,'c_body' => $c_body , 'c_ip' => ptitle($ipsss) , 'c_ipaddress' => ptitle($ipaddress) ,'c_time' =>time()));
	$lastid=$db->lastinsertid();
	$tempcode12252= jcode($lastid);
	$db->update('message', array('c_code' =>$tempcode12252), array('id' => $lastid));
	$con='<center style="font-size:16px;">'.$ln_success.'</center>';
	$_SESSION['vcode']='';





	if (trim($web_email)!=''){

		postmail($web_email,'网站留言通知.','您的网站有新的留言！请注意查收。<br><br> 请登录 '.$hosturl.'admin.html<br><br>
		
		地区：'.ptitle($ipaddress).' 电话：'.$yourphone.'   时间：'.ftime(time()).'  <br><br> 

		内容：'.$c_body.'

		');

	}


}else{
	$con='<center><a href="javascript:history.go(-1)" style="color:red;font-size:16px;">'.$ln_message_err07.'</a></center>';
}


@include template('page',$ym_tpl."/");
die();
?>