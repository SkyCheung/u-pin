<?php
if (!defined('in_mx')) {exit('Access Denied');}
/* * 
 * 功能：支付宝页面跳转同步通知页面
 */

//直接依赖服务端的异步通知，忽略同步返回 
if($_GET['extra_common_param']==1)
{
	redirect("mymoney.html"); 
}
redirect("payresult.html?oid=".$_GET['out_trade_no']); 
die();


 
require_once './inc/lib/pay.php';
require_once(pay_root."alipay/alipay.config.php");
require_once(pay_root."alipay/lib/alipay_notify.class.php");

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();

if($verify_result) {//验证成功	
	$out_trade_no = $_GET['out_trade_no'];//商户订单号	
	$trade_no = $_GET['trade_no'];//支付宝交易号	
	$trade_status = $_GET['trade_status'];//交易状态
	
    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
		
    }
    else {
      echo "trade_status=".$_GET['trade_status'];
    }
		
	echo "支付成功<br />".$trade_no;
 
}
else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyReturn函数
    echo "支付失败";
}
die();
?>
