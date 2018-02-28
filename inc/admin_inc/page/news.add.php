<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'news');//权限检测

$time=ftime(time());

//$row=$db->fetchall('columns', '*',  array('c_id' => $login_id,'c_sid' => $site_id,'c_type' => 'news'), 'id asc', '');
//$listarr=$row;

$teeeid=0;
if($id){
  $teeeid=intval(ucode($id));
}
$sortcon=tree_option("ym_allsort",0,"",$teeeid,'news');

?>