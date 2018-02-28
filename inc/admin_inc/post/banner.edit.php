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

$c_name=str_replace("?","",$c_name);
$c_name=str_replace("%","",$c_name);
$c_name=str_replace("\\","",$c_name);
$c_name=str_replace("'","",$c_name);
$c_name=str_replace(")","",$c_name);
$c_name=str_replace("(","",$c_name);
$c_name=str_replace("<","",$c_name);
$c_name=str_replace(">","",$c_name);
$c_name=str_replace(";","",$c_name);


$_march = '/^[a-z][a-z0-9]{1,20}$/';

if(!preg_match($_march, $c_name)) {  
	message('变量名称只能英文、数字，并且英文开头。');  
}


$idcode=ptitle($id);
$id=intval(ucode($id));
if ($id==0){message("ERROR.");}



$vsql=" id<>".$id." and c_name='".ptitle($c_name)."'";

$row=$db->fetch('banner','*',$vsql,'id asc');

if ($row){message("变量名称已存在，请更换。");}

$db->update('banner', array('c_width' => intval($c_width),'c_height' => intval($c_height),'c_name' => ptitle($c_name), 'c_title' => ptitle($c_title)), array('id' => $id));

message("提交成功，数据已更新。",'/admin.html?do=banner');


?>