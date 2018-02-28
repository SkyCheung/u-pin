<?php
if (!defined('in_mx')) {exit('Access Denied');
}


$res = array('err' => '', 'res' => '', 'data' => array());

if($act)
{
	if(!checkAuth($login_id, 'nav'))//权限检测
	{
		$res['err'] = $lang['access_denied'];
		die(json_encode($res));
	}
	if ($act == 'edit_sort')//编辑排序
	{
		if (!is_numeric(trim($val))|| strlen(trim($val))>6) {
			$res['err'] = '必须是数字，且是6位之内';
			echo json_encode($res);
			die();
		}
	
		$db -> update('nav', array('sort' => trim($val)), array('id' => trim($id)));
		$res['res'] = '更新成功';
		echo json_encode($res);
		die();
	}
	elseif ($act == 'edit_status')//编
	{
		if (!is_numeric(trim($val))) {
			$res['err'] = '必须是数字';
			echo json_encode($res);
			die();
		}
	
		$db -> update('nav', array('status' => trim($val)), array('id' => trim($id)));
		$res['res'] = '更新成功';
		echo json_encode($res);
		die();
	}
	elseif ($act == 'edit_target')//编
	{
		if (!is_numeric(trim($val))) {
			$res['err'] = '必须是数字';
			echo json_encode($res);
			die();
		}
	
		$db -> update('nav', array('target' => trim($val)), array('id' => trim($id)));
		$res['res'] = '更新成功';
		echo json_encode($res);
		die();
	}  
}
else {
	checkAuth($login_id, 'nav');//权限检测
	$page=intval($page)==0?1:intval($page);
	$pagenum=12; 
	$startI = $page*$pagenum-$pagenum;
	$count =$db->rowcount('nav', '');
	$pages = getPages($count,$page, $pagenum);

	$row = $db -> fetchall('nav', "*,case type when 'top' then '头部导航栏' when 'mid' then '主导航栏' when 'bot' then '底部导航栏' else '' end as type_name ", '', 'type asc, sort asc', $startI . ',' . $pagenum);
}
?>