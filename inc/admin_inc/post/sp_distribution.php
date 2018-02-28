<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*分销*/
checkAuth($login_id, 'business');//权限检测

require_once './inc/lib/admin/setting.php';
if(!$act || $act =='')
{
	message("操作类型错误");
}
elseif ($act == 'config') {
	
	$commission =array();
	$len = count($commiss_name);
	$max_id = 0;
	for ($i=0; $i < $len; $i++) { 
		if(trim($commiss_id[$i]) !='' && intval($commiss_id[$i])>$max_id)
		{
			$max_id = intval($commiss_id[$i]);
		}
	}
	
	for ($i=0; $i < $len; $i++) { 
		if(trim($commiss_name[$i]) !='')
		{
			$commiss = array();
			if(intval($commiss_id[$i]) ==0)
			{
				$max_id++;
				$commiss_id[$i] = $max_id;
			}
			$commiss['id'] = $commiss_id[$i];
			$commiss['name'] = $commiss_name[$i];
			$commiss['level_1'] = floatval($level_1[$i])*0.01;
			$commiss['level_2'] = floatval($level_2[$i])*0.01;
			$commiss['level_3'] = floatval($level_3[$i])*0.01;
			
			$commission[] = $commiss;
		}
	}
	$data =array(
		'distrib_level' => intval($distrib_level),
		'commission' => $commission,
		'distrib_require' => intval($distrib_require),
		'distrib_require_count' => intval($distrib_require_count),
		'distrib_require_amount' => floatval($distrib_require_amount),
		'sub_require' => intval($sub_require),
		'expire' => intval($expire)
	);
	
	

	update_shopconfig('ditribution_config', json_encode_yec($data));			
	
	message("保存成功",'/admin.html?do=sp_distribution');
}


?>