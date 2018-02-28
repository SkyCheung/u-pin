<?php
 if (!defined('in_mx')) {exit('Access Denied');}

 /**更新配置文件*/
function update_config(){
	global $db,$login_id;
	
	$rs=$db->fetchall('shop_config', '*'); 
    $htmlext=$rs['htmlext']==0?'.html':'/';
	$php_pre = "<?php if (!defined('in_mx')) {exit('Access Denied');}".PHP_EOL;
	$config_con= $php_pre;
	
	//站点设置
    foreach ($rs as $k => $v) {
    	$v['value'] = str_replace("'", "\\'", stripslashes($v['value']));
    	switch ($v['key']) {
    		case 'keepword'://用户名保留字
    			write_file(cache_data."censor.php", $php_pre."\$ym_keepword='".$v['value']."';");
    			break;
    		case 'invoice_con'://发票内容
    			$tmp_invoice= explode(PHP_EOL, $v['value']); 
				$config_con .='$ym_'.$v['key']."=". array_to_string(array_filter($tmp_invoice)).";";
    			break;
			case 'ditribution_config'://分销
				$ditrib = json_decode($v['value'], true);
				$commiss =array();
				foreach ($ditrib['commission'] as $key => $val) {
					$commiss[$val['id']] = $val;
				}
				$ditrib['commission'] = $commiss;
				$config_con .='$ym_'.$v['key']."=". arrayeval($ditrib).";"; 
				break;
			case 'chat_config'://聊天配置
				$config_con .='$ym_'.$v['key']."=". arrayeval(json_decode($v['value'], true)).";"; 
				break;	
    		default:
    			$config_con .='$ym_'.$v['key']."='". $v['value']."';";
    			break;
    	}   	
    }	
	//是否开自提点
	$is_pickup = get_express_common(0, 'pickup');
	$config_con .="\$ym_is_pickup=". (count($is_pickup)>0 ?  $is_pickup[0]['status'] : 0).";";
	//默认短信服务商
	//$config_con .="\$ym_sms_sp='alidayu';";
	//默认快递服务商
	$config_con .="\$ym_exp_sp='kdniao';";
	
	//是否开启余额支付
	require_once './inc/lib/pay.php'; 
	$is_bal = get_payment('bal', 1, '1', 0); //支付方式
	$config_con .="\$ym_is_bal=". (count($is_bal)>0 ? "1" : "0") .";";
	
	$config_con=str_replace('$'."ym_htmlext='0';", '$'."ym_htmlext='.html';", $config_con);
	$config_con=str_replace('$'."ym_htmlext='1';", '$'."ym_htmlext='/';", $config_con);
	
	$row=$db->fetchall('columns', '*',  '', 'c_sort asc, id asc', '');
	foreach($row as $sort){
		$sortarr['id']=$sort['id'];
		$sortarr['pid']=$sort['c_pid'];
		$sortarr['is_system']=$sort['is_system'];
		$sortarr['name']=$sort['c_title'];
		$sortarr['title']=$sort['c_title'];
		$sortarr['img']=$sort['c_simg']=="" ? '': $sort['c_simg'];
		$sortarr['code']=$sort['c_code'];
		$sortarr['txt']=$sort['c_txt'];
		$sortarr['width']=$sort['c_width'];
		$sortarr['member']=$sort['c_member'];
		$sortarr['height']=$sort['c_height'];
		$sortarr['num']=$sort['c_num'];
		$sortarr['smtype']=$sort['c_smtype'];
		$sortarr['dir']=$sort['c_dir'];
		$sortarr['type']=$sort['c_type'];
		$sortarr['is']=$sort['c_is'];
		$sortarr['look']=$sort['c_look'];
		$sortarr['sort']=$sort['c_sort'];
		$sortarr['file']=$sort['c_name'];
		$sortarr['is_help']=$sort['is_help'];
		$tempdirdir=$sort['c_dir']==""?"":$sort['c_dir']."/";
		$sortarr['url']=$sort['c_url']==''?'/'.$tempdirdir.'n-'.$sort['c_name'].$htmlext:$sort['c_url'];
		$sortarr['mo']=$sort['c_mo'];
		$sortarr['son']=array();
		$sortaaa[]=$sortarr;
		if ($sort['c_is']==1){
			$sortbbb[]=$sortarr;
		}		
	}
	$sort_con=str_replace("'' => Array", "'son' => Array", @arrayeval(genTree($sortbbb),'code'));
	$sort_con2=str_replace(",'' => Array()", "", @arrayeval($sortaaa,'file'));
	$sort_con3=str_replace(",'' => Array()", "", @arrayeval($sortaaa,'id'));

   $config_con .=PHP_EOL;	
   $config_con.='$ym_sort = '.$sort_con.';$ym_allsort = '.$sort_con2.';$ym_idsort = '.$sort_con3.';';
     
   //所有商品分类
   $cat= get_category(0, false, -1);
   $cats = array();
   $cat_kv=array();
   foreach ($cat as $k => $v) {
   		$cats[$v['id']] =$v;
   		$cats[$v['id']]['url'] = $v['link']!=''? $v['link']: '/'.$v['urlname'].$htmlext;
		$cat_kv[$v['id']]['id'] = $v['id'];
		$cat_kv[$v['id']]['code'] = $v['urlname'];
   }
   $catlist= str_replace(",'' => Array()","",  @arrayeval($cats,'id')); 
   $catlist_kv= str_replace(",'' => Array()","",  @arrayeval($cat_kv,'code')); 
   $config_con.= "\$ym_cats=".$catlist.";" ;
   $config_con.= "\$ym_cats_kv=".$catlist_kv.";" ;
   
   //不含不显示的分类
   $cat= get_category(0, false, 1);
   $cats = array();
   foreach ($cat as $k => $v) {
   		$cats[$v['id']] =$v;
   		$cats[$v['id']]['url'] = $v['link']!=''? $v['link']: $v['urlname'].$htmlext;
   }
   $catlist= str_replace(",'' => Array()","",  @arrayeval($cats,'id')); 
   $config_con.="\$ym_cat=".$catlist.";" ;
   
   write_file(Webfile, $config_con);
}

 /**更新自定义内容*/
