<?php
/**
 * 
 * @Author: Sky Cheung<sky_zhang@live.com>
 * Date: 18/1/28
 * @Copyright (C) Dev-os Inc. All rights reserved
 *
 */
define('in_mx', TRUE);
$doc = dirname(__FILE__);
require("$doc/inc/function/global_cron.php");
//require("./inc/plugin/cron/auto_down.php");
//require("./inc/plugin/cron/auto_cancel.php");
if(isset($argv[1])){
    $file = $doc.'/inc/plugin/cron/auto_'.$argv[1].'.php';
    is_file($file) && require($file);
}
