<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");

define('HOLDER',2); 
define('SPOUSE',3); 
define('DEPEND',1); 
define('UW',2); 
define('SURVEY',1); 
/*
 * @ def 		: class AXA_Edit
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
class AXA_Edit extends mysql
{

/*
 * @ def 		: AXA_Edit / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */

private static $InsuredId = null;

/*
 * @ def 		: AXA_Edit / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
private static $action = null;

/*
 * @ def 		: AXA_Edit / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */

private static $ProductId = null;

/*
 * @ def 		: AXA_Edit / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
function AXA_Edit()
{
	parent::__construct();
	
	if( $this -> havepost('action') )
	{
		self::$action = trim($this -> escPost('action'));
		self::$InsuredId = trim($this -> escPost('InsuredId'));
		self::$ProductId = trim($this -> escPost('ProductId'));
	}
	
	self::index();
}


/*
 * @ def 		: AXA_Edit / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 function _self_personal_premi( $ProductId=null, $_argument =null )
 {
	$_personal_premi = 0;
	if( !is_null($_argument) AND is_array($_argument) AND !is_null($ProductId) )
	{
		// $sql = " SELECT a.ProductPlanPremium, a.ProductPlanId FROM t_gn_productplan a  WHERE 1=1 ";
		if($_argument['GenderId'] == 1){
                    $sql = " SELECT (CASE a.`ProductPlanGender` WHEN 1 THEN a.ProductPlanPremiumMale ELSE a.ProductPlanPremium END) AS ProductPlanPremium, a.ProductPlanId FROM t_gn_productplan a  WHERE 1=1 ";
        }else{
                    $sql = " SELECT (CASE a.`ProductPlanGender` WHEN 1 THEN a.ProductPlanPremiumFemale ELSE a.ProductPlanPremium END) AS ProductPlanPremium, a.ProductPlanId FROM t_gn_productplan a  WHERE 1=1 ";
        }
		if( !is_null($ProductId) AND $ProductId!=FALSE ){
			$sql.= " AND a.ProductId= '". $ProductId ."'";
		}
				
		if( isset($_argument['PayModeId']) 
			AND !is_null($_argument['PayModeId']) 
			AND $_argument['PayModeId']!=FALSE )
		{
			$sql.= " AND a.PayModeId= '". $_argument['PayModeId'] ."'";	
		}
				
		if( isset($_argument['ProductPlan']) 
			AND !is_null($_argument['ProductPlan']) 
			AND $_argument['ProductPlan']!=FALSE )
		{
			$sql.= " AND a.ProductPlan= '". $_argument['ProductPlan'] ."'";	
		}
				
		if( isset($_argument['PremiumGroupId']) 
			AND !is_null($_argument['PremiumGroupId']) 
			AND $_argument['PremiumGroupId']!=FALSE )
		{
					$sql.= " AND a.PremiumGroupId= '". $_argument['PremiumGroupId'] ."'";	
		}
				
		if( isset($_argument['start_age']) 
			AND !is_null($_argument['start_age']) 
			AND $_argument['start_age']!=FALSE )
		{
			$sql.= " AND {$_argument['start_age']} BETWEEN ProductPlanAgeStart and ProductPlanAgeEnd ";
		}
		
		$qry = $this -> query($sql);
		if( $qry -> result_num_rows()==1 )
		{
			$_personal_premi = array
			(
				'ProductPlanPremium' => (INT)$qry -> result_get_value('ProductPlanPremium'),
				'ProductPlanId' => (INT)$qry -> result_get_value('ProductPlanId')
			);
		}
	}
	
	return $_personal_premi;		
}	

/*
 * @ def 		: AXA_Edit / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
private function PolicyId()
{
	$_conds = null;
	
	$sql = "select a.PolicyId from t_gn_insured a where a.InsuredId='". self::$InsuredId ."'" ;
	$qry = $this -> query($sql);
	if( !$qry -> EOF() )
	{
		$_conds = (INT)$qry -> result_singgle_value();
	}
	
	return $_conds;
}		

/*
 * @ def 		: AXA_Edit / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 		
function index()
{
	if(!is_null(self::$action) )
	{
		switch(self::$action) 
		{
			case '_get_age'	: self::_get_personal_age(); break;
			case '_get_premi' : self::_get_personal_premi(); break;
			case '_set_update_insured' : self::_set_update_insured(); break;
			case '_set_add_benefiecery' : self::_set_add_benefiecery(); break;
			case '_set_update_benefiecery' : self::_set_update_benefiecery(); break;
			case '_set_update_payers' : self::_set_update_payer(); break;	
			case '_set_update_survey' : self::_save_survey(); break;	
		}
	}	
}

private function _get_input_key($product=0)
{
	$data = array();
	$sql="	SELECT a.prod_survey_id, b.survey_quest_id,d.type_from_user
			FROM t_gn_questioner quest
			INNER JOIN t_gn_prod_survey a ON quest.questioner_id=a.questioner_id
			INNER JOIN t_lk_survey b ON a.survey_id=b.survey_id
			INNER JOIN t_lk_type_ans_survey c ON b.type_ans_id = c.type_ans_id
			INNER JOIN t_lk_type_survey d ON c.type_survey_id=d.type_survey_id
			WHERE quest.product_id = ".$product."
			AND quest.questioner_flag = 1 ";
	$qry  = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows ) 
	{
		
		$data[$rows['type_from_user']][$rows['survey_quest_id']][]= $rows['prod_survey_id'];
	}
	return $data;
}

/*private function _get_data_survey($product=0)
{
	$data=array();
	$sql = "SELECT a.prod_survey_id,c.survey_quest_id,c.survey_question, d.ans_label,e.type_survey
			FROM t_gn_questioner quest 
			INNER JOIN t_gn_prod_survey a ON quest.questioner_id = a.questioner_id
			INNER JOIN t_lk_survey b ON a.survey_id=b.survey_id
			INNER JOIN t_lk_question_survey c ON b.survey_quest_id=c.survey_quest_id
			INNER JOIN t_lk_type_ans_survey d ON b.type_ans_id = d.type_ans_id
			INNER JOIN t_lk_type_survey e ON d.type_survey_id=e.type_survey_id
			WHERE quest.product_id = ".$product."
			AND quest.questioner_flag = 1
			AND quest.questioner_type = 1
			ORDER BY c.survey_quest_order,d.ans_order ASC";
	$qry  = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows ) 
	{
		$data['question'][$rows['survey_quest_id']]= $rows['survey_question'];
		$data['answer_label'][$rows['survey_quest_id']][$rows['prod_survey_id']]= $rows['ans_label'];
		$data['setup_answer'][$rows['survey_quest_id']]= $rows['type_survey'];
	}
	return $data;
}*/
private function _get_data_survey($product=0,$questioner_type=0)
{
	$data=array();
	if($questioner_type!=0)
	{
		$sql = "SELECT a.prod_survey_id,c.survey_quest_id,c.survey_question, d.ans_label,e.type_survey,quest.questioner_type
			FROM t_gn_questioner quest 
			INNER JOIN t_gn_prod_survey a ON quest.questioner_id = a.questioner_id
			INNER JOIN t_lk_survey b ON a.survey_id=b.survey_id
			INNER JOIN t_lk_question_survey c ON b.survey_quest_id=c.survey_quest_id
			INNER JOIN t_lk_type_ans_survey d ON b.type_ans_id = d.type_ans_id
			INNER JOIN t_lk_type_survey e ON d.type_survey_id=e.type_survey_id
			WHERE quest.product_id = ".$product."
			AND quest.questioner_flag = 1
			AND quest.questioner_type = ".$questioner_type."
			ORDER BY c.survey_quest_order,d.ans_order ASC";
	}
	else
	{
		$sql = "SELECT a.prod_survey_id,c.survey_quest_id,c.survey_question, d.ans_label,e.type_survey,quest.questioner_type
			FROM t_gn_questioner quest 
			INNER JOIN t_gn_prod_survey a ON quest.questioner_id = a.questioner_id
			INNER JOIN t_lk_survey b ON a.survey_id=b.survey_id
			INNER JOIN t_lk_question_survey c ON b.survey_quest_id=c.survey_quest_id
			INNER JOIN t_lk_type_ans_survey d ON b.type_ans_id = d.type_ans_id
			INNER JOIN t_lk_type_survey e ON d.type_survey_id=e.type_survey_id
			WHERE quest.product_id = ".$product."
			AND quest.questioner_flag = 1
			ORDER BY c.survey_quest_order,d.ans_order ASC";
	}
	$qry  = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows ) 
	{
		
		$data['question'][$rows['survey_quest_id']]= $rows['survey_question'];
		$data['questioner_type'][$rows['survey_quest_id']]= $rows['questioner_type'];
		$data['setup_answer'][$rows['survey_quest_id']]= $rows['type_survey'];
		$data['answer_label'][$rows['survey_quest_id']][$rows['prod_survey_id']]= $rows['ans_label'];
		// $data['answer_tree'][$rows['survey_quest_id']][$rows['prod_survey_id']] = $rows['ans_perent_id'];
	}
	return $data;
}