function update_diy(){
	global $db,$ym_cats,$shopid,$site_code,$ym_idsort,$conext,$ym_htmlext,$ym_sort,$ttteadxxid;
		
	$filecon0 = "<?php if (!defined('in_mx')) {exit('Access Denied');}";
	require_once './inc/lib/admin/promotion.php';
	require_once './inc/lib/promotion.php';
	require_once './inc/lib/coupon.php';
	
	$cat_brand = get_cache('cat_brand','./cache/data/');
	
	//自定义内容	
	$row= $db->fetchall('diy', '*',  array(), 'id asc', ''); 
	foreach($row as $d){ //if($d['id']!=14){continue;}
		$tempconname='filecon'.$d['index'];
		$sql='';
		$sow= array();
		switch ($d['diy_type']) {
			case 'goods':
				$recom = json_decode($d['recommends'], true);
				$where='';
				$field='goods_id,code,g.name,subname,cat_id,brand_id,g.img,thumb,imgs,number,virtualnum,price,marketprice,g.addtime';
				$order= " g.sort asc,addtime desc ";
				
				if($recom['discount'] == 1) //限时抢购/秒杀
				{					
					$list = get_discount("and id in(".$d['promotion_ids'].")", 0 , $d['num'],1);					
				}
				else {		
					$cat_ids ='';
					foreach ($recom as $k => $v) {
						if($v == '1')
						{
							$where.='or '. $k."=".$v.' ';
						}			
					}
					if($where!='')
					{
						$where = ltrim($where, "or");
						$where = strpos($where, ' or ')!==false ? ' and ('.$where.')' : ' and '.$where;
					}
	
					$cat_ids = $d['cat_ids'];
					if($d['cat_ids']=='all')
					{
						$cat_ids = get_childIDs(0);	//所有分类id			
					}
					
					$list=array();
					if($d['is_eachnum'] == 0 && $d['is_childnum'] == 0) //分类和子分类不带商品
					{
						$cid = $cat_ids;
						if($d['include_son'] ==9)
						{
							$cid = get_childIDs($cid);	
						}
						else if($d['include_son'] !=0 ) {						
							$cid = get_childIDs($cid, false, intval($catinfo['level'])+1);						
						}
						$list[0]['child'] = array();
						$list[0]['goods'] =  get_goods_list($where.($cid==''?'  ':" and (cat_id in(".$cid.")  or g.goods_id ". get_extend_goods($cid) .")"), $field, $order, 0, $d['num']);
						$list[0]['brands'] = array();
					}
					else
					{ 
						$list = explode(',', str_replace("'","",$cat_ids));	//所选择分类
						foreach ($list as $k => $cid) {
							$catinfo = $ym_cats[intval($cid)];
							$list[$k] = $catinfo;
							$list[$k]['brands'] = $cat_brand[$cid];
																						
							if($d['include_son'] ==0 )//不含下一级
							{
								$list[$k]['child'] = array();							
							}
							else {
									$childs = get_children($cid); 						
									
									if($d['include_son'] ==9) //含所有子分类 
									{
										$cid = get_childIDs($cid); 
									} 
									else {					
										$cid = get_childIDs($cid, false, intval($catinfo['level'])+1);																
									}
									if($d['is_childnum'] == 1) //各分类下面一级子分类  的数量
									{
										foreach ($childs as $key => $val) { 
											$childs[$key]['goods']= get_goods_list(' and ' . "(cat_id in(".$val['id'].")  or g.goods_id ". get_extend_goods($val['id']) .")". $where, $field, $order, 0, $d['num']); //下一级子分类产品
										}
									}	
								$list[$k]['child'] = $childs; 		
							}	
							if($d['is_eachnum'] == 1)//各分类数量
							{	
								$list[$k]['goods'] = get_goods_list(' and ' . "(cat_id in(".$cid.")  or g.goods_id ". get_extend_goods($cid) .")". $where, $field, $order, 0, $d['num']);
							}												
						}
					}					 
				}
				 
				//print_r($list);
				//print count($list);
				foreach($list as $i=>$c){
					$goodslist = $c['goods'];
					if($goodslist && count($goodslist)>0)
					{
						$iik =0;
						foreach ($goodslist as $k => $v) {		
							$iik+=1;
							$list[$i]['goods'][$k]['goods_id']=$v['goods_id'];
							$list[$i]['goods'][$k]['i']=$iik;
							$list[$i]['goods'][$k]['code']=$v['code'];
							$list[$i]['goods'][$k]['name']= trim($v['name']);
							$list[$i]['goods'][$k]['subname']= trim($v['subname']);
							$list[$i]['goods'][$k]['cat_id']=$v['cat_id'];
							$list[$i]['goods'][$k]['brand_id']=$v['brand_id'];
							$list[$i]['goods'][$k]['img']=$v['img'];	
							$list[$i]['goods'][$k]['thumb']=$v['thumb']; 
							$list[$i]['goods'][$k]['imgs']=$v['imgs']; 
							$list[$i]['goods'][$k]['number']=$v['number'];
							$list[$i]['goods'][$k]['salenum']= $v['salenum'];
							$list[$i]['goods'][$k]['virtualnum']= $v['virtualnum'];
							$list[$i]['goods'][$k]['price']= $v['price'];
							$list[$i]['goods'][$k]['goods_price']= $v['goods_price'];
							$list[$i]['goods'][$k]['marketprice']= $v['marketprice']; 
							$list[$i]['goods'][$k]['addtime']=ftime($v['addtime']);	
							$list[$i]['goods'][$k]['url']= get_url($v['code']);
						}
					}
				}
				//print_r($list);
				$vvvcon='';
				$vvvcon=@arrayeval($list);
				$$tempconname .= '$diy_'.$d['name'].' = '.$vvvcon.';';
				
				break;
			case 'news':
					$look=$ym_idsort[$d['cat_ids']]['look'];
					$recom = json_decode($d['recommends'], true);
					$sql='';
					$order= " sort asc , ";
					foreach ($recom as $k => $v) {
						if($v == '1')
						{
							switch ($k) {
								case 'recommend':
									$sql.=" and c_c=".$v;
									break;
								case 'haspic':
									$sql.=" and c_simg<>'' ";
									break;
								case 'hits':
									$order =" c_count desc , ";
									break;									
								default:	
									break;
							}
						}			
					}
	
					if (intval($look)==1){
						$navarr= get_parents($d['cat_ids'], $ym_idsort);
						$tttemp='$ym_sort';
						foreach ($navarr as $rs){
							$tttemp .="['".$rs['code']."']['son']";
						}
						eval('$thisarr = '.$tttemp.';');
						@$searchid=$look==1 ? $d['cat_ids'].thisallid($thisarr): $d['cat_ids'];	
						$sql= '1 '.$sql.'and c_toid in ('.$searchid.') '.$keycode;
						$sow=$db->fetchall('news', '*',  $sql, $order.' c_time desc', $d['num']);
					}else{
						$sql= '1 '.$sql." and c_toid ='".$d['cat_ids']."' ".$keycode; 
						$sow=$db->fetchall('news', '*',  $sql, $order.' c_time desc', $d['num']);	//print_r($sow);			
					}
		
					$ttteadxxid="";
					$tttemp='';
					$thisarr='';
					$stttssql='';
					$searchid='';
					$subnav='';
					$navarr='';
					$look='';
					$iik=0;
					
					foreach($sow as $v){
						$iik+=1;
						$xxx = array();
						$xxx['id']=$v['id'];
						$xxx['i']=$iik;
						$xxx['code']=$v['c_code'];
						$xxx['title']=ctitle($v['c_title']);
						$xxx['name']=ctitle($v['c_title']);
						$xxx['img']=$v['c_bimg'];
						$xxx['txt']=$v['c_txt'];
						$xxx['simg']=$v['c_simg'];
						$xxx['bimg']=$v['c_bimg'];						
						$xxx['time']=ftime($v['c_time']);		
						$xxx['y']=intval(date("Y",$v['c_time']));
						$xxx['m']=intval(date("m",$v['c_time']));
						$xxx['d']=intval(date("d",$v['c_time']));		
						//$tempaaurl234=$ym_idsort[$v['c_toid']]['dir']==''?'':$ym_idsort[$v['c_toid']]['dir'].'/';
						//$xxx['url']=$tempaaurl234.$ym_idsort[$d['diy_type']]['file'].Condir.$v['c_code'].$conext[$d['diy_type']].$ym_htmlext;
						$xxx['url']= "news".Condir.$v['c_code'].$conext[$d['diy_type']].$ym_htmlext;
						$vvv[]=$xxx;
					}
					$vvvcon=@arrayeval($vvv,'id');
					$$tempconname .= "$".'diy_'.$d['name'].' = '.$vvvcon.';';					
					$vvv=array();
					$vvvcon='';
				break;
			case 'coupon':
				$list = get_coupon_list("and id in(" . $d['ids'] . ")");
				$coupon_list = array();
				$ii=0;
				foreach ($list as $k => $v) {
					$coupon = array();
					$ii++;
					$coupon['i'] = $ii;
					$coupon['id'] = $v['id'];
					$coupon['name'] = $v['name'];
					$coupon['amount'] = $v['amount'];
					$coupon['amount_reached'] = $v['amount_reached'];
					$coupon['num'] = $v['num'];
					$coupon['limit_start'] = $v['limit_start'];
					$coupon['limit_end'] = $v['limit_end'];
					$coupon['limit_num'] = $v['limit_num'];
					$coupon['date_start'] = $v['date_start'];
					$coupon['date_end'] = $v['date_end'];
					$coupon['days'] = $v['days'];
					$coupon['client'] = $v['client'];
					$coupon_list[] = $coupon;
				}
				$$tempconname .= "$"."diy_".$d['name']." = ".@arrayeval($coupon_list).";";
				break;		
			case 'custom':
				$d['body']=str_replace('\'','\\\'',$d['body']);
				$$tempconname .= "$"."diy_".$d['name']." = "."'".$d['body']."';";
				break;	
			default:			
				break;
			}	
	}
	
	//banner
	$gow=$db->fetchall('banner', '*',  array(), 'id asc', '');
	foreach($gow as $g){
		$tempconname='filecon0';
		$fow=$db->fetchall('banner_pic', '*',  array('c_toid' => $g['id']), 'c_order asc,id asc', '');
		$uuu =array();
		foreach($fow as $f){
			$www =array();
			$www['id']=$f['id'];
			$www['code']=$f['c_code'];
			$www['title']=ctitle($f['txt']);
			$www['url']=ctitle($f['c_url']);
			$www['txt']=ctitle($f['c_txt']);
			$www['img']=$f['c_bimg'];
			$www['simg']=$f['c_simg'];
			$www['bimg']=$f['c_bimg'];
			$www['time']=ftime($f['c_time']);
			$uuu[]=$www;
		}
		$uuucon=@arrayeval($uuu,'id');
		$filecon0 .= '$'.$g['c_name'].' = '.$uuucon.';';
	}

    //友情链接
	$fow=$db->fetchall('link', '*',  array('c_is' =>'1'), 'c_order asc,id asc', '');
	$uuu = array();
	foreach($fow as $f){
		$www = array();
		$www['title']=ctitle($f['c_title']);
		$www['url']=ctitle($f['c_url']);
		$www['id']=$f['id'];
		$www['simg']=$f['c_simg'];
		$www['img']=$www['simg'];
		$uuu[]=$www;
	}
	$uuucon=@arrayeval($uuu,'id');
	$filecon0 .= '$link= '.$uuucon.';';

	write_file(cache_data."diy.php", $filecon0);
}

