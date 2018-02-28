<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'set');//权限检测

$c_title=str_replace("/","",$c_title);
$c_title=str_replace("?","",$c_title);
$c_title=str_replace("%","",$c_title);
$c_title=str_replace("\\","",$c_title);
$c_title=str_replace("'","",$c_title);
$c_title=str_replace(" ",",",$c_title);
$c_title=str_replace(";","",$c_title);

if (trim($c_title)==""){message("请输入标题名称。");}
  
$_march = '/^[a-z][a-z0-9]{1,20}$/';

if(!preg_match($_march, $c_name)) {  
	message('变量名称只能英文、数字，并且英文开头。');  
}

$vsql="c_name='".ptitle($c_name)."'";
$row=$db->fetch('banner','*',$vsql,'id asc');
if ($row){message("变量名称已存在，变量名称不能与其它重复。");}

$db->insert('banner', array('c_id' => intval($login_id),'c_width' => intval($c_width),'c_height' => intval($c_height), 'c_title' => ptitle($c_title) , 'c_name' => ptitle($c_name) ,'c_time' =>time(),'c_code' =>''));
$lastid=$db->lastinsertid();

$tempcode12252= jcode($lastid);

$db->update('banner', array('c_code' =>$tempcode12252), array('id' => $lastid));

//update_config($lastid,ptitle($c_name),ptitle($c_title));

message("提交成功，数据已更新。",'/admin.html?do=banner');


?>