<?php
if (!defined('in_mx')) {exit('Access Denied');
}
/*评论管理*/
checkAuth($login_id, 'comment');//权限检测

$res = array('err' => '', 'res' => '', 'data' => array());
 
if($act)
{
	if($act =='edit_status')
	{
		if(!isset($id) || trim($id)=='' || !is_numeric($id)){$res['err']="获取不到id";die(json_encode($res));} 
		$db -> update('comment', array('status' => intval($val)), array('id' => intval($id)));	
		$res['res'] = '更新成功';
		die(json_encode($res));
	}
	elseif($act =='delete') {
		$db->delete('comment', array('id' => $id));
		message("删除成功。", $url);
		exit();
	}
}
else {
	$where='';
	$uname = addslashes(trim($uname));
	
	$start_date= $start_date ? $start_date : date('Y-m-d',strtotime('-365day'));
	$end_date= $end_date ? $end_date : date('Y-m-d H:i');
	
	 
	$page=intval($page)==0 ? 1:intval($page);
	$pagenum = 12; 
	$start = $page*$pagenum-$pagenum;
	$count = get_comments_count(0, 0, $uname, strtotime($start_date),strtotime($end_date));
	$pages = getPages($count, $page, $pagenum);
	$row = get_comments_list(0, 0, $uname, strtotime($start_date),strtotime($end_date), $start, $pagenum);
 	
}

 
?>