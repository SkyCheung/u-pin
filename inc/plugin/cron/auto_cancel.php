<?php
/**
 * 
 * @Author: Sky Cheung<sky_zhang@live.com>
 * Date: 18/1/28
 * @Copyright (C) Dev-os Inc. All rights reserved
 * 20分钟内不完成支付的订单自动取消，恢复库存
 */
if (!defined('in_mx')) {exit('Access Denied');}
//echo 8888;exit;
$now = time()-20*60; //20 分钟
$where = 'and o.status = '.order_paying.' and o.add_time<'.$now;

$clear_cache = false;
$list = get_order_list(0, '', 0,$where);
foreach($list as $key=>$v){
    //只下有单减库存才要恢复库存
    if($ym_stocktime == 1){
        foreach($v['goods'] as $key2=>$v2){
            $sql = 'update '.$db->table('goods').' set status='.goods_up.' , number = number + '.$v2['num'].', salenum = salenum-'.$v2['num'].' where goods_id='.$v2['goods_id'];
            $db -> exec($sql);
        }
    }
    $order = array('id'=>$v['oid'],'status'=>order_cancel);
    update_order($order);
    $clear_cache = true;
}

if($clear_cache){
    $cache_file = cache_data.'diy.php';
    if(file_exists($cache_file)){
        unlink($cache_file);
    }
}