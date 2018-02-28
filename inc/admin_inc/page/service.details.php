<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'service');//权限检测 

/*退换货详情*/
if(!isset($id) || intval($id)==0)
{
	message('获取服务单号失败');
}

$row = get_service_info($id);
$cbk_status[$row['status']]='checked="checked"';

if($row['refund_no'] != '' && intval($row['refund_status']) !='1' ) //已生成退款单且未付款， 则生成付款按钮
{
	require_once './inc/lib/refund.php';
	$res_data = get_refund_button($row['refund_no'], $row['order_sn'], $row['refund_fee']);
}

?>