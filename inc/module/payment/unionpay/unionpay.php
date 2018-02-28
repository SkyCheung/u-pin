<?php
if (!defined('in_mx')) {exit('Access Denied');}

/**
 *银联支付  跳转网关支付产品
 *消费交易：前台跳转，有前台通知应答和后台通知应答
 * */
 
$payment_mod = array();
$payment_mod['pay_code']    = basename(__FILE__, '.php'); //代码
$payment_mod['pay_name']    = '银联支付';//支付方式名称
$payment_mod['pay_desc']    = '电脑端，银联在线支付网关是中国银联的网上支付工具，主要支持输入卡号付款、用户登录支付、网银支付、迷你付（IC卡支付）等多种支付方式。';
$payment_mod['isonline']    = '1';//是否在线,是1 否0
$payment_mod['client']    = '1';////客户端类型,0所有,1 pc,2 wap,3 app, 混合用逗号隔开 如1,3
$payment_mod['config']  =  json_encode(array('merId'=>'','signcertpwd'=>''));//配置信息
$payment_param=array(
	array('name'=>'payconfig_merId','value'=>'商户号','type'=>'text','desc'=>'填写您收款的商户号。 <a class="lnk"  href="https://merchant.unionpay.com" target="_blank">点击这里签约</a>'),
	array('name'=>'payconfig_signcertpwd','value'=>'私钥证书密码','type'=>'text','desc'=>'')
  );  
  
class unionpay
{
	function __construct()
    {
    }
	
	/** @name 生成支付html代码
   */  
    function get_payHtml($order, $payment=array(), $is_getsign=false)
    {
    	header ( 'Content-type:text/html;charset=utf-8' );				
		include_once pay_root. 'unionpay/sdk/acp_service.php'; 
		
		$txnTime =$order['pay_time'] != 0 ? $order['pay_time'] : time();
		update_order(array('order_sn'=>$order['order_sn'], 'pay_time'=>$txnTime)); //查询交易状态需要
		global $ym_client;
		
		$params = array(
			//以下信息非特殊情况不需要改动
			'version' =>'5.0.0', //版本号
			'encoding' =>'utf-8', //编码方式
			'txnType' =>'01', //交易类型
			'txnSubType' =>'01', //交易子类
			'bizType' =>'000201', //业务类型
			'frontUrl' =>SDK_FRONT_NOTIFY_URL, //前台通知地址
			'backUrl' =>SDK_BACK_NOTIFY_URL, //后台通知地址
			'signMethod' =>'01', //签名方法
			'channelType' => ($ym_client ==1 ?'07':'08'), //渠道类型，07-PC，08-手机
			'accessType' =>'0', //接入类型
			'currencyCode' =>'156', //交易币种，境内商户固定156
			
			//以下信息需要填写
			'merId' =>merId, //商户代码
			'orderId' =>$order['order_sn'], //商户订单号，8-32位数字字母，不能含“-”或“_”
			'txnTime' =>date('YmdHis', $txnTime), //订单发送时间
			'txnAmt' =>floatval($order['payble_amount']) * 100, //交易金额，单位分
			'reqReserved' =>$order['com_param'], //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现
		);
		
		com\unionpay\acp\sdk\AcpService::sign($params);
		if($is_getsign)
		{
			$res = array('err' => '', 'res' => '', 'data' => array());
			$url = com\unionpay\acp\sdk\SDK_App_Request_Url;
			$result_arr = com\unionpay\acp\sdk\AcpService::post($params,$url);
			if(count($result_arr)<=0) { //没收到200应答的情况				
				$res['err']= "可能网络有问题，没收到银联200应答"; // printResult ($url, $params, "" );
				return $res;
			}
			
			if (!com\unionpay\acp\sdk\AcpService::validate($result_arr) ){
				$res['err']= "应答报文验签失败";
				return $res;
			}
						
			if ($result_arr["respCode"] == "00"){
			    $res['res']= $result_arr["tn"];
			} else {
			    $res['err'] = $result_arr["respMsg"];//其他应答码做以失败处理
			}
			
			return $res;
		}
		else {
			$url = com\unionpay\acp\sdk\SDK_FRONT_TRANS_URL;
			$html = com\unionpay\acp\sdk\AcpService::createAutoFormHtml($params, $url); 
			$result['pay_html'] = $html;
			$result['err_code'] = '';
			return $result;
		}		
    }
    
    /** 退款
	 * */
   function refund($order)
   {
   		include_once pay_root. 'unionpay/sdk/acp_service.php'; 
   		require_once pay_root."unionpay/refund.php";
		
		$data = array('is_success'=>1, 'trade_no'=>'', 'trade_msg'=>''); //退款结果统一格式		
   		return unionpay_refund($order);
   }
   
