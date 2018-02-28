<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*云EC电商系统开放平台 数据接口  YunecAPI-SDK */

class Yunec_API
{
	private $appid = '';
	private $secret ='';
	private $access_token ='openyuneccn201700001';
	
	public function __construct($appid = '', $secret ='')
	{		
		$this->appid = $appid;
		$this->secret = $secret;
		if (!$this->check_auth()) 
		{
			die("云EC未授权");
			return false;
		}
	}
	
	/**
	 * 通用auth验证方法
	 * @param string $appid
	 * @param string $appsecret
	 */
	public function check_auth($appid='',$appsecret=''){
		global $access_token;
		if($this->access_token=='' || $this->access_token != $access_token)
		{
			//return false;
		}
		return true;
	}
	
	/*会员登陆*/
	public function login()
	{
		global $act,$ym_is_api,$username,$password,$smscode,$authtype,$authcode,$ym_is_api;

		if (!$this->check_auth()) 
		{
			return false;
		}
		
		include("./inc/module/user_api.php");
		$res['data'] =array('id'=>$user['id'], 'uname'=>$user['uname'], 'mobile'=>$user['mobile'], 'img'=>$user['img']);
		$res["res"]="ok";
		die(json_encode_yec($res));
	}
	
	/*第三方登陆*/
	public function oauth_login()
	{
		global $act,$ym_is_api,$oauth_type, $openid,$platform,$unionid;

		include("./inc/module/user_api.php");
		die(json_encode_yec($res));
	}
	
	/*第三方绑定*/
	public function oauth_bind()
	{
		global $act,$ym_is_api,$mobile,$username,$password,$img,$email,$oauth_type, $openid,$platform,$unionid;

		include("./inc/module/user_api.php");
		die(json_encode_yec($res));
	}
	
	/*第三方注册绑定手机号*/
	public function user_bind()
	{
		global $act,$ym_is_api,$mobile,$smscode;
		$tel = $mobile;
		$ym_uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
		if($ym_uid == 0){
			$res["res"] = "unlogin";
			$res["err"]="用户id不能为空";die(json_encode_yec($res));
		} 
		include("./inc/module/user_api.php");
		die(json_encode_yec($res));
	}
	
	/*注册*/
	public function reg()
	{
		global $act,$tel,$username,$password,$repassword,$img,$email,$birthday,$qq,$sex,$realname,$smscode,$authtype,$ditrib_id;

		if($ditrib_id && intval($ditrib_id) >0)
		{
			session_start();
			$_SESSION['ditrib_id'] = $ditrib_id;
		}
		$repassword = !isset($repassword) ? $password : $repassword;
		include("./inc/module/user_api.php");                                                                             
		$res["res"]="ok";
		die(json_encode_yec($res));
	}
	
	/*用户个人信息*/
	public function userinfo()
	{
		global $res,$uid;
		dbc();
		$uid =intval($uid);
		if($uid == 0){$res["err"]="用户id不能为空";die(json_encode_yec($res));}
		$user = get_user($uid);
		unset($user['pwd']);
		$res["data"]= $user;
		$res["res"]="ok";
		die(json_encode_yec($res));
	}
	
	/*编辑会员信息*/
	public function edit_userinfo()
	{
		global $act,$res,$ym_uid,$img,$sex,$username,$birthday,$email,$realname,$memo;
		$ym_uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
		if($ym_uid == 0){
			 $res["err"]="用户id不能为空";
			 die(json_encode_yec($res));
		}
		
		include("./inc/module/user.php");
		$res["res"]="ok"; 
		die(json_encode_yec($res));
	}
	
	/*绑定手机号*/
	public function bind_mobile()
	{
		global $act,$res, $mobile,$smscode,$ym_uid,$uid;
		$ym_uid = $uid;
		include("./inc/module/user.php");
		$res["res"]="ok"; 
		die(json_encode_yec($res));
	}
	
	/*设置密码*/
	public function update_pwd()
	{
		global $act,$res, $oldpwd, $pwd, $repwd, $authtype, $smscode,$ym_uid,$sms_id;
		$repwd = isset($repwd) ? $repwd : $pwd;
		$ym_uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;

		if($ym_uid == 0){$res["err"]="用户id不能为空";die(json_encode_yec($res));}
		
		$sms_id = isset($sms_id) ? endecrypt(trim($sms_id), 'DECODE', ym_token) : 0; 		
		if(intval($sms_id) > 0)
		{
			session_start();
			$_SESSION['findpwd_uid'] = $ym_uid;
			$_SESSION['findpwd_checkcode'] =1;
		}
		
		$act = 'updatepwd';
		include("./inc/module/user.php");
		$res["res"] = "ok"; 
		die(json_encode_yec($res));
	}
	
