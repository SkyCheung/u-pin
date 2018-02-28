<?php
if (!defined('in_mx')) {exit('Access Denied');}
 
checkAuth($login_id, 'nav');//权限检测 
if ($id=='' || intval($id)==0){message("ERROR.");}

$row=$db->fetch('nav', '*',  array('id' => $id), 'id asc');
 
$cbk_status[$row['status']]=' checked="checked"';
$cbk_target[$row['target']]=' checked="checked"';
$cbk_type[$row['type']]=' checked="checked"';	 

?>