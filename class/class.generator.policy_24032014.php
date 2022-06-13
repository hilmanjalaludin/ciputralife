<?php

/* @ def 	: class Policy
 *
 * @ param  : generator policy parameter
 * @ author : razaki team 
 */ 
 
class Polis extends mysql
{

/* @ def 	: private static 
 * 
 * @ param	: INT
 * @ aksss	: ProductId 
 */
 
private static $ProductId;

/* @ def 	: private static 
 * 
 * @ param	: INT
 * @ aksss	: PolicyId 
 */

private static $PolicyId;

/* @ def 	: private static 
 * 
 * @ param	: INT
 * @ aksss	: Sufix 
 */

private static $Sufix;

/* @ def 	: class Policy
 *
 * @ param  : generator policy parameter
 * @ author : razaki team 
 */ 
 
private static $instance = null;

/* @ def 	: instance
 *
 * @ param  : generator policy parameter
 * @ author : razaki team 
 */ 
 	
function Polis()
{
	parent::__construct();
    // & run aksosor data 
}


/* @ def 	: class Policy
 *
 * @ param  : generator policy parameter
 * @ author : razaki team 
 */ 
 
 public static function &get_instance()
 {
	if(is_null(self::$instance)) {
		self::$instance = new self();
	}
	
	return self::$instance;
 }
 
/* @ def 	: class Policy
 *
 * @ param  : generator policy parameter
 * @ author : razaki team 
 */ 
 
function _set_polis_number($ProductId = 0, $PolicyId = 0, $_sufix='S' ) 
 {
	if(($ProductId!=0) && ($PolicyId!=0) )
	{
		self::$ProductId = (INT)$ProductId;
		self::$PolicyId  = (INT)$PolicyId;
		self::$Sufix = $_sufix;
	}
 }



/* @ def 	: getPolicyId()
 *
 * @ param  : generator policy parameter
 * @ author : razaki team 
 */ 
 
private function getPolicyId()
 {
	$_conds = null;
	
	if( self::$PolicyId ){
		$_conds = self::$PolicyId;
	}
	
	return $_conds;
}
	

/* @ def 	: getProductId()
 *
 * @ param  : generator policy parameter
 * @ author : razaki team 
 */ 
 
private function getProductId()
{
	if( self::$ProductId ) {
		return self::$ProductId;
	}
	else
		return null;
}

/* @ def 	: getProductId()
 *
 * @ param  : generator policy parameter
 * @ author : razaki team 
 */ 

private function get_policy_prefix() 
{
  $ProductId = self::getProductId();
  $_conds = null;
  
  if( !is_null($ProductId) )
  {
	$sql =" SELECT a.ProductId, a.PrefixChar, a.PrefixLength 
			FROM t_gn_productprefixnumber a 
			INNER JOIN t_gn_product b on a.ProductId=b.ProductId 
			LEFT JOIN t_gn_campaignproduct c on a.ProductId=c.ProductId
			WHERE a.PrefixFlagStatus=1
			AND a.ProductId='$ProductId' ";
				
	$qry = $this -> query($sql);
	foreach( $qry -> result_assoc() as $rows )
	{
		$_conds[$rows['ProductId']]['prefix_chars']  = $rows['PrefixChar'];
		$_conds[$rows['ProductId']]['prefix_length'] = $rows['PrefixLength'];
	}
	return $_conds;
 }
}
	
	
/* @ def 	: getProductId()
 *
 * @ param  : generator policy parameter
 * @ author : razaki team 
 */ 
 
private function NumberChars()
{

 $_conds = false;
 $policy_code = self::get_policy_prefix();

 if( is_array($policy_code) && !is_null($policy_code) )
 {
	$result_code = substr( $policy_code[self::getProductId()]['prefix_chars'],0,strlen( $policy_code[self::getProductId()]['prefix_chars'] ) - ( strlen( self::getPolicyId() )+( self::$Sufix ? strlen(self::$Sufix) : 0 )));
	$result_code.= ( self::getPolicyId() ? self::getPolicyId() : '' ).( self::$Sufix ? self::$Sufix : '' );
	if( strlen( $result_code ) == (INT)$policy_code[self::getProductId()]['prefix_length'] )
	{
		$_conds = $result_code;
	}
 }
 
 return $_conds;
	
}
	
/* @ def 	: getProductId()
 *
 * @ param  : generator policy parameter
 * @ author : razaki team 
 */ 
 
public function _get_polis_number()
{
	$_conds = null;
	$_policy_chars = self::NumberChars();
	if( $_policy_chars!=FALSE)
	{
		$_conds = self::NumberChars();
	}
	
	return $_conds;
}

/* @ def 	: getProductId()
 *
 * @ param  : generator policy parameter
 * @ author : razaki team 
 */ 
 
}

?>