<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'member.grade');//权限检测

 $res = array('err' => '', 'res' => '', 'data' => array());
 switch ($act) {
 	case 'edit_grade_name':
		if(!isset($id) || intval(trim($id))==0){$res['err']="获取不到id";die(json_encode($res));}
 		$row = $db -> fetch('member_grade', '*', "grade_name='" . trim($val) . "' and grade_id<>'" . $id . "'");
		if ($row) {
			$res['err'] = '商品名称已存在，请更换。';
			die(json_encode($res));
		}
		$db -> update('member_grade', array('grade_name' => trim($val)), array('grade_id' => intval($id)));	
		$res['res'] = '更新成功';
		die(json_encode($res));
 		break;
 	case 'edit_point_require':
 		if(!isset($id) || intval(trim($id))==0){$res['err']="获取不到id";die(json_encode($res));}
		$db -> update('member_grade', array('point_require' => intval($val)), array('grade_id' => intval($id)));	
		$res['res'] = '更新成功';
		die(json_encode($res));
 		break;
	case 'edit_discount':
 		if(!isset($id) || intval(trim($id))==0){$res['err']="获取不到id";die(json_encode($res));}
		$db -> update('member_grade', array('discount' => intval($val)), array('grade_id' => intval($id)));	
		$res['res'] = '更新成功';
		die(json_encode($res));
 		break;
	case 'edit_sort':
		if(!isset($id) || intval(trim($id))==0){$res['err']="获取不到id";die(json_encode($res));} 
		$db -> update('member_grade', array('sort' => intval($val)), array('grade_id' => intval($id)));
		$res['res'] = '更新成功';
		die(json_encode($res));
 		break;
	case 'delete':
		if(!isset($id) || intval(trim($id))==0){message("获取不到id",'/admin.html?do=member.grade');} 
		$db -> delete('member_grade', array('grade_id' => intval($id)));
		del_member_price(0, $id); //删除相应的会员价格
		message("更新成功",'/admin.html?do=member.grade');
 		break;	
	case 'edit_is_default':
		if(!isset($id) || intval(trim($id))==0){$res['err']="获取不到id";die(json_encode($res));} 
		if($val ==1)
		{
			$db -> update('member_grade', array('is_default' => 0), array('is_default' => 1)); //取消其它默认
			$db -> update('member', array('grade_id' => intval($id)), array('grade_id' => 0)); //更新等级id为0的为默认等级
		}
		
		$db -> update('member_grade', array('is_default' => intval($val)), array('grade_id' => intval($id)));
		
		$res['res'] = '更新成功';
		die(json_encode($res));
 		break;			
 	default:
 		$row = $db -> fetchall('member_grade', '*',"","`sort`");	
 		break;
 }
 

 
?>