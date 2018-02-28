<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'message');//权限检测

$id=intval(ucode($id));
$db->delete('message', array('id' => $id, 'c_sid' => $site_id));

message("处理成功。",$url);
exit();

?>