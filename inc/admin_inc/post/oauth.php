<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'system');//权限检测
  
if (trim($act)==""){message("操作类型不正确。");} 

if($act =='config')
{
 	if (trim($code)==""){message("获取编号失败。");} 
		$row = $db->fetch('oauth', '*', array('code'=>$code));
	  if($row)
	  {
	  	 $db->update('oauth', array('name'=>trim($name),'appid'=>trim($appid),'status'=>intval($status),'appsecret' =>trim($appsecret)), array('code'=>$code));
	  }
	  else {
	  	 $db->insert('oauth', array('code'=>$code,'name'=>trim($name),'appid'=>trim($appid),'status'=>intval($status),'appsecret' =>trim($appsecret) ));
	  }	  
	  
	  //更新缓存文件 
		$row=  $db->fetchall('oauth', '*');	
		write_file(cache_static.'oauth.php', $php_pre."\$ym_oauth=".@arrayeval($row,'code').";".PHP_EOL);  
   
	  message("保存成功",'/admin.html?do=oauth&id=1');  
 }
	 
 




?>