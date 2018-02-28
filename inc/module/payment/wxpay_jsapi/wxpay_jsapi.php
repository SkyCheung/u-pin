<?php 
if (!defined('in_mx')) {exit('Access Denied');}

/** 公众号支付  微信内网页支付 */

$payment_mod = array();
$payment_mod['pay_code']    =  'wxpay_jsapi'; //代码
$payment_mod['pay_name']    = '微信支付';//支付方式名称
$payment_mod['pay_desc']    = '公众号支付是用户在微信中打开商户的网页，商户网页通过调用微信支付提供的JSAPI接口调起微信支付完成支付。';
$payment_mod['isonline']    = '1';//是否在线,是1 否0
$payment_mod['client']    = '2';//客户端类型,0所有,1 pc,2 wap,3 app, 混合用逗号隔开 如1,3
$payment_mod['config']  =  json_encode(array('appid'=>'','mch_id'=>'','key'=>'','secret'=>''));//配置信息
$payment_param=array(
	array('name'=>'payconfig_appid','value'=>'APPID','type'=>'text','desc'=>'appid是微信公众账号或开放平台APP的唯一标识，可在微信公众平台-->开发者中心查看，商户的微信支付审核通过邮件中也会包含该字段值。'),
	array('name'=>'payconfig_mch_id','value'=>'微信商户号','type'=>'text','desc'=>'微信支付分配的商户收款账号，可在微信商户平台里查看。'),
	array('name'=>'payconfig_key','value'=>'API密钥','type'=>'text','desc'=>'微信商户平台(pay.weixin.qq.com)-->账户设置-->API安全-->密钥设置 里查看。'),
	array('name'=>'payconfig_secret','value'=>'Appsecret','type'=>'text','desc'=>'AppSecret是APPID对应的接口密码，在开发模式中获取AppSecret。')
  );  


class wxpay_jsapi
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
		if(!is_weixin())
		{
			$res['pay_html']='<p style="display:block;font-size:16px;" class="red">请在微信中使用支付</p>';
			return $res;	
		}
		
		ini_set('date.timezone','Asia/Shanghai');
		//error_reporting(E_ERROR);
		require_once pay_root."wxpay/lib/WxPay.Api.php";
		require_once pay_root."wxpay/lib/WxPay.JsApiPay.php";
		require_once "./inc/lib/log.php";		
		
		//1、获取用户openid
		$tools = new JsApiPay(); 
		$openId = $tools->GetOpenid();
		
		//2、统一下单
		$input = new WxPayUnifiedOrder();
		$input->SetBody($order['body']);
		$input->SetAttach($order['com_param']); //暂放 订单类型
		$input->SetOut_trade_no($order['order_sn']);
		$input->SetTotal_fee(floatval($order['payble_amount'])*100);//单位为分 
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 3600));
		$input->SetGoods_tag("");
		$input->SetNotify_url($ym_url."paynotify_wxpay_jsapi.html");
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		$wx_order = WxPayApi::unifiedOrder($input);
		if( ($wx_order['return_msg'] !='' &&  strtoupper($wx_order['return_msg']) !='OK') || ($wx_order['err_code'] && $wx_order['err_code'] !=''))
		{
			$res['pay_html']=$wx_order['err_code'].'：'.$wx_order['err_code_des'];
			return $res;
		}
						
		$jsApiParameters = $tools->GetJsApiParameters($wx_order);
		
		//获取共享收货地址js函数参数
		//$editAddress = $tools->GetEditAddressParameters();

		$html ='<script type="text/javascript">';
		$html .='function jsApiCall(){WeixinJSBridge.invoke("getBrandWCPayRequest",';
		$html .= $jsApiParameters.',';
		$html .='function(res){';
		$html .= 'if(res.err_msg == "get_brand_wcpay_request:ok"){window.location.href="'.$ym_url.'payresult.html?oid='.$order['order_sn'].'";}';
		$html .= 'else{msg("支付失败："+res.err_code+res.err_desc);}});}';		
		$html .='function callpay(){if (typeof WeixinJSBridge == "undefined"){';
		$html .='if( document.addEventListener ){ document.addEventListener("WeixinJSBridgeReady", jsApiCall, false);}';
		$html .='else if (document.attachEvent){document.attachEvent("WeixinJSBridgeReady",jsApiCall);'; 
		$html .='document.attachEvent("onWeixinJSBridgeReady",jsApiCall);}}else{jsApiCall();}}</script>';
		$html .='<button class="fix-btn" type="button" onclick="callpay()" >立即支付</button>';
		
		$res['pay_html']=$html; 
		return $res;
	}

	//退款
   function refund($order)
   {
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

    

	/*//获取共享地址
	function editAddress()
	{
		WeixinJSBridge.invoke(
			"editAddress",
			<?php echo $editAddress; ?>,
			function(res){
				var value1 = res.proviceFirstStageName;
				var value2 = res.addressCitySecondStageName;
				var value3 = res.addressCountiesThirdStageName;
				var value4 = res.addressDetailInfo;
				var tel = res.telNumber;
			}
		);
	}
	
	window.onload = function(){
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener("WeixinJSBridgeReady", editAddress, false);
		    }else if (document.attachEvent){
		        document.attachEvent("WeixinJSBridgeReady", editAddress); 
		        document.attachEvent("onWeixinJSBridgeReady", editAddress);
		    }
		}else{
			editAddress();
		}
	};*/
	
	
	
	
 