<?php
if (!defined('in_mx')) {exit('Access Denied');}

//支付方式
function get_payment($pay_code='', $status = 1, $client='0', $no_bal= 1)
{
	global $db;
	if(!$db)
	{
		$db= dbc();
	}
	$where ='1';
	if($pay_code !='')
	{
		$where .=" and pay_code='".$pay_code."'";
	}
	if($status !=0)
	{
		$where .=' and status='.$status;
	}
	if($no_bal == 1)
	{
		$where .=" and pay_code<>'bal'";
	}
	if($client !='0')
	{
		$where .=" and (client='0' or find_in_set('".$client."',client))";
	}
	/*else {
		$where .=" and client='0'";
	}*/
 
	$payment = $db->fetchall('payment','*', $where);
	if ($payment && count($payment)>0)
    {
        foreach ($payment as $k => $v) {
        	$payment[$k]['config'] = json_decode($v['config'], true);
        } 
    }

    return $payment;
}

//支付交易查询
function query_pay($trade_no='', $order_sn='', $pay_code='',$tran_time='')
{
	$res = array('err' => '', 'res' => '', 'data' => array()); //返回结果
	if($pay_code =='')
	{
		$order = get_order_info(0, $order_sn, 0);
		$pay_code = $order['pay_code'];
	}
	
	$pay_file = pay_root.$pay_code."/".$pay_code.'.php';
	if(file_exists($pay_file))
	{
		require_once $pay_file;
		if(class_exists($pay_code))
		{
			$pay = new $pay_code;
			return $pay->query($trade_no, $order_sn, $tran_time);
		}
		else {
			$res['err'] = '支付方式错误'.$pay_code;
			return $res;
		}
	}
	else {
		$res['err'] = "支付方式不存在：".$pay_code;
		return $res;
	}
}

//支付后处理
function update_order_payment($order_sn, $trade_no='',$pay_code='',$trade_buyer='', $pay_status=pay_payed, $pay_time='', $cur_order=array())
{
	global $ym_notice_order_sms;
	$order = array();
	$order['order_sn']= $order_sn;
	$order['trade_no']= $trade_no;	
	$order['trade_buyer']= $trade_buyer;
	$order['status']= order_deliver;
	$order['pay_status']= $pay_status;	
	$order['trade_msg']='';
	if($pay_code !='')
	{
		$order['pay_code']= $pay_code;
	}
	if($pay_status==pay_payed)
	{
		$order['payble_amount']= 0;
		$order['pay_time']= $pay_time=='' ? time() : $pay_time;
		
		if($cur_order['coupon_ids'] !='') //用了优惠券，设为已用
		{
			require './inc/lib/coupon.php';
			update_coupon_user_status($cur_order['coupon_ids'], coupon_used);
		}
	}
	
	//发短信/微信提醒给商家
	if($ym_notice_order_sms && $ym_notice_order_sms ==1) {
		require("./inc/lib/sms.php");
		$res = sms_notice('', 'order', array('order_sn'=>$order_sn));
	}			
		
	update_order($order); 
}

//增加支付记录
function add_pay_log($pay_sn,$amount, $type, $uid=0, $order_sn='')
{
	global $db;
	
	$db->insert('pay_log', array('pay_sn'=>$pay_sn,'order_sn'=>$order_sn, 'amount'=>floatval($amount),'type'=>intval($type),'trade_no'=>$trade_no,'trade_buyer'=>$trade_buyer,'trade_msg'=>$trade_msg,'pay_status'=>0,'uid'=>intval($uid), 'addtime'=>time()));
}

//更新支付记录
function update_pay_log($pay_sn, $pay_status=1, $pay_code='', $trade_no='', $trade_buyer='', $trade_msg='')
{
	global $db;	
	$db->update('pay_log', array('pay_status'=>intval($pay_status), 'pay_code'=>$pay_code,'trade_no'=>$trade_no,'trade_buyer'=>$trade_buyer,'trade_msg'=>$trade_msg), array('pay_sn'=>$pay_sn));
}

//删除未支付记录
function del_pay_log($pay_sn)
{
	global $db;	
	$db->delete('pay_log', array('pay_sn'=>trim($pay_sn)));
}

//获取支付记录
function get_pay_log($pay_sn='', $trade_no='', $pay_status=-1, $uid=0, $order_sn='')
{
	global $db;	
	$where ='';
	if($pay_sn !='')
	{
		$where .=" and pay_sn='".$pay_sn."'";
	}
	if($order_sn !='')
	{
		$where .=" and order_sn='".$order_sn."'";
	}
	if($trade_no !='')
	{
		$where .=" and trade_no='".$trade_no."'";
	}
	if($pay_status !=-1)
	{
		$where .=" and pay_status=".$pay_status;
	}
	if($uid != 0)
	{
		$where .=" and uid=".$uid;
	}

	return $db->query('select p.*,amount as payble_amount,m.uname from '.$db->table('pay_log')." p left join ".$db->table('member')." m on p.uid=m.id  where 1 ".$where);
}

?>