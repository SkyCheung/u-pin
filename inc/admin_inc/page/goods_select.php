<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*商品选择*/

if($act && $act =='get_goods')
{
	require_once  './inc/lib/promotion.php';
	$res = array('err' => '', 'res' => '', 'data' => array()); 
	//选择商品
	$condition='';
	if(isset($word) && $word!='') //分类
	{
		$condition .=" and g.name like '%".$word."%'";
	}
	if($catid != '') //分类
	{
		$condition .=" and cat_id in(".$catid.")";
	}
	
	$page = intval($page)>0 ? intval($page) : 1;
	$num = isset($num)? intval($num) : 10;
	$startI = ($page-1)*$num;
	
	$count = $db->get_count($db->table('goods')." g JOIN " . $db->table('category') . " c ON g.cat_id = c.id where g.status=". goods_up . " and uptime<=".time().$condition);
	$total = ceil($count/$num);
	$goods = get_goods_list($condition, 'g.*', '', $startI , $num,1,1);
	$res['total'] = $total;
	$res['count'] = $count;
	$res['data'] = $goods;
	
	die(json_encode($res));
}
else {
	$cat = array_query('pid', 0, $ym_cats, false); //商品分类
}
	
?>