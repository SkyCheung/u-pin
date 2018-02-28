<?php
if (!defined('in_mx')) {exit('Access Denied');
}

$res = array('err' => '', 'res' => '', 'data' => array());

if($act)
{
	if(!checkAuth($login_id, 'category'))//权限检测
	{
		$res['err'] = $lang['access_denied'];
		die(json_encode($res));
	}
	//编辑分类名称
	if ($act == 'edit_name') {	
		if(check_catname($cat_name, $pid, $id))
		{
			$res['err'] = '名称已存在，请更换。'; echo json_encode($res);die();
		}
	
	 	$db->update('category', array('name' => trim($val)), array('id' => trim($id)));
		
		update_config();//更新缓存
		$res['res'] = '更新成功';	 
		echo json_encode($res);die();
	}
	elseif ($act == 'edit_sort') //编辑排序
	{
		if (!is_numeric(trim($val))|| strlen(trim($val))>6) {
			$res['err'] = '必须是数字，且是6位之内。'; echo json_encode($res);die();
		}
		
		$db->update('category', array('sort' => trim($val)), array('id' => trim($id)));
		
		update_config();//更新缓存
		$res['res'] = '更新成功';	 
		echo json_encode($res);
		die();
	} 
}
else {
	checkAuth($login_id, 'category');//权限检测
	$row = get_category(0, true, -1);
}
?>