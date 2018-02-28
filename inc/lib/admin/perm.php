<?php
if (!defined('in_mx')) {exit('Access Denied');}

/**检测权限
 * @param $uid int 用户ID
 * @param $perm_code string 权限代码*/
function checkAuth($uid, $perm_code, $is_return =false)
{
	global $lang;
	$ym_perm = get_cache($uid, cache_static."perm/", 'perm');
	if(!$ym_perm || count($ym_perm) ==0)
	{	
		return false; 		
	}
	if(in_array($perm_code, $ym_perm))
	{
		return true;
	}
	else {
		if($is_return == false)
		{
			message($lang['access_denied'], $url);
		}
		return false;
	}
}

/*权限*/
function get_user_menu($uid)
{
	global $db;
	return $db->queryall("select * from ".$db->table('sys_menu')." WHERE status=1 and find_in_set(id, (SELECT group_concat(r.mid separator ',') mid FROM ".$db->table('sys_role_user')." rs join ".$db->table('sys_role')." r on rs.role_id=r.id  WHERE rs.uid=".intval($uid).") ) order by `sort` ");	
}

//
function get_menu($where=array())
{
	global $db;
	
	return $db->fetchall('sys_menu','*', $where);	
}

//获取角色
function get_role_info($id)
{
	global $db;
	$row = $db->fetch('sys_role', "*", array('id'=>intval($id)));
	$row['mid_arr'] = explode(",", $row['mid']);
	return $row;
}

//获取角色列表
function get_role($ids='', $status =null ,$startI =0, $pagenum=null)
{
	global $db;
	$where  ='1';
	$limit ='';
	if($pagenum != null)
	{
		$limit = $startI.','. $pagenum;
	}
	if($ids !='')
	{
		$where .= " and id in(".$ids.")";
	}
	if($status != null)
	{
		$where .= " and status=".intval($status);
	}
	return $db->fetchall('sys_role', "*", $where,'', $limit);
}

//添加角色
function add_role($data)
{
	global $db;
	$db->insert('sys_role', $data);
}

//编辑角色
function update_role($data, $id)
{
	global $db;
	$db->update('sys_role', $data, array('id'=>intval($id)));
}

function get_role_user($uid)
{
	global $db;
	return $db->fetchall('sys_role_user','*', array('uid'=>intval($uid)));
}

//添加用户所属角色
function add_role_user($role_id, $uid)
{
	global $db;
	$db->insert('sys_role_user', array('role_id'=>intval($role_id), 'uid'=>intval($uid)));
}

//删除用户所属角色/删除某角色
function del_role_user($role_id=0, $uid=0)
{
	global $db;
	$where = array();
	if($role_id !=0)
	{
		$where['role_id'] =intval($role_id);
	}
	if($uid !=0) {
		$where['uid'] =intval($uid);
	}
	if($role_id==0 && $uid==0)
	{
		return false;
	}
	$db->delete('sys_role_user', $where);
}

function cache_perm($role_id=0, $uid=0)
{
	global $db;
	$where ='';
	if($uid !=0)
	{
		$where .=" and u.id=". intval($uid);
	}
	if($role_id !=0)
	{
		$where .=" and r.id=". intval($role_id);
	}
	$row = $db->queryall("SELECT distinct u.id,m.url FROM ".$db->table('user')." u left join ".$db->table('sys_role_user')." ru on u.id=ru.uid join ".$db->table('sys_role')." r on ru.role_id=r.id join ".$db->table('sys_menu')." m on find_in_set(m.id,r.mid) WHERE r.status=1 ".$where." order by u.id");
	$user = array();
	foreach ($row as $k => $v) {		
		$user[$v['id']][] = $v['url'];			
	}
	$php_pre = "<?php if (!defined('in_mx')) {exit('Access Denied');}".PHP_EOL;
	if(file_exists(cache_static."perm/")==false)
	{
		@mdir(cache_static."perm/");
	}
	foreach ($user as $k => $v) {
		$data= array_to_string($v); 
		write_file(cache_static."perm/".$k.".php", $php_pre."\$ym_perm=".$data.";");
	}
}

function del_userperm($uid=0)
{
	del_dir(cache_static."perm/".$uid.".php");
}

?>