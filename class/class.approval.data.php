<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	
	class Approval extends mysql{
		var $action;
		
		function __construct(){
			parent::__construct();
			$this -> action = $this ->escPost('action');
		}
		
		function index(){
			if( $this -> havepost('action')){
				switch( $this -> action ):
					case 'approve_list_phone' : $this -> approvListPhone(); break;
					case 'update_name'	: $this -> UpdateName();break;
				endswitch;
			}
		}
		
		function UpdateName(){
			$sql = " UPDATE t_gn_customer a SET a.CustomerFirstName = '".$_REQUEST['newcustomername']."' 
					 WHERE a.CustomerId='".$_REQUEST['customerid']."' ";
			  
			$res = $this -> execute($sql,__FILE__,__LINE__);
			if( $res ){
				$sql = " UPDATE t_gn_payer a SET a.PayerFirstName ='".$_REQUEST['newcustomername']."' 
						 WHERE a.CustomerId='".$_REQUEST['customerid']."' ";
						 
				$res = $this -> execute($sql,__FILE__,__LINE__);
					if( $res ) {
						$sql = " UPDATE t_gn_insured a SET a.InsuredFirstName ='".$_REQUEST['newcustomername']."'  
									WHERE a.CustomerId='".$_REQUEST['customerid']."'  
									and a.PremiumGroupId=2";
								
						$res = $this -> execute($sql,__FILE__,__LINE__);
						if( $res ) echo 1;
						else
							echo 0;
					}
					else
						echo 0;
			}
			else
				echo 0;
		}
		
		private function getColumns(){
			$ListPhone = $this -> getInfoList();
			$columns = array(
					2 => 'CustomerHomePhoneNum',
					3 => 'CustomerMobilePhoneNum',
					4 => 'CustomerWorkPhoneNum',
					5 => 'CustomerWorkExtPhoneNum'
				);
				
				if( ($ListPhone ->ApprovalItemId!=6) && ($ListPhone ->ApprovalItemId!=1) ):
					return $columns[$ListPhone ->ApprovalItemId];
				else:
					return false;
				endif;
		}
		
		function getInfoList(){
			$sql = " select * from t_gn_approvalhistory a where a.ApprovalHistoryId='".$_REQUEST['rowsid']."'";
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			$row = $this -> fetchrow($qry);
			return $row;
		}
		
		private function UpdateInHistory(){
			$ListUpdate = array( 'ApprovalUpdatedTs' => date('Y-m-d H:i:s'), 'UpdatedById' => $this ->getSession('UserId'),'ApprovalApprovedFlag' => 1);
			$where['ApprovalHistoryId']	= $_REQUEST['rowsid'];
			
			$query = $this ->set_mysql_update('t_gn_approvalhistory', $ListUpdate, $where); 
			
			if( $query ) : return true;
				else:
					return false;
				endif;	
		}
	
	/* tap with condition request **/
	
		function approvListPhone(){
			$ListPhone = $this -> getInfoList();
			if( ($ListPhone ->ApprovalItemId ==6) && ($ListPhone -> ApprovePhoneType!='') ):
					$addPhone = array(
						'CustomerId' => $ListPhone -> CustomerId, 
						'AddPhoneType' => $ListPhone -> ApprovePhoneType, 
						'AddPhoneNumber' => $ListPhone -> ApprovalNewValue, 
						'AddPhoneApproveId' => $ListPhone -> ApprovalHistoryId);
				   
			    $query = $this -> set_mysql_insert('t_gn_addphone',$addPhone); 	
				// echo var_dump($query);
				if( $query ) :  
					$this -> UpdateInHistory();
						echo 1;
				else : 
					echo 0;
				endif;	
			else:
				if( $this -> updateTgnCustomers())
				{
					if( $this -> UpdateInHistory() ) : echo 1;
					else :
						echo 0; 
					endif;	
				}
				else { echo 0; }
			endif;
		}
		
	/** update if request is change phone number **/ 
	
		function updateTgnCustomers(){
			$column = $this -> getColumns();
			$ListPhone = $this -> getInfoList();
			
		
			if($column):
				$cust[$column] = $ListPhone -> ApprovalNewValue; 
				$where['CustomerId'] = $ListPhone -> CustomerId;
				$query = $this -> set_mysql_update('t_gn_customer',$cust,$where);
				
				if( $query ) : return true;
				else : return false; endif;
			endif;	
		}
	}
	
	$Approval = new Approval(true);
	$Approval -> index();

?>