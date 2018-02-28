<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*修改密码*/

$nav = get_nav(); //导航
$nav_footer = get_nav('bot');
$cats = get_catTree(); //分类树
$help = get_help(); //帮助

if(isset($step) && isset($_SESSION['findpwd_uid'])) 
{
	$p = $p.(intval($step)>0 ? '_step'.$step :'');
	if($step==2)
	{
		dbc();
		$user = get_user($_SESSION['findpwd_uid']);
		$mobile = format_anon($user['mobile'], 3, 3,5);
		unset($user);
	}
}
 
 include template($p, $ym_tpl."/");
 die();

?>