<?php
	require_once("../sisipan/sessions.php");
	require_once("../fungsi/global.php");
	require_once("../class/MYSQLConnect.php");
	require_once("../class/lib.form.php");
	
	class MonRec extends mysql{
		
		function __construct(){
			parent::__construct();
		}
		
		function index()
		{
			if( $this -> havepost('action'))
			{
				switch($_REQUEST['action'])
				{
					case 'get_agent_byspv'	 : $this -> getAgentBySpv(); 	break;
					case 'get_data_null'  	 : $this -> getDataNull(); 		break;
					case 'get_fromspv_byam'	 : $this -> getSpvFromAM();		break;	
					case 'get_tospv_byam'	 : $this -> getSpvToAM(); 		break;
					case 'get_toagent_byspv' : $this -> getToAgentBySpv(); 	break;
					case 'move_to_spv'    	 : $this -> MoveToSPV(); 		break;
					case 'move_to_agent'	 : $this -> MoveToAgent(); 		break;
					case 'move_to_mgr'	 	 : $this -> MoveToMgr(); 		break;	
					case 'get_data_user'	 : $this -> getUserData();		break;
					case 'get_ajax_data'	 : $this -> getAjaxData();		break;
					
				}
			}
		}
		
		function getUserData()
		{
			$sql = "SELECT * from tms_agent a where a.UserId='".$_REQUEST['UserId']."' AND a.profile_id='".$_REQUEST['handle']."'";
			$qry = $this -> query($sql);
			if( !$qry -> EOF() )
			{
				echo json_encode( $qry -> result_first_assoc() );
			}
		}
		
		
	}
	
	$MonRec = new MonRec(true);
	$MonRec -> index();
?>