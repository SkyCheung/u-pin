<?php
if (!defined('in_mx')) {exit('Access Denied');}

$id=ucode(trim($id));
if (intval($id)==0){message("ERROR.");}

checkAuth($login_id, 'set');//权限检测 

$row=$db->fetch(''.$type, '*',  array('id' => $id), 'id asc');
$title=$row['c_title'];
 
$notime=time();

$timestamp=$notime;
$token = md5('yun_mx' . $timestamp);

$xow=$db->fetchall('banner_pic','*', array('c_toid' => $id),'c_order asc,id asc','');

foreach($xow as $rs){
	$rs['id']=$rs['id'];
	$rs['time']=ftime($rs['c_time']);
	$rs['title']=$rs['c_title'];
	$rs['code']=$rs['c_code'];
	$ls[]=$rs;
}

 

?>