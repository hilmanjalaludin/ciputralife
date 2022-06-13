<?php

class Customer extends mysql{

function Customer(){

}

/**
 ** get data policy factory Method
 ** render all queries data & all
 ** attribut 
 ** return < object >
 **/ 
 

function DataPolicy($CustomerId)
 {
	$Payers 	= " SELECT a.*, b.*, a.IdentificationTypeId payerIdentificationTypeId,
						a.ProvinceId as PayerProvinceId, a.SalutationId as PayerSalutationId, 
						a.RelationshipTypeId as PayerRelationshipTypeId,
						a.GenderId as pay_gender,
						b.GenderId as ins_gender
					FROM t_gn_payer a LEFT JOIN t_gn_insured b on a.CustomerId=b.CustomerId  
					WHERE a.CustomerId='$CustomerId'";//AND a.PayerDOB=b.InsuredDOB "?; 
	
	$Insured 	= " SELECT a.* FROM t_gn_insured a where a.CustomerId='$CustomerId' ORDER BY a.InsuredId ASC ";
	
	$Holder 	= " SELECT a.* FROM t_gn_insured a where a.CustomerId='$CustomerId' AND a.PremiumGroupId=2 ORDER BY a.InsuredId ASC ";
	
	$CiputHolder 	= " SELECT a.* FROM t_gn_holder a where a.CustomerId='$CustomerId' ";
	
	$Spouse 	= " SELECT a.* FROM t_gn_insured a where a.CustomerId='$CustomerId' AND a.PremiumGroupId=3 ORDER BY a.InsuredId ASC ";
	
	$Dependent 	= " SELECT a.* FROM t_gn_insured a where a.CustomerId='$CustomerId' AND a.PremiumGroupId=1 ORDER BY a.InsuredId ASC ";
	
	$Beneficery = " SELECT a.*,c.*,d.* FROM t_gn_beneficiary a
					LEFT JOIN t_lk_gender c on a.GenderId=c.GenderId
					LEFT JOIN t_lk_relationshiptype d on a.RelationshipTypeId=d.RelationshipTypeId 
					WHERE a.CustomerId = '$CustomerId'";
					
	$Policy 	= " SELECT a.InsuredId, b.*, c.* ,d.*,e.*,f.*
					FROM t_gn_insured a 
					LEFT JOIN t_gn_policy b ON a.PolicyId=b.PolicyId 
					LEFT JOIN t_gn_productplan c ON b.ProductPlanId=c.ProductPlanId 
					LEFT JOIN t_lk_paymode d on c.PayModeId=d.PayModeId
					LEFT JOIN t_lk_gender e on a.GenderId=e.GenderId
					LEFT JOIN t_lk_relationshiptype f on a.RelationshipTypeId=f.RelationshipTypeId
					WHERE a.CustomerId='$CustomerId' ORDER BY a.InsuredId ASC ";
	// echo $Payers;					
	$Customer = " select * from t_gn_customer a where a.CustomerId='$CustomerId'";
	
	$sql = array
	   (
			'Payers' => $Payers, 
			'Insured' => $Insured, 
			'Beneficery' => $Beneficery, 
			'Policy' => $Policy, 
			'Customer' => $Customer,
			'Holder' => $Holder,
			'Spouse' => $Spouse,
			'Dependent' => $Dependent,
			'CiputHolder' => $CiputHolder
		);
	
	if( !is_array($sql))  return null;
	else{
		foreach($sql as $ClassName => $query ){
			$data[$ClassName]= $this -> query($query);	
		}
	}
	return $data;
 }
 
 /**
  ** get lookup Relation Of customer
  ** define of user
  ** return < array >
  **/
  
