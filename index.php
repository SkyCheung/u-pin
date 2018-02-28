<?php
define('in_mx', TRUE);

$ym_version='1.1.7';
$p=isset($_GET['p']) ? addslashes($_GET['p']) : '';
$p=(trim($p)=='') ? 'index' : trim($p);

require("./inc/function/global.php");

switch ($p){
	case 'admin':
		include("./inc/function/global_admin".Ext);
		exit();
    break;
    case 'install':
		require("./install/index".Ext);
		exit();
    break;
    default:
		if(strpos($p, "n-")===0 || $ym_url_path[0] === 'news'){
			include("./inc/function/global_news".Ext);
		}
		else{
			include("./inc/function/global_page".Ext);
		}
    break;
}

?>