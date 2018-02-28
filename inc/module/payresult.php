<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*支付结果通知*/

$nav = get_nav(); //导航
$nav_footer = get_nav('bot');
$cats = get_catTree(); //分类树
$help = get_help(); //帮助

if($ym_uid ==0)
{
	$ym_uid = check_login();
}
if(!isset($oid) || trim($oid) =='' || !is_num($oid))
{
	$err = "订单号错误，请重新支付。";
	return;
}

require_once './inc/lib/pay.php';
dbc();

$order = get_pay_log($oid,'', -1, $ym_uid);
$order['order_sn'] = $order['pay_sn'];
if(!$order)
{
	$order = get_order_info(0, $oid, $ym_uid);	
}


?>