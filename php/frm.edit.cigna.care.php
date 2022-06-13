<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");
require(dirname(__FILE__)."/../class/class.application.php");

/**
 ** Window_Care
 **/
 
class WindowCignaCare extends mysql
{

 var $_url;
 var $_tem;
 var $_WData;
 var $_WUser;
 var $_CustomerId;
 var $_CamapaignId;
 var $_CallReasonId;
 
 
/**
 **
 **/
 
 public function WindowCignaCare()
	{
		parent::__construct();
		
		$this -> _url =& new application();
		$this -> _tem =& new Themes();
		$this -> _CustomerId =& $this -> escPost('customerid');
		$this -> _CampaignId =& $this -> escPost('campaignid');
		$this -> _CallReasonId =& $this -> escPost('callstatus');
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

/**
 **
 **/
 
private function index()
{
	$this -> _WData = $this -> Customer -> DataPolicy( $this -> _CustomerId );
	$this -> _WUser = $this -> Customer -> SellerId( $this -> _CustomerId );
	
	if( is_array( $this -> _WData ) )
	 {	
		self::__Window_header();
		self::__Window_Script();
		self::__Window_Body();
		self::__Window_Holder();
		self::__Window_Insured();
		self::__Window_Plan();
		self::__Window_Payers();
		self::__Window_Benefit();
		self::__Window_Footer();
	 }
}

function  __Window_Holder(){
	?>
	
	<!-- start : tab HHolder -->
	
	<div id="tabs" style="margin-top:10px;margin-left:6px;margin-right:6px;">
		<ul>
			<li><a href="#tabs-1"> HOLDER</a></li>
			<li><a href="#tabs-2"> INSURED</a></li>
			<li><a href="#tabs-3"> PLAN</a></li>
			<li><a href="#tabs-4"> PAYER AND ADDRESS INFO</a></li>
			<li><a href="#tabs-5"> BENEFICIARY</a></li>
		</ul>
		
    <div id="tabs-1">
		<form name="frm_tabs1">
			<table width="99%" border=0>
			<tr>
				<td class="header-text sunah">Holder Id</td>
				<td><?php $this -> DBForm -> jpInput("edit_holder","input_box", $this -> _WData['Holder']->result_get_value('InsuredId'),NULL,1); ?></td>
			</tr>
			<tr>
				<td class="header-text wajib">Holder Type</td>
				<td style="height:30px;"><?php $this -> DBForm -> jpCombo('cb_holder_holdertype',NULL, $this -> Customer -> PremiumGroup(),'2',NULL,1); ?>
				<input type="checkbox" name="chekclist" id="chekclist" onchange="setMandatory(this.checked);" style="text-align:left;margin:0px;border;1px solid #000;" checked=true disabled=true> &nbsp; Holder = Payer </td>
				<td class="header-text wajib">First Name</td>
				<td style="height:30px;"><?php $this -> DBForm -> jpInput('frm_holder_firstname',"input",$this -> _WData['Payers'] -> result_get_value('PayerFirstName') ); ?></td>
				<td class="header-text sunah" style="display:none;">Last Name</td>
				<td style="height:30px;"><input type="text" style="display:none;" class="input" onkeyup="isStrValue(this);" name="frm_holder_lastname" id="frm_holder_lastname" style="width:200px;" value=""></td>
			</tr>
			<tr>
				<td class="header-text wajib">ID-Type</td>
				<td style="height:30px;"><?php $this -> DBForm -> jpCombo('cb_holder_idtype',NULL, $this -> Customer -> IndentificationId(), $this -> _WData['Payers'] -> result_get_value('IdentificationTypeId') ); ?> </td>
				<td class="header-text wajib">Relation</td>
				<td style="height:30px;"><?php $this -> DBForm -> jpCombo('frm_holder_rel',NULL, $this -> Customer -> RelationshipType(),$this -> _WData['Payers'] -> result_get_value('PayerRelationshipTypeId') ); ?> </td>
				<td class="header-text wajib">DOB</td>
				<td style="height:30px;"><?php $this -> DBForm -> jpInput('frm_holder_dob','input', $this -> formatDateId($this -> _WData['Payers']-> result_get_value('PayerDOB')),'onkeyup="Ext.Set(this.id).IsNumber();"'); ?>
										<img src="<?php echo $this -> _url ->basePath();?>gambar/calendar.gif"> 
										<?php $this -> DBForm -> jpInput('text_dob_size','input_box', $this -> _WData['Payers']-> result_get_value('InsuredAge'),'onkeyup="Ext.Set(this.id).IsNumber();"'); ?> 
				</td>
			</tr>
			<tr>
				<td class="header-text wajib">ID-No</td>
				<td style="height:30px;"><?php $this -> DBForm -> jpInput('frm_holder_idno','input', $this -> _WData['Payers']-> result_get_value('PayerIdentificationNum'),'onkeyup="Ext.Set(this.id).IsNumber();"'); ?></td>
				<td class="header-text wajib">Gender</td>
				<td style="height:30px;"><?php $this -> DBForm -> jpCombo('frm_holder_gender',NULL,$this -> Customer -> Gender(), $this -> _WData['Payers']->result_get_value('GenderId') ); ?> </td>
				<td class="header-text wajib">Title</td>
				<td style="height:30px;"><?php $this -> DBForm -> jpCombo('frm_holder_title',NULL,$this -> Customer -> Salutation(), $this -> _WData['Payers']->result_get_value('PayerSalutationId') ); ?></td>
			</tr>
			</table>
		</form>	
	</div>
	<!-- stop : tab HHolder -->
<?php }

/**
 **
 **
 **/
 
 private function __Window_Body(){ ?>
	<body onload="setMandatory();">
	<!-- stop open info if was creted before ** -->
	  <div id="page_info_header" style="margin:0;margin-top:-2px;"><center>DATA INFORMATION</center></div>
	  <div id="page_info_panel" style="margin-top:1px;">
		<table border=0 width="99%" align="center" cellpadding="2px;">
			<tr>
				<td class="header-text sunah">Policy Number</td>
				<td><span id="policy_number_html"><?php $this -> DBForm -> jpInput('main_cust_policy_number','input', $this -> _WData['Policy'] -> result_get_value('PolicyNumber'),NULL,1);?></span></td>
				<td class="header-text sunah">Input date</td>
				<td><?php $this -> DBForm -> jpInput('main_cust_policy_date','input',$this -> _WData['Policy'] -> result_get_value('PolicySalesDate'),NULL,1);?> </td>
				<td class="header-text sunah">Campaign Name</td>
				<td><?php $this -> DBForm -> jpInput('main_cust_policy_campaign','input',$this->Customer -> _CampaignName($this -> _CampaignId),NULL,1);?></td>
			</tr>
			<tr>
				<td class="header-text sunah">Telemarketer</td>
				<td><?php $this -> DBForm -> jpInput('main_cust_policy_user','input',$this -> _WUser -> getUsername(),NULL,1);?></td>
				<td class="header-text sunah">Effective Date</td>
				<td> <?php $this -> DBForm -> jpInput('main_cust_policy_efective','input',$this -> _WData['Policy'] -> result_get_value('PolicyEffectiveDate'),NULL,1);?></td>
				<td class="header-text sunah">&nbsp;</td>
				<td><span id="loading_html" style="color:red;font-size:12px;"></span></td>
			</tr>
		</table>
	</div>
	
	<?php
 }
 
 
 function __Window_Insured()
  {  ?>
	
	<!-- start: Insurance -->
	<div id="tabs-2" style="height:430px;overflow:auto;">
	<form name="frm_tabs2">	
       <table width="99%" align="center" style="border:1px dotted #dddddd;">
			<tr>
				<td style="text-align:right;height:30px;border:1px dotted #dddddd;"  valign="top">
					<table>
						<tr>
							<td class="header-top"><h4>SPOUSE</h4></td>
							<td><?php $this -> DBForm -> jpCheck("cbx_ins_folow",NULL,'0',NULL,$this -> _WData['Spouse']->result_get_value('InsuredId'), self::__iConstant());?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Insured Id</td>
							<td><?php $this -> DBForm -> jpInput("edit_number_0","input_box",$this -> _WData['Spouse']->result_get_value('InsuredId'),NULL,1); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Holder Type</td>
							<td><?php $this -> DBForm -> jpCombo('cb_insurance_sp_holdertype',NULL, $this -> Customer -> PremiumGroup(),3,NULL,1); ?></td>
						</tr>
						<tr>
							<td class="header-text <?php echo ($_REQUEST['callstatus']==402?"wajib":"sunah"); ?>">ID Type</td>
							<td><?php $this -> DBForm -> jpCombo('cb_insurance_sp_idtype',NULL,$this -> Customer -> IndentificationId(),$this -> _WData['Spouse']->result_get_value('IdentificationTypeId')); ?> </td>
						</tr>
						<tr>
							<td class="header-text <?php echo ($_REQUEST['callstatus']==402?"wajib":"sunah"); ?>">ID No</td>
							<td><?php $this -> DBForm -> jpInput('txt_insurance_sp_idno','input',$this -> _WData['Spouse']->result_get_value('InsuredIdentificationNum'),'onkeyup="Ext.Set(this.id).IsNumber();"'); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Relation</td>
							<td><?php $this -> DBForm -> jpCombo('cb_insurance_sp_relation',NULL, $this -> Customer -> RelationshipType(),$this -> _WData['Spouse']->result_get_value('RelationshipTypeId')); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Title</td>
							<td><?php $this -> DBForm -> jpCombo('cb_insurance_sp_salut',NULL, $this -> Customer -> Salutation(),$this -> _WData['Spouse']->result_get_value('SalutationId')); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">First Name</td>
							<td><?php $this -> DBForm -> jpInput('txt_insurance_sp_firstname','input',$this -> _WData['Spouse']->result_get_value('InsuredFirstName'),NULL,'onkeyup="Ext.Set(this.id).IsString();"'); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Last Name</td>
							<td><?php $this -> DBForm -> jpInput('txt_insurance_sp_lastname','input',$this -> _WData['Spouse']->result_get_value('InsuredLastName'),'onkeyup="Ext.Set(this.id).IsString();"'); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Gender</td>
							<td><?php $this -> DBForm -> jpCombo('txt_insurance_sp_gender',NULL,$this -> Customer -> Gender(),$this -> _WData['Spouse']->result_get_value('GenderId')); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">DOB</td>
							<td><?php $this -> DBForm -> jpInput('txt_insurance_sp_dob','input',$this -> formatDateId($this -> _WData['Spouse']->result_get_value('InsuredDOB')) ); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Age</td>
							<td><?php $this -> DBForm -> jpInput('txt_insurance_sp_age','input',$this -> _WData['Spouse']->result_get_value('InsuredAge')); ?></td>
						</tr>
						
					</table>
				</td>
				<?php for( $s_i= 1; $s_i<=2;  $s_i++) : ?>
					<td style="height:30px;border:1px dotted #dddddd;" valign="top" >
					<table>
						<tr>
							<td><h4>DEPENDENT <?php echo $s_i; ?></h4></td>
							<td><?php $this -> DBForm -> jpCheck("cbx_ins_folow",NULL,"$s_i",NULL,$this -> _WData['Dependent']->result_get_value('InsuredId',$s_i-1),self::__iConstant());?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Insured Id</td>
							<td><?php $this -> DBForm -> jpInput("edit_number_{$s_i}","input_box", $this -> _WData['Dependent']->result_get_value('InsuredId',(($s_i)-1)),NULL,1); ?></td>
						</tr>
						
						<tr>
							<td class="header-text sunah">Holder Type</td>
							<td><?php $this -> DBForm -> jpCombo("cb_insurance_dp{$s_i}_holdertype",NULL, $this -> Customer ->PremiumGroup(),1,NULL,1); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Relation</td>
							<td><?php $this -> DBForm -> jpCombo("cb_insurance_dp{$s_i}_rel",NULL, $this -> Customer ->RelationshipType(), $this -> _WData['Dependent']->result_get_value('RelationshipTypeId',$s_i-1) ); ?>
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Title</td>
							<td><?php $this -> DBForm -> jpCombo("cb_insurance_dp{$s_i}_salut",NULL, $this -> Customer -> Salutation(),$this -> _WData['Dependent']->result_get_value('SalutationId',$s_i-1) ); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">First Name</td>
							<td><?php $this -> DBForm -> jpInput("txt_insurance_dp{$s_i}_firstname","input", $this -> _WData['Dependent']->result_get_value('InsuredFirstName',$s_i-1) );?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Last Name</td>
							<td><?php $this -> DBForm -> jpInput("txt_insurance_dp{$s_i}_lastname","input", $this -> _WData['Dependent']->result_get_value('InsuredLastName',$s_i-1));?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Gender</td>
							<td><?php $this -> DBForm -> jpCombo("cb_insurance_dp{$s_i}_gender","input",$this -> Customer -> Gender(),$this -> _WData['Dependent']->result_get_value('GenderId',$s_i-1)); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">DOB</td>
							<td><?php $this -> DBForm -> jpInput("txt_insurance_dp{$s_i}_dob","input", $this -> formatDateId($this -> _WData['Dependent']->result_get_value('InsuredDOB',$s_i-1)));?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Age</td>
							<td><?php $this -> DBForm -> jpInput("txt_insurance_dp{$s_i}_age","input", $this -> _WData['Dependent']->result_get_value('InsuredAge',$s_i-1));?></td>
						</tr>
					</table>
					</td>
				<?php endfor; ?>
			</tr>
			<tr>
			<?php for( $s_i=3; $s_i<=4;  $s_i++ ) : ?>
				<td style="height:30px;border:1px dotted #dddddd;" valign="top" >
				<table>
						<tr>
							<td><h4>DEPENDENT <?php echo $s_i; ?></h4></td>
							<td><?php $this -> DBForm -> jpCheck("cbx_ins_folow",NULL,"$s_i",NULL,$this -> _WData['Dependent']->result_get_value('InsuredId',$s_i-1), self::__iConstant());?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Insured Id</td>
							<td><?php $this -> DBForm -> jpInput("edit_number_{$s_i}","input_box", $this -> _WData['Dependent']->result_get_value('InsuredId',(($s_i)-1)),NULL,1); ?></td>
						</tr>
						
						<tr>
							<td class="header-text sunah">Holder Type</td>
							<td><?php $this -> DBForm -> jpCombo("cb_insurance_dp{$s_i}_holdertype",NULL, $this -> Customer ->PremiumGroup(),1,NULL,1); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Relation</td>
							<td><?php $this -> DBForm -> jpCombo("cb_insurance_dp{$s_i}_rel",NULL, $this -> Customer ->RelationshipType(), $this -> _WData['Dependent']->result_get_value('RelationshipTypeId',$s_i-1) ); ?>
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Title</td>
							<td><?php $this -> DBForm -> jpCombo("cb_insurance_dp{$s_i}_salut",NULL, $this -> Customer -> Salutation(),$this -> _WData['Dependent']->result_get_value('SalutationId',$s_i-1) ); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">First Name</td>
							<td><?php $this -> DBForm -> jpInput("txt_insurance_dp{$s_i}_firstname","input", $this -> _WData['Dependent']->result_get_value('InsuredFirstName',$s_i-1) );?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Last Name</td>
							<td><?php $this -> DBForm -> jpInput("txt_insurance_dp{$s_i}_lastname","input", $this -> _WData['Dependent']->result_get_value('InsuredLastName',$s_i-1));?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Gender</td>
							<td><?php $this -> DBForm -> jpCombo("cb_insurance_dp{$s_i}_gender","input",$this -> Customer -> Gender(),$this -> _WData['Dependent']->result_get_value('GenderId',$s_i-1)); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">DOB</td>
							<td><?php $this -> DBForm -> jpInput("txt_insurance_dp{$s_i}_dob","input", $this -> formatDateId($this -> _WData['Dependent']->result_get_value('InsuredDOB',$s_i-1)));?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Age</td>
							<td><?php $this -> DBForm -> jpInput("txt_insurance_dp{$s_i}_age","input", $this -> _WData['Dependent']->result_get_value('InsuredAge',$s_i-1));?></td>
						</tr>
					</table>
				 </td>
				<?php endfor;  ?>
				
			</tr>
			</table>
		</form>	
	</div>
	
 <?php }
private function __Window_Plan(){ ?>
	<!-- start : tab3 -->
	<div id="tabs-3">
		<form name="frm_tabs3">
		<table width="100%" align="center" border=0>
			<tr>
				<td class="header-text wajib" valign="top">Product</td>
				<td style="height:30px;"  valign="top"><?php $this -> DBForm -> jpMultiple('plan_product_id', 'multiple',$this -> Customer -> ProductId($this ->_CampaignId),$this -> Customer -> ProductId($this ->_CampaignId),'onchange="getPlanByProduct(this);"');?> </td>
				<td class="header-text wajib" valign="top">Plan</td>
				<td style="height:30px;" id="html_inner_plan" valign="top"> <?php $this -> DBForm -> jpCombo('plan_plan', NULL, $this -> Customer -> ProductPlanId($this ->_CampaignId), $this -> _WData['Policy']-> result_get_value('ProductPlan'),'onchange="getPremiByPlan(this.value);"');?></td>
				<td class="header-text wajib" valign="top">Pay Mode</td>
				<td style="height:30px;"  valign="top"> <?php $this -> DBForm -> jpCombo('plan_paymode',NULL, $this -> Customer -> Paymode() ,$this -> _WData['Policy']-> result_get_value('PayModeId'),'onchange="getPremiByPlanMode(this.value);"');?></td>
			</tr>
			<tr>
				<td class="header-text wajib" valign="top">Pay Type</td>
				<td style="height:30px;" valign="top"> <?php $this -> DBForm -> jpCombo('plan_paytype', NULL, $this -> Customer -> PaymentTypeId() ,$this -> _WData['Payers']-> result_get_value('PaymentTypeId'));?></td>
				<td style="text-align:right;height:30px;"></td>
				<td style="height:30px;" colspan="3"> <div id="callculation_premi"></div></td>
			</tr>
			<tr>
				<td class="header-text wajib" valign="top" colspan="6"> <div id="product_benefit"> </div></td>
			</tr>
		</table>
		</form>
	</div>
  <?php
 }

/** 
 **
 **
 **
 **/
 
private function __Window_Payers() {  ?>
  <div id="tabs-4">
  <form name="frm_tabs4">
	
	<table width="100%" align="center">	
		
		<tr>
		
			<td class="header-text wajib">Title</td>
			<td><?php $this -> DBForm -> jpCombo("payer_salutation",NULL,$this -> Customer -> Salutation(), $this -> _WData['Payers'] -> result_get_value('PayerSalutationId'));?></td>
			<td class="header-text wajib" >First Name</td>
			<td><?php $this -> DBForm -> jpInput("payer_first_name","input",$this -> _WData['Payers'] -> result_get_value('PayerFirstName'));?></td>
			<td class="header-text sunah">Last Name</td>
			<td><?php $this -> DBForm -> jpInput("payer_last_name","input",$this -> _WData['Payers'] -> result_get_value('PayerLastName'));?></td>
		</tr>
		<tr>
			<td class="header-text wajib">Gender</td>
			<td><?php $this -> DBForm -> jpCombo("payer_gender",NULL,$this -> Customer -> Gender(),$this -> _WData['Payers'] -> result_get_value('GenderId'));?></td>
			<td class="header-text wajib">DOB</td>
			<td><?php $this -> DBForm -> jpInput("payer_dob","input",$this -> formatDateId($this -> _WData['Payers'] -> result_get_value('PayerDOB')),'onKeyup="Ext.Date(this.id).Long(\'-\');"' );?></td>
			<td class="header-text wajib">Address</td>
			<td><?php $this -> DBForm -> jpInput("payer_address1","input",$this -> _WData['Payers'] -> result_get_value('PayerAddressLine1'));?></td>
		</tr>
		<tr>
			<td class="header-text wajib">ID - Type </td>
			<td><?php $this -> DBForm -> jpCombo("payer_holder_idtype","input",$this -> Customer -> IndentificationId(), $this -> _WData['Payers'] -> result_get_value('IdentificationTypeId'));?></td>
			<td class="header-text wajib" >ID No</td>
			<td><?php $this -> DBForm -> jpInput("payer_idno","input",$this -> _WData['Payers'] -> result_get_value('PayerIdentificationNum'),'onkeyup="Ext.Set(this.id).IsNumber();"');?></td>
		</tr>
		<tr>
			<td class="header-text wajib">Mobile Phone</td>
			<td><?php $this -> DBForm -> jpInput("payer_mobile_phone","input",$this -> _WData['Payers'] -> result_get_value('PayerMobilePhoneNum'),'onkeyup="Ext.Set(this.id).IsNumber();"');?> </td>
			<td class="header-text wajib">City</td>
			<td><?php $this -> DBForm -> jpInput("payer_city","input",$this -> _WData['Payers'] -> result_get_value('PayerCity'));?>  </td>
			<td class="header-text wajib"></td>
			<td> <?php $this -> DBForm -> jpInput("payer_address2","input",$this -> _WData['Payers'] -> result_get_value('PayerAddressLine2'));?></td>
		</tr>	
		<tr>
			<td class="header-text wajib">Home Phone </td>
			<td><?php $this -> DBForm -> jpInput("payer_home_phone","input",$this -> _WData['Payers'] -> result_get_value('PayerHomePhoneNum'),'onkeyup="Ext.Set(this.id).IsNumber();"');?> </td>
			<td class="header-text wajib">Zip</td>
			<td><?php $this -> DBForm -> jpInput("payer_zip_code","input",$this -> _WData['Payers'] -> result_get_value('PayerZipCode'),'onkeyup="Ext.Set(this.id).IsNumber();"');?></td>
			<td class="header-text wajib"></td>
			<td><?php $this -> DBForm -> jpInput("payer_address3","input",$this -> _WData['Payers'] -> result_get_value('PayerAddressLine3'));?></td>
		</tr>	
		<tr>
			<td class="header-text wajib">Office Phone </td>
			<td><?php $this -> DBForm -> jpInput("payer_office_phone", "input", $this -> _WData['Payers'] -> result_get_value('PayerOfficePhoneNum'),'onkeyup="Ext.Set(this.id).IsNumber();"');?> </td>
			<td class="header-text wajib">Province</td>
			<td><?php $this -> DBForm -> jpCombo("payer_province", NULL, $this -> Customer -> Province(),$this -> _WData['Payers'] -> result_get_value('PayerProvinceId'));?></td>
			<td class="header-text wajib"></td>
			<td><?php $this -> DBForm -> jpInput("payer_address4", "input", $this -> _WData['Payers'] -> result_get_value('PayerAddressLine4') );?>  </td>
		</tr>	
		<tr>
			<td class="header-text wajib" valign="top">Card Number</td>
			<td valign="top"><?php $this -> DBForm -> jpInput("payer_card_number", "input", $this -> _WData['Payers'] -> result_get_value('PayerCreditCardNum'),'onkeyup="getNexValidationCard(this.value);" onkeyup="Ext.Set(this.id).IsNumber();"' );?><span id="error_message_html"></span></td>
			<td class="header-text sunah">Bank</td>
			<td><?php $this -> DBForm -> jpCombo("payer_bank", NULL, $this -> Customer -> Bank(),$this -> _WData['Payers'] -> result_get_value('PayersBankId'));?></td>
			<td class="header-text sunah">Fax Phone</td>
			<td><?php $this -> DBForm -> jpInput("payer_fax_number", "input", $this -> _WData['Payers'] -> result_get_value('PayerFaxNum') );?></td>
		</tr>	
		<tr>
			<td class="header-text wajib">Expiration Date</td>
			<td><?php $this -> DBForm -> jpInput("payer_expired_date", "input", $this -> _WData['Payers'] -> result_get_value('PayerCreditCardExpDate'),'onKeyup="Ext.Date(this.id).Sort(\'/\');"', null, null);?>(mm/yy)</td>
			<td class="header-text wajib">Card Type</td>
			<td><?php $this -> DBForm -> jpCombo("payer_card_type", NULL, $this -> Customer -> CardType(),$this -> _WData['Payers'] -> result_get_value('CreditCardTypeId'));?></td>
			<td class="header-text sunah">Email</td>
			<td><?php $this -> DBForm -> jpInput("payer_email", "input", $this -> _WData['Payers'] -> result_get_value('PayerEmail') );?></td>
		</tr>	
	 </table>
	 </form>
   </div>
	<?php				
 }
 
/**
 **
 **
 **/

function __Window_Benefit(){
?>
<div id="tabs-5" style="height:430px;overflow:auto;">
<form name="frm_tabs5" >
<table width="99%">
	<tr>
	<?php for($s_i=1; $s_i<=3; $s_i++ ) : ?>
		<td>
			<table>
				<tr>
					<td><h4>BENEFICIARY <?php echo $s_i; ?></h4> </td>
					<td><?php $this -> DBForm -> jpCheck("benef_box",NULL, $s_i,NULL,$this -> _WData['Beneficery'] -> result_get_value('BeneficiaryId',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah">Beneficiary Id</td>
					<td><?php $this -> DBForm -> jpInput("BeneficeryId_{$s_i}","input_box", $this -> _WData['Beneficery'] -> result_get_value('BeneficiaryId',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah">Holder Type</td>
					<td><?php $this -> DBForm -> jpCombo("txt_benef{$s_i}_holdertype",NULL, $this -> Customer -> PremiumGroup(),1,NULL,1); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah">Relation</td>
					<td><?php $this -> DBForm -> jpCombo("txt_benef{$s_i}_rel",NULL,$this -> Customer -> RelationshipType(),$this -> _WData['Beneficery'] -> result_get_value('RelationshipTypeId',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah">Title</td>
					<td><?php $this -> DBForm -> jpCombo("txt_benef{$s_i}_title",NULL, $this -> Customer -> Salutation(),$this -> _WData['Beneficery'] -> result_get_value('SalutationId',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah" class="input">First Name</td>
					<td> 
						<?php $this -> DBForm -> jpInput("txt_benef{$s_i}_first","input",$this -> _WData['Beneficery'] -> result_get_value('BeneficiaryFirstName',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah" class="input">Last Name</td>
					<td><?php $this -> DBForm -> jpInput("txt_benef{$s_i}_lastname","input",$this -> _WData['Beneficery'] -> result_get_value('BeneficiaryLastName',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah" class="input">Percentage</td>
					<td><?php $this -> DBForm -> jpInput("txt_benef{$s_i}_persen","input",$this -> _WData['Beneficery'] -> result_get_value('BeneficieryPercentage',$s_i-1)); ?>&nbsp;%</td>
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
					<td><?php $this -> DBForm -> jpCheck("benef_box",NULL, $s_i,NULL,$this -> _WData['Beneficery'] -> result_get_value('BeneficiaryId',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah">Beneficiary Id</td>
					<td><?php $this -> DBForm -> jpInput("BeneficeryId_{$s_i}","input_box", $this -> _WData['Beneficery'] -> result_get_value('BeneficiaryId',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah">Holder Type</td>
					<td><?php $this -> DBForm -> jpCombo("txt_benef{$s_i}_holdertype",NULL, $this -> Customer -> PremiumGroup(),1,NULL,1); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah">Relation</td>
					<td><?php $this -> DBForm -> jpCombo("txt_benef{$s_i}_rel",NULL,$this -> Customer -> RelationshipType(),$this -> _WData['Beneficery'] -> result_get_value('RelationshipTypeId',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah">Title</td>
					<td><?php $this -> DBForm -> jpCombo("txt_benef{$s_i}_title",NULL, $this -> Customer -> Salutation(),$this -> _WData['Beneficery'] -> result_get_value('SalutationId',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah" class="input">First Name</td>
					<td> 
						<?php $this -> DBForm -> jpInput("txt_benef{$s_i}_first","input",$this -> _WData['Beneficery'] -> result_get_value('BeneficiaryFirstName',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah" class="input">Last Name</td>
					<td><?php $this -> DBForm -> jpInput("txt_benef{$s_i}_lastname","input",$this -> _WData['Beneficery'] -> result_get_value('BeneficiaryLastName',$s_i-1)); ?></td>
				</tr>
				<tr>
					<td class="header-text sunah" class="input">Percentage</td>
					<td><?php $this -> DBForm -> jpInput("txt_benef{$s_i}_persen","input",$this -> _WData['Beneficery'] -> result_get_value('BeneficieryPercentage',$s_i-1)); ?>&nbsp;%</td>
				</tr>	
			</table>
		</td>
		<?php endfor; ?>
	</tr>
</table>
</form>
</div>
<?php
} 
 
/**
 **
 **
 **/
 
 private function __Window_header(){
	?>		
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<!-- start Link : css --> 
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta content="utf-8" http-equiv="encoding">
		<title><?php echo $this -> _tem -> V_WEB_TITLE; ?> :: Edit Policy </title>
		<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>gaya/gaya_utama.css" />
		<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>gaya/other.css" />
		<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-themes-1.7.2/themes/<?php echo $this -> _tem -> V_UI_THEMES;?>/ui.all.css" />	
		<link type="text/css" rel="stylesheet" media="all" href="<?php echo $this -> _url -> basePath();?>gaya/chat.css" />
		<link type="text/css" rel="stylesheet" media="all" href="<?php echo $this -> _url -> basePath();?>gaya/screen.css" />
		<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>gaya/custom.css" />
		<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-1.3.2.js"></script>    
		<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.7.2/ui/jquery-ui.js"></script>
		<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.7.2/external/bgiframe/jquery.bgiframe.js"></script>
		<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>js/javaclass.js?time=<?php echo time();?>"></script>
		<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>js/Ext.CignaCare.js?time=<?php echo time();?>"></script>
	
	<?php
}

private function __Window_Script(){
?>
	<script>
		
		var CustomerId 		= '<?php echo $this -> _CustomerId; ?>';
		var CampaignId 		= '<?php echo $this -> _CampaignId; ?>';
		var interestStatus 	= '<?php echo $this -> _CallReasonId; ?>'; 
		var CallStatus 		= '<?php echo $this -> _CallReasonId; ?>'; 
		var initGroup 		= false;
		var	iniatedUmur 	= '';
		var crosscek 		= false;


		$(document).ready(function() {
			$("#tabs" ).tabs();
			//$( "#tabs" ).tabs( "option", "disabled", []);	
			
			$("#frm_holder_dob").datepicker({ dateFormat: 'dd-mm-yy',
				buttonImage: '../gambar/calendar.gif', 
				buttonImageOnly: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '1945:2030',
				onSelect:function(date){
					toRenderValue(date,$(this).attr('name'),2)
				}
			});
			
		//payer data 	
			$('#payer_dob').datepicker({ 
				dateFormat: 'dd-mm-yy',
				buttonImage: '../gambar/calendar.gif', 
				buttonImageOnly: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '1945:2030'
			});
			
			
		// spouse 	
			$('#txt_insurance_sp_dob').datepicker({ 
				dateFormat: 'dd-mm-yy',
				buttonImage: '../gambar/calendar.gif', 
				buttonImageOnly: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '1945:2030',
				onSelect:function(date){
						$("#txt_insurance_sp_age").val(AjaxGetSizeAge(date,3));
						
					}
				
			});
			
		//dp1	
			$('#txt_insurance_dp1_dob').datepicker({ 
				dateFormat: 'dd-mm-yy',
				buttonImage: '../gambar/calendar.gif', 
				buttonImageOnly: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '1945:2030',
				onSelect:function(date){
						$("#txt_insurance_dp1_age").val(AjaxGetSizeAge(date,1));
					}
				
			});
			
		//dp2
			$('#txt_insurance_dp2_dob').datepicker({ 
				dateFormat: 'dd-mm-yy',
				buttonImage: '../gambar/calendar.gif', 
				buttonImageOnly: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '1945:2030',
				onSelect:function(date){
						$("#txt_insurance_dp2_age").val(AjaxGetSizeAge(date,1));
					}
				
			});
			
		//dp3
			
			$('#txt_insurance_dp3_dob').datepicker({ 
				dateFormat: 'dd-mm-yy',
				buttonImage: '../gambar/calendar.gif', 
				buttonImageOnly: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '1945:2030',
				onSelect:function(date){
							$("#txt_insurance_dp3_age").val(AjaxGetSizeAge(date,1));
					}
				
			});
			
			
			//dp4
			
			$('#txt_insurance_dp4_dob').datepicker({ 
				dateFormat: 'dd-mm-yy',
				buttonImage: '../gambar/calendar.gif', 
				buttonImageOnly: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '1945:2030',
				onSelect:function(date){
					$("#txt_insurance_dp4_age").val(AjaxGetSizeAge(date,1));
					}
				
			});
	});
	
	var getFuckJquery = function (name){
		$(document).ready(function() {
			$("#"+name).datepicker({ dateFormat: 'dd-mm-yy',
					buttonImage: '../gambar/calendar.gif', 
					buttonImageOnly: true,
					changeMonth: true,
					yearRange: '1945:2030',
					changeYear: true,
					onSelect:function(date){
						doJava.dom(name).value = date;
					}
			});
		});
	}
	
	var cekExpiredDate = function(obj)
	{
		var FindInput = obj.value;
		var StartPost = obj.id.split('_')//start_date_1;
		try
		{
			obj.maxLength = 5; 
			var IndexData =  StartPost[StartPost.length-1];
			if (FindInput.match(/^\d{2}$/) !== null) { obj.value = FindInput + '/'; } 
			else{
				console.log(obj.value)
			}
			
			if( obj.value.length >=5 )
			{
				doJava.File = "../class/class.frm.policy.php";
				doJava.Params = {
				action  : 'expired_action',
				cekcard :  obj.value.trim()
			}
				var error = doJava.Post();
				if( error==0){
					obj.value='';
				}
			}
		return false;
		
	  }
	  catch(e){
		console.log(e)
	  }
	  
		
	}
		
	var getUmurSizeFunc  = function(fo,to,init){
		$(function(){
			$("#"+fo).datepicker({ dateFormat: 'dd-mm-yy',
					buttonImage: '../gambar/calendar.gif', 
					buttonImageOnly: true,
					changeMonth: true,
					changeYear: true,
					yearRange: '1945:2030',
					onSelect:function(date){
						$('#'+to).val(AjaxGetSizeAge(date,init));
					}
			});
		});
	}	
	 

		
    </script>
	<style>
		#page_info_header { color:#3c3c36;background-color:#eeeeee;height:20px;padding:3px;font-size:14px;font-weight:bold;border-bottom:2px solid #dddfff;margin-bottom:4px;}
		 table td{ font-size:11px; text-align:left;}
		 table p{font-size:12px;color:blue;}
		 table td .input{ border:1px solid #b4d2d4;background-color:#f4f5e6;width:160px;height:20px;}
		  table td .input_box{ border:1px solid #b4d2d4;background-color:#f4f5e6;width:40px;height:20px;}
		 table td .input:hover{ border:1px solid red;background-color:#f9fae3}
		 table td select{ border:1px solid #b4d2d4;background-color:#f2f2e9;}
		.header-text {text-align:right;font-weight:normal;}
		.sunah {color:#4c4c47;font-size:11px;font-family:Arial;}
		.wajib {color:red;font-size:11px;font-family:Arial;}
		 h4{background-color:#8da0cf;color:#FFFFFF;padding:4px;cursor:pointer;width:120px;}
		 h4:hover{color:#f04a1d;background-color:#d8f7f9;}
		 .age{width:60px;}
		 .multiple{width:160px;}
	</style>
	</head>
<?php }


/**
 **
 **
 **/
private function __Window_footer(){
?>	
 </div> 
	<div style="float:right;">	
		<a href="javascript:void(0);" class="sbutton" onclick="javascript:doJava.winew.winClose();" style="margin:4px;"><span>&nbsp;Exit</span></a> &nbsp;
		<a href="javascript:void(0);" class="sbutton" onclick="javascript:saveCreatePolish();" style="margin:4px;"><span>&nbsp;Update</span></a> &nbsp;
	</div>	
</body>
</html>
<?php }

}	

new WindowCignaCare();
	
	
	
?>