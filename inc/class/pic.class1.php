<?php
/**
 * 
 * 图像处理类
 * @author FC_LAMP
 * @internal功能包含：水印,缩略图
 */
class Img
{
 //图片格式
 private $exts = array ('jpg', 'jpeg', 'gif', 'bmp', 'png' );
 
 /**
  * 
  * 
  * @throws Exception
  */
 public function __construct()
 {
  if (! function_exists ( 'gd_info' ))
  {
   throw new Exception ( '加载GD库失败！' );
  }
 }
 
 /**
  * 
  * 裁剪压缩
  * @param $src_img 图片
  * @param $save_img 生成后的图片
  * @param $option 参数选项，包括： $maxwidth  宽  $maxheight 高
  * array('width'=>xx,'height'=>xxx)
  * @internal
  * 我们一般的压缩图片方法，在图片过长或过宽时生成的图片
  * 都会被“压扁”，针对这个应采用先裁剪后按比例压缩的方法
  */
 public function thumb_img($src_img, $save_img = '', $option)
 {
  
  if (empty ( $option ['width'] ) or empty ( $option ['height'] ))
  {
   return array ('flag' => False, 'msg' => '原图长度与宽度不能小于0' );
  }
  $org_ext = $this->is_img ( $src_img );
  if (! $org_ext ['flag'])
  {
   return $org_ext;
  }
  
  //如果有保存路径，则确定路径是否正确
  if (! empty ( $save_img ))
  {
   $f = $this->check_dir ( $save_img );
   if (! $f ['flag'])
   {
    return $f;
   }
  }
  
  //获取出相应的方法
  $org_funcs = $this->get_img_funcs ( $org_ext ['msg'] );
  
  //获取原大小
  $source = $org_funcs ['create_func'] ( $src_img );
  $src_w = imagesx ( $source );
  $src_h = imagesy ( $source );
  
  //调整原始图像(保持图片原形状裁剪图像)
  $dst_scale = $option ['height'] / $option ['width']; //目标图像长宽比
  $src_scale = $src_h / $src_w; // 原图长宽比
  if ($src_scale >= $dst_scale)
  { // 过高
   $w = intval ( $src_w );
   $h = intval ( $dst_scale * $w );
   
   $x = 0;
   $y = ($src_h - $h) / 3;
  } else
  { // 过宽
   $h = intval ( $src_h );
   $w = intval ( $h / $dst_scale );
   
   $x = ($src_w - $w) / 2;
   $y = 0;
  }
  // 剪裁
  $croped = imagecreatetruecolor ( $w, $h );
  imagecopy ( $croped, $source, 0, 0, $x, $y, $src_w, $src_h );
  // 缩放
  $scale = $option ['width'] / $w;
  $target = imagecreatetruecolor ( $option ['width'], $option ['height'] );
  $final_w = intval ( $w * $scale );
  $final_h = intval ( $h * $scale );
  imagecopyresampled ( $target, $croped, 0, 0, 0, 0, $final_w, $final_h, $w, $h );
  imagedestroy ( $croped );
  
  //输出(保存)图片
  if (! empty ( $save_img ))
  {
   
	if ($org_funcs ['save_func'] == 'imagejpeg'){
		$org_funcs ['save_func'] ( $target, $save_img ,93);  //imagejpeg
	}else{
		$org_funcs ['save_func'] ( $target, $save_img );  //imagejpeg

	}
		
	
  } else
  {
   header ( $org_funcs ['header'] );
   $org_funcs ['save_func'] ( $target );
  }
  imagedestroy ( $target );
  return array ('flag' => True, 'msg' => '' );
 }
 
