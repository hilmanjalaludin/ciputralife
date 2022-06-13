<?php

require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");

/** log out session from user ***/


class UserLogout extends mysql
{
	var $IP_Address;
	var $UserName;
	
	function UserLogout()
	{
		session_start();
		parent::__construct();
		if(isset($_SESSION))
		{
			$this -> IP_Address = $this -> getRealIpAddr();
			$this -> UserName   = $this -> getSession('username');
			
			self::initLogout();
		}	
	}
	
	public function initLogout()
	{
		if( $this -> UpdateTmsAgent() )
		{
			$this -> setLogHistory();
			$this -> disconnectDB();
			$this -> setDestroySession();
		}
	}
	
	private function UpdateTmsAgent()
	{
		$sql = " UPDATE tms_agent SET logged_state = '0', ip_address= null WHERE id='".$this -> UserName."'";
		$qry = $this -> execute($sql,__FILE__,__LINE__);
		
		if( $qry ) return true;
		else
			return false;
	}
	
	private function setLogHistory($activity='LOGOUT')
	{
		if( $this -> getSession('UserId')!=0 )
		{
			$SQL_Insert['UserId'] 		  	 = $this -> getSession('UserId');
			$SQL_Insert['ActivityLocation']  = $this -> getRealIpAddr();
			$SQL_Insert['ActivityAction'] 	 = $activity;
			$SQL_Insert['ActivitySessionId'] = session_id();
			$SQL_Insert['ActivityDateTs'] 	 = date('Y-m-d H:i:s');
				
			$qry = $this -> set_mysql_insert('tms_agent_activity',$SQL_Insert);
			if( $qry ) return true;
			else
				return false;
		}			
	}
	
	private function setDestroySession()
	{
		session_destroy();
	}
}	

$UserLogout = new UserLogout();


?>

<script language="JavaScript">
  parent.location="../index.php"
</script>