/*
 * @ def 		: AXA_Edit / _save_survey 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
private function _save_survey()
{
	$respon = array('success'=> 0);
	$ins_count = 0;
	$up_count = 0;
	if($this->havepost('ProductId') && $this->havepost('questioner_type'))
	{
		$product = $this->escPost('ProductId');
		$customerid = $this -> escPost('CustomerId');
		$questioner_type = $this -> escPost('questioner_type');
		// $question = $this->_get_question_id($product);
		$survey_data = $this->_get_data_survey($product,$questioner_type);
		$question = $survey_data['question'];
		$answer_label = $survey_data['answer_label'];
		// $questioner_type = $survey_data['questioner_type'];
		
		/* if product have question of survey */
		if(count($question) >0)
		{
			/* get post from form survey */
			$post_survey = array();
			$input = $this->_get_input_key($product);
			$input_keyboard = $input[1];
			$input_choose = $input[0];
		
			foreach($question as $qst_id => $qst)
			{
				if( array_key_exists($qst_id,$input_keyboard) )
				{
					foreach($input_keyboard[$qst_id] as $idx => $prod_survey_id)
					{
						$param = "survey_".$qst_id."_".$prod_survey_id;
						if($this->havepost($param))
						{
							$post_survey[$qst_id][$prod_survey_id]['answer_value'] = strtoupper($this->escPost($param));
							$post_survey[$qst_id][$prod_survey_id]['quest_have_ans'] = "1";
						}
						else 
						{
							$post_survey[$qst_id][$prod_survey_id]['answer_value'] = "";
							$post_survey[$qst_id][$prod_survey_id]['quest_have_ans'] = "0";
						}
					}
				}
				else if ( array_key_exists($qst_id,$input_choose) )
				{
					$param = "survey_".$qst_id;
					if($this->havepost($param))
					{
						$post = $this->escPost($param);
						$answer = explode(",",$post);
					}
					
					foreach($input_choose[$qst_id] as $idx => $prod_survey_id)
					{
						if( in_array($prod_survey_id,$answer) )
						{
							$post_survey[$qst_id][$prod_survey_id]['answer_value'] = strtoupper($answer_label[$qst_id][$prod_survey_id]);
							$post_survey[$qst_id][$prod_survey_id]['quest_have_ans'] = "1";
						}
						else
						{
							$post_survey[$qst_id][$prod_survey_id]['answer_value'] = "";
							$post_survey[$qst_id][$prod_survey_id]['quest_have_ans'] = "0";
						}
					}
				}
			}
			// echo "<pre>";
			// print_r($post_survey);
			// echo "</pre>";
			/*** Ok we get the answer, Save now **/
			foreach($post_survey as $qst_id => $array_value)
			{
				// echo var_dump($questioner_type[$qst_id]==UW);
				if($questioner_type==UW)
				{
					$insured_id = $this->escPost('InsuredId');
				}
				else
				{
					$insured_id = "0";
				}
				$customer_survey=array(
					'customer_id'=> $customerid,
					'question_id'=> $qst_id,
					'product_id'=>$product,
					'insured_id'=>$insured_id
				);
				// print_r($customer_survey);
				$_conds = $this -> set_mysql_insert('t_gn_ans_survey', $customer_survey);
				if( $_conds )
				{
					$ans_id = $this -> get_insert_id();
					foreach($array_value as $prod_survey_id => $value)
					{
						$data = array (
							'ans_survey_id' => $ans_id,
							'customer_id' => $customerid,
							'insured_id' => $insured_id,
							'prod_survey_id' => $prod_survey_id,
							'quest_have_ans' => $value['quest_have_ans'],
							'answer_value' => $value['answer_value'],
							'questioner_type' => $questioner_type,
							'ins_datets' => date('Y-m-d H:i:s')
						);
						
						if( $this -> set_mysql_insert('t_gn_multians_survey', $data) )
						{
							$ins_count++;
						}
					}
				}
				else
				{
					foreach($array_value as $prod_survey_id => $value)
					{
						// echo $customerid."<br />";
						// echo $insured_id."<br />";
						// echo $prod_survey_id."<br />";
						if ( $this -> set_mysql_update('t_gn_multians_survey', 
								array(
									'quest_have_ans' => $value['quest_have_ans'],
									'answer_value' => $value['answer_value'],
									'update_datets' => date('Y-m-d H:i:s')
								),
								array(
									'customer_id'=>$customerid,
									'insured_id'=>$insured_id,
									'questioner_type' => $questioner_type,
									'prod_survey_id'=>$prod_survey_id)) )
						{
							$up_count++;
						}
					}
				}
			}
		}
	}
	// echo $ins_count;
	// echo $up_count;
	if ( $ins_count > 0 || $up_count > 0 )
	{
		$respon = array('success'=> 1);
	}
	// echo "<pre>";
	// print_r($post_survey);
	// echo "</pre>";
	echo json_encode($respon);
}

