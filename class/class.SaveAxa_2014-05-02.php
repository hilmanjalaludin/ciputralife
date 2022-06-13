<?php

require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");

/*
 * @ package 	: Save Policy For AXA 
 * 
 * @ param	 	: Over all request paramter
 * @ method		: Class aksess
 * @ return 	: JSON parameter 
 * @ revision	: 2
 */
 
define('HOLDER',2); 
define('SPOUSE',3); 
define('DEPEND',1); 
 
 
class AXA_Save extends mysql
{

	private $PolicyNo;
	private $PolicyLast;
/*
 * @ def 		: AXA_Save / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
 public function AXA_Save()
 {
	parent::__construct();
	if( $this -> havepost('action') ){
		self::index();
	}
	
 }
 
/* @ def 	: getPolicyId()
 *
 * @ param  : generator policy parameter
 * @ author : razaki team 
 */
 
private function _set_save_insured( $Insured = null )
{
	if( !is_null( $Insured ) AND is_array($Insured) )
	{
		$this -> set_mysql_insert('t_gn_insured', $Insured);
	}
}
/*
 * @ def 		: AXA_Save / constructor 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */

function _get_class_policy()
{
	$_CLASS_APPLICATION = dirname(__FILE__) ."/class.generator.policy.php";
	if( file_exists( $_CLASS_APPLICATION ) )
	{
		require_once( $_CLASS_APPLICATION );	
	}	
	
	$_instance =& Polis::get_instance();
	return $_instance;
}  
 
/*
 * @ def 		: AXA_Save / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
public function index()
{
	switch( $this -> escPost('action')) 
	{
		case '_get_age'			: self::_get_personal_age(); 	break;
		case '_get_age_payer'	: self::_get_payer_age(); 		break;
		case '_get_premi' 		: self::_get_personal_premi();  break;
		case '_get_payer_data'  : self::_get_payer_data(); 		break;
		case '_get_split'		: self::_get_splite_data(); 	break;
		case '_savePolis'		: self::_set_save_data(); 		break;
		case '_get_policy'		: self::_get_policy(); 			break;
		case '_get_detail'		: self::_get_Detail(); 			break;
		case '_get_transaction' : self::_get_transaction();     break;
		case '_get_pay_mode' 	: self::_get_pay_mode();     	break;
		case '_get_group_premi' : self::_get_group_premi();    	break;
		case '_get_plan_type' 	: self::_get_plan_type();     	break;
		case '_get_same_plan' 	: self::getExistingPlan();     	break;
		case '_get_valid_ins' 	: self::validInsured();     	break;
		case '_get_benefit' 	: self::_get_benefit();     	break;
		case '_check_benef' 	: self::checkBenef();     		break;
		case '_get_product_benef': self::_get_product_benef();  break;
		case '_update_qc'		 : self::_update_qc();  		break;
		case '_get_benefInsured' : self::_get_benefInsured();	break;
		case '_get_valid_prefix' : self::_get_valid_prefix();	break;
	}
}

function _get_valid_prefix()
{
	$_conds = array('result'=>0, 'img'=>'<img src="../gambar/icon/delete.png">');
	
	if(strlen($this->escPost('card_num')) >= 6)
	{
		$card_num = substr($this->escPost('card_num'),0,6); //$this->escPost('card_num')
		
		$sql = "select * from t_lk_validccprefix a where a.ValidCCPrefix = '".$card_num."'";
		$qry = $this->query($sql);
		
		if($qry->result_num_rows() > 0)
		{
			$_conds = array('result'=>1, 'img'=>'<img src="../gambar/icon/accept.png">');
		}
	}

	echo json_encode($_conds);
}

function _get_benefInsured(){
	if($this->havepost('ProductId') and $this->havepost('plan'))
	{
		$sql = "select a.ProductPlanBenefit, a.ProductPlanBenefitDesc from t_gn_productplanbenefit a
				where 1=1
				AND a.ProductId = '".$this->escPost('ProductId')."'
				AND a.ProductPlan = '".$this->escPost('plan')."'
				AND a.ProductPlanBenefitStatusFlag = 1";
		$qry = $this->query($sql);

		echo "<table width=\"100%\" border=\"1px\" cellspacing=\"1px\" cellpadding=\"6px\">
			 <tr>
					<td align=\"center\" width=\"15%\">Plan Benefit</td>
					<td align=\"center\" width=\"95%\">Plan Description</td>
			 </tr>";
		foreach($qry->result_rows() as $Qry2){
			echo "<tr>
					<td>".($Qry2[0])."</td>
					<td>".($Qry2[1])."</td>
				  </tr>
			";
		}
		echo "</table>";
	}
}

function _get_payer_age()
{
	$_conds = array('success' => 0, 'personal_age'=> 0 );
	$_DOB  = $this -> escPost('DOB');
	
	if( !is_null($_DOB) )
	{
		if( class_exists('DateFactory'))
		{
			$_date = $this -> Date -> set_date_diff( $_DOB, date('Y-m-d') );
			$_total_years = round(($_date['months_total']/12 ),1);
			if( $_total_years!='' AND $_total_years > 0 )
			{
				$Range =  self::_get_range_date( $this ->escPost('ProductId'),$this -> escPost('GroupPremi') );
				if( $_total_years >= $Range['MINIMUM'] AND  $_total_years <= $Range['MAXIMUM'] ){
					$_conds = array('success' =>1, 'personal_age'=> $_total_years );
				}
				else{
					$_conds = array('success' =>0, 'personal_age'=> 0,'Error' => 'Range Must Be  ' . $Range['MINIMUM'] .' && ' . $Range['MAXIMUM'] );
				}
			}
			else{
				$_conds = array('success' =>1, 'personal_age'=> 0,'Error' => 'Age not in range ');
			}
		}
	}
	
	echo json_encode($_conds);
}

function _update_qc()
{
	$_conds = array('result' => 0);
	
	$sql_upd['QCStatus'] = $this->escPost('StatusId');
	$sql_whe['InsuredId'] = $this->escPost('InsuredId');
	
	if( $this->set_mysql_update('t_gn_insured',$sql_upd,$sql_whe) )
	{
		$_conds = array('result' => 1);
	}
	
	echo json_encode($_conds);
}

function _get_product_benef()
{
	$_conds = array('result' => 0);
	
	$sql = "select a.ProductBenefFlag from t_gn_product a where a.ProductId = '".$this->escPost('ProductId')."'";
	$qry = $this->query($sql);
	
	if($qry->result_num_rows() > 0)
	{
		$_conds = array('result' => 1, 'value' => (INT)$qry->result_get_value('ProductBenefFlag'));
	}
	
	echo json_encode($_conds);
}

/* @ def 	: _get_member_polis()
 *
 * @ param  : jika dependent tidak berdiri sendiri && dan terikat oleh holder / spouse 
 * @ author : razaki team 
 */ 

