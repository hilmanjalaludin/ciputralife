<?php

/*
   @ Class name : getFunction
*/


# cs : start Object Class
 
class getFunction extends mysql{
		var $CustData;
		var $holderEdit;
		
		function __construct(){
			parent::__construct();
			
			$this -> getCustomer();
		}
		
		function getCustomer()
		{
			$sql = " select * from t_gn_customer a 
						where a.CustomerId=".$this->escPost('customerid')."
						and a.CampaignId=".$this->escPost('campaignid')."";
			$qry = $this ->execute($sql,__FILE__,__LINE__);
			$this->CustData = $this -> fetchrow($qry); 
		}
		
/** class get benefiecery ***/

	public function getBenefiecery()
	{
		$result = array();
		$sql = "SELECT * FROM t_gn_beneficiary a WHERE a.CustomerId='".$this->escPost('customerid')."'";
		$qry = $this -> query($sql);
		$num=1;
		foreach( $qry -> result_assoc() as $rows )
		{
			$result[$num] = $rows;
			$num++;
		}
		return $result;
	}		
		
		
/** show detail policy closing **/
	
	public function getDetailPolicy()
	{	
		$sql = " SELECT 
					a.*, b.RelationshipTypeId, b.PremiumGroupId, b.ProvinceId, f.*, 
					c.PayerDOB, c.PaymentTypeId, c.PayerCreditCardNum, c.PayerIdentificationNum, c.PayerFirstName, 
					c.CreditCardTypeId, c.PayersBankId,
					c.ProvinceId as PayersPropinceId,
					b.IdentificationTypeId,
					c.PayerAddressLine1,
					c.PayerAddressLine4, 
					c.PayerMobilePhoneNum,
					c.PayerHomePhoneNum,
					c.PayerWorkPhoneNum,
					c.PayerZipCode,
					c.PayerWorkExtPhoneNum,
					c.PayerAddressLine2,
					c.PayerAddressLine3,
					c.PayerAddressLine4,
					c.PayerCity,
					c.PayerCreditCardNum,
					c.PayerEmail,
					b.SalutationId
				FROM t_gn_customer a 
				 left JOIN t_gn_insured b on a.CustomerId =b.CustomerId  
				 left JOIN t_gn_payer c ON a.CustomerId = c.CustomerId
				 left JOIN t_gn_policyautogen d ON a.CustomerId = d.CustomerId
				 left JOIN t_gn_policy e ON d.PolicyNumber = e.PolicyNumber
				 left join t_gn_product f on d.ProductId = f.ProductId
				 WHERE b.CustomerId='".$this->escPost('customerid')."' LIMIT 1 ";
		//echo $sql;		 
		$qry = $this -> query($sql);
		if(!$qry -> EOF()) 
		{
			return $qry;	
		}	
	}
	 
	/** tidak dipakai dulue **/
	
	function getHolderEdit()
		{
			$sql = " SELECT * from t_gn_customer a 
					 LEFT JOIN v_sales_date b on b.CustomerId=a.CustomerId
					 LEFT OUTER JOIN t_gn_insured c on ( c.CustomerId=b.CustomerId and c.PremiumGroupId=2) 
					 WHERE a.CustomerId=".$this->escPost('customerid')."
					 AND a.CampaignId=".$this->escPost('campaignid')."";
	
			$qry = $this ->execute($sql,__FILE__,__LINE__);
			$this->holderEdit = $this -> fetchrow($qry); 
		}
		
