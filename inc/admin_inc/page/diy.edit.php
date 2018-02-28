<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'diy');//权限检测

if (intval($id)==0){message("获取编号失败");}
$row= $db->fetch('diy', '*',  array('id' => $id), 'id asc');
if($row['diy_type']=='goods' && $row['cat_ids']!='all' && $row['cat_ids']!='each')
{
	$cate= $db->queryall('select id,name from '. $db->table('category').' where id in('.$row['cat_ids'].')'); 	
}
elseif($row['diy_type']=='custom' && $row['cus_type'] == 2) {
	$bd = json_decode($row['body'], true);
	foreach ($bd as $k => $v) {
		$bd[$k] = '<img src="'.$v.'">';
	}
	$row['body'] = implode('', $bd);
}

$cus_type[$row['cus_type']]= ' checked="checked"';
$diy_type[$row['diy_type']]= ' checked="checked"';
$cbk_cat_ids[$row['cat_ids']]= ' checked="checked"';
$cbk_include_son[$row['include_son']]= ' checked="checked"';

$recommends=json_decode($row['recommends'],true);
$cbk[0]='';
$cbk[1]= ' checked="checked"';
if(is_array($recommends))
{
	foreach ($recommends as $k => $v) {
		$$k= $cbk[$v];
	}
}
$cbk_iseachnum[$row['is_eachnum']]= ' checked="checked"';
$cbk_is_childnum[$row['is_childnum']]= ' checked="checked"';

$tmp_arr = explode(",", $row['promotion_ids']); 
$tmp_arr = array_filter($tmp_arr);
$row['promotion_count'] =  count($tmp_arr);

$tmp_arr = explode(",", $row['ids']); 
$tmp_arr = array_filter($tmp_arr);
$row['ids_count'] =  count($tmp_arr);

$cat = get_children(0); 
 
$sortcon = tree_option("ym_allsort",0,"",$row['cat_ids']);


?>