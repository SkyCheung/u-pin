<?php
if (!defined('in_mx')) {exit('Access Denied');}

/**快递鸟快递接口   申请http://www.kdniao.com/ServiceApply.aspx
 * 如使用 物流跟踪api 请在快递鸟后台http://www.kdniao.com/UserCenter/Dev/Index.aspx 填写 回调地址http://域名/plugin.html?mod=express@kdniao@callback
 * */
require_once plugin.'/express/kdniao/kdniaosdk.php'; 

class kdniao
{
	private $config;
	public function __construct()
	{
		$ym_express_track= get_cache('express_track', cache_static);
		$this->config = $ym_express_track['kdniao'];
	}

	const url_query='http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx';	
	const url_eorder='http://api.kdniao.cc/api/Eorderservice'; //测试正式地址  http://testapi.kdniao.cc:8081/api/Eorderservice
	const url_trace='http://api.kdniao.cc/api/dist';//测试请求地址   http://testapi.kdniao.cc:8081/api/dist

	/*即时查询api 订单物流轨迹*/
	function query($no, $exp_code, $orderCode='')
	{
		$res = array('res'=>'','err'=>'', 'data'=>array());	
		$requestData = json_encode(array('OrderCode' => $orderCode,'ShipperCode' => $exp_code,'LogisticCode' => $no));
		
		$datas = array(
	        'EBusinessID' => $this->config['appid'],
	        'RequestType' => '1002',
	        'RequestData' => urlencode($requestData) ,
	        'DataType' => '2',
	    );
	    $datas['DataSign'] = encrypt($requestData, $this->config['appkey']); 
		$row = sendPost(self::url_query, $datas);
		$row = json_decode($row,true);
		if($row['Traces'])
		{
			rsort($row['Traces']);
		}
		
		$res['data'] = $row['Traces'];
		$res['err'] = $row['Reason']==null ?'':$row['Reason'];
		return $res;
	}
	
	/**提交电子面单
	 */
	function eOrder($requestData){
		$datas = array(
	        'EBusinessID' => $this->config['appid'],
	        'RequestType' => '1007',
	        'RequestData' => urlencode($requestData) ,
	        'DataType' => '2',
	    );
	    $datas['DataSign'] = encrypt($requestData, $this->config['appkey']);
		return sendPost(self::url_eorder, $datas);	
	}
	
	/**
	 * 物流跟踪api 物流信息订阅 
	 */
	function trace($no, $exp_code, $orderCode=''){ 
		$requestData="{'OrderCode': '".$orderCode."','ShipperCode':'".$exp_code."','LogisticCode':'".$no."'}";

		$datas = array(
	        'EBusinessID' => $this->config['appid'],
	        'RequestType' => '1008',
	        'RequestData' => urlencode($requestData) ,
	        'DataType' => '2',
	    );
	    $datas['DataSign'] = encrypt($requestData, $this->config['appkey']);
		return sendPost(self::url_trace, $datas);
	}
}

?>