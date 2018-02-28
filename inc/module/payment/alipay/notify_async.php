<?php
if (!defined('in_mx')) {exit('Access Denied');}
/* *
 * 功能：支付宝服务器异步通知页面
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 */

require_once './inc/lib/pay.php';
require_once(pay_root."alipay/alipay.config.php");
require_once(pay_root."alipay/lib/alipay_notify.class.php");

$msg = createLinkstring($_POST);

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

dbc();

$trade_no = $_POST['trade_no'];//支付宝交易号
$out_trade_no = $_POST['out_trade_no'];//商户订单号
$trade_status = $_POST['trade_status'];//交易状态
$buyer_email = $_POST['buyer_email'];//
$com_param = json_decode($_POST['extra_common_param'], true);
$is_split = $com_param['is_split'];
$order_type = $com_param['order_type'];

if($order_type==0)
{
	$order = get_order_info(0, $out_trade_no, 0);	
}
else {
	$order = get_pay_log($out_trade_no);
} 

if(!$order || count($order)==0)
{
	logs('订单'.$out_trade_no.'不存在。', 'pay');
	echo "fail"; die();
}
elseif($order['pay_status'] == pay_payed)
{
	logs('订单'.$out_trade_no.'已支付', 'pay');
	echo "success"; die();
}

if($verify_result) {//验证成功	
    if($_POST['trade_status'] == 'TRADE_FINISHED') {    	  	
 
    }
    else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
    	$err = '';
		if($order['payble_amount'] != $_POST['total_fee'])
		{
			$err .='支付金额和订单金额不一致。';
			//echo "success";die();
		}
		if($alipay_config['seller_id'] != $_POST['seller_id'])
		{
			$err .='收款支付宝账号不一致。';
			//echo "fail";die();
		}
		$ym_uid = $order['uid'];		
		if($order_type !=0)
		{
			update_pay_log($out_trade_no, 1, $pay_code, $trade_no, $buyer_email, $err);
			if($order_type ==1)
			{
				add_member_log($ym_uid, asset_balance, $_POST['total_fee'], '充值'.$_POST['total_fee'], $out_trade_no);
				update_account($ym_uid, $_POST['total_fee']);//增加余额
			}
		}
		else {
			update_order_payment($out_trade_no, $trade_no, $pay_code, $buyer_email,pay_payed, '', $order);
			add_order_log($out_trade_no, $ym_uid, $order['uname'], role_user, '支付订单');
		}
    }
    logs($msg.$err, "pay");
	echo "success";		//请不要修改或删除
}
else {	
	if($order_type ==0)
	{
		update_order(array('order_sn'=>$out_trade_no, 'trade_msg'=>$err, 'pay_code'=>$pay_code)); 
	}
	else {		
		update_pay_log($out_trade_no, 0, $pay_code, $trade_no, $buyer_email, '验证失败');
	}
	logs($msg.' 验证失败。', "pay");
    //验证失败
    echo "fail";
}	
die();

?>