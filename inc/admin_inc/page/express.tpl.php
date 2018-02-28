<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*快递单模板*/
checkAuth($login_id, 'express');//权限检测
$row = get_exp_tpl("id=".intval($id));
$tpl_content = json_decode($row['tpl'], true); 
 
?>