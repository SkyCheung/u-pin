<?php
if (!defined('in_mx')) {exit('Access Denied');}
/**
 *余额支付*/
$payment_mod = array();
$payment_mod['pay_code']    = basename(__FILE__, '.php'); //代码
$payment_mod['pay_name']    = '余额支付';//支付方式名称
$payment_mod['pay_desc']    = '';
$payment_mod['isonline']    = '1';//是否在线,是1 否0
$payment_mod['client']  = '0';////客户端类型,0所有,1 pc,2 m,3 app, 混合用逗号隔开 如1,3
$payment_mod['config']  =  json_encode(array());//配置信息
$payment_param = array();   

class bal
{
    function __construct()
    {
    }

    /** @name 生成支付html代码
   */  
    function get_payHtml($order=array())
    {
    	return '';
	}
	
	//退款
   function refund($order)
   {   		
   		update_account($order['uid'], $order['refund_fee'], -$order['point']); //扣除相应的赠送积分
		add_member_log($order['uid'], asset_balance, $order['refund_fee'], '订单'.$order['order_sn']. '退货');
		add_member_log($order['uid'], asset_point, -$order['point'], '订单'.$order['order_sn']. '退货'); 
		
		return array('is_success'=>1, 'trade_no'=>'', 'trade_msg'=>''); 
   }
}	
 
?>