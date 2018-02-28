<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'member');//权限检测

require_once './inc/lib/admin/member.php';
$grade = get_grade();

?>