<?php
if (!defined('in_mx')) {exit('Access Denied');}

/*会员业务处理*/

function check_login($is_ajax = 0)
{	 
	if(!isset($_SESSION['uid']) || intval($_SESSION['uid'])==0)
	{
		if(!isset($_COOKIE['user']) || trim($_COOKIE['user'])=='')
		{
			global $ym_fullurl;
			if($is_ajax ==1)
			{
				return 0;
			}
			else {
				redirect("login.html?return_url=".urlencode($ym_fullurl));
			}			
		}
		else {
			session_start();
			$userinfo = json_decode($_COOKIE['user'], true);
			$_SESSION['uid']= ucode($userinfo['uid'], ym_token) ;
			$_SESSION['uname']= $userinfo['uname']; 

			return intval($_SESSION['uid']);
		}
	}
	else {
		return intval($_SESSION['uid']);
	}	
}

//获取用户id
function get_userid()
{
	return (!isset($_SESSION['uid']) ? 0 : intval($_SESSION['uid']));
}

function get_username()
{
	//session_start();
	if(!isset($_SESSION['uname']))
	{
		check_login(1);
	}
	return (!isset($_SESSION['uname']) ? '' : $_SESSION['uname']);
}

//生成用户名
function build_username()
{
	$username = "YEC".date('ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
	if(check_user_field('uname', $username))
	{
		build_username();
	}
	return $username;
}

/**获取会员信息*/
function get_user($uid =0)
{
	global $db, $ym_uid;
	$uid= $uid==0 ?$ym_uid: intval($uid);
	$row = $db->query("select m.*,g.grade_name,g.discount*0.01 discount,g.point_require from ".$db->table('member')." m left join ".$db->table('member_grade')." g on m.grade_id = g.grade_id where id=".$uid);
	$row['addtime'] = date('Y-m-d H:i:s', $row['addtime']);  
	$row['birthday'] = $row['birthday'] ==0 ?'': date('Y-m-d',$row['birthday']);
	//unset($row['pwd']);
	return $row;
}

/**获取会员信息*/
function get_user_by_mobile($mobile='', $uname ='')
{
	global $db;
    if($mobile !='')
	{
		$where = array('mobile'=>$mobile);
	}
	elseif($uname !='')
	{
		$where =array('uname'=>$uname);
	}
	else {
		return false;
	}
	return $db->fetch('member', 'id,status,uname,mobile,pwd,salt,img', $where);
}

/**获取会员id*/
function get_uid_by_IdOrName($val)
{
	global $db;
 
	$row = $db->query("select id from ".$db->table('member')." where id =".intval($val)." or uname ='".addslashes($val)."'");
	return $row ? $row['id'] : 0;
}

//增加会员
function add_user($username, $mobile, $password, $img='', $email='',$birthday=0, $qq='',$sex=0,$realname='',$pids= array(), $commiss_id=0, $grade_id=0)
{
	global $db;
	$salt= get_salt();
	$grade_id= $grade_id ==0 ? get_default_gradeid() : intval($grade_id);
	$result = $db->insert('member', array('uname'=>trim($username),'mobile'=>trim($mobile),'pwd' => encryptStr($password, $salt),'salt' =>$salt,'status'=>normal,'addtime'=>time(),'logintime'=>time(),'ip'=>getip(),'img'=>trim($img),'email'=>trim($email),'birthday'=>trim($birthday),'qq'=>trim($qq),'sex'=>intval($sex),'realname'=>trim($realname),'pid1'=>$pids['pid1'],'pid2'=>$pids['pid2'],'pid3'=>$pids['pid3'], 'commiss_id'=>intval($commiss_id), 'grade_id'=>$grade_id));
	if($result==false)
	{
		return 0;
	}
	return $result==false ? 0 : $db->lastinsertid();
}

//更新用户信息
function update_userinfo($uid, $userinfo=array())
{
	global $db;
	$db->update('member', $userinfo, array('id'=>$uid));	
}

//资金、积分更新
function update_account($uid, $balance=0, $point=0)
{
	global $db;
	$db->query("update ".$db->table('member')." set balance= balance + ".floatval($balance).", point= point + ".intval($point)." where id=".intval($uid));	
}

//增加资金、积分变动记录
function add_member_log($uid, $type=asset_balance, $val=0,$description='', $order_sn='')
{
	global $db;
	$db->insert('member_log', array('uid'=>intval($uid), 'type'=>intval($type), 'val'=>floatval($val), 'description'=>$description, 'order_sn'=>$order_sn, 'addtime'=>time()));
}

//删除资金、积分变动记录
function del_member_log($uid)
{
	global $db;
	return $db->delete('member_log', array('uid'=>intval($uid)));
}

//更新用户信息
function del_user($uid)
{
	global $db;
	$db->delete('member', "id in(".$uid.")");	
}

/*合并两个账号 $main_user 主账户信息, $sub_uid 被合并账户ID*/
function merge_user($main_user, $sub_uid)
{
	global $db;
	if(function_exists('merge_cart')==FALSE)
	{
		require_once("./inc/lib/cart.php");
	}
	
	$main_uid = $main_user['id'];	
	$sub_user = get_user($sub_uid);
	
	if(trim($main_user['img']) == '')
	{
		update_userinfo($main_uid, array('img'=>$sub_user['img']));
	}
	if($main_user['uname'] == '')
	{
		update_userinfo($main_uid, array('uname'=>$sub_user['uname']));
	}
	
	merge_cart($main_uid, $sub_uid); //合并购物车
	
	$db->update('comment', array('uid'=>$main_uid), array('uid'=>$sub_uid));
	$db->update('comment_reply', array('uid'=>$main_uid), array('uid'=>$sub_uid));
	$db->update('comment_reply', array('reply_uid'=>$main_uid), array('reply_uid'=>$sub_uid));
	$db->update('login_log', array('uid'=>$main_uid), array('uid'=>$sub_uid));
	$db->update('member_address', array('uid'=>$main_uid), array('uid'=>$sub_uid));
	$db->update('member_fav', array('uid'=>$main_uid), array('uid'=>$sub_uid)); //todo重复记录
	$db->update('member_log', array('uid'=>$main_uid), array('uid'=>$sub_uid));
	//$db->update('order', array('uid'=>$main_uid), array('uid'=>$sub_uid));
	//$db->update('order_log', array('op_uid'=>$main_uid), array('op_uid'=>$sub_uid,'op_type'=>role_user));
	//$db->update('order_service', array('uid'=>$main_uid), array('uid'=>$sub_uid));
	
	$db->update('member_oauth', array('uid'=>$main_uid), array('uid'=>$sub_uid));
	$db->delete('member', array('id'=>$sub_uid));
	$db->delete('member_token',  array('uid'=>$sub_uid));
	
	//自动登录
	set_login_session($main_uid, $main_user['uname'], 1, false);
}

function check_user_field($field, $val)
{
	global $db;
	$user = $db->fetch('member', $field, array($field=>$val));
	return ($user && count($user)==1);
}

/*第三方登录会员绑定数据*/
function add_member_oauth($uid, $type, $openid, $platform='', $unionid='')
{
	global $db;
	return $db->insert('member_oauth', array('type'=>$type, 'uid'=>intval($uid), 'openid'=>trim($openid), 'platform'=>trim($platform), 'unionid'=>trim($unionid), 'addtime'=>time()));
}

/*取消第三方授权*/
function del_member_oauth($uid, $type='', $openid='', $platform='')
{
	global $db;
	$where = array('uid'=>intval($uid));
	if($openid !='' && $type !='')
	{
		$where['type'] = $type;
		$where['openid'] = $openid;
		$where['platform'] = $platform;
	}
	return $db->delete('member_oauth', $where);
}

function get_member_oauth($type='', $openid='', $platform='', $uid=0)
{
	global $db;
	$where ='1';
	if($type !='')
	{
		$where .= " and mo.`type`='".$type."'";
	}
	if($openid !='')
	{
		if($type =='wx') //微信需要区分平台
		{
			$where .= " and mo.platform='".$platform."'";
		}
		$where .= " and mo.openid='".$openid."'";
	}
	if($uid != 0)
	{
		$where .= " and mo.uid=".intval($uid);
	}
	$sql = "select mo.*,ifnull(m.uname,'') uname,mobile,ifnull(m.`status`,".normal.") status from ". $db->table('member_oauth') ." mo left join ". $db->table('member') ." m on mo.uid = m.id where  ".$where;
	return $db->query($sql); 
}

//获取第三方登陆方式
function get_oauth($status=1, $code='')
{
	global $db;
	$where = array('status'=>$status);
	if($code !='')
	{
		$where['code'] = $code;
	}
	$row = $db->fetchall('oauth', '*', $where);
	foreach ($row as $k => $v) {
		if($v['is_qrcode']==1)
		{
			$oauthfile = plugin."oauth/".trim($v['code']).'/'.trim($v['code']).Ext;
			if(file_exists($oauthfile))
			{
				@include($oauthfile);
				$row[$k]['src']= get_oauthcode();
			}
		}
	}
	return $row;
}

/*第三方登陆*/
function oauth_login($oauth_c, $oauth_type, $openid, $platform='',$unionid='', $scope='')
{
	global $ym_ditribution_config, $ym_client;

	$res = array('err' => '', 'res' => '', 'sid'=>'', 'data' => array()); 
	if($oauth_type=='' || $openid =='')
	{
		$res['err'] ='请提供oauth_type和openid参数';	
		return $res;	
	}
	
	$user = get_member_oauth($oauth_type, $openid, $platform);  //微信需要区分平台
	if($user && count($user)>0) //已经绑定过，直接登录
	{
		if($user['status'] == locked)
		{
			$res['err'] ='出于安全原因，系统已冻结您的账户，请与客服联系。';
			return $res;
		}
		$uid = $user['uid'];
		$username = $user['uname'];
	}
	else { //未绑定，注册账号 
		if($oauth_type=='wx' && $scope=='snsapi_base')
		{
			$oauth_c->login(false, $ym_client);
		}
		
		$res = $oauth_c->get_user_info(); //获取第三方用户信息
		
		$oauth_userinfo = array('openid'=>$openid);
		if($res['ret'] == 0){
			$username = $res['nickname'];
			do {
				$name_exist = check_uname($username, true);
				if($name_exist !='')
				{
					$username = $username.rand(1000, 9999);
				}
			} while ($name_exist !='');
			
			$img = get_oauthimg($res['avatar']);	//头像 68x68
		}
		else {			
			$username = build_username();
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
		
		$uid = add_user($username, '','',$img, '',0,'', 0,'', $pids);
		if($uid==0)
		{
			$res['err']="自动注册失败，请稍后再试";
		 	return $res;
		}		
		
		$oauth_userinfo['type'] = $oauth_type;
		$oauth_userinfo['platform']= $platform;
		$oauth_userinfo['openid']= $openid;
		$oauth_userinfo['unionid']= $unionid;
		$result = bind_user($uid, $oauth_userinfo);
		if($result !='')
		{
			del_user($uid);
			$res['err']= $result;
			return $res;
		}	
	}
	
	set_login_session($uid, $username);
		
	$res['res']="ok";
	$res['sid']= get_sid();

	return $res;
}

/*绑定第三方登录*/
function bind_user($uid, $oauth_userinfo)
{
	global $db;
	if($oauth_userinfo['openid'] !='')
	{
			$platform = isset($oauth_userinfo['platform']) ? $oauth_userinfo['platform'] : '';
			$unionid = isset($oauth_userinfo['unionid']) ? $oauth_userinfo['unionid'] : '';
			$user_oauth = get_member_oauth($oauth_userinfo['type'], $oauth_userinfo['openid'], $platform); 
			if(!$user_oauth || count($user_oauth) ==0)
			{
				add_member_oauth($uid, $oauth_userinfo['type'], $oauth_userinfo['openid'], $platform, $unionid); //绑定第三方 oauth
			}
		else {
			$row = $db->fetch('oauth', '`name`', array('code'=>$oauth_userinfo['type']));
			return "该 ".$row['name']." 账户已经被绑定";				
		}
	}
	return '';
}

//拉取第三方登录头像
function get_oauthimg($imgurl)
{
	$fileName = upload_avatar.date("Ymd").'/'.get_newName().'.jpg';	
	$res_img = get_curl_file($imgurl, $fileName, 5);
	return $res_img=='' ? $fileName : '';
}

//登录后信息处理
function set_login_session($uid, $uname='', $autologin=1, $is_merge_cart=true)
{
	if(function_exists('merge_cart')==FALSE)
	{
		require_once("./inc/lib/cart.php");
	}
	if(function_exists('add_login_log')==FALSE)
	{
		require("./inc/lib/system.php");
	}

	global $db,$ym_client;
	session_cache_limiter('private');
	session_start();
	
	$_SESSION['uid']= $uid;
	$_SESSION['uname']= $uname;
	
	$data = array('uid'=>jcode($uid, ym_token),'uname'=>$uname);
	$login_time = intval($autologin)==1 ? time() + 15552000 : "0";
	
	set_cookie('user', json_encode($data), $login_time); 
 
	update_userinfo($uid, array('logintime'=>time(), 'ip'=>getip()), array('id'=>$uid));
		
	if($is_merge_cart)
	{
		merge_cart($uid); //合并游客购物车
	}
		
	add_login_log($uid, role_user, login_success);//登录日志
	
	//保存登录状态		
	add_member_token($uid, time()+7776000);//3个月过期
}

function get_sid()
{
	return urlencode(endecrypt(session_id(), 'encode', ym_token));
	//return jcode(session_id(), ym_token);
}

//清空某用户 todo
function del_user_session($uid)
{
	global $db;
	$row = $db->fetchall("member_token",'*', array('uid'=>trim($uid)));
	$db->delete("member_token", array('uid'=>trim($uid)));
	if($row)
	{
		foreach ($row as $k => $v) {
			/*if(file_exists(session_save_path().DIRECTORY_SEPARATOR."sess_".trim($v['sid'])))
			{

				session_write_close();
			}*/
		}				
	}	
}

//清空某用户
function del_session($sid)
{
	global $db;
	
	$db->delete("member_token", array('sid'=>trim($sid)));
	if(file_exists(session_save_path().DIRECTORY_SEPARATOR."sess_".trim($sid)))
	{
		session_unset();
		session_destroy();
		//session_unset();
		//session_write_close();
	}	
}

function add_member_token($uid, $expire)
{
	global $db, $ym_sid;
	if(empty($ym_sid))
	{
		$db->insert('member_token', array('sid'=>session_id(), 'uid'=>$uid,'expire'=>$expire,'ip'=>getip()));
	}
	else {
		$row = $db->rowcount("member_token", array('sid'=>trim($ym_sid)));
		if($row && count($row)>0)
		{
			$db->update('member_token', array('expire'=>$expire,'ip'=>getip()), array('sid'=>$ym_sid));
		}
		else {
			$db->insert('member_token', array('sid'=>$ym_sid, 'uid'=>$uid,'expire'=>$expire,'ip'=>getip()));
		}
	}	
}

//检测用户登录令牌
function check_user_token($sid)
{
	global $db;
	$res = array('err' => '', 'res' => '', 'data' => array());
	$row = $db->query("select t.*,m.uname,m.status from ". $db->table('member_token')." t join ". $db->table('member') ." m on t.uid=m.id where sid='".trim($sid)."'");
	
	if(!$row || count($row)==0 || $row['expire'] < time())
	{
		$res['err'] = '登录超时，请重新登录';
	}
	else if($row['status']==0){
		$res['err'] = '出于安全原因，系统已冻结您的账户，请与客服联系。';
	}
	else {
		set_login_session($row['uid'], $row['uname'], 1);
	}
	
	return $res;
}

//检测用户名
function check_uname($username, $is_exist=false, $uid=0)
{
	/*$name_len = get_strlen($username);
	if($name_len <4 || $name_len>20 || !is_username($username) )
	{
		return "请使用中文、字母、数字、“-”和“_”的组合，4-20个字符";
	}*/

	if(is_num($username) && $name_len>10)
	{
		return "纯数字用户名只能10位以下";
	}
	if(check_censor($username)==false)
	{
		return "该用户名已注册";
	}
	if($is_exist)
	{
		$row = check_username($username, $uid);
		if($row)
		{
			return "该用户名已注册";
		}
	}
	
	return '';
}

//查询用户名
function check_username($val, $uid = 0)
{
	global $db;
	$where = $uid == 0 ? '' : " and id<>".intval($uid);
	return $db->fetch('member','id,mobile', "(uname='".$val."' or mobile='".$val."')".$where);
}

//加入收藏
function add_fav($uid, $goods_id,$spec='')
{
	global $db;
	$db->insert('member_fav', array('uid'=>$uid, 'goods_id'=>$goods_id, 'spec'=>$spec, 'addtime'=>time()));
}

//取消收藏
function del_fav($uid, $goods_id=0,$spec='')
{
	global $db;
	$where =array('uid'=>$uid);
	if($goods_id!=0)
	{
		$where['goods_id']= intval($goods_id);
		$where['spec']= trim($spec);
	}
	$db->delete('member_fav', $where);
}

//获取收藏
function get_fav($uid, $goods_id=0,$spec='',$start=0 , $pagenum=10)
{
	global $db;
	$where= '';
	if($goods_id != 0)
	{
		$where .= ' and f.goods_id='.$goods_id;
	}
	if($spec !='')
	{
		$where .= " and f.spec='".$spec."'";
	}
	return $db->queryall("select g.thumb,g.code,g.name,ifnull(s.`values`,'') spec_name,ifnull(s.price,g.price) price, f.* from ".$db->table('member_fav')." f join ".$db->table('goods')." g on f.goods_id=g.goods_id left join ".$db->table('goods_spec')." s on f.spec=s.values and f.goods_id=s.goods_id where f.uid=".$uid.$where." limit ".$start.",".$pagenum);
}

//获取收藏数
function get_fav_count($uid=0)
{
	global $db;
 
	return $db->get_count($db->table('member_fav')." where uid=". intval($uid));
}

/** 添加回复
 *@param $pid_type 回复的对象：0评论 1回复
 * */
function add_comment_reply($cid,$uid = 0,$reply_uid=0, $pid=0, $pid_type=0, $role_type=role_user, $content='')
{
	global $db, $ym_comment_audit;
	$db->insert('comment_reply', array('cid'=>$cid,'pid'=>$pid, 'pid_type'=>$pid_type, 'uid'=>$uid, 'reply_uid'=>$reply_uid, 'role_type'=>$role_type, 'content'=>$content, 'status'=>intval($ym_comment_audit), 'addtime'=>time(),'ip'=>getip()));
	$id = $db->lastinsertid();
	return $id;
}

function get_comment_starcount($goods_id, $uid)
{
	global $db;
	$where='';
	if($uid==0)
	{
		$where .= " and status=1 ";
	}
	else {
		$where .= " and (status=1 or (status=0 and uid=".$uid.")) ";
	}
	$row =$db->queryall("select (case star when 4 then 'good' when 5 then 'good' when 2 then 'mid' when 3 then 'mid' else 'bad' end) starlevel,count(*) count from ".$db->table('comment')." where type=0 and item_id=".intval($goods_id).$where." group by starlevel order by addtime");
	$count = array('good'=>0 ,'mid'=>0 ,'bad'=>0);
	foreach ($row as $k => $v) {
		if($v['starlevel']=='good')
		{
			$count['good']=$v['count'];
		}
		elseif($v['starlevel']=='mid')
		{
			$count['mid']=$v['count'];
		}
		elseif($v['starlevel']=='bad')
		{
			$count['bad']=$v['count'];
		}
	}
	return $count;
}

function get_comment_count($goods_id, $star_level='', $type=0,$uid =0)
{
	global $db;
	$where = '';
	if($star_level != '')
	{
		switch ($star_level) {
			case 'good':
				$star ='4,5';
				break;
			case 'mid':
				$star ='2,3';
				break;
			case 'bad':
				$star ='1';
				break;	
			default:
				$star ='0';	
				break;
		}
		$where =" and star in(".$star.")";
	}
	if($uid==0)
	{
		$where .= " and c.status=1";
	}
	else {
		$where .= " and (c.status=1 or (c.status=0 and uid=".$uid."))";
	}
	$row = $db->query("select count(*) count from ".$db->table('comment')." c where c.type=".intval($type)." and c.item_id=".intval($goods_id).$where);

	return intval($row['count']);
}


/** 所有评价
 * @param $star_level 星级：good好  mid中 bad差
 * */
function get_all_comment_list($star_level='', $type=0, $start =0, $num =10, $uid=0)
{
    global $db;
    $where = '';
    if($star_level != '')
    {
        switch ($star_level) {
            case 'good':
                $star ='4,5';
                break;
            case 'mid':
                $star ='2,3';
                break;
            case 'bad':
                $star ='1';
                break;
            default:
                $star ='0';
                break;
        }
        $where =" and star in(".$star.")";
    }
    if($uid==0)
    {
        $where .= " and c.status=1";
    }
    else {
        $where .= " and (c.status=1 or (c.status=0 and uid=".$uid."))";
    }
    $row = $db->queryall("select c.*,m.uname,m.img uimg,grade_name from ".$db->table('comment')." c left join ".$db->table('member')." m on c.uid=m.id left join ".$db->table('member_grade')." mg on m.grade_id=mg.grade_id where c.type=".intval($type).$where." order by addtime desc limit ".$start.",".$num);

    foreach ($row as $k => $v) {
        $reply_count= $db->query("select count(*) count from ".$db->table('comment_reply')." where status=1 and cid=".$v['id']." and role_type=".role_user);
        $row[$k]['reply_count'] = intval($reply_count['count']); //回复数量
        $row[$k]['admin_reply'] = get_comment_reply(0,$v['id'],$v['id'], 0, role_admin); //管理员回复
        $row[$k]['anon_name']= $v['is_anon']==0 ? $v['uname']:format_anon($v['uname'], 1, 1);
        $row[$k]['img']= json_decode($v['img'],true);
        $row[$k]['thumb']= json_decode($v['thumb'],true);
        $row[$k]['addtime']= date('Y-m-d H:i', $v['addtime']);
    }
    return $row;
}


/** 评价
 * @param $star_level 星级：good好  mid中 bad差
 * */ 
function get_comment_list($goods_id, $star_level='', $type=0, $start =0, $num =10, $uid=0)
{
	global $db;
	$where = '';
	if($star_level != '')
	{
		switch ($star_level) {
			case 'good':
				$star ='4,5';
				break;
			case 'mid':
				$star ='2,3';
				break;
			case 'bad':
				$star ='1';
				break;	
			default:
				$star ='0';				
				break;
		}
		$where =" and star in(".$star.")";
	}
	if($uid==0)
	{
		$where .= " and c.status=1";
	}
	else {
		$where .= " and (c.status=1 or (c.status=0 and uid=".$uid."))";
	}
	$row = $db->queryall("select c.*,m.uname,m.img uimg,grade_name from ".$db->table('comment')." c left join ".$db->table('member')." m on c.uid=m.id left join ".$db->table('member_grade')." mg on m.grade_id=mg.grade_id where c.type=".intval($type)." and c.item_id=".intval($goods_id).$where." order by addtime desc limit ".$start.",".$num);
	
	foreach ($row as $k => $v) {
		$reply_count= $db->query("select count(*) count from ".$db->table('comment_reply')." where status=1 and cid=".$v['id']." and role_type=".role_user);
		$row[$k]['reply_count'] = intval($reply_count['count']); //回复数量
		$row[$k]['admin_reply'] = get_comment_reply(0,$v['id'],$v['id'], 0, role_admin); //管理员回复
		$row[$k]['anon_name']= $v['is_anon']==0 ? $v['uname']:format_anon($v['uname'], 1, 1);
		$row[$k]['img']= json_decode($v['img'],true);
		$row[$k]['thumb']= json_decode($v['thumb'],true);
		$row[$k]['addtime']= date('Y-m-d H:i', $v['addtime']);
	}
	return $row;
}

/** 我的评价
 * @param $type
 * */ 
function get_mycomment_count($uid=0, $type=0)
{
	global $db;
	$where = '';
	if($uid !=0)
	{
		$where .=" and uid=".$uid;
	}
	return $db->rowcount('comment'," type=".intval($type)." and status=1 ".$where);
}

/** 我的评价
 * @param $type 评论类型:0商品 1文章
 * */ 
function get_mycomment($uid=0, $type=0, $start =0, $num =10)
{
	global $db;
	$where = '';
	if($uid !=0)
	{
		$where .=" and c.uid=".$uid;
	}
	$row = $db->queryall("select c.*,g.name from ".$db->table('comment')." c left join ".$db->table('goods')." g on g.goods_id=c.item_id left join ".$db->table('goods_spec')." s on s.values=c.spec where c.type=".intval($type)." and c.status=1 ".$where." order by addtime desc limit ".$start.",".$num);
	foreach ($row as $k => $v) {
		$row[$k]['img']= json_decode($v['img'],true);
		$row[$k]['thumb']= json_decode($v['thumb'],true);
		$row[$k]['addtime']= date('Y-m-d H:i', $v['addtime']);
	}
	return $row;
}

/** 评价的回复
 *@param $pid_type 回复的对象：0评论 1回复
 * */
function get_comment_reply($id=0, $cid=0, $pid=0, $pid_type=0, $role_type = null,$uid=0, $reply_uid=0)
{
	global $db;
	$where ='';
	if($id !=0)
	{
		$where .=' and c.id='.$id;
	}
	if($cid !=0)
	{
		$where .=' and c.cid='.$cid;
	}
	if($pid !=0)
	{
		$where .=' and c.pid='.$pid;
	}
	if($pid_type !=0)
	{
		$where .=' and c.pid_type='.$pid_type;
	}
	if($role_type !=null)
	{
		$where .=' and c.role_type='.$role_type;
	}
	if($uid !=0)
	{
		$where .=' and c.uid='.$uid;
	}
	if($reply_uid !=0)
	{
		$where .=' and c.reply_uid='.$reply_uid;
	}
	
	$row = $db->queryall("select c.*,ifnull(m.uname,u.name) uname,ifnull(r.uname,ur.name) reply_name from ".$db->table('comment_reply')." c left join ".$db->table('member')." m on c.uid=m.id left join ".$db->table('member')." r on c.reply_uid=r.id left join ".$db->table('user')." u on c.uid=u.id left join ".$db->table('user')." ur on c.reply_uid=ur.id where c.status=1 ".$where.' order by addtime asc');	
	foreach ($row as $k => $v) {
		$row[$k]['addtime']= date('Y-m-d H:i', $v['addtime']);
		//$row[$k]['uname']=format_anon($v['uname'], 1, 1);
		//$row[$k]['reply_name']=format_anon($v['reply_name'], 1, 1);
	}
	return $row;
}

//检测每个会员或每个ip 一小时内只能评论10次
function check_comment_reply($uid, $ip='')
{
	global $db;
	$where ='';
	$row_uid = $db->query("select count(*) count from ".$db->table('comment_reply')." where addtime>=".strtotime(date('Y-m-d H:i:s',strtotime("-1 hour")))." and uid=".$uid);
	$row_ip = $db->query("select count(*) count from ".$db->table('comment_reply')." where addtime>=".strtotime(date('Y-m-d H:i:s',strtotime("-1 hour")))." and ip='".$ip."'");

	if( (!$row_uid || count($row_uid)==0 || intval($row_uid['count']) < 10) && (!$row_ip || count($row_ip)==0 || intval($row_ip['count']) < 10))	
	{
		return true;
	}
	
	return false;
}

//获取默认等级ID
function get_default_gradeid()
{
	global $db;
	$row = $db->fetch('member_grade', "grade_id", array('is_default'=>1));
	return !$row ? 0 : intval($row['grade_id']);
}

function get_comment_uid($id, $pid_type=0)
{
	global $db;
	if($pid_type ==0)
	{
		$row=$db->fetch('comment', 'uid', array('id'=>$id));
	}
	else {
		$row=$db->fetch('comment_reply', 'uid', array('id'=>$id));
	}
	 
	return $row['uid'];
}

//评价
function get_comment($order_sn='', $uid=0)
{
	global $db;
	$where ='1';
	if($order_sn !='')
	{
		$where .=" and order_sn=".$order_sn;
	}
	if($uid !=0)
	{
		$where .=" and uid=".$uid;
	}
	return $db->queryall("select c.*,g.name from ".$db->table('comment')." c join ".$db->table('goods')." g on c.goods_id=g.goods_id where ".$where);
}

//未评价商品
function get_not_comment($order_sn='', $uid=0)
{
	global $db;
	$where ='';
	if($order_sn !='')
	{
		$where .=" and o.order_sn=".$order_sn;
	}
	if($uid !=0)
	{
		$where .=" and o.uid=".$uid;
	}
	$goods = $db->queryall("select g.thumb,g.code,ifnull(s.`values`,'') spec_name,og.* from ".$db->table('order')." o join ".$db->table('order_goods')." og on o.id=og.order_id left join ".$db->table('goods')." g on g.goods_id=og.goods_id left join ".$db->table('goods_spec')." s on og.spec=s.values and og.goods_id=s.goods_id left join ".$db->table('comment')." c on c.order_sn=o.order_sn and c.item_id=og.goods_id and c.spec=og.spec where c.id is null  ".$where);
	
	foreach ($goods as $k => $v) {
		$goods[$k]['price'] = format_price($v['price']);
		$goods[$k]['amount'] = format_price($v['price'] * $v['num']);
		$goods[$k]['url'] = $v['code'].'-g.html';
	}
	return $goods;
}

/**将到期积分, 次年底：大于等于去年第一天，小于今年第一天*/
function get_expire_point($uid)
{
	global $db;
	$row = $db->query("select sum(val) val from ".$db->table('member_log')." where type=2 and addtime>=".strtotime((date("Y",time())-1)."-01-01")." and addtime<".strtotime(date("Y",time())."-01-01")." and uid=".$uid);
	
	return intval($row['val']);
}

/**会员账户记录   余额/积分
 * @param $type 类型：1余额，2积分
 * */
function get_member_log_count($uid=0, $type=0,$role_type=0, $uname='', $start_time="" , $end_time="", $val_type=0)
{
	global $db;	 
	$where ='1';
	if($role_type !=0)
	{
		$where .= " and a.role_type=".intval($role_type);
	}
	if($uid !=0)
	{
		$where .= " and uid=".intval($uid);
	}
	if($type !=0)
	{
		$where .= " and type=".intval($type);
	}
	if($uname!='')
	{
		$where .= " and m.uname ='".$uname."'";
	}
	if($start_time!='')
	{
		$where .= " and a.addtime>=".$start_time;
	}
	if($end_time!='')
	{
		$where .= " and a.addtime<=".$end_time;
	}
	if($val_type ==1)
	{
		$where .= " and a.val>=0";
	}
	elseif($val_type ==2)
	{
		$where .= " and a.val<0";
	}
		
	$row= $db->query("select count(*) count from ".$db->table('member_log')." a join ".$db->table('member')." m on a.uid=m.id where ".$where);
	return intval($row['count']);
}

/**会员账户记录   余额/积分
 * @param $type 类型：1余额，2积分
 * */
function get_member_log($uid=0,$type=0,$role_type=0, $uname='', $start_time="" , $end_time="", $start =0, $num =10, $val_type=0)
{
	global $db;
	$where ='1';
	if($role_type !=0)
	{
		$where .= " and a.role_type=".intval($role_type);
	}
	if($uid !=0)
	{
		$where .= " and a.uid=".intval($uid);
	}
	if($type !=0)
	{
		$where .= " and a.type=".intval($type);
	}
	if($uname!='')
	{
		$where .= " and m.uname ='".$uname."'";
	}
	if($start_time!='')
	{
		$where .= " and a.addtime>=".$start_time;
	}
	if($end_time!='')
	{
		$where .= " and a.addtime<=".$end_time;
	}
	if($val_type ==1)
	{
		$where .= " and a.val>=0";
	}
	elseif($val_type ==2)
	{
		$where .= " and a.val<0";
	}
	$row = $db->queryall("select a.*,m.uname from ".$db->table('member_log')." a join ".$db->table('member')." m on a.uid=m.id where ".$where." order by addtime desc limit ".$start.",".$num);
	
	foreach ($row as $k => $v) {
		$row[$k]['addtime'] = date("Y-m-d H:i:s", $v['addtime']);
	}
	return $row;
}

/**申请退换货*/
function add_order_service($row)
{
	global $db;
	$db->insert('order_service', $row);
}

function update_order_service($id, $row)
{
	global $db;
	$db->update('order_service', $row, array("id"=>intval($id)));
}

/** 我的退换货申请
 * @param $type
 * */ 
function get_myorder_service_count($uid=0, $type=0,$status=0)
{
	global $db;
	$where = '';
	if($uid !=0)
	{
		$where .=" and c.uid=".$uid;
	}
	if($type !=0)
	{
		$where .=" and c.type=".$type;
	}
	if($status !=0)
	{
		$where .=" and c.status=".$status;
	}
	$row = $db->query("select count(*) count from ".$db->table('order_service')." c left join ".$db->table('goods')." g on g.goods_id=c.goods_id left join ".$db->table('goods_spec')." s on g.goods_id=s.goods_id and s.values=c.spec where 1 ".$where);
	return intval($row['count']);
}

/** 我的退换货申请
 * @param $type
 * */ 
function get_myorder_service($uid=0, $type=0,$status=0, $start =0, $num =10)
{
	global $db;
	$where = '';
	if($uid !=0)
	{
		$where .=" and c.uid=".$uid;
	}
	if($type !=0)
	{
		$where .=" and c.type=".$type;
	}
	if($status !=0)
	{
		$where .=" and c.status=".$status;
	}
	$row = $db->queryall("select c.*,g.code,g.name,ifnull(s.attr_ids,'') as spec_ids,ifnull(s.`values`,'')as spec_name from ".$db->table('order_service')." c left join ".$db->table('goods')." g on g.goods_id=c.goods_id left join ".$db->table('goods_spec')." s on g.goods_id=s.goods_id and s.values=c.spec where 1 ".$where." order by addtime desc limit ".$start.",".$num);
	foreach ($row as $k => $v) {
		$row[$k]['img']= json_decode($v['img'],true);
		$row[$k]['thumb']= json_decode($v['thumb'],true);
		$row[$k]['addtime']= date('Y-m-d', $v['addtime']);
		$row[$k]['url'] = $v['code'].'-g.html';
		$row[$k]['spec'] = '';
		if($v['spec_ids'] !='')
		{
			$row[$k]['spec'] = get_spec_val($v['spec_ids'], $v['spec_name']);
		}
	}
	return $row;
}

/** 退换货
 * */ 
function get_service_info($id, $uid=0)
{
	global $db, $lang_service,$lang_service_type;
	$where = ' c.id='.intval($id);
	if($uid !=0)
	{
		$where .=" and c.uid=".$uid;
	}

	$row = $db->query("select c.*, r.refund_no,r.trade_no,r.trade_msg, r.status as refund_status,g.code,g.name,g.thumb as goods_thumb,og.price,ifnull(s.attr_ids,'') as spec_ids,ifnull(s.`values`,'')as spec_name from ".$db->table('order_service')." c left join ".$db->table('order_refund')." r on c.refund_no=r.refund_no join ".$db->table('order_goods')." og on og.goods_id=c.goods_id and og.spec=c.spec join ".$db->table('order')." o on o.id=og.order_id and c.order_sn=o.order_sn left join ".$db->table('goods')." g on g.goods_id=c.goods_id left join ".$db->table('goods_spec')." s on s.values=c.spec where ".$where);
	
	$row['speclist'] = get_spec_val($row['spec_ids'], $row['spec_name']);
	$row['img']= json_decode($row['img'],true);
	$row['thumb']= json_decode($row['thumb'],true);
	$row['addtime']= date('Y-m-d H:i:s', $row['addtime']);
	$row['url'] = $row['code'].'-g.html';
	$row['type_name'] = $lang_service_type[$row['type']];
	$row['status_name'] = $lang_service[$row['status']];
	return $row;
}

/*获取等级名称*/
function get_grade_name($ids, $grade = null)
{
	if(empty($ids))
	{
		return '所有';
	}
	if($grade == null)
	{
		$grade = get_grade();
	}
	$grade_new = array();
	foreach($grade as $k => $v) {
		$grade_new[$v['grade_id']] = $v;
	}
	if(!is_array($ids))
	{
		$ids = explode(",", $ids);
	}
	$name = array();
	foreach ($ids as $k => $v) {
		$name[] = $grade_new[$v]['grade_name'];
	}
	return implode(",", $name);
}

/*获取N天未登陆的用户*/
function get_unlogin_user($role_type=0,  $grade_ids='', $sex=0, $lasttime=0, $start=0, $num=null)
{
	global $db;
	$where ="a.status=1 ";
	if($role_type !=0)
	{
		$where .=" and role_type=".intval($role_type);
	}
	if($grade_ids !='')
	{
		$where .=" and grade_id in(".trim($grade_ids).") ";
	}
	if($sex !=0)
	{
		$where .=" and sex=".intval($sex);
	}
	if($lasttime !=0)
	{
		$where .=" and (lasttime is null or lasttime<".trim($lasttime).")";
	}	
	$where =" or (". $where."))";
	if($num !=null)
	{
		$where .=" limit ".$start.",".$num;
	}
	
	return  $db->queryall("select distinct b.id uid, b.uname from ".$db->table("login_log")." a right join ".$db->table("member")." b on a.uid=b.id where b.status=".normal." and (a.id is null ".$where);
}

/*获取N天未下单的用户*/
function get_unorder_user($grade_ids ='', $sex=0, $time='', $start=0, $num =null)
{
	global $db;  
	$where = "";
	if($grade_ids !='')
	{
		$where .=" and grade_id in(".trim($grade_ids).") ";
	}
	if($sex !=0)
	{
		$where .=" and sex=".intval($sex);
	}		
	$limit = $num ==null ? "" : " limit ".intval($start).",".intval($num);
	
	return $db->queryall("select distinct m.id uid from ".$db->table('order')." o right join ".$db->table('member')." m on o.uid=m.id where m.status=".normal.$where." and (o.id is null or (o.status=".order_finish." and o.add_time<'".$time."')) order by add_time desc ".$limit);
}

?>