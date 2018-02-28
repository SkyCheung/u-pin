<?php
if (!defined('in_mx')) {exit('Access Denied');}

const imtype_custmer='custmer'; //会员
const imtype_crmuser='crmuser'; //客服人员

//获取客服
function get_chat_user($where='')
{
	global $db;
	$row = $db->queryall("SELECT distinct u.id,u.username,u.name,u.coname FROM ".$db->table('user')." u left join ".$db->table('sys_role_user')." ru on u.id=ru.uid join ".$db->table('sys_role')." r on ru.role_id=r.id WHERE r.id=2 ".$where." order by u.id");
	
	foreach ($row as $k => $v) {
		$row[$k]['addtime'] = date("Y-m-d H:i:s", $v['addtime']);
	}
	return $row;
}

//$of_url = 'http://192.168.1.222/dsc/action/sysDataService/'; 
//$of_url = 'http://119.147.136.181:8080/dsc/';
$of_url = 'http://192.168.1.13/dsc/action/sysDataService/';
function data_server($action, $data=array())
{
	global $of_url;
	$url = $of_url . $action;
	$data = http_build_query($data);
	switch ($action) {
		case 'sysLoginByHttp':
			$systemId ='ym';
			$timestamp =''. time();		
			$service_user = get_service_usr($systemId);
			$tmp_data= array($service_user['PASSWORD'], $timestamp, $systemId);
			sort($tmp_data);
			$tmp_str = implode($tmp_data);
			$signature = sha1($tmp_str);
			
			$data = 'timestamp='.$timestamp . '&systemId='.$systemId.'&signature='.$signature; 		
			break;	
		case 'executeByRquestParam':			
			$data .= '&moduleId=registerImUserService';
			break;			
		default:
			return false;
			break;
	}
	//print $data;
	return post_url($url, $data); 
}

//添加聊天用户
function add_chat_user($uid, $uname, $pwd, $type)
{
	global $ym_chat_config;
	$appid = $ym_chat_config['appid'];
	session_start();
	if(isset($_SESSION['im_sid']))
	{
		$im_sid = $_SESSION['im_sid'];
	}
	else {
		$auth_result= data_server('sysLoginByHttp'); //print_r($auth_result);
		if($auth_result['err'] !='')
		{
			return "连接服务器失败";
		}
		else {			
			$tmp_data = json_decode($auth_result['data'], true); 
			$im_sid = $tmp_data['sessionId'];
			$_SESSION['im_sid'] =$im_sid;			
		}
	}
	
	$reg_result = data_server('executeByRquestParam', array('sessionId'=>$im_sid,'domain'=>$appid,'loginId'=>intval($uid),'nickname'=>$uname,'password'=>$pwd,'type'=>$type));
	$reg_data = json_decode($reg_result['data'], true); 
	if($reg_data['returnCode'] ==0)
	{
		if($type == imtype_custmer)
		{
			update_userinfo($uid, array('is_im'=>1)); 
		}						
	}
 	return '';
}

function get_service_usr($login_id)
{
	global $db;
	return $db->fetch('service_usr', '*', array("login_id"=>$login_id));
}

/**
 * 设置上次聊天超过5分钟的客服状态为空闲
 */
function set_customer_status($cus_id, $cus_status)
{
	$sql = "update " . $GLOBALS['ecs']->table('chat_customer') . " set cus_status = '$cus_status' where cus_id = '$cus_id'";
	$result = $GLOBALS['db']->query($sql);
}

/**
 * 根据客服类型和入驻商编号获取客服列表，然后获取每个客服的在线状态(status)和是否存在于聊天系统(exist)
 * @param string $cus_type
 * @param int $supp_id
 * @return array
 */
function get_customers($cus_type = CUSTOMER_SERVICE, $supp_id = -1)
{
	if(!empty($supp_id) && $supp_id != 0)
	{
		$where = " AND supp_id = '$supp_id'";
	}
	else
	{
		$where = " AND supp_id = '-1'";
	}
	
	if(empty($cus_type))
	{
		$cus_type = CUSTOMER_SERVICE;
	}
	
	// 按客服的类型进行倒序排列，方便售前、售后比客服先获取用户权限
	$sql = "select * from " . $GLOBALS['ecs']->table('chat_customer') . " WHERE cus_enable = 1 AND cus_type in ($cus_type) $where ORDER BY cus_type desc";
	
	$list = $GLOBALS['db']->getAll($sql);
	
	foreach ($list as &$customer)
	{
		$of_username = $customer['of_username'];
		
		$exist = check_of_username_exist($of_username);
		
		if($exist)
		{
			$status = trim(get_of_user_status($of_username));
			$customer['status'] = $status;
		}
		else
		{
			$customer['status'] = 'unavailable';
		}
		
		$customer['exist'] = $exist;
	}
	
	return $list;
}

