<?php
if (!defined('in_mx')) {exit('Access Denied');}


function get_app_list($type=0, $startI=0, $pagenum=10)
{
	global $db;
	$where =array();
	if($type !=0)
	{
		$where['type'] = intval($type);
	}
	$row = $db->fetchall('app', '*', $where,'addtime desc',$startI .",". $pagenum);
	foreach ($row as $k => $v) {
		$row[$k]['addtime']= date("Y-m-d H:i", $v['addtime']);
	}
	return $row;
}

function get_cron_list($status=-1, $nexttime=0)
{
	global $db;
	$where ='1';
	if($status != -1)
	{
		$where .=" and status =". intval($status);
	}
	if($nexttime !=0)
	{
		$where .=" and nexttime<=". $nexttime;
	}
	$row = $db->fetchall('cron', '*', $where,'addtime desc');
	foreach ($row as $k => $v) {
		$row[$k]['addtime_format']= date("Y-m-d H:i", $v['addtime']);
		$row[$k]['lasttime_format']= $v['lasttime']==0?'': date("Y-m-d H:i:s", $v['lasttime']);
		$row[$k]['nexttime_format']= $v['nexttime']==0?'': date("Y-m-d H:i:s", $v['nexttime']);
	}
	return $row;
}

function update_cron($id, $row = array())
{
	global $db;
	return $db -> update('cron', $row, array('id'=>intval($id)));
	
}

/*添加登陆日志*/
function add_login_log($uid, $role_type, $status)
{
	global $db;
	return $db->insert('login_log', array('uid'=>intval($uid),'role_type'=>intval($role_type),'status'=>intval($status), 'ip'=>getip(),'lasttime'=>time()));
}

/*获取指定时间内的登陆失败次数, 默认一小时*/
function get_login_count($uid, $role_type, $time = 3600)
{
	global $db;
	return  $db->rowcount('login_log', "uid=".intval($uid)." and role_type=".intval($role_type)." and status=".login_fail." and lasttime>".(time()-$time) );
}

/*获取登陆日志*/
function get_login_log($uid=0, $log_status=0, $role_type=0, $ip='', $grade_ids='', $sex=0, $min_time=0, $max_time=0, $start=0, $num=null)
{
	global $db;
	$where = '';
	if($uid !=0)
	{
		$where .=" and uid=".intval($uid);
	}
	if($log_status !=0)
	{
		$where .=" and a.status=".intval($log_status);
	}
	if($role_type !=0)
	{
		$where .=" and role_type=".intval($role_type);
	}
	if($ip !='')
	{
		$where .=" and ip='".$ip."'";
	}
	if($grade_ids !='')
	{
		$where .=" and grade_id in(".trim($grade_ids).") ";
	}
	if($sex !=0)
	{
		$where .=" and sex=".intval($sex);
	}
	if($min_time !=0)
	{
		$where .=" and lasttime>=".trim($min_time);
	}
	if($max_time !=0)
	{
		$where .=" and lasttime<".trim($max_time);
	}	
	if($num !=null)
	{
		$where .=" limit ".$start.",".$num;
	}
	
	return  $db->queryall("select a.*, b.uname,b.status u_status from ".$db->table("login_log")." a join ".$db->table("member")." b on a.uid=b.id where 1=1 ".$where);
}



?>