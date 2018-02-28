<?php
if (!defined('in_mx')) {exit('Access Denied');}
header("Pramga: no-cache"); 
ob_clean();
require("./inc/module/vcode/vcode2.php");

$width =isset($width) ? intval($width): 120;
$height =isset($height) ? intval($height): 40;
$counts =isset($counts) ? intval($counts): 4; 

if($ym_client == client_app && $act =='get_vcodesid')
{
	if(isset($ym_sid) && $ym_sid!='')
	{
		//session_destroy();
	 	session_id($ym_sid);
		session_start();
	
		die('vcode='.$_SESSION['vcode']);// die(session_id());
	}
		
	require("./inc/class/session.php");	
	$session = new ym_session();
	$sid = $session->get_session_id('vcode');
	die($sid);
}

Header("Content-type: image/GIF");
$imagecode = new  Imagecode($width, $height, $counts);
$imagecode->imageout();

exit;

?>