<?php
class Customers extends mysql
{
	private $CustomerId = null;
	private $Customers  = array();
/** aksesor ***/
	
	function Customers($CustomerId,$CampaignId)
	{
		$sql =" SELECT 
				a.CustomerCardType,
				a.CustomerFirstName ,
				a.CustomerDOB,
				b.AssignSelerId, 
				c.CampaignName, 
				c.CampaignStartDate, 
				c.CampaignEndDate 
			FROM t_gn_customer a
				INNER JOIN t_gn_assignment b on a.CustomerId= b.CustomerId
				LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId
				WHERE c.CampaignStatusFlag=1
				AND a.CustomerId = ".$CustomerId."
				AND a.CampaignId = ".$CampaignId." ";
		$qry  = $this -> query($sql);
		if( !$qry -> EOF() )
		{
			$this -> Customers = $qry -> result_first_assoc();	
		}				
	}
	
/** CustomerCardType **/

	function CustomerCardType()
	{
		return $this -> Customers['CustomerCardType'];
	}
	
/** CustomerFirstName **/

	function CustomerFirstName()
	{
		return $this -> Customers['CustomerFirstName'];
	}		
	
/** CustomerDOB **/
	function CustomerDOB()
	{
		return $this -> Customers['CustomerDOB'];
	}	

/** AssignSelerId **/
	function AssignSelerId()
	{
		return $this -> Customers['AssignSelerId'];
	}	

/** CampaignName **/
	function CampaignName()
	{
		return $this -> Customers['CampaignName'];
	}	

/** CampaignStartDate **/
	function CampaignStartDate()
	{
		return $this -> Customers['CampaignStartDate'];
	}	

/** CampaignEndDate **/
	function CampaignEndDate()
	{
		return $this -> Customers['CampaignEndDate'];
	}		
					
}	
?>