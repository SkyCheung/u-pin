<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*优惠券*/

/*获取优惠券详情*/
function get_couponinfo($id, $is_item =true)
{
	global $db;
	
	$row = $db->fetch('coupon','*', array('id'=>intval($id)));
	if(!$row)
	{
		return false;
	}
	if($row['days'] == 0)
	{
		$row['date_start'] = date("Y-m-d H:i", $row['date_start']);
		$row['date_end'] = date("Y-m-d H:i", $row['date_end']);
	}
	else {
		$row['date_start'] = '';
		$row['date_end'] = '';
	}
	if($row['get_type'] == coupon_gettype_get)
	{
		$row['limit_start'] = date("Y-m-d H:i", $row['limit_start']);
		$row['limit_end'] = date("Y-m-d H:i", $row['limit_end']);
	}
	else {
		$row['limit_start'] = '';
		$row['limit_end'] = '';
		$row['limit_num'] = '';
		$row['day_num'] = '';
		$row['user_day_num'] = '';
	}
	$row['grade_arr'] = explode(",", $row['grade_ids']);
	
	if($is_item)
	{
		$row['items'] = get_coupon_item($id, $row['type']);
		$data = array();
		foreach ($row['items'] as $k => $v) {
			$data[]= $v['id'];
		}
		$row['items_ids'] =$data;
	}
	
	return $row;
}

/*获取优惠券的品类/商品*/
function get_coupon_item($cid, $type)
{
	global $db;
	
	if($type == coupon_type_cat)
	{
		$row = $db->queryall("select item_id id,ifnull(name,'全品类') name from ".$db->table('coupon_item'). " i left join ".$db->table('category'). " c on i.item_id=c.id where cid=".intval($cid));
	}
	elseif($type == coupon_type_goods) {
		$row = $db->queryall("select item_id id,ifnull(name,'全品类') name from ".$db->table('coupon_item'). " i left join ".$db->table('goods'). " c on i.item_id=c.goods_id where cid=".intval($cid));
	}
	else {
		return array();
	}	

	return $row;
} 
  
/*更新优惠券*/
function update_coupon($data = array(), $id)
{
	global $db;
	return $db->update('coupon', $data, array('id'=>intval($id)));
}

/*更新已领取数量*/
function update_coupon_getNum($id)
{
	global $db;
	return $db->query("update ".$db->table('coupon')." set get_num=get_num+1 where id=".intval($id));
}

/*更新已使用数量*/
function update_coupon_usedNum($num =1, $id)
{
	global $db;
	return $db->query("update ".$db->table('coupon')." set used_num=used_num+".$num." where id=".intval($id));
}

/*更新某优惠券状态*/
function update_coupon_user_status($ids, $status)
{
	global $db;
	if(is_array($ids))
	{
		$ids = implode(",", $ids);
	}
	return $db->query("update ".$db->table('coupon_user')." set status=".intval($status)." where id in(".$ids.")");
}

/**获取优惠券记录数
 * @param int $status_type 状态类型：0不限，1未过期, 2已过期
 * */
function get_coupon_count($where='',$status_type=1)
{
	global $db;
	$where ="status=1 ".$where;		
	if($status_type==1)
	{
		$where .=" and date_end>" . time();	
	}	
	$row = $db->rowcount('coupon',$where);
	
	return $row;
} 
 
/**获取优惠券
 * @param int $status_type 状态类型：0不限，1未过期, 2已过期
 * */
