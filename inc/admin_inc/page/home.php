<?php
if (!defined('in_mx')) {exit('Access Denied');}
 
if($act=='left')
{
	$do="left";	
} 
else {
	 
	$tmp_menu = get_user_menu($login_id);
	$menu = array();
	if($tmp_menu && count($tmp_menu)>0)
	{
		$menu = getTree($tmp_menu, 0, 'id', 'pid');
	}
	$menu_json =json_encode($menu); //print_r($menu);
}

?>