function get_online_customers($cus_type, $supp_id)
{
	$customer_list = get_customers($cus_type, $supp_id);		
}

/**
 * 根据用户类型和所属的店铺编号获取客服信息列表
 * @param string $user_type 用户类型：00-管理员 10-用户 20-平台售前客服 21-平台售后客服 30-入驻商售前客服 31-入驻商售后客服
 * @param int $shop_id 入驻商家编号：-1 - 空，其他-入驻商编号
 * @return 用户信息列表，未查询到则返回空数组
 */
function get_of_customers($user_type = 10, $shop_id = null)
{

	global $ym_chat_config;
	$of_username = $ym_chat_config['admin_uname'];
	$of_password = $ym_chat_config['admin_password'];
	$of_ip = $ym_chat_config['host'];
	$of_port = $ym_chat_config['port'];;
	$of_url = get_openfire_url($of_ip, $of_port);
	
	$url = $of_url.'/plugins/userService/properties/?type='.$user_type;
	
	if(!empty($shop_id))
	{
		$url = $url.'&shop_id='.$shop_id;
	}
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	// 授权验证
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, $of_username.":".$of_password);
	// 设置可以读取返回值
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	// 运行curl
	$result = curl_exec ( $ch );
	
	// 关闭
	curl_close ( $ch );
	
	$users = array();
	
	if(!empty($result))
	{
		
		$result = simplexml_load_string($result);
		
		for ($i = 0; $i < count($result->user); $i++) 
		{
			$u = $result->user[$i];
			
			$user = new User();
			
			$user->username = (string)$u->username;
			$user->name = (string)$u->name;

			
			for ($j = 0; $j < count($u->properties->property); $j++)
			{
				$p = $u->properties->property[$j];
				
				$property = new Property((string)$p->attributes()->key, (string)$p->attributes()->value);
				
				array_push($user->properties, $property);
				
			}
			
			array_push($users, $user);
			
		}
		
	}
		
	return $users;
}

/**
 * 
 * 获取“空闲”和“在线”两个状态的客服列表
 * 
 * @param number $user_type
 * @param string $shop_id
 * @return Ambigous <用户信息列表，未查询到则返回空数组, multitype:>|multitype:
 */
function get_of_online_customers($user_type = 10, $shop_id = null)
{
	$users = get_of_customers($user_type, $shop_id);
	
	if(empty($users))
	{
		return $users;
	}
	
	$list = array();
	
	for ($i = 0; $i < count($users); $i++) {
		$user = $users[$i];
		$username = $user->username;
		$status = trim(get_of_user_status($username));
		
		if($status == '在线' || $status == '空闲')
		{
			array_push($list, $user);
		}
		
	}
	
	return $list;
	
}

/**
 * 
 * 获取用户当前在线状态
 * 
 * @param unknown $username
 * @param string $type 返回的数据类型：xml,text,image,默认为text
 * @return mixed text[空闲、在线、离开、电话中、正忙]
 */
function get_of_user_presence ($username, $type = 'text')
{
	global $ym_chat_config;
	$of_username = $ym_chat_config['admin_uname'];
	$of_password = $ym_chat_config['admin_password'];
	$of_ip = $ym_chat_config['host'];
	$of_port = $ym_chat_config['port'];;
	
	$of_url = get_openfire_url($of_ip, $of_port);
	
	$url = $of_url.'/plugins/presence/status?jid='.$username.'&type='.$type;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	// 授权验证
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, $of_username.":".$of_password);
	// 设置可以读取返回值
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	// 运行curl
	$result = curl_exec ( $ch );
	
	// 关闭
	curl_close ( $ch );
	
	return $result;
}

/**
 *
 * 获取用户当前在线状态
 *
 * @param unknown $username
 * @return mixed text[空闲、在线、离开、电话中、正忙、unavailable]
 */
function get_of_user_status($username)
{
	global $ym_chat_config;
	$of_username = $ym_chat_config['admin_uname'];
	$of_password = $ym_chat_config['admin_password'];
	$of_ip = $ym_chat_config['host'];
	$of_port = $ym_chat_config['port'];;
	
	$of_url = get_openfire_url($of_ip, $of_port);
	$of_domain = get_chat_domain();

	$url = $of_url.'/plugins/presence/status?jid='.$username.'@'.$of_domain.'&type=xml';
	$result = get_byCurl($url, '', '', $of_username, $of_password);
	$xml = simplexml_load_string($result);

	$type = $xml->attributes()->type;

	if(!empty($type))
	{
		return (string)$type;
	}
	else if(!empty($xml->status))
	{
		$status = $xml->status;
		return (string)$status;
	}

	return 'unavailable';
}

