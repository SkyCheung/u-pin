<?php
if (!defined('in_mx')) {exit('Access Denied');}
  
 checkAuth($login_id, 'message');//权限检测 
  
if (intval($id)==""){message("获取编号失败。");} 

$row = $db->fetch('message', '*',array('id'=>$id)); 

$config=json_decode($row['config'],true);
foreach ($config as $k => $v) {	
	$$k=$v;
}

?>