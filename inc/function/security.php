<?php
function filter($str){
	$str=str_replace(".html","",$str);
	$str=str_replace(".htm","",$str);
	$str=str_replace(".asp","",$str);
	$str=str_replace(".php","",$str);
	$str=str_replace(".jsp","",$str);
	$str=str_replace(".","",$str);
	$str=str_replace("/","",$str);
	$str=str_replace("&","",$str);
	$str=str_replace("*","",$str);
	$str=str_replace("%","",$str);
	$str=str_replace("#","",$str);
	$str=str_replace("!","",$str);
	$str=str_replace("(","",$str);
	$str=str_replace(")","",$str);
	$str=str_replace("-","_",$str);
} 







?>