function get_coupon_list($where='',$status_type=1, $startI=0, $pagenum=null, $uid=0)
{
	global $db,$lang_client;
	$limit ='';
	$where ="status=1 ".$where;		
	if($status_type==1)
	{
		$where .=" and (days>0 or date_end>" . time().")";	
	}
	if($pagenum != null)
	{
		$limit = $startI.','. $pagenum;
	}
		
	$row = $db->fetchall('coupon','*', $where,'id desc',$limit);
	$grade = get_grade();
	$ii = 0;
	$today = date("Y.m.d 00:00:00");
	foreach($row as $k => $v) {
		$ii++;
		$row[$k]['i'] = $ii;
		$row[$k]['date_start'] = date("Y.m.d", $v['date_start']);
		$row[$k]['date_end'] = date("Y.m.d", $v['date_end']);
		$row[$k]['limit_start'] = date("Y.m.d", $v['limit_start']);
		$row[$k]['limit_end'] = date("Y.m.d", $v['limit_end']);
		$row[$k]['client_name'] = $lang_client[$v['client']];
		$row[$k]['grade_name'] = get_grade_name($v['grade_ids'], $grade);
		$row[$k]['receive_status'] = $v['get_num'] >= $v['num'] ? 'runout' :''; //已领完
		
		if($row[$k]['receive_status'] =='')
		{
			$receiver_count = get_receiver_count($v['id'], true);
			if($v['day_num'] !=0 && $v['day_num'] <= $receiver_count) 
			{
				$row[$k]['receive_status'] = "todayrunout"; //今日已领完
			}
			elseif($uid !=0) {
				$coupon_user = get_coupon_user($uid, $v['id']);
				if($coupon_user)
				{
					if(count($coupon_user) >= $v['limit_num'])
					{
						$row[$k]['receive_status'] = "geted"; //当前登陆会员已领过
					}
					else {
						$total_count = 0;
						foreach($coupon_user as $key => $val) {
							if(date('Y.m.d 00:00:00', $val['get_time']) == $today)
							{
								$total_count ++;
							}
						}
						if($total_count >= $v['user_day_num'])
						{
							$row[$k]['receive_status'] = "todaygeted"; //当前登陆会员今日已领过
						}
					}
				}
			}
		}
	}
	
	return $row;
} 

/*领取优惠券*/
function add_coupon_user($cid, $uid, $ip='')
{
	global $db;
	
	$res = $db->insert('coupon_user', array('cid'=>$cid, 'uid'=>intval($uid), 'status'=>coupon_unused, 'get_time'=>time(), 'ip'=>$ip));
	if(!$res){
		return false;
	}
	else {
		update_coupon_code($cid, $db->lastinsertid());
	}
}

/*更新某优惠券编号*/
function update_coupon_code($cid, $id=0)
{
	global $db;
	if($id!=0)
	{
		$code = $cid.str_pad($id, 7, '0', STR_PAD_LEFT);
		return $db->update('coupon_user', array('code'=>$code), array('id'=>intval($id)));
	}		
	else {
		return $db->query("update ".$db->table('coupon_user')." set code=CONCAT(".$cid.",LPAD(id, 7, 0)) where code=''");
	}
}

/*将某优惠券更新为过期*/
function update_couponuser_expire()
{
	global $db;
	return $db->query("update ".$db->table('coupon_user')." u,".$db->table('coupon')." c set u.status=".coupon_expire." where u.cid=c.id and date_end <=".time());
}

/*获取优惠券*/
function get_coupon_user($uid = 0, $cid= 0)
{
	global $db;
	$where =array();
	if($uid != 0)
	{
		$where['uid'] = intval($uid);
	}
	if($cid != 0)
	{
		$where['cid'] = intval($cid);
	}
	return $db->fetchall('coupon_user', '*', $where);
} 

/*获取(每天)已领取数量*/
function get_receiver_count($cid, $is_curdate = true)
{
	global $db;
	if($is_curdate)
	{
		$where = " and get_time>=" . strtotime(date("Y-m-d"));
	}
	
	$row = $db->get_count($db->table('coupon_user')." where cid=".intval($cid) . $where);
	return intval($row);
} 

/**获取我的优惠券各类状态汇计
 * */
function get_mycoupon_sum($uid)
{
	global $db;	

	$row = $db->queryall("select count(u.id) count, u.status from ".$db->table('coupon_user')." u join ".$db->table('coupon')." c on u.cid=c.id where uid=".intval($uid)." group by u.status");
	$data[coupon_unused] = 0;
	$data[coupon_used] = 0;
	$data[coupon_expire] = 0;
	$data[coupon_freeze] = 0;
	$data['total'] = 0;
	if($row)
	{
		foreach ($row as $k => $v) {
			$data[$v['status']] = $v['count'];
			$data['total'] += intval($v['count']);
		}
	}
	
	return $data;
} 

