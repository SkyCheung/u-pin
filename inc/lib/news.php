<?php
if (!defined('in_mx')) {exit('Access Denied');}

//获取文章-单页面内容
function get_spage($id = 0, $c_toid =0) {
	if($id == 0 && $c_toid == 0)
	{
		return false;
	}
	$db = dbc();
	$where = '';
	if($id != 0)
	{
		$where .=' and id=' . intval($id);
	}
	if($c_toid != 0)
	{
		$where .=' and c_toid=' . intval($c_toid);
	}
	$row = $db->query("select a.*,b.c_code from ".$db->table('page')." a join ".$db->table('columns')." b on a.c_toid=b.id  where 1 ". $where);
	$row['url']= build_newsurl($row);
	return $row;
}

//获取文章
function get_news($id = 0) {
	$db = dbc();
	$row = $db->fetch('news', '*', array('id' => $id), 'sort asc');
	$row['url']= build_newsurl($row);
	return $row;
}

//获取文章数
function get_news_count($cids ='') {
	global $db;	
	$where = '1';

	if ($cids != '') {
		$where .=" and c_toid in (". $cids.")";
	}
	return $db->rowcount('news', $where);
}

//获取文章
function get_newslist($cids ='',$start=0, $num=12) {
	global $ym_idsort,$ym_htmlext,$db;	
	$where = '1';

	if ($cids != '') {
		$where .=" and c_toid in (". $cids.")";
	}
	$row = $db->fetchall('news', '*', $where, 'sort asc', $start.','.$num);
	foreach($row as $k=>$v){
		$row[$k]['id']=$v['id'];
		$row[$k]['i']= $k+1;
		$row[$k]['time']=ftime($v['c_time']);
		$row[$k]['y']=date("Y",$v['c_time']);
		$row[$k]['m']=date("m",$v['c_time']);
		$row[$k]['d']=date("d",$v['c_time']);
		$row[$k]['title']=$v['c_title'];
		$row[$k]['name']=$v['c_title'];
		$row[$k]['code']=$v['c_code'];
		$row[$k]['txt']=$v['c_txt']=="" ? mb_substr(strip_tags($v['c_body']),0,60,'utf-8') : $v['c_txt'];
		$row[$k]['body']=$v['c_body'];
		$row[$k]['note']=$v['txt'];
		$row[$k]['img']=$v['c_simg'];
		$row[$k]['simg']= $v['c_simg'];
		$row[$k]['bimg']= $v['c_bimg'];
		$row[$k]['classname']= $ym_idsort[$v['c_toid']]['title'];
		$row[$k]['sortname']= $v['classname'];
		$row[$k]['sort']= $v['classname'];
		$row[$k]['classurl']= $ym_idsort[$v['c_toid']]['url'];
		//$tmp_dir = $ym_idsort[$v['c_toid']]['dir']==''?'' : $ym_idsort[$v['c_toid']]['dir']; 
		$row[$k]['url']= '/news'.Condir.$v['c_code'].$ym_htmlext;
	}
	return $row;
}

/*function tosub($subid=0){
	global $ym_idsort, $arr; 
	if ($ym_idsort[$subid]['pid']!=0){
		$arr =tosub($ym_idsort[$subid]['pid']);
	} 
	$arr[] =$ym_idsort[$subid]; 
	return $arr;
}*/

function getsub($subid = 0){
	global $ym_idsort,$subtoparr,$ym_sort; 
	if ($ym_idsort[$subid]['pid']!=0){
		getsub($ym_idsort[$subid]['pid']);
	}else{
		$subtoparr = $ym_sort[$ym_idsort[$subid]['code']];
	}
	return $subtoparr;
}

function getallid($thiscode){
	global $ym_sort,$ids;
	if ($ym_sort[$thispage]['son']){
		foreach ($ym_sort[$thispage]['son'] as $rs){
			$ids .=','.$rs['id']; //显示所有子类
			if ($rs['son']) getallid($rs['code']);
		}
	}
	return $ids;
}

function thisallid($arr){
	global $tmp_id;
	if(!isset($arr) || count($arr)==0)
	{
		return '';
	}
	foreach ($arr as $rs){
		$tmp_id .=','.$rs['id']; //显示所有子类
		if ($rs['son']){
			foreach ($rs['son'] as $v){
				$tmp_id .=','.$v['id']; //显示所有子类
				if ($v['son']) {
					thisallid($v['son']);
				}
			}
		}
	}
	return $tmp_id;
}

//帮助
function get_help()
{
	global $ym_idsort; 
	if(!isset($ym_idsort)){
		return array();
	}

	$arr= array();  
	foreach ($ym_idsort as $k => $v) { 
		if($v['is_help'] ==1 && $v['is']==1)
		{
			$v['son']= array_query('pid', $v['id'], $ym_idsort);
			foreach ($v['son'] as $key => $val) {
				if($val['is']!=1)
				{
					unset($v['son'][$key]);
				}
			}
			
			$arr[] = $v;
		}
	}
	return $arr;
}


?>