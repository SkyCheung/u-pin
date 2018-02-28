<?php
/* +-------------------------------------------------------------+
* | Copyright (c) 2008-2009 Diqiye.Com All rights reserved.        
* +-------------------------------------------------------------+
* | Info : 图像处理类
* +-------------------------------------------------------------+

// 原始图片
$img1 = './image/test.jpg';
// 水印后的图片
$img2 = './image/test_new.jpg';
// 水印
$water = './image/water.gif';
$img = new image();
// 等比缩放
$img->param($img1)->thumb('./image/test_0.jpg', 200,200,0);
// center center 裁剪
$img->param($img1)->thumb('./image/test_1.jpg', 200,200,1);
// top left 裁剪
$img->param($img1)->thumb('./image/test_2.jpg', 200,200,2);
// 右下角添加水印
$img->param($img1)->water($img2,$water,9);

*/
class image {
        // 当前图片
        protected $img;
        // 图像types 对应表
        protected $types = array(
                        1 => 'gif',
                        2 => 'jpg',
                        3 => 'png',
                        6 => 'bmp'
                    );
        
        // image
        public function __construct($img=''){
                !$img && $this->param($img);
        }
        
        // Info
        public function param($img){
                $this->img = $img;
                return $this;
        }
        
        // imageInfo
        public function getImageInfo($img){
                $info = @getimagesize($img);
                if(isset($this->types[$info[2]])){
                        $info['ext'] = $info['type'] = $this->types[$info[2]];
                } else{
                        $info['ext'] = $info['type'] = 'jpg';
                }
                $info['type'] == 'jpg' && $info['type'] = 'jpeg';
                $info['size'] = @filesize($img);
                return $info;
        }
        
      //生成缩略图 (新图地址, 宽, 高, 裁剪方式, 允许放大)
      public function thumb($filename,$new_w=200,$new_h=200,$cut=0,$big=0){
        // 获取原图信息   
        $info  = $this->getImageInfo($this->img);
        if(!empty($info[0])) {
            $old_w  = $info[0];
            $old_h  = $info[1];
            $type   = $info['type'];
            $ext    = $info['ext'];
            unset($info);
            // 如果原图比缩略图小 并且不允许放大
            if($old_w < $new_h && $old_h < $new_w && !$big){
                   // return false;
            }
            // 裁剪图片
            if($cut == 0){ // 等比列
                    $scale = min($new_w/$old_w, $new_h/$old_h); // 计算缩放比例
                    $width  = (int)($old_w*$scale); // 缩略图尺寸
                    $height = (int)($old_h*$scale);
                    $start_w = $start_h = 0;
                    $end_w = $old_w;
                    $end_h = $old_h;
            } 
            elseif($cut == 1){ // center center 裁剪
                        $scale1 = round($new_w/$new_h,2);
                        $scale2 = round($old_w/$old_h,2);
                        if($scale1 > $scale2){
                                $end_h = round($old_w/$scale1,2);
                                $start_h = ($old_h-$end_h)/2;
                                $start_w  = 0;
                                $end_w    = $old_w;
                        } else{
                                $end_w  = round($old_h*$scale1,2);
                                $start_w  = ($old_w-$end_w)/2;
                                $start_h = 0;
                                $end_h   = $old_h;
                        }
                    $width = $new_w;
                    $height= $new_h; 
            } 
            elseif($cut == 2){ // left top 裁剪           
                        $scale1 = round($new_w/$new_h,2);
                    $scale2 = round($old_w/$old_h,2);
                    if($scale1 > $scale2){
                            $end_h = round($old_w/$scale1,2);
                            $end_w = $old_w;
                    } else{
                            $end_w = round($old_h*$scale1,2);
                            $end_h = $old_h;
                    }
                    $start_w = 0;
                    $start_h = 0;
                    $width = $new_w;
                    $height= $new_h; 
            }
			
            // 载入原图
            $createFun  = 'ImageCreateFrom'.$type;
            $oldimg     = $createFun($this->img); 
            // 创建缩略图
            if($type!='gif' && function_exists('imagecreatetruecolor')){
                $newimg = imagecreatetruecolor($width, $height); 
            } else{
                $newimg = imagecreate($width, $height); 
            }
			//上传PNG底色不为黑色
			$color=imagecolorallocate($newimg,255,255,255);
			imagecolortransparent($newimg,$color);
			imagefill($newimg,0,0,$color);	
			
            // 复制图片
            if(function_exists("ImageCopyResampled")){
                    ImageCopyResampled($newimg, $oldimg, 0, 0, $start_w, $start_h, $width, $height, $end_w,$end_h); 
            } else{
                ImageCopyResized($newimg, $oldimg, 0, 0, $start_w, $start_h, $width, $height, $end_w,$end_h);
            } 

            // 对jpeg图形设置隔行扫描
            $type == 'jpeg' && imageinterlace($newimg,1);
            // 生成图片
            $imageFun = 'image'.$type;
			if ($type=='jpg' || $type=='jpeg'){
				!@$imageFun($newimg,$filename,95) && die('上传失败，检查目录是否存在并且可写?');
			}else{
				!@$imageFun($newimg,$filename) && die('上传失败，检查目录是否存在并且可写?');
			}
            ImageDestroy($newimg);
            ImageDestroy($oldimg);
            return $filename;
        }
        return false;
    }
    

