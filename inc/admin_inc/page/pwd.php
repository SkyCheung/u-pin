<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'pwd');//权限检测 

$id=intval($login_id);

if (intval($id)==0){message("ERROR.");}

$row=$db->fetch('user', '*',  array('id' => $id), 'id asc');




?>