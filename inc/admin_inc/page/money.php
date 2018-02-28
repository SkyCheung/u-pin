<?php
if (!defined('in_mx')) {exit('Access Denied');
}
/*资金管理*/
checkAuth($login_id, 'money');//权限检测

$res = array('err' => '', 'res' => '', 'data' => array());
 
if($act)
{
	
}
else {
	$where='';
	$uname = trim($uname);
	
	if(!$is_search)
	{
		$start_date= date('Y-m-d',strtotime('-365day'));
		$end_date= date('Y-m-d H:i');
	}	 	
	 
	$page=intval($page)==0?1:intval($page);
	$pagenum = 12; 
	$start = $page*$pagenum-$pagenum;
	$count = get_member_log_count(0, asset_balance,0,$uname,strtotime($start_date),strtotime($end_date));
	$pages = getPages($count, $page, $pagenum);
	$row = get_member_log(0, asset_balance, 0, $uname, strtotime($start_date),strtotime($end_date), $start, $pagenum);
	
	$balance_row = $db->query("select sum(balance) balance from ".$db->table('member'));
	$balance = $balance_row['balance'];
	
}

 
?>