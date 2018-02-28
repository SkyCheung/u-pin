<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*银联在线支付   异步通知*/
include_once pay_root . 'unionpay/sdk/acp_service.php';

$order_sn = $_POST ['orderId']; //订单号
$respCode = $_POST ['respCode']; //应答码
$trade_no = isset($_POST['queryId']) ? $_POST['queryId'] :'';//支付交易号
$buyer_accno = isset($_POST['accNo']) ? $_POST['accNo'] : '';//支付账户
$order_type = intval($_POST['reqReserved']);
$total_fee = floatval($_POST['txnAmt'])*0.01;

if (isset( $_POST ['signature'] )) {	
	if(com\unionpay\acp\sdk\AcpService::validate ($_POST)==false)
	{
		$err='验签失败';
	}		
	else {
		if ($respCode == "00"){//交易成功
			$err = "";
		} 
		else if ($respCode == "03" || $respCode == "04" || $respCode == "05" ){
			$err = "查询超时"; //后续需发起交易状态查询交易确定交易状态
		} 
		else {		
			$err = $_POST["respMsg"];//其他应答码以失败处理
		}
	}
} else {
	$err='签名为空';
}

dbc();
if($order_type ==0)
{
	$order = get_order_info(0, $order_sn, 0);	
}
else {
	$order = get_pay_log($order_sn);
} 

if(!$order || count($order)==0)
{
	logs('订单'.$order_sn.'不存在。', 'pay');
	die();
}
elseif($order['pay_status'] == pay_payed)
{
	logs('订单'.$order_sn.'已支付', 'pay');
	die();
}
	
if($err=='')
{		
	$ym_uid = $order['uid'];		
	if($order_type !=0)
	{
		update_pay_log($order_sn, 1, $pay_code, $trade_no, $buyer_accno, '');
		if($order_type ==1)
		{
			add_member_log($ym_uid, asset_balance, $total_fee, '充值'.$total_fee, $order_sn);
			update_account($ym_uid, $total_fee);//增加余额
		}
	}
	else {
		update_order_payment($order_sn, $trade_no, $pay_code, $buyer_accno, pay_payed, $_POST ['txnTime'], $order);
		add_order_log($order_sn, $ym_uid, $order['uname'], role_user, '支付订单');
	}
}
else {
	if($order_type ==0)
	{
		update_order(array('order_sn'=>$order_sn, 'trade_msg'=>$err, 'pay_code'=>$pay_code)); 
	}
	else {
		update_pay_log($order_sn, 0, $pay_code, $trade_no, $buyer_accno, $err);
	}
	logs($order_sn.$err, 'pay');
}
		
die();

?>