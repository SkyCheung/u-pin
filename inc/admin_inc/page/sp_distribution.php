<?php
if (!defined('in_mx')) {exit('Access Denied');}

require_once './inc/lib/admin/setting.php';

if(isset($act))
{
	$res = array('err' => '', 'res' => '', 'data' => array());
	if(!checkAuth($login_id, 'business'))//权限检测
	{
		message($lang['access_denied']);
	}
	if($act =='config')
	{
		$config = get_shopconfig_bykey('ditribution_config');
		$row =json_decode(stripslashes($config['value']), true);
		if($row)
		{
			foreach ($row['commission'] as $k => $v) {
				$row['commission'][$k]['level_1'] = floatval($v['level_1'])*100;
				$row['commission'][$k]['level_2'] = floatval($v['level_2'])*100;
				$row['commission'][$k]['level_3'] = floatval($v['level_3'])*100;
			}
		}
		$html ='checked="checked"';
		$cbk_distrib_level[$row['distrib_level']] = $html;
		$cbk_distrib_require[$row['distrib_require']] = $html;
		$cbk_sub_require[$row['sub_require']] = $html;
	 	$do ="sp_distribution.config";
	}
	
}
else {
	require_once "inc/lib/distrib.php";
	$page=intval($page)==0?1:intval($page);
	$pagenum = 12; 
	$startI = $page*$pagenum-$pagenum;
	$count =get_distribution_count();
	$pages = getPages($count,$page, $pagenum);
	
	$row = get_distribution('', $startI, $pagenum);
}


?>