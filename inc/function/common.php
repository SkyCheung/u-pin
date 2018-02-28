<?php
 if (!defined('in_mx')) {exit('Access Denied');}

/*公用函数*/

/** 拼接成in格式: IN('a','b','c')
	 * @access   public
	 * @param    mix      $list      列表数组或字符串
	 * @return   string
*/
function create_in($list= '')
{
	if (empty($list))
	{
	    return " IN ('') ";
	}
	else
	{
	    $str = joinString($list); 			
		return trim($str)=='' ? " IN ('') " : " IN (" . $str . ") ";
	}
}

/**
 * @name 拼接字符串，并且去掉重复项: 'a','b','c'
	 * @access   public
	 * @param    mix      $list      列表数组或字符串
 	 * @param    bool	 $res_arr 是否返回数组
	 * @return   string
 */
function joinString($list='', $delimiter =',', $res_arr=false)
{
	if (!is_array($list))
	{
		$list = explode($delimiter, $list);
	}
	$list = array_unique($list);
	$arr = array(); 
	foreach ($list AS $v)
	{
		if (is_array($v)) {
			foreach ($v as $key => $val) {
				$arr[]= "'".$val."'";
			}			
		}
		elseif ($v !== '')
		{
			$arr[]= "'$v'";
		}
	}
	if($res_arr)
	{
		return $arr;
	}	
	return count($arr)==0 ? " " : join($delimiter, $arr);
}

//读取缓存数据
function get_cache($val, $path = cache_data, $name='')
{
	$ym_val= 'ym_'.$val; 
	$file = $path.$val.'.php';
	if(file_exists($file))
	{
		require $file;
		$ym_val= 'ym_'.($name==''? $val :$name);
		return $$ym_val;
	}
	else {
		return array();
	}	
}

//导航 mid top bot
function get_nav($type ='mid')
{
	$ym_nav = get_cache("nav");
	if($type !='')
	{
		foreach ($ym_nav as $k => $v) {
			if($type!=$v['type'])
			{
				unset($ym_nav[$k]);
			}			
		}
	}
	return $ym_nav; 
}

function get_crumbs_nav($type='', $id= 0)
{
	switch ($type) {
		case 'goods':
			global $ym_cats; 
			$list = get_parents($id, $ym_cats);
			$tmp_nav=''; 
			foreach ($list as $k => $v) {
				$tmp_nav .= ' &gt; <a href="'.$v['url'].'">'.$v['name'].'</a>';
			}
			return $tmp_nav;
			break;
		case 'news':
			global $ym_idsort;
			$list = get_parents($id, $ym_idsort);
			$tmp_nav=''; 
			foreach ($list as $k => $v) {
				$tmp_nav .= '&gt; <a href="'.$v['url'].'">'.$v['name'].'</a>';
			}
			return ltrim($tmp_nav,'&gt; ');
			break;
		default:
			return '';
			break;
	}	
}

/*获取祖先  $arr[0]为根*/
function get_parents($id = 0, $list, $pid = 'pid'){	
	if ($list[$id][$pid]!=0){
		$arr = get_parents($list[$id][$pid], $list);
	}
	$arr[] =$list[$id]; 
	return $arr;
}

/*获取子分类 id*/
function get_child_ids($id = 0, $list, $res_arr=false, $delimiter=','){
	global $ids;
	foreach ($list as $k => $v) {
		if($v['pid']==$id){
			$ids[]= $v['id'];
			unset($list[$k]);
			get_child_ids($v['id'], $list);
		}
	}

	if($res_arr==true)
	{
		return $ids;
	}
	return count($ids)==0 ? "" : join($delimiter, $ids);
}

/**@name 分类树
*@param pid 指定根id
 * */
function get_catTree($pid = 0)
{
	global $ym_cats;
	return getTree($ym_cats, $pid, 'id', 'pid'); 
}

