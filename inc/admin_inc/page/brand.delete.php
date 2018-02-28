<?php
if (!defined('in_mx')) {exit('Access Denied');}
 
	if ($id=='' || !isNumComma($id)){message("品牌编号错误");}
	
checkAuth($login_id, 'brand');//权限检测 	
	$row = $db->fetchall('brand', '*', 'id in('.$id. ')');	
 	$db->delete ('brand', 'id in('.$id. ')' );
	foreach ($row as $k => $v) {
		if(trim($v['logo'])!='' && file_exists(trim($v['logo'])))
		{
			@del_file(trim($v['logo']));
		}
	}	
	
	message("删除成功",'/admin.html?do=brand');
?>