<?php
if (!defined('in_mx')) {exit('Access Denied');}
/*
* 验证码产生程序
* www.qSyz.net
*/
$letter = '';
//获取随机数字
for ($i=0; $i<2; $i++) {
	$letter .= chr(mt_rand(48,57));
}
//获取随机字母
for ($i=0; $i<2; $i++) {
	$letter .= chr(mt_rand(65,90));
}
//重构字符顺序
$strs = str_split($letter);
shuffle ($strs);
$rndstring = "";
while (list ( , $str) = each ($strs)) {
	$rndstring .= $str;
}
//如果支持GD，则绘图

$_SESSION['vcode']=$rndstring;



if(function_exists("imagecreate"))
{
	//向浏览器写入cookie
	setcookie("zjs_ckstr", md5( strtolower($rndstring) ), time()+900,'/');//验证码有效期5分钟    
	$rndcodelen = strlen($rndstring);
	//图片大小
	$im = imagecreate(100,30);
	//$im = imagecreatefromgif("code.gif");
	//字体
	$font_type ="./inc/class/TektonPro-BoldCond.otf";
	//背景颜色
	$backcolor = imagecolorallocate($im,255,255,255);   
	//字体色
	//不支持 imagettftext
	$fontColor = ImageColorAllocate($im, 0,0,0);
	//支持 imagettftext
	$fontColor2 = ImageColorAllocate($im, 0,0,0);
	//阴影
	$fontColor1 = ImageColorAllocate($im, 255,255,25);
	//添加背景杂点
	$pixColor = imagecolorallocate($im, 199, 199, 199);//杂点颜色
	for($j=0; $j<1000; $j++){
		imagesetpixel($im, rand(0,100), rand(0,30), $pixColor);
	}
	//添加背景线
	for($j=0; $j<=3; $j++){
		//背景线颜色
		$lineColor1 = ImageColorAllocate($im, rand(0, 255),rand(0, 255),rand(0, 255));
		//背景线方向大小
		imageline($im,rand(0,40),rand(3,25),rand(40,88),rand(3,25),$lineColor1);
	}    


	$strposs = array();
	//文字
	for($i=0;$i<$rndcodelen;$i++){
	  if(function_exists("imagettftext")){
		  $strposs[$i][0] = $i*16+17;//x轴
		  $strposs[$i][1] = mt_rand(20,23);//y轴
		  imagettftext($im, 5, 5, $strposs[$i][0]+1, $strposs[$i][1]+1, $fontColor1, $font_type, $rndstring[$i]);
	  } else{
		  imagestring($im, 5, $i*16+7, mt_rand(2, 4), $rndstring[$i], $fontColor);
	  }
	}
	//文字
	for($i=0;$i<$rndcodelen;$i++){
	  if(function_exists("imagettftext")){
		  imagettftext($im, 16,5, $strposs[$i][0]-1, $strposs[$i][1]-1, $fontColor2, $font_type, $rndstring[$i]);
	  }
	}
  ob_clean();
  header("Pragma:no-cache\r\n");
  header("Cache-Control:no-cache\r\n");
  header("Expires:0\r\n");
  //输出特定类型的图片格式，优先级为 gif -> jpg
  if(function_exists("imagegif")){
		header("content-type:image/gif\r\n");
	  imagegif($im);
  }else{
		header("content-type:image/jpeg\r\n");
	  imagejpeg($im);
  }
  ImageDestroy($im);
}


exit;

?>