/**获取我的优惠券记录数
 * @param int $status 状态：0未使用，1已使用，2已过期，3已冻结
 * */
function get_mycoupon_count($uid,  $status=-1)
{
	global $db;
	$where ="uid=".intval($uid);		
	if($status !=-1)
	{
		$where .=" and u.status=" . intval($status);	
	}	
	$row = $db->get_count($db->table('coupon_user')." u join ".$db->table('coupon')." c on u.cid=c.id where ".$where);
	
	return $row;
} 
 
/**获取我的优惠券
 * @param int $status 状态：0未使用，1已使用，2已过期，3已冻结
 * */
function get_mycoupon($uid,  $status=-1,$startI=0, $num=null)
{
	global $db,$lang_client;
	$limit ='';
	$where ="uid=".intval($uid);		
	if($status !=-1)
	{
		$where .=" and u.status=" . intval($status);	
	}
	if($num != null)
	{
		$limit = "limit ".$startI.','. $num;
	}
		
	$row = $db->queryall("select u.*,c.name,c.amount,c.amount_reached,c.date_start,c.date_end,c.client,c.grade_ids,c.type from ".$db->table('coupon_user')." u join ".$db->table('coupon')." c on u.cid=c.id where ".$where." order by id desc ".$limit);
	$grade = get_grade();
	$ii = 0;
	$today = date("Y.m.d 00:00:00");
	foreach($row as $k => $v) {
		$ii++;
		$row[$k]['i'] = $ii;
		$row[$k]['date_start'] = date("Y.m.d", $v['date_start']);
		$row[$k]['date_end'] = date("Y.m.d", $v['date_end']);
		$row[$k]['client_name'] = $lang_client[$v['client']];
		$row[$k]['grade_name'] = get_grade_name($v['grade_ids'], $grade);
		
		switch ($v['status']) {
			case coupon_unused:
				$row[$k]['status_code'] = 'unused'; //0未使用
				break;
			case coupon_used:
				$row[$k]['status_code'] = 'used'; //1已使用
				break;
			case coupon_expire:
				$row[$k]['status_code'] = 'expire'; //2已过期
				break;	
			case coupon_freeze:
				$row[$k]['status_code'] = 'freeze'; //3已冻结
				break;	
			default:
				$row[$k]['status_code'] = ''; //未知
				break;
		}
	}

	return $row;
} 

/*获取优惠券内的分类或商品id*/
function get_coupon_itemids($cid)
{
	global $db;

	$coupon = get_couponinfo($cid, false);
	if(!$coupon)
	{
		return false;
	}
	$coupon_item = get_coupon_item($cid, $coupon['type']);

	$ids = array();	
	foreach ($coupon_item as $k => $v) {
		$ids[] = $v['id'];
	}
	if(count($ids) ==1 && $ids[0] == 0)
	{
		return '';
	}
	return ($coupon['type'] == coupon_type_cat ? 'cat_id' : 'goods_id') . create_in($ids);	
}

/*根据分类或商品id获取能用的优惠券id*/
function get_couponid_by_itemid($item_id)
{
	global $db;
	if(is_array($item_id))
	{
		$item_id = implode(",", $item_id);
	}
	if(isNumComma($item_id) ==false)
	{
		return false;
	}
	return  $db->queryall("SELECT c.id FROM ".$db->table('coupon')." c JOIN ".$db->table('coupon_item')." i ON c.id = i.cid WHERE item_id in(".trim($item_id).") AND type =".coupon_type_cat." and c.status=1 and (days>0 or date_end>" . time().")"); 
}

/*获取指定商品能用的优惠券*/
function get_couponforgoods($goods_id)
{
	global $db, $ym_cats;

	$cids1 = get_couponid_by_itemid($goods_id);
	
	$goods = get_goods($goods_id);	
	$cats = get_parents($goods['cat_id'], $ym_cats);
	$cat_ids = array();
	if($cats)
	{
		foreach ($cats as $k => $v) {
			array_push($cat_ids, $v['id']); 
		}
	}
	
	$cids2 = get_couponid_by_itemid($cat_ids); 
	$ids = array_merge($cids1, $cids2);
	
	$cids = array();
	foreach ($ids as $k => $v) {
		array_push($cids, $v['id']);
	}
	sort($cids);
	return array_unique($cids);	
}

