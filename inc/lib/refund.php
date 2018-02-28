<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*退款处理*/

/*获取退款按钮代码*/
function get_refund_button($refund_no, $order_sn='', $refund_fee=0)
{
	global $login_id;
	require_once './inc/lib/pay.php'; 
	
		$order = get_order_info(0, $order_sn);
		$pay_code = $order['pay_code'];
		$order['order_sn'] = $order_sn;
		$order['refund_no'] = $refund_no;
		$order['total_fee'] = $order['amount'];
		$order['refund_fee'] = $refund_fee;
		$order['op_user'] = $login_id;
		
		$payment = get_payment($pay_code, 1, '1');//支付方式	
		
		$res_data=array('refund_html'=>'','err'=>'','refund_no'=>$refund_no,'pay_code'=>$pay_code);
		if(intval($payment[0]['is_redirect'])==1)
		{
			$pay_file = pay_root . $pay_code."/" . $pay_code.'.php';
			if(file_exists($pay_file))
			{
				require_once $pay_file;
				if(class_exists($pay_code))
				{
					$pay = new $pay_code;
					$res_data['refund_html'] = $pay->refund($order);
				}
				else {
					$res_data['err'] = '支付文件不存在'.$pay_code;
				}				
			}
			else{
				$res_data['err'] = "支付方式不存在：".$pay_code;
			}
		}
	return 	$res_data;
}

/*提交退款*/
function apply_refund($order)
{
	$res = array(); 
	$refundinfo = get_refundinfo($order["refund_no"]);
	if($refundinfo['status'] ==1)
	{
		$res['is_success']=1;
		$res['trade_msg'] = '您已成功退款，请刷新页面查看结果';
		return $res;
	}
	$order['pay_time']= $refundinfo['pay_time'] ;
	
	require_once './inc/lib/pay.php'; 
	$payment = get_payment('');//支付方式
		
	$pay_code = $order['pay_code'];
	$pay_file = pay_root . $pay_code."/" . $pay_code.'.php';
	
	if(file_exists($pay_file))
	{
		require_once $pay_file;
		if(class_exists($pay_code))
		{	
			$pay = new $pay_code; 
			$res = $pay->refund($order);
			$status = $res['is_success']==1 ? 1:0;  
			update_refund($order["refund_no"], $res["trade_no"], $res["trade_msg"], $status, isset($res["pay_time"]) ? $res["pay_time"] : 0);		
		}
		else {
			$res['trade_msg'] = '支付文件不存在'.$pay_code;
		}
	}
	else{
		$res['trade_msg'] = "支付方式不存在：".$pay_code;
	}
	
	if($res['is_success']==1 && $res["trade_msg"]=='')
	{
		if($pay_code !='bal' && $pay_code !='cod')
		{
			//优惠券、余额和积分处理
			add_member_log($order['uid'], asset_balance, $order['refund_fee'], '订单'.$order['order_sn']. '退货');
			if(intval($order['point']) != 0)//扣除相应的赠送积分
			{
				update_account($order['uid'], 0, -$order['point']); 
				add_member_log($order['uid'], asset_point, -$order['point'], '订单'.$order['order_sn']. '退货');
			}	
		}	
				
		if($order['coupon_ids'] !='' && check_return($order['order_sn'], $order["refund_no"])) //用了优惠券的，同时订单内的商品全退完，则设为未使用
		{			
			require './inc/lib/coupon.php';			
			update_coupon_user_status($order['coupon_ids'], coupon_unused);
		}
	}
	
	return $res;
}

/*生成退款单*/
function add_refund($trade_no='', $order_sn='', $refund_fee=0, $uid=0)
{
	global $db;
	$data = array('trade_no'=>$trade_no,'order_sn'=>$order_sn,'refund_fee'=>$refund_fee,'status'=>0,'uid'=>intval($uid),'addtime'=>time() );
	
	$ok = false;
	do
	{	
		$data['refund_no'] = build_order_sn(1); 
		$db->insert('order_refund', $data);
		//$id = $db->lastinsertid(); 
		$code = $db->errorinfo();
		$ok = $code[1] == 1062;//防止重复			
	}while($ok==true);
	
	return $data['refund_no'];
}

/*更新退款单*/
function update_refund($refund_no, $trade_no='', $trade_msg='', $status=0, $pay_time=0)
{
	global $db;
	$data = array('trade_no'=>$trade_no, 'trade_msg'=>$trade_msg, 'status'=>intval($status));
	if($pay_time !=0)
	{
		$data['pay_time'] = $pay_time;
	}
	$db->update('order_refund', $data, array('refund_no'=>$refund_no));
}

/*退款单信息*/
function get_refundinfo($refund_no)
{
	global $db;
	
	$db->fetch('order_refund', '*', array('refund_no'=>$refund_no));
}


?>