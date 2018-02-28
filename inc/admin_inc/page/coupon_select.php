<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*优惠券选择*/

if($act && $act =='get_coupon')
{
	require_once  './inc/lib/coupon.php';
	$res = array('err' => '', 'res' => '', 'data' => array()); 
	//选择优惠券
	$amount = intval($amount);
	$get_type = intval($get_type);
	$condition='';
	if(isset($word) && $word!='') //券名/号
	{
		$condition .=" and name like '%".addslashes($word)."%'";
	}
	if($amount != 0) //金额
	{
		$condition .=" and amount=".$amount;
	}
	if($get_type != 0) //发放方式
	{
		$condition .=" and get_type=".$get_type;
	}
	
	$page = intval($page)>0 ? intval($page) : 1;
	$num = isset($num)? intval($num) : 10;
	$startI = ($page-1)*$num;
	
	$count = $db->get_count($db->table('coupon')." where status=1 ".$condition);
	$total = ceil($count/$num);
	$data = get_coupon_list($condition,1, $startI, $num);
	$res['total'] = $total;
	$res['count'] = $count;
	$res['data'] = $data;
	
	die(json_encode_yec($res));
}
else {
	$cat = array_query('pid', 0, $ym_cats, false); //优惠券分类
}
	
?>