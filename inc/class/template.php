<?php
/*php 7.0*/
//define('tpl_C', './cache/');		// 模板编译路径
define('ROOT', "");
function template($file,$tpl_dir="default/", $is_plugin=0) { 
	global $tplrefresh,$exitcon,$ym_tpl; //print $tpl_dir." <br>";
	$tplfile = ($is_plugin==1 ? '' : (".".tpl)).$tpl_dir.$file.'.html';   //print $tplfile.' ';
	//print ' '.$tpl_dir;
	$objfile = tpl_C.($tpl_dir=='admin/'?'': ($is_plugin==1 && strpos($tpl_dir, "./inc/")===0 ? substr($tpl_dir, 6, strlen($tpl_dir)-6):$tpl_dir)); 
	$tmpdir = $tpl_dir;
	$tpl_dir = $is_plugin==1 ? str_replace('./inc/', './cache/', $tpl_dir):$tpl_dir; // print $tplfile.'<br>'.$objfile;
	//print ' '.$tpl_dir;die(' '.$file);
	if($objfile!='' && !file_exists($objfile))
	{
		@mdir($objfile);
	}
	$objfile = $objfile.$file.'.tpl.php'; 
	
	// print $tplfile.' objfile='.$objfile."  <br>";//die();
	$file1 = $file;
	if($tplrefresh == 1 || ($tplrefresh > 1 && substr(time(), -1) > $tplrefresh)) {
		if(!file_exists($objfile) || @filemtime($tplfile) > @filemtime($objfile)) { 
			parse_template($file,$tmpdir,$file1,$is_plugin); 
		}
	}
	return $objfile;
}

function parse_template($file,$tmpdir,$file1='', $is_plugin=0) {
	global $tcon,$hostname,$ym_tpl;
	$nest = 5;
	$tplfile = ($is_plugin==1 ? '' : (".".tpl)).$tmpdir.$file1.'.html'; 
	$objfile = tpl_C. ($tmpdir=='admin/'?'':($is_plugin==1 && strpos($tmpdir, "./inc/")===0 ? substr($tmpdir, 6, strlen($tmpdir)-6):$tmpdir)) .$file.'.tpl.php'; //print $tplfile.' ';
	if(!@$fp = fopen($tplfile, 'r')) {
		exit("Current template file '$file.html' not found or have no access!");
	}

	$template = @fread($fp, filesize($tplfile));
	fclose($fp);
	
	$template=preg_replace("/<\?php.*\?>/si","", $template); //过滤php标签   
	$template=preg_replace("/<\?(.*)\?>/si","《？\\1？》",$template);
	$var_regexp = "({(\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)";
	$const_regexp = "([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)";
	
	$template = str_replace("\$images/", "{\$images}/", $template);
	$template = str_replace("\"images/", "\"{\$images}/", $template);
	$template = str_replace("'images/", "'{\$images}/", $template);
	$template = str_replace("(images/", "({\$images}/", $template);
	$template = $tcon==''?$template:str_replace("{\$con}", $tcon, $template);

	$template = str_replace("href=\"css/", "href=\"{\$css}/", $template);
	$template = str_replace("=\"js/", "=\"{\$js}/", $template);

	if(strpos($template, '{tpl')!==false) {
		$template = preg_replace_callback("/[\n\r\t]*(\<\!\-\-)?\{tpl\s+([a-z0-9_:\/]+)\}(\-\-\>)?[\n\r\t]*/is",parse_template_callback_loadsubtemplate_2, $template);		
	} 

	$template = preg_replace("/([\n\r]+)\t+/s", "\\1", $template);
	$template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
	$template = str_replace("{LF}", "<?=\"\\n\"?>", $template);

	$template = preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\.\x7f-\xff]+)\}/s", "<?=\\1?>", $template);
	$template = preg_replace_callback("/$var_regexp/s", parse_template_callback_addquote_1a, $template);
	$template = preg_replace_callback("/\<\?\=\<\?\=$var_regexp\?\>\?\>/s", parse_template_callback_addquote_1a, $template);

	$template = "<?php if (!defined('in_mx')) {exit('Access Denied');} ?>\n$template";
	$template = preg_replace("/[\n\r\t]*\{template\s+([a-z0-9_]+)\}[\n\r\t]*/is", "\n<? include template('\\1'); ?>\n", $template);
	$template = preg_replace("/[\n\r\t]*\{template\s+(.+?)\}[\n\r\t]*/is", "\n<? include template(".$ym_tpl."/\\1); ?>\n", $template);
	$template = preg_replace_callback("/[\n\r\t]*\{eval\s+(.+?)\}[\n\r\t]*/is", parse_template_callback_evaltags_2, $template);
	$template = preg_replace_callback("/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/is", parse_template_callback_evaltags_1, $template);
	$template = preg_replace_callback("/([\n\r\t])*\{elseif\s+(.+?)\}[\n\r\t]*/is", parse_template_callback_stripvtags_1d, $template);
	$template = preg_replace("/[\n\r\t]*\{else\}[\n\r\t]*/is", "\n<? } else { ?>\n", $template);

	for($i = 0; $i < $nest; $i++) {
		$template = preg_replace_callback("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r\t]*/is", parse_template_callback_stripvtags_1e, $template);
		$template = preg_replace_callback("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*/is", parse_template_callback_stripvtags_1f, $template);
		$template = preg_replace("/\{\/loop\}/i", "<? } ?>", $template);
		$template = preg_replace_callback("/[\n\r\t]*\{if\s+(.+?)\}[\n\r]*(.+?)[\n\r]*\{\/if\}[\n\r\t]*/is", parse_template_callback_stripvtags_1j, $template);
	}

	$template = preg_replace("/\{$const_regexp\}/s", "<?=\\1?>", $template);
	$template = preg_replace("/ \?\>[\n\r]*\<\? /s", " ", $template);
