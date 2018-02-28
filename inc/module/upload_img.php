<?php 
if(!defined('in_mx')) exit('Access Denied');
/**图片上传
 */

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$allow_ext=array('gif','jpg','jpeg','bmp','png');
$res = array('err' => '', 'res' => '', 'data' => array()); //回调参数
if(intval($_SESSION["lg_id"]) ==0 && $ym_uid==0) //需要登录
{	
	$ym_uid = check_login(1);
	if($ym_uid==0)
	{
		$res['url'] = 'login.html';
		die(json_encode_yec($res));
	}
}	

// Support CORS
// header("Access-Control-Allow-Origin: *");
// other CORS headers if any...
if($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit; // finish preflight CORS requests here
}

if ( !empty($_REQUEST[ 'debug' ]) ) {
    $random = rand(0, intval($_REQUEST[ 'debug' ]) );
    if ( $random === 0 ) {
        header("HTTP/1.0 500 Internal Server Error");
        exit;
    }
}

// 5 minutes execution time
@set_time_limit(5 * 60);

// Uncomment this one to fake upload time
// usleep(5000);

// Settings
// $targetDir = ini_get("upload_tmp_dir") . '/' . "plupload";
/*$targetDir = 'upload_tmp';
$uploadDir = 'upload';*/
 

