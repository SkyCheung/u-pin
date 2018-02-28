<?php
if (!defined('in_mx')) {exit('Access Denied');}


$payment  = get_payment('alipay_wap'); //print_r($payment);
if(!$payment || count($payment) ==0)
{
	die("手机版支付宝还没有安装");
}
$config = $payment[0]['config'];  //print_r($payment);
if(!$config['appid'] || trim($config['appid']) =='')
{
	die("请先在后台配置支付宝信息");
}

//合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
$alipay_config['partner']	= $config['partner'];

//开放平台 应用ID
$alipay_config['appid']		= $config['appid'];

//收款支付宝账号，以2088开头由16位纯数字组成的字符串，一般情况下收款账号就是签约账号
$alipay_config['seller_id']	= $config['partner'];

// privateKey 商户私钥
$alipay_config['private_Key'] = $config['privateKey'];

// alipay_public_key公钥
$alipay_config['alipay_public_key'] = $config['alipayPublicKey'];

//private_key证书路径地址
$alipay_config['private_key_filepath'] = pay_root.'alipay_wap/RSA/rsa_private_key.pem';

global $ym_url;
// 服务器异步通知页面路径  需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
$alipay_config['notify_url'] = $ym_url."paynotify_alipay_wap_async.html"; 

// 页面跳转同步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
$alipay_config['return_url'] = $ym_url."paynotify_alipay_wap.html";  

$alipay_config['refund_notify_url'] = $ym_url."paynotify_alipay_refund.html"; //退款异步通知地址

//退款日期 时间格式 yyyy-MM-dd HH:mm:ss
//date_default_timezone_set('PRC');//设置当前系统服务器时间为北京时间，PHP5.1以上可使用。
$alipay_config['refund_date']=date("Y-m-d H:i:s",time());

//退款调用的接口名，无需修改
$alipay_config['refund_service']='refund_fastpay_by_platform_pwd';

// 公用回传参数
//$alipay_config['extra_common_param'] = "alipay";

//签名方式
$alipay_config['sign_type']    = strtoupper('RSA');

//字符编码格式 目前支持 gbk 或 utf-8
$alipay_config['input_charset']= strtolower('utf-8');

//ca证书路径地址，用于curl中ssl校验
//请保证cacert.pem文件在当前文件夹目录中
$alipay_config['cacert']    = pay_root."alipay/cacert.pem";  //getcwd()

//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$alipay_config['transport']    = 'http';

// 支付类型 ，无需修改
$alipay_config['payment_type'] = "1";
		
// 产品类型，无需修改
$alipay_config['service'] = "alipay.wap.create.direct.pay.by.user";//= "create_direct_pay_by_user";

//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
 
?>