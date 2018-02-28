<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'member');//权限检测

$id = intval($id);
require 'inc/lib/cart.php';

$db->delete('member', array('id' => $id));
del_member_oauth($id); //取消第三方授权
del_fav($id); //删除用户收藏
del_member_log($id); //删除用户账单记录
del_cart(0, $id);//删除购物车
del_comment($id); //删除评价
del_comment_reply($id);//删除相关回复评价
del_comment_reply($id);

message("删除成功。",$url);
exit();

?>