if(isset($act))
{
	if($act == 'del_img') //删除图片
	{
		if(!isset($id) || trim($id)=='')
		{
			$res['err']="获取不到图片地址";
			die(json_encode_yec($res));
		}  
 
		if(del_file($id)==false || del_file(get_smallName($id))==false)
		{
			$res['err']= '删除失败';
			die(json_encode_yec($res));
		}
	}
 	die(json_encode_yec($res));	
}	
else {
	$uploadDir ='common';//默认存储目录
	//自定上传目录
	if(isset($_REQUEST["uploadDir"])&&trim($_REQUEST["uploadDir"])!='')
	{
		if(isLetter(trim($_REQUEST["uploadDir"]))==false)
		{
			$res['err']="上传目录格式不正确.";die(json_encode_yec($res));
		}
		$uploadDir = trim($_REQUEST["uploadDir"]);	
	}
	$con_type =array('comment','avatar','service','chat');
	if(in_array($uploadDir, $con_type)==false)
	{
		$res['err']="上传目录不正确.";die(json_encode_yec($res));
	}
	$tmp_uploadDir=$uploadDir;
	$targetDir = upload.'tmp'; // Relative to the root
	$uploadDir = upload_img.$uploadDir.'/'.date("Ymd"); // Relative to the root
	
	$cleanupTargetDir = true; // Remove old files
	$maxFileAge = 5 * 3600; // Temp file age in seconds
	
	// Create target dir
	if (!file_exists($targetDir)) {
	    @mdir($targetDir);
	}
	
	// Create target dir
	if (!file_exists($uploadDir)) {
	    @mdir($uploadDir);
	}
	
	// Get a file name
	
	if (isset($_REQUEST["filename"]) && $_REQUEST["filename"]!='' && is_enAndnum($_REQUEST["filename"],10,32) && strlen($_REQUEST["filename"])<=32) {
		dbc();		
		$oldname ='';
		switch ($uploadDir) {
			case 'avatar':
				$user=get_user($ym_uid);
				$oldname = $user['img'];
				break;			
			default:				
				break;
		}
		if($oldname!='' && $oldname != $_REQUEST["filename"])
		{
			$fileName = get_newName();
		}
	    else {
	    	$fileName = $_REQUEST["filename"];
	    }
	}
	else {
	    $fileName = get_newName();
	}

	$fileInfo = pathinfo($_FILES['file']['name']); 
	$ext = strtolower($fileInfo["extension"]); //扩展名
	if(in_array($ext, $allow_ext)==false && !isset($is_h5))
	{
		$res['err']="图片格式仅支持 gif,jpg,jpeg,bmp,png.";die(json_encode_yec($res));
	}
	 
	$filePath = $targetDir . '/' . $fileName.'.'.$ext;
	$uploadPath = $uploadDir . '/' . $fileName.'.'.$ext;
	
	//h5上传
	if(isset($is_h5) && $is_h5==1){
		require("./inc/class/pic.class.php");
		$img = $_POST['img'];
		$img = str_replace(' ', '+', trim($img));//post的数据，加号会被替换为空格
		if(!empty($img)){		  
		    if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $img)){
		    	$file_name = $uploadPath . "jpg";
		        $base64_body = substr(strstr($img,','),1);
				$image = imagecreatefromstring(base64_decode($base64_body)); 
				imagejpeg($image, $file_name);
				imagedestroy($image); 
				
				global $ym_url;
		        $res['data'] = array('img'=>$ym_url.ltrim($file_name,"./"), 'thumb'=>$ym_url.ltrim($file_name,"./"));
		    }
            else {
				$res['err']="图片内容不正确";
            }
			die(json_encode_yec($res));
		}
		$res['err']="请选择图片";die(json_encode_yec($res));		 
	}
	
	// Chunking might be enabled
	$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
	$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;
	
	// Remove old temp files
	if ($cleanupTargetDir) {
	    if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
	    	$res['err']="打开临时目录失败.";die(json_encode_yec($res));
	    }
	
	    while (($file = readdir($dir)) !== false) {
	        $tmpfilePath = $targetDir . '/' . $file;
	
	        // If temp file is current file proceed to the next
	        if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
	            continue;
	        }
	
	        // Remove temp file if it is older than the max age and is not the current file
	        if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpfilePath) < time() - $maxFileAge)) {
	            @unlink($tmpfilePath);
	        }
	    }
	    closedir($dir);
	}
	
	// Open temp file
	if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
		$res['err']="上传失败,Failed to open output stream";die(json_encode_yec($res));
	}
	
	if (!empty($_FILES)) {
	    if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
	        die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
	    }
	
	    // Read binary input stream and append it to temp file
	    if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
	        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	    }
	} else {
	    if (!$in = @fopen("php://input", "rb")) {
	    	$res['err']="上传失败,Failed to open input stream";die(json_encode_yec($res));
	    }
	}
	
	while ($buff = fread($in, 4096)) {
	    fwrite($out, $buff);
	}
	
	@fclose($out);
	@fclose($in);
	
	rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");
	
	$index = 0;
	$done = true;
	for( $index = 0; $index < $chunks; $index++ ) {
	    if ( !file_exists("{$filePath}_{$index}.part") ) {
	        $done = false;
	        break;
	    }
	}
	if ( $done ) {
	    if (!$out = @fopen($uploadPath, "wb")) {
	    	$res['err']="上传失败,Failed to open output stream";die(json_encode_yec($res));
	    }
	
	    if ( flock($out, LOCK_EX) ) {
	        for( $index = 0; $index < $chunks; $index++ ) {
	            if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {
	                break;
	            }
	
	            while ($buff = fread($in, 4096)) {
	                fwrite($out, $buff);
	            }
	
	            @fclose($in);
	            @unlink("{$filePath}_{$index}.part");
	        }
	
	        flock($out, LOCK_UN);
	    }
	    @fclose($out);
	}
	
	$is_thumb = $_REQUEST['is_thumb'];
	$smalltargetFile='';
	if(isset($is_thumb) && intval(trim($is_thumb))==1)//生成缩略图
	{
		require("./inc/class/pic.class.php");
	
		$img = new image();
		$smallfilename=$fileName."_s".'.'.$ext;
		$smalltargetFile= $uploadDir . '/' . $smallfilename;
		
		$thumb_w= 200;
		$thumb_h= 200;
		
		if(in_array($uploadDir, $con_type))
		{
			$str_width ="ym_".$uploadDir."_width";
			$str_height ="ym_".$uploadDir."_height";
			$ym_width = $$str_width;
			$ym_height = $$str_height;
			$thumb_w=intval($ym_width)==0? $thumb_w :intval($ym_width);
			$thumb_h=intval($ym_height)==0? $thumb_h :intval($ym_height);
		}
	
		$img->param($uploadPath)->thumb($smalltargetFile, $thumb_w, $thumb_h, 1);
	}
	if($tmp_uploadDir=='avatar')
	{
		$uploadPath = $smalltargetFile;
	}

	$res['data'] = array('img'=>ltrim($uploadPath,"./"), 'thumb'=>ltrim($smalltargetFile,"./"));
	die(json_encode_yec($res));
}
?>