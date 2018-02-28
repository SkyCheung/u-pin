<?php
/**
 * PHP Class Openfire User Service Plugin
 * see http://www.igniterealtime.org/projects/openfire/plugins/userservice/readme.html
 * @copyright 2013 Noercholis
 * @license   GNU General Public License
 * @version   Release 1.0.0

 * @example   See below
 * <code>
 * <?php
 * $user = new UserService;
 *   // can't enable curl??
 *   // don't worry, you can use 
 *   // $user = new UserService(false);
 *  
 * $param = array("testuser","testpass","testname","testmail@web.com");
 *   // sometimes we need to use this parameter structure
 *   // $param = array(
 *   //		"username"=>"testuser",
 *   //		"password"=>"password",
 *   //		"name"=>"testname",
 *   //		"email"=>"testmail@web.com"
 *   //		);
 * echo $result = $user->api("add",$param);
 * ?>
 * </code>
**/
class UserService{
	private $secret="qn4PaL2o";//bigsecret
	private $host="localhost";
	private $header="http";//or https to avoid sniff,require ssl cert
	private $port="9090";
	private $plugin = "plugins/userService/userservice";//plugin dir
	private $curlEnable=true;
	private $cmd=array();
	private $base;
	public function __construct($curl=true){
		$this->curlEnable=$curl;
		$this->base=$this->header."://".$this->host;
		if(!$curl){
			$this->base.=":".$this->port;
		}
		$this->base.="/".$this->plugin;
		$this->cmd=array(
			"add"=>array("username","password","name","email"),
			"delete"=>array("username"),
			"disable"=>array("username"),
			"enable"=>array("username"),
			"update"=>array("username","password","name","email"),
			"add_roster"=>array("username","item_jid","name","subscription"),
			"update_roster"=>array("username","item_jid","name","subscription"),
			"delete_roster"=>array("username","item_jid")
		);
	}
	public function api($cmd,$param){
		if(isset($this->cmd[$cmd])){
			$data = $this->buildData($cmd,$param);
		}else{
			die("Method Not Exists");		
		}			
		$content = $this->post($data);
		return $content;		
	}
	private function buildData($cmd,$param){
		$data = "secret=".$this->secret."&type=".$cmd;
		if($this->isAssoc($param)){
			$data.="&".http_build_query($param);
		}else{
			$arr = $this->cmd[$cmd];
			for($i=0;$i<count($arr);$i++){
				if(isset($param[$i])){
					$data.="&".$arr[$i]."=".urlencode($param[$i]);				
				}			
			}
		}
		return $data;
	
	}
	private function post($data){
		if($this->curlEnable){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$this->base);
			curl_setopt($ch, CURLOPT_PORT,$this->port);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$res = curl_exec ($ch);
			curl_close ($ch);
		}else{
			$fopen = fopen($this->base."?".$data, 'r');
			$res = fread($fopen, 1024);
			fclose($fopen);
		}
		return $res;
	}
	private function isAssoc($array){
    		return array_keys($array) !== range(0, count($array) - 1);
	}
	

}
?>
