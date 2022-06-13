<?php
	
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");

/**
 ** product < easy care blue >
 **	class untuk action product
 ** author : omens
 ** date : 2012-10-17
 */
	
class CignaCare extends mysql
{
 
 var $_Action;
 var $_ProductId;
 var $_CampaignId;
 var $_CustomerId;
 var $_PlanId;
 var $_PayModeId;
 
 function CignaCare()
 {
	parent::__construct();
	$this -> _Action = & $this -> escPost('action');
	$this -> _PlanId = & $this -> escPost('plan_plan'); 	
	$this -> _PayModeId= & $this -> escPost('plan_paymode');
	$this -> _ProductId = & $this -> escPost('ProductId');
	$this -> _CampaignId = & $this -> escPost('CampaignId');
	$this -> _CustomerId = & $this -> escPost('CustomerId');
	self::index();
 }
 
/** 
 ** _PlanProductId get data plan id 
 ** return < plan id >
 ** testing only
 **/
  
 function index()
 {
	switch( $this -> _Action )
	{	
		case 'update' : $this -> Update(); 	break;
		case 'plan'	  : $this -> Plan(); 	break;
	}
 }
 
/** 
 ** _PlanProductId get data plan id 
 ** return < plan id >
 ** testing only
 **/
 
 function _PlanProductId( $_ProductId='', $_Plan='',$_PayModeId ='', $_GroupId='', $_Age='' )
 {
	$_PlanId = 0;
	$_Premi  = 0;
	
	$sql = " SELECT F_GetPlanId('$_ProductId', '$_Plan', '$_PayModeId', '$_GroupId', '$_Age') as PlanId ";
	$qry = $this -> query($sql);
	if( !$qry -> EOF() ){
		$_PlanId = $qry -> result_get_value('PlanId');
	}
	
	if( $_PlanId!=null )
	{
		$sql = " SELECT a.ProductPlanPremium FROM t_gn_productplan a WHERE a.ProductPlanId='$_PlanId'";
		$qry = $this -> query($sql);
		if( !$qry -> EOF() )
		{
			$_Premi = $qry -> result_get_value('ProductPlanPremium'); 
		}
	}
	if( $_PlanId && $_Premi ){
		return array('PlanId' => $_PlanId, 'Premi' => $_Premi );
	}
	else
		return false;
 }
 
/** 
 ** _PlanProductId get data plan id 
 ** return < plan id >
 ** testing only
 **/
 
private function _Policy($_PolicyId=0)
{ 
    $sql = " SELECT a.*,c.* from t_gn_policy a 
			 LEFT JOIN t_gn_policyautogen b on a.PolicyNumber=b.PolicyNumber
		     LEFT JOIN t_gn_insured c on a.PolicyId=c.PolicyId
		     WHERE b.CustomerId='{$this -> _CustomerId}'
			 AND a.PolicyId='$_PolicyId'"; 
			 
	$qry = $this -> query($sql);
	
	if(is_object($qry)) return $qry;
	else
		return false;
}
 
/** 
 ** _PlanProductId get data plan id 
 ** return < plan id >
 ** testing only
 **/
 
function _SellerPlan()
{
	$sql ="select c.ProductPlan, c.ProductPlanName from t_gn_insured a 
		  left join t_gn_policy b on a.PolicyId=b.PolicyId
		  left join t_gn_productplan c on b.ProductPlanId=c.ProductPlanId
		  where a.CustomerId='".$this -> _CustomerId."' ";
	$qry = $this -> query($sql);
	if( is_object($qry)){
		return $qry;
	}
	else
		return false;

}

/** 
 ** _PlanProductId get data plan id 
 ** return < plan id >
 ** testing only
 **/
 
private function _BeneficeryBox()
{
	if($this -> havepost('benefBox')){
		$_box= EXPLODE(',',$this -> escPost('benefBox'));
		if( is_array($_box)){
			return $_box;
		}
		else
			return false;
	}
	else
		return true;
}

 /** 
 ** _PlanProductId get data plan id 
 ** return < plan id >
 ** testing only
 **/
 
 private function _InsuredBox()
{
	if($this -> havepost('insuranceBox')){
		$_box= EXPLODE(',',$this -> escPost('insuranceBox'));
		if( is_array($_box)){
			return $_box;
		}
		else
			return false;
	}
	else
		return true;
}

/** 
 ** _PlanProductId get data plan id 
 ** return < plan id >
 ** testing only
 **/
 
