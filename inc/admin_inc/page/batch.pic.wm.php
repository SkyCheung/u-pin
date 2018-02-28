<?php
if (!defined('in_mx')) {exit('Access Denied');}

$txt=$txt==''?'本操作影响产品模块、图片模块下的所有数据，请慎重处理。':$txt;


$xx33['photo'][0]='图片模块删除水印工作';
$xx33['photo'][1]='图片模块生成水印工作';

$xx33['product'][0]='产品模块删除水印工作';
$xx33['product'][1]='产品模块生成水印工作';

$iswms=intval($_GET["iswms"]);

if ($type!='' && $txt!='ok'){
	if ($iswms==1 && $iswm!=1){
		message('<span style="color:red;">水印功能没有开放，请先设置水印功能！</span>');
		exit;
	}
	if (file_exists(tpl.$ym_tpl."/wm.png")) {
		$wmimg=tpl.$ym_tpl."/wm.png";
	}
	if ($wmimg=='' && $iswms==1){
		message('<span style="color:red;">没有找到水印文件，请先完善水印设置！</span>');
		exit;
	}
	$stttssql='c_sid='.$site_id.'';
	if (intval($allnum)==0){
		$allnum=$db->rowcount(''.$type,$stttssql);
	}
	$pagenum=5;
	$nownum=intval($_GET["nownum"]);
	$xow=$db->fetchall(''.$type,'*',$stttssql,' id desc',$nownum .','.$pagenum);
	$targetFolder = upload_common; // Relative to the root
	require("./inc/class/pic.class.php");
	$img = new image();
	foreach($xow as $rs){
		$c_iswm=$rs['c_iswm'];
		$biggetFile = rtrim($targetFolder,'/') . '/' .$rs['c_bimg'];
		$thegetFile = str_replace('_b','_ox',$biggetFile);
		//$pic_info=getimagesize($biggetFile);
		$xxxx332 .= $rs['c_title']." ";

		if ($iswms==1){
			if ($c_iswm==0){
				@copy($biggetFile,$thegetFile);
				
				@$img->param($biggetFile)->water_mark($biggetFile,$wmimg,$wmwz);  //水印
				$db->update(''.$type, array( 'c_iswm' =>'1'), array('id' => $rs['id'],'c_sid' => $site_id));
			}else{
				@copy($thegetFile,$biggetFile);
				@$img->param($biggetFile)->water_mark($biggetFile,$wmimg,$wmwz);  //水印
			}

		}else{

			if ($c_iswm==1){
				@del_file($biggetFile);
				@rename($thegetFile,$biggetFile);
				$db->update(''.$type, array( 'c_iswm' =>'0'), array('id' => $rs['id'],'c_sid' => $site_id));
			}

		}
		

	}

	$nextnum=$nownum+5;

	$txt= "已完成：".intval($nextnum)." / ".$allnum." ".$xxxx332;

	if ($allnum>$nextnum){
		$tttss= '<meta http-equiv="refresh" content="1; url=/admin.html?do=batch.pic.wm&allnum='.$allnum.'&nownum='.$nextnum.'&type='.$type.'&iswms='.$iswms.'">';

	}else{
		$tttss= '<meta http-equiv="refresh" content="1; url=/admin.html?do=batch.pic.wm&allnum='.$allnum.'&txt='.$xx33[$type][$iswms].'处理完成。">';
	}

}




?>