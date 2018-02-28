<?php
if (!defined('in_mx')) {exit('Access Denied');}
header('Cache-control: private, must-revalidate');
session_cache_limiter('private, must-revalidate');
session_start();
$url = $_SERVER["HTTP_REFERER"];
define('tpl_C', cache.'manage/');
$images=tpl."/admin/\$images";
require('./inc/function/functions_admin.php');
dbc();
if ($_POST["action"]=="login"){@require("./inc/manage_inc/post/login.php");exit();}
if ($_COOKIE["manage_user"]=="" || $_COOKIE["manage_id"]==""){include template("login","manage/");exit();}
$manage_user=intval($_COOKIE["manage_user"]);
$manage_id=intval($_COOKIE["manage_id"]);




if (intval($_SESSION["mpower"])==0){
	$llow = $db->fetch('manage', '*', array('id' => $manage_id), 'id DESC');
	if ($llow['lastip']==getip()){
		$_SESSION["manage_ok"]=$llow["id"];
		$_SESSION["mpower"]=$llow["c_power"];		
	}else{
		include template("login","manage/");
		exit();
	}
}
$mpower=intval($_SESSION["mpower"]);


if (Trim($_POST["action"])!="" ){ //&& file_exists("./inc/admin_inc/post/".Trim($_POST["action"]).".php")
	@require("./inc/manage_inc/post/".Trim($_POST["action"]).".php");
	exit();
}
$do=Trim($_GET["do"])==""?"home":trim($_GET["do"]);
if (file_exists("./inc/manage_inc/page/".$do.".php") ){
	require("./inc/manage_inc/page/".$do.".php");
}

@include template($do,"manage/");

?>