	/*设置支付密码*/
	public function set_paypwd()
	{
		global $act,$res, $smscode,$ym_uid;
		$ym_uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
		$paypwd = isset($_POST['paypwd']) ? trim($_POST['paypwd']) : '';
		if($ym_uid == 0){$res["err"]="用户id不能为空";die(json_encode_yec($res));}
		
		$authcode = $smscode;
		$repaypwd = isset($_POST['repaypwd']) ? trim($_POST['repaypwd']) : $paypwd;
		include("./inc/module/user.php");
		$res["res"]="ok"; 
		die(json_encode_yec($res));
	}
	
	/*找回密码验证身份*/
	public function findpwd_auth()
	{
		global $res, $mobile, $smscode;
		$mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : 0;

		if(intval($mobile) ==0)
		{
			$res["err"]="手机号码不能为空";die(json_encode_yec($res));
		}
		$_SESSION['findpwd_uid'] = 1;
		$_SESSION['findpwd_mobile'] = $mobile;
		$act = 'findpwd_checkcode';
		include("./inc/module/user.php");
		$res["res"]="ok"; 
		die(json_encode_yec($res));
	}
			
	/*商品信息*/
	public function goods()
	{
		global $res,$uid,$goods_id,$ym_url, $is_imgs, $is_specs, $is_details, $is_service,$is_goods_spec, $is_goods_attr;
		if(!$goods_id){$res["err"]="商品编号id不能为空;";die(json_encode_yec($res));}
		$uid =intval($uid);
		$is_details = isset($is_details) ? intval($is_details) : 1;
		$is_service = isset($is_service) ? intval($is_service) : 1;
		 
		dbc();
				 
		include("./inc/lib/promotion.php");
		$goods = get_goods($goods_id);
		if(!$goods){
			$res["err"]="商品不存在;";die(json_encode_yec($res));
		}
		if($goods["status"] == goods_del){
			$res["err"]="商品已删除;";
			die(json_encode_yec($res));
		}
		if($uid == 0){
			$uid=0;
			$goods['is_fav']= 0;
		}
		else {
			$fav_user = get_fav($uid, $goods_id);
			$goods['is_fav']= ($fav_user && count($fav_user)>0)?1:0;
		}				
		
		$price = $goods["min_price"] ==0 ? $goods["price"] : $goods["min_price"];
		$discount =0;
		if($uid !=0){
			$user = get_user($uid);
			$discount = $user["discount"];
			$user_price = $user ? format_price($user["discount"] * $price): 0;
		}
		$goods["goods_price"] = format_price(get_discount_price($goods_id, $uid, $price, $discount));
		$goods['img'] = url_to_abs($goods['img']);
		$goods['thumb'] = url_to_abs($goods['thumb']);
		$goods['url'] = url_to_abs($goods['url']);
		
		if ($is_details == 0) {
		    unset($goods['details']);
		    unset($goods['details_m']);
		}
		else {
			$goods["details"] = replace_uploadURL($goods["details"]);
			$goods["details_m"] =replace_uploadURL($goods["details_m"]);
		}
		if ($is_service == 0) {
		    unset($goods['service']);
		    unset($goods['service_m']);
		}
		else {
			$goods["service"] = replace_uploadURL($goods["service"]);
			$goods["service_m"] =replace_uploadURL($goods["service_m"]);
		}
		
		//处理商品图片
		if ($is_imgs == 0) {
		    unset($goods['imgs']);
		} else {
		    $arr_imgs = json_decode($goods['imgs'], true);
		    if ($arr_imgs && count($arr_imgs) > 0) {
		        foreach($arr_imgs as $key =>$val) {
		            $arr_imgs[$key]['img'] = url_to_abs($val['img']);
		            $arr_imgs[$key]['thumb'] = url_to_abs($val['thumb']);
		        }
		    }
		    $goods['imgs'] = $arr_imgs;
		}
		
		//处理规格
		if ($is_specs == 0) {
		    unset($goods['specs']);
		} else {
		    $goods['specs'] = format_specs($goods['specs']);
		}

		if(intval($is_goods_spec) ==1)
		{
			$goods["goods_spec"]= get_goods_spec($goods_id);
		}
		if(intval($is_goods_attr) ==1)
		{
			$goods["goods_attr"]= get_attr_val($goods_id);
		}		
		 		
		$res['data'] = $goods;

		die(json_encode_yec($res));
	}
	
