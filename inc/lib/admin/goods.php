<?php
if (!defined('in_mx')) {exit('Access Denied');}


//获取商品类型
function get_types($id = '') {
	global $db;
	$where = array();
	if ($id != '') {
		$where['id'] = $id;
	}
	return $db -> fetchall('type', '*', $where, 'sort asc');
}

//同级分类
function get_siblingsCat($id = 0) {
	global $db;
    $where='';
	if(intval($id) != 0)
	{
		$where= " and b.id='".$id."'";
	}
    return $db -> queryall("SELECT a.*,ifnull(b.`name`, '顶级分类') as parent_name FROM ".$db->table('category')." a left join ".$db->table('category')." b on a.`pid`=b.`id` where 1 $where order by a.`sort` asc");
}

//商品分类
function get_category($pid = 0,$istree=false, $status=1, $child = 'child') {
	global $db;
	$where = '';
    if($status ==0 || $status ==1 )
	{
		$where .=' and a.status='.$status;
	}
    $rows = $db -> queryall("SELECT a.*,ifnull(b.`name`, '顶级分类') as parent_name,ifnull(c.`name`, '') as type_name FROM ".$db->table('category')." a left join ".$db->table('category')." b on a.`pid`=b.`id` left join ".$db->table('type')." c on a.type_id=c.id where 1 $where order by a.`sort` asc");

    if($istree)
    {
    	return getTree($rows, $pid, 'id', 'pid', $child);
    }
	else {
		return $rows;
	}   
}

//获取品牌关联的分类
function get_brandCat($catid)
{
	global $db;
	return $db->queryall("select * from ".$db->table('brand')." where FIND_IN_SET($catid, `cat_ids`)");	 
}

//获取扩展分类
function get_goodsCat($goods_id=0, $cat_id=0)
{
	global $db;
	$where= '';
	if(intval($goods_id) != 0)
	{
		$where=" and a.goods_id=".$goods_id;
	}
	if($cat_id != 0)
	{
		$where=" and a.cat_id=". $cat_id;
	}

	return $db->queryall("SELECT a.*,b.name as cat_name FROM  ".$db->table('goods_cat')." a JOIN ".$db->table('category')." b ON a.cat_id = b.id WHERE 1 $where");	 
}

/**
 * 检查分类名称是否已经存在
 * @param   string      $name       分类名称
 * @param   integer     $pid     上级分类
 * @param   integer     $except_id        排除的分类ID
 */
function check_catname($name, $pid, $except_id = 0)
{
	global $db;
	$row = $db->rowcount('category', "pid = '$pid' AND name = '".addslashes($name)."' AND id<>'$except_id'");
   
    return intval($row) > 0;
}

/*检查分类文件名称是否已经存在*/
function check_urlname($urlname, $except_id = 0)
{
	global $db;
	$row = $db->rowcount('category', "urlname = '".addslashes($urlname)."' AND id<>'$except_id'");
   
    return intval($row) > 0;
}

//获取商品的规格
function get_goodsSpecs($goods_id=0)
{
	global $db;
	if(intval($goods_id) == 0)
	{
		return false;
	}

	return $db->fetchall('goods_spec', '*',  array('goods_id' => $goods_id));	 
}

//获取商品的属性
function get_goodsAttr($goods_id=0)
{
	global $db;
	if(intval($goods_id) == 0)
	{
		return false;
	}

	$row= $db->fetchall('goods_attr', '*',  array('goods_id' => $goods_id));
	
	foreach ($row as $k => $v) {
		$row[$k]["val"] = explode(',', $v['values']) ; 
	}
	return $row;	 
}

//获取商品的属性
/*function get_goodsAttr($type_id=0)
{
	global $db;
	$row= $db->queryall("select a.id,a.value,g.values from ".$db->table('attribute')." a left join ".$db->table('goods_attr')." g on a.id=g.attr_ids where type_id=". $type_id.' order by a.`sort`');
	
	foreach ($row as $k => $v) {
		$row[$k]["val"] = explode(',', $v['value']); 
		$row[$k]["goods_val"] = explode(',', $v['values']);
	} //print_r($row);
	
	return $row;	 
}*/

//属性
function get_attribute($type_id=0)
{
	global $db;
	return $db->fetchall('attribute', '*',  array('type_id' => $type_id), 'id asc');
}