/*
 * @ def 		: AXA_Edit / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
function _set_update_insured()
{
  $_conds = array('success'=> 0);
 
  if(!is_null(self::$InsuredId))
  {
	$_exist_group_premi = $this -> Customer -> _getExistGroupPremi(self::$ProductId);
	if( $_exist_group_premi ) 
	{
		$_argument['PremiumGroupId'] = (INT)$this -> escPost('InsuredGroupPremi'); 
	}
						
	$_argument['PayModeId']	  = ( $this -> havepost('InsuredPayMode')?$this -> escPost('InsuredPayMode'):false ); 
	$_argument['ProductPlan'] = ( $this -> havepost('InsuredPlanType')?$this -> escPost('InsuredPlanType'):false ); 
	$_argument['start_age']	  = ( $this -> havepost('InsuredAge')?$this -> escPost('InsuredAge'):false );
	$_argument['GenderId']	  = ( $this -> havepost('InsuredGenderId')?$this -> escPost('InsuredGenderId'):false );
		
	//return by json data 
	
		$_totals = self::_self_personal_premi( self::$ProductId, $_argument );
		if( ($_totals!=FALSE) 
			AND (is_array($_totals)) )
		{
			if( $this -> set_mysql_update('t_gn_policy',
				array
				(
					'ProductPlanId'=> $_totals['ProductPlanId'],
					'Premi' => (INT) $_totals['ProductPlanPremium']
				),
				array('PolicyId' => self::PolicyId())
			))
			{
				
				if( $this -> set_mysql_update("t_gn_insured", 
						array
						(
							'PremiumGroupId' => $this -> escPost('InsuredGroupPremi'),
							'CustomerId' => $this -> escPost('CustomerId'),
							'SalutationId' => $this -> escPost('InsuredSalutationId'),
							'GenderId' => $this -> escPost('InsuredGenderId'),
							'IdentificationTypeId' => $this -> escPost('InsuredIdentificationTypeId'),
							'InsuredIdentificationNum' => $this -> escPost('InsuredIdentificationNum'),
							'RelationshipTypeId' => $this -> escPost('InsuredRelationshipTypeId'),
							'UpdatedById' => $this -> getSession('UserId'),
							'InsuredFirstName' => $this -> escPost('InsuredFirstName'),
							'InsuredLastName' => $this -> escPost('InsuredLastName'),
							'InsuredDOB' => $this -> formatDateEng($this -> escPost('InsuredDOB')),
							'InsuredAge' => $this -> escPost('InsuredAge'),
							'InsuredUpdatedTs' => date('Y-m-d H:i:s')
						), 
				array('InsuredId' => self::$InsuredId )  
				))
				{
					  $_conds = array('success'=>1);
				}
			}	
		}
  }
  echo json_encode($_conds);		
  
}


/*
 * @ def 		: AXA_Edit / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */		

