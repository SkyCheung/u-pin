<?php
if (!defined('in_mx')) {exit('Access Denied');}

/* * 
 * 功能：支付宝页面跳转同步通知页面
 */

//直接依赖服务端的异步通知，忽略同步返回 
if($_GET['passback_params']==1)
{
	redirect("mymoney.html"); 
}
redirect("payresult.html?oid=".$_GET['out_trade_no']); 
die();

?>
