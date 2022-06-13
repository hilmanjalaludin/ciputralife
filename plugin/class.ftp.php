<?php

class FTP {

	var $IP_Remote;
	var $US_Remote;
	var $PW_Remote;
	var $PO_Remote;
	var $FP_Buffer;
	var $FP_Login;
	var $FP_Connect;
	var $FP_get_dir;
	var $FP_put_dir;
	var $FP_patern;
	
	function __construct($__host='127.0.0.1', $__user='animous', $__pwd='',$__port=21 ){
		$this -> IP_Remote = $__host;
		$this -> US_Remote = $__user;
		$this -> PW_Remote = $__pwd;
		$this -> PO_Remote = $__port;
	}
	
	function __FTP_Setup($Options=''){
		if( is_array($Options))
		{
			$this -> FP_get_dir = $Options['get_dir'];
			$this -> FP_put_dir = $Options['put_dir'];
			$this -> FP_patern  = $Options['patern'];
			$this -> __FTP_Mode($Options['mode']);
		}	
	}
	
/* void @ coonect ftp */

	function __FTP_Mode($Mode)
	{
		
		switch($Mode):
			case 'get' : $this -> __FTP_Download(); break;
			case 'put' : $this -> __FTP_Upload(); 	break;
		endswitch;
	}
		
 /* void @ coonect ftp */
  
	function __FTP_Connect(){
	
		$this -> FP_Connect = ftp_connect($this -> IP_Remote, $this -> PO_Remote);
		if( $this -> FP_Connect ):
			$this -> __FTP_Login();
		endif;
	}
	
 /* void @ coonect ftp */
   
	function __getFTP_Connect(){
		if( $this -> FP_Connect  ) :
			return $this -> FP_Connect;
		else :
			echo " Connection Failure ";
		endif;	
	}
	
 /* void @ coonect ftp */
   
	function __FTP_Login()
	{
		$this -> FP_Login = false;
		if( $this -> __getFTP_Connect() ):
			$this -> FP_Login = ftp_login($this -> FP_Connect, $this -> US_Remote, $this -> PW_Remote); 
		endif;
	}
	
 /** void @ __FTP_Download **/
	
	function __FTP_Download(){
		if( $this -> FP_Login )
		{
			ftp_chdir($this -> FP_Connect, $this-> FP_get_dir);
			$content  = ftp_nlist($this -> FP_Connect,'.');
			$FContent = array();
			if( is_array($content) ){
				foreach($content as $k => $v )
				{
					$FP_file = explode('.',$v);
					if( ($this -> FP_patern == $FP_file[1]) ){
						$FContent[] = $v; 
					}	
				}
			}
			
			foreach($FContent as $i => $list)
			{	
				if( ftp_get($this -> FP_Connect, $this -> FP_put_dir.'/'.$list, $list, FTP_BINARY))
				{
					$var_log_date = date('Ymd')."_".date('H:i')."";
					ftp_rename($this -> FP_Connect,$list, 'SUCCESS_'.$list.'_'.$var_log_date.'.log');
				}
				else
					echo "Failed";
			}	
		}
	}
}


