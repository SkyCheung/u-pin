<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*订单处理*/
require_once './inc/lib/cart.php';
require_once './inc/lib/pay.php'; 
require_once  './inc/lib/promotion.php';

if(isset($act))
{
	$res = array('err' => '', 'res' => '', 'data' => array());
	
	if ($act == 'edit_consignee')//保存收货地址
	{
		$id = !isset($id)? 0 : intval(trim($id));
		$name = !isset($name)? '' : trim($name);
		$mobile = !isset($mobile)|| !is_numeric(trim($mobile))? 0 : trim($mobile);
		$tel = !isset($tel)? '' : trim($tel);
		$district = !isset($district)? '' : trim($district);
		$address = !isset($address)? '' : trim($address);
		$is_default = !isset($isdefault)? 0 : intval($isdefault);
				
		if($name =='' || !is_consignee($name))
		{
			$res['err']= '请填写收货人，限中文或英文';
			die(json_encode_yec($res));
		}
		if($mobile== '' || !is_mobile($mobile))
		{
			$res['err']= '请填写手机号码';
			die(json_encode_yec($res));
		}
		if($tel !='' && !is_tel($tel))
		{
			$res['err']= '固定电话格式不正确';
			die(json_encode_yec($res));
		}
		if(trim($district)=='')
		{
			$res['err']= '请选择所在地区';
			die(json_encode_yec($res));
		}
		else {
			$district_list = explode('-', $district);
			if(count($district_list)!=4)
			{
				$res['err']= '请选择所在地区';
				die(json_encode_yec($res));
			}
			$province = intval($district_list[0]);
			$city = intval($district_list[1]);
			$area = intval($district_list[2]);
			$town = intval($district_list[3]); 
			if($province==0 || $city==0)
			{
				$res['err']= '请正确选择所在地区';
				die(json_encode_yec($res));
			}
		}
		if(strlen($address) < 3)
		{
			$res['err']= '请填写详细地址';
			die(json_encode_yec($res));
		}
		
		if($ym_uid==0) //需要登录
		{
			$ym_uid =check_login(1);
			if($ym_uid==0)
			{
				$res['url'] = 'login.html';
				die(json_encode_yec($res));
			}			
		}
		$db=dbc();
		$data=array('uid'=>$ym_uid, 'name'=>$name, 'mobile'=>$mobile, 'tel'=>$tel, 'province'=>$province, 'city'=>$city, 'area'=>$area, 'town'=>$town, 'address'=>$address, 'is_default'=>$is_default);
		if($id == 0)
		{			
			if($is_default==0)
			{
				$count = $db->rowcount("member_address", array('uid'=>$ym_uid));
				if($count ==0)
				{
					$data['is_default'] =1;
				}
			}
			$db->insert('member_address', $data);
			$res['res']= $db->lastinsertid();
		}
		else {
			$db->update('member_address', $data, array("uid"=> $ym_uid,"id"=> $id));
			$res['res']= $id;
		}
		if($is_default == 1) //将其它地址设为非默认
		{
			$db->update('member_address', array("is_default"=>0), "uid=".$ym_uid." and id<>".$res['res']); 
		}
	}
	elseif ($act == 'get_consignee')//获取收货地址
	{
		dbc();
		if($ym_uid==0) //需要登录
		{
			$ym_uid =check_login(1);
			if($ym_uid==0)
			{
				$res['url'] = 'login.html';
				die(json_encode_yec($res));
			}			
		}
		$res['data'] = get_consignee(intval($id), $ym_uid); 
	}
	elseif ($act == 'del_consignee')//删除收货地址
	{
		if(intval($id) ==0)
		{
			$res['err']= '地址编号不正确';
			die(json_encode_yec($res));
		}
		if($ym_uid==0) //需要登录
		{
			$ym_uid =check_login(1);
			if($ym_uid==0)
			{
				$res['url'] = 'login.html';
				die(json_encode_yec($res));
			}			
		}
		dbc();
		del_consignee(intval($id), $ym_uid); 
	}
	elseif ($act == 'get_expfee')//获取运费
	{
		dbc();
		$res['res'] = get_express_fee($ym_uid, array(), intval($id), $cityid); 
	}
	elseif ($act == 'add_order')//添加订单
	{
		if($ym_uid==0) //需要登录
		{
			$ym_uid =check_login(1);
			if($ym_uid==0)
			{
				$res['url'] = 'login.html';
				die(json_encode_yec($res));
			}			
		}		
		
		$cneeid = isset($cneeid)? intval($cneeid) : 0;
		$balance = isset($balance) && $ym_is_bal==1  ? abs(floatval(trim($balance))) : 0; 
		$point = isset($point)? abs(intval(trim($point))) : 0; //积分
		$coupon_ids = isset($coupon_ids) && isNumComma($coupon_ids) ? trim($coupon_ids) : '';
		$coupon_amount = 0; //优惠券金额
		$pay_code = isset($pay)? trim($pay) : '';
		$exp_id = isset($exp_id)? intval($exp_id) : 0;
		$paypwd = isset($paypwd)? trim($paypwd) : '';
		$user_remark = isset($user_remark)? trim($user_remark) : '';
		$point_amount =0; //积分抵用金额
		$invoice_title = $ym_is_invoice==1 && isset($invoice_title) ? trim($invoice_title) : '';//发票抬头
		$invoice_con = $ym_is_invoice==1 && isset($invoice_con) ? trim($invoice_con) : '';//发票内容		
		
		if($cneeid == 0)
		{
			$res['err'] = '请选择收货地址'; 
			die(json_encode_yec($res));
		}
		$ym_express = get_cache("express"); //配送方式
		if($ym_express_type == 1 && count($ym_express) == 0 && $exp_id == 0)
		{
			$res['err'] = '请选择配送方式';
			die(json_encode_yec($res));
		}
		if(mb_strlen($invoice_title) >200)
		{
			$res['err'] = '发票抬头长度超过200';
			die(json_encode_yec($res));
		}
		if(mb_strlen($invoice_con) >200)
		{
			$res['err'] = '发票内容长度超过200';
			die(json_encode_yec($res));
		}
		if(mb_strlen($user_remark) >200)
		{
			$res['err'] = '留言长度超过200';
			die(json_encode_yec($res));
		}		
	 
		$db=dbc();		
		
		$cart = get_cart(1);
		$cart_goods = $cart['goods'];
		if($cart['num'] == 0)
		{
			$res['err'] = '您的购物车还没有商品，请刷新页面后重试。'; 
			die(json_encode_yec($res));
		}
		$goods_ids = array();
		$sql_goods='';
		$sql_number =''; //更新库存
		$sql_number_spec =''; //更新库存
		$sql_salenum ='';//更新销售数量
		$sql_salenum_spec ='';//更新销售数量
		$err ='';
		foreach ($cart_goods as $k => $v) {
			$goods_ids[] = $v['goods_id'];
			/*$cart_goods[$v['goods_id']] = $cart_goods[$v['goods_id']];
			unset($cart_goods[$k]);*/
			if($v['num'] >$v['number'])
			{
				$err .= '库存不足，商品【'.$v['name'].'】库存只有'.$v['number'].$v['unit'].'<br>'; 
			}
			if($v['goods_status'] != goods_up) //已下架
			{
				$err .= '商品【'.$v['name'].'】已下架<br>'; 
			}
			if($v['goods_status'] == goods_up && $v['uptime']> time()) //未上架
			{
				$err .= '商品【'.$v['name'].'】还没有上架<br>'; 
			}
			$sql_goods .="('order_id', ".intval($v['goods_id']).", '".$v['spec_name']."', ".intval($v['num']).", ".($v['user_price']!=0?floatval($v['user_price']): floatval($v['price'])).", ".floatval($v['costprice']).", '".addslashes($v['name'])."',".intval($v['point'])."),";
			if($v['spec_ids'] =='')
			{
				$sql_number .=" when ".$v['goods_id']." then number-". intval($v['num'])."";
				$sql_salenum .=" when ".$v['goods_id']." then salenum+". intval($v['num'])."";
			}
			else {
				$sql_number_spec .=" when ".$v['goods_id']." then number-". intval($v['num'])."";
				$sql_salenum_spec .=" when ".$v['goods_id']." then salenum+". intval($v['num'])."";
			}
		}
		if($err !='')
		{
			$res['err'] = $err;
			die(json_encode_yec($res));
		}
		//$goods = get_goods_list(implode(',', $goods_ids), '','', 0, null, 0);		
		
		if($coupon_ids !='') //优惠券
		{			
			require  './inc/lib/coupon.php';
			$coupon_amount_res = get_coupon_amount($coupon_ids, $ym_uid, $cart); 
			if($coupon_amount_res['err'] !='')
			{
				$res['err'] = $coupon_amount_res['err']; 
				die(json_encode_yec($res));				
			}
			$coupon_amount = $coupon_amount_res['res'];
		}
				
		$user = get_user();				
		$consignee = get_consignee($cneeid, $ym_uid);
		if(count($consignee)==0)
		{
			$res['err'] = '请填写收货人信息'; 
			die(json_encode_yec($res));
		}			
		$consignee = $consignee[0];
		$exp_fee = format_price(get_express_fee($ym_uid, $cart, $exp_id, $consignee['city']));//运费
		$goods_amount = $cart['amount'];
		$amount = format_price($goods_amount + $exp_fee);
		$pay_amount =0; //使用余额和积分等支付的金额		
		
		if( $balance > 0 || $point>0)
		{
			session_start();
			if(isset($_SESSION['paypwd_err_count']) && intval($_SESSION['paypwd_err_count']) >=5)
			{
				$res['err'] = '您已输错5次支付密码，请稍后再试'; 
				die(json_encode_yec($res));
			}
			elseif($paypwd =='')
			{
				$res['err'] = '您当前的余额或积分，需要输入支付密码'; 
				die(json_encode_yec($res));
			}
			elseif(md5(md5($paypwd, $user['salt_pay'])) != $user['paypwd']) {
				$_SESSION['paypwd_err_count'] = intval($_SESSION['paypwd_err_count']) + 1;
				$res['err'] = '支付密码不正确';
				die(json_encode_yec($res));
			}
		}
		if($point>0)
		{
			if($point < $ym_pointpay)
			{
				$point =0;
			}
			if($point % $ym_pointpay !==0)
			{
				$res['err'] = "使用积分的数量必须是". $ym_pointpay ."的倍数"; 
				die(json_encode_yec($res));
			}
			$point_amount = $point / $ym_pointpay; //积分抵用金额
			if($point_amount > $amount * $ym_mostpoint * 0.01)
			{
				$res['err'] = "积分抵用金额最多不超过结算金额的".$ym_mostpoint."%"; 
				die(json_encode_yec($res));
			}			
		}
		
		$pay_amount = $point_amount + $balance + $coupon_amount; //使用余额 + 积分支付 + 优惠券
		if($amount < $pay_amount)
		{
			$res['err'] = "您使用积分抵用和余额之和大于应付金额"; 
			die(json_encode_yec($res));
		}
						
		$data = array(
			'uid' => $ym_uid,
			'amount' => $amount,//应付总金额
			'payble_amount' => abs($amount - $pay_amount), //未支付金额
			'goods_amount' => $goods_amount,
			'exp_amount' => $exp_fee,
			'balance_amount' => $balance,
			'point_amount' => $point_amount,
			'coupon_amount' => $coupon_amount,
			'coupon_ids' => $coupon_ids,
			'cashback' => 0,
			'cnee_name' => $consignee['name'],
			'cnee_mobile' => $consignee['mobile'],
			'cnee_tel' => $consignee['tel'],
			'cnee_dist_ids' => intval($consignee['province']).','.intval($consignee['city']).','.intval($consignee['area']).','.intval($consignee['town']),
			'cnee_dist_name' => $consignee['province_name'].$consignee['city_name'].$consignee['area_name'].$consignee['town_name'],
			'cnee_address' => $consignee['address'],
			'invoice_title' => $invoice_title,
			'invoice_con' => $invoice_con,
			'pay_code' => $pay_code,
			'exp_id' => $exp_id,
			'status' => order_paying,
			'pay_status' => pay_unpayed,
			'deliver_status' => deliver_not,
			'client' => $ym_client,
			'add_time' => time(),
			'user_remark' => $user_remark	
		);
		
		if($user['balance'] < $balance)
		{
			$res['err'] = '您的账户余额只有'.$user['balance'].'元，不足支付。'; 
			die(json_encode_yec($res));
		}
		if ($pay_amount >= $amount) { //用余额和积分等足够支付，设为已支付
			$data['status'] = order_deliver;
			$data['pay_status'] = pay_payed;
			$data['payble_amount'] = 0;
			$data['pay_code'] = 'bal';
			$data['pay_time'] = time();
		}
		if($pay_amount < $amount && $pay_code =='')
		{
			$res['err'] = '请选择支付方式'; 
			die(json_encode_yec($res));
		}
		if($pay_code=='cod') //货到付款
		{
			$data['status'] = order_deliver;
		}				
		
		$ok = false;
		do
		{	
			$data['order_sn'] = build_order_sn(); 
			$db->insert('order', $data);
			$order_id = $db->lastinsertid(); 
			$code = $db->errorinfo();			
			$ok = $code[1] == 1062;//防止重复			
		}while($ok==true);
		
		$sql = "insert into ".$db->table('order_goods')."(order_id, goods_id, spec, num, price, costprice, name, point) values";
		$sql .= rtrim(str_replace('order_id', $order_id, $sql_goods), ',');
		$db->query($sql);
		
		$goods_ids_str =implode(',', $goods_ids);
		//减少库存
		if($ym_stocktime == 1)
		{
			if($sql_number!='')
			{
				$db -> exec('update '.$db->table('goods').' set number= case goods_id '.$sql_number.' end where goods_id in('.$goods_ids_str.")");	
			}
			 if($sql_number_spec!='')
			{
				$db -> exec('update '.$db->table('goods').' set number= case goods_id '.$sql_number_spec.' end where goods_id in('.$goods_ids_str.")");	
			}
		}
		//更新销售量
		if($sql_salenum!='')
		{
			$db -> exec('update '.$db->table('goods').' set salenum= case goods_id '.$sql_salenum.' end where goods_id in('.$goods_ids_str.")");  
		}
		if($sql_salenum_spec!='')
		{
			$db -> exec('update '.$db->table('goods').' set salenum= case goods_id '.$sql_salenum_spec.' end where goods_id in('.$goods_ids_str.")");  
		}
		
		if($coupon_ids !='') //用了优惠券，设为已用或冻结
		{
			update_coupon_user_status($coupon_ids, $data['status'] == order_deliver ? coupon_used :coupon_freeze);
		}	
		if($balance >0) //用了余额
		{
			update_account($ym_uid, -$data['balance_amount'], 0);
			add_member_log($ym_uid, asset_balance, -$data['balance_amount'], '订单'.$data['order_sn'].' 使用余额'.$data['balance_amount']);			
		}
		if($point>0) //用了积分
		{
			update_account($ym_uid, 0, -$point);
			add_member_log($ym_uid, asset_point, -$point, '订单'.$data['order_sn'].' 使用积分'.$point);
		}
		
		add_order_log($data['order_sn'], $ym_uid, $ym_uname, role_user, '您提交了订单');
		
		//已付款或货到付款，发短信/微信提醒商家
		if(($pay_amount >= $amount || $pay_code=='cod') && $ym_notice_order_sms && $ym_notice_order_sms ==1) {
			require_once("./inc/lib/sms.php");
			sms_notice('', 'order', array('order_sn'=>$data['order_sn']));
		}	
	
		//清空购物车里相关的商品
		clear_cart(0, $ym_uid);
		
		//设置cookie购物车数量
		set_cookie('cnum', intval($_COOKIE['cnum']-$cart['num']), time() + 31536000);
		
		$res['res'] ='ok';
		//生成支付链接
		if($data['pay_status'] == pay_payed)
		{
			$res['url'] ="payresult.html?oid=".$data['order_sn'];
		}
		else {
			$res['url'] ="pay.html?oid=".$data['order_sn'];
		}
	}
	elseif ($act =='confirm_receiving') //确认收货
	{
		if(!isset($oid) || intval($oid) == 0)
		{
			$res['err'] = '获取订单号失败';
			die(json_encode_yec($res));
		}
		if($ym_uid==0) //需要登录
		{
			$ym_uid =check_login(1);
			if($ym_uid==0)
			{
				$res['url'] = 'login.html';
				die(json_encode_yec($res));
			}
		}		
		dbc();
		if(!isset($ym_uname) || empty($ym_uname))
		{
			$user = get_user($ym_uid);
			$ym_uname = $user['uname'];
		}				
		
		$order= get_order_info(0, $oid, $ym_uid);
		if($order['status'] == order_finish)
		{
			$res['err'] = '已确认收货';
			$res['res'] = 'ok';
			die(json_encode_yec($res));
		}
		$row = array("order_sn"=>$oid,'status'=>order_finish,'receiving_time'=>time());
		if($order['pay_code'] == 'cod')
		{
			$row['payble_amount']= 0;
		}
		
		//更新赠送积分
		$point = get_order_point($oid);
		update_account($ym_uid, 0, $point); 
		add_member_log($ym_uid, asset_point, $point, '购物获得积分，订单号：'.$oid);

		if($ym_ditribution_config['distrib_level']>0) //分销佣金
		{
			require_once "inc/lib/distrib.php";
			pay_commission($ym_uid, $ym_uname, $order['goods_amount']);
		}
		
		update_order($row,$ym_uid); 
		add_order_log($oid, $ym_uid, $ym_uname, role_user, '您已确认收货，感谢您在'. $ym_title .'购物');
		$res['res'] = 'ok';
	}
	elseif ($act =='order_cancel') //取消订单
	{
		if(!isset($oid) || is_num($oid) == false)
		{
			$res['err'] = '获取订单号失败';
			die(json_encode_yec($res));
		}
		if($ym_uid==0) //需要登录
		{
			$ym_uid =check_login(1);
			if($ym_uid==0)
			{
				$res['url'] = 'login.html';
				die(json_encode_yec($res));
			}
		}
		dbc();
		$order= get_order_info(0, $oid, $ym_uid);
		if(!$order || count($order) ==0)
		{
			$res['err'] = '获取订单失败';
			die(json_encode_yec($res));
		}
		if($order['status'] == order_deliver)
		{
			$res['err'] = '该订单正在发货，请联系客服申请取消';
			die(json_encode_yec($res));
		}
		if($order['status'] != order_paying)
		{
			$res['err'] = '该订单的状态不能取消';
			die(json_encode_yec($res));
		}

		$user = get_user($ym_uid);
		$balance = $user['balance'] + $order['balance_amount'];
		$point = $user['point'] +  $order['point_amount'] * $ym_pointpay;
		update_userinfo($ym_uid,array('balance'=>$balance , 'point'=>$point));
		update_order(array('status'=>order_cancel, 'order_sn'=>$oid));
		add_order_log($oid, $ym_uid, $ym_uname, role_user, '已取消订单');	
		
		$res['res'] = 'ok';	 				
	}
	elseif ($act =='get_exp_track')//获取物流轨迹 
	{
		$exp_no = isset($exp_no)?trim($exp_no):'';
		$exp_code = isset($exp_code)?trim($exp_code):'';
		if($exp_no=='' || $exp_code=='')
		{
			$res['err'] = '请提供快递单号和快递代码';
			die(json_encode_yec($res));
		}
		require_once './inc/lib/express.php'; 
		$exp = new express();
		$res = $exp->queryapi($exp_no, $exp_code);
	}
	die(json_encode_yec($res));
}

if($ym_uid==0) //需要登录
{
	$ym_uid =check_login(1);
}

dbc();

$nav = get_nav(); //导航
$nav_footer = get_nav('bot');
$cats = get_catTree(); //分类树
$help = get_help(); //帮助
$cart = get_cart(1);
$user = get_user();

$payment = get_payment('', 1, $ym_client); //支付方式

$isweixin = is_weixin();
foreach ($payment as $k => $v) {
	if(strpos($v['pay_code'], 'alipay') ===0 && $isweixin) //微信端屏蔽支付宝
	{
		unset($payment[$k]);
	}
}

if($ym_uid!=0)
{
	$consignee = get_consignee(0, $ym_uid); //收货地址 
	$express_fee = format_price(get_express_fee($ym_uid, $cart));//运费
}
else {
	$consignee = 0;
	$express_fee = 0;
}
$total = format_price($cart['amount'] + $express_fee);//总金额

$ym_express = get_cache("express"); //配送方式
if($ym_express_type==1 && count($ym_express) == 0)
{
	$express_common = get_cache("express_common");
}

?>