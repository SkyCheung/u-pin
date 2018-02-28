<?php
if (!defined('in_mx')) {exit('Access Denied');}

if ($id == '' || intval($id) == 0) {message("ERROR.");}

checkAuth($login_id, 'category');//权限检测

$row = array_query('pid', $id, $ym_cats);
if(count($row)>0)
{
	message("请先删除子分类", '/admin.html?do=category');
} 
else {
	$row=$db->queryall("SELECT a.* FROM ".$db->table('goods')." a left join ".$db->table('goods_cat')." b on a.goods_id=b.goods_id  where a.cat_id=$id or b.cat_id=$id");
	if(count($row)>0)
	{
		message("该分类下有商品，请先将其删除", '/admin.html?do=category');
	}
}
 
$db -> delete('category', array('id' => $id));
$row = array_query('id', $id, $ym_cats); 
if(trim($row[0]['img'])!='' && file_exists(trim($row[0]['img'])))
{
	@del_file(trim($row[0]['img']));
}
update_config();//更新缓存	
message("删除成功", '/admin.html?do=category');

?>