function _set_add_benefiecery()
{
	$_conds = array('success'=> 0);
	
	$Add  = ( $this -> havepost('Add') ? explode(',', $this -> escPost('Add')) : null); // 1,2,3
	if( !is_null($Add) )
	{
		$tot_add = 0;
		foreach( $Add as $k => $post )
		{
			$SQL_insert['RelationshipTypeId'] = $this -> escPost("AddBenefRelationshipTypeId_$post"); 
			$SQL_insert['SalutationId'] = $this -> escPost("AddBenefSalutationId_$post"); 
			$SQL_insert['BeneficiaryFirstName'] = $this -> escPost("AddBenefFirstName_$post"); 
			$SQL_insert['BeneficiaryLastName'] = $this -> escPost("AddBenefLastName_$post"); 
			$SQL_insert['BeneficieryPercentage'] = $this -> escPost("AddBenefPercentage_$post"); 
			$SQL_insert['GenderId'] = $this -> escPost("AddBenefGenderId_$post"); 
			$SQL_insert['BeneficiaryDOB'] = $this -> formatDateEng($this -> escPost("AddBenefDOB_$post")); 
			$SQL_insert['BeneficiaryCreatedTs'] = date('Y-m-d H:i:s');
			$SQL_insert['CreatedById'] = $this -> getSession('UserId');
			$SQL_insert['CustomerId'] =  $this -> escPost('CustomerId');
			$SQL_insert['InsuredId'] = self::$InsuredId;
			
			if( $this -> set_mysql_insert("t_gn_beneficiary",$SQL_insert) ){
				$tot_add++; 	
			}
		}
		
		if( $tot_add > 0 ){
			$_conds = array('success'=> 1);
		}
	}
	
	echo json_encode($_conds);
}	

