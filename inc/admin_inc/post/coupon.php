<?php
if (!defined('in_mx')) {exit('Access Denied');}

//优惠券

checkAuth($login_id, 'coupon');//权限检测

require_once './inc/lib/admin/coupon.php';
require_once './inc/lib/coupon.php';
if(!$act || $act =='')
{
	message("操作类型错误");
}
elseif ($act == 'add' || $act == 'edit') 
{
	$id =isset($id)? intval($id):0;
	$name =isset($name)? trim($name):'';
	$amount =isset($amount)? floatval($amount):0;
	$amount_reached =isset($amount_reached)? floatval($amount_reached):0;
	$num =isset($num)? intval($num): 0;	
	$date_type =isset($date_type)? $date_type: 1;
	if($date_type==1)//绝对时间
	{
		$date_start = isset($date_start) ? strtotime($date_start) : 0;
		$date_end =isset($date_end)? strtotime($date_end):0;
	}
	else{
		$days =isset($days)? intval($days):0;
	}
	
	$get_type =isset($get_type)? intval($get_type): 1;
	if($get_type==1)
	{
		$limit_start =isset($limit_start) ? strtotime($limit_start):0;
		$limit_end =isset($limit_end) ? strtotime($limit_end):0;
		$user_day_num =isset($user_day_num)? intval($user_day_num):0;
		$limit_num =isset($limit_num)? intval($limit_num):0;
		$day_num =isset($day_num)? intval($day_num):0;
	}
	else {
		$limit_start =0;
		$limit_end =0;
		$user_day_num =0;
		$limit_num =0;
		$day_num =0;
	}
	
	$client =isset($client)? $client: 0;
	$grade_ids =isset($grade_ids)? $grade_ids:array();
	$type =isset($type)? intval($type):1;
	$cat_ids =isset($cat_ids)? trim($cat_ids): '';
	$goods_ids =isset($goods_ids)? trim($goods_ids):'';
	$status = isset($status)? $status: 0;
	$shop_id =1;//自营	 
	
	if($act == 'edit' && $id ==0)
	{
		message("获取优惠券编号失败");
	}
	if($name =='')
	{
		message("请填写券名");
	}
	if($amount =='')
	{
		message("请填写面值");
	}
	if($amount_reached =='')
	{
		message("请填写使用条件");
	}
	if($num =='')
	{
		message("请填写发行量");
	}
		
	if($date_type==1)//绝对时间
	{
		if($date_start== 0)
		{
			message("请选择有效期开始时间");
		}
		if($date_end== 0)
		{
			message("请选择有效期结束时间");
		}
		if($date_start > $date_end)
		{
			message("有效期的开始时间不能大于结束时间");
		} 
	}
	elseif($date_type==2 && $days == 0){
		message("有效期的天数不能为空");
	}
	if($get_type==1)
	{
		if($limit_start== 0)
		{
			message("请选择领取开始时间");
		}
		if($limit_end== 0)
		{
			message("请选择领取结束时间");
		}
		if($limit_start > $limit_end)
		{
			message("领取的开始时间不能大于结束时间");
		}
		if($date_type==1 && $limit_start > $date_end)
		{
			message("领取的开始时间不能大于有效期结束时间");
		}
		if($date_type==1 && $limit_end > $date_end)
		{
			message("领取的结束时间不能大于有效期结束时间");
		}
		if($limit_num== 0)
		{
			message("请填写每人限领数量");
		}
		if($user_day_num== 0)
		{
			message("请填写每人每天限领数量");
		}
		if($day_num== 0)
		{
			message("请填写每天限领数量");
		}
	}
	
	if($type ==1)
	{
		$item_ids = explode(",", $cat_ids);
	}
	elseif($type == 2) {
		$item_ids = explode(",", $goods_ids);		
	}
	
	if(!$grade_ids || count($grade_ids)==0 || count($grade_ids) == $grade_count)//不选或全选则为0，所有会员
	{
		$ids = '0';
	}
	else {
		$ids = implode(",", $grade_ids);
	}
 
	$data = array('name' => addslashes($name), 'amount' => $amount, 'amount_reached' => $amount_reached,'num'=>$num,'date_start'=>$date_start,'date_end'=>$date_end,'days'=>$days,'limit_start'=>$limit_start,'limit_end'=>$limit_end,'user_day_num'=>$user_day_num,'limit_num'=>$limit_num,'day_num'=>$day_num,'client'=>$client,'grade_ids'=>$ids,'get_type'=>$get_type,'type'=>$type,'shop_id'=>$shop_id,'status'=>$status);
			//die(''.$act);
	if($act == 'add')
	{
		$data['addtime'] = time();
		$id=add_coupon($data);		
	}
	elseif ($act == 'edit') {
		update_coupon($data, $id);
		del_coupon_item($id);
	}
	add_coupon_item($item_ids, $id);
	
	message("保存成功",'/admin.html?do=coupon');
}
elseif ($act == 'give') //发放优惠券
{
	$type = isset($type)? intval($type):0; print $type;
	$days = 0;
	$uname = isset($uname)? trim($uname): "";
	$sex = isset($sex)? intval($sex):0;	
	$grade_ids = isset($grade_ids)? implode(",", $grade_ids) : '';
	$coupon_ids = isset($coupon_ids)? trim($coupon_ids): "";
	$time = strtotime("-".$days." day");
	if($type==2)
	{
		$days = intval($days_login);
	}
	elseif($type==3)
	{
		$days = intval($days_order);
	}
	
	if(($type == 2 || $type == 3) && $days ==0)
	{
		message("请填写天数");
	}
	
	$coupon = get_couponinfo($coupon_ids);
	
	$row =array();
	if($type == 1)
	{
		if($uname == '')
		{
			message("请填写会员ID/会员名");
		}
		$uid = get_uid_by_IdOrName($uname);
		if(!$uid)
		{
			message("该会员不存在");
		}
	 	add_coupon_user($coupon_ids, $uid);	
		update_coupon_getNum($coupon_ids);//更新数量
		message("发放成功");return;
	}
	elseif($type == 2)//N天没有登陆
	{
		$row = get_unlogin_user(role_user,$grade_ids, $sex,$time);		
	}
	elseif($type == 3)//N天没有下单
	{				
		$row = get_unorder_user($grade_ids, $sex, $time);
	}
	$count =0;
	if($row)
	{
		$count = count($row);
		if($count > ($coupon['num']-$coupon['get_num']))
		{
			message("发放的数量（".$count."）大于剩余数量（".($coupon['num']-$coupon['get_num'])."）");
		}

		$sql = array();
		foreach ($row as $k => $v) {
			$sql[] = "(".$coupon_ids.",".$v['uid'].",".coupon_unused.",".time().",'')";
		}

		$res = $db->query("insert into ".$db->table('coupon_user')."(cid,uid,status,get_time,ip) values".implode(",", $sql));
		update_coupon_code($coupon_ids);
		update_coupon_getNum($coupon_ids, $count);//更新数量
	}
		
	message("发放成功，符合条件的有".$count."人");
	
	
}

?>