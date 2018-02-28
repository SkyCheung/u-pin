<?php

//if(!defined('in_mx')) exit('Access Denied');
/**
 * upload.php
 *
 * Copyright 2013, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */

#!! IMPORTANT:
#!! this file is just an example, it doesn't incorporate any security checks and
#!! is not recommended to be used in production environment as it is. Be sure to
#!! revise it and customize to your needs.


/*$type=$dotype;
$login_id=$lg_id;


$filecode=trim($idcode);
$id=intval($id);
$c_toid=intval($c_toid);*/
/* 

$row=$db->fetch('t_'.$type.'', 'id,c_toid', array('id' => $id,'c_sid' => $site_id,'c_id' => $login_id), 'id asc');
if (!$row){message("Error.");}
$xtoid=$row['c_toid'];
$c_toid=$row['id'];
$row='';

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

// header("HTTP/1.0 500 Internal Server Error");
// exit;


// 5 minutes execution time
@set_time_limit(5 * 60);

// Uncomment this one to fake upload time
// usleep(5000);

// Settings
// $targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";

$targetDir = upload.'tmp'; // Relative to the root
$uploadDir = upload.'img'; // Relative to the root

//$targetDir = 'upload_tmp';
//$uploadDir = 'upload';

$cleanupTargetDir = true; // Remove old files
$maxFileAge = 5 * 3600; // Temp file age in seconds


// Create target dir
if (!file_exists($targetDir)) {
    @mkdir($targetDir);
}

// Create target dir
if (!file_exists($uploadDir)) {
    @mkdir($uploadDir);
}

// Get a file name
if (isset($_REQUEST["name"])) {
    $fileName = $_REQUEST["name"];
} elseif (!empty($_FILES)) {
    $fileName = $_FILES["file"]["name"];
} else {
    $fileName = uniqid("file_");
}

function unicode2utf8($str){
if(!$str) return $str;
$decode = json_decode($str);
if($decode) return $decode;
$str = '["' . $str . '"]';
$decode = json_decode($str);
if(count($decode) == 1){
return $decode[0];
}
return $str;
}
 
$fileext=end(explode('.', $fileName));


$fileName2=str_replace(".".$fileext,"",unicode2utf8('"'.$fileName.'"'));



$fileName=uniqid().".".$fileext;






$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
$uploadPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

// Chunking might be enabled
$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;


// Remove old temp files
if ($cleanupTargetDir) {
    if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
        die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
    }

    while (($file = readdir($dir)) !== false) {
        $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

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
    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
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
        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
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
        die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
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


require("./inc/class/pic.class2.php");

//$pic_info=getimagesize($uploadPath);
$img = new image();

$smallfilename="s_".$fileName;

$smalltargetFile= $uploadDir . DIRECTORY_SEPARATOR . $smallfilename;

$c_uw=intval($zt_w)==0?190:intval($zt_w);
$c_uh=intval($zt_h)==0?180:intval($zt_h);

//$img->param($uploadPath)->thumb($smalltargetFile, $c_uw,$c_uh,1);




if ($pic_info[0]>$max_w){
	//$img->param($biggetFile)->thumb($biggetFile,$max_w,$max_h,0);
}



/*$db->insert('t_'.$type.'_pic', array('c_id' => $login_id, 'c_sid' => $site_id,  'c_toid' => intval($c_toid), 'c_simg' => ptitle($smallfilename), 'c_bimg' => ptitle($fileName), 'c_title' => ptitle($fileName2), 'c_txt' => ptitle(trim($fileName2)), 'c_time' => time()));
$lastid=$db->lastinsertid();
$tempcode12252= jcode($lastid);
$db->update('t_'.$type.'_pic', array('c_code' =>$tempcode12252), array('id' => $lastid));


$tempadaurl234=$ym_idsort[$xtoid]['dir']==''?'':$ym_idsort[$xtoid]['dir'].'/';
$xx5323cdfq='/'.$tempadaurl234.$ym_idsort[$xtoid]['file'].Condir.$filecode.$conext[$type];
$rsx=$db->fetch('t_site', 'c_url',  array('id' => $site_id,'c_id' => $login_id ), 'id asc');
$urlxx35ry=$rsx['c_url'];
$hostname33d3=strtolower(str_replace("www.","",trim($urlxx35ry)));
$hostnamexx3a=explode(",",$hostname33d3);
for ($k=0;$k<count($hostnamexx3a);$k++){
	$xxxfiele3q2=$hostnamexx3a[$k].md5($xx5323cdfq);
	@del_file(cache.$site_code.'/content/'.$xxxfiele3q2);
}*/


// Return Success JSON-RPC response
die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
