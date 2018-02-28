<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'columns');//权限检测

$id=ucode(trim($id));
if (intval($id)==0){message("ERROR.");}
$row= $db->fetch('columns', '*',  array('id' => $id), 'id DESC');

$c_look[$row['c_look']]= ' checked="checked"';
$is_help[$row['is_help']]= ' checked="checked"';
$c_is[$row['c_is']]= ' checked="checked"';
$c_member[$row['c_member']]= ' checked="checked"';
$c_type[$row['c_type']]=' checked="checked"';

$c_smtype["a".$row['c_smtype']]= ' checked="checked"';

$c_seo=trim($row['c_seo']);

if ($c_seo<>''){
	
	$seokk = json_decode($c_seo, true);
	$row['seo_title']=$seokk['seo_title'];
	$row['seo_keyword']=$seokk['seo_keyword'];
	$row['seo_description']=$seokk['seo_desc'];
}

$sortcon=tree_option("ym_allsort",0,"", $row["c_pid"]); 



?>