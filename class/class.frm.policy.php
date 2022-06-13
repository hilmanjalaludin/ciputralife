	<?php
	
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	
	/*
	 *	class untuk action product
	 *  author : omens
	 *  date : 2012-10-17
	*/
class FormPolicy extends mysql
{
	var $action;
	var $PolicyPrefix  = array();
	var $MaxlengthPolicy = array();
	var $PolicyLastId;
	
	
	function __construct()
	{
		parent::__construct();
		$this -> action = $this->escPost('action');
		if( $this -> havepost('campaignid'))
		{
			
		}
	}
		
	function index()
		{
			if( $this->havepost('action'))
			{
				switch($this->action)
				{
					case 'hitung_dob_customer'		: $this	-> HitungUserDob(); 		break;
					case 'get_plan_customer'		: $this	-> getPlanByProduct(); 		break;
					case 'hitung_premi_customer' 	: $this	-> HitungPremiCustomer(); 	break;
					case 'get_prefix_cardnumber' 	: $this	-> validateCardNumber(); 	break;
					case 'save_create_policy'		: $this	-> SaveCreatePolicy(); 		break;
					case 'value_sec_num'			: $this	-> validCardNumber();  		break;
					case 'expired_action'			: $this	-> getExpired2Mon();  		break;
					case 'interest_with_spouse'		: $this	-> InterestWithSpouse();  	break;
				}
			}
		}
		
		
	/** InterestWithSpouse() **/
	function InterestWithSpouse()
		{
			$array = array('result'=> 0);
			if( $this -> havepost('call_status_id'))
			{
				$sql = " SELECT a.CallReasonSpouse FROM t_lk_callreason a 
						 WHERE a.CallReasonId='".$this -> escPost('call_status_id')."' ";
				$qry = $this -> query($sql);
				if( $qry -> result_singgle_value() > 0 ) {
					$array = array('result'=> 1);
				}	
			}
			echo json_encode($array);
		}
			
	/** valid card Number **/
	
	function get_validasi_card_type($value='')
		{
			$sql= "select count(a.ValidCCPrefixId) from t_lk_validccprefix a where a.ValidCCPrefix REGEXP('".$value."')";
			$qry = $this -> query($sql); 
			
			if( $qry -> result_singgle_value() > 0 ) return 1;
			else 
				return 0;
		
		}
		
	/** nect valid card Number with Formula **/	
	function validCardNumber()
		{
			$array = array('result'=> 0);
			
			$sizeNumber = 0; $sizePosition = 10; $sizeLength =0; 
			if( $this -> havepost('number') )
			{	
				$number_post = $this -> escPost('number');
				if( strlen($number_post) >=6 ){
					$card_number = substr($number_post,0,6);
					$array = array('result' => $this -> get_validasi_card_type($card_number) );
				}
			}	
			
			echo json_encode($array);
			
		}	
		
/** prefix product ******/
	
