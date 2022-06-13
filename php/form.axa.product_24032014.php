<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");

 
/*
||/\/\/\/\/\/\/\/\/-----------------------------------------------
||/\/\/\/\/\/\/\/\/-----------------------------------------------
*/
 
/* 
 * @ package 	: clas AXA_Product
 * 
 * @ params		: extends mysql
 * @ render		: object
 */
 
 // NOTES : js diganti dulu sama js/Ext.AxaProduct_dep.js (abie)
 
class AXA_Product extends mysql
{

  var $_url; 
  var $_tem;
  var $_data;	
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */

private function _get_data_customer()
{
	$datas = $this -> Customer -> DataPolicy( $this -> escPost('customerid') ); // data customer 
	if( !is_array($datas) ) return null;
	else
	{
		return $datas['Customer'];
	}
} 



/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */

 private function _getCustomerId()
 {
	$_conds = 0;
	if($this -> havepost('customerid')){
		$_conds = (int)$this -> escPost('customerid');
	}
	
	return $_conds;
 }
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */

 
  
public function AXA_Product()
{
	parent::__construct();
	
	$this -> _url  =& application::get_instance(); /// Application();
	$this -> _tem  =& Themes::get_instance();  // Themes
	$this -> _data =& self::_get_data_customer(); // customer;
	
	if(class_exists('Themes')) 
	{
		self::AXA_Header();
		self::AXA_Body();
	}
 }
 
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
 
private function _get_member_of()
{
	return array
	(
		'1'=>'Self', /// dependent berdiri sendiri tidak terikat 
		'2'=>'Holder', // terikat dgn holder 
		'3'=>'Spouse' // terikat dgn spouse
	);
}	

/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
 
