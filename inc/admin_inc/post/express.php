<?php
if (!defined('in_mx')){exit('Access Denied');}
 
checkAuth($login_id, 'express');//权限检测
 
if($act=='express')//配送模式
{
	if(intval($express_type)==0){message('运费标准类型错误');}	 
	$db ->update('shop_config', array("value"=>$express_type), array("`key`"=>'express_type')); 
	
	update_config();
	message("提交成功，数据已更新。", $url);
}
elseif($act=='add' || $act=='edit')//保存配送方式
{
	$code = trim($code);
	if(trim($name)==''){message('请填写快递名称');}	
	if($code==''){message('请填写快递代码');}
	if($act=='add')
	{
		$row = get_exp_tpl(array('name'=>$name));
		if($row){message('快递名称已存在');}
		$row = get_exp_tpl(array('code'=>$code));
		if($row){message('快递代码已存在');}
		if($code != pickup)
		{
			$row = get_exp_tpl(array('code2'=>$code2));
			if($row){message('快递代码2已存在');}
		}
	}
	else {
		$row = get_exp_tpl("name='".$name."' and id<>".$express_id);
		if($row){message('快递名称已存在');}		
		$row = get_exp_tpl("code='".$code."' and id<>".$express_id);
		if($row){message('快递代码已存在');}
		if($code != pickup)
		{
			$row = get_exp_tpl("code2='".$code2."' and id<>".$express_id);
			if($row){message('快递代码2已存在');}
		}
	}
	
	$data = array();
	$data['name']= trim($name);
	$data['code']= $code;
	$data['code2']= $code2;
	$data['status']= intval($status);
	$data['express_type']= intval($express_type);
	$data['sort']= intval($c_sort);
			
	if($act=='add')
	{
		$data['addtime']= time();
		$db->insert('express_common', $data);
		$express_id = $db->lastinsertid();
	}
	else {
		update_exp_tpl($express_id, $data);
		if($code == pickup)
		{
			$db->delete('express_picksite', array("province"=>intval($province),"city"=>intval($city),"district"=>intval($district)));
		}
		else {
			$db->delete('express_district', array("express_id"=>$express_id));
		}		
	}
	
	if($code == pickup)//自提
	{
		$sql='insert into '.$db->table('express_picksite').'(`express_id`,`address`, `picker`, `tel`, `picktime`,province,city) values'; 
		$is=false;
		foreach ($address as $k => $v) {		
			if(trim($v)!='')
			{
				$sql.= "(".$express_id.",'".trim($address[$k])."','".trim($picker[$k])."','".trim($tel[$k])."','".trim($picktime[$k])."',".intval($province).",".intval($city)."),";
				$is = true;
			}
		}
		if($is)
		{
			$db->query(rtrim($sql,','));
		}
	}
	else {//商家配送
		$sql='insert into '.$db->table('express_district').'(`express_id`, `first_price`, `added_price`, `first_weight`, `added_weight`, `district_id`) values'; 
		$is=false;
		foreach ($district_id as $k => $v) {
			if(trim($v)!='')
			{
				$sql.="(".$express_id.",".intval($first_price[$k]).','.intval($added_price[$k]).','.intval($first_weight[$k]).','.intval($added_weight[$k]).",'".rtrim($v,',')."'),";
				$is=true;
			}		
		}
		if($is)
		{
			$db->query(rtrim($sql,','));
		}
	}
	
	message("提交成功，数据已更新。", '/admin.html?do=express');
}
elseif($act=='express_order')//按订单金额范围计算运费
{
	$db->delete('express', array());
	if(isset($money_reached) && count($money_reached)>0)
	{
		$is=false;
		$sql='insert into '.$db->table('express').'(`grade_id`, `express_type`, `money_reached`, `express_fee`, `sort`, `shop_id`) values';  		
		foreach ($money_reached as $k => $value) {
			if(is_numeric(trim($money_reached[$k])))
			{
				$sql.="('".trim($grade_id[$k])."',".intval($express_type[$k]).','.intval($money_reached[$k]).','.floatval($express_fee[$k]).','.intval($c_sort[$k]).",0),";
				$is=true;
			}			
		}		
		if($is)
		{
			$db->query(rtrim($sql,','));						
		}				
	}
	update_config();
	message("提交成功，数据已更新。", $url);		
}
elseif ($act =='tpl') {
	if(empty($id) || empty($content))
	{
		message("参数不正确");
	}
	update_exp_tpl($id, array('tpl'=>json_encode($content)));
	message("保存成功", '/admin.html?do=express');
}

 
?>