/*
 * @ def 		: AXA_Edit / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */		
 
function _set_update_benefiecery() 
{
	$_conds = array('success'=> 0);
	$Edit = ( $this -> havepost('Edit') ? explode(',', $this -> escPost('Edit')) : null); // 1,2,3
	if( !is_null($Edit) )
	{
		$tot_edit = 0;
		foreach( $Edit as $k => $post )
		{
			$SQL_update['RelationshipTypeId'] = $this -> escPost("EditBenefRelationshipTypeId_$post"); 
			$SQL_update['SalutationId'] = $this -> escPost("EditBenefSalutationId_$post"); 
			$SQL_update['BeneficiaryFirstName'] = $this -> escPost("EditBenefFirstName_$post"); 
			$SQL_update['CustomerId'] =  $this -> escPost('CustomerId');
			$SQL_update['BeneficiaryLastName'] = $this -> escPost("EditBenefLastName_$post"); 
			$SQL_update['GenderId'] = $this -> escPost("EditBenefGenderId_$post"); 
			$SQL_update['BeneficiaryDOB'] = $this -> formatDateEng($this -> escPost("EditBenefDOB_$post")); 
			$SQL_update['BeneficieryPercentage'] = $this -> escPost("EditBenefPercentage_$post"); 
			$SQL_update['BeneficiaryUpdatedTs'] = date('Y-m-d H:i:s');
			$SQL_update['UpdatedById'] = $this -> getSession('UserId');
			$SQL_update['InsuredId'] = self::$InsuredId;
			
			if( $this -> set_mysql_update("t_gn_beneficiary",$SQL_update, array("BeneficiaryId" => $post )) )
			{
				$tot_edit++; 	
			}
		}
		
		if( $tot_edit > 0 ){
			$_conds = array('success'=> 1);
		}
	}
	
	echo json_encode($_conds);
}

