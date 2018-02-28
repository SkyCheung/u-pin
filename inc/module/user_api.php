<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*session_cache_limiter('private');
session_start();*/

/*会员注册、登录和发送短信验证码*/
$db=dbc();
$res = array('err' => '', 'res' => '', 'data' => array()); 
require("./inc/function/const.php");
require("./inc/lib/sms.php");

if($act =="reg")
{
	$tel=!isset($tel)?'': make_semiangle(trim($tel));
	$username = isset($username)? trim($username): '';
	$password = isset($password)? trim($password): '';
	$img = isset($img)? trim($img): '';
	$email = isset($email)? trim($email): '';
	$birthday = isset($birthday)? trim($birthday): 0;
	$qq = isset($qq)? trim($qq): '';
	$sex = isset($sex)? intval($sex): '';
	$realname = isset($realname)? trim($realname): '';	
	$smscode = isset($smscode)? trim($smscode): '';
	$agree = isset($agree)? intval($agree): 1;
	$authtype  = isset($authtype)? intval($authtype): 0;
		
	if($username =='')
	{
		$username = build_username();
	}
	else {
		/*if(mb_strlen($username,'utf-8')<4 || mb_strlen($username,'utf-8')>20 || !is_username($username) )
		{
			$res['err']= "请使用中文、字母、数字、“-”和“_”的组合，4-20个字符";
		 	die(json_encode_yec($res));
		}
		if(is_num($username))
		{
			$res['err']= "用户名不能是纯数字";
		 	die(json_encode_yec($res));
		}
		if(check_censor($username)==false)
		{
			$res['err']= "该用户名已注册";
		 	die(json_encode_yec($res));
		}*/
		$name_err = check_uname($username);
		if($name_err !='')
		{
			$res['err']= $name_err;
		 	die(json_encode_yec($res));
		}
	}		
	
	if($tel==0 || is_mobile($tel)==false)
	{
		$res['err']= "手机格式不正确";
	 	die(json_encode_yec($res));
	}
	$row=$db->fetch('member', 'mobile, uname', "mobile=".$tel." or uname='".$username."'");
	if($row)
	{
		if($row['mobile']==$tel)
		{
			$res['err']= "该手机号已注册账号";
		 	die(json_encode_yec($res));
		}
		if($row['uname']==$username)
		{
			$res['err']= "该用户名已注册，请更换";
		 	die(json_encode_yec($res));
		}
	}
	if($smscode =='' || strlen($smscode)<4)
	{
		$res['err']="请填写短信验证码";
	 	die(json_encode_yec($res));
	}
	$sms_session = get_sms_session($tel, $smscode, "reg");
	if(!$sms_session) {
		$res['err']="短信验证码不正确";
	 	die(json_encode_yec($res));
	}
	if($authtype ==1)//验证方式:1 短信/0 短信+密码
	{
		$password = get_salt(8);		
	}
	else {
		if($password=='' || mb_strlen($password,'utf-8')<6 || mb_strlen($password,'utf-8')>20)
		{
			$res['err']= "密码不正确，请使用6-20个字符";
		 	die(json_encode_yec($res));
		}
		if($password !== trim($repassword))
		{
			$res['err']="两次密码输入不一致";
		 	die(json_encode_yec($res));
		}
	}
	if($agree==0) 
	{
		$res['err']="请先阅读并同意注册协议";
	 	die(json_encode_yec($res));
	}
	$oauth_userinfo = json_decode($_SESSION['oauth_userinfo'], true);
	unset($_SESSION['oauth_userinfo']);
		
	if($oauth_userinfo) //下载头像
	{
		$img = get_oauthimg($oauth_userinfo['avatar']);	
	}
	elseif(!$img)
	{
		$img = '';
	}
				
	if($_SESSION['ditrib_id'] && $ym_ditribution_config['distrib_level']>0)	//分销	
	{
		require_once "inc/lib/distrib.php";
		$pids = get_parent_uid(intval($_SESSION['ditrib_id']));	
		$pids['pid3'] = $pids['pid2'];
		$pids['pid2'] = $pids['pid1'];
		$pids['pid1'] = $_SESSION['ditrib_id'];
	}
	else {
		$pids = array('pid1'=>0,'pid2'=>0,'pid3'=>0);
	}
	
	$uid = add_user($username,$tel,$password,$img, $email,$birthday,$qq, $sex,$realname, $pids);
	if($uid==0)
	{
		$res['err']="注册失败，请稍后再试";
	 	die(json_encode_yec($res));
	}
	
	update_sms_status($tel, 'reg');//更新短信验证码为已用
	
	if($isOauth && intval($isOauth)>0)
	{
		if($openid && $openid !='')
		{
			$oauth_userinfo['type'] = $type;
			$oauth_userinfo['platform']= $platform;
			$oauth_userinfo['openid']= $openid;
			$oauth_userinfo['unionid']= $unionid;
		}	
		if($oauth_userinfo)
		{
			$result = bind_user($uid, $oauth_userinfo);
			if($result !='')
			{
				del_user($uid);
				$res['err']= $result;
				die(json_encode_yec($res));
			}
		}		
	}
	set_login_session($uid, $username);
	
	$res['res']="ok";
	$res['sid']= get_sid();
	if($ym_is_api)
	{	
		$res['data']= array('id'=>$uid);
	}
	die(json_encode_yec($res)); 
}
elseif($act =="login")//登录
{
	$username = isset($username) ? trim($username) : '';//用户名/手机号
	$authtype = isset($authtype) ? intval($authtype) : 0;
	if($username=='' || mb_strlen($username)<3)
	{
		$res['err']="请输入用户名/手机号";
	 	die(json_encode_yec($res));
	}
	if($authtype ==1)//验证方式:1 短信/0 短信+密码
	{
		$res['err']= check_smscode($username, $smscode, 'login');
		if($res['err'] !="")
		{			
		 	die(json_encode_yec($res));
		}		
	}
	else {
		if(trim($password)=='' || strlen(trim($password))<6)
		{
			$res['err']= "请输入密码";
		 	die(json_encode_yec($res));
		}
	}			 	
				
	if(is_mobile($username))
	{
		update_sms_status($username, 'login');//更新短信验证码为已用
		$user = get_user_by_mobile($username, '');
	}
	else {
		$user = get_user_by_mobile('', $username);
	}	
	if(!$user)
	{
		$res['err']= "用户不存在";
	 	die(json_encode_yec($res));
	}
	if($user['status']==locked)
	{		
		$res['err']= "出于安全原因，系统已冻结您的账户，请与客服联系。";
	 	die(json_encode_yec($res));
	}	
	
	require_once("./inc/lib/system.php");
	$count = get_login_count($user['id'], role_user);
	if($count >=3)
	{
		if(!isset($authcode)|| strlen(trim($authcode))!=4)
		{
			$res['data']= array('authcode'=>'1'); 
			$res['err']= "请正确输入验证码".$authcode;
	 		die(json_encode_yec($res));
		}
		elseif(strtolower($_SESSION['vcode']) != strtolower($authcode)) {
			$res['data']= array('authcode'=>'1'); 
			$res['err']= "验证码错误";
	 		die(json_encode_yec($res));
		}
	}
	if($count>=6)
	{
		//$lasttime = $login_log[0]['lasttime'];//最后提交时间
		//$lefttime = $lasttime-time();		
		$res['err']= "密码错误6次，请1小时后再试，或您可以找后密码";
	 	die(json_encode_yec($res));
	}	
	
	if($authtype ==0)//验证方式:1 短信/0 短信+密码
	{
		$salt = $user['salt']; 
		if($user['pwd'] != encryptStr($password, $salt) )
		{			
			$count= $count + 1;
			add_login_log($user['id'], role_user, login_fail);
			if($count >=2)
			{
				$res['data']= array('authcode'=>'1'); 
			}
			if($count == 4 || $count == 5)
			{
				$err= "账户名或密码不匹配，您还可以尝试".(6-$count)."次";
			}
			elseif($count>=6)
			{
				$err= "密码错误6次，请1小时后再试，或您可以找回密码";
			}
			else {
				$err="账户名或密码不匹配，请重新输入";
			}
			$res['err']= $err; 
		 	die(json_encode_yec($res));
		}
	}		
		
	if($isOauth && intval($isOauth)>0 && $_SESSION['oauth_userinfo'])//绑定第三方登录
	{
		$oauth_userinfo = json_decode($_SESSION['oauth_userinfo'], true);
		unset($_SESSION['oauth_userinfo']);		
		$result = bind_user($user['id'],$oauth_userinfo);
		if($result !='')
		{
			$res['err']= $result;
			die(json_encode_yec($res));
		}
		if(trim($user['img']) =='') //无头像时，从第三方拉取
		{			
			$img = get_oauthimg($oauth_userinfo['avatar']);	
			update_userinfo($user['id'], array('img'=>$img));
		}	
	}
		
	set_login_session($user['id'], $user['uname'], $autologin);

	$res['sid']= get_sid();
	if($ym_is_api)
	{	
		return $user; 
	}
	else {
		$res['res']="登录成功!";
		die(json_encode_yec($res));
	}
}
elseif ($act =='oauth_login')//第三方登录
{ 
	$res['sid']= '';
	if($oauth_type=='' || $openid =='')
	{
		$res['err']= '请提供完整的oauth_type和openid';
		die(json_encode_yec($res));
	}
	$user = get_member_oauth($oauth_type, $openid, $platform);  //微信需要区分平台
	if($user && count($user)>0 && $user['mobile'] !='') //已经绑定过，直接登录
	{
		if($user['status'] == locked)
		{
			$res['err']= '出于安全原因，系统已冻结您的账户，请与客服联系。';
			die(json_encode_yec($res));
		}
		set_login_session($user['uid'], $user['uname'], 1);
		
		$res['sid']= get_sid();
		die(json_encode_yec($res));
	}
	else {			
		$res['res']='unbind';		
		die(json_encode_yec($res));
	}
}
elseif($act =='user_bind') //第三方注册绑定手机号
{
	$mobile=!isset($tel)?'': make_semiangle(trim($tel));
	$smscode = isset($smscode)? trim($smscode): '';
	
	if($mobile==0 || is_mobile($mobile)==false)
	{
		$res['err']= "手机格式不正确";
	 	die(json_encode_yec($res));
	}
	if($smscode =='' || strlen($smscode)<4)
	{
		$res['err']="短信验证码不正确";
	 	die(json_encode_yec($res));
	}
	$sms_session = get_sms_session($mobile, $smscode, "login");
	if(!$sms_session) {
		$res['err']="短信验证码不正确";
	 	die(json_encode_yec($res));
	}
	
	if($ym_uid==0) //需要登录
	{
		$ym_uid =check_login(1);
	}
	
	$row = $db->fetch('member', '*', array("mobile"=>$mobile));
	if($row)
	{
		merge_user($row, $ym_uid);//合并当前账户到已有账户
	}
	else {
		update_userinfo($ym_uid, array("mobile"=>$mobile));
	}
	
	$res['res']="ok";
	$res['sid']= get_sid();
	die(json_encode_yec($res));
}
elseif ($act =='oauth_bind') //绑定第三方登录
{ 
	$mobile=!isset($mobile)?'': make_semiangle(trim($mobile));
	$username = isset($username)? trim($username): '';
	$password = isset($password)? trim($password): '';
	$img = isset($img)? trim($img): '';
	$email = isset($email)? trim($email): '';
	$oauth_type = isset($oauth_type)? trim($oauth_type): '';
	$platform = isset($platform)? trim($platform): '';
	$openid = isset($openid)? trim($openid): '';
	$unionid = isset($unionid)? trim($unionid): '';
		
	if($mobile =='')
	{
		$res['err']= '请填写手机号码';
		die(json_encode_yec($res));
	}
	if($oauth_type=='' || $openid =='')
	{
		$res['err']= '请提供完整的oauth_type和openid';
		die(json_encode_yec($res));
	}
	
	$oauth_user = get_member_oauth($oauth_type, $openid, $platform);  //微信需要区分平台
	if($oauth_user && count($oauth_user)>0 && $oauth_user['mobile'] !='') //已经绑定过，直接登录
	{
		if($oauth_user['status'] == locked)
		{
			$res['err']= '出于安全原因，系统已冻结您的账户，请与客服联系。';
			die(json_encode_yec($res));
		}
		set_login_session($oauth_user['uid'], $oauth_user['uname'], 1);
		
		$res['sid']= session_id();
		die(json_encode_yec($res));
	}
	
	if($username =='')
	{
		$username = build_username();
	}
	else {
		$name_err = check_uname($username);
		if($name_err !='')
		{
			$res['err']= $name_err;
		 	die(json_encode_yec($res));
		}
	}
	
	if($password =='')
	{
		$password = get_salt(8);
	}	
	else
	{
		$pwd_len = get_strlen($password);
		if($pwd_len<6 || $pwd_len>20)
		{
			$res['err']= "密码不正确，请使用6-20个字符";
		 	die(json_encode_yec($res));
		}		
	}
	
	$user = get_user_by_mobile($mobile, '');
	if($user)
	{
		if($user['pwd'] != encryptStr($password, $user['salt']) )
		{
			require_once("./inc/lib/system.php");
						
			$count = get_login_count($user['id'], role_user,2);
			$count= $count + 1;
			add_login_log($user['id'], role_user);
			if($count == 4 || $count == 5)
			{
				$err= "账户名或密码不匹配，您还可以尝试".(6-$count)."次";
			}
			elseif($count>=6)
			{
				$err= "密码错误6次，请1小时后再试，或您可以找回密码";
			}
			else {
				$err="账户名或密码不匹配，请重新输入";
			}
			$res['err']= $err; 
		 	die(json_encode_yec($res));
		}
		$uid = $user['id'];
		$username = $user['uname'];
	}	
	else {
		if($_SESSION['ditrib_id'] && $ym_ditribution_config['distrib_level']>0)	//分销	
		{
			require_once "inc/lib/distrib.php";
			$pids = get_parent_uid(intval($_SESSION['ditrib_id']));	
			$pids['pid3'] = $pids['pid2'];
			$pids['pid2'] = $pids['pid1'];
			$pids['pid1'] = $_SESSION['ditrib_id'];
		}
		else {
			$pids = array('pid1'=>0,'pid2'=>0,'pid3'=>0);
		}
		
		//添加用户
		$uid = add_user($username,$mobile,$password,$img, $email,'','',0,'', $pids);
	}
		
	if($uid ==0)
	{
		$res['err']="注册失败，请稍后再试";
	 	die(json_encode_yec($res));
	}
	
	//绑定第三方 oauth
	add_member_oauth($uid, $oauth_type, $openid, $platform, $unionid);
	 
	//自动登录
	set_login_session($uid, $username, 1);
	
	$res['res']="ok";
	$res['sid']= get_sid();
	die(json_encode_yec($res)); 	
}
elseif ($act =='sms_reg') {//发验证码-注册
	$mobile = isset($mobile)? trim($mobile) :0 ; 	
	sendsms_service($mobile,'reg', true);
}
elseif ($act =='sms_login') {//发验证码-登陆
	$mobile = isset($mobile)? trim($mobile) :0 ; 	
	sendsms_service($mobile,'login');
}
elseif ($act =='sms_setpaypwd') {//发验证码-设置支付密码
	$mobile = isset($mobile)? trim($mobile) :0 ; 	
	sendsms_service($mobile,'setpaypwd');
}
elseif ($act =='sms_oldmobile') {//发验证码-换手机号码。验证旧手机
	if(check_login(1)==0)
	{
		$res['err']= "登陆超时，请重新登陆";
		die(json_encode_yec($res));
	}
	$user = get_user($ym_uid);
	sendsms_service($user['mobile'],'changemobile');
}
elseif ($act =='sms_changemobile') {//发验证码-换手机号码
	$mobile = isset($mobile) ? trim($mobile) : 0;
	sendsms_service($mobile,'changemobile', true);
}
elseif ($act =='sms_findpwd') {//发验证码-找回密码
	$mobile = isset($_SESSION['findpwd_mobile']) ? trim($_SESSION['findpwd_mobile']) :0 ; 
	if($mobile ==0)
	{
		$res['err']= "操作超时，请重新申请找回密码";
		die(json_encode_yec($res));
	}
	sendsms_service($mobile,'updatepwd');
}
elseif ($act =='sms_updatepwd') {//发验证码-修改密码
	$mobile = isset($mobile) ? trim($mobile) : 0;
	sendsms_service($mobile,'updatepwd');
}
elseif ($act =='check_mobile') //检测手机号码是否使用
{
	if(isset($mobile) && is_mobile($mobile))
	{
		if(check_user_field("mobile",$mobile)==true)
		{
			$res['err']= "该手机号码已注册";
			$res['res']= 1;
		}
	}
	die(json_encode_yec($res));
}
elseif ($act =='check_cur_mobile') //验证当前手机号码
{
	if(check_login(1)==0)
	{
		$res['err']= "登陆超时，请重新登陆";
		die(json_encode_yec($res));
	}
	if(!$smscode)
	{
		$res['err']= "请输入验证码";
		die(json_encode_yec($res));
	}
	$user = get_user($ym_uid);	
	$res['err'] = check_smscode($user['mobile'], $smscode, 'changemobile');	
	if($res['err'] =='')
	{
		update_sms_status($user['mobile'], 'changemobile');//更新验证码为已用
	}	
	die(json_encode_yec($res));
}
elseif ($act =='get_userinfo') //获取当前登陆会员信息
{
	$ym_uid = check_login(1);
	if($ym_uid ==0)
	{
		$res['err']= "登录超时，请重新登录";
		die(json_encode_yec($res));
	}
	$res['data'] = get_user($ym_uid);
	die(json_encode_yec($res));
}
else {
	$res['err']= "未知操作，检查您的act值";
	die(json_encode_yec($res));
}

