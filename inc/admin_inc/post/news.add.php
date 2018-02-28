<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'news');//权限检测  

$filecode=trim($id);
$id=intval(ucode($id));

if (trim($c_title)==''){message("请输入文章标题！");}

/*$row=$db->fetch('columns', 'id', array('id' => $id), 'id asc');
if (!$row){message("Error.");}
$row='';*/
$c_body=udownload(1,$c_body);

//上传
$simg_filename='';
if ($_FILES){
	$targetPath =upload_news; // Relative to the root
	$tempFile = $_FILES['file_upload']['tmp_name'];
	$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
	$fileParts = pathinfo($_FILES['file_upload']['name']); 

	$thisext=strtolower($fileParts["extension"]); //扩展名
	$filetxt = get_newName(); //唯一ID
	
	$targetPath = rtrim($targetPath,'/') . '/'.date("Ymd")."/";
	if( !is_dir($targetPath) ){
		mdir($targetPath);
	}
	$simg_filename = $targetPath .$filetxt.'_s.'.$thisext;
	$bimg_filename = $targetPath .$filetxt.'_b.'.$thisext;
	if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
		move_uploaded_file($tempFile,$bimg_filename);
		require("./inc/class/pic.class.php"); 
		$pic_info=getimagesize($bimg_filename);
		$img = new image();

		$thumb_config = get_thumb_config($c_toid, 'news');
		$thumb_type= $c_smtype!='' ? $c_smtype : $thumb_config['thumb_type'];
		$img->param($bimg_filename)->thumb($simg_filename,$thumb_config['width'],$thumb_config['height'], $thumb_type);

		if ($pic_info[0]>1200){
			$img->param($bimg_filename)->thumb($bimg_filename,1200,1200, $thumb_type);
		}

	}else{
		$simg_filename='';
		$bimg_filename='';
	}
}

//上传

$btime=intval(strtotime($c_time));

$c_body=udownload(1,$c_body);
$c_body=str_replace('<img src="upload','<img src="/upload',$c_body);

$db->insert('news', array('c_id' => $login_id,  'c_toid' => intval($c_toid), 'c_c' => intval($c_c),'c_smtype' => trim($c_smtype), 'c_simg' => ptitle($simg_filename), 'c_bimg' => ptitle($bimg_filename), 'c_time' => $btime, 'c_title' => ptitle($c_title), 'c_author' => ptitle($c_author), 'c_addtime' => time(), 'c_txt' => $c_txt,'c_body' => $c_body));
$lastid=$db->lastinsertid();

$btime=$btime+$lastid;

$db->update('news', array('c_code' =>jcode($lastid),'c_time' =>$btime), array('id' => $lastid));

del_dir(cache.$site_code.'/newslist');
message("提交成功，数据已更新。","./admin.html?do=news&id=".jcode($c_toid));

?>