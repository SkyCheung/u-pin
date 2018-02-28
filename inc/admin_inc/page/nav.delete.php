<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'nav');//权限检测
 
	if ($id=='' || intval($id)==0){message("ERROR.");}
 	$db->delete ('nav', array('id' => $id) );
 
	message("删除成功",'/admin.html?do=nav');
?>