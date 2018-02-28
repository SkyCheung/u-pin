<?php
if (!defined('in_mx')) {exit('Access Denied');}
header('Cache-control: private, must-revalidate');
session_cache_limiter('private, must-revalidate');
session_start();
$url = $_SERVER["HTTP_REFERER"];
$php_pre = "<?php if (!defined('in_mx')) {exit('Access Denied');}".PHP_EOL;
$ym_lang='zh_cn';
define('tpl_C', cache.'admin/');
define('adm_tpl', './view/admin');
$images=tpl."admin/images";
require('./inc/lang/'.$ym_lang.'/com.php');
require('./inc/function/functions_admin.php');
require('./inc/lib/admin/goods.php');
require('./inc/lib/admin/user.php');
require('./inc/lib/admin/order.php');
require('./inc/lib/admin/perm.php');

dbc();

$adminname = isset($_COOKIE["adminname"])? $_COOKIE["adminname"]:"";
if ($_GET["do"]=="login"){require("./inc/admin_inc/page/login.php");exit();} //登录提交
if (!isset($_SESSION['lg_id']) || intval($_SESSION['lg_id'])==0){include template("login","admin/");exit();}

$login_id=intval($_SESSION["lg_id"]);

$dosite = explode(".", $do);

if(!isset($ym_name))
{
	if(!file_exists(cache_data))
	{
		mdir(cache_data);
	}
	update_config();
}
@include(Webfile); 

$max_whh=explode(",",$max_wh);
$max_w=intval($max_whh[0])==0?1100:intval($max_whh[0]);
$max_h=intval($max_whh[1])==0?4600:intval($max_whh[1]);

if (trim($_POST["action"])!="" ){
	if (file_exists("./inc/admin_inc/post/".trim($_POST["action"]).".php")){
		require("./inc/admin_inc/post/".trim($_POST["action"]).".php");
		exit();
	}else{
		message("操作错误。",$url);
	}
}
$do=trim($_GET["do"])==""?"home":trim($_GET["do"]);
if (file_exists("./inc/admin_inc/page/".$do.".php") ){
	require("./inc/admin_inc/page/".$do.".php");
} 

@include template($do,"admin/");

?>