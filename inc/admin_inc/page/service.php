<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*退换货*/
$res = array('err' => '', 'res' => '', 'data' => array()); 
if($act)
{
	if(!checkAuth($login_id, 'service'))//权限检测
	{
		$res['err'] = $lang['access_denied'];
		die(json_encode($res));
	}
	require_once './inc/lib/refund.php';
	if($act== 'add_refund')
	{
		if(!$id || $id=='')
		{
			$res['err'] ='获取服务单号失败';
			die(json_encode($res));
		}	
		
		require_once './inc/lib/pay.php'; 
		$refund_no = add_refund('', $order_sn, $refund_fee, $login_id);	//生成退款单
		update_order_service($id, array('refund_fee'=>$refund_fee,'refund_no'=>$refund_no)); //更新服务单
									
		$res_data = get_refund_button($refund_no, $order_sn, $refund_fee);		
		$res['data'] = $res_data;
	}
	elseif ($act == 'apply_refund') {
		if(!isset($refund_no) || $refund_no=='')
		{
			$res['err'] ='退款单号不能为空';
			die(json_encode($res));
		}
		
		$order = get_order_info(0, $order_sn);
		$order['order_sn'] = $order_sn;
		$order['refund_no'] = $refund_no;
		$order['total_fee'] = $order['amount'];
		$order['refund_fee'] = $refund_fee;
		$order['op_user'] = $login_id;
		$order['coupon_ids'] = $order['coupon_ids'];
		$res['data'] = apply_refund($order);
	}
	die(json_encode($res));
}

checkAuth($login_id, 'service');//权限检测 
$page = intval($page)==0 ? 1 : intval($page);
$pagenum = 12; 
$start = $page * $pagenum - $pagenum;
$count = get_myorder_service_count($ym_uid);
if ($count>0)
{
	$pages = getPages($count, $page, $pagenum);
	$row = get_myorder_service($ym_uid, 0 , 0, $start, $pagenum);
}
else {
	$row='';
}


?>