//获取优惠券是否可用
function set_coupon_enable($coupon = array(), $cart= array(), $ym_client)
{
	global $ym_cats;

	$goods = $cart['goods'];
	$goods_list = array();
	foreach($goods as $k => $v) {
		$goods_info = get_goods($v['goods_id']);	
		$cats = get_parents($goods_info['cat_id'], $ym_cats);
		$cat_ids = array();
		if($cats)
		{
			foreach ($cats as $key => $val) {
				array_push($cat_ids, $val['id']); 
			}
		}
		$v['cat_ids'] = $cat_ids; //祖先分类
		$goods_list[$v['goods_id']]= $v;
	} 

	$discount_amount =0;//优惠金额	
	$cats_used = array();//已选的品类券
	$goods_used = array();//已选的商品券
	
	$flag = array();  
	foreach($coupon as $v){
		$flag[] = $v['amount'];  
	}  			  
	array_multisort($flag, SORT_DESC, $coupon); //金额由大到小排序
	
	foreach($coupon as $k => $v) {
		$coupon[$k]['is_selected'] = 0;
		$coupon[$k]['is_vaild'] = 0;
		$coupon_item = get_coupon_item($v['cid'], $v['type']); 
		$goods_amount = 0;
		if($v['client'] !=0 && $v['client'] !=$ym_client)//客户端不适用
		{
			continue;
		}
		if(count($coupon_item) == 0) //全品类
		{
			if($coupon[$k]['amount_reached'] <= $cart['amount'])
			{
				$discount_amount += $v['amount'];
				$coupon[$k]['is_vaild'] = 1;
				$coupon[$k]['is_selected'] = 1;	
			}
		}
		else{ 
			$ids = array();	
			if($v['type'] == coupon_type_cat) //品类券
			{				
				foreach($coupon_item as $key => $val) {
					foreach ($goods_list as $m => $g) { 
						if(in_array($val['id'], $g['cat_ids']))//商品所属分类符合条件
						{							
							if(!in_array($val['id'], $ids))
							{
								array_push($ids, $val['id']);
							}
							$goods_amount += $g['amount']; 		
						}
					}
				}

				if($coupon[$k]['amount_reached'] <= $goods_amount)
				{
					$discount_amount += $v['amount'];
					$coupon[$k]['is_vaild'] = 1;
					foreach ($ids as $key => $val) {
						if(!isset($cats_used[$val]))//品类券未选中，则将它选中
						{
							$cats_used[$val] = 1;
							$coupon[$k]['is_selected'] = 1;
						}						
					}
				}
			}
			elseif($v['type'] == coupon_type_goods) //商品券
			{
				foreach($coupon_item as $key => $val) {
					if($goods_list[$val['id']])
					{
						if(!in_array($val['id'], $ids))
						{
							array_push($ids, $val['id']);
						}
						$goods_amount += $goods_list[$val['id']]['amount'];
					}
				}
				
				if($coupon[$k]['amount_reached'] <= $goods_amount)
				{
					$discount_amount += $v['amount'];
					$coupon[$k]['is_vaild'] = 1;
					foreach ($ids as $key =>$val) {
						$cat_intersect = array_intersect($goods_list[$val]['cat_ids'], $cats_used);
						if(count($cat_intersect) >0) //已经选中品类券
						{							
							continue;
						}
						foreach ($goods_list[$val]['cat_ids'] as $m => $c) {//加到已选品类券
							array_push($cats_used, $c);
						}
						if(!isset($goods_used[$val]))//商品券未选中，则将它选中
						{
							$goods_used[$val] = 1;
							$coupon[$k]['is_selected'] = 1;
						}
					}
				}
			}
		}
	}
	
	$flag = array();  
	foreach($coupon as $v){  
		$flag[] = $v['is_vaild'];  
	}
	array_multisort($flag, SORT_DESC, $coupon); //可用的排前面
	
	return $coupon;
}

