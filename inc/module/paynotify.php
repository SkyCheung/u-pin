<?php
if (!defined('in_mx')) {exit('Access Denied');}
/* 支付/退款通知  */

$t=time();
$txt_refund= ''; //退款通知
$txt_async=''; //是否异步
$pay_code = preg_replace('/^paynotify_(.*)$/i','$1', $p); //paynotify_alipay_async paynotify_wxpay_native paynotify_wxpay_native_refund

if(preg_match("/^(.*)_async$/i", $pay_code)) //异步
{
	$txt_async = '_async';
	$pay_code = preg_replace('/^(.*)_async$/i','$1', $pay_code);
}
if(preg_match("/^(.*)_refund$/i", $pay_code)) //退款
{
	$txt_refund = '_refund';
	$pay_code = preg_replace('/^(.*)_refund$/i','$1', $pay_code);
}  

if(empty($pay_code))
{
	message("支付方式错误","index.html");
}
require_once './inc/lib/pay.php';
$payment = get_payment($pay_code, 1);//支付方式
if(!$payment || count($payment)==0)
{
	message("支付方式错误".$pay_code, "index.html");
}

$pay_file = pay_root . $pay_code ."/".'notify' . $txt_refund . $txt_async . ".php";
if(file_exists($pay_file))
{
	require_once $pay_file;
}	


?>