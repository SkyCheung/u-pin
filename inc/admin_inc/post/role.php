<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*角色*/
checkAuth($login_id, 'role');//权限检测

require_once './inc/lib/admin/perm.php';
if(!$act || $act =='')
{
	message("操作类型错误");
}
elseif ($act == 'add' || $act == 'edit') {
	$data =array(
		'name' => trim($name),
		'status' => intval($status),
		'mid' => trim($menu_ids),
		'memo' => trim($memo)
	);

	if($act == 'add')
	{
		add_role($data);
	}
	elseif ($act == 'edit') {		
		update_role($data, trim($id));
		if(intval($status) ==0)
		{
			del_role_user($id, 0);//删除用户角色权限
		}
		cache_perm($id, 0); //缓存权限 
	}
	
	message("保存成功",'/admin.html?do=role');
}

?>