//将数组转为数组形式的字符串
function arrayeval($array, $arrna='',$level = 0) {    
    $space = '';    
    for($i = 0; $i <= $level; $i++) {    
        $space .= "";
        //$space .= "\t";  
    }    
    $evaluate = "Array("; 
    //$evaluate = "Array\n$space(\n";

    $comma = $space;    
    foreach($array as $key => $val) {    
        $key = is_string($key) ? '\''.addcslashes($key, '\'\\').'\'' : $key;    
        $val = !is_array($val) && (!preg_match("/^\-?\d+$/", $val) || strlen($val) > 12) ? '\''.addcslashes($val, '\'\\').'\'' : $val;    
        if(is_array($val)) {
			$arrval=$arrna==""?$key:"'".$val[$arrna]."'";
            $evaluate .= "$comma".$arrval." => ".arrayeval($val,$arrna, $level + 1);    
        } else {
            $evaluate .= "$comma$key=>$val";    
        }    
       $comma = ",";
       //  $comma = ",\n$space";   
    }    
    $evaluate .= ")";
   // $evaluate .= "\n$space)";  
    return $evaluate;    
}  

//将数组转为一维数组形式的字符串   $str="array('a','b','c')";
function array_to_string($array) {        
    $res = "array(";  
    foreach($array as $key => $val) {      
        $res .= "'".$val."',";     
    }    	
    return rtrim($res,","). ")";    
}   

