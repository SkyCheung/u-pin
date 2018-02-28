<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'columns');//权限检测

$id=intval(ucode($id));

if ($id!==0){

	$c_width=$ym_idsort[$id]['width'];
	$c_height=$ym_idsort[$id]['height'];
	$c_dir=$ym_idsort[$id]['dir'];
	$c_mo=$ym_idsort[$id]['mo'];

	$parents_cat = get_parents($id, $ym_idsort, 'pid');
	$c_mo = $parents_cat[0]['file']; //模板文件和上级一样
}
$sortcon = tree_option("ym_allsort",0,"",$id)



?>