 function RelationshipType()
 {
	$sql = " select a.RelationshipTypeId, a.RelationshipTypeDesc FROM t_lk_relationshiptype a ";
	$qry = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows )
	{
		$datas[$rows['RelationshipTypeId']] = $rows['RelationshipTypeDesc'];
	}
	return $datas; 	
 }
 
 /**
  ** get lookup Relation Of customer
  ** define of user
  ** return < array >
  **/
  
 function PremiumGroup()
 {
	$sql = " select a.PremiumGroupId, a.PremiumGroupDesc FROM t_lk_premiumgroup a ";
	$qry = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows )
	{
		$datas[$rows['PremiumGroupId']] = $rows['PremiumGroupDesc'];
	}
	return $datas; 	
 }
 
 /**
  ** get lookup Relation Of customer
  ** define of user
  ** return < array >
  **/
  
 function Salutation()
 {
	$sql = " select a.SalutationId, a.Salutation FROM t_lk_salutation a WHERE a.SalutationFlag = 1";
	$qry = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows )
	{
		$datas[$rows['SalutationId']] = $rows['Salutation'];
	}
	return $datas; 	
 }
 
 /**
  ** get lookup Relation Of customer
  ** define of user
  ** return < array >
  **/
  
 function Paymode( $CampaignId=NULL )
 {
	$sql = " SELECT a.PayModeId, b.PayMode
			 FROM t_gn_productplan a inner join t_lk_paymode b ON a.PayModeId=b.PayModeId ";
			 
	if( !is_null( $CampaignId ) ) 
	{
		$_aP = self::ProductId($CampaignId);
		if( is_array( $_aP ) ) 
		{	
			$sql .= " WHERE a.ProductId IN(".IMPLODE(',',ARRAY_KEYS($_aP)).")";  	
		}	
	}
	
	$sql .= " GROUP BY a.PayModeId ";
	$qry = $this -> query($sql);	
	
	foreach($qry -> result_assoc() as $rows ) {
		$datas[$rows['PayModeId']] = $rows['PayMode'];
	}
	return $datas; 	
 }
 
/** get exist product id by level group premi **/

function _getExistGroupPremi( $ProductId=NULL )
{ 
	$_conds = false;
	$sql = " SELECT IF( a.PremiumGroupId is not null,1,0) as ExistGroupPremi	
			 FROM t_gn_productplan a WHERE a.ProductId IN('$ProductId') LIMIT 1 ";
	
	$qry = $this -> query($sql);	
	if( !$qry -> EOF() )
	{
		$_conds = (INT) $qry -> result_singgle_value();
	}	
	
	return $_conds;
} 
 
 
/**
 **
 **/
function ProductPlan( $CampaignId=NULL )
{
	$sql = " SELECT a.ProductPlan, a.ProductPlanName FROM t_gn_productplan a ";
	if(!is_null($CampaignId ))
	{
		$_aP = self::ProductId($CampaignId);
		if( is_array($_aP))
		{	
			$sql .= " WHERE a.ProductId IN(".IMPLODE(',',ARRAY_KEYS($_aP)).")"; 
		}	
	}	
			 
	$sql .= " GROUP BY a.ProductPlanName ";
	$qry  = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows ) 
	{
		$datas[$rows['ProductPlan']] = $rows['ProductPlanName'];
	}
	
	return $datas; 
}
 /**
  ** get lookup Relation Of customer
  ** define of user
  ** return < array >
  **/
  
function Gender()
 {
	$sql = " SELECT a.GenderId, a.Gender FROM t_lk_gender a ";
	$qry = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows )
	{
		$datas[$rows['GenderId']] = $rows['Gender'];
	}
	return $datas; 	
 }
 
  function _PayerPH($CustomerId)
 {
	$Beneficery = " select a.CallNumber from t_gn_callhistory a
					WHERE a.CustomerId = '$CustomerId'
					and a.CallReasonId=15
					order by a.CallHistoryId
					limit 1 ";
					
	$qry = $this -> query($Beneficery);
	foreach($qry -> result_assoc() as $rows )
	{
		$datas = $rows['CallNumber'];
	}
	return $datas; 	
 }
 
