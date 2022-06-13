<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	
	
	class SpvActivity extends mysql{
	
		var $CustomerId;
		var $CampaignId;
		var $CallResult;
		var $CallNumber;
		var $Remarks;
		var $UserId;
		var $Action;
		
		
		/*
			calllaterdate : CallLaterDate,
			calllaterhour : CallLaterHour,
			calllatersec : CallLaterSec
		*/
		
		function __construct(){
			parent::__construct();
			
			if( $this ->havepost('action')){
				$this -> Action 	 = $this -> escPost('action');
				$this -> CustomerId  = $this -> escPost('customerid');
				$this -> CampaignId  = $this -> escPost('campaignid');
				$this -> CallResult  = $this -> escPost('callresult');
				$this -> CallNumber  = $this -> escPost('callnumber');
				$this -> Remarks     = $this -> escPost('callremarks');
				$this -> UserId 	 = $this -> getSession('UserId');
			}
			
			
		}
		
		function index(){
			switch( $this -> Action ){
				case 'save_confirm_valid'    : $this -> saveActivityCall(); break;
				case 'save_confirm_novalid'  : $this -> saveActivityCall(); break;
				case 'save_confirm_callback' : $this -> saveActivityCall(); break;
			}
		}
		
		/*
			action	save_confirm_valid
			callnumber	0226042881
			callremarks	ASSASSS
			callresult	1
			campaignid	1
			customerid	1

		*/
		
		function getResult($codec)
		{
			$result['401']= array(1=>16);
			$result['402']= array(1=>17);
			return $result[$codec][$this -> CallResult];
		}
		
		function isValidPolicy(){
				$sql = " select count(a.PolicyAutoGenId) 
						 from t_gn_policyautogen a where a.CustomerId='".$this -> escPost('customerid')."'";
						 
				$valid = $this -> valueSQL($sql);
				if( $valid > 0 ) : echo 1;
				else :
					echo 0;
				endif;	
		}
	
	
 /** save Activity Call **/

		private function getCallReasonId()
		{
			$sql = "select a.CallReasonId from t_gn_customer a where a.CustomerId='".$this -> escPost('customerid')."'";
			return $this -> fetchval($sql,__FILE__,__LINE__);
			
		}
		
 /** save Activity Call **/
	
		function saveActivityCall(){
				
			if( $this -> havepost('callresult')	):
				if( $this -> escPost('callresult')==1){
					$datas = array( 'CallReasonId' => $this -> getResult($_REQUEST['codec']));
					$where = array( 'CampaignId'=>$_REQUEST['campaignid'], 'CustomerId'=>$_REQUEST['customerid']);
					$query = $this -> set_mysql_update('t_gn_customer', $datas, $where);
					
					if( $query ):
						$CallResult = $this -> getResult($_REQUEST['codec']);
						if( $this -> saveHistoryCall( $CallResult ))  echo 1;
						else echo 0;
						
					endif;
					
				}
				
				else if( $this -> escPost('callresult')==2) {
					$CallResult = $this -> getCallReasonId();
					
					if( $this -> saveHistoryCall( $CallResult )){
						echo 1;
					}
					else
						echo 0;
				}
				
				else if( $this -> escPost('callresult')==3) {
					if( $this -> saveCallReminder()) :
						echo 1;
					else:
						echo 0;
					endif;	
				}
			endif;
		}
		
	/** save History Call **/	
		
		function saveHistoryCall($ResultCode=''){
			$CallHistory = array(
						'CustomerId' => $this -> CustomerId, 
						'CallReasonId' => $ResultCode,  
						'CreatedById' => $this -> UserId, 
						'CallNumber' => $this->CallNumber, 
						'UpdatedById' => $this->UserId, 
						'CallHistoryCallDate' => date('Y-m-d H:i:s'), 
						'CallHistoryNotes' => $this -> Remarks, 
						'CallHistoryCreatedTs' => date('Y-m-d H:i:s'), 
						'CallHistoryUpdatedTs' => date('Y-m-d H:i:s')
					);
					
			$queryHistory = $this -> set_mysql_insert("t_gn_callhistory",$CallHistory);
			if( $queryHistory ) : return true;
			else :
				return false;
			endif;	
		}
		
	/* function setCall Later **/
		
		function setCallLater(){
			if( $this -> havepost('calllaterdate') ):
				$call_later_date = $this -> formatDateEng($_REQUEST['calllaterdate'])." ".$_REQUEST['calllaterhour'].":".$_REQUEST['calllatersec'].":00";
				return $call_later_date;	
			else:
				return null;
			endif;
		}
		
	 /** call reminder **/

		function saveCallReminder(){
			$CallApoinment = array
			(
					'CustomerId' => $this -> CustomerId, 
					'UserId' => $this->UserId, 
					'ApoinmentDate' => $this -> setCallLater(), 
					'ApoinmentCreate' => date('Y-m-d H:i:s')
			);
					
			$queryApoinment = $this ->set_mysql_insert('t_gn_appoinment',$CallApoinment);
			
			if( $queryApoinment ) : return true;
			else : 
				return false;
			endif;
		}
	}
	
	$SpvActivity = new SpvActivity();
	$SpvActivity -> index();
?>