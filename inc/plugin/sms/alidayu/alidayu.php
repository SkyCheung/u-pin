<?php
	if (!defined('in_mx')) {exit('Access Denied');}
    include "TopSdk.php";
    date_default_timezone_set('Asia/Shanghai'); 
	
	$sms_mod = array();
	$sms_mod['code']    = "alidayu"; //代码
	$sms_mod['name']    = '阿里大于';//名称
	$sms_mod['config']  =  json_encode(array('appkey'=>'','secretKey'=>'','reg'=>'','login'=>'','updatepwd'=>'','setpaypwd'=>'','changemobile'=>''));//配置信息
	$sms_param=array(
		array('name'=>'smsconfig_appkey','value'=>'appkey','type'=>'text','desc'=>'申请地址：<a href="http://www.alidayu.com/" target="_blank" class="lnk">http://www.alidayu.com</a>'),
		array('name'=>'smsconfig_secretKey','value'=>'secretKey','type'=>'text','desc'=>''),
		array('name'=>'smsconfig_reg','value'=>'注册模板','type'=>'text','desc'=>'需提供参数：${product} 和 ${code}'),
		array('name'=>'smsconfig_login','value'=>'登录模板','type'=>'text','desc'=>'需提供参数：${product} 和 ${code}'),
		array('name'=>'smsconfig_updatepwd','value'=>'修改密码模板','type'=>'text','desc'=>'需提供参数：${product} 和 ${code}'),
		array('name'=>'smsconfig_setpaypwd','value'=>'设置支付密码模板','type'=>'text','desc'=>'需提供参数：${product} 和 ${code}'),
		array('name'=>'smsconfig_changemobile','value'=>'更改手机号码模板','type'=>'text','desc'=>'需提供参数：${product} 和 ${code}'),
		array('name'=>'smsconfig_order','value'=>'订单提醒模板','type'=>'text','desc'=>'有新订单时，短信提醒。需提供参数：${order_sn}'),
	); 

class alidayu
{
	function sendsms($recNum, $appkey,$secretKey,$signName='',$param='',$tplcode='',$extend='',$sms_type='code')
	{
		//$param =$sms_type=='code' ? str_replace('#code', $param, $subject) :$param;
		$c = new TopClient;
	    $c->appkey = "".$appkey."";
	    $c->secretKey = $secretKey;
	    $req = new AlibabaAliqinFcSmsNumSendRequest;
		$req ->setExtend($extend);
		$req ->setSmsType("normal");
		$req ->setSmsFreeSignName($signName);
		$req ->setSmsParam($param);
		$req ->setRecNum($recNum);
		$req ->setSmsTemplateCode($tplcode);
		$resp = $c ->execute($req);
		
		$res = array('code'=>'','err'=> '');
		if($resp->result->success !='true')
		{
			$arr = (array)$resp;
			$res['code'] = $arr['code'];
			$res['sub_code'] = $arr['sub_code'];
			$res['err'] = $arr['sub_code']=='isv.BUSINESS_LIMIT_CONTROL' ? '每分钟只能发1条，每小时5条。' : ($arr['msg'] .' '.$arr['sub_msg']);
		}	
	 
		return $res;
	}
}	
	

?>