 private function _UpdateBenefiecery()
 {
	$_conds = 0;
	$_box = $this -> _BeneficeryBox();
	if( is_array($_box) )
	{
		foreach( $_box as $_key => $s_i )
		{
			$SQL_Update['SalutationId'] 		 = strtoupper($this -> escPost("txt_benef{$s_i}_title")); 
			$SQL_Update['PremiumGroupId'] 		 = strtoupper($this -> escPost("txt_benef{$s_i}_holdertype")); 
			$SQL_Update['RelationshipTypeId'] 	 = strtoupper($this -> escPost("txt_benef{$s_i}_rel")); 
			$SQL_Update['BeneficiaryFirstName']	 = strtoupper($this -> escPost("txt_benef{$s_i}_first")); 
			$SQL_Update['BeneficiaryLastName']	 = strtoupper($this -> escPost("txt_benef{$s_i}_lastname")); 
			$SQL_Update['BeneficieryPercentage'] = strtoupper($this -> escPost("txt_benef{$s_i}_persen")); 
			$SQL_Update['UpdatedById']			 = strtoupper($this -> getSession("UserId")); 
			$SQL_Update['BeneficiaryUpdatedTs']  = strtoupper(date('Y-m-d'));
			
		/**
		 ** Where conds 
		 ** define update data  
		 **/	
			
			$SQL_Wheres['CustomerId'] = $this -> _CustomerId;
			$SQL_Wheres['BeneficiaryId'] = $this -> escPost("BeneficeryId_{$s_i}"); 
			if( $this -> set_mysql_update("t_gn_beneficiary",$this -> SQLnull($SQL_Update),$SQL_Wheres)){
				$_conds+=1;
			}
		}
	}
	
	return $_conds;   
 }
 
/** 
 ** update data insured of _UpdateSpouse
 ** tape case of by index follower
 ** return < void >
 **/
 
private function _UpdateSpouse( $_ux='' )
{
 $_conds = 0;
 if( $_ux!='' ) 
 {
	$SQL_Update['InsuredIdentificationNum'] = strtoupper( $this -> escPost("txt_insurance_sp_idno"));
	$SQL_Update['IdentificationTypeId'] = strtoupper( $this -> escPost("cb_insurance_sp_idtype"));
	$SQL_Update['RelationshipTypeId'] = strtoupper( $this -> escPost("cb_insurance_sp_relation"));
	$SQL_Update['InsuredFirstName'] = strtoupper( $this -> escPost("txt_insurance_sp_firstname"));
	$SQL_Update['InsuredLastName'] = strtoupper( $this -> escPost("txt_insurance_sp_lastname"));
	$SQL_Update['PremiumGroupId'] = strtoupper( $this -> escPost("cb_insurance_sp_holdertype"));
	$SQL_Update['SalutationId'] = strtoupper( $this -> escPost("cb_insurance_sp_salut"));
	$SQL_Update['GenderId'] = strtoupper( $this -> escPost("txt_insurance_sp_gender"));
	$SQL_Update['InsuredDOB'] = strtoupper( $this -> formatDateEng($this -> escPost("txt_insurance_sp_dob")));
	$SQL_Update['InsuredAge'] = strtoupper( $this -> escPost("txt_insurance_sp_age")); 
	$SQL_Update['UpdatedById'] = strtoupper( $this -> getSession("UserId"));
	$SQL_Update['InsuredUpdatedTs'] = strtoupper(date('Y-m-d H:i:s'));
	
 /** wheres conditions 
  ** field < InsuredId >
  **/
  
	$SQL_Wheres['InsuredId'] = strtoupper($this -> escPost("edit_number_{$_ux}"));
	if( $this -> set_mysql_update("t_gn_insured",$this -> SQLnull($SQL_Update), $SQL_Wheres)){
		$_conds +=1; 
	}
 }
 return $_conds;
} 

/** 
 ** update data insured of _UpdateDependent
 ** tape case of by index follower
 ** return < void >
 **/
 
private function _UpdateDependent($_ux=0){
 $_conds = 0;
 if( $_ux!=0 ) 
 {
	$SQL_Update['InsuredIdentificationNum'] = strtoupper( $this -> escPost("txt_insurance_dp{$_ux}_idno"));
	$SQL_Update['IdentificationTypeId'] = strtoupper( $this -> escPost("cb_insurance_dp{$_ux}_idtype"));
	$SQL_Update['RelationshipTypeId'] = strtoupper( $this -> escPost("cb_insurance_dp{$_ux}_relation"));
	$SQL_Update['InsuredFirstName'] = strtoupper( $this -> escPost("txt_insurance_dp{$_ux}_firstname"));
	$SQL_Update['InsuredLastName'] = strtoupper( $this -> escPost("txt_insurance_dp{$_ux}_lastname"));
	$SQL_Update['PremiumGroupId'] = strtoupper( $this -> escPost("cb_insurance_dp{$_ux}_holdertype"));
	$SQL_Update['SalutationId'] = strtoupper( $this -> escPost("cb_insurance_dp{$_ux}_salut"));
	$SQL_Update['GenderId'] = strtoupper( $this -> escPost("txt_insurance_dp{$_ux}_gender"));
	$SQL_Update['InsuredDOB'] = strtoupper( $this -> formatDateEng($this -> escPost("txt_insurance_dp{$_ux}_dob")));
	$SQL_Update['InsuredAge'] = strtoupper( $this -> escPost("txt_insurance_dp{$_ux}_age")); 
	$SQL_Update['UpdatedById'] = strtoupper( $this -> getSession("UserId"));
	$SQL_Update['InsuredUpdatedTs'] = strtoupper(date('Y-m-d H:i:s'));
	
 /** wheres conditions 
  ** field < InsuredId >
  **/
  
	$SQL_Wheres['InsuredId'] = strtoupper($this -> escPost("edit_number_{$_ux}"));
	if( $this -> set_mysql_update("t_gn_insured",$this -> SQLnull($SQL_Update), $SQL_Wheres)){
		$_conds +=1; 
	}
 }
 
 return $_conds;
} 
 
/** 
 ** update data insured of tertanggug
 ** tape case of by index follower
 ** return < void >
 **/
  