/**
 * 获取聊天服务器的域名
 *
 * @param unknown $username
 * @param string $type 返回的数据类型：xml,text,image,默认为text
 * @return string
 */
function get_chat_domain()
{
	global $ym_chat_config;
	$of_username = $ym_chat_config['admin_uname'];
	$of_password = $ym_chat_config['admin_password'];
	$of_ip = $ym_chat_config['host'];
	$of_port = $ym_chat_config['port'];

	return $result;
}

/**
 *
 * 判断用户是否存在
 *
 * @param string $username
 * @return boolean
 */
function check_of_username_exist($username)
{
	global $ym_chat_config;
	$of_username = $ym_chat_config['admin_uname'];
	$of_password = $ym_chat_config['admin_password'];
	$of_ip = $ym_chat_config['host'];
	$of_port = $ym_chat_config['port'];;
	
	$of_url = get_openfire_url($of_ip, $of_port);
	
	if(empty($username))
	{
		return false;
	}

	$url = $of_url.'/plugins/userService/users/'.$username.'/exist';// print  $url.' '.$of_username.' '.$of_password;
	
	$result = get_byCurl($url, '', '', $of_username, $of_password);

	/*$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	// 授权验证
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, $of_username.":".$of_password);
	// 设置可以读取返回值
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	// 运行curl
	$result = trim(curl_exec ( $ch ));

	// 关闭
	curl_close ( $ch );*/
	
	if($result == 'true')
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * 创建用户信息,如果用户信息存在则更新
 * 
 * @param string $username 用户名
 * @param string $password 密码
 * @param string $name 昵称
 * @param string $email 邮箱
 * @param string $type 用户类型
 * @param string $shop_id 店铺ID
 * @return boolean
 */
function create_of_user($username = null, $password = null, $name = null, $email = null, $type = 10, $shop_id = -1)
{
	global $ym_chat_config;
	$of_username = $ym_chat_config['admin_uname'];
	$of_password = $ym_chat_config['admin_password'];
	$of_ip = $ym_chat_config['host'];
	$of_port = $ym_chat_config['port'];;
	
	$of_url = get_openfire_url($of_ip, $of_port);

	if($username == null || strlen($username) == 0)
	{
		return false;
	}
	
	// 判断用户是否已经存在
	$exist = check_of_username_exist($username);
	
	if($exist)
	{
		if($password == null || strlen($password) == 0)
		{
			$password = null;
		}
		
		$url = $of_url.'/plugins/userService/users/'.$username;
		$method = 'PUT';
	}
	else
	{
		if($password == null || strlen($password) == 0)
		{
			return false;
		}
		
		$url = $of_url.'/plugins/userService/users';
		$method = 'POST';
	}
	
	$user = new User();
	$user->username = $username;
	$user->password = $password;
	$user->name = $name;
	$user->email = $email;
	$user->properties = array(new Property('type', $type), new Property('shop_id', $shop_id));
	
	
	$result = get_byCurl($url, $user->asXML(), array('Content-Type: application/xml'), $of_username, $of_password, $method);	 
	
	if(strpos($result, '201 Created') >= 0)
	{
		return true;
	}
	else if(strpos($result, '400 Bad Request') >= 0)
	{
		return false;
	}
	else if(strpos($result, 'UserAlreadyExistsException') >= 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function get_byCurl($url, $data='', $httpheader='', $uname='', $password='', $method="post")
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);	
	curl_setopt($ch, CURLOPT_HEADER, 1);// 设置HTTP头	
	if($httpheader != '')
	{
		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader); //array('Content-Type: application/xml')
	}	
	
	if($uname !='' && $password!='')// 授权验证
	{
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $uname . ":" . $password);
	}	
	// 设置可以读取返回值
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	/*if($method == "post") //post提交方式
	{		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);		 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);// 提交的数据
	}	*/

	$result = trim(curl_exec($ch));
	curl_close($ch);
	return $result;
}

/**
 * 根据IP地址和端口号获取OpenFire的服务URL
 * @param unknown $ip
 * @param number $port
 * @return string
 */
function get_openfire_url($ip, $port = 80, $uri = '')
{
	return "http://$ip:$port$uri";
}


?>