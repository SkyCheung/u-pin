<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*if ($_SERVER["REQUEST_URI"]=='/favicon.ico'){
	echo read_file(tpl.$ym_tpl."/favicon.ico");
	exit();
}*/
 
$tp=explode("/",$p);
$p=count($tp)>1?$tp[count($tp)-1]:$p;
$pp=explode("-",$p);

$p= $ym_cats[$p] ? $p : str_replace('-'.$pp[count($pp)-1],'',$p);

if (file_exists(cache_data."diy".Ext)) {
	include(cache_data."diy".Ext); //引入全局缓存
}

$ym_module = array('index','cart', 'user','apidata');
if(in_array($p, $ym_module)) 
{
	if(trim($_REQUEST["action"])!=''){ 
		@include("./inc/lib/".trim($_REQUEST["action"]).Ext);exit();
	}
	require("./inc/module/".$p.Ext); 
	@include template($p, $ym_tpl."/");exit();
}
elseif ($p=='plugin') {
	$ym_tmp_filename = plugin.trim($_REQUEST["mod"]).'/'.trim($_REQUEST["c"]);	
	if(file_exists($ym_tmp_filename.Ext)) // plugin/qq/qq.php
	{
		@include($ym_tmp_filename.Ext);
		exit();
	}
	elseif(file_exists($ym_tmp_filename.'/'.trim($_REQUEST["c"]).Ext)) // plugin/oauth/qq/qq.php
	{
		@include($ym_tmp_filename.'/'.trim($_REQUEST["c"]).Ext);
		exit();
	}
	$ym_tmp_mod = explode('@', trim($_REQUEST["mod"])); 
	if(count($ym_tmp_mod)==3)
	{	
		$ym_tmp_filename = plugin.$ym_tmp_mod[0].'/'.$ym_tmp_mod[1].'/'.$ym_tmp_mod[2].Ext;
		if(file_exists($ym_tmp_filename))
		{
			@include($ym_tmp_filename);
			exit();
		}
		else {
			exit($exitcon);
		}
	}
	else {
		exit($exitcon);
	}	
}
elseif(strpos($p, "paynotify_")===0) { //支付通知
	require("./inc/module/paynotify".Ext); exit();
}

if (count($pp)>1 && $page==0 && !$ym_cats_kv[$p]){ 
	switch ($pp[count($pp)-1]){
		case 'g':
			require("./inc/module/item".Ext);
			exit();
	    break;
	    case 'A':
			require("./inc/module/news_view".Ext);
			exit();
	    break;
	    case 'M':
			require("./inc/module/".$p.Ext);
			exit();
	    break;
	    default:
			exit($exitcon);
	    break;
	}
}
else{	
	$cid = $ym_cats_kv[$p]['id'];		
	$type= isset($type) ? trim($type) : 'list';
	$page = $page==0 ? 1: $page;
	$distmp = trim($ym_cats[$cid]['tpl'])==''? $type:$ym_cats[$cid]['tpl'];
	  
	if (file_exists("./inc/module/".$p.Ext)) {
		require("./inc/module/".$p.Ext); //引入模块程序
	}
	 
	if(intval($cid)==0){
		if (!file_exists(tpldir.$ym_tpl."/".$p.".html")) {
			exit($exitcon);
		}else{
			include template($p,$ym_tpl."/");
			exit();
		}
	}
	
	if (file_exists("./inc/module/".$type.Ext) && $p!=$type) {
		require("./inc/module/".$type.Ext); //引入模块程序
	}
	@include template($distmp, $ym_tpl."/");
} 


?>