	/*推荐商品*/
	public function remmend_goods()
	{
		global $goods_id,$res;
		$goods_id = isset($goods_id) ? intval($goods_id) : 0;
		if($goods_id == 0){
			$row["err"]="商品编号id不能为空;";
			die(json_encode_yec($res));
		}
		
		//推荐商品
		$row = array();
		if($diy_itemrem)
		{
			$goods = get_goods($goods_id);
			$diy_goods = get_diy_goods($diy_itemrem, $goods["cat_id"]);
			if(count($diy_goods)>0){
				$row=$diy_goods[0]["goods"];
				foreach($row as $k=>$v){
					$row[$k]["goods_img"] = url_to_abs($v['img']);
				}
			};
		}
		$res['data'] = $row;
		die(json_encode_yec($res));
	}
	
	/*评论列表*/
	public function get_comment()
	{
		global $res,$act,$uid,$num,$goods_id,$level,$page,$is_count;
		$ym_uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
		$id = intval($goods_id);
		include("./inc/module/user.php");
		die(json_encode_yec($res));
	}
	
	/*添加评论*/
	public function add_comment_reply()
	{
		global $act,$res, $goods_id, $cid, $pid, $ptype, $content;		
		$ym_uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
		if($ym_uid == 0){
			$res["res"] = "unlogin";
			$res["err"]="用户id不能为空";die(json_encode_yec($res));
		}
		
		include("./inc/module/user.php");
		die(json_encode_yec($res));
	}
	
	/*某评论的回复*/
	public function get_comment_reply()
	{
		global $act,$cid, $res;
		include("./inc/module/user.php");
		die(json_encode_yec($res));
	}
	
	/*我的订单*/
	public function myorder()
	{
		global $res, $keyword,$status,$t,$trade_start_date,$trade_end_date,$page,$num,$ym_uid,$is_count;
		$is_ajax = 1;
		//$arr = file_get_contents("php://input");  //$data= json_decode($arr, true); print_r($data); ;
		
		$ym_uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
		if($ym_uid == 0)
		{
			$res['res'] = 'unlogin';
			$res['err'] = '用户id不能为空';
			die(json_encode_yec($res));
		}
		include("./inc/module/myorder.php");
		die(json_encode_yec($res));
	}
	
	/*用户收藏*/
	public function fav()
	{
		global $res,$uid,$page,$num, $is_count;
		$uid =intval($uid);
		if($uid ==0)
		{
			$res['res'] = 'unlogin';
			$res['err'] = '用户id不能为空';
			die(json_encode_yec($res));
		}
		dbc();
		$page = intval($page)==0 ? 1 : intval($page);
		$num = isset($num) ? intval($num) : 12;
		$start = $page * $num - $num;

		$fav = get_fav($uid, 0,'',$start , $num);
		if(is_array($fav) && !empty($fav)){
			foreach($fav as $k=>$v){
				$fav[$k]['thumb']= url_to_abs($v['thumb']);
			};
		}
		if($is_count ==1)
		{
			$res['count'] = get_fav_count($uid);
		}
		$res['data'] = $fav;

		die(json_encode_yec($res));
	}
	
	/*用户中心的订单数*/
	public function order_num()
	{
		global $res,$uid;
		$uid = intval($uid);
		if($uid == 0){
			$res['res'] = 'unlogin';
			$res["err"]="用户id不能为空";
			die(json_encode_yec($res));
		}
		dbc();
		
		$row["unpay"] = order_count($uid, order_paying); //待付款数
		$row["delivery"] = order_count($uid, order_deliver); //待发货
		$row["receive"] = order_count($uid, order_receiving); //待收货
		$row["comment"] = order_count($uid, order_finish, "and is_comment=0"); //待评价

		$res["data"] = $row;
		die(json_encode_yec($res));
	}
	