//选择优惠券
function select_coupon($coupon = array(), $cart, $cid)
{
	global $ym_cats;
	$total_amount = $cart['amount']; //商品总金额
	$cats = array();
	$cancel_ids = array();//需取消选择的券
	$cur_coupon = get_couponinfo($cid);
				 
	if(count($cur_coupon['items_ids']) == 0)//全品类券
	{
		foreach($coupon as $k => $v) {
			$coupon_item = get_coupon_item($v['id'], $v['type']); 
			if(count($coupon_item) == 0)
			{
				$cancel_ids[] = strval($v['id']); 
			}
		}	
		return $cancel_ids;
	}
	
	$flag = array();
	$goods = $cart['goods'];
	$goods_list = array();
	foreach($goods as $k => $v) {
		$goods_info = get_goods($v['goods_id']);
		$cats_tmp = get_parents($goods_info['cat_id'], $ym_cats);
		$cat_ids = array();
		if($cats_tmp)
		{
			foreach ($cats_tmp as $key => $val) {
				array_push($cat_ids, $val['id']); 
			}
		}
		
		$flag[] = $v['amount'];
		$v['cat_ids'] = $cat_ids; //祖先分类
		$goods_list[$v['goods_id']]= $v;
	}
	array_multisort($flag, SORT_DESC, $goods_list); //价格高的排前面
	
	$com_goods = array();//所选券适用的商品
	$uncom_goods = array();//所选券不适用的商品
	if($cur_coupon['type'] == coupon_type_cat) //品类券
	{
		$goods_amount = 0;
		$is_enough = false;//商品的累积金额是否已满足条件
		foreach($goods_list as $k => $v) {
			$cat_intersect = array_intersect($v['cat_ids'], $cur_coupon['items_ids']); //交集			
			if(count($cat_intersect) >0 && $is_enough == false)
			{
				$goods_amount += $v['amount'];
				$com_goods[$v['goods_id']] = $v;
				if($cur_coupon['amount_reached'] <= $goods_amount)
				{
					$is_enough = true;
				}
			}
			else {
				$uncom_goods[$v['goods_id']] = $v;
			}
		}
	}
	elseif($cur_coupon['type'] == coupon_type_goods) //商品券
	{
		$goods_amount = 0;
		$is_enough = false;//商品的累积金额是否已满足条件
		foreach($goods_list as $k => $v) {
			$goods_intersect = array_intersect($v['goods_ids'], $cur_coupon['items_ids']); //交集			
			if(count($goods_intersect) >0 && $is_enough == false)
			{
				$goods_amount += $v['amount'];
				$com_goods[$v['goods_id']] = $v;
				if($cur_coupon['amount_reached'] <= $goods_amount)
				{
					$is_enough = true;
				}
			} 
			else {
				$uncom_goods[$v['goods_id']] = $v;
			}
		}
	}

	foreach($coupon as $k => $v) {
		$coupon_item = get_coupon_item($v['id'], $v['type']); 
		$goods_amount = 0;
		if($v['type'] == coupon_type_cat) //品类券
		{
		   $is_match = false;
		   foreach ($coupon_item as $key => $val) {
		   	  foreach ($com_goods as $m=>$g) { 
		   	  	if(in_array($val['id'], $g['cat_ids']))
				{
					$is_match = true;
					break;
				}
		   	  }
			  if($is_match)
			  {
			  	break;
			  }
		   }
		   
			if($is_match)
			{
			   foreach ($coupon_item as $key => $val) {
			   	  foreach ($uncom_goods as $m=> $g) {
			   	  	if(in_array($val['id'], $g['cat_ids']))
					{
						$goods_amount += $g['amount'];
						unset($uncom_goods[$m]);
						break;
					}
			   	  }				  
			   }
			   
			   if($goods_amount ==0 || $cur_coupon['amount_reached'] > $goods_amount)//未满足
			   {		
					$cancel_ids[] =strval($v['id']);
			   }
			}
		}
		elseif($v['type'] == coupon_type_goods) //商品券
		{
		   $is_match = false;
		   foreach ($coupon_item as $key => $val) {
		   	  if(isset($com_goods[$val['id']]))
				{
					$is_match = true;
					break;
				}
		   }
		   
			if($is_match)
			{
			   foreach ($coupon_item as $key => $val) {
			   	  	if(isset($uncom_goods[$val['id']]))
					{
						$goods_amount += $g['amount'];
						unset($uncom_goods[$m]);
						break;
					}
			   }
			   if($cur_coupon['amount_reached'] > $goods_amount)//未满足
			   {
					$cancel_ids[] =strval($v['id']);
			   }
			}
		}
	}
	
	return $cancel_ids;
}

