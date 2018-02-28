<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'system');//权限检测
  
if (trim($act)==""){message("操作类型不正确。");} 

if($act =='config')
{
 	if (trim($code)==""){message("获取编号失败。");} 
		$row = $db->fetch('express_track', '*', array('code'=>$code));
	  if($row)
	  {
	  	 $db->update('express_track', array('name'=>trim($name),'appid'=>trim($appid),'status'=>intval($status),'appkey' =>trim($appkey)), array('code'=>$code));
	  }
	  else {
	  	 $db->insert('express_track', array('code'=>$code,'name'=>trim($name),'appid'=>trim($appid),'status'=>intval($status),'appkey' =>trim($appkey) ));
	  }	  
	  
	  //更新缓存文件 
		$row=  $db->fetchall('express_track', '*');	
		write_file(cache_static.'express_track.php', $php_pre."\$ym_express_track=".@arrayeval($row,'code').";".PHP_EOL);  
   
	  message("保存成功",'/admin.html?do=express_track');  
 }
	 
 




?>