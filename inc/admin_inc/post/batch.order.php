<?php
if (!defined('in_mx')) {exit('Access Denied');}


for ($i=0;$i<count($h_id);$i++){

	$db->update(''.$kktype, array('c_order' => intval($c_order[$i])), array('id' => $h_id[$i]));
	//echo $c_order." , ";
}

//update_config($site_id,$site_mo,$site_url);

message("更新排序完成。",$url);
?>