	private function get_policy_prefix($ProductId=NULL)
		{
			$sql =" SELECT a.ProductId, a.PrefixChar, a.PrefixLength 
					FROM t_gn_productprefixnumber a 
					INNER JOIN t_gn_product b on a.ProductId=b.ProductId 
					LEFT JOIN t_gn_campaignproduct c on a.ProductId=c.ProductId
					WHERE c.CampaignId=".$this -> escPost('campaignid')." AND a.PrefixFlagStatus=1 ";
					
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['ProductId']]['prefix_chars']  = $rows['PrefixChar'];
				$datas[$rows['ProductId']]['prefix_length'] = $rows['PrefixLength'];
			}
			
			return $datas[$ProductId];
		}
	/** get last ID **/
	
	private function get_general_number($policyNumber = NULL, $PrefixCode = NULL )
	{
		
			$policeLastId = $policyNumber;
			if( !empty( $policeLastId) && is_array($PrefixCode) )
			{
				$result = substr( $PrefixCode['prefix_chars'],0,strlen($PrefixCode['prefix_chars']) - (strlen($policeLastId)+1) );
				$result.= $policeLastId.'T'; 
				
				if( strlen($result) == $PrefixCode['prefix_length']){
					return $result;
				}
				else{
					return false;
				}
			}
			else{	
				return true;
		}
	}
		
	/** cek before inserting PRODUCT **/
	
	private function FProductPolicy($ProductId)
		{
			$sql = " select count(a.PolicyAutoGenId) from t_gn_policyautogen a where a.productId='$ProductId'";
			$qry = $this -> query($sql);
			if( !$qry -> EOF() )
			{
				if( $qry -> result_singgle_value() > 0 ) 
					return true;
				else 
					return false;
			}
			else
				return false;
		}
		
	/** cek before inserting PRODUCT **/
	private function FCustomerPolicy($CustomerId)
		{
			$sql = " select count(a.PolicyAutoGenId) from t_gn_policyautogen a where a.CustomerId='$CustomerId'";
			$qry = $this -> query($sql);
			if( !$qry -> EOF() )
			{
				if( $qry -> result_singgle_value() > 0 ) 
					return true;
				else 
					return false;
			}
			else
				return false;
		}
		
		
	/** function cek insurance duplikasi group **/
	 private function FInsuranceGroup($PremiumGroupId='')
		{
			$sql = "select count(*) from t_gn_insured a where a.CustomerId='".$_REQUEST['customerid']."' and a.PremiumGroupId='".$PremiumGroupId."'";
			$jumlah = $this -> valueSQL($sql);
			if( ($jumlah > 0)):
				if( ($PremiumGroupId==2) || ($PremiumGroupId==3) ):
					return false;
				else:
					return true;
				endif;
			else:
				return true;
			endif;	
		} 
		
		
	/** Policy Generate **/
	function PolicyGenerate($ProductId, $CustomerId )
		{
			$V_UNIX_NUMBER = NULL;
			
			if( ($ProductId !=NULL) && ($CustomerId!=NULL))
			{
				if( $this -> FProductPolicy( $ProductId ) )
				{
					if( $this -> FCustomerPolicy( $CustomerId ) )
					{
						$V_SQL =" select a.PolicyNumber from t_gn_policyautogen a where a.CustomerId='$CustomerId' AND a.ProductId='$ProductId' ";
						$V_UNIX_NUMBER = $this -> valueSQL($V_SQL);
					}	
					else
					{
						$V_SQL = " select (max(a.PolicyLastNumber)+1)  from t_gn_policyautogen a where a.ProductId='$ProductId'";
						$V_UNIX_RESULT = $this ->valueSQL($V_SQL);
						if( $V_UNIX_RESULT!='')
						{ 
							$V_UNIX_NUMBER = $this -> get_general_number($V_UNIX_RESULT, $this -> get_policy_prefix($ProductId));
							if( $V_UNIX_NUMBER)
							{
								$V_DATAS = array(
											'PolicyLastNumber' => $V_UNIX_RESULT, 
											'CustomerId'=> $CustomerId, 
											'ProductId'=> $ProductId, 
											'PolicyNumber'=> $V_UNIX_NUMBER 
										);
								$this -> set_mysql_insert("t_gn_policyautogen", $V_DATAS, $V_DATAS);
							}	
						}
					}
				}
				else
				{
					$V_SQL = " SELECT MAX(a.PolicyLastNumber) FROM t_gn_policyautogen a  WHERE a.CustomerId='$CustomerId' AND a.ProductId='$ProductId'";
					$V_QRY = $this -> query($V_SQL);
					$V_UNIX_RESULT = ($V_QRY -> result_singgle_value()+1);
					if($V_UNIX_RESULT!='')
					{
						$V_UNIX_NUMBER = $this -> get_general_number($V_UNIX_RESULT, $this -> get_policy_prefix($ProductId));
						if($V_UNIX_NUMBER)
						{
							$V_DATAS = array(
											'PolicyLastNumber' => $V_UNIX_RESULT, 
											'CustomerId'=> $CustomerId, 
											'ProductId'=> $ProductId, 
											'PolicyNumber'=> $V_UNIX_NUMBER 
										);
										
							$this -> set_mysql_insert("t_gn_policyautogen", $V_DATAS,$V_DATAS);			
						}
					}	
				}
			}
			
			return 	$V_UNIX_NUMBER;
		}	
			
/** efective date **/
	private function getCutOffDate()
		{
			$qry = $this -> query("SELECT a.CutoffDate FROM t_lk_cutoffdate a WHERE a.CutoffMonth=MONTH(NOW())");
			if( !$qry -> EOF())
			{
				return $qry -> result_singgle_value();
			}
		}
		
/** efective date **/
	private function getEfectiveDate()
		{	
			$qry = $this -> query("SELECT CONCAT( F_getEfectiveDate('".$this -> getCutOffDate()."', '".date('Y-m-d')."'),' ',time(now()) )");
			
			if(!$qry -> EOF() )
			{
				return $qry -> result_singgle_value();
			}
		}
	
  
	
 
	/** extract age **/
	
	private function spliteAge($POST_AGE)
	{
		$V_DATA = explode(" ",$POST_AGE);
		if( is_array($V_DATA)){ return trim($V_DATA[0]); }
		else return null;	
	}
	
	/** cek table **/
	
	private function getJumlahRows($group_premi)
	{
		$sql = "select count(a.InsuredId) from t_gn_transaction_policy a where a.CustomerId =".$_REQUEST['customerid']." and a.PremiumGroupId =".$group_premi."";
		$qry = $this -> query($sql);
		return $qry -> result_singgle_value();
	}
	
