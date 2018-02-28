<?php
if (!defined('in_mx')) {exit('Access Denied');}
/*订单管理*/
	checkAuth($login_id, 'order');//权限检测
	
	$exp =get_cache('express_common');
	$exp[0]=array('id'=>0,'name'=>'商家配送');
	if($ym_is_pickup && $ym_is_pickup==1) //开启自提
	{
		$exp[1]=array('id'=>1,'name'=>'自提');
	}
	sort($exp);

	$where = "";
	if(isset($status) && $status != '')
	{
		$where .=" and o.status=". intval($status);
	}
	if(isset($exp_id) && $exp_id != '')
	{
		$where .=" and o.exp_id=". intval($exp_id);
	}
	if(isset($client) && $client != '')
	{
		$where .=" and o.client=". intval($client);
	}
	if(intval($t)==1)//待付款
	{
		$where .=" and pay_code<>'cod' and o.pay_status=0";
	}
	if(intval($t)==2)//待收货
	{
		$where .=" and (pay_code='cod' or o.pay_status=".pay_payed.") and o.deliver_status=". deliver_ed;
	}
	if(intval($t)==3)//待评价
	{
		$where .=" and is_comment=0 and o.status=".order_finish;
	}
	if($trade_start_date != '')
	{
		$where .=" and o.add_time>=". strtotime($trade_start_date);
	}
	if($trade_end_date != '')
	{
		$where .=" and o.add_time<=". strtotime($trade_end_date);
	}
	if(trim($order_sn) != '')
	{
		$where .=" and o.order_sn like '%".$order_sn. "%'";
	}
	if(trim($uname) != '')
	{
		$where .=" and m.uname like '%".$uname. "%'";
	}

	$page=intval($page)==0 ? 1 : intval($page);
	$pagenum= 12; 
	$start = $page * $pagenum - $pagenum;
	$count = get_order_count(0,'',$ym_uid, $where); 
	if ($count>0)
	{
		$pages = getPages($count, $page, $pagenum);	
	}
	$order = order_list(0,'',0, $where, $start, $pagenum); 
	
	
?>