<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'set');//权限检测 

$id=intval(ucode($id));

$row=$db->fetch('banner_pic', '*',  array('id' => $id), 'id DESC');
$targetFolder = upload_common; // Relative to the root
$oldfile1=$targetFolder.'/'.trim($row['c_simg']);
$oldfile2=$targetFolder.'/'.trim($row['c_bimg']);

@del_file($oldfile1);
@del_file($oldfile2);

$db->delete('banner_pic', array('id' => $id));


message("处理成功。",$url);
exit();

?>