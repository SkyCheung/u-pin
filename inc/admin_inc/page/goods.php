<?php
if (!defined('in_mx')) {exit('Access Denied');}

if($act)
{
	 $res = array('err' => '', 'res' => '', 'data' => array());
	 if(!checkAuth($login_id, 'goods.edit'))//权限检测
	{
		$res['err'] = $lang['access_denied'];
		die(json_encode($res));
	}
	 switch ($act) {
	 	case 'edit_name':
			if(!isset($id) || intval(trim($id))==0){$res['err']="获取不到id";die(json_encode($res));}
	 		$row = $db -> fetch('goods', '*', "name='" . trim(addslashes($val)) . "' and goods_id<>'" . $id . "'");
			if ($row) {
				$res['err'] = '商品名称已存在，请更换。';
				die(json_encode($res));
			}
			$db -> update('goods', array('name' => trim($val)), array('goods_id' => intval($id)));	
			$res['res'] = '更新成功';
			die(json_encode($res));
	 		break;
	 	case 'edit_number':
	 		if(!isset($id) || intval(trim($id))==0){$res['err']="获取不到id";die(json_encode($res));}
			$db -> update('goods', array('number' => intval($val)), array('goods_id' => intval($id)));	
			$res['res'] = '更新成功';
			die(json_encode($res));
	 		break;
		case 'edit_status':
	 		if(!isset($id) || trim($id)=='' || !is_numeric($id)){$res['err']="获取不到id";die(json_encode($res));} 
			update_goodsStatus($id,$val);	
			$res['res'] = '更新成功';
			die(json_encode($res));
	 		break;
		case 'edit_sort':
			if(!isset($id) || intval(trim($id))==0){$res['err']="获取不到id";die(json_encode($res));} 
			$db -> update('goods', array('sort' => intval($val)), array('goods_id' => intval($id)));
			$res['res'] = '更新成功';
			die(json_encode($res));
	 		break;		
	 	default: 			
	 		break;
	 }
 }
else {
		checkAuth($login_id, 'goods');//权限检测 
				
		$cat = array_query('pid', 0, $ym_cats, false); 

		if($id) {
			$row = array_query('id', $id, $ym_cats, false);
			$row = $row[0];
		
			$pid = $row['pid']; 
			for ($i = $row['level']; $i >= 1; $i--) {
				$cattmp = 'cat' . $i;
				$parentid = 'pid' . $i; 
				$$parentid = $pid; 
				$$cattmp = array_query('pid', $pid, $ym_cats, false);
				$$cattmp['level'] = $i;
				$pidtmp = array_query('id', $pid, $ym_cats, false);
				$pid = $pidtmp[0]['pid']; 
			}
		}
		
		$where='';
		$keyword = trim(addslashes($keyword));
		if(trim($status)!='')
		{
			$where .=' status='.intval($status);
		}
		else {
			$where .=' status<>'.goods_del;
		}
		if(trim($cat_id)!='')
		{
			$child_ids = get_childIDs(intval($cat_id));
			$where .=' and cat_id in('.$child_ids.")";
		}
		if ($keyword != ""){
			$where .=" and (goods_id LIKE '%$keyword%' or name LIKE '%$keyword%')";
		} 
		
 		$page=intval($page)==0 ? 1:intval($page);
		$pagenum=12; 
		$startI = $page*$pagenum-$pagenum;
		$count =$db->rowcount('goods', $where);
		$pages=getPages($count, $page, $pagenum);
		$where=str_replace('status', 'g.status', $where);
		$where=str_replace('name', 'g.name', $where);
		$row= get_goodsList($where,$startI,$pagenum);	
}

?>