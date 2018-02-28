<?php
if (!defined('in_mx')) {exit('Access Denied');}
 
  checkAuth($login_id, 'types');//权限检测 
 
	if ($id=='' || intval($id)==0){message("ERROR.");}
	$rs=$db->fetch('type', '*',  array('id' => $id), 'id asc');
	$row=$db->fetchall('attribute', '*',  array('type_id' => $rs['id']), 'id asc');

?>