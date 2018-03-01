<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*获取购物车数量*/
function get_cart_amount($is_new = 0)
{	
	if(!isset($_COOKIE['cnum']) || $is_new ==1)
	{
		$db = dbc();
		global $ym_uid;
		$id = !isset($_COOKIE['ckey'])? 0: intval($_COOKIE['ckey']);
		$where='';
		if($ym_uid!=0)
		{
			$where .= " and uid=" .$ym_uid;
		}
		elseif($id!=0)
		{
			$where .= " and a.id=" .$id;
		}
		else {
			return 0;
		}	
		$row = $db->query("select sum(b.num) cnum from ".$db->table('cart')." a left join ".$db->table('cart_item')." b on a.id=b.cid where 1 " . $where);
		set_cookie('cnum',  intval($row['cnum']), time() + 15552000); 
		return intval($row['cnum']);
	}
	else {
		return intval($_COOKIE["cnum"]);
	}
}

//获取收货地址
function get_consignee($id=0 , $uid=0, $is_default=-1)
{
	global $db;
	$where ='';
	if($id !=0)
	{
		$where .=' and m.id='.$id;
	}
	if($is_default != -1)
	{
		$where .=' and is_default='.$is_default;
	}
  
	return $db->queryall("SELECT m.*,concat(ifnull(p.name,''),ifnull(c.name,''),ifnull(a.name,''),ifnull(d.name,''),address) addr,p.name province_name,c.name city_name,a.name area_name,d.name town_name FROM ".$db->table('member_address')." m left join ".$db->table('district')." p on m.province=p.id left join ".$db->table('district')."  c on m.city=c.id left join ".$db->table('district')." a on m.area=a.id left join ".$db->table('district')."  d on m.town=d.id where uid=".$uid .$where);
}

//删除收货地址
function del_consignee($id=0, $uid=0)
{
	global $db;
	$where ='';
	if($id !=0)
	{
		$where =' and id='.$id;
	}
	$db->delete('member_address', 'uid='. $uid. $where);
}

//计算运费
function get_express_fee($uid=0, $cart=array(), $express_id =0, $city_id=0)
{
	global $ym_express_type, $ym_uid;
	if($uid==0){ $uid=$ym_uid; };
 
	$fee = 0.00;
	if($ym_express_type==1)
	{
		if(count($cart) == 0)
		{
			$cart = get_cart(1);
		}
		$amount= $cart['amount'];
		$express = get_cache("express");
		if($express && count($express)>0) //优先按订单金额范围计算运费
		{
			$arr = array(); 
			foreach ($express as $k => $v) {
				$grade_ids= explode(',', $v['grade_id']);
				if((count($grade_ids)==0 || $grade_ids[0]==0 || in_array($grade_id, $grade_ids)) && $amount >= $v['money_reached'])
				{
					$arr[] = $v['express_fee'];								
				}
			}
			if(count($arr)>0)
			{
				sort($arr);
				$fee = $arr[0];
			}
		}
		else //区域计费方式
		{
			$ym_country = get_cache("country", cache_static); 
			$express_district = get_cache("express_district");
			$tmp_dist = array_query('express_id', $express_id, $express_district); 
			$dist =array();
			 
			foreach ($tmp_dist as $k => $v) {
				$dist_ids = explode(',', $v['district_id']);
				if(in_array($city_id, $dist_ids)) //优先具体区域
				{
					$dist = $v;
					break;
				}
				elseif (in_array($dist_ids[0], $ym_country)) //是否全国
				{
					$dist = $v;
				}
			}
			if(count($dist)> 0)
			{
				$weight = $cart['weight'];
				$fee = $dist['first_price'] + ($weight <= $dist['first_weight'] ? 0 : (ceil($weight - $dist['first_weight'])/$dist['added_weight']) * $dist['added_price']); // 首费 + (物重 -首重)/续重 * 续费
			}
			else {
				$fee=0;
			}
		}
		
		$fee =format_price($fee,2);
	}	
	
 	return $fee;
}

function get_max_express_fee()
{
	$express = get_cache("express");

	$tmp = 0; 
	foreach ($express as $k => $v) {
		if($v['express_fee']==0 && $v['money_reached'] >$tmp)
		{
			$tmp = $v['money_reached'];
		}
	}
 	return format_price($tmp,2);
}

