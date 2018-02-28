<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'goods.add');//权限检测

require './inc/lib/admin/member.php';

//模板
$tplpath = upload_tpldetails; 
$tplrow=getfiles($tplpath);
foreach ($tplrow as $k => $v) {
	$tplrow[$k]=urldecode(substr($v, 0,strrpos($v, '.'))) ;
}

$types = get_types();//商品类型
$member_grade = get_grade(); //会员等级

//品牌
$brand =$db->fetchall('brand','*','',"sort asc");
$jsonbrand = json_encode($brand);

$cat = array_query('pid', 0, $ym_cats, false); 

if ($id) {
	$row = array_query('id', $id, $ym_cats, false);
	$row = $row[0];

	$pid = $row['pid']; 
	for ($i = $row['level']; $i >= 1; $i--) {
		$cattmp = 'cat' . $i;
		$parentid = 'pid' . $i; 
		$$parentid = $pid; 
		$$cattmp = array_query('pid', $pid, $ym_cats, false);
		$$cattmp['level'] = $i;
		$pidtmp = array_query('id', $pid, $ym_cats, false);
		$pid = $pidtmp[0]['pid']; 
	}
}


?>