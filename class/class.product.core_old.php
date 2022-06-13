<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	
/*
 * @ Class 		Product Core
 *
 * @ Dynamic 	form with ajax 	
 * @ author 	< omens >
 * @ date 		< 2012-10-17 >
 */
 
class ProductCore extends mysql
{
 
 private $_action; 
 private $_ProductPlan;
 private $_RangeAge;
 private $_GroupPremi;
 private $_PayMode;
 private $_Gender;	
/*
 * @ def 		: __construct 
 *
 * @ param		: $_REQUEST ( array() )
 * @ return		: void()
 */	
 
function ProductCore()
{
	parent::__construct();
	
	$this -> _action		= $this -> escPost('action');
	$this -> _ProductPlan 	= (INT)$this -> escPost('ProductPlan');
	$this -> _RangeAge    	= (INT)$this -> escPost('RangeAge');
	$this -> _GroupPremi  	= ($this -> havepost('PremiGroup')?EXPLODE(",",$this -> escPost('PremiGroup')):0);
	$this -> _PayMode 	  	= ($this -> havepost('PayMode')?EXPLODE(",",$this -> escPost('PayMode')):0);
	$this -> _Gender        = $this->escPost('Statusgender');
	//$this -> errorJson = array( 'result'=> 0, 'productid' => 0, 'productname' => $prod_name, 'error_type'=> 'Failed, No SQL Syntax..!' );
}

/*
 * @ def 		: __construct 
 *
 * @ param		: $_REQUEST ( array() )
 * @ return		: void()
 */	
 
 function _ProductCore()
 {
	if( $this -> havepost('action'))
	{
		switch( $this -> _action )
		{
			case 'get_line_age'  		: self::tplLineAge(); break;
			case 'get_line_content'		: self::getContents(); break;
			case 'save_product' 		: self::SaveProductCore(); break;
			case 'get_credit_shield'	: self::getCreditShield();	break;
		}
	}	
 }
 
/*
 * @ def 		: _get_exist_plan 
 *
 * @ param		: $_REQUEST ( array() )
 * @ return		: void()
 */	
  
 function _set_insert_product( $_REQUEST, $Credit)
{
	$_conds = 0;
	
	if( (is_array($_REQUEST)) 
		AND (isset($_REQUEST['ProductId'])) AND (isset($_REQUEST['ProductName'])) 
		AND (isset($_REQUEST['ProductCores'])) AND (isset($_REQUEST['ProductType'])) )
	{
		$SQL_insert['ProductCode'] = $_REQUEST['ProductId']; 
		$SQL_insert['ProductName']  = $_REQUEST['ProductName'];
		$SQL_insert['CampaignGroupId'] = $_REQUEST['ProductCores']; 
		$SQL_insert['ProductTypeId'] = $_REQUEST['ProductType']; 
		$SQL_insert['ProductStatusFlag'] = $_REQUEST['ProductStatus']; 
		$SQL_insert['ProductBenefFlag'] = $_REQUEST['beneficiary']; 
		$SQL_insert['ProductPolicyNumPrefix'] = $Credit;
		
		if(( $SQL_insert['ProductCode']!='' ))
		{
			$this -> set_mysql_insert('t_gn_product', $SQL_insert );
			if( $this -> set_mysql_insert('t_gn_product', $SQL_insert ) ) { 
				$_conds = (INT)$this -> get_insert_id();
			}	
			else
			{
				if(strchr(mysql_error(), 'Duplicate')) 
				{
					$sql = "SELECT ProductId 
							FROM t_gn_product a 
							WHERE a.ProductCode='". $_REQUEST['ProductId'] ."'";
							
					$qry = $this -> query($sql);
					if( !$qry -> EOF() ) 
					{
						$_conds = (INT)$qry -> result_singgle_value();
					}
				}
			}
		}
	}
	
	return $_conds;
} 
 
/*
 * @ def 		: _get_exist_plan 
 *
 * @ param		: $_REQUEST ( array() )
 * @ return		: void()
 */	
 
