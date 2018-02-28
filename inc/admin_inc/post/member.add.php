<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'member');//权限检测

$uname = trim($uname);
if($uname==''){message("用户名不能为空");}
if(trim($pwd)==''){message("密码不能为空");}

$pids= array('pid1'=>intval($pid1),'pid2'=>0, 'pid3'=>0);
$uid = add_user($uname,trim($mobile),$pwd,$img, trim($email),intval($birthday),trim($qq), intval($sex),trim($realname), $pids, intval($commiss_id), intval($grade_id));

message("提交成功，数据已更新。",'/admin.html?do=member');


?>