function genTree($items,$id='id',$pid='pid',$son = 'son'){
	$tree = array(); //格式化的树
	$tmpMap = array();  //临时扁平数据
	
	foreach ($items as $item) {
		$tmpMap[$item[$id]] = $item;
	}
	
	foreach ($items as $item) {
		if (isset($tmpMap[$item[$pid]])) {
			$tmpMap[$item[$pid]][$son][] = &$tmpMap[$item[$id]];
		} else {
			$tree[] = &$tmpMap[$item[$id]];
		}
	}
	unset($tmpMap);
	return $tree;
}

// #######################读取文件 #######################
function read_file($filename) 
{
	if ($fp=fopen($filename,"r"))
	{ 
		$cc=fread($fp,filesize($filename)); 
		fclose($fp); 
		return $cc; 
	} 
}
// #######################写入文件 #######################
function write_file($filename,$contents) 
{
	if ($fp=fopen($filename,"w")) 
	{ 
		//fwrite($fp,stripslashes($contents)); 
		$contents=trim($contents)==''?' ':$contents;
		fwrite($fp,$contents); 
		fclose($fp); 
		return true; 
	} else {
		return false; 
	} 
} 

function del_dir($directory){ 
	if(is_dir($directory)){
		//递归删除某个目录下的全部文件
		if($dh=@opendir($directory)){ 
		while ($filename=readdir($dh)){ 
			if($filename!="." && $filename!=".."){ 
			//是文件则删除文件 
				if(is_file($directory."/".$filename)){ 
					unlink($directory."/".$filename); 
				}else{ 
				//非空目录则递归删除子文件夹或文件 
					del_dir($directory."/".$filename); 
					rmdir($directory."/".$filename);
				} 
			} 
		} 
		@closedir($dh); 
		//rmdir($directory); 
		} 
	}else{
		//直接删除指定某个文件
		if(file_exists($directory)){
			unlink($directory);
		}
	}
}