 function _get_exist_plan( $Where = array() )
 {
	$_callback = '';
	$_where = NULL;
	
	$i = 1;	
	foreach( $Where as $keys => $value )
	{
		if( $i==1 ) { 
			$_where.= " WHERE ". $keys ."='". $value ."'"; 
		}
		else {
			$_where.= " AND ". $keys ."='". $value ."'";
		}	
		$i++;
	}
	
	if( !is_null($_where) )
	{	
		$sql = " SELECT COUNT(ProductPlanId) FROM t_gn_productplan $_where ";
		$qry = $this -> query($sql);
		if( !$qry -> EOF()) 
		{
			$_callback = (INT)$qry -> result_singgle_value();
		}
	}
	
	return $_callback;
 } 
 
/*
 * @ def 		: __construct 
 *
 * @ param		: $_REQUEST ( array() )
 * @ return		: void()
 */	
 
function SaveProductCore()
{
	$CreditShield 	= ($this -> havepost('CreditShield')?$this -> escPost('CreditShield'):null);
	$GroupPremi		= ($this -> havepost('GroupPremi')?explode(',',$this -> escPost('GroupPremi')):null);
	$PayMode		= ($this -> havepost('PayMode')?explode(',',$this -> escPost('PayMode')):null);
	$ProductCores	= ($this -> havepost('ProductCores')?$this -> escPost('ProductCores'):null);
	$ProductId		= ($this -> havepost('ProductId')?$this -> escPost('ProductId'):null);
	$ProductName	= ($this -> havepost('ProductName')?$this -> escPost('ProductName'):null);
	$ProductPlan	= ($this -> havepost('ProductPlan')?$this -> escPost('ProductPlan'):null);
	$ProductStatus	= ($this -> havepost('ProductStatus')?$this -> escPost('ProductStatus'):null);
	$RangeAge		= ($this -> havepost('RangeAge')?$this -> escPost('RangeAge'):null);
	
	
// set product cores 
	$totals    = 0;
	$insert_id = self::_set_insert_product($_REQUEST, $CreditShield);
	$_success  = array('success'=>0);
	
	if( $insert_id )
	{
		if( ((INT)$CreditShield!=1) &&(isset($CreditShield))) 
		{
			if( is_array($PayMode) && (!is_null($PayMode)) )
			{
				foreach($PayMode as $_KeyPayId => $VPayMode )
				{
				   /* 
					* @ def 	: if group is array && not null 
					*/
					 
					if( !is_null( $GroupPremi) && is_array($GroupPremi) )
					{
						foreach( $GroupPremi as $_KeyGroupId => $VGroupId )
						{
							$_count = (INT)$RangeAge;
							$_sizes = (INT)$ProductPlan;
							
							$_postz = 1;
							for( $start=0; $start<$_count; $start++)
							{
								for( $plan =1; (INT)$plan<=$_sizes; $plan++ ) 
								{
									$PayModeId = $VPayMode;
									$PremiumGroupId = $VGroupId;
									$ProductPlan = $plan;
									$ProductPlanName = 'PLAN '.$plan;
									$ProductPlanStatusFlag = (INT)$ProductStatus;
									$ProductPlanAgeStart = $this -> escPost('start_age_'. $VPayMode .'_'. $VGroupId .'_'. $_postz );
									$ProductPlanAgeEnd = $this -> escPost('end_age_'. $VPayMode .'_'. $VGroupId .'_'. $_postz );
									$ProductPlanPremium = $this -> escPost('plan_premi_'. $VPayMode .'_'. $VGroupId .'_'. $_postz .'_'. $plan);
									
								/** paymode can't empty ***/
									
									if( ($PayModeId!='') && ($PayModeId!=0) ) 
									{
										$_SQL['ProductId']= $insert_id;
										$_SQL['PayModeId'] = $PayModeId; 
										$_SQL['PremiumGroupId'] = $PremiumGroupId;
										$_SQL['ProductPlan'] = $ProductPlan;
										$_SQL['ProductPlanName'] = $ProductPlanName;
										$_SQL['ProductPlanAgeStart'] = $ProductPlanAgeStart;
										$_SQL['ProductPlanAgeEnd'] = $ProductPlanAgeEnd;
										$_SQL['ProductPlanPremium'] = $ProductPlanPremium; 
										$_SQL['ProductPlanStatusFlag'] = $ProductPlanStatusFlag;
										if( ( self::_get_exist_plan($_SQL)!=TRUE) )
										{
											if( $this -> set_mysql_insert('t_gn_productplan',$_SQL)) 
												$totals++;
										}
										else{
											$totals++;
										}	
									}	
								}
								
								$_postz++;
							}
						}
					}
					else
					{
					  /* 
					   * @ def 	: if group is array && not null 
					   */
					   
						$_count = (INT)$RangeAge;
						$_sizes = (INT)$ProductPlan;
						
						$_postz2 = 1;
						for( $start=0; $start<$_count; $start++)
						{
							for( $plan=1;  $plan<=$_sizes; $plan++ ) 
							{
								$PayModeId = $VPayMode;
								$ProductPlan = $plan;
								$ProductPlanName = 'PLAN '.$plan;
								$ProductPlanStatusFlag = (INT)$ProductStatus;
								$ProductPlanAgeStart = $this -> escPost('start_age_'. $VPayMode .'_'. $_postz2 );
								$ProductPlanAgeEnd = $this -> escPost('end_age_'. $VPayMode .'_'. $_postz2 );
								$ProductPlanPremium = $this -> escPost('plan_premi_'. $VPayMode .'_'. $_postz2 .'_'. $plan);
									
								
								/** paymode can't empty ***/
									
								if( ($PayModeId!='') && ($PayModeId!=0) ) 
								{
									$_SQL['ProductId']= $insert_id;
									$_SQL['PayModeId'] = $PayModeId; 
									$_SQL['ProductPlan'] = $ProductPlan;
									$_SQL['ProductPlanName'] = $ProductPlanName;
									$_SQL['ProductPlanAgeStart'] = $ProductPlanAgeStart;
									$_SQL['ProductPlanAgeEnd'] = $ProductPlanAgeEnd;
									$_SQL['ProductPlanPremium'] = $ProductPlanPremium; 
									$_SQL['ProductPlanStatusFlag'] = $ProductPlanStatusFlag;
										
									if( ( self::_get_exist_plan($_SQL)!=TRUE) )
									{
										if( $this -> set_mysql_insert('t_gn_productplan',$_SQL)) 
											$totals++;
									}
									else{
										$totals++;
									}	
								}	
							}
							$_postz2++;
						}
					}
					// stop level 2
				}	
			}
		}
	}
	
	if( $totals > 0) {
		$_success = array('success'=>1);
	}	
		
	echo json_encode($_success);
	
}
		
/*
 * @ def 		: getCreditShield 
 *
 * @ param		: $_REQUEST ( array() )
 * @ return		: void()
 */	
 
function getCreditShield()
{
	if( $this -> havepost('product_type'))
	{
		$sql = " select a.ProductFlags from t_lk_producttype a where a.ProductTypeId='".$this -> escPost('product_type')."' ";
		$qry = $this -> query($sql);
		if( $qry -> result_singgle_value() > 0 ) $array_result = array('result'=> 1);
		else
		{
			$array_result = array('result'=> 0);
		}
	}
	else
	{
		$array_result = array('result'=> 1);
	}
  
  echo json_encode($array_result);
}

/*
 * @ def 		: getPremiumGroup 
 *
 * @ param		: $_REQUEST ( array() )
 * @ return		: void()
 */	
 
