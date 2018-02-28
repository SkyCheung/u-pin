<?php
if (!defined('in_mx')) {exit('Access Denied');}

if (file_exists(cache_data."diy.php")) {
	include(cache_data."diy.php");
}

$nav_header = get_nav('top'); 
$nav = get_nav(); //导航
$cats = get_catTree(); //分类树
$help = get_help(); //帮助
$nav_footer = get_nav('bot'); //底部导航
?>