 private function _single_policy()
 {
	$_conds = false;
	$polis = self::_get_singgle_polis();
	$data = array_values($polis);
	if( is_array($data) ) 
	{
		$_conds = $data[0];
	}
	
	return $_conds;
 }
 
/* @ def 	: _get_member_polis()
 *
 * @ param  : jika dependent tidak berdiri sendiri && dan terikat oleh holder / spouse 
 * @ author : razaki team 
 */ 
 
function _get_range_date($ProductId=0, $GroupPremi=0)
{
	$_conds = array('MINIMUM' =>0, 'MAXIMUM' => 0 );
	
	$sql = " SELECT MIN(a.ProductPlanAgeStart) as min_age, MAX(a.ProductPlanAgeEnd) as max_age  
			 FROM t_gn_productplan a 
			 WHERE a.ProductId='$ProductId'
			 AND a.PremiumGroupId='$GroupPremi'";
	$qry = $this -> query($sql);
	if( !$qry -> EOF() )
	{
		$_conds = array
			(
				'MINIMUM' => ( $qry -> result_get_value('min_age') ? $qry -> result_get_value('min_age') : 0 ), 
				'MAXIMUM' => ( $qry -> result_get_value('max_age') ? $qry -> result_get_value('max_age') : 0 ) 
			);
	}	
	
	return $_conds;
}

/*
 * @ def 		: _get_singgle_polis 
 *
 * @ params 	: if true Policy exist else no exist then you can create polis 
 * @ return 	: boolean
 */
 
function _get_policy()
{
	$_conds = array();
	$_polis = self::_get_singgle_polis();
	if( !is_null($_polis) AND is_array($_polis ) )
	{
		$_conds = $_polis;
	}	
	
	$this -> DBForm -> jpCombo('InsuredPolicyNumber','select long',$_conds,($this->escPost('SplitPolis')?'new':''),"onchange=Ext.DOM.LoadSamePlan(this)",($this->escPost('SplitPolis')?'0':'1'));
}

function _get_group_premi()
{
	$sql = "select distinct(a.PremiumGroupId) as PremiumGroupId, b.PremiumGroupCode, b.PremiumGroupDesc from t_gn_productplan a
			left join t_lk_premiumgroup b on a.PremiumGroupId = b.PremiumGroupId
			where a.ProductId = '".$this->escPost('ProductId')."'";
			
	$qry  = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows ) 
	{
		$datas[$rows['PremiumGroupId']] = $rows['PremiumGroupDesc'];
	}
	
	$this -> DBForm -> jpCombo('InsuredGroupPremi','select long', $datas,null, "onchange=Ext.DOM.ClearInsured();" );
}

function _get_plan_type()
{
	$sql = "select distinct(a.ProductPlan) as ProductPlan, a.ProductPlanName, if(b.PlanNameAlias<>'',b.PlanNameAlias,a.ProductPlanName) as PlanNameAlias from t_gn_productplan a
			left join t_lk_plan_name b on a.ProductPlan = b.PlanSection
			where a.ProductId = '".$this->escPost('ProductId')."'";
			
	$qry  = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows ) 
	{
		$datas[$rows['ProductPlan']] = $rows['PlanNameAlias'];
	}
	
	$this -> DBForm -> jpCombo('InsuredPlanType','select long', $datas, null,"OnChange=getPremi(this);");
}

function _get_pay_mode()
{
	$sql  = "select distinct(a.PayModeId) as PayModeId, b.PayMode, b.PayModeCode from t_gn_productplan a
			left join t_lk_paymode b on a.PayModeId = b.PayModeId
			where a.ProductId = '".$this->escPost('ProductId')."'";
	
	$qry  = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows ) 
	{
		$datas[$rows['PayModeId']] = $rows['PayMode'];
	}
	
	$this -> DBForm -> jpCombo('InsuredPayMode','select long', $datas, null,"OnChange=getPremi(this);");
}

function getExistingPlan()
{
	$_conds = array('result'=>0);
		
	$sql = "select c.PayModeId, c.ProductPlan from t_gn_policyautogen a
			left join t_gn_policy b on a.PolicyNumber = b.PolicyNumber
			left join t_gn_productplan c on b.ProductPlanId = c.ProductPlanId
			where a.PolicyNumber = '".$this->escPost('PolicyNum')."'";
			
	$qry = $this->query($sql);
	
	if($qry->result_num_rows() > 0)
	{
		$_conds = array('result'=>1,'paymode'=>$qry->result_get_value('PayModeId'),'plan'=>$qry->result_get_value('ProductPlan'));
	}
	
	echo json_encode($_conds);
}

/*
 * @ def 		: _get_singgle_polis 
 *
 * @ params 	: if true Policy exist else no exist then you can create polis 
 * @ return 	: boolean
 */
 
private function _get_singgle_polis()
{
	$_conds = false;
	
	$ProductId  = ( $this -> havepost('ProductId') ? $this -> escPost('ProductId') : null );
	$CustomerId = ( $this -> havepost('CustomerId') ? $this -> escPost('CustomerId') : null );
	
	if( !is_null($ProductId) AND !is_null($CustomerId) )
	{
		$sql = " SELECT a.PolicyNumber FROM t_gn_policyautogen a WHERE a.ProductId='$ProductId' AND a.CustomerId = '$CustomerId'";
		$qry = $this -> query($sql);
		
		$_conds['new'] = 'New Policy';
		if( !$qry -> EOF() )
		{	
			foreach( $qry -> result_assoc() as $rows )
			{
				$_conds[$rows['PolicyNumber']] = $rows['PolicyNumber'];
			}
		}
	}
	
	return $_conds;
}
 
/*
 * @ def 		: AXA_Save / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
private function _get_personal_age()
{
	$_conds = array('success' => 0, 'personal_age'=> 0 );
	$_DOB  = $this -> escPost('DOB');
	
	if( !is_null($_DOB) )
	{
		if( class_exists('DateFactory'))
		{
			$_date = $this -> Date -> set_date_diff( $_DOB, date('Y-m-d') );
			
			$_total_years = round(($_date['months_total']/12 ),1);
			if( $_total_years!='' AND $_total_years > 0 )
			{
				$Range =  self::_get_range_date( $this ->escPost('ProductId'),$this -> escPost('GroupPremi') );
				if( $_total_years >= $Range['MINIMUM'] AND  $_total_years <= $Range['MAXIMUM'] ){
					$_conds = array('success' =>1, 'personal_age'=> $_total_years );
				}
				else{
					$_conds = array('success' =>0, 'personal_age'=> 0,'Error' => 'Range Must Be  ' . $Range['MINIMUM'] .' && ' . $Range['MAXIMUM'] );
				}
			}
			else{
				$_conds = array('success' =>1, 'personal_age'=> 0,'Error' => 'Age not in range ');
			}
		}	
	}
	echo json_encode($_conds);
}

/*
 * @ def 		: AXA_Save / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
private function _get_personal_premi()
{
 
 $totals_premi_rupiah = 0;
 $_argument = array();
 $_conds  = array('success'=> 0, 'personal_premi'=> 0 );
 
 if( class_exists('Customer'))
 {
		$_exist_group_premi = $this -> Customer -> _getExistGroupPremi( $this -> escPost('ProductId'));
		if( $_exist_group_premi ) {
			$_argument['PremiumGroupId'] = (INT)$this -> escPost('GroupPremi'); 
		}
		
		$_argument['PayModeId']	  = ( $this -> havepost('PayModeId')?$this -> escPost('PayModeId'):false ); 
		$_argument['ProductPlan'] = ( $this -> havepost('PlanTypeId')?$this -> escPost('PlanTypeId'):false ); 
		$_argument['start_age']	  = ( $this -> havepost('PersonalAge')?(int)$this -> escPost('PersonalAge'):false );
		
		// print_r($_argument);
		
		// return by json data 
		$totals_premi_rupiah = self::_self_personal_premi( $this -> escPost('ProductId'),$_argument);
		$_conds  = array
		(
			'success'=> 1, 
			'personal_premi' => formatRupiah($totals_premi_rupiah['ProductPlanPremium']) 
		);
 }
			
 echo json_encode( $_conds );

}

/*
 * @ def 		: AXA_Save / constructor 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */

