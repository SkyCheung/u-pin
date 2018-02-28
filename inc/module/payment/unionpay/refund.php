<?php
if (!defined('in_mx')) {exit('Access Denied');}

/**银联退款
 * 交易说明： 1）以后台通知或交易状态查询交易确定交易成功，建议发起查询交易的机制：可查询N次（不超过6次），每次时间间隔2N秒发起,即间隔1，2，4，8，16，32S查询（查询到03，04，05继续查询，否则终止查询）
 *        2）退货金额不超过总金额，可以进行多次退货
 *        3）退货能对11个月内的消费做（包括当清算日），支持部分退货或全额退货，到账时间较长，一般1-10个清算日（多数发卡行5天内，但工行可能会10天），所有银行都支持
 * */

header ( 'Content-type:text/html;charset=utf-8' );
include_once pay_root. 'unionpay/sdk/acp_service.php';

/*退款*/
function unionpay_refund($order)
{
	$pay_time = time();
	$data = array('is_success'=>0, 'trade_no'=>'', 'trade_msg'=>'', 'pay_time'=>$pay_time); //退款结果统一格式	
	$params = array(
		//以下信息非特殊情况不需要改动
		'version' => '5.0.0',		      //版本号
		'encoding' => 'utf-8',		      //编码方式
		'signMethod' => '01',		      //签名方法
		'txnType' => '04',		          //交易类型
		'txnSubType' => '00',		      //交易子类
		'bizType' => '000201',		      //业务类型
		'accessType' => '0',		      //接入类型
		'channelType' => ($ym_client ==1 ?'07':'08'),		      //渠道类型
		'backUrl' => REFUND_NOTIFY_URL, //后台通知地址
		
		//以下信息需要填写
		'orderId' => $order["refund_no"],	    //商户退款订单号，8-32位数字字母，不能含“-”或“_”
		'merId' => merId,	        //商户代码
		'origQryId' => $order["trade_no"], //原消费的queryId
		'txnTime' => date('YmdHis', $pay_time),	   //订单发送时间，格式为YYYYMMDDhhmmss
		'txnAmt' => floatval($order["refund_fee"]) * 100,  //交易金额，单位为分，退货总金额需要小于等于原消费
		//'reqReserved' =>'0',            //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据
	);logs($data['pay_time'].' '.date('YmdHis',$data['pay_time']));
	//print_r($params);
	com\unionpay\acp\sdk\AcpService::sign($params); // 签名
	$url = com\unionpay\acp\sdk\SDK_BACK_TRANS_URL;
	
	$result_arr = com\unionpay\acp\sdk\AcpService::post($params, $url); //print_r($result_arr);
	if(count($result_arr)<=0) { //没收到200应答的情况
		$data['trade_msg'] = '银联没收到200应答，可能网络有问题';
		return $data;
	}
	if (!com\unionpay\acp\sdk\AcpService::validate($result_arr) ){
		$data['trade_msg'] = "应答报文验签失败";
		return $data;
	}
	
	if(isset($result_arr["queryId"]))
	{
		$data['trade_no'] =  $result_arr["queryId"];
	}
	if ($result_arr["respCode"] == "00"){
	   $data['is_success'] =1;//银联受理成功，请稍后刷新页面查看结果
	} 
	else if ($result_arr["respCode"] == "03" || $result_arr["respCode"] == "04" || $result_arr["respCode"] == "05" ){
	    $data['trade_msg'] = "银联处理超时，请稍后查询。";
	} else {
	    $data['trade_msg'] = $result_arr["respMsg"];//其他应答码做以失败处理
	}
	return $data;
	
	exit();
}

?>
