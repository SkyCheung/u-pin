<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*优惠券*/  
require_once './inc/lib/coupon.php';
require_once './inc/lib/admin/coupon.php';

if($act)
{
	$res = array('err' => '', 'res' => '', 'data' => array());
	if(!checkAuth($login_id, 'coupon'))//权限检测
	{
		$res['err'] = $lang['access_denied'];
		die(json_encode($res));
	}
	if($act =='add' || $act =='edit')
	{		
		$grade = get_grade();
		$grade_count = count($grade);
		$cat = array_query('pid', 0, $ym_cats, false);			
				
		$checked = 'checked="checked"';
		$do = "coupon.edit";
		if($act =='edit')
		{
			$row = get_couponinfo($id);
			$item_ids = implode(",", $row['items_ids']);
			
			if($row['type'] == coupon_type_cat)
			{
				$cat_selected = $row['items'];
			}
			elseif($row['type'] == coupon_type_goods)
			{
				$goods_count = count($row['items']);
			}
						
			$cbk_status[$row['status']] = $checked;
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
	elseif ($act == 'give')
	{
		$grade = get_grade();
		$grade_count = count($grade);
		$do = "coupon.give";
	}
	elseif ($act == 'edit_status')
	{
		if (!is_numeric(trim($val))) {
			$res['err'] = '必须是数字';
			echo json_encode($res);
			die();
		}
	 
		update_coupon(array('status' => trim($val)), trim($id));
		$res['res'] = '更新成功';
		die(json_encode($res));
	}
	elseif ($act == 'delete')
	{
		if(!isset($id) || trim($id)=='' || !is_numeric($id)){message("获取编号失败");} 
		delete_adm_coupon($id);
		del_role_coupon(0, $id);//删除用户所属角色
		del_couponperm($id); //删除权限缓存
		message("删除成功", $url);
	}
}
else {
	checkAuth($login_id, 'coupon');//权限检测
	
	$page=intval($page)==0?1:intval($page);
	$pagenum = 12; 
	$startI = $page*$pagenum-$pagenum;
	$count =$db->rowcount('coupon', '');
	$pages = getPages($count,$page, $pagenum);
	
	$row = get_coupon($where=array(), $startI, $pagenum);
}
	
?>