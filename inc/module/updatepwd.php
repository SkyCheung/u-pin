<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*修改密码*/

dbc();

$nav = get_nav(); //导航
$nav_footer = get_nav('bot');
$cats = get_catTree(); //分类树
$help = get_help(); //帮助

check_login();
 
$user = get_user($ym_uid);

if($act && $act =='pay')
{
	$p = "updatepaypwd";
}

?>