function _get_payer_data()
{
	$_data = array();
	$sql = "select * from t_gn_customer a where a.CustomerId='". $this -> escPost('CustomerId') ."'";
	$qry = $this -> query($sql);
	if( !$qry -> EOF() )
	{
		$rows = $qry -> result_first_assoc();
		if( count($rows) > 1)
		{
			$set_diff = $this -> Date -> set_date_diff( $this -> formatDateId($rows['CustomerDOB']), date('Y-m-d') );
			$data = array
					( 
						'PayerSalutationId' 		=> $rows['SalutationId'],
						'PayerFirstName' 			=> $rows['CustomerFirstName'],
						'PayerLastName' 			=> $rows['CustomerLastName'],
						'PayerGenderId' 			=> $rows['GenderId'],
						'PayerAddrType' 			=> '0',
						'PayerDOB'					=> $this -> formatDateId($rows['CustomerDOB']), 
						'PayerAge'					=> $set_diff['years'],
						'PayerAddressLine1'			=> $rows['CustomerAddressLine1'],
						'PayerAddressLine2'			=> $rows['CustomerAddressLine2'],
						'PayerAddressLine3'			=> $rows['CustomerAddressLine3'],
						'PayerAddressLine4'			=> $rows['CustomerAddressLine4'],
						'PayerIdentificationTypeId'	=> $rows['IdentificationTypeId'],
						'PayerIdentificationNum' 	=> $rows['CustomerIdentificationNum'],
						'PayerMobilePhoneNum'		=> $rows['CustomerMobilePhoneNum'],
						'PayerMobilePhoneNum2'		=> $rows['CustomerMobilePhoneNum2'],
						'PayerCity'					=> $rows['CustomerCity'],
						'PayerHomePhoneNum'			=> $rows['CustomerHomePhoneNum'],
						'PayerHomePhoneNum2'		=> $rows['CustomerHomePhoneNum2'],
						'PayerZipCode'				=> $rows['CustomerZipCode'],
						'PayerOfficePhoneNum'		=> $rows['CustomerWorkPhoneNum'],
						'PayerOfficePhoneNum2'		=> $rows['CustomerWorkPhoneNum2'],
						'PayerProvinceId'			=> $rows['ProvinceId'],
						'CreditCardTypeId'			=> $rows['CardTypeId'],
						'PayerEmail' 				=> $rows['CustomerEmail'],
						'PayerCreditCardNum'		=> ($rows['CustomerCreditCardNum'] ? $rows['CustomerCreditCardNum'] : ''),
						'PayerFaxNum'				=> '',
						'PayerCreditCardExpDate'	=> $rows['CustomerCreditCardExpDate'],
						'PayersBankId'				=> '0'
				);
		}
	}
	echo json_encode($data);
}

function checkBenef()
{
	$_conds = array('result'=>0);
	
	$sql = "select a.ProductBenefFlag from t_gn_product a where a.ProductId = '".$this->escPost('ProductId')."'";
	$qry = $this->query($sql);
	
	if($qry->result_get_value('ProductBenefFlag'))
	{
		$_conds = array('result'=>1);
	}
	
	echo json_encode($_conds);
}

 function validInsured()
 {
	$_conds = array('result'=>0);
		
	$sql = "select a.PolicyNumber, a.MemberGroup from t_gn_policyautogen a
			where 1=1
			and a.CustomerId = '".$this->escPost('CustomerId')."'
			and a.ProductId = '".$this->escPost('ProductId')."'
			and a.MemberGroup = '".$this->escPost('MemberGroup')."'";
			
	$qry = $this->query($sql);
	
	if($qry->result_num_rows() > 0)
	{
		$_conds = array("result"=>1,"PolicyNumber"=>$qry->result_get_value('PolicyNumber'),"MemberGroup"=>(INT)$qry->result_get_value('MemberGroup'));
	}
	
	echo json_encode($_conds);
 }
 
/*
 * @ def 		: AXA_Save / constructor 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
function _get_splite_data()
{
	$_conds = array('success'=> 0, 'error'=> '');
	
	$_ProductId = $this -> escPost('ProductId');
	
	$qry = $this -> query("select a.PrefixMethod from t_gn_productprefixnumber a where a.ProductId='$_ProductId'");
	//echo "select a.PrefixMethod from t_gn_productprefixnumber a where a.ProductId='$_ProductId'";
	if( !$qry -> EOF() )
	{
		$_conds = array
		(
			'success'=> 1, 
			'pecah'=> $qry -> result_singgle_value()
		);
	}
	
	echo json_encode($_conds);
	
}

/*
 * @ def 		: AXA_Save / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
 function _self_personal_premi( $ProductId=null, $_argument =null )
 {
	$_personal_premi = 0;
	
	if( !is_null($_argument) AND is_array($_argument) AND !is_null($ProductId) )
	{
		$sql = " SELECT a.ProductPlanPremium, a.ProductPlanId FROM t_gn_productplan a  WHERE 1=1 ";
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
		
		if( $_argument['start_age']!='' )
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
 * @ def 		: AXA_Save / constructor 
 
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
 function _set_save_data()
 {
	$_conds = false;
	if( $this -> havepost('CustomerId') ) 
	{
		if( !(INT)$this -> escPost('PecahPolicy') ) 
		{
			$_conds = self::_set_save_one_to_one();
			if( is_array($_conds) )
			{
				self::_set_save_data_payer();
			}	
		}
		else
		{
			$_conds = self::_set_save_one_to_many();
			if( is_array($_conds) )
			{
				self::_set_save_data_payer();	
			}	
		}
	}
	
	echo json_encode($_conds);
 }
 
/*
 * @ def 		: _set_save_one_to_many 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */

