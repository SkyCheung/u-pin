<?php
if (!defined('in_mx')) {exit('Access Denied');}
/*报表统计*/

//总销量和和总金额
function stat_sale_total($date_start='', $date_end='', $where ='')
{
	global $db;
	if($date_start !='')
	{
		$where .= " and add_time>=".$date_start;
	}
	if($date_end !='')
	{
		$where .= " and add_time<=".$date_end;
	}
	
	return $db->query("SELECT sum(amount) amount, sum(num) num FROM ".$db->table("order")." o join ".$db->table("order_goods")." og on o.id=og.order_id WHERE status=".order_finish." ".$where);	
}

//各时间点总销量
function sale_num_day($days=array())
{
	global $db;
	$where  = '';
	if(count($days)!=0)
	{
		foreach ($days as $k => $v) {
			$where .= " or FROM_UNIXTIME(add_time,'%Y-%m-%d')='".$v."'";
		}
		$where =' and (' . ltrim($where, " or"). ') ';
	}	

	return $db->queryall("SELECT sum(num) num,FROM_UNIXTIME(add_time,'%Y-%m-%d') sale_date FROM ".$db->table("order")." o join ".$db->table("order_goods")." og on o.id=og.order_id WHERE status=".order_finish." ".$where." group by sale_date order by sale_date");	
}

//某月各天总销量
function sale_num_month($year, $month)
{
	global $db;
	$where  = " and FROM_UNIXTIME(add_time,'%Y%m')=".$year.$month;

	return $db->queryall("SELECT sum(num) num,FROM_UNIXTIME(add_time,'%d') sale_date FROM ".$db->table("order")." o join ".$db->table("order_goods")." og on o.id=og.order_id WHERE status=".order_finish." ".$where." group by sale_date order by sale_date");	
}

//某年各月份总销量
function sale_num_year($year)
{
	global $db;
	$where  = " and FROM_UNIXTIME(add_time,'%Y')=".$year;

	return $db->queryall("SELECT sum(num) num,FROM_UNIXTIME(add_time,'%m') sale_date FROM ".$db->table("order")." o join ".$db->table("order_goods")." og on o.id=og.order_id WHERE status=".order_finish." ".$where." group by sale_date order by sale_date");	
}

/*商品销售排行*/
function sale_top($date_start='', $date_end='', $num =10, $where ='')
{
	global $db;
	if($date_start !='')
	{
		$where .= " and add_time>=".$date_start;
	}
	if($date_end !='')
	{
		$where .= " and add_time<=".$date_end;
	}

	$row = $db->queryall("SELECT g.code,g.name,g.thumb,sum(og.num) total FROM ".$db->table("order")." o join ".$db->table("order_goods")." og on o.id=og.order_id join ".$db->table("goods")." g on g.goods_id=og.goods_id WHERE o.status=".order_finish." ".$where." group by g.goods_id order by total desc limit 0,".intval($num));	
	foreach ($row as $k => $v) {
		$row[$k]['url'] = get_url($v['code']);
	}
	return $row;
}

//会员统计
function stat_member($date_start='', $date_end='')
{
	global $db;
	if($date_start !='')
	{
		$where .= " and addtime>=".$date_start;
	}
	if($date_end !='')
	{
		$where .= " and addtime<=".$date_end;
	}
	
	return $db->queryall("SELECT count(id) num,ifnull(grade_name,'无等级') grade_name FROM ".$db->table("member")." m left join ".$db->table("member_grade")." mg on m.grade_id=mg.grade_id WHERE 1 ".$where." group by grade_name ");	
}

//某年各月份新增会员
function member_num_year($year)
{
	global $db;
	$where  = " and FROM_UNIXTIME(addtime,'%Y')=".$year;

	return $db->queryall("SELECT count(id) num,FROM_UNIXTIME(addtime,'%m') addtime FROM ".$db->table("member")." WHERE 1 ".$where." group by addtime order by addtime");	
}

//某月各天新增会员
function member_num_month($year, $month)
{
	global $db;
	return $db->queryall("SELECT count(id) num,FROM_UNIXTIME(addtime,'%d') addtime FROM ".$db->table("member")." WHERE FROM_UNIXTIME(addtime,'%Y%m')=".$year.$month." group by addtime order by addtime");	
}

//各时间点新增会员
function member_num_day($days=array())
{
	global $db;
	$where  = '';
	if(count($days)!=0)
	{
		foreach ($days as $k => $v) {
			$where .= " or FROM_UNIXTIME(addtime,'%Y-%m-%d')='".$v."'";
		}
		$where =' and (' . ltrim($where, " or"). ') ';
	}	

	return $db->queryall("SELECT count(id) num,FROM_UNIXTIME(addtime,'%Y-%m-%d') addtime FROM ".$db->table("member")." WHERE 1 ".$where." group by addtime order by addtime");	
}



//获取日期
function get_days($month=0)
{
	$month_len = date("t", time());
	$days = array();
	for ($i=1; $i <=$month_len; $i++) { 
		$days[]=$i;
	}
	return $days;
}

//获取月份
function get_months()
{
	return array('1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月');
}

//获取月份
function get_years($cur_year)
{
	$years = array($cur_year);
	for ($i=1; $i < 5; $i++) { 
		$years[]= $cur_year-$i;
	}
	return $years;
}

/**
 * 把指定时间段切分时间点
 * -----------------------------------
 * @param string $start 开始时间
 * @param string $end 结束时间
 * @param int $num 切分数目
 */
function cut_time_part($start, $end="", $num = 8) {
    $start = strtotime(date("Y-m-d", strtotime($start)));
    $end   = strtotime(date("Y-m-d", strtotime($end)));
    $parts = ($end - $start)/$num;
    $last  = ($end - $start)%$num;

    if ($last > 0) {
       $parts = ($end - $start-$last)/$num;
    }
    for ($i=1; $i <= $num; $i++) { 
        $arr[] = date("Y-m-d", $start + $parts * ($i-1));
    }
	$arr[$num-1]=date("Y-m-d", $end);
    
    return $arr;
}

?>