	/*商品列表*/
	public function goods_list()
	{
		global $res, $cid,$at,$pr,$word,$num, $page,$sort, $is_imgs, $is_specs, $is_details, $is_service;

		$at = isset($at) ? trim($at) : '';
		$pr = isset($pr) ? trim($pr) : '';
		$word = isset($word) ? trim($word) : '';
		$page = isset($page) ? intval($page) : 0;
		$num = isset($num) ? intval($num) : 0;
		$sort = isset($sort) ? trim($sort) : 'a1'; //a1 时间排序，s1 销量排序，p1 价格升序，p2 价格降序
		$is_imgs = isset($is_imgs) ? intval($is_imgs) : 0; //是否返回组图
		$is_specs = isset($is_specs) ? intval($is_specs) : 0; //是否返回规格及其图片
		$is_details = isset($is_details) ? intval($is_details) : 0; //是否返回详情
		$is_service = isset($is_service) ? intval($is_service) : 0; //是否返回售后
		$is_ajax =2; //避免list.php查询多余数据

		include_once  "./inc/module/list.php";
		   
		if(is_array($goods) && count($goods)>0){
			foreach($goods as $k => $v){
				$goods[$k]['img']= url_to_abs($v['img']);
				$goods[$k]['thumb']= url_to_abs($v['thumb']);
				$goods[$k]['url']= url_to_abs($v['url']);
				
				if($is_details == 0)
				{
					unset($goods[$k]['details']);
					unset($goods[$k]['details_m']);
				}
				if($is_service == 0)
				{
					unset($goods[$k]['service']);
					unset($goods[$k]['service_m']);
				}
				
				if($is_imgs == 0)
				{
					unset($goods[$k]['imgs']);
				}
				else {
					$arr_imgs =json_decode($v['imgs'], true);
					if($arr_imgs && count($arr_imgs) >0)
					{
						foreach ($arr_imgs as $key => $val) {
							$arr_imgs[$key]['img'] = url_to_abs($val['img']);
							$arr_imgs[$key]['thumb'] = url_to_abs($val['thumb']);
						}
					}
					$goods[$k]['imgs'] = $arr_imgs;
				}
				
				if($is_specs == 0)
				{
					unset($goods[$k]['specs']);
				}
				else {
					$goods[$k]['specs'] =format_specs($v['specs']);
				}
			}
		}
		$res['count'] = $count;
 		$res['data'] = $goods; 
		die(json_encode_yec($res));
	}	
	
	/*加购物车*/
	public function add_cart()
	{
		global $act,$res,$ym_uid,$goods_id,$spec,$cart_id,$num,$total,$direct,$pid;
		$cart_id = isset($cart_id) ? intval($cart_id) : 0;
		$ym_uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
		/*if($ym_uid == 0 && $cart_id ==0){
			$res["err"]="用户id和购物车id必须要提供一个";die(json_encode_yec($res));
		}*/
		$gid = $goods_id;
		$ckey = $cart_id;
		$act = 'addtocart';
		require "./inc/module/cart.php";		
	}
	
	/*更新商品选择状态*/
	public function set_cart_status()
	{
		global $act,$res,$ym_uid,$goods_id,$spec,$cart_id,$status;
		$cart_id = isset($cart_id) ? intval($cart_id) : 0;
		$ym_uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
		if($ym_uid == 0 && $cart_id ==0){
			$res["err"]="用户id和购物车id必须要提供一个";die(json_encode_yec($res));
		}
		$gid = $goods_id;
		$ckey = $cart_id;
		require "./inc/module/cart.php";		
	}
	
	/*移除购物车的商品*/
	public function remove_goods()
	{
		global $act,$res,$ym_uid,$goods_id,$spec,$cart_id,$status;
		$cart_id = isset($cart_id) ? intval($cart_id) : 0;
		$ym_uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
		if($ym_uid == 0 && $cart_id ==0){
			$res["err"]="用户id和购物车id必须要提供一个";die(json_encode_yec($res));
		}
		$gid = $goods_id;
		$ckey = $cart_id;
		require "./inc/module/cart.php";		
	}
	
	/*获取购物车的商品*/
	public function cart()
	{
		global $act,$res,$uid,$ym_uid,$cart_id;
		$uid = isset($uid) ? intval($uid) : 0;
		$cart_id = isset($cart_id) ? intval($cart_id) : 0;
		if($uid == 0 && $cart_id ==0){
			$res["err"]="用户id和购物车id必须要提供一个";die(json_encode_yec($res));
		}
		require_once "./inc/lib/cart.php";
		require  "./inc/lib/promotion.php";
		$ym_uid = $uid;
		$_COOKIE["ckey"] = $cart_id;
		
		dbc();
		$cart = get_cart();
		if($cart['goods']){
			foreach($cart['goods'] as $k=>$v){
				$cart['goods'][$k]["thumb"]= url_to_abs($v['thumb']);
			};
		}
		$res['data'] = $cart;

		die(json_encode_yec($res));
	}
	
