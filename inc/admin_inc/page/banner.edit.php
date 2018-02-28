<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'set');//权限检测 

$code=$id;
$id=ucode(trim($id));
if (intval($id)==0){message("ERROR.");}
$row=$db->fetch('banner', '*',  array('id' => $id), 'id asc');



?>