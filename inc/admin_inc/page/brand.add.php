<?php
if (!defined('in_mx')) {exit('Access Denied');
}

checkAuth($login_id, 'brand');//权限检测 

$types = get_types();
$cat = array_query('pid', 0, $ym_cats, false);
  
?>