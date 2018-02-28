<?php
if (!defined('in_mx')) {exit('Access Denied');}

$txt=$txt==''?'如因操作不当，导致网站内容的缩略图大小不一致，可使用本操作。':$txt;


$xx33['news']='文章模块工作';
$xx33['goods']='商品模块工作';


$iswms=intval($_GET["iswms"]);

if ($type!='' && $txt!='ok'){
	if (intval($allnum)==0){
		$allnum=$db->rowcount(''.$type,'');
	}
	$pagenum=5;
	$nownum=intval($_GET["nownum"]);
	$xow=$db->fetchall(''.$type,'*', '',$type=='goods'? 'goods_id': 'id'.' desc', $nownum .','.$pagenum);
	$targetFolder = $type=='goods' ? upload_goods: upload_news;
	require("./inc/class/pic.class.php");
			
		if($type =='goods')
		{
			foreach($xow as $rs){
				$specs = json_decode($rs['specs'], true);
				$tmp_img='';
				if(count($specs['spec_val'])>0)
				{
					$spec_val= $specs['spec_val'];
					foreach ($spec_val as $k => $v) {
						$imgs= $v['imgs']; 
						if(count($imgs)==0){continue; }
						foreach ($imgs as $key => $val) {
							$tmp_img = $val['img'];
							if($tmp_img=='' || !file_exists($tmp_img))
							{
								continue;
							} 
							$smFile = get_smallName($tmp_img); 															 
							$img = new image();				
							$img->param($tmp_img)->thumb($smFile, $ym_goods_width, $ym_goods_height, intval($ym_thumb_type));	
						}						
					}												
				}
				else {										
					$tmp_img = $rs['img'];
					if($tmp_img=='' || !file_exists($tmp_img))
					{
						continue;
					} 
					$smFile = get_smallName($tmp_img); 								
					$img = new image();				
					$img->param($tmp_img)->thumb($smFile, $ym_goods_width, $ym_goods_height, intval($ym_thumb_type));
				}
				$doingtit .= $rs['name']." "; 																	
			}
		}
		else {
			foreach($xow as $rs){
				if($rs['c_bimg']=='')
				{
					continue;
				}
				$c_iswm=$rs['c_iswm'];
	
				$bigFile = rtrim($targetFolder,'/') . '/' .$rs['c_bimg'];
				if(!file_exists($bigFile))
				{
					continue;
				}
				if ($c_iswm==1){
					$bigFile = str_replace('_b','_ox',$bigFile);
				}
				$smFile = rtrim($targetFolder,'/') . '/' .$rs['c_simg']; 
				
				$doingtit .= $rs['c_title']." ";
				$c_toid=$rs['c_toid'];
				$width_ss=intval($ym_idsort[intval($c_toid)]['width'])==0 ? (intval($ym_news_width)==0? 200: $ym_news_width) : intval($ym_idsort[intval($c_toid)]['width']);
				$height_ss=intval($ym_idsort[intval($c_toid)]['height'])==0 ? (intval($ym_news_height)==0? 160: $ym_news_height) : intval($ym_idsort[intval($c_toid)]['height']);
				
				$img = new image();		
				$xxm= $ym_idsort[intval($c_toid)]['smtype']==''? $xxm:$ym_idsort[intval($c_toid)]['smtype'];			
				$img->param($bigFile)->thumb($smFile,$width_ss,$height_ss,intval($xxm));
			}
		}	

	$nextnum=$nownum+5;
	$txt= "已完成：".intval($nextnum)." / ".$allnum."  &nbsp; / ".$doingtit;

	if ($allnum>$nextnum){
		$tttss= '<meta http-equiv="refresh" content="1; url=/admin.html?do=batch.pic.sm&xxm='.intval($xxm).'&allnum='.$allnum.'&nownum='.$nextnum.'&type='.$type.'&iswms='.$iswms.'">';
	}else{
		$tttss= '<meta http-equiv="refresh" content="1; url=/admin.html?do=batch.pic.sm&xxm='.intval($xxm).'allnum='.$allnum.'&txt='.$xx33[$type].'处理完成。">';
	}

}




?>