<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*优惠券*/
 
 
function add_coupon($data =array())
{
	global $db;
	$db->insert('coupon', $data);
	return $db->lastinsertid();
} 

/**保存优惠券适用的品类/商品
 @param int $cid 优惠券id
 * */
function add_coupon_item($data =array(), $cid)
{
	global $db;
	if(count($data)==0){return;}
	$sql = ' ';
	foreach ($data as $k => $v) {
		$sql .= "(".$cid.",'".$v."'),";
	}
	$sql = "insert into". $db->table("coupon_item")."(cid,item_id) values" . rtrim($sql,","); 
	return $db->query($sql);
} 

//编辑
/*function update_coupon($data, $id)
{
	global $db;
	return $db->update('coupon', $data, array('id'=>intval($id)));
}*/

/**删除优惠券适用的品类/商品
 @param int $cid 优惠券id
 * */
function del_coupon_item($cid=0, $item_id=0)
{
	global $db;
	
	$where = array();
	if($cid !=0)
	{
		$where['cid']= intval($cid);
	}
	if($item_id !=0)
	{
		$where['item_id']= intval($item_id);
	}
	if($cid ==0 && $item_id == 0){
		return false;
	}

	return $db->delete('coupon_item', $where);
} 

/*获取优惠券*/
function get_coupon($where=array(), $startI=0, $pagenum=null)
{
	global $db;
	$limit ='';
	if($pagenum != null)
	{
		$limit = $startI.','. $pagenum;
	}
	$row = $db->fetchall('coupon','*', $where,'id desc',$limit);
	foreach($row as $k => $v) {
		$row[$k]['date_start'] = date("Y-m-d H:i", $v['date_start']);
		$row[$k]['date_end'] = date("Y-m-d H:i", $v['date_end']);
		$row[$k]['limit_start'] = date("Y-m-d H:i", $v['limit_start']);
		$row[$k]['limit_end'] = date("Y-m-d H:i", $v['limit_end']);
	}
	
	return $row;
} 


?>