<?php
if (!defined('in_mx')) {exit('Access Denied');}

$webtitle=$name.' - '.$web_endtitle; 
 
$db = dbc(); 
$row = get_news(1); 
$nav_footer = get_nav('bot');

$oauth = get_oauth();
$oauth_userinfo = json_decode($_SESSION['oauth_userinfo'], true);
 
@include template("reg",$ym_tpl."/");
exit();

?>