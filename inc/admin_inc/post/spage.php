<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'columns');//权限检测

$id=intval(ucode($id));
$filecode=$ym_idsort[$id]['file'];


$row=$db->fetch('columns', 'id', array('id' => $id), 'id asc');
if (!$row){message("Error.");}
$row='';


$c_body=udownload(1,$c_body);

$c_body=str_replace('<img src="/upload','<img src="upload',$c_body);
 


$oimglist=getImgs($c_body);
if ($oimglist){
	foreach($oimglist as $xx){
		$oimg =str_replace($xx,'',$oimg);
	}
}
$oimgx=explode(",",$oimg);
for ($k=0;$k<count($oimgx);$k++){
	if ($oimgx[$k]!=''){
		@del_file(upload_news.$oimgx[$k]);
	}
}

$row=$db->fetch('page', 'id', array('c_toid' => $id), 'id asc');
if ($row){
	//更新
	$db->update('page', array('c_body' => $c_body), array('id' => $row['id']));

}else{
	//增加
	$db->insert('page', array('c_toid' => $id, 'c_body' => $c_body));
}

if(file_exists(cache.$ym_tpl.'/spage') ==false)
{
	mkdir(cache.$ym_tpl.'/spage');
}

html_file(cache.$ym_tpl.'/spage/'.$filecode.'.html',$c_body);

message("提交成功，数据已更新。");
?>