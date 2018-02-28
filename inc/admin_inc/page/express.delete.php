<?php
if (!defined('in_mx')) {exit('Access Denied');}
 
  checkAuth($login_id, 'express');//权限检测
  
	if ($id=='' || isNumComma($id)==false){message("获取编号失败");}
 	$db->delete ('express_common', "id in(". $id .")") ;
	$db->delete('express_district', " express_id in (".$id.")");
	$db->delete('express_picksite', " express_id in (".$id.")");
	message("删除成功",'/admin.html?do=express');
?>