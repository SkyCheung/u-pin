<?php
if (!defined('in_mx')) {exit('Access Denied');}

for ($i=0;$i<count($h_id);$i++){
	//order_sort(intval($listid[$i]),intval($c_order[$i]),trim($type));

	$db->update(''.$type.'_pic', array('c_order' => intval($c_sort[$i])), array('id' => $h_id[$i]));

}


message("更新完成。。。。",$url);
?>