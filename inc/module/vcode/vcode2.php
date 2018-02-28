<?php
if (!defined('in_mx')) {exit('Access Denied');}

header("Pramga: no-cache"); 
class Imagecode{
	private $width ;
	private $height;
	private $counts;
	private $distrubcode;
	private $fonturl;
	private $session;
	
	function __construct($width = 120,$height = 35, $counts = 5, $distrubcode="123546789QwertyupkjhgfdaszxcvbnmABCDEFGHJKLMNPRSTUVWXYZ",$fonturl="./inc/class/TektonPro-BoldCond.otf"){
		$this->width=$width;
		$this->height=$height;
		$this->counts=$counts;
		$this->distrubcode=$distrubcode;
		$this->fonturl=$fonturl;
		$this->session= get_salt($counts, false);
		session_start();
		$_SESSION['vcode']=$this->session;
	}
	
	function imageout(){
	 	global $ym_client;
		$im=$this->createimagesource();
		$this->setbackgroundcolor($im);
		$this->set_code($im);
		$this->setdistrubecode($im);
		/*if($ym_client == client_app)
		{
			$filename = $ym_client != client_app ?'' :'vcode'; 
			ImageGIF($im,$filename); 
		}
		else */{
			ImageGIF($im,$filename);
			ImageDestroy($im);
		}		
	}
	
	private function createimagesource(){
		return imagecreate($this->width,$this->height);
	}
	private function setbackgroundcolor($im){
		$bgcolor = ImageColorAllocate($im, rand(200,255),rand(200,255),rand(200,255));//背景颜色
		imagefill($im,0,0,$bgcolor);
	}
	
	private function setdistrubecode($im){
		$count_h=$this->height;
		$cou=floor($count_h*2);
		for($i=0;$i<$cou;$i++){
			$x=rand(0,$this->width);
			$y=rand(0,$this->height);
			$jiaodu=rand(0,360);
			$fontsize=rand(5,10);
			$fonturl=$this->fonturl;
			$originalcode = $this->distrubcode;
			$countdistrub = strlen($originalcode);
			$dscode = $originalcode[rand(0,$countdistrub-1)];
			$color = ImageColorAllocate($im, rand(40,140),rand(40,140),rand(40,140));
			imagettftext($im,$fontsize,$jiaodu,$x,$y,$color,$fonturl,$dscode);			
		}
	}
	
	private function set_code($im){
			$width=$this->width;
			$counts=$this->counts;
			$height=$this->height;
			$scode=$this->session;
			$y=floor($height/2)+floor($height/4);
			$fontsize=rand(30,35);
			$fonturl=$this->fonturl;//$this->fonturl;
			
			$counts=$this->counts;
			for($i=0;$i<$counts;$i++){
				$char=$scode[$i];
				$x=floor($width/$counts)*$i+8;
				$jiaodu=rand(-20,30);
				$color = ImageColorAllocate($im,rand(0,50),rand(50,100),rand(100,140));
				imagettftext($im,$fontsize,$jiaodu,$x,$y,$color,$fonturl,$char);
			}
	}
}




?>