//配送方式
function get_express($grade_id= 0, $express_type=0)
{
	global $db;
	$where = '1';
	if($grade_id != 0)
	{
		$where .= " and find_in_set('". $grade_id. "', a.`attr_ids`)";
	}
	if($express_type !=0 )
	{
		$where .= ' and express_type='. $express_type;
	}
	
	return $db->fetchall('express', '*', $where, 'id asc');	
}

//配送方式
function get_express_common($id= 0, $code='', $status=1)
{
	global $db;
	$where = array();
	if($id != 0)
	{
		$where['id']=$id;
	}
	if($code != '' )
	{
		$where['code']= $code;
	}
	if($status != -1)
	{
		$where['status']= $status;
	}
	
	return $db->fetchall('express_common', '*', $where, 'sort asc, id desc');	
}

function get_express_district($id = 0, $status=-1)
{
	global $db;
	$where = '';
	if($id != 0)
	{
		$where .=" and express_id=".$id;
	}
	if($status != -1)
	{
		$where .= " and status=".$status;
	}
	return $db->queryall("select b.code,b.insure,a.* from ".$db->table('express_district')." a join ".$db->table('express_common')." b on a.express_id=b.id where 1 ".$where);
}

//自提点
function get_express_picksite($province= '', $city='', $district ='')
{
	global $db;
	$where = array();
	if($province != '')
	{
		$where['province']=$province;
	}
	if($city != '' )
	{
		$where['city']= $city;
	}
	if($district != '' )
	{
		$where['district']= $district;
	}
	
	return $db->fetchall('express_picksite', '*', $where);	
}
	
function get_district($where ='')
{
	global $db;
	return $db -> fetchall('district', '*', $where, ' level asc,sort asc');
}

