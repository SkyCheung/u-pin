<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*优惠券*/
require  './inc/lib/coupon.php';

if($act)
{
	$res = array('err' => '', 'res' => '', 'data' => array());
	if($act == 'receive_coupon') //领取优惠券
	{
		$coupon_id = isset($coupon_id) ? intval($coupon_id) :0;
		if($coupon_id == 0)
		{
			$res['err'] = "优惠券id不能为空";
			die(json_encode_yec($res));						
		}
		$ym_uid = check_login(1);  
		if($ym_uid == 0)
		{
			$res['url'] = "login.html?return_url=/quan.html";
			die(json_encode_yec($res));
		}
		
		$db=dbc();
		$today = date("Y-m-d 00:00:00");
		$coupon = get_couponinfo($coupon_id, false);
		if(!$coupon)
		{
			$res['err'] = "获取不到该优惠券信息";
			die(json_encode_yec($res));
		}
		if($coupon['status'] == 0)
	    {
		  	$res['err'] = "该优惠券暂时不能领取";
			die(json_encode_yec($res));			
		}
		if($coupon['get_type'] == coupon_gettype_give)
	    {
		  	$res['err'] = "该优惠券需要平台发放 ，不可自行领取";
			die(json_encode_yec($res));			
		}
		if($coupon['limit_start'] > $today)
	    {
		  	$res['err'] = "还没到领取时间<br>开始时间是 " . $coupon['limit_start'];
			die(json_encode_yec($res));			
		}
		if($coupon['limit_end'] < date("Y-m-d H:i"))
	    {
		  	$res['err'] = "来晚了，领取时间已结束";
			die(json_encode_yec($res));			
		}
		if($coupon['num'] <= $coupon['get_num'])
		{
			$res['err'] = "很遗憾，已经领完了";
			die(json_encode_yec($res));
		}
		$receiver_count = get_receiver_count($coupon_id, true);
		if($coupon['day_num'] <= $receiver_count)
		{
			$res['err'] = "今天已经领完了，明天早点来吧";
			die(json_encode_yec($res));
		}
			
		$row = get_coupon_user($ym_uid, $coupon_id);
		if($row)
		{
			if(count($row) >= $coupon['limit_num'])
			{
				$res['err'] = "每人限领 ".$coupon['limit_num']." 张，别贪心哦";
				die(json_encode_yec($res));
			}
			$total_count = 0;
			foreach($row as $k => $v) {
				if(date('Y-m-d 00:00:00', $v['get_time']) == $today)
				{
					$total_count ++;
				}
			}
			if($total_count >= $coupon['user_day_num'])
			{
				$res['err'] = "每人每天限领 ".$coupon['user_day_num']." 张，明天再来吧";
				die(json_encode_yec($res));
			}
		}
		
		
		$grade_ids = explode(",", $coupon['grade_ids']);
		if(trim($coupon['grade_ids']) !='0' && count($grade_ids)>0)
		{
			$user = get_user($ym_uid);
			$is_limit = true;
			foreach ($grade_ids as $k => $v) {
				if($user['grade_id'] == $v)
				{
					$is_limit = false;
					break;
				}
			}
			if($is_limit)
			{
				$res['err'] = "您的会员等级不满足条件 ";
				die(json_encode_yec($res));
			}
		}
		
		add_coupon_user($coupon_id, $ym_uid, getip());
		update_coupon_getNum($coupon_id);//更新数量
		$res['res'] = "ok";
	}
	elseif($act == 'get_coupon') //获取优惠券列表 
	{
		$db=dbc();
		$ym_uid = intval($ym_uid);
		$is_count = isset($is_count) ? intval($is_count) : 0; 
		$page=intval($page)==0 ? 1 : intval($page);
		$num = isset($num) ? intval($num) : 12; 
		$startI = $page*$num - $num;
		$where = "and get_type=1 and limit_start<=".time()." and limit_end>".time();
		if($is_count ==1)
		{
			$res['count'] = get_coupon_count($where);
			$res['total'] = ceil($res['count']/$num);
		} 
		$res['data'] = get_coupon_list($where, 1, $startI, $num, $ym_uid);
	}
	
	die(json_encode_yec($res));
}

$nav_header = get_nav('top');
$nav = get_nav(); //导航
$cats = get_catTree(); //分类树
$help = get_help(); //帮助
$nav_footer = get_nav('bot'); //底部导航


?>