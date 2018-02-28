<?php
if (!defined('in_mx')) {exit('Access Denied');}


//UPDATE  `t_columns` SET  `c_width` =  '200', `c_height` =  '160' WHERE  `c_sid` =18 AND c_type =  'product'

$db->update('columns', array('c_width' => intval($kk_width),'c_height' => intval($kk_height)), array('c_type' => $kk_type));

message("更新完成。。。",$url);
?>