<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'diy');//权限检测

$where='';
if($client)
{
	$where = "client=0 or find_in_set(".intval($client).",client)";
}
$row = $db->fetchall('diy', '*',  $where, 'diy_type asc', '');
 
$cbk_diy_type['goods']='商品';
$cbk_diy_type['news']='文章';
$cbk_diy_type['custom']='自定义';
$cbk_diy_type['coupon']='优惠券';

?>