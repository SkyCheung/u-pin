<?php
if (!defined('in_mx')) {exit('Access Denied');}
/**
 *支付宝*/
 
$payment_mod = array();
$payment_mod['pay_code']    = basename(__FILE__, '.php'); //代码
$payment_mod['pay_name']    = '支付宝';//支付方式名称
$payment_mod['pay_desc']    = '电脑端，支付宝网上交易时，买家的交易资金直接打入卖家支付宝账户，快速回笼交易资金。';
$payment_mod['isonline']    = '1';//是否在线,是1 否0
$payment_mod['client']    = '1';////客户端类型,0所有,1 pc,2 wap,3 app, 混合用逗号隔开 如1,3
$payment_mod['config']  =  json_encode(array('seller_id'=>'','partner'=>'','key'=>''));//配置信息
$payment_param=array(
	array('name'=>'payconfig_partner','value'=>'合作者身份(PID)','type'=>'text','desc'=>'签约后支付宝分配给您的合作者身份，可在支付宝商家服务中心查看，或<a class="lnk"  href="https://open.alipay.com" target="_blank">点击这里签约，获取PID、Key</a>'),
	array('name'=>'payconfig_key','value'=>'校验密钥(Key)','type'=>'text','desc'=>'')
  );  
  
class alipay
{
	function __construct()
    {
    }
	
	/** @name 生成支付html代码
   */  
    function get_payHtml($order, $payment=array())
    {
    	require_once(pay_root."alipay/alipay.config.php");		
		require_once(pay_root.$order['pay_code']."/lib/alipay_submit.class.php");
		
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"service"       => $alipay_config['service'],
				"partner"       => $alipay_config['partner'],
				"seller_id"  => $alipay_config['seller_id'],
				"payment_type"	=> $alipay_config['payment_type'],
				"notify_url"	=> $alipay_config['notify_url'],
				"return_url"	=> $alipay_config['return_url'],				
				"anti_phishing_key"=>$alipay_config['anti_phishing_key'],
				"exter_invoke_ip"=>$alipay_config['exter_invoke_ip'],
				"out_trade_no"	=> $order['order_sn'],
				"subject"	=> $order['subject'],
				"total_fee"	=> $order['payble_amount'],
				"body"	=> '', //商品描述
				"extra_common_param" => $order['com_param'],
				"sign_type"    => $alipay_config['sign_type'],
				"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))		
				
		);
		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$result['pay_html'] = $alipaySubmit->buildRequestForm($parameter,"get", "立即支付");
		$result['err_code'] = '';
		
		return $result;
    }
    
    /** 退款
	 *  @param $request_type 请求类型  form表单, http 模拟远程HTTP返回地址
	 * */
   function refund($order, $request_type='form', $is_newblank= true)
   {
   		require_once pay_root."alipay/refund.php";
		
		$data = array('is_success'=>1, 'trade_no'=>'', 'trade_msg'=>''); //退款结果统一格式
   		$result = ali_refund($order, $request_type, "退款", $is_newblank);
		
		return $result;
   }
}

?>