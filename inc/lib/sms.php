<?php
if (!defined('in_mx')) {exit('Access Denied');}

/**
 * 短信服务类
 */
class sms {
	private $sms_config;
	private $sms;
	private $sms_sp;//提供商
	
	/**
	 * 
	 * @param $sms_sp 提供商
	 * */
	public function __construct($sms_sp='') {
		global $ym_sms_sp;
		$sms_sp = $sms_sp==''? $ym_sms_sp : $sms_sp;
		$this->sms_sp = $sms_sp;
		$ym_val=get_cache('sms', cache_static,$sms_sp);
		$this->sms_config = $ym_val;
		require plugin.'sms/'.$sms_sp.'/'.$sms_sp.'.php';
	}

    /**
	 * @param $recNum 手机号码，多个号码用英文逗号隔开
	 * @param $tpl_type 模板类型  reg login updatepwd
	 * @param $extend 公共回传参数
	 * @param $param 短信模板变量 	
	 * */
	public function send($recNum, $tpl_type,$param='', $extend='')
	{
		$sms =new $this->sms_sp;
		return $sms->sendsms($recNum, $this->sms_config['config']['appkey'],$this->sms_config['config']['secretKey'],$this->sms_config['signname'],$param,$this->sms_config['config'][$tpl_type],$extend);
	}
	
	public function query($recNum='')
	{
			
	}
}

//短信临时表
function add_sms_session($mobile, $code, $tpl_type='')
{
	global $db;
	$db->insert('sms_session', array('mobile'=>$mobile,'code'=>$code,'sendtime'=>time(),'ip'=>getip(),'tpl_type'=>$tpl_type));
}

//获取短信验证码
function get_sms_session($mobile, $code, $tpl_type, $time_out = 10)
{
	global $db;
	return	$db->query("select id from ".$db->table('sms_session')." where mobile='".$mobile."' and code='".$code."' and status=0 and tpl_type='".$tpl_type."' and sendtime>=".strtotime(date('Y-m-d H:i:s',strtotime("-".$time_out." minute")))." order by sendtime desc limit 0,1");	
}

//获取用户尝试次数
function get_sms_err_count($mobile, $tpl_type)
{
	global $db;
	$row = $db->fetch('sms_session',"err_count",array('mobile'=>$mobile, 'tpl_type'=>$tpl_type), 'sendtime desc');	
	return $row ? intval($row['err_count']) : -1;
}

//更新用户尝试次数
function update_sms_err_count($mobile, $tpl_type)
{
	global $db;
	return	$db->query("update ".$db->table('sms_session')." set err_count=err_count + 1 where status=0 and mobile='".$mobile."' and tpl_type='".$tpl_type."'");
}

//更新验证码为已用
function update_sms_status($mobile, $tpl_type)
{
	global $db;
	return	$db->update('sms_session', array('status'=>1),array('mobile'=>$mobile, 'tpl_type'=>$tpl_type));
}

function get_sms_count($mobile='', $tpl_type='', $minute=2, $is_ip=0)
{
	global $db;
	$where ='';
	if($is_ip==1)
	{
		$where =" and ip='".getip()."'";
	}
	else {
		$where =" and mobile=".$mobile;
	}
	$row = $db->query("select count(*) sms_count from ".$db->table('sms_session')." where 1 and tpl_type='".$tpl_type."' and sendtime>=".(time()-intval($minute)*60).$where);
	
	if($row)
	{
		return intval($row['sms_count']);
	}
	return 0;
}

	//校验短信验证码
   function check_smscode($mobile, $smscode, $type)
   {
	    if($smscode =='' || strlen($smscode)<4)
		{
			return "请填写短信验证码";
		}
		$err_count = get_sms_err_count($mobile, $type);
		$sess_name = 'sms_'.$type.'_err_time';
		
		if(isset($_SESSION[$sess_name]))
		{
			$sms_limit_time = ceil(($_SESSION[$sess_name] + 900-time())/60);
			if($sms_limit_time<=0)
			{
				unset($_SESSION[$sess_name]);
				return "验证码已失效，请重新发送";
			}
		}

		if($err_count >= 5  && (!isset($_SESSION[$sess_name]) || $sms_limit_time>0))
		{			
			return "您已输错5次，请".$sms_limit_time."分钟后再试";
		}
		$sms_session = get_sms_session($mobile, $smscode, $type);
		
		if(!$sms_session) {
			
			if($err_count==-1)
			{
				$_SESSION[$sess_name] = time();
			}
			$err_count ++;
			if($err_count==4)
			{
				session_start();
				$_SESSION[$sess_name] = time();
			}
			
			update_sms_err_count($mobile, $type);//更新错误次数
		 	return "短信验证码不正确";
	 	}
		
		return '';
 	}


/**发送短信通知提醒*/
function sms_notice($mobile, $type, $param=array())
{
	global $ym_notice_mobile;
	
	switch ($type) {
		case 'order': //下订单通知商家
			$tpl_type ='order';
			$mobile = $ym_notice_mobile;
			break;
		
		default:
			$param='';
			break;
	}
	
	return sendsms_patch($mobile, $tpl_type, json_encode($param));
}

  /**群发短信
 * @param string/array $mobile 手机号, 多个号码用数组或用英文逗号隔开
 * @param string $tpl_type 短信模板类型
 * */
function sendsms_patch($mobile, $tpl_type, $param="")
{
	global $ym_name;
	$res = array('err' => '', 'res' => '', 'data' => array()); 
	
	if(!is_array($mobile))
	{
		$mobile = explode(",", $mobile);
	}
	array_filter($mobile);
	foreach ($mobile as $k => $v) {
		if(intval($v)==0 || is_mobile($v)==false) //过虑格式不正确的号码
		{
			unset($mobile[$k]);
		}
	}
	
	if(count($mobile)==0)
	{
		$res['err'] = "手机号码为空，或格式不正确";
		return $res;
	} 
	 			
	$sms= new sms;
	$result= $sms->send(implode(",", $mobile), $tpl_type, $param);
	if($result['err'] !='')
	{
		$res['err'] = $result['err'];
	}
	
	return $res;
}
  
	
?>