 private function _UpdateInsured()
 {
	$_conds = 0;
	$_box = $this -> _InsuredBox();
	if( is_array($_box) ){
	  foreach( $_box as $_key => $s_i )
	  {
		if( $s_i==0 ) {
			if( $this -> _UpdateSpouse($s_i) ) $_conds+=1;
		}	
		else{
			if( $this -> _UpdateDependent($s_i) ) $_conds+=1;
		}	
	  }
	}
	return $_conds;   
 }
 
/** 
 ** action update data policy for
 ** specific product < easy care blue >
 ** return < void >
 **/
 
private function _UpdatePayers()
{
  $_Conds = 0;
  if ( $this -> havepost('CustomerId') )
  {
	$SQL_Update['SalutationId'] 		 = strtoupper( $this -> escPost("payer_salutation")); 
	$SQL_Update['GenderId'] 			 = strtoupper( $this -> escPost("payer_gender")); 
	$SQL_Update['IdentificationTypeId']	 = strtoupper( $this -> escPost("payer_holder_idtype")); 
	$SQL_Update['PaymentTypeId']		 = strtoupper( $this -> escPost("plan_paytype"));	
	$SQL_Update['RelationshipTypeId']    = strtoupper( $this -> escPost("frm_holder_rel")); 
	$SQL_Update['ProvinceId']			 = strtoupper( $this -> escPost("payer_province")); 
	$SQL_Update['CreditCardTypeId']		 = strtoupper( $this -> escPost("payer_card_type")); 
	$SQL_Update['UpdatedById']			 = strtoupper( $this -> getSession("UserId")); 
	$SQL_Update['PayerFirstName']		 = strtoupper( $this -> escPost("payer_first_name")); 
	$SQL_Update['PayerLastName']		 = strtoupper( $this -> escPost("payer_last_name")); 
	$SQL_Update['PayerDOB']				 = strtoupper( $this -> formatDateEng($this -> escPost("payer_dob"))); 
	$SQL_Update['PayerIdentificationNum']= strtoupper( $this -> escPost("payer_idno")); 
	$SQL_Update['PayerAddressLine1']	 = strtoupper( $this -> escPost("payer_address1")); 
	$SQL_Update['PayerAddressLine2']	 = strtoupper( $this -> escPost("payer_address2")); 
	$SQL_Update['PayerAddressLine3']     = strtoupper( $this -> escPost("payer_address3")); 
	$SQL_Update['PayerAddressLine4']     = strtoupper( $this -> escPost("payer_address4")); 
	$SQL_Update['PayerCity']		     = strtoupper( $this -> escPost("payer_city")); 
	$SQL_Update['PayerZipCode']			 = strtoupper( $this -> escPost("payer_zip_code")); 
	$SQL_Update['PayerHomePhoneNum']	 = strtoupper( $this -> escPost("payer_home_phone")); 
	$SQL_Update['PayerMobilePhoneNum']   = strtoupper( $this -> escPost("payer_mobile_phone")); 
	$SQL_Update['PayerWorkPhoneNum']	 = strtoupper( $this -> escPost("payer_office_phone")); 
	$SQL_Update['PayerOfficePhoneNum']   = strtoupper( $this -> escPost("payer_office_phone")); 
	$SQL_Update['PayerFaxNum']			 = strtoupper( $this -> escPost("payer_fax_number")); 
	$SQL_Update['PayerEmail']			 = strtolower( $this -> escPost("payer_email")); 
	$SQL_Update['PayerCreditCardNum']	 = strtoupper( $this -> escPost("payer_card_number")); 
	$SQL_Update['PayersBankId']			 = strtoupper( $this -> escPost("payer_bank")); 
	$SQL_Update['PayerCreditCardExpDate']= strtoupper( $this -> escPost("payer_expired_date")); 
	$SQL_Update['PayerUpdatedTs']		 = strtoupper( date("Y-m-d H:i:s"));

/** _Condition < CustomerId >**/

	$SQL_Wheres['CustomerId']  = strtoupper( $this -> escPost("CustomerId") );
	if( $this -> set_mysql_update("t_gn_payer",$this -> SQLnull($SQL_Update),$SQL_Wheres )){
		$_Conds+=1;
	}	
  }
  
  return $_Conds;
}


