<?php
if (!defined('in_mx')) {exit('Access Denied');}

/* 常量 */

/*商品状态*/
const goods_del= -1; //删除
const goods_down= 0; //下架
const goods_up= 1; //上架
const goods_normal= 2; //正常
const goods_settimeup= 3;//定时上架

//配送类型
const expresstype_delivery=1;//商家配送
const expresstype_pickup=2;//自提
const pickup='pickup';

//订单状态：1等待付款,2正在发货,3等待收货,6取消，7删除,8完成
const order_paying=1; //等待付款
const order_deliver=2; //正在发货
const order_receiving=3; //等待收货
const order_refund=4; //退货 
const order_cancel=6; //取消
const order_del=7; //删除
const order_finish=8; //完成 

$lang_status[order_paying] = "待付款";
$lang_status[order_deliver] = "等待发货";
$lang_status[order_receiving] = "待收货";
$lang_status[order_refund] = "已退货";
$lang_status[order_cancel] = "已取消";
$lang_status[order_del] = "已删除";
$lang_status[order_finish] = "完成";

//支付状态
const pay_unpayed=0; //未支付
const pay_payed=1; //已支付

//发货状态
const deliver_not=0; //未发货
const deliver_part=1; //部分发货
const deliver_ed=2; //已发货
$lang_deliver[deliver_not] = '未发货';
$lang_deliver[deliver_part] = '部分发货';
$lang_deliver[deliver_ed] = '已发货';

//售后类型
const service_change=1; //换货
const service_return=2; //退货
$lang_service_type[service_change]='换货';
$lang_service_type[service_return]='退货';

//售后状态
const service_apply =1; //处理中
const service_passed =2; //审核通过
const service_failed =3; //审核不通过
const service_handlegoods =4; //商品处理
const service_cancel =5; //取消
const service_finish =8; //已完成
$lang_service[service_apply] = "处理中";
$lang_service[service_passed] = "审核通过";
$lang_service[service_failed] = "审核不通过";
$lang_service[service_handlegoods] = "商品处理";
$lang_service[service_cancel] = "已取消";
$lang_service[service_finish] = "已完成";

//会员状态
const locked=0; //冻结
const normal=1; //正常

//客户端来源
const client_all =0;
const client_pc =1;
const client_m =2;
const client_app =3;
$lang_client[client_all] = '全平台';
$lang_client[client_pc] = 'PC端';
$lang_client[client_m] = '手机端';
$lang_client[client_app] = 'APP';

//操作者类型
const role_system = 0; //系统 
const role_admin = 1; //管理员
const role_user = 2; //会员 
const role_seller = 3; //商家
$lang_role_name[role_system] ='系统';
$lang_role_name[role_admin] ='管理员';
$lang_role_name[role_user] ='会员';
$lang_role_name[role_seller] ='商家';

//登录情况
const login_success =1;//成功
const login_fail =2;//失败

//资产类型
const asset_balance =1; //余额
const asset_point =2; //积分
$lang_asset_type[asset_balance] = '余额';
$lang_asset_type[asset_point] = '积分';

//文章固定分类
const cat_news =1; //新闻分类
const cat_help =2; //帮助中心
const cat_system =3; //系统分类

const tpl_delivery = 1; //发货单
const tpl_details = 2; //商品详情

//促销优惠方式
const promotion_type_fixed=1; //统一价
const promotion_type_cut=2; //直减
const promotion_type_off=3; //折扣

//优惠券领取方式
const coupon_gettype_get = 1; //自取
const coupon_gettype_give = 2; //平台发放

//优惠券适用范围
const coupon_type_cat =1; //品类
const coupon_type_goods =2; //商品

//优惠券状态
const coupon_unused =0; //未使用
const coupon_used =1; //已使用
const coupon_expire =2; //已过期
const coupon_freeze =3; //已冻结

?>