//设置分页  
function getPages($count, $page=1, $pagenum=12, $style=1)
{
	if($count<=$pagenum)
	{return array('pages'=>'','first_page'=>'','last_page'=>'');;}	
	require("./inc/class/page.class.php");
	$url = parse_url($_SERVER["REQUEST_URI"]);
	parse_str($url['query'],$url_param)	;
	unset($url_param['page']);
  
	$params = array(
		'total_rows'=>$count,
		'method'    =>'html',
		'parameter' => $url['path'].(count($url_param)>0 ? ('?'.http_build_query($url_param).'&') : '?').'page={?}', 
		'now_page'  =>$page,
		'list_rows' =>$pagenum,
	);
	$thispage = new Core_Lib_Page($params);
	return $thispage->show($style);
} 

//创建一个新名字
function get_newName()
{
	return md5(uniqid(md5(microtime(true)),true));
}

//获取随机数字
function get_randNum($len=6)
{
	$chars = '0123456789';
    $str = '';
    for ( $i = 0; $i < $len; $i++ ) 
    {
        $str .= $chars[mt_rand(0, strlen($chars) - 1) ];
    }
	return $str;
}

//获取字符串中英文混合长度 
function get_strlen($str,$charset='utf-8'){
 	if($charset=='utf-8') $str = iconv('utf-8','gb2312',$str);
	$num = strlen($str);
	$cnNum = 0;
	for($i=0;$i<$num;$i++){
		if(ord(substr($str,$i+1,1))>127){
			$cnNum++;
			$i++;
		}
	} 
	$enNum = $num-($cnNum*2); 
	return $cnNum*2+$enNum;
}

/**@name 构造url
*@param 
 * */
function build_url($k, $v, $list = array()) {	
	$url = parse_url($_SERVER['REQUEST_URI']);
	parse_str($url['query'], $param); 
	
	$v_arr = explode('_', $v);
	if($k=='at' && array_key_exists($k, $param))
	{		
		$at_arr= array_filter(explode('@', $param[$k]));  // at=71_1000克@72_白色
		$i = count($at_arr);			 
		foreach ($at_arr as $key => $val) {
			$val_arr = explode('_', $val);
			if($val_arr[0] == $v_arr[0])
			{
				$i = $key;
				break;
			}
		}
		if($v_arr[1]=='')
		{
			unset($at_arr[$i]);
		}
		else {
			$at_arr[$i] = $v;
		}
		$param[$k] = stripslashes(implode($at_arr, '@'));
	}
	elseif($k=='at' && $v_arr[1]=='') {
		unset($param[$k]);
	}
	else {
		$param[$k] = stripslashes($v);
	}
	$param['page']=1;
	$param = array_filter($param);
	return $url['path'].(count($param)==0?'': '?'.http_build_query($param));
}

/**写日志*/
function logs($data='', $file='', $mode=FILE_APPEND)
{
	$logs_dir ='./inc/logs/'; 
	if( !file_exists($logs_dir) )
	{
		@mdir($logs_dir);
	}
	$file= ($file=='' ? 'log-' : $file.'-').date('Ymd',time()).'.log';

	file_put_contents($logs_dir . $file, date('Y-m-d H:i:s',time()).' '.$data.PHP_EOL, $mode);
}

//提示信息
function message($msg, $url="back", $miao="3"){
	global $ym_tpl;
	if ($url=="back") {$url="javascript:history.back(1)";}
	if ($miao=="0") {header("Location:$url");}
	if($_GET["p"] !='admin')
	{	
		redirect("showmessage.html?msg=".$msg."&url=".$url."&s=".$miao);
	}
	
	$redcon=read_file(".".tpl."admin/showmessage.html");
	$redcon=str_replace("{msg}",$msg,$redcon);
	$redcon=str_replace("{s}",$miao,$redcon);
	$redcon=str_replace("{url}",$url,$redcon);
	$redcon=str_replace("\$images",tpl."admin/\$images",$redcon);
	echo $redcon;
	exit;
}

/*跳转页面*/
function redirect($url, $time=0)
{
	if($time !=0)
	{
		echo '<meta http-equiv="Refresh" content="'.$time.'; url='.$url.'" />';
	} 
	else {
		header("Location:".$url);
	}
	exit(0);
}

//获取一个字符串中的后缀
function get_extension($str)
{
	$str= explode('.', $str);
	return end($str);
}

