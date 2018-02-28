<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'system');//权限检测

$plugin_path = plugin."com/";
  
if($iscom ==1)
{  
	if (trim($act)==""){message("操作类型不正确。");} 
	
	if($act =='config')
	{
	 	if (trim($code)==""){message("获取插件编号失败。");} 
			
			$db->update('plugin', array('name'=>trim($name),'author'=>trim($author),'status'=>intval($status),'version' =>trim($version),'memo' =>trim($memo),'config' =>json_encode($config)), array('code'=>$code));	  
		  
		  message("保存成功",'/admin.html?do=plugin');  
	 }
}
else {
	if (trim($plugin_code)==""){message("获取插件编号失败。");} 
	if (trim($act)==""){message("操作类型不正确。");} 
	
	require $plugin_path.$plugin_code.'/'.$plugin_code.".php";
	
	$c_plugin = new $plugin_code;
	$c_plugin->$act();
	
}	 
 




?>