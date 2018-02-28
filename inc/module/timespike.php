<?php
if (!defined('in_mx')) {exit('Access Denied');}
$nav = get_nav(); //导航
$cats = get_catTree(); //分类树
$help = get_help(); //帮助
$nav_footer = get_nav('bot'); //底部导航
dbc();
if($ym_uid){
	$user = get_user($ym_uid);
};
$time = time();
$j=1;
if(is_array($diy_timespike)){
	foreach($diy_timespike as $k=>$v){
		if($v['end_time']>$time){
			if($v['start_time']<$time){
				$spike=$v;
				if($v['type']==1){
					foreach($spike['goods'] as $w=>$y){
						$spike['goods'][$w]['price']=$spike['val'];
					}					
				}else if($v['type']==2){
					foreach($spike['goods'] as $w=>$y){
						$spike['goods'][$w]['price']=$spike['goods'][$w]['price']-$spike['val'];
					}	
				}else if($v['type']==3){
					foreach($spike['goods'] as $w=>$y){
						$spike['goods'][$w]['price']=$spike['goods'][$w]['price']*$spike['val'];
					}	
				}
			}else{
				$nextspike=$v;
				if($v['type']==1){
					foreach($nextspike['goods'] as $w=>$y){
						$nextspike['goods'][$w]['price']=$nextspike['val'];
					}					
				}else if($v['type']==2){
					foreach($nextspike['goods'] as $w=>$y){
						$nextspike['goods'][$w]['price']=$nextspike['goods'][$w]['price']-$nextspike['val'];
					}	
				}else if($v['type']==3){
					foreach($spike['goods'] as $w=>$y){
						$nextspike['goods'][$w]['price']=$nextspike['goods'][$w]['price']*$nextspike['val'];
					}	
				}
			}
		}
	}
}
$nowtime = $spike[end_time]-$time;
$nexttime = $nextspike[start_time]-$time;
?>