//获取一个字符串中的文件名
function get_filename($str)
{
	if($str==''){return '';}
	if(stripos($str, '/') !==false)
	{
		$str = substr($str, strrpos($str, '/') + 1, strlen($str));
	}
	return substr($str, 0, strrpos($str, '.'));
}

//根据大图地址返回小图地址
function get_smallName($filename)
{
	$pathinfo = pathinfo($filename);
	$tmp= basename($filename,".".$pathinfo['extension']);
	return str_replace($tmp, basename($filename,".".$pathinfo['extension']).'_s', $filename);
} 

//返回某目录下的所有文件(不包括子目录)
function getfiles($dir)
{
	if($dir !='' && substr($dir, strlen($dir)-1)!='')
	{
		$dir .='/';
	}
	$row=array();
	if (is_dir($dir)) {
		if ($di = opendir($dir)) {
			while (($file = readdir($di)) !== false) {
				if(is_file($dir.$file) && $file !='.' && $file !='..')
				{
					$row[]=$file;
				}
			} 
			closedir($di);			
		}
	}
	return $row;
}

//返回某目录下的所有文件(不包括子目录)
function get_dir($dir)
{
	if($dir !='' && substr($dir, strlen($dir)-1)!='')
	{
		$dir .='/';
	}
	$row =array();
	if (is_dir($dir)) {
		if ($di = opendir($dir)) {
			while (($file = readdir($di)) !== false) {
				if(is_dir($dir.$file) && $file !='.' && $file !='..')
				{
					$row[] = $file;
				}
			} 
			closedir($di);
		}
	}
	return $row;
}

//删除文件
function del_file($file){
       $delete = @unlink($file);
        clearstatcache();
        if(@file_exists($file)){
        	$filesys = str_replace("/","\\",$file);
        	$delete = @system("del $filesys");
        	clearstatcache();
        	if(@file_exists($file)){
                	$delete = @chmod ($file, 0777);
                	$delete = @unlink($file);
                	$delete = @system("del $filesys");
                }
        }
        clearstatcache();
        if(@file_exists($file)){
                return false;
        }else{
               return true;
     }
}

//创建目录
function mdir($directoryName) {
	$directoryName = str_replace("\\","/",$directoryName);
	$dirNames = explode('/', $directoryName);
	$total = count($dirNames) ;
	$temp = '';
	for($i=0; $i<$total; $i++) {
		$temp .= $dirNames[$i].'/';
		if (!is_dir($temp)) {
			$oldmask = umask(0);
			if (!mkdir($temp, 0777)) exit("can't md dir $temp"); 
			umask($oldmask);
			}
		}
	return true;
}

/**@name 获取随机字符串，大小写字母+数字，转变不区分大小写
*@param $len 长度
*@param $ignoreCase 忽略大小写
 * */
function get_salt($len=6, $ignoreCase=true)
{
	//return substr(uniqid(rand()), -$len);
	$discode="123546789wertyupkjhgfdaszxcvbnm".($ignoreCase?'': 'QABCDEFGHJKLMNPRSTUVWXYZ');
	$code_len = strlen($discode);
	$code = "";	
	for($j=0; $j<$len; $j++){
		$code .= $discode[rand(0, $code_len-1)];
	}
	return $code;
}

//加密字符串
function encryptStr($val, $salt='')
{	
	return md5(md5(trim($val)).$salt);
}

/**@name 是否只有数字和逗号
*@param str 字符串
 * */ 
function isNumComma($str)
{
	return preg_match("/^[\d,]+$/", $str);
}

/**@name 是否只有数字
*@param str 字符串
 * */ 
function is_num($str)
{
	return preg_match("/^[0-9]+$/", $str);
}

/**@name 字母和数字
*@param str 字符串
 * */ 
function is_enAndnum($str, $start=1, $end=16)
{
	return preg_match("/^[A-Za-z0-9]{".$start.",".$end."}$/", $str);
}

/**是否只有字母*/ 
function isLetter($str)
{
	return preg_match("/^[a-zA-Z]+$/", $str);
}

/**@name 是否email
 * @param   string   $str 
*/
function is_email($str)
{
	return preg_match("/^[a-zA-Z]+$/", $str);
}

