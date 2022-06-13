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
 */
 
define('HOLDER',2); 
define('SPOUSE',3); 
define('DEPEND',1); 
 
class AXA_Save extends mysql
{
	
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
		case '_get_premi' 		: self::_get_personal_premi();  break;
		case '_get_payer_data'  : self::_get_payer_data(); 		break;
		case '_get_split'		: self::_get_splite_data(); 	break;
		case '_savePolis'		: self::_set_save_data(); 		break;
		
		case '_load_plan'		: self::loadPlan(); 			break;
		case '_load_pay'		: self::loadPay(); 				break;
		case '_load_plan_sp'	: self::loadPlanSpouse(); 		break;
		case '_load_pay_sp'		: self::loadPaySpouse(); 		break;
		case '_load_plan_dp'	: self::loadPlanDependent(); 	break;
		case '_load_pay_dp'		: self::loadPayDependent(); 	break;
	}
}

/* @ def 	: _get_member_polis()
 *
 * @ param  : jika dependent tidak berdiri sendiri && dan terikat oleh holder / spouse 
 * @ author : razaki team 
 */ 
 
private function _get_member_polis( $Group = null)
{
	$_conds = null;
	
	$CustomerId = ( $this -> havepost('CustomerId') ? $this -> escPost('CustomerId') : null );
	$ProductId	= ( $this -> havepost('ProductId') ? $this -> escPost('ProductId') : null );
	
	if( (!is_null($Group)) AND (!is_null($CustomerId)) AND (!is_null($ProductId)) AND ($Group!= DEPEND) ) 
	{
		$sql = " SELECT a.PolicyNumber FROM t_gn_policyautogen a 
				 WHERE a.CustomerId= '$CustomerId'
				 AND a.ProductId= '$ProductId' 
				 AND a.MemberGroup='$Group' ";
		$qry = $this -> query($sql);
		if( !$qry -> EOF() )
		{
			$_conds = $qry -> result_get_value('PolicyNumber');
		}
	}

	return $_conds;
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

/* @ def 	: getPolicyId()
 *
 * @ param  : generator policy parameter
 * @ author : razaki team 
 */ 
 
 private function _get_polis_number( $InsertId=0 )
 {	
	$_conds = null;
	
	$sql = " SELECT a.PolicyNumber FROM t_gn_policyautogen a WHERE a.PolicyAutoGenId='$InsertId'";
	$qry = $this -> query($sql);
	if( !$qry -> EOF() )
	{
		$_conds = $qry -> result_get_value('PolicyNumber');
	}
	
	return $_conds;
 }
 
/* @ def 	: getPolicyId()
 *
 * @ param  : generator policy parameter
 * @ author : razaki team 
 */ 

function _get_last_number()
{
	$_conds = 0;
	if( $this -> havepost('ProductId') )
	{
		$sql = " SELECT MAX(a.PolicyLastNumber) as Number FROM t_gn_policyautogen a WHERE a.ProductId = '".$this -> escPost('ProductId')."'";
		
		$qry = $this -> query($sql);
		if( !$qry -> EOF() )
		{
			$_conds = (( (INT) $qry -> result_singgle_value())+1);
		}
	}
	return $_conds;
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
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */

function _set_save_data()
{
  $_conds = false;
  if( $this -> havepost('CustomerId') ) 
  {
		if( (INT)$this -> escPost('PecahPolicy')!=FALSE) {
			$_conds = self::_set_save_one_to_one();
		}
		else{
			$_conds = self::_set_save_one_to_many();
		}
  }
  
  //echo $_conds;
}


/*
 * @ def 		: _set_save_one_to_one
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
private function _set_save_holder()
{
 
 $_conds = false;
 
 $Polis = self::_get_class_policy();
 if( class_exists('Polis') ) 
 {
	$Polis -> _set_polis_number( $this -> escPost('ProductId'), self::_get_last_number(), 'Y' );
	if( $this -> set_mysql_insert('t_gn_policyautogen', 
		array
		(
			'ProductId' => $this->escPost('ProductId'),
			'CustomerId' => $this->escPost('CustomerId'),
			'PolicyNumber' => $Polis->_get_polis_number(), 
			'PolicyLastNumber' => self::_get_last_number(),
			'MemberGroup' => $this -> escPost('HoldGroup')		
		)
	))
	{
		$_get_insert_id = $this -> get_insert_id();
		
		if( class_exists('Customer') && $this -> havepost('Holder') && $_get_insert_id )
		{
			$_exist_group_premi = $this -> Customer -> _getExistGroupPremi( $this -> escPost('ProductId'));
			
			if( ($_exist_group_premi!=FALSE) )
			{
				$_argument['PremiumGroupId'] = (INT)$this -> escPost('HoldGroup'); 
			}
			
			$_argument['PayModeId']	  = ( $this -> havepost('HoldPayMode')?$this -> escPost('HoldPayMode'):false ); 
			$_argument['ProductPlan'] = ( $this -> havepost('HoldPlanType')?$this -> escPost('HoldPlanType'):false ); 
			$_argument['start_age']	  = ( $this -> havepost('HoldAge')?$this -> escPost('HoldAge'):false );
			
			$totals_premi = self::_self_personal_premi($this -> escPost('ProductId'),$_argument);
			if( $this -> set_mysql_insert('t_gn_policy',
				array
				(
					'ProductPlanId'=> $totals_premi['ProductPlanId'],
					'Premi' => $totals_premi['ProductPlanPremium'],
					'PolicyNumber'=> self::_get_polis_number($_get_insert_id),
					'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
					'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
				)
			)){
				
				self::_set_save_insured ( array (
					'PremiumGroupId' => HOLDER,
					'CustomerId' => $this -> escPost('CustomerId'),
					'PolicyId' => $this -> get_insert_id(),
					'SalutationId' => $this -> escPost('HoldSalutationId'),
					'GenderId' => $this -> escPost('HoldGenderId'),
					'IdentificationTypeId' => $this -> escPost('HoldIdentificationTypeId'),
					'InsuredIdentificationNum' => $this -> escPost('HoldIdentificationNum'),
					'RelationshipTypeId' => $this -> escPost('HoldRelationshipTypeId'),
					'CreatedById' => $this -> getSession('UserId'),
					'InsuredFirstName' => $this -> escPost('HoldFirstName'),
					'InsuredLastName' => $this -> escPost('HoldLastName'),
					'InsuredDOB' => $this -> formatDateEng($this -> escPost('HoldDOB')),
					'InsuredAge' => $this -> escPost('HoldAge'),
					'InsuredCreatedTs' => date('Y-m-d H:i:s')
				));
				
				$_conds = array( self::_get_polis_number($_get_insert_id) ); 
			}
		}
	}
  }
  
  return $_conds;
}

/*
 * @ def 		: _set_save_spouse_one_to_one
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
private function _set_save_spouse()
{
 $_conds = false; 
 $Polis = self::_get_class_policy();
 if( class_exists('Polis') AND $this -> havepost('Spouse') ) 
 {
	$Polis -> _set_polis_number( $this -> escPost('ProductId'), self::_get_last_number(), 'Y' );
	if( $this -> set_mysql_insert('t_gn_policyautogen', 
		array
		(
			'ProductId' => $this->escPost('ProductId'),
			'CustomerId' => $this->escPost('CustomerId'),
			'PolicyNumber' => $Polis->_get_polis_number(), 
			'PolicyLastNumber' => self::_get_last_number(),
			'MemberGroup' => $this -> escPost('SpGroup')	
		)
	))
	{
		$_get_insert_id = $this -> get_insert_id();
		
		if( class_exists('Customer') && $this -> havepost('Spouse') && $_get_insert_id )
		{
			$_exist_group_premi = $this -> Customer -> _getExistGroupPremi( $this -> escPost('ProductId'));
			
			if( ($_exist_group_premi!=FALSE) ) 
			{
				$_argument['PremiumGroupId'] = (INT)$this -> escPost('SpGroup'); 
			}
			
			$_argument['PayModeId']	  = ( $this -> havepost('SpPaymode')?$this -> escPost('SpPaymode'):false ); 
			$_argument['ProductPlan'] = ( $this -> havepost('SpPlanType')?$this -> escPost('SpPlanType'):false ); 
			$_argument['start_age']	  = ( $this -> havepost('SpAge')?$this -> escPost('SpAge'):false );
			
			$totals_premi = self::_self_personal_premi($this -> escPost('ProductId'),$_argument);
			if( $this -> set_mysql_insert('t_gn_policy',
				array
				(
					'ProductPlanId'=> $totals_premi['ProductPlanId'],
					'Premi' => $totals_premi['ProductPlanPremium'],
					'PolicyNumber'=> self::_get_polis_number($_get_insert_id),
					'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
					'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
				))){
				
				self::_set_save_insured(array(
					'PremiumGroupId' => SPOUSE,
					'CustomerId' => $this -> escPost('CustomerId'),
					'PolicyId' => $this -> get_insert_id(),
					'SalutationId' => $this -> escPost('SpSalutationId'),
					'GenderId' => $this -> escPost('SpGenderId'),
					'IdentificationTypeId' => $this -> escPost('SpIdentificationTypeId'),
					'InsuredIdentificationNum' => $this -> escPost('SpIdentificationNum'),
					'RelationshipTypeId' => $this -> escPost('SpRelationshipTypeId'),
					'CreatedById' => $this -> getSession('UserId'),
					'InsuredFirstName' => $this -> escPost('SpFirstName'),
					'InsuredLastName' => $this -> escPost('SpLastName'),
					'InsuredDOB' => $this -> formatDateEng($this -> escPost('SpDOB')),
					'InsuredAge' => $this -> escPost('SpAge'),
					'InsuredCreatedTs' => date('Y-m-d H:i:s')
					));
				
					$_conds = array( self::_get_polis_number($_get_insert_id) ); 
				}
		}
	}
  }
  
  return $_conds;
}


/*
 * @ def 		: _set_save_dependent 
 *
 * @ params 	: policy di pecah 
 * @ return 	: void 
 */
 
private function _set_save_dependent()
{

 $_conds = false; 	
 $Dependent = ( $this -> havepost('dependent') ? explode(',', $this -> escPost('dependent')) : null );
 
 if( !is_null($Dependent) )
 {
	foreach( $Dependent as $k => $pos ) 
	{
		$Polis = self::_get_class_policy();
		if( class_exists('Polis'))
		{
			if( ( $this -> havepost("DepMemberOf_$pos") ) AND ($this -> escPost("DepMemberOf_$pos")!=DEPEND ) )
			{
				$Polis -> _set_polis_number( $this -> escPost('ProductId'), self::_get_last_number(), 'Y' );
				$_get_polis_number = self ::_get_member_polis( $this -> escPost("DepMemberOf_$pos"), $Polis -> _get_polis_number() );
				
			// terikat dengan member 
				if( class_exists('Customer') && $this -> havepost('DepGroup') )
				{
					$_exist_group_premi = $this -> Customer -> _getExistGroupPremi( $this -> escPost('ProductId'));
					
					if(($_exist_group_premi!=FALSE)) 
					{
						$_argument['PremiumGroupId'] = (INT)$this -> escPost('DepGroup'); 
					}
						
					$_argument['PayModeId']	  = ( $this -> havepost("DepPaymode_$pos")?$this -> escPost("DepPaymode_$pos"):false ); 
					$_argument['ProductPlan'] = ( $this -> havepost("DepPlanType_$pos")?$this -> escPost("DepPlanType_$pos"):false ); 
					$_argument['start_age']	  = ( $this -> havepost("DepAge_$pos")?$this -> escPost("DepAge_$pos"):false );
						
					$totals_premi = self::_self_personal_premi( $this -> escPost('ProductId'), $_argument );
					if( $this -> set_mysql_insert('t_gn_policy',
						array
						(
							'ProductPlanId' => $totals_premi['ProductPlanId'],
							'Premi' => $totals_premi['ProductPlanPremium'],
							'PolicyNumber' => $_get_polis_number,
							'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
							'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
					 )))
					{
						self::_set_save_insured(array(
							'PremiumGroupId' => DEPEND,
							'CustomerId' => $this -> escPost('CustomerId'),
							'PolicyId' => $this -> get_insert_id(),
							'SalutationId' => $this -> escPost("DepSalutationId_$pos"),
							'GenderId' => $this -> escPost("DepGenderId_$pos"),
							'RelationshipTypeId' => $this -> escPost("DepRelationshipTypeId_$pos"),
							'CreatedById' => $this -> getSession('UserId'),
							'InsuredFirstName' => $this -> escPost("DepFirstName_$pos"),
							'InsuredLastName' => $this -> escPost("DepLastName_$pos"),
							'InsuredDOB' => $this -> formatDateEng($this -> escPost("DepDOB_$pos")),
							'InsuredAge' => $this -> escPost("DepAge_$pos"),
							'InsuredCreatedTs' => date('Y-m-d H:i:s')
							));
							
						$_conds[$_get_polis_number] = $_get_polis_number; 
					}
				}
			}
			else // tidak terikat dengan member 
			{
				$Polis -> _set_polis_number( $this -> escPost('ProductId'), self::_get_last_number(), 'Y' );
				if( $this -> set_mysql_insert('t_gn_policyautogen', 
					array
					(
						'ProductId' => $this->escPost('ProductId'),
						'CustomerId' => $this->escPost('CustomerId'),
						'PolicyNumber' => $Polis->_get_polis_number(), 
						'PolicyLastNumber' => self::_get_last_number(),
						'MemberGroup' => $this -> escPost('DepGroup')	
					)
				))
				{
					$_get_insert_id = $this -> get_insert_id();
					
					if( class_exists('Customer') && $this -> havepost('DepGroup') && $_get_insert_id )
					{
						$_exist_group_premi = $this -> Customer -> _getExistGroupPremi( $this -> escPost('ProductId'));
						
						if(($_exist_group_premi!=FALSE)) 
						{
							$_argument['PremiumGroupId'] = (INT)$this -> escPost('DepGroup'); 
						}
						
						$_argument['PayModeId']	  = ( $this -> havepost("DepPaymode_$pos")?$this -> escPost("DepPaymode_$pos"):false ); 
						$_argument['ProductPlan'] = ( $this -> havepost("DepPlanType_$pos")?$this -> escPost("DepPlanType_$pos"):false ); 
						$_argument['start_age']	  = ( $this -> havepost("DepAge_$pos")?$this -> escPost("DepAge_$pos"):false );
						
						$totals_premi = self::_self_personal_premi( $this -> escPost('ProductId'), $_argument );
						if( $this -> set_mysql_insert('t_gn_policy',
							array
							(
								'ProductPlanId'=> $totals_premi['ProductPlanId'],
								'Premi' => $totals_premi['ProductPlanPremium'],
								'PolicyNumber'=> $Polis->_get_polis_number(),
								'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
								'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
							)))
							{
								self::_set_save_insured(array(
									'PremiumGroupId' => DEPEND,
									'CustomerId' => $this -> escPost('CustomerId'),
									'PolicyId' => $this -> get_insert_id(),
									'SalutationId' => $this -> escPost("DepSalutationId_$pos"),
									'GenderId' => $this -> escPost("DepGenderId_$pos"),
									'RelationshipTypeId' => $this -> escPost("DepRelationshipTypeId_$pos"),
									'CreatedById' => $this -> getSession('UserId'),
									'InsuredFirstName' => $this -> escPost("DepFirstName_$pos"),
									'InsuredLastName' => $this -> escPost("DepLastName_$pos"),
									'InsuredDOB' => $this -> formatDateEng($this -> escPost("DepDOB_$pos")),
									'InsuredAge' => $this -> escPost("DepAge_$pos"),
									'InsuredCreatedTs' => date('Y-m-d H:i:s')
									));
									
									
								$_conds[$Polis->_get_polis_number()] = $Polis->_get_polis_number(); 
							}
					}
				}
			
			
			}
			
			// next process 
			
		}
	}
  }	
  
  return $_conds;
}

/*
 * @ def 		: _set_save_one_to_one
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
private function _set_save_one_to_one()
{
  $conds  = array("success"=>0,"polis" => null );
  $polis  = array();
  
  
 
  if( (self::_get_singgle_polis()!=TRUE) ) 
  {
	foreach
	( 
		array 
		( 
			self::_set_save_holder(), 
			self::_set_save_spouse(), 
			self::_set_save_dependent() 
		) 
		as $k => $data ) 
	{
		foreach( $data as $_k => $_polis ) 
		{
			$polis[] = $_polis; 
		}	
	 }
		
		$conds  = array("success" => 1, "polis" => $polis);
		if( is_array( $conds) )
		{
			self::_set_save_data_payer();
			self::_set_save_benefiecery();	
		}
  }
  else
  {
	$conds = array("success" => 2, "polis" => self::_get_singgle_polis() );
  }

	echo json_encode($conds);	
}

/*
 * @ def 		: _set_save_one_to_many 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
 
private function _set_save_one_to_many()
{
  $conds = array("success"=>0,"polis" => '');
  $Polis = self::_get_class_policy();
  
  if( (class_exists('Polis')!=FALSE) && ( self::_get_singgle_polis()!=TRUE ) )
  {
	
	$Polis -> _set_polis_number( $this -> escPost('ProductId'), self::_get_last_number(), 'N' );
	if( $this -> set_mysql_insert('t_gn_policyautogen', 
		array 
		(
			'ProductId' => $this->escPost('ProductId'),
			'CustomerId' => $this->escPost('CustomerId'),
			'PolicyNumber' => $Polis->_get_polis_number(), 
			'PolicyLastNumber' => self::_get_last_number(),
			'MemberGroup' => $this -> escPost('HoldGroup')	
		)
	 ))
	 {
		$_get_insert_id = $this -> get_insert_id();
		
		/** set holder jika tidak pecah **/
		
		if( class_exists('Customer') && $this -> havepost('HoldGroup') && $_get_insert_id )
		{
			$_exist_group_premi = $this -> Customer -> _getExistGroupPremi( $this -> escPost('ProductId'));
			if( $_exist_group_premi )
			{ 
				$_argument['PremiumGroupId'] = (INT)$this -> escPost('HoldGroup'); 
			}
				
			$_argument['PayModeId']	  = ( $this -> havepost('HoldPayMode')?$this -> escPost('HoldPayMode'):false ); 
			$_argument['ProductPlan'] = ( $this -> havepost('HoldPlanType')?$this -> escPost('HoldPlanType'):false ); 
			$_argument['start_age']	  = ( $this -> havepost('HoldAge')?$this -> escPost('HoldAge'):false );
			
			$totals_premi = self::_self_personal_premi($this -> escPost('ProductId'),$_argument);
			if( $this -> set_mysql_insert('t_gn_policy',
				array
				(
					'ProductPlanId'=> $totals_premi['ProductPlanId'],
					'Premi' => $totals_premi['ProductPlanPremium'],
					'PolicyNumber'=> self::_get_polis_number($_get_insert_id),
					'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
					'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
				)
			 ))
			{
				self::_set_save_insured ( array (
					'PremiumGroupId' => HOLDER,
					'CustomerId' => $this -> escPost('CustomerId'),
					'PolicyId' => $this -> get_insert_id(),
					'SalutationId' => $this -> escPost('HoldSalutationId'),
					'GenderId' => $this -> escPost('HoldGenderId'),
					'IdentificationTypeId' => $this -> escPost('HoldIdentificationTypeId'),
					'InsuredIdentificationNum' => $this -> escPost('HoldIdentificationNum'),
					'RelationshipTypeId' => $this -> escPost('HoldRelationshipTypeId'),
					'CreatedById' => $this -> getSession('UserId'),
					'InsuredFirstName' => $this -> escPost('HoldFirstName'),
					'InsuredLastName' => $this -> escPost('HoldLastName'),
					'InsuredDOB' => $this -> formatDateEng($this -> escPost('HoldDOB')),
					'InsuredAge' => $this -> escPost('HoldAge'),
					'InsuredCreatedTs' => date('Y-m-d H:i:s')
				));
				
				$conds = array("success" => 1, "polis" => array( self::_get_polis_number($_get_insert_id)));
			}
		}
			
		/** set spouse jika tidak pecah **/
			
		if( class_exists('Customer') && $this -> havepost('Spouse') && $_get_insert_id )
		{
			$_exist_group_premi = $this -> Customer -> _getExistGroupPremi( $this -> escPost('ProductId'));
			if( $_exist_group_premi ) 
			{
				$_argument['PremiumGroupId'] = (INT)$this -> escPost('SpGroup'); 
			}
			
			$_argument['PayModeId']	  = ( $this -> havepost('SpPaymode')?$this -> escPost('SpPaymode'):false ); 
			$_argument['ProductPlan'] = ( $this -> havepost('SpPlanType')?$this -> escPost('SpPlanType'):false ); 
			$_argument['start_age']	  = ( $this -> havepost('SpAge')?$this -> escPost('SpAge'):false );
			
			$totals_premi = self::_self_personal_premi($this -> escPost('ProductId'),$_argument);
			if( $this -> set_mysql_insert('t_gn_policy',
				array
				(
					'ProductPlanId'=> $totals_premi['ProductPlanId'],
					'Premi' => $totals_premi['ProductPlanPremium'],
					'PolicyNumber'=> self::_get_polis_number($_get_insert_id),
					'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
					'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
				)
			))
			{
				self::_set_save_insured(array(
					'PremiumGroupId' => SPOUSE,
					'CustomerId' => $this -> escPost('CustomerId'),
					'PolicyId' => $this -> get_insert_id(),
					'SalutationId' => $this -> escPost('SpSalutationId'),
					'GenderId' => $this -> escPost('SpGenderId'),
					'IdentificationTypeId' => $this -> escPost('SpIdentificationTypeId'),
					'InsuredIdentificationNum' => $this -> escPost('SpIdentificationNum'),
					'RelationshipTypeId' => $this -> escPost('SpRelationshipTypeId'),
					'CreatedById' => $this -> getSession('UserId'),
					'InsuredFirstName' => $this -> escPost('SpFirstName'),
					'InsuredLastName' => $this -> escPost('SpLastName'),
					'InsuredDOB' => $this -> formatDateEng($this -> escPost('SpDOB')),
					'InsuredAge' => $this -> escPost('SpAge'),
					'InsuredCreatedTs' => date('Y-m-d H:i:s')
					));
					
				$conds = array("success" => 1, "polis" => array( self::_get_polis_number($_get_insert_id)) );
			}
		}
			
		/**   set dependent jika tidak pecah **/
		
		if( class_exists('Customer') && $this -> havepost('Dependent') && $_get_insert_id ) 
		{
			
			$_exist_group_premi = $this -> Customer -> _getExistGroupPremi( $this -> escPost('ProductId'));
			if( $_exist_group_premi )
			{
				$_argument['PremiumGroupId'] = (INT)$this -> escPost('DepGroup'); 
			}
			
			$Dependent = ( $this -> havepost('dependent') ? explode(',', $this -> escPost('dependent')) : null );
			
			if( !is_null($Dependent) AND is_array($Dependent) )
			{
				foreach( $Dependent as $k => $pos )
				{
					$_argument['PayModeId']	  = ( $this -> havepost("DepPaymode_$pos")?$this -> escPost("DepPaymode_$pos"):false ); 
					$_argument['ProductPlan'] = ( $this -> havepost("DepPlanType_$pos")?$this -> escPost("DepPlanType_$pos"):false ); 
					$_argument['start_age']	  = ( $this -> havepost("DepAge_$pos")?$this -> escPost("DepAge_$pos"):false );
					
					$totals_premi = self::_self_personal_premi($this -> escPost('ProductId'),$_argument);
					if( $this -> set_mysql_insert('t_gn_policy', 
						array 
						(
							'ProductPlanId'=> $totals_premi['ProductPlanId'],
							'Premi' => $totals_premi['ProductPlanPremium'],
							'PolicyNumber'=> self::_get_polis_number($_get_insert_id),
							'PolicyEffectiveDate'=> $this -> formatDateEng($this -> escPost("EfectiveDate")),
							'PolicySalesDate'=> $this -> formatDateEng($this -> escPost("SalesDate"))
						)
					))
					{
						self::_set_save_insured(array(
							'PremiumGroupId' => DEPEND,
							'CustomerId' => $this -> escPost('CustomerId'),
							'PolicyId' => $this -> get_insert_id(),
							'SalutationId' => $this -> escPost("DepSalutationId_$pos"),
							'GenderId' => $this -> escPost("DepGenderId_$pos"),
							'RelationshipTypeId' => $this -> escPost("DepRelationshipTypeId_$pos"),
							'CreatedById' => $this -> getSession('UserId'),
							'InsuredFirstName' => $this -> escPost("DepFirstName_$pos"),
							'InsuredLastName' => $this -> escPost("DepLastName_$pos"),
							'InsuredDOB' => $this -> formatDateEng($this -> escPost("DepDOB_$pos")),
							'InsuredAge' => $this -> escPost("DepAge_$pos"),
							'InsuredCreatedTs' => date('Y-m-d H:i:s')
							));
						$conds = array("success" => 1, "polis" => array( self::_get_polis_number($_get_insert_id) ) );
					}
				}
			}	
		}
		
	/* 
	 * @ def	: save benefiecery if have succes policy create 
	 * @ param	: benefiecery && payers data 
	 */
   	
		if( is_array($conds) )
		{
			$cons = self::_set_save_data_payer();
			$cons = self::_set_save_benefiecery();
		}
	}
 }
 else
 {
	$conds = array("success" => 2, "polis" => self::_get_singgle_polis()); // polis exist
 }
 

 echo json_encode($conds); // callback to client 
 
}

