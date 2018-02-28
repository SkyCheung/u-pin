<?php
if (!defined('in_mx')) {exit('Access Denied');}

/**
 * 快递服务类
 */
class express {
	private $exp_sp;
	/**
	 * @param $exp_sp 提供商
	 * */
	public function __construct($exp_sp='') {
		global $ym_exp_sp;
		$this->exp_sp = $exp_sp==''? $ym_exp_sp : $exp_sp;
		require plugin.'express/'.$this->exp_sp.'/'.$this->exp_sp.'.php';
	}
	
	function queryapi($no, $exp_code, $orderCode='')
	{
		$exp =new $this->exp_sp;
	    return $exp->query($no, $exp_code, $orderCode);
	}
	
	
}
?>