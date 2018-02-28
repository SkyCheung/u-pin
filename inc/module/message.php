<?php
if (!defined('in_mx')) {exit('Access Denied');}

$nav = get_nav(); //导航
$nav_footer = get_nav('bot');
$cats = get_catTree(); //分类树
$help = get_help(); //帮助


?>