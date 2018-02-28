<?php

$hostname=str_replace("www.","",$_SERVER['HTTP_HOST']);
$domain=$_SERVER['HTTP_HOST'];

define("approot",$_SERVER["DOCUMENT_ROOT"]); //网站根目录
define('tpl', '/view/');	//前台引用模板目录
define('tpldir', './view/');
define('upload', './upload/');	//上传目录
define('upload_brand', './upload/img/brand/');	//品牌
define('upload_cat', './upload/img/cat/');	//分类
define('upload_goods', './upload/img/goods/');	//商品
define('upload_comment', './upload/img/comment/');//评论
define('upload_service', './upload/img/service/'); //售后
define('upload_news', './upload/img/news/');	//新闻
define('upload_avatar', './upload/img/avatar/'); //头像
define('upload_banner', './upload/img/banner/'); //banner海报
define('upload_link', './upload/img/upload_link/'); //友情链接
define('upload_common', './upload/img/common/');	//通用
define('upload_tpldetails', './upload/tpl-details/');	//详情模板
define('upload_img', './upload/img/');	//图片上传根目录
define('cache', './cache/');	//缓存目录 
define('cache_data', './cache/data/');	//数据缓存目录
define('cache_static', './cache/static/');	//静态数据缓存目录
define('pay_root', './inc/module/payment/');
define('plugin', './inc/plugin/');
define('Ext', '.php');
define('Condir', '/');

const HTTP="http://";
const HTTPS="https://";
const authCodeLen=6; //验证码长度
const cookiedomain = ''; //COOKIE作用域
const ym_token = '{ym_token}'; //安全令牌，用于加密解密，请数字与字母！！ 
$dbhost = '{dbhost}';			// 数据库服务器
$dbport = '{dbport}';  //数据库端口
$dbuser = '{dbuser}';			// 数据库用户名
$dbpw = '{dbpw}';				// 数据库密码 
$dbname = '{dbname}';			// 数据库名
$tablepre ="{tablepre}";     //表前缀
$pconnect = 0;				// 数据库持久连接 0=关闭, 1=打开
$tplrefresh = 1;	// 模板自动刷新开关 0=关闭, 1=打开
$dbcharset='utf8';  //数据库编码
$sqlitedb="yec.db"; //sqlite数据库文件
$dbtype='mysql'; //数据库连接类型

function dbc($dbtype='mysql'){
	global $dbhost,$dbuser,$dbpw,$dbname,$dbcharset,$dbport,$pconnect,$tablepre,$sqlitedb,$db;
	$db = new db();
	if ($dbtype=='mysql'){
		$db->mysql($dbhost, $dbuser, $dbpw, $dbname, $tablepre, $dbcharset,$dbport);
	}else{
		$db->sqlite($sqlitedb);
	}
	return $db;
}

?>