// #######################写入文件 #######################
function html_file($filename,$contents) 
{
	if ($fp=fopen($filename,"w")) 
	{
		$contents=trim($contents)==''?' ':$contents;
		fwrite($fp,stripslashes($contents)); 
		fclose($fp); 
		return true; 
	} else {
		return false; 
	} 
} 

function ftime($dfsdftime){
	return date("Y-m-d",$dfsdftime);
}

function ptitle($contents) 
{
	//$contents=htmlspecialchars(addslashes(trim($contents)));
	return $contents;
} 
function ctitle($ctitlecon){
	return str_replace("'","\'",$ctitlecon);
}
/*// #######################转向提示页 #######################
function message($msg,$url="back",$miao="3"){
	if ($url=="back") {$url="javascript:history.back(1)";}
	if ($miao=="0") {header("Location:$url");}
	$redcon=read_file(".".tpl."admin/showmessage.html");
	$redcon=str_replace("{msg}",$msg,$redcon);
	$redcon=str_replace("{s}",$miao,$redcon);
	$redcon=str_replace("{url}",$url,$redcon);
	$redcon=str_replace("\$images",tpl."admin/\$images",$redcon);
	echo $redcon;
	exit;
}*/
function getip2(){
    static $realip;
    if (isset($_SERVER)){
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")){
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else {
            $realip = getenv("REMOTE_ADDR");
        }
    }

    return $realip;
}

