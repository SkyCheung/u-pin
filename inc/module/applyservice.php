<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*退换货申请*/
$ym_uid = check_login();

if($act && $act =='add')
{	
	$oid =isset($oid) ? trim($oid): '';
	$goods_id =isset($goods_id) ? trim($goods_id) :'';
	$spec =isset($spec) ? trim($spec) :'';
	$num =isset($num) ? intval($num) :0;
	$content =isset($content) ? $content :'';
	$imgs =isset($imgs) ? $imgs : '';
	$thumbs =isset($thumbs) ? $thumbs : '';
	$apply_type =isset($apply_type) ?intval($apply_type):0;
	$district_name=isset($district_name) ? trim($district_name): '';
	$address=isset($address) ? trim($address): '';
	$name=isset($name) ? trim($name): '';
	$mobile=isset($mobile) ? trim($mobile): '';	
	
	$sql = '';
	if(!isset($oid) || intval($oid)==0)
	{
		message('获取订单号失败');
	}
	if($goods_id=='')
	{
		message('获取商品编号失败');
	}
	if(intval($num)==0)
	{
		message('申请数量不正确');
	}
	if($content=='')
	{
		message('请填写问题描述哦~');
	}
	if(mb_strlen($content,'utf-8')>=500)
	{
		message('问题描述字符数不能超过500,谢谢！');
	}
	if(intval($apply_type)==0)
	{
		message('服务类型不正确');
	}
	if($name=='')
	{
		message('请填写您的姓名');
	}
	if($mobile=='')
	{
		message('请填写您的手机号码');
	}
	if($apply_type==service_change && $address=='')
	{
		message('请填写您的收货地址');
	}
		
	$ym_uid =check_login(1);
	if($ym_uid==0)
	{
		message('请先登录', '/login.html');
	}
	$time = time();
	dbc();

	$order = get_order_details(0, $oid, $ym_uid);
	if(!$order || count($order)==0)
	{
		message('订单异常');
	}
	$row = array('uid'=>$ym_uid,'order_sn'=>$oid,'type'=>$apply_type,'goods_id'=>$goods_id,'spec'=>$spec,'num'=>$num,'content'=>$content,'img'=>json_encode($imgs),'thumb'=>json_encode($thumbs),'status'=>service_apply,'address'=>$district_name.$address,'uname'=>$name,'mobile'=>$mobile,'remark'=>'','addtime'=>time());
	add_order_service($row);
	message('申请成功，请等待商家审核',"/details.html?oid=".$oid, 5);
	die();
}

if(!isset($oid) || intval($oid)==0)
{	
	message('获取订单号失败',"/myorder.html");
}

$gid = isset($gid) ? intval($gid):0;
$spec = isset($spec) ? trim($spec): '';

dbc();

$nav = get_nav(); //导航
$nav_footer = get_nav('bot');
$cats = get_catTree(); //分类树
$help = get_help(); //帮助

$order = get_order_info(0, $oid, $ym_uid);
$cnee_dist_ids = explode(",", $order['cnee_dist_ids']);
$order['cnee_dist_ids'] = str_replace(",", "-", $order['cnee_dist_ids']);
$row = get_order_goods($oid, $ym_uid, $gid, $spec);

?>