	/*添加订单*/
	public function add_order()
	{
		global $act,$res,$ym_uid,$uid,$cneeid,$balance,$point,$coupon_amount,$pay,$exp_id,$paypwd,$user_remark,$invoice_title,$invoice_con;
		$ym_uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;		
		if($ym_uid == 0){
			$res["res"] = "unlogin";
			$res["err"]="用户id不能为空";die(json_encode_yec($res));
		} 
		require "./inc/module/order.php";
	}
	
	/*保存收货地址*/
	public function edit_consignee()
	{
		global $act,$res, $id,$uid, $name,$mobile,$tel,$district,$address,$isdefault,$ym_uid;
		//$ym_uid = $uid;
		$ym_uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
		if($ym_uid == 0){
			$res["res"] = "unlogin";
			$res["err"]="用户id不能为空";die(json_encode_yec($res));
		} 
		require "./inc/module/order.php";
	}
	
	/*删除收货地址*/
	public function del_consignee()
	{
		global $act,$res,$ym_uid,$id;
		$ym_uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;		
		if($ym_uid == 0){
			$res["err"]="用户id不能为空";die(json_encode_yec($res));
		}
		require "./inc/module/order.php";
	}
	
	/*获取会员收货地址列表*/
	public function get_consignee()
	{
		global $act,$res,$ym_uid,$id;
		$ym_uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
		if($ym_uid == 0){
			$res["err"]="用户id不能为空";die(json_encode_yec($res));
		}
		require "./inc/module/order.php";		
	}
	
	/*确认收货*/
	public function confirm_receiving()
	{
		global $act,$res,$ym_uid,$oid;
		$ym_uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
		if($ym_uid == 0){
			$res["err"]="用户id不能为空";die(json_encode_yec($res));
		}
		
		require "./inc/module/order.php";		
	}
	
	/*取消订单*/
	public function order_cancel()
	{
		global $act,$res,$ym_uid,$oid;
		$ym_uid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
		if($ym_uid == 0){
			$res["err"]="用户id不能为空";die(json_encode_yec($res));
		}
		
		require "./inc/module/order.php";		
	}
	
	/*获取物流轨迹*/
	public function get_exp_track()
	{
		global $act,$res,$exp_no,$exp_code;
		
		require "./inc/module/order.php";		
	}
	
	/*收藏商品*/
	public function add_fav()
	{
		global $act,$res, $goods_id, $spec,$uid;
		$act = 'addfav';
		$gid = $goods_id ;
		$uid =intval($uid);
		if($uid ==0)
		{
			$res['res'] = 'unlogin';
			$res['err'] = '用户id不能为空';
			die(json_encode_yec($res));
		}
		$ym_uid = $uid;
		include_once  "./inc/module/user.php";
	}
	
	/*取消收藏*/
	public function del_fav()
	{
		global $act,$res, $goods_id, $spec,$uid;
		$gid = $goods_id ;
		$uid =intval($uid);
		if($uid ==0)
		{
			$res['res'] = 'unlogin';
			$res['err'] = '用户id不能为空';
			die(json_encode_yec($res));
		}
		$ym_uid = $uid;
		include_once  "./inc/module/user.php";
	}
	
	/*获取个人积分列表*/
	public function mypoint()
	{
		global $res,$uid, $is_expire_point,$is_count,$is_total,$page,$num, $t;
		$uid = isset($uid) ? intval($uid) : 0;
		$is_count = isset($is_count) ? intval($is_count) : 0;
		$t = intval($t);
		if($uid == 0){
			$res["err"]="用户id不能为空";die(json_encode_yec($res));
		}
		dbc();				
				
		$page = intval($page)==0 ? 1 : intval($page);
		$num =  intval($num)==0 ? 20 : intval($num); 
		$start = $page * $num - $num; 				
		
		if($is_count ==1)
		{
			$res["count"] = get_member_log_count($uid, asset_point, 0,'', strtotime(date('Y-m-d H:i:s',strtotime('-2 year'))),'', $t);
		}
		$res['data'] = get_member_log($uid, asset_point, 0,'', strtotime(date('Y-m-d H:i:s',strtotime('-2 year'))),'', $start, $num, $t);
		
		$res['expire_point'] = ($is_expire_point && $is_expire_point==1) ? get_expire_point($uid) : 0;
		if($is_total && $is_total == 1)
		{
			$user = get_user($uid); 
			$res['total'] = $user['point'];
		}

		die(json_encode_yec($res));
	}
	
