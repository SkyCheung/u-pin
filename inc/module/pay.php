<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*支付*/
require_once './inc/lib/pay.php'; 

dbc();
$err_code='';
$order_type = isset($order_type) ? intval($order_type) : 0;
if($act)
{
	if($ym_uid ==0)
	{	
		$ym_uid = check_login(1);
	}
	if($ym_uid ==0)
	{
		$res['err']='登录超时，请重新登录'; 
		$res['url']= 'login.html';
		die(json_encode($res));
	}
	if($act == "get_payhtml") //获取支付按钮
	{
		$res['data'] = get_pay_html();
		$res['err_code'] = $err_code;
		die(json_encode($res));
	}
	elseif ($act =='get_sign') 
	{
		$res = array('err' => '', 'res' => '', 'data' => array());
		try
		{
			global $ym_name;
			if(empty($oid) || !is_numeric($oid))
			{
				$res['err']='请提供正确订单号'; 
				die(json_encode($res));
			}
			
			$order = get_order(0, $oid);
			$order['com_param'] = json_encode(array('is_split'=>$is_split, "order_type"=>$order_type));
			switch ($pay_code)//生成签名等信息给APP 
			{
				case 'alipay_app':
			    	require_once pay_root."alipay_app/alipay.config.php";
					require_once pay_root.'alipay_wap/aop/AopClient.php'; 
					require_once pay_root.'alipay_wap/aop/request/AlipayTradeAppPayRequest.php'; 
					
					$aop = new AopClient;
					$aop->appId = $alipay_config['appid']; 
					$aop->rsaPrivateKeyFilePath = $alipay_config['private_key_filepath']; 
					
					$request = new AlipayTradeAppPayRequest();
					$request->setNotifyUrl($alipay_config['notify_url']);
					$request->setBizContent('{' .
					'seller_id:"'.$alipay_config['seller_id'].'",' .
					'subject:"'.$ym_name.'订单",' .
					'out_trade_no:"'.$order['order_sn'].'",' .
					'total_amount:"'.$order['payble_amount'].'",' .
					'product_code:"QUICK_MSECURITY_PAY",' .
					'passback_params:"'.$order['com_param'].'",' .
					'body:""' .
					'}'); 
					
					$str = $aop->get_sign($request);
					ksort($str);
					$res['res'] = http_build_query($str);
					break;
				case 'wxpay_app':
					$order['pay_code'] = $pay_code;
					require_once pay_root.'wxpay_app/wxpay_app.php'; 
					$order['body']= $ym_name.'订单';
					$wxpay = new wxpay_app();
					$res = $wxpay ->get_payHtml($order);
					break;
				case 'unionpay':
					$order['pay_code'] = $pay_code;
					require_once pay_root.'unionpay/unionpay.php'; 
					$order['body']= $ym_name.'订单';
					$unionpay = new unionpay();
					$res = $unionpay ->get_payHtml($order, array(), true);
					break;	
				default:
					break;
			}
		}
		catch(exception $ex)
		{
			$res['err'] = $ex;
		}
		die(json_encode($res));
	}	
	elseif ($act == "check_paystatus") //检测是否支付
	{
		$res = array('err' => '', 'res' => '', 'data' => array());
		$err_code ='';
		$order = array();
		if($pay_code =='unionpay') //todo 暂时只是银联从接口查  || $pay_code =='wxpay_app'
		{
			$res = query_pay('', $oid, $pay_code, $pay_time);
			if($res['res'] == 'SUCCESS')
			{
				$result = pay_payed ;
			}
			else {
				$result = $res;
			}			
		}
		elseif($ym_client == client_app)
		{			
			$err = get_pay_html($act);	
			if($err_code =='ORDERPAID')
			{
				$res['err'] ='';
				$res['res'] ='ok';
			}
			else {
				$res['err'] = $err!='' ? $err : $order['trade_msg'];
			}			
			die(json_encode($res));
		}
		else {			
			$err = get_pay_html($act);	
			if($err_code =='ORDERPAID')
			{
				$result = pay_payed ;
			}
			else {
				$result['err'] = $err;
			}
		} 
		die(json_encode($result));
	}
}
else {
	$ym_uid = check_login();
	$nav = get_nav(); //导航
	$nav_footer = get_nav('bot');
	$cats = get_catTree(); //分类树
	$help = get_help(); //帮助
	
	$err = '';	
	$order = array();
	$pay_again = 0;
	$payable =0;
	$is_qrcode =0;
	$payment = array();		

	if($pay_amount && isset($_POST['submit']))//充值/分批支付/取消分批
	{
		$pay_amount = floatval($pay_amount);
		$p_order_sn = $oid ? $oid :'';
		$order_type = intval($order_type); 
		$pay_id = $pay_id ? $pay_id: '';
		$iscancel = $iscancel ? intval($iscancel) : 0;
		
		if($pay_id !='') //删除上次选择未支付的
		{
			$pay_log = get_pay_log($pay_id);
			if($pay_log && $pay_log['uid'] == $ym_uid)
			{
				del_pay_log($pay_id);
			}
		}
		
		if($iscancel ==1)
		{
			redirect("pay.html?oid=$oid&pay_amount=$pay_amount");
		}
		else {
			if($pay_amount<=0)
			{
				message("金额错误");
			}
			$pay_id = build_order_sn(1);
			add_pay_log($pay_id, $pay_amount, $order_type, $ym_uid, $p_order_sn);
			redirect("pay.html?oid=$pay_id&order_type=1&pay_amount=$pay_amount&pay_id=".$pay_id);
		}		
	}
	elseif(isset($_POST['codsubmit']))
	{
		$db->update('order', array("pay_code"=>$pay_code), array("order_sn"=>$oid,'uid'=>$ym_uid));
		redirect($_SERVER['HTTP_REFERER']);
		die();
	}
	
	$res = get_pay_html();
	$payhtml = $err!=''?'': $res['pay_html']; //print_r($payhtml);
	return;
}

