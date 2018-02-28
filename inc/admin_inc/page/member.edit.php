<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'member');//权限检测

if (intval($id)==0){message("获取不到id");} 

require_once './inc/lib/admin/member.php';
$grade = get_grade();
$row=$db->fetch('member', '*',  array('id' => $id));

$addtime= date("Y-m-d H:i:s",$row['addtime']);
$cbk_sex[$row['sex']]="checked='checked'";
$cbk_status[$row['status']]="checked='checked'";

?>