<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*商品列表页*/
require_once  './inc/lib/promotion.php';
if (file_exists(cache_data."diy.php")) {
	include(cache_data."diy.php");
}
$cid =isset($cid) ? intval($cid) :0; //分类
$bid =isset($bid) ? intval($bid) :0; //品牌
$qid =isset($qid) ? intval($qid) :0; //优惠券id

$db=dbc();
if($is_ajax ==0)
{
	$nav = get_nav(); //导航
	$nav_footer = get_nav('bot'); //底部导航
	$cats = get_catTree(); //分类树
	$help = get_help(); //帮助
	$crumbs_nav = get_crumbs_nav('goods', $cid); //面包导航

	//品牌
	$brand = get_cat_brand($cid);
	if($brand)
	{
		foreach ($brand as $k => $v) {
			$brand[$k]['url'] = build_url('bid', $v['id']);
		}
		array_unshift($brand, array('name'=> '全部', 'url' => build_url('bid', '')));
	}
}	

$catinfo = get_catInfo($cid); //分类信息
$catinfo = $catinfo[0];

if(count($catinfo)==0)
{
	$ym_title= '所有分类 - '.$ym_title;
}
else {
	$ym_title = $catinfo['name'].' - '.$ym_title;
	$ym_keywords = $catinfo['keywords'];
	$ym_description = $catinfo['description'];
}

//价格
$pr= $pr=='' ? '全部': trim($pr);
$price = explode(',', $catinfo['grade']);
$price_grade = array();
$price_grade[0]['name'] = '全部';	
$price_grade[0]['url'] = build_url('pr', '');
foreach ($price as $k => $v) {
	$price_grade[$k+1]['name'] = $v;
	$price_grade[$k+1]['url'] = build_url('pr', $v);
}

//属性
$child_ids = get_childIDs($cid);
$childs= get_catInfo(explode(',', $cid));
$type_ids = array(); 
foreach ($childs as $k => $v) {
	$type_ids[$k] = $v['type_id'];
} 
$attr = get_attrs($type_ids); 