private function _set_save_one_to_one()
{
	$conds = array("success"=>0,"polis" => '');
	$Polis = self::_get_class_policy();
	
	if( class_exists('Polis')!=FALSE )
	{
		if( $this -> havepost('InsuredPolicyNumber')!=TRUE or $this->escPost('InsuredPolicyNumber')=='new')
		{
			$Polis -> _set_polis_number( $this -> escPost('ProductId'),null );
			//$Polis -> _set_polis_number( $this -> escPost('ProductId'), 'N' ); with 'N' sufix policynumber
			$PolicyNumber = $Polis -> _get_polis_number();
			$PolicyLastId = $Polis -> _get_last_number();
			
			$Data = array 
			(
				'ProductId' => $this -> escPost('ProductId'),
				'CustomerId' => $this -> escPost('CustomerId'),
				'MemberGroup' => $this -> escPost('InsuredGroupPremi'), 
				'PolicyNumber' => $PolicyNumber, 	
				'PolicyLastNumber' => $PolicyLastId				
			);
			
			$_conds = $this -> set_mysql_insert('t_gn_policyautogen', $Data);	
			if( $_conds ) 
			{
				$_argument['PremiumGroupId'] = ( $this -> havepost('InsuredGroupPremi')?$this -> escPost('InsuredGroupPremi'):false );
				$_argument['PayModeId']	  	 = ( $this -> havepost('InsuredPayMode')?$this -> escPost('InsuredPayMode'):false ); 
				$_argument['ProductPlan'] 	 = ( $this -> havepost('InsuredPlanType')?$this -> escPost('InsuredPlanType'):false ); 
				$_argument['start_age']	  	 = ( $this -> havepost('InsuredAge')?$this -> escPost('InsuredAge'):false );
				
				$_totals = self::_self_personal_premi($this -> escPost('ProductId'),$_argument);
				if( $this -> set_mysql_insert('t_gn_policy',array
				(
					'ProductPlanId'=> $_totals['ProductPlanId'],
					'Premi' => $_totals['ProductPlanPremium'],
					'PolicyNumber'=> $PolicyNumber,
					'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
					'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
				)))
				{
					self::_set_save_insured ( array (
						'PremiumGroupId' => $this -> escPost('InsuredGroupPremi'),
						'CustomerId' => $this -> escPost('CustomerId'),
						'PolicyId' => $this -> get_insert_id(),
						'SalutationId' => $this -> escPost('InsuredSalutationId'),
						'GenderId' => $this -> escPost('InsuredGenderId'),
						'IdentificationTypeId' => $this -> escPost('InsuredIdentificationTypeId'),
						'InsuredIdentificationNum' => $this -> escPost('InsuredIdentificationNum'),
						'RelationshipTypeId' => $this -> escPost('InsuredRelationshipTypeId'),
						'CreatedById' => $this -> getSession('UserId'),
						'InsuredFirstName' => $this -> escPost('InsuredFirstName'),
						'InsuredLastName' => $this -> escPost('InsuredLastName'),
						'InsuredDOB' => $this -> formatDateEng($this -> escPost('InsuredDOB')),
						'InsuredAge' => $this -> escPost('InsuredAge'),
						'InsuredCreatedTs' => date('Y-m-d H:i:s')
					));
					
					$InsuredId =  $this -> get_insert_id();
					if( $InsuredId ){
						self::_set_save_benefiecery($InsuredId);
					}
					
					$conds = array("success" => 1, "polis" => $PolicyNumber );
				}
			}	
			else
				$conds = array("success" => 2, "polis" => $PolicyNumber );
		}
		else // jika mau di ikutkan 
		{
			$_argument['PremiumGroupId'] = ( $this -> havepost('InsuredGroupPremi')?$this -> escPost('InsuredGroupPremi'):false );
			$_argument['PayModeId']	  	 = ( $this -> havepost('InsuredPayMode')?$this -> escPost('InsuredPayMode'):false ); 
			$_argument['ProductPlan'] 	 = ( $this -> havepost('InsuredPlanType')?$this -> escPost('InsuredPlanType'):false ); 
			$_argument['start_age']	  	 = ( $this -> havepost('InsuredAge')?$this -> escPost('InsuredAge'):false );
			
			$_totals = self::_self_personal_premi($this -> escPost('ProductId'),$_argument);
			
			if( $this -> set_mysql_insert('t_gn_policy',array
			(
				'ProductPlanId'=> $_totals['ProductPlanId'],
				'Premi' => $_totals['ProductPlanPremium'],
				'PolicyNumber'=> $this -> escPost('InsuredPolicyNumber'),
				'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
				'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
			)))
			{
				self::_set_save_insured ( array (
					'PremiumGroupId' => $this -> escPost('InsuredGroupPremi'),
					'CustomerId' => $this -> escPost('CustomerId'),
					'PolicyId' => $this -> get_insert_id(),
					'SalutationId' => $this -> escPost('InsuredSalutationId'),
					'GenderId' => $this -> escPost('InsuredGenderId'),
					'IdentificationTypeId' => $this -> escPost('InsuredIdentificationTypeId'),
					'InsuredIdentificationNum' => $this -> escPost('InsuredIdentificationNum'),
					'RelationshipTypeId' => $this -> escPost('InsuredRelationshipTypeId'),
					'CreatedById' => $this -> getSession('UserId'),
					'InsuredFirstName' => $this -> escPost('InsuredFirstName'),
					'InsuredLastName' => $this -> escPost('InsuredLastName'),
					'InsuredDOB' => $this -> formatDateEng($this -> escPost('InsuredDOB')),
					'InsuredAge' => $this -> escPost('InsuredAge'),
					'InsuredCreatedTs' => date('Y-m-d H:i:s')
				));
				
				$InsuredId =  $this -> get_insert_id();
				if( $InsuredId )
				{
					self::_set_save_benefiecery($InsuredId);
				}
				
				$conds = array("success" => 1, "polis" => $this -> escPost('InsuredPolicyNumber') );
			}
		}
	}
	
	return $conds;
}
  
 private function validExistPolicy( $param = array() )
 {
	foreach($param as $key => $value)
	{
		if($key != "new")
		{
			$datas[] = $value;
		}
	}
	
	return $datas;
 }
 
