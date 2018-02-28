<?php
namespace com\unionpay\acp\sdk;

if (!defined('in_mx')) {exit('Access Denied');}

global $ym_url;

if(!function_exists('get_payment'))
{
	require_once './inc/lib/pay.php'; 
}

$payment_info  = get_payment('unionpay');
if(!$payment_info || count($payment_info) ==0)
{
	die("银联支付还没有安装");
}
$config = $payment_info[0]['config'];
if(!$config['merId'] || trim($config['merId']) =='')
{
	die("请先在后台配置银联支付信息");
}
 
// 签名证书路径
define('SDK_SIGN_CERT_PATH', approot.'/inc/module/payment/unionpay/certs/acp_prod_sign.pfx');

// 密码加密证书（这条一般用不到的请随便配）
//const SDK_ENCRYPT_CERT_PATH = 'D:/certs/acp_test_enc.cer';
define('SDK_ENCRYPT_CERT_PATH', approot.'/inc/module/payment/unionpay/certs/acp_prod_enc.cer');

define('merId', $config['merId']);

// 签名证书密码
//const SDK_SIGN_CERT_PWD = '000000';
define('SDK_SIGN_CERT_PWD', $config['signcertpwd']);//$config['signcertpwd']

// 验签证书路径（请配到文件夹，不要配到具体文件）
//const SDK_VERIFY_CERT_DIR = 'G:/www/ymshop/web/inc/module/payment/unionpay/certs/';
define('SDK_VERIFY_CERT_DIR', approot.'/inc/module/payment/unionpay/certs/');

// 前台请求地址
const SDK_FRONT_TRANS_URL = 'https://gateway.95516.com/gateway/api/frontTransReq.do';

// 后台请求地址
const SDK_BACK_TRANS_URL = 'https://gateway.95516.com/gateway/api/backTransReq.do';

// 批量交易
const SDK_BATCH_TRANS_URL = 'https://gateway.95516.com/gateway/api/batchTrans.do';

//单笔查询请求地址
const SDK_SINGLE_QUERY_URL = 'https://gateway.95516.com/gateway/api/queryTrans.do';

//文件传输请求地址
const SDK_FILE_QUERY_URL = 'https://filedownload.95516.com/';

//有卡交易地址
const SDK_Card_Request_Url = 'https://gateway.95516.com/gateway/api/cardTransReq.do';

//App交易地址
const SDK_App_Request_Url = 'https://gateway.95516.com/gateway/api/appTransReq.do';

// 前台通知地址 (商户自行配置通知地址)
//const SDK_FRONT_NOTIFY_URL = 'http://localhost:8085/upacp_demo_b2c/demo/api_01_gateway/FrontReceive.php';
define('SDK_FRONT_NOTIFY_URL', $ym_url.'paynotify_unionpay.html');

// 后台通知地址 (商户自行配置通知地址，需配置外网能访问的地址)
//const SDK_BACK_NOTIFY_URL = 'http://222.222.222.222/upacp_demo_b2c/demo/api_01_gateway/BackReceive.php';
define('SDK_BACK_NOTIFY_URL', $ym_url.'paynotify_unionpay_async.html');

//退款后台通知地址
define('REFUND_NOTIFY_URL', $ym_url.'paynotify_unionpay_refund.html');

//文件下载目录 
//const SDK_FILE_DOWN_PATH = 'D:/file/';
define('SDK_FILE_DOWN_PATH', approot."inc/logs/");

//日志 目录 
//const SDK_LOG_FILE_PATH = 'D:/logs/';
define('SDK_LOG_FILE_PATH', approot."inc/logs/");

//日志级别，关掉的话改PhpLog::OFF
const SDK_LOG_LEVEL = PhpLog::OFF;


/** 以下缴费产品使用，其余产品用不到，无视即可 */
// 前台请求地址
const JF_SDK_FRONT_TRANS_URL = 'https://gateway.95516.com/jiaofei/api/frontTransReq.do';
// 后台请求地址
const JF_SDK_BACK_TRANS_URL = 'https://gateway.95516.com/jiaofei/api/backTransReq.do';
// 单笔查询请求地址
const JF_SDK_SINGLE_QUERY_URL = 'https://gateway.95516.com/jiaofei/api/queryTrans.do';
// 有卡交易地址
const JF_SDK_CARD_TRANS_URL = 'https://gateway.95516.com/jiaofei/api/cardTransReq.do';
// App交易地址
const JF_SDK_APP_TRANS_URL = 'https://gateway.95516.com/jiaofei/api/appTransReq.do';

?>