<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*编辑角色*/  

if($act)
{
	$res = array('err' => '', 'res' => '', 'data' => array());
	if(!checkAuth($login_id, 'user'))//权限检测
	{
		$res['err'] = $lang['access_denied'];
		die(json_encode($res));
	}
	if($act =='add' || $act =='edit')
	{
		$role = get_role('', 1);
		$do = "user.add";
		if($act =='edit')
		{
			$row = get_adm_userinfo($id);	
			$role_user = get_role_user($id);
			foreach ($role_user as $k => $v) {
				$cbk_role[$v['role_id']] = 'checked="checked"';
			}
			$cbk_status[$row['status']] = 'checked="checked"';
		}
	}
	elseif ($act == 'edit_status')
	{
		if (!is_numeric(trim($val))) {
			$res['err'] = '必须是数字';
			echo json_encode($res);
			die();
		}
	 
		update_adm_user(array('status' => trim($val),'updatetime'=>time()), trim($id));
		$res['res'] = '更新成功';
		die(json_encode($res));
	}
	elseif ($act == 'delete')
	{
		if(!isset($id) || trim($id)=='' || !is_numeric($id)){message("获取编号失败");} 
		delete_adm_user($id);
		del_role_user(0, $id);//删除用户所属角色
		del_userperm($id); //删除权限缓存
		message("删除成功", $url);
	}
}
else {
	checkAuth($login_id, 'user');//权限检测
	
	$page=intval($page)==0?1:intval($page);
	$pagenum = 12; 
	$startI = $page*$pagenum-$pagenum;
	$count =$db->rowcount('user', '');
	$pages = getPages($count,$page, $pagenum);
	
	$row = get_adm_user('', $startI, $pagenum);
}
	
?>