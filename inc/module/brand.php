<?php
if (!defined('in_mx')) {exit('Access Denied');}



$nav = get_nav(); //导航
$cats = get_catTree(); //分类树
$help = get_help(); //帮助
$nav_footer = get_nav('bot'); //底部导航
$id =isset($id)? $id : 0;
$pagenum= isset($num) ? intval($num) :20; 
$page=intval($page)==0 ? 1 : intval($page);
$startI = $page * $pagenum - $pagenum;

dbc();
$brand = get_brand(0, '', 1 );
$sort_list = array('a1'=>'addtime desc','s1'=>'salenum desc', 'p1'=>'price asc', 'p2'=>'price desc');
$sort = (isset($sort) && trim($sort)!='') ? trim($sort) : 'a1';
$order = $sort_list[trim($sort)];
$sort_add_time = build_url('sort', 'a1');
$sort_sale = build_url('sort', 's1');	
$sort_price = build_url('sort', trim($sort)=='p2' ? 'p1': 'p2');
if($id){
	$condition .=" and g.brand_id =".$id;
}
$count =$db->get_count($db->table('goods')." g JOIN " . $db->table('category') . " c ON g.cat_id = c.id where g.status=". goods_up .$condition);
if ($count>0)
{
	$pages = getPages($count, $page, $pagenum);
	$goods = get_goods_list($condition, 'g.*', $order, $startI , $pagenum,1,1);
}
else {
	$goods='';
}

if($is_ajax==1)
{
	$res = array('err' => '', 'res' => '', 'data' => array()); 
	$res['data'] = $goods;
	die(json_encode_yec($res));
}

?>