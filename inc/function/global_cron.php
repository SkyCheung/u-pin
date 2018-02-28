<?php
/**
 * 
 * @Author: Sky Cheung<sky_zhang@live.com>
 * Date: 18/1/28
 * @Copyright (C) Dev-os Inc. All rights reserved
 *
 */
date_default_timezone_set ('Etc/GMT-8');
error_reporting(E_ALL ^ E_NOTICE);

require("$doc/config/config.php"); //加载配置文件

require("$doc/inc/class/db_mysql_class.php");
require("$doc/inc/function/const.php");
require("$doc/inc/function/common.php");
require("$doc/inc/lib/goods.php");
require("$doc/inc/lib/order.php");
dbc();