 /** 
  ** action update data policy for
  ** specific product < easy care blue >
  ** return < void >
  **/

private function _UpdatePremi()
{
	$_conds = 0;
	$_box = $this -> _InsuredBox();
	if( is_array($_box) ){
		foreach( $_box as $_key => $s_i )
		{
		  if( $s_i==0 )  // update premi for spouse 
		  {	
			//if( $this -> _Holder() ) { // update premi Holder before update spouse
			
				$_spouse_age = $this -> escPost("txt_insurance_sp_age");
				$_spouse_typ = $this -> escPost("cb_insurance_sp_holdertype");
				$_Policy_Id  = $this -> escPost("edit_number_{$s_i}");
				
				if( (INT)$_spouse_age > 0 )
				{
					$_PlanId = $this -> _PlanProductId($this -> _ProductId, $this -> _PlanId, $this -> _PayModeId, $_spouse_typ, $_spouse_age); 
					if( is_array($_PlanId) )
					{
						$_Policy = $this -> _Policy( $_Policy_Id );
						$_DataPolicy = $_Policy -> result_first_assoc();
						if( is_array( $_DataPolicy )) 
						{
							$SQL_Update['Premi'] = $_PlanId['Premi'];
							$SQL_Update['ProductPlanId'] = $_PlanId['PlanId'];
							$SQL_Wheres['PolicyId'] = $_DataPolicy['PolicyId'];
							if( $this -> set_mysql_update("t_gn_policy",$this -> SQLnull($SQL_Update), $SQL_Wheres)){
								$_conds+=1;
							}
						}
					}	
				}
			//}	
		  }
		  else  // update for dependent
		  {
			    $_dependent_age = $this -> escPost("txt_insurance_sp{$s_i}_age");
				$_dependent_typ = $this -> escPost("cb_insurance_sp{$s_i}_holdertype");
				$_Policy_Id  = $this -> escPost("edit_number_{$s_i}");
				
				if( (INT)$_dependent_age > 0 )
				{
					$_PlanId = $this -> _PlanProductId($this -> _ProductId, $this -> _PlanId, $this -> _PayModeId, $_dependent_typ, $_dependent_age); 
					if( is_array($_PlanId) )
					{
						$_Policy = $this -> _Policy( $_Policy_Id );
						$_DataPolicy = $_Policy -> result_first_assoc();
						if( is_array( $_DataPolicy )) 
						{
							$SQL_Update['Premi'] = $_PlanId['Premi'];
							$SQL_Update['ProductPlanId'] = $_PlanId['PlanId'];
							$SQL_Wheres['PolicyId'] = $_DataPolicy['PolicyId'];
							if( $this -> set_mysql_update("t_gn_policy",$this -> SQLnull($SQL_Update), $SQL_Wheres)){
								$_conds+=1;
							}
						}
					}	
				}
		  	}
		}
	}
	
	if( $this -> _Holder()) $_conds+=1;
	
	
	return $_conds;  
}
 
 /** 
  ** action update data policy for
  ** specific product < easy care blue >
  ** return < void >
  **/
 