/*
 * @ def 		: _set_save_one_to_many 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
private function checkPolicy()
{
	$sql = "select * from t_gn_policyautogen a where a.PolicyNumber = '".$this -> PolicyNo."'";
	$qry = $this->query($sql);
	
	if($qry->result_num_rows() > 0)
	{
		return true;
	}
	else{
		return false;
	}
}
 
private function _set_save_one_to_many()
{
	$conds = array("success"=>0,"polis" => '');
	$Polis = self::_get_class_policy();
  
	if( class_exists('Polis')!=FALSE ) 
	{
		if($this->havepost('InsuredPolicyNumber') && $this->escPost('InsuredPolicyNumber')!= 'new')
		{
			$conds = $this->proses_save($this->escPost('InsuredPolicyNumber'));
		}
		else{
			$Polis -> _set_polis_number( $this -> escPost('ProductId'),null);
			$PolicyNumber = $Polis -> _get_polis_number();
			$PolicyLastId = $Polis -> _get_last_number();
			
			$conds = $this->proses_save($PolicyNumber,$PolicyLastId);
		}
	}
	
	return $conds;
}

private function proses_save($polNum,$LastId = 0)
{
	$this -> PolicyNo = $polNum;
	$this -> PolicyLast = $LastId;
	
	switch( $this -> escPost('InsuredGroupPremi')) 
	{
		case HOLDER : return $this->saveHolder(); 				break;
		case SPOUSE : return $this->saveSpouse(); 				break;
		case DEPEND : return $this->saveDepend(); 				break;
		default 	: return array("success"=>0,"polis" => ''); break;
	}
}

function saveHolder()
{
	$kondisi = array("success"=>0,"polis" => '');
	
	if( !$this->checkPolicy() )
	{
		// baru, tidak ikut policy siapapun
		$Data = array 
		(
			'ProductId' => $this -> escPost('ProductId'),
			'CustomerId' => $this -> escPost('CustomerId'),
			'MemberGroup' => $this -> escPost('InsuredGroupPremi'), 
			'PolicyNumber' => $this -> PolicyNo, 	
			'PolicyLastNumber' => $this -> PolicyLast			
		);
		
		$_conds = $this -> set_mysql_insert('t_gn_policyautogen', $Data);
		
		if( $_conds )
		{
			$_argument['PremiumGroupId'] = ( $this -> havepost('InsuredGroupPremi')?$this -> escPost('InsuredGroupPremi'):false );
			$_argument['PayModeId']	  	 = ( $this -> havepost('InsuredPayMode')?$this -> escPost('InsuredPayMode'):false ); 
			$_argument['ProductPlan'] 	 = ( $this -> havepost('InsuredPlanType')?$this -> escPost('InsuredPlanType'):false ); 
			$_argument['start_age']	  	 = ( $this -> havepost('InsuredAge')?$this -> escPost('InsuredAge'):false );
			
			$_totals = self::_self_personal_premi($this -> escPost('ProductId'),$_argument);
			if( $this -> set_mysql_insert('t_gn_policy',array
			(
				'ProductPlanId'=> $_totals['ProductPlanId'],
				'Premi' => $_totals['ProductPlanPremium'],
				'PolicyNumber'=> $this -> PolicyNo,
				'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
				'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
			))){
				self::_set_save_insured ( array (
					'PremiumGroupId' => HOLDER,
					'CustomerId' => $this -> escPost('CustomerId'),
					'PolicyId' => $this -> get_insert_id(),
					'SalutationId' => $this -> escPost('InsuredSalutationId'),
					'GenderId' => $this -> escPost('InsuredGenderId'),
					'IdentificationTypeId' => $this -> escPost('InsuredIdentificationTypeId'),
					'InsuredIdentificationNum' => $this -> escPost('InsuredIdentificationNum'),
					'RelationshipTypeId' => $this -> escPost('InsuredRelationshipTypeId'),
					'CreatedById' => $this -> getSession('UserId'),
					'InsuredFirstName' => $this -> escPost('InsuredFirstName'),
					'InsuredLastName' => $this -> escPost('InsuredLastName'),
					'InsuredDOB' => $this -> formatDateEng($this -> escPost('InsuredDOB')),
					'InsuredAge' => $this -> escPost('InsuredAge'),
					'InsuredCreatedTs' => date('Y-m-d H:i:s')
				));
				
				$InsuredId =  $this -> get_insert_id();
				if( $InsuredId ){
					self::_set_save_benefiecery($InsuredId);
				}
				$kondisi = array("success"=>1,"polis" => $this -> PolicyNo);
			}
		}
		else{
			$kondisi = array("success"=>2,"polis" => $this -> PolicyNo);
		}
	}
	
	return $kondisi;
}

function saveSpouse()
{
	$kondisi = array("success"=>0,"polis" => '');
	
	if( $this->checkPolicy() )
	{
		$_argument['PremiumGroupId'] = ( $this -> havepost('InsuredGroupPremi')?$this -> escPost('InsuredGroupPremi'):false );
		$_argument['PayModeId']	  	 = ( $this -> havepost('InsuredPayMode')?$this -> escPost('InsuredPayMode'):false ); 
		$_argument['ProductPlan'] 	 = ( $this -> havepost('InsuredPlanType')?$this -> escPost('InsuredPlanType'):false ); 
		$_argument['start_age']	  	 = ( $this -> havepost('InsuredAge')?$this -> escPost('InsuredAge'):false );
		
		$_totals = self::_self_personal_premi($this -> escPost('ProductId'),$_argument);
		if( $this -> set_mysql_insert('t_gn_policy',array
		(
			'ProductPlanId'=> $_totals['ProductPlanId'],
			'Premi' => $_totals['ProductPlanPremium'],
			'PolicyNumber'=> $this -> PolicyNo,
			'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
			'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
		))){
			self::_set_save_insured ( array (
					'PremiumGroupId' => SPOUSE,
					'CustomerId' => $this -> escPost('CustomerId'),
					'PolicyId' => $this -> get_insert_id(),
					'SalutationId' => $this -> escPost('InsuredSalutationId'),
					'GenderId' => $this -> escPost('InsuredGenderId'),
					'IdentificationTypeId' => $this -> escPost('InsuredIdentificationTypeId'),
					'InsuredIdentificationNum' => $this -> escPost('InsuredIdentificationNum'),
					'RelationshipTypeId' => $this -> escPost('InsuredRelationshipTypeId'),
					'CreatedById' => $this -> getSession('UserId'),
					'InsuredFirstName' => $this -> escPost('InsuredFirstName'),
					'InsuredLastName' => $this -> escPost('InsuredLastName'),
					'InsuredDOB' => $this -> formatDateEng($this -> escPost('InsuredDOB')),
					'InsuredAge' => $this -> escPost('InsuredAge'),
					'InsuredCreatedTs' => date('Y-m-d H:i:s')
				));
				
			$InsuredId =  $this -> get_insert_id();
			if( $InsuredId ){
				self::_set_save_benefiecery($InsuredId);
			}
			
			$kondisi = array("success"=>1,"polis" => $this -> PolicyNo);
		}
	}
	else{
		$Data = array 
		(
			'ProductId' => $this -> escPost('ProductId'),
			'CustomerId' => $this -> escPost('CustomerId'),
			'MemberGroup' => $this -> escPost('InsuredGroupPremi'), 
			'PolicyNumber' => $this -> PolicyNo, 
			'PolicyLastNumber' => $this -> PolicyLast		
		);
		
		$_conds = $this -> set_mysql_insert('t_gn_policyautogen', $Data);
		
		if( $_conds )
		{
			$_argument['PremiumGroupId'] = ( $this -> havepost('InsuredGroupPremi')?$this -> escPost('InsuredGroupPremi'):false );
			$_argument['PayModeId']	  	 = ( $this -> havepost('InsuredPayMode')?$this -> escPost('InsuredPayMode'):false ); 
			$_argument['ProductPlan'] 	 = ( $this -> havepost('InsuredPlanType')?$this -> escPost('InsuredPlanType'):false ); 
			$_argument['start_age']	  	 = ( $this -> havepost('InsuredAge')?$this -> escPost('InsuredAge'):false );
			
			$_totals = self::_self_personal_premi($this -> escPost('ProductId'),$_argument);
			if( $this -> set_mysql_insert('t_gn_policy',array
			(
				'ProductPlanId'=> $_totals['ProductPlanId'],
				'Premi' => $_totals['ProductPlanPremium'],
				'PolicyNumber'=> $this -> PolicyNo,
				'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
				'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
			))){
				self::_set_save_insured ( array (
						'PremiumGroupId' => SPOUSE,
						'CustomerId' => $this -> escPost('CustomerId'),
						'PolicyId' => $this -> get_insert_id(),
						'SalutationId' => $this -> escPost('InsuredSalutationId'),
						'GenderId' => $this -> escPost('InsuredGenderId'),
						'IdentificationTypeId' => $this -> escPost('InsuredIdentificationTypeId'),
						'InsuredIdentificationNum' => $this -> escPost('InsuredIdentificationNum'),
						'RelationshipTypeId' => $this -> escPost('InsuredRelationshipTypeId'),
						'CreatedById' => $this -> getSession('UserId'),
						'InsuredFirstName' => $this -> escPost('InsuredFirstName'),
						'InsuredLastName' => $this -> escPost('InsuredLastName'),
						'InsuredDOB' => $this -> formatDateEng($this -> escPost('InsuredDOB')),
						'InsuredAge' => $this -> escPost('InsuredAge'),
						'InsuredCreatedTs' => date('Y-m-d H:i:s')
					));
					
				$InsuredId =  $this -> get_insert_id();
				if( $InsuredId ){
					self::_set_save_benefiecery($InsuredId);
				}
				
				$kondisi = array("success"=>1,"polis" => $this -> PolicyNo);
			}
		}
		else{
			$kondisi = array("success"=>2,"polis" => $this -> PolicyNo);
		}
		
	}
	
	return $kondisi;
}

function saveDepend()
{
	$kondisi = array("success"=>0,"polis" => '');
	
	if( $this->checkPolicy() )
	{
		$_argument['PremiumGroupId'] = ( $this -> havepost('InsuredGroupPremi')?$this -> escPost('InsuredGroupPremi'):false );
		$_argument['PayModeId']	  	 = ( $this -> havepost('InsuredPayMode')?$this -> escPost('InsuredPayMode'):false ); 
		$_argument['ProductPlan'] 	 = ( $this -> havepost('InsuredPlanType')?$this -> escPost('InsuredPlanType'):false ); 
		$_argument['start_age']	  	 = ( $this -> havepost('InsuredAge')?$this -> escPost('InsuredAge'):false );
		
		$_totals = self::_self_personal_premi($this -> escPost('ProductId'),$_argument);
		if( $this -> set_mysql_insert('t_gn_policy',array
		(
			'ProductPlanId'=> $_totals['ProductPlanId'],
			'Premi' => $_totals['ProductPlanPremium'],
			'PolicyNumber'=> $this -> PolicyNo,
			'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
			'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
		))){
			self::_set_save_insured ( array (
					'PremiumGroupId' => DEPEND,
					'CustomerId' => $this -> escPost('CustomerId'),
					'PolicyId' => $this -> get_insert_id(),
					'SalutationId' => $this -> escPost('InsuredSalutationId'),
					'GenderId' => $this -> escPost('InsuredGenderId'),
					'IdentificationTypeId' => $this -> escPost('InsuredIdentificationTypeId'),
					'InsuredIdentificationNum' => $this -> escPost('InsuredIdentificationNum'),
					'RelationshipTypeId' => $this -> escPost('InsuredRelationshipTypeId'),
					'CreatedById' => $this -> getSession('UserId'),
					'InsuredFirstName' => $this -> escPost('InsuredFirstName'),
					'InsuredLastName' => $this -> escPost('InsuredLastName'),
					'InsuredDOB' => $this -> formatDateEng($this -> escPost('InsuredDOB')),
					'InsuredAge' => $this -> escPost('InsuredAge'),
					'InsuredCreatedTs' => date('Y-m-d H:i:s')
				));
			
			$InsuredId =  $this -> get_insert_id();
			if( $InsuredId ){
				self::_set_save_benefiecery($InsuredId);
			}
				
			$kondisi = array("success"=>1,"polis" => $this -> PolicyNo);
		}
	}
	else{
		$Data = array 
		(
			'ProductId' => $this -> escPost('ProductId'),
			'CustomerId' => $this -> escPost('CustomerId'),
			'MemberGroup' => $this -> escPost('InsuredGroupPremi'), 
			'PolicyNumber' => $this -> PolicyNo, 	
			'PolicyLastNumber' => $this -> PolicyLast			
		);
		
		$_conds = $this -> set_mysql_insert('t_gn_policyautogen', $Data);
		
		if( $_conds )
		{
			$_argument['PremiumGroupId'] = ( $this -> havepost('InsuredGroupPremi')?$this -> escPost('InsuredGroupPremi'):false );
			$_argument['PayModeId']	  	 = ( $this -> havepost('InsuredPayMode')?$this -> escPost('InsuredPayMode'):false ); 
			$_argument['ProductPlan'] 	 = ( $this -> havepost('InsuredPlanType')?$this -> escPost('InsuredPlanType'):false ); 
			$_argument['start_age']	  	 = ( $this -> havepost('InsuredAge')?$this -> escPost('InsuredAge'):false );
			
			$_totals = self::_self_personal_premi($this -> escPost('ProductId'),$_argument);
			if( $this -> set_mysql_insert('t_gn_policy',array
			(
				'ProductPlanId'=> $_totals['ProductPlanId'],
				'Premi' => $_totals['ProductPlanPremium'],
				'PolicyNumber'=> $this -> PolicyNo,
				'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
				'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
			))){
				self::_set_save_insured ( array (
						'PremiumGroupId' => DEPEND,
						'CustomerId' => $this -> escPost('CustomerId'),
						'PolicyId' => $this -> get_insert_id(),
						'SalutationId' => $this -> escPost('InsuredSalutationId'),
						'GenderId' => $this -> escPost('InsuredGenderId'),
						'IdentificationTypeId' => $this -> escPost('InsuredIdentificationTypeId'),
						'InsuredIdentificationNum' => $this -> escPost('InsuredIdentificationNum'),
						'RelationshipTypeId' => $this -> escPost('InsuredRelationshipTypeId'),
						'CreatedById' => $this -> getSession('UserId'),
						'InsuredFirstName' => $this -> escPost('InsuredFirstName'),
						'InsuredLastName' => $this -> escPost('InsuredLastName'),
						'InsuredDOB' => $this -> formatDateEng($this -> escPost('InsuredDOB')),
						'InsuredAge' => $this -> escPost('InsuredAge'),
						'InsuredCreatedTs' => date('Y-m-d H:i:s')
					));
				
				$InsuredId =  $this -> get_insert_id();
				if( $InsuredId ){
					self::_set_save_benefiecery($InsuredId);
				}
					
				$kondisi = array("success"=>1,"polis" => $this -> PolicyNo);
			}
		}
		else{
			$kondisi = array("success"=>2,"polis" => $this -> PolicyNo);
		}
	}
	
	return $kondisi;
}

/*
 * @ def 		: _set_save_one_to_many 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
function _get_Detail()
{
	$_conds = array('success'=>0, 'data' => array());
	
	$sql = "select a.*, d.* from t_gn_insured a left join t_gn_policy b on a.PolicyId=b.PolicyId
			left join t_gn_policyautogen c on c.PolicyNumber=b.PolicyNumber
			left join t_gn_productplan d on b.ProductPlanId=d.ProductPlanId
			where c.PolicyNumber='".$this -> escPost('InsuredPolicyNumber') ."' 
			AND a.PremiumGroupId='" .$this -> escPost('GroupPremi'). "'";
	//echo $sql;		
	$qry = $this -> query($sql);
	$rows = $qry -> result_first_assoc();
	if( count($rows) > 0 )
	{
		$_data['IdentificationTypeId'] = $rows['IdentificationTypeId'];
		$_data['InsuredIdentificationNum'] = $rows['InsuredIdentificationNum'];
		$_data['RelationshipTypeId'] = $rows['RelationshipTypeId'];
		$_data['SalutationId'] = $rows['SalutationId'];
		$_data['InsuredFirstName'] = $rows['InsuredFirstName'];
		$_data['InsuredLastName'] = $rows['InsuredLastName'];
		$_data['GenderId'] = $rows['GenderId'];
		$_data['InsuredDOB'] = $this -> formatDateId($rows['InsuredDOB']);
		$_data['InsuredAge'] = $rows['InsuredAge'];
		$_data['PayModeId'] = $rows['PayModeId'];
		$_data['ProductPlan'] = $rows['ProductPlan'];
		$_data['ProductPlanPremium'] = formatRupiah($rows['ProductPlanPremium']);
		
		if( is_array($_data))
		{
			$_conds = array('success'=>1, 'data' => $_data );
		}	
	}
	
	echo json_encode($_conds);
}

/*
 * @ def 		: AXA_Save / constructor 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
private function _set_save_benefiecery( $InsuredId = null )
{
 $BoxBenef = ( $this -> havepost('BenefBox') ? explode(",",$this -> escPost('BenefBox')) : false );
 if(($BoxBenef!=FALSE) AND (is_array($BoxBenef)) )
 {
	foreach( $BoxBenef as $k => $pos )
	{
		$this -> set_mysql_insert('t_gn_beneficiary',
			array
			(
				'InsuredId' => $InsuredId,
				'CustomerId' => $this -> escPost("CustomerId"), 
				'SalutationId' => $this -> escPost("BenefSalutationId_$pos"), 
				'RelationshipTypeId' => $this -> escPost("BenefRelationshipTypeId_$pos"),
				'BeneficiaryFirstName'=> $this -> escPost("BenefFirstName_$pos"),
				'BeneficiaryLastName' => $this -> escPost("BenefLastName_$pos"),
				'BeneficieryPercentage'=> $this -> escPost("BenefPercentage_$pos"),
				'BeneficiaryDOB'=> $this -> formatDateEng($this -> escPost("BenefDOB_$pos")),
				'BeneficiaryCreatedTs'=> date('Y-m-d H:i:s')
			));	
	}
 }
}


/*
 * @ def 		: AXA_Save / constructor 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
 private function _set_save_data_payer()
 {
	$_conds = false;
	
	if( $this -> set_mysql_insert('t_gn_payer', array(
		'CustomerId' => $this -> escPost('CustomerId'),
		'SalutationId' => $this -> escPost('PayerSalutationId'),
		'PayerFirstName' => $this -> escPost('PayerFirstName'),
		'PayerLastName' => $this -> escPost('PayerLastName'),
		'GenderId' => $this -> escPost('PayerGenderId'),
		'PayerDOB' => $this -> formatDateEng($this -> escPost('PayerDOB')),
		'PayerAddrType' => $this -> escPost('PayerAddrType'),
		'PayerAddressLine1' => $this -> escPost('PayerAddressLine1'),
		'IdentificationTypeId' => $this -> escPost('PayerIdentificationTypeId'),
		'PayerIdentificationNum' => $this -> escPost('PayerIdentificationNum'),
		'PayerMobilePhoneNum' => $this -> escPost('PayerMobilePhoneNum'),
		'PayerCity' => $this -> escPost('PayerCity'),
		'PayerAddressLine2' => $this -> escPost('PayerAddressLine2'),
		'PayerHomePhoneNum' => $this -> escPost('PayerHomePhoneNum'),
		'PremiumGroupId' => ($this -> escPost('pyisholder')?$this -> escPost('HoldGroup'):4),
		'PayerZipCode' => $this -> escPost('PayerZipCode'),
		'PayerAddressLine3' => $this -> escPost('PayerAddressLine3'),
		'PayerOfficePhoneNum' => $this -> escPost('PayerOfficePhoneNum'),
		'ProvinceId' => $this -> escPost('PayerProvinceId'),
		'PayerAddressLine4' => $this -> escPost('PayerAddressLine4'),
		'PayerCreditCardNum' => $this -> escPost('PayerCreditCardNum'),
		'PayersBankId' => $this -> escPost('PayersBankId'),
		'PayerFaxNum' => $this -> escPost('PayerFaxNum'),
		'PayerCreditCardExpDate' => $this -> escPost('PayerCreditCardExpDate'),
		'CreditCardTypeId' => $this -> escPost('CreditCardTypeId'),
		'PayerEmail' => $this -> escPost('PayerEmail'),
		'PayerCreatedTs' => date('Y-m-d H:i:s')
		
	))){ 
		$_conds = true; 
	} 
	else 
	{
		if( $this -> set_mysql_update('t_gn_payer', 
		array(
			'SalutationId' => $this -> escPost('PayerSalutationId'),
			'PayerFirstName' => $this -> escPost('PayerFirstName'),
			'PayerLastName' => $this -> escPost('PayerLastName'),
			'GenderId' => $this -> escPost('PayerGenderId'),
			'PayerAddrType' => $this -> escPost('PayerAddrType'),
			'PayerDOB' => $this -> formatDateEng( $this -> escPost('PayerDOB') ),
			'PayerAddressLine1' => $this -> escPost('PayerAddressLine1'),
			'IdentificationTypeId' => $this -> escPost('PayerIdentificationTypeId'),
			'PayerIdentificationNum' => $this -> escPost('PayerIdentificationNum'),
			'PayerMobilePhoneNum' => $this -> escPost('PayerMobilePhoneNum'),
			'PayerCity' => $this -> escPost('PayerCity'),
			'PayerAddressLine2' => $this -> escPost('PayerAddressLine2'),
			'PayerHomePhoneNum' => $this -> escPost('PayerHomePhoneNum'),
			'PayerZipCode' => $this -> escPost('PayerZipCode'),
			'PayerAddressLine3' => $this -> escPost('PayerAddressLine3'),
			'PayerOfficePhoneNum' => $this -> escPost('PayerOfficePhoneNum'),
			'ProvinceId' => $this -> escPost('PayerProvinceId'),
			'PayerAddressLine4' => $this -> escPost('PayerAddressLine4'),
			'PayerCreditCardNum' => $this -> escPost('PayerCreditCardNum'),
			'PayersBankId' => $this -> escPost('PayersBankId'),
			'PayerFaxNum' => $this -> escPost('PayerFaxNum'),
			'PayerCreditCardExpDate' => $this -> escPost('PayerCreditCardExpDate'),
			'CreditCardTypeId' => $this -> escPost('CreditCardTypeId'),
			'PayerEmail' => $this -> escPost('PayerEmail'),
			'PayerUpdatedTs' => date('Y-m-d H:i:s')
		),array('CustomerId'=>$this -> escPost('CustomerId')))){
			$_conds = true;
		}
	}
	
	return $_conds;
 }
/*
 * @ def 		: get_size_insured 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
 
 function get_size_insured($PolicyNumber)
 {
	$_conds = array();
	
	$sql = "SELECT b.InsuredId, e.PremiumGroupDesc,
			d.PayMode, c.ProductPlanName, h.PlanName, h.PlanNameAlias,
			c.ProductPlanPremium, b.InsuredFirstName, b.InsuredDOB, b.InsuredAge, b.PremiumGroupId,
			f.ProductCode, f.ProductName, g.StatusQCcode, b.QCStatus
			FROM t_gn_policy a 
			inner join t_gn_insured b on a.PolicyId=b.PolicyId
			LEFT JOIN t_gn_productplan c on a.ProductPlanId=c.ProductPlanId
			LEFT JOIN t_lk_paymode d on c.PayModeId=d.PayModeId
			LEFT JOIN t_lk_premiumgroup e on c.PremiumGroupId=e.PremiumGroupId
			left join t_gn_product f on c.ProductId = f.ProductId
			left join t_lk_qcstatus g on b.QCStatus = g.StatusQCid
			left join t_lk_plan_name h on c.ProductPlan = h.PlanSection
			WHERE a.PolicyNumber='$PolicyNumber'";
	
	$qry = $this -> query($sql);
	$i=0;
	foreach($qry -> result_assoc() as $rows ) 
	{
		$_conds[$i]['InsuredId']= $rows['InsuredId'];
		$_conds[$i]['PremiumGroupDesc']= $rows['PremiumGroupDesc'];	
		$_conds[$i]['PayMode']= $rows['PayMode'];
		$_conds[$i]['ProductPlanName']= $rows['PlanNameAlias'];
		$_conds[$i]['ProductPlanPremium']= $rows['ProductPlanPremium'];
		$_conds[$i]['InsuredFirstName']= $rows['InsuredFirstName'];
		$_conds[$i]['InsuredDOB']= $rows['InsuredDOB'];
		$_conds[$i]['InsuredAge']= $rows['InsuredAge'];
		$_conds[$i]['PremiumGroupId']= $rows['PremiumGroupId'];
		$_conds[$i]['ProductName']= $rows['ProductCode'].' - '.$rows['ProductName'];
		$_conds[$i]['StatusQCcode']= $rows['StatusQCcode'];
		$_conds[$i]['QCStatus']= $rows['QCStatus'];
		$i++;	
	}
	return $_conds;	
 } 
 
/*
 * @ def 		: get_size_policy 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
 
 function get_size_policy()
 {
	$_conds = array();
	$sql = " SELECT b.PolicyNumber, UCASE(( SELECT d.InsuredFirstName FROM t_gn_insured d WHERE d.PolicyId = b.PolicyId ORDER BY d.InsuredId ASC  LIMIT 1 )) as NamaPemegangPolicy FROM t_gn_policyautogen a 
			 INNER JOIN t_gn_policy b on a.PolicyNumber=b.PolicyNumber
		     WHERE a.CustomerId = '".$this -> escPost('CustomerId')."'
		     GROUP BY b.PolicyNumber ";
	// echo $sql;
	$qry = $this -> query($sql);
	
	$i=0;
	foreach($qry -> result_assoc() as $rows ) 
	{
		$_conds[$i]['PolicyNumber']= $rows['PolicyNumber'];
		$_conds[$i]['NamaPemegangPolicy']= $rows['NamaPemegangPolicy'];	
		$i++;	
	}
	return $_conds;	
 }
 
/*
 * @ def 		: _get_transaction 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 function _get_benefit()
 {
	if($this->havepost('ProductId'))
	{
		$sql = "select distinct(a.ProductPlan) as ProductPlan, a.ProductPlanName, concat(b.ProductCode,' - ',b.ProductName) as Product
				from t_gn_productplan a
				left join t_gn_product b on a.ProductId = b.ProductId
				where a.ProductId = '".$this->escPost('ProductId')."'";
		$qry = $this->query($sql);
		?>
		<table border="1" width="99%" cellpadding="2" cellspacing="0">
			<tr>
				<td colspan="<?php echo ($qry->result_num_rows()*2); ?>"><?php echo $qry->result_get_value('Product'); ?></td>
			</tr>
			<tr>
				<?php
				foreach( $qry->result_assoc() as $rows )
				{
					echo "<td colspan=\"2\">".$rows['ProductPlanName']."</td>";
					$content[$rows['ProductPlan']] = $this->getContentBenefit($rows['ProductPlan']);
				}
				?>
			</tr>
			<tr>
				<?php
				for($idx = 1;$idx<=$qry->result_num_rows();$idx++)
				{
					?>
					<td>Benefit</td>
					<td>Description</td>
					<?php
				}
				?>
			</tr>
			<tr>
				<?php
				for($idz = 1;$idz<=$qry->result_num_rows();$idz++)
				{
					?>
					<td><?php echo $content[$idz]['ProductPlanBenefit']; ?></td>
					<td><?php echo $content[$idz]['ProductPlanBenefitDesc']; ?></td>
					<?php
				}
				?>
			</tr>
		</table>
		<?php
	}
 }

 function getContentBenefit($planId=0)
 {
	$sql = "select a.ProductPlanBenefit, a.ProductPlanBenefitDesc from t_gn_productplanbenefit a
			where 1=1
			AND a.ProductId = '".$this->escPost('ProductId')."'
			AND a.ProductPlan = '".$planId."'
			AND a.ProductPlanBenefitStatusFlag = 1";
	$qry = $this->query($sql);
	
	$html['ProductPlanBenefit'] = "<table>";
	$html['ProductPlanBenefitDesc'] = "<table>";
	foreach($qry->result_assoc() as $rows)
	{
		$html['ProductPlanBenefit'] .="<tr>
											<td>".$rows['ProductPlanBenefit']."</td>
										</tr>";
		$html['ProductPlanBenefitDesc'] .="<tr>
											<td>".$rows['ProductPlanBenefitDesc']."</td>
										</tr>";
	}
	$html['ProductPlanBenefit'] .= "</table>";
	$html['ProductPlanBenefitDesc'] .= "</table>";
	
	return $html;
 }
  
  function getStyle($id)
  {
	switch($id)
	{
		case 1 : $value = "style='font-weight:bold;color:#009900;'"; break;
		case 2 : $value = "style='font-weight:bold;color:#FFCC00;'"; break;
		case 3 : $value = "style='font-weight:bold;color:#FF0000;'"; break;
		case 4 : $value = "style='font-weight:bold;color:#FF0000;'"; break;
		
		default : $value = ""; break;
	}
	
	return $value;
  }
  
function _get_transaction()
{ 
	$GrandTotals = 0;
	$GrandDiscount = 0;
	
	echo "<table border=\"0\" align=\"center\" width=\"99%\" cellpadding=\"2\" cellspacing=\"0\">
				<tr>
					<td class=\"header-first\" align=\"center\">#</td>
					<td class=\"header-first\">Group Premi</td>
					<td class=\"header-first\">First Name </td>
					<td class=\"header-first\">DOB</td>
					<td class=\"header-first\">Age</td>
					<td class=\"header-first\">Plan</td>
					<td class=\"header-first\">Product</td>
					<td class=\"header-first\">Payment Type</td>
					<td class=\"header-first\">Premi</td>
					<td class=\"header-last\">Status</td>
				</tr> ";
				
	foreach( self::get_size_policy() as $rows )
	{
		echo "  <tr>
					<td class=\"rows-first\" style=\"background-color:#FFFCCC;height:24px;padding-left:4px;font-weight:bold;color:green;font-size:12px;\" align=\"left\" colspan=\"10\">". $rows['PolicyNumber']. "  / " . $rows['NamaPemegangPolicy'] ." </td>
				</tr>";
							
				
			/** calculation evry Number policy **/
			
				$SubTotals = 0;  $dis=0; $discount = 0;
				foreach( self::get_size_insured($rows['PolicyNumber']) as $rows )
				{
					echo "  <tr>
								<td class=\"rows-first\" align=\"center\"><input type=\"checkbox\" value=\"{$rows['InsuredId']}\" onclick=\"InsuredWindow(this)\"></td>
								<td class=\"rows-first\" align=\"left\">".strtoupper($rows['PremiumGroupDesc'])."</td>
								<td class=\"rows-first\" align=\"left\">".strtoupper($rows['InsuredFirstName'])."</td>
								<td class=\"rows-first\" align=\"left\">".strtoupper($rows['InsuredDOB'])."</td>
								<td class=\"rows-first\" align=\"center\">".strtoupper($rows['InsuredAge'])."</td>
								<td class=\"rows-first\" align=\"left\">".strtoupper($rows['ProductPlanName'])."</td>
								<td class=\"rows-first\" align=\"left\">".strtoupper($rows['ProductName'])."</td>
								<td class=\"rows-first\" align=\"left\">".strtoupper($rows['PayMode'])."</td>
								<td class=\"rows-first\" align=\"right\">".formatRupiah($rows['ProductPlanPremium'])."</td>
								<td class=\"rows-last\" ".$this->getStyle($rows['QCStatus'])." align=\"right\">".($rows['StatusQCcode']?strtoupper($rows['StatusQCcode']):'-')."</td>
							</tr>";
							
					$SubTotals+= (int)$rows['ProductPlanPremium']; 	
					$dis++;	
				} 
				
		$discount = ( $dis > 1 ? ($SubTotals*10/100):0 );
		$adiscount = $SubTotals-( $dis > 1 ? ($SubTotals*10/100):0 );
		$GrandDiscount+= $discount;
		$GrandaDiscount+= $adiscount;
		$GrandTotals+= $SubTotals;
		
		
		echo "  <tr>
					<td class=\"rows-first\" style=\"border-top:1px solid red;\" align=\"left\" colspan=\"2\"><b style=\"color:red;\"> * Discount</b></td>
					<td class=\"rows-first\" style=\"border-top:1px solid red;\" align=\"right\">".formatRupiah($discount)."</td>
					<td class=\"rows-first\" style=\"border-top:1px solid red;\" align=\"left\" colspan=\"4\"></td>
					<td class=\"rows-first\" style=\"border-top:1px solid red;\" align=\"left\"><b style=\"color:red;\"> Sub Total</b></td>
					<td class=\"rows-last\"  style=\"border-top:1px solid red;\" align=\"right\" colspan=\"2\">".formatRupiah($SubTotals)."</td>
				</tr>";	
		echo "  <tr>
				<td class=\"rows-first\" style=\"border-top:1px solid red;\" align=\"left\" colspan=\"2\"><b style=\"color:red;\"> After Discount</b></td>
					<td class=\"rows-first\" style=\"border-top:1px solid red;\" align=\"right\">".formatRupiah($adiscount)."</td>
				</tr>";	
		
	}
	
	
	echo " <tr>
				<td class=\"header-first\" style=\"border-top:1px solid red;\" align=\"left\" colspan=\"2\"><b style=\"color:white;\"> Discount Total</b></td>
				<td class=\"header-first\" style=\"border-top:1px solid red;\" align=\"right\">".formatRupiah($GrandDiscount)."</td>
				<td class=\"header-first\" style=\"border-top:1px solid red;\" align=\"left\" colspan=\"4\">&nbsp;</td>
				<td class=\"header-first\" style=\"border-top:1px solid red;\" align=\"left\"><b style=\"color:white;\"> Grand Total</b></td>
				<td class=\"header-last\" style=\"border-top:1px solid red;\" align=\"right\" colspan=\"2\">".formatRupiah($GrandTotals)."</td>
			</tr>";

	echo " <tr>
			<td class=\"header-first\" style=\"border-top:1px solid red;\" align=\"left\" colspan=\"2\"><b style=\"color:white;\"> After Discount Total</b></td>
				<td class=\"header-first\" style=\"border-top:1px solid red;\" align=\"right\">".formatRupiah($GrandaDiscount)."</td>
			</tr>";	
				
	echo "</table><br>";
	echo "<span class=\"wrap\" style=\"font-size:11px;\"> <i> * ) Discount 10 % IF Insured > 1 , Payment Premi : ".formatRupiah( ($GrandTotals-$GrandDiscount) )." </i></span>";
}

}
new AXA_Save();