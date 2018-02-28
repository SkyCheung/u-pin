<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'order');//权限检测  

if(!isset($id) || intval($id)==0)
{
	message("获取编号失败");
}

$order = array('order_sn'=>$id, 'payble_amount'=>$payble_amount, 'cnee_name'=>$cnee_name, 'cnee_mobile'=>$cnee_mobile, 'cnee_tel'=>$cnee_tel, 'cnee_dist_ids'=>str_replace('-', ',', $cnee_dist_ids), 'cnee_dist_name'=>$cnee_dist_name, 'cnee_address'=>$cnee_address, 'exp_no'=>$exp_no);
update_order($order);

message("更新成功");

?>