function getip(){
       static $ip = null;
       if($ip)
         return $ip; // 不需要计算第二次.
       $ip=false;
       if($_SERVER['HTTP_VIA']){
           if(!empty($_SERVER["HTTP_CLIENT_IP"])){
                $ip = $_SERVER["HTTP_CLIENT_IP"];
           }else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
                if ($ip){
                    array_unshift($ips, $ip); $ip = '';
                }
                $ipss = count($ips);
                for ($i = 0; $i < $ipss; $i++){
                     if (!preg_match('/^(10|172\.16|192\.168)\./', $ips[$i])){
                               $ip = $ips[$i];
                               break;
                     }
                }
           }
       }else{
            $ip = $_SERVER['REMOTE_ADDR'];
       }
         
       # 更兼容的获取.
        if(!$ip)
        if(!$ip = getenv("REMOTE_ADDR"))
        if (!$ip = getenv("HTTP_CLIENT_IP"))
        if(!$ip = getenv("HTTP_X_FORWARDED_FOR"))
            $ip = '';
       return $ip;
}

//加密
function jcode($txt, $key="8899666457303223445567")
{
	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";//ABCDEFGHIJKLMNOPQRSTUVWXYZ
	$nh = rand(0,61);
	$ch = $chars[$nh];
	$mdKey = md5($key.$ch);
	$mdKey = substr($mdKey,$nh%8, $nh%8+7);
	$txt = base64_encode($txt);
	$tmp = '';
	$i=0;$j=0;$k = 0;
	for ($i=0; $i<strlen($txt); $i++) {
		$k = $k == strlen($mdKey) ? 0 : $k;
		$j = ($nh+strpos($chars,$txt[$i])+ord($mdKey[$k++]))%61;
		$tmp .= $chars[$j];
	}
	return $ch.$tmp;
}

