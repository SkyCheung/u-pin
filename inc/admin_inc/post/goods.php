<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'goods');//权限检测

require './inc/lib/admin/member.php';
  
if (trim($act)==""){message("操作类型不正确。");} 
if (trim($name)==""){message("请填写商品名称。");}
if (strlen(trim($name))>150){message("请控制在150个字符内");}
if (intval($cat_id)==0){message("请选择商品分类");}

$row=$db->fetch('goods','*', array("name"=>trim($name)));
//if ($row){message("商品名称已存在，请更换。");} 

//商品
$goods=array('name' => trim($name),
	'subname'=> trim($subname),
	'cat_id'=> intval($cat_id),
	'brand_id'=> intval($brand),
	'virtype'=> intval($virtype),
	'price'=> floatval($price),
	'marketprice'=> floatval($marketprice),
	'costprice'=> floatval($costprice),
	'sn'=> trim($sn),
	'barcode'=> trim($barcode),
	'unit'=> trim($unit),
	'number'=> intval($number),
	'warnnum'=> intval($warnnum),
	'weight'=> intval($weight),
	'status'=> intval($status),
	'virtualnum'=> intval($virtualnum),
	'point'=> intval($point),
	'comment_reward'=> floatval($comment_reward),
	'img'=> trim($img),
	'thumb'=> trim($thumb),
	'addtime'=> time(),
	'details'=> trim($details),
	'service'=> trim($service),
	'details_m'=> trim($details_m),
	'service_m'=> trim($service_m),
	'hot'=> intval($hot),
	'new'=> intval($new),
	'best'=> intval($best),
	'recommend'=> intval($recommend),
	'freeshipping'=> intval($freeshipping),//包邮
	'keyword'=> trim($c_keyword),
	'description'=> trim($description),
	'keyword'=> trim($keyword), 
	'sort'=> intval($c_sort),
	'goods_type'=> intval($goods_type)
);

if($goods['status']==goods_up || $goods['status']== goods_settimeup)
{	
	if($act=='add' || ($act=='edit' && $goods['status']==goods_settimeup) || ($act=='edit' && $goods['status']==goods_up && strtotime($uptime)>time()))
	{ 
		$goods['uptime'] =intval($status)==goods_settimeup && strtotime($uptime) ?strtotime($uptime): time();
		$goods['status']= goods_up;
	}
}
elseif($goods['status']==goods_down && $act=='edit')
{
	$goods['downtime'] = time();
}
if(trim($downtime)!='')
{
	$goods['downtime'] = strtotime(trim($downtime));
}

//组图
$imgslist=array();
$len=count($imgs);
for ($i=0; $i < $len; $i++) { 
	$imgslist[$i]['img'] = $imgs[$i];
	$imgslist[$i]['descipt'] = $descipt[$i];
	$imgslist[$i]['thumb'] = $thumbs[$i];
	resize_img($imgs[$i], 'goodsimg');
}
$goods['imgs'] =json_encode($imgslist);

//规格组图
$specs = array();
$specs['spec_id']=trim($spec_id);
$specs['spec_name']=trim($spec_name); 
$spec_val=array();
if(trim($specs_list)!='')
{
	$specs_list=rtrim($specs_list, ',');
	$list=explode(',', $specs_list);
	foreach ($list as $k => $v) {				
		$tmp_descipt = $_POST["descipt_".$v];
		$tmp_imgs = $_POST["imgs_".$v];
		$tmp_thumbs = $_POST["thumbs_".$v];
				
		$spec_imgs=array();	
		if(isset($tmp_imgs))
		{
			$l=count($tmp_imgs);
			$n=1;
			for ($i=0; $i < $l; $i++) { 
				$spec_imgs[$n]['descipt']=$tmp_descipt[$i];
				$spec_imgs[$n]['img']=$tmp_imgs[$i];
				$spec_imgs[$n]['thumb']=$tmp_thumbs[$i];
				resize_img($tmp_imgs[$i], 'goodsimg');
				$n++;
			}
		}
		$spec_imgs[0]['descipt']='';
		$spec_imgs[0]['img']= trim($_POST["spec_img_".$v]);
		$spec_imgs[0]['thumb']= trim($_POST["spec_thumb_".$v]);
		sort($spec_imgs);
		$spec_val[$k]['value']=trim($v);
		$spec_val[$k]['imgs']= $spec_imgs;
	}
}
$specs['spec_val']=$spec_val; 
$goods['specs'] =json_encode($specs); 

if($act=='add')
{
	$db->insert('goods', $goods);
	$id= $db->lastinsertid();
	$db->update('goods', array('code' =>jcode($id)), array('goods_id' => $id));
}
elseif($act=='edit')
	{
		$db->update('goods', $goods, array('goods_id'=>intval($goods_id)));
	}

