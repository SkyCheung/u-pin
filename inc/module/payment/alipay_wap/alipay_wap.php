<?php
if (!defined('in_mx')) {exit('Access Denied');}

$payment_mod = array();
$payment_mod['pay_code']    = basename(__FILE__, '.php'); //代码
$payment_mod['pay_name']    = '支付宝';//支付方式名称
$payment_mod['pay_desc']    = '手机端，网页中集成支付宝支付功能.支付宝手机支付接口2.0。';
$payment_mod['isonline']    = '1';//是否在线,是1 否0
$payment_mod['client']    = '2';////客户端类型,0所有,1 pc,2 wap,3 app, 混合用逗号隔开 如1,3
$payment_mod['config']  =  json_encode(array('seller_id'=>'','appid'=>'','privateKey'=>''));//配置信息
$payment_param=array(
	array('name'=>'payconfig_partner','value'=>'合作身份者ID','type'=>'text','desc'=>'合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串。 <a class="lnk"  href="https://memberprod.alipay.com/account/reg/enterpriseIndex.htm" target="_blank">点击这里签约</a>'),
	array('name'=>'payconfig_appid','value'=>'应用ID','type'=>'text','desc'=>'开发者的应用ID，可在支付宝开放平台管理中心查看 https://open.alipay.com/'),
	array('name'=>'payconfig_privateKey','value'=>'AES密钥','type'=>'text','desc'=>''),
	array('name'=>'payconfig_alipayPublicKey','value'=>'支付宝公钥','type'=>'text','desc'=>'使用RSA(SHA1)密钥')
);  


class alipay_wap
{
	function __construct()
    {
    }
	
	/** @name 生成支付html代码
   */  
    function get_payHtml($order, $payment=array())
    {
    	global $ym_name;
    	require_once(pay_root."alipay_wap/alipay.config.php");
		require_once pay_root.'alipay_wap/aop/AopClient.php'; 
		require_once pay_root.'alipay_wap/aop/request/AlipayTradeWapPayRequest.php'; 
		
		$aop = new AopClient;
		$aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
		$aop->appId = $alipay_config['appid']; 
		//$aop->privateKey = $alipay_config['private_Key'];
		//$aop->format = "json";
		//$aop->charset= "UTF-8";		
		//$aop->alipayPublicKey = "alipay PublicKey";
		$aop->rsaPrivateKeyFilePath = $alipay_config['private_key_filepath']; 
		
		//实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.wap.pay
		$request = new AlipayTradeWapPayRequest();
		$request->setNotifyUrl($alipay_config['notify_url']);
		$request->setReturnUrl($alipay_config['return_url']);
		$request->setBizContent("{" .
		"    \"subject\":\"".$ym_name."订单\"," .
		"    \"out_trade_no\":\"".$order['order_sn']."\"," .
		"    \"total_amount\":".$order['payble_amount']."," .
		"    \"product_code\":\"QUICK_WAP_PAY\"," .
		"    \"passback_params\":\"".$order['order_type']."\"" .
		"  }");
		$result = $aop->pageExecute($request); //print (json_encode($result)); die("cc");
		echo $result;
	}
    
    /** 退款
	 *  @param $request_type 请求类型  form表单, http 模拟远程HTTP返回地址
	 * */
   function refund($order, $request_type='form')
   {
   		require_once pay_root."alipay/refund.php";
		
		$data = array('is_success'=>1, 'trade_no'=>'', 'trade_msg'=>''); //退款结果统一格式
   		$result = ali_refund($order, $request_type);
		
		return $result;
   }
}   
   
?>