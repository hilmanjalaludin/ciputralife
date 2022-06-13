<?php
	require(dirname(__FILE__)."/../sisipan/sessions.php");
	require(dirname(__FILE__)."/../fungsi/global.php");
	require(dirname(__FILE__)."/../class/MYSQLConnect.php");
	require(dirname(__FILE__)."/../class/class.application.php");
	require(dirname(__FILE__)."/../sisipan/parameters.php");
	
	class AXA_EDIT extends mysql
	{
		var $_url; 
		var $_tem;
		var $_data;
		var $_productId;
		var $policy; 
		
		function AXA_EDIT()
		{
			parent::__construct();
	
			$this -> _url  =& application::get_instance(); /// Application();
			$this -> _tem  =& Themes::get_instance();  // Themes
			$this -> _data =& $this->get_data_customer(); // customer;
			$this -> _productId =& $this ->_data['Policy']->result_get_value('ProductId');
			$this -> policy =& $this->getPolicy();
			
			if(class_exists('Themes')) 
			{
				$this->header();
				$this->body();
			}
		}
		
		
		
		function header()
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
			<script type="text/javascript" src="<?php echo $this -> _url -> basePath();?>js/Ext.AxaEditProduct_dep.js?time=<?php echo time();?>"></script>
			<script>
					function closeWindow() {
						window.open('','_parent','');
						window.close();
					}
					function NotReadyYet() {
						alert("UnderConstruction !! Fiture Temporary disable!"); return false; 
					}
			</script>  
			</head>
			 <?php
		}
		
		function body()
		{
			?>
			<body>
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
			//echo "ENAAAAAAAAAAAK!!!!";
		}
		
		function AXA_Toper()
		{
			$method = $this->getSplit();
			?>
			<fieldset class="corner" style="background:url('../gambar/pager_bg.png') left top;">
				<legend class="icon-product"> &nbsp;&nbsp;&nbsp;Product</legend>
				<form name="form_data_product">	
				<input type="hidden" name="CustomerId" id="CustomerId" value="<?php echo $_REQUEST['customerid']; ?>"/>
				<table cellpadding="5px" width="100%" align="center">
					<tr>
						<td class="header_table">Product</td>
						<td><?php $this -> DBForm -> jpCombo("ProductId","select long", $this -> Customer -> ProductId($_REQUEST['campaignid']),$this -> _productId,null,1);?></td>
						<td class="header_table">Sales Date</td>
						<td><?php $this -> DBForm -> jpInput("SalesDate","input long",$this -> formatDateId($this -> _data['Policy']->result_get_value('PolicySalesDate')),null,1);?></td>
					</tr>
					<tr>
						<td class="header_table">Pecah Policy</td>
						<td><?php $this -> DBForm -> jpCombo("PecahPolicy","select long", array('0'=>'No','1'=>'Yes'),($method?($this -> _data['Policy']->result_get_value('NumberMember') > 1?'1':'0'):''),null,1);?></td>
						<td class="header_table">Efective Date</td>
						<td><?php $this -> DBForm -> jpInput("EfectiveDate","input long",$this -> formatDateId($this -> _data['Policy']->result_get_value('PolicyEffectiveDate')),null,1);?></td>
					</tr>
				</table>
				</form>
			</fieldset>
		<?
		}
		
		function AXA_Tabs()
		{
			?>
			<!-- start : layout content -->	
			<fieldset class="corner">
				<legend class="icon-customers">&nbsp;&nbsp;&nbsp;Policy </legend>
				<div id="tabs" class="corener">
					<ul>
						<li><a href="#tabs-1">DATA</a></li>
						<li><a href="#tabs-2">HOLDER</a></li>
						<li><a href="#tabs-3">SPOUSE</a></li>
						<li><a href="#tabs-4">DEPENDENT</a></li>
						<li><a href="#tabs-5">PAYER AND ADDRESS INFO</a></li>
						<li><a href="#tabs-6">BENEFICIARY</a></li>
					</ul>
					
					<div id="tabs-1" style="height:360px;overflow:auto;"><?php self::AXA_Data();?></div>
					<div id="tabs-2" style="height:360px;overflow:auto;"><?php self::AXA_Holder();?></div>
					<div id="tabs-3" style="height:360px;overflow:auto;"><?php self::AXA_Spouse();?></div>
					<div id="tabs-4" style="height:360px;overflow:auto;"><?php self::AXA_Dependent();?></div>
					<div id="tabs-5" style="height:360px;overflow:auto;"><?php self::AXA_Payers();?></div>
					<div id="tabs-6" style="height:360px;overflow:auto;"><?php self::AXA_Benefiecery();?></div>
				</div>
			</fieldset>	
			<?php
		}
		
		function AXA_Footer()
		{
			?>
			<div style="float:right;">	
				<a href="javascript:void(0);" onclick="javascript:closeWindow();" class="sbutton" style="margin:4px;"><span>&nbsp;Exit</span></a> &nbsp;
				<!--<a href="javascript:void(0);" class="sbutton" onclick="javascript:UpdatePolis();" style="margin:4px;"><span>&nbsp;Update</span></a> &nbsp;-->
				<a href="javascript:void(0);" class="sbutton" onclick="javascript:NotReadyYet();" style="margin:4px;"><span>&nbsp;Update</span></a> &nbsp;
				<a href="javascript:void(0);" class="sbutton" onclick="javascript:SavePolis();" style="margin:4px;"><span>&nbsp;Save</span></a> &nbsp;
			</div>	
			<?php
		}
		
		function AXA_Data()
		{
			?>
			<form name="form_data_customers">
			<fieldset class="corner">
					<legend class="icon-application"> &nbsp;&nbsp;&nbsp;<b>Data Customer</b></legend>
			 <table border=0 width="70%" cellpadding="6px">
				<tr>
					<td class="header_table">Customer Name </td>
					<td style="height:30px;"><?php $this -> DBForm -> jpInput("CustomerFirstName","input long", $this->_data['Customer']->result_get_value('CustomerFirstName'),null,1);?></td>
					<td class="header_table">Gender</td>
					<td style="height:30px;"><?php $this -> DBForm -> jpCombo("GenderId","select long", $this -> Customer -> Gender(), $this->_data['Customer']->result_get_value('GenderId'));?></td>
				</tr>
				<tr>
					<td class="header_table">DOB</td>
					<td style="height:30px;"><?php $this -> DBForm -> jpInput("CustomerDOB","input long date",$this -> formatDateId($this->_data['Customer']->result_get_value('CustomerDOB')),null,1);?></td>
					<td class="header_table wajib">Address 1</td>
					<td style="height:30px;"><?php $this -> DBForm -> jpInput("CustomerAddressLine1","input",$this->_data['Customer']->result_get_value('CustomerAddressLine1'),null,1);?></td>
				</tr>
				<tr>
					<td class="header_table wajib">Mobile Phone</td>
					<td style="height:30px;"><?php $this -> DBForm -> jpInput("CustomerMobilePhoneNum","input long",$this->_data['Customer']->result_get_value('CustomerMobilePhoneNum'),null,1);?></td>
					<td class="header_table wajib">Address 2</td>
					<td style="height:30px;"><?php $this -> DBForm -> jpInput("CustomerAddressLine2","input",$this ->_data['Customer']->result_get_value('CustomerAddressLine2'),null,1);?></td>
				</tr>
				<tr>
					<td class="header_table wajib">Home Phone</td>
					<td style="height:30px;"><?php $this -> DBForm -> jpInput("CustomerHomePhoneNum","input long",$this ->_data['Customer']->result_get_value('CustomerHomePhoneNum'),null,1);?></td>
					<td class="header_table wajib">Address 3</td>
					<td style="height:30px;"><?php $this -> DBForm -> jpInput("CustomerAddressLine3","input ",$this->_data['Customer']->result_get_value('CustomerAddressLine3'),null,1);?></td>
				</tr>
				<tr>
					<td class="header_table wajib">Office Phone</td>
					<td style="height:30px;"><?php $this -> DBForm -> jpInput("CustomerWorkPhoneNum","input long",$this->_data['Customer']->result_get_value('CustomerWorkPhoneNum'),null,1);?></td>
					<td class="header_table wajib">Kota</td>
					<td style="height:30px;"><?php $this -> DBForm -> jpInput("CustomerCity","input long",$this->_data['Customer']->result_get_value('CustomerCity'),null,1);?></td>
			  </table>
			  </fieldset>
			</form>	
			<?php	
		}
		
		function AXA_Holder()
		{
			?>
			<form name="form_data_holder">
			<fieldset class="corner" style="margin-left:-5px;">
				<input type="hidden" name="HoldInsuredId" id="HoldInsuredId" value="<?php echo $this->_data['Holder']->result_get_value('InsuredId'); ?>"/>
				<legend class="icon-application">&nbsp;&nbsp;&nbsp;<b>Holder</b>
				<?php $this -> DBForm -> jpCheck("Holder",null,2,null,($this->_data['Holder']->result_num_rows() > 0?'1':'0'),1);?>
				</legend>
				<table cellpadding="4px">
					<tr>
						<td class="header_table sunah">Policy Number</td>
						<td><?php $this -> DBForm -> jpInput('HoldPolicyNumber','select long', $this -> policy[2][0]['PolicyNumber'],null,1); ?></td>
					</tr>
					<tr>
						<td class="header_table required">* ID Type</td>
						<td><?php $this -> DBForm -> jpCombo('HoldIdentificationTypeId','select long', $this->Customer->IndentificationId(), $this->_data['Holder']->result_get_value('IdentificationTypeId') ); ?> </td>
						<td class="header_table">Payment Mode</td>
						<td><span id="pay_plan_h"><?php $this -> DBForm -> jpCombo('HoldPayMode','select long', $this -> Customer -> Paymode($this -> _productId), $this->_data['Holder']->result_get_value('PayModeId'),"OnChange=getPremi(this);"); ?></span> </td>
					</tr>
					<tr>
						<td class="header_table required">* ID No</td>
						<td><?php $this -> DBForm -> jpInput('HoldIdentificationNum','input long',$this->_data['Holder']->result_get_value('InsuredIdentificationNum')); ?></td>
						<td class="header_table">Plan Type</td>
						<td><span id="plan_plan_h"><?php $this -> DBForm -> jpCombo('HoldPlanType','select long',$this -> Customer -> ProductPlan($this -> _productId), $this->_data['Holder']->result_get_value('ProductPlan'),"OnChange=getPremi(this);"); ?></span> </td>
					</tr>
					<tr>
						<td class="header_table sunah">Relation</td>
						<td><?php $this -> DBForm -> jpCombo('HoldRelationshipTypeId','select long', $this->Customer->RelationshipType(),$this->_data['Holder']->result_get_value('RelationshipTypeId')); ?></td>
						<td class="header_table">Premi</td>
						<td><?php $this -> DBForm -> jpInput('HoldPremi','input long',formatRupiah($this->_data['Holder']->result_get_value('Premi')), null, 1); ?> <span class="wrap"> ( IDR ) </span></td>
					</tr>
					<tr>
						<td class="header_table sunah">Title</td>
						<td><?php $this -> DBForm -> jpCombo('HoldSalutationId','select long',$this->Customer->Salutation(),$this->_data['Holder']->result_get_value('SalutationId')); ?></td>
					</tr>
					<tr>
						<td class="header_table sunah">First Name</td>
						<td><?php $this -> DBForm -> jpInput('HoldFirstName','input long',$this->_data['Holder']->result_get_value('InsuredFirstName')); ?></td>
					</tr>
					<tr>
						<td class="header_table sunah">Last Name</td>
						<td><?php $this -> DBForm -> jpInput('HoldLastName','input long',$this->_data['Holder']->result_get_value('InsuredLastName')); ?></td>
					</tr>
					<tr>
						<td class="header_table sunah">Gender</td>
						<td><?php $this -> DBForm -> jpCombo('HoldGenderId','select long',$this -> Customer -> Gender(),$this->_data['Holder']->result_get_value('GenderId')); ?></td>
					</tr>
					<tr>
						<td class="header_table sunah">DOB</td>
						<td><?php $this -> DBForm -> jpInput('HoldDOB','input date',$this->formatDateId($this->_data['Holder']->result_get_value('InsuredDOB')), null, 1); ?></td>
					</tr>
					<tr>
						<td class="header_table sunah">Age</td>
						<td><?php $this -> DBForm -> jpInput('HoldAge','input',$this->_data['Holder']->result_get_value('InsuredAge'), null, 1); ?></td>
					</tr>
				</table>	
			</fieldset>
			</form>
			<?php
		}
		
		function AXA_Spouse()
		{
			?>
			<form name="form_data_spouse">
			<fieldset class="corner" style="margin-left:-5px;">
				<input type="hidden" name="SpInsuredId" id="SpInsuredId" value="<?php echo $this->_data['Spouse']->result_get_value('InsuredId'); ?>"/>
				<legend class="icon-application">&nbsp;&nbsp;&nbsp;<b>Spouse</b>
				<?php $this -> DBForm -> jpCheck("Spouse",null,3,null,($this->_data['Spouse']->result_num_rows() > 0?'1':'0'),1);?>
				</legend>	
			<table cellpadding="4px">
				<tr>
					<td class="header_table sunah">Policy Number</td>
					<td><?php $this -> DBForm -> jpInput('SpPolicyNumber','select long', $this -> policy[3][0]['PolicyNumber'],null,1); ?></td>
				</tr>
				<tr>
					<td class="header_table required">* ID Type</td>
					<td><?php $this -> DBForm -> jpCombo('SpIdentificationTypeId','select long', $this->Customer->IndentificationId(),$this->_data['Spouse']->result_get_value('IdentificationTypeId')); ?> </td>
					<td class="header_table">Payment Mode</td>
					<td><span id="pay_plan_s"><?php $this -> DBForm -> jpCombo('SpPaymode','select long',$this -> Customer -> Paymode($this -> _productId),$this->_data['Spouse']->result_get_value('PayModeId'),"OnChange=getPremi(this);"); ?></span> </td>
				</tr>
				<tr>
					<td class="header_table required">* ID No</td>
					<td><?php $this -> DBForm -> jpInput('SpIdentificationNum','input long',$this->_data['Spouse']->result_get_value('InsuredIdentificationNum')); ?></td>
					<td class="header_table">Plan Type</td>
					<td><span id="plan_plan_s"><?php $this -> DBForm -> jpCombo('SpPlanType','select long',$this -> Customer -> ProductPlan($this -> _productId),$this->_data['Spouse']->result_get_value('ProductPlan'),"OnChange=getPremi(this);"); ?></span> </td>
				</tr>
				<tr>
					<td class="header_table sunah">Relation</td>
					<td><?php $this -> DBForm -> jpCombo('SpRelationshipTypeId','select long', $this->Customer->RelationshipType(),$this->_data['Spouse']->result_get_value('RelationshipTypeId') ); ?></td>
					<td class="header_table">Premi</td>
					<td><?php $this -> DBForm -> jpInput('SpPremi','input long',formatRupiah($this->_data['Spouse']->result_get_value('Premi')), null, 1); ?> <span class="wrap"> ( IDR ) </span></td>
				</tr>
				<tr>
					<td class="header_table sunah">Title</td>
					<td><?php $this -> DBForm -> jpCombo('SpSalutationId','select long', $this->Customer->Salutation(),$this->_data['Spouse']->result_get_value('SalutationId') ); ?></td>
				</tr>
				<tr>
					<td class="header_table sunah">First Name</td>
					<td><?php $this -> DBForm -> jpInput('SpFirstName','input long',$this->_data['Spouse']->result_get_value('InsuredFirstName')); ?></td>
				</tr>
				<tr>
					<td class="header_table sunah">Last Name</td>
					<td><?php $this -> DBForm -> jpInput('SpLastName','input long',$this->_data['Spouse']->result_get_value('InsuredLastName')); ?></td>
				</tr>
				<tr>
					<td class="header_table sunah">Gender</td>
					<td><?php $this -> DBForm -> jpCombo('SpGenderId','select long', $this->Customer->Gender(), $this->_data['Spouse']->result_get_value('GenderId') ); ?></td>
				</tr>
				<tr>
					<td class="header_table sunah">DOB</td>
					<td><?php $this -> DBForm -> jpInput('SpDOB','input date',$this->formatDateId($this->_data['Spouse']->result_get_value('InsuredDOB')), null, 1); ?></td>
				</tr>
				<tr>
					<td class="header_table sunah">Age</td>
					<td><?php $this -> DBForm -> jpInput('SpAge','input',$this->_data['Spouse']->result_get_value('InsuredAge'), null, 1); ?></td>
				</tr>
			</table>	
			</legend>
			</form>
			<?php
		}
		
		function AXA_Dependent()
		{
			$no = 0;
			$dp = $this->_data['Dependent']->result_assoc();
			?>
			<form name="form_data_dependent">
			<?php 
			for ( $_dependent=1; $_dependent<=4; $_dependent++) 
			{ 
				?>
				<fieldset class="corner" style="margin-left:-5px;">
					<input type="hidden" name="DepInsuredId_<?php echo $_dependent; ?>" id="DepInsuredId_<?php echo $_dependent; ?>" value="<?php echo $dp[$no]['InsuredId']; ?>"/>
					<legend class="icon-application">&nbsp;&nbsp;&nbsp;<b>Dependent <?php echo $_dependent;?></b>
						<?php $this -> DBForm -> jpCheck("Dependent",null,$_dependent,null,($this->_data['Dependent']->result_num_rows() > $no ?'1':'0'),1);?>
					</legend>
						
					<table cellpadding="4px">
						<tr>
							<td class="header_table sunah">Policy Number</td>
							<td><?php $this -> DBForm -> jpInput("DepPolicyNumber_{$_dependent}",'select long', $this -> policy[1][$no]['PolicyNumber'],null,1); ?></td>
						</tr>
						<tr>
							<td class="header_table sunah">Relation</td>
							<td><?php $this -> DBForm -> jpCombo("DepRelationshipTypeId_{$_dependent}",'select long', $this->Customer->RelationshipType(),$dp[$no]['RelationshipTypeId']); ?></td>
							
							<td class="header_table">Member Of</td>
							<td><?php $this -> DBForm -> jpCombo("DepMemberOf_{$_dependent}",'select long',self::_get_member_of(),$this -> policy[1][$no]['MemberGroup'],null,1); ?> <span class="wrap"></td>
						</tr>
						<tr>
							<td class="header_table sunah">Title</td>
							<td><?php $this -> DBForm -> jpCombo("DepSalutationId_{$_dependent}",'select long', $this->Customer->Salutation(),$dp[$no]['SalutationId']); ?></td>
							<td class="header_table">Payment Mode</td>
							<td><span id="pay_plan_d_<?php echo $_dependent; ?>"><?php $this -> DBForm -> jpCombo("DepPaymode_{$_dependent}",'select long',$this -> Customer -> Paymode($this -> _productId),$dp[$no]['PayModeId'],"OnChange=getPremi(this);",($this -> policy[1][$no]['MemberGroup'] != 1?'1':'0')); ?> </span></td>
						</tr>
						<tr>
							<td class="header_table required"> * First Name</td>
							<td><?php $this -> DBForm -> jpInput("DepFirstName_{$_dependent}",'input long',$dp[$no]['InsuredFirstName']); ?></td>
							
							<td class="header_table">Plan Type</td>
							<td><span id="plan_plan_d_<?php echo $_dependent; ?>"><?php $this -> DBForm -> jpCombo("DepPlanType_{$_dependent}",'select long',$this -> Customer -> ProductPlan($this -> _productId),$dp[$no]['ProductPlan'],"OnChange=getPremi(this);",($this -> policy[1][$no]['MemberGroup'] != 1?'1':'0')); ?> </span></td>
						
						</tr>
						<tr>
							<td class="header_table sunah">Last Name</td>
							<td><?php $this -> DBForm -> jpInput("DepLastName_{$_dependent}",'input long',$dp[$no]['InsuredLastName']); ?></td>
							
							<td class="header_table">Premi</td>
							<td><?php $this -> DBForm -> jpInput("DepPremi_{$_dependent}",'input long',formatRupiah($dp[$no]['Premi']), null, 1); ?> <span class="wrap"> ( IDR ) </span></td>
						</tr>
						<tr>
							<td class="header_table sunah">Gender</td>
							<td><?php $this -> DBForm -> jpCombo("DepGenderId_{$_dependent}",'select long', $this->Customer->Gender(),$dp[$no]['GenderId']); ?></td>
						</tr>
						<tr>
							<td class="header_table sunah">DOB</td>
							<td><?php $this -> DBForm -> jpInput("DepDOB_{$_dependent}",'input date',$this->formatDateId($dp[$no]['InsuredDOB']), null, 1); ?></td>
						</tr>
						<tr>
							<td class="header_table sunah">Age</td>
							<td><?php $this -> DBForm -> jpInput("DepAge_{$_dependent}",'input',$dp[$no]['InsuredAge'], null, 1); ?></td>
						</tr>
					</table>	
				</fieldset><br>
			<?php 
				$no++;
			} ?>
			  
			  </form>
			  <?
		}
		
		function AXA_Payers()
		{
			?>
			<fieldset class="corner" style="margin-left:-5px;">
				<legend class="icon-application ">&nbsp;&nbsp;&nbsp;
					<b>Payer & information</b>
				</legend>	
				<form name="form_data_payer">
					<input type="hidden" name="PayerId" id="PayerId" value="<?php echo $this->_data['Payers']->result_get_value('PayerId'); ?>"/>
					<table width="100%" align="center" cellpadding="5px">	
						<tr>
							<td class="header_table required">* Title</td>
							<td><?php $this -> DBForm -> jpCombo("PayerSalutationId",'select long', $this -> Customer -> Salutation(),$this->_data['Payers']->result_get_value('SalutationId'));?></td>
							<td class="header_table required" nowrap>* First Name</td>
							<td><?php $this -> DBForm -> jpInput("PayerFirstName","input long",$this->_data['Payers']->result_get_value('PayerFirstName'));?></td>
							<td class="header_table" nowrap>Last Name</td>
							<td><?php $this -> DBForm -> jpInput("PayerLastName","input long",$this->_data['Payers']->result_get_value('PayerLastName'));?></td>
						</tr>
						<tr>
							<td class="header_table">Gender</td>
							<td><?php $this -> DBForm -> jpCombo("PayerGenderId",'select long',  $this -> Customer -> Gender(), $this->_data['Payers']->result_get_value('GenderId'));?></td>
							<td class="header_table">DOB</td>
							<td><?php $this -> DBForm -> jpInput("PayerDOB","input long date",$this->formatDateId($this->_data['Payers']->result_get_value('PayerDOB')));?></td>
							<td class="header_table">Address</td>
							<td><?php $this -> DBForm -> jpInput("PayerAddressLine1","input",$this->_data['Payers']->result_get_value('PayerAddressLine1'));?></td>
						</tr>
						<tr>
							<td class="header_table required">ID - Type </td>
							<td><?php $this -> DBForm -> jpCombo("PayerIdentificationTypeId","select long", $this -> Customer -> IndentificationId(), $this->_data['Payers']->result_get_value('IdentificationTypeId') );?></td>
							<td class="header_table required" >* ID No</td>
							<td><?php $this -> DBForm -> jpInput("PayerIdentificationNum","input long",$this->_data['Payers']->result_get_value('PayerIdentificationNum'));?></td>
						</tr>
						<tr>
							<td class="header_table">Mobile Phone</td>
							<td><?php $this -> DBForm -> jpInput("PayerMobilePhoneNum","input long",$this->_data['Payers']->result_get_value('PayerMobilePhoneNum'));?> </td>
							<td class="header_table">City</td>
							<td><?php $this -> DBForm -> jpInput("PayerCity","input long",$this->_data['Payers']->result_get_value('PayerCity'));?>  </td>
							<td class="header_table"></td>
							<td> <?php $this -> DBForm -> jpInput("PayerAddressLine2","input",$this->_data['Payers']->result_get_value('PayerAddressLine2'));?></td>
						</tr>	
						<tr>
							<td class="header_table">Home Phone </td>
							<td><?php $this -> DBForm -> jpInput("PayerHomePhoneNum","input long",$this->_data['Payers']->result_get_value('PayerHomePhoneNum'));?> </td>
							<td class="header_table">Zip</td>
							<td><?php $this -> DBForm -> jpInput("PayerZipCode","input long",$this->_data['Payers']->result_get_value('PayerZipCode'),null,0,5);?></td>
							<td class="header_table"></td>
							<td><?php $this -> DBForm -> jpInput("PayerAddressLine3","input",$this->_data['Payers']->result_get_value('PayerAddressLine3'));?></td>
						</tr>	
						<tr>
							<td class="header_table">Office Phone </td>
							<td><?php $this -> DBForm -> jpInput("PayerOfficePhoneNum", "input long",$this->_data['Payers']->result_get_value('PayerOfficePhoneNum'));?> </td>
							<td class="header_table">Province</td>
							<td><?php $this -> DBForm -> jpCombo("PayerProvinceId", 'select long',$this -> Customer -> Province(),$this->_data['Payers']->result_get_value('PayerProvinceId') );?></td>
							<td class="header_table"></td>
							<td><?php $this -> DBForm -> jpInput("PayerAddressLine4", "input",$this->_data['Payers']->result_get_value('PayerAddressLine4'));?>  </td>
						</tr>	
						<tr>
							<td class="header_table" valign="top">Card Number</td>
							<td valign="top"><?php $this -> DBForm -> jpInput("PayerCreditCardNum", "input long",$this->_data['Payers']->result_get_value('PayerCreditCardNum'),null,0,16);?><span id="error_message_html"></span></td>
							<td class="header_table">Bank</td>
							<td><?php $this -> DBForm -> jpCombo("PayersBankId", 'select long',$this -> Customer -> Bank(),$this->_data['Payers']->result_get_value('PayersBankId'));?></td>
							<td class="header_table">Fax Phone</td>
							<td><?php $this -> DBForm -> jpInput("PayerFaxNum", "input long",$this->_data['Payers']->result_get_value('PayerFaxNum'));?></td>
						</tr>	
						<tr>
							<td class="header_table " nowrap>Expiration Date</td>
							<td><?php $this -> DBForm -> jpInput("PayerCreditCardExpDate", "input long", $this->_data['Payers']->result_get_value('PayerCreditCardExpDate'), null);?><span class="wrap">&nbsp;(mm/yy)</span></td>
							<td class="header_table">Card Type</td>
							<td><?php $this -> DBForm -> jpCombo("CreditCardTypeId", 'select long',$this -> Customer -> CardType(), $this->_data['Payers']->result_get_value('CreditCardTypeId') );?></td>
							<td class="header_table">Email</td>
							<td><?php $this -> DBForm -> jpInput("PayerEmail", "input long",$this->_data['Payers']->result_get_value('PayerEmail'));?></td>
						</tr>	
					</table>
				</form>
			</fieldset> 
				
			<?php 
			//print_r($this->_data['Payers']->result_assoc());
		}
		
		function AXA_Benefiecery()
		{
			$no = 0;
			$bnf = $this->_data['Beneficery']->result_assoc();
			?>
			<form name="form_data_benefiecery">
			<?php
			for( $_benefiecery=1; $_benefiecery<=4; $_benefiecery++)
			{  ?>

				<fieldset class="corner" style="margin-left:-5px;">
					<input type="hidden" name="BenefId_<?php echo $_benefiecery; ?>" id="BenefId_<?php echo $_benefiecery; ?>" value="<?php echo $bnf[$no]['BeneficiaryId']; ?>"/>
					<legend class="icon-application ">&nbsp;&nbsp;&nbsp;
						<b>Benefiecery <?php echo $_benefiecery; ?></b>
						<?php $this -> DBForm -> jpCheck("Benefeciery",null,$_benefiecery,null,($this->_data['Beneficery']->result_num_rows() > $no ?'1':'0'),1);?>
					</legend>	
				
					<table cellpadding="5px"> 
						<tr>
							<td class="header_table">Relation</td>
							<td><?php $this -> DBForm -> jpCombo("BenefRelationshipTypeId_{$_benefiecery}",'select long',  $this -> Customer -> RelationshipType(),$bnf[$no]['RelationshipTypeId']); ?></td>
						</tr>
						<tr>
							<td class="header_table">Title</td>
							<td><?php $this -> DBForm -> jpCombo("BenefSalutationId_{$_benefiecery}",'select long', $this -> Customer -> Salutation(),$bnf[$no]['SalutationId']); ?></td>
						</tr>
						<tr>
							<td class="header_table required">* First Name</td>
							<td> <?php $this -> DBForm -> jpInput("BenefFirstName_{$_benefiecery}","input long",$bnf[$no]['BeneficiaryFirstName']); ?></td>
						</tr>
						<tr>
							<td class="header_table ">Last Name</td>
							<td><?php $this -> DBForm -> jpInput("BenefLastName_{$_benefiecery}","input long",$bnf[$no]['BeneficiaryLastName']); ?></td>
						</tr>
						<tr>
							<td class="header_table required">* Percentage</td>
							<td><?php $this -> DBForm -> jpInput("BenefPercentage_{$_benefiecery}","input long",$bnf[$no]['BeneficieryPercentage']); ?>&nbsp;<span class="wrap">( % )</span></td>
						</tr>
					</table>
				</fieldset><br>		
			<?php 
				$no++;
			}	 ?>		
			</form>
			<?php 
		}
		
		private function get_data_customer()
		{
			$datas = $this -> Customer -> DataPolicy( $this -> escPost('customerid') ); // data customer 
			if( !is_array($datas) ) return null;
			else
			{
				return $datas;
			}
		}
		
		private function getSplit()
		{
			$sql = "select a.PrefixMethod from t_gn_productprefixnumber a where a.ProductId='".$this -> _productId."'";
			$qry = $this->query($sql);
			
			if($qry -> result_singgle_value())
			{
				return $qry->result_get_value('PrefixMethod');
			}
			else{
				return false;
			}
		}
		
		function getPolicy()
		{
			$sql = "select a.*, b.PolicyNumber,c.MemberGroup from t_gn_insured a 
					left join t_gn_policy b on a.PolicyId = b.PolicyId 
					left join t_gn_policyautogen c on b.PolicyNumber = c.PolicyNumber
					where a.CustomerId = ".$this -> escPost('customerid');
			
			$qry = $this->query($sql);
			foreach($qry->result_assoc() as $rows)
			{
				$datas[$rows['PremiumGroupId']][] = $rows;
			}
			
			return $datas;
		}
		
		private function _get_member_of()
		{
			return array
			(
				'2'=>'Holder', // terikat dgn holder 
				'3'=>'Spouse', // terikat dgn spouse
				'1'=>'Self' /// dependent berdiri sendiri tidak terikat 
			);
		}
		
	}
	
	new AXA_EDIT();
?>