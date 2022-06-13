	<?php
	
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	
	/*
	 *	class untuk action product
	 *  author : omens
	 *  date : 2012-10-17
	*/
	
	
	
	class EditFormPolicy extends mysql{
		
		var $action;
		var $PolicyPrefix;
		var $MaxlengthPolicy;
		var $PolicyLastId;
		
		function __construct(){
			parent::__construct();
			
			$this -> action = $this->escPost('action');
			if($this ->havepost('campaignid')){
				$this -> getProductPrefix();
			}
		}
		
		function index(){
			if( $this->havepost('action')){
				switch($this->action){
					case 'hitung_dob_customer'		: $this	-> HitungUserDob(); 		break;
					case 'get_plan_customer'		: $this	-> getPlanByProduct(); 		break;
					case 'hitung_premi_customer' 	: $this	-> HitungPremiCustomer(); 	break;
					case 'get_prefix_cardnumber' 	: $this	-> validateCardNumber(); 	break;
					case 'update_policy'			: $this	-> SaveCreatePolicy(); 		break;
					case 'value_sec_num'			: $this	-> validCardNumber();  		break;
				}
			}
		}
		
		
	
	/** valid card Number **/
	
		function validateCardNumber($value=''){
			$sql= "select count(a.ValidCCPrefixId) from t_lk_validccprefix a
					where a.ValidCCPrefix REGEXP('".$value."')";
			//echo $sql;
			$res = $this ->valueSQL($sql);
			if( $res >0 ) return 1;
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
					$array = array('result' => $this -> validateCardNumber($card_number) );
				}
			}	
			
			echo json_encode($array);
			
		}
		// function validCardNumber(){
			// $sizeNumber = 0; $sizePosition = 10; $sizeLength =0; 
			
			// if( $this -> havepost('number') )
			// {	
				// $number_post = $_REQUEST['number'];
				
				// if( strlen($number_post) <=6 ){
					// echo $this -> validateCardNumber($number_post);
				
				// }	
				// else{
						
					// $number = $number_post;
					
					// for( $i=0; $i<strlen($number); $i++){
						// if( $i%2!=0) { $isNumber[] = 1; }
						// else{ $isNumber[] = 2; }
						// $arrNumber[] = substr($number,$i,1);
					// }
					
					// $v=0;
					// foreach( $arrNumber as $key=> $index){
							// $maxLength[$v] = ( ($arrNumber[$key]) * ($isNumber[$key]) ); 
							// if(strlen($maxLength[$v]) >1 ): $volume[$v] = ( substr($maxLength[$v],0,1)+substr($maxLength[$v],1,1)); endif;
							// if( strlen($maxLength[$v]) ==1): $volume[$v] = $maxLength[$v]; endif;
						// $v++;
					// }
					
					// $sizeNumber  = (is_array($volume)?(array_sum($volume)/($sizePosition)):0); 
					
					// if($sizeNumber >0 ): $sizeLength  = count(explode(".",$sizeNumber)); endif;
						// if( $sizeLength==1) : echo 1;
						// else : 
							// echo 0; 
						// endif;
				// }	
			// }	
		// }	
		
		private function getProductPrefix(){
			$sql =" select  a.PrefixChar, a.PrefixLength 
					from t_gn_productprefixnumber a 
					inner join t_gn_product b on a.ProductId=b.ProductId 
					left join t_gn_campaignproduct c on a.ProductId=c.ProductId
					where c.CampaignId=".$_REQUEST['campaignid']." and a.PrefixFlagStatus=1";
			
			$query  = $this -> execute($sql,__file__,__line__);
			$result = $this -> fetcharray($query);
			if( $result ) :
				$this -> MaxlengthPolicy = $result['PrefixLength'];
				$this -> PolicyPrefix 	 = $result['PrefixChar'];
			endif;
		}

	/** cek before inserting PRODUCT **/
	
		private function FProductPolicy(){
			$sql = " select count(a.PolicyAutoGenId) from t_gn_policyautogen a where a.productId=".$_REQUEST['plan_product_id']."";
			
			
			$ncust = $this -> valueSQL($sql);
				if( $ncust > 0 ) return true;
				else return false;
		}
		
	/** cek before inserting PRODUCT **/
		
		private function FCustomerPolicy(){
			$sql = " select count(a.PolicyAutoGenId) from t_gn_policyautogen a where a.CustomerId=".$_REQUEST['customerid']."";
			$ncust = $this -> valueSQL($sql);
				if( $ncust > 0 ) return true;
				else return false;
		}
		
		
	/** function cek insurance duplikasi group **/
	
		private function FInsuranceGroup($PremiumGroupId=''){
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
	
		function PolicyGenerate(){
		
			$V_UNIX_NUMBER ='';
			
			if( $this -> havepost('plan_product_id') && $this -> havepost('customerid') ):
			
				if( $this -> FProductPolicy() ):
					if( $this -> FCustomerPolicy() ):
						$V_SQL =" select a.PolicyNumber from t_gn_policyautogen a where a.CustomerId=".$_REQUEST['customerid']."";
						$V_UNIX_NUMBER = $this -> valueSQL($V_SQL);
					else:
						$V_SQL = " select (max(a.PolicyLastNumber)+1) from t_gn_policyautogen a
									where a.ProductId=".$_REQUEST['plan_product_id']."";
						
						
						$V_UNIX_RESULT = $this ->valueSQL($V_SQL);
						if( $V_UNIX_RESULT!=''): 
							$V_UNIX_NUMBER = $this -> generatePolicyNumber($V_UNIX_RESULT);
							if( $V_UNIX_NUMBER){
									$V_DATAS = array(
													'PolicyLastNumber' => $V_UNIX_RESULT, 
													'CustomerId'=> $_REQUEST['customerid'], 
													'ProductId'=> $_REQUEST['plan_product_id'], 
													'PolicyNumber'=> $V_UNIX_NUMBER 
												);
									//print_r($V_DATAS);
									$this -> set_mysql_insert("t_gn_policyautogen", $V_DATAS, $V_DATAS);
							}	
						endif;
					endif;	
					
				else :
					$V_SQL = " select max(a.PolicyLastNumber) from t_gn_policyautogen a
								where a.CustomerId=".$_REQUEST['customerid']."
							    and a.ProductId=".$_REQUEST['plan_product_id']."";
					
					$V_UNIX_RESULT = ($this ->valueSQL($V_SQL)+1);
					if($V_UNIX_RESULT!=''):
						 $V_UNIX_NUMBER = $this -> generatePolicyNumber($V_UNIX_RESULT);
							if($V_UNIX_NUMBER){
							
								$datas = array(
											'PolicyLastNumber' => $V_UNIX_RESULT, 
											'CustomerId'=> $_REQUEST['customerid'], 
											'ProductId'=> $_REQUEST['plan_product_id'], 
											'PolicyNumber'=> $V_UNIX_NUMBER 
										);
								//print_r($datas);	
								$this -> set_mysql_insert("t_gn_policyautogen", $datas,$datas);			
							}
					endif;	
				endif;
			endif;
			return 	$V_UNIX_NUMBER;
		}	
		
		private function getCutOffDate(){
			$cutOffDate = " select a.CutoffDate from t_lk_cutoffdate a where a.CutoffMonth=MONTH(NOW()) ";
			return $this->valueSQL($cutOffDate);
		}
		
/** efective date **/

		private function getEfectiveDate(){
			$src_off_date = date('Y-m-d');
			$sql = " SELECT CONCAT( F_getEfectiveDate('".$this -> getCutOffDate()."', '".$src_off_date."'),' ',time(now()) )";
			return $this ->valueSQL($sql);			
		}
	
  
	
 /** get last ID **/
 
		private function generatePolicyNumber($policyNumber=""){
			$policeLastId = $policyNumber;
			if( !empty( $policeLastId) ):
				$result = substr( $this -> PolicyPrefix,0,strlen($this -> PolicyPrefix) - (strlen($policeLastId)+1) );
				$result.= $policeLastId.'X'; 
					if( strlen($result) == $this -> MaxlengthPolicy):
						return $result;
					else:
						return false;
					endif;
			else:
				return true;
			endif;
		}
	
	
	/** extract age **/
	
	private function spliteAge($POST_AGE){
			$V_DATA = explode(" ",$POST_AGE);
			if( is_array($V_DATA)){ return trim($V_DATA[0]); }
			else return null;	
		}
	/** cek table **/
	
	private function getJumlahRows($group_premi){
		$sql = "select count(a.InsuredId) from t_gn_transaction_policy a
					 where a.CustomerId =".$_REQUEST['customerid']."
					 and a.PremiumGroupId =".$group_premi."";
			 
			$jumlah = $this ->valueSQL($sql);
			return $jumlah;
	}
	
/** @ hitung Premi **/
 	
		function HitungPremiCustomer(){
			$URL_STRING = explode("~",$_REQUEST['urlstring']);
			$JUM_PREMI  = 0;
			
			foreach($URL_STRING as $key => $_STRING)
			{
				
				$V_STRING = explode("|",$_STRING);
				$sql = "SELECT F_GetPremiumAge('".$this->escPost('planid')."','".$this->escPost('productid')."', 
										   '".$this->escPost('paymode')."','".trim($V_STRING[0])."', 
										   '".trim($V_STRING[1])."')";
										   
				//echo $sql;						   
											 
				$JUM_PREMI += $this ->valueSQL($sql);
			}
			
			if( $JUM_PREMI ) echo $JUM_PREMI;
			else echo 0; 
		}

		