/**@name 用户名：中文、字母、数字、“-”“_”的组合
 * @param   string   $str 
*/
function is_username($str)
{
	return preg_match('/^[A-Za-z0-9_\-\x{4e00}-\x{9fa5}]+$/u', $str);
}

/**@name 收货人：中文、字母和空格的组合
 * @param   string   $str 
*/
function is_consignee($str)
{ 
	return preg_match('/^(([\x{4e00}-\x{9fa5}])([\x{4e00}-\x{9fa5}·]){0,10}([\x{4e00}-\x{9fa5}]))|([a-zA-Z]([a-zA-Z]|\s){2,20})+$/u', $str);
}

/**是否手机号码
 * @param   string   $str 
*/
function is_mobile($str)
{
	return preg_match("/^(13|14|15|18|17)[0-9]{9}$/", $str);
}

/**是否固定电话
 * @param   string   $str 
*/
function is_tel($str)
{
	return preg_match("/^[0-9]{1,4}(-|[0-9])\d{5,15}$/", $str);
}

/**将字符串匿名化   比如 158***545
 * */
function format_anon($val, $start_len=1, $end_len=1, $cover_len=3, $cover='*')
{
	$cover_str="";
	for ($i=0; $i < $cover_len; $i++) { 
		$cover_str .=$cover;
	}
	return mb_substr($val, 0,$start_len,'utf-8').$cover_str.mb_substr($val, mb_strlen($val,'utf-8')-$end_len,$end_len,'utf-8');
}

/** 
    *将一个字串中含有全角的数字字符、字母、空格或'%+-()'字符转换为相应半角字符 
    * @access    public 
    * @param     string $str     待转换字串 
    * @return    string  $str    处理后字串 
    */  
function make_semiangle($str) {  
     $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4','５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9', 'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E','Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J', 'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O','Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T','Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y','Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd','ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i','ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n','ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's', 'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x', 'ｙ' => 'y', 'ｚ' => 'z','（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[','】' => ']', '〖' => '[', '〗' => ']', '“' => '[', '”' => ']','‘' => '[', '’' => ']', '｛' => '{', '｝' => '}', '《' => '<','》' => '>','％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-','：' => ':', '。' => '.', '、' => ',', '，' => '.', '、' => '.',     '；' => ',', '？' => '?', '！' => '!', '…' => '-', '‖' => '|', '”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"','　' => ' ');  
     return strtr($str, $arr);  
}

/**格式化价格
 * @param   float   $price  商品价格
 * @param 	int		$format_type 价格格式
 * @return  string
*/
function format_price($price, $format_type=-1)
{
	$price = floatval($price);
	$format_type= $format_type == -1 ? $ym_priceformat : $format_type;
 
    switch ($format_type)
    {
        case 0:
            $price = number_format($price, 2, '.', '');
            break;
		case 1: // 不四舍五入，保留一位小数
        	$price = substr(number_format($price, 2, '.', ''), 0, -1);
            break;
        case 2: // 不四舍五入，不保留小数
            $price = intval($price);
            break;	
        case 3: // 四舍五入，不保留小数
            $price = round($price);
            break;
        case 4: // 四舍五入，保留一位小数
            $price = number_format($price, 1, '.', '');
            break;
        case 5: // 四舍五入，保留两位小数
            $price = number_format($price, 2, '.', '');
            break;
    }   

    return $price;
}

/*获取多维数组里的某些数组
 $key=键名
 $val=键值
 $arr=数组
 * */
function array_query($key, $val, $arr) {
	$res =array(); 
	if(is_array($arr))
	{
		foreach ($arr as $k => $v) {
			if (trim($v[$key])===trim($val) ) {	 
				$res[] = $v;		
			} 
		}
	}
	return $res;
}

//树形数组
function getTree($rows, $root = 0, $id = 'id', $pid = 'pid', $child = 'child') {
	$tree = array();	
	if (is_array($rows)) {
		$array = array();
		foreach ($rows as $key => $item) {
			$array[$item[$id]] = &$rows[$key];
		}

		foreach ($rows as $key => $item) {			
			$parentId = $item[$pid];
			if ($root == $parentId) { 
				$tree[] = &$rows[$key];
			} else {
				if (isset($array[$parentId])) { 
					$parent = &$array[$parentId];
					$parent[$child][] = &$rows[$key];					
				}
			}
		} 
	}
	return $tree;
}