//解密函数
function ucode($txt, $key="8899666457303223445567")
{
	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	$ch = $txt[0]; //if(!$ch){return;}
	$nh = strpos($chars,$ch);
	$mdKey = md5($key.$ch);
	$mdKey = substr($mdKey,$nh%8, $nh%8+7);
	$txt = substr($txt,1);
	$tmp = '';
	$i=0;$j=0; $k = 0;
	for ($i=0; $i<strlen($txt); $i++) {
		$k = $k == strlen($mdKey) ? 0 : $k;
		$j = strpos($chars,$txt[$i])-$nh - ord($mdKey[$k++]);
		while ($j<0) $j+=61;
		$tmp .= $chars[$j];
	}
	return base64_decode($tmp);
}

function tree($arrname="arrayc",$id,$tag,$tag2) {
    global $$arrname,$arrcon;//查询出来的数组
	$thisarr=$$arrname; 
    $result = findChild($thisarr,$id);//取得当前节点下的所有同级子节点
	$tttttxtt[0]='<font style="color:#aaa;"> [隐藏]</font>';
    foreach ($result as $k => $v){
        // 缩进显示节点名称
        $arrcon.= $tag."<li><span><input name=\"c_sort[]\" type=\"text\" id=\"c_sort[]\" value=\"".intval($v['sort'])."\" size=\"2\" maxlength=\"5\">
<input name=\"h_id[]\" type=\"hidden\" id=\"h_id[]\" value=\"".$v['id']."\"><a href=\"/admin.html?do=columns.add&id=".$v['code']."\">添加下级</a> <a class=\"lnk\" target=\"_blank\" href=\"".get_url($v['code'], 'news')."\">查看</a><a href=\"/admin.html?do=columns.edit&id=".$v['code']."\" class=edit>修改</a>" .
($v['is_system']==1 ? '<a href="javascript:void(0);" class="disabled del" title="系统内置分类，不可删除">删除</a>' :" <a href=\"/admin.html?do=columns.delete&id=".$v['code']."\" class=del>删除</a>").
"</span><a href=\"/admin.html?do=".$v['type']."&id=".$v['code']."\">".$v['title'] ."</a>".$tttttxtt[$v['is']]." <span style=\"font-size:11px;color:#bbb;min-width: 50px;\">[".$v['id']."]</span></li>". $tag2;
        //再次调用这个函数显示子节点下的同级子节点
        tree($arrname,$v['id'],$tag."<ul>",$tag2."</ul>");  
   }
	
   return $arrcon;
}

function tree_option($arrname="arrayc",$id,$tag,$inid=0,$arrtype='') {
    global $$arrname,$arrcon;
	$thisarr=$$arrname;
    $result = findChild($thisarr,$id);
	$xxx[$inid]=' selected="selected"';
    foreach ($result as $k => $v){
		if ($arrtype!=''){
			if ($arrtype==$v['type'])$arrcon .= "<option value=".$v['id']."".$xxx[$v['id']].">".$tag.$v['title'] ."</option>";
		}else{
			$arrcon .= "<option value=".$v['id']."".$xxx[$v['id']]." data-file=".($v['file']==$v['code']?"":$v['file']).">".$tag.$v['title'] ."</option>";
		}
        tree_option($arrname,$v['id'],$tag."|___",$inid,$arrtype);  
   }
   return $arrcon;
}

