<?php
if (!defined('in_mx')) {exit('Access Denied');}

if($act)
{
	if(!checkAuth($login_id, 'express'))//权限检测
	{
		$res['err'] = $lang['access_denied'];
		die(json_encode($res));
	}
	if($act=='get_pickup')//获取自提点
	{
		$res = array('err' => '', 'res' => '', 'data' => array()); 
		if(intval($province) ==0)
		{
			$res['err'] = "请选择省份";
			die(json_encode($res));
		}
		if(intval($city) ==0)
		{
			$res['err'] = "请选择城市";
			die(json_encode($res));
		}
	
		$res['data'] = $db->fetchall('express_picksite', '*', array('province' => intval($province),'city' => intval($city)));
		die(json_encode($res));
	}
}
else {
	checkAuth($login_id, 'express');//权限检测
	$row = $db->fetch('express_common', '*',array("id"=>$id));
	if($row['code'] =='pickup')
	{
		//$details=$db->fetchall('express_picksite', '*',array("express_id"=>$id));
		include(cache_static."district.php");
		$district= array_query('level', 1, $ym_district);		
	}
	else {
		$details= get_express_district($id); 
		foreach ($details as $k => $v) {
			$district_names= $db->query("select GROUP_CONCAT(name) name from ".$db->table('district')." where id in(".$v['district_id'].")" ); 		
			$details[$k]['district_names'] = $district_names['name'];
		}
	}
	
	$cbk_status[$row['status']]='checked="checked"';
	/*$cbk_express_type[$row['express_type']]='checked="checked"';*/
}

 
?>