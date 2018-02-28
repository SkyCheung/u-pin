<?php
if (!defined('in_mx')) {exit('Access Denied');}

$res = array('err' => '', 'res' => '', 'data' => array());
$plugin_path = plugin."com/";

if($act)
{
	if(!checkAuth($login_id, 'system'))//权限检测
	{
		$res['err'] = $lang['access_denied'];
		die(json_encode($res));
	}
	if($act == 'install')//安装
	{
		if (empty($code)) {
			message("插件代码不能为空");
		}
		
		$count = $db->rowcount("plugin", array("code"=>$code));
		if($count && intval($count)>0)
		{
			message("插件已安装过");
		}
		
		require $plugin_path.$code.'/'.$code.".php";
		
		$c_plugin = new $code;
		$plugin_cf = $c_plugin->get_config();
		$db -> insert('plugin', array('code' => trim($plugin_cf['code']), 'name' => trim($plugin_cf['name']), 'author' => trim($plugin_cf['author']), 'status' => 0, 'version' => trim($plugin_cf['version']), 'memo' => trim($plugin_cf['memo']),'config'=>json_encode($plugin_cf['config'])));		
		
		$c_plugin->install();
		message("安装成功","admin.html?do=plugin");
	}
	elseif($act == 'uninst')//卸载
	{
		if (empty($code)) {
			message("插件代码不能为空");
			die(json_encode($res));
		}
		$db -> delete('plugin', array('code' => $code));
		
		require $plugin_path.$code.'/'.$code.".php";		
		$c_plugin = new $code;
		$c_plugin->uninst();
		message("卸载成功","admin.html?do=plugin");
	}
	elseif($act == 'edit_status')//编辑排序
	{
		if (!is_numeric(trim($val))|| strlen(trim($val))>6) {
			$res['err'] = '必须是数字，且是6位之内';
			die(json_encode($res));
		}
	
		$db -> update('plugin', array('status' => trim($val)), array('code' => trim($id)));
		$res['res'] = '更新成功';
		die(json_encode($res));
	}
	elseif($act=="config") {
		if($code=='')
		{
			message("插件代码不能为空");
		}
		if(!file_exists($plugin_path.$code."/".$code.".php"))
		{
			message("插件文件已删除");
		}
		require_once $plugin_path.$code."/".$code.".php";	
		$c_plugin = new $code;
		$c_plugin ->init_config();
		
		exit();
	}
	elseif($isajax==1) {
		if (trim($plugin_code)==""){
			$res['err'] = '获取插件编号失败';	
			die(json_encode($res));
		} 
		if (trim($act)==""){$res['err'] = '操作类型不正确';die(json_encode($res));} 
		
		require $plugin_path.$plugin_code.'/'.$plugin_code.".php";
		
		$c_plugin = new $plugin_code;
		$c_plugin->$act();
	}	
}
else {
	checkAuth($login_id, 'system');//权限检测 
	
	$plugin =array();
	$plugin_list = get_dir($plugin_path);
	$plugin = $db -> fetchall('plugin', '*'); 
	foreach ($plugin_list as $k => $v) {
		$tmp = array();
		$tmp['code'] = $v;
		$tmp['name'] = '';
		foreach ($plugin as $key => $val) {			
			if($v == $val['code'])
			{
				$tmp = $val;
				$tmp['config'] = json_decode($tmp['config'], true);
				$tmp['has_install'] =1;
				break;
			}			
		}
		if($tmp['name'] == '')
		{
			if(file_exists($plugin_path.$v."/".$v.".php"))
			{
				require_once $plugin_path.$v."/".$v.".php";			
				$c_plugin = new $v;
				$tmp = $c_plugin->get_config();
				$tmp['has_install'] = 0;
				$tmp['status'] = 0;	
			}
			else {
				continue;
			}		
		}
		
		$row[] = $tmp;
	} 
}
?>