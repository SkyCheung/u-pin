<?php
if (!defined('in_mx'))
{
	exit('Access Denied');
}

/*微信登录*/
class wxoauth
{
	private $appid;
	private $appsecret;
	private $openid;
	private $access_token;
	
	public function __construct($access_token = "", $openid = "")
	{		
        global $ym_url,$ym_client;
		$oauth_code = $ym_client && $ym_client==client_pc ? "wx_op" :"wx";
        $ym_oauth =  get_cache('oauth', cache_static);    //读取配置文件    
        if(empty($ym_oauth) || empty($ym_oauth[$oauth_code])){
        	$_SESSION['wx_autologin_fail'] = 1;
            message("无法读取配置文件，请在后台 第三方登陆配置微信，并更新缓存. <a href='login.html'>使用其它方式登陆</a>", 'login.html');
        }
		$this->appid = $ym_oauth[$oauth_code]['appid'];
		$this->appsecret = $ym_oauth[$oauth_code]['appsecret'];
		$this -> wx_openid = $openid;
		$this -> access_token = $access_token;
	}

	public function login($is_auto = false, $client = client_pc)
	{
		global $ym_url;
		$callback_url = $ym_url . "plugin.html?mod=oauth&c=wx&act=callback";
		$scope = "snsapi_base";
		if ($is_auto == false)
		{
			$scope = "snsapi_userinfo";
		}
		$state = get_salt(16);
		session_start();
		$_SESSION['wx_state'] = $state;
		if ($client == client_pc)
		{
			$_SESSION['wx_platform']='op'; //开放平台
			$scope ='snsapi_login';
			$oauth_url = "https://open.weixin.qq.com/connect/qrconnect?appid=" . $this ->appid . "&redirect_uri=" . urlencode($callback_url) . "&response_type=code&scope=" . $scope . "&state=".$state."#wechat_redirect";
		}
		else
		{
			$_SESSION['wx_platform']='mp'; //公众平台
			$oauth_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this ->appid . "&redirect_uri=" . urlencode($callback_url) . "&response_type=code&scope=" . $scope . "&state=".$state."#wechat_redirect";
		}
		//echo $oauth_url;
		header("Location: $oauth_url");die();
	}

	public function callback()
	{
		$code = !empty($_GET['code']) ? trim($_GET['code']) : '';
		$state = !empty($_GET['state']) ? trim($_GET['state']) : '';
		
		if ($code == '')
		{
			session_start();
			$_SESSION['wx_autologin_fail'] = 1;
			message("没有获得code!", 'login.html');
		}
		
		if($_SESSION['wx_state'] != $state)
		{			
			session_start();
			$_SESSION['wx_autologin_fail'] = 1;
			message("state验证失败", 'login.html');
		}
		unset($_SESSION['wx_state']);

		//获得access_token和openid
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this -> appid . '&secret=' . $this -> appsecret . '&code=' . $code . '&grant_type=authorization_code';
		$res = $this ->curl_get_contents($url);
		$ret_oa = json_decode($res);

		if($ret_oa -> openid && $ret_oa -> openid != '')
		{
			$this -> wx_openid = $ret_oa -> openid;
			$this -> access_token = $ret_oa ->access_token;
		}
		$data['openid'] = $this ->wx_openid;
		$data['unionid'] = $ret_oa ->unionid ? $ret_oa ->unionid : '';
		$data['access_token'] = $this -> access_token;
		$data['scope'] = $ret_oa ->scope;

		return $data;
	}

	//获得用户信息
	public function get_user_info()
	{
		$url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $this -> access_token . '&openid=' . $this -> wx_openid . '&lang=zh_CN';
		$res = $this -> curl_get_contents($url);
		$result = json_decode($res, true);
		$result['avatar']= $result['headimgurl'];
		return $result;
	}

	/**
	 * 异步将远程链接上的内容(图片或内容)写到本地
	 *
	 *  unknown $url
	 *            远程地址
	 *  unknown $saveName
	 *            保存在服务器上的文件名
	 *  unknown $path
	 *            保存路径
	 *  boolean
	 */
	public function put_file_from_url_content($url, $filename)
	{
		// 设置运行时间为无限制
		//set_time_limit ( 10 );
		//$this->writelog('curl_exec前');
		$url = trim($url);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		// 设置你需要抓取的URL
		curl_setopt($curl, CURLOPT_HEADER, FALSE);
		// 设置header
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
		$file = curl_exec($curl);
		curl_close($curl);
		//$this->writelog('curl_exec后');
		// 将文件写入获得的数据
		$write = @fopen($filename, "w");
		if ($write == false)
		{
			return false;
		}
		if (fwrite($write, $file) == false)
		{
			return false;
		}
		if (fclose($write) == false)
		{
			return false;
		}
	}

	function curl_get_contents($url)
	{
		$ch = curl_init();
		/*    curl_setopt($ch, CURLOPT_URL, $url);
		 curl_setopt($ch, CURLOPT_TIMEOUT, 1);
		 curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
		 curl_setopt($ch, CURLOPT_REFERER, _REFERER_);
		 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);*/

		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		$r = curl_exec($ch); 
		curl_close($ch);
		if($r){
			curl_close($ch);
			return $r;
		} else { 
			$error = curl_errno($ch);
			curl_close($ch);
			//logs("curl出错，错误码:".$error." url=".$url);			
		}
		
		return $r;
	}

}
?>