<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'pay');//权限检测  
  
if (trim($act)==""){message("操作类型不正确。");} 
if (trim($pay_code)==""){message("获取不到支付方式编号。");}
if (trim($pay_name)==""){message("请填写支付方式名称。");}
if (strlen(trim($pay_name))>50){message("支付方式名称请控制在50个字符内");}
 
if($act =='config')
{ 	  
    /*$row=$db->fetch('payment','*', "pay_name='".trim($pay_name)."' and pay_code<>'".$pay_code."'");
    if ($row){message("支付方式名称已存在，请更换。");} */
	
		$config = array();
		foreach ($_POST as $k => $v) {
				if(strpos($k, 'payconfig_')===0 && $k!='[]')
				{
					$config[str_replace('payconfig_', '', $k)]= trim($v);
				}
		}

 	  $db->update('payment', array('pay_name' => trim($pay_name),'pay_desc' => trim($pay_desc),'sort' => intval($c_sort),'status' => intval($status),'config' => json_encode($config) ), array('pay_code' => trim($pay_code)));
		
    message("更新成功",'/admin.html?do=pay');
} 
	 
 




?>