<?php
if (!defined('in_mx')) {exit('Access Denied');}

require_once "inc/class/plugin.class.php";
/*会员注册机*/
class RobotUser extends Plugin
{
	private $code = "robotuser";
	
	public function __construct(){
		parent::__construct($this->code);
	}
	
	/*注册会员*/
	public function create_user($user='')
	{
		global $db;
		
		//处理名库
		$name_list = trim($_POST['name_list']) ;
		if(empty($name_list))
		{
			message("先填写要生成的会员名");
		}
		$name_arr = explode(PHP_EOL, $name_list); 
		$name_arr = array_filter($name_arr);
		$num = count($name_arr);
		
		$total = 0;//最终生成会员数
		$time = time();

		//处理会员等级
		require_once './inc/lib/admin/member.php';
		$grade = get_grade();
		$grade_ids = array();
		foreach ($grade as $k => $v) {
			array_push($grade_ids, $v['grade_id']);
		}
		sort($grade_ids);
		$grade_len = count($grade_ids)-1;		
		
		foreach ($name_arr as $k => $v) {
			$tmp_count = $db->rowcount('member', array('uname'=>$v));
			if($tmp_count >0)
			{
				continue;
			}

			$grade_id = $grade_ids[rand(0, $grade_len)];//随机等级
			$salt= get_salt();
			$sql .= "('".$v."', '".md5(md5($salt).$salt)."','".$salt."',".$grade_id.", $time, 1, 1),";
			$total++;
		}
		if($sql !='')
		{
			$sql ="insert into ".$db->table('member')."(uname,pwd,salt,grade_id,addtime,status,is_robot) values".rtrim($sql,",");
			$db->query($sql);
		}
		
		message("生成成功，共生成".$total."个会员", "admin.html?do=plugin&act=config&code=".$this->code);
	}
	
	//获取机器会员	
	public function get_robotuser()
	{
		global $db;
		return $db->fetchall('member',"*", array("is_robot"=>1));
	}
	
	public function init_config()
	{
		global $db;
		$robot_count = $db->rowcount("member",array("is_robot"=>1)); 
		$robot_count = intval($robot_count);

		@include template("config", $this->tpl_path, 1);
	}
	
	public function get_config()
	{
		return array("code"=>$this->code, "name"=>"会员注册机","version"=>"1.0", "author"=>"云EC", "memo"=>"会员注册机可用于批量生成海量会员，制造人气火爆景象。",
			"config"=>array()
		);
	}
	
	public function install()
	{
		global $db;
		$sql ="alter table ".$db->table("member")." add is_robot tinyint(1) default '0'  COMMENT '是否机器人'; ";
		$db->query($sql);
		
		return true;
	}	
	
    public function uninst()
	{
		global $db;
		$sql ="alter table ".$db->table("member")." drop column is_robot; ";
		$db->query($sql);
		return true;
	}

}
?>