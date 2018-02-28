<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'set');//权限检测

if (trim($keyword)!= ""){
	$keycode=" and c_title LIKE '%$keyword%'";
}else{
	$keycode='';
}

$searchid=$sssortid;

$xow=$db->fetchall('link','*','',' id asc');

foreach($xow as $rs){
	$rs['id']=$rs['id'];
	$rs['time']=ftime($rs['c_time']);
	$rs['title']=$rs['c_title'];
	$rs['url']=$rs['c_url'];
	$rs['code']=$rs['c_code'];
	$row[]=$rs;
}


?>