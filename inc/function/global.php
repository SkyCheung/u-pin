<?php

/*云EC系统统一入口、初始化数据*/

ob_start();
Header('Content-type: text/html; charset=UTF-8');
date_default_timezone_set ('Etc/GMT-8');
error_reporting(E_ALL ^ E_NOTICE);

function customError($errno, $errstr, $errfile, $errline)
{ 
	echo "<b>Error number:</b> [$errno],error on line $errline in $errfile<br />";
	die();
}
set_error_handler("customError",E_ERROR);
$getfilter="\\b(and|or)\\b.+?(>|<|=|in|like)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
$postfilter="\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";
$cookiefilter="\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";

function StopAttack($StrFiltKey,$StrFiltValue,$ArrFiltReq){  
	if(is_array($StrFiltValue))
	{
		$StrFiltValue=implode($StrFiltValue);
	}		
	if (preg_match("/".$ArrFiltReq."/is",urldecode($StrFiltValue))){
			print "网址有误~";
			exit();
	}      
}  

foreach($_GET as $key=>$value){
	StopAttack($key,$value,$getfilter);
}
if ($_GET["p"]!=='admin'){
	foreach($_POST as $key=>$value){ 
		StopAttack($key,$value,$postfilter);
	}
}

foreach($_COOKIE as $key=>$value){ 
	StopAttack($key,$value,$cookiefilter);
}

function slog($logs)
{
  $toppath=$_SERVER["DOCUMENT_ROOT"]."/log.htm";
  $Ts=fopen($toppath,"a+");
  fputs($Ts,$logs."\r\n");
  fclose($Ts);
}

define('Webfile', './config/shop_config.php'); //配置文件
$hosturl="http://".$_SERVER['HTTP_HOST']."/";
if($p !='install')
{	
	if(!file_exists('./config/yec.lock'))
	{		 
		if(!@fopen($hosturl."install.html", "r"))
		{
			die('<b>本系统安装使用前，请先配置伪静态规则！请仔细阅读安装包里的 readme.txt 安装说明。</b><br><br>可能的原因有：1、伪静态规则未配置<br>2、商城程序文件必须放于站点根目录<br>3、域名未解析');	
		}
		header("Location:install.html"); 
		exit();
	}else{
		require("./config/config.php"); //加载配置文件
		include(Webfile); //加载数据缓存文件	
	}
}else{
	require("./config/config_default.php"); //加载配置文件
}
 
if($ym_is_https==1 && $_SERVER["HTTPS"] != 'on' && $p!='admin')
{
	header("Location:https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
}

require('./inc/class/template.php');
require("./inc/class/db_mysql_class.php");
require('./inc/function/const.php');
require('./inc/function/common.php');
require './inc/lib/goods.php';
require("./inc/lib/news.php");
require("./inc/lib/order.php");
require("./inc/lib/users.php");

if (!empty($_GET)){ foreach($_GET AS $key => $value) $$key = $value; }
if (!empty($_POST)){ foreach($_POST AS $key => $value) $$key = $value; }
 
$ym_client = get_client(); //客户端类型
if($ym_client == client_app)
{
	$ym_tpl = $ym_app_tpl;
}
elseif($domain == $ym_m_siteurl || $ym_client == client_m)
{
	$ym_tpl = $ym_m_tpl;
}
else {
	$ym_tpl = $ym_pc_tpl;
}

$ym_weixin = is_weixin();//是否微信内
 
$ym_fullurl = get_fullurl(); //完整url
$ym_url_path = explode("/", rtrim(ltrim($_SERVER['REQUEST_URI'],'/'),"/")); 
$ym_url = ($ym_is_https ? HTTPS : HTTP).$ym_siteurl."/";  //  网址 
$ym_htmlext = trim($ym_htmlext)=='' ? '.html':$ym_htmlext;
define('tpl_root', './view/'.$ym_tpl."/");

if($p !='admin') { 	
	$exitcon='<center><h1>404 Not Found</h1></center><hr><center>nginx/1.7.5</center>';	
	
	define('tpl_C', cache);
	define('uploadir', '/upload/');
	$images=tpl.$ym_tpl."/images";
	$js=tpl.$ym_tpl."/js";
	$css=tpl.$ym_tpl."/css";
		
	$furl=trim($_SERVER["HTTP_REFERER"])=="" ? "/" : $_SERVER["HTTP_REFERER"];

	if($ym_isclosed==1 && $p != 'vcode-M')
	{
		die('<p style="text-align:center; margin:200px auto;">'.$ym_closedreason.'<p>');
	} 
	
	if($ym_url_path[0] ==='static')
	{
		exit();
	}
	require './inc/function/functions.php';
	if(!empty($ym_sid) && !isset($_SESSION['uid'])) //来自app   todo需要绑定设备码
	{
		$ym_sid = endecrypt(trim($ym_sid), 'DECODE', ym_token);
	    if(file_exists(session_save_path().DIRECTORY_SEPARATOR."sess_".trim($ym_sid)))
	    {
			session_destroy();
			session_id($ym_sid);
			session_start();
	    }
	 	else
		{
			dbc();
			$res = check_user_token($ym_sid);
	 	}
	}
	
	$ym_uname = get_username();
	$ym_uid = get_userid();
	$ym_cnum = get_cart_amount();
	
	//微信内自动登陆
	if($ym_weixin && $ym_uid==0 && !isset($_COOKIE['ym_logout']) && !isset($_SESSION['wx_autologin_fail']) && $act !='callback' && !isset($_SESSION['wx_autologin']))
	{
		$_SESSION['wx_autologin'] = 1;
		require_once plugin.'oauth/wx/wxoauth.php';
		$WX = new wxoauth();
		$WX->login(true, $ym_client);		
	}

}
 

?>