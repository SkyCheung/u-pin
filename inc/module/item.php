<?php
if (!defined('in_mx')) {exit('Access Denied');}

require_once  './inc/lib/promotion.php';
if(isset($act)) 
{
	$res = array('err' => '', 'res' => '', 'data' => array());
	if($id==0){
		$res['err'] = '商品不存在';
		die(json_encode($res));
	}
	if($act == 'get_specinfo')//获取某规格信息
	{
		dbc();		
		
		$spec_data = get_specinfo($id, $spec);
		$spec_data['goods_price'] = $spec_data['price'];
		$spec_data['price']= format_price(get_discount_price($id, $ym_uid, $spec_data['price']));
		$res['data'] = $spec_data;
	}
	die(json_encode($res));
}	

$id = ucode($p);
if($id==0){message('商品不存在','/');}

dbc();
$goods = get_goods($id);
if(!$goods)
{
	message('商品不存在','/');
}
if($goods['status'] == goods_del)
{
	message('商品已删除','/');
}

$imgs = json_decode($goods['imgs'],true);
$nav = get_nav(); //导航
$nav_footer = get_nav('bot');
$cats = get_catTree(); //分类树
$help = get_help(); //帮助
$crumbs_nav = get_crumbs_nav('goods', $goods['cat_id']); //面包导航
$express_fee_news = get_spage(0, 14); //print ($a['url']);

$prev_goods = get_prev_goods($id);//上一个商品  
$next_goods = get_next_goods($id);//下一个商品

$spec_img = json_decode($goods['specs'],true);
//规格
$spec= get_goods_spec($id);
//属性
$attr= get_attr_val($id);
if($spec && count($spec)>0 && $spec_img && count($spec_img)>0 && count($spec_img['spec_val'])>0)
{	
	foreach ($spec as $k => $v) {
		if($v['id'] == $spec_img['spec_id'])
		{
			$spec_val = array();
			foreach ($v['val'] as $key => $val) {
				$tmp_val= array();
				$tmp_val['name'] = $val;
				foreach ($spec_img['spec_val'] as $n => $s) {
					if($s['value'] == $val){
						$tmp_val['img']= json_encode($s['imgs'][0]);
						unset($s['imgs'][0]);
						$tmp_val['imgs']= json_encode($s['imgs']);
						unset($spec_img['spec_val'][$n]);
						break;
					}	
				}
				$spec_val[$key] = $tmp_val;
			}
			$spec[$k]['val']= $spec_val;
			$spec[$k]['is_img']=1;
			break;
		}
	}
}

//会员等级价格
//$grade = get_grade();
$price = $goods['min_price'] ==0 ? $goods['price'] : $goods['min_price'];
$discount =0;
if($ym_uid !=0)
{
	$user = get_user($ym_uid);
	$discount = $user['discount'];
	$user_price = $user ? format_price($user['discount'] * $price): 0; //会员价	
}

$goods['goods_price'] = format_price(get_discount_price($id, $ym_uid, $price, $discount));//优惠价

//SEO
$ym_title = $goods['name'].' - '.$ym_title;
$ym_keywords = trim($goods['keyword']) !='' ? $goods['keyword'] : $ym_keywords;
$ym_description = trim($goods['description']) !='' ? $goods['description'] : $ym_description;

//推荐
$diy_goods = get_diy_goods($diy_itemrem, $goods['cat_id']);
$remmend_goods=array();
if(count($diy_goods)>0)
{
	$remmend_goods=$diy_goods[0]['goods'];
}

//运费 
if($ym_express_type==1)
{
	$express_fee = get_max_express_fee(); 
}
if($ym_is_pickup==1)
{
	//$ym_express_picksite = get_cache('express_picksite');
	//print_r($ym_express_picksite);
}

//各星级评价数
$uid = intval($ym_uid);
$comment_starcount=get_comment_starcount($id, $uid);
$commnet_total =0;
foreach ($comment_starcount as $k => $v) {
	$commnet_total = $commnet_total+ $v;
}
$good_count = intval($comment_starcount['good']);
$mid_count = intval($comment_starcount['mid']);
$bad_count = intval($comment_starcount['bad']);
$good_pacent = $commnet_total==0 ? 0 : format_price($good_count/$commnet_total*100,2);  
$mid_pacent = $commnet_total==0 ? 0 : format_price($mid_count/$commnet_total*100,2);
$bad_pacent = $commnet_total==0 ? 0 : format_price($bad_count/$commnet_total*100,2); 

$total = $good_pacent+$mid_pacent+$bad_pacent;
if($commnet_total !=0 && $total<100) 
{
	$good_pacent = $good_pacent + 100 -$total ;
}

//评价
$start =0;
$num = 10;
//$comment = get_comment_list($id, '', 0, $start, $num, $uid);
$comment = get_all_comment_list('', 0, $start, $num, $uid);

//记录点击数
update_goods($id, array('click'=>($goods['click']+1) ));

$fav_user= get_fav_users($id); //收藏会员

//记录最近浏览
$history_tmp = isset($_COOKIE['his']) ? $_COOKIE['his'] : '';
$his = array_filter(explode('@', $history_tmp));
if(in_array($p, $his)==false)
{
	if(count($his) == 30)//todo 可配置
	{
		unset($his[0]);
	}
	array_push($his,$p);
	$history_tmp = implode('@', $his);
	set_cookie('his', $history_tmp, time()+15552000); 
}
 
if (isset($goods['item_tpl']) && file_exists(tpl.$ym_tpl."/item_".$goods['item_tpl'].".html")) {
	$pagetmp = $goods['item_tpl'];
}else{
	$pagetmp="item";
}
$history = get_history(10);


@include template($pagetmp, $ym_tpl."/");
$markcon = ob_get_contents();
@write_file(cache.$pagetmp."/".$cachefile,$markcon);



?>