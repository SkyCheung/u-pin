<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'news');//权限检测  

if (trim($c_title)==""){message("请填写文章标题。");}
$filecode=trim($id);
$id=intval(ucode($id));
if ($id==0){message("获取文章编号出错.");}


$row=$db->fetch('news', 'id,c_toid', array('id' => $id), 'id asc');
if (!$row){message("获取文章信息失败.");}
$xtoid=$row['c_toid'];
$row='';

$simg_filename=trim($c_simg);
$bimg_filename=trim($c_bimg);
if ($_FILES){
	$targetPath = upload_news;	
	$tempFile = $_FILES['file_upload']['tmp_name'];
	$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
	$fileParts = pathinfo($_FILES['file_upload']['name']);

	if (in_array(strtolower($fileParts['extension']),$fileTypes)) {
		$thisext=strtolower($fileParts["extension"]); //扩展名
		$filetxt = get_newName(); //唯一ID
		//$bigfilename= $filetxt.'_b.'.$thisext;
		$targetPath = rtrim($targetPath,'/') . '/'.date("Ymd")."/";
		if( !is_dir($targetPath) ){
			mdir($targetPath);
		}
		$simg_filename = $targetPath .$filetxt.'_s.'.$thisext;
		$bimg_filename = $targetPath .$filetxt.'_b.'.$thisext;
		
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

		if($c_simg!='' && file_exists($c_simg))
		{
			@del_file($c_simg);
		}
		if($c_bimg!='' && file_exists($c_bimg))
		{
			@del_file($c_bimg);
		}
	}

}
//上传

$btime=intval(strtotime($c_time));
$btime=$btime+$id;

$c_body=udownload(1,$c_body);

$c_body=str_replace('<img src="upload','<img src="/upload',$c_body);

$db->update('news', array( 'c_title' => ptitle($c_title),'c_author' => ptitle($c_author), 'c_time' => $btime ,  'c_toid' => intval($c_toid),'c_smtype' => trim($c_smtype), 'c_c' => intval($c_c), 'c_body' => $c_body  , 'c_simg' => ptitle(ltrim($simg_filename,"./")), 'c_bimg' => ptitle(ltrim($bimg_filename,"./")), 'c_txt' => $c_txt), array('id' => $id));


del_dir(cache.$site_code.'/newslist');

/*$tempadaurl234=$ym_idsort[$xtoid]['dir']==''?'':$ym_idsort[$xtoid]['dir'].'/';
$xx5323cdfq='/'.$tempadaurl234.$ym_idsort[$xtoid]['file'].Condir.$filecode.$conext['news'];
$rsx=$db->fetch('site', 'c_url',  array('id' => $site_id,'c_id' => $login_id ), 'id asc');
$urlxx35ry=$rsx['c_url'];
$hostname33d3=strtolower(str_replace("www.","",trim($urlxx35ry)));
$hostnamexx3a=explode(",",$hostname33d3);
for ($k=0;$k<count($hostnamexx3a);$k++){
	$xxxfiele3q2=$hostnamexx3a[$k].md5($xx5323cdfq);
	@del_file(cache.$site_code.'/content/'.$xxxfiele3q2);
}
*/

//update_config($id,ptitle($c_mo),ptitle($c_url));

message("提交成功，数据已更新。",'/admin.html?do=news&id='.$toid);
?>