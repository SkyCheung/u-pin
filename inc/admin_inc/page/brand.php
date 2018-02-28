<?php
if (!defined('in_mx')) {exit('Access Denied');
}

$res = array('err' => '', 'res' => '', 'data' => array());
if($act)
{
	if(!checkAuth($login_id, 'brand'))//权限检测
	{
		$res['err'] = $lang['access_denied'];
		die(json_encode($res));
	}
	if ($act == 'edit_name') //编辑名称
	{
		$row=$db->fetch('brand','*', 'name="'.$c_name.'" and id<>"'.intval($id).'"');
		if ($row) {
			$res['err'] = '名称已存在，请更换。';
			echo json_encode($res);
			die();
		}
		$db -> update('brand', array('name' => trim($val)), array('id' => trim($id)));
	
		$res['res'] = '更新成功';
		echo json_encode($res);
		die();
	} 
	elseif ($act == 'edit_sort')//编辑排序
	{
		if (!is_numeric(trim($val))|| strlen(trim($val))>6) {
			$res['err'] = '必须是数字，且是6位之内';
			echo json_encode($res);
			die();
		}
	
		$db -> update('brand', array('sort' => trim($val)), array('id' => trim($id)));
		$res['res'] = '更新成功';
		echo json_encode($res);
		die();
	}
	elseif ($act == 'edit_recommend')//编辑推荐
	{
		if (!is_numeric(trim($val))) {
			$res['err'] = '必须是数字';
			echo json_encode($res);
			die();
		}
	
		$db -> update('brand', array('recommend' => trim($val)), array('id' => trim($id)));
		$res['res'] = '更新成功';
		echo json_encode($res);
		die();
	} 
}
else {
	checkAuth($login_id, 'brand');//权限检测 
	$page=intval($page)==0?1:intval($page);
	$pagenum=12; 
	$startI = $page*$pagenum-$pagenum;
	$count =$db->rowcount('brand','');
	$pages = getPages($count,$page, $pagenum);

	$row = $db -> fetchall('brand', '*', '', ' name asc', $startI . ',' . $pagenum);
}
?>