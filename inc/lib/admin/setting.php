<?php
if (!defined('in_mx')) {exit('Access Denied');}


function get_shopconfig_bykey($key)
{
	global $db;
	return $db->fetch('shop_config', '*', array('`key`'=>$key));
}

function get_shopconfig()
{
	global $db;	
	return $db->fetchall('shop_config', '*');
}

function update_shopconfig($key, $value)
{
	global $db;
	$db -> update('shop_config', array('`value`'=>$value), array('`key`'=>$key)); 
}

function get_message()
{
	global $db;
	$row=$db->fetchall('message','*','',' id desc');
	return $row;
}


?>