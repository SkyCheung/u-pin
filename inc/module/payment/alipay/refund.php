<?php
if (!defined('in_mx')) {exit('Access Denied');}

/** 
 * //手机版和电脑版共用
 * 即时到账批量退款有密接口  版本：3.4 */


/**退款
 * @param $request_type 请求类型  form表单, http 模拟远程HTTP返回地址
 * */
function ali_refund($order, $request_type='http', $button_text='退款', $is_newblank=true)
{
	global $ym_url;   
	require_once(pay_root."alipay/alipay.config.php");
	require_once(pay_root."alipay/lib/alipay_submit.class.php");
	$notify_url= explode("?", $alipay_config['refund_notify_url']);
	
	//构造要请求的参数数组，无需改动
	$parameter = array(
			"service" => trim($alipay_config['refund_service']),
			"partner" => trim($alipay_config['partner']),
			"notify_url"	=> trim($notify_url[0]),
			"seller_user_id"	=> trim($alipay_config['seller_id']),
			"refund_date"	=> trim($alipay_config['refund_date']),
			"batch_no"	=> $order['refund_no'],
			"batch_num"	=> isset($order['batch_num']) ? $order['batch_num'] : 1, //#字符出现的数量加1，最大支持1000笔
			"detail_data"	=> isset($order['detail_data']) ? $order['detail_data'] : ($order['trade_no'].'^'.$order["refund_fee"].'^'.'协商退款'),//多笔用#隔开
			"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))			
	);
	
	//建立请求
	$alipaySubmit = new AlipaySubmit($alipay_config);
	if($request_type == 'form')
	{
		$html_text = $alipaySubmit->buildRequestForm($parameter, "post", $button_text,0 , $is_newblank); 
	}
	else {
		$html_text =$alipaySubmit->buildRequestHttp($parameter);
	}
	return $html_text;
}

?>
