<?php
/* *
 * //手机版和电脑版共用
 * 功能：支付宝服务器异步通知页面
开发文档中心（https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.oxen1k&treeId=66&articleId=103600&docType=1
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 */

require_once(pay_root."alipay/alipay.config.php");
require_once(pay_root."alipay/lib/alipay_notify.class.php");
require_once './inc/lib/refund.php'; 

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

if($verify_result) {//验证成功	
	$batch_no = $_POST['batch_no'];//批次号	$success_num = $_POST['success_num'];//批量退款数据中转账成功的笔数
	$result_details = $_POST['result_details'];//批量退款数据中的详细信息

	//判断是否在商户网站中已经做过了这次通知返回的处理
	$res_details = explode(';', $result_details);
	foreach ($res_details as $k => $v) {
		$tmp_res = explode('$', $v);
		$tmp_result = explode('^', $tmp_res[0]);
		if($tmp_result[2] === 'SUCCESS')
		{
			update_refund($batch_no, $tmp_result[0], '', 1);
		}
		else {
			update_refund($batch_no, $tmp_result[0], $tmp_result[2], 0);	
		}		
	}	
        
	echo "success";	
}
else {
    //验证失败
    echo "fail";
}
?>