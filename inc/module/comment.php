<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*评价*/
$ym_uid = check_login();

if($act && $act =='add')
{	
	$oid =isset($oid) ? trim($oid): '';
	$item_id =isset($item_id) ? $item_id :'';
	$type =isset($type) ? intval($type) :0;
	$content =isset($content) ? $content :'';
	$star =isset($star) ? $star : '';
	$is_anon =isset($is_anon) ?intval($is_anon):0;
	
	$sql = '';
	if($item_id=='')
	{
		message('获取编号失败');
	}
	if($content=='')
	{
		message('请填写内容哦~');
	}
	$time = time();
	dbc();
	if($type ==0)
	{
		if(!isset($oid) || intval($oid)==0)
		{	
			message('获取订单号失败');
		}
		$order = get_order_details(0, $oid, $ym_uid);
		if(!$order || count($order)==0)
		{
			message('该订单好像不是您的！');
		}
		if($order['is_comment'] ==1)
		{
			message('该订单已经评价了！');
		}
		$goods_ids = array();
		$count = count($item_id);
		$comment_audit = intval($ym_comment_audit) ==1?0:1;
		foreach ($item_id as $k => $v) {
			if(intval($star[$k]) > 0 && trim($content[$k]) != '')
			{
				$imgs_str = "imgs_".$item_id[$k];//.$spec[$k];
				$imgs = $$imgs_str;
				$thumbs_str ="thumbs_".$item_id[$k];//.$spec[$k];
				$thumbs = $$thumbs_str;
				$sql .= "('".$oid."',".$item_id[$k].",'".$spec[$k]."',".$type.",".$ym_uid.",'".trim($content[$k])."',".intval($star[$k]).",'".json_encode($imgs)."','".json_encode($thumbs)."',".$comment_audit.",".$is_anon.",".$time."),";
				$count--;
				
				if(count($imgs)>0 && intval($star[$k])==5)//晒单 + 5星
				{
					 $goods_ids[]= $item_id[$k];
				}
			}			
		} 
		if($sql =='')
		{
			message('请至少对一件商品评价！');
		}
		$sql='insert into '.$db->table('comment'). "(order_sn, item_id,spec, type, uid, content, star, img,thumb, status,is_anon, addtime) values".rtrim($sql, ","); //print $sql;die();
		$db->query($sql); 
		if($count == 0)//全部商品评价完, 更新订单表已评价
		{
			update_order(array('order_sn'=>$oid, 'is_comment'=>1),$ym_uid);
		}	
		
		if(count($goods_ids)>0) //晒单 + 5星 奖励现金/积分
		{
			$gids = create_in($goods_ids);
			$gids =str_replace("'", '', $gids);
			$goods = get_goods_list('and goods_id '.$gids, 'comment_reward', '', 0, null);
			$comment_reward = 0;
			foreach ($goods as $k => $v) {
				$comment_reward = $comment_reward + floatval($v['comment_reward']);
			}
			if($comment_reward > 0)
			{
				if($ym_comment_reward==asset_balance)
				{
					$comment_reward =format_price($comment_reward);
					update_account($ym_uid, $comment_reward, 0);
					add_member_log($ym_uid, $ym_comment_reward, $comment_reward, '订单'.$oid.' 晒单+5星好评 获得现金'.$comment_reward);
				}
				elseif($ym_comment_reward==asset_point)
				{
					update_account($ym_uid, 0, $comment_reward);
					add_member_log($ym_uid, $ym_comment_reward, $comment_reward, '订单'.$oid.' 晒单+5星好评 获得积分'.$comment_reward);
				}				
			}
		}
	}
		
	message('评价成功',"details.html?oid=".$oid);
	die();
}

if(!isset($oid) || intval($oid)==0)
{	
	redirect("index.html");
}
dbc();

$nav = get_nav(); //导航
$nav_footer = get_nav('bot');
$cats = get_catTree(); //分类树
$help = get_help(); //帮助

$goods = get_not_comment($oid, $ym_uid);


?>