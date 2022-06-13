<?php
require("../sisipan/sessions.php");
require("../fungsi/global.php");
require("../class/MYSQLConnect.php");
	/*
	 *	class untuk action call result
	 *  author : omens
	 *  date : 2012-10-17
	*/
	
class BucketFTP extends mysql
{

 function BucketFTP()
 {
	parent::__construct();
	self::index();
 }
 
  function index()
  {
	switch($_REQUEST['action'])
	{
		case 'get_campaign' : $this -> getCampaignData(); break;
		case 'save_to_campaign' : $this -> SaveToCampaign(); break;
	}
  }
  
/*** private function cek other campaign custoomer id *****/

private function getSameCustomer($CampaignId = 0, $CustomerNumber =0 )
		{ 
			$sql = " SELECT COUNT(*) as jumlah FROM t_gn_customer a 
					 WHERE a.CampaignId IN('$CampaignId') AND a.CustomerNumber='$CustomerNumber' ";
					 
			$qry = $this ->query($sql);
			if( $qry -> result_singgle_value() > 0 ) 
				return true;
			else
				return false;
		}		  
 
/* SaveToCampaign ****/
 
  function SaveToCampaign()
  {
		$datas = array
				( 'result'=>1, 'totals_success'=> 0, 'totals_duplicate'=> 0 );
		$ftp_list_id = EXPLODE(",",$_REQUEST['ftp_list_id']);
		$CampaignId = $_REQUEST['campaign_id'];
		
		$totals_result_rows = 0;
		$totals_duplicate_rows = 0;
		
		foreach( $ftp_list_id as $key => $CustomerFtpId )
		{
			$sql = " select * from t_gn_ftp_customers a where a.CustomerId='$CustomerFtpId' ";
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				if( !$this -> getSameCustomer($CampaignId, $rows['CustomerNumber']) )
				{
					$Contents['CampaignId']			  	= $CampaignId;
					$Contents['CustomerNumber'] 		= $rows['CustomerNumber'];
					$Contents['CustomerFirstName'] 	 	= $rows['CustomerFirstName'];
					$Contents['CustomerDOB'] 			= $rows['CustomerDOB'];
					$Contents['CustomerAddressLine1']   = $rows['CustomerAddressLine1'];
					$Contents['CustomerAddressLine2']   = $rows['CustomerAddressLine2'];
					$Contents['CustomerAddressLine3']   = $rows['CustomerAddressLine3'];
					$Contents['CustomerAddressLine4']   = $rows['CustomerAddressLine4'];
					$Contents['CustomerCity'] 		  	= $rows['CustomerCity'];
					$Contents['CustomerZipCode'] 		= $rows['CustomerZipCode'];
					$Contents['CustomerCardType']		= $rows['CustomerCardType'];
					$Contents['CustomerHomePhoneNum']   = $rows['CustomerHomePhoneNum'];
					$Contents['CustomerMobilePhoneNum'] = $rows['CustomerMobilePhoneNum'];
					$Contents['CustomerWorkPhoneNum']   = $rows['CustomerWorkPhoneNum'];
					$Contents['GenderId']		  		= $this -> getGender($rows['GenderId']);
					$Contents['UploadedById']			= $this -> getSession('UserId');
					$Contents['CustomerUploadedTs']     = date('Y-m-d H:i:s');
					
					if( $this -> set_mysql_insert('t_gn_customer',$Contents) ){
						$totals_result_rows +=1;
						$this -> Assigment($this -> get_insert_id()); 
						$this -> UFTPCustomer($rows['CustomerId']); 
					}
				}
				else{
					$totals_duplicate_rows+=1;
				}
			}
		}
		
		$datas = array
				(
					'result'=>1, 
					'totals_success'=> $totals_result_rows,
					'totals_duplicate'=> $totals_duplicate_rows
				);
		
	 echo json_encode($datas);	
  }
  
/** UFTPCustomer **/
  function UFTPCustomer( $FTP_customerId )
  {
	$sql = " UPDATE t_gn_ftp_customers a SET a.AssignCampign=1 WHERE a.CustomerId='$FTP_customerId' ";
	if($this -> execute($sql,__FILE__,__LINE__)) return true;
	else
		return false;
	
  }	
	
  
/**	function get gender **/
	
	function getGender($code)
	{
		$sql = " select a.GenderId from t_lk_gender a where a.GenderShortCode='".strtoupper($code)."' ";
		$qry = $this -> query($sql);
		return $qry -> result_singgle_value();
		
	} 
  
 /** set to assignment **/ 
 
  private function Assigment($custid)
  {
		if($custid!='')
		{
			$sql =" INSERT INTO t_gn_assignment
					(CustomerId, AssignAdmin)
					VALUES (".$custid.", ".$this -> getSession('UserId').")";
						
				$this->execute($sql,__FILE__,__LINE__);		
		}	
	}
  
/* getCampaignData ***/
  
  function getCampaignData()
  {
	 $datas = array();
	 $sql = " SELECT * FROM t_gn_campaign a WHERE a.CampaignStatusFlag=1 ";
	 $qry = $this -> query($sql);
	 
	  foreach( $qry -> result_assoc() as $rows )
		 {
			$datas[$rows['CampaignId']] = $rows['CampaignName'];	
		 } 	
		
		echo json_encode($datas); 
  }
  
}
new BucketFTP();
?>