<?php
if (!defined('in_mx')) {exit('Access Denied');}
/*发货*/
checkAuth($login_id, 'order');//权限检测

if(!isset($ids) || isNumComma($ids)==false)
{
	message("获取订单编号失败");
}

if($act && $act=='add')
{
	$update_count = count($goods_ids);
	$order = get_order_details(0, $ids, 0);
	if($order['deliver_status']==deliver_ed)
	{
		header("Location:$url&ok=1");
		die();
	}
	if($goods_ids && count($goods_ids)>0) //具体商品
	{		
		$goods = $order['goods'];
		$not_delivery_goods=array();
		if($update_count != count($goods))
		{
			foreach ($goods as $k => $v) {
				if($v['exp_id'] =='')
				{
					array_push($not_delivery_goods, $v['goods_id']);
				}
			}
		}
		/*if($update_count == count($goods))
		{
			update_order_goods($order['id'],array('exp_id'=>intval($exp_id),'exp_no'=>trim($exp_no),'deliver_time'=>time()));
		}
		else {
			$db -> exec("update ".$db->table('order_goods')." set exp_id=".intval($exp_id).",exp_no='".trim($exp_no)."',deliver_time=".time()." where order_id=".$order['id']." and goods_id ".create_in($goods_ids));
		}*/	
		$delivery=get_delivery_info($ids,$exp_id,$exp_no); 
		if(!$delivery || count($delivery)==0)
		{
			add_delivery($ids, $exp_id, $exp_no);
			$delivery_id=$db->lastinsertid();
		}
		else {
			$delivery_id =$delivery['id'];
		}
		
		$sql="";	
		foreach ($goods_ids as $k => $v) {
			$spec = 'spec_'.$v;
			$sql .= "(".$delivery_id.",".$v.",'".$$spec."'),";
		}
		
		if($sql !="")
		{
			$sql =rtrim($sql,",");
			$sql="insert into ".$db->table('delivery_goods')."(delivery_id,goods_id,spec) values".$sql;
			$db->query($sql);
		}
		
		if($update_count == count($not_delivery_goods) || $update_count == count($goods)) //本次发货后，订单所有商品已发完，则更新订单表
		{
			update_order(array('order_sn'=>$ids,"status"=>order_receiving,"deliver_status"=>deliver_ed,'exp_id'=>intval($exp_id),'exp_no'=>trim($exp_no),'deliver_time'=>time()));
		}
		else {
			update_order(array('order_sn'=>$ids,"deliver_status"=>deliver_part)); //部分发货
		}
		add_order_log($ids, $login_id, $adminname, role_admin, '您的订单已发货：'.$exp_name.',快递单号'.trim($exp_no).' '.trim($msg), trim($exp_no) );
	}
	else { //批量发货
		message('批量发货功能未开放');
		return;
		/*$delivery=get_delivery($ids,$exp_id,$exp_no); 
		if(!$delivery || count($delivery)==0)
		{
			add_delivery($ids, $exp_id, $exp_no);
			$delivery_id=$db->lastinsertid();
		}
		else {
			$delivery_id =$delivery['id'];
		}*/
	
		$db -> exec("UPDATE ".$db->table('order')." SET status=".order_receiving.",deliver_status=".deliver_ed.",exp_id=".intval($exp_id).",exp_no ='".trim($exp_no)."',deliver_time=".time()." WHERE 1 AND order_sn ".create_in($ids));					
	
		$ids_arr = implode(',', $ids);		
		foreach ($ids_arr as $k => $v) {
			add_order_log($v, $login_id, $adminname, role_admin, '您的订单已发货：'.$exp_name.',快递单号'.trim($exp_no).' '.trim($msg), trim($exp_no) );
		}
	}
	
}
header("Location:$url&ok=1");
	 
?>