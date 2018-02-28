<?php
if (!defined('in_mx')) {exit('Access Denied');}

if(isset($act))
{
	$res = array('err' => '', 'res' => '', 'data' => array());
	if ($act == 'set_paypwd')
	{
		if(check_login(1)==0)
		{
			$res['url']= 'login.html';
			die(json_encode_yec($res));
		}
		
		require("./inc/lib/sms.php");
		$authcode = !isset($authcode)? 0:trim($authcode);
		$paypwd = !isset($paypwd)?'':trim($paypwd);
		$repaypwd = !isset($repaypwd)?'':trim($repaypwd);
		
		dbc();			
		$user = get_user($ym_uid);
		$mobile = $user['mobile'];
		if(strlen($authcode)< 4)
		{
			$res['err']= '短信验证码不正确';
			die(json_encode_yec($res));
		}
		else {
			$sms_session = get_sms_session($mobile, $authcode, "setpaypwd");
			if(!$sms_session) {
				$res['err']="短信验证码不正确";
			 	die(json_encode_yec($res));
			}
		}
		if(strlen($paypwd)< 6 || strlen($repaypwd)< 6)
		{
			$res['err']= '请填写支付密码';
			die(json_encode_yec($res));
		}
		if(!is_enAndnum($paypwd))
		{
			$res['err']= '请使用数字和字母作为支付密码';
			die(json_encode_yec($res));
		}
		if($paypwd !== $repaypwd)
		{
			$res['err']= '两次输入的支付密码不一致';
			die(json_encode_yec($res));
		}
				
		update_sms_status($mobile, 'setpaypwd');//更新短信验证码为已用
			
		$salt = get_salt(); //生成新salt
		update_userinfo($ym_uid, array('paypwd' => md5(md5($paypwd, $salt)),'salt_pay' =>$salt));
		
		$res['res']= 'ok';
	}
	elseif ($act == 'logout')
	{
		unset($_SESSION['uid']);
		unset($_SESSION['uname']);
		set_cookie('user', ''); 
		set_cookie('cnum', '');
		
		dbc();
		del_session(session_id());
		session_regenerate_id();//生成新会话
		session_start();
		set_cookie('ym_logout', '1');//标记人工退出，防止微信内自动登录
	
		if(isset($ym_sid))
		{
			/*if(file_exists(session_save_path()."/sess_".$ym_sid))
			{
				session_id($ym_sid);
				session_unset();
				session_write_close();
			}*/			
			die();
		}
		else {
			header("Location:index.html");
		}				
	}
	elseif ($act == 'add_comment_reply')
	{
		$content = isset($content) ? trim($content) : '';
		$cid = isset($cid) ? intval($cid) : '';
		/*if(intval($gid)==0)
		{
			$res['err'] = '获取商品号失败';
			die(json_encode_yec($res));
		}*/
		if(intval($pid)==0)
		{
			$res['err'] = '回复的对象错误';
			die(json_encode_yec($res));
		}
		if(!isset($content) || $content == '')
		{
			$res['err'] = '请输入回复内容';
			die(json_encode_yec($res));
		}
		elseif(mb_strlen($content,'utf-8')>120) {
			$res['err'] = '字数请控制在120个以内';
			die(json_encode_yec($res));
		}
		if($ym_uid==0) //需要登录
		{
			$ym_uid =check_login(1);
			if($ym_uid==0)
			{
				$res['url'] = 'login.html';
				die(json_encode_yec($res));
			}
		}
		dbc();		
		if(check_comment_reply($ym_uid, getip())==false)
		{
			$res['err'] = '您评论的有点频繁了，请休息一小时再来吧~';
			die(json_encode_yec($res));
		}
		$reply_id = get_comment_uid(intval($pid), intval($ptype));
		$id = add_comment_reply($cid,$ym_uid, $reply_id, intval($pid), intval($ptype), role_user, $content);
		$res['res'] = $id;
	}
	elseif ($act == 'get_comment')
	{
		$num = isset($num) ?intval($num) : 10;
		$id = !isset($id)?0 :intval($id);
		$star_level=!isset($level) ?'':trim($level); //星级：good好  mid中 bad差
		$page=intval($page)==0 ? 1 : intval($page);
		$start = $page * $num - $num;
		$is_count = !isset($is_count)?0 :intval($is_count);
		
		if($id ==0)
		{
			$res['err'] = '获取商品号失败';
			die(json_encode_yec($res));
		}
		dbc();

		$count = get_comment_count($id, $star_level, 0,$ym_uid); 
		$row = get_comment_list($id, $star_level, 0, $start, $num, $ym_uid);
		if($row){
			foreach($row as $k=>$v){
				$row[$k]["uimg"]= url_to_abs($v['uimg']);
				$row[$k]["uname"]= $v['uname'];
				$row[$k]["content"]= $v['content'];
				$row[$k]["time"]= $v['addtime'];
				if($v['img'])
				{
					foreach($v['img'] as $a => $b){
						$row[$k]["img"][$a]= url_to_abs($b);
					};
					foreach($v['thumb'] as $a => $b){
						$row[$k]["thumb"][$a]= url_to_abs($b);
					};
				}
			}
		}
		if($count<= $start + $num)
		{
			$res['res'] = '1';
		} 
		if($is_count == 1)
		{
			$res['count'] = $count;
		}
	 	$res['data'] = $row;
	}
	elseif ($act == 'get_comment_reply') //某人评价的回复
	{
		dbc();
		$cid = isset($cid)? intval($cid) : 0;
		if($cid ==0)
		{
			$res['err'] = '获取评论编号失败';
			die(json_encode_yec($res));
		}
		$row =get_comment_reply(0, $cid);
	 	$res['data'] = $row;
	}
	elseif ($act == 'edit_userinfo') //编辑个人信息
	{
		if($ym_uid==0) //需要登录
		{
			$ym_uid =check_login(1);
			if($ym_uid==0)
			{
				$res['url'] = 'login.html';
				die(json_encode_yec($res));
			}
		}
		
		dbc();

		$data = array();
		if(isset($username))
		{
			$username = addslashes(trim($username));
			if($username=='' || mb_strlen($username)<3)
			{
				$res['err']="用户名太短";
			 	die(json_encode_yec($res));
		 	}
			$name_err = check_uname($username, true, $ym_uid);
			if($name_err !='')
			{
				$res['err']= $name_err;
				 die(json_encode_yec($res));
			}			
			
			$data['uname'] = $username;
		}
									
		if(isset($img))
		{
			$data['img'] = addslashes(trim($img));
		}
		if(isset($sex))
		{
			$data['sex'] = intval($sex);
		}
		if(isset($email))
		{
			$data['email'] = addslashes(trim($email));
		}
		if(isset($birthday))
		{
			$data['birthday'] = strtotime($birthday);
		}
		if(isset($realname))
		{
			$data['realname'] = addslashes(trim($realname));
		}
		if(isset($memo))
		{
			$data['memo'] = addslashes(trim($memo));
		}
		
	 	update_userinfo($ym_uid, $data);
	}
    elseif ($act == 'bind_mobile') //绑定手机号
	{
		require("./inc/lib/sms.php");		
		$mobile = isset($mobile) ? trim($mobile) : "";
		$smscode = isset($smscode) ? trim($smscode) : "";
		
		if($mobile =='')
		{
			$res['err'] = "请输入手机号码";
			die(json_encode_yec($res));			
		}
		if(is_mobile($mobile) ==false)
		{
			$res['err'] = "手机号码不正确";
			die(json_encode_yec($res));			
		}
		
		if($ym_uid==0) //需要登录
		{
			$ym_uid =check_login(1);
			if($ym_uid==0)
			{
				$res['url'] = 'login.html';
				die(json_encode_yec($res));
			}
		}
		dbc();
		$user = get_user($ym_uid);
		if(check_user_field('mobile', $mobile) && $user['mobile'] != $mobile)
		{
			$res['err'] = "手机号码已存在";
			die(json_encode_yec($res));	
		}
				
		if(trim($smscode)=='' || strlen($smscode)<4)
		{
			$res['err']="请填写短信验证码";
			die(json_encode_yec($res));
		}
		$sms_session = get_sms_session($mobile, $smscode, "changemobile");
		if(!$sms_session) {
			$res['err']="短信验证码不正确";
			die(json_encode_yec($res));
		}

		update_sms_status($mobile, 'changemobile');//更新短信验证码为已用
		
	 	update_userinfo($ym_uid, array('mobile'=>$mobile));
	}
	elseif ($act == 'check_uname')
	{
		$uname = !isset($uname)?'':trim($uname);
		$checkcode = !isset($checkcode)?'':trim($checkcode);
				
		if($uname =='')
		{
			$res['err']= '请填写用户名/手机号码';
			die(json_encode_yec($res));
		}
		if($checkcode =='')
		{
			$res['err']= '请填写验证码';
			die(json_encode_yec($res));
		}
		if(strtolower($_SESSION['vcode']) != strtolower($checkcode))
		{
			$res['err']= '验证码不正确';
			die(json_encode_yec($res));
		}
 		session_start();
		if(isset($_SESSION['findpwd_count']))
		{
			$n = intval($_SESSION['findpwd_count']);
			if($n>10)
			{
				$res['err']= '你输错次数太多，明天再来吧';
				die(json_encode_yec($res));
			}
			$_SESSION['findpwd_count'] = $n + 1;
		}
		else {
			$_SESSION['findpwd_count'] = 1;
		}
		
		dbc();
		$user = check_username($uname);
		if($user==false)
		{
			$res['err']= '您输入的账户名不存在';
			die(json_encode_yec($res));
		} 
		if($user && intval($user['id'])>0)
		{			
			$_SESSION['findpwd_uid'] = $user['id'];
			$_SESSION['findpwd_mobile'] = $user['mobile'];
			die(json_encode_yec($res));
		} 
		else {
			$res['err']= '0';
			die(json_encode_yec($res));
		}
	}
	elseif ($act == 'updatepwd') //修改登陆密码
	{
		require("./inc/lib/sms.php");
		$oldpwd = !isset($oldpwd)?'':trim($oldpwd);
		$pwd = !isset($pwd)?'':trim($pwd);
		$repwd = !isset($repwd)?'':trim($repwd);
		$findpwd_uid = isset($_SESSION['findpwd_uid'])? intval($_SESSION['findpwd_uid']) :0 ;
		$is_findpwd= isset($_SESSION['findpwd_checkcode'])? trim($_SESSION['findpwd_checkcode']):0; //是否找回密码
		$authtype = !isset($authtype) ? 0:intval($authtype); //验证身份方式，0密码，1短信
		$smscode = !isset($smscode)?'': trim($smscode);
				
		if($findpwd_uid > 0)
		{
			if($is_findpwd ==0)
			{
			 	$res['err']= '请先验证手机号';
				die(json_encode_yec($res));
			}
		}
		else{
			if($authtype == 0)
			{
				if($oldpwd =='')
				{
					$res['err']= '请填写旧密码';
					die(json_encode_yec($res));
				}
				elseif(strlen($oldpwd)< 6)
				{
					$res['err']= '旧密码不正确';
					die(json_encode_yec($res));
				}
			}
			elseif($smscode=='' && $is_findpwd !=1)
			{
				$res['err']= '手机验证码不正确';
				die(json_encode_yec($res));	
			}
		}		
		
		if($pwd =='')
		{
			$res['err']= '请填写新密码';
			die(json_encode_yec($res));
		}
		elseif(strlen($pwd)< 6)
		{
			$res['err']= '新密码长度为6-20个字符';
			die(json_encode_yec($res));
		}
		if($repwd =='')
		{
			$res['err']= '请填写确认新密码';
			die(json_encode_yec($res));
		}
		if($pwd !== $repwd)
		{
			$res['err']= '两次输入的密码不一致';
			die(json_encode_yec($res));
		}				
		
		if($ym_uid == 0)
		{
			$ym_uid = check_login(1);
		}				
		if($findpwd_uid ==0 && $ym_uid==0)
		{
			$res['url']= 'login.html';
			die(json_encode_yec($res));	
		}
		dbc();
		$ym_uid =$findpwd_uid>0 ? $findpwd_uid : $ym_uid;
		$user = get_user($ym_uid);
		if($findpwd_uid ==0)
		{
			if($authtype ==0 && $user['pwd'] !== encryptStr($oldpwd, $user['salt']))
			{
				$res['err']= '旧密码不正确';
				die(json_encode_yec($res));
			}
			elseif($authtype ==1){
				$res['err']= check_smscode($user['mobile'], $smscode, 'updatepwd');
				if($res['err'] !="")
				{			
				 	die(json_encode_yec($res));
				}
				update_sms_status($user['mobile'], 'updatepwd');//更新短信验证码为已用
			}			
		}
				
		$salt= get_salt(); //生成新salt		
		update_userinfo($ym_uid, array('pwd' =>encryptStr($pwd, $salt),'salt' =>$salt));
		if($findpwd_uid>0) //如是找回密码，直接登录
		{
			set_login_session($ym_uid, $user['uname'], 0); 
		}
	}
	elseif ($act == 'findpwd_checkcode') //找回密码，检验验证码
	{	
		$smscode = !isset($smscode)?'':trim($smscode);	
		$uid = isset($_SESSION['findpwd_uid'])? intval($_SESSION['findpwd_uid']) :0 ; 
		$mobile = isset($_SESSION['findpwd_mobile'])? trim($_SESSION['findpwd_mobile']) :0 ;
			
		if($smscode =='' || strlen($smscode)<4)
		{
			$res['err']= '请输入验证码';
			die(json_encode_yec($res));
		}		
		if($uid ==0)
		{
			$res['err']= "操作超时，请重新找回密码";
			die(json_encode_yec($res));
		}
		
		dbc();
		require("./inc/lib/sms.php");
		$sms_session = get_sms_session($mobile, $smscode, 'updatepwd');
		if(!$sms_session) {
			$res['err']="短信验证码不正确";
			die(json_encode_yec($res));
		}
		
		update_sms_status($mobile, 'updatepwd');//更新短信验证码为已用
		session_start();
		$_SESSION['findpwd_checkcode']=1;
		$res["res"]= endecrypt($sms_session['id'], 'ENCODE', ym_token); 
	}
	elseif ($act == 'addfav') //商品加入收藏
	{		
		$gid_list = explode("@", $gid);
		$spec_list = explode("@", $spec);
		
		$db = dbc();
		if(!isset($ym_uid) || $ym_uid==0) //需要登录
		{
			$ym_uid =check_login(1);
			if($ym_uid==0)
			{
				$res['url'] = 'login.html';
				die(json_encode_yec($res));
			}
		}

		foreach ($gid_list as $k => $v) {
			$sp = $spec_list[$k];
			if(intval($v) <= 0 )			
			{
				continue;
			}
			$goods = get_fav($ym_uid, $v, $sp);
			if(count($goods)>0)
			{
				$res['err'] = '您已收藏过了';
				die(json_encode_yec($res));
			}
			add_fav($ym_uid, $v, $sp);
		}
		
		die(json_encode_yec($res));
	}
	elseif ($act == 'del_fav') //取消收藏
	{		
		$gid = isset($gid) ? intval($gid): 0;
		$spec = isset($spec) ? trim($spec): '';
		if($gid==0)
		{
			$res['err'] = '商品编号不能为空';
			die(json_encode_yec($res));
		}
		
		$db = dbc();
		if(!isset($ym_uid) || $ym_uid==0) //需要登录
		{
			$ym_uid =check_login(1);
			if($ym_uid==0)
			{
				$res['url'] = 'login.html';
				die(json_encode_yec($res));
			}
		}
		
		del_fav($ym_uid, $gid, $spec);		
		$res['res'] = 'ok';
		die(json_encode_yec($res));
	}
	elseif ($act == 'cancel_service') //取消服务单
	{		
		$id = isset($id) ? intval($id): 0;
		if($id==0)
		{
			$res['err'] = '服务单号不能为空';
			die(json_encode_yec($res));
		}

		$db = dbc();
		if(!isset($ym_uid) || $ym_uid==0) //需要登录
		{
			$ym_uid =check_login(1);
			if($ym_uid==0)
			{
				$res['url'] = 'login.html';
				die(json_encode_yec($res));
			}
		}
		
		update_order_service($id, array('status'=>service_cancel));		
		die(json_encode_yec($res));
	}
	elseif($act == 'updatemobile') {
		$ym_uid = check_login();
		dbc();
		$user = get_user($ym_uid);
		@include template('updatemobile', $ym_tpl."/");
		die();
	}
	if(!$ym_is_api)
	{	
		die(json_encode_yec($res));	
	}
}	
else {
	dbc();
	
	$nav = get_nav(); //导航
	$nav_footer = get_nav('bot');
	$cats = get_catTree(); //分类树
	$help = get_help(); //帮助
	
	$ym_uid = check_login();
	$user = get_user($ym_uid);
	
	$count_unpay = order_count($ym_uid, order_paying); //待付款数
	$count_deliver = order_count($ym_uid, order_deliver); //待发货
	$count_receiving = order_count($ym_uid, order_receiving); //待收货
	$count_comment = order_count($ym_uid, order_finish, "and is_comment=0"); //待评价
	
	$order = get_order_list(0,'',$ym_uid, '', 0, 2); //print_r($order);
	
	$history = get_history(10); 	
} 
 

?>