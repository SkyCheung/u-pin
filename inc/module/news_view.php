<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*文章详情页*/

$id=intval(ucode($p)); 
if($id==0)
{
	exit($exitcon);
}
$nav = get_nav(); //导航
$nav_footer = get_nav('bot');
$cats = get_catTree(); //分类树
$help = get_help(); //帮助
dbc();

$row= get_news($id); 
if (!$row){
	exit($exitcon);
}

$crumbs_nav = get_crumbs_nav('news', $row['c_toid']); //面包导航

//子分类
$sub=getsub($row['c_toid']);
$subname=$sub['title'];
$subtitle=$sub['title'];
$subtxt=$sub['txt'];
$suburl=$sub['url'];
$subcode=$sub['code'];
$subid=$sub['id'];
$sub=$sub['son'];

$code=$ym_idsort[$row["c_toid"]]['code'];
$author=$row['c_author'];
$title =$row['c_title'];
$body = $row['c_body'];
$name = $title;
$time=ftime($row['c_time']);

$p_id = $row['c_toid'];
//左边分类树 
if($p_id==0)
{
	$cat = $ym_sort[$code]; 
}
else {
	$root_cat = get_parents($p_id, $ym_idsort);
	$cat = $ym_sort[$root_cat[0]['code']];
}
$channel_name = $cat['name']; 
$cat = $cat['son'];
$mo = $ym_idsort[$row["c_toid"]]['mo'];
$mo = $mo && $mo!='' ? $mo : 'news';


$prev_news=$db->fetch('news', 'c_code,c_title,c_toid,c_time,id', 'c_time>'.$row['c_time'].' and id<>'.$row['id'].'  and c_toid='.$row["c_toid"],' c_time asc'); //上一条数据
$next_news=$db->fetch('news', 'c_code,c_title,c_toid,c_time,id', 'c_time<'.$row['c_time'].' and id<>'.$row['id'].'  and c_toid='.$row["c_toid"],' c_time desc'); //上一条数据

$ptemp12=$ym_idsort[$prev_news['c_toid']]['dir']==''?'':$ym_idsort[$prev_news['c_toid']]['dir'].'/';
$ntemp12=$ym_idsort[$next_news['c_toid']]['dir']==''?'':$ym_idsort[$next_news['c_toid']]['dir'].'/';
$prev_news['url']='/'.$ptemp12.$ym_idsort[$prev_news['c_toid']]['file'].Condir.$prev_news['c_code'].$ym_htmlext;
$next_news['url']='/'.$ntemp12.$ym_idsort[$next_news['c_toid']]['file'].Condir.$next_news['c_code'].$ym_htmlext;

@include template($mo."_view", $ym_tpl."/");

exit;

?>