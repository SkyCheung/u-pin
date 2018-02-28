<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*晒单列表页*/

if (file_exists(cache_data."diy.php")) {
	include(cache_data."diy.php");
}

$db=dbc();
$page=intval($page)==0 ? 1 : intval($page);
$pagenum = 20;
$startI = $page * $pagenum - $pagenum;
//$startI = 0;

$comment = get_all_comment_list('', 0, $startI, $pagenum, $uid);	
//var_dump($comment);exit;	
if($is_ajax==1){
	$res = array('err' => '', 'res' => '', 'data' => array());
	$res['count'] = $count;
	$res['data'] = $comment;
	
	die(json_encode_yec($res));
}
?>
