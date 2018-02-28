<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*我的订单*/

 	$res = array('err' => '', 'res' => '', 'data' => array());	
	$is_ajax=isset($is_ajax) ? intval($is_ajax): 0;		
	$keyword = isset($keyword) ? trim(addslashes($keyword)) : '';
	
	if($ym_uid ==0)
	{
		$ym_uid = check_login($is_ajax);
	}
	
	dbc();
	if($is_ajax == 0)
	{
		$nav = get_nav(); //导航
		$nav_footer = get_nav('bot');
		$cats = get_catTree(); //分类树
		$help = get_help(); //帮助
	}	
	elseif($ym_uid ==0) {
		$res['err'] ='请先登录';
		$res['url'] ='login.html';
		die(json_encode_yec($res));
	}

	$where = "";
	if(isset($status) && $status != -1)
	{
		$where .=" and o.status=". intval($status);
	}
	if(intval($t)==1)//待付款
	{
		$where .=" and pay_code<>'cod' and o.pay_status=0 and o.status<>".order_cancel." and o.status<>".order_del;
	}
	if(intval($t)==2)//待收货
	{
		$where .=" and o.status=". order_receiving;
	}
	if(intval($t)==3)//待评价
	{
		$where .=" and is_comment=0 and o.status=".order_finish;
	}
	if(intval($t)==4)//待发货
	{
		$where .=" and o.status=".order_deliver;
	}
	if($trade_start_date != '')
	{
		$where .=" and o.add_time>=". strtotime($trade_start_date);
	}
	if($trade_end_date != '')
	{
		$where .=" and o.add_time<=". strtotime($trade_end_date);
	}
	if($keyword != '')
	{
		$where .=" and (o.order_sn like '%".$keyword. "%' or og.name like '%".$keyword. "%')";
		$page =0;
	}

	$page=intval($page)==0 ? 1 : intval($page);
	$pagenum=isset($num)? intval($num) : 10; 
	$start = $page * $pagenum - $pagenum;
	$count = get_order_count(0,'',$ym_uid, $where); 
	if ($count>0 && $is_ajax==0)
	{
		$pages = getPages($count, $page, $pagenum);	
	}
	$order = get_order_list(0,'',$ym_uid, $where, $start, $pagenum); 
	
	$trade_start_date ='';
	$trade_end_date ='';

	if($is_ajax == 1)
	{
		if($is_count ==1){$res['count'] = $count;}
		$res['data'] = $order;
		die(json_encode_yec($res));
	}
	
?>