 /**
  * 
  * 等比例缩放图像
  * @param $src_img 原图片
  * @param $save_img 需要保存的地方
  * @param $option 参数设置 array('width'=>xx,'height'=>xxx)
  * 
  */
 function resize_image($src_img, $save_img = '', $option)
 {
  $org_ext = $this->is_img ( $src_img );
  if (! $org_ext ['flag'])
  {
   return $org_ext;
  }
  
  //如果有保存路径，则确定路径是否正确
  if (! empty ( $save_img ))
  {
   $f = $this->check_dir ( $save_img );
   if (! $f ['flag'])
   {
    return $f;
   }
  }
  
  //获取出相应的方法
  $org_funcs = $this->get_img_funcs ( $org_ext ['msg'] );
  
  //获取原大小
  $source = $org_funcs ['create_func'] ( $src_img );
  $src_w = imagesx ( $source );
  $src_h = imagesy ( $source );
  
  if (($option ['width'] && $src_w > $option ['width']) || ($option ['height'] && $src_h > $option ['height']))
  {
   if ($option ['width'] && $src_w > $option ['width'])
   {
    $widthratio = $option ['width'] / $src_w;
    $resizewidth_tag = true;
   }
   
   if ($option ['height'] && $src_h > $option ['height'])
   {
    $heightratio = $option ['height'] / $src_h;
    $resizeheight_tag = true;
   }
   
   if ($resizewidth_tag && $resizeheight_tag)
   {
    if ($widthratio < $heightratio)
     $ratio = $widthratio;
    else
     $ratio = $heightratio;
   }
   
   if ($resizewidth_tag && ! $resizeheight_tag)
    $ratio = $widthratio;
   if ($resizeheight_tag && ! $resizewidth_tag)
    $ratio = $heightratio;
   
   $newwidth = $src_w * $ratio;
   $newheight = $src_h * $ratio;
   
   if (function_exists ( "imagecopyresampled" ))
   {
    $newim = imagecreatetruecolor ( $newwidth, $newheight );
    imagecopyresampled ( $newim, $source, 0, 0, 0, 0, $newwidth, $newheight, $src_w, $src_h );
   } else
   {
    $newim = imagecreate ( $newwidth, $newheight );
    imagecopyresized ( $newim, $source, 0, 0, 0, 0, $newwidth, $newheight, $src_w, $src_h );
   }
  }
  //输出(保存)图片
  if (! empty ( $save_img ))
  {
   
   //$org_funcs ['save_func'] ( $newim, $save_img );

	if ($org_funcs ['save_func'] == 'imagejpeg'){
		$org_funcs ['save_func'] ( $newim, $save_img ,93);  //imagejpeg
	}else{
		$org_funcs ['save_func'] ( $newim, $save_img );  //imagejpeg

	}

  } else
  {
   header ( $org_funcs ['header'] );
   $org_funcs ['save_func'] ( $newim );
  }
  imagedestroy ( $newim );
  return array ('flag' => True, 'msg' => '' );
 }
 
 /**
  * 
  * 生成水印图片
  * @param  $org_img 原图像
  * @param  $mark_img 水印标记图像
  * @param  $save_img 当其目录不存在时，会试着创建目录
  * @param array $option 为水印的一些基本设置包含：
  * x:水印的水平位置,默认为减去水印图宽度后的值
  * y:水印的垂直位置,默认为减去水印图高度后的值
  * alpha:alpha值(控制透明度),默认为50
  */
 public function water_mark($org_img, $mark_img, $weizhi = 'cc', $option = array())
 {
	$dst_info = getimagesize($org_img); 
	switch ($dst_info[2])
	{
	case 1:
	$dst_im =imagecreatefromgif($org_img);break;
	case 2:
	$dst_im =imagecreatefromjpeg($org_img);break;
	case 3:
	$dst_im =imagecreatefrompng($org_img);break;
	case 6:
	$dst_im =imagecreatefromwbmp($org_img);break;
	default:
	die("不支持的文件类型1");exit;
	}
	$src_info = getimagesize($mark_img);
	switch ($src_info[2])
	{
	case 1:
	$src_im =imagecreatefromgif($mark_img);break;
	case 2:
	$src_im =imagecreatefromjpeg($mark_img);break;
	case 3:
	$src_im =imagecreatefrompng($mark_img);break;
	case 6:
	$src_im =imagecreatefromwbmp($mark_img);break;
	default:
	die("不支持的文件类型1");exit;
	}

	switch ($weizhi){
		case 'tl':
			$wx=20;
			$wy=20;

		break;


		case 'tc':
			$wx=($dst_info[0]/2)-($src_info[0]/2);
			$wy=20;
		break;


		case 'tr':
			$wx=$dst_info[0]-$src_info[0]-20;
			$wy=20;
		break;

		case 'cl':
			$wx=20;
			$wy=($dst_info[1]/2)-($src_info[1]/2);
		break;

		case 'cc':
			$wx=($dst_info[0]/2)-($src_info[0]/2);
			$wy=($dst_info[1]/2)-($src_info[1]/2);
		break;

		case 'cr':
			$wx=$dst_info[0]-$src_info[0]-20;
			$wy=($dst_info[1]/2)-($src_info[1]/2);
		break;


		case 'dl':
			$wx=20;
			$wy=$dst_info[1]-$src_info[1]-20;
		break;

		case 'dc':
			$wx=($dst_info[0]/2)-($src_info[0]/2);
			$wy=$dst_info[1]-$src_info[1]-20;
		break;

		case 'dr':
			$wx=$dst_info[0]-$src_info[0]-20;
			$wy=$dst_info[1]-$src_info[1]-20;
		break;


		default:

		break;
	}
	


	imagecopy($dst_im,$src_im,$wx,$wy,0,0,$src_info[0],$src_info[1]);


	switch ($dst_info[2]){ 
	case 1: 
	imagegif($dst_im,$org_img);break; 
	case 2: 
	imagejpeg($dst_im,$org_img,93);break; 
	case 3: 
	imagepng($dst_im,$org_img);break; 
	case 6: 
	imagewbmp($dst_im,$org_img);break; 
	default: 
	die("不支持的文件类型2");exit; 
	} 
	imagedestroy($dst_im); 
	imagedestroy($src_im);
}
 
