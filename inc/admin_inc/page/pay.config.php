<?php
if (!defined('in_mx')) {exit('Access Denied');}
 
checkAuth($login_id, 'pay');//权限检测 
 
if ($id==''){message("获取不到支付方式编号");}
if(file_exists(pay_root.$id.'/'.$id.'.php')==false){message("获取不到支付方式文件 ".$id.'.php');}
require(pay_root.$id.'/'.$id.'.php');
 
$row = $db->fetch('payment', '*',  array('pay_code' => $id));
$pay_config = json_decode($row['config'], true);
$cbk_status[$row['status']] =' checked="checked"';

foreach ($payment_param as $k => $v) {
	$payment_param[$k]['key']= str_replace('payconfig_', '', $v['name']);
}
 
	 
?>