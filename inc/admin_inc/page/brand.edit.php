<?php
if (!defined('in_mx')) {exit('Access Denied');}
 
if ($id=='' || intval($id)==0){message("ERROR.");}

checkAuth($login_id, 'brand');//权限检测 

$types = get_types();

$cat = array_query('pid', 0, $ym_cats, false);	

$row=$db->fetch('brand', '*',  array('id' => $id), 'id asc');
$row['name'] = stripslashes($row['name']);
$row['logo_name'] = get_filename($row['logo']);
$row['banner_name'] = get_filename($row['banner']);
$brandcat=array(); 
$catids=explode(",", $row['cat_ids']);
$catids=array_filter($catids);
foreach ($catids as $k => $v) { 
	$tmp = array_query('id', $v, $ym_cats, false);
	$brandcat[$k]['id']=$v;
	$brandcat[$k]['name']=$tmp[0]['name'];
}
$ischecked[$row['recommend']]=' checked="checked"';


	 
?>