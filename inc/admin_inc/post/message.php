<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'message');//权限检测
  
if (trim($act)==""){message("操作类型不正确。");} 
require_once "inc/lib/admin/setting.php";

 if($act =='email')
 {
 	if (trim($code)==""){message("获取编号失败。");} 
 	  $config=array('host'=>trim($host),'port'=>intval($port),'email'=>trim($email));  
	  if(trim($pwd)=='')
	  {
	  	$row=$db->fetch('message', 'config', array('code'=>$code));
		  $old_config= json_decode($row['config'],true);
	  	$config['pwd']= endecrypt($old_config['pwd'],'encode');
	  }
	  else {
	  	$config['pwd']= endecrypt($pwd,'encode');
	  }	  
	  
    $db->update('message', array('config' =>json_encode($config) ), array('code'=>$code));
	  
	  message("保存成功",'/admin.html?do=message');  
 }
 elseif ($act=='sendtestmail') {
 		$res = array('err' => '', 'res' => '', 'data' => array()); 
	  if(!isset($val) || trim($val)==''){$res['err']='获取收件邮箱失败';}
	 
 		if(sendmail(trim($val),'测试邮件发送','恭喜您配置邮件服务器成功！ '))
		{
			$res['res']='恭喜您配置成功！'; die(json_encode($res));
		}else {
			$res['err']='发送失败，'; die(json_encode($res));
		}
 }
else if($act =='sms')
{
 	if (trim($code)==""){message("获取编号失败。");} 
 	  $config = array();  
		foreach ($_POST as $k => $v) {
				if(strpos($k, 'smsconfig_')===0 && $k!='[]')
				{
					$config[str_replace('smsconfig_', '', $k)]= trim($v);
				}
		}
		
		$row = $db->fetch('sms_config', 'config', array('code'=>$code));
	  if($row)
	  {
	  	 $db->update('sms_config', array('name'=>trim($name),'signname'=>trim($signname),'status'=>intval($status),'config' =>json_encode($config)), array('code'=>$code));
	  }
	  else {
	  	 $db->insert('sms_config', array('code'=>$code,'name'=>trim($name),'signname'=>trim($signname),'status'=>intval($status),'config' =>json_encode($config) ));
	  }	  
	  
	  if($status==1)
	  {
	  	$db->update('sms_config', array('status'=>0), "code<>'".addslashes($code)."'");//将其它短信设置不启用
	  	update_shopconfig('sms_sp', addslashes($code));
	  }
	  
	//更新缓存文件 
	$row=  $db->fetchall('sms_config', '*');
	$con = '';
	foreach ($row as $k => $v) {
		$v['config'] = json_decode($v['config'], true);
		$con .= "\$ym_".$v['code']."=".@arrayeval($v,'').";".PHP_EOL;
	}
	write_file(cache_static.'sms.php', $php_pre . $con);
   
	message("保存成功",'/admin.html?do=message.sms&id=1');  
	
}
	 



?>