<?php
if (!defined('in_mx')) {exit('Access Denied');}

require_once './inc/lib/system.php';

if(isset($act))
{
	$res = array('err' => '', 'res' => '', 'data' => array());
	if(!checkAuth($login_id, 'cron'))//权限检测
	{
		$res['err'] = $lang['access_denied'];
		die(json_encode($res));
	}
	if($act =='add' || $act =='edit')
	{
		$do ="cron.edit";
		if($act =='edit')
		{
			if(!isset($id) || intval($id)==0)
			{
				message("获取信息失败");
			}
			
			$row = $db->fetch('cron', '*', array('id'=>$id));
			if(!$row || count($row)==0)
			{
				message("获取信息失败");
			}
			$row['json_week'] = json_encode(explode(",", $row['week']));
			$row['json_day'] = json_encode(explode(",", $row['day']));
			$row['json_hour'] = json_encode(explode(",", $row['hour']));
			$row['json_minute'] = json_encode(explode(",", $row['minute']));
			//print_r($row)
			/*$cbk = 'checked="checked"';
			foreach ($row['week'] as $k => $v) {
				$cbk_week[$v] = $cbk;
			}*/
		}
	}
	elseif($act =='edit_status')
	{
		if(!isset($id) || trim($id)=='' || !is_numeric($id)){message("获取编号失败");} 
		update_cron($id, array('status'=>intval($val)));
		$res['res'] = '更新成功';
		die(json_encode($res));
	}
	elseif($act =='delete')
	{
		if(!isset($id) || trim($id)=='' || !is_numeric($id)){message("获取编号失败");} 
		$db->delete("cron", array('id'=>intval($id)));
		$res['res'] = '删除成功';
		die(json_encode($res));
	}
}
else {
	checkAuth($login_id, 'cron');//权限检测 

	
	$row = get_cron_list();
}


?>