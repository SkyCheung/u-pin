<?php
if (!defined('in_mx')) {exit('Access Denied');}

require_once './inc/lib/admin/promotion.php';

if(isset($act))
{
	$res = array('err' => '', 'res' => '', 'data' => array());
	if(!checkAuth($login_id, 'business'))//权限检测
	{
		$res['err'] = $lang['access_denied'];
		if($act =='edit_status')
		{
			die(json_encode($res));
		}
		else {
			message($res['err']);
		}		
	}
	if($act =='add' || $act =='edit')
	{
		require_once './inc/lib/admin/member.php';
		$grade = get_grade();
		$grade_count = count($grade);
		$do ="sp_discount.edit";
		if($act =='edit')
		{
			if(!isset($id) || intval($id)==0)
			{
				message("获取促销活动失败");
			}
			$checked = 'checked="checked"';
			$row = get_discount_info($id);
			$cbk_status[$row['status']] = $checked;
			$cbk_promotion_type[$row['type']] = $checked; 
			
			$grade_arr = $row['grade_arr'];
			if($grade_arr != '0')
			{
				foreach ($grade as $k => $v) {
					foreach ($grade_arr as $key => $val) {
						if($v['grade_id'] == $val)
						{
							$grade[$k]['checked'] = $checked;
						}
					}
				}
			}
		}
	}
	elseif($act =='delete')
	{
		if(!isset($id) || trim($id)=='' || !is_numeric($id)){message("获取编号失败");} 
		$db->delete("sp_discount", array('id'=>intval($id)));
		message("删除成功", $url);
	}
	elseif($act =='edit_status')
	{
		if(!isset($id) || trim($id)=='' || !is_numeric($id)){$res['err']="获取不到id";die(json_encode($res));} 
		update_discount($id,array('status'=>$val));
		$res['res'] = '更新成功';
		die(json_encode($res));
	}
}
else {
	checkAuth($login_id, 'business');//权限检测 
	$page=intval($page)==0?1:intval($page);
	$pagenum = 12; 
	$startI = $page*$pagenum-$pagenum;
	$count =$db->rowcount('sp_discount', '');
	$pages = getPages($count,$page, $pagenum);
	
	$row = get_discount_list('', $startI, $pagenum);
}


?>