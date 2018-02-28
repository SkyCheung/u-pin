<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'types');//权限检测

if (trim($act)==""){message("操作类型不正确。");} 
if (trim($types_name)==""){message("请填写名称。");}
if (strlen(trim($types_name))>30){message("请控制在30个字符内");}

 $values=$_POST['value'];
 $len=count($attr_name); 
 
 if($act =='add')
 {
 	  $row=$db->fetch('type','*', array("name"=>trim($types_name)));
    if ($row){message("名称已存在，请更换。");} 	 	
    $db->insert('type', array('name' => trim($types_name),'sort' => intval($types_sort)));
	  $lastid=$db->lastinsertid();
	  
	  for($i=0; $i < $len; $i++) { 
		 if(trim($attr_name[$i])!='') 
		 { 
		 	 $db->insert('attribute', array('type_id' => $lastid,'name' => trim($attr_name[$i]),'value' => trim($values[$i]),'search' => trim($search[$i]),'uitype' => trim($uitype[$i]), 'type' => trim($type[$i])
		 	 ,'sort' => intval($sort[$i])));
		 }
	  }
	  message("保存成功",'/admin.html?do=types');  
 }
 elseif ($act =='edit') {	
    $row=$db->fetch('type','*', "name='".trim(addslashes($types_name))."' and id<>".intval($id));
    if ($row){message("名称已存在，请更换。");} 
 	  $db->update('type', array('name' => trim($types_name),'sort' => intval($types_sort)), array('id' => trim($id)));
	
    $del_ids=rtrim($del_id, ","); 
	  if ($del_ids!='' && isNumComma($del_ids)==false){message("没获取到id.");}
	  if($del_ids!='')
	  {
	  	 $db->delete('attribute', "  id in (".$del_ids.")");
	  }

	  for($i=0; $i < $len; $i++) { 
		 if(trim($attr_name[$i])!='') 
		 {  
		 	  if($attr_ids[$i]=='' )
			  {			 	  		 	 
		     	$db->insert('attribute', array('type_id' => $id,'name' => trim($attr_name[$i]),'value' => trim($values[$i]),'search' => trim($search[$i]),'uitype' => trim($uitype[$i]), 'type' => trim($type[$i]),'sort' => intval($sort[$i])));
			  }
			  else { 
			  	$db->update('attribute', array('name' => trim($attr_name[$i]),'value' => trim($values[$i]),'search' => trim($search[$i]),'uitype' => trim($uitype[$i]), 'type' => trim($type[$i]),'sort' => intval($sort[$i])), array('id' => $attr_ids[$i]));
			  }
		 }
	 } 
	  message("保存成功",'/admin.html?do=types');
 }
 




?>