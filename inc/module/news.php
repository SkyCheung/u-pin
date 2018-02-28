<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*修改密码*/

$nav = get_nav(); //导航
$nav_footer = get_nav('bot');
$cats = get_catTree(); //分类树
$help = get_help(); //帮助
$crumbs_nav = get_crumbs_nav('news', $pageid); //面包导航

dbc();
$columns=$db->fetch('columns', 'c_body,c_seo',  array('id' => $pageid), 'id asc');
$seokk=json_decode($columns['c_seo'],true);
if($seokk)
{
	$name = $seokk['seo_title']==''? $name : $seokk['seo_title'];
	$webtitle= $seokk['seo_title'] ;
	$web_keyword= $seokk['seo_keyword']=='' ? $ym_keywords : $seokk['seo_keyword'];
	$web_description= $seokk['seo_desc']=='' ? $ym_description : $seokk['seo_desc'];
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

$pagenum = $ym_idsort[$pageid]['num']==0 ? 12: $ym_idsort[$pageid]['num'];
$page = intval($page)==0 ? 1 : intval($page);
$start = $page * $pagenum - $pagenum;

$child_ids = get_child_ids($pageid, $ym_idsort); 

$cids = $pageid .($child_ids=='' ? '': ','.$child_ids); //print $pageid;
$count = get_news_count($cids);	//print $cids;
if ($count>0)
{
	$pages = getPages($count, $page, $pagenum);
	$row = get_newslist($cids, $start, $pagenum);
}
else {
    $row='';
}

 
?>