function tree_type($arrname="arrayc",$id,$tag,$inid=0,$arrtype='') {
    global $$arrname,$arrcon;
	$thisarr=$$arrname;
    $result = findChild($thisarr,$id);
	$xxx[$inid]=' selected="selected"';
    foreach ($result as $k => $v){
		if ($arrtype!=''){
			if ($arrtype==$v['type'])$arrcon .= "<option value=".$v['type']."".$xxx[$v['id']].">".$tag.$v['title'] ."</option>";
		}else{
			$arrcon .= "<option value=".$v['type']."".$xxx[$v['id']].">".$tag.$v['title'] ."</option>";
		}
        tree_type($arrname,$v['id'],$tag."|___",$inid,$arrtype);  
   }
   return $arrcon;
}

//取得当前节点下的所有同级子节点
function findChild($arr,$id){
    $childs=array();
     foreach ($arr as $k => $v){
         if($v['pid']== $id){
              $childs[]=$v;
         }
    }
    return $childs;
}

function udownload($outsave,$body){
	global $site_code,$hostname;
	if ($outsave==1){

		
	if(get_magic_quotes_gpc()){
		//$body = stripslashes($body);
	}

		//$body = str_replace($hostname,'#basehost#',$body);
		//$body = preg_replace("/(<a[ \t\r\n]{1,}[^>]*href=[\"']{0,}http:\/\/[^\/]([^>]*)>)|(<\/a>)/isU","",$body);
	    //$body = str_replace('#basehost#',$hostname,$body);

		$img_array = array();
		preg_match_all("/(src|SRC)=[\"|'| ]{0,}(http:\/\/(.*)\.(gif|jpg|jpeg|png))/isU",$body,$img_array);
		$img_array = array_unique($img_array[2]);
		set_time_limit(0);
		$milliSecond = strftime("%H%M%S",time());
		if(!is_dir(upload_news)) @mkdir(upload_news,0777);
		foreach($img_array as $key =>$value)
		{
			$i=$i+1;
			$value = trim($value);
			//$get_file = @file_get_contents($value);
			$vfile=$milliSecond.$key.".".substr($value,-3,3);
			$rndFileName = $imgPath."/".$vfile;
			$fileurl = upload_news.$vfile;
			curl_download($value,$rndFileName); 
			$body = str_replace($value,$fileurl,$body);
			if ($i==1){$_SESSION["outpic"]=$fileurl;}
		}
	}
	return $body;
}

function curl_download($remote,$local){//远程下载
	$ch = curl_init();
	$timeout = 8;
	curl_setopt ($ch, CURLOPT_URL, $remote);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en; rv:1.9.2) Gecko/20100115 Firefox/3.6 GTBDFff GTB7.0');
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$thumb = curl_exec($ch);
	curl_close($ch);
	$fp=@fopen($local,"a");
	fwrite($fp,$thumb);
	fclose($fp);
}

function make_password( $length = 12 )
{
    // 密码字符集，可任意添加你需要的字符
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKL
MNOPQRSTUVWXYZ0123456789~!@#%^&*_+.';

    $password = '';
    for ( $i = 0; $i < $length; $i++ ) 
    {
        // 这里提供两种字符获取方式
        // 第一种是使用 substr 截取$chars中的任意一位字符；
        // 第二种是取字符数组 $chars 的任意元素
        // $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
    }

    return $password;
}

function getImgs($content,$order='ALL'){
	global $site_code;
	$pattern="/<img.*?src=[\'|\"]\/upload\/".$site_code."\/image(.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
	preg_match_all($pattern,$content,$match);
	if(isset($match[1])&&!empty($match[1])){
		if($order==='ALL'){
			return $match[1];
		}
		if(is_numeric($order)&&isset($match[1][$order])){
			return $match[1][$order];
		}
	}
	return '';
}

 
?>