    public function water_mark($org_img, $mark_img, $weizhi = '5') {
    	// 加载水印图片
    	$dst_info = getimagesize($org_img);
    	switch ($dst_info[2]) {
    	case 1:
    		$dst_im = imagecreatefromgif($org_img);
    		break;
    	case 2:
    		$dst_im = imagecreatefromjpeg($org_img);
    		break;
    	case 3:
    		$dst_im = imagecreatefrompng($org_img);
    		break;
    	case 6:
    		$dst_im = imagecreatefromwbmp($org_img);
    		break;
    	default:
    		//die("不支持的文件类型1");
    		//exit;
    		break;
    	}
    	$src_info = getimagesize($mark_img);
    	switch ($src_info[2]) {
    	case 1:
    		$src_im = imagecreatefromgif($mark_img);
    		break;
    	case 2:
    		$src_im = imagecreatefromjpeg($mark_img);
    		break;
    	case 3:
    		$src_im = imagecreatefrompng($mark_img);
    		break;
    	case 6:
    		$src_im = imagecreatefromwbmp($mark_img);
    		break;
    	default:
    		die("不支持的文件类型1");
    		exit;
    	}

    	switch ($weizhi) {
    	case '1':
    		$wx = 20;
    		$wy = 20;

    		break;


    	case '2':
    		$wx = ($dst_info[0] / 2) - ($src_info[0] / 2);
    		$wy = 20;
    		break;


    	case '3':
    		$wx = $dst_info[0] - $src_info[0] - 20;
    		$wy = 20;
    		break;

    	case '4':
    		$wx = 20;
    		$wy = ($dst_info[1] / 2) - ($src_info[1] / 2);
    		break;

    	case '5':
    		$wx = ($dst_info[0] / 2) - ($src_info[0] / 2);
    		$wy = ($dst_info[1] / 2) - ($src_info[1] / 2);
    		break;

    	case '6':
    		$wx = $dst_info[0] - $src_info[0] - 20;
    		$wy = ($dst_info[1] / 2) - ($src_info[1] / 2);
    		break;


    	case '7':
    		$wx = 20;
    		$wy = $dst_info[1] - $src_info[1] - 20;
    		break;

    	case '8':
    		$wx = ($dst_info[0] / 2) - ($src_info[0] / 2);
    		$wy = $dst_info[1] - $src_info[1] - 20;
    		break;

    	case '9':
    		$wx = $dst_info[0] - $src_info[0] - 20;
    		$wy = $dst_info[1] - $src_info[1] - 20;
    		break;


    	default:

    		break;
    	}



    	imagecopy($dst_im, $src_im, $wx, $wy, 0, 0, $src_info[0], $src_info[1]);


    	switch ($dst_info[2]) {
    	case 1:
    		imagegif($dst_im, $org_img);
    		break;
    	case 2:
    		imagejpeg($dst_im, $org_img, 95);
    		break;
    	case 3:
    		imagepng($dst_im, $org_img);
    		break;
    	case 6:
    		imagewbmp($dst_im, $org_img);
    		break;
    	default:
			break;
    		//die("不支持的文件类型2");
    		//exit;
    	}
    	imagedestroy($dst_im);
    	imagedestroy($src_im);


    }
}
?>