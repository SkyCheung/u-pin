<?php

/**获取快递模板*/
function get_exp_tpl($where='')
{
	global $db;	
	if ($where == '') {
		return false;
	}

	return $db->fetch('express_common', '*', $where);	
}

/**更新快递模板*/
function update_exp_tpl($exp_id=0, $data)
{
	global $db;
	$db->update('express_common', $data, array("id"=>intval($exp_id)));		
}

/**批量更新订单表*/
function update_order_patch($order, $where)
{
	global $db;
	$db->update('order', $order, $where);
}

?>