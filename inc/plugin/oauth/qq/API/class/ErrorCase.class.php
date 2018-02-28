<?php
if (!defined('in_mx')) {exit('Access Denied');}
/* PHP SDK
 * @version 2.0.0
 * @author connect@qq.com
 * @copyright © 2013, Tencent Corporation. All rights reserved.
 */

require_once(CLASS_PATH."Recorder.class.php");

/*
 * @brief ErrorCase类，封闭异常
 * */
class ErrorCase{
    private $errorMsg;

    public function __construct(){
        $this->errorMsg = array(
            "20001" => "<h3>QQ登录未配置，请在后台【系统】-【第三方登录】里配置</h3>",
            "30001" => "<h3>The state does not match.</h3>",
            "50001" => "<h3>可能是服务器无法请求https协议</h3><br>可能未开启curl支持,请尝试开启curl支持，重启web服务器"
            );
    }

    /**
     * showError
     * 显示错误信息
     * @param int $code    错误代码
     * @param string $description 描述信息（可选）
     */
    public function showError($code, $description = '$'){
      /*  $recorder = new Recorder();
        if(! $recorder->readInc("errorReport")){
           die();
        }*/

        echo "<meta charset=\"UTF-8\">";
        if($description == "$"){
            die($this->errorMsg[$code]);
        }else{
            echo "<h3>error:</h3>$code";
            echo "<h3>msg  :</h3>$description";
            exit(); 
        }
    }
    public function showTips($code, $description = '$'){
    }
}