/** get Productplan ID **/
	
	private function getProductPlanId($V_GROUP,$V_AGES){
	
			$sql = "select F_GetPlanId('".$this->escPost('plan_product_id')."','".$this->escPost('plan_plan')."',
										   '".$this->escPost('plan_paymode')."','".trim($V_GROUP)."', 
										   '".trim($V_AGES)."')";
										   
			return $this ->valueSQL($sql);			
		}
		

/* on update holder edit then update tgn_customer dob **/

	private function UpdateCustomerDOB($custid){
			if( !empty($custid)){
				$sql = "update t_gn_customer a set a.CustomerDOB = '".$this->formatDateEng($_REQUEST['frm_holder_dob'])."' 
							where a.CustomerId='".$_REQUEST['customerid']."' 
							and (a.CustomerDOB is null OR a.CustomerDOB <>'".$this->formatDateEng($_REQUEST['frm_holder_dob'])."')";
					
				$this ->execute($sql,__FILE__,__LINE__);			

			}
			
		}	
	
/** main insurance **/
	
	private function UpdatesMainInsurance(){
			$datasHolder['UpdatedById']	 = $this->getSession('UserId');
			$datasHolder['InsuredFirstName'] = $_REQUEST['frm_holder_firstname'];
			$datasHolder['InsuredLastName'] = $_REQUEST['frm_holder_lastname'];
			$datasHolder['InsuredDOB']	= $this ->formatDateEng($_REQUEST['frm_holder_dob']);
			$datasHolder['InsuredAge']	= $_REQUEST['holder_age'];
			$datasHolder['InsuredIdentificationNum'] = $_REQUEST['frm_holder_idno'];
			$datasHolder['InsuredUpdatedTs'] = date('Y-m-d H:i:s');			
			$where['PremiumGroupId'] = 2;
			$where['CustomerId'] = $_REQUEST['customerid'];
			print_r($datasHolder);
			return false;
			$V_RESULT = $this -> set_mysql_update("t_gn_insured",$datasHolder,$where);
			// $V_RESULT = true;
				
			 if( $V_RESULT ){
				$this -> UpdateCustomerDOB();
				return true;
			}
			else 
				return false;
			
	}
	
	
	
