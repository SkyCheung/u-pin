<?php
if (!defined('in_mx')) {exit('Access Denied');}
/*发货*/
checkAuth($login_id, 'order');//权限检测

if(!isset($ids) || isNumComma($ids)==false)
{
	message("获取订单编号失败");
}

if($batch) 
{
	$goods=array();
}
else {
	$goods = get_not_delivery($ids);
}

$exp =get_cache('express_common');
$exp[0]=array('id'=>0,'name'=>'商家配送');
if($ym_is_pickup && $ym_is_pickup==1) //开启自提
{
	$exp[1]=array('id'=>1,'name'=>'自提');
}
sort($exp);

$ok= isset($ok)?intval($ok):0;
	 
?>