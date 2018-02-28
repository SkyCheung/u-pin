<?php
if (!defined('in_mx')) {exit('Access Denied');}
 
checkAuth($login_id, 'system');//权限检测 
 
 
//$sp_file = plugin."oauth/".$code.'/'.$code.'.php';
$path = plugin."express/";
$dirs =get_dir($path); 

if($oauth_code)
{
	if(file_exists($sp_file)==false){message("获取不到文件 ".$code.'.php');}
	require($sp_file);
	$row = $db->fetch('express_track', '*',  array('code' => $code));
}

$row = $db->fetchall('express_track', '*'); 

 //die("a".$p);

	 
?>