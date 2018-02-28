<?php
if (!defined('in_mx')) {exit('Access Denied');}


$ym_express_track= get_cache('express_track', cache_static);
		
die('{"EBusinessID":"'.$ym_express_track['kdniao']['appid'].'","UpdateTime":"2016-11-11 14:40:25","Success":true}');

?>