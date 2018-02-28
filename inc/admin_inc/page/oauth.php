<?php
if (!defined('in_mx')) {exit('Access Denied');}
 
checkAuth($login_id, 'system');//权限检测 
 

$path = plugin."oauth/";
$dirs =get_dir($path); 

$row = $db->fetchall('oauth', '*', "","sort"); 

 

	 
?>