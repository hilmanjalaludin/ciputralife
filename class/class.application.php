<?php
class application
{

var $pattern;
var $address;
var $scriptdir;
var $scriptfile;
	

/*
 * @ def 	: get instance of class 
 *
 * @ params : one akses level
 */

private static $instance = null;
public static function &get_instance()
{
  if(is_null(self::$instance) ){
	self::$instance = new self();
  }
  return self::$instance;
}

		
function __construct()
{
	$this -> pattern    = $_SERVER;
	$this -> address    = $this -> pattern['HTTP_HOST'];
	$this -> scriptdir  = $this -> pattern['SCRIPT_NAME'];
	$this -> scriptfile = $this -> pattern['SCRIPT_FILENAME'];
 }
 		
function base_app()
{
	$e_base_app = '';
		if( !empty($this -> scriptdir )):
			$e_script   = explode( '/', $this -> scriptdir );
			for($i=0; $i< (count($e_script)-1); $i++):
				$e_base_app.= $e_script[$i].'/';	
			endfor;
		endif;
		
		return ('http://'.$this -> address .$e_base_app);
	}

	
function setMaskText($maskTek="",$type="",$length= 3)
{
	if($type=='') $type='x';
		$ft  = strlen($maskTek)-$length;
		$str.= substr($maskTek,0,$ft);
		//$fv  = strlen($str)-3;
		
		for ($i=$ft+1; $i<=strlen($maskTek); $i++){
			$str.=$type;	
		}
		return $str."";//substr($maskTek,-0);//,strlen($maskTek));
	}
	
function basePath()
{
	$e_base_app = '';
		if( !empty($this -> scriptdir )):
			$e_script   = explode( '/', $this -> scriptdir );
			for($i=0; $i< (count($e_script)-2); $i++):
				$e_base_app.= $e_script[$i].'/';	
			endfor;
		endif;
		
		return ('http://'.$this -> address .$e_base_app);
	}	
function readjs(){

}
		
function page_index(){
		if(!empty($this -> address)):
			$e_page_index = ('http://'.$this -> address.$this -> scriptdir);
			if( $e_page_index ) : return $e_page_index; endif;
		endif;
	}
	
function issetSession(){
	session_start();
	if( $_SESSION['UserId']!=''){
		header('location:'.$this->base_app().'include/main.php');
	}	
}	
		
function base_dir(){
			
	}
}

$app = new application();

