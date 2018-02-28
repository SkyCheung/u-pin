<?php
if (!defined('in_mx')) {exit('Access Denied');}

 $res = array('err' => '', 'res' => '', 'data' => array());
 switch ($act) { 	
	case 'edit_status':
		if(!checkAuth($login_id, 'member'))//权限检测
		{
			$res['err'] = $lang['access_denied'];
			die(json_encode($res));
		}
 		if(!isset($id) || trim($id)=='' || !is_numeric($id)){$res['err']="获取不到id";die(json_encode($res));} 
		$db -> update('member', array('status' => intval($val)), array('id' => intval($id)));	
		$res['res'] = '更新成功';
		die(json_encode($res));
 		break;	
 	default: 
		checkAuth($login_id, 'member');//权限检测
		require ("./inc/lib/admin/member.php");		
		$where='1 ';
		if (trim($keyword)!= ""){
			$where="(id LIKE '%$keyword%' or uname LIKE '%$keyword%' or mobile LIKE '%$keyword%')";
		} 
		if(trim($status)!='')
		{
			$where .=' and status='.intval($status);
		}
		
		$page=intval($page)==0?1:intval($page);
		$pagenum=12; 
		$startI = $page*$pagenum-$pagenum;
		$count =$db->rowcount('member', $where);
		$pages=getPages($count, $page, $pagenum);
		$row =getMemberList($where,$startI , $pagenum);
 		
 		break;
 	}

?>