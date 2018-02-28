<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'goods.delete');//权限检测

//删除商品
if ($id == '' || isNumComma($id) == FALSE) {message("获取不到商品编号");}


if(isset($act) && $act=='recycle')
{
	update_goodsStatus($id, goods_down);
	message("还原成功", '/admin.html?do=goods.recycle');
}
else {
	if(isset($istrue) && $istrue == 1)
	{
		del_goods($id);	
		message("删除成功", '/admin.html?do=goods.recycle');
	}
	else {
		update_goodsStatus($id, goods_del);
		message("删除成功，您可通过回收站还原。", '/admin.html?do=goods');
	}	
}


?>