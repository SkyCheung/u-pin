<?php
if (!defined('in_mx')) {exit('Access Denied');}
  
 checkAuth($login_id, 'brand');//权限检测 
  
$c_name = trim($c_name);
  
if (trim($act)==""){message("操作类型不正确。");} 
if ($c_name==""){message("请填写品牌名称。");}
if (strlen($c_name)>50){message("品牌名称请控制在50个字符内");}
$c_url= trim($c_url)!='http://'?trim($c_url):'';
 
 //上传图片
 
 if($act =='add')
 {
 	  $row=$db->fetch('brand','*', array("name"=>$c_name));
    if ($row){message("名称已存在，请更换。");}
    $db->insert('brand', array('name' => $c_name,'cat_ids' => trim($c_cat_ids),'sort' => intval($c_sort),'description' => trim($c_description),'logo' => $logo,'banner' => $banner,'recommend' => trim($recommend),'url' => trim($c_url) ));
	
	  message("保存成功",'/admin.html?do=brand');  
 }
 elseif ($act =='edit') {	
    $row=$db->fetch('brand','*', "name='".addslashes($c_name)."' and id<>".intval($id));
    if ($row){message("名称已存在，请更换。");} 
 	  $db->update('brand', array('name' => $c_name,'cat_ids' => trim($c_cat_ids),'sort' => intval($c_sort),'description' => trim($c_description),'logo' => $logo,'banner' => $banner,'recommend' => trim($recommend),'url' => trim($c_url) ), array('id' => intval($id)));

    message("更新成功",'/admin.html?do=brand');
	 } 
	 
 




?>