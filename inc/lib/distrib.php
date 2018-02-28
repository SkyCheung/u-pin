<?php
if (!defined('in_mx')) {exit('Access Denied');}
/*分销*/

//获取父级uid
function get_parent_uid($uid)
{
	global $db;
	$row = $db->query("select pid1,pid2,pid3,concat_ws(',',pid1,pid2,pid3) pids from ".$db->table('member')." where id=".intval($uid));
	
	return $row && count($row)>0 ? $row : array('pid1'=>0,'pid2'=>0,'pid3'=>0, 'pids'=>'');	
}

//获取父级比例
function get_parent_commiss($uids)
{
	global $db;
	$row = $db->queryall("select id,commiss_id from ".$db->table('member')." where id in(".$uids.")");
	
	return $row;	
}

//获取某会员的下级
function get_subUser_count($uid)
{
	global $db;
	$uid = intval($uid);
	$row = $db->get_count($db->table('member')." where pid1=".$uid." or pid2=".$uid." or pid3=".$uid);
	
	return $row;	
}

//获取某会员的下级
function get_subUser($uid, $startI=0, $num=12)
{
	global $db;
	$uid = intval($uid);
	$row = $db->queryall("select id,uname,commiss_id,case when pid1=".$uid." then 1 when pid2=".$uid." then 2 when pid3=".$uid." then 3 else 0 end level from ".$db->table('member')." where pid1=".$uid." or pid2=".$uid." or pid3=".$uid." order by level limit ".$startI.",".$num);
	
	return $row;	
}

//支付佣金
function pay_commission($uid, $name='', $order_amount)
{
	global $db, $ym_ditribution_config; 
	$commission = $ym_ditribution_config['commission']; //获取所有级别佣金
	
	$p_user = get_parent_uid($uid); //获取所有上级	
	$row = get_parent_commiss($p_user['pids']); 

  	unset($p_user["pids"]);
  	$user = array_flip($p_user);
	unset($user[0]);
	 
  	foreach ($user as $k => $v) {
  		$user[$k] = ltrim($v, "pid");
  	}
  
	foreach ($row as $k => $v) {
		$amount = 0; 
		if($v['commiss_id'] !=0)
		{
			$commiss = $commission[$v['commiss_id']]["level_".$user[$v['id']]];
			if($commiss != 0)
			{
				$amount = $order_amount * $commiss;
				update_account($v['id'], $amount, 0); 
				add_member_log($v['id'], asset_balance, $amount, '分销获取佣金。'.($name!=''? "会员：".$name :''));
			}			
		}		
		
		if($ym_ditribution_config['distrib_level'] == ($k+1)) //开启分销级数
		{
			break;
		}	
	}
}

function get_distribution_count($where = '')
{
	global $db;
	return $db->get_count("(select  id,uname,commiss_id,sum(level1) level1, sum(level2) level2, sum(level3) level3 from (
	SELECT m.id,m.uname,m.commiss_id,count(a.id) level1, 0 level2, 0 level3 FROM ".$db->table('member')." m left join ".$db->table('member')." a on m.id=a.pid1 WHERE m.commiss_id>0 and m.status=1 $where group by m.id
	union all
	SELECT m.id,m.uname,m.commiss_id,0 level1, count(a.id) level2, 0 level3 FROM ".$db->table('member')." m left join ".$db->table('member')." a on m.id=a.pid2 WHERE m.commiss_id>0 and m.status=1 $where group by m.id
	union all
	SELECT m.id,m.uname,m.commiss_id,0 level1, 0 level2, count(a.id) level3 FROM ".$db->table('member')." m left join ".$db->table('member')." a on m.id=a.pid3 WHERE m.commiss_id>0 and m.status=1 $where group by m.id
	) t group by id) t");
	
	
}

function get_distribution($where = '', $startI=0, $num =10)
{
	global $db;
	return $db->queryall("select  id,uname,commiss_id,sum(level1) level1, sum(level2) level2, sum(level3) level3 from (
	SELECT m.id,m.uname,m.commiss_id,count(a.id) level1, 0 level2, 0 level3 FROM ".$db->table('member')." m left join ".$db->table('member')." a on m.id=a.pid1 WHERE m.commiss_id>0 and m.status=1 $where group by m.id
	union all
	SELECT m.id,m.uname,m.commiss_id,0 level1, count(a.id) level2, 0 level3 FROM ".$db->table('member')." m left join ".$db->table('member')." a on m.id=a.pid2 WHERE m.commiss_id>0 and m.status=1 $where group by m.id
	union all
	SELECT m.id,m.uname,m.commiss_id,0 level1, 0 level2, count(a.id) level3 FROM ".$db->table('member')." m left join ".$db->table('member')." a on m.id=a.pid3 WHERE m.commiss_id>0 and m.status=1 $where group by m.id
	) t group by id limit ".$startI.",".$num);
	
	
}

?>