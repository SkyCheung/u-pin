<?php
if (!defined('in_mx')) {exit('Access Denied');}

 
if($mod && $mod== 'payment')
{
	if($code && $code !='')
	{
		require pay_root.$pay_code.'/'.$code.'.php';
	}
}
if($act)
{
	if($act == 'auth')
	{
		//$u 
	}
}
die(0);
?>