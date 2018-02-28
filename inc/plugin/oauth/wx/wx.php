<?php 
if (!defined('in_mx')) {exit('Access Denied');}

/*微信登录 pc+wap*/

require_once dirname(__FILE__).'/wxoauth.php';
$oauth_c =new wxoauth();

if($act)
{
	if($act=='callback')
	{	
		$data = $oauth_c->callback();
		$openid = $data['openid'];  

		if(empty($openid))
		{
			session_start();
			unset($_SESSION['oauth_userinfo']);
			$_SESSION['wx_autologin_fail'] = 1;
			message("获取openid失败", "login.html");
		}
			
		dbc();
		
		$res = oauth_login($oauth_c, 'wx', $openid, $_SESSION['wx_platform'], $data['unionid'], $data['scope']);			
		if($res['err'] !='')
		{
			unset($_SESSION['oauth_userinfo']);
			$_SESSION['wx_autologin_fail'] = 1;
		}
		redirect("index.html");die();		

		/*$user = get_member_oauth('wx', $openid, $_SESSION['wx_platform']);  //微信需要区分平台
		if($user && count($user)>0 && $user['mobile'] !='') //已经绑定过，直接登录
		{
			if($user['status'] == locked)
			{
				session_start();
				$_SESSION['wx_autologin_fail'] = 1;
			 	message("出于安全原因，系统已冻结您的账户，请与客服联系。", "login.html");
			}
			
			set_login_session($user['uid'], $user['uname'], 0);
			unset($_SESSION['wx_autologin']);
			redirect("user.html");
		}
		else {			
			session_start();
			$res = $oauth_c->get_user_info(); //获取用户微信信息
			$oauth_userinfo = array('openid'=>$openid);
			if($res['ret'] == 0){
				$oauth_userinfo['type'] ='wx';
				$oauth_userinfo['nickname'] = $res['nickname'];
				$oauth_userinfo['avatar'] = $res['headimgurl']; //头像 68x68
				$oauth_userinfo['platform'] = $_SESSION['wx_platform'];
				$oauth_userinfo['unionid'] = $res['unionid']; 
			}
			$_SESSION['oauth_userinfo'] = json_encode($oauth_userinfo);
			
			if($_SESSION['wx_autologin'])
			{
				unset($_SESSION['wx_autologin']);
				$oauth_userinfo['openid'] = jcode($oauth_userinfo['openid'], ym_token);
				set_cookie("oauth_info", json_encode($oauth_userinfo));
				redirect("index.html");
			}
			else {
				redirect("login.html");
			}			
		}*/
	}	
}
else{ 
	$oauth_c->login(true, $ym_client);
}
die();


		
?>	