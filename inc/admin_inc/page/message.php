<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'message');//权限检测

require_once "inc/lib/admin/setting.php";
$row= get_message();
 

?>