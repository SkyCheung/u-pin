<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'set');//权限检测

for ($i=0;$i<count($h_id);$i++){
	//order_sort(intval($listid[$i]),intval($c_order[$i]),trim($type));
	$db->update('banner_pic', array('c_order' => intval($c_sort[$i])), array('id' => $h_id[$i]));
}

update_diy();
message("更新完成。。。。",'/admin.html?do=banner.upload&type=banner&id='.$id);
?>