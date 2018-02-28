<?php
if (!defined('in_mx')) {exit('Access Denied');}

checkAuth($login_id, 'stat');//权限检测 

require './inc/lib/admin/stat.php';
$months = get_months();

if($act)
{
	$res = array('err' => '', 'res' => '', 'data' => array());
	if($act =='get_sale')
	{
		if($groupby == 'year')
		{
			$cat = $months;
			$row = sale_num_year($year);
			$data =array();
			foreach ($cat as $key => $day) {
				$data[$key] = 0; 
				foreach ($row as $k => $v) {
					if($day == intval($v['sale_date'])."月")
					{
						$data[$key] = $v['num'];
						break;
					}
				}
			}
		}
		elseif($groupby == 'month') {
			$cat = get_days();
			$month = intval(str_replace("月", "", $month));			
			$row = sale_num_month($year, ($month<10?'0'.$month:$month));
			$data =array();
			foreach ($cat as $key => $day) {
				$data[$key] = 0; 
				foreach ($row as $k => $v) {
					if($day == intval($v['sale_date']))
					{
						$data[$key] = $v['num'];
						break;
					}
				}
			}
		}
		elseif($groupby == 'day') {
			$cat = cut_time_part($start_date, $end_date);
			$row = sale_num_day($cat);
			$data =array();
			foreach ($cat as $key => $day) {
				$data[$key] = 0; 
				foreach ($row as $k => $v) {
					if($day == $v['sale_date'])
					{
						$data[$key] = $v['num'];
						break;
					}
				}
			}			
		}
		$res['data']['cat'] = $cat;
		$res['data']['data'] = $data;		
	}
	if($act =='get_total')
	{
		$res['data']= stat_sale_total();
	}
	die(json_encode($res));
}

$start_date = date("Y-m-d", strtotime("-30 day")); //-1 week
$end_date = date("Y-m-d", time());

$cur_year = date("Y", time());
$cur_month = date("m", time());
$years = get_years($cur_year);
 
$cbk_year[$cur_year] = 'class="selected"';
$cbk_month[intval($cur_month)."月"] = 'class="selected"';

 
?>