//扩展分类
if(trim($extcat_ids)!='')
{
	$sql='insert into '.$db->table('goods_cat').'(goods_id,cat_id) values';  
	$cat_ids = explode(',', $extcat_ids); 
	foreach ($cat_ids as $k => $v) {
		$sql.='('.$id.',"'.trim($v).'"),';
	}
	if($cat_ids!='' && count($cat_ids)>0)
	{
		if($act=='edit')
		{
			$db->delete('goods_cat', array('goods_id'=>$id)); //先删除已有的
		}		
		$db->query(rtrim($sql,','));
	}
}
else {
		if($act=='edit')
		{
			$db->delete('goods_cat', array('goods_id'=>$id)); //先删除已有的
		}	
}

//会员价格
/*$sql='';
foreach ($_POST as $k => $v) {
	if(strpos($k, "member_price_") ===0)
	{
		$sql.="(" .$id .",0,". ltrim($k, "member_price_").",".floatval($v)."),";
	}	
}
if($sql !='')
{
	$sql= "INSERT INTO ".$db->table('member_price')."(`goods_id`,`sku_id`, `grade_id`, `price`) VALUES".rtrim($sql,',');
	if($act =='edit')
	{
		 del_member_price($id); //先删除已有的
	}
	$db->query($sql);
}*/

//商品sku
//$grade = get_grade(); //会员等级
$sql = '';	
for ($i=0; $i < $specs_num; $i++) {
	if(trim($_POST["number_".$i])!= '')
	{		
		$arr=array();
		$arr['goods_id']= $id;
		$arr['attr_ids']=!isset($_POST["ids_".$i])?'': trim($_POST["ids_".$i]);
		$arr['values']=!isset($_POST["name_".$i])?'': trim($_POST["name_".$i]);
		$arr['number']=intval($_POST["number_".$i]);
		$arr['price']= floatval($_POST["price_".$i]);
		$arr['marketprice']=floatval($_POST["marketprice_".$i]);
		$arr['costprice']=floatval($_POST["costprice_".$i]);
		$arr['weight']=intval($_POST["weight_".$i]);
		$arr['sn']= !isset($_POST["sn_".$i])?'': trim($_POST["sn_".$i]);
		$arr['barcode']=!isset($_POST["barcode_".$i])?'': trim($_POST["barcode_".$i]);
 		
 		$sql.="(" . $db->formatFields($arr)."),";
		/*$db->query("insert into ".$db->table('goods_spec')."(goods_id, attr_ids, `values`, number, price, marketprice, costprice, weight, sn,barcode) values(" . $db->formatFields($arr).")");
		$sku_id = $db->lastinsertid();
		
		//商品sku的会员价格
		$sql = '';			
		foreach ($grade as $k => $v) {
			$tmp_price = $_POST["spec_member_price_".$i."_" .$v['grade_id']];
			if(!empty($tmp_price))
			{
				$sql.="(" .$id .",".$sku_id.",". $v['grade_id'].",".floatval($tmp_price)."),";
			}
		}
		if($sql !='')
		{
			$db->query("INSERT INTO ".$db->table('member_price')."(`goods_id`,`sku_id`, `grade_id`, `price`) VALUES".rtrim($sql,','));
		}*/
	}
}
if($act=='edit')
{
	$db->delete('goods_spec', array('goods_id'=>$id)); //先删除已有的
}
if(isset($specs_num) && intval(trim($specs_num))>0 && $sql!='')
{
	$db->query('insert into '.$db->table('goods_spec').'(goods_id, attr_ids, `values`, number, price, marketprice, costprice, weight, sn,barcode) values'. rtrim($sql,','));
}

//商品属性
$sql=''; 
for ($i=0; $i < $attr_num; $i++) {
	$attr_val= $_POST["values_".$i];
	if(isset($attr_val) && $attr_val!= '')
	{
		$arr=array();
		$arr['goods_id']=$id;
		$arr['attr_ids']= !isset($_POST["attr_ids_".$i])?'': trim($_POST["attr_ids_".$i]);
		$arr['values']= is_array($attr_val)?implode(',', $attr_val): trim($attr_val); 
		$sql.="(" . $db->formatFields($arr)."),";
	}
} 
if(isset($attr_num) && intval(trim($attr_num))>0 && $sql!='') 
{
	$sql='insert into '.$db->table('goods_attr').'(goods_id, attr_ids, `values`) values' . rtrim($sql,','); 
	if($act=='edit')
	{
		$db->delete('goods_attr', array('goods_id'=>$id)); //先删除已有的
	}
	$db->query($sql);
}

	
message("保存成功",'/admin.html?do=goods');  


?>