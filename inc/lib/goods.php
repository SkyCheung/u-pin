<?php
if (!defined('in_mx')) {exit('Access Denied');}

function get_childIDs($cat_id = 0, $res_arr=false, $level= 0)
{
	global $ym_cat;
	if(!isset($ym_cat))
	{
		$ym_cat = get_category(0, false, 1);
	}	
	unset($GLOBALS['arr_ids']);
	$ids = get_catIds($ym_cat, $cat_id, $level);
	if(count($ids)==0){return $cat_id;}
	sort($ids);
	$list= joinString(array_unique($cat_id==0 ? $ids: array_merge(array($cat_id), $ids)), ',' ,$res_arr);
	$list = str_replace("'", '', $list);
	$list = trim($list)=='' ? 0 : $list;
	return $list;
}

/**获取子分类id
 *@param $arr 树形数组
 * */ 
function get_childIDs_tree($arr, $child='child'){
	global $ids;
	foreach ($arr as $rs){
		$ids[] = $rs['id'];
		if ($rs[$child]){
			foreach ($rs[$child] as $v){
				$ids[] = $v['id'];
				if ($v[$child]) {
					get_childIDs_tree($v[$child]);
				}
			}
		}
	}
	sort($ids);
	return implode(',', $ids);
}

//获取子分类的id  有bug
function get_catIds2($rows, $root = 0, $level=99, $id = 'id', $pid = 'pid') {
	$ids = array(); //print $root =2;
	if (is_array($rows)) {
		foreach ($rows as $key => $item) {
			$parentId = $item[$pid];
			if ( ($root == $parentId || in_array($parentId, $ids)) && $item['level']<= $level) {
				$ids[] = $item[$id]; 
			}
		}
	}
	
	return $ids;
}

//获取子分类的id 
function get_catIds($rows, $pid = 0, $level = 0, $id_name = 'id', $pid_name = 'pid')
{
    global $arr_ids ; 
    if(empty($rows)) {return array();}
    $level++;
    foreach($rows as $key => $value)
    {
        if($value[$pid_name] == $pid)
        {
            $value[ 'level'] = $level;
            $arr_ids[] = $value[$id_name];
            unset($rows[$key]); //移除当前节点数据，减少已无用的遍历
            get_catIds($rows, $value[$id_name], $level); 
        }
    } 
    return $arr_ids;
}

//获取下一级子分类
function get_children($cat_id = 0)
{
	global $ym_cats; 
	return array_query('pid', intval($cat_id), $ym_cats);	
}

//获取子分类
function get_child($rows, $root = 0, $level=99, $id = 'id', $pid = 'pid', $child = 'child') {
	$tree=array();
	if (is_array($rows)) {
		foreach ($rows as $key => $item) {
			$parentId = $item[$pid];
			if ($root == $parentId && $item['level']<=$level) {
				$tree[] = $item[$id]; 
			} else if (isset($rows[$parentId]) && $item['level']<= $level){
				$tree[] = $item[$id]; 
			}
		}
	}
	
	return $tree;
}

/**获取商品列表
 * @param @where 前面须加 and
 * */
function get_goods_list($where='',$field ='', $sort = '', $startI=0, $num=10, $is_up=1, $is_formated=0)
{	
	global $db, $ym_uid,$doc;
	if(!function_exists('get_discount_price'))
	{
		if($doc){
			require "$doc/inc/lib/promotion.php";
		}else{
			require "./inc/lib/promotion.php";
		}
	}
	if($field == '')
	{
		$field ='g.*';
	}
	if($sort == '')
	{
		$sort ='addtime desc';
	}
	if($num !=null)
	{
		$limit = " limit ".$startI.','.$num;
	}
	if($is_up==1)
	{
		$where .= ' and'." g.status=". goods_up . " and uptime<=".time();
	}
	
	$sql = "SELECT distinct c.`name` as cat_name, $field FROM " . $db->table('goods')." g JOIN " . $db->table('category') . " c ON g.cat_id = c.id where 1 ". $where . " order by " . $sort . $limit;
	//print $sql.'<br>';
	$row = $db->queryall($sql); 
	foreach ($row as $k => $v) {
		$row[$k]['url'] = get_url($v['code']);
		$row[$k]['price'] = format_price($v['price']);
		$row[$k]['marketprice'] = format_price($v['marketprice']);
		$discount_price = get_discount_price($v['goods_id'], $ym_uid, $v['price']);//获取活动价格
		$tmp_price = get_min_max_price($v['goods_id']);
		$row[$k]['min_price'] = $tmp_price['min_price'] ? format_price($tmp_price['min_price']) : 0;
		$row[$k]['max_price'] = $tmp_price['max_price'] ? format_price($tmp_price['max_price']) : 0; 
		$row[$k]['goods_price'] = $tmp_price['min_price'] && $tmp_price['min_price']!==0 ? format_price(get_discount_price($v['goods_id'], $ym_uid, $tmp_price['min_price'])) : format_price($discount_price);  
	}
	
	return $row;
}