//获取会员的优惠券的金额
function get_coupon_amount($coupon_ids, $uid, $cart)
{
	global $db, $ym_cats, $lang_client;
	$res = array("res" =>'', "err" =>'');	

	$coupon = $db->queryall("select c.amount,c.name,c.amount_reached,c.date_start,c.date_end,c.type,u.* from ". $db->table('coupon')." c join ". $db->table('coupon_user')." u on c.id=u.cid where u.id in(".$coupon_ids.") and uid=".intval($uid));
	 
	$flag = array();  
	foreach($coupon as $v){  
		$flag[] = $v['amount'];  
	}  			  
	array_multisort($flag, SORT_DESC, $coupon); //金额由大到小排序
								
	$flag = array();
	$goods = $cart['goods'];
	$goods_list = array();
	foreach($goods as $k => $v) {
		$goods_info = get_goods($v['goods_id']);
		$cats_tmp = get_parents($goods_info['cat_id'], $ym_cats);
		$cat_ids = array();
		if($cats_tmp)
		{
			foreach ($cats_tmp as $key => $val) {
				array_push($cat_ids, $val['id']); 
			}
		}
		
		$flag[] = $v['amount'];
		$goods[$k]['cat_ids'] = $cat_ids; //祖先分类
	}
	array_multisort($flag, SORT_DESC, $goods); //价格高的排前面
	foreach($goods as $k => $v) {
		$goods_list[$v['goods_id']]= $v; 
		unset($goods_list[$k]);
	}

	$total_amount = $cart['amount']; //商品总金额
	$amount =0;
	$is_allcoupon = 0; //全品类券数量
	$cats = array();
	foreach($coupon as $k => $v) {
		if($v['client'] !=0 && $v['client'] != $ym_client)//客户端不适用
		{
			$res['err'] = '该券'.$v['name'].' 为'.$lang_client[$v['client']].'端专用';
			return $res;
		}
		
		$coupon_item = get_coupon_item($v['cid'], $v['type']); 
		$goods_amount = 0;
		$is_use =false;
		if(count($coupon_item) == 0)//全品类券
		{
			if($is_allcoupon)
			{
				$res['err'] = '一个订单只能用一张全品类券';
				return $res;
			}
			$amount += $v['amount'];
			$is_allcoupon =1;
			$is_use =true;
		}
		elseif($v['type'] == coupon_type_cat) //品类券
		{
			   foreach ($coupon_item as $key => $val) {
			   	  foreach ($goods_list as $m=> $g) {
			   	  	if(in_array($val['id'], $g['cat_ids']))
					{
						$goods_amount += $g['amount'];
						unset($goods_list[$m]);
						if($goods_amount >= $v['amount_reached'])
					   {
					   		$amount += $v['amount'];
							$is_use =true;
							break;
					   }
					}
			   	  }		
				  if($is_use)
				  {
				  	break;
				  }	  
			   }
		}
		elseif($v['type'] == coupon_type_goods) //商品券
		{  
			   foreach ($coupon_item as $key => $val) {
			   	  	if(isset($goods_list[$val['id']]))
					{
						$goods_amount += $goods_list[$val['id']]['amount'];
						unset($goods_list[$val['id']]);
						if($goods_amount >= $v['amount_reached'])
					    {
					   		$amount += $v['amount'];
							$is_use =true;
							break;
					    }
					}
			   }
		}				
	}
	
	$res['res'] = $amount;
	return $res; 
}

?>