<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'news');//权限检测
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

$navarr= get_parents($id, $ym_idsort);
$list=array();
foreach ($navarr as $i=> $rs){
	$subnav .= ' &gt; <a href="'.$rs['url'].'">'.$rs['title'].'</a>';
	$list = $i==0 ? $ym_sort[$rs['code']]['son'] : $list[$rs['code']]['son'];
}
$sssortid = $id.thisallid($list);

if (trim($keyword)!= ""){
	$keycode="and c_title LIKE '%$keyword%'";
}else{
	$keycode='';
}

$searchid=$sssortid;

$stttssql=' c_toid in ('.$searchid.') '.$keycode;

$page=intval($page)==0?1:intval($page);
$pagenum=12; 
$startI = $page*$pagenum-$pagenum;
$count =$db->rowcount('news', '');
$pages = getPages($count,$page, $pagenum);

$xow=$db->fetchall('news','*','c_toid in ('.$searchid.') '.$keycode,' sort asc,c_time desc',$startI .','.$pagenum);

foreach($xow as $rs){
	$rs['id']=$rs['id'];
	$rs['time']=ftime($rs['c_time']);
	$rs['title']=$rs['c_title'];
	$rs['code']=$rs['c_code'];
	$row[]=$rs;
}


?>