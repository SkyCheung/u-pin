<?php
if (!defined('in_mx')) {exit('Access Denied');}

error_reporting(E_ERROR);
require_once './inc/lib/phpqrcode/phpqrcode.php';
$url = urldecode($_GET["data"]);
QRcode::png($url); die(0);
