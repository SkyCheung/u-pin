<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'diy');//权限检测
$db->delete('diy', array('id' => $id));

update_diy();


message("删除成功。",$url);
exit();

?>