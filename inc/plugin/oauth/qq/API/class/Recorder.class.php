<?php
if (!defined('in_mx')) {exit('Access Denied');}
/* PHP SDK
 * @version 2.0.0
 * @author connect@qq.com
 * @copyright © 2013, Tencent Corporation. All rights reserved.
 */

require_once(CLASS_PATH."ErrorCase.class.php");
class Recorder{
    private static $data;
    private $inc;
    private $error;

    public function __construct(){
        $this->error = new ErrorCase();

        //-------读取配置文件
        global $ym_url;
        $ym_oauth =  get_cache('oauth', cache_static);        
        if(empty($ym_oauth) || empty($ym_oauth['qq'])){
        	 $this->inc['errorReport'] = true;
            $this->error->showError("20001");
        }
 
		$ym_oauth['qq']['errorReport'] = true;
        $ym_oauth['qq']['scope'] ="get_user_info";
		$ym_oauth['qq']['callback'] = $ym_url.'plugin.html?mod=oauth@qq@callback';
        $this->inc = $ym_oauth['qq'];

        if(empty($_SESSION['QC_userData'])){
            self::$data = array();
        }else{
            self::$data = $_SESSION['QC_userData'];
        }
    }

    public function write($name,$value){
        self::$data[$name] = $value;
    }

    public function read($name){
        if(empty(self::$data[$name])){
            return null;
        }else{
            return self::$data[$name];
        }
    }

    public function readInc($name){
        if(empty($this->inc[$name])){
            return null;
        }else{
            return $this->inc[$name];
        }
    }

    public function delete($name){
        unset(self::$data[$name]);
    }

    function __destruct(){
        $_SESSION['QC_userData'] = self::$data;
    }
}
