<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../sisipan/parameters.php");
	
 /* User chat Class **/
 
	class UserChat extends mysql{
	
		var $vagent;
		var $handlingtype;
		var $username;
		var $mgrid;
		var $usergroup;
		
		function __construct(){
			parent::__construct();
			
			$this -> username	  	= $this -> getSession('username');
			$this -> handlingtype 	= $this -> getSession('handling_type');
			$this -> mgrid			= $this -> getSession('user_group');
			$this -> usergroup		= $this -> getSession('mgr_id');
		}
		
		function initChat(){
			if( isset( $this -> handlingtype) ):
				switch( $this -> handlingtype )
				{
					case 1: $this -> getAllAgent(); 		break;
					case 2: $this -> getAgentByGroups();	break;
					case 3: $this -> getAgentBySpv(); 		break;
					case 4: $this -> getAgentByAgent(); 	break;
					case 5: $this -> getAllAgent(); 		break;
				}
				
				$this -> chatList();
			endif;
			
			
		}
		
		function getAllAgent(){
			$sql = " select id,full_name  from tms_agent  WHERE UserId!='".$this -> getSession('UserId')."'";
			$res = $this ->valueSqlLoop($sql);
			if( $res ):
				$this -> vagent = $res;
			endif;
		}
		
		
		function getAgentByQC(){
			$sql = " select id,full_name  from tms_agent WHERE user_state='1' and logged_state='1' and UserId!='".$this -> getSession('UserId')."'";
			$res = $this ->valueSqlLoop($sql);
			if( $res ):
				$this -> vagent = $res;
			endif;
		}
		
		function getAgentByGroups(){
			$sql = " select id,full_name  from tms_agent where 1=1
						AND ( mgr_id='".$this->getSession('UserId')."' 
						OR handling_type in('1','3','4','5')  )
						and UserId!='".$this->getSession('UserId')."' 
						and logged_state='1' ";
			$res = $this ->valueSqlLoop($sql);
				if( $res ):
					$this -> vagent = $res;
				endif;
		}
		
		function getAgentBySpv(){
			$sql = " select id,full_name  from tms_agent where handling_type='4' 
						and logged_state='1' and user_state=1
						and spv_id='".$this->getSession('spv_id')."' 
						and UserId!='".$this -> getSession('UserId')."'"; 
			$res = $this ->valueSqlLoop($sql);
				if( $res ):
					$this -> vagent = $res;
				endif;
		}
		
		function getAgentByAgent(){
		
			$sql = " select id,full_name  from tms_agent where handling_type='3' 
						and logged_state='1'  and user_state=1
						and spv_id='".$this -> getSession('spv_id')."'
						and UserId!='".$this -> getSession('UserId')."'"; 
						
			$res = $this ->valueSqlLoop($sql);
				if( $res ):
					$this -> vagent = $res;
				endif;
		}
		
		function chatList(){
			if( isset( $this -> vagent ) ):
				foreach($this -> vagent as $vAgentOnline){
					echo "<li class=\"chatLine\">
							<a href=\"javascript:void(0)\" 
							onClick=\"javascript:chatWith('".$vAgentOnline[0]."')\">(".$vAgentOnline[0].") "
															.$vAgentOnline[1]."</a>
						</li>";
				}
			endif;
		}
	}
	
	$UserChat = new UserChat(true);
	$UserChat -> initChat();
?>