$at_arr= explode('@', $at);
$at_param = array(); //选择的属性
foreach ($at_arr as $k => $v) {
	$at_tmp =explode('_', $v);
	if(count($at_tmp)==2)
	{
		$at_param[$at_tmp[0]]['val'] = $at_tmp[1];
	}
}
if($attr)
{
	foreach ($attr as $k => $v) { //&at=72_红色@71_1000克
		$value = explode(',', $v['value']);
		$attr_value = array(); 	
		$attr_value[0]['name'] = '全部';	
		$attr_value[0]['url'] = build_url('at', $v['id'].'_');
		$attr_value[0]['cur'] = (count($at_param)==0 || $at_param[$v['id']]['val']=='' )? '1' : '0';
		foreach ($value as $key => $val) {
			$attr_value[$key+1]['name']= $val;
			$attr_value[$key+1]['url']= build_url('at', $v['id'].'_'.$val);	
			$attr_value[$key+1]['cur']= $at_param[$v['id']]['val']==$val ? '1' : '0';
		}	
		$attr[$k]['value'] = $attr_value;
		if($at_param[$v['id']]['val']!='')
		{
			$at_param[$v['id']]['name']=$v['name'];
			$at_param[$v['id']]['type']=$v['type'];
			$at_param[$v['id']]['name']=$v['name'];
			$at_param[$v['id']]['url']=$attr_value[0]['url'];
		}
	}	
}	

 	//子分类	
	$cat_child_tmp = get_children($cid);  //子分类
	$cat_child = array(); 	
	
	/*$cat_child[0]['id'] = '';
	$cat_child[0]['name'] = '全部';	
	$cat_child[0]['url'] = build_url('son', '');*/
	$url = parse_url($_SERVER['REQUEST_URI']);
	foreach ($cat_child_tmp as $k => $v) {		
		//$v['url'] = build_url('son', $v['id']);
		$v['url'] = $v['urlname'].'.html'.(!isset($url['query'])?'':'?' .$url['query']);
		$cat_child[] = $v;
	} 
	
	$sort_list = array('a1'=>'addtime desc','s1'=>'salenum desc', 'p1'=>'price asc', 'p2'=>'price desc');
	$sort = (isset($sort) && trim($sort)!='') ? trim($sort) : 'a1';
	$order = $sort_list[trim($sort)]; 

	$cur[$sort]='class="red"' ;
	$sort_add_time = build_url('sort', 'a1');
	$sort_sale = build_url('sort', 's1');	
	$sort_price = build_url('sort', trim($sort)=='p2' ? 'p1': 'p2');

	$page=intval($page)==0 ? 1 : intval($page);
	if($num && intval($num) > 0)
	{
		$pagenum= $num; 
	}
	else {
		$pagenum= intval($catinfo['num'])==0 ? 20 : $catinfo['num']; 
	}
	$startI = $page * $pagenum - $pagenum;
	
	//过滤条件
	$condition='';
	if(isset($word) && $word!='') //分类
	{
		$condition .=" and g.name like '%".addslashes($word)."%'";
	}
	//if(intval($son)>0) //分类
	{
		$condition .=" and (g.cat_id in(".$child_ids.")  or g.goods_id ". get_extend_goods($cid) .")";
	}
	if($bid != 0)
	{
		$condition .=" and g.brand_id=". $bid;
	}
	
	//价格
	if(isset($pr) && trim($pr)!='') 
	{
		$price_arr= explode('-', $pr);
		$price_m = intval($price_arr[0]);
		$price_l = intval($price_arr[1]);
		if($price_l >0 && count($price_arr)==2)
		{
			$condition .=' and price>='. $price_m . ' and price<='.$price_l;			
		}
		elseif($price_m !=0)
		{
			$condition .=' and price>='.intval($price_m);
		}
		elseif($price_l!=0)
		{
			$condition .=' and price<='.intval($price_l);
		}		
	}
	
	//属性  格式如71_3000克@63_1200@44_16g
	if(count($at_param)>0) 
	{
		$wh_attr = '';
		$wh_spec ='';
		$ids_attr =array();
		$ids_spec =array();
		foreach ($at_param as $k => $v) {
			if($v['type']== 1)
			{
				$wh_spec .= " and (find_in_set('". $k."', b.`attr_ids`) and  find_in_set('". $v['val']."', b.`values`)) ";
				$ids_spec[]=$k;
			}
			else {
				$wh_attr .= " and (find_in_set('". $k."',a.`attr_ids`) and find_in_set('". $v['val']."',a.`values`))";
				$ids_attr[]=$k;
			}
		}
			
		if($wh_attr !='' && $wh_spec !='')
		{
			$condition .=" and g.goods_id in(SELECT DISTINCT(goods_id) FROM ( SELECT a.goods_id, concat(a.attr_ids,',',b.attr_ids) attr_ids, concat(a.`values`,',',b.`values`)  val FROM ( select goods_id, GROUP_CONCAT(distinct attr_ids) attr_ids, GROUP_CONCAT( `values`) `values` from " 
			. $db->table('goods_attr') . " where 1 and attr_ids in(". implode(',', $ids_attr).") group by goods_id ) a join ( select goods_id, GROUP_CONCAT( distinct attr_ids) attr_ids, GROUP_CONCAT( `values`) `values` from " 
			. $db->table('goods_spec') 
			. " where 1 and attr_ids in(". implode(',', $ids_spec).") group by goods_id ) b on a.goods_id=b.goods_id " . $wh_spec. $wh_attr. ") t )";
		}
		else {
			if($wh_attr !='')
			{ 
				$wh_attr ="SELECT DISTINCT(goods_id) FROM (select goods_id, GROUP_CONCAT( attr_ids) attr_ids, GROUP_CONCAT( `values`) `values` from " . $db->table('goods_attr') . " a where 1 and attr_ids in(". implode(',', $ids_attr).") group by goods_id) a where 1 ". $wh_attr;						
			}
			if($wh_spec !='')
			{
				$wh_spec ="SELECT DISTINCT(goods_id) FROM (select goods_id, GROUP_CONCAT( attr_ids) attr_ids, GROUP_CONCAT( `values`) `values` from " . $db->table('goods_spec') . " b where 1 and attr_ids in(". implode(',', $ids_spec).") group by goods_id) b WHERE 1 ". $wh_spec;
			}
			$condition .=" and g.goods_id in(".$wh_attr .  $wh_spec.  " )";
		}		
	}	 
	
	//优惠券
	if($qid !=0)
	{
		require  './inc/lib/coupon.php';
		$cids = get_coupon_itemids($qid);
		if($cids !='')
		{
			$condition .=" and ".$cids;
		}
		
		$coupon = get_couponinfo($qid);		
	}
	
	$count = $db->get_count($db->table('goods')." g JOIN " . $db->table('category') . " c ON g.cat_id = c.id where g.status=". goods_up . " and uptime<=".time().$condition);
	if ($count>0)
	{
		if($is_ajax == 0){
			$pages = getPages($count, $page, $pagenum);	
		}
		$goods = get_goods_list($condition, 'g.*', $order, $startI , $pagenum,1,1);
	}
    else {
    	$goods='';
    }

	if($is_ajax==1){
		$res = array('err' => '', 'res' => '', 'data' => array());
		$res['count'] = $count;
		$res['data'] = $goods;
	
		die(json_encode_yec($res));
	} 

?>