<?php
if (!defined('in_mx')) {exit('Access Denied');}


if (intval($login_id)==0){
	message('请先登录', "login.html");  
}

$newpwd =trim($newpwd);
$oldpwd =trim($oldpwd);

if ($oldpwd ==''){
	message('请输入旧密码');  
}
if ($newpwd ==''){
	message('请输入新密码');  
}
 
$user= get_adm_userinfo($login_id);
if(encryptStr($oldpwd, $user['salt']) != $user['pwd'])
{
	message('旧密码不正确'); 
}

$salt= get_salt(); //生成新salt
$data['pwd'] = encryptStr($newpwd, $salt);
$data['salt'] = $salt;
$res= update_adm_user($data, $login_id); logs(json_encode($res));
 
message( ($res==false ?'修改失败':"修改成功"),'/admin.html?do=pwd');
?>