<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'set');//权限检测 

$id=intval(ucode($id));

$vow=$db->fetchall('banner_pic', '*',  array('c_toid' => $id,'c_sid' => $site_id), 'id asc', '');

foreach($vow as $row){
	$targetFolder = upload_common; // Relative to the root
	$oldfile1=$targetFolder.'/'.trim($row['c_simg']);
	$oldfile2=$targetFolder.'/'.trim($row['c_bimg']);

	@del_file($oldfile1);
	@del_file($oldfile2);

	$db->delete('banner_pic', array('id' => $row['id'], 'c_id' => $login_id));
}
$db->delete('banner', array('id' => $id, 'c_id' => $login_id));

message("处理成功。",$url);
exit();

?>