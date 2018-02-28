<?php
if (!defined('in_mx')) {exit('Access Denied');}
$code=trim($id);
$id=intval(ucode(trim($id)));
if ($id==0){message("ERROR.");}


$name=$ym_idsort[$id]['title'];
$pid=$ym_idsort[$id]['pid'];
$type=$ym_idsort[$id]['type'];
$backcode=$ym_idsort[$pid]['code'];
$backtype=$ym_idsort[$pid]['type'];
$sssortid = $id;
$thisfile=$ym_idsort[$id]['file'];
$thiscode=$ym_idsort[$id]['code'];
$look=$ym_allsort[$thisfile]['look'];

foreach($ym_allsort as $alist){
	if ($alist['pid']==$id){
		$nextlist[]=$alist;
	}
}
$nextsortname='下级分类';
if (!$nextlist){
	foreach($ym_allsort as $alist){
		if ($alist['pid']==$pid){
			$nextlist[]=$alist;
		}
	}
	$nextsortname='同级分类';
}

$navarr=get_parents($id, $ym_idsort);
$tttemp='$sort';
foreach ($navarr as $rs){
	$subnav .= ' &gt; <a href="'.$rs['url'].'">'.$rs['title'].'</a>';
	$tttemp .="['".$rs['code']."']['son']";
}
eval('$thisarr = '.$tttemp.';');
$sssortid = $id.thisallid($thisarr);


if (trim($keyword)!= ""){
	$keycode="and c_title LIKE '%$keyword%'";
}else{
	$keycode='';
}



$searchid=$sssortid;

$stttssql='c_sid='.$site_id.' and c_toid in ('.$searchid.') '.$keycode;

$page=intval($page)==0?1:intval($page);
$pagenum=12;
require("./inc/class/page.class.php");
$thisfile='/'.$dir.'/'.$file;
$thispageurl=explode('?',$_SERVER["REQUEST_URI"]);
$thispageurl=$thispageurl[1]==''?'':'?'.$thispageurl[1];
$params = array(
			'total_rows'=>$db->rowcount(''.$type,$stttssql), #(必须)
			'method'    =>'html', #(必须)
			'parameter' => $thispageurl.'&page={?}',  #(必须)
			'now_page'  =>$page,  #(必须)
			'list_rows' =>$pagenum, #(可选) 默认为15
);
$thispage = new Core_Lib_Page($params);
$pages=$thispage->show(1);
$startI = $page*$pagenum-$pagenum;

$targetFolder = upload_common;

$xow=$db->fetchall(''.$type,'*',$stttssql,' c_order asc,id desc');

foreach($xow as $rs){
	$rs['id']=$rs['id'];
	$rs['time']=ftime($rs['c_time']);
	$rs['title']=$rs['c_title'];
	$rs['code']=$rs['c_code'];
	$rs['order']=$rs['c_order'];
	$rs['simg']=$targetFolder.$rs['c_simg'];
	$row[]=$rs;
}


?>