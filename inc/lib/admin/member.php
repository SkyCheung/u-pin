<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*获取会员列表*/
function getMemberList($where='', $startI=0, $pagenum=12)
{
	global $db;		

	if($where !='')
	{
		$where= 'where '.$where;
	}

	return $db->queryall("SELECT m.*,g.grade_name FROM ".$db->table('member')." m left JOIN ".$db->table('member_grade')." g ON m.grade_id = g.grade_id  $where order by m.id desc LIMIT ".$startI." , ".$pagenum); 
}



?>