/** main insurance **/

	private function UpdatesSpouseInsurance($PolicyId){
			
			
			$datasHolder['PolicyId'] = $PolicyId;
			$datasHolder['SalutationId'] = $_REQUEST['cb_insurance_sp_salut'];
			$datasHolder['GenderId'] = $_REQUEST['txt_insurance_sp_gender'];
			$datasHolder['IdentificationTypeId']= $_REQUEST['cb_insurance_sp_idtype'];
			$datasHolder['PremiumGroupId'] = $_REQUEST['cb_insurance_sp_holdertype'];
			$datasHolder['RelationshipTypeId']	= $_REQUEST['cb_insurance_sp_relation'];
			$datasHolder['UpdatedById']	 = $this->getSession('UserId');
			$datasHolder['InsuredFirstName'] = $_REQUEST['txt_insurance_sp_firstname'];
			$datasHolder['InsuredLastName'] = $_REQUEST['txt_insurance_sp_lastname'];
			$datasHolder['InsuredDOB']	= $this ->formatDateEng($_REQUEST['txt_insurance_sp_dob']);
			$datasHolder['InsuredAge']	= $this -> spliteAge($_REQUEST['txt_insurance_sp_age']);
			$datasHolder['InsuredIdentificationNum'] = $_REQUEST['txt_insurance_sp_idno'];
			$datasHolder['InsuredUpdatedTs'] = date('Y-m-d H:i:s');
			$whereHolder['CustomerId'] = $_REQUEST['customerid'];
			
			$V_RESULT = $this -> set_mysql_update("t_gn_insured",$datasHolder,$whereHolder);
			// $V_RESULT = true;
				
			 if( $V_RESULT ) : return true;
			 else :
				return false;
			 endif;
	}

