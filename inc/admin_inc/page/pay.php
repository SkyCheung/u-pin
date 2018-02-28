<?php
if (!defined('in_mx')) {exit('Access Denied');
}

$res = array('err' => '', 'res' => '', 'data' => array());

if($act)
{
	if(!checkAuth($login_id, 'pay'))//权限检测
	{
		$res['err'] = $lang['access_denied'];
		die(json_encode($res));
	}
	if ($act == 'edit_sort')//编辑排序
	{
		if (!is_numeric(trim($val))|| strlen(trim($val))>6) {
			$res['err'] = '必须是数字，且是6位之内';
			die(json_encode($res));
		}
	
		$db -> update('payment', array('sort' => trim($val)), array('pay_code' => trim($id)));
		$res['res'] = '更新成功';
		die(json_encode($res));
	}
	elseif ($act == 'edit_status')//编辑排序
	{
		if (!is_numeric(trim($val))|| strlen(trim($val))>6) {
			$res['err'] = '必须是数字，且是6位之内';
			die(json_encode($res));
		}
	
		$db -> update('payment', array('status' => trim($val)), array('pay_code' => trim($id)));
		$res['res'] = '更新成功';
		die(json_encode($res));
	}
}
else {
	checkAuth($login_id, 'pay');//权限检测 
	$row = $db -> fetchall('payment', '*','','sort asc');
}
?>