 /**
  * 
  * 检查图片
  * @param unknown_type $img_path
  * @return array('flag'=>true/false,'msg'=>ext/错误信息) 
  */
 private function is_img($img_path)
 {
  if (! file_exists ( $img_path ))
  {
   return array ('flag' => False, 'msg' => "加载图片 $img_path 失败！" );
  }
  $ext = explode ( '.', $img_path );
  $ext = strtolower ( end ( $ext ) );
  if (! in_array ( $ext, $this->exts ))
  {
   return array ('flag' => False, 'msg' => "图片 $img_path 格式不正确！" );
  }
  return array ('flag' => True, 'msg' => $ext );
 }
 
 /**
  * 
  * 返回正确的图片函数
  * @param unknown_type $ext
  */
 private function get_img_funcs($ext)
 {
  //选择
  switch ($ext)
  {
   case 'jpg' :
    $header = 'Content-Type:image/jpeg';
    $createfunc = 'imagecreatefromjpeg';
    $savefunc = 'imagejpeg';
	$zliang = '92';
    break;
   case 'jpeg' :
    $header = 'Content-Type:image/jpeg';
    $createfunc = 'imagecreatefromjpeg';
    $savefunc = 'imagejpeg';
	$zliang = '92';
    break;
   case 'gif' :
    $header = 'Content-Type:image/gif';
    $createfunc = 'imagecreatefromgif';
    $savefunc = 'imagegif';
	$zliang = '';
    break;
   case 'bmp' :
    $header = 'Content-Type:image/bmp';
    $createfunc = 'imagecreatefrombmp';
    $savefunc = 'imagebmp';
	$zliang = '';
    break;
   default :
    $header = 'Content-Type:image/png';
    $createfunc = 'imagecreatefrompng';
    $savefunc = 'imagepng';
	$zliang = '';
  }
  return array ('save_func' => $savefunc, 'create_func' => $createfunc, 'header' => $header , 'z_liang' => $zliang );
 }
 
 /**
  * 
  * 检查并试着创建目录
  * @param $save_img
  */
 private function check_dir($save_img)
 {
  $dir = dirname ( $save_img );
  if (! is_dir ( $dir ))
  {
   if (! mkdir ( $dir, 0777, true ))
   {
    return array ('flag' => False, 'msg' => "图片保存目录 $dir 无法创建！" );
   }
  }
  return array ('flag' => True, 'msg' => '' );
 }
}

if (! empty ( $_FILES ['test'] ['tmp_name'] ))
{
 //例子
 $img = new Img ();
 //原图
 $name = explode ( '.', $_FILES ['test'] ['name'] );
 $org_img = 'D:/test.' . end ( $name );
 move_uploaded_file ( $_FILES ['test'] ['tmp_name'], $org_img );
 $option = array ('width' => $_POST ['width'], 'height' => $_POST ['height'] );
 if ($_POST ['type'] == 1)
 {
  $s = $img->resize_image ( $org_img, '', $option );
 } else
 {
  $img->thumb_img ( $org_img, '', $option );
 }
 unlink ( $org_img );
}