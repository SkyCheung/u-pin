<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'set');//权限检测

$filecode=trim($id);
$id=intval(ucode($id));

$row=$db->fetch('banner', 'id', array('id' => $id), 'id asc');
if (!$row){message("Error.");}
$row='';

//上传
$newfilename='';
if ($_FILES){
	$targetFolder = upload_banner; // Relative to the root
	if( !is_dir($targetFolder) ){
		mdir($targetFolder);
	}
	$tempFile = $_FILES['file_upload']['tmp_name'];
	$targetPath =  $targetFolder; //$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
	$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
	$fileParts = pathinfo($_FILES['file_upload']['name']);
	$oldFilename = $_FILES['file_upload']['name'];

	$thisext=strtolower($fileParts["extension"]); //扩展名
	$filetxt = get_newName(); //唯一ID
	$newfilename= $filetxt.'_s.'.$thisext;
	$bigfilename= $filetxt.'_b.'.$thisext;
	$targetFile = rtrim($targetPath,'/') . '/' .$newfilename;
	$biggetFile = rtrim($targetPath,'/') . '/' .$bigfilename;
	if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
		move_uploaded_file($tempFile,$biggetFile); 
		require("./inc/class/pic.class.php");
		$pic_info = getimagesize($biggetFile);
		$img = new image();


		if (intval($c_width)==0 || intval($c_height)==0){

		}else{
			$img->param($biggetFile)->thumb($targetFile,$c_width,$c_height,1);
		}

		$db->insert('banner_pic', array('c_id' => $login_id,  'c_toid' => intval($c_toid), 'c_simg' => ltrim(upload_banner,"./").ptitle($newfilename), 'c_bimg' => ltrim(upload_banner,"./").ptitle($bigfilename), 'c_title' => ptitle($oldFilename),  'c_url' => ptitle($c_url),'c_txt' => ptitle($c_txt),  'c_time' => time(),'c_code'=>''));
		$lastid=$db->lastinsertid();
		$tempcode12252= jcode($lastid);
		$db->update('banner_pic', array('c_code' =>$tempcode12252), array('id' => $lastid));
	}else{
		$newfilename='';
		$bigfilename='';
	}
}
//上传

message("提交成功，数据已更新。","./admin.html?do=banner.upload&type=".$type."&id=".$filecode);
?>