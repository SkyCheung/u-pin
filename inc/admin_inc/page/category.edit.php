<?php
/*商品分类编辑*/
if (!defined('in_mx')) {exit('Access Denied');}

if (trim($id)==""|| intval($id)==0){message("获取不到分类id。");}

checkAuth($login_id, 'category');//权限检测

$types = get_types();
$row = array_query('id',$id, $ym_cats); 
$row = $row[0];
$row['grade']= str_replace(',', PHP_EOL, trim($row['grade']));
 
$pid=$row['pid']; 
for($i=$row['level']; $i >= 1; $i--) { 
	$cattmp= 'cat'. $i;
	$parentid='pid'. $i;
	$$parentid=$pid;
	$$cattmp= array_query('pid', $pid, $ym_cats); 
	//$$cattmp['level'] = $i;
	$pidtmp= array_query('id', $pid, $ym_cats); 
	$pid = $pidtmp[0]['pid']; 
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