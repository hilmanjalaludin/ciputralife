<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.getfunction.php");
	require("../plugin/lib.form.php");
	
	class FormTpl extends mysql{
		
		var $Action;
		var $Payers;
		var $JP_Plugin;
		
		function __construct()
		{
			parent::__construct();
			$this -> Action 	= $this ->escPost('action');
			$this -> Payers 	= $this ->escPost('payers');
			$this -> JP_Plugin  = new jpForm(true);
		}
		
		function index()
		{
			switch( $this -> Action)
			{
				case 'get_form_payers' : $this -> tplFrmPayers(); break;
				case 'get_form_plan'   : $this -> getPlanByProduct(); break;
			}
		}
		
		function getContact(){
			$sql = " select * from t_gn_customer a	where a.CustomerId='".$this -> escPost('customerid')."' ";
			$qry = $this -> execute($sql,__FILE__,__LINE__);
			$res = $this -> fetchrow($qry);
			if( $res ){
				return $res;
			}
		}
		
		function getPlanByProduct()
		{
			$sql = "SELECT  distinct b.ProductPlanName,	 a.ProductPlanBenefit, a.ProductPlanBenefitDesc 
					from t_gn_productplanbenefit a 
					left join t_gn_productplan b on (a.ProductPlan=b.ProductPlan)
					where a.ProductId ='".$_REQUEST['productid']."'
					and a.ProductPlanBenefitStatusFlag=1";
			
			if( $this -> havepost('planbenef') ) $sql .=" AND a.ProductPlan ='".$_REQUEST['planbenef']."'";	
			
			$sql .= " order by b.ProductPlanName, a.ProductPlanBenefit";
			//echo $sql;
			?>
			<div style="height:300px;"> 
			<fieldset style="border:1px solid #dddddd;">
				<legend style="font-weight:bold;color:green;font-size:14px;"> Product Benefit </legend>
				<table align="left" width="100%" cellspacing="0" style="border-bottom:1px solid #dddddd;border-right:1px solid #dddddd;">
					<tr>
						<th style="text-align:left;padding:3px;border-top:1px solid #dddddd;border-left:1px solid #dddddd;background-color:#EEEEEE;font-size:12px;font-family:Arial;color:green;height:22px;"> Plan </th>
						<th style="text-align:left;padding:3px;border-top:1px solid #dddddd;border-left:1px solid #dddddd;background-color:#EEEEEE;font-size:12px;font-family:Arial;color:green;height:22px;"> Product Benefit</th>
						<th style="text-align:left;padding:3px;border-top:1px solid #dddddd;border-left:1px solid #dddddd;background-color:#EEEEEE;font-size:12px;font-family:Arial;color:green;height:22px;"> Description </th>
					</tr>	
			<?php	
				$qry = $this ->query($sql);
				//echo $sql;
				$i = 0;
				//echo $qry -> result_num_rows();
				foreach($qry -> result_assoc() as $rows )
				{
					$color= ($i%2!=0?'#FFFFFF':'#FFFCCC');
					?>
						<tr bgcolor="<?php echo $color;?>">
							<td width="12%" style="padding:3px;height:22px;font-size:12px;font-family:Arial;border-top:1px solid #dddddd;border-left:1px solid #dddddd;color:black" nowrap align="center"><strong style="color:red;"><?php echo $rows['ProductPlanName']; ?> </strong></td>
							<td style="padding:3px;height:22px;font-size:12px;font-family:Arial;border-top:1px solid #dddddd;border-left:1px solid #dddddd;color:black"><?php echo $rows['ProductPlanBenefit']; ?> </td>
							<td style="padding:3px;height:22px;font-size:12px;font-family:Arial;border-top:1px solid #dddddd;border-left:1px solid #dddddd;color:black"><?php echo $rows['ProductPlanBenefitDesc'];?> </td>
						</tr>	
					<?php
					$i++;
				}
			?>
			</table>	</fieldset></div>
			<?php	
		
		}
		
		
		
		function tplFrmPayers()
		{
		
			//print_r($_REQUEST);
			if( $this -> Payers==1){
				$this -> tplFrmPayers1();
			}
			else{
				$this -> tplFrmPayers2();
			}
		}
		
		function tplFrmPayers2(){
			global $getFunction; 
			$datas =  $this -> getContact();
			$BasicPayer  = $getFunction -> getBasicPayers();
			
			?>
				
				<table width="100%" align="center">	
					<tr>
						<td class="header-text wajib">Title</td>
						<td >
							<select name="payer_salutation" id="payer_salutation">
								<?php $getFunction->getSalutation( ($BasicPayer->SalutationId?$BasicPayer->SalutationId:$_REQUEST['payer_salutation']) ); ?>
							</select>
						</td>
						<td class="header-text wajib" >First Name</td>
						<td><input type="text" name="payer_first_name"  id="payer_first_name" class="input" value="<?php echo ($BasicPayer->PayerFirstName?$BasicPayer->PayerFirstName:$_REQUEST['payer_first_name']); ?>" ></td>
						<td class="header-text sunah">Last Name</td>
						<td><input type="text" name="payer_last_name"  id="payer_last_name" class="input" value="<?php echo ($BasicPayer->PayerLastName?$BasicPayer->PayerLastName:$_REQUEST['payer_last_name']); ?>" disabled></td>
					</tr>
						
					<tr>
						<td class="header-text wajib">Gender</td>
						<td ><?php $this -> JP_Plugin -> jpCombo('payer_gender',''); ?>
							<select name="payer_gender" id="payer_gender">
								<?php $getFunction->getGender( ($BasicPayer->GenderId?$BasicPayer->GenderId:$_REQUEST['payer_gender']) ); ?>
							</select>
						</td>
						<td class="header-text wajib">DOB</td>
						<td><input type="text" name="payer_dob" id="payer_dob" class="input" onclick="getFuckJquery(this.name);"
								value="<?php echo ($BasicPayer->PayerDOB?$BasicPayer->PayerDOB:$_REQUEST['payer_dob']); ?>"></td>
						<td class="header-text wajib">Address</td>
						<td><input type="text" name="payer_address1" id="payer_address1"  class="input" 
							value="<?php echo ($BasicPayer->PayerAddressLine1?$BasicPayer->PayerAddressLine1:$_REQUEST['payer_address1']); ?>"></td>
					</tr>
					
					<tr>
						<td class="header-text wajib">ID - Type </td>
						<td >
							<select id="payer_holder_idtype" name="payer_holder_idtype" style="width:120px;">
								<?php $getFunction->getIdType( ($BasicPayer->IdentificationTypeId?$BasicPayer->IdentificationTypeId:$_REQUEST['payer_holder_idtype']) ) ; ?>
							</select>
						</td>
						<td class="header-text wajib" >ID No</td>
						<td><input type="text" name="payer_idno" onkeyup="isNumber(this);" id="payer_idno" class="input" value="<?php echo ($BasicPayer->PayerIdentificationNum?$BasicPayer->PayerIdentificationNum:$_REQUEST['payer_idno']); ?>" ></td>
					</tr>

					<tr>
						<td class="header-text wajib">Mobile Phone</td>
						<td ><input type="text" name="payer_mobile_phone"  onkeyup="isNumber(this);" id="payer_mobile_phone" class="input" 
						value="<?php echo ($BasicPayer->PayerMobilePhoneNum?$BasicPayer->PayerMobilePhoneNum:$_REQUEST['payer_mobile_phone']); ?>"></td>
						<td class="header-text wajib">City</td>
						<td><input type="text" name="payer_city" id="payer_city" onkeyup="isStrValue(this);"  class="input" value="<?php echo ($BasicPayer->PayerCity?$BasicPayer->PayerCity:$_REQUEST['payer_city']); ?>"> </td>
						<td class="header-text wajib"></td>
						<td><input type="text" name="payer_address2" id="payer_address2" class="input" 
							value="<?php echo ($BasicPayer->PayerAddressLine2?$BasicPayer->PayerAddressLine2:$_REQUEST['payer_address2']); ?>"></td>
					</tr>	
					<tr>
						<td class="header-text wajib">Home Phone </td>
						<td ><input type="text" name="payer_home_phone" id="payer_home_phone" onkeyup="isNumber(this);" class="input" value="<?php echo ($BasicPayer->PayerHomePhoneNum?$BasicPayer->PayerHomePhoneNum:$_REQUEST['payer_home_phone']); ?>"></td>
						<td class="header-text wajib">Zip</td>
						<td><input type="text" name="payer_zip_code" id="payer_zip_code" onkeyup="isNumber(this);" class="input" maxlength="5"
						value="<?php echo ($BasicPayer->PayerZipCode?$BasicPayer->PayerZipCode:$_REQUEST['payer_zip_code']); ?>"></td>
						<td class="header-text wajib"></td>
						<td><input type="text" name="payer_address3" id="payer_address3"  class="input" 
							value="<?php echo($BasicPayer->PayerAddressLine3?$BasicPayer->PayerAddressLine3:$_REQUEST['payer_address3']); ?>"></td>
					</tr>	
					<tr>
						<td class="header-text wajib">Office Phone </td>
						<td ><input type="text" name="payer_office_phone" onkeyup="isNumber(this);" id="payer_office_phone" class="input" value="<?php echo ($BasicPayer->PayerWorkPhoneNum?$BasicPayer->PayerWorkPhoneNum:$_REQUEST['payer_office_phone'] ); ?>"></td>
						<td class="header-text wajib">Province</td>
						<td >
							<select name="payer_province" id="payer_province">
								<option value=""> -- Choose --</option>
								<?php $getFunction->getProvince( ($BasicPayer->ProvinceId?$BasicPayer->ProvinceId:$_REQUEST['payer_province']) ); ?>
							</select>
						</td>
						<td class="header-text wajib"></td>
						<td><input type="text" name="payer_address4" id="payer_address4" class="input" 
							value="<?php echo ($BasicPayer->PayerAddressLine4?$BasicPayer->PayerAddressLine4:$_REQUEST['payer_address4']); ?></td>
					</tr>	
					<tr>
						<td class="header-text wajib" valign="top">Card Number</td>
						<td valign="top">
							<input type="text" maxlength="16" name="payer_card_number" id="payer_card_number" value="<?php echo ($BasicPayer->PayerCreditCardNum?$BasicPayer->PayerCreditCardNum:$_REQUEST['payer_card_number']); ?>" onkeyup="getNexValidationCard(this.value);" onkeyup="isNumber(this);" class="input">
						<span id="error_message_html"></span></td>
						<td class="header-text sunah">Bank</td>
						<td >
							<select name="payer_bank" id="payer_bank">
								<option value=""> -- Choose --</option>
								<?php $getFunction->getBanking( ($BasicPayers->ValidCCPrefixId?$BasicPayers->ValidCCPrefixId:$_REQUEST['payer_bank']) ); ?>
							</select>
						</td>
						<td class="header-text sunah">Fax Phone</td>
						<td><input type="text" name="payer_fax_number" onkeyup="isNumber(this);" id="payer_fax_number" class="input" 
								value="<?php echo ($BasicPayer->PayerFaxNum?$BasicPayer->PayerFaxNum:$_REQUEST['payer_fax_number']); ?>"></td>
					</tr>	
					<tr>
						<td class="header-text wajib">Expiration Date</td>
						<td ><input type="text" name="payer_expired_date" id="payer_expired_date" onKeyup="cekExpiredDate(this);" class="input" value="<?php echo ($BasicPayer->PayerCreditCardExpDate?$BasicPayer->PayerCreditCardExpDate:$_REQUEST['payer_expired_date']); ?>"> (mm/yy)</td>
						<td class="header-text wajib">Card Type</td>
						<td >
							<select name="payer_card_type" id="payer_card_type" >
								<?php $getFunction->getCardType( ($BasicPayer->CreditCardTypeId?$BasicPayer->CreditCardTypeId:$_REQUEST['payer_card_type']) ); ?>
							</select>
						</td>
						<td class="header-text sunah">Email</td>
						<td><input type="text" name="payer_email" id="payer_email" class="input" 
						value="<?php echo ($BasicPayer->PayerEmail?$BasicPayer->PayerEmail:$_REQUEST['payer_email']); ?>"></td>
					</tr>	
					</table>
			<?php
			}
			
		
		function tplFrmPayers1(){
		
			global $getFunction; 
			$datas =  $this -> getContact();
			$BasicPayer  = $getFunction -> getBasicPayers();
			
			?>
				<table width="100%" align="center">	
					<tr>
						<td class="header-text wajib">Title</td>
						<td ><?php $this -> JP_Plugin -> jpCombo('payer_salutation',NULL, $getFunction->getSalutation(),($_REQUEST['payer_salutation']?$_REQUEST['payer_salutation']:$BasicPayer->SalutationId)); ?></td>
						<td class="header-text wajib" >First Name</td>
						<td><input type="text" name="payer_first_name"  id="payer_first_name" class="input" value="<?php echo ($_REQUEST['payer_first_name']?$_REQUEST['payer_first_name']:$datas->CustomerFirstName); ?>"  disabled=true></td>
						<td class="header-text sunah">Last Name</td>
						<td><input type="text" name="payer_last_name" id="payer_last_name" class="input" value="<?php echo ($_REQUEST['payer_last_name']?$_REQUEST['payer_last_name']:$datas->CustomerLastName); ?>" ></td>
					</tr>	
					<tr>
						<td class="header-text wajib">Gender</td>
						<td ><?php $this -> JP_Plugin -> jpCombo('payer_gender',NULL, $getFunction->getGender(),($_REQUEST['payer_gender']?$_REQUEST['payer_gender']:$BasicPayer->GenderId)); ?></td>
						<td class="header-text wajib">DOB</td>
						<td><input type="text" name="payer_dob" id="payer_dob" class="input" onclick="getFuckJquery(this.name);" value="<?php echo ($_REQUEST['payer_frm_holder_dob']?$_REQUEST['payer_frm_holder_dob']:($this ->formatDateId($datas->CustomerDOB)?$this ->formatDateId($datas->CustomerDOB):$_REQUEST['payer_frm_holder_dob'])); ?>"></td>
						<td class="header-text wajib">Address</td>
						<td><input type="text" name="payer_address1" id="payer_address1"  class="input" value="<?php echo ($_REQUEST['payer_address1']?$_REQUEST['payer_address1']:($_REQUEST['frm_payer']?'':$datas->CustomerAddressLine1)); ?>"></td>
					</tr>
					
					<tr>
						<td class="header-text wajib">ID - Type </td>
						<td ><?php $this -> JP_Plugin -> jpCombo('payer_holder_idtype',NULL, $getFunction->getIdType(),($BasicPayer->IdentificationTypeId?$BasicPayer->IdentificationTypeId:$_REQUEST['payer_holder_idtype'])); ?>
							<!--<select id="payer_holder_idtype" name="payer_holder_idtype" style="width:120px;">
								<#?php $getFunction->getIdType( ($BasicPayer->IdentificationTypeId?$BasicPayer->IdentificationTypeId:$_REQUEST['payer_holder_idtype']) ) ; ?>
							</select>-->
						</td>
						<td class="header-text wajib" >ID No</td>
						<td><input type="text" name="payer_idno" onkeyup="isNumber(this);" id="payer_idno" class="input" value="<?php echo ($BasicPayer->PayerIdentificationNum?$BasicPayer->PayerIdentificationNum:$_REQUEST['payer_idno']); ?>" ></td>
					</tr>
					
					<tr>
						<td class="header-text wajib">Mobile Phone</td>
						<td ><input type="text" name="payer_mobile_phone" onchange="" onkeyup="isNumber(this);" id="payer_mobile_phone" class="input" value="<?php echo ($_REQUEST['payer_mobile_phone']?$_REQUEST['payer_mobile_phone']:($_REQUEST['frm_payer']?'':$datas->CustomerMobilePhoneNum)); ?>"></td>
						<td class="header-text wajib">City</td>
						<td><?php $this -> JP_Plugin -> jpInput('payer_city','input',($_REQUEST['payer_city']?$_REQUEST['payer_city']:$BasicPayer->PayerCity),'onkeyup="isStrValue(this);"'); ?>
						<td class="header-text wajib"></td>
						<td><input type="text" name="payer_address2" id="payer_address2" class="input" value="<?php echo ($_REQUEST['payer_address2']?$_REQUEST['payer_address2']:($_REQUEST['frm_payer']?'':$datas->CustomerAddressLine2)); ?>"></td>
					</tr>	
					<tr>
						<td class="header-text wajib">Home Phone </td>
						<td ><input type="text" name="payer_home_phone" id="payer_home_phone" onkeyup="isNumber(this);" class="input" value="<?php echo ($_REQUEST['payer_home_phone']?$_REQUEST['payer_home_phone']:($_REQUEST['frm_payer']?'':$datas->CustomerHomePhoneNum)); ?>"></td>
						<td class="header-text wajib">Zip</td>
						<td><input type="text" name="payer_zip_code" id="payer_zip_code" onkeyup="isNumber(this);" class="input" maxlength="5" value="<?php echo ($_REQUEST['payer_zip_code']?$_REQUEST['payer_zip_code']:($this -> Payers==1?($BasicPayer->PayerZipCode?$BasicPayer->PayerZipCode:$datas->CustomerZipCode):$BasicPayer->PayerZipCode)); ?>"></td>
						<td class="header-text wajib"></td>
						<td><input type="text" name="payer_address3" id="payer_address3"  class="input" value="<?php echo ($_REQUEST['payer_address3']?$_REQUEST['payer_address3']:($_REQUEST['frm_payer']?'':$datas->CustomerAddressLine3)); ?>"></td>
					</tr>	
					<tr>
						<td class="header-text wajib">Office Phone </td>
						<td ><input type="text" name="payer_office_phone" onkeyup="isNumber(this);" id="payer_office_phone" class="input" value="<?php echo ($_REQUEST['payer_office_phone']?$_REQUEST['payer_office_phone']:($_REQUEST['frm_payer']?'':$datas->CustomerWorkPhoneNum)); ?>"></td>
						<td class="header-text wajib">Province</td>
						<td ><?php $this -> JP_Plugin -> jpCombo('payer_province',NULL, $getFunction->getProvince(),($_REQUEST['payer_province']?$_REQUEST['payer_province']:$BasicPayer->ProvinceId)); ?></td>
						<td class="header-text wajib"></td>
						<td><input type="text" name="payer_address4" id="payer_address4" class="input" value="<?php echo ($_REQUEST['payer_address4']?$_REQUEST['payer_address4']:($_REQUEST['frm_payer']?'':$datas->CustomerAddressLine4)); ?>"></td>
					</tr>	
					<tr>
						<td class="header-text wajib" valign="top">Card Number</td>
						<td valign="top"><?php $this -> JP_Plugin -> jpInput('payer_card_number','input',($_REQUEST['payer_card_number']?$_REQUEST['payer_card_number']:$BasicPayer->PayerCreditCardNum),'onkeyup="getNexValidationCard(this.value);"',FALSE,16); ?>
						<span id="error_message_html"></span></td>
						<td class="header-text sunah">Bank</td>
						<td ><?php $this -> JP_Plugin -> jpCombo('payer_bank',NULL, $getFunction->getBanking(),($_REQUEST['payer_bank']?$_REQUEST['payer_bank']:$BasicPayer->PayersBankId));?></td>
						<td class="header-text sunah">Fax Phone</td>
						<td><input type="text" name="payer_fax_number" onkeyup="isNumber(this);" id="payer_fax_number" class="input"  value="<?php echo ($_REQUEST['payer_fax_number']?$_REQUEST['payer_fax_number']:$BasicPayer->PayerFaxNum); ?>"></td>
					</tr>	
					<tr>
						<td class="header-text wajib">Expiration Date</td>
						<td><?php $this -> JP_Plugin -> jpInput('payer_expired_date','input',($_REQUEST['payer_expired_date']?$_REQUEST['payer_expired_date']:$BasicPayer->PayerCreditCardExpDate),'onkeyup="cekExpiredDate(this);"',FALSE,5); ?> (mm/yy)</td>
						<td class="header-text wajib">Card Type</td>
						<td ><?php $this -> JP_Plugin -> jpCombo('payer_card_type',NULL, $getFunction->getCardType(),($_REQUEST['payer_card_type']?$_REQUEST['payer_card_type']:$BasicPayer->CreditCardTypeId)); ?></td>
						<td class="header-text sunah">Email</td>
						<td><input type="text" name="payer_email" id="payer_email" class="input" value="<?php echo ($_REQUEST['payer_email']?$_REQUEST['payer_email']:$BasicPayer->PayerEmail); ?>"></td>
					</tr>	
					</table>
			<?php
			}
	}	
	
	$FormTpl = new FormTpl();
	$FormTpl -> index();
	