//检测审查的文字
function check_censor($field, $word='keepword')
{
	$words =get_cache('censor', $path = cache_data, $word);
	$exp = '/^('.str_replace(array('\\*', "\r\n", ' '), array('.*', '|', ''), preg_quote(($words = trim($words)), '/')).')$/i'; //print $words.' '.$exp;
	if($words && @preg_match($exp, $field)) {
		return false;
	}
	return true;
}

function build_newsurl($row)
{
	global $ym_htmlext, $ym_idsort,$conext;
	//$dir=$ym_idsort[$row['c_toid']]['dir']==''?'':$ym_idsort[$row['c_toid']]['dir'].'/';
	//return '/'.$dir.$ym_idsort[$row['c_toid']]['file'].Condir.$row['c_code'].$conext['news'].$ym_htmlext;
	return '/n-'.$row['c_code'].$conext['news'].$ym_htmlext;
}

function get_url($code, $type='goods')
{
	global $ym_htmlext, $ym_idsort,$conext;
	switch ($type) {
		case 'goods':
			return $code.'-g.html';
		case 'news':			
			return '/n-'.$code.$conext['news'].".html";
		default:
			return '';
	}
}

//发送邮件
function sendmail($to, $subject = '',$body = '')
{
	require cache_data.'message'.'.php';
	$msg =array_query('code','email', $ym_message);
	if($msg[0]['config']==''){return '请正确配置邮件服务器';}
	
	$config = json_decode($msg[0]['config'] ,true);
	if(trim($config['host'])=='' || intval($config['port'])==0 || trim($config['email'])=='' || trim($config['pwd'])=='')
	{
		return '请正确配置邮件服务器';
	}
	logs($config['pwd'].' '.endecrypt($config['pwd']));
	return postmail($to, $subject,$body,$config['host'],$config['port'],$config['email'],endecrypt($config['pwd'],'DECODE') );
}

//发邮件 $to 表示收件人地址 $subject 表示邮件标题 $body表示邮件正文
function postmail($to, $subject = '',$body = '',$host,$port=25,$from,$pwd){	
    //error_reporting(E_ALL);
    error_reporting(E_STRICT);
    date_default_timezone_set('Asia/Shanghai');//设定时区东八区
    require_once('./inc/class/class.phpmailer.php');
    include('./inc/class/class.smtp.php');
    $mail             = new PHPMailer(); //new一个PHPMailer对象出来
    $body            = str_replace("[\]",'',$body); //对邮件内容进行必要的过滤
    $mail->CharSet ="utf-8";//设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP(); // 设定使用SMTP服务
    $mail->SMTPDebug  = 1;                     // 启用SMTP调试功能
    // 1 = errors and messages
    // 2 = messages only
    $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
    $mail->SMTPSecure = "tls";  // tls/ssl               // 安全协议，可以注释掉
    $mail->Host       = $host;      // SMTP 服务器
    $mail->Port       = $port;                   // SMTP服务器的端口号
    $mail->Timeout  = 30;
    $mail->Username   = $from;  // SMTP服务器用户名
    $mail->Password   = $pwd;            // SMTP服务器密码
    $mail->SetFrom($from, '');
    $mail->AddReplyTo($from, '');
    $mail->Subject    = $subject;
    $mail->AltBody    = 'To view the message, please use an HTML compatible email viewer!'; // optional, comment out and test
    $mail->MsgHTML($body);
    $address = $to;
    $mail->AddAddress($address, '');
    //$mail->AddAttachment("images/phpmailer.gif");      // attachment
    //$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
    
    if(!$mail->Send()) {
        return $mail->ErrorInfo;
    } else {
		return true;
    }
}

