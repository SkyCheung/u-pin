<?php
if (!defined('in_mx')) {exit('Access Denied');}

dbc();
//$goods = get_goods($id);

$nav = get_nav(); //导航
$nav_footer = get_nav('bot');
$cats = get_catTree(); //分类树
$help = get_help(); //帮助
//$crumbs_nav = get_crumbs_nav('goods', $goods['cat_id']); //面包导航
//SEO
$ym_title = $goods['name'].' - '.$ym_title;
$ym_keywords = trim($goods['keyword']) !='' ? $goods['keyword'] : $ym_keywords;
$ym_description = trim($goods['description']) !='' ? $goods['description'] : $ym_description;

//$pagetmp="cs";
@include template('cs', $ym_tpl."/");
$markcon = ob_get_contents();
@write_file(cache.$pagetmp."/".$cachefile,$markcon);

?>