function PAStatus( $CustomerId=NULL)
 {
	$sql = sprintf(" select a.no_induk, a.no_polis, a.expired from t_gn_fpa_registered a where a.customerid=%d limit 1 ", $CustomerId);
	$qry = $this -> query($sql);
	$datas=array();
	foreach($qry -> result_assoc() as $rows )
	{
		$datas['no_induk'] = $rows['no_induk'];
		$datas['no_polis'] = $rows['no_polis'];
		$datas['expired'] = $rows['expired'];
	}
	return $datas; 	
 }
 
  
 /**
  ** get lookup Relation Of customer
  ** define of user
  ** return < array >
  **/
  
 function Province()
 {
	$sql = " SELECT a.ProvinceId, a.Province FROM t_lk_province a ";
	$qry = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows )
	{
		$datas[$rows['ProvinceId']] = $rows['Province'];
	}
	return $datas; 	
	 
 }
 
 function TypeAlamat()
 {
	$sql = " SELECT a.Id, a.TypeDesc FROM t_lk_addrtype a ";
	$qry = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows )
	{
		$datas[$rows['Id']] = $rows['TypeDesc'];
	}
	return $datas; 	
	 
 }
 
 function getIncomeList(){
	$sql1 = " SELECT a.IncId, a.IncDesc FROM t_lk_income a order by a.IncId asc";
	$qry = $this -> query($sql1);
	foreach($qry -> result_assoc() as $rows )
	{
		$datar[$rows['IncId']] = $rows['IncDesc'];
	}
	return $datar;
 }
 
 function getCertificateList(){
	$sql1 = " SELECT a.Id, a.Desc FROM t_lk_certificate_type a";
	$qry = $this -> query($sql1);
	foreach($qry -> result_assoc() as $rows )
	{
		$datar[$rows['Id']] = $rows['Desc'];
	}
	return $datar;
 }
 
 function getOcupationList(){
	$sql2 = " SELECT a.OccId, a.OccDesc FROM t_lk_occupation a ";
	$qry = $this -> query($sql2);
	foreach($qry -> result_assoc() as $rows )
	{
		$datas[$rows['OccId']] = $rows['OccDesc'];
	}
	return $datas;
 }
 
 function getJobPosList(){
	$sql3 = " SELECT a.PositionId, a.PositionDesc FROM t_lk_position a ";
	$qry = $this -> query($sql3);
	foreach($qry -> result_assoc() as $rows )
	{
		$datas[$rows['PositionId']] = $rows['PositionDesc'];
	}
	return $datas;
 }
 
 function PaymentMethod(){
     $sql = "select PaymentTypeDesc,port_number from t_lk_paymenttype";
     $qry = $this -> query($sql);
     foreach($qry -> result_assoc() as $rows ) {
		$datas[$rows['port_number']] = $rows['PaymentTypeDesc'];
	}
	return $datas;
 }
 
 function payment_method()
 {
	$sql = " SELECT a.PaymentTypeId, a.PaymentTypeDesc FROM t_lk_paymenttype a ";
	$qry = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows )
	{
		$datas[$rows['PaymentTypeId']] = $rows['PaymentTypeDesc'];
	}
	return $datas; 	
 }
  
 /**
  ** get lookup Relation Of customer
  ** define of user
  ** return < array >
  **/
  
 function IndentificationId()
 {
	$sql = " SELECT a.IdentificationTypeId, a.IdentificationType FROM t_lk_identificationtype a ";
	$qry = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows )
	{
		$datas[$rows['IdentificationTypeId']] = $rows['IdentificationType'];
	}
	return $datas;
 }
 
 /**
  ** get lookup Relation Of customer
  ** define of user
  ** return < array >
  **/
  
 function Bank()
 {
	$sql = " SELECT a.* FROM t_lk_bank a where a.BankStatusFlag=1";
	$qry = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows )
	{
		$datas[$rows['BankId']] = $rows['BankName'];
	}
	return $datas;
 }
 function SavingBank()
 {
	$sql = " SELECT a.* FROM t_lk_bank a where a.BankStatusFlag=1 and a.SavingBankFlag = 1";
	$qry = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows )
	{
		$datas[$rows['BankId']] = $rows['BankName'];
	}
	return $datas;
 }
 
 function getMaritallist(){
	$sql = " SELECT a.* FROM t_lk_marital_status a";
	$qry = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows )
	{
		$datas[$rows['Id']] = $rows['StatusDesc'];
	}
	return $datas;
 }
 
 
 /**
  ** get lookup Relation Of customer
  ** define of user
  ** return < array >
  **/
  
function CardType($id_payment_method=null)
 {
	$sql =" select a.CreditCardTypeId,a.CreditCardTypeDesc from t_lk_creditcardtype a";
	if(!is_null($id_payment_method))
	{
		$sql .= " where a.PaymentTypeId = ".$id_payment_method;
	}
	$qry = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows )
	{
		$datas[$rows['CreditCardTypeId']] = $rows['CreditCardTypeDesc'];
	}
	return $datas;
 }
 
 function cc_type_pattern()
 {
	$datas = array();
	$sql =" SELECT a.CreditCardTypeId,a.CreditCardTypeDesc,a.CreditCardTypePatern,a.CreditCardTypeLength
			FROM t_lk_creditcardtype a
			WHERE a.PaymentTypeId = 1";
	$qry = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows )
	{
		$datas[$rows['CreditCardTypeId']]['name'] = $rows['CreditCardTypeDesc'];
		$datas[$rows['CreditCardTypeId']]['pattern'] = $rows['CreditCardTypePatern'];
		$datas[$rows['CreditCardTypeId']]['length'] = $rows['CreditCardTypeLength'];
	}
	return $datas;
 }
 /**
  ** get lookup Relation Of customer
  ** define of user
  ** return < array >
  **/
  
 function ProductId( $CampaignId=0 )
 {
	$sql = " SELECT a.ProductId, a.ProductName 
			 FROM t_gn_product a left join t_gn_campaignproduct b on a.ProductId=b.ProductId
			 WHERE b.CampaignId='$CampaignId'
			 
			 ";
	$qry = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows )
	{
		$datas[$rows['ProductId']] = $rows['ProductName'];
	}
	return $datas;
}
  
 /**
  ** get lookup Relation Of customer
  ** define of user
  ** return < array >
  **/
  