// $string： 明文 或 密文  
// $operation：DECODE表示解密,其它表示加密  
// $key： 密匙  
// $expiry：密文有效期  
function endecrypt($string, $operation = 'DECODE', $key = 'yunec.cn', $expiry = 0) {  
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙  
    global $ym_auth_key;
    $ckey_length = 4;  
       
    // 密匙  
    $key = md5($key ? $key : $ym_auth_key); 
       
    // 密匙a会参与加解密  
    $keya = md5(substr($key, 0, 16));  
    // 密匙b会用来做数据完整性验证  
    $keyb = md5(substr($key, 16, 16));  
    // 密匙c用于变化生成的密文  
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';  
    // 参与运算的密匙  
    $cryptkey = $keya.md5($keya.$keyc);  
    $key_length = strlen($cryptkey);  
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性  
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确  
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;  
    $string_length = strlen($string);  
    $result = '';  
    $box = range(0, 255);  
    $rndkey = array();  
    // 产生密匙簿  
    for($i = 0; $i <= 255; $i++) {  
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);  
    }  
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度  
    for($j = $i = 0; $i < 256; $i++) {  
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;  
        $tmp = $box[$i];  
        $box[$i] = $box[$j];  
        $box[$j] = $tmp;  
    }  
    // 核心加解密部分  
    for($a = $j = $i = 0; $i < $string_length; $i++) {  
        $a = ($a + 1) % 256;  
        $j = ($j + $box[$a]) % 256;  
        $tmp = $box[$a];  
        $box[$a] = $box[$j];  
        $box[$j] = $tmp;  
        // 从密匙簿得出密匙进行异或，再转成字符  
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));  
    }  
    if($operation == 'DECODE') {  
        // substr($result, 0, 10) == 0 验证数据有效性  
        // substr($result, 0, 10) - time() > 0 验证数据有效性  
        // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性  
        // 验证数据有效性，请看未加密明文的格式  
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {  
            return substr($result, 26);  
        } else {  
            return '';  
        }  
    } else {  
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因  
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码  
        return $keyc.str_replace('=', '', base64_encode($result));  
    }  
}

/*时间函数*/
function time_diff($begin_time,$end_time)
{
      if($begin_time < $end_time){
         $starttime = $begin_time;
         $endtime = $end_time;
      }else{
         $starttime = $end_time;
         $endtime = $begin_time;
      }

      //计算天数
      $timediff = $endtime-$starttime;
      $days = intval($timediff/86400);
      //计算小时数
      $remain = $timediff%86400;
      $hours = intval($remain/3600);
      //计算分钟数
      $remain = $remain%3600;
      $mins = intval($remain/60);
      //计算秒数
      $secs = $remain%60;
      return array("day" => $days,"hour" => $hours,"min" => $mins,"sec" => $secs);
}

	/** 以post方式提交data到对应的接口url
	 * @param string $url  url
	 * @param string $data  需要post的数据, 可以是数组、xml、json、字符串等 
	 * @param int $second  url执行超时时间，默认30s
	 * @param string $sslcert_path 证书cert路径
 	 * @param string $sslcert_type 证书cert类型
	 * @param string $sslkey_path  证书key路径
	 * @param string $sslkey_type  证书key类型
	 * @throws Exception
	 */
function post_url($url, $data='', $timeout = 30, $sslcert_path='', $sslcert_type='PEM',$sslkey_path='', $sslkey_type='PEM',$verifypeer=FALSE,$verifyhost=0)
	{		
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		
		//如果有配置代理这里就设置代理
		if(CURL_PROXY_HOST != "0.0.0.0" && CURL_PROXY_PORT != 0){
			curl_setopt($ch,CURLOPT_PROXY, CURL_PROXY_HOST);
			curl_setopt($ch,CURLOPT_PROXYPORT, CURL_PROXY_PORT);
		}
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, $verifypeer);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, $verifyhost);//是否严格校验
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	
		if($sslcert_path !== ''){//使用证书：cert
			curl_setopt($ch,CURLOPT_SSLCERTTYPE, $sslcert_type);
			curl_setopt($ch,CURLOPT_SSLCERT, $sslcert_path);
		}
		if($sslkey_path !== ''){
			curl_setopt($ch,CURLOPT_SSLKEYTYPE, $sslkey_type);
			curl_setopt($ch,CURLOPT_SSLKEY, $sslkey_path);
		}
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		$data = curl_exec($ch); //var_dump($data);
		
		$res =array('err'=>'', 'res'=>'', 'data'=>array());

		if($data){
			curl_close($ch);
			$res['data'] =$data;			
		} else { 
			$error = curl_errno($ch);
			$info = curl_getinfo($ch);
			curl_close($ch);
			$res['err'] = "curl出错，错误码:$error http_code=".$info['http_code'];
		}
		return $res;
	}

	/**通过curl方式获取远程图片到本地 
	*@param $url 完整的图片地址 
	*@param $filename 文件名 
	*/ 
