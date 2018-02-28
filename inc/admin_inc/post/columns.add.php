<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'columns');//权限检测

if (trim($c_title)==""){message("请填写分类名称。");}

$c_name=str_replace(".html","",$c_name);
$c_name=str_replace(".htm","",$c_name);
$c_name=str_replace(".asp","",$c_name);
$c_name=str_replace(".php","",$c_name);
$c_name=str_replace(".jsp","",$c_name);
$c_name=str_replace(".","",$c_name);
$c_name=str_replace("/","",$c_name);
$c_name=str_replace("&","",$c_name);
$c_name=str_replace("*","",$c_name);
$c_name=str_replace("%","",$c_name);
$c_name=str_replace("#","",$c_name);
$c_name=str_replace("!","",$c_name);
$c_name=str_replace("(","",$c_name);
$c_name=str_replace(")","",$c_name);
$c_name=str_replace("-","_",$c_name);


if ($c_name=='admin'){
	message('文件名称中含有不允许字符。'); 
}

if ($c_name=='manage'){
	message('文件名称中含有不允许字符。'); 
}

$_march = '/^[a-zA-Z0-9_-]{3,16}$/';  
if(!preg_match($_march, $c_name) && trim($c_name)!='') {  
	message('文件名称中含有不允许字符。');  
} 

if(!preg_match($_march, $c_mo) && trim($c_mo)!='') {  
	message('模板文件名称中含有不允许字符。');  
} 

$row=$db->fetch('columns', 'id', array('c_sid' => $site_id,'c_id' => $login_id,'c_name' => ptitle($c_name)), 'id asc');
if ($row){message("文件名称已存在，文件名称不能与其它栏目重复。");}

$c_dir=str_replace(".html","",$c_dir);
$c_dir=str_replace("/","",$c_dir);
$c_dir=str_replace("\\","",$c_dir);

if(!preg_match($_march, $c_dir) && trim($c_dir)!='') {  
	message('存放目录名称中含有不允许字符。');  
} 

if (trim($c_mo)=='' && trim($c_type)=='index'){
	$c_mo=='index';
}


$c_mo=str_replace(".html","",$c_mo);
$c_mo=str_replace(".htm","",$c_mo);
$c_mo=$c_mo=='page'?'':$c_mo;

//上传
$newfilename='';
if ($_FILES){
	$targetFolder = upload_news; // Relative to the root
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

if (trim($c_url)!=''){
	if (substr($c_url,0,1)!='/' && substr($c_url,0,5)!='http:' && substr($c_url,0,6)!='https:'){
		$c_url='/'.$c_url;
	}
}

$c_body=str_replace("src=\"/images/","src=\"".tpl.$site_mo."/images/",$c_body);
$c_body=str_replace("src=\"images/","src=\"".tpl.$site_mo."/images/",$c_body);

if (trim($seo_title)<>'' || trim($seo_keyword)<>'' || trim($seo_description)<>''){
	$c_seo=json_encode(array('seo_title'=>trim($seo_title),'seo_keyword'=>trim($seo_keyword),'seo_desc'=>trim($seo_description)));
}


$db->insert('columns', array('c_id' => intval($login_id),'c_pid' => intval($c_pid),'c_sid' => intval($site_id), 'c_member' => intval($c_member), 'c_width' => intval($c_width),'c_height' => intval($c_height),'c_num' => intval($c_num),'c_name' => ptitle($c_name), 'c_title' => ptitle($c_title),'c_txt' => ptitle($c_txt), 'c_url' => ptitle($c_url), 'c_seo' => ptitle($c_seo), 'c_mo' => ptitle($c_mo),'c_smtype' => trim($c_smtype), 'c_dir' => ptitle($c_dir), 'c_simg' => ptitle(ltrim($biggetFile,"./")),'c_look' => intval($c_look),'is_help' => intval($is_help),'c_body' => $c_body,'c_is' => intval($c_is), 'c_type' => ptitle($c_type)));
$lastid=$db->lastinsertid();

$idcode=jcode($lastid);

if (ptitle($c_name)==''){
	$db->update('columns', array('c_name' => $idcode), array('id' => $lastid));
}

$db->update('columns', array('c_code' => $idcode), array('id' => $lastid));


update_config($site_id,$site_mo,$site_url);

message("提交成功，数据已更新。",'/admin.html?do=columns');


?>