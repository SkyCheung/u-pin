<?php
if (!defined('in_mx')) {exit('Access Denied');}
  
 checkAuth($login_id, 'cron');//权限检测 
  
if (trim($act)==""){message("操作类型不正确。");} 
if (trim($name)==""){message("请填写任务名称");}
 
 
 if($act =='add')
 {
 	  $row=$db->fetch('cron','*', array("name"=>trim($name)));
    if ($row){message("任务名称已存在，请更换。");}
    $db->insert('cron', array('name' => trim($name),'week' => trim($week),'day' => trim($day),'hour' => trim($hour),'minute' => trim($minute),'memo' => trim($memo),'filename' => trim($filename),'status' => trim($status),'addtime'=>time()));

	  message("保存成功",'/admin.html?do=cron');  
 }
 elseif ($act =='edit') {	
    $row=$db->fetch('cron','*', "name='".trim($name)."' and id<>'".intval($id)."'");
    if ($row){message("任务名称已存在，请更换。");} 
 	  $db->update('cron', array('name' => trim($name),'week' => trim($week),'hour' => trim($hour),'minute' => trim($minute),'memo' => trim($memo),'filename' => trim($filename),'status' => trim($status)), array('id' => intval($id)));	
		
    message("更新成功",'/admin.html?do=cron');
}
	 
 




?>