 private function getPremiumGroup($group='')
 {
	$data = array();
	$sql = "SELECT a.PremiumGroupId, a.PremiumGroupDesc  from t_lk_premiumgroup a";
	$qry = $this -> query($sql);
	foreach( $qry -> result_assoc() as $rows )
	{
		$data[$rows['PremiumGroupId']] = $rows['PremiumGroupDesc'];
	}
	
	return $data;
}

/*
 * @ def 		: getPaymode 
 *
 * @ param		: $_REQUEST ( array() )
 * @ return		: void()
 */		
 
private function getPaymode()
{
	$data = array();
	$sql = " select a.PayModeId, a.PayMode from t_lk_paymode a ";
	$qry = $this ->query($sql);
	foreach( $qry -> result_assoc() as $rows ){
		$data[$rows['PayModeId']] = $rows['PayMode'];
	}
	
	return $data;
}

/*
 * @ def 		: getPaymode 
 *
 * @ param		: $_REQUEST ( array() )
 * @ return		: void()
 */		
 private function setCss()
 {
	?>
			<!-- start :css -->
			<style>
				table .input_age { height:18px;background:url('../gambar/input_bg.png');width:50px; border:1px solid red;font-size:11px;}
				table .input_premi { height:18px;background:url('../gambar/input_bg.png');width:80px; border:1px solid red;font-size:11px;}	
				table .input_group { height:18px;background:url('../gambar/input_bg.png');width:30px; border:1px solid red;font-size:11px;}
				table .value_group { text-align:center;}
				table h4{ background-color:#eeeeee;padding-left:4px;font-size:14px;}
				table .th{ font-size:12px; background-color:#eeeeee;}
			</style>
			<!-- stop :css -->
			
		<?php
		}


/*
 * @ package		: Product Cores 
 *
 * @ params			: Product Plan, Premi Group, Range Age,
 * @ plan			: INT,
 * @ premi			: Array()
 * @ paymode		: Array() 
 */
 
 
function getContents()
{
	self::setCss(); 
	$PayMode = self::getPaymode();
	$GroupPremi = self::getPremiumGroup();
 ?>
 
 <div class="box-shadow"  style="background-color:white;width:'99'%;">
 <fieldset style="border:1px solid green;">
 <legend class="icon-application" >&nbsp;&nbsp;&nbsp;Product Premi </legend>
	<div>
	<?php foreach( $this -> _PayMode as $PayModeId => $PayModeValue ){ ?>
		<fieldset style="border:1px solid green;">
			<legend class="icon-menulist" >&nbsp;&nbsp;&nbsp;<b><?php echo $PayMode[$PayModeValue]; ?></b></legend> 
			
			<!-- content with group premi -->
			<?php if( count($this -> _GroupPremi)>0 && is_array($this -> _GroupPremi) )  { ?>
			<div>
				<?php foreach( $this -> _GroupPremi as $_GroupId => $_GroupValue ) {?>
				<fieldset style="border:1px solid green;">
				<legend class="icon-customers" style="color:red;font-weight:bold;">&nbsp;&nbsp;&nbsp;<?php echo  $GroupPremi[$_GroupValue]; ?></legend>
					<table cellpadding="4px">
						<tr>
							<th style="background-color:#0099FF;color:#FFFFFF;"> AGE BAND </th>
							<?php for( $header=1; $header <= $this -> _ProductPlan; $header++) { ?>
								<th style="font-weight:bold;background-color:#0099FF;color:#FFFFFF;">PLAN <?php echo $header; ?></th>
							<?php } ?>
						</tr>
						<?php for( $y=1; $y <= $this -> _RangeAge; $y++){  ?> <!-- start counter of age --> 
						<tr>
							<td>
								<?php $this -> DBForm -> jpInput("start_age_{$PayModeValue}_{$_GroupValue}_{$y}",'input_age box'); ?> - 
								<?php $this -> DBForm -> jpInput("end_age_{$PayModeValue}_{$_GroupValue}_{$y}",'input_age box'); ?>
							</td>	
							<?php for( $plan =1; $plan <= $this -> _ProductPlan; $plan++){ ?> <!-- start counter of plan  --> 
							<td><?php $this -> DBForm -> jpInput("plan_premi_{$PayModeValue}_{$_GroupValue}_{$y}_{$plan}", "input_premi box"); ?> </td>
							<?php } ?>
						</tr>
						<?php }  ?>		
					</table>
				</fieldset>
				<br>
				<?php } ?>
			</div>
			<? } else { ?>
			<div>
				<table cellpadding="4px">
				<!-- header -->
					<tr>
						<th style="background-color:#0099FF;color:#FFFFFF;">AGE BAND </th>
						<?php for( $header=1; $header <= $this -> _ProductPlan; $header++) { ?>
						<th style="background-color:#0099FF;color:#FFFFFF;">PLAN <?php echo $header; ?></th>
						<?php } ?>
					</tr>
						<?php for( $y=1; $y <= $this -> _RangeAge; $y++){  ?> <!-- start counter of age --> 
					<tr>
						<td>
							<?php $this -> DBForm -> jpInput("start_age_{$PayModeValue}_{$y}",'input_premi box'); ?> - 
							<?php $this -> DBForm -> jpInput("end_age_{$PayModeValue}_{$y}",'input_premi box'); ?>
						</td>								
							<?php for( $plan =1; $plan <= $this -> _ProductPlan; $plan++){ ?> <!-- start counter of plan  --> 
								<td><?php $this -> DBForm -> jpInput("plan_premi_{$PayModeValue}_{$y}_{$plan}", "input_premi box"); ?> </td>
							<?php } ?>
					</tr>
					<?php }  ?>		
				</table>
			</div>
			<?php }?>
		</fieldset><br>
	<?php } ?>
	</div>
 </fieldset>
 </div>
 <?php
}

/*
 * @ package		: Product Cores 
 *
 * @ params			: Product Plan, Premi Group, Range Age,
 * @ plan			: INT,
 * @ premi			: Array()
 * @ paymode		: Array() 
 */
 
 		
public function tplLineAge() { 
	self::setCss(); 
?>
 <div class="box-shadow" style="background-color:white;width:'99'%;">	
 <fieldset style="border:1px solid green;">
	<legend class="icon-application">&nbsp;&nbsp;&nbsp;Range Age </legend>
	<table align="center" width="99%" cellspacing=0 cellpadding=0>
		<tr> 
			<?php for( $i=1; $i<= $this -> _RangeAge; $i++){ ?> 
				<th style="border-left:1px solid #ffffff;padding-left:2px;padding-right:2px;">Range Age <?php echo $i; ?> </th> 
			<?php } ?> 
		</tr>
		<tr> 
			<?php for( $i=1; $i<= $this -> _RangeAge; $i++){?> 
			<td style="text-align:center;height:23px;border-right:1px solid #dddddd;">
				<table width="100%" align="center" cellspacing=0 cellpadding=0>
					<tr> 
						<td style="text-align:center;font-weight:bold;color:white;height:23px;background-color:#0099FF;">Min. Age</td>
						<td style="text-align:center;font-weight:bold;color:white;height:23px;background-color:#0099FF;">Max. Age</td>
					</tr>	
				</table>
			</td> 
			<?php } ?> 
		</tr>
		<tr> 
			<?php for( $i=1; $i<= $this -> _RangeAge; $i++) { ?> 
			<td style="text-align:center;height:23px;border-right:1px solid #dddddd;">
				<table width="100%" align="center">
					<tr> 
						<td align="center"> <?php $this -> DBForm -> jpInput("start_age_{$i}","input_age",null,"onkeyup=setInsert('start',this);"); ?></td>
						<td align="center">&nbsp;-&nbsp;</td>
						<td align="center"><?php $this -> DBForm -> jpInput("end_age_{$i}","input_age",null,"onkeyup=setInsert('end',this);"); ?></td>
					</tr>	
				</table>
			</td> 
			<?php } ?> 
		</tr>
	</table>

</fieldset>
</div>
<? }
	
} 
	
$Cores = new ProductCore();
$Cores -> _ProductCore();

// END OF FILE 
?>