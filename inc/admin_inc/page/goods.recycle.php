<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'goods');//权限检测
 
$page=intval($page)==0?1:intval($page);
$pagenum =12; 
$startI = $page * $pagenum-$pagenum;
$count =$db->rowcount('goods', 'status='.goods_del);
$pages=getPages($count, $page, $pagenum);
$row= get_goodsList('g.status='.goods_del,$startI,$pagenum);			

 
?>