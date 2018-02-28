<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*订单详情*/

if(!isset($oid) || intval($oid)==0)
{
	redirect("index.html");
}
 
dbc();

$nav = get_nav(); //导航
$nav_footer = get_nav('bot');
$cats = get_catTree(); //分类树
$help = get_help(); //帮助

$ym_uid = check_login();

$order = get_order_details(0, $oid, $ym_uid);
if(!$order)
{
	$oid ="订单异常";
	return;
}
$order_log= get_order_log($oid); 
$delivery = get_delivery($oid); 

if(count($delivery) == 0)
{
	$delivery[0]['order_log'] = $order_log;
}
else {
	foreach ($delivery as $k => $v) {
		$tmp_log = array();
		foreach ($order_log as $key => $val) {
			if($v['exp_no'] == $val['exp_no'] || $val['exp_no']=='')
			{
				$tmp_log[] = $val;
			}
		}
		$delivery[$k]['order_log'] = $tmp_log;
	}
}



?>