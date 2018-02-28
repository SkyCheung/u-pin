<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*银联在线支付   前台同步通知*/

if(intval($_POST['reqReserved']) ==1) //充值
{
	redirect("mymoney.html"); 
}
redirect("payresult.html?oid=".$_POST['orderId']); 
die();

//logs("同步收到".json_encode($_POST));
/*include_once pay_root . 'unionpay/sdk/acp_service.php';
if (isset ( $_POST ['signature'] )) {
	if(com\unionpay\acp\sdk\AcpService::validate ($_POST)==false)
	{
		$err='验签失败';
	}
	$respCode = $_POST ['respCode']; //判断respCode=00或A6即可认为交易成功
} else {
	echo '签名为空';
}*/


?>