 function Update()
 {
	$array = array("result"=>0,"error"=>"");
	if( $this -> _UpdatePremi() )
	{
		$this -> _UpdateInsured(); 
		$this -> _UpdateBenefiecery();
		$this -> _UpdatePayers();
		if( $this -> _UpdateCustomer() ){
			$array = array("result"=>1,"error"=>"Success, Update Premi By Plan..!");
		}
		else
			$array = array("result"=>0,"error"=>"Failed, Update Customer Data..!");
	}
	else
		$array = array("result"=>0,"error"=>"Failed, Update Premi By Plan..!");
		
		
	echo json_encode($array);	
 }
 
 /** 
  ** get Plan if selected product by User Spesfic
  ** specific product < easy care blue >
  ** return < array >
  **/
 
  private function Product()
 {
	$datas  = NULL;
	
	$sql = " SELECT c.ProductPlan, c.ProductPlanName
			 FROM t_gn_product a left join t_gn_campaignproduct b on a.ProductId=b.ProductId
			 left join t_gn_productplan c on a.ProductId=c.ProductId
			 where b.ProductId='".$this -> _ProductId."'
			 group by c.ProductPlan ";
	$qry = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows ){
		$datas[$rows['ProductPlan']] = $rows['ProductPlanName'];
	}
	
	return $datas;
 }
 
 
 /** 
  ** get Plan if selected product by User Spesfic
  ** specific product < easy care blue >
  ** return < void >
  **/
 
 function Plan()
 {	
	$content = array("content"=>"");
	$_SellerPlan = $this ->_SellerPlan();  
	if( is_object($_SellerPlan))
	{
		$rows = $_SellerPlan -> result_first_assoc();
		if( is_array( $rows ))
		{
			$content = array('content' => $this -> DBForm -> jpSelect("plan_plan",null,$this -> Product(),$rows['ProductPlan']),'onchange="getPremiByPlan(this.value);"');
		}
	}
	echo json_encode($content);
}
 
 /** 
  ** get benefit if selected product & Plan & Payment Type
  ** specific product < easy care blue >
  ** return < string json >
  **/
 
function _Holder()
 {
	$_holder_age = $this -> escPost("text_dob_size");
	$_holder_typ = $this -> escPost("cb_holder_holdertype");
	$_Policy_Id  = $this -> escPost("edit_holder");
	
	$_conds = 0;
	if( (INT)$_holder_age > 0 )
	{
		$_PlanId = $this -> _PlanProductId($this -> _ProductId, $this -> _PlanId, $this -> _PayModeId, $_holder_typ, $_holder_age); 
		if( is_array($_PlanId) )
		{
			$_Policy = $this -> _Policy( $_Policy_Id );
			$_DataPolicy = $_Policy -> result_first_assoc();
			if( is_array( $_DataPolicy )) 
			{
				$SQL_Update['Premi'] = $_PlanId['Premi'];
				$SQL_Update['ProductPlanId'] = $_PlanId['PlanId'];
				$SQL_Wheres['PolicyId'] = $_DataPolicy['PolicyId'];
				if( $this -> set_mysql_update("t_gn_policy",$this -> SQLnull($SQL_Update), $SQL_Wheres)){
					$_conds+=1;
				}
			}
		}	
	}
	return $_conds;
 }
 
  
 /** 
  ** get benefit if selected product & Plan & Payment Type
  ** specific product < easy care blue >
  ** return < string json >
  **/
 
 private function _UpdateCustomer()
 {
	$_Conds = 0;
	
	$SQL_Update['CustomerIdentificationNum'] = strtoupper( $this -> escPost("frm_holder_idno") );
	$SQL_Update['IdentificationTypeId'] = strtoupper( $this -> escPost("cb_holder_idtype") );
	$SQL_Update['CustomerFirstName'] = strtoupper( $this -> escPost("frm_holder_firstname") );
	$SQL_Update['CustomerLastName'] = strtoupper( $this -> escPost("frm_holder_lastname	") );
	$SQL_Update['ProvinceId'] = strtoupper( $this -> escPost("payer_province") );
	$SQL_Update['RelationId'] = strtoupper( $this -> escPost("frm_holder_rel") );
	$SQL_Update['SalutationId'] = strtoupper( $this -> escPost("frm_holder_title") );
	$SQL_Update['GenderId'] = strtoupper( $this -> escPost("frm_holder_gender") );
	$SQL_Update['CustomerDOB'] = strtoupper( $this -> formatDateEng($this -> escPost("frm_holder_dob")));
	
	$SQL_Wheres['CustomerId'] = strtoupper( $this -> escPost("CustomerId") );
	
	if( $this -> set_mysql_update("t_gn_customer",$this -> SQLnull($SQL_Update),$SQL_Wheres) ){
		$_Conds =1;
	}
	
	return $_Conds;	
 }
 
 
}
new CignaCare(); 
?>