/** @ hitung Premi **/
 	
	function HitungPremiCustomer()
		{
			
			$ProductId 	= EXPLODE(',',$this -> escPost('productid'));
			$url_string = EXPLODE('~',$this -> escPost('urlstring'));
			$GroupPremi = ARRAY(1=>'Dependent',2=>'Main Holder',3=>'Spouse');
			$JsonData	= ARRAY("size_premi"=> 0,"content"=>"");
			$pay_mode 	= $this -> escPost('paymode');
			
			$content = "<table border=0 cellpadding=8 cellspacing=0 align='center' width='100%'><tr>";
			$content .=" <th style='background-color:#BBB000;color:#FFFFFF;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>Group Premi</th>";
			foreach( $ProductId as $v => $colsname )
				{
					$content .="<th style='background-color:#BBB000;color:#FFFFFF;border-left:1px solid #dddddd;border-top:1px solid #dddddd;'>Premi</th> ";
				}
			$content .="</tr> ";
			
			$totals= 0;
			foreach($url_string as $key => $_string)
			{
				$content.="<tr>";
				
				$v_string = explode("|",$_string);
				$content.="<td style='background-color:#FFFFFF;color:#000000;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' >".$GroupPremi[$v_string[0]]."</td>";	
				foreach( $ProductId as $key => $valueId)
				{
					$sql = " SELECT F_GetPremiumAge('".$this->escPost('planid')."','".$valueId."', '".$pay_mode."','".trim($v_string[0])."', '".trim($v_string[1])."')";
					$qry = $this -> query($sql);
					
					if( !$qry -> EOF() )
					{
						$summary = $qry -> result_singgle_value();
						$content.="<td style='background-color:#FFFFFF;color:#000000;border-left:1px solid #dddddd;border-top:1px solid #dddddd;text-align:right;' >".formatRupiah($summary)."</td>";	
						$totals += $summary;
					}
				}
				$content.="</tr>";	
			}
			$content.="<tr>
							<td style='background-color:blue;color:#FFFFFF;border-left:1px solid #dddddd;border-top:1px solid #dddddd;' >Totals</td>
							 <td style='background-color:blue;color:#FFFFFF;border-left:1px solid #dddddd;border-top:1px solid #dddddd;text-align:right;' colspan=\"".count($ProductId)."\">".formatRupiah($totals)."</td>
						</tr>";
						
			$content.="</table>";
			
			if( $totals )
			{
				$JsonData	= array("size_premi"=> $totals,"content" => $content);
			}
			
			echo json_encode($JsonData);	
		}
		
/** get Premi by PlanId ***/
	function get_premi_by_plan($PlanId = 0 )
	{
		if( $PlanId!=0 )
		{
			$sql = " select a.ProductPlanPremium as jml from t_gn_productplan a where a.ProductPlanId='$PlanId' ";
			$qry  = $this -> query($sql);
			if( $qry -> result_singgle_value() > 0 ) 
				return $qry -> result_singgle_value();
			else
				return NULL;
			
		}
	}
			
/** get Productplan ID **/
	
	private function getProductPlanId($V_PRODUCT,$V_PLAN,$V_GROUP, $V_AGES)
	{	
		$V_PAY_MODE  = $this -> escPost('plan_paymode');
		$sql = "SELECT F_GetPlanId
				(
					'".$V_PRODUCT."',
					'".$V_PLAN."',
					'".$V_PAY_MODE."',
					'".$V_GROUP."', 
					'".$V_AGES."' 
				) ";
				
		$qry = $this -> query($sql);
		if( !$qry -> EOF() )
		{	
			return $qry -> result_singgle_value();			
		}	
	}	
	
/** main insurance ( Holder )**/

	private function SQL_Holder($SQL_insert)
	{
		$totals = 0;
		foreach( $SQL_insert as $Key => $SQL_Data )
		{
			if( $this -> set_mysql_insert("t_gn_insured",$SQL_Data))
			{
				$totals++;
			}
		}	
	}
	
/** main insurance (SPOUSE) **/

	private function SQL_Spouse($SQL_insert)
	{
		$totals = 0;
		foreach( $SQL_insert as $Key => $SQL_Datas )
		{
			
			foreach( $SQL_Datas as $Keys => $SQL_Data)
			{
				if( $this -> set_mysql_insert('t_gn_insured',$SQL_Data))
				{
					$totals++;
				}
			}	
		}	
	}

/** main insurance ( Dependent ) **/

	private function SQL_Depend( $SQL_insert )
	{
		$totals = 0;
		foreach( $SQL_insert as $Key => $SQL_Datas )
		{
			foreach( $SQL_Datas as $Keys => $SQL_Data)
			{
				if( $this -> set_mysql_insert('t_gn_insured',$SQL_Data))
				{
					$totals++;
				}
			}	
		}	
	}	
	
/** insert payers **/
 
	private function SQL_Payers( $SQL_insert )
	{
		$totals = 0;
		foreach( $SQL_insert as $Key => $SQL_Data )
		{
			if( $Key == 0 )
			{
				if( $this ->set_mysql_insert('t_gn_payer',$SQL_Data)){
					$totals++;
				}else{
					echo "\nGak ke insert!";
				}
			}
			
		}
	}
	
	
