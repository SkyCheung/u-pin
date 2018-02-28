<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'set');//权限检测

$id=intval(ucode($id));

$row=$db->fetch('link', '*',  array('id' => $id,'c_sid' => $site_id), 'id DESC');

$targetFolder = upload_common; // Relative to the root
$oldfile1=$targetFolder.'/'.trim($row['c_simg']);

@del_file($oldfile1);

$db->delete('link', array('id' => $id, 'c_sid' => $site_id));

update_diy();


message("处理成功。",$url);
exit();

?>