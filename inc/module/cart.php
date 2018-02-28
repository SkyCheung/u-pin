<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*购物车*/
require_once './inc/lib/cart.php';
require  './inc/lib/promotion.php';
$res = array('err' => '', 'res' => '', 'data' => array());

if(isset($act)) 
{
	if($act == 'addtocart')//加入购物车
	{		
		$ckey = $ckey ? intval($ckey) : intval($_COOKIE['ckey']);
		/*$gid_list= explode("-", trim($gid));
		$spec_list= explode("-", trim($spec));
		if(count($gid_list)==0)
		{
			$res['err'] = '获取商品编号失败'.$act;
			die(json_encode_yec($res));
		}
		foreach ($gid_list as $k => $v) {
			if(intval(trim($v)) >0)
			{
				
			}
		}*/
		$gid = intval($gid);
		$spec = !isset($spec) ? '' : trim($spec);
		$num = intval($num);
		$total =intval($total);
		$pid = !isset($pid) ? 0 : intval($pid);
		$direct = !isset($direct) ? 0 : intval($direct);
		if($gid == 0) {
			$res['err'] = '获取商品编号失败';
			die(json_encode_yec($res));
		}
		if($total<=0 && $num <= 0) {
			$res['err'] = '购买数量格式错误';
			die(json_encode_yec($res));
		}
		
		$db = dbc();
		
		$cart_goods = array();
		if($ym_uid > 0) {
			$cart_goods = get_cart_goods(0, $ym_uid);
		} else if ($ckey > 0) {
			$cart_goods = get_cart_goods($ckey);
		}
		if(count($cart_goods) >= 100)
		{
			$res['err'] = '抱歉，您的购物车已经满了，先去结账吧';
			die(json_encode_yec($res));
		}
		
		$goods = get_goods_num($gid, $spec);
		$err ='';
		$cart_total = 0;
		foreach ($cart_goods as $k => $v) {
			if($v['gid'] == $gid && $v['spec'] == $spec)
			{
				$cart_total = $cart_total+$v['num'];
				break;
			}
		}

			if( ($cart_total + $num) >$goods['number'])
				{
					$err .= '库存不足，库存只有'.$goods['number'].'件<br>'; 
				}
				if($goods['goods_status'] != goods_up) //已下架
				{
					$err .= '来晚了，商品已下架<br>'; 
				}
				if($goods['goods_status'] == goods_up && $goods['uptime']> time()) //未上架
				{
					$err .= '来早了，商品还没有上架<br>'; 
				}
		if($err !='')
		{
			$res['err'] = $err;
			die(json_encode_yec($res)); 
		}
		
		try
		{
			$spec = $goods['spec'];
			if($direct == 1) //立即购买
			{
				update_cart($ckey, $ym_uid);
			}

			$n = '';		
			$cnum = $num;
			if ($cart_goods)//已有商品
			{
				foreach ($cart_goods as $k => $v) {
					if ($v['gid'] == $gid && $v['spec'] == $spec) {
						$n = $k;
					}
					$cnum= $cnum +$v['num'];
				}
				$id = $cart_goods[0]['cid'];
				$db -> update('cart', array('lasttime' => time()), array("id" =>$id));
			} 
			else {
				$db -> insert('cart', array('uid' => $ym_uid, 'lasttime' => time())); 
				$id = $db -> lastinsertid();
			}
			
			if ($n !== '') {
				$db -> update('cart_item', array('num' => ($total >0 ? $total: $num + $cart_goods[$n]['num']), 'pid' => $pid, 'status' => '1'), array('cid' => $id, 'gid' => $gid, 'spec' => $spec));
			} 
			else {
				$db -> insert('cart_item', array('cid' => $id, 'gid' => $gid, 'num' => $num, 'pid' => $pid, 'spec' => $spec, 'status' => '1'));
			}
		
			$time = time() + 15552000;
			if ($ym_uid == 0 && $ckey == 0) {				
				set_cookie('ckey', $id, $time);	//游客购物车id
				$res['cart_id'] = $id;
			}	
			
			if($total >0)
			{
				$cnum=$cnum -$cart_goods[$n]['num']+$total;
			}
			set_cookie('cnum',  $cnum, $time); //购物车数量
			$res['res'] = $cnum;
			die(json_encode_yec($res));
		}
		catch(exception $e)
		{
			$res['err'] = '网络异常';
			die(json_encode_yec($res));
		}
	}
	elseif ($act == 'set_cart_status') //更新商品选择状态
	{		
		$ckey = $ckey ? intval($ckey) : intval($_COOKIE['ckey']);
		$gid = intval($gid);
		$spec = !isset($spec) ? '' : trim($spec);
		$status=intval($status);
		$db = dbc();
		if ($ym_uid > 0)
		{
			$cart_goods = get_cart_goods(0, $ym_uid);
			$id= $cart_goods[0]['cid'];
		}
		elseif ($ckey >0) {
			$id = $ckey;
		}
		else {
			$res['err'] = '请刷新页面再试';
			die(json_encode_yec($res));
		}
				
		$where = array('cid' => $id);
		if($gid > 0)
		{
			$where['gid'] =$gid;
		}
		if($spec !='')
		{
			$where['spec'] =$spec;
		}
		
		$db -> update('cart_item', array('status' => $status), $where);
		die(json_encode_yec($res));
	}
	elseif ($act == 'remove_goods') //移除商品
	{		
		$ckey = $ckey ? intval($ckey) : intval($_COOKIE['ckey']);
		$gid_list = explode("@", $gid);
		$spec_list = explode("@", $spec);
		
		$db = dbc();
		$where ='';
		if($ckey != 0)
		{
			$where =' and cid='.$ckey;
		}
		elseif($ym_uid !=0)
		{
			$where =' and uid='.$ym_uid;
		}
		else {
			$res['err'] = '请刷新页面再试';
			die(json_encode_yec($res));
		}
		
		foreach ($gid_list as $k => $v) {
			$sp = $spec_list[$k];
			if(intval($v)<=0)
			{
				continue;
			}			
									
			$db->query("delete i FROM ".$db->table('cart')." c join ".$db->table('cart_item')." i on c.id=i.cid where gid=".$v." and spec='".$sp."' ".$where);
		}
		
		$cnum = get_cart_amount(1);
		$res['res'] = $cnum;
		$time = time() + 15552000;				
		set_cookie('cnum',  $cnum, $time); //购物车数量
		die(json_encode_yec($res));
	}	
	elseif ($act == 'get_cart')
	{
		dbc();
		$row= get_cart(); 
		$res['data']= $row; 
		set_cookie('cnum',  intval($row['num']), time() + 15552000);
		die(json_encode_yec($res));
	}
	elseif ($act == 'get_area')
	{
		if(!isset($pid) || !is_numeric(trim($pid)))
		{
			$res['err']= '区域编号错误';
			die(json_encode_yec($res));
		}
		$db=dbc();
		$tmp_district = $db ->fetchall('district', 'id,name,pid', 'pid='.$pid, ' sort asc');
		foreach ($tmp_district as $k => $v) {
			$tmp_row = $db->rowcount('district', array('pid'=> $v['id']));
			$tmp_district[$k]['son']=intval($tmp_row);
		}
		die(json_encode_yec($tmp_district));
	}
}

dbc();

$nav = get_nav(); //导航
$nav_footer = get_nav('bot');
$cats = get_catTree(); //分类树
$help = get_help(); //帮助

$cart = get_cart();
$history = get_history(10);

?>