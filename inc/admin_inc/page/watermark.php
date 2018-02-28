<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'set');//权限检测

$c_iswm[intval($ym_is_watermark)]='  checked="checked" ';
$thumb_type[intval($ym_thumb_type)]=' checked="checked" ';
$c_wmwz[$ym_watermark_posi] = ' checked="checked" ';

?>