/*
 * @ def 		: AXA_Edit / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */		
 
function _set_update_payer()
{
	// print_r();
	
	$_conds = array('success'=> 0);
	if($this -> havepost('PayerId') )
	{
		$ProductCategory = $this -> Customer ->getProductCategory();
		if( $this -> havepost('ProductId') )
		{
			if($ProductCategory =="FPA")
			{
				$payment_cc = "1";
				$payment_saving = "2";
				$paytypeid = NULL;
				$cc_saving_num = NULL;
				$expire_cc_saving = "/";
				if($this -> havepost('PayerPaymentType'))
				{
					$paytypeid = $this -> escPost('PayerPaymentType');
					if($paytypeid==$payment_cc)
					{
						$cc_saving_num = $this -> escPost('PayerCreditCardNum');
						$expire_cc_saving = $this -> escPost('PayerCreditCardExpDate');
					}
					elseif($paytypeid==$payment_saving)
					{
						$cc_saving_num = $this -> escPost('SavingAccount');
						$expire_cc_saving = "/";
					}
					$SQL_update	['PaymentTypeId']	=	 $paytypeid;
					$SQL_update	['PayerCreditCardNum']	=	 $cc_saving_num;
					$SQL_update	['PayerCreditCardExpDate']	=	 $expire_cc_saving;
					$SQL_update	['PayersBankId']	=	 $this -> escPost('PayersBankId');
					$SQL_update	['CreditCardTypeId']	=	 $this -> escPost('CreditCardTypeId');
				}
			}
			// elseif($ProductCategory =="APE")
			// {
				// $SQL_update	['PayersBankId']	=	 $this -> escPost('PayersBankId');
			// }
			$SQL_update	['CustomerId']	=	 $this -> escPost('CustomerId');
			$SQL_update	['PayerFirstName']	=	 $this -> escPost('PayerFirstName');
			$SQL_update	['PayerLastName']	=	 $this -> escPost('PayerLastName');
			$SQL_update	['PayerDOB']	=	 formatDateEng($this -> escPost('PayerDOB'));
			$SQL_update	['PayerAddressLine1']	=	 $this -> escPost('PayerAddressLine1');
			$SQL_update	['PayerMobilePhoneNum']	=	 $this -> escPost('PayerMobilePhoneNum');
			$SQL_update	['PayerCity']	=	 $this -> escPost('PayerCity');
			$SQL_update	['PayerAddressLine2']	=	 $this -> escPost('PayerAddressLine2');
			$SQL_update	['PayerHomePhoneNum']	=	 $this -> escPost('PayerHomePhoneNum');
			$SQL_update	['PayerZipCode']	=	 $this -> escPost('PayerZipCode');
			$SQL_update	['PayerAddressLine3']	=	 $this -> escPost('PayerAddressLine3');
			$SQL_update	['PayerOfficePhoneNum']	=	 $this -> escPost('PayerOfficePhoneNum');
			$SQL_update	['PayerAddressLine4']	=	 $this -> escPost('PayerAddressLine4');
			$SQL_update	['PayerFaxNum']	=	 $this -> escPost('PayerFaxNum');
			$SQL_update	['PayerEmail']	=	 $this -> escPost('PayerEmail');
			$SQL_update	['SalutationId']	=	 $this -> escPost('PayerSalutationId');
			$SQL_update	['GenderId']	=	 $this -> escPost('PayerGenderId');
			$SQL_update	['IdentificationTypeId']	=	 $this -> escPost('PayerIdentificationTypeId');
			$SQL_update	['PayerIdentificationNum']	=	 $this -> escPost('PayerIdentificationNum');
			$SQL_update	['ProvinceId']	=	 $this -> escPost('PayerProvinceId');
			$SQL_update	['UpdatedById']	=	 $this -> getSession('UserId');
			$SQL_update	['PayerUpdatedTs']	=	 date('Y-m-d H:i:s');
			if( $this -> set_mysql_update('t_gn_payer',$SQL_update, 
				array(	
					'PayerId' => $this -> escPost('PayerId') 
			))) {
				$_conds = array('success'=> 1);
			} 

		}
	}
	echo json_encode($_conds);
  // $_conds = array('success'=> 0);
  // if($this -> havepost('PayerId') )
  // {
	// $payment_cc = "1";
	// $payment_saving = "2";
	// $paytypeid = NULL;
	// $cc_saving_num = NULL;
	// $expire_cc_saving = "/";
	// if($this -> havepost('PayerPaymentType'))
	// {
		// $paytypeid = $this -> escPost('PayerPaymentType');
		// if($paytypeid==$payment_cc)
		// {
			// $cc_saving_num = $this -> escPost('PayerCreditCardNum');
			// $expire_cc_saving = $this -> escPost('PayerCreditCardExpDate');
		// }
		// elseif($paytypeid==$payment_saving)
		// {
			// $cc_saving_num = $this -> escPost('SavingAccount');
			// $expire_cc_saving = "/";
		// }
	// }
	
	// $SQL_update = array
		// (
			// 'CustomerId' 				=> $this -> escPost('CustomerId'),
			// 'PayerFirstName'	 		=> $this -> escPost('PayerFirstName'),
			// 'PayerLastName' 			=> $this -> escPost('PayerLastName'),
			// 'PayerDOB' 					=> formatDateEng($this -> escPost('PayerDOB')),
			// 'PayerAddressLine1' 		=> $this -> escPost('PayerAddressLine1'),
			// 'PayerMobilePhoneNum' 		=> $this -> escPost('PayerMobilePhoneNum'),
			// 'PayerCity' 				=> $this -> escPost('PayerCity'),
			// 'PayerAddressLine2' 		=> $this -> escPost('PayerAddressLine2'),
			// 'PayerHomePhoneNum' 		=> $this -> escPost('PayerHomePhoneNum'),
			// 'PayerZipCode' 				=> $this -> escPost('PayerZipCode'),
			// 'PayerAddressLine3' 		=> $this -> escPost('PayerAddressLine3'),
			// 'PayerOfficePhoneNum' 		=> $this -> escPost('PayerOfficePhoneNum'),
			// 'PayerAddressLine4' 		=> $this -> escPost('PayerAddressLine4'),
			// 'PayerCreditCardNum' 		=> $cc_saving_num,
			// 'PayerFaxNum' 				=> $this -> escPost('PayerFaxNum'),
			// 'PayerCreditCardExpDate' 	=> $expire_cc_saving,
			// 'PaymentTypeId' 			=> $paytypeid,
			// 'PayerEmail' 				=> $this -> escPost('PayerEmail'),
			// 'SalutationId' 				=> $this -> escPost('PayerSalutationId'),
			// 'GenderId' 					=> $this -> escPost('PayerGenderId'),
			// 'IdentificationTypeId' 		=> $this -> escPost('PayerIdentificationTypeId'),
			// 'PayerIdentificationNum' 	=> $this -> escPost('PayerIdentificationNum'),
			// 'ProvinceId' 				=> $this -> escPost('PayerProvinceId'),
			// 'PayersBankId' 				=> $this -> escPost('PayersBankId'),
			// 'CreditCardTypeId' 			=> $this -> escPost('CreditCardTypeId'),
			// 'UpdatedById'				=> $this -> getSession('UserId'),
			// 'PayerUpdatedTs'			=> date('Y-m-d H:i:s')
		// );
	
	// if( $this -> set_mysql_update('t_gn_payer',$SQL_update, 
		// array(	
			// 'PayerId' => $this -> escPost('PayerId') 
	// ))) {
		// $_conds = array('success'=> 1);
	// } 
  // }
	
	// echo json_encode($_conds);
}

	
}
new AXA_Edit();
?>