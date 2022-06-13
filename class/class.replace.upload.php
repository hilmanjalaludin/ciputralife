<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
	
class ReplaceUpload extends mysql
{
	private $CampaignUploadId;
	
	function ReplaceUpload()
	{
		parent::__construct();
		$this -> CampaignUploadId = $_REQUEST['CampaignUploadId'];
	}
	
	function index()
	{
		if( $this -> havepost('action'))
		{
			switch($_REQUEST['action'])
			{
				case 'upload_replace_yes' : $this -> NextUploadProcess(); 	break;
				case 'upload_replace_no'  : $this -> ClearUploadProcess();  break;
			}
		}
	}
	
/** private function get campaign name ***/
		
	private function getCampaignName($CampaignId=0)
	{
		$sql = "select a.CampaignName from t_gn_campaign a where a.CampaignId='$CampaignId' ";
		$qry = $this -> query($sql);
		if( $qry -> result_num_rows() > 0 )
		{
			return $qry -> result_singgle_value();
		}
		else	
			return null;
		
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
	
// assign table 

	private function SaveAssigment($CustomerId)
	{
		if($CustomerId!='')
		{
			$sql =" INSERT INTO t_gn_assignment (CustomerId, AssignAdmin) VALUES (".$CustomerId.", ".$this->getSession('UserId').")";
			$res = $this->execute($sql,__FILE__,__LINE__);
			if( $res ) return true;
			else
				return false;
		}		
	}
		
		
// if user click confirm
 	
	function NextUploadProcess()
	{
		$sql = " SELECT * FROM t_gn_tmpupload a where CampaignId='".$this -> CampaignUploadId."'";
		$qry = $this -> query($sql);
		if( $qry -> result_num_rows() > 0 )
		{
			$i = 0;
			foreach($qry -> result_assoc() as $rows )
			{
				$SQL_Ins['CampaignId']				= $rows['CampaignId'];
				$SQL_Ins['CustomerNumber']			= $rows['CustomerNumber'];
				$SQL_Ins['CustomerFirstName']		= $rows['CustomerFirstName'];
				$SQL_Ins['GenderId']				= $rows['Gender'];
				$SQL_Ins['CustomerCardType']		= $rows['CardTypeDesc'];
				$SQL_Ins['CustomerDOB']				= $rows['CustomerDOB'];
				$SQL_Ins['CustomerAddressLine1']	= $rows['CustomerAddressLine1'];
				$SQL_Ins['CustomerAddressLine2']	= $rows['CustomerAddressLine2'];
				$SQL_Ins['CustomerAddressLine3']	= $rows['CustomerAddressLine3'];
				$SQL_Ins['CustomerAddressLine4']	= $rows['CustomerAddressLine4'];
				$SQL_Ins['CustomerCity']			= $rows['CustomerCity'];
				$SQL_Ins['CustomerZipCode']			= $rows['CustomerZipCode'];
				$SQL_Ins['CustomerHomePhoneNum']	= $rows['CustomerHomePhoneNum'];
				$SQL_Ins['CustomerMobilePhoneNum']	= $rows['CustomerMobilePhoneNum'];
				$SQL_Ins['CustomerWorkPhoneNum']	= $rows['CustomerWorkPhoneNum'];
				$SQL_Ins['UploadedById']			= $rows['UploadedById'];
				$SQL_Ins['CustomerUploadedTs']		= $rows['CustomerUploadedTs'];
				
				if( !$this -> getSameCustomer($SQL_Ins['CampaignId'], $SQL_Ins['CustomerNumber']) )
				{
					if( $this -> set_mysql_insert('t_gn_customer',$SQL_Ins) )
					{	
						$this -> SaveAssigment( $this -> get_insert_id() );
						$sql = " DELETE FROM t_gn_tmpupload WHERE CustomerNumber='".$SQL_Ins['CustomerNumber']."'";
						$qry = $this -> execute($sql,__FILE__,__LINE__);
						if( $qry ) {
							$total_replace_rows +=1;
						}
					}
				}	
				else{
					$sql = " DELETE FROM t_gn_tmpupload WHERE CustomerNumber='".$SQL_Ins['CustomerNumber']."'";
					$qry = $this -> execute($sql,__FILE__,__LINE__);	
				}
				
				$i++;
			}
			
			if( $total_replace_rows > 0 ) 
				$result = array('result'=>1, 'msg'=>'Replace Data Is OK');
			else
				$result = array('result'=>1, 'msg'=>'Customer Alerdy Exist in Campaign ( '.$this -> getCampaignName($SQL_Ins['CampaignId']).' ) ');
		}
		
		echo json_encode($result);
	}
	
// if user click cancel 	
	
	function ClearUploadProcess()
	{
		$sql = " DELETE FROM t_gn_tmpupload WHERE CampaignId='".$this -> CampaignUploadId."'";
		$qry = $this -> execute($sql,__FILE__,__LINE__);
		if( $qry )
		{
			$result = array('result'=>1, 'msg'=>'Cancel Data Is OK');
		}	
		echo json_encode($result);
	}
}
$ReplaceUpload  = new ReplaceUpload();
$ReplaceUpload -> index();
?>