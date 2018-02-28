<?php if (!defined('in_mx')) {exit('Access Denied');}

/*云EC电商系统开放平台 数据接口  YunecAPI-SDK*/

require("./inc/lib/yunec_API.php");

$ym_is_api=true;

if($act)
{
	$res = array('err' => '', 'res' => '', 'data' => array()); 
	$api = new Yunec_API();
	
	if(method_exists($api,$act))
	{
		$api->$act();
	} 
	else{
		$res['err'] = $act."接口未定义";
		die(json_encode_yec($res));
	}
	
		 
 	
	//首页接口
	if($act=="index"){
		dbc();
		$brand = $db->queryall("SELECT * FROM " . $db->table("brand") ." WHERE 1");
		if(is_array($mindextop)&&(!empty($mindextop))){
			foreach($mindextop as $v){$row["topBanner"] = $ym_url.$v['img'];};
		}else{
			$row["topBanner"] = "";
		};
		if(is_array($mindexbanner)&&(!empty($mindexbanner))){
			foreach($mindexbanner as $v){
				$row["adImg"][] = $ym_url.$v['img'];
			};
		};
		if(is_array($mindexsale)&&(!empty($mindexsale))){
			foreach($mindexsale as $v){
				$row["saleImg"] = $ym_url.$v['img'];
				$row["saleTitle"] = "特卖专场";
				$row["saleNotice"] = $v[txt];
				$row["saleBtn"] = "马上去抢购";
			};
		}else{
			$row["saleImg"] = "";$row["saleTitle"] = "特卖专场";$row["saleNotice"] = "";$row["saleBtn"] = "马上去抢购";
		};
		if(is_array($diy_timespike)&&(!empty($diy_timespike))){
			$brand = $db->queryall("SELECT a.* FROM " . $db->table("brand") ." a WHERE 1");
			foreach($diy_timespike as $v){
				if($v[status]&&($v[start_time]<time())&&($v[end_time]>time())){
					foreach($v[goods] as $k => $y){
						if($v['img']){$row["saleList"][$k]['goods_img']=$ym_url.$y['img'];}else{$row["saleList"][$k]['goods_img']="";};
						$row["saleList"][$k][brandName]=$y[sub_name];
						$row["saleList"][$k][goodsCate]=$y[cat_name];
						$row["saleList"][$k][goodsName]=$y[name];
						$row["saleList"][$k][saleTitle]="特卖价";
						if($v[type]=="1"){
							$price = $v[val];
						}else if($v[type]=="2"){
							$price = $y[price] - $v[val];
						}else{
							$price = $y[price] * $v[val];
						};
						$row["saleList"][$k][price]=$price;
						$row["saleList"][$k][originalPrice]=$y[price];
						$row["saleList"][$k][goodId]=$y['goods_id'];
					}
				}
			};
		}else{
			$row["saleImg"] = "";$row["saleTitle"] = "特卖专场";$row["saleNotice"] = "";$row["saleBtn"] = "马上去抢购";
		};
		$i=0;
		if(is_array($diy_newpro)&&(!empty($diy_newpro))){
			foreach($diy_newpro as $y){
				foreach($y[goods] as $v){
					if($v['img']){
						$row["newList"][$i]['goods_img']=$ym_url.$v['img'];
					}else{
						$row["newList"][$i]['goods_img']="";
					};
					$row["newList"][$i][goodsCate]=$y[cat_name];
					$row["newList"][$i][goodsName]=$v[name];
					$row["newList"][$i]['goods_id']=$v['goods_id'];
					$i++;
				}
			};
		}else{
			$row["newList"][$i]['goods_img']="";
			$row["newList"][$i][goodsCate]="";
			$row["newList"][$i][goodsName]="";
			$row["newList"][$i]['goods_id']="";
		};
		$i=0;
		if(is_array($diy_best)&&(!empty($diy_best))){
			foreach($diy_best as $y){
				foreach($y[goods] as $v){
					if($y['img']){
						$row["brandList"][$i][brandImg]=$ym_url.$y['img'];
					}else{
						$row["brandList"][$i][brandImg]="";
					};
					if($v['img']){
						$row["brandList"][$i]['goods_img']=$ym_url.$v['img'];
					}else{
						$row["brandList"][$i]['goods_img']="";
					};
					$row["brandList"][$i][likeImg]="";
					$row["brandList"][$i][likedImg]="";
					$row["brandList"][$i][cartImg]="";
					$row["brandList"][$i][brandName]=$y[name];
					$row["brandList"][$i][goodsName]=$v[name];
					$row["brandList"][$i][price]=$v[price];
					$row["brandList"][$i]['goods_id']=$v['goods_id'];$i++;
				}
			};
		}else{
			$row["brandList"][$i][brandImg]="";
			$row["brandList"][$i]['goods_img']="";
			$row["brandList"][$i][likeImg]="";
			$row["brandList"][$i][likedImg]="";
			$row["brandList"][$i][cartImg]="";
			$row["brandList"][$i][brandName]="";
			$row["brandList"][$i][goodsName]="";
			$row["brandList"][$i][price]="";
			$row["brandList"][$i]['goods_id']="";
		};
		$i=0;
		if(is_array($diy_itemrem)&&(!empty($diy_itemrem))){
			foreach($diy_itemrem as $y){
				foreach($y[goods] as $v){
					if($v['img']){
						$row["recommendList"][$i]['goods_img']=$ym_url.$v['img'];
					}else{
						$row["recommendList"][$i]['goods_img']="";
					};
					$row["recommendList"][$i][brandName]=$y[name];
					$row["recommendList"][$i][goodsName]=$v[name];
					$row["recommendList"][$i]['goods_id']=$v['goods_id'];
					$i++;
				}
			};
		}else{
			$row["recommendList"][$i]['goods_img']="";
			$row["recommendList"][$i][brandName]="";
			$row["recommendList"][$i][goodsName]="";
			$row["recommendList"][$i]['goods_id']="";
		};
		die(json_encode_yec($row));
	}

	//特卖接口
	if($act=="sale"){
		if(is_array($salebanner)&&(!empty($salebanner))){
			foreach($salebanner as $x){
				if((!$i)&&($i=1)){
					$row['sale']['topBanner']=$ym_url.$x['img'];
				}
			};
		}else{
			$row['sale']['topBanner']="";
		}
		if(is_array($diy_timespike)&&(!empty($diy_timespike))){
			foreach($diy_timespike as $v){
				if($v['status']&&($v['start_time']<time())&&($v['end_time']>time())){
					foreach($v['goods'] as $k => $y){
						$row['sale']["saleList"][$k]['goods_img']=$ym_url.$y['img'];
						$row['sale']["saleList"][$k]['brandName']=$y['cat_name'];
						$row['sale']["saleList"][$k]['goodsName']=$y['name'];
						if($v['type']=="1"){
							$price = $v['val'];
						}else if($v['type']=="2"){
							$price = $y['price'] - $v['val'];
						}else{
							$price = $y['price'] * $v['val'];
						};
						$row['sale']["saleList"][$k]['price']=$price;
						$row['sale']["saleList"][$k]['goods_id']=$y['goods_id'];
					}
				}
			};
		}else{
			$row['sale']["saleList"][0]['goods_img']="";
			$row['sale']["saleList"][0]['brandName']="";
			$row['sale']["saleList"][0]['goodsName']="";
			$row['sale']["saleList"][0]['price']="";
			$row['sale']["saleList"][0]['goods_id']="";
		}
		die(json_encode_yec($row));
	}

 
	
}
else {
	$res['err'] = "欢迎使用云EC API接口";
	die(json_encode_yec($res));
}

?>