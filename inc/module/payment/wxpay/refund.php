<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*退款*/
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);
require_once pay_root."wxpay/lib/WxPay.Api.php";
require_once "./inc/lib/log.php";

//初始化日志
$logHandler= new CLogFileHandler("./upload/logs/pay-".date('Ymd').'.log');
$log = Log::Init($logHandler, 15);

/*退款*/
function wx_refund($order)
{
	$input = new WxPayRefund();	
	$pay_code = $order['pay_code']; 
	if(isset($order["transaction_id"]) && $order["transaction_id"] != ""){		
		$input->SetTransaction_id($order["transaction_id"]);
	}
 
	if(isset($order["order_sn"]) && $order["order_sn"] != ""){
		$input->SetOut_trade_no($order["order_sn"]);		
	}
	$input->SetTotal_fee(floatval($order["total_fee"]) * 100); //以分为单位，原订单总金额
	$input->SetRefund_fee(floatval($order["refund_fee"]) * 100); //以分为单位，退款金额
	$input->SetOut_refund_no($order["refund_no"]);
	$input->SetOp_user_id($order['op_user']);
	
	return  WxPayApi::refund($input);
	exit();
}

?>
