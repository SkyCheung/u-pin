<?php
if (!defined('in_mx')) {exit('Access Denied');}

$id=intval(ucode(trim($id)));
if ($id==0){message("ERROR.");}

$row=$db->fetch('columns', 'id,c_title,c_pid',  array('id' => $id,'c_id' => $login_id), 'id DESC');
$name=$row["c_title"];

if ($row["c_pid"]!=0){
	$sow=$db->fetch('columns', 'c_type,c_code',  array('id' => $row["c_pid"],'c_id' => $login_id), 'id DESC');
}

$vow=$db->fetchall('columns', '*',  array('c_id' => $login_id, 'c_pid' => $id,), 'id asc', '');
$clist=$vow;

/*
foreach ($vow as $xow) {
   $db->update('columns', array('c_code' => jcode($vow['id'])), array('id' => $vow['id']));
}
*/


?>