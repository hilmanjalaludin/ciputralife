<?php

require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");
	
class PreviewPolicy extends mysql
{
 var $_tem;
 var $_url;
 var $_Wds;
 var $_CIs;
 var $_Cmp;
 var $_WUs;
 
 function PreviewPolicy()
 {
	parent::__construct();
	$this -> _url = & new application();
	$this -> _tem = & new Themes();
	$this -> _CIs = & $this -> escPost('customerid');
	$this -> _Cmp = & $this -> escPost('campaignid');
	self::index();
	
 }
  
/**
 **
 **
 **/
private function __iConstant()
{
	if( $this -> _CallReasonId==15 ) return 1;
	else if( $this -> _CallReasonId==16 ) return 0;	
	else
		return false;
}	
	
function index()
{
	$this -> _Wds = $this -> Customer -> DataPolicy( $this -> _CIs );
	$this -> _WUs = $this -> Customer -> SellerId( $this -> _CIs  );
	
	self::_W_Header();
	self::_W_Body();
	self::_W_Footer();
}

/**
 ** _W_Header ********************
 ** _W_Header ********************
 ** _W_Header ********************
 **/

function _W_Header() { 
?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<!-- start Link : css --> 
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta content="utf-8" http-equiv="encoding">
		<title><?php echo $this -> _tem -> V_WEB_TITLE; ?> :: Preview Policy </title>
		<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>gaya/other.css" />
		<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-themes-1.7.2/themes/<?php echo $this -> _tem -> V_UI_THEMES;?>/ui.all.css" />	
		<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-1.3.2.js"></script>    
		<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.7.2/ui/jquery-ui.js"></script>
		<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.7.2/external/bgiframe/jquery.bgiframe.js"></script>
		<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>js/javaclass.js"></script>
		<style>
			 #page_info_header { color:#3c3c36;background-color:#eeeeee;height:20px;padding:0px;font-size:16px;font-weight:bold;border-bottom:2px solid #dddfff;margin-bottom:4px;}
			 table td{ font-size:11px; text-align:left;}
			 table p{font-size:14px;color:#4c4c47;}
			 table td .input{ border:1px solid #a5bb89;background-color:#fbfaf5;width:120px;height:18px;font-size:11px;}
			 table td .box{ border:1px solid #a5bb89;background-color:#fbfaf5;width:50px;height:18px;font-size:11px;}
			  
			 table td .input:hover{ border:1px solid #a5bb89;background-color:#e7d795;font-size:11px;}
			 table td .select{ border:1px solid #a5bb89;background-color:#fbfaf5;font-size:11px;height:20px;}
			.header-text {text-align:right;font-weight:normal;font-size:11px;}
			.sunah{color:#4c4c47;font-size:12px;font-family:Arial;}
			.wajib{color:#4c4c47;font-size:12px;font-family:Arial;}
			 h4{background-color:#61605e;color:white;padding:2px;cursor:pointer;width:120px;}
			 h4:hover{color:white;background-color:blue;}
			.age{width:60px;}
		</style>
	</head>
	<body style="overflow:auto;background-color:#eee;background-position:center;">
<?php
}

/** { Window_Body } **************************
 ** { Window_Body } **************************
 ** { Window_Body } **************************
 **/
 
function _W_Body(){ ?>
 <table align="center">
	<tr> 
	 <td>
	<div id="print_pages" style="background-color:#FFFFFF;background-position:center;margin-left:1px;padding-bottom:25px;padding-top:25px;margin-right:1px;margin-top:0px;width:900px;border:0px solid #000;">
		<table border=0 align="center" style="border:0px solid #dddddd;">
			<tr>
				<td Style="background-color:#61605e;color:#FFFFFF;font-size:16px;font-weight:bold;height:22px;text-align:center;padding:4px;">SHOW POLICY FORM </TD>
			</tr> 
			<tr><td>
			  <div id="page_info_panel" style="margin-top:1px;">
				<table border=0 width="99%" align="center" cellpadding="4px;">
					<tr>
						<td class="header-text sunah" nowrap>Policy Number</td>
						<td nowrap><?php $this -> DBForm -> jpInput('main_cust_policy_number','input',$this -> _Wds['Policy']->result_get_value('PolicyNumber') ); ?></td>
						<td class="header-text sunah" nowrap>Input date</td>
						<td nowrap><input type="text" name="main_cust_policy_date" id="main_cust_policy_date" class="input" value="<?php echo $this -> _Wds['Policy']->result_get_value('PolicySalesDate'); ?>" disabled></td>
						<td class="header-text sunah" nowrap>Campaign Name</td>
						<td nowrap><?php $this -> DBForm ->  jpInput('main_cust_policy_campaign','input',$this -> Customer -> _CampaignName($this -> _Cmp )); ?></td>
					</tr>
					<tr>
						<td class="header-text sunah">Telemarketer</td>
						<td><input type="text" name="main_cust_policy_user" id="main_cust_policy_user" class="input" value="<?php echo $this -> _WUs -> getUsername();?>" disabled></td>
						<td class="header-text sunah" nowrap>Effective Date</td>
						<td><input type="text" name="main_cust_policy_efective" id="main_cust_policy_efective" class="input" value="<?php echo $this -> _Wds['Policy']->result_get_value('PolicyEffectiveDate'); ?>" disabled></td>
						<td class="header-text sunah">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
			</div>
	</td>
	
	</tr>
	<tr>
		<td style="background-color:#61605e;;color:#FFFFFF;text-align:left;height:24px;padding-left:4px;font-size:16px;font-weight:bold;">HOLDER</TD>
	</tr>
	<tr>
		<td>
			<div id="tabs-1" style="border:0px solid #dddddd;">
				<table width="99%" border=0>
			<tr>
				<td class="header-text sunah">Holder Id</td>
				<td><?php $this -> DBForm -> jpInput("edit_holder","box", $this -> _Wds['Holder']->result_get_value('InsuredId'),NULL,1); ?></td>
			</tr>
			<tr>
				<td class="header-text wajib">Holder Type</td>
				<td style="height:30px;"><?php $this -> DBForm -> jpCombo('cb_holder_holdertype',"input", $this -> Customer -> PremiumGroup(),'2',NULL,1); ?>
				<input type="checkbox" name="chekclist" id="chekclist" onchange="setMandatory(this.checked);" style="text-align:left;margin:0px;border;1px solid #000;" checked=true disabled=true> &nbsp; Holder = Payer </td>
				<td class="header-text wajib">First Name</td>
				<td style="height:30px;"><?php $this -> DBForm -> jpInput('frm_holder_firstname',"input",$this -> _Wds['Payers'] -> result_get_value('PayerFirstName') ); ?></td>
				<td class="header-text sunah" style="display:none;">Last Name</td>
				<td style="height:30px;"><input type="text" style="display:none;" class="input" onkeyup="isStrValue(this);" name="frm_holder_lastname" id="frm_holder_lastname" style="width:200px;" value=""></td>
			</tr>
			<tr>
				<td class="header-text wajib">ID-Type </td>
				<td style="height:30px;"><?php $this -> DBForm -> jpCombo('cb_holder_idtype',"select", $this -> Customer -> IndentificationId(), $this -> _Wds['Payers'] -> result_get_value('IdentificationTypeId') ); ?> </td>
				<td class="header-text wajib">Relation</td>
				<td style="height:30px;"><?php $this -> DBForm -> jpCombo('frm_holder_rel',"select", $this -> Customer -> RelationshipType(),$this -> _Wds['Payers'] -> result_get_value('PayerRelationshipTypeId') ); ?> </td>
				<td class="header-text wajib">DOB</td>
				<td style="height:30px;"><?php $this -> DBForm -> jpInput('frm_holder_dob','input', $this -> formatDateId($this -> _Wds['Payers']-> result_get_value('PayerDOB')),'onkeyup="Ext.Set(this.id).IsNumber();"'); ?>
										<img src="<?php echo $this -> _url ->basePath();?>gambar/calendar.gif"> 
										<?php $this -> DBForm -> jpInput('text_dob_size','box', $this -> _Wds['Payers']-> result_get_value('InsuredAge'),'onkeyup="Ext.Set(this.id).IsNumber();"'); ?> 
				</td>
			</tr>
			<tr>
				<td class="header-text wajib">ID-No</td>
				<td style="height:30px;"><?php $this -> DBForm -> jpInput('frm_holder_idno','input', $this -> _Wds['Payers']-> result_get_value('PayerIdentificationNum'),'onkeyup="Ext.Set(this.id).IsNumber();"'); ?></td>
				<td class="header-text wajib">Gender</td>
				<td style="height:30px;"><?php $this -> DBForm -> jpCombo('frm_holder_gender','input',$this -> Customer -> Gender(), $this -> _Wds['Payers']->result_get_value('GenderId') ); ?> </td>
				<td class="header-text wajib">Title</td>
				<td style="height:30px;"><?php $this -> DBForm -> jpCombo('frm_holder_title','select',$this -> Customer -> Salutation(), $this -> _Wds['Payers']->result_get_value('PayerSalutationId') ); ?></td>
			</tr>
			</table>
			</div>
			</td>
		</tr>	
	<!-- stop : tab HHolder -->
	<TR>
		<TD style="background-color:#61605e;color:#FFFFFF;text-align:left;height:24px;padding-left:4px;font-size:14px;font-weight:bold;">INSURANCE</TD>
	</TR>
	<!-- start: Insurance -->
	<tr>
		<td>
			
       <table width="99%" align="center">
			<tr>
				<td style="text-align:right;height:30px;  valign="top">
					<table>
						<tr>
							<td class="header-top"><h4>SPOUSE</h4></td>
							<td><?php $this -> DBForm -> jpCheck("cbx_ins_folow",NULL,'0',NULL,$this -> _Wds['Spouse']->result_get_value('InsuredId'),self::__iConstant());?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Insured Id</td>
							<td><?php $this -> DBForm -> jpInput("edit_number_0","input",$this -> _Wds['Spouse']->result_get_value('InsuredId'),NULL,1); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Holder Type</td>
							<td><?php $this -> DBForm -> jpCombo('cb_insurance_sp_holdertype',"select", $this -> Customer -> PremiumGroup(),3,NULL,1); ?></td>
						</tr>
						<tr>
							<td class="header-text <?php echo ($_REQUEST['callstatus']==402?"wajib":"sunah"); ?>">ID Type</td>
							<td><?php $this -> DBForm -> jpCombo('cb_insurance_sp_idtype',"select",$this -> Customer -> IndentificationId(),$this -> _Wds['Spouse']->result_get_value('IdentificationTypeId')); ?> </td>
						</tr>
						<tr>
							<td class="header-text <?php echo ($_REQUEST['callstatus']==402?"wajib":"sunah"); ?>">ID No</td>
							<td><?php $this -> DBForm -> jpInput('txt_insurance_sp_idno','input',$this -> _Wds['Spouse']->result_get_value('InsuredIdentificationNum'),'onkeyup="Ext.Set(this.id).IsNumber();"'); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Relation</td>
							<td><?php $this -> DBForm -> jpCombo('cb_insurance_sp_relation',"select", $this -> Customer -> RelationshipType(),$this -> _Wds['Spouse']->result_get_value('RelationshipTypeId')); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Title</td>
							<td><?php $this -> DBForm -> jpCombo('cb_insurance_sp_salut',"select", $this -> Customer -> Salutation(),$this -> _Wds['Spouse']->result_get_value('SalutationId')); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">First Name</td>
							<td><?php $this -> DBForm -> jpInput('txt_insurance_sp_firstname','input',$this -> _Wds['Spouse']->result_get_value('InsuredFirstName'),NULL,'onkeyup="Ext.Set(this.id).IsString();"'); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Last Name</td>
							<td><?php $this -> DBForm -> jpInput('txt_insurance_sp_lastname','input',$this -> _Wds['Spouse']->result_get_value('InsuredLastName'),'onkeyup="Ext.Set(this.id).IsString();"'); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Gender</td>
							<td><?php $this -> DBForm -> jpCombo('txt_insurance_sp_gender',"select",$this -> Customer -> Gender(),$this -> _Wds['Spouse']->result_get_value('GenderId')); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">DOB</td>
							<td><?php $this -> DBForm -> jpInput('txt_insurance_sp_dob','input',$this -> formatDateId($this -> _Wds['Spouse']->result_get_value('InsuredDOB')) ); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Age</td>
							<td><?php $this -> DBForm -> jpInput('txt_insurance_sp_age','input',$this -> _Wds['Spouse']->result_get_value('InsuredAge')); ?></td>
						</tr>
						
					</table>
				</td>
				<?php for( $s_i= 1; $s_i<=2;  $s_i++) : ?>
					<td style="height:30px;" valign="top" >
					<table>
						<tr>
							<td><h4>DEPENDENT <?php echo $s_i; ?></h4></td>
							<td><?php $this -> DBForm -> jpCheck("cbx_ins_folow",NULL,"$s_i",NULL,$this -> _Wds['Dependent']->result_get_value('InsuredId',$s_i-1), self::__iConstant());?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Insured Id</td>
							<td><?php $this -> DBForm -> jpInput("edit_number_{$s_i}","input", $this -> _Wds['Dependent']->result_get_value('InsuredId',(($s_i)-1)),NULL,1); ?></td>
						</tr>
						
						<tr>
							<td class="header-text sunah">Holder Type</td>
							<td><?php $this -> DBForm -> jpCombo("cb_insurance_dp{$s_i}_holdertype","select", $this -> Customer ->PremiumGroup(),1,NULL,1); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Relation</td>
							<td><?php $this -> DBForm -> jpCombo("cb_insurance_dp{$s_i}_rel","select", $this -> Customer ->RelationshipType(), $this -> _Wds['Dependent']->result_get_value('RelationshipTypeId',$s_i-1) ); ?>
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Title</td>
							<td><?php $this -> DBForm -> jpCombo("cb_insurance_dp{$s_i}_salut","select", $this -> Customer -> Salutation(),$this -> _Wds['Dependent']->result_get_value('SalutationId',$s_i-1) ); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">First Name</td>
							<td><?php $this -> DBForm -> jpInput("txt_insurance_dp{$s_i}_firstname","input", $this -> _Wds['Dependent']->result_get_value('InsuredFirstName',$s_i-1) );?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Last Name</td>
							<td><?php $this -> DBForm -> jpInput("txt_insurance_dp{$s_i}_lastname","input", $this -> _Wds['Dependent']->result_get_value('InsuredLastName',$s_i-1));?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Gender</td>
							<td><?php $this -> DBForm -> jpCombo("cb_insurance_dp{$s_i}_gender","select",$this -> Customer -> Gender(),$this -> _Wds['Dependent']->result_get_value('GenderId',$s_i-1)); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">DOB</td>
							<td><?php $this -> DBForm -> jpInput("txt_insurance_dp{$s_i}_dob","input", $this -> formatDateId($this -> _Wds['Dependent']->result_get_value('InsuredDOB',$s_i-1)));?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Age</td>
							<td><?php $this -> DBForm -> jpInput("txt_insurance_dp{$s_i}_age","input", $this -> _Wds['Dependent']->result_get_value('InsuredAge',$s_i-1));?></td>
						</tr>
					</table>
					</td>
				<?php endfor; ?>
			</tr>
			<tr>
			<?php for( $s_i=3; $s_i<=4;  $s_i++ ) : ?>
				<td style="height:30px;" valign="top" >
				<table>
						<tr>
							<td><h4>DEPENDENT <?php echo $s_i; ?></h4></td>
							<td><?php $this -> DBForm -> jpCheck("cbx_ins_folow",NULL,"$s_i",NULL,$this -> _Wds['Dependent']->result_get_value('InsuredId',$s_i-1), self::__iConstant());?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Insured Id</td>
							<td><?php $this -> DBForm -> jpInput("edit_number_{$s_i}","input", $this -> _Wds['Dependent']->result_get_value('InsuredId',(($s_i)-1)),NULL,1); ?></td>
						</tr>
						
						<tr>
							<td class="header-text sunah">Holder Type</td>
							<td><?php $this -> DBForm -> jpCombo("cb_insurance_dp{$s_i}_holdertype","select", $this -> Customer ->PremiumGroup(),1,NULL,1); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Relation</td>
							<td><?php $this -> DBForm -> jpCombo("cb_insurance_dp{$s_i}_rel","select", $this -> Customer ->RelationshipType(), $this -> _Wds['Dependent']->result_get_value('RelationshipTypeId',$s_i-1) ); ?>
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Title</td>
							<td><?php $this -> DBForm -> jpCombo("cb_insurance_dp{$s_i}_salut","select", $this -> Customer -> Salutation(),$this -> _Wds['Dependent']->result_get_value('SalutationId',$s_i-1) ); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">First Name</td>
							<td><?php $this -> DBForm -> jpInput("txt_insurance_dp{$s_i}_firstname","input", $this -> _Wds['Dependent']->result_get_value('InsuredFirstName',$s_i-1) );?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Last Name</td>
							<td><?php $this -> DBForm -> jpInput("txt_insurance_dp{$s_i}_lastname","input", $this -> _Wds['Dependent']->result_get_value('InsuredLastName',$s_i-1));?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Gender</td>
							<td><?php $this -> DBForm -> jpCombo("cb_insurance_dp{$s_i}_gender","select",$this -> Customer -> Gender(),$this -> _Wds['Dependent']->result_get_value('GenderId',$s_i-1)); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">DOB</td>
							<td><?php $this -> DBForm -> jpInput("txt_insurance_dp{$s_i}_dob","input", $this -> formatDateId($this -> _Wds['Dependent']->result_get_value('InsuredDOB',$s_i-1)));?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Age</td>
							<td><?php $this -> DBForm -> jpInput("txt_insurance_dp{$s_i}_age","input", $this -> _Wds['Dependent']->result_get_value('InsuredAge',$s_i-1));?></td>
						</tr>
					</table>
				 </td>
				<?php endfor;  ?>
				
			</tr>
			</table>
		</td>
	</tr>	
	<TR>
		<TD style="background-color:#61605e;color:#FFFFFF;text-align:left;height:24px;padding-left:4px;font-size:16px;font-weight:bold;">PLAN</TD>
	</TR>
	<!-- start : tab3 -->
    <tr>
		<td>
			<div id="tabs-3">
				<table width="100%" align="center" border=0>
				<tr>
					<td class="header-text wajib" valign="top">Product</td>
					<td style="height:30px;"  valign="top"><?php $this -> DBForm -> jpCombo('plan_product_id', "select", $this -> Customer -> ProductId($this ->_Cmp), $this -> _Wds['Policy']->result_get_value('ProductId'));?> </td>
					<td class="header-text wajib" valign="top">Plan</td>
					<td style="height:30px;" id="html_inner_plan" valign="top"> <?php $this -> DBForm -> jpCombo('plan_plan', "select", $this -> Customer -> ProductPlanId($this ->_Cmp), $this -> _Wds['Policy']-> result_get_value('ProductPlan'),'onchange="getPremiByPlan(this.value);"');?></td>
					<td class="header-text wajib" valign="top">Pay Mode</td>
					<td style="height:30px;"  valign="top"> <?php $this -> DBForm -> jpCombo('plan_paymode',"select", $this -> Customer -> Paymode() ,$this -> _Wds['Policy']-> result_get_value('PayModeId'),'onchange="getPremiByPlanMode(this.value);"');?></td>
				</tr>
				<tr>
					<td class="header-text wajib" valign="top">Pay Type</td>
					<td style="height:30px;" valign="top"> <?php $this -> DBForm -> jpCombo('plan_paytype', "select", $this -> Customer -> PaymentTypeId() ,$this -> _Wds['Payers']-> result_get_value('PaymentTypeId'));?></td>
					<td style="text-align:right;height:30px;">Premi ( IDR )</td>
					<td style="height:30px;" colspan="3"><?php $this -> DBForm -> jpInput('total_premi', "input", formatRupiah($this -> Customer -> TotalPremi($this ->_CIs)) );?></td>
				</tr>
				<tr>
					<td class="header-text wajib" valign="top" colspan="6"> <div id="product_benefit"> </div></td>
				</tr>
			</table>
				</div>
		</td>
	</tr>
	<!-- stop : tab3 -->
	<TR>
			<TD style="background-color:#61605e;color:#FFFFFF;text-align:left;height:24px;padding-left:4px;font-size:16px;font-weight:bold;">PAYER</TD>
		</TR>
	<!-- start : tab4 -->
	<tr>
		<td>
		 <div id="tabs-4">
			<table width="100%" align="center">	
		
		<tr>
		
			<td class="header-text wajib">Title</td>
			<td><?php $this -> DBForm -> jpCombo("payer_salutation","select",$this -> Customer -> Salutation(), $this -> _Wds['Payers'] -> result_get_value('PayerSalutationId'));?></td>
			<td class="header-text wajib" >First Name</td>
			<td><?php $this -> DBForm -> jpInput("payer_first_name","input",$this -> _Wds['Payers'] -> result_get_value('PayerFirstName'));?></td>
			<td class="header-text sunah">Last Name</td>
			<td><?php $this -> DBForm -> jpInput("payer_last_name","input",$this -> _Wds['Payers'] -> result_get_value('PayerLastName'));?></td>
		</tr>
		<tr>
			<td class="header-text wajib">Gender</td>
			<td><?php $this -> DBForm -> jpCombo("payer_gender","select",$this -> Customer -> Gender(),$this -> _Wds['Payers'] -> result_get_value('GenderId'));?></td>
			<td class="header-text wajib">DOB</td>
			<td><?php $this -> DBForm -> jpInput("payer_dob","input",$this -> formatDateId($this -> _Wds['Payers'] -> result_get_value('PayerDOB')),'onKeyup="Ext.Date(this.id).Long(\'-\');"' );?></td>
			<td class="header-text wajib">Address</td>
			<td><?php $this -> DBForm -> jpInput("payer_address1","input",$this -> _Wds['Payers'] -> result_get_value('PayerAddressLine1'));?></td>
		</tr>
		<tr>
			<td class="header-text wajib">ID - Type </td>
			<td><?php $this -> DBForm -> jpCombo("payer_holder_idtype","input",$this -> Customer -> IndentificationId(), $this -> _Wds['Payers'] -> result_get_value('IdentificationTypeId'));?></td>
			<td class="header-text wajib" >ID No</td>
			<td><?php $this -> DBForm -> jpInput("payer_idno","input",$this -> _Wds['Payers'] -> result_get_value('PayerIdentificationNum'),'onkeyup="Ext.Set(this.id).IsNumber();"');?></td>
		</tr>
		<tr>
			<td class="header-text wajib">Mobile Phone</td>
			<td><?php $this -> DBForm -> jpInput("payer_mobile_phone","input",$this -> _Wds['Payers'] -> result_get_value('PayerMobilePhoneNum'),'onkeyup="Ext.Set(this.id).IsNumber();"');?> </td>
			<td class="header-text wajib">City</td>
			<td><?php $this -> DBForm -> jpInput("payer_city","input",$this -> _Wds['Payers'] -> result_get_value('PayerCity'));?>  </td>
			<td class="header-text wajib"></td>
			<td> <?php $this -> DBForm -> jpInput("payer_address2","input",$this -> _Wds['Payers'] -> result_get_value('PayerAddressLine2'));?></td>
		</tr>	
		<tr>
			<td class="header-text wajib">Home Phone </td>
			<td><?php $this -> DBForm -> jpInput("payer_home_phone","input",$this -> _Wds['Payers'] -> result_get_value('PayerHomePhoneNum'),'onkeyup="Ext.Set(this.id).IsNumber();"');?> </td>
			<td class="header-text wajib">Zip</td>
			<td><?php $this -> DBForm -> jpInput("payer_zip_code","input",$this -> _Wds['Payers'] -> result_get_value('PayerZipCode'),'onkeyup="Ext.Set(this.id).IsNumber();"');?></td>
			<td class="header-text wajib"></td>
			<td><?php $this -> DBForm -> jpInput("payer_address3","input",$this -> _Wds['Payers'] -> result_get_value('PayerAddressLine3'));?></td>
		</tr>	
		<tr>
			<td class="header-text wajib">Office Phone </td>
			<td><?php $this -> DBForm -> jpInput("payer_office_phone", "input", $this -> _Wds['Payers'] -> result_get_value('PayerOfficePhoneNum'),'onkeyup="Ext.Set(this.id).IsNumber();"');?> </td>
			<td class="header-text wajib">Province</td>
			<td><?php $this -> DBForm -> jpCombo("payer_province", "select", $this -> Customer -> Province(),$this -> _Wds['Payers'] -> result_get_value('PayerProvinceId'));?></td>
			<td class="header-text wajib"></td>
			<td><?php $this -> DBForm -> jpInput("payer_address4", "input", $this -> _Wds['Payers'] -> result_get_value('PayerAddressLine4') );?>  </td>
		</tr>	
		<tr>
			<td class="header-text wajib" valign="top">Card Number</td>
			<td valign="top"><?php $this -> DBForm -> jpInput("payer_card_number", "input", $this -> _Wds['Payers'] -> result_get_value('PayerCreditCardNum'),'onkeyup="getNexValidationCard(this.value);" onkeyup="Ext.Set(this.id).IsNumber();"' );?><span id="error_message_html"></span></td>
			<td class="header-text sunah">Bank</td>
			<td><?php $this -> DBForm -> jpCombo("payer_bank", "select", $this -> Customer -> Bank(),$this -> _Wds['Payers'] -> result_get_value('PayersBankId'));?></td>
			<td class="header-text sunah">Fax Phone</td>
			<td><?php $this -> DBForm -> jpInput("payer_fax_number", "input", $this -> _Wds['Payers'] -> result_get_value('PayerFaxNum') );?></td>
		</tr>	
		<tr>
			<td class="header-text wajib">Expiration Date</td>
			<td><?php $this -> DBForm -> jpInput("payer_expired_date", "input", $this -> _Wds['Payers'] -> result_get_value('PayerCreditCardExpDate'),'onKeyup="Ext.Date(this.id).Sort(\'/\');"', null, null);?>(mm/yy)</td>
			<td class="header-text wajib">Card Type</td>
			<td><?php $this -> DBForm -> jpCombo("payer_card_type", "select", $this -> Customer -> CardType(),$this -> _Wds['Payers'] -> result_get_value('CreditCardTypeId'));?></td>
			<td class="header-text sunah">Email</td>
			<td><?php $this -> DBForm -> jpInput("payer_email", "input", $this -> _Wds['Payers'] -> result_get_value('PayerEmail') );?></td>
		</tr>	
	 </table>
		</div>
	</td>
	</tr>	
	
	<TR>
			<TD style="background-color:#61605e;color:#FFFFFF;text-align:left;height:24px;padding-left:4px;font-size:16px;font-weight:bold;">BENEFICIARY</TD>
	</TR>
	<tr>
		<td>	
		
<table width="99%">
	<tr>
	<?php for($s_i=1; $s_i<=3; $s_i++ ) : ?>
		<td>
			<table>
				<tr>
					<td><h4>BENEFICIARY <?php echo $s_i; ?></h4> </td>
					<td><?php $this -> DBForm -> jpCheck("benef_box",NULL, $s_i,NULL,$this -> _Wds['Beneficery'] -> result_get_value('BeneficiaryId',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah">Beneficiary Id</td>
					<td><?php $this -> DBForm -> jpInput("BeneficeryId_{$s_i}","box", $this -> _Wds['Beneficery'] -> result_get_value('BeneficiaryId',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah">Holder Type</td>
					<td><?php $this -> DBForm -> jpCombo("txt_benef{$s_i}_holdertype","select", $this -> Customer -> PremiumGroup(),1,NULL,1); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah">Relation</td>
					<td><?php $this -> DBForm -> jpCombo("txt_benef{$s_i}_rel","select",$this -> Customer -> RelationshipType(),$this -> _Wds['Beneficery'] -> result_get_value('RelationshipTypeId',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah">Title</td>
					<td><?php $this -> DBForm -> jpCombo("txt_benef{$s_i}_title","select", $this -> Customer -> Salutation(),$this -> _Wds['Beneficery'] -> result_get_value('SalutationId',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah" class="input">First Name</td>
					<td> 
						<?php $this -> DBForm -> jpInput("txt_benef{$s_i}_first","input",$this -> _Wds['Beneficery'] -> result_get_value('BeneficiaryFirstName',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah" class="input">Last Name</td>
					<td><?php $this -> DBForm -> jpInput("txt_benef{$s_i}_lastname","input",$this -> _Wds['Beneficery'] -> result_get_value('BeneficiaryLastName',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah" class="input">Percentage</td>
					<td><?php $this -> DBForm -> jpInput("txt_benef{$s_i}_persen","input",$this -> _Wds['Beneficery'] -> result_get_value('BeneficieryPercentage',$s_i-1)); ?>&nbsp;%</td>
				</tr>	
			</table>
		</td>
		<?php endfor; ?>
	</tr>
	<tr>
		<?php for($s_i=4; $s_i<=5; $s_i++ ) : ?>
		<td>
			<table>
				<tr>
					<td><h4>BENEFICIARY <?php echo $s_i; ?></h4> </td>
					<td><?php $this -> DBForm -> jpCheck("benef_box",NULL, $s_i,NULL,$this -> _Wds['Beneficery'] -> result_get_value('BeneficiaryId',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah">Beneficiary Id</td>
					<td><?php $this -> DBForm -> jpInput("BeneficeryId_{$s_i}","box", $this -> _Wds['Beneficery'] -> result_get_value('BeneficiaryId',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah">Holder Type</td>
					<td><?php $this -> DBForm -> jpCombo("txt_benef{$s_i}_holdertype","select", $this -> Customer -> PremiumGroup(),1,NULL,1); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah">Relation</td>
					<td><?php $this -> DBForm -> jpCombo("txt_benef{$s_i}_rel","select",$this -> Customer -> RelationshipType(),$this -> _Wds['Beneficery'] -> result_get_value('RelationshipTypeId',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah">Title</td>
					<td><?php $this -> DBForm -> jpCombo("txt_benef{$s_i}_title","select", $this -> Customer -> Salutation(),$this -> _Wds['Beneficery'] -> result_get_value('SalutationId',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah" class="input">First Name</td>
					<td> 
						<?php $this -> DBForm -> jpInput("txt_benef{$s_i}_first","input",$this -> _Wds['Beneficery'] -> result_get_value('BeneficiaryFirstName',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah" class="input">Last Name</td>
					<td><?php $this -> DBForm -> jpInput("txt_benef{$s_i}_lastname","input",$this -> _Wds['Beneficery'] -> result_get_value('BeneficiaryLastName',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah" class="input">Percentage</td>
					<td><?php $this -> DBForm -> jpInput("txt_benef{$s_i}_persen","input",$this -> _Wds['Beneficery'] -> result_get_value('BeneficieryPercentage',$s_i-1)); ?>&nbsp;%</td>
				</tr>	
			</table>
		</td>
		<?php endfor; ?>
	</tr>
</table>
</div>
		</td>
	</tr>
	</table>
	</div>
	</td>
	</tr>
	</table>
 <?php
 }
 
/**##################################
 **#################################
 **##################################
 **/
 
 function _W_Footer(){ ?>
	</body>
	</html>
 <?php 
}


}

new PreviewPolicy();	
?>

	