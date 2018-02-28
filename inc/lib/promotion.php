<?php
if (!defined('in_mx')) {exit('Access Denied');}

function get_discount($where='',$startI, $pagenum, $is_goods=0)
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

//获取活动价格
function get_discount_price($goods_id, $uid=0, $price, $user_discount=0)
{
	global $db;
	static $grade_id; 
	$where ='';
	
	if($grade_id !=null && intval($grade_id)!=0)
	{
		$where .=" and grade_id=".intval($grade_id);
	}
	else {
	 	$where .=" and (grade_ids=0 ".($uid == 0? "":"or find_in_set((select grade_id from ".$db->table('member')." where id=".intval($uid)."), grade_ids)").") ";
	}
	
	$row = $db->query("select type,val from " .$db->table('sp_discount')." WHERE status=1 and start_time<=" .time(). " and end_time>=" .time(). " and find_in_set(".intval($goods_id).", goods_ids) " . $where." order  by addtime desc");
	
	if($row && count($row)>0)
	{
		$val = floatval($row['val']);
		switch ($row['type']){
			case promotion_type_fixed: //统一价
				return $val;
				break;
			case promotion_type_cut: //直减
				return $price>$val ? ($price - $val) : 0;
				break;
			case promotion_type_off: //折扣
				return $price * $val;
				break;
			default:
				break;
		}
	}
	else {
		if($user_discount ==0)//无折扣或未登录  || $uid ==0
		{
			if($uid==0)
			{
				return $price;
			}
			else {
				$user = get_user($uid);
				return $user['discount']==0 ? $price : format_price($user['discount'] * $price);
			}			
		}
		else {
			return $price * $user_discount;
		}
	}
	
	return $price;
}

?>