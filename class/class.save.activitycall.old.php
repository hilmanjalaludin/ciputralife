<?php
require("../sisipan/sessions.php");
require("../fungsi/global.php");
require("../class/MYSQLConnect.php");
class CallActivity extends mysql
{
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
		
		function CallActivity()
		{
			parent::__construct();
			
			if( $this ->havepost('action'))
			{
				$this -> Action 	 = $this -> escPost('action');
				$this -> CustomerId  = $this -> escPost('customerid');
				$this -> CampaignId  = $this -> escPost('campaignid');
				$this -> CallResult  = $this -> escPost('callresult');
				$this -> CallNumber  = $this -> escPost('callnumber');
				$this -> Remarks     = $this -> escPost('callremarks');
				$this -> UserId 	 = $this -> getSession('UserId');
			
				if(in_array($this -> escPost('callresult'), $this -> Entity -> getCallBack()))
				{
					$this -> saveCallReminder();
				}
			}
		}
		
	/** index data **/
	
		function index()
		{
			switch( $this -> Action )
			{
				case 'save_activity_call' 	:  $this -> saveActivityCall(); 	break;
				case 'isvalidPolicy' 		:  $this -> isValidPolicy(); 		break;
				case 'get_hirarki_status'	:  $this -> CallHirarkiStatus();  	break;
			}
		}
		
		
	/** cek validation **/	
	
		function isValidPolicy()
		{
			$sql = " select count(a.PolicyAutoGenId) 
					 from t_gn_policyautogen a where a.CustomerId='".$this -> escPost('customerid')."'";
			//echo $sql;
				$valid = $this -> valueSQL($sql);
				if( $valid > 0 ) : echo 1;
				else :
					echo 0;
				endif;	
		}
	
		function getCallReasonCode($callid='')
		{
			if ($callid!='') {
				$sql = "select r.CallReasonCode from t_lk_callreason r
						where r.CallReasonId='$callid'";
				$qry = $this -> query($sql);
				$val = (INT)$qry -> result_get_value('CallReasonCode');
				return $val;
			}
		}

		function CallHirarkiStatus()
		{

			$CallReasonId = $this -> escPost('CallReasonId');
			$LevelReason  = $this -> Entity -> LevelReason($CallReasonId);
			$callcode=$this->getCallReasonCode($CallReasonId);

			// if ($callcode==999) {
			// 	$array_success = array('success'=>1);
			// 	echo json_encode($array_success);
			// 	die();
			// }

			$array_success = array('success'=>0);
			/*$sql = "SELECT h.CallHistoryId, h.CallReasonId, a.CustomerId, b.CallReasonLevel as CallReasonLevel
					 FROM t_gn_customer a 
					 left join t_gn_callhistory h on h.CustomerId=a.CustomerId
					 LEFT JOIN t_lk_callreason b on h.CallReasonId=b.CallReasonId
					 -- LEFT JOIN t_lk_callreasoncategory c on b.CallReasonCategoryId=c.CallReasonCategoryId  
					 WHERE a.CustomerId= '".$this -> escPost('CustomerId')."' 
					 AND b.CallReasonCode <> 999
					 order by h.CallHistoryId desc
					 limit 0,1 ";*/

			$sql = "SELECT a.CallReasonId , b.CallReasonLevel as CallReasonLevel
					 FROM t_gn_customer a 
					 LEFT JOIN t_lk_callreason b on a.CallReasonId=b.CallReasonId
					 LEFT JOIN t_lk_callreasoncategory c on b.CallReasonCategoryId=c.CallReasonCategoryId
					 WHERE a.CustomerId= '".$this -> escPost('CustomerId')."'
					 GROUP BY a.CustomerId ";	

			$qry = $this -> query($sql);
			$cnt = $qry -> result_num_rows($qry);
			// if ($cnt == 0) {
			// 	$array_success = array('success'=>1);
			// }
			// else if($LevelReason >= (INT)$qry -> result_get_value('CallReasonLevel'))
			// {
			// 	$array_success = array('success'=>1);
				
			// }
			if ($cnt == 0) {
				$array_success = array('success'=>1);
			}
			else if(3 == (INT)$qry -> result_get_value('CallReasonLevel') or
				4 == (INT)$qry -> result_get_value('CallReasonLevel'))
			{
				$array_success = array('success'=>0);
			}
			else 
			{
				$array_success = array('success'=>1);
				
			}  
			
			echo json_encode($array_success);
		}
 /** save Activity Call **/

