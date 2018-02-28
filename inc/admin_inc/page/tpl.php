<?php
if (!defined('in_mx')) {exit('Access Denied');}
/*模板管理*/

if($act)
{
	$res = array('err' => '', 'res' => '', 'data' => array());
	if(!checkAuth($login_id, 'set'))//权限检测
	{
		$res['err'] = $lang['access_denied'];
		die(json_encode($res));
	}
	if($act == 'apply_tpl')
	{
		if(!$pc_tpl && !$m_tpl)
		{
			$res['err'] ='请选择模板';
			die(json_encode($res));
		}
		require_once('./inc/lib/admin/setting.php');
		update_shopconfig('pc_tpl', $pc_tpl);
		update_shopconfig('m_tpl', $m_tpl);
		update_shopconfig('app_tpl', $app_tpl);
		
		//更新缓存
		del_dir(cache.$ym_tpl.'/');
		update_config();
	}
	die(json_encode($res));
}

checkAuth($login_id, 'set');//权限检测 

$tplpath = upload_tpldetails; 

$row = getfiles($tplpath);
foreach ($row as $k => $v) {
	$row[$k]=urldecode(substr($v, 0,strrpos($v, '.'))) ;
}

$delivery = $db->fetch('tpl', "*", array('type'=>tpl_delivery));

$tpl = get_dir(tpldir); 


?>
