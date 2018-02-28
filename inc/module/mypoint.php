<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*我的积分*/
dbc();

if($act == 'get_point')
{
	$nav = get_nav(); //导航
	$nav_footer = get_nav('bot');
	$cats = get_catTree(); //分类树
	$help = get_help(); //帮助
}

check_login();

$user = get_user($ym_uid); 
$expire_point= get_expire_point($ym_uid);
$year = date("Y",time());

$t =intval($t);
$page=intval($page)==0 ? 1 : intval($page);
$pagenum = 20; 
$start = $page * $pagenum - $pagenum; 
$count = get_member_log_count($ym_uid, asset_point, 0,'', strtotime(date('Y-m-d H:i:s',strtotime('-2 year'))),'',$t);
if ($count>0)
{
	$pages = getPages($count, $page, $pagenum);
	$row = get_member_log($ym_uid, asset_point, 0,'', strtotime(date('Y-m-d H:i:s',strtotime('-2 year'))),'', $start, $pagenum,$t);
}
else {
	$row='';
}

if($act == 'get_point')
{	
	$res = array('err' => '', 'res' => '', 'data' => $row, 'count' => $count, 'total'=>$user['point'], 'expire_point'=>$expire_point);
	die(json_encode_yec($res));
}	
	

?>