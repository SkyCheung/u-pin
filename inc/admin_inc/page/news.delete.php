<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'news');//权限检测

$id=intval(ucode($id));

$row=$db->fetch('news', '*',  array('id' => $id), 'id DESC');

$targetFolder = upload_news; // Relative to the root
$oldfile1=$targetFolder.'/'.trim($row['c_simg']);
$oldfile2=$targetFolder.'/'.trim($row['c_bimg']);

@del_file($oldfile1);
@del_file($oldfile2);

$db->delete('news', array('id' => $id));

message("处理成功。",$url);
exit();

?>