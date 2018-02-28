<?php
if (!defined('in_mx')) {exit('Access Denied');}
  
checkAuth($login_id, 'category');//权限检测
 
$cat_name =isset($cat_name) ? trim($cat_name) : '';
if (trim($act)==""){message("操作类型不正确。");} 
if ($cat_name==""){message("请填写分类名称。");}
if (strlen($cat_name)>50){message("分类名称请控制在50个字符内");}
if (trim($pid)==""){message("请选择父级分类。");}
if (strlen(trim($url))>255){message("url请控制在255个字符内");}
if(trim($urlname)!='' && !preg_match('/^[A-Za-z0-9]{1,20}$/', $urlname))
{
		message('文件名称只能使用英文和数字');  
}		
$grade= str_replace(PHP_EOL, ',', trim($grade));
$link=trim($link)=='http://' ? '' :trim($link);

 
 $values=$_POST['value'];
 $len=count($attr_name); 

 //上传
$newfilename=!isset($oldimg)|| trim($oldimg)==''?'':trim($oldimg);
if($_FILES && $_FILES['img']['tmp_name']!=''){
	$targetFolder = upload_cat; // Relative to the root
	if(!is_dir($targetFolder) ){
		mdir($targetFolder);
	}
	$tempFile = $_FILES['img']['tmp_name'];
	$targetPath =  $targetFolder; 
	$fileTypes = array('jpg','jpeg','gif','png');
	$fileParts = pathinfo($_FILES['img']['name']); 

	$thisext=strtolower($fileParts["extension"]); //扩展名
	$filetxt = get_newName(); //唯一ID
	$newfilename= $filetxt.'.'.$thisext;
	$newfilename = rtrim($targetPath,'/') . '/' .$newfilename;
	if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
		move_uploaded_file($tempFile,$newfilename);		
		//require("./inc/class/pic.class.php"); 
	}else{
		$newfilename='';
		message("仅支持jpg、jpeg、gif、png格式的图片");return;		
	}
}
 
 
 if($act =='add')
 {
 	  if(check_catname($cat_name, $pid))
		{
			message("分类名称已存在，请更换。");
		}
		if(check_urlname(trim($urlname)))
		{
			message("文件名称已存在，请更换。");
		} 			 	 		

    $db->insert('category', array('name' => $cat_name,'pid' => intval($pid),'type_id' => intval($goods_type),'status' => intval($status),'sort' => intval($cat_sort),'remark' => trim($remark),'keywords' => trim($c_keywords),'description' => trim($c_description),'img' => trim(ltrim($newfilename,"./")),'grade' => $grade,'urlname' => trim($urlname),'tpl' => trim($tpl),'link' => $link,'level' => intval($level),'num' => intval($num) ));
	  $lastid=$db->lastinsertid();
	  
	  if(trim($urlname) =='')
	  {
	  	$db->update('category', array('urlname' => jcode($lastid)), array('id' => $lastid));
	  }
	  
	  update_config();//更新缓存	 
	  message("保存成功",'/admin.html?do=category');  
 }
 elseif ($act =='edit') {	
	  if(check_catname($cat_name, $pid, $id))
		{
			message("分类名称已存在，请更换。");
		}
		if(check_urlname(trim($urlname), $id))
		{
			message("文件名称已存在，请更换。");
		}
	
		if(trim($urlname) =='')
	  {
	  	$urlname = $old_urlname!=''? $old_urlname: jcode($id);
	  }	
 	  $db->update('category', array('name' => $cat_name,'pid' => intval($pid),'type_id' => intval($goods_type),'status' => intval($status),'sort' => intval($cat_sort),'remark' => trim($remark),'keywords' => trim($c_keywords),'description' => trim($c_description),'img' => trim(ltrim($newfilename,"./")),'grade' => $grade,'urlname' => trim($urlname),'tpl' => trim($tpl),'link' => $link,'level' => intval($level),'num' => intval($num) ), array('id' => trim($id)));
	
	  if($newfilename!=$oldimg && file_exists(trim($oldimg)))
	  {
	  	@del_file($oldimg);  
	  }
	   update_config();//更新缓存	 
     message("更新成功",'/admin.html?do=category');
	 } 
	 
 


?>