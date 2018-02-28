<?php
if (!defined('in_mx')){exit('Access Denied');}

checkAuth($login_id, 'set');//权限检测 

if ($ym_htmlext == '/') {
	$cbk_htmlext[1] = ' checked="checked"'; 
} else {
	$cbk_htmlext[0] = ' checked="checked"';
}
require_once './inc/lib/admin/setting.php';
$row = get_shopconfig(); 
foreach ($row as $k => $v) {
	$a = $v['key'];
	$$a = stripslashes($v['value']);
}

?>