/** main insurance **/

	private function insertDependentInsurance($PolicyId,$datas=array() ){
			
			$datasDep['PolicyId'] 			 = $PolicyId;
			$datasDep['SalutationId'] 		 = $datas['cb_insurance_dp_salut'];
			$datasDep['GenderId'] 			 = $datas['cb_insurance_dp_gender'];
			$datasDep['PremiumGroupId'] 	 = $datas['cb_insurance_dp_holdertype'];
			$datasDep['RelationshipTypeId']	 = $datas['cb_insurance_dp_rel'];
			$datasDep['UpdatedById']	 	 = $this->getSession('UserId');
			$datasDep['InsuredFirstName'] 	 = $datas['txt_insurance_dp_firstname'];
			$datasDep['InsuredLastName'] 	 = $datas['txt_insurance_dp_lastname'];
			$datasDep['InsuredDOB']		     = $this ->formatDateEng($datas['txt_insurance_dp_dob']);
			$datasDep['InsuredAge']			 = $this -> spliteAge($datas['txt_insurance_dp_age']);
			$datasDep['InsuredUpdatedTs'] 	 = date('Y-m-d H:i:s');
			
			$whereDep['CustomerId'] 		 = $_REQUEST['customerid'];
			
			$V_RESULT = $this -> set_mysql_update("t_gn_insured",$datasDep,$whereDep);
			// $V_RESULT = true;
			
			 if( $V_RESULT ) : return true;
			 else :
				return false;
			 endif;
	}	
	
/** insert to policy **/
	
	private function UpdatePolicy($V_PLAN_ID,$V_UNIX_NUMBER){
			$sql = " select a.PolicyId from t_gn_policy a
						left join t_gn_insured b on a.PolicyId=b.PolicyId
								where b.CustomerId=".$_REQUEST['CustomerId']." ";
			return $this->valueSQL($sql);
		}
		
		
/** genarate prefix and card number **/
		
	private function concateCardNumber(){
		$CardNumber = $_REQUEST['payer_card_number'];
		return $CardNumber;
	}
		
	

