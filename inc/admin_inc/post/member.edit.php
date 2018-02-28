<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'member');//权限检测
 
if(intval($id)==0){message("获取不到id");}
if(trim($uname)==''){message("用户名不能为空");}

$data = array('uname' => trim($uname) ,'sex' => intval($sex),'realname' => trim($realname),'email' => trim($email), 'tel' => trim($tel) ,'mobile' => trim($mobile) ,'status' => intval($status) ,'grade_id' =>intval($grade_id),'birthday'=>intval($birthday), 'qq' => trim($qq), 'commiss_id' => intval($commiss_id));
if (trim($pwd)!=''){
	$salt= get_salt();
	$data['pwd'] = encryptStr($pwd, $salt);
	$data['salt'] = $salt;
}

$db->update('member', $data, array('id' => intval($id)));


message("提交成功，数据已更新。",'/admin.html?do=member');


?>