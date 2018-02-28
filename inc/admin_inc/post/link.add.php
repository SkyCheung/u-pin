<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'set');//权限检测

if (trim($c_title)==""){message("请输入标题名称!");}
if (trim($c_url)==""){message("请输入链接地址!");}

$c_url=trim($c_url);
if(substr($c_url,0,4)!='http'){
	$c_url='http://'.$c_url;
}

//上传
$bigfilename='';
if ($_FILES){
	$targetFolder = upload_link; // Relative to the root
	if( !is_dir($targetFolder) ){
		mdir($targetFolder);
	}
	$tempFile = $_FILES['file_upload']['tmp_name'];
	$targetPath =  $targetFolder; //$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
	$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
	$fileParts = pathinfo($_FILES['file_upload']['name']);

	$thisext=strtolower($fileParts["extension"]); //扩展名
	$filetxt = get_newName(); //唯一ID
	$bigfilename= $filetxt.'.'.$thisext;
	$biggetFile = rtrim($targetPath,'/') . '/' .$bigfilename;
	if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
		//move_uploaded_file($tempFile,$targetFile);
		move_uploaded_file($tempFile,$biggetFile); 
	}else{
		$bigfilename='';
	}
}
//上传

$db->insert('link', array('c_id' => intval($login_id),'c_url' => ptitle($c_url),'c_index' => intval($c_index), 'c_simg' => ltrim(upload_link,"./").ptitle($bigfilename), 'c_title' => ptitle($c_title) , 'c_is' => intval($c_is) ,'c_time' =>time()));
$lastid=$db->lastinsertid();

$tempcode12252= jcode($lastid);

$db->update('link', array('c_code' =>$tempcode12252), array('id' => $lastid));

update_diy();

message("提交成功，数据已更新。",'/admin.html?do=link');


?>