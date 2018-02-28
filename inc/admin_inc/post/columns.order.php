<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'columns');//权限检测

for ($i=0;$i<count($h_id);$i++){
	//order_sort(intval($listid[$i]),intval($c_order[$i]),trim($type));
	$db->update('columns', array('c_sort' => intval($c_sort[$i])), array('id' => $h_id[$i]));
}

update_config();

message("更新排序完成。",'/admin.html?do=columns');
?>