/** insert payers **/
 
	private function UpdatesPayers(){
		$datas['SalutationId']			 = $this -> escPost('payer_salutation');
		$datas['GenderId']				 = $this -> escPost('payer_gender');
		$datas['PremiumGroupId']		 = $this -> escPost('payer_holder_idtype');
		$datas['PayerIdentificationNum'] = $this -> escPost('payer_idno');
		$datas['ProvinceId']			 = $this -> escPost('payer_province');
		$datas['CreditCardTypeId']		 = $this -> escPost('payer_card_type');
		$datas['UpdatedById']			 = $this -> getSession('UserId');
		
	if($_REQUEST['chekclist']==1):
		$datas['PayerFirstName']		 = $this -> escPost('frm_holder_firstname');
		$datas['PayerLastName']			 = $this -> escPost('frm_holder_lastname');
	else:
		$datas['PayerFirstName']		 = $this -> escPost('payer_first_name');
		$datas['PayerLastName']			 = $this -> escPost('payer_last_name');
	endif;
	
		$datas['PayerDOB']				 = $this -> formatDateEng($this -> escPost('payer_dob'));
		$datas['PayerAddressLine1']		 = $this -> escPost('payer_address');
		$datas['PayerAddressLine2']		 = $this -> escPost('payer_address2');
		$datas['PayerAddressLine3']		 = $this -> escPost('payer_address3');
		$datas['PayerAddressLine4']		 = $this -> escPost('payer_address4');
		$datas['PayerCity']				 = $this -> escPost('payer_city');
		$datas['PayerZipCode']			 = $this -> escPost('payer_zip_code');
		$datas['PayerHomePhoneNum']		 = $this -> escPost('payer_home_phone');
		$datas['PayerMobilePhoneNum']	 = $this -> escPost('payer_mobile_phone');
		$datas['PayerWorkPhoneNum']		 = $this -> escPost('payer_office_phone');
		$datas['PayerHomePhoneNum2']		 = $this -> escPost('payer_home_phone2');
		$datas['PayerMobilePhoneNum2']	 = $this -> escPost('payer_mobile_phone2');
		$datas['PayerWorkPhoneNum2']		 = $this -> escPost('payer_office_phone2');
		$datas['PayerFaxNum']			 = $this -> escPost('payer_fax_number');
		$datas['PayerEmail']			 = $this -> escPost('payer_email');
		$datas['PayerCreditCardNum']	 = $this -> concateCardNumber();
		$datas['PayerCreditCardExpDate'] = $this -> escPost('payer_expired_date');
		$datas['PayerUpdatedTs']		 = date('Y-m-d H:i:s');
		$where['CustomerId']			 = $this -> escPost('customerid');
		
			$V_RESULT = $this ->set_mysql_update('t_gn_payer',$datas,$where);
			
			if( $V_RESULT ) : return true;
			else :
				return false;
			endif;	
	}
	
	
	private function contextNull($datas){
		$clearNull = array(); 
		foreach( $datas as $key=>$value){
			if(trim($value)!=''):
				$clearNull[$key] = $value;	
			endif;
		}
		return $clearNull;
	}
	
	function getBenefId($customer){
		$BenId = array();
		
		$sql = "select a.BeneficiaryId from t_gn_beneficiary a where a.CustomerId = '".$customer."'";
		$query  = $this -> execute($sql,__file__,__line__);
		
		$i = 1;
		while($rows = $this->fetcharray($query)){
			$BenId[$i] = $rows['BeneficiaryId'];
			$i++;
		}
			
		return $BenId;
	}
	
	private function UpdatesBenfeciery(){
		
		if( $this ->havepost('benefBox')):
			if( $this ->escPost('benefBox')!=''){
				
				$benf = $this -> getBenefId($_REQUEST['customerid']);
				//$listBenef = explode(",",$this ->escPost('benefBox'));
				foreach($benf as $key=>$val)
				{
					
					$benef_datas['SalutationId']					= $_REQUEST['txt_benef'.$key.'_title'];
					$benef_datas['PremiumGroupId']					= $_REQUEST['txt_benef'.$key.'_holdertype'];
					$benef_datas['RelationshipTypeId']				= $_REQUEST['txt_benef'.$key.'_rel'];
					$benef_datas['UpdatedById']						= $this -> getSession('UserId');;
					$benef_datas['BeneficiaryFirstName']			= $_REQUEST['txt_benef'.$key.'_first'];
					$benef_datas['BeneficiaryLastName']				= $_REQUEST['txt_benef'.$key.'_lastname'];
					$benef_datas['BeneficiaryCreatedTs']			= date('Y-m-d H:i:s');
					$benef_datas['BeneficiaryUpdatedTs']			= date('Y-m-d H:i:s');
					$where['BeneficiaryId']							= $val;
					
					$V_RESULT = $this -> set_mysql_update('t_gn_beneficiary',$benef_datas,$where);
				}
			}
		endif;
	}
		
		
	function getFormatAges($data){
		$ages_value = explode(" ",$data);
		return $ages_value[0];
		
	}	
		
	/** save Policy create **/
	
		function SaveCreatePolicy(){
		
		   $V_UNIX_NUMBER = $this->escPost('main_cust_policy_number');
			if($V_UNIX_NUMBER!='')
			{	
				if( $this -> havepost('main_cust_policy_number') )
				{
					if( $this->havepost('frm_holder_firstname') ): 
						$this -> UpdatesMainInsurance(); 
						return false;
					endif;
					
					$follow_insured = explode(",",$_REQUEST['insuranceBox']);
					
					if( count($follow_insured)>0 )
					{
						foreach($follow_insured as $idx=>$value)
						{//txt_insurance_dp_dob_22
							$datas= array(
									'SalutationId'=>$_REQUEST['cb_insurance_dp_salut_'.$value],
									'GenderId'=>$_REQUEST['cb_insurance_dp_gender_'.$value],
									'RelationshipTypeId'=>$_REQUEST['cb_insurance_dp_rel_'.$value],
									'UpdatedById'=>$_SESSION['UserId'],
									'InsuredFirstName'=>$_REQUEST['txt_insurance_dp_firstname_'.$value],
									'InsuredLastName'=>$_REQUEST['txt_insurance_dp_lastname_'.$value],
									'InsuredDOB'=> $this->formatDateEng($_REQUEST['txt_insurance_dp_dob_'.$value]),
									'InsuredAge'=> $this->getFormatAges($_REQUEST['txt_insurance_dp_age_'.$value]),
									'InsuredUpdatedTs'=>date('Y-m-d H:i:s'),
									'IdentificationTypeId'=>$_REQUEST['cb_insurance_dp_idtype_'.$value],
									'InsuredIdentificationNum'=>$_REQUEST['txt_insurance_dp_idno_'.$value]
								);
							//	print_r($_REQUEST);
							$where = array('InsuredId'=>$value);
							$this -> set_mysql_update("t_gn_insured",$this->contextNull($datas),$where);
						}
					}
					
					$this -> UpdatesPayers();
				}
				
				
				/** insert Benfiecery **/
				
				$this -> UpdatesBenfeciery();
					
				echo $V_UNIX_NUMBER;	
			}
			else{
				echo 0;
			}
		}
		
		
  
	
		
	/** @ get Product **/
	
		function getPlanByProduct(){
		
			$sql =" select a.ProductPlan, a.ProductPlanName from t_gn_productplan a 
					left join t_gn_product b on a.ProductId=b.ProductId
						where a.ProductId=".$this->escPost('productid')."
					group by a.ProductPlan";
				
			$qry = $this ->execute($sql,__FILE__,__LINE__);
			echo " <select name=\"plan_plan\" id=\"plan_plan\" onchange=\"getPremiByPlan(this.value);\">".
				 " <option value=\"\">-- Choose --</option>";
				
				while( $row = $this ->fetcharray($qry)){
					echo "<option value=\"{$row[0]}\">{$row[1]}</option>";
				}
			echo "</select>";	
		}
		
		function getMinimalAge(){
			$sql = " select MIN(a.ProductPlanAgeStart) from t_gn_productplan a where a.PremiumGroupId='".$_REQUEST['init']."'";
			return $this -> valueSQL($sql);
			
		} 	
		
	/* get Diff Date **/
	
		function datediff($d1, $d2){  
				$d1 = (is_string($d1) ? strtotime($d1) : $d1);  
				$d2 = (is_string($d2) ? strtotime($d2) : $d2);  
				$diff_secs = abs($d1 - $d2);  
				$base_year = min(date("Y", $d1), date("Y", $d2));  
				$diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);  
			return array( "years" => date("Y", $diff) - $base_year,  "months_total" => (date("Y", $diff) - $base_year) * 12 + date("n", $diff) - 1,  "months" => date("n", $diff) - 1,  "days_total" => floor($diff_secs / (3600 * 24)),  "days" => date("j", $diff) - 1,  "hours_total" => floor($diff_secs / 3600),  "hours" => date("G", $diff),  "minutes_total" => floor($diff_secs / 60),  "minutes" => (int) date("i", $diff),  "seconds_total" => $diff_secs,  "seconds" => (int) date("s", $diff)  );  
		} 
	
	/* Hitung User DOB **/
	
		function HitungUserDob(){
			$accept_date = $this ->formatDateEng($this->escPost('user_dob'));
			
			$data_test = $this ->datediff($accept_date,date('Y-m-d'));
			$user_umur = $data_test['years']." Years , ".$data_test['months']." Month";
			
			
			if( $data_test['years'] < $this -> getMinimalAge() ){
				echo 0;
			}	
			else{
				echo $user_umur;
			}	
		}
	}

	$EditFormPolicy	= new EditFormPolicy();
	$EditFormPolicy -> index();
	