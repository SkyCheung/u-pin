<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*销售排行*/
checkAuth($login_id, 'stat');//权限检测 

require './inc/lib/admin/stat.php';

if($act)
{
	$res = array('err' => '', 'res' => '', 'data' => array());
	if($act =='get_sale_top')
	{
		if($days !='')
		{
			$end_date = time();
			$start_date = strtotime(date("Y-m-d", strtotime("-".$days." day")));
		}
		else {
			$end_date = trim($end_date)=='' ? time():strtotime(trim($end_date));
			$start_date =strtotime(trim($start_date));
		}
		$res['data'] = sale_top($start_date, $end_date, $num, '');		
	}

	die(json_encode($res));
}

$start_date = date("Y-m-d", strtotime("-330 day")); //-1 week
$end_date = date("Y-m-d", time());
 

 
?>