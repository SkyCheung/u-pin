<?php
if (!defined('in_mx')) {exit('Access Denied');}
/*模板管理*/
checkAuth($login_id, 'tpl');//权限检测 
 
if(!$act){
	message('操作类型错误');
}

if($act == 'goods_details')//商品详情
{
	if (trim($tplname)==""){message("请填写名称。");}
	if (strlen(trim($tplname))>50){message("名称请控制在50个字符内");}
	if (trim($details)==""){message("请填写内容。");}
	
	$tplpath= upload_tpldetails;
	if(!is_dir($tplpath))
		{
			mdir($tplpath);
		}
	$filename  = urlencode($tplname);
	html_file($tplpath.$filename.'.html', $details);
	message('保存成功');
}
elseif($act == 'delivery')  //发货单
{
	$name = '发货单';
	$content = isset($content) ? trim($content) :'';
	$row =$db->fetch('tpl', 'id', array('type'=>tpl_delivery, 'name'=>$name));
	if($row && count($row)>0)
	{
		update_tpl(tpl_delivery, $name, $content);
	}
	else {
		add_tpl(tpl_delivery, $name, $content);
	}
	
	message('保存成功');
}

function add_tpl($type=0, $name='',$content='')
{
	global $db;
	$db->insert('tpl', array('type'=>$type, 'name'=>$name, 'content'=>$content));
}

function update_tpl($type=0, $name='',$content='')
{
	global $db;
	$db->update('tpl', array('content'=>$content), array('type'=>$type, 'name'=>$name));
}

?>