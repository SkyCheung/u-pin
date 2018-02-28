<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'columns');//权限检测

$sortcon=tree('ym_allsort', 0, "", "");

 
?>