/*
 * @ def 		: AXA_Save / constructor 
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */

private function _set_save_benefiecery()
{
 $BoxBenef = ( $this -> havepost('BenefBox') ? explode(",",$this -> escPost('BenefBox')) : false );
 if(($BoxBenef!=FALSE) AND (is_array($BoxBenef)) )
 {
	foreach( $BoxBenef as $k => $pos )
	{
		$this -> set_mysql_insert('t_gn_beneficiary',
			array
			(
				'CustomerId' => $this -> escPost("CustomerId"), 
				'SalutationId' => $this -> escPost("BenefSalutationId_$pos"), 
				'RelationshipTypeId' => $this -> escPost("BenefRelationshipTypeId_$pos"),
				'BeneficiaryFirstName'=> $this -> escPost("BenefFirstName_$pos"),
				'BeneficiaryLastName' => $this -> escPost("BenefLastName_$pos"),
				'BeneficieryPercentage'=> $this -> escPost("BenefPercentage_$pos"),
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
		))){
			$_conds = true;
		}
	}
	
	return $_conds;
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
	if( !$qry -> EOF() )
	{
		if($qry -> result_singgle_value())
		{
			$_conds = array
			(
				'success'=> 1, 
				'pecah'=> $qry -> result_singgle_value()
			);
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
			$data = array
					( 
						'PayerSalutationId' 		=> $rows['SalutationId'],
						'PayerFirstName' 			=> $rows['CustomerFirstName'],
						'PayerLastName' 			=> $rows['CustomerLastName'],
						'PayerGenderId' 			=> $rows['GenderId'],
						'PayerDOB'					=> $this -> formatDateId($rows['CustomerDOB']), 
						'PayerAddressLine1'			=> $rows['CustomerAddressLine1'],
						'PayerAddressLine2'			=> $rows['CustomerAddressLine2'],
						'PayerAddressLine3'			=> $rows['CustomerAddressLine3'],
						'PayerAddressLine4'			=> $rows['CustomerAddressLine4'],
						'PayerIdentificationTypeId'	=> $rows['IdentificationTypeId'],
						'PayerIdentificationNum' 	=> $rows['CustomerIdentificationNum'],
						'PayerMobilePhoneNum'		=> $rows['CustomerMobilePhoneNum'],
						'PayerCity'					=> $rows['CustomerCity'],
						'PayerHomePhoneNum'			=> $rows['CustomerHomePhoneNum'],
						'PayerZipCode'				=> $rows['CustomerZipCode'],
						'PayerOfficePhoneNum'		=> $rows['CustomerWorkPhoneNum'],
						'PayerProvinceId'			=> $rows['ProvinceId'],
						'CreditCardTypeId'			=> $rows['CardTypeId'],
						'PayerEmail' 				=> $rows['CustomerEmail'],
						'PayerCreditCardNum'		=> ($rows['CustomerCreditCardNum'] ? $rows['CustomerCreditCardNum'] : ''),
						'PayerFaxNum'				=> '',
						'PayerCreditCardExpDate'	=> $rows['CustomerCreditCardExpDate'],
						'PayersBankId'				=> '0',
				);
		}
	}
	
	echo json_encode($data);
	
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
		$_argument['start_age']	  = ( $this -> havepost('PersonalAge')?$this -> escPost('PersonalAge'):false );
		
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
			
			if( ($_total_years!='') && ($_total_years > 0) )
			{
				$_conds = array('success' =>1, 'personal_age'=> $_total_years );
			}
		}	
	}
	
	echo json_encode($_conds);
}

