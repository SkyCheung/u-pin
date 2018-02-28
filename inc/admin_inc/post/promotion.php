<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'business');//权限检测  

/*促销*/
require_once './inc/lib/admin/promotion.php';
if(!$act || $act =='')
{
	message("操作类型错误");
}
elseif ($act == 'sp_discount_add' || $act == 'sp_discount_edit') {
	$name = trim($name);
	$status = intval($status);
	$description = trim($description);
	$goods_ids = trim($goods_ids);
	$start_time = trim($start_time);
	$end_time = trim($end_time);
	$type = intval($type);
	$val = floatval($val);
	
	if(!$name || $name=='')
	{
		message("请填写名称");
	}
	if(!$start_time || $start_time=='')
	{
		message("请选择活动开始时间");
	}
	if(!$end_time || $end_time=='')
	{
		message("请选择活动结束时间");
	}
	if(strtotime($start_time) > strtotime($end_time))
	{
		message("结束时间不能小于结束时间");
	}
	if(!$type || $type==0)
	{
		message("请选择优惠方式");
	}
	if(!$val || $val==0)
	{
		message("请填写优惠值");
	}
	if(!$goods_ids || $goods_ids=='')
	{
		message("请选择参与活动的商品");
	}
		
	if(!$grade_ids || count($grade_ids)==0 || count($grade_ids) == $grade_count)//所有则为0
	{
		$ids = '0';
	}
	else {
		$ids = implode(",", $grade_ids);
	}
	$row = array(
		'name' => $name,
		'status' => $status,
		'description' => $description,
		'goods_ids' => $goods_ids,
		'start_time' => strtotime($start_time),
		'end_time' => strtotime($end_time),
		'type' => $type,
		'val' => $val,
		'grade_ids' => $ids
	);
	if($act=='sp_discount_add')
	{
		$row['addtime'] = time();
		add_discount($row);	
	}
	elseif ($act=='sp_discount_edit') {
		update_discount($id, $row);
	}	
	
	message("保存成功",'/admin.html?do=sp_discount');
}

?>