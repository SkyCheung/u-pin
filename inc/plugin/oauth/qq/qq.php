<?php
if (!defined('in_mx')) {exit('Access Denied');}

require_once(plugin."oauth/qq/API/qqConnectAPI.php");
$qc = new QC();
$qc->qq_login();

exit();
		
?>	