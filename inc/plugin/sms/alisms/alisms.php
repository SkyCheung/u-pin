<?php
	if (!defined('in_mx')) {exit('Access Denied');}
    
    date_default_timezone_set('Asia/Shanghai'); 
	
	$sms_mod = array();
	$sms_mod['code']    = "alisms"; //代码
	$sms_mod['name']    = '阿里云短信';//名称
	$sms_mod['config']  =  json_encode(array('appkey'=>'','secretKey'=>'','reg'=>'','login'=>'','updatepwd'=>'','setpaypwd'=>'','changemobile'=>''));//配置信息
	$sms_param=array(
		array('name'=>'smsconfig_appkey','value'=>'appkey','type'=>'text','desc'=>'申请地址：<a href="https://dysms.console.aliyun.com/dysms.htm" target="_blank" class="lnk">https://dysms.console.aliyun.com/dysms.htm</a>'),
		array('name'=>'smsconfig_secretKey','value'=>'secretKey','type'=>'text','desc'=>''),
		array('name'=>'smsconfig_reg','value'=>'注册模板','type'=>'text','desc'=>'需提供参数 ${code}'),
		array('name'=>'smsconfig_login','value'=>'登录模板','type'=>'text','desc'=>'需提供参数 ${code}'),
		array('name'=>'smsconfig_updatepwd','value'=>'修改密码模板','type'=>'text','desc'=>'需提供参数 ${code}'),
		array('name'=>'smsconfig_setpaypwd','value'=>'设置支付密码模板','type'=>'text','desc'=>'需提供参数 ${code}'),
		array('name'=>'smsconfig_changemobile','value'=>'更改手机号码模板','type'=>'text','desc'=>'需提供参数  ${code}'),
		array('name'=>'smsconfig_order','value'=>'订单提醒模板','type'=>'text','desc'=>'有新订单时，短信提醒。需提供参数${order_sn}'),
	); 

/**阿里云短信*/	
class alisms	
{
    function sendsms($recNum, $appkey,$secretKey,$signName='',$param='',$tplcode='',$extend='',$sms_type='code')
	{
		include 'aliyun-php-sdk-core/Config.php';
    include_once 'Dysmsapi/Request/V20170525/SendSmsRequest.php';
    include_once 'Dysmsapi/Request/V20170525/QuerySendDetailsRequest.php';
		
	    //此处需要替换成自己的AK信息
	    $accessKeyId = $appkey;
	    $accessKeySecret = $secretKey;
	    //短信API产品名
	    $product = "Dysmsapi";
	    //短信API产品域名
	    $domain = "dysmsapi.aliyuncs.com";
	    //暂时不支持多Region
	    $region = "cn-hangzhou";
	    
	    //初始化访问的acsCleint
	    $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
	    DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
	    $acsClient= new DefaultAcsClient($profile);
	    
	    $request = new Dysmsapi\Request\V20170525\SendSmsRequest;
	    //必填-短信接收号码
	    $request->setPhoneNumbers($recNum);
	    //必填-短信签名
	    $request->setSignName($signName);
	    //必填-短信模板Code
	    $request->setTemplateCode($tplcode);
	    //选填-假如模板中存在变量需要替换则为必填(JSON格式)
	    $request->setTemplateParam($param);
	    //选填-发送短信流水号
	    //$request->setOutId("1234");
	    
	    //发起访问请求
	    $acsResponse = $acsClient->getAcsResponse($request);
	    $res = array('code'=>'','err'=> '');
	    if($acsResponse->Code !='OK')
		{
			$arr = (array)$acsResponse;
			$res['err'] = $arr['Code']=='isv.BUSINESS_LIMIT_CONTROL' ? '每分钟只能发1条，每小时5条。' : ($arr['Message'] .' '.$arr['Code']);
		}	
	}
}


?>