//生成订单编号
function build_order_sn($is_fullyear=0)
{
	return date($is_fullyear==0 ?'ymd' :'Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

//批量获取订单状态
function get_order_status_patch($ids)
{
	global $db;
	return $db->fetchall("order","status,order_sn","order_sn in(".$ids.")");
}

//订单基本信息
function get_order_info($id=0, $order_sn='', $uid =0)
{
	global $db;
	$where = '';
	if($id !=0)
	{
		$where .=' and id='.intval($id);
	}
	if($order_sn != '')
	{
		$where .=" and order_sn='" .$order_sn."'";
	}
	if($uid !=0)
	{
		$where .=' and uid='.intval($uid);
	}
	$order = $db->query("select o.*, m.uname from ".$db->table('order')." o left join ".$db->table('member')." m on o.uid=m.id where 1 ".$where);
	
	$order['status_name'] = order_status($v['status']);	
	$order['add_time'] = date('Y-m-d H:i:s', $order['add_time']);
	$order['deliver_time'] = $order['deliver_time']==0 ?'': date('Y-m-d H:i:s', $order['deliver_time']);
	$order['receiving_time'] = $order['receiving_time']==0 ?'':  date('Y-m-d H:i:s', $order['receiving_time']);
	
	return $order;
}

//订单信息，包含商品
function get_order($id=0, $order_sn='', $uid =0)
{
	global $db;
	$where = array();
	if($id !=0)
	{
		$where['id'] =$id;
	}
	if($order_sn != '')
	{
		$where['order_sn'] = $order_sn;
	}
	if($uid !=0)
	{
		$where['uid'] =$uid;
	}
	$order = $db->fetch('order','*', $where);
	$goods = $db->fetchall('order_goods','*', array("order_id"=>$order['id']));
	foreach ($goods as $k => $v) {
		$v['url'] = $v['code'].'-g.html';
	}
	$order['goods'] = $goods;
	$order['status_name'] = order_status($v['status']);	
	$order['add_time'] = date('Y-m-d H:i:s', $order['add_time']);
	$order['deliver_time'] =$order['deliver_time']==0 ?'': date('Y-m-d H:i:s', $order['deliver_time']);
	$order['receiving_time'] =$order['receiving_time']==0 ?'':  date('Y-m-d H:i:s', $order['receiving_time']);
	
	return $order;
}

//订单商品
function get_order_goods($order_sn='', $uid =0,$gid=0,$spec='')
{
	global $db;
	$where = '';
	if($id !=0)
	{
		$where .=' and o.id='.$id;
	}
	if($gid != 0)
	{
		$where .=" and og.goods_id=".$gid;
	}
	if($spec != '')
	{
		$where .=" and og.spec='".$spec."'";
	}
	if($uid !=0)
	{
		$where .= ' and o.uid='.$uid;
	}

	$goods = $db->queryall("select g.thumb,g.code,ifnull(s.`values`,'') spec_name,og.*,o.amount as order_amount,o.add_time as order_time from ".$db->table('order')." o join ".$db->table('order_goods')." og on o.id=og.order_id left join ".$db->table('goods')." g on g.goods_id=og.goods_id left join ".$db->table('goods_spec')." s on og.spec=s.values and og.goods_id=s.goods_id where  o.order_sn=".$order_sn.$where);	
	if($goods && count($goods)>0)
	{
		foreach ($goods as $k => $v) {
			$goods[$k]['price'] = format_price($v['price']);
			$goods[$k]['amount'] = format_price($v['price'] * $v['num']);
			$goods[$k]['url'] = $v['code'].'-g.html';
		}
	}
	return $goods;
}

//订单详情
function get_order_details($id=0, $order_sn='', $uid =0)
{
	global $db;
	$where = '1';
	if($id !=0)
	{
		$where .=' and o.id='.$id;
	}
	if($order_sn != '')
	{
		$where .=" and order_sn='".$order_sn."'";
	}
	if($uid !=0)
	{
		$where .= ' and uid='.$uid;
	}

	$order = $db->query("select o.*,ifnull(e.name,'商家配送') exp_name,t.pay_name,concat(cnee_dist_name,cnee_address) cnee_addr,m.uname from ".$db->table('order')." o left join ".$db->table('payment')." t on t.pay_code=o.pay_code left join ".$db->table('express_common')." e on e.id=o.exp_id left join ".$db->table('member')." m on m.id=o.uid where ".$where);
	if(!$order || count($order)==0)
	{
		return $order;
	}

	$goods = $db->queryall("select g.thumb,g.code,ifnull(s.attr_ids,'') spec_ids,ifnull(s.`values`,'') spec_name,ifnull(de.exp_id,'') exp_id,ifnull(de.exp_no,'') exp_no,ifnull(de.exp_time,'') exp_time,og.* from ".$db->table('order_goods')." og left join ".$db->table('goods')." g on g.goods_id=og.goods_id left join ".$db->table('goods_spec')." s on og.spec=s.values and og.goods_id=s.goods_id left join  (select exp_id,exp_no,exp_time,goods_id,spec from ".$db->table('delivery_goods')." dg join ".$db->table('delivery')." d on d.id=dg.delivery_id where order_sn=".$order['order_sn']." ) de on de.goods_id=og.goods_id and de.spec=og.spec where order_id=".$order['id']);
	
	foreach ($goods as $k => $v) {
		$goods[$k]['price'] = format_price($v['price']);
		$goods[$k]['amount'] = format_price($v['price'] * $v['num']);
		$goods[$k]['url'] = $v['code'].'-g.html';
		$goods[$k]['order_log'] = get_order_log($order['order_sn'], $v['exp_no']); 
		$goods[$k]['spec'] = '';
		if($v['spec_ids'] !='')
		{
			$goods[$k]['spec'] = get_spec_val($v['spec_ids'], $v['spec_name']);
		}
	}
	$order['goods'] = $goods;
	$order['amount'] = format_price($order['amount']);
	$order['goods_amount'] = format_price($order['goods_amount']);	
	$order['exp_amount'] = format_price($order['exp_amount']);	
	$order['balance_amount'] = format_price($order['balance_amount']);
	$order['point_amount'] = format_price($order['point_amount']);
	$order['coupon_amount'] = format_price($order['coupon_amount']);
	$order['status_name'] = order_status($order['status']);	
	$order['remain_time'] = time_diff($order['add_time'], time()-86400);
	$order['add_time'] = date('Y-m-d H:i:s', $order['add_time']);
	$order['pay_time'] =$order['pay_time']==0 ?'': date('Y-m-d H:i:s', $order['pay_time']);
	$order['deliver_time'] =$order['deliver_time']==0 ?'': date('Y-m-d H:i:s', $order['deliver_time']);
	$order['receiving_time'] =$order['receiving_time']==0 ?'':  date('Y-m-d H:i:s', $order['receiving_time']);
		
	return $order;
}

function get_order_count($id=0, $order_sn='', $uid =0,$where)
{
	global $db;
	
	if($id !=0)
	{
		$where .=" and o.id=". intval($id);
	}
	if($order_sn != '')
	{
		$where .=" and order_sn='". $order_sn."'";
	}
	if($uid !=0)
	{
		$where .=" and o.uid=". intval($uid);
	}
	
	$row = $db->query("select count(distinct o.id) count from ".$db->table('order')." o join ".$db->table('order_goods')." og on o.id=og.order_id left join ".$db->table('goods')." g on g.goods_id=og.goods_id left join ".$db->table('member')." m on o.uid=m.id where 1 ".$where);
 
	return intval($row['count']);
}	

//订单列表
function get_order_list($id=0, $order_sn='', $uid =0,$where='',$start=0, $num =10)
{
	global $db;
	
	if($id !=0)
	{
		$where .=" and o.id=". intval($id);
	}
	if($order_sn != '')
	{
		$where .=" and order_sn='". $order_sn."'";
	}
	if($uid !=0)
	{
		$where .=" and o.uid=". intval($uid);
	}	
	
	$row = $db->queryall("select o.id oid,o.order_sn,o.amount,o.add_time,o.status,o.is_comment,o.pay_status,o.deliver_status,o.pay_code,o.cnee_name,concat(o.cnee_dist_name,o.cnee_address) address,o.cnee_mobile,g.code,g.thumb,g.sn,og.*,ifnull(s.attr_ids,'') as spec_ids,ifnull(s.`values`,'')as spec_name from ".$db->table('order')." o join ".$db->table('order_goods')." og on o.id=og.order_id left join ".$db->table('goods')." g on g.goods_id=og.goods_id left join ".$db->table('goods_spec'). " s on og.goods_id=s.goods_id and s.values =og.spec where 1 ".$where."  order by add_time desc limit ".intval($start).",".intval($num));
	
	$order = array();
	if($row)
	{
		foreach ($row as $k => $v) {
			if(count($order[$v['oid']]) == 0)
			{
				$order[$v['oid']]['oid'] = $v['oid'];
				$order[$v['oid']]['order_sn'] = $v['order_sn'];
				$order[$v['oid']]['amount'] = format_price($v['amount']);
				$order[$v['oid']]['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
				$order[$v['oid']]['is_comment'] = $v['is_comment'];
				$order[$v['oid']]['pay_code'] = $v['pay_code'];
				$order[$v['oid']]['pay_status'] = $v['pay_status'];
				$order[$v['oid']]['deliver_status'] = $v['deliver_status'];
				$order[$v['oid']]['status'] = $v['status'];
				$order[$v['oid']]['address'] = $v['address'];
				$order[$v['oid']]['name'] = $v['cnee_name'];
				$order[$v['oid']]['mobile'] = $v['cnee_mobile'];
				$order[$v['oid']]['status_name'] = order_status($v['status']);
			}
			$v['thumb'] = url_to_abs($v['thumb']);
			$v['url'] = $v['code'].'-g.html';
			$v['spec'] = '';
			if($v['spec_ids'] !='')
			{
				$v['spec'] = get_spec_val($v['spec_ids'], $v['spec_name']);
			}
			$order[$v['oid']]['goods'][] = $v;
		}
	}
	return $order;
}

//获取指定订单的快递信息
function get_order_exp_info($order_sn_list)
{
	global $db;
	return $db->queryall("select order_sn,exp_id,status,cnee_name,cnee_mobile,cnee_tel,cnee_address,concat(cnee_dist_name,cnee_address) address,cnee_dist_ids from ".$db->table('order')." where order_sn in(".$order_sn_list.")");
}

//获取订单赠送积分
function get_order_point($order_sn, $goods_id=0, $spec='')
{
	global $db;
	$where ='';
	if($goods_id !=0)
	{
		$where .=" and og.goods_id=".intval($goods_id);
	}
	if($spec !='')
	{
		$where .=" and og.spec='".trim($spec)."'";
	}
	
	$row =$db->query("SELECT sum(og.point) point  FROM ".$db->table('order')." o join ".$db->table('order_goods')." og on o.id=og.order_id where order_sn='".$order_sn."' ".$where);
	return $row ? intval($row['point']) : 0;
}

//后台订单列表
function order_list($id=0, $order_sn='', $uid =0,$where,$start=0, $num =null)
{
	global $db;  
	if($id !=0)
	{
		$where .=" and o.id=". intval($id);
	}
	if($order_sn != '')
	{
		$where .=" and order_sn='". $order_sn."'";
	}
	if($uid !=0)
	{
		$where .=" and o.uid=". intval($uid);
	}
	$limit = $num ==null ? "" : " limit ".intval($start).",".intval($num);
	
	$row = $db->queryall("select o.id oid,o.uid,o.order_sn,o.amount,o.goods_amount,o.payble_amount,o.add_time,cnee_name,cnee_mobile,concat(cnee_dist_name,cnee_address) cnee_addr,o.status,o.is_comment,o.pay_status,o.deliver_status,o.pay_code,m.uname,m.status u_status  from ".$db->table('order')." o left join ".$db->table('member')." m on o.uid=m.id where 1 ".$where.' order by add_time desc '.$limit);
	
	if($row)
	{
		foreach ($row as $k => $v) {
			$row[$k]['amount'] = format_price($v['amount']);
			$row[$k]['goods_amount'] = format_price($v['goods_amount']);
			$row[$k]['payble_amount'] = format_price($v['payble_amount']);
			$row[$k]['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
			$row[$k]['status_name'] = order_status($v['status']);
			$row[$k]['goods'] = get_order_goods($v['order_sn']);
		}
	}
	return $row;
}

function order_status($status='')
{
	global $lang_status;
	return $lang_status[$status] ?$lang_status[$status]:'订单异常';
}

function get_order_status($status=0, $pay_status=0, $deliver_status=0,$cod=0)
{
	switch ($status) { 
		case order_paying://正常		
			if($pay_status== pay_unpayed && $cod ==0)
			{
				return "等待付款";
			}
			elseif($pay_status==pay_payed || $cod ==1)
			{
				if($deliver_status==deliver_not)//deliver_ed
				{
					return "正在发货";
				}
				else {
					return "等待收货";
				}
			}
			else {
				return '订单异常';
			}
			break;
		case order_cancel://取消
			return "已取消";
			break;
		case order_del://删除
			return "已删除";
			break;
		case order_finish://完成
			return "已完成";
			break;		
		default:
			return '订单异常';
			break;
	}
}

/**更新订单表*/
function update_order($order=array(), $uid=0)
{
	global $db;
	$where = array();
	if($uid !=0)
	{
		$where["uid"] = intval($uid);
	}
	if(isset($order['order_sn']))
	{
		$where["order_sn"] = $order['order_sn'];
		unset($order['order_sn']);
	}
	elseif (isset($order['id'])) {
		$where["id"] = $order['id'];
		unset($order['id']);
	}
	else {
		return false;
	}
        //fix Incorrect decimal value: '' for column 'payble_amount'
        if(is_null($order['payble_amount'])){
    	    unset($order['payble_amount']);
        }
	$db->update('order', $order, $where);
}

/**更新订单商品表*/
function update_order_goods($order_id, $order_goods=array())
{
	global $db;
	$db->update('order_goods', $order_goods, array("order_id"=>$order_id));
}

function add_order_log($order_sn='', $op_uid=0, $op_name='', $op_type=2, $msg='', $exp_no='')
{
	global $db;
	$db->insert('order_log', array("order_sn"=>$order_sn,"op_uid"=>$op_uid,"op_name"=>$op_name,"op_type"=>$op_type,"msg"=>$msg,"exp_no"=>$exp_no,"addtime"=>time(),"ip"=>getip()));
 	
}

function get_order_log($order_sn, $exp_no='')
{
	global $db;
	$row= $db->queryall("select * from ".$db->table('order_log')." where order_sn=".$order_sn.($exp_no==''?'':" and exp_no='".$exp_no."'")." order by addtime desc");
 	foreach ($row as $k => $v) {
 		$row[$k]['addtime']= date('Y-m-d H:i:s', $v['addtime']);
 	}
	return $row;
}

function add_delivery($order_sn,$exp_id='',$exp_no='')
{
	global $db;
	$db->insert('delivery', array("order_sn"=>$order_sn,"exp_id"=>intval($exp_id),"exp_no"=>$exp_no,"exp_time"=>time()));
}

//发货信息
function get_delivery_info($order_sn='',$exp_id='',$exp_no='')
{
	global $db;
	return $db->fetch('delivery',"*", array("order_sn"=>$order_sn,"exp_id"=>intval($exp_id),"exp_no"=>trim($exp_no)));
}

//发货信息
function get_delivery($order_sn)
{
	global $db;
	$row = $db->queryall("select d.*,ifnull(e.name,'商家配送') exp_name,e.code exp_code,e.code2 exp_code2 from ".$db->table('delivery')." d left join ".$db->table('express_common')." e on d.exp_id=e.id where order_sn=".$order_sn);

	foreach ($row as $k => $v) {
		$row[$k]['exp_time']= date('Y-m-d H:i:s', $v['addtime']);
		//$row[$k]['goods'] = get_delivery_goods($order_sn);
	}
	return $row;
}

//发货商品
function get_delivery_goods($order_sn)
{
	global $db;
	return $db->queryall("select * from ".$db->table('delivery')." d join ".$db->table('delivery_goods')." dg on d.id=dg.delivery_id where order_sn in(".$order_sn.")");
}

//获取指定订单的发货单
function get_order_delivery($order_sn)
{
	global $db;
	return $db->queryall("select d.exp_id,d.order_sn,d.exp_no,o.status order_status,cnee_name,cnee_mobile,cnee_tel,cnee_address,concat(cnee_dist_name,cnee_address) address,cnee_dist_ids from ".$db->table('delivery')." d join ".$db->table('order')." o on d.order_sn=o.order_sn where d.order_sn in(".$order_sn.")");
}

//未发货商品
function get_not_delivery($order_sn)
{
	global $db;
	$goods = $db->queryall("SELECT g.thumb,g.code,og.* FROM ".$db->table('order')." o join ".$db->table('order_goods')." og on o.id=og.order_id left join ".$db->table('goods')." g on g.goods_id=og.goods_id left join (select order_sn,concat(goods_id,spec)goods from ".$db->table('delivery')." d join ".$db->table('delivery_goods')." dg on d.id=dg.delivery_id WHERE order_sn=".$order_sn.") de on de.order_sn=o.order_sn where concat(og.goods_id,og.spec)<>ifnull(goods,'') and o.order_sn=".$order_sn);
 
	foreach ($goods as $k => $v) {
		$goods[$k]['price'] = format_price($v['price']);
		$goods[$k]['amount'] = format_price($v['price'] * $v['num']);
		$goods[$k]['url'] = $v['code'].'-g.html';
	}
	return $goods;
}

/**订单数*/
function order_count($uid=0, $status=0, $where='')
{
	global $db;
	if($uid !=0)
	{
		$where .=" and uid=".$uid;
	}
	if($status !=0)
	{
		$where .=" and status=".$status;
	}
	return $db->rowcount('order', "1 ".$where);
}

function get_distictinfo($id)
{
	global $db;
	return $db->fetch('district', '*', array('id'=>intval($id)));
}

function get_distict_name($id)
{
	$row = get_distictinfo($id);
	if($row)
	{
		return $row['name'];
	}
	return '';
}

/*检测是否全退完*/
function check_return($order_sn, $refund_no)
{
	global $db;
	$return_total =$db->query("select ifnull(sum(num),0) total from ".$db->table("order_service") . " where type=".service_return." and order_sn=". $order_sn." and (status=".service_finish." or refund_no='".$refund_no."')");
	$order_total =$db->query("select ifnull(sum(og.num),0) total from ".$db->table("order_goods") . " og join ".$db->table("order") . " o on og.order_id=o.id where order_sn=". $order_sn);

	return $return_total['total'] == $order_total['total'];
	
}


/**
 * 根据订单SN(必须为已经支付成功订单)取消与订单相同商品的其他待支付的订单(没有库存了)
 * @param $order_sn
 * @return mixed
 */
function cancel_other_order($order_sn)
{
	global $db;

    $rs = get_order_goods($order_sn);


    foreach ($rs as $v){
    	echo $v['goods_id'] ;

    	$res = get_orders_by_goods_id($v['goods_id']);

    	var_dump($res);

	}

    return$rs;
}


function get_orders_by_goods_id($goods_id, $status=order_paying)
{
    global $db;

    return $db->queryall("select id,order_id,goods_id,status from ".$db->table('order_goods')."  where goods_id=".$goods_id." and status=".$status);

}

?>
