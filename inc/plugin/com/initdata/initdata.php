<?php
if (!defined('in_mx')) {exit('Access Denied');}

require_once "inc/class/plugin.class.php";

/*清空演示数据*/
class initData extends Plugin
{
	private $code = "initdata";
	
	public function __construct(){
		parent::__construct($this->code);
	}
	
	public function initdata()
	{
		global $db;
		if(file_exists('./config/yec_initdata.lock'))
		{		 
			message("请先删除config目录下的yec_initdata.lock文件", "admin.html?do=plugin&act=config&code=".$this->code);
		}
		
		$sql ="TRUNCATE TABLE ".$db->table('attribute').";";
		$sql .="delete from ".$db->table('columns')." where is_system=0;";
		$sql .="delete from ".$db->table('news')." where c_toid <>".cat_system.";"; //系统分类的文章不删除
		//$sql .="TRUNCATE TABLE ".$db->table('banner').";";
		//$sql .="TRUNCATE TABLE ".$db->table('banner_pic').";";
		$sql .="TRUNCATE TABLE ".$db->table('category').";";
		$sql .="TRUNCATE TABLE ".$db->table('goods').";";
		$sql .="TRUNCATE TABLE ".$db->table('goods_attr').";";
		$sql .="TRUNCATE TABLE ".$db->table('goods_spec').";";		
		$sql .="TRUNCATE TABLE ".$db->table('goods_cat').";";
		$sql .="TRUNCATE TABLE ".$db->table('type').";";
		$sql .="TRUNCATE TABLE ".$db->table('brand').";";
		$sql .="TRUNCATE TABLE ".$db->table('order_service').";";
		$sql .="TRUNCATE TABLE ".$db->table('order_goods').";";
		$sql .="TRUNCATE TABLE ".$db->table('order_log').";";
		$sql .="TRUNCATE TABLE ".$db->table('order_refund').";";
		$sql .="TRUNCATE TABLE ".$db->table('order').";";
		$sql .="TRUNCATE TABLE ".$db->table('pay_log').";";
		$sql .="TRUNCATE TABLE ".$db->table('sp_discount').";";
		$sql .="TRUNCATE TABLE ".$db->table('sms_session').";";
		$sql .="TRUNCATE TABLE ".$db->table('member_price').";";
		$sql .="TRUNCATE TABLE ".$db->table('member_log').";";
		$sql .="TRUNCATE TABLE ".$db->table('member_fav').";";
		$sql .="TRUNCATE TABLE ".$db->table('delivery').";";
		$sql .="TRUNCATE TABLE ".$db->table('delivery_goods').";";
		$sql .="TRUNCATE TABLE ".$db->table('comment').";";
		$sql .="TRUNCATE TABLE ".$db->table('comment_reply').";";
		$sql .="TRUNCATE TABLE ".$db->table('cart_item').";";
		
		$db->query($sql);// die($sql);
		
		//删除相关图片
		del_dir(upload_banner);
		del_dir(upload_brand);
		del_dir(upload_cat);
		del_dir(upload_goods);
		del_dir(upload_news);
		del_dir(upload_comment);
		del_dir(upload_common);
		del_dir(upload_service);
		
		write_file("config/yec_initdata.lock", "");
		
		message("清空成功", "admin.html?do=plugin&act=config&code=".$this->code);
	}
	
	public function init_config()
	{
		$plugin_code =$this->code;
		@include template("config", $this->tpl_path, 1);
	}
	
	public function get_config()
	{
		return array("code"=>$this->code, "name"=>"清空演示数据","version"=>"1.0", "author"=>"云EC", "memo"=>"一键清空安装的演示数据。",
			"config"=>array()
		);
	}
	
	public function install()
	{
		return true;
	}	
	
    public function uninst()
	{
		return true;
	}

}
?>