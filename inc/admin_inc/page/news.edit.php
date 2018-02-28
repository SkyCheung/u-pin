<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'news');//权限检测
$id=ucode(trim($id));
if (intval($id)==0){message("ERROR.");}


$row=$db->fetch('news', '*',  array('id' => $id), 'id asc');

$time=ftime($row['c_time']);

$c_cc[0]= '';
$c_cc[1]= ' checked="checked"';
$cc=$c_cc[$row['c_c']];

$c_smtype["a".$row['c_smtype']]= ' checked="checked"';

//$sow=$db->fetchall('columns', '*',  array('c_id' => $login_id,'c_sid' => $site_id,'c_type' => 'news'), 'id asc', '');
//$listarr=$sow;


$sortcon=tree_option("ym_allsort",0,"",$row["c_toid"],'news')

?>