function get_min_max_price($goods_id)
{
	global $db;
	$sql = "SELECT min(price) min_price,min(price) max_price  FROM " . $db->table('goods_spec')." where goods_id=". intval($goods_id) ;
	return $db->query($sql);
}

function get_goods($goods_id=0)
{
	if(intval($goods_id)==0)
	{
		return false;
	}
	global $db;
	$sql="SELECT c.`name` as cat_name,ifnull(b.`name`,'') brand_name,ifnull(t.`name`,'') as type_name,g.*,ifnull(s.min_price,0) min_price,ifnull(s.max_price,0) max_price FROM ".$db->table('goods')." g left JOIN ".$db->table('category')." c ON g.cat_id = c.id left join ".$db->table('brand')." b on g.brand_id=b.id left join ".$db->table('type')." t on g.goods_type=t.id left join (SELECT min(price) min_price,max(price) max_price,ifnull(goods_id,0) goods_id FROM ".$db->table('goods_spec')." where goods_id=".intval($goods_id).") s on g.goods_id=s.goods_id where g.goods_id=".intval($goods_id);
	//
	$row = $db->query($sql);
	if($row)
	{
		$row['url'] = get_url($row['code']);
	}
	return $row;
}

/*扩展分类商品*/
function get_extend_goods($cat_id)
{
	global $db;
	$row = $db->fetchall('goods_cat', 'goods_id', array('cat_id'=>intval($cat_id)));
	$data = array();
	if(!$row || count($row)==0)
	{
		return create_in('');
	}
	foreach ($row as $k => $v) {
		$data[] = $v['goods_id'];
	}
	
	return create_in($data);
}

/*获取商品库存*/
function get_goods_num($goods_id=0, $spec='')
{
	global $db;
		
	if($spec !='')
	{
		$spec_arr = explode(",", $spec);
		$spec_arr = array_reverse($spec_arr);
		$spec2 =implode(",", $spec_arr);
		return $db->query("SELECT g.status goods_status,g.uptime,ifnull(s.number, g.number) number,ifnull(s.values,'') spec from ".$db->table('goods'). " g left join ".$db->table('goods_spec'). " s on g.goods_id=s.goods_id where g.goods_id=".intval($goods_id). " and (s.`values`='".$spec."' or s.`values`='".$spec2."')");
	}
	else {
		return $db->query("SELECT g.*,g.status goods_status,g.uptime,g.number,'' spec from ".$db->table('goods'). " g  where g.goods_id=".intval($goods_id));
	}	
}

//获取商品某规格的信息
function get_specinfo($goods_id, $spec='')
{
	global $db;
	$where ='';
	if($spec !='')
	{
		$spec_arr = explode(",", $spec);
		$spec_arr = array_reverse($spec_arr);
		$spec2 =implode(",", $spec_arr);
		$where .= " and (`values`='".trim($spec)."' or `values`='".trim($spec2)."')";
	}
	return $db->query("select price,number from ".$db->table('goods_spec')." where goods_id=".intval($goods_id).$where);
}

/**更新商品*/
function update_goods($goods_id=0, $goods = array())
{
	global $db;
	$db->update('goods', $goods, array('goods_id'=>intval($goods_id)));
}

//获取某分类信息
function get_catInfo($cid)
{
	global $ym_cats; 
	$list = array(); 
	if(is_array($cid))
	{
		foreach ($cid as $k => $v) {
			$tmp= array_query('id', intval($v), $ym_cats);
			$list[$k] = $tmp[0];
		}
	}
	else {
		$list = array_query('id', intval($cid), $ym_cats);
	}//print_r($list);
	
	return $list;	
}

