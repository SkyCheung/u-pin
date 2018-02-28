<?php
if (!defined('in_mx')) {exit('Access Denied');}
/*会员等级*/
 checkAuth($login_id, 'member.grade');//权限检测
  
if (trim($act)==""){message("操作类型不正确。");} 
if (trim($grade_name)==""){message("请填写等级名称。");}
if (strlen(trim($grade_name))>50){message("等级名称请控制在50个字符内");}
 

 if($act =='add')
 {
 	  $row=$db->fetch('member_grade','*', "grade_name='".trim($grade_name)."'");
    if ($row){message("名称已存在，请更换。");}
    $db->insert('member_grade', array('grade_name' => trim($grade_name),'point_require' => intval($point_require),'discount' => intval($discount),'sort' => intval($c_sort) ));

	  message("保存成功",'/admin.html?do=member.grade');  
 }
 elseif ($act =='edit') {	
    $row=$db->fetch('member_grade','*', "grade_name='".trim($grade_name)."' and grade_id<>'".$grade_id."'");
    if ($row){message("名称已存在，请更换。");} 
 	  $db->update('member_grade', array('grade_name' => trim($grade_name),'point_require' => intval($point_require),'discount' => intval($discount),'sort' => intval($c_sort) ), array('grade_id' => intval($grade_id)));	
		
    message("更新成功",'/admin.html?do=member.grade');
} 
	 
 




?>