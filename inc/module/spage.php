<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*单页面*/
$nav = get_nav(); //导航
$nav_footer = get_nav('bot');
$cats = get_catTree(); //分类树
$help = get_help(); //帮助
$crumbs_nav = get_crumbs_nav('news', $pageid); //面包导航
 
dbc();

$columns=$db->fetch('columns', 'c_body,c_seo',  array('id' => $pageid), 'id asc');
/*if (file_exists(cache.$ym_tpl.'/spage/'.$code.'.html') ) {
	$body=read_file(cache.$ym_tpl.'/spage/'.$code.'.html');
}else*/
{
	$row=$db->fetch('page', 'c_body,c_toid', array('c_toid' => $pageid), 'id asc');
	$body=$row['c_body'];
}

$c_body= $columns['c_body'];//分类描述
$title = $name;

$seo=json_decode($columns['c_seo'],true);
if($seo)
{
	$name = $seo['seo_title']==''? $name : $seo['seo_title'];
	$webtitle= $seo['seo_title'] ;
	$web_keyword= $seo['seo_keyword']=='' ? $ym_keywords : $seo['seo_keyword'];
	$web_description= $seo['seo_desc']=='' ? $ym_description : $seo['seo_desc'];
}

//左边分类树 
if($p_id==0)
{
	$cat = $ym_sort[$code];
}
else {
	$root_cat = get_parents($pageid, $ym_idsort);
	$cat = $ym_sort[$root_cat[0]['code']];
}
$channel_name = $cat['name']; 
$cat = $cat['son'];
$mo = $distmp ? $distmp:$ym_idsort[$row["c_toid"]]['mo'];
$mo = $mo && $mo!='' ? $mo : 'news';

@include template($mo."_view",$ym_tpl."/");
die();

?>