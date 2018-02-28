<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*我的余额*/

dbc();

$nav = get_nav(); //导航
$nav_footer = get_nav('bot');
$cats = get_catTree(); //分类树
$help = get_help(); //帮助

check_login();  
require_once './inc/lib/pay.php';

$user = get_user($ym_uid); 
$page=intval($page)==0 ? 1 : intval($page);
$pagenum = 12; 
$start = $page * $pagenum - $pagenum;
$count = get_member_log_count($ym_uid, asset_balance, 0,'',strtotime(date('Y-m-d H:i:s',strtotime('-2 year'))));//print $count;
if ($count>0)
{
	$pages = getPages($count, $page, $pagenum);
	$row = get_member_log($ym_uid, asset_balance, 0,'', strtotime(date('Y-m-d H:i:s',strtotime('-2 year'))),'', $start, $pagenum);
}
else {
	$row='';
}



?>