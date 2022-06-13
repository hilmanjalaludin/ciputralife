<?php

class socketTelnet 
{
	/*
	 @ telnet host
	*/
	public $serverHost		= 'localhost';
	/*
	 @ telnet port
	*/
	public $serverPort		= '9800';
	public $command_string	= '';
	
	/*
	 @ Error string 
	*/
	public $error_string	= '';
	
	/*
	 @ header replay messages
	*/
	private $messages_replay	= '';
	
	/*
	 @ Body Of class
	 @ socketTelnet
	*/
	function __construct(){}
	
/** sdet mgr host and port ****/
	
	function open_socket()
	{
		$fp = fsockopen($this->serverHost, $this->serverPort, $err_no, $err_str, 10);
		return $fp;
	}
	
/** sdet mgr host and port ****/
	
	function set_fp_server($ManagerHost='localhost',$ManagerPort = 9800 )
	{
		if( !empty($ManagerHost) ) $this -> serverHost = $ManagerHost;
		if( !empty($ManagerPort) ) $this->serverPort = $ManagerPort;
	}	
	
		
	
/** return of function string ***/
	
	function get_fp_response()
	{
		if( !empty($this -> messages_replay) )
		{
			return $this -> messages_replay;
		}
		else
			return 0;
	}	
	
/** function sset sent command **/

	function set_fp_command( $command='' )
	{ 
		if( $command!='' )
		{
			$this -> command_string = $command;  
		}	
	}	
	
/** function sset sent command **/

	function fp_get_command()
	{ 
		if( !empty( $this -> command_string) )
		{
			return $this -> command_string;
		} 
		else
			return NULL;
	}	
	
/** comander ***/
	
	function send_fp_comand()
	{
		$fp = $this -> open_socket();
		
		if(!$fp)
		{
			$this -> error_string ='Server connection error ';
			$this -> messages_replay = false;
		}
	  
	    fwrite($fp, $this -> fp_get_command(), strlen( $this -> fp_get_command() ));
	   
	    if(function_exists('stream_set_timeout')){
			stream_set_timeout($fp,5);
	    }
	    else{
			socket_set_timeout($fp,5);
	    }	
		
		
		$this->messages_replay ='';
		while(false!==($char = fgetc($fp))){
		/* call back */
			if ($char == "\n") break;
			$this->messages_replay.=$char;
		}
		$headers  				= explode(" ", $this->messages_replay);
		$size 	  				= $headers[1];
		$cnt 	  				= 0;
		$this->messages_replay  = 0;
		$char = fgetc($fp);
		
		if( is_array($headers) )
		{
			if( $size=='OK' ) $this->messages_replay = true; 	
			else
			{
				$this->messages_replay = false;
			}
		}
	   return $this->messages_replay;
	}
	
}

?>