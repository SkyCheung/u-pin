<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*活动选择*/
require_once './inc/lib/admin/promotion.php';

if($act && $act =='get_promotion')
{
	$res = array('err' => '', 'res' => '', 'data' => array()); 

	$condition='status=1';
	if(isset($word) && $word!='') //分类
	{
		$condition .=" and name like '%".$word."%'";
	}
 
	$page = intval($page)>0 ? intval($page) : 1;
	$num = isset($num)? intval($num) : 10;
	$startI = ($page-1)*$num;
	
	$count = $db->rowcount('sp_discount', $condition); 
	$res['total'] = ceil($count/$num);//总页数
	$res['count'] = $db->rowcount('sp_discount', $condition);
	$res['data'] = get_discount_list('and '.$condition, $startI , $num); //print_r($res); die();
	
	die(json_encode($res));
}
else {
	
}
	
?>