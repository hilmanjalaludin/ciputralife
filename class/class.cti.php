<?php
	class CTI extends mysql{
		var $username;
		function __construct($username=''){
			parent::__construct();
			$this->username = $username;
		}
		
		function initExt()
		{
			$sql = "SELECT * FROM cc_agent WHERE userid = '".$this->username."'";
			$res = $this->execute($sql,__FILE__,__LINE__);	
			
			if ($res && ($row = $this->fetchrow($res))){
				$this -> setSession("agentid", $row->id);
				$this -> setSession("user_name", $row->userid);
				$this -> setSession("agentgroup", $row->agent_group);
				$this -> setSession("login_status",$row->login_status);
			}
		}
		
		function mgrSendMsg($mgrHost, $mgrPort, $msg){
				$fp = fsockopen($mgrHost, $mgrPort, $errno, $errstr, 10);
			  if (!$fp) {
				echo "Server connection error";
				return;
			  }

			  fwrite($fp, $msg, strlen($msg));

			  if (function_exists('stream_set_timeout'))
				stream_set_timeout ( $fp, 5);
			  else
				socket_set_timeout( $fp, 5);

				/* read reply header */
			  $msgReply = "";
			  while (false !== ($char = fgetc($fp))) {
				if ($char == "\n")
					break;
				$msgReply .= $char;
				}
				
			  $headers = explode(" ", $msgReply);
			  $size = $headers[2];
			  $cnt = 0;
			  $msgReply = "";

				/* read reply content */
			  while ($cnt < $size && (false !== ($char = fgetc($fp)))) {
				$msgReply .= $char;
				$cnt++;
			  }

			  fclose($fp);
				return $msgReply;
		}
		
	

		function includeCTI()
		{
			$agentId				= $this -> getSession('agentid');
			$agentExt				= $this -> getSession('user_ext');
			$agentIpAddress 		= $_SERVER['REMOTE_ADDR'];
			$pbxId					= 0;

			$sql = "select * from cc_agent where userid = '".$this->getSession('username')."'";
			$res = $this->execute($sql,__FILE__,__LINE__);

			if (!$agentId) die("Invalid agent id");

			if ($agentExt){
				$dynamicIp = true;
					$this->setSession('agentExt',$agentExt);
					
				$sql = "SELECT a.pbx FROM cc_extension_agent a "
							."WHERE a.ext_number = '$agentExt'";
				$res = $this->execute($sql,__FILE__,__LINE__);
				
				if ($row = @mysql_fetch_row($res)):  
					$pbxId = $row[0];
				endif;
				
			}else{
		
		/* agent extension not suplied, get extension by agent ip */
				
				$sql = "SELECT a.ext_number, a.pbx FROM cc_extension_agent a WHERE a.ext_location = '$agentIpAddress'";
				$res = $this->execute($sql,__FILE__,__LINE__);
				if ($row = @mysql_fetch_row($res)){
						$this->setSession('agentExt',$row[0]);
						$pbxId= $row[1];
				}else			
					die("Ip-Address not registered [$agentIpAddress]");
			}

		/* GET Instance ID */
			
			$sql = "SELECT instance_id FROM cc_settings ".
				   "WHERE set_modul='cti' AND set_name='pbx.id' AND set_value='$pbxId'";
			$res = $this->execute($sql);
			if ($row = @mysql_fetch_row($res))
				$instanceId = $row[0];
			else
				$instanceId = 0;
			if($res)mysql_free_result($res);

		/* read app settings */
			
			$sql = "SELECT set_name, set_value FROM cc_settings ".
					"WHERE set_modul = 'agent' AND instance_id='$instanceId' ";
			$res = $this->execute($sql);
			while ($row = @mysql_fetch_row($res)){	
				if ($row[0] == "server.host"){
					$this->setSession('ctiIp',$row[1]);
				}
				else if ($row[0] == "server.port"){
					$this->setSession('ctiUdpPort',$row[1]);
			    }
			}
			if($res)mysql_free_result($res);

		/* read pbx settings */
			
			$sql = "SELECT set_name, set_value FROM cc_pbx_settings WHERE pbx = '$pbxId'";
			$res = $this->execute($sql);
			while ($row = @mysql_fetch_row($res)){	
				if($row[0] == "tac"){
					$this->setSession('pbxTAC',$row[1]);
				}
			}
			if($res)mysql_free_result($res);

		/* manager settings */
			
			$sql = "SELECT set_name, set_value FROM cc_settings ".
				   "WHERE set_modul = 'manager' AND instance_id='$instanceId' ";
			$res = $this->execute($sql);
			while ($row = @mysql_fetch_row($res)){	
			  if($row[0] == "server.host"){
					$managerHost = $row[1];
			  }
			  else if ($row[0] == "server.port"){
					$managerPort = $row[1];
			  }
			}
			if($res)mysql_free_result($res);

			$sql = " SELECT a.id, a.userid , a.name, a.occupancy, now() 'login_time', a.agent_group "
				  ." FROM cc_agent a, cc_agent_group b "
				  ." WHERE a.id = '$agentId' and a.agent_group = b.id";
				  
				  
			
			$res =$this->execute($sql,__FILE__,__LINE__);
				
			if ($row = @mysql_fetch_row($res)){
				session_start();
				
					$this -> setSession('agentId',$row[0]);
					$this -> setSession('agentLogin',$row[1]);
					$this -> setSession('agentName',$row[2]);
					$this -> setSession('agentLevel',$row[3]);
					$this -> setSession('agentLoginTime',$row[4]);
					$this -> setSession('agentGroup',$row[5]);
			  
					mysql_free_result($res);
			}else{
				echo " Agent not found";
		
		/** agent id not found, assume we have new agent, insert to DB */
		
				$defaultGroup = 1;
				$defaultLevel = 1;
				$sql = "INSERT INTO agent (userid, name, occupancy, agent_group)"
					  ."VALUES ('$agentId', '$agentId', $defaultLevel, $defaultGroup)";
				$res =$this->execute($sql,__FILE__,__LINE__);
				$agentRecordId = mysql_insert_id();
				  
				  
				  $this -> setSession('agentId',$agentRecordId);
				  $this -> setSession('agentLogin', $agentId);
				  $this -> setSession('agentName', $agentId);
				  $this -> setSession('agentLevel',$defaultLevel);
				  $this -> setSession('agentLoginTime',time());
				  $this -> setSession('agentGroup',$defaultGroup);
				  
		 /* add default skill to this agent */
			 
			}
		}
		
	/** get acess telphone by user handling **/
	
		function getTelphone()
		{
			$sql = "select a.telphone from tms_agent a where a.id='".$this->username."'";
			$qry = $this -> query($sql);
			if( $qry -> result_singgle_value() > 0 ) return true;
			else
				return false;
		}
	}
?>