/*获取商品列表*/
function get_goodsList($where='', $startI=0, $pagenum=12)
{
	global $db;		
	if($where !='')
	{
		$where= 'where '.$where;
	}
	//print "SELECT g.*,c.`name` as cat_name FROM ".$db->table('goods')." g JOIN ".$db->table('category')." c ON g.cat_id = c.id  $where order by g.sort asc,g.goods_id desc LIMIT ".$startI." , ".$pagenum;
	return $db->queryall("SELECT g.*,c.`name` as cat_name FROM ".$db->table('goods')." g JOIN ".$db->table('category')." c ON g.cat_id = c.id  $where order by g.sort asc,g.goods_id desc LIMIT ".$startI." , ".$pagenum); 
}


//更新商品状态(支持批量逻辑删除商品)
function update_goodsStatus($goods_ids, $val)
{
	if($goods_ids =='' || isNumComma($goods_ids)==false)
	{
		return false;
	}
	global $db;
	$status = array();
	$status['status'] = intval($val);
	if(intval($val) == goods_up)
	{
		$status['uptime'] = time(); 
	}
	elseif (intval($val) == goods_down) {
		$status['downtime'] = time(); 
	}
	$db -> update('goods', $status,  "goods_id in(". $goods_ids.")");	
}

/*物理删除商品 ,不可以恢复 
$goods_ids为    1,2,3*/
function del_goods($goods_ids ='' )
{
	if($goods_ids =='' || isNumComma($goods_ids)==false)
	{
		return false;
	}
	global $db;//print ($goods_ids);
	$rows = get_goodsList("g.goods_id in(". $goods_ids .")",0,99999999999);
	$db -> delete('goods', "goods_id in(". $goods_ids .")");
	$db -> delete('goods_cat', "goods_id in(". $goods_ids .")");
	$db -> delete('goods_spec', "goods_id in(". $goods_ids .")");
	$db -> delete('goods_attr', "goods_id in(". $goods_ids .")");
	
	foreach ($rows as $i => $row) {			
		//删除图片	
		if(trim($row['img'])!='' && file_exists(trim($row['img'])))
		{
			@del_file(trim($row['img']));
			if(file_exists(trim($row['thumb'])))
			{
				@del_file(trim($row['thumb']));
			}
		}
		$specs = json_decode($row['specs'],true);
		if(count($specs['spec_val'])> 0) 
		{
			foreach ($specs['spec_val'] as $key => $val) {
				foreach ($val['imgs'] as $k => $v) {
					if(file_exists($v['img']))
					{
						@del_file($v['img']);
					}
				}
			}
		}
		$imgs=json_decode($row['imgs'],true);
		foreach ($imgs as $k => $v) {
			if(file_exists($v['img']))
			{
				@del_file($v['img']);
			}
		}
	}	
}

//删除商品某个图片
function del_goodsimg($goods_id=0, $src='')
{
	if(intval($goods_id)==0 || $src=='')
	{
		return false;
	}
	global $db;
	
	$row=$db->fetch('goods','*',array('goods_id'=>$goods_id));
	$specs = json_decode($row['specs'],true);
	if($row['img']== $src)
	{
		$db -> update('goods', array('img' => '', 'thumb'=>''), array('goods_id' => intval($goods_id)));
	}
	elseif(count($specs['spec_val'])> 0) 
	{
		foreach ($specs['spec_val'] as $key => $val) {
			foreach ($val['imgs'] as $k => $v) {
				if($v['img'] == $src)
				{
					unset($specs['spec_val'][$key]['imgs'][$k]);
					sort($specs['spec_val'][$key]['imgs']);
					$db -> update('goods', array('specs' => json_encode($specs)), array('goods_id' => intval($goods_id)));
					return false;
				}
			}
		}
	}
	else
	{
		$imgs=json_decode($row['imgs'],true);
		foreach ($imgs as $k => $v) {
			if($v['img']==$src)
			{
				unset($imgs[$k]);
				sort($imgs);
				$db -> update('goods', array('imgs' => json_encode($imgs)), array('goods_id' => intval($goods_id)));
				return false;	
			}
		}
	}
}

function del_member_price($goods_id=0, $grade_id=0)
{
	global $db;
	$where = array();
	if($goods_id !=0)
	{
		$where['goods_id'] = intval($goods_id);
	}
	elseif($grade_id !=0)
	{
		$where['grade_id'] = intval($grade_id);
	}
	else{
		return false;
	}
	
	return $db->delete('member_price', $where);
}

?>