 public function AXA_Header()
 { 
   ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta content="utf-8" http-equiv="encoding">
<title>Create Policy </title>
<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>gaya/policy.screen.css?time=<?php echo time();?>" />
<link type="text/css" rel="stylesheet" href="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-themes-1.7.2/themes/<?php echo $this -> _tem -> V_UI_THEMES;?>/ui.all.css?time=<?php echo time();?>" />	
<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-1.3.2.js?time=<?php echo time();?>"></script>    
<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.7.2/ui/jquery-ui.js?time=<?php echo time();?>"></script>
<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>pustaka/jquery/jquery-ui-1.7.2/external/bgiframe/jquery.bgiframe.js?time=<?php echo time();?>"></script>
<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>js/EUI_1.0.2.js?time=<?php echo time();?>"></script>
<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>js/Ext.AxaProduct_dep.js?time=<?php echo time();?>"></script>

</head>
 <?php }
 
 
 /*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
 
 public function AXA_Body() 
 {  ?>
	<body >
	<table border=0 width="70%" align="center" cellpadding="5px">	
			<!-- start : layout top -->
			<tr><td><?php self::AXA_Toper(); ?></td></tr>
			<!-- start : layout top -->
			<tr><td><?php self::AXA_Tabs(); ?></td></tr>	
			<!-- start : layout footer -->
			<tr><td><?php self::AXA_Footer();?></td></tr>	
		</table>
	</body>
	</html>
	<?php 
 }
 
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
 
private function AXA_Insured()
{ ?>

<form name="form_data_insured">
<fieldset class="corner" style="margin-left:-5px;">
	<legend class="icon-application">&nbsp;&nbsp;&nbsp;<b>Insured</b></legend>	
<table cellpadding="4px">
	<tr>
		<td class="header_table required">* Policy Number</td>
		<td><?php $this -> DBForm -> jpCombo('InsuredPolicyNumber','select long',array()); ?> </td>
		<td class="header_table">Payment Mode</td>
		<td><span id="pay_plan_h"><?php $this -> DBForm -> jpCombo('InsuredPayMode','select long', $this ->Customer -> Paymode( $this -> escPost('campaignid') ), null,"OnChange=getPremi(this);"); ?></span> </td>
	</tr>
	
	<tr>
		<td class="header_table required">* Group Premi</td>
		<td><?php $this -> DBForm -> jpCombo('InsuredGroupPremi','select long', $this->Customer->PremiumGroup(),null, "onchange=Ext.DOM.ClearInsured(this);" ); ?> </td>
		<td class="header_table">Plan Type</td>
		<td><span id="plan_plan_h"><?php $this -> DBForm -> jpCombo('InsuredPlanType','select long', $this -> Customer -> ProductPlan($this -> escPost('campaignid')), null,"OnChange=getPremi(this);"); ?></span> </td>
	</tr>
	
	<tr>
		<td class="header_table required">* ID Type</td>
		<td><?php $this -> DBForm -> jpCombo('InsuredIdentificationTypeId','select long', $this->Customer->IndentificationId() ); ?> </td>
		<td class="header_table">Premi</td>
		<td><?php $this -> DBForm -> jpInput('InsuredPremi','input long',null, null, 1); ?> <span class="wrap"> ( IDR ) </span></td>
	</tr>
	<tr>
		<td class="header_table required">* ID No</td>
		<td><?php $this -> DBForm -> jpInput('InsuredIdentificationNum','input long',null,'onkeyup="Ext.Set(this.id).IsNumber();"'); ?></td>
		
	</tr>
	<tr>
		<td class="header_table sunah">Relation</td>
		<td><?php $this -> DBForm -> jpCombo('InsuredRelationshipTypeId','select long', $this->Customer->RelationshipType(),79); ?></td>
		
	</tr>
	<tr>
		<td class="header_table sunah">Title</td>
		<td><?php $this -> DBForm -> jpCombo('InsuredSalutationId','select long',$this->Customer->Salutation()); ?></td>
	</tr>
	<tr>
		<td class="header_table sunah">First Name</td>
		<td><?php $this -> DBForm -> jpInput('InsuredFirstName','input long',null,'onkeyup="Ext.Set(this.id).IsString();"'); ?></td>
	</tr>
	<tr>
		<td class="header_table sunah">Last Name</td>
		<td><?php $this -> DBForm -> jpInput('InsuredLastName','input long',null,'onkeyup="Ext.Set(this.id).IsString();"'); ?></td>
	</tr>
	<tr>
		<td class="header_table sunah">Gender</td>
		<td><?php $this -> DBForm -> jpCombo('InsuredGenderId','select long',$this -> Customer -> Gender()); ?></td>
	</tr>
	<tr>
		<td class="header_table sunah">DOB</td>
		<td><?php $this -> DBForm -> jpInput('InsuredDOB','input date',null, null, 1); ?></td>
	</tr>
	<tr>
		<td class="header_table sunah">Age</td>
		<td><?php $this -> DBForm -> jpInput('InsuredAge','input',null, null, 1); ?></td>
	</tr>
</table>	
</fieldset>
</form>
<?php
}

/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
 
private function AXA_Benefiecery() { ?>

<form name="form_data_benefiecery">
<?php
 for( $_benefiecery=1; $_benefiecery<=4; $_benefiecery++)
 {  ?>

 <fieldset class="corner" style="margin-left:-5px;">
	<legend class="icon-application ">&nbsp;&nbsp;&nbsp;
		<b>Benefiecery <?php echo $_benefiecery; ?></b>
		<?php $this -> DBForm -> jpCheck("Benefeciery",null,$_benefiecery,"onclick=FormBenefiecery(this,". $_benefiecery .");");?>
	</legend>	
	
	<table cellpadding="5px"> 
		<tr>
			<td class="header_table">Relation</td>
			<td><?php $this -> DBForm -> jpCombo("BenefRelationshipTypeId_{$_benefiecery}",'select long',  $this -> Customer -> RelationshipType()); ?></td>
		</tr>
		<tr>
			<td class="header_table">Title</td>
			<td><?php $this -> DBForm -> jpCombo("BenefSalutationId_{$_benefiecery}",'select long', $this -> Customer -> Salutation()); ?></td>
		</tr>
		<tr>
			<td class="header_table required">* First Name</td>
			<td> <?php $this -> DBForm -> jpInput("BenefFirstName_{$_benefiecery}","input long",null,'onkeyup="Ext.Set(this.id).IsString();"'); ?></td>
		</tr>
		<tr>
			<td class="header_table ">Last Name</td>
			<td><?php $this -> DBForm -> jpInput("BenefLastName_{$_benefiecery}","input long",null,'onkeyup="Ext.Set(this.id).IsString();"'); ?></td>
		</tr>
		<tr>
			<td class="header_table required">* Percentage</td>
			<td><?php $this -> DBForm -> jpInput("BenefPercentage_{$_benefiecery}","input long",null,'onkeyup="Ext.Set(this.id).IsNumber();"'); ?>&nbsp;<span class="wrap">( % )</span></td>
		</tr>
	</table>
  </fieldset><br>		
<?php }	 ?>		
</form>
<?php 
}				
 

/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
 
private function AXA_Payers() { ?>

 <fieldset class="corner" style="margin-left:-5px;">
	<legend class="icon-application ">&nbsp;&nbsp;&nbsp;
		<b>Payer & information</b>
		<?php $this -> DBForm -> jpCheck("CopyData",'Payer = holder',1,"onchange=CopyData(this);");?>
	</legend>	
	<form name="form_data_payer" >
	<table width="100%" align="center" cellpadding="5px">	
		<tr>
			<td class="header_table required">* Title</td>
			<td><?php $this -> DBForm -> jpCombo("PayerSalutationId",'select long', $this -> Customer -> Salutation());?></td>
			<td class="header_table required" nowrap>* First Name</td>
			<td><?php $this -> DBForm -> jpInput("PayerFirstName","input long",null,'onkeyup="Ext.Set(this.id).IsString();"');?></td>
			<td class="header_table" nowrap>Last Name</td>
			<td><?php $this -> DBForm -> jpInput("PayerLastName","input long",null,'onkeyup="Ext.Set(this.id).IsString();"');?></td>
		</tr>
		<tr>
			<td class="header_table">Gender</td>
			<td><?php $this -> DBForm -> jpCombo("PayerGenderId",'select long',  $this -> Customer -> Gender());?></td>
			<td class="header_table">DOB</td>
			<td><?php $this -> DBForm -> jpInput("PayerDOB","input long date");?></td>
			<td class="header_table">Address</td>
			<td><?php $this -> DBForm -> jpInput("PayerAddressLine1","input");?></td>
		</tr>
		<tr>
			<td class="header_table required">ID - Type </td>
			<td><?php $this -> DBForm -> jpCombo("PayerIdentificationTypeId","select long", $this -> Customer -> IndentificationId() );?></td>
			<td class="header_table required" >* ID No</td>
			<td><?php $this -> DBForm -> jpInput("PayerIdentificationNum","input long",null,'onkeyup="Ext.Set(this.id).IsNumber();"');?></td>
		</tr>
		<tr>
			<td class="header_table">Mobile Phone</td>
			<td><?php $this -> DBForm -> jpInput("PayerMobilePhoneNum","input long",null,'onkeyup="Ext.Set(this.id).IsNumber();"',true);?> </td>
			<td class="header_table">City</td>
			<td><?php $this -> DBForm -> jpInput("PayerCity","input long",null,'onkeyup="Ext.Set(this.id).IsString();"');?>  </td>
			<td class="header_table"></td>
			<td> <?php $this -> DBForm -> jpInput("PayerAddressLine2","input");?></td>
		</tr>	
		<tr>
			<td class="header_table">Home Phone </td>
			<td><?php $this -> DBForm -> jpInput("PayerHomePhoneNum","input long",null,'onkeyup="Ext.Set(this.id).IsNumber();"', true);?> </td>
			<td class="header_table">Zip</td>
			<td><?php $this -> DBForm -> jpInput("PayerZipCode","input long",null,null,0,5);?></td>
			<td class="header_table"></td>
			<td><?php $this -> DBForm -> jpInput("PayerAddressLine3","input");?></td>
		</tr>	
		<tr>
			<td class="header_table">Office Phone </td>
			<td><?php $this -> DBForm -> jpInput("PayerOfficePhoneNum", "input long",null,'onkeyup="Ext.Set(this.id).IsNumber();"', true);?> </td>
			<td class="header_table">Province</td>
			<td><?php $this -> DBForm -> jpCombo("PayerProvinceId", 'select long',$this -> Customer -> Province() );?></td>
			<td class="header_table"></td>
			<td><?php $this -> DBForm -> jpInput("PayerAddressLine4", "input");?>  </td>
		</tr>	
		<tr>
			<td class="header_table" valign="top">Card Number</td>
			<td valign="top"><?php $this -> DBForm -> jpInput("PayerCreditCardNum", "input long",null,'onkeyup="Ext.Set(this.id).IsNumber();"',0,16);?><span id="error_message_html"></span></td>
			<td class="header_table">Bank</td>
			<td><?php $this -> DBForm -> jpCombo("PayersBankId", 'select long',$this -> Customer -> Bank());?></td>
			<td class="header_table">Fax Phone</td>
			<td><?php $this -> DBForm -> jpInput("PayerFaxNum", "input long",null,'onkeyup="Ext.Set(this.id).IsNumber();"');?></td>
		</tr>	
		<tr>
			<td class="header_table " nowrap>Expiration Date</td>
			<td><?php $this -> DBForm -> jpInput("PayerCreditCardExpDate", "input long", null, null);?><span class="wrap">&nbsp;(mm/yy)</span></td>
			<td class="header_table">Card Type</td>
			<td><?php $this -> DBForm -> jpCombo("CreditCardTypeId", 'select long',$this -> Customer -> CardType() );?></td>
			<td class="header_table">Email</td>
			<td><?php $this -> DBForm -> jpInput("PayerEmail", "input long");?></td>
		</tr>	
	 </table>
	 </form>
	</fieldset> 
	
	<?php 
}

/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
 
 
public function AXA_Tabs() 
{ ?>
<!-- start : layout content -->	
 <fieldset class="corner">
 <legend class="icon-customers">&nbsp;&nbsp;&nbsp;Policy </legend>
	<div id="tabs" class="corener">
		<ul>
			<li><a href="#tabs-5">PAYER AND ADDRESS INFO</a></li>
			<li><a href="#tabs-2">INSURED</a></li>
			<li><a href="#tabs-6">BENEFICIARY</a></li>
		</ul>
		
		<div id="tabs-5" style="height:420px;overflow:auto;"><?php self::AXA_Payers();?></div>
		<div id="tabs-2" style="height:420px;overflow:auto;"><?php self::AXA_Insured();?></div>
		<div id="tabs-6" style="height:420px;overflow:auto;"><?php self::AXA_Benefiecery();?></div>
	</div>
 </fieldset>	
 <?php
 }
 
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
  
public function AXA_Toper() 
 { ?>
	<fieldset class="corner" style="background:url('../gambar/pager_bg.png') left top;">
		<legend class="icon-product"> &nbsp;&nbsp;&nbsp;Product</legend>
		<form name="form_data_product">	
			<input type="hidden" name="CustomerId" id="CustomerId" value="<?php echo self::_getCustomerId(); ?>"/>
			<table cellpadding="5px" width="100%" align="center">
				<tr>
					<td class="header_table">Product</td>
					<td><?php $this -> DBForm -> jpCombo("ProductId","select long", $this -> Customer -> ProductId($_REQUEST['campaignid']),null,"onChange=getSplitProduct(this);");?></td>
					<td class="header_table">Sales Date</td>
					<td><?php $this -> DBForm -> jpInput("SalesDate","input long",$this -> formatDateId(date('Y-m-d')),null,1);?></td>
				</tr>
				<tr>
					<td class="header_table">Pecah Policy</td>
					<td><?php $this -> DBForm -> jpCombo("PecahPolicy","select long", array('0'=>'No','1'=>'Yes'),0,"onChange=Ext.DOM.PecahPolicy(this.value);",1);?></td>
					<td class="header_table">Efective Date</td>
					<td><?php $this -> DBForm -> jpInput("EfectiveDate","input long",$this -> formatDateId(date('Y-m-d')),null,1);?></td>
				</tr>
			</table>
	</form>
	</fieldset>
 <?php }
 
/*
 * @ def 	 : AXA_Header
 * 
 * @ params	 : defualt 
 * @ return  : void
 */
public function AXA_Footer()
{ ?>
	<div style="float:right;">	
		<a href="javascript:void(0);" class="sbutton" onclick="javascript:doJava.winew.winClose();" style="margin:4px;"><span>&nbsp;Exit</span></a> &nbsp;
		<a href="javascript:void(0);" class="sbutton" onclick="javascript:SavePolis();" style="margin:4px;"><span>&nbsp;Save</span></a> &nbsp;
	</div>	
<?php }  

 }
 
 new AXA_Product();
 
 
?>

