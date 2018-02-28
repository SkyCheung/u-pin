<?php
if (!defined('in_mx')) {exit('Access Denied');}
 
checkAuth($login_id, 'message');//权限检测 
 
 
$row = $db->fetchall('sms_config', '*', ""); 
foreach($row as $key => $val) {
	$code = $val['code'];
	$sp_file = plugin."sms/".$code.'/'.$code.'.php';
	if(file_exists($sp_file)==false){continue;}
	
	$sms_param = array();
	require_once($sp_file);

	$row[$key]['config']= json_decode($val['config'], true);  	
	foreach ($sms_param as $k => $v) {
		$sms_param[$k]['key']= str_replace('smsconfig_', '', $v['name']);
		$sms_param[$k]['val'] = $row[$key]['config'][$sms_param[$k]['key']];
	}
	$row[$key]['config'] = $sms_param;
}
 
	 
?>