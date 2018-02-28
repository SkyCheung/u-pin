<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*退换货详情*/
checkAuth($login_id, 'service');//权限检测

if(!isset($id) || intval($id)==0)
{
	message('获取服务单号失败');
}

update_order_service($id, array('status'=>intval($status), 'remark'=>trim($remark), 'updatetime'=>time()));
 
message('更新成功','/admin.html?do=service');
 
?>