/*
 * @ def 		: _get_singgle_polis 
 *
 * @ params 	: if true Policy exist else no exist then you can create polis 
 * @ return 	: boolean
 */
 
function _get_singgle_polis()
{
	$_conds = false;
	
	$ProductId  = ( $this -> havepost('ProductId') ? $this -> escPost('ProductId') : null );
	$CustomerId = ( $this -> havepost('CustomerId') ? $this -> escPost('CustomerId') : null );
	
	if( !is_null($ProductId) AND !is_null($CustomerId) )
	{
		$sql = " SELECT a.PolicyNumber FROM t_gn_policyautogen a WHERE a.ProductId='$ProductId' AND a.CustomerId = '$CustomerId'";
		$qry = $this -> query($sql);
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
 *
 * @ params 	: all request by customizde 
 * @ return 	: void 
 */
 
 // ============================================================================================================================ //
 
		// holder
		function loadPlan()
		{
			if( $this -> havepost('ProductId') )
			{
				$this -> DBForm -> jpCombo('HoldPlanType','select long',$this -> Customer -> ProductPlan($this -> escPost('ProductId')),null,"OnChange=getPremi(this);");
			}
			else{
				$this -> DBForm -> jpCombo('HoldPlanType','select long');
			}
		}
		
		function loadPay()
		{
			if( $this -> havepost('ProductId') )
			{
				$this -> DBForm -> jpCombo('HoldPayMode','select long',$this -> Customer -> Paymode($this -> escPost('ProductId'),null,"OnChange=getPremi(this);"));
			}
			else{
				$this -> DBForm -> jpCombo('HoldPayMode','select long');
			}
		}
		// spouse
		function loadPlanSpouse()
		{
			if( $this -> havepost('ProductId') )
			{
				$this -> DBForm -> jpCombo('SpPlanType','select long',$this -> Customer -> ProductPlan($this -> escPost('ProductId')),null,"OnChange=getPremi(this);");
			}
			else{
				$this -> DBForm -> jpCombo('SpPlanType','select long');
			}
		}
		
		function loadPaySpouse()
		{
			if( $this -> havepost('ProductId') )
			{
				$this -> DBForm -> jpCombo('SpPaymode','select long',$this -> Customer -> Paymode($this -> escPost('ProductId')),null,"OnChange=getPremi(this);");
			}
			else{
				$this -> DBForm -> jpCombo('SpPaymode','select long');
			}
		}
		
		function loadPlanDependent()
		{
			if( $this -> havepost('ProductId') && $this -> havepost('Depend') )
			{
				$this -> DBForm -> jpCombo('DepPlanType_'.$this -> escPost('Depend'), 'select long',$this -> Customer -> ProductPlan($this -> escPost('ProductId')),null,"OnChange=getPremi(this);");
			}
			else{
				$this -> DBForm -> jpCombo('DepPlanType_'.$this -> escPost('Depend'),'select long');
			}
		}
		
		function loadPayDependent()
		{
			if( $this -> havepost('ProductId') && $this -> havepost('Depend') )
			{
				$this -> DBForm -> jpCombo('DepPaymode_'.$this -> escPost('Depend'),'select long',$this -> Customer -> Paymode($this -> escPost('ProductId')),null,"OnChange=getPremi(this);");
			}
			else{
				$this -> DBForm -> jpCombo('DepPaymode_'.$this -> escPost('Depend'),'select long');
			}
		}
 
}

new AXA_Save();

?>