	/*品牌列表*/
	public function brandlist()
	{
		global $name,$res, $recommend, $page, $num;		
				
		$name = isset($name) ? addslashes($name) : '';
		$recommend = isset($recommend) ? intval($recommend) : 0;  

		$page=intval($page)==0 ? 1 : intval($page);
		if(isset($num))
		{
			$num =  intval($num);
			$startI = $page * $num - $num;
		}
		else {
			$num =  null;
			$startI =0;
		}
		 
		dbc();
		$row = get_brand(0, $name, $recommend, $startI, $num);
		if($row){
			foreach($row as $k => $v){
				$row[$k]['logo'] = url_to_abs($v['logo']);
				$row[$k]['banner'] = url_to_abs($v['banner']);
			}
			$res['data']=$row;
		}
		
		die(json_encode_yec($res));
	}
	
	/*获取某品牌信息*/
	public function brand_info()
	{
		global $brand_id,$res;
		$brand_id = isset($brand_id) ? intval($brand_id) : 0;
		if($brand_id == 0){$res["err"]="brand_id不能为空";die(json_encode_yec($res));}
		
		dbc();
		$brand = get_brand_info($brand_id); 
		if(!$brand){$res["err"]="品牌不存在";die(json_encode_yec($res));}
		$brand['logo'] = url_to_abs($brand['logo']);
		$brand['banner'] = url_to_abs($brand['banner']);
		
		$child_ids = get_childIDs($brand['cat_id']);
		$childs= get_catInfo(explode(",", $child_ids));
		$type_ids = array();
		foreach ($childs as $k => $v) {	
			$type_ids[$k] = $v["type_id"];
		}
		$attr = get_attrs($type_ids);
		$at_arr= explode('@', $at);
		
		if($attr)
		{
			foreach ($attr as $k => $v) {
				$value = explode(',', $v['value']);
				$attr_value = array(); 	
				foreach ($value as $key => $val) {
					$attr_value[$key]['name']= $val;
				}	
				$attr[$k]['value'] = $attr_value;				
			}	
		}
		
		$brand["attr"]= $attr;
		$res["data"]= $brand;

		die(json_encode_yec($res));
	}
	
	/*获取某品牌的商品列表*/
	public function brand_goods()
	{
		global $brand_id,$res,$num,$page,$sort;
		$brand_id = isset($brand_id) ? intval($brand_id) : 0;
		if($brand_id == 0){$res["err"]="brand_id不能为空";die(json_encode_yec($res));}
		dbc();

		$num= isset($num) ? intval($num) :20; 
		$page=intval($page)==0 ? 1 : intval($page);
		$startI = $page * $num - $num;
		
		$sort_list = array('a1'=>'addtime desc','s1'=>'salenum desc', 'p1'=>'price asc', 'p2'=>'price desc');
		$sort = (isset($sort) && trim($sort)!='') ? trim($sort) : 'a1';
		$order = $sort_list[trim($sort)];

		if($brand_id){
			$condition .=" and g.brand_id =".$brand_id;
		}
		
		$goods = get_goods_list($condition, 'g.*', $order, $startI , $num,1,1);
		if($goods && count($goods)>0)
		{
			foreach ($goods as $k => $v) {
				$goods[$k]['img'] = url_to_abs($v['img']);
				$goods[$k]['thumb'] = url_to_abs($v['thumb']);
				unset($goods[$k]['specs']);
				unset($goods[$k]['imgs']);
				unset($goods[$k]['specs']);
				unset($goods[$k]['details']);
				unset($goods[$k]['details_m']);
				unset($goods[$k]['service']);
				unset($goods[$k]['service_m']);
			}
		}
		
		$res["data"]= $goods;
		die(json_encode_yec($res));
	}
	
	/*推荐品牌及期商品*/
	public function recommend_brand_goods()
	{
		global $name,$res, $page, $num, $goods_num;						
		$name = isset($name) ? addslashes($name) : ''; 
		$goods_num = isset($goods_num) ? intval($goods_num) : 10;
		$page=intval($page)==0 ? 1 : intval($page);
		if(isset($num))
		{
			$num =  intval($num);
			$startI = $page * $num - $num;
		}
		else {
			$num =  null;
			$startI =0;
		}
		 
		dbc();
		$row = get_brand(0, $name, 1, $startI, $num);
		if($row){
			foreach($row as $k => $v){
				$row[$k]['logo'] = url_to_abs($v['logo']);
				$row[$k]['banner'] = url_to_abs($v['banner']);
				$goods = get_goods_list(" and g.brand_id =".$v['id'], 'g.*', 'addtime desc', 0 , $goods_num,1,1);
				if($goods && count($goods)>0)
				{
					foreach ($goods as $key => $val) {
						$goods[$key]['img'] = url_to_abs($val['img']);
						$goods[$key]['thumb'] = url_to_abs($val['thumb']);
						unset($goods[$key]['specs']);
						unset($goods[$key]['imgs']);
						unset($goods[$key]['specs']);
						unset($goods[$key]['details']);
						unset($goods[$key]['details_m']);
						unset($goods[$key]['service']);
						unset($goods[$key]['service_m']);
					}
				}
				
				$row[$k]['goods']= $goods;
			}
			$res['data'] = $row;
		}
		
		die(json_encode_yec($res));
	}
	
