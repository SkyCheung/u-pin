<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'diy');//权限检测

if (intval($id)==0){message("获取编号失败");}

$c_title = addslashes($c_title);
if (trim($c_title)==""){message("请输入标题名称。");}

$cus_type = intval($cus_type);
$c_body =str_replace("script","", $c_body);

if(!preg_match('/^[a-z]|[a-z]([a-z0-9]|[-_]){1,20}$/', $c_name)) {  
	message('变量名称只能英文、数字、- 和  _ ，并且英文开头。');  
}

switch ($diy_type) {
	case 'goods':
		if($cat_ids=='')
		{
			message('请选择商品分类');  
		}
		if (intval($c_num)==0){
			message('请填写引用数量！');  
		}
		$c_body='';
		$recommends=json_encode(array('hot'=>intval($hot),'new'=>intval($new),'best'=>intval($best),'recommend'=>intval($recommend),'freeshipping'=>intval($freeshipping),'discount'=>intval($discount)));
		if($discount ==1)
		{
			$ids = $promotion_ids;
		}
		break;
	case 'news':
		$cat_ids=$c_toid;
		$c_type='';
		if (intval($c_num)==0){
			message('请填写引用数量！');
		}
		$c_body='';
		$include_son=0;
		$is_eachnum = 0;
		$is_childnum =0;
		$recommends=json_encode(array('recom'=>intval($recom),'haspic'=>intval($haspic),'hits'=>intval($hits)));
		break;
	case 'coupon':
		$cat_ids = '';
		$include_son=0;
		$is_eachnum = 0;
		$is_childnum =0;
		$recommends=json_encode(array());
		$c_num=0;
		$ids = $coupon_ids;
		break;	
	case 'custom':
		$cat_ids = '';
		$include_son=0;
		$is_eachnum = 0;
		$is_childnum =0;
		$recommends=json_encode(array());
		$c_num=0;
		if($cus_type==1)
		{
			$c_body = strip_tags($c_body);		
		}
		elseif($cus_type==2) {
			preg_match_all("/<img([^>]*)\s*src=('|\")([^'\"]+)('|\")/i", $c_body, $matches);	 
			$c_body = json_encode($matches[3]);	
		}
		break;		
	
	default:		
		break;
}

$row=$db->fetch('diy', 'id',"id<>".$id." and name='".trim($c_name)."'");
if($row){message("变量名称已存在，请更换。");}

$db->update('diy', array('diy_type' => trim($diy_type),'cat_ids' => trim($cat_ids),'recommends' => $recommends,'num' => intval($c_num),'`index`' => intval($c_index), 'title' => trim($c_title),'include_son'=>intval($include_son),'is_eachnum'=>intval($is_eachnum),'is_childnum'=>intval($is_childnum), 'client' => trim($client) , 'name' => trim($c_name),'cus_type'=>$cus_type, 'body' => $c_body, 'promotion_ids' => trim($promotion_ids),'ids' => trim($ids)), array('id' => $id));
 
update_diy();

message("提交成功，数据已更新。",'/admin.html?do=diy');


?>