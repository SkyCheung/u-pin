<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'set');//权限检测 
$name='Banner组图';

$type='banner';


$xow=$db->fetchall('banner','*','',' id desc');


foreach($xow as $rs){
	$rs['id']=$rs['id'];
	$rs['time']=ftime($rs['c_time']);
	$rs['title']=$rs['c_title'];
	$rs['code']=$rs['c_code'];
	$row[]=$rs;
}


?>