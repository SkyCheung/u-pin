<?php
if (!defined('in_mx')) {exit('Access Denied');}

$notime=time();

$time=ftime($notime);

//$row=$db->fetchall('columns', '*',  array('c_id' => $login_id,'c_sid' => $site_id,'c_type' => 'news'), 'id asc', '');
//$listarr=$row;
$timestamp=$notime;
$token = md5('yun_mx' . $timestamp);


$teeeid=intval(ucode($id));

$sortcon=tree_option("ym_allsort",0,"",$teeeid,$type);





?>