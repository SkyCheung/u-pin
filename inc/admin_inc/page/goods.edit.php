<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'goods.edit');//权限检测

$id = intval($id);
if($id==0)
{
	message("获取商品编号失败");
}
 
require './inc/lib/admin/member.php'; 
 
//模板
$tplpath = upload_tpldetails; 
$tplrow=getfiles($tplpath);
foreach ($tplrow as $k => $v) {
	$tplrow[$k]=urldecode(substr($v, 0,strrpos($v, '.'))) ;
}

$types = get_types();//商品类型

//品牌
$brand=$db->fetchall('brand','*','',"sort asc");
$jsonbrand=json_encode($brand); 

//商品
$row = get_goods($id); 
foreach ($row as $k => $v) {
	$$k = $v;	
}

/*
$member_grade = get_grade(); //会员等级
$member_grade_sku = array();
//会员价格
$member_price = get_member_price($id); //print_r($member_price);

foreach($member_price as $key => $val){
	foreach($member_grade as $k => $v){
		if($val['grade_id'] == $v['grade_id'])
		{
			if($val['sku_id']==0)
			{
				$member_grade[$k]['price'] = $val['price'];				
			}
			else{
				$member_grade_sku[$val['sku_id']][$val['grade_id']]['sku_id'] =	$val['sku_id'];
				$member_grade_sku[$val['sku_id']][$val['grade_id']]['grade_id'] =	$val['grade_id'];
				$member_grade_sku[$val['sku_id']][$val['grade_id']]['price'] = $val['price'];
			}		
		}
	}
}
$member_grade_sku=json_encode($member_grade_sku);*/	

//分类
$cat = array_query('pid', 0, $ym_cats, false); 

//扩展分类
$extcat= get_goodsCat($id); 

//规格
$goods_specs= get_goodsSpecs($id); 
$goods_spec= json_encode($goods_specs) ; 
if($goods_specs && count($goods_specs)>0)
{	
	$spec_ids= json_encode(explode(',', $goods_specs[0]['attr_ids']));
}//print_r($member_grade_sku);

//属性
$goods_attrs= json_encode(get_goodsAttr($id)); 

//图片
$imglist= json_decode($imgs,true); 

$cbk_uptime[$row['status']]="checked='checked'"; 
if($uptime>time())
{
	$c_uptime= date("Y-m-d H:i",$uptime);
	$cbk_uptime[3]="checked='checked'";
}
if($downtime>time())
{
	$c_downtime= date("Y-m-d H:i",$downtime);
}

$cbk_virtype[$row['virtype']]="checked='checked'";
$cbk_hot[$row['hot']]="checked='checked'";
$cbk_new[$row['new']]="checked='checked'";
$cbk_best[$row['best']]="checked='checked'";
$cbk_recommend[$row['recommend']]="checked='checked'";
$cbk_freeshipping[$row['freeshipping']]="checked='checked'";

?>