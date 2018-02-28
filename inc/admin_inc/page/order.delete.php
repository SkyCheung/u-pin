<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*删除订单*/
$row = get_order_status_patch($id);
if(!$row || count($row) == 0)
{
	message("获取订单信息失败。");
}

$fail_list = '';
$del_list = array();
foreach ($row as $k => $v) {
	if($v['status'] == order_paying || $v['status'] ==order_refund  || $v['status'] ==order_finish)
	{	
		array_push($del_list, $v['order_sn']);
	}
	else {
		$fail_list .= $v['order_sn']. '、';
	}
}

if(count($del_list)>0)
{
	update_order_patch(array('status'=>order_del), 'order_sn '.create_in($del_list));
}

message("删除成功！".($fail_list!=''? '以下订单失败，该状态下不能删除：'.$fail_list:''), $url);

exit();

?>