<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'columns');//权限检测 

$code=$id;
$id=intval(ucode(trim($id)));
if ($id==0){message("ERROR.");}

$name=$ym_idsort[$id]['title'];
$pid=$ym_idsort[$id]['pid'];
$type=$ym_idsort[$id]['type'];
$backcode=$ym_idsort[$pid]['code'];
$backtype=$ym_idsort[$pid]['type'];
$sssortid = $id;
$thisfile=$ym_idsort[$id]['file'];
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

if (file_exists(cache.$ym_tpl.'/spage/'.$code.'.html') ) {
	$c_body=read_file(cache.$ym_tpl.'/spage/'.$code.'.html');
}else{
	$row=$db->fetch('page', 'c_body', array('c_toid' => $id), 'id asc');
	$c_body=$row['c_body'];
}

$oimglist = getImgs($c_body);

if ($oimglist){
	foreach($oimglist as $xx){
		$oimg .=$xx.',';
	}
}



?>