	/*短信接口*/
	public function sms()
	{
		global $act,$res,$type,$mobile;
		$act = $type;
		if($act =='sms_findpwd')
		{
			session_start();
			$_SESSION['findpwd_mobile'] = $mobile;
		}
		include("./inc/module/user_api.php");
	}
	
	/*上传图片*/
	public function upload_img()
	{
		global $uid,$ym_uid,$dir,$res;
		
		$is_h5 = 1;
		$_REQUEST["uploadDir"] = $dir;
		$ym_uid = $uid;
		if($ym_uid == 0){
			$res["res"] = "unlogin";
			$res["err"] = "用户id不能为空";die(json_encode_yec($res));
		} 
	
		include("./inc/module/upload_img.php"); 
		
		die(json_encode_yec($res));
	}
	
	/*自定义内容*/
	public function diy()
	{
		global $res, $diy_codes;

		if (!file_exists(cache_data."diy.php")) {
			$res['err'] = "数据未生成";
			die(json_encode_yec($res));
		}
		require cache_data."diy.php";
		
		$diy_codes = isset($diy_codes) ? trim($diy_codes) : '';
		if($diy_codes == '')
		{
			$res['err'] = "未提供diy_codes参数";
			die(json_encode_yec($res));
		}
		$diy_codes_arr = explode(',', $diy_codes);
		$row = array();
		if($diy_codes_arr && count($diy_codes_arr)>0)
		{
			foreach ($diy_codes_arr as $k => $v) {			 	
			 	$tmp_code = "diy_".$v;
				$diy_arr =  $$tmp_code;
				if(is_array($diy_arr) && count($diy_arr)>0) 
				{  
					if($diy_arr[0] && is_array($diy_arr[0]) && array_key_exists('goods', $diy_arr[0]))//商品
					{									
						foreach($diy_arr as $key => $val) {
							$diy_arr[$key]['img'] = url_to_abs($diy_arr[$key]['img']); 
							$diy_arr[$key]['url'] = url_to_abs($diy_arr[$key]['url']);
							if(is_array($val['brands']))
							{
								foreach ($val['brands'] as $i => $j) {
									$diy_arr[$key]['brands'][$i]['logo'] = url_to_abs($diy_arr[$key]['brands'][$i]['logo']);
								}
							}
							if(is_array($val['child']))
							{
								foreach ($val['child'] as $i => $j) {
									$diy_arr[$key]['child'][$i]['img'] = url_to_abs($diy_arr[$key]['child'][$i]['img']);
									$diy_arr[$key]['child'][$i]['url'] = url_to_abs($diy_arr[$key]['child'][$i]['url']);
								}
							}
							if(is_array($val['goods']))
							{
								foreach ($val['goods'] as $i => $j) {
									$diy_arr[$key]['goods'][$i]['img'] = url_to_abs($diy_arr[$key]['goods'][$i]['img']);
									$diy_arr[$key]['goods'][$i]['thumb'] = url_to_abs($diy_arr[$key]['goods'][$i]['thumb']);
									$diy_arr[$key]['goods'][$i]['url'] = url_to_abs($diy_arr[$key]['goods'][$i]['url']);
									$tmp_imgs = json_decode($diy_arr[$key]['goods'][$i]['imgs'], true);
									if($tmp_imgs && count($tmp_imgs)>0)
									{
										foreach ($tmp_imgs as $m => $n) {
											$tmp_imgs[$m]['img'] = url_to_abs($n['img']);
											$tmp_imgs[$m]['thumb'] = url_to_abs($n['thumb']);
										}
									}
									$diy_arr[$key]['goods'][$i]['imgs'] = $tmp_imgs;
								}
							}
						}
					}
					else{//文章
						foreach($diy_arr as $key => $val) {
							$diy_arr[$key]['img'] = url_to_abs($diy_arr[$key]['img']); 
							$diy_arr[$key]['bimg'] = url_to_abs($diy_arr[$key]['bimg']); 
							$diy_arr[$key]['simg'] = url_to_abs($diy_arr[$key]['simg']); 
							$diy_arr[$key]['url'] = url_to_abs($diy_arr[$key]['url']); 
						}	
					}		
				}
				else { //自定义
					$arr = json_decode($diy_arr, true);
					if(json_last_error() == JSON_ERROR_NONE)//主要是数组，如：只要图片url
					{
						if(count($arr) >1)
						{
							foreach ($arr as $key => $val) { 	
								$arr[$key] = replace_uploadURL($val);
							}
							$diy_arr = $arr;
						}
						else {
							$diy_arr = replace_uploadURL($arr[0]);
						}						
					}
					else {
						$diy_arr = replace_uploadURL($diy_arr);
					}
				}
			 	$row[$v] = $diy_arr;
			}
		}
	
		$res['data'] = $row;
		die(json_encode_yec($res));
	}

