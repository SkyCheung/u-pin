<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*用户*/

//增加用户
function add_adm_user($username, $pwd, $salt, $coname='', $status=0)
{
	global $db;	
	$db->insert('user',array('username' => trim($username),'pwd' =>$pwd, 'salt' => $salt, 'coname' => trim($coname), 'status' => intval($status),'addtime'=>time()));
	return $db->lastinsertid();
}

//编辑用户
function update_adm_user($data, $id)
{
	global $db;
	return $db->update('user', $data, array('id'=>intval($id)));
}

//删除用户
function delete_adm_user($id)
{
	global $db;
	$db->delete("user", array('id'=>intval($id)));
}

function get_adm_userinfo($id)
{
	global $db;
	return $db->fetch('user', "*", array('id'=>intval($id)));
}

function get_adm_user($where='', $startI=0, $pagenum=null)
{
	global $db;
	$limit ='';
	if($pagenum != null)
	{
		$limit = $startI.','. $pagenum;
	}
	$row = $db->fetchall('user', "id,username,name,coname,lastip,lastlogintime,addtime,updatetime,status", $where,'id', $limit);
	foreach ($row as $k => $v) {
		$row[$k]['addtime'] = date("Y-m-d H:i:s", $v['addtime']);
	}
	return $row;
}

/** 评价
 * @param $type 评论类型:0商品 1文章
 * */ 
function get_comments_count($uid=0, $type=0, $uname='', $start_time='', $end_time='')
{
	global $db;
	$where = '';
	if($uid !=0)
	{
		$where .=" and c.uid=".$uid;
	}
	if($uname!='')
	{
		$where .= " and m.uname ='".$uname."'";
	}
	if($start_time!='')
	{
		$where .= " and c.addtime>=".$start_time;
	}
	if($end_time!='')
	{
		$where .= " and c.addtime<=".$end_time;
	}
	$row = $db->query("select count(*)count from ".$db->table('comment')." c left join ".$db->table('goods')." g on g.goods_id=c.item_id left join ".$db->table('goods_spec')." s on s.values=c.spec left join ".$db->table('member')." m on c.uid=m.id where c.type=".intval($type)."  ".$where);
	return $row['count'];
}

/** 评价
 * @param $type 评论类型:0商品 1文章
 * */ 
function get_comments_list($uid=0, $type=0, $uname='', $start_time='', $end_time='', $start =0, $num =10)
{
	global $db;
	$where = '';
	if($uid !=0)
	{
		$where .=" and c.uid=".$uid;
	}
	if($uname!='')
	{
		$where .= " and m.uname ='".$uname."'";
	}
	if($start_time!='')
	{
		$where .= " and c.addtime>=".$start_time;
	}
	if($end_time!='')
	{
		$where .= " and c.addtime<=".$end_time;
	}
	$row = $db->queryall("select c.*, g.name,m.uname,g.code from ".$db->table('comment')." c left join ".$db->table('goods')." g on g.goods_id=c.item_id left join ".$db->table('goods_spec')." s on s.values=c.spec left join ".$db->table('member')." m on c.uid=m.id where c.type=".intval($type)." ".$where." order by c.id desc limit ".$start.",".$num);
	
	foreach ($row as $k => $v) {
		$row[$k]['url']= get_url($v['code']);
		$row[$k]['img']= json_decode($v['img'],true);
		$row[$k]['thumb']= json_decode($v['thumb'],true);
		$row[$k]['addtime']= date('Y-m-d H:i', $v['addtime']);
	}
	return $row;
}

//删除评价
function del_comment($uid=0, $order_sn='', $item_id=0)
{
	global $db;
	$where ='';
	if($uid !=0)
	{
	 	$where .= " and c.uid=". intval($uid);
	}
	if($order_sn != '')
	{
	 	$where .= " and c.order_sn='". trim($order_sn)."'";
	}
	if($item_id !=0)
	{
	 	$where .= " and c.item_id=". intval($item_id);
	}
	
	$db->query("delete c,cr from ". $db->table('comment'). " c left join ". $db->table('comment_reply'). " cr on c.id=cr.cid where 1 ".$where);
} 

//删除评价
function del_comment_reply($uid=0)
{
	global $db;
	
	$db->query("delete from ". $db->table('comment_reply'). " where uid=". intval($uid)." or reply_uid=". intval($uid));
} 

?>