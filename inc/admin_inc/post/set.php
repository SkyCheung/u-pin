<?php
if (!defined('in_mx')){exit('Access Denied');}

checkAuth($login_id, 'set');//权限检测

$list = '';
$fields='';
if (!empty($_POST))  {
	foreach($_POST AS $key => $value) 
	{
	 	if($key == 'action' || $key == 'file_upload')
		{
			continue;
		}
		 $list .=" when '".$key."' then '". addslashes($value)."'";
		 $fields .= "'" .$key."',";
	} //die($list);
	$bigfilename=trim($watermark_oldimg);
	if($_FILES && $_FILES['file_upload']['tmp_name']!='')//水印图片
	{		
		$targetFolder = upload_common; 
		if( !is_dir($targetFolder) ){
			mdir($targetFolder);
		}
		$tempFile = $_FILES['file_upload']['tmp_name'];
		$targetPath =  $targetFolder; 
		$fileTypes = array('png'); 
		$fileParts = pathinfo($_FILES['file_upload']['name']);

		if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
			$thisext=strtolower($fileParts["extension"]); //扩展名
			$bigfilename = rtrim($targetPath,'/') . '/watermark.'.$thisext;;
			move_uploaded_file($tempFile, $bigfilename); 
		}
		else {
			message("请上传png格式的图片", $url); return;
		}
	}
	$list .=" when 'watermark_img' then '". $bigfilename."'";
	$fields .= "'watermark_img',";
}
if($list == '')
{
	return;
}
$fields=rtrim($fields,",");
//die("".'update '.$tablepre.'shop_config set value= case `key` '.$list.' end where `key` in('.$fields.")");
$db -> exec('update '.$tablepre.'shop_config set value= case `key` '.$list.' end where `key` in('.$fields.")"); 

//update_config();
message("提交成功，数据已更新。", $url);


?>