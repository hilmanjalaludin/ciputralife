<?php
require("../sisipan/sessions.php");
require("../fungsi/global.php");
require("../class/MYSQLConnect.php");
	
	
class SaveCounter extends mysql
{
	function SaveCounter()
	{
		parent::__construct();
		self::index();
	}
	
/** start index **/
	
	function index()
	{
		if( $this -> havepost('action'))
		{
			switch( $this -> escPost('CounterType') )
			{
				 case 'start_counter_ringing'   : $this -> SaveStartCounterRinging(); 	break;
				 case 'start_counter_connected' : $this -> SaveStartCounterConnect(); 	break;
				 case 'start_counter_acw'		: $this -> SaveStartCounterACW(); 		break;
				 case 'stop_counter_endtime' 	: $this -> SaveStopCounterACW(); 		break;
				 
			}
		}	
	}
	
/* start counter ***/
	
	function SaveStartCounterRinging()
	{
		$result = array('result'=>0, 'sessionCallId' => 0);
		
		if($this -> havepost('CallerNumber') )
		{
			$SQL_Insert['CallerNumber'] = $this -> escPost('CallerNumber');
			$SQL_Insert['CustomerId'] = $this -> escPost('CustomerId');
			$SQL_Insert['CallSession'] = $this -> escPost('SessionKey');
			$SQL_Insert['AgentId'] = $this -> getSession('UserId');
			$SQL_Insert['StartCallTs'] = date('Y-m-d H:i:s');
			$SQL_Insert['ConnectTs'] = date('Y-m-d H:i:s');
			$SQL_Insert['DisconnectTs'] = date('Y-m-d H:i:s');
			$SQL_Insert['EndCallTs'] = date('Y-m-d H:i:s');
			
			if( $this -> set_mysql_insert('t_gn_activitycall',$SQL_Insert))
			{
				$result = array
				(
					'result' => 0, 
					'sessionCallId' => $this -> escPost('SessionKey')
				);
			}
		}
		
		echo json_encode($result);
	
	}
	
/** stop counter ***/
	
	function SaveStartCounterACW()
	{
		$result = array('result'=>0, 'sessionCallId' => 0);
		
		if( $this -> havepost('SessionKey') )
		{
			$SQL_Update['DisconnectTs']  =  date('Y-m-d H:i:s');
			$SQL_Wheres['CallSession'] = $this -> escPost('SessionKey');
			
			if( $this -> set_mysql_update('t_gn_activitycall', $SQL_Update, $SQL_Wheres)){
				$result = array
				(
					'result' => 1, 
					'sessionCallId' => $this -> escPost('SessionKey')
				);
			}
		}
		
		echo json_encode($result);
	}
	
/** SaveStopCounterConnect **/
	function SaveStartCounterConnect()
	{
		$result = array('result'=>0, 'sessionCallId' => 0);
		
		if( $this -> havepost('SessionKey') )
		{
			$SQL_Update['ConnectTs']  =  date('Y-m-d H:i:s');
			$SQL_Wheres['CallSession'] = $this -> escPost('SessionKey');
			
			if( $this -> set_mysql_update('t_gn_activitycall', $SQL_Update, $SQL_Wheres)){
				$result = array
				(
					'result' => 1, 
					'sessionCallId' => $this -> escPost('SessionKey')
				);
			}
		}
		
		echo json_encode($result);
	}	
	
	
/* SaveStopCounterACW **/
	function SaveStopCounterACW()
	{
		$result = array('result'=>0, 'sessionCallId' => 0);
		if( $this -> havepost('SessionKey') )
		{
			$SQL_Update['EndCallTs']  =  date('Y-m-d H:i:s');
			$SQL_Wheres['CallSession'] = $this -> escPost('SessionKey');
			if( $this -> set_mysql_update('t_gn_activitycall', $SQL_Update, $SQL_Wheres)){
				$result = array
				(
					'result' => 1, 
					'sessionCallId' => $this -> escPost('SessionKey')
				);
			}
		}
		
		echo json_encode($result);
	}
	
}

new SaveCounter();


?>