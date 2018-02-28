<?php
if (!defined('in_mx')) {exit('Access Denied');}

$id =isset($id)? $id : 0;
/*$pagenum= isset($page) ? 12 :0; 
$page=intval($page)==0 ? 1 : intval($page);
$startI = $page * $pagenum - $pagenum;
*/
$db=dbc();
$brand = get_brand($id);//品牌
$cats = get_catTree(); 

?>