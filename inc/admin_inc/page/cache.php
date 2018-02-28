<?php
if (!defined('in_mx')) {exit('Access Denied');}
 
del_dir(cache_data);
del_dir(cache.'admin/');
del_dir(cache.$ym_pc_tpl.'/');
del_dir(cache.$ym_m_tpl.'/');

mdir(cache_data);

update_config();

$pre = "<?php if (!defined('in_mx')) {exit('Access Denied');}".PHP_EOL;

//更新静态数据
if(!is_dir(cache_data)){mkdir(cache_data);}
if(!is_dir(cache_static)){mkdir(cache_static);}

//地区
if(!is_file(cache_static.'district.php')) 
{
	$tmp_district = get_district('level<4');
	write_file(cache_static.'district.php', $pre."\$ym_district=".@arrayeval($tmp_district, 'id').";");
	
	$tmp_dist="var ChineseDistricts = {";
	$level = 4; 
	$tmp_country = array();//国家ids
	foreach ($tmp_district as $k => $v) {
		$tmp = get_district('level<4 and pid=' . $v['id']);
		if($v['pid']==0)
		{
			$tmp_country[] = $v['id'];			
		}
		if(count($tmp) !=0)
		{
			$str = $v['id']. ': {';
			foreach ($tmp as $key => $val) {
				$str .= $val['id'].":'". $val['name']."',";
			}
			$tmp_dist .= rtrim($str, ','). ' },' ;
		}		
	}
	
	$tmp_dist = rtrim($tmp_dist, ',') ."};";
	write_file(cache_static.'district.js', $tmp_dist);
	write_file(cache_static.'country.php', $pre."\$ym_country=array(".implode(',', $tmp_country).");");
	
   	$province = array_query('level', 1, $tmp_district);
    $province_list='';
	$city_list =''; 
	$ym_list='';
	foreach ($province as $k => $v) {		
		$city_list .= '"'.$v['id'].'":[';
		$city = array_query('pid', $v['id'], $tmp_district);
		foreach ($city as $key => $val) {
			$city_list .= '{"id":'.$val['id'].',"name":"'.$val['name'].'"},';
		}
		$ym_list .='<li><a href="#none" data-value="'.$v['id'].'">'.$v['name'].'</a></li>';
		$province_list .='"'.$v['name'].'":{id:"'.$v['id'].'",root:'.$v['pid'].',c:'. (count($city)>0 ? $city[0]['id'] :0) .'},';
		$city_list = rtrim($city_list, ',') .'],'; 
	}
	$city_list = rtrim($city_list, ',');
	$province_list = rtrim($province_list, ','); 
	$iplocation = read_file("./static/js/location_tpl.js"); 
	$iplocation=str_replace('ym_area_list', $ym_list , $iplocation);
	$iplocation=str_replace('ym_iplocation', $province_list , $iplocation);
	$iplocation=str_replace('ym_provinceCityJson', $city_list , $iplocation);
	write_file(cache_static.'location.js', $iplocation);	
	
	write_file(cache_static.'province.js', 'var province={'.$province_list."}");
}
 
//导航栏
$tmp_nav = $db -> fetchall('nav', '*', 'status=1', ' type asc,sort asc');
foreach ($tmp_nav as $k => $v) {
	$tmp_nav[$k]['url']= stripos($v['url'], 'http://') ===0 ? $v['url'] : "/".$v['url'];
}
write_file(cache_data.'nav.php', $pre."\$ym_nav=".@arrayeval($tmp_nav,'id').";");

//通讯
$tmp_message = $db -> fetchall('message', '*');
write_file(cache_data.'message.php', $pre."\$ym_message=".arrayeval($tmp_message, 'id').";");

//属性
$tmp_attr = $db -> queryall("SELECT b.name type_name,a.* FROM ".$db->table('attribute')." a join ".$db->table('type')." b on a.type_id=b.id where search=1 and uitype<>'input'");
write_file(cache_data.'attrs.php', $pre."\$ym_attrs=".@arrayeval($tmp_attr,'id').";");

//配送方式
$tmp_express = get_express();
$tmp_express_common = get_express_common();
$tmp_express_district = get_express_district(0, 1);
$tmp_express_picksite = get_express_picksite();

foreach ($tmp_express_common as $k => $v) {
	if($v['id'] == 1)
	{
		unset($tmp_express_common[$k]);//排除自提点
	}
}

write_file(cache_data.'express.php', $pre."\$ym_express=".@arrayeval($tmp_express,'').";");
write_file(cache_data.'express_common.php', $pre."\$ym_express_common=".@arrayeval($tmp_express_common,'id').";");
write_file(cache_data.'express_district.php', $pre."\$ym_express_district=".@arrayeval($tmp_express_district,'id').";");
write_file(cache_data.'express_picksite.php', $pre."\$ym_express_picksite=".@arrayeval($tmp_express_picksite,'').";");

//更新缓存文件 
$row=  $db->fetchall('express_track', '*');	
write_file(cache_static.'express_track.php', $php_pre."\$ym_express_track=".@arrayeval($row,'code').";".PHP_EOL);

//短信
$row=  $db->fetchall('sms_config', '*');	
$con = '';
foreach ($row as $k => $v) {
	$v['config'] = json_decode($v['config'], true);
	$con .= "\$ym_".$v['code']."=".@arrayeval($v,'').";".PHP_EOL;
}
write_file(cache_static.'sms.php', $php_pre . $con);

//第三方登录
$row =  $db->fetchall('oauth', '*');
write_file(cache_static.'oauth.php', $pre."\$ym_oauth=".@arrayeval($row,'code').";".PHP_EOL); 

//某分类下面的品牌
$row =  get_brand();
$cat_brand = array();
foreach ($row as $k => $v) {
	if(trim($v['cat_ids']) ==''){
		continue;
	}
	$cids = explode(",", $v['cat_ids']);
	foreach ($cids as $key => $val) {
		$cat_brand[$val][]= $v;
	}
}
write_file(cache_data.'cat_brand.php', $pre."\$ym_cat_brand=".@arrayeval($cat_brand).";".PHP_EOL); 

//自定义内容
update_diy();

require_once './inc/lib/admin/perm.php';
cache_perm($id, 0); //缓存权限 

message("更新完成。");


?>