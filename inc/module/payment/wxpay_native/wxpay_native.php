<?php
if (!defined('in_mx')) {exit('Access Denied');}
/**
 *微信扫码支付
 * 1、调用统一下单，取得code_url，生成二维码
 * 2、用户扫描二维码，进行支付
 * 3、支付完成之后，微信服务器会通知支付成功
 * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
 */
  
$payment_mod = array();
$payment_mod['pay_code']    = 'wxpay_native'; //代码
$payment_mod['pay_name']    = '微信扫码支付';//支付方式名称
$payment_mod['pay_desc']    = '扫码支付是商户系统生成支付二维码，用户用微信“扫一扫”完成支付的模式。该模式适用于PC网站支付、实体店单品或订单支付、媒体广告支付等场景。';
$payment_mod['isonline']    = '1';//是否在线,是1 否0
$payment_mod['client']    = '1';//客户端类型,0所有,1 pc,2 wap,3 app, 混合用逗号隔开 如1,3
$payment_mod['config']  =  json_encode(array('appid'=>'','mch_id'=>'','key'=>'','secret'=>''));//配置信息
$payment_param=array(
	array('name'=>'payconfig_appid','value'=>'APPID','type'=>'text','desc'=>'appid是微信公众账号或开放平台APP的唯一标识，可在微信公众平台-->开发者中心查看，商户的微信支付审核通过邮件中也会包含该字段值。'),
	array('name'=>'payconfig_mch_id','value'=>'微信商户号','type'=>'text','desc'=>'微信支付分配的商户收款账号，可在微信商户平台里查看。'),
	array('name'=>'payconfig_key','value'=>'API密钥','type'=>'text','desc'=>'微信商户平台(pay.weixin.qq.com)-->账户设置-->API安全-->密钥设置 里查看。'),
	array('name'=>'payconfig_secret','value'=>'Appsecret','type'=>'text','desc'=>'AppSecret是APPID对应的接口密码，在开发模式中获取AppSecret。')
  );  


class wxpay_native
{
    function __construct()
    {
    }

    /** @name 生成支付html代码
   */  
    function get_payHtml($order)
    {
    	global $ym_url;
		$pay_code = $order['pay_code']; 
		ini_set('date.timezone','Asia/Shanghai');
		//error_reporting(E_ERROR);
		
		require_once pay_root."wxpay/lib/WxPay.Api.php";
		require_once pay_root."wxpay/lib/WxPay.NativePay.php";
		require_once "./inc/lib/log.php";
		
		$notify = new NativePay();
		$input = new WxPayUnifiedOrder();
		$input->SetBody($order['body']);//商品
		$input->SetAttach($order['com_param']); //暂放 订单类型
		$input->SetOut_trade_no($order['order_sn']);
		$input->SetTotal_fee(floatval($order['payble_amount'])*100);//单位为分
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 3600));
		$input->SetGoods_tag("");
		$input->SetNotify_url($ym_url."paynotify_wxpay_native.html");
		$input->SetTrade_type("NATIVE");
		$input->SetProduct_id($order['order_sn']); //print_r($input);
		$result = $notify->GetPayUrl($input);//print $pay_code;
		$url = $result["code_url"];
		if($result["err_code_des"] !='' || $result["return_code"] =='FAIL')
		{
			$result['pay_html'] ='<p style="display:block;font-size:16px;" class="red">'.$result["err_code_des"].' '.$result["return_msg"].'</p>';
		}
		else {
			$result['pay_html'] ='<h3 class="wxpay">请用微信扫一扫完成支付</h3><p style="display:block;"><img width="200" height="200" src="/qrcode.html?data='.urlencode($url).'" alt="支付码" /></p>';
		}
		
        return $result;
    }
	
	//退款
   function refund($order)
   {
   		$pay_code = $order['pay_code'];
   		require_once pay_root."wxpay/refund.php";
		$data = array('is_success'=>1, 'trade_no'=>'', 'trade_msg'=>''); //退款结果统一格式
   		$result = wx_refund($order);
		
		if($result['return_code']=='SUCCESS')
		{
			if($result['result_code']=='FAIL')
			{
				if($result['err_code']=='SYSTEMERROR' && $is_again==1)
				{
					wx_refund($order, 0);//系统超时, 再次调用API
				}
				$data['is_success'] = 0;
				$data['trade_msg'] = $result['err_code'].':'.$result['err_code_des'];
				logs('订单'.$order["order_sn"].'退款失败，'.$result['err_code'].':'.$result['err_code_des'], 'pay');
			}
			else {
				$data['is_success'] = 1;
				$data['trade_no'] = $result['refund_id'];
				$data['trade_msg'] = '';
			}
		}
		else {
			$data['is_success'] = 0;
			$data['trade_msg'] = $result['return_msg'];
			logs('订单'.$order["order_sn"].'退款失败，'.$result['return_msg'], 'pay');
		}
		return $data;
   }
}

?>