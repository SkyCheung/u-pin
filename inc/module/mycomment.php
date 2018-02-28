<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*我的评价*/

dbc();

$nav = get_nav(); //导航
$nav_footer = get_nav('bot');
$cats = get_catTree(); //分类树
$help = get_help(); //帮助

check_login();

$page=intval($page)==0 ? 1 : intval($page);
$pagenum= 12; 
$start = $page * $pagenum - $pagenum;
$count = get_mycomment_count($ym_uid, 0);
if ($count>0)
{
	$pages = getPages($count, $page, $pagenum);
	$row = get_mycomment($ym_uid, 0, $start, $pagenum);
}
else {
	$row='';
}

?>