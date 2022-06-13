<?php

 require_once("../sisipan/sessions.php");
 require_once("../fungsi/global.php");
 require_once("../class/MYSQLConnect.php");
	
/* 
 * class transfer by customer extends mysql
 * untuk action yang berhubungan
 * dengan distribusi
 * author : omens
 */
 
 /*
	@ POST PARAMETER :
		action :'distribusi_by_customer', ((str))
		campaign_id : campaign_id,  ( ((str)))
		agent_id : agent_id, ((str))
		cust_id : cust_id,((str))
 */

 class 	CustomerTransfer extends mysql{
	
	var $Action;
	var $CampaignId;
	var $AgentId;
	var $UserId;
	var $CustomerId;
	var $TransferType;
	
	
	function __construct(){
		parent::__construct();
		$this -> Action 	  = $this -> escPost('action');
		$this -> CampaignId   = $this -> escPost('campaign_id');
		$this -> AgentId 	  = $this -> escPost('agent_id');
		$this -> UserId 	  = $this -> escPost('user_id');
		$this -> CustomerId   = explode(',',$this -> escPost('cust_id'));
		$this -> TransferType = $this -> escPost('transfer_type'); 
		
	}
	
	function index(){
		if( $this->havepost('action')){
			switch($this -> Action){
				case 'transfer_by_customer'	 :  $this -> transferByCustomer(); break;
				case 'save_dist_bymount'	 :  $this -> saveDistByMount(); 	 break;
			}
		}
		
	}
	
	private function getAssignColumns(){
		$array_field = array('1'=>'AssignMgr', '2'=>'AssignSpv','3'=>'AssignSelerId');
		
		if($this -> getSession('handling_type')!='' ):
			return $array_field[$this -> getSession('handling_type')];
		else:
			return false;
		endif;
	}
	
/* function update on customer table **/

   private function UpdateTgnCustomers($customerid='',$sellerId){
		if( $customerid!=''):
			$sql = "Update t_gn_customer Set SellerId='$sellerId' where CustomerId='$customerid'";
			$q = $this -> execute($sql,__FILE__,__LINE__);
			if( $q ) return true;
			else return false;
		else:
			return false;
		endif;
	}	
	
	private function insertTgndistlog($customerid='',$sellerId,$UserId){
		 Global $db;
		 $sesi = $this -> getSession('UserId');
		 if( $customerid!=''):
		 $sql = "INSERT INTO t_gn_distribusi_log (LogAssignmentId, LogUserId, LogAssignUserId, LogCreatedDate, FromTrfMenu, LogTransferFrom)
				VALUES ($customerid, $sellerId, $sesi, now(), '1', $UserId)";
		 //echo $sql;
		 $q = $this -> execute($sql,__FILE__,__LINE__);
		 if( $q ) return true;
		 else return false;
		 else:
		 return false;
		 endif;
	}	
	
	
/** by customers **/
	
	function transferByCustomer(){
		$i=0;
		foreach($this -> CustomerId as $key => $customerid )
		{
			$Columns = $this -> getAssignColumns();
			$AgentId = $this -> AgentId;
			$UserId	 = $this -> UserId;
			
			if( ($this -> getSession('handling_type')==1) )
			{
				$sql = " UPDATE t_gn_assignment a 
						 SET a.$Columns = $AgentId, a.AssignDate = NOW(), 
							 a.AssignMode = 'MOV', a.AssignSelerId = NULL
						 WHERE a.CustomerId = $customerid ";
			}
			else if ( ($this -> getSession('handling_type')==2) )
			{
				$sql = " UPDATE t_gn_assignment a 
						 SET  a.$Columns = $AgentId, a.AssignDate = NOW(), 
							  a.AssignMode = 'MOV', a.AssignSelerId = NULL
						 WHERE a.CustomerId = $customerid ";
			}
			else if ( ( $this -> getSession('handling_type')==3 )  ) 
			{
				$sql = " UPDATE t_gn_assignment a 
						 SET a.$Columns = $AgentId,  a.AssignDate = NOW(), 
							 a.AssignMode = 'MOV'
						 WHERE a.CustomerId = $customerid ";
			}	
			
		////////////////////////////////////////////////////////////////////////	
		////////////////////////////////////////////////////////////////////////	
		
			if( $this -> UpdateTgnCustomers($customerid, $this -> AgentId) ):
				$Query = $this -> execute($sql,__FILE__,__LINE__); //$this->set_mysql_update('t_gn_assignment',$datas,$where);
				if( $Query ) $i++;	
			endif;
			if( $this -> insertTgndistlog($customerid, $this -> AgentId, $UserId) ):
				$Query = $this -> execute($sql,__FILE__,__LINE__); 
				if( $Query ) $i++;	
			endif;
			
		}
		
		if( $i>0 ) { 
			$this -> activityLog("Transfer data to SellerId");
			echo 1; 
		}
		else echo 0;
	}
 }
 
 $CustomerTransfer = new CustomerTransfer();
 $CustomerTransfer -> index();
 
?>
