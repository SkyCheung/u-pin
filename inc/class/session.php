<?php
if (!defined('in_mx')) {exit('Access Denied');}

class ym_session
{
	public function __construct()
	{
		
	}
	
	function get_session_id($val)
	{
		if(!isset($val) || $val =='' || !isset($_SESSION[$val]))
		{
			return '';
		}
		return session_id();
	}
}

?>