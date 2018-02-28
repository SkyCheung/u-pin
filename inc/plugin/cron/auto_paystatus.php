<?php
/**
 * 
 * @Author: Sky Cheung<sky_zhang@live.com>
 * Date: 18/2/28
 * @Copyright (C) Dev-os Inc. All rights reserved
 *
 */
if (!defined('in_mx')) {exit('Access Denied');}


/*$list = get_goods_list('','goods_id,number,salenum,addtime','',0,null);
$clear_cache = false;
foreach($list as $key=>$v){
    if($v['number']==0){
        $arr['status'] = goods_down;
        update_goods($v['goods_id'], $arr);
        $clear_cache = true;
    }
}

if($clear_cache){
    $cache_file = cache_data.'diy.php';
    if(file_exists($cache_file)){
        unlink($cache_file);
    }
}*/