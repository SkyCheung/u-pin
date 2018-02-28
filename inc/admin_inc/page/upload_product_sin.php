<?php

if (!defined('in_mx')) {exit('Access Denied');}


$targetFolder = upload_common; // Relative to the root

if( !is_dir($targetFolder) ){
	mdir($targetFolder);
}

$verifyToken = md5('yun_mx' . $_POST['timestamp']);

if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
	require("./inc/class/pic.class.php");
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath =  $targetFolder; //$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
	
	// Validate the file type
	$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
	$fileParts = pathinfo($_FILES['Filedata']['name']);

	$thisext=strtolower($fileParts["extension"]); //��չ��
	$filetxt = get_newName(); //ΨһID
	$newfilename= $filetxt.'_b.'.$thisext;
	$smallfilename=$filetxt.'_s.'.$thisext;
	$targetFile = rtrim($targetPath,'/') . '/' .$newfilename;
	$smallfile= rtrim($targetPath,'/') . '/' .$smallfilename;
	$oldname=$_FILES['Filedata']['name'];
	$titlename=str_replace('.'.$fileParts["extension"],'',$oldname);
	
	if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
		//move_uploaded_file($tempFile,$targetFile);
		move_uploaded_file($tempFile,$targetFile); 

		$pic_info=getimagesize($targetFile);

		

		$water='./inc/class/water2.png';

		///����Сͼ

		$img = new image();
	
		$smalltype=$ym_idsort[intval($c_toid)]['smtype']==''?$smalltype:$ym_idsort[intval($c_toid)]['smtype'];

		$img->param($targetFile)->thumb($smallfile, $product_w,$product_h,intval($smalltype));

		if ($pic_info[0]>$max_w){
			$img->param($targetFile)->thumb($targetFile,$max_w,$max_h,0);
		}

		//$img->param($targetFile)->water_mark($targetFile,$water,'dr');  //ˮӡ



		$db->insert(''.$stype, array('c_id' => intval($login_id),'c_sid' => intval($site_id),'c_toid' => intval($c_toid),'c_time' => time(),'c_addtime' => time(), 'c_simg' => ptitle($smallfilename),'c_bimg' => ptitle($newfilename), 'c_title' => ptitle($titlename)));
		$lastid=$db->lastinsertid();
		$tempcode12252 = jcode($lastid);
		$db->update(''.$stype, array('c_code' =>$tempcode12252), array('id' => $lastid));
		



/* 
		//������1
		$img=new Img();
		$option2 = array ('width' => $product_w, 'height' => $product_h );
		if (intval($smalltype)==0){
			$flag = $img->thumb_img($targetFile, $smallfile,$option2);
		}else{
			$flag = $img->resize_image($targetFile, $smallfile,$option2);
		}
		if ($pic_info[0]>$max_w){
			$option1 = array ('width' => 1000, 'height' => 16000 );
			$flag = $img->resize_image ($targetFile, $targetFile,$option1);
		}

		//$flag = $img->water_mark($targetFile,$water,'tc');// //ˮӡ
		//$resultss = @unlink ($targetFile); 

*/
		echo $newfilename.'';
	} else {
		echo '';
	}
}


exit;
?>