	/*banner海报*/
	public function banner()
	{
		global $res, $code;

		if (!file_exists(cache_data."diy.php")) {
			$res['err'] = "数据未生成";
			die(json_encode_yec($res));
		}
		require cache_data."diy.php";
		
		$code = isset($code) ? trim($code) : '';
		if($code == '')
		{
			$res['err'] = "未提供code参数";
			die(json_encode_yec($res));
		}
		$code_arr = explode(',', $code);
		$row = array();
		if($code_arr && count($code_arr)>0)
		{
			foreach ($code_arr as $k => $v) {			 	
			 	$tmp_code = $v;
				$diy_arr =  $$tmp_code;
				foreach ($diy_arr as $key => $val) {
					$diy_arr[$key]['url'] = url_to_abs($diy_arr[$key]['url']);
					$diy_arr[$key]['img'] = url_to_abs($diy_arr[$key]['img']);
					$diy_arr[$key]['simg'] = url_to_abs($diy_arr[$key]['simg']);
					$diy_arr[$key]['bimg'] = url_to_abs($diy_arr[$key]['bimg']);
				}
				
			 	$row[$v] = $diy_arr;
			}
		}
	
		$res['data'] = $row;
		die(json_encode_yec($res));
	}
	
	/** 导航
	 *@param $type= mid top bot,不提供则为全部 */
	public function nav()
	{
		global $type,$res;
		$type = isset($type) ? trim($type): '';
		$res['data'] = get_nav($type);
		die(json_encode_yec($res));	
	}
	
	/*分类树 */
	public function cats()
	{
		global $pid,$res;
		$pid = isset($pid) ? intval($pid): 0;
		
		$res['data'] = get_catTree($pid); //分类树		
		die(json_encode_yec($res));	
	}
		
	/*站点配置文件 */
	public function shop_config()
	{
		global $res;	 
		$db = dbc();
		$row = $db->fetchall('shop_config', '*'); 
	    
		//站点设置
	    foreach ($row as $k => $v) {
	    	$v['value'] = str_replace("'", "\\'", stripslashes($v['value']));
	    	switch ($v['key']) {
	    		case 'keepword'://用户名保留字
	    			unset($row[$k]);
	    			break;
	    		case 'invoice_con'://发票内容
	    			$row[$k] = array_filter(explode(PHP_EOL, $v['value'])); 
	    			break;
				case 'ditribution_config'://分销
					$ditrib = json_decode($v['value'], true);
					$commiss =array();
					foreach ($ditrib['commission'] as $key => $val) {
						$commiss[$val['id']] = $val;
					}
					$ditrib['commission'] = $commiss;
					$row[$k] = $ditrib; 
					break;
				case 'chat_config'://聊天配置
					$row[$k] = json_decode($v['value'], true);
					break;
	    		default:
	    			break;
	    	}   	
	    }	

		$is_pickup = get_express_common(0, 'pickup');
		$row[] = array('key'=>'is_pickup', 'value'=>(count($is_pickup)>0 ?  $is_pickup[0]['status'] : 0),'desc'=>'是否开自提点');
		
		$row[] = array('key'=>'exp_sp', 'value'=>'kdniao','desc'=>'默认快递服务商');
		
		//是否开启余额支付
		require_once './inc/lib/pay.php'; 
		$is_bal = get_payment('bal', 1, '1', 0); //支付方式
		$row[] = array('key'=>'is_bal', 'value'=>(count($is_bal)>0 ? "1" : "0"),'desc'=>'是否开启余额支付');
        
		$res['data'] = $row;

		die(json_encode_yec($res));	
	}
	
	/*获取城市数据*/
	public function get_area()
	{
		global $act, $res, $pid;
		require "./inc/module/cart.php";
	}
	
	
}

?>