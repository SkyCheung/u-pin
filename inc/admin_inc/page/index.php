<?php
if (!defined('in_mx')) {exit('Access Denied');}

header("Location:/admin.html?do=diy");


$vow=$db->fetchall('diy', 'id,c_code,c_title,c_type,c_index',  array('c_sid' => $site_id), 'id asc', '');
foreach($vow as $tow){
	$tow[$tow['c_index']]['title']=$tow['c_title'];
	$tow[$tow['c_index']]['code']=$tow['c_code'];

	$row[$tow['c_index']][]=$tow[$tow['c_index']];
}


?>