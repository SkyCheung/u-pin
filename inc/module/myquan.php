<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*我的优惠券*/
require  './inc/lib/coupon.php';

if($act)
{
	$res = array('err' => '', 'res' => '', 'data' => array());
	if($act == 'get_coupon') //获取我的优惠券
	{
		$db = dbc();
		
		$ym_uid = intval($ym_uid);
		$is_available = isset($is_available) ? intval($is_available) : 0; 
		$is_count = isset($is_count) ? intval($is_count) : 0; 
		$page = intval($page)==0 ? 1 : intval($page);
		$num = isset($num) ? intval($num) : 12; 
		$startI = $page * $num - $num;		
		$type = isset($type) && !isLetter($type) ? intval($type) : -1; 
		
		update_couponuser_expire();//todo
		$coupon = get_mycoupon($ym_uid,$type, $startI, $num);
		if($is_available == 1)
		{
			require_once './inc/lib/cart.php';
			require  './inc/lib/promotion.php';
			$cart = get_cart(1);
			$coupon = set_coupon_enable($coupon, $cart, $ym_client);
		}
		
		$res['data'] = $coupon; 
		if($is_count ==1)
		{
			$res['count'] = get_mycoupon_sum($ym_uid, $type);
		} 
	}
	elseif($act =='select_coupon')//选择优惠券
	{
		$db = dbc();
		
		$ym_uid = intval($ym_uid);
		$id = intval($id);
		$ids = isNumComma($ids) ? $ids : '';	
		if($id == 0){
			$res['err'] ='当前所选优惠券不能为空';
			die(json_encode_yec($res));
		}
		if($ids == ''){
			$res['err'] ='已选优惠券不能为空';
			die(json_encode_yec($res));
		}
		
		$coupon = get_coupon_list("and id in(".$ids.")"); 
		require_once './inc/lib/cart.php';
		require  './inc/lib/promotion.php';
		$cart = get_cart(1);
		$res['data'] = select_coupon($coupon, $cart, $id);
	}
	//print_r($res);
	die(json_encode_yec($res));
}

$nav_header = get_nav('top');
$nav = get_nav(); //导航
$cats = get_catTree(); //分类树
$help = get_help(); //帮助
$nav_footer = get_nav('bot'); //底部导航
 
?>