function get_attrs($type_id=array() , $id =0)
{
	if(!file_exists(cache_data.'attrs'.'.php'))
	{
		return false;
	}
	require cache_data.'attrs'.'.php';
	if(count($type_id) >0)
	{	$list=array(); 
		$type_id = array_unique($type_id);
		foreach ($type_id as $k => $v) {
			$tmp = array_query('type_id', $v, $ym_attrs);
			$list=array_merge($list,$tmp);
		} 
		return array_filter($list);
	}
	elseif($id !=0)
	{
		return array_query('id', $id, $ym_attrs);
	}
	else {
		return FALSE;
	}	
}

//获取指定分类商品
function get_diy_goods($list=array(), $cid = 0)
{
	if(!isset($list)|| count($list)==0)
	{
		return false;
	}
	foreach ($list as $k => $v) {
		if($v['id'] != $cid)
		{
			unset($list[$k]);
		}
	}
	return array_merge($list);
}

function get_goods_specimg($goods)
{
	$list=json_decode($goods['specs'],true);
	if(count($specs['spec_val'])> 0) 
	{
		foreach ($specs['spec_val'] as $key => $val) {
			foreach ($val['imgs'] as $k => $v) {
				
			}
		}
	}	
	return $list;
}

/**获取某商品的规格*/
function get_goods_spec($gid)
{
	global $db;
	$row = $db->fetchall('goods_spec', '*', array('goods_id'=>intval($gid)));
	$spec = array();
	if($row && count($row)>0)
	{
		$tmp_goods_spec=array();
		$attr_ids =array();
		$vals = array();
		foreach ($row as $k => $v) {
			$tmp_ids= explode(',', $v['attr_ids']);
			$tmp_vals = explode(',', $v['values']);
			$attr_ids = array_merge($attr_ids, $tmp_ids);  
			foreach ($tmp_ids as $key => $val) {
				$tmp_name = $tmp_vals[$key];
				if( !in_array($val.$tmp_name, $vals) )
				{
					array_push($vals, $val.$tmp_name);
					$tmp['id']=$val;
					$tmp['name']=$tmp_name;
					$tmp_goods_spec[]= $tmp;
				}
			}
		}

		sort($tmp_goods_spec);
				
		$attr_ids = array_unique($attr_ids);
		$spec = $db->fetchall('attribute', '*', 'id'.create_in($attr_ids), "`sort` desc" ); 
		foreach ($spec as $k => $v) {
			$names = array();
			foreach ($tmp_goods_spec as $key => $val) {
				if($v['id']==$val['id'])
				{
					$names[]= $val['name'];
				}
			}
			$spec[$k]['val'] = $names;
		}
	}	
	return $spec;
}

//获取某商品的规格
function get_spec_val($spec_ids, $spec_name)
{
	if($spec_ids=='' || $spec_name == '')
	{
		return '';
	}
	global $db;
	$spec_row = $db->fetchall('attribute', "name", "id in(".$spec_ids.")","`sort`");
	$spec_name = explode(',', $spec_name);
	foreach ($spec_row as $k => $v) {
		$spec_row[$k]['val'] = $spec_name[$k];
	}
	return $spec_row;
}

//获取某商品的参数
function get_attr_val($goods_id=0)
{	
	if(intval($goods_id) == 0)
	{
		return false;
	}
	global $db;
	$row= $db->queryall("select a.id,a.name,a.uitype,ga.values from ".$db->table('goods_attr' )." ga join ".$db->table('attribute' )." a on ga.attr_ids=a.id where goods_id=".intval($goods_id)." order by `sort`"); 
	
	$attr = array();
	foreach($row as $k => $v) {
		if(count($attr[$v['id']]) == 0)
		{
			$attr[$v['id']]['id'] = $v['id'];
			$attr[$v['id']]['name'] = $v['name'];
			$attr[$v['id']]['uitype'] = $v['uitype'];
		}
		$attr[$v['id']]['val'] = explode(',', $v['values']);
	}
	
	return $attr;
}