/**发送短信服务
 * @param string $mobile 手机号
 * @param string $tpl_type 短信模板类型
 * @param bool $check_mobile 是否检测手机存在
 * @param bool $add_sess 是否保存到临时表
 * */
function sendsms_service($mobile,$tpl_type,$check_mobile=false, $add_sess=true)
{
	global $res,$ym_name;
	if(intval($mobile)==0 || is_mobile($mobile)==false)
	{
		$res['err']= "手机格式不正确";
	 	die(json_encode_yec($res));
	}
	if($check_mobile==true && check_user_field("mobile",$mobile)==true)
	{
		$res['err']= "该手机号码已存在";
	 	die(json_encode_yec($res));
	}
	$sms_count_minute = get_sms_count($mobile, $tpl_type, 2); //2分钟内发送数量
	$sms_count_hour = get_sms_count($mobile, $tpl_type, 60); //1小时内发送数量
	$sms_count_ip = get_sms_count($mobile, $tpl_type, "day", 1440); //同个ip一天内发送数量

	if($sms_count_minute>0)
	{
		$res['err']= "请两分钟后再发送";
	 	die(json_encode_yec($res));	
	}
	if($sms_count_hour>6)
	{
		$res['err']= "您发的有点频繁，请一个小时后再试";
	 	die(json_encode_yec($res));	
	}
	if($sms_count_ip>=50)
	{
		$res['err']= "您发的太频繁了，请后天再来吧！";
	 	die(json_encode_yec($res));	
	}

	$extend='';
	$code = get_randNum(authCodeLen);
	$param='{"code":"'.$code.'","product":"'.$ym_name.'"}';
		
	$sms= new sms;
	$result= $sms->send($mobile, $tpl_type, $param);
	if($result['err'] !='')
	{
		$res['err'] = $result['err'];
		//die(json_encode_yec($res)); //todo
	}
	if($add_sess)
	{
		add_sms_session($mobile, $code, $tpl_type);
	}
	
	$res['res']= $code;
	
	die(json_encode_yec($res));
}

?>