function get_pay_html($act='')
{
	global $oid, $ym_uid,$err,$order,$pay_again,$payable,$payment,$pay_code,$payhtml,$is_qrcode,$err_code,$ym_name, $order_type,$pay_amount,$ym_client;
		
	if($order_type==0)//普通订单
	{	
		if(!isset($oid) || trim($oid) =='' || !is_num($oid))
		{
			$err = "订单号错误，请重新支付。";
			return $err;
		}
		$order = get_order_info(0, $oid, $ym_uid);		
		if(!$order || intval($order['id'])==0)
		{
			$err = "订单处理异常，请稍后再试或重新支付。";
			$pay_again=1;
			return $err;
		}
		if($order['pay_status'] == pay_payed)
		{
			$err = "订单已支付。";
			$err_code='ORDERPAID';
			return $err;
		}		
		if($order['pay_code'] == 'cod')
		{
			$err = "该订单您已选择货到付款，请收货后再付款。";
			return $err;
		}
		$pay_id = $pay_id ? trim($pay_id) :'';
		if($pay_id !='')
		{
			$order = get_pay_log($pay_id);
			if($order['pay_status'] == pay_payed)
			{
				$err = "订单已支付。";
				$err_code ='ORDERPAID';
				return $err;
			}
		}
	}
	else{
		$order = get_pay_log($oid);
		if($act == "check_paystatus")
		{			
			if($order['pay_status'] == pay_payed)
			{
				$err = "订单已支付。";
				$err_code ='ORDERPAID';
				return $err;
			}
			return '';
		}
		
		$order = array('order_sn'=>$oid, 'payble_amount'=>$order['amount']);
	}
	if($act == "check_paystatus")
	{
		return '';
	}
	$payable = 1;
	
	try
	{
		if($action =='pay' && isLetter($pay_code)) {
			$db->update('order', array("pay_code"=>$pay_code), array("order_sn"=>$oid,'uid'=>$ym_uid));
			redirect("payresult.html?oid=".$oid);
		} 
		
		if($order_type ==0 || $pay_amount > 0)
		{
			$payment = get_payment('', 1, $ym_client);//支付方式
		}
		
		if($order_type !=0 && $pay_code==''){return;}
		if(!isset($pay_code) || $pay_code=='')
		{
			$isweixin = is_weixin();
			foreach ($payment as $k => $v) {
				if($v['pay_code'] == $order['pay_code'] && ($v['client'] == $ym_client || $v['client']==client_all) )//若之前选择的不同设备的，需要重新选择
				{
					$pay_code= $order['pay_code'];
				}
				if(strpos($v['pay_code'], 'alipay') ===0 && $isweixin) //微信端屏蔽支付宝
				{
					unset($payment[$k]);
					if($v['pay_code'] == $order['pay_code']) 
					{
						$pay_code='';
					}
				}
			}
			if(!isset($pay_code) || $pay_code=='')
			{
				return;
			}
		}
		else {
			foreach ($payment as $k => $v) {
				if(strpos($v['pay_code'], 'alipay') ===0 && $isweixin) //微信端屏蔽支付宝
				{
					unset($payment[$k]);
					break;
				}
			}
			$pay_code= trim($pay_code);
		}
		
		//$pay_code=isset($pay_code)? trim($pay_code): $order['pay_code'];
		$pay_file = pay_root.$pay_code."/".$pay_code.'.php';
		if(file_exists($pay_file))
		{
			require_once $pay_file;
			if(class_exists($pay_code))
			{
				$tmp=explode("_", $pay_code);
				$is_qrcode = $tmp[count($tmp)-1]=='native'? 1:0;
				
				$order['com_param'] = json_encode(array('is_split'=>($pay_id ==''?0:1), "order_type"=>$order_type));
				$order['order_type'] = ''.$order_type;
				$order['pay_code'] = $pay_code;
				$order['subject'] = $ym_name.'订单';
				$order['body'] = $ym_name.'订单';
				$pay = new $pay_code;
				return $pay->get_payhtml($order);
			}
		}
		else {
			$err = "支付方式不存在：".$pay_code;
			return $err;
		}
	}
	catch(exception $e)
	{
		
	}
}

?>