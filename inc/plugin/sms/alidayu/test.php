<?php
    include "TopSdk.php";
    date_default_timezone_set('Asia/Shanghai'); 

	$c = new TopClient;
    $c->appkey = '23422797';
    $c->secretKey = '5814ced3a5f2f83c7e0aa11b7e9380da';
    $req = new AlibabaAliqinFcSmsNumSendRequest;
	$req ->setExtend("");
	$req ->setSmsType("normal");
	$req ->setSmsFreeSignName("万鱼网");
	$req ->setSmsParam('{"code":"56565635","product":"万鱼网"}');
	$req ->setRecNum("15820685545");
	$req ->setSmsTemplateCode("SMS_12895119");
	$resp = $c ->execute($req); // print_r($resp);
	
	$res = array('code'=>'','err'=> '');
	if($resp->result->success !='true')
	{
		$arr = (array)$resp;
		$res['code'] = $arr['sub_code'];
		$res['err'] = $arr['sub_msg'];
	}	
	return $res;

?>