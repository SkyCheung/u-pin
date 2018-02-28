<?php
if (!defined('in_mx')) {exit('Access Denied');}

//管理员角色
const adm_role_kefu = 2; //客服

/*用户*/
checkAuth($login_id, 'user');//权限检测

require_once './inc/lib/admin/perm.php';
if(!$act || $act =='')
{
	message("操作类型错误");
}
elseif ($act == 'add' || $act == 'edit') {
	$coname =isset($coname)?$coname:'';
	$username = trim($username);
	$pwd =trim($pwd);
	
	if($username =='')
	{
		message("请填写用户名");
	}
	if($pwd =='' && $act == 'add')
	{
		message("请填写密码");
	}

	if($act == 'add')
	{
		$salt= get_salt(); //生成新salt
		$pwd = encryptStr($pwd, $salt);
		$id = add_adm_user($username, $pwd,$salt, $coname, $status);			
		
	}
	elseif ($act == 'edit') {
		$data = array('username' => $username, 'coname' => trim($coname), 'status' => intval($status),'updatetime'=>time());
		if($pwd !='')
		{
			$salt= get_salt(); //生成新salt
			$pwd = encryptStr($pwd, $salt);
			$data['pwd'] = $pwd;
			$data['salt'] = $salt;
		}
		else {
			$user = get_adm_userinfo($id);
			$pwd = $user['pwd'];
		}
		update_adm_user($data, $id);		
		del_role_user(0, $id);
	}
	if($role_id && count($role_id)>0)
	{
		foreach ($role_id as $k => $v) {
			add_role_user($v, $id);
		}
		if($role_id && in_array(adm_role_kefu, $role_id)) //如果是客服，添加聊天用户
		{
			require_once("./inc/lib/chat.php");
			add_chat_user($id, $username, $pwd, imtype_crmuser);
		}
	}		
	cache_perm(0, $id); //缓存权限
	
	message("保存成功",'/admin.html?do=user');
}

?>