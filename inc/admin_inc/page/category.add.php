<?php
if (!defined('in_mx')) {exit('Access Denied');
}

checkAuth($login_id, 'category');//权限检测

$types = get_types();
$cat = array_query('pid', 0, $ym_cats, false);

if ($id) {
	$row = array_query('id', $id, $ym_cats, false);
	$row = $row[0];
	$level = $row['level'];

	$pid = $row['pid']; 
	for ($i = $row['level']; $i >= 1; $i--) {
		$cattmp = 'cat' . $i;
		$parentid = 'pid' . $i; 
		$$parentid = $pid; 
		$$cattmp = array_query('pid', $pid, $ym_cats, false); 
		//$$cattmp['level'] = $i;
		$pidtmp = array_query('id', $pid, $ym_cats, false);
		$pid = $pidtmp[0]['pid']; 
	}
}
else {
	$level=1;
	$id= 0;
} 

$filelist = getfiles(tpldir.$ym_tpl);
$tpls='';
foreach ($filelist as $k => $v) {
	if(strpos($v, "list_")===0 && get_extension($v)=='html')
	{
		$tpls .='<option value="'.get_filename($v).'">' .$v . '</option>';
	}
} 

?>