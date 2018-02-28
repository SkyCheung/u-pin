<?php
if (!defined('in_mx')) {exit('Access Denied');}

$id=intval(ucode($id));

checkAuth($login_id, 'columns');//权限检测

$row=$db->fetch('columns','*',' c_pid='.$id,'id asc');

if ($row){message("该栏目下面还有子栏目，不能删除！");}


$db->delete('columns', array('id' => $id));


update_config();
message("处理成功。",$url);

exit();

?>