function get_curl_file($url = "", $filename = "" , $time=6) { 
		if(!is_dir($filename)) { 
			mdir(str_replace(basename($filename), '', $filename));
		} 
		//去除URL连接上面可能的引号 
		$url = preg_replace( '/(?:^[\'"]+|[\'"\/]+$)/', '', $url ); 
		$ch = curl_init(); 
		$fp = fopen($filename,'wb'); 
		curl_setopt($ch,CURLOPT_URL,$url); 
		curl_setopt($ch,CURLOPT_FILE,$fp); 
		curl_setopt($ch,CURLOPT_HEADER,0); 
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1); 
		curl_setopt($ch,CURLOPT_TIMEOUT, $time); 
		curl_exec($ch); 
		curl_close($ch); 
		fclose($fp); 
		return ''; 
	}
	
	//获取完整url
	function get_fullurl() {
		global $ym_is_https;
	    $protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' && $ym_is_https==1 ? 'https://' : 'http://';
	    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	    $req_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
	    return $protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$req_url;
	}
	
	//获取用户设备类型
	function get_client()
	{
		global $ym_client;
		if(isset($_SESSION['client']))
		{
			return $_SESSION['client'];
		}
		session_start();
		if($ym_client && ($ym_client ==client_pc || $ym_client ==client_m || $ym_client ==client_app))
		{
			$_SESSION['client'] = $ym_client;
			return $ym_client;
		}
		include("./inc/lib/mobile_detect.php");
		$detect = new Mobile_Detect();
		if($detect->isMobile())
		{
			$_SESSION['client'] = client_m;
			return client_m;
		}else {
			$_SESSION['client'] = client_pc;
			return client_pc;
		}		
	} 

	/**是否微信浏览器*/
	function is_weixin()
	{		
        if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'micromessenger') === false)
        {
            return false;
        }
		return true;
	}
	
	//获取图片缩略图设置
	function get_thumb_config($cat_id=0,$type='goods')
	{
		global $ym_idsort,$ym_thumb_type,$ym_goods_width,$ym_goods_height,$ym_news_width,$ym_news_height, $ym_goodsimg_width, $ym_goodsimg_height,$ym_brand_width,$ym_brand_height ;
		$result = array('width'=>0, 'height'=>0, 'thumb_type'=>0);
		switch ($type) {
			case 'goods':
				$width=isset($ym_cats) ? intval($ym_cats[intval($cat_id)]['width']) : 0;				
				if($width ==0)
				{
					if(isset($ym_goods_width) && intval($ym_goods_width) !=0)
					{
						$width = $ym_goods_width;
					}
					else {
						$width = 200;
					}
				}

				$height=isset($ym_cats) ? intval($ym_cats[intval($cat_id)]['height']) : 0;				
				if($height ==0)
				{
					if(isset($ym_goods_height) && intval($ym_goods_height) !=0)
					{
						$height = $ym_goods_height;
					}
					else {
						$height = 200;
					}	
				}
				
				$result['width'] = $width;
				$result['height'] = $height;
				$result['thumb_type'] = $ym_cats[intval($cat_id)]['smtype']=='' ? $ym_thumb_type : $ym_cats[intval($cat_id)]['smtype'];
				break;
			case 'news':								
				$width=isset($ym_idsort) ? intval($ym_idsort[intval($cat_id)]['width']) : 0;				
				if($width ==0)
				{
					if(isset($ym_news_width) && intval($ym_news_width) !=0)
					{
						$width = $ym_news_width;
					}	
					else {
						$width = 200;
					}				
				}
				
				$height=isset($ym_idsort) ? intval($ym_idsort[intval($cat_id)]['height']) : 0;				
				if($height ==0)
				{
					if(isset($ym_news_height) && intval($ym_news_height) !=0)
					{
						$height = $ym_news_height;
					}
					else {
						$height = 200;
					}
				}
								
				$result['width'] = $width;
				$result['height'] = $height;
				$result['thumb_type'] = $ym_idsort[intval($cat_id)]['smtype']=='' ? $ym_thumb_type : $ym_idsort[intval($cat_id)]['smtype'];
				break;
			case 'goodsimg'://商品图册					
				if(isset($ym_goodsimg_width) && intval($ym_goodsimg_width) !=0)
				{
					$width = $ym_goodsimg_width;
				}	
				else {
					$width = 450;
				}
				
				if(isset($ym_goodsimg_height) && intval($ym_goodsimg_height) !=0)
				{
					$height = $ym_goodsimg_height;
				}	
				else {
					$height = 450;
				}
								
				$result['width'] = $width;
				$result['height'] = $height;
				$result['thumb_type'] = isset($ym_thumb_type) ? intval($ym_thumb_type) :1;
				break;
			case 'brand'://品牌Logo					
				if(isset($ym_brand_width) && intval($ym_brand_width) !=0)
				{
					$width = $ym_brand_width;
				}	
				else {
					$width = 450;
				}
				
				if(isset($ym_brand_height) && intval($ym_brand_height) !=0)
				{
					$height = $ym_brand_height;
				}	
				else {
					$height = 450;
				}
								
				$result['width'] = $width;
				$result['height'] = $height;
				$result['thumb_type'] = isset($ym_thumb_type) ? intval($ym_thumb_type) :1;
				break;	
			default:
				break;
		}
		return $result;
	}

	//设置cookie
	function set_cookie($name, $value='',$expire=0, $path="/", $domain=null)
	{
		$domain = $domain==null ? cookiedomain : $domain;
		setcookie($name, $value, $expire, $path, $domain); 
	}
	
	//裁剪图片
	function resize_img($filename,$type='', $width=0, $height=0, $thumb_type=-1)
	{
		require_once("./inc/class/pic.class.php");
		$img = new image();
		if($type !='')
		{
			$tb_config = get_thumb_config(0, $type);
			$tb_type = $tb_config['thumb_type'];
		}
		else {
			$tb_type = isset($ym_thumb_type) ? intval($ym_thumb_type) :1;
		}
		$tb_type = $thumb_type ==-1 ? $tb_type :$thumb_type;
		//die($tb_config['width'].''.$tb_config['height']);
		$img->param($filename)->thumb($filename, ($width !=0 ? $width : $tb_config['width']) ,($height!=0 ? $height : $tb_config['height']), $tb_type);
	}

	/**
	* 对变量进行 JSON 编码
	* @param mixed $val 待编码的值
	* @return string 返回json形式数据
	*/
	function json_encode_yec($val)
	{
	    if (version_compare(PHP_VERSION,'5.4.0','<'))
	    {
	        $str = json_encode($val);
	        $str = preg_replace_callback("#\\\u([0-9a-f]{4})#i",function($m)
	        {
	            return iconv('UCS-2BE', 'UTF-8', pack('H4', $m[1]));
	        },$str);
	        return $str;
	    }
	    else
	    {
	        return json_encode($val, JSON_UNESCAPED_UNICODE);
	    }
	}
	
	//判断字符串是否是绝对地址
	function is_httpOrhttps($val)
	{
		return preg_match('/(http:\/\/)|(https:\/\/)/i', $val);
	}
	
	
	/*将相对路径转换成绝对路径*/
	function url_to_abs($url)
	{
		global $ym_url;
		if($url == '')
		{
			return '';
		}
		if(is_httpOrhttps($url))
		{
			return $url;
		}
		
		return $ym_url.ltrim($url,"/");
	}

	/*将图片的相对路径变为绝对路径*/
	function replace_uploadURL($val)
	{
		global $ym_url;
		if(strpos($val, "/upload/") ==0)
		{
			return str_replace('/upload/', $ym_url.'upload/', $val);
		}else {
			return str_replace('"/upload', '"'.$ym_url.'upload', $val);
		}
		
	}
?>