		private function getCallReasonId()
		{
			$sql = "select a.CallReasonId from t_lk_callreason a where a.CallReasonId = '".$this ->escPost('callresult')."'";
			return $this -> fetchval($sql,__FILE__,__LINE__);
		}
		
 /** save Activity Call **/
	
		function saveActivityCall()
		{
			$QualityId = $this -> Entity -> getEskalasiStatus(USER_QUALITY, USER_TELESALES);	
			
			if( ($this -> havepost('verifiedStatus')) && in_array($this ->escPost('verifiedStatus'), array_keys($QualityId)) )
			{
				$SQL_insert['CallReasonId'] 	 = $this -> getCallReasonId(); 
				$SQL_insert['SellerId'] 		 = $this -> UserId;
				$SQL_insert['CallReasonQue'] 	 = $this -> Entity -> VerifiedConfirm();
				$SQL_insert['CustomerUpdatedTs'] = date('Y-m-d H:i:s');
			}
			else{
				$SQL_insert['CallReasonId']   	 = $this -> getCallReasonId(); 
				$SQL_insert['SellerId'] 	 	 = $this -> UserId;
				$SQL_insert['CustomerUpdatedTs'] =  date('Y-m-d H:i:s');
			}
			
			$SQL_wheres['CustomerId'] = $this -> CustomerId; 
			$SQL_wheres['CampaignId'] = $this -> CampaignId;
			if( $this -> set_mysql_update('t_gn_customer', $SQL_insert, $SQL_wheres) )
			{
				$this -> saveHistoryCall();
				echo 1;
			}
			else{ 
				echo 0;
			}
		}
		
	/** save History Call **/	
		
		function saveHistoryCall()
		{
			$CallHistory = array
			(
						'CustomerId' => $this -> CustomerId, 'CallReasonId' => $this -> getCallReasonId() , 
						'CreatedById' => $this -> UserId, 'CallNumber' => $this->CallNumber, 
						'UpdatedById' => $this->UserId, 'CallHistoryCallDate' => date('Y-m-d H:i:s'), 
						'CallHistoryNotes' => $this -> Remarks, 'CallHistoryCreatedTs' => date('Y-m-d H:i:s'), 
						'CallHistoryUpdatedTs' => date('Y-m-d H:i:s')
					);
					
			$queryHistory = $this -> set_mysql_insert("t_gn_callhistory",$CallHistory);
			if( $queryHistory )  return true;
			else
			{
				return false;
			}	
		}
		
	/* function setCall Later **/
		
		function setCallLater()
		{
			if( $this -> havepost('calllaterdate') ){
				$call_later_date = $this -> formatDateEng($_REQUEST['calllaterdate'])." ".$_REQUEST['calllaterhour'].":".$_REQUEST['calllatersec'].":00";
				return $call_later_date;	
			}	
			else{
				return null;
			}
		}
		
	 /** call reminder **/

		function saveCallReminder()
		{
			$CallApoinment = array
			(
				'CustomerId' => $this -> CustomerId, 
				'UserId' => $this->UserId, 
				'ApoinmentDate' => $this -> setCallLater(), 
				'ApoinmentCreate'=> date('Y-m-d H:i:s')
			);
					
			$queryApoinment = $this ->set_mysql_insert('t_gn_appoinment',$CallApoinment);
			
			if( $queryApoinment ) return true;
			else{
				return false;
			}
		}
	}
	
	$CallActivity = new CallActivity();
	$CallActivity -> index();
?>