<?php
if (!defined('in_mx')) {exit('Access Denied');
}

 include cache_static.'district.php'; 

$district = array();
$province=array_query('level', '1', $ym_district , false); 
foreach($province as $k => $v) {
	$district[$k]['id']= $v['id'];
	$district[$k]['pid']= $v['pid'];
	$district[$k]['name']= $v['name'];
	$district[$k]['city']= $db->fetchall('district', '*',array("pid"=>$v['id']));
}

	
?>