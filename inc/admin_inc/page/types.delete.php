<?php
if (!defined('in_mx')) {exit('Access Denied');}
 
   checkAuth($login_id, 'types');//权限检测 
	if ($id=='' || isNumComma($id)==false){message("获取编号失败");}
 	$db->delete ('type', "id in(". $id .")") ;
	$db->delete('attribute', " type_id in (".$id.")");
	message("删除成功",'/admin.html?do=types');
?>