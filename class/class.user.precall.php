<?php

require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../plugin/lib.form.php");


class UserPrecall extends mysql
{
 
 function UserPrecall()
	{
		parent::__construct();
		self::index();
	}
	
	
 function index()
	{
		if( $this -> havepost('action'))
		{
			switch( $this -> escPost('action') )
			{
				case 'get_inventory_data' : $this -> getInventoryData(); break;
				case 'get_start_call_data'	: $this -> getStartCallData(); break;
				
			}
		}
	}
	
function getStartCallData()
{
	$result = array( 'valid' => 0, 'CustomerId'=> '', 'CampaignId'=> '', 'CallReasonId'=> '','LastCallDate'=> '' );
	if( $this -> havepost('CampaignId'))
	{
		$sql = " SELECT 
					a.CustomerId,a.CampaignId,a.CallReasonId, a.CustomerUpdatedTs 
				 FROM t_gn_customer a 
				 left join t_gn_assignment b on a.CustomerId=b.CustomerId
				 left join t_gn_campaign c on a.CampaignId=c.CampaignId
				 WHERE b.AssignSelerId='".$this->getSession('UserId')."' 
				 AND ( a.CallReasonId IN('".$this->Entity->ImplArrFollowUp()."')  OR a.CallReasonId IS NULL ) ";
				 // echo $sql;
		
		// filetring data 
		
		$filter ="";
		
		if( $this -> havepost('CallReasonId')){
			$CallReason = $this -> escPost('CallReasonId');
			 if( $CallReason==0 ){
				$this -> setSession('CallReasonId','0');
				$filter .= " AND a.CallReasonId is null "; 
			 }	
			 else{
				$this -> setSession('CallReasonId',$this -> escPost('CallReasonId'));
				$filter = " AND a.CallReasonId ='$CallReason'";
			 }
		}
		else{
			unset($_SESSION['CallReasonId']);
		}
		
		if( $this -> havepost('LastCallDate')){
			$LastCallDate = $this -> escPost('LastCallDate');
			if( $LastCallDate==0 ){
				$this -> setSession('LastCallDate','0');
				$filter .= ""; 
			}	
			else{
				$this -> setSession('LastCallDate',$this -> escPost('LastCallDate'));
				$filter = " AND date(a.CustomerUpdatedTs) ='$LastCallDate'";
			}
		}
		else{
			unset($_SESSION['LastCallDate']);
		}
		
		if( $this -> havepost('LastCallTime')){
			$LastCallTime = $this -> escPost('LastCallTime');
			if( $LastCallTime==0 ){
				$this -> setSession('LastCallTime','0');
				$filter .= ""; 
			}	
			else{
				$this -> setSession('LastCallTime',$this -> escPost('LastCallTime'));
				$filter = " AND hour(a.CustomerUpdatedTs) ='$LastCallTime'";
			}
		}
		else{
			unset($_SESSION['LastCallTime']);
		}
		
		
		if( $this -> havepost('CampaignId'))
		{
			$this -> setSession('CampaignId',$this -> escPost('CampaignId'));
			$filter .= " AND a.CampaignId ='".$this -> escPost('CampaignId')."'"; 
		}
		
		if( $this -> havepost('CustomerName') && $this -> escPost('CustomerName')!='null')
		{
			$CustomerFirstName = $this -> escPost('CustomerName');
			$filter .= " AND a.CustomerFirstName LIKE '%$CustomerFirstName%'"; 
		}
		
		if( $this -> havepost('CustomerAddress') && $this -> escPost('CustomerAddress')!='null')
		{
			$CustomerAddress = $this -> escPost('CustomerAddress');
			$filter .= " AND ( 
							a.CustomerAddressLine1 LIKE '%$CustomerAddress%' 
							OR a.CustomerAddressLine2 LIKE '%$CustomerAddress%' 
							OR a.CustomerAddressLine3 LIKE '%$CustomerAddress%'
							OR a.CustomerAddressLine4 LIKE '%$CustomerAddress%'
						) "; 
		}
			
		
		$sql.= $filter." ORDER BY a.CustomerUpdatedTs, RAND() DESC LIMIT 1 ";
		//echo $sql;
		$qry = $this -> query($sql);
		if( !$qry -> EOF())	
		{
			$result = array(
				'valid' => 1,
				'CustomerId'=> $qry -> result_get_value('CustomerId'),
				'CampaignId'=> $qry -> result_get_value('CampaignId'),
				'CallReasonId'=> $qry -> result_get_value('CallReasonId'),
				'CustomerName' => $qry -> result_get_value('CustomerFirstName'),
				'CustomerAddress' => $CustomerAddress
			);
		}	
	}
	
	echo json_encode($result);
}
	

function getInventoryData()
 {
	$result = array( 'inventory_data' => 0, 'utilize_data' => 0 );
	
	$sql = " SELECT count(a.CustomerId) as inventory_data, 
			 SUM(IF( date(a.CustomerUpdatedTs)=date(NOW()),1,0)) as utilize_data 
			 from t_gn_customer a 
			 left join t_gn_assignment b on a.CustomerId=b.CustomerId 
			 left join t_gn_campaign c on a.CampaignId=c.CampaignId
			 WHERE c.CampaignStatusFlag=1
			 AND b.AssignSelerId='".$this -> getSession('UserId')."'
			 group by b.AssignSelerId ";
	$qry = $this -> query($sql);
	if( !$qry->EOF() )
	{
		$result = array
		(
			'inventory_data' => $qry -> result_get_value('inventory_data'),
			'utilize_data' => $qry -> result_get_value('utilize_data')
		);
	}
	
	echo json_encode($result);
 }		
	
}

new UserPrecall();

?>