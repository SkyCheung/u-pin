<?php

/*插件类*/
abstract class Plugin
{
	private $code="";
	public $tpl_path ="";
	public $version ="";
	public $author ="";
	public $memo ="";	
	
	public function __construct($code){
		$this->code = $code;
		$this->tpl_path = plugin ."com/".$code."/tpl/";
	}
		
	public function get_config()
	{
		return array("code"=>"", "name"=>"","version"=>"", "author"=>"", "memo"=>"", "config"=>array());
	}
	
	public function update_config($data, $plugin_code)
	{
		global $db;					
		return $db->update('plugin', $data, array('code'=>$plugin_code));
	}
	
	//检测是否安装
	public function check_install($code)
	{
		global $db;
		$code = empty($code)? $this->code : $code;
		$count = $db->rowcount("plugin", array("code"=>$code));
		
		return $count && intval($count)>0;
	}
	
	//安装插件
    abstract public function install();

    //卸载插件
    abstract public function uninst();
}

?>