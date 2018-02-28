<?php
if (!defined('in_mx')) {exit('Access Denied');}

require_once './inc/lib/system.php';

if(isset($act))
{
	$res = array('err' => '', 'res' => '', 'data' => array());
	if(!checkAuth($login_id, 'app'))//权限检测
	{
		$res['err'] = $lang['access_denied'];
		die(json_encode($res));
	}
	if($act =='add' || $act =='edit')
	{
		$do ="app.edit";
		if($act =='edit')
		{
			if(!isset($id) || intval($id)==0)
			{
				message("获取失败");
			}
			$row = $db->fetch('app', '*', array('id'=>$id));
		}
	}
	elseif($act =='delete')
	{
		if(!isset($id) || trim($id)=='' || !is_numeric($id)){message("获取编号失败");} 
		$db->delete("app", array('id'=>intval($id)));
		message("删除成功", $url);
	}
}
else {
	checkAuth($login_id, 'app');//权限检测 
	$page=intval($page)==0?1:intval($page);
	$pagenum = 12; 
	$startI = $page*$pagenum-$pagenum;
	$count =$db->rowcount('app', '');
	$pages = getPages($count,$page, $pagenum);
	$type = intval($type)==0 ? 1 : $type;
	
	$row = get_app_list($type, $startI, $pagenum);
}


?>