/*获取最近浏览商品*/
function get_history($num)
{
	$history = isset($_COOKIE['his']) ? $_COOKIE['his'] : '';
	$his = array_filter(explode('@', $history));
	$count = count($his);
	$ids = array();
	$row = array();
	$num = $count > $num ? $num : $count;
	$n = $count - 1;	
	for ($i=0; $i < $num; $i++) { 
		$id = intval(ucode($his[$n-$i]));
		if($id > 0)
		{
			$ids[] = $id;
		}
	}
	if(count($ids)>0)
	{
		$row = get_goods_list("and g.goods_id ".create_in($ids),'', '', 0, $num,1,1);
	}
	
	return $row;
}

function get_fav_users($goods_id)
{
	global $db;
	return $db->queryall("SELECT distinct m.id,m.uname,m.img FROM ".$db->table('member_fav')." f join ".$db->table('member')." m on f.uid=m.id where goods_id=".intval($goods_id));
}

//上一个商品  
function get_prev_goods($goods_id)
{
	global $db;
	$row = $db->query("select goods_id,name,code from ".$db->table('goods')." where goods_id<".intval($goods_id)." order by goods_id desc limit 1");
	$row['url']= get_url($row['code']);
	return $row;
}

//下一个商品
function get_next_goods($goods_id)
{
	global $db;
	$row = $db->query("select goods_id,name,code from ".$db->table('goods')." where goods_id>".intval($goods_id)." order by goods_id asc limit 1");
	$row['url']= get_url($row['code']);
	return $row;
}

//获取品牌
function get_brand($id=0, $name='', $recommend=0 , $startI=0, $num=null)
{
	global $db;
	$where = '' ;
	if($id != 0)
	{
		$where = ' and id='.intval($id) ;
	}
	if($name != 0)
	{
		$where = " and name like '%".trim(addslashes($name))."%'" ;
	}
	if($recommend != 0)
	{
		$where = " and recommend=".intval($recommend) ;
	}
	if($num != null)
	{
		$where = ' limit '.intval($startI).",".intval($num) ;
	}
	return $db->queryall("select * from ".$db->table('brand')." where status=1 ".$where);	 
}

/*获取某品牌信息*/
function get_brand_info($brand_id)
{
	global $db;
	$row = $db->fetch("brand", '*', array('id'=>intval($brand_id)));
	
	return $row;
}

//获取分类关联的品牌
function get_cat_brand($cid)
{
	global $db;

	return $db->queryall("select id,name,logo,url from ".$db->table('brand')." where status=1 and find_in_set(".intval($cid).", cat_ids) ");	 
}

//会员价
function get_member_price($goods_id)
{
	global $db;
	return $db->queryall("select mp.*,mg.grade_name from ".$db->table('member_price')." mp join ".$db->table('member_grade')." mg on mp.grade_id=mg.grade_id where goods_id=".intval($goods_id));
}

//某会员的会员等级折扣
function get_userGradeDiscount($uid)
{
	global $db;
	/*$row = $db->query("select discount from ".$db->table('member')." g join ".$db->table('member_grade')." m on g.grade_id=m.grade_id where goods_id=".intval($goods_id)." and m.id=".intval($uid));
	return (!$row || count($row) == 0) ? 0 : floatval($row['discount']);*/
	return FALSE;
}

/*获取会员等级*/
function get_grade()
{
	global $db;
	return $db->fetchall('member_grade', '*','', 'sort asc');
}

/*处理规格*/
function format_specs($specs)
{
	$arr_specs = json_decode($specs, true);
	if($arr_specs && count($arr_specs) >0 && count($arr_specs['spec_val'])>0)
	{
		foreach($arr_specs['spec_val'] as $key => $val) {
		    if (count($val['imgs']) > 0) {
		        foreach($val['imgs'] as $i => $j) {
		            $arr_specs['spec_val'][$key]['imgs'][$i]['img'] = url_to_abs($j['img']);
		            $arr_specs['spec_val'][$key]['imgs'][$i]['thumb'] = url_to_abs($j['thumb']);
		        }
		    }
		}
	}
	return $arr_specs;
} 

function get_catids_by_gids($gids)
{
	global $db;
	$cids = array();
	if(is_array($gids))
	{
		$gids = implode(",", $gids);
	}
	$row = $db->queryall("select cat_id from ".$db->table('goods')." where goods_id in(".trim($gids).")");
	if($row)
	{
		foreach($row as $k => $v) {
			if(!in_array($v['cat_id'], $cids))	
			{
				$cids[] = $v['cat_id'];
			}
		}
	}
	
	return $cids;
}

?>