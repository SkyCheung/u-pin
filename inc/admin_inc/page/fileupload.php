<?php 
if(!defined('in_mx')) exit('Access Denied');
/**
 * upload.php
 *
 * Copyright 2013, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */

// Make sure file is not cached (as it happens for example on iOS devices)
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Support CORS
// header("Access-Control-Allow-Origin: *");
// other CORS headers if any...
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
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
$res = array('err' => '', 'res' => '', 'data' => array()); //回调参数
$uploadDir ='common';//默认存储目录
//自定上传目录
if(isset($_REQUEST["uploaddir"])&&trim($_REQUEST["uploaddir"])!='')
{
	if(isLetter(trim($_REQUEST["uploaddir"]))==false)
	{
		$res['err']="上传目录格式不正确，仅限字母";die(json_encode($res));
	}
	$uploadDir = trim($_REQUEST["uploaddir"]);	
}

$targetDir = upload.'tmp'; // Relative to the root
if($uploadDir =='logo')
{
	$uploadDir = "./static/images";
}
else {
	$uploadDir = upload_img.$uploadDir.'/'. ($uploadDir =='expresstpl'?'': date("Ymd"));	
}

$cleanupTargetDir = true; // Remove old files
$maxFileAge = 5 * 3600; // Temp file age in seconds

// Create target dir
if (!file_exists($targetDir)) {
    @mkdir($targetDir);
}

// Create upload dir
if (!file_exists($uploadDir)) {
    @mdir($uploadDir);
}

// Get a file name
if (isset($_REQUEST["filename"]) && $_REQUEST["filename"]!='' && is_enAndnum($_REQUEST["filename"],1,32) && strlen($_REQUEST["filename"])<=32) {				
		$oldname ='';
		switch ($uploadDir) {
			case 'avatar':
			dbc();
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
$ext=strtolower($fileInfo["extension"]); //扩展名
 
$filePath = $targetDir . '/' . $fileName.'.'.$ext;
$uploadPath = $uploadDir . '/' . $fileName.'.'.$ext; 

// Chunking might be enabled
$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;

// Remove old temp files
if ($cleanupTargetDir) {
    if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
    	$res['err']="打开临时目录失败.";die(json_encode($res));
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
	$res['err']="上传失败,Failed to open output stream";die(json_encode($res));
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
    	$res['err']="上传失败,Failed to open input stream";die(json_encode($res));
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
    	$res['err']="上传失败,Failed to open output stream";die(json_encode($res));
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
	$smallfilename = $fileName."_s".'.'.$ext;
	$smalltargetFile= $uploadDir . '/' . $smallfilename;
	$c_uw=intval($ym_goods_width)==0? 200 :intval($ym_goods_width);
	$c_uh=intval($ym_goods_height)==0? 200 :intval($ym_goods_height);
	$img->param($uploadPath)->thumb($smalltargetFile, $c_uw,$c_uh, (isset($ym_thumb_type) ? intval($ym_thumb_type) :1) );
}

$row= array();
list($width, $height) = getimagesize($uploadPath);
$row['attr']= array("width"=>$width, "height"=>$height);
$row['img']=ltrim($uploadPath,"./");
$row['thumb']=ltrim($smalltargetFile,"./");
$res['data'] = $row;
die(json_encode($res));

?>