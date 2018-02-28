<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'diy');//权限检测

$id=intval(ucode($id));
$sortcon=tree_option("ym_allsort",0,"",$id);
$cat =get_children(0);

?>