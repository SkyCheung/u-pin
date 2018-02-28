<?php
if (!defined('in_mx')) {exit('Access Denied');}

$payment_mod = array();
$payment_mod['pay_code']    = basename(__FILE__, '.php'); //代码
$payment_mod['pay_name']    = '支付宝';//支付方式名称
$payment_mod['pay_desc']    = '手机端，网页中集成支付宝支付功能.支付宝手机支付接口2.0。';
$payment_mod['isonline']    = '1';//是否在线,是1 否0
$payment_mod['client']    = '3';////客户端类型,0所有,1 pc,2 wap,3 app, 混合用逗号隔开 如1,3
$payment_mod['config']  =  json_encode(array('seller_id'=>'','appid'=>'','privateKey'=>''));//配置信息
$payment_param=array(
	array('name'=>'payconfig_partner','value'=>'合作身份者ID','type'=>'text','desc'=>'合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串。 <a class="lnk"  href="https://memberprod.alipay.com/account/reg/enterpriseIndex.htm" target="_blank">点击这里签约</a>'),
	array('name'=>'payconfig_appid','value'=>'应用ID','type'=>'text','desc'=>'开发者的应用ID，可在支付宝开放平台管理中心查看 https://open.alipay.com/'),
	array('name'=>'payconfig_privateKey','value'=>'私钥(privateKey)','type'=>'text','desc'=>'')
);  


class alipay_app
{
	function __construct()
    {
    }
	
	/** @name 生成支付html代码
   */  
    function get_payHtml($order, $payment=array())
    {
		echo '请在app上实现';
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