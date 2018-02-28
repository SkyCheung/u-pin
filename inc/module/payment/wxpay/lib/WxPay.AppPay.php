<?php
require_once pay_root . "wxpay/lib/WxPay.Api.php";

/**
 * app支付实现类
 * @author yec
 *
 */
class AppPay
{
	/**
	 * 
	 * 获取app支付的参数
	 * @param array $UnifiedOrderResult 统一支付接口返回的数据
	 * @throws WxPayException
	 * @return 数组
	 */
	public function GetParameters($UnifiedOrderResult)
	{
		if(!array_key_exists("appid", $UnifiedOrderResult)
		|| !array_key_exists("prepay_id", $UnifiedOrderResult)
		|| $UnifiedOrderResult['prepay_id'] == "")
		{
			throw new WxPayException("参数错误");
		}
		$jsapi = new WxPayAppPay();
		$jsapi->SetAppid($UnifiedOrderResult["appid"]);
		$timeStamp = time();
		$jsapi->SetTimeStamp("$timeStamp");
		$jsapi->SetNonceStr(WxPayApi::getNonceStr());
		$jsapi->SetPrepay_id($UnifiedOrderResult['prepay_id']);
		$jsapi->SetPackage('Sign=WXPay');
		$jsapi->SetMch_id(WXPAY_MCHID);//商户号
		$jsapi->SetPaySign($jsapi->MakeSign());				
		$vals= $jsapi->GetValues();
		//$vals = array_change_key_case($vals);
		return json_encode($vals);
	}
	
	/**
	 *
	 * 参数数组转换为url参数
	 * @param array $urlObj
	 */
	private function ToUrlParams($urlObj)
	{
		$buff = "";
		foreach ($urlObj as $k => $v)
		{
			$buff .= $k . "=" . $v . "&";
		}

		$buff = trim($buff, "&");
		return $buff;
	}

	/**
	 *
	 * 生成预支付
	 * @param UnifiedOrderInput $input
	 */
	public function GetPrepay($input)
	{
		try
		{
			return WxPayApi::unifiedOrder($input);
		} catch(exception $ex)
		{
			return $ex;
		}
	}

}
