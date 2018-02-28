<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*退换货详情*/
if(!isset($id) || intval($id)==0)
{
	message('获取服务单号失败', 'service.html');
}

dbc();

$nav = get_nav(); //导航
$nav_footer = get_nav('bot');
$cats = get_catTree(); //分类树
$help = get_help(); //帮助

check_login();

$row = get_service_info($id,$ym_uid);

?>