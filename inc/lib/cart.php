<?php
if (!defined('in_mx')) {exit('Access Denied');}

//获取购物车里的商品
function get_cart($status= -1)
{	 
	global $db, $ym_uid;
	$id= intval($_COOKIE['ckey']);
	$discount =0;
	if($id==0 && $ym_uid==0){return false;}
	$where = '';
		
	if($ym_uid !=0)
	{
		$where .= " and c.uid=" .intval($ym_uid);
		$user = get_user($ym_uid);
		$discount = $user['discount'];
	}
	elseif($id !=0)
	{
		$where .= " and i.cid=" .$id;
	}
	else {
		$cart['num'] = 0;
		return $cart;
	}
	if($status != -1)
	{
		$where .= " and i.status=" .$status;
	}
	$row = $db->queryall("SELECT g.goods_id,g.name,g.code,g.thumb,g.unit,g.status goods_status,ifnull(g.point*i.num, 0) point,g.uptime,ifnull(s.weight, g.weight) weight,ifnull(s.attr_ids,'') as spec_ids,ifnull(s.`values`,'')as spec_name, ifnull(s.price, g.price) price,ifnull(s.number, g.number) number,(ifnull(s.price, g.price)*i.num) amount,(ifnull(s.weight, g.weight)*i.num) total_weight, c.uid,ifnull(f.id,0) as favid,i.* from " . $db->table('cart'). " c join ".$db->table('cart_item'). " i on c.id = i.cid join ".$db->table('goods'). " g on i.gid=g.goods_id left join ".$db->table('goods_spec'). " s on i.gid=s.goods_id and s.values =i.spec left join ".$db->table('member_fav'). " f on f.goods_id=i.gid and f.spec=i.spec and f.uid=c.uid where 1 ". $where); 
	
	$num = 0;
	$amount = 0;
	$weight = 0;
	$cart= array();
	foreach ($row as $k => $v) {		
		$row[$k]['url']= url_to_abs($v['code'].'-g.html');
		$num = $num + intval($row[$k]['num']);		
		$weight +=intval($row[$k]['total_weight']);		
		if($v['spec_ids'] !='')
		{
			$row[$k]['spec'] = get_spec_val($v['spec_ids'], $v['spec_name']);
		}
		else {
			$row[$k]['spec'] = '';
		}
		if($ym_uid !=0)
		{
			$row[$k]['price'] = format_price(get_discount_price($v['goods_id'], $ym_uid, $v['price'],$discount));//活动价格
			$row[$k]['amount'] = format_price($row[$k]['price']*$row[$k]['num']);
			/*if($row[$k]['user_price']==0)
			{
				$amount = format_price($amount + floatval($row[$k]['amount']), $ym_priceformat);
			}
			else*/ 
			{
				$amount = format_price($amount + floatval($row[$k]['amount']), $ym_priceformat);
			}
		}
		else {
			$row[$k]['price']= format_price($v['price']);
			$row[$k]['amount']= format_price($v['amount']);
			$amount = format_price($amount + floatval($row[$k]['amount']), $ym_priceformat);
		}
	}
	$cart['goods'] = $row;
	$cart['num'] = $num;
	$cart['amount'] = $amount;
	$cart['weight'] = $weight;
	
	return $cart;
}

function get_cart_goods($id = 0, $uid =0) 
{	 
	global $db;
	$where = '';
	if($id!=0)
	{
		$where .= " and c.id=" .intval($id);
	}	
	if($uid!=0)
	{
		$where .= " and c.uid=" .intval($uid);
	}
		
	return $db->queryall("SELECT c.id as cid,c.uid,i.gid,i.pid,i.num,i.spec,i.status from ".$db->table('cart')." c left join ".$db->table('cart_item')." i on c.id=i.cid where 1 ". $where);
}

//删除购物车
function del_cart($id = 0, $uid=0)
{
	global $db;
	$where = '';
	if($id==0 && $uid==0)
	{
		return;
	}
	if($id !=0)
	{
		$where .=' and id='. $id;
	}
	if($uid !=0)
	{
		$where .=' and uid='. $uid;
	}
	
	$db->query("delete i,c from ".$db->table('cart_item')." i join ".$db->table('cart')." c on c.id=i.cid where 1 ".$where);
}

