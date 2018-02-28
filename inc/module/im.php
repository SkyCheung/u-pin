<?php
if (!defined('in_mx')) {exit('Access Denied');}

$res = array('err' => '', 'res' => '', 'data' => array());

dbc();

if(isset($act))
{
    if($act == 'get_goods')
	{
		if(!isset($gid) && intval($gid) ==0)
		{
			$res['err']= '请提供商品编号';
		}		
		$goods = get_goods($gid);
		
		$res['data'] =$goods;
	}
	elseif($act == 'get_order')
	{
		if(!isset($oid) && intval($oid) ==0)
		{
			$res['err']= '请提供订单编号';
		}
		if($ym_uid==0) //需要登录
		{
			$ym_uid =check_login(1);
			if($ym_uid==0)
			{
				$res['url'] = 'login.html';
				die(json_encode($res));
			}			
		}
		$order = get_order_goods($oid, $ym_uid);
		$res['data'] = array('thumb'=>$order[0]['thumb'],'addtime'=>date('Y-m-d H:i:s',$order[0]['order_time']), 'amount'=>$order[0]['order_amount']);
	}
	elseif($act == 'get_userinfo')
	{
		$jid = isset($jid) ? trim($jid) : ''; //1000001#custmer#3
		$tmp_jid = explode('#', $jid);
		if(count($tmp_jid) ==0 || $tmp_jid[2] == '')
		{
			$res['err'] = 'jid错误';
			die(json_encode($res));	
		}
		$lg_id = intval($_SESSION["lg_id"]); 
		if($lg_id >0)
		{			
			$user = get_user($tmp_jid[2]);
			$tmp_row['id'] = $user['id'];
			$tmp_row['uname'] = $user['uname'];
			$tmp_row['img'] = $user['img'];
			$res['data'] =$tmp_row;
		}
		else {
			$res['err'] = '登录超时，请重新登录';
		}		
	}
	die(json_encode($res));	
}


?>