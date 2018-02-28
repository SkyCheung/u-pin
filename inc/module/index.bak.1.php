<?php
if (!defined('in_mx')) {exit('Access Denied');}

if (file_exists(cache_data."diy.php")) {
	include(cache_data."diy.php");
}

$nav_header = get_nav('top');
$nav = get_nav(); //导航
$cats = get_catTree(); //分类树
$help = get_help(); //帮助
$nav_footer = get_nav('bot'); //底部导航

dbc();
$brandnum = isset($brandnum) ? intval($brandnum) : 10; 
$brand = get_brand(0, '', 1 , 0, $brandnum);  //todo 做缓存
 
$cur='index';

//根据游览记录获取相关的猜你喜欢产品
$history = get_history(100);
foreach($history as $k=>$y){	
	$cat_ids[$k] = $y['cat_id']; //获取同级别的属性id
}
if(count($cat_ids)>0){
	$cat_ids = array_unique($cat_ids);
	$diy_goodslike = array();
	foreach($cat_ids as $k=>$y){
		$diy_goods = get_diy_goods($diy_mobileindexyoulike, $y);
		if($diy_goods)
		{
			$diy_goodslike = array_merge($diy_goodslike,$diy_goods);
		}
	}
}
$remmend_goods=array();
$goodsidnum = '';
$i=0;
if(count($diy_goodslike)>0)
{
	foreach($diy_goodslike as $y){
		
		foreach($y['goods'] as $w){
			if($i==$ym_youlike_num){
				break;
			}
			$remmend_goods[] = $w;
			$goodsidnum = $goodsidnum .$w['goods_id'].',';
			$i++;
		}
	}
}
$goodsnum =  $ym_youlike_num - count($remmend_goods);
if($goodsnum){
	if($diy_itemrem){
		foreach($diy_itemrem as $u){
			foreach($u['goods'] as $y){
				if(!strstr($goodsidnum,$y['goods_id'])){
					$remmend_goods[] = $y;
					$goodsnum--;
				}
				if($goodsnum==0){
					break;
				}
			}
			if($goodsnum==0){
				break;
			}
		}
	}else{
		$rowss = $db -> queryall("SELECT * FROM ".$db->table('goods')." where 1  order by `goods_id` desc LIMIT ".$goodsnum);
		foreach($rowss as $s){
			$remmend_goods[] = $s;
		}
	}
}

if($ym_uid !=0)
{
	$user = get_user($ym_uid);
}

$time = time(); 
if(is_array($diy_timespike)){
	foreach($diy_timespike as $k=>$v){
		if(!$v['goods'])
		{
			continue;
		}
		if($v['end_time']>$time){
			if($v['start_time']<$time){
				$spike=$v;
				if($v['type']== promotion_type_fixed){
					foreach($spike['goods'] as $w=>$y){
						$spike['goods'][$w]['price']= format_price($spike['val']);
					}					
				}else if($v['type']==promotion_type_cut){
					foreach($spike['goods'] as $w=>$y){
						$spike['goods'][$w]['price']= format_price($spike['goods'][$w]['price']-$spike['val']);
					}	
				}else if($v['type']==promotion_type_off){
					foreach($spike['goods'] as $w=>$y){
						$spike['goods'][$w]['price']= format_price($spike['goods'][$w]['price']*$spike['val']);
					}	
				}
			}else{
				$nextspike=$v;				
				if($v['type']== promotion_type_fixed){
					foreach($nextspike['goods'] as $w=>$y){
						$nextspike['goods'][$w]['price']= format_price($nextspike['val']);
					}					
				}else if($v['type']==promotion_type_cut){
					foreach($nextspike['goods'] as $w=>$y){
						$nextspike['goods'][$w]['price']=format_price($nextspike['goods'][$w]['price']-$nextspike['val']);
					}	
				}else if($v['type']==promotion_type_off){
					if($spike['goods'])
					{
						foreach($spike['goods'] as $w=>$y){
							$nextspike['goods'][$w]['price']=format_price($nextspike['goods'][$w]['price']*$nextspike['val']);
						}	
					}
				}
			}
		}
	}
}
$nowtime = $spike[end_time]-$time;
$nexttime = $nextspike[start_time]-$time;

?>