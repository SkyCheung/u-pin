<?php
if (!defined('in_mx')) {exit('Access Denied');}


$res = array('err' => '', 'res' => '', 'data' => array()); 

switch ($act) {
	case 'getCats'://下级分类	
		ajax_getCats($pid, $res);
		break;
	case 'getTypes'://商品类型
		ajax_getTypes($id, $res);
		break;
	case 'getAttrs'://规格&属性
		ajax_getAttrs($id, $res);
		break;
	case 'getBrandCat'://品牌关联的分类
		ajax_getBrandCat($id, $res);
		break;
	case 'getTplDetails'://模版
		ajax_getTplDetails($id, $res);
		break;
	case 'del_TplDetails'://删除模版
		ajax_delTplDetails($id, $res);
		break;
	case 'del_goodsimg'://删除产品图片
		ajax_del_goodsimg($id,$goods_id, $res);
		break;				
	default:
		$res['err']="操作类型不正确。";
		die(json_encode($res));
		break;
}
 

function ajax_getCats($pid, $res)
 {
 	global $ym_cats;
 	if(!isset($pid) || trim($pid)=='')
 	{
 		$res['err']="获取不到父分类id";
 		die(json_encode($res));
	} 
	$row= array_query('pid', $pid, $ym_cats, false);
	$res['data']=$row;
 	die(json_encode($res));
 }
 
function ajax_getTypes($id, $res)
 {
 	if(!isset($id) || intval(trim($id))==0){$res['err']="获取不到id";die(json_encode($res));} 
	$row= getTypes($id);
	$rowattr = get_attribute($id);
	foreach ($rowattr as $k => $v) {
		$ar= explode(',', $v['value']);
		$rowattr[$k]['val']=$ar;
	}
	$row['attrs']=$rowattr;
	
	$res['data']=$row;
 	die(json_encode($res));
 }

function ajax_getAttrs($id, $res)
 {
 	if(!isset($id) || intval(trim($id))==0){$res['err']="获取不到id";die(json_encode($res));} 
	$row= get_attribute($id);
	foreach ($row as $k => $v) {
		$ar= explode(',', $v['value']);
		$row[$k]['vals']=$ar;
	}
	$res['data']=$row;
 	die(json_encode($res));
 } 
 
function ajax_getBrandCat($id, $res) {
	if(!isset($id) || intval(trim($id))==0){$res['err']="获取不到分类id";die(json_encode($res));} 
	$res['data']= get_brandCat($id);
 	die(json_encode($res));		
}

function ajax_getTplDetails($id, $res){
	if(!isset($id) || trim($id)=='')
	{
		$res['err']="获取不到id";
		die(json_encode($res));
	} 
	$filename=upload_tpldetails.urlencode($id).'.html';
	if(!file_exists($filename))
	{
		$res['err']="文件不存在";
		die(json_encode($res));
	}
	$res['data']= read_file ($filename);
 	die(json_encode($res));		
}
function ajax_delTplDetails($id, $res){
	if(!isset($id) || trim($id)=='')
	{
		$res['err']="获取不到id";
		die(json_encode($res));
	} 
	$filename=upload_tpldetails.urlencode($id).'.html';
	if(del_file($filename)==false)
	{
		$res['err']= '删除失败';
	}	
	
 	die(json_encode($res));		
} 
function ajax_del_goodsimg($id, $goods_id=0, $res){
	if(!isset($id) || trim($id)=='')
	{
		$res['err']="获取不到图片地址";
		die(json_encode($res));
	}  

	if(isset($goods_id) && intval(trim($goods_id))!=0)
	{
		del_goodsimg($goods_id,$id);
	}
	if(del_file($id)==false || del_file(get_smallName($id))==false)
	{
		$res['err']= '删除失败';
	}	
	
 	die(json_encode($res));		
} 
 
?>