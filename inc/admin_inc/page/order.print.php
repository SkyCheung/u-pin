<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*打印发货单*/
checkAuth($login_id, 'order');//权限检测
 
$ids = isset($ids) ? $ids : $_COOKIE['print_delivery'];
if(!$ids || trim($ids)=='')
{
	message("请先选择订单");
} 
 
$delivery = $db->fetch('tpl', "*", array('type'=>tpl_delivery));
if(!$delivery || count($delivery)==0)
{
	message("请先在模板管理里设置发货单模板");
}

$order = get_order_list(0, '', 0," and order_sn in(".$ids.")",0, 999);

$delivery_tpl = $delivery['content'];

$res ='';
foreach ($order as $k => $v) {
	$tmp_tpl = str_replace("{order_sn}", $v['order_sn'], $delivery_tpl);
	$tmp_tpl = str_replace("{ym_name}", $ym_name, $tmp_tpl);
	$tmp_tpl = str_replace("{add_time}", $v['add_time'], $tmp_tpl);
	$tmp_tpl = str_replace("{name}", $v['name'], $tmp_tpl);
	$tmp_tpl = str_replace("{mobile}", $v['mobile'], $tmp_tpl);
	$tmp_tpl = str_replace("{address}", $v['address'], $tmp_tpl);
	
	$list_start = substr($tmp_tpl, strpos($tmp_tpl,'<tr id="goodslist">'));
	$list_end = substr($tmp_tpl, strpos($tmp_tpl,'<tr id="goodslist">'), strpos($list_start, '<tr>'));
	$total_num = 0;
	$total_amount = 0;
	$goods=array();
	foreach($v['goods'] as $key => $val){
		$str = str_replace('{sn}', $key+1, $list_end);
		$str = str_replace('{goods_name}', $val['name'], $str);
		$str = str_replace('{goods_sn}', $val['sn'], $str);
		$str = str_replace('{num}', $val['num'], $str);
		$str = str_replace('{price}', $val['price'], $str);
		$str = str_replace('{sub_amount}', ($val['num']*$val['price']), $str);
		$goods[$key] = $str;
		$total_num = $total_num + $val['num'];
		$total_amount =  $total_amount + $val['num']*$val['price'];
	}
	$goods=implode('', $goods);	
	$tmp_tpl=str_replace($list_end, $goods, $tmp_tpl);
	$tmp_tpl=str_replace('{total_num}', $total_num, $tmp_tpl);
	$tmp_tpl=str_replace('{total_amount}', format_price($total_amount), $tmp_tpl);
	
	$res .=$tmp_tpl;
}


?>