/*清空购物车*/
function clear_cart($id=0, $uid=0, $status =1)
{
	global $db,$tablepre;
	$where = '';
	if($id==0 && $uid==0)
	{
		return;
	}
	if($id !=0)
	{
		$where .=' and id='. $id;
	}
	if($uid !=0)
	{
		$where .=' and uid='. $uid;
	}
	if($status != -1)
	{
		$where .=' and status='. $status;
	}
	
	$db->query("delete i from ".$tablepre."cart_item i join ".$tablepre."cart c on c.id=i.cid where 1 ".$where);
}

/*将购物车的所有商品设为不选中*/
function update_cart($id=0, $uid=0)
{
	global $db,$tablepre;
	$where = '';
	if($id==0 && $uid==0)
	{
		return;
	}
	if($uid !=0)
	{
		$where .=' and uid='. $uid;
	}
	else if($id !=0)
	{
		$where .=' and c.id='. $id;
	}
	 
	$db->query("update ".$tablepre."cart_item i join ".$tablepre."cart c on c.id=i.cid set i.status=0 where 1 ".$where);
}

//合并游客购物车里的商品到当前登录用户, 或者合并第三方登陆的到已有账户
function merge_cart($uid, $sub_uid=0)
{
	global $db,$direct,$return_url;
	if($sub_uid !=0)
	{
		$cart_goods_cookie = get_cart_goods(0, $sub_uid);
		if(!$cart_goods_cookie)
		{
			return;
		}
		$ckey = $cart_goods_cookie[0]['cid'];
	}
	else{
		$ckey = isset($_SESSION['cart_id']) ? intval($_SESSION['cart_id']) : intval($_COOKIE['ckey']);
		if($ckey == 0)
		{
			return;
		}
		$cart_goods_cookie = get_cart_goods($ckey);
	}

	$cart_goods = get_cart_goods(0, $uid);	
	$cart_goods_count = count($cart_goods);
	if($cart_goods_count >= 100 || count($cart_goods_cookie)==0) // 清空游客购物车
	{
		$db->delete('cart', array('id'=>$ckey));
		$db->delete('cart_item', array('cid'=>$ckey));
		return;
	}	
 	
	if ($cart_goods)//已有商品
	{
		if($return_url && !empty($return_url))
		{
			$url = parse_url(urldecode($return_url));
			parse_str($url['query'], $param);
			if($url['path']=='/order.html' && intval($param['directbuy']) == 1)//立即购买
			{
				update_cart(0, $uid);
			}
		}

		$id = $cart_goods[0]['cid'];
		$db -> update('cart', array('lasttime' => time()), array("id" =>$id));
		foreach ($cart_goods_cookie as $key => $val) {
			if($cart_goods_count >= 100)
			{
				break;
			}
			$is_exists =false;
			foreach ($cart_goods as $k => $v) {		
				if($v['gid'] == $val['gid'] && trim($v['spec']) == trim($val['spec']) ) //只加没有的
				{
					unset($cart_goods[$k]);
					$is_exists=true;
					break;	
				}				
			}
			if($is_exists==false)
			{
				$db ->insert('cart_item', array('cid' => $id, 'gid' => $val['gid'], 'num' => $val['num'], 'pid' => $val['pid'], 'spec' => $val['spec'], 'status' => $val['status']));
				$cart_goods_count ++;
			}
		}
		
		$db->delete('cart', array('id'=>$ckey));
		$db->delete('cart_item', array('cid'=>$ckey));
	}
	else {
		$id = $cart_goods_cookie[0]['cid'];
		if(intval($cart_goods_cookie[0]['uid']) > 0) //防cookie伪造
		{
			return;
		}
		$db -> update('cart', array('uid' => $uid, 'lasttime' => time()), array("id" =>$id));
	} 
		
	set_cookie('ckey', '');			
	set_cookie('cnum',  ''); //购物车数量	
}

?>