<?php
/**
 *
 * 快递鸟电子面单接口
 *
 * @技术QQ: 4009633321
 * @技术QQ群: 200121393
 * @see: http://www.kdniao.com/MiandanAPI.aspx
 * @copyright: 深圳市快金数据技术服务有限公司
 * 
 * ID和Key请到官网申请：http://www.kdniao.com/ServiceApply.aspx
 */

//电商ID
defined('EBusinessID') or define('EBusinessID', '请到快递鸟官网申请http://www.kdniao.com/ServiceApply.aspx');
//电商加密私钥，快递鸟提供，注意保管，不要泄漏
defined('AppKey') or define('AppKey', '请到快递鸟官网申请http://www.kdniao.com/ServiceApply.aspx');
//请求url，接口正式地址：http://api.kdniao.cc/api/Eorderservice
defined('ReqURL') or define('ReqURL', 'http://testapi.kdniao.cc:8081/api/Eorderservice');


//调用获取物流轨迹
//-------------------------------------------------------------

//构造电子面单提交信息
$eorder = array();
$eorder["ShipperCode"] = "SF";
$eorder["OrderCode"] = "PM201604062341";
$eorder["PayType"] = 1;
$eorder["ExpType"] = 1;

$sender = array();
$sender["Name"] = "李先生";
$sender["Mobile"] = "18888888888";
$sender["ProvinceName"] = "李先生";
$sender["CityName"] = "深圳市";
$sender["ExpAreaName"] = "福田区";
$sender["Address"] = "赛格广场5401AB";

$receiver = array();
$receiver["Name"] = "李先生";
$receiver["Mobile"] = "18888888888";
$receiver["ProvinceName"] = "李先生";
$receiver["CityName"] = "深圳市";
$receiver["ExpAreaName"] = "福田区";
$receiver["Address"] = "赛格广场5401AB";

$commodityOne = array();
$commodityOne["GoodsName"] = "其他";
$commodity = array();
$commodity[] = $commodityOne;

$eorder["Sender"] = $sender;
$eorder["Receiver"] = $receiver;
$eorder["Commodity"] = $commodity;

//调用电子面单
$jsonParam = json_encode($eorder, JSON_UNESCAPED_UNICODE);

//$jsonParam = JSON($eorder);//兼容php5.2（含）以下

echo "电子面单接口提交内容：<br/>".$jsonParam;
$jsonResult = submitEOrder($jsonParam);
echo "<br/><br/>电子面单提交结果:<br/>".$jsonResult;

//解析电子面单返回结果
$result = json_decode($jsonResult, true);
echo "<br/><br/>返回码:".$result["ResultCode"];
if($result["ResultCode"] == "100") {
	echo "<br/>是否成功:".$result["Success"];
}
else {
	echo "<br/>电子面单下单失败";
}
//-------------------------------------------------------------




  
?>