function PaymentTypeId()
{
	$sql =" select a.PaymentTypeId,a.PaymentTypeDesc from t_lk_paymenttype a ";
	$qry = $this ->query($sql);
	foreach($qry -> result_assoc() as $rows )
	{
		$datas[$rows['PaymentTypeId']] = $rows['PaymentTypeDesc'];
	}
	return $datas;		
}
  
 /**
  ** get lookup Relation Of customer
  ** define of user
  ** return < array >
  **/
  
function ProductPlanId($CampaignId=0)
{
	$sql= " SELECT c.ProductPlan, c.ProductPlanName
			from t_gn_product a left join t_gn_campaignproduct b on a.ProductId=b.ProductId
			left join t_gn_productplan c on a.ProductId=c.ProductId
			where b.CampaignId='$CampaignId'
			group by c.ProductPlan ";
	$qry = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows ){
		$datas[$rows['ProductPlan']] = $rows['ProductPlanName'];
	}
	return $datas;
}

/**
 ** function get Campaign Name 
 ** Rnder By CampaignId 
 ** return < string >
 **/ 
 
 public function _CampaignName($_CampaignId=0 )
 {
	$_conds = 0;
	$sql = " SELECT a.CampaignName FROM t_gn_campaign a 
			 WHERE a.CampaignId = '$_CampaignId'"; 
	$qry = $this -> query($sql);
	if( !$qry -> EOF() )
	{
		$_conds = $qry -> result_get_value("CampaignName");
	}
	
	return $_conds;
}
 
 /**
 ** function get Agent name  
 ** render By Customer Id
 ** return < string >
 **/ 
 
 function TotalPremi( $CustomerId=0 )
 {
	$sql = " select sum(b.Premi) as jumlah from t_gn_policyautogen a 
			left join t_gn_policy b on a.PolicyNumber=b.PolicyNumber
			where a.CustomerId='$CustomerId' ";
	$qry = $this -> query($sql);
	return (INT)$qry -> result_get_value("jumlah");
 }
 
  
 /**
 ** function get Agent name  
 ** render By Customer Id
 ** return < string >
 **/ 
 
 public function SellerId( $_CustomerId=0,$_Class)
 {
	parent::__construct();
	
	$_conds = 0;
	$_SellerId = 0;
	$sql = "SELECT a.*, c.AssignSelerId 
			FROM t_gn_policyautogen a 
			LEFT JOIN t_gn_customer b on a.CustomerId=b.CustomerId
			LEFT JOIN t_gn_assignment c on b.CustomerId=c.CustomerId
			where a.CustomerId='$_CustomerId' ";
	$qry = $this -> query($sql);
	if( !$qry -> EOF() )
	{
		$_conds = $qry -> result_get_value("AssignSelerId");
		if( $_conds ){
			$_SellerId = $this -> Users -> getUsers( $_conds );
		}
	}
	
	return $_SellerId;
	
 }
 
 public function getProductType()
 {
	$sql = " select * from t_lk_producttype a ";
	$qry = $this -> query($sql);
	foreach( $qry -> result_assoc() as $rows )
	{
		$datas[$rows['ProductTypeId']] = $rows['ProductType'];
	}
	
	return $datas;
 }
 
  public function getProductCategory()
 {
	$sql = " SELECT b.ProductId, b.ProductName, a.product_category_id,a.product_category_code
			FROM t_gn_product_category a
			INNER JOIN t_gn_product b ON a.product_category_id = b.product_category_id
			WHERE b.ProductStatusFlag = 1 ";
	$qry = $this -> query($sql);
	foreach( $qry -> result_assoc() as $rows )
	{
		$datas['category_id'][$rows['ProductId']] = $rows['product_category_id'];
		$datas['category_name'][$rows['ProductId']] = $rows['product_category_code'];
	}
	
	return $datas;
 }
}

?>