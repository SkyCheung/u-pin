<?php
if (!defined('in_mx')) {exit('Access Denied');}

unset($_SESSION["lg_id"]);
$_SESSION["lg_id"] =  null;
SetCookie("username","",0,"/");
SetCookie("lg_id","",0,"/");
header("Location:/admin.html");


?>