<?php
	class getFunction extends mysql{
		var $CustData;
		
		function __construct(){
			parent::__construct();
			
			$this -> getCustomer();
		}
		
		function getCustomer(){
			$sql = " select * from t_gn_customer a 
						where a.CustomerId=".$this->escPost('customerid');
						//and a.CampaignId=".$this->escPost('campaignid')."";
			$qry = $this ->execute($sql,__FILE__,__LINE__);
			$this->CustData = $this -> fetchrow($qry); 
		}
		
		
	}
	
	$getFunction = new getFunction(); 
?>