		function getRelation()
		{
			$sql =" select a.RelationshipTypeId, a.RelationshipTypeDesc from t_lk_relationshiptype a ";
			$qry = $this ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$datas[$rows['RelationshipTypeId']] = $rows['RelationshipTypeDesc'];
			}
			return $datas;
		}
		
		function editRelation()
		{
			$sql =" select a.RelationshipTypeId, a.RelationshipTypeDesc from t_lk_relationshiptype a ";
			$qry = $this ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$datas[$rows['RelationshipTypeId']] = $rows['RelationshipTypeDesc'];
			}
			return $datas;
		}
		
		
	/** efective date &&& ***/
	
		function GetEfectiveDate()
		{
			$cut = $this -> query("SELECT a.CutoffDate FROM t_lk_cutoffdate a WHERE a.CutoffMonth=MONTH(NOW())");
			$qry = $this -> query("SELECT F_getEfectiveDate('".$cut -> result_singgle_value()."','". date('Y-m-d')."') ");
			if( !$qry -> EOF())
			{
				return $qry -> result_singgle_value();
			}
		}	
		
		
		function getStartForm(){
			$datas = array
					(
						'inputDate' => date('Y-m-d'),
						'efectiveDate'=> $this -> GetEfectiveDate()
					);
					
			return $datas;		
		
		}
		
		function getBankId($choose)
		{
			$sql ="select b.BankId, c.BankName from t_gn_payer a
					left join t_lk_validccprefix b on ( mid(a.PayerCreditCardNum,1,6) = b.ValidCCPrefix) 
					left join t_lk_bank c on c.BankId=b.BankId
					where a.CustomerId='".$this->CustomerId."'";
					
			return $this -> valueSQL($sql);		
		}	
		
		function getSalutation()
		{
			$sql ="select a.SalutationId, a.Salutation from t_lk_salutation a";
			$qry = $this ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$datas[$rows['SalutationId']] = $rows['Salutation'];
			}
			return $datas;
		}
		
		function getHolderType(){
			
			$sql ="select a.PremiumGroupId, a.PremiumGroupDesc from t_lk_premiumgroup a";
			$qry = $this ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$datas[$rows['PremiumGroupId']] = $rows['PremiumGroupDesc'];
			}
			return $datas;
			
		}
		
		function getGender()
		{
			$sql =" select a.GenderId, a.Gender from t_lk_gender a ";
			$qry = $this ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$datas[$rows['GenderId']] = $rows['Gender'];
			}
			return $datas;
		}
		
		function getInsuranceSp(){
			$sql = "select a.PremiumGroupId as IndexRows, a.*  from t_gn_insured a
							where a.CustomerId='".$_REQUEST['customerid']."' 
							AND a.PremiumGroupId = 3 ";		
			$qry = $this ->execute($sql,__FILE__,__LINE__);
			while( $row = $this->fetchrow($qry) ){
				$datas[$row->IndexRows] = $row;
			}	
			return $datas;
		}
		
		function getInsuranceDp()
		{
			$sql = "select a.InsuredId as IndexRows, a.*  from t_gn_insured a
							where a.CustomerId='".$_REQUEST['customerid']."' 
							AND a.PremiumGroupId = 1 
							group by a.InsuredId";
							
						
			$qry = $this ->execute($sql,__FILE__,__LINE__);
			$i=1;
			while( $row = $this->fetchrow($qry) ){
				$datas[$i] = $row;
				$i++;
			}	
			return $datas;
		}
		
		function getBanking()
		{	
			$sql = "select a.BankId, a.BankName from t_lk_bank a";
			$qry = $this ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$datas[$rows['BankId']] = $rows['BankName'];
			}
			return $datas;
		
		}
		
		function getProvince()
		{
			$sql="select a.ProvinceId, a.Province from t_lk_province a";
			$qry = $this ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$datas[$rows['ProvinceId']] = $rows['Province'];
			}
			return $datas;
		}
		
		function getProduct($choose=0)
		{
			$sql =" select a.SalutationId, a.Salutation from t_lk_salutation a";
			$qry = $this ->execute($sql,__FILE__,__LINE__);
			echo "<option value=\"\">-- Choose --</option>";
				
			while( $row = $this ->fetcharray($qry)){
				if($row[0]==$choose):
					echo "<option value=\"{$row[0]}\" selected>{$row[1]}</option>";
				else :
					echo "<option value=\"{$row[0]}\">{$row[1]}</option>";
				endif;
			}
			
		}
		
		function getPlan()
		{
			$sql =" select a.ProductPlanId, a.ProductPlanId, a.ProductPlan, a.ProductPlanName, a.ProductPlanAgeStart, a.ProductPlanAgeEnd from t_gn_productplan a 
					left join t_gn_product b on a.ProductId=b.ProductId
						where a.ProductId=12
					group by a.ProductPlan";
					
			$qry = $this ->execute($sql,__FILE__,__LINE__);
			echo "<option value=\"\">-- Choose --</option>";
				
			while( $row = $this ->fetcharray($qry)){
				echo "<option value=\"{$row[0]}\">{$row[1]}</option>";
			}
			
		}
		
		function getProductByCampaign()
		{
			$sql = "SELECT a.ProductId, a.ProductName FROM t_gn_product a  
					RIGHT JOIN t_gn_campaignproduct b ON a.ProductId=b.ProductId
					WHERE b.CampaignId='".$this -> escPost('campaignid')."'
					AND a.ProductStatusFlag=1 ";
			//echo $sql;		
			
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['ProductId']] = $rows['ProductName'];
			}
			
			return $datas;
		}
		
		function getPlanByCampaign()
		{
			$sql = "select a.* from t_gn_productplan a
					left join t_gn_product b on a.ProductId = b.ProductId
					left join t_gn_campaignproduct c on b.ProductId=c.ProductId
					where c.CampaignId='".$this -> escPost('campaignid')."' ";
			
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['ProductId']] = $rows['ProductName'];
			}
			
			return $datas;
		}
		
		function getCardType()
		{
			$sql =" select a.CreditCardTypeId,a.CreditCardTypeDesc from t_lk_creditcardtype a";
			$qry = $this ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$datas[$rows['CreditCardTypeId']] = $rows['CreditCardTypeDesc'];
			}
			return $datas;
		}
		
		function getPayType()
		{
			$sql =" select a.PaymentTypeId,a.PaymentTypeDesc from t_lk_paymenttype a ";
			$qry = $this ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$datas[$rows['PaymentTypeId']] = $rows['PaymentTypeDesc'];
			}
			return $datas;
			
			
		}
		
		function getPlanEdit()
		{
			$sql =" select * from t_gn_policy a 
									left join t_gn_productplan b on a.ProductPlanId=b.ProductPlanId
									left join t_gn_insured c on c.PolicyId=a.PolicyId
									where c.CustomerId='".$this->escPost('customerid')."'";
			//echo $sql;
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			$row = $this -> fetchrow($qry);
			return $row;	

		}
		
		function IsHolder(){
			$sql ="select count(a.PayerFirstName) as jumlah from t_gn_payer a 
						inner join t_gn_insured b
						on a.PayerFirstName=b.InsuredFirstName
						where a.CustomerId='".$this->escPost('customerid')."'";
			$jumlah  = $this ->valueSQL($sql);
			if( $jumlah>0) return true;
			else return false;
		}
		
		
		function getEditPremium(){
			$sql = " select sum( c.ProductPlanPremium) as premium from t_gn_insured a
						left join t_gn_policy b on a.PolicyId=b.PolicyId
							left join t_gn_productplan c on b.ProductPlanId=c.ProductPlanId 
							where a.CustomerId='".$this->escPost('customerid')."'";
			return $this -> valueSQL($sql);				

		}
		
		function getPayMode()
		{
			$sql = "SELECT a.PayModeId,a.PayMode FROM t_lk_paymode a";
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['PayModeId']] = $rows['PayMode'];
			}
			return $datas;
		}
		
		function getIdType()
		{
			$sql = " select a.IdentificationTypeId, a.IdentificationType from t_lk_identificationtype a ";
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['IdentificationTypeId']] = $rows['IdentificationType'];
			}
			return $datas;
		}
		
		function getCampaignName()
		{
			$sql = " select a.CampaignNumber , a.CampaignName from t_gn_campaign  a where a.CampaignId = '".$this->escPost('campaignid')."'";
			$qry = $this -> query($sql);
			if( !$qry -> EOF() ){
				return $qry -> result_get_value('CampaignName');
			}
			else
				return null;
		}
		
		function getCampaignNumber()
		{
			$sql = " select a.CampaignNumber , a.CampaignName from t_gn_campaign  a where a.CampaignId = '".$this->escPost('campaignid')."'";
			$qry = $this -> query($sql);
			if( !$qry -> EOF() ){
				return $qry -> result_get_value('CampaignNumber');
			}
			else
				return null;
		}
		
		private function getInfoPolicy(){
			$sql = " select count(*) from t_gn_policyautogen a where a.CustomerId=".$this->escPost('customerid');
			
			if( $this->valueSQL($sql) >0 ): return true;
			else : return false; endif;
		}
		
		function getPlanChoose(){
			if( $this->getInfoPolicy() ):
				
			endif;	
		}
		
		function getInsuranceChoose(){
		
			if( $this->getInfoPolicy() ):
				
				$sql = " select c.* from t_gn_policyautogen a
							left join t_gn_payer b on  a.CustomerId = b.CustomerId
							left join t_gn_insured c on a.CustomerId=c.CustomerId
							left join t_gn_policy d on c.PolicyId=d.PolicyId
							left join t_lk_premiumgroup e on c.PremiumGroupId=e.PremiumGroupId
							where a.CustomerId=".$this->escPost('customerid')."
							and e.PremiumGroupId NOT IN(2,4)
							group by e.PremiumGroupId ";
				$qry = $this -> execute($sql,__FILE__,__LINE__);	
				$row = $this -> fetchrow($qry);	
				return $row;
			endif;
			
		}
		
		function getBasicHolder()
		{
			if( $this->getInfoPolicy() ):
				$sql="SELECT a.*,
								 c.*, d.*, e.*, d.id as CignaUser 
						  FROM t_gn_customer a left join t_gn_policyautogen b on a.CustomerId=b.CustomerId
								LEFT JOIN t_gn_policy c on c.PolicyNumber=b.PolicyNumber
								LEFT JOIN tms_agent d on d.UserId = a.SellerId
								LEFT JOIN t_gn_insured e on ( a.CustomerId=e.CustomerId and e.PremiumGroupId=2)
						  WHERE a.CustomerId='".$this->escPost('customerid')."'";
				
				$qry = $this -> execute($sql,__FILE__,__LINE__);	
				$row = $this -> fetchrow($qry);	
				return $row;
			endif;
		}
		
		function getBasicPayers(){
			if( $this->getInfoPolicy() ):
				$sql = " select b.* from t_gn_policyautogen a
						 left join t_gn_payer b on  a.CustomerId = b.CustomerId
						 where a.CustomerId=".$this->escPost('customerid');
				$qry = $this -> execute($sql,__FILE__,__LINE__);	
				$row = $this -> fetchrow($qry);
				return $row;
			endif;
		}
		
		function getEditPayers(){
			if( $this->havepost('customerid') ):
				$sql = " select b.* from t_gn_policyautogen a
						 left join t_gn_payer b on  a.CustomerId = b.CustomerId
						 where a.CustomerId=".$this->escPost('customerid');
				//echo $sql;		 
				$qry = $this -> execute($sql,__FILE__,__LINE__);	
				$row = $this -> fetchrow($qry);
				return $row;
			endif;
		}
	
/** *******************************/
		
		function getProductId()
		{
			$sql = " select a.ProductId from t_gn_product a 
					 left join t_gn_campaignproduct b on a.ProductId=b.ProductId
					 where b.CampaignId='".$_REQUEST['campaignid']."' ";
						
			$qry = $this ->query($sql);
			if( $qry -> result_num_rows() > 0 )
			{
				return $qry -> result_singgle_value();
			}
			else
				return NULL;
		}
		
	/** *******************************/
	
		function get_size_premi()
		{
			$sql = " SELECT sum(b.Premi) FROM t_gn_policyautogen a INNER JOIN t_gn_policy b on a.PolicyNumber = b.PolicyNumber
					 WHERE a.CustomerId='".$this->escPost('customerid')."' group by a.PolicyNumber ";
					 
			$qry = $this -> query($sql);
			if( !$qry -> EOF() ){
				return (INT)$qry ->result_singgle_value();
			}
			else
				return 0;
		}
	/*****************/	
		
		
	/*********************************************/
	}	
		
# ce : end Object Class
 	
	
	$getFunction = new getFunction(); 
?>	