   	/** 查询交易状态 */
   	function query($trade_no='', $order_sn, $tran_time)
	{
		include_once pay_root. 'unionpay/sdk/acp_service.php'; 
		$res = array('err' => '', 'res' => '', 'data' => array()); //返回结果 SUCCESS	FAIL PROCESSING	 
		$params = array(
			//以下信息非特殊情况不需要改动
			'version' => '5.0.0',		  //版本号
			'encoding' => 'utf-8',		  //编码方式
			'signMethod' => '01',		  //签名方法
			'txnType' => '00',		      //交易类型
			'txnSubType' => '00',		  //交易子类
			'bizType' => '000000',		  //业务类型
			'accessType' => '0',		  //接入类型
			'channelType' => ($ym_client ==1 ?'07':'08'),		  //渠道类型
	
			//以下信息需要填写
			'merId' => merId,	     //商户代码
			'orderId' => $order_sn,  //订单号，8-32位数字字母，不能含“-”或“_”	'2016000001172836', 
			'txnTime' => date('YmdHis', $tran_time), //订单发送时间，格式为YYYYMMDDhhmmss
		);
		
		com\unionpay\acp\sdk\AcpService::sign ( $params ); //签名
		$url = com\unionpay\acp\sdk\SDK_SINGLE_QUERY_URL;
		$result_arr = com\unionpay\acp\sdk\AcpService::post ($params, $url);
		if(count($result_arr)<=0) { //没收到200应答的情况
			$res['err'] ='没收到200应答';
			$res['res'] ='FAIL';
			return $res;
		}
		
		if (!com\unionpay\acp\sdk\AcpService::validate ($result_arr) ){
			$res['err'] ='应答报文验签失败';
			$res['res'] ='FAIL';
			return $res;
		}				
		
		//应答码判断
		if ($result_arr["respCode"] == "00"){
			if ($result_arr["origRespCode"] == "00"){				
				$res['res'] ='SUCCESS';//交易成功
			} else if ($result_arr["origRespCode"] == "03" || $result_arr["origRespCode"] == "04" || $result_arr["origRespCode"] == "05"){
				$res['res'] ='PROCESSING';//后续需发起交易状态查询交易确定交易状态
				$res['err'] ='交易处理中';
			} else {				
				$res['res'] ='FAIL';//其他应答码做以失败处理
				$res['err'] ="交易失败：" . $result_arr["origRespMsg"];
			}
		} else if ($result_arr["respCode"] == "03" || $result_arr["respCode"] == "04" || $result_arr["respCode"] == "05" ){
			$res['res'] ='PROCESSING';
			$res['err'] ='处理超时';
		} else {
			$res['res'] ='FAIL';//其他应答码做以失败处理
			$res['err'] ="交易失败：" . $result_arr["respMsg"];
		}
		
		$order_sn = $result_arr['orderId']; //订单号
		$respCode = $result_arr['respCode']; //应答码
		$trade_no = isset($result_arr['queryId']) ? $result_arr['queryId'] :'';//支付交易号
		$buyer_accno = isset($result_arr['accNo']) ? $result_arr['accNo'] : '';//支付账户
		$order_type = intval($result_arr['reqReserved']);//订单类型
		$total_fee = floatval($result_arr['txnAmt']); 
		
		if($order_type ==0)		
		{
			$order = get_order_info(0, $order_sn, 0);	
		}
		else {
			$order = get_pay_log($order_sn);
		} 
		
		if(!$order || count($order)==0)
		{
			logs('订单'.$order_sn.'不存在。', 'pay');
			$res['err'] ='订单'.$order_sn.'不存在。';
			return $res;
		}
		elseif($order['pay_status'] == pay_payed)
		{
			logs('订单'.$order_sn.'已支付', 'pay');
			$res['err'] = '订单'.$order_sn.'已支付';
			return $res;
		}
		
		//处理订单
		if($res['err'] =='')
		{				
			$ym_uid = $order['uid'];		
			if($order_type !=0)
			{
				update_pay_log($order_sn, 1, $pay_code, $trade_no, $buyer_accno, '');
				if($order_type ==1)
				{
					add_member_log($ym_uid, asset_balance, $total_fee, '充值'.$total_fee, $order_sn);
					update_account($ym_uid, $total_fee);//增加余额
				}
			}
			else {
				update_order_payment($order_sn, $trade_no, $pay_code, $buyer_accno, pay_payed, $_POST ['txnTime'], $order);
				add_order_log($order_sn, $ym_uid, $order['uname'], role_user, '支付订单');
			}
		}
		else {
			if($order_type ==0)
			{
				update_order(array('order_sn'=>$order_sn, 'trade_msg'=>$res['err'], 'pay_code'=>$pay_code)); 
			}
			else {
				update_pay_log($order_sn, 0, $pay_code, $trade_no, $buyer_accno, $res['err']);
			}
			logs($order_sn.$res['err'], 'pay');
		}
		
		return $res;
	}

}

?>