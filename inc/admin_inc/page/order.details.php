<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*订单详情*/

if($act)
{
	$res = array('err' => '', 'res' => '', 'data' => array());
	if(!isset($id) || intval($id) == 0)
	{
		$res['err'] = '获取订单号失败';
		die(json_encode($res));
	}
	if ($act =='confirm_receiving') //确认收货
	{		
		$order= get_order_info(0, $id, 0);
		if($order['status'] == order_finish)
		{
			$res['err'] = '已确认收货';
			die(json_encode($res));
		}
		$row = array("order_sn"=>$id,'status'=>order_finish,'receiving_time'=>time());
		if($order['pay_code'] == 'cod')
		{
			$row['payble_amount']= 0;
		}
		
		//更新赠送积分
		$point = get_order_point($id);
		update_account($order['uid'], 0, $point);
		add_member_log($order['uid'], asset_point, $point, '购物获得积分，订单号：'.$id);
		
		if($ym_ditribution_config['distrib_level']>0) //分销佣金
		{
			require_once "inc/lib/distrib.php";
			pay_commission($order['uid'], $order['uname'], $order['goods_amount']);
		}
		
		update_order($row);
		add_order_log($id, $login_id, $adminname, role_admin, '已确认收货');		
	}
	elseif ($act =='pay') //支付订单
	{
		$order= get_order_info(0, $id, 0);
		if( $order['payble_amount']!=0 && ($order['status']==order_paying || ($order['status']==order_deliver && $order['pay_code']=='cod') ) )
		{
			update_order(array('status'=>order_deliver, 'payble_amount' => 0,'pay_status' => pay_payed, 'pay_time' => time(), 'order_sn'=>$id));
			add_order_log($id, $login_id, $adminname, role_admin, '支付订单');
		}
		else {
			$res['err'] = '该状态下不能支付订单';
			die(json_encode($res));
		}
	}
	elseif ($act =='cancel') //取消订单
	{
		$order= get_order_info(0, $id, 0);
		if($order['status'] == order_paying || $order['status'] == order_deliver)
		{
			if($order['status'] == order_deliver && $order['pay_code'] !='cod')
			{
				$user = get_user($order['uid']);
				$balance = $user['balance'] + $order['balance_amount'];
				$point = $user['point'] +  $order['point_amount'] * $ym_pointpay;
				update_userinfo($ym_uid, array('balance'=>$balance , 'point'=>$point));				
			}
			
			update_order(array('status'=>order_cancel, 'order_sn'=>$id));
			add_order_log($id, $login_id, $adminname, role_admin, '已取消订单');
		}
		else {
			$res['err'] = '该状态下订单不能取消';
			die(json_encode($res));
		}
	}
	else {
		$res['err']="操作类型不正确。";
	}
	die(json_encode($res));
}


if(!isset($id) || intval($id)==0)
{
	message("获取编号失败");
}

$order = get_order_details(0, $id, 0); //print_r($order['goods']);
$cnee_dist_ids = explode(",", $order['cnee_dist_ids']);
$province = $cnee_dist_ids[0];
$city = $cnee_dist_ids[1];
$area = $cnee_dist_ids[2]; 
$town = $cnee_dist_ids[3]; 

$order_log = get_order_log($id); 

?>