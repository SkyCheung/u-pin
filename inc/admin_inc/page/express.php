<?php
if (!defined('in_mx')) {exit('Access Denied');
}

$res = array('err' => '', 'res' => '', 'data' => array());
if($act)
{
	if(!checkAuth($login_id, 'express'))//权限检测
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
	
		$db -> update('express_common', array('sort' => trim($val)), array('id' => trim($id)));
		$res['res'] = '更新成功';
		echo json_encode($res);
		die();
	}
	elseif ($act == 'edit_status')//编辑推荐
	{
		if (!is_numeric(trim($val))) {
			$res['err'] = '必须是数字';
			echo json_encode($res);
			die();
		}
	
		$db -> update('express_common', array('status' => trim($val)), array('id' => trim($id)));
		$res['res'] = '更新成功';
		echo json_encode($res);
		die();
	} 
} 
else {
	checkAuth($login_id, 'express');//权限检测
	$grade = $db -> fetchall('member_grade', '*');
	$express_type = $db -> fetch('shop_config', 'value', array('`key`'=>'express_type')); 
	$cbk_express_type[$express_type['value']]=" checked='checked'"; 
	
	$gradelist=array();
	foreach ($grade as $k => $v) {
		$gradelist[$v['grade_id']]= $v['grade_name'];
	}
	$row=$db->fetchall('express', '*', array(), 'sort asc'); 
	if(count($gradelist)>0)
	{
		foreach ($row as $k => $v) {
			$grade_ids = explode(',', $v['grade_id']);
			$grade_name='';
			foreach ($grade_ids as $key => $val) {
				$grade_name .= $gradelist[$val].'、';
			}
			$row[$k]['grade_name'] = empty($grade_name)? '所有会员' : rtrim($grade_name,'、');
		}
	}

	$page=intval($page)==0?1:intval($page);
	$pagenum=20; 
	$startI = $page*$pagenum-$pagenum;
	$count =$db->rowcount('express_common','');
	$pages = getPages($count,$page, $pagenum);
	 
	$express = $db -> fetchall('express_common', '*', '', ' sort asc', $startI . ',' . $pagenum);
}
?>