<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*分销*/
if($i)
{
	$_SESSION['ditrib_id'] = ucode($i);
	redirect("index.html");
}

require_once "inc/lib/distrib.php";

dbc();

$nav = get_nav(); //导航
$nav_footer = get_nav('bot');
$cats = get_catTree(); //分类树
$help = get_help(); //帮助

check_login();
 
$dt_id = jcode($ym_uid);

if($ym_ditribution_config && $ym_ditribution_config['commission'])
{
	foreach ($ym_ditribution_config['commission'] as $k => $v) {
		$ym_ditribution_config['commission'][$k]['level_1'] = floatval($v['level_1'])*100;
		$ym_ditribution_config['commission'][$k]['level_2'] = floatval($v['level_2'])*100;
		$ym_ditribution_config['commission'][$k]['level_3'] = floatval($v['level_3'])*100;
	}
}	

$page=intval($page)==0 ? 1 : intval($page);
$pagenum=isset($num)? intval($num) : 12; 
$start = $page * $pagenum - $pagenum;
$count = get_subUser_count($ym_uid);
if ($count>0)
{
	$pages = getPages($count, $page, $pagenum);	
}
$row = get_subUser($ym_uid, $start, $pagenum);//我的下级
$level[0] = "";
$level[1] = "一级";
$level[2] = "二级";
$level[3] = "三级";

?>