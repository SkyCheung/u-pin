<?php
if (!defined('in_mx')) {exit('Access Denied');}
  
 checkAuth($login_id, 'app');//权限检测 
  
if (trim($act)==""){message("操作类型不正确。");} 
if (trim($version)==""){message("请填写App版本");}
 
 
 if($act =='add')
 {
 	  $row=$db->fetch('app','*', array("version"=>trim($version), 'type'=>intval($type)));
    if ($row){message("版本已存在，请更换。");}
    $db->insert('app', array('version' => trim($version),'url' => trim($c_url),'memo' => trim($memo),'type' => intval($type),'addtime'=>time()));

	  message("保存成功",'/admin.html?do=app&type='.$type);  
 }
 elseif ($act =='edit') {	
    $row=$db->fetch('app','*', "version='".trim($version)."' and type=".intval($type)." and id<>'".intval($id)."'");
    if ($row){message("版本已存在，请更换。");} 
 	  $db->update('app', array('version' => trim($version),'url' => trim($c_url),'memo' => trim($memo),'type' => intval($type)), array('id' => intval($id)));	
		
    message("更新成功",'/admin.html?do=app&type='.$type);
	} 
	 
 




?>