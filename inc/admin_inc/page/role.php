<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*编辑角色*/

if($act)
{
	$res = array('err' => '', 'res' => '', 'data' => array());
	if(!checkAuth($login_id, 'role'))//权限检测
	{
		if($type =='ajax')
		{
			$res['err'] = $lang['access_denied'];
			die(json_encode($res));
		}
		else {
			message($lang['access_denied']);
		}	
	}
	if($act =='add' || $act =='edit')
	{
		$menu =json_encode(get_menu());
		$do = "role.".$act;
		if($type =='ajax')
		{
			$res['data'] = $menu;
			die(json_encode($res));
		}
		if($act =='edit')
		{
			$row = get_role_info($id);
			$role_menu = json_encode($row['mid_arr']);
			$cbk_status[$row['status']] = 'checked="checked"';
		}
	}
	elseif ($act == 'edit_status')
	{
		$val = intval($val);	 
		update_role(array('status' => $val), trim($id));
		/*if($val == 0)
		{
			del_role_user($id, 0);//删除用户角色权限
		}*/
		cache_perm($id, 0); //缓存权限
		$res['res'] = '更新成功';
		die(json_encode($res));
	}
	elseif ($act == 'delete')
	{
		if(!isset($id) || trim($id)=='' || !is_numeric($id)){message("获取编号失败");} 
		$db->delete("sys_role", array('id'=>intval($id)));
		del_role_user($id, 0);//删除用户角色权限
		cache_perm($id, 0); //缓存权限
		message("删除成功", $url);
	}
}
else {
	checkAuth($login_id, 'role');//权限检测 
	$page=intval($page)==0?1:intval($page);
	$pagenum = 12; 
	$startI = $page*$pagenum-$pagenum;
	$count =$db->rowcount('sys_role', '');
	$pages = getPages($count,$page, $pagenum);
	
	$row = get_role('', null, $startI, $pagenum);
}
	
?>