<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'set');//权限检测 
//$row=$db->fetch('site', '*', array('id' => $site_id,'c_id' => $login_id), 'id asc');

$product_w=intval($product_w)==0?200:intval($product_w);
$product_h=intval($product_h)==0?150:intval($product_h);
$product_l=intval($product_l)==0?12:intval($product_l);

$photo_w=intval($photo_w)==0?200:intval($photo_w);
$photo_h=intval($photo_h)==0?150:intval($photo_h);
$photo_l=intval($photo_l)==0?12:intval($photo_l);

$news_w=intval($news_w)==0?200:intval($news_w);
$news_h=intval($news_h)==0?150:intval($news_h);
$news_l=intval($news_l)==0?18:intval($news_l);

?>