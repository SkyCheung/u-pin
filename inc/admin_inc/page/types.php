<?php
if (!defined('in_mx')) {exit('Access Denied');}     
 
  checkAuth($login_id, 'types');//权限检测 
$page=intval($page)==0?1:intval($page);
$pagenum=12; 
$startI = $page*$pagenum-$pagenum;
$count =$db->rowcount('type', '');
$pages = getPages($count,$page, $pagenum);

$xow=$db->queryall("SELECT a . * , GROUP_CONCAT( b.`name` ) attrs FROM ".$db->table('type')." a left JOIN  ".$db->table('attribute')." b ON a.id = b.type_id GROUP BY a.id order by sort asc LIMIT ".$startI." , ".$pagenum);

 foreach($xow as $rs){ 
	$rs['id']=$rs['id'];
	$rs['name']=$rs['name'];
	$rs['title']=$rs['name'];
	$rs['sort']=$rs['sort'];
	$rs['attrs']=$rs['attrs'];
	$row[]=$rs;
 }
 
?>