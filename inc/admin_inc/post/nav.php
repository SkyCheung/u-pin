<?php
if (!defined('in_mx')) {exit('Access Denied');}
  
checkAuth($login_id, 'nav');//权限检测  
  
if (trim($act)==""){message("操作类型不正确。");} 
if (trim($name)==""){message("请填写导航名称。");}
if (strlen(trim($name))>50){message("导航名称请控制在50个字符内");}

 if($act =='add')
 {
    $db->insert('nav', array('name' => trim($name),'target' => trim($target),'sort' => intval($c_sort),'type' => trim($type),'status' => intval($status),'style'=>trim($style),'url' => trim($c_url) ));
	  message("保存成功",'/admin.html?do=nav');  
 }
 elseif ($act =='edit') {	
 	  $db->update('nav', array('name' => trim($name),'target' => trim($target),'sort' => intval($c_sort),'type' => trim($type),'status' => intval($status),'style'=>trim($style),'url' => trim($c_url) ), array('id' => trim($id)));
	
    message("更新成功",'/admin.html?do=nav');
	 } 
	 
 




?>