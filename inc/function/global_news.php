<?php
if (!defined('in_mx')) {exit('Access Denied');}
 
$tp=explode("/",$p);
$p=count($tp)>1?$tp[count($tp)-1]:$p;
 
$pp = explode("-", $p);  //print_r($pp);
if($ym_url_path[0] !=='news')
{
	$p = $pp[1]; 
}
//$page=count($pp)==2 ? 0 : intval($pp[2]);

$p= $ym_allsort[$p]?$p:str_replace('-'.$pp[count($pp)-1],'',$p);  //print_r($ym_url_path); //echo " p= ".$p.'  ym_url_path='.$ym_url_path[0]; 

if (file_exists(cache_data."diy".Ext)) {
	include(cache_data."diy".Ext); //引入全局缓存
}
 
if(trim($_REQUEST["action"])!=''){
	@include("./inc/post/".trim($_REQUEST["action"]).Ext);
} 

if ($page==0 && $ym_url_path[0]=='news'){ //print $ym_url_path[0];
	require("./inc/module/".$ym_url_path[0]."_view".Ext);
	
	//require("./inc/module/diy/".$p.Ext); //验证码    
}else{
	$name=$ym_allsort[$p]['title'];
	$txt=$ym_allsort[$p]['txt'];
	$pageid=$ym_allsort[$p]['id'];
	$type=$ym_allsort[$p]['type']==''?'index':$ym_allsort[$p]['type'];
	$url=$ym_allsort[$p]['url'];
	$file=$ym_allsort[$p]['file'];
	$sort_power=$ym_allsort[$p]['member'];
	$code=$ym_allsort[$p]['code'];
	$dir=$ym_allsort[$p]['dir']==""?"":$ym_allsort[$p]['dir']."/";
	$look=$ym_allsort[$p]['look'];
	$cimg=$ym_allsort[$p]['img'];

	$p_id=$ym_allsort[$p]['pid'];
	$p_name=$ym_idsort[$p_id]['title'];
	$p_txt=$ym_idsort[$p_id]['txt'];
	$p_url=$ym_idsort[$p_id]['url'];
	$p_code=$ym_idsort[$p_id]['code'];
	$p_dir=$ym_idsort[$p_id]['dir'];
	$p_look=$ym_idsort[$p_id]['look'];
	$p_cimg=$ym_idsort[$p_id]['img'];

	$sub=getsub($pageid);
	$subname=$sub['title'];
	$subtitle=$sub['title'];
	$subtxt=$sub['txt'];
	$suburl=$sub['url'];
	$subfile=$sub['file'];
	$subcode=$sub['code'];
	$subid=$sub['id'];
	$subcimg=$sub['img'];
	$sub=$sub['son']; 
	$page=count($pp)<2?1:intval($page);
	$page=$page==0?1:$page; 
	$distmp=trim($ym_allsort[$p]['mo'])==''?$type:$ym_allsort[$p]['mo'];
	$inmodule=$type==''?$p:$type;
	$navarr=get_parents($pageid, $ym_idsort);
	$tttemp='$sort';
	foreach ($navarr as $rs){
		$subnav .= ' &gt; <a href="'.$rs['url'].'">'.$rs['title'].'</a>';
		$tttemp .="['".$rs['code']."']['son']";
	}
	eval('$thisarr = '.$tttemp.';');
	
	$insort=$thisarr;
	$tosort=$insort;
	if (!$tosort){
		foreach($ym_allsort as $alist){
			if ($alist['pid']==$p_id){
				$tosort[]=$alist;
			}
		}
	}

	if (file_exists("./inc/module/".$type.Ext)) {
		require("./inc/module/".$type.Ext); //引入模块程序
	}
} 
@include template($distmp,$ym_tpl."/");

?>