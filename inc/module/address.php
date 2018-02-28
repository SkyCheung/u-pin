<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*收货地址*/

dbc();

$nav = get_nav(); //导航
$nav_footer = get_nav('bot');
$cats = get_catTree(); //分类树
$help = get_help(); //帮助

check_login();
$consignee = get_consignee(0, $ym_uid); //收货地址 

?>