//print $objfile.' ';
	if(!@$fp = fopen($objfile, 'w')) {
		exit("Directory templates not found or have no access!");
	}

	$template = preg_replace_callback("/\"(http)?[\w\.\/:]+\?[^\"]+?&[^\"]+?\"/", parse_template_callback_transamp_0, $template);
	$template = preg_replace_callback("/\<script[^\>]*?src=\"(.+?)\"(.*?)\>\s*\<\/script\>/is", parse_template_callback_stripscriptamp_12, $template);
	$template = preg_replace("/\<\?(\s{1})/is", "<?php\\1", $template);
	$template = preg_replace("/\<\?\=(.+?)\?\>/is", "<?php echo \\1;?>", $template);

	flock($fp, 2);
	fwrite($fp, $template);
	fclose($fp);
}
		
function parse_template_callback_loadsubtemplate_2($matches) {
		return  loadtemplate($matches[2]);
	}
function parse_template_callback_addquote_1a($matches) {
		return addquote('<?='.$matches[1].'?>');
	}
function parse_template_callback_evaltags_2($matches) {
		return stripvtags('<? '.$matches[2].' ?>',''); 
	}	
function parse_template_callback_evaltags_1($matches) {
		return stripvtags('<? echo '.$matches[2].'; ?>',''); 
	}
function parse_template_callback_stripvtags_1d($matches) {
		return stripvtags($matches[1].'<? } elseif('.$matches[2].') { ?>'.$matches[3]);
	}
function parse_template_callback_stripvtags_1e($matches) {
		return stripvtags('<? if(is_array('.$matches[1].')) foreach('.$matches[1].' as '.$matches[2].'){ ?>');
	}
function parse_template_callback_stripvtags_1f($matches) {
		return stripvtags('<? if(is_array('.$matches[1].')) foreach('.$matches[1].' as '.$matches[2].' => '.$matches[3].'){ ?>');
	}	
function parse_template_callback_stripvtags_1j($matches) {
		return stripvtags('<? if('.$matches[1].') { ?>', $matches[2].'<? } ?>');
	}
function parse_template_callback_transamp_0($matches) {
		return transamp($matches[0]);
	}
function parse_template_callback_stripscriptamp_12($matches) {
		return stripscriptamp($matches[1], $matches[2]);
	}

function transamp($str) {
	$str = str_replace('&', '&amp;', $str);
	$str = str_replace('&amp;amp;', '&amp;', $str);
	$str = str_replace('\"', '"', $str);
	return $str;
}

function addquote($var) {
	return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
}

function stripvtags($expr, $statement='') {
	$expr = str_replace("\\\"", "\"", preg_replace("/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr));
	$statement = str_replace("\\\"", "\"", $statement);
	return $expr.$statement;
}

function stripscriptamp($s, $extra='') {
	$s = str_replace('&amp;', '&', $s);
	return "<script src=\"$s\" type=\"text/javascript\"$extra></script>";
}

function loadtemplate($file) {
		global $ym_tpl;
		$tmp_tpl = strpos($file, 'admin_')===0 ? "admin/" : $ym_tpl;
		$tplfile = template($file,$tmp_tpl.'/');
		$filename = $tplfile;
		if(($content = @implode('', file($filename))) || ($content = getphptemplate(@implode('', file(substr($filename, 0, -4).'.php'))))) {
			return $content;
		} else { 
			return '<!-- '.$file.' -->';
		}
}

function getphptemplate($content) {
	$pos = strpos($content, "\n");
	return $pos !== false ? substr($content, $pos + 1) : $content;
}

function display($do)
{
	@include template($do,"admin/");die(0);
}
?>