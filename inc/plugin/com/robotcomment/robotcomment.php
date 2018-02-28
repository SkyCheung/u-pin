<?php
if (!defined('in_mx')) {exit('Access Denied');}

require_once "inc/class/plugin.class.php";

/*评价机器人*/
class RobotComment extends Plugin
{
	private $code = "robotcomment";

	public function __construct()
	{
		parent::__construct($this -> code);
	}

	/*评价*/
	public function create_comment($user = '')
	{
		global $db;

		$item_id = isset($_POST['goods_ids']) ? trim($_POST['goods_ids']) : '';
		$content = isset($_POST['content']) ? trim($_POST['content']) : '';
		$star = 5;
		$uid = 0;
		$sql = '';

		if ($item_id == '')
		{
			message('获取商品失败');
		}
		if ($content == '')
		{
			message('请填写内容哦~');
		}

		$item_id = explode(",", $item_id);
		$time = time();

		$robotuser_code = 'robotuser';
		if (parent::check_install($robotuser_code))
		{
			require plugin . "com/" . $robotuser_code . "/" . $robotuser_code . ".php";
			$c_plugin = new $robotuser_code;
			$user = $c_plugin -> get_robotuser();
			if ($user && count($user) > 0)
			{
				$user_count = count($user);
			}
			$is_anon = 0;
			//匿名评价
		}
		else
		{
			$is_anon = 1;
			//匿名评价
		}

		foreach ($item_id as $k => $v)
		{
			$imgs = $_POST['imgs_'];
			$thumbs = $_POST['thumbs_'];

			if ($user_count > 0)
			{
				$uid = $user[rand(0, $user_count - 1)]['id'];
			}
			$sql .= "(''," . $item_id[$k] . ",'',0," . $uid . ",'" . $content . "'," . $star . ",'" . json_encode($imgs) . "','" . json_encode($thumbs) . "',1," . $is_anon . "," . $time . "),";
		}

		$sql = 'insert into ' . $db -> table('comment') . "(order_sn, item_id,spec, type, uid, content, star, img,thumb, status,is_anon, addtime) values" . rtrim($sql, ",");
		$db -> query($sql);

		message('评价成功', "admin.html?do=plugin&act=config&code=robotcomment");
	}

	public function init_config()
	{
		global $db, $ym_comment_imglimit;
		$code = $this -> code;
		$row = $db -> fetchall("plugin_robotcomment", "*");
		@include  template("config", $this -> tpl_path, 1);
	}

	public function get_config()
	{
		return array("code" => $this -> code, "name" => "评价机器人", "version" => "1.0", "author" => "云EC", "memo" => "评价机器人可用于批量生成海量真实评价，制造人气火爆景象。", "config" => array());
	}

	//添加模板
	public function add_tpl()
	{
		global $db;
		$tpl = trim($_GET['tpl']);
		$res = array('err' => '', 'res' => '', 'data' => array());
		$db -> insert("plugin_robotcomment", array('content' => $tpl));
		$res['res'] = $db -> lastinsertid();
		die(json_encode($res));
	}

	//删除模板
	public function del_tpl()
	{
		global $db;
		$id = trim($_GET['id']);
		$res = array('err' => '', 'res' => '', 'data' => array());
		$db -> delete("plugin_robotcomment", array('id' => $id));
		die(json_encode($res));
	}

	public function install()
	{
		global $db;
		$sql = "CREATE TABLE IF NOT EXISTS " . $db -> table("plugin_robotcomment") . " ( `id` int(11) NOT NULL AUTO_INCREMENT,  `type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '分类', `content` varchar(500) DEFAULT '',  PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ";
		$db -> query($sql);

		//存入预置评价模板
		$word_file = plugin . "com/" . $this -> code . "/word.txt";
		if (file_exists($word_file))
		{
			$word = read_file($word_file);
			$word_arr = explode(PHP_EOL, $word);
			$word_arr = array_filter($word_arr);
			if (count($word_arr) != 0)
			{
				foreach ($word_arr as $k => $v)
				{
					$db -> insert("plugin_robotcomment", array('content' => $v));
				}
			}
		}

		return true;
	}

	public function uninst()
	{
		global $db;
		$sql = "drop table " . $db -> table("plugin_robotcomment") . ";";
		$db -> query($sql);

		return true;
	}

}
?>