/** insert to policy **/
	
	private function insertPolicy($V_PLAN_ID, $V_UNIX_NUMBER)
	{
		$get_insert_id = 0;
		if( ($V_PLAN_ID!='') )
		{
			$SQL_Policy['ProductPlanId'] =	$V_PLAN_ID; 
			$SQL_Policy['PolicySalesDate']= date('Y-m-d H:i:s');
			$SQL_Policy['PolicyNumber']	= $V_UNIX_NUMBER;
			$SQL_Policy['PolicyEffectiveDate'] = $this -> getEfectiveDate();
			$SQL_Policy['Premi'] = $this -> get_premi_by_plan($V_PLAN_ID);
			if( $this -> set_mysql_insert('t_gn_policy',$SQL_Policy))
			{
				$get_insert_id = $this -> get_insert_id();
			}
		}		
		
		return $get_insert_id;
	}
		
		
/** genarate prefix and card number **/
		
	private function concateCardNumber()
	{
		if( $this -> havepost('payer_card_number') )
		{
			return $this -> escPost('payer_card_number');
		}
		else
			return NULL;
	}
	
	
 /* update DOB ***/
	
	private function UpdateDOB()
		{
			
			if( $this -> havepost('customerid') && $this ->havepost('frm_holder_dob') 
				&&  $this -> havepost('frm_holder_rel') &&  $this -> havepost('cb_holder_idtype') )
			{
				$sql = "UPDATE t_gn_customer a 
						SET a.CustomerDOB = '".$this -> formatDateEng($this -> escPost('frm_holder_dob'))."',
							a.IdentificationTypeId ='".$this -> escPost('cb_holder_idtype')."',
							a.GenderId = '".$this -> escPost('frm_holder_gender')."',
							a.SalutationId = '".$this -> escPost('frm_holder_title')."',
							a.RelationId = '".$this -> escPost('frm_holder_rel')."',
							a.CustomerFirstName= '".strtoupper($this -> escPost('frm_holder_firstname'))."'	
						WHERE a.CustomerId='".$this -> escPost('customerid')."' LIMIT 1 ";
				
				$this ->execute($sql,__FILE__,__LINE__);			

			}
			
		}
		
	/* insert benefiecery ****/
	
		private function insertBenfeciery()
		{
						
			if( $this ->havepost('benefBox'))
			{
				if( $this ->escPost('benefBox')!='')
				{
					$listBenef = explode(",",$this ->escPost('benefBox'));
					
					foreach($listBenef as $key=>$val)
					{
						$benef_datas['CustomerId']						= $_REQUEST['customerid'];
						$benef_datas['SalutationId']					= $_REQUEST['txt_benef'.$val.'_title'];
						$benef_datas['GenderId']					    = $_REQUEST['cb_benef'.$val.'_gender'];
						$benef_datas['PremiumGroupId']					= 4;//di pelak
						$benef_datas['RelationshipTypeId']				= $_REQUEST['txt_benef'.$val.'_rel'];
						$benef_datas['CreatedById']						= $this -> getSession('UserId');
						$benef_datas['UpdatedById']						= $this -> getSession('UserId');
						$benef_datas['BeneficiaryFirstName']			= $_REQUEST['txt_benef'.$val.'_first'];
						$benef_datas['BeneficiaryLastName']				= $_REQUEST['txt_benef'.$val.'_lastname'];
						$benef_datas['GenderId']						= $_REQUEST['txt_benef'.$val.'_gender'];
						$benef_datas['BeneficieryPercentage']			= $_REQUEST['txt_benef'.$val.'_persen'];
						$benef_datas['BeneficiaryCreatedTs']			=  date('Y-m-d H:i:s');
						$benef_datas['BeneficiaryUpdatedTs']			=  date('Y-m-d H:i:s');
						//print_r($benef_datas);
						$this -> set_mysql_insert('t_gn_beneficiary',$benef_datas);
					}
				}
			}
		}
		
		
	/** save Policy create **/
	
	function SaveCreatePolicy()
	{
			$value_postdata = EXPLODE(',',$this -> escPost('ProductId'));
			$CustomerId = $this -> escPost('customerid');
			$CampaignId = $this -> escPost('campaignid');
			$PlanSelect = $this -> escPost('plan_plan');
			
			$DATAS_ERROR_VALUE = array('result'=> 0, 'policy' =>0 );
			$V_POLICY_INUM = array();
			$V_UNIX_NUMBER = array();
			
			$listBenef = explode(",",$this ->escPost('benefBox'));
			
			if( !$this -> FCustomerPolicy($CustomerId) )
			{
				foreach($value_postdata as $key => $ProductId )
				{
					$V_UNIX_NUMBER[$key]= $this -> PolicyGenerate($ProductId, $CustomerId);
					
					if( $V_UNIX_NUMBER[$key]!='')
					{	
						
						if( $this -> havepost('holder_age') )
						{
							$V_HOLDER_TYPE = ( $this -> havepost('cb_holder_holdertype')? $this -> escPost('cb_holder_holdertype'):NULL);
							if( $this -> FInsuranceGroup($V_HOLDER_TYPE))
							{
								$V_POLICY_INUM[$key] = $this -> insertPolicy($this -> getProductPlanId($ProductId, $PlanSelect, $V_HOLDER_TYPE , trim($this -> escPost('holder_age') ) ),$V_UNIX_NUMBER[$key]);
								if( ($V_POLICY_INUM[$key]!=''))
								{
								 /** sql insert untuk holder data ***/
									$SQL_HOLDER[$key]['PolicyId'] = $V_POLICY_INUM[$key];
									$SQL_HOLDER[$key]['InsuredDOB'] = $this -> formatDateEng($this -> escPost('frm_holder_dob'));
									$SQL_HOLDER[$key]['CreatedById'] = $this -> getSession('UserId');
									$SQL_HOLDER[$key]['UpdatedById'] = $this -> getSession('UserId');
									$SQL_HOLDER[$key]['CustomerId'] = $this -> escPost('customerid');
									//$SQL_HOLDER[$key]['RelationId'] = $this -> escPost('customerid');
									//$SQL_HOLDER[$key]['ProvinceId'] = $this -> escPost('payer_province');
									$SQL_HOLDER[$key]['SalutationId'] = $this -> escPost('frm_holder_title');
									$SQL_HOLDER[$key]['GenderId'] = $this -> escPost('frm_holder_gender');
									$SQL_HOLDER[$key]['IdentificationTypeId']= $this -> escPost('cb_holder_idtype');
									$SQL_HOLDER[$key]['PremiumGroupId'] = $this -> escPost('cb_holder_holdertype');
									$SQL_HOLDER[$key]['RelationshipTypeId']	= $this -> escPost('frm_holder_rel');
									$SQL_HOLDER[$key]['InsuredFirstName'] = strtoupper($this -> escPost('frm_holder_firstname'));
									$SQL_HOLDER[$key]['InsuredLastName'] = strtoupper($this -> escPost('frm_holder_lastname'));
									$SQL_HOLDER[$key]['InsuredAge']	= $this -> escPost('holder_age');
									$SQL_HOLDER[$key]['InsuredIdentificationNum'] = $this -> escPost('frm_holder_idno');
									$SQL_HOLDER[$key]['InsuredCreatedTs'] = date('Y-m-d H:i:s');
									$SQL_HOLDER[$key]['InsuredUpdatedTs'] = date('Y-m-d H:i:s');
									
								 /** sql insert untuk holder Payers ***/
								 
									$SQL_PAYERS[$key]['CustomerId'] = $this -> escPost('customerid');
									$SQL_PAYERS[$key]['SalutationId']= $this -> escPost('payer_salutation');
									$SQL_PAYERS[$key]['GenderId'] = $this -> escPost('payer_gender');
									$SQL_PAYERS[$key]['PremiumGroupId'] = $this -> escPost('cb_holder_holdertype');
									$SQL_PAYERS[$key]['PayerIdentificationNum'] = $this -> escPost('payer_idno');
									$SQL_PAYERS[$key]['ProvinceId']	= $this -> escPost('payer_province');
									$SQL_PAYERS[$key]['CreditCardTypeId'] = $this -> escPost('payer_card_type');
									$SQL_PAYERS[$key]['CreatedById'] = $this -> getSession('UserId');
									$SQL_PAYERS[$key]['UpdatedById'] = $this -> getSession('UserId');
									$SQL_PAYERS[$key]['PayerFirstName'] = strtoupper($this -> escPost('payer_first_name'));
									$SQL_PAYERS[$key]['PayerLastName'] = strtoupper($this -> escPost('payer_last_name'));
									$SQL_PAYERS[$key]['PayerDOB'] = $this -> formatDateEng($this -> escPost('payer_dob'));
									$SQL_PAYERS[$key]['PayerAddressLine1'] = strtoupper($this -> escPost('payer_address1'));
									$SQL_PAYERS[$key]['PayerAddressLine2'] = strtoupper($this -> escPost('payer_address2'));
									$SQL_PAYERS[$key]['PayerAddressLine3'] = strtoupper($this -> escPost('payer_address3'));
									$SQL_PAYERS[$key]['PayerAddressLine4'] = strtoupper($this -> escPost('payer_address4'));
									$SQL_PAYERS[$key]['PayerCity'] = strtoupper($this -> escPost('payer_city'));
									$SQL_PAYERS[$key]['PayerZipCode'] = $this -> escPost('payer_zip_code');
									$SQL_PAYERS[$key]['PayerHomePhoneNum'] = $this -> escPost('payer_mobile_phone');
									$SQL_PAYERS[$key]['PayerMobilePhoneNum'] = $this -> escPost('payer_home_phone');
									$SQL_PAYERS[$key]['PayerWorkPhoneNum'] = $this -> escPost('payer_office_phone');
									$SQL_PAYERS[$key]['PayerFaxNum'] = $this -> escPost('payer_fax_number');
									$SQL_PAYERS[$key]['PayerEmail'] = $this -> escPost('payer_email');
									$SQL_PAYERS[$key]['PayerCreditCardNum']	 = $this -> concateCardNumber();
									$SQL_PAYERS[$key]['PayerCreditCardExpDate'] = $this -> escPost('payer_expired_date');
									$SQL_PAYERS[$key]['PayersBankId'] = $this -> escPost('payer_bank');
									$SQL_PAYERS[$key]['PayerCreatedTs'] = date('Y-m-d H:i:s');
									$SQL_PAYERS[$key]['PayerUpdatedTs'] = date('Y-m-d H:i:s');	
								}
							}
						}
						
						if( $this ->havepost('insuranceBox') )
						{
							$ListBox = EXPLODE(",", $this ->escPost('insuranceBox'));
							$V_POLICY_INUM_SP = array();
							foreach($ListBox as $keys => $key_follow_box)
							{
								if( $key_follow_box==0)
								{
									if( $this -> havepost('txt_insurance_sp_age') )
									{
										$V_HOLDER_TYPE = ( $this -> havepost('cb_insurance_sp_holdertype')? $this -> escPost('cb_insurance_sp_holdertype'):NULL);	
										if( $this -> FInsuranceGroup( $V_HOLDER_TYPE ) )
										{
											$V_POLICY_INUM_SP[$key][$keys]= $this -> insertPolicy( $this -> getProductPlanId( $ProductId, $PlanSelect,$V_HOLDER_TYPE ,$this -> escPost('txt_insurance_sp_age')),$V_UNIX_NUMBER[$key]);
											if( ($V_POLICY_INUM_SP[$key][$keys]) )
											{
											  /** sql insert untuk SPOUSE ***/
												 
												$SQL_SPOUSE[$key][$keys]['PolicyId'] 	=  $V_POLICY_INUM_SP[$key][$keys];
												$SQL_SPOUSE[$key][$keys]['InsuredDOB']	= $this -> formatDateEng($this -> escPost('txt_insurance_sp_dob'));
												$SQL_SPOUSE[$key][$keys]['InsuredAge']	= $this -> escPost('txt_insurance_sp_age');
												$SQL_SPOUSE[$key][$keys]['CreatedById']= $this -> getSession('UserId');
												$SQL_SPOUSE[$key][$keys]['UpdatedById']= $this -> getSession('UserId');
												$SQL_SPOUSE[$key][$keys]['CustomerId'] = $this -> escPost('customerid');
												$SQL_SPOUSE[$key][$keys]['SalutationId'] = $this -> escPost('cb_insurance_sp_salut');
												$SQL_SPOUSE[$key][$keys]['GenderId'] = $this -> escPost('txt_insurance_sp_gender');
												$SQL_SPOUSE[$key][$keys]['IdentificationTypeId']= $this -> escPost('cb_insurance_sp_idtype');
												$SQL_SPOUSE[$key][$keys]['PremiumGroupId'] = $this -> escPost('cb_insurance_sp_holdertype');
												$SQL_SPOUSE[$key][$keys]['RelationshipTypeId']	= $this -> escPost('cb_insurance_sp_relation');
												$SQL_SPOUSE[$key][$keys]['InsuredFirstName'] = strtoupper($this -> escPost('txt_insurance_sp_firstname'));
												$SQL_SPOUSE[$key][$keys]['InsuredLastName'] = strtoupper($this -> escPost('txt_insurance_sp_lastname'));
												$SQL_SPOUSE[$key][$keys]['InsuredIdentificationNum'] = $this -> escPost('txt_insurance_sp_idno');
												$SQL_SPOUSE[$key][$keys]['InsuredCreatedTs'] = date('Y-m-d H:i:s');
												$SQL_SPOUSE[$key][$keys]['InsuredUpdatedTs'] = date('Y-m-d H:i:s');
												
											}	
										}
									}
								}		
								else
								{
									if( $this -> havepost('txt_insurance_dp'.$key_follow_box.'_age') )
									{
										$V_HOLDER_TYPE = ( $this -> havepost('cb_insurance_dp'.$key_follow_box.'_holdertype')? $this -> escPost('cb_insurance_dp'.$key_follow_box.'_holdertype'):NULL);	
										if( $this -> FInsuranceGroup( $V_HOLDER_TYPE ) )
										{	
											$V_POLICY_INUM_DP[$key][$keys] = $this -> insertPolicy( $this -> getProductPlanId( $ProductId, $PlanSelect,$V_HOLDER_TYPE  ,$this -> escPost('txt_insurance_dp'.$key_follow_box.'_age')), $V_UNIX_NUMBER[$key]);
											if( ($V_POLICY_INUM_DP[$key][$keys]))
											{
												/** sql insert untuk Dependent ***/
												
												$SQL_DEPEND[$key][$keys]['PolicyId'] = $V_POLICY_INUM_DP[$key][$keys];
												$SQL_DEPEND[$key][$keys]['CustomerId'] = $this -> escPost('customerid');
												$SQL_DEPEND[$key][$keys]['SalutationId'] = $this -> escPost('cb_insurance_dp'.$key_follow_box.'_salut'); 
												$SQL_DEPEND[$key][$keys]['GenderId']  = $this -> escPost('cb_insurance_dp'.$key_follow_box.'_gender');
												$SQL_DEPEND[$key][$keys]['PremiumGroupId'] = $this -> escPost('cb_insurance_dp'.$key_follow_box.'_holdertype');
												$SQL_DEPEND[$key][$keys]['RelationshipTypeId'] 	= $this -> escPost('cb_insurance_dp'.$key_follow_box.'_rel');
												$SQL_DEPEND[$key][$keys]['InsuredFirstName'] = strtoupper($this -> escPost('txt_insurance_dp'.$key_follow_box.'_firstname'));
												$SQL_DEPEND[$key][$keys]['InsuredLastName'] = strtoupper($this -> escPost('txt_insurance_dp'.$key_follow_box.'_lastname'));
												$SQL_DEPEND[$key][$keys]['InsuredDOB'] = $this -> formatDateEng($this -> escPost('txt_insurance_dp'.$key_follow_box.'_dob'));
												$SQL_DEPEND[$key][$keys]['InsuredAge']  = $this -> escPost('txt_insurance_dp'.$key_follow_box.'_age');
												$SQL_DEPEND[$key][$keys]['CreatedById']  = $this -> getSession('UserId');
												$SQL_DEPEND[$key][$keys]['UpdatedById']  = $this -> getSession('UserId');
												$SQL_DEPEND[$key][$keys]['InsuredCreatedTs'] = date('Y-m-d H:i:s');
												$SQL_DEPEND[$key][$keys]['InsuredUpdatedTs'] = date('Y-m-d H:i:s');
											}
										}
									}	
								}
							}
						}
						
						/** insert Benfiecery **/
						$this -> insertBenfeciery();
					}
				}
				/*echo"\n";
				print_r($SQL_HOLDER);
				echo"-----------------------------------------\n";
				print_r($SQL_PAYERS);
				echo"-----------------------------------------\n";
				print_r($SQL_SPOUSE);
				echo"\n";*/
				
				$this -> SQL_Holder($SQL_HOLDER);
				$this -> SQL_Payers($SQL_PAYERS);
				$this -> SQL_Spouse($SQL_SPOUSE);
				$this -> SQL_Depend($SQL_DEPEND);
				//return false;
				if(count($V_UNIX_NUMBER)> 0 ){
					$DATAS_ERROR_VALUE = array('result'=> 1, 'policy' => $V_UNIX_NUMBER );
					$this -> UpdateDOB();
				}
			}
			else{
				$sql = "SELECT a.PolicyNumber FROM t_gn_policyautogen a WHERE a.CustomerId='$CustomerId'";
				$qry = $this -> query($sql);
				$DATAS_ERROR_VALUE = array('result'=> 2, 'policy' => $qry-> result_get_value('PolicyNumber'));
			}
			echo json_encode($DATAS_ERROR_VALUE);	
		}
		
		
	/** @ get Product **/
	
		function getPlanByProduct()
		{

			$ProductId 	= IMPLODE("','",EXPLODE(",",$this -> escPost('productid')));
			$sql =" select a.ProductPlan, a.ProductPlanName from t_gn_productplan a 
					left join t_gn_product b on a.ProductId=b.ProductId
					where a.ProductId IN ('".$ProductId."') GROUP BY a.ProductPlan ";
			
					
			$qry = $this ->query($sql);
			
			echo " <select name=\"plan_plan\" id=\"plan_plan\" onchange=\"showFormBenefit(this);\">".
				 " <option value=\"\">-- Choose --</option>";
					
					foreach( $qry -> result_array() as $rows )
					{	
						echo "<option value=\"{$rows[0]}\">{$rows[1]}</option>";
					}
					
			echo "</select>";	
		}
		
	/** get Product by campaign**/
		private function getProductByCampaign($CampaignId)
		{
			$sql = " select a.ProductId from t_gn_campaignproduct a where a.CampaignId='$CampaignId'";
			$qry = $this -> query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['ProductId']] =  $rows['ProductId']; 
			}
			return $datas;
		}
		
	/* Max age ***/
	
		function getMinimalAge()
		{
			$ProductWithId = IMPLODE("','",$this -> getProductByCampaign($_REQUEST['CampaignId']));
			
			$sql = " select MIN(a.ProductPlanAgeStart) from t_gn_productplan a 
						where a.PremiumGroupId='".$this -> escPost('init')."'
						AND a.ProductId IN('$ProductWithId') ";
			$qry = $this -> query($sql);
			if( !$qry -> EOF() ){
				return $qry -> result_singgle_value();
			}
			else
				return null;
		} 	
		
	/* minimal age ***/
	
		function getMaximallAge()
		{
			$ProductWithId = IMPLODE("','",$this -> getProductByCampaign($_REQUEST['CampaignId']));
			$sql = " select MAX(a.ProductPlanAgeEnd) from t_gn_productplan a 
					 where a.PremiumGroupId='".$this -> escPost('init')."'
					 AND a.ProductId IN('$ProductWithId') ";
					 
			$qry = $this -> query($sql);
			if( !$qry -> EOF() ){
				return $qry -> result_singgle_value();
			}
			else
				return null;
			
		} 	
		
	/* get Diff Date **/
	
		function datediff($d1, $d2)
		{  
				$d1 = (is_string($d1) ? strtotime($d1) : $d1);  
				$d2 = (is_string($d2) ? strtotime($d2) : $d2);  

				//ndang, penyederhanaan
				$age = floor(abs($d2 - $d1) / (60*60*24*365.2421896));
				return $age;

				$diff_secs = abs($d1 - $d2);  
				$base_year = min(date("Y", $d1), date("Y", $d2));  
				$diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);  
			return array( "years" => date("Y", $diff) - $base_year,  
				"months_total" => (date("Y", $diff) - $base_year) * 12 + date("n", $diff) - 1,  
				"months" => date("n", $diff) - 1,  
				"days_total" => floor($diff_secs / (3600 * 24)),  
				"days" => date("j", $diff) - 1,  
				"hours_total" => floor($diff_secs / 3600),  
				"hours" => date("G", $diff),  
				"minutes_total" => floor($diff_secs / 60),  
				"minutes" => (int) date("i", $diff),  
				"seconds_total" => $diff_secs,  
				"seconds" => (int) date("s", $diff)  );  
		} 
	
	/* Hitung User DOB **/
	
		function HitungUserDob()
		{
			
			$result = array(
				'result' => 0, 
				'message' => '', 
				'umur_size' => 0, 
				'complete'=> 0 
			);
			
			$start_date = $this -> formatDateEng($this->escPost('user_dob'));
			$ClassDate  = $this ->  Date -> get_date_diff($start_date,date('Y-m-d'));
		
			$DecimalInt = number_format(($ClassDate -> months_total()/12),1,'.','.');
			if( $DecimalInt  < $this -> getMinimalAge() ){
				$result = array(
					"result"=> 0, "message" => "Age must be greater than {$this -> getMinimalAge()}", 
					"umur_size" => $DecimalInt ,"complete"=> 0
				);
			}
			else if( (INT)$DecimalInt  > $this -> getMaximallAge()  ){
				$result = array(
					"result"=> 0, "message" => "Age must be less than {$this -> getMaximallAge()}", 
					"umur_size" => $DecimalInt,"complete"=> 0
				);
			}	
			else{
				if( ceil($this -> getMinimalAge())==0) {
					$result = array(
							"result"=> 0, "message" => "Age xmust be greater than {$this -> getMinimalAge()}", 
							"umur_size" => $DecimalInt ,"complete"=> 0
					);
				}
				else{	
					$result = array(
							"result"=> 1, "message" => "OK", 
							"umur_size" => $DecimalInt,
							"complete"=> 0
					);
				}
			}	
			
			echo json_encode($result);
			
		}
		
		
	 /** function get Now() > 2 cannot create ***/
	 
		function getExpired5Mon()
		{
			$InitMonth = date('m');
			$Month = EXPLODE('/',$this -> escPost('cekcard') );
			if( count($Month)>=2 )
			{
				$MonthCheck = $Month[0];
				if( $MonthCheck!='')
				{
					if( $MonthCheck > 12 ) echo 0;
					else
					{
						$InitMonth2  = (($InitMonth)+2);
						if( $MonthCheck <= $InitMonth2 ){ echo 1; }
						else{ echo 1; }	
				    }	
				}
			}
		}
		
		function getExpired2Mon()
		{
			$Month = EXPLODE('/', $this -> escPost('cekcard') );
			$Ym = "20{$Month[1]}-{$Month[0]}-01";
			if( count($Month)>=2 )
			{
				$value_of_date = date('Y-m-d',strtotime("+1 months"));
				if( strtotime($Ym)> strtotime($value_of_date) ){ echo 1; }
				else{ echo 0; }
			}
			else{ echo 0; }
		}
		
	}

	$FormPolicy	= new FormPolicy();
	$FormPolicy -> index();
	