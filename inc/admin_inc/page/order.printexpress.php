<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*打印快递单*/
checkAuth($login_id, 'order');//权限检测
 
$ids = isset($ids) ? $ids : $_COOKIE['print_express'];
if(!$ids || trim($ids)=='')
{
	message("请先选择订单");
} 

$ym_express_common = get_cache('express_common');
if(!$ym_express_common || count($ym_express_common)==0)
{
	message("获取快递单模板失败");
}

$delivery = get_order_exp_info($ids); 
if(!$delivery || count($delivery)==0)
{
	message("获取订单信息失败");
}
$row = array();
$fail_list = array(); 
$expname_list = array();
foreach ($delivery as $k => $v) {	
	if($v['status'] == order_deliver || $v['status'] == order_receiving) // 正在发货/等待收货
	{
		$exp_code = $ym_express_common[$v['exp_id']]['code'];
		$exp_name = $ym_express_common[$v['exp_id']]['name'];
		if(!in_array($exp_name, $expname_list))
		{
			array_push($expname_list, $exp_name);
		}
		if($exp_code == null){$exp_code='';$exp_name='';}
		
		//获取地址
		$cnee_dist_ids = explode(",", $v['cnee_dist_ids']);
		$province = get_distict_name($cnee_dist_ids[0]);
		$city = get_distict_name($cnee_dist_ids[1]);
		$town = get_distict_name($cnee_dist_ids[2]); 
		$addr = ($cnee_dist_ids[3] ? get_distict_name($cnee_dist_ids[3]) : '') . $v['cnee_address'];  //还有下一级 ，放详细地址里
		
		//获取快递模板		
		$tmp_tpl = $ym_express_common[$v['exp_id']]['tpl'];
		if($tmp_tpl !='')
		{ 		
			$tmp_tpl = json_decode($tmp_tpl,true);
			$tmp_tpl = str_replace("寄件人{send_name}", $ym_return_name, $tmp_tpl);
			$tmp_tpl = str_replace("寄件人手机{send_mobile}", $ym_return_tel, $tmp_tpl);
			$tmp_tpl = str_replace("寄件人完整地址{send_address}", $ym_return_addr, $tmp_tpl);
			$tmp_tpl = str_replace("姓名{name}", $v['cnee_name'], $tmp_tpl);
			$tmp_tpl = str_replace("手机{mobile}", $v['cnee_mobile'], $tmp_tpl);
			$tmp_tpl = str_replace("完整地址{address}", $v['address'], $tmp_tpl);
			$tmp_tpl = str_replace("省{province}", $province, $tmp_tpl);
			$tmp_tpl = str_replace("市(县){city}", $city, $tmp_tpl);
			$tmp_tpl = str_replace("区(镇){town}", $town, $tmp_tpl);
			$tmp_tpl = str_replace("详细地址{addr}", $addr, $tmp_tpl);
			$tpl = json_decode($tmp_tpl,true);
		}
		$tpl['order_sn'] = $v['order_sn'];
		$tpl['exp_code'] = $exp_code;
		$tpl['exp_name'] = $exp_name;
		$row[]	= $tpl;
		
		$delivery[$k]['exp_code'] = $exp_code;
		$delivery[$k]['exp_name'] = $exp_name;
		$delivery[$k]['send_name'] = $ym_return_name;
		$delivery[$k]['send_mobile'] = $ym_return_tel;
		$delivery[$k]['send_address'] = $ym_return_addr;
		$delivery[$k]['name'] = $v['cnee_name'];
		$delivery[$k]['mobile'] = $v['cnee_mobile'];
		$delivery[$k]['address'] = $v['address'];
		$delivery[$k]['province'] = $province;
		$delivery[$k]['city'] = $city;
		$delivery[$k]['town'] = $town;
		$delivery[$k]['addr'] = $addr;
	}
	else {
		array_push($fail_list, $v['order_sn']);
	}
} //print_r($row);

foreach ($ym_express_common as $k => $v) {
	$ym_express_common[$k]['tpl'] = json_decode($v['tpl']);
}

$json_delivery =json_encode($delivery);
$cur_expcode = count($delivery)==1 ? $exp_code : '';
 
?>
