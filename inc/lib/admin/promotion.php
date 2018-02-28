<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*优惠活动：限时抢购(秒杀)*/
function add_discount($row)
{
	global $db;
	$db->insert('sp_discount', $row);
}

//更新优惠活动
function update_discount($id, $row)
{
	global $db;
	unset($row['id']);
	return $db->update('sp_discount', $row, array('id'=>intval($id)));
}

//获取某优惠活动明细
function get_discount_info($id)
{
	global $db;
	$row = $db->query("SELECT a.*  FROM ".$db->table('sp_discount')." a where id=".intval($id));
	$row['addtime'] = date('Y-m-d H:i:s', $row['addtime']);
	$row['start_time_format'] = date('Y-m-d H:i:s', $row['start_time']);
	$row['end_time_format'] = date('Y-m-d H:i:s', $row['end_time']);
	
	$goods_arr =explode(",", $row['goods_ids']);
	$row['goods_count'] =  count($goods_arr);
	$row['grade_arr'] = explode(",", $row['grade_ids']);
	return $row;	
}

function get_discount_list($where='',$startI, $pagenum, $is_goods=0)
{
	global $db,$lang_promotion_type;
	$limit = $pagenum==null ?'': "LIMIT ".$startI." , ".$pagenum;
	$row = $db->queryall("SELECT a.*  FROM ".$db->table('sp_discount')." a where 1 ".$where." order by addtime desc ".$limit);	
	foreach ($row as $k => $v) {
		$row[$k]['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
		$row[$k]['start_time_format'] = date('Y-m-d H:i:s', $v['start_time']);
		$row[$k]['end_time_format'] = date('Y-m-d H:i:s', $v['end_time']);
		$row[$k]['type_name'] = $lang_promotion_type[$v['type']];
		$status =get_statusByTime($v['start_time'], $v['end_time']);
		$row[$k]['status_name'] = $status['status_name'];
		
		if($is_goods ==1)
		{
			$goods = get_goods_list('and goods_id in('.$v['goods_ids'].')','goods_id,code,g.name,subname,cat_id,brand_id,g.img,thumb,imgs,number,virtualnum,price,marketprice,g.addtime', '', 0, null);
			$row[$k]['goods'] = $goods;
		}
	}
	return $row;	
}

function get_statusByTime($start_time=0, $end_time=0)
{
	$res =array('status_id'=>0,'status_name'=>'未知状态');
	$now = time();
	if($start_time >$now)
	{
		$res['status_id'] =1;
		$res['status_name'] ='未开始';
	}
	elseif($start_time <=$now && $end_time >=$now)
	{
		$res['status_id'] =2;
		$res['status_name'] ='进行中';
	}
	elseif($start_time >$now)
	{
		$res['status_id'] =3;
		$res['status_name'] ='已结束';		
	}
	
	return $res;
}

/*function get_discount_list($where='',$startI, $pagenum)
{
	global $db,$lang_promotion_type;
	$row = $db->queryall("SELECT a.*  FROM ".$db->table('sp_discount')." a where ".$where." order by addtime desc LIMIT ".$startI." , ".$pagenum);
	 get_goods_list('goods_id in('.$row['goods_ids'].')',$field ='', $order = '', $startI=0, $num=10, $is_up=1, $is_formated=0);
	foreach ($row as $k => $v) {
		$row[$k]['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
		$row[$k]['start_time'] = date('Y-m-d H:i:s', $v['start_time']);
		$row[$k]['end_time'] = date('Y-m-d H:i:s', $v['end_time']);
		$row[$k]['type_name'] = $lang_promotion_type[$v['type']];
	}
	return $row;	
}*/

$lang_promotion_type[promotion_type_fixed] ='统一价';
$lang_promotion_type[promotion_type_cut] ='直减';
$lang_promotion_type[promotion_type_off] ='折扣';


?>