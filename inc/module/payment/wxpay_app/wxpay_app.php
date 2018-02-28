<?php 
if (!defined('in_mx')) {exit('Access Denied');}

/** 公众号支付  微信内网页支付 */

$payment_mod = array();
$payment_mod['pay_code']    =  'wxpay_app'; //代码
$payment_mod['pay_name']    = '微信APP支付';//支付方式名称
$payment_mod['pay_desc']    = '适用于在移动端APP中集成微信支付功能。';
$payment_mod['isonline']    = '1';//是否在线,是1 否0
$payment_mod['client']    = '3';//客户端类型,0所有,1 pc,2 wap,3 app, 混合用逗号隔开 如1,3
$payment_mod['config']  =  json_encode(array('appid'=>'','mch_id'=>'','key'=>'','secret'=>''));//配置信息
$payment_param=array(
	array('name'=>'payconfig_appid','value'=>'APPID','type'=>'text','desc'=>'appid是微信公众账号或开放平台APP的唯一标识，可在微信公众平台-->开发者中心查看，商户的微信支付审核通过邮件中也会包含该字段值。'),
	array('name'=>'payconfig_mch_id','value'=>'微信商户号','type'=>'text','desc'=>'微信支付分配的商户收款账号，可在微信商户平台里查看。'),
	array('name'=>'payconfig_key','value'=>'API密钥','type'=>'text','desc'=>'微信商户平台(pay.weixin.qq.com)-->账户设置-->API安全-->密钥设置 里查看。'),
	array('name'=>'payconfig_secret','value'=>'Appsecret','type'=>'text','desc'=>'AppSecret是APPID对应的接口密码，在开发模式中获取AppSecret。')
  );  


class wxpay_app
{
    function __construct()
    {
    }

    /** @name 生成支付html代码
   */  
    function get_payHtml($order)
    {
    	global $ym_url, $pay_code;
		$pay_code = empty($pay_code) ? $order['pay_code'] :$pay_code;
		
		ini_set('date.timezone','Asia/Shanghai');
		//error_reporting(E_ERROR);
		require_once pay_root."wxpay/lib/WxPay.Api.php";
		require_once pay_root."wxpay/lib/WxPay.AppPay.php";
		require_once "./inc/lib/log.php";		
		
		$appPay = new AppPay(); 
		
		$input = new WxPayUnifiedOrder();
		$input->SetBody($order['body']);
		$input->SetAttach(''.$order['com_param']); //暂放 订单类型
		$input->SetOut_trade_no($order['order_sn']);
		$input->SetTotal_fee(floatval($order['payble_amount'])*100);//单位为分 
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 3600));
		$input->SetNotify_url($ym_url."paynotify_wxpay_app.html");
		$input->SetTrade_type("APP");
		$wx_order = WxPayApi::unifiedOrder($input); //print_r($input);
	 		
		$res = array('err' => '', 'res' => '', 'data' => array());
		if( ($wx_order['return_msg'] !='' &&  strtoupper($wx_order['return_msg']) !='OK') || ($wx_order['err_code'] && $wx_order['err_code'] !=''))
		{
			$res['err']=$wx_order['err_code'].'：'.$wx_order['err_code_des'];
			return $res;
		}
						
		$param = $appPay->GetParameters($wx_order);
			
		$res['res']= $param;
		return $res;
	}

	/** 查询交易状态 */
   	function query($trade_no='', $order_sn='')
	{		
		ini_set('date.timezone','Asia/Shanghai');
		error_reporting(E_ERROR);

		$pay_code = 'wxpay_app';
		require_once pay_root. "wxpay/lib/WxPay.Api.php";
		
		$res = array('err' => '', 'res' => '', 'data' => array()); //返回结果	SUCCESS	FAIL PROCESSING
		$input = new WxPayOrderQuery();
		if($trade_no !='')
		{
			$input->SetTransaction_id($trade_no);
		}
		elseif($order_sn !='') {
			$input->SetOut_trade_no($order_sn);
		}
		else {
			$res['err'] ="请提供交易号或订单号";
			die(json_encode($res));
		}
		
		$trade_state =array('SUCCESS'=>'支付成功',
			'REFUND'=>'转入退款',
			'NOTPAY'=>'未支付',
			'CLOSED'=>'已关闭',
			'REVOKED'=>'已撤销',
			'USERPAYING'=>'用户支付中',
			'PAYERROR'=>'支付失败');
		$result = WxPayApi::orderQuery($input);
		if($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS')
		{
			if($result['trade_state']=='SUCCESS')
			{
				$res['res'] = 'SUCCESS';
			}
			else {
				$res['err'] = $trade_state[$result['trade_state']];
				$res['res'] = 'FAIL';
			}			
		}
		else {
			if($result['err_code'] == 'ORDERNOTEXIST')
			{
				$res['err'] = '查询系统中不存在此交易订单号';
			}
			$res['res'] = 'FAIL';
		}
		return $res;
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

 
	
	
 