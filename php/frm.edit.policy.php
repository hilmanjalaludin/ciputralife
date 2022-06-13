<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.application.php");
	require("../class/class.main.menu.php");
	require("../class/class.getfunction.php");
	require('../sisipan/parameters.php');
	require("../plugin/lib.form.php");
	
	/* get info plan **/
	
	$getPlan	 = $getFunction -> getPlanEdit();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- start Link : css --> 
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta content="utf-8" http-equiv="encoding">
	<title>.: Edit Policy :.</title>
	
	<link type="text/css" rel="stylesheet" href="<?php echo $app->basePath();?>gaya/other.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo $app->basePath();?>pustaka/jquery/jquery-ui-themes-1.7.2/themes/<?php echo $Themes->V_UI_THEMES;?>/ui.all.css" />	
	
 
 <!-- stop Link : css -->
	
 <!-- start Link : Javascript -->
	
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/jquery-1.3.2.js"></script>    
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/jquery-ui-1.7.2/ui/jquery-ui.js"></script>
    <script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/jquery-ui-1.7.2/external/bgiframe/jquery.bgiframe.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
	
	<script>
		
		var CcCardValid = false;
		$(document).ready(function() {
		//	$("#tabs" ).tabs();
			
			$("#frm_holder_dob,#payer_dob").datepicker({ dateFormat: 'dd-mm-yy',
				buttonImage: '../gambar/calendar.gif', 
				buttonImageOnly: true,
				changeMonth: true,
				changeYear: true,
				yearRange: '1945:2030',
				onSelect:function(date){
					toRenderValue(date,$(this).attr('name'),2)
				}
			});
			
			
		var isCheck = doJava.dom('chekclist');
				doJava.File  = '../class/class.frm.tpl.php';
				doJava.Params= {
					action:'get_form_payers',
					customerid:CustomerId,
					payers:(isCheck.checked?1:0)
					}
				
				
		
			
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
		
	var getUmurSizeFunc  = function(fo,to,init){
		$(document).ready(function() {
			$("#"+fo).datepicker({ dateFormat: 'dd-mm-yy',
					buttonImage: '../gambar/calendar.gif', 
					buttonImageOnly: true,
					changeMonth: true,
					changeYear: true,
					yearRange: '1945:2030',
					onSelect:function(date){
						var umurSize = AjaxGetSizeAge(date,init);
						if( umurSize!='' ){
							$('#'+to).val(umurSize.trim());
						}
						else{
							alert('Age is not valid!');
							doJava.dom(to).value='';
						}
					}
			});
		});
	}	
		
		var CustomerId 		= '<?php echo $db -> escPost('customerid'); ?>';
		var CampaignId 		= '<?php echo $db -> escPost('campaignid'); ?>';
		var interestStatus 	= '<?php echo $db -> escPost('callstatus'); ?>'; 
		var initGroup 		= false;
		var	iniatedUmur 	= '';
		var crosscek 		= false;
		
/* get by tag name **/

	var getTagNameInput = function(){
		var frmEl= document.getElementsByTagName('input');
		var frmString = '';
			for(var i in frmEl){
				frmString +="&"+frmEl[i].name+"="+frmEl[i].value;  
			}
		return frmString.substring(1,frmString.length);
	}

/* get by tag select **/
	
	var getTagNameSelect = function(){
		var frmEl= document.getElementsByTagName('select');
		var frmString = '';
			for(var i in frmEl){
				frmString +="&"+frmEl[i].name+"="+frmEl[i].value;  
			}
		return frmString;
	}


/* validation field tabs 4 **/

		
	
						
/* validation field tabs 4 **/
		
		var getUmurSize = function(){
			if( iniatedUmur!=''){
				var iniatedUmurx = iniatedUmur.trim().split(" ");
				return (iniatedUmurx[0]?iniatedUmurx[0]:<?php echo $getPlan->InsuredAge;?>)
			}
			else{
				return false;
			}
		}
		
/* validation field tabs 4 **/
		
		var getPlanByProduct = function(productid){
			 doJava.File = '../class/class.edit.policy.php';
					doJava.Params = {
						action	: 'get_plan_customer',
						productid : productid
				}
				var error = doJava.Post();
					doJava.dom('html_inner_plan').innerHTML= error;
		}
		
/* validation field tabs 4 **/
		
		var AjaxGetSizeAge = function(date,init){
				doJava.File = '../class/class.edit.policy.php';
				doJava.Params = {
					action	: 'hitung_dob_customer',
					user_dob : date,
					init:init
				}
				var ret = doJava.Post();
				if( ret==0){
					return '';
				}
				else{ return ret; }
		}
		
/* validation field tabs 4 **/
		
		var toRenderValue = function(date,name,init){	
			if( name =='frm_holder_dob'){
				doJava.File = '../class/class.edit.policy.php';
				doJava.Params = {
					action	: 'hitung_dob_customer',
					user_dob : date,
					init:init
				}
				var error = doJava.Post();
				if( error==0){
					alert("Age is not valid!");
					doJava.dom('text_dob_size').innerHTML= '';
				}
				else{	
					iniatedUmur = error;
					doJava.dom('text_dob_size').innerHTML= error;
				}	
			}
		}  
		
		
/* validation field tabs 4 **/
		
		var getValueTabs1=function (){
			var cb_holder_holdertype    = doJava.dom('cb_holder_holdertype');
			var frm_holder_firstname 	= doJava.dom('frm_holder_firstname');
			var frm_holder_lastname 	= doJava.dom('frm_holder_lastname');
			var cb_holder_idtype		= doJava.dom('cb_holder_idtype');
			var frm_holder_rel			= doJava.dom('frm_holder_rel');
			var frm_holder_dob			= doJava.dom('frm_holder_dob');
			var frm_holder_idno			= doJava.dom('frm_holder_idno');
			var frm_holder_gender		= doJava.dom('frm_holder_gender');
			var frm_holder_title		= doJava.dom('frm_holder_title');
			
			if(cb_holder_holdertype.value=='' ){ cb_holder_holdertype.style.borderColor='red'; return false;}
			else if( frm_holder_firstname.value==''){ frm_holder_firstname.style.borderColor='red'; return false;}
			else if( frm_holder_rel.value==''){ frm_holder_rel.style.borderColor='red'; return false;}
			else if( frm_holder_gender.value==''){ alert('Gender cannot be empty!'); frm_holder_gender.style.borderColor='red'; return false; }
			else if( frm_holder_dob.value==''){ alert('Please select DOB!');  frm_holder_dob.style.borderColor='red'; return false;}
			else if( frm_holder_title.value==''){ alert('Title cannot be empty!'); frm_holder_title.style.borderColor='red'; return false;}
			else if( iniatedUmur==''){ frm_holder_dob.focus(); return false;}
			else { return true;}
		}
		
/* validation field tabs 4 **/
		
		var getValueTabs2 = function(){
			var cb_insurance_sp_holdertype 	= doJava.dom('cb_insurance_sp_holdertype');
			var cb_insurance_sp_idtype 		= doJava.dom('cb_insurance_sp_idtype');
			var txt_insurance_sp_idno 		= doJava.dom('txt_insurance_sp_idno');
			var cb_insurance_sp_relation 	= doJava.dom('cb_insurance_sp_relation');
			var cb_insurance_sp_salut 		= doJava.dom('cb_insurance_sp_salut');
			var txt_insurance_sp_firstname 	= doJava.dom('txt_insurance_sp_firstname');
			var txt_insurance_sp_lastname  	= doJava.dom('txt_insurance_sp_lastname');
			var txt_insurance_sp_gender 	= doJava.dom('txt_insurance_sp_gender');
			var txt_insurance_sp_dob 		= doJava.dom('txt_insurance_sp_dob');
			
				if( interestStatus =='402'){
					if( cb_insurance_sp_idtype.value==''){ cb_insurance_sp_idtype.focus();return false;}
					else if( txt_insurance_sp_idno.value==''){ txt_insurance_sp_idno.focus();return false;}
					else { 
						return true;
					}	
				}
				else{
					return true;
				}
		} 
		
/* validation field tabs 4 **/
		
		var getValueTabs3 = function(){

			var plan_product_id = doJava.dom('plan_product_id');
			var plan_plan 		= doJava.dom('plan_plan');
			var plan_paymode 	= doJava.dom('plan_paymode');
			var plan_paytype 	= doJava.dom('plan_paytype');
			var plan_premi      = doJava.dom('plan_premi'); 
			
			if( plan_product_id.value =='') {plan_product_id.focus(); return false}
			else if( plan_plan.value =='') {plan_plan.focus(); return false}
			else if( plan_paymode.value =='') {plan_paymode.focus(); return false}
			else if( plan_paytype.value =='') {plan_paytype.focus(); return false}
			else{
				return true;
			}
		} 
		
/* validation field tabs 4 **/
		
		var clearTextAge = function(string){
			var ArrString = string.split(' ');
				return ArrString[0].trim();
		}
		
/* validation field tabs 4 **/
		
		var getPrameterInsurance = function(){
			var inCheckBoxList = doJava.checkedValue('cbx_ins_folow');
			var insText = inCheckBoxList.split(',');
			var holder_type = new Array();
			var holder_Age  = '';
				
			if( inCheckBoxList.length >0 ){
				for( var i in insText )
				{
					if( insText[i]=='0')
					{
						holder_Age+= "~"+doJava.dom('cb_insurance_sp_holdertype').value+"|"+ clearTextAge(doJava.dom('txt_insurance_sp_age').value);
					}
					else
					{
						holder_Age+= "~"+doJava.dom('cb_insurance_dp'+insText[i]+'_holdertype').value+"|"+clearTextAge(doJava.dom('txt_insurance_dp'+insText[i]+'_age').value);
					}	
				}
				return holder_Age;
			}	
			
		}  	

/* validation field tabs 4 **/
		
		var getValueTabs4= function(){			
				var payer_salutation = doJava.dom('payer_salutation');
				var payer_first_name = doJava.dom('payer_first_name');
				var payer_last_name = doJava.dom('payer_last_name');
				var payer_gender = doJava.dom('payer_gender');
				var payer_dob = doJava.dom('payer_dob');
				var payer_address1= doJava.dom('payer_address1');
				var payer_mobile_phone = doJava.dom('payer_mobile_phone');
				var payer_city = doJava.dom('payer_city');
				var payer_address2 = doJava.dom('payer_address2');
				var payer_home_phone = doJava.dom('payer_home_phone');
				var payer_zip_code = doJava.dom('payer_zip_code');
				var payer_address3 = doJava.dom('payer_address3');
				var payer_office_phone = doJava.dom('payer_office_phone');
				var payer_province = doJava.dom('payer_province');
				//var payer_card_prefix = doJava.dom('payer_card_prefix');
				var payer_card_number = doJava.dom('payer_card_number');
				var payer_bank =doJava.dom('payer_bank');
				var payer_fax_number = doJava.dom('payer_fax_number');
				var payer_expired_date= doJava.dom('payer_expired_date');
				var payer_card_type = doJava.dom('payer_card_type');
				var payer_email = doJava.dom('payer_email');
				
								
				if( payer_salutation.value==''){alert('Payer Title cannot be empty!'); return false;}
				else if( payer_first_name.value==''){alert('Payer First Name cannot be empty!'); return false;}
				else if( payer_gender.value==''){ alert('Payer Gender cannot be empty!'); return false;}
				else if( payer_dob.value==''){alert('Payer DOB cannot be empty!'); return false;}
				else if( payer_address1.value==''){ alert('Payer Address cannot be empty!'); return false;}
				else if( payer_mobile_phone.value==''){ alert('Mobile Phone cannot be empty!'); return false;}
				else if( payer_city.value==''){ alert('Payer City cannot be empty!'); return false;}
				else if( payer_home_phone.value==''){ alert('Payer Home Phone cannot be empty!'); return false;}
				else if( payer_zip_code.value==''){ alert('Payer Zip Code cannot be empty!'); return false;}
				else if( payer_office_phone.value==''){ alert('Office Phone cannot be empty!'); return false;}
				else if( payer_province.value==''){ alert('Payer Province cannot be empty!'); return false;}
				else if( payer_card_number.value==''){alert('Payer Card Number cannot be empty!'); return false;}
				else if( payer_expired_date.value==''){ alert('Payer Expiration Date cannot be empty!'); return false;}
				else if( payer_card_type.value==''){ alert('Payer Card Type cannot be empty!'); return false;}
				else if(payer_expired_date.value.indexOf("/") < 0){ alert('Payer Expiration Date is not valid'); return false;}
				else if(payer_expired_date.value.length !=5 ){ alert('Payer Expiration Date is less than 5'); return false;}
				//else if(payer_card_prefix.value.lenght<6){  alert('Payer Card Prefix is less than 6'); return false; }
				else if(payer_card_number.value.length < 9){  alert('Payer Card Number is less than 9'); return false; }
				else{
					
					if( CcCardValid){ return true; }
					else { 
						alert('Card Number is not valid!')
						return false; 
					}
				}

		}
		
/* validate next polish **/
		
		var getPremiByPlan=function(planid)
		{
			
			
			var campaignid 	= "<?php echo $db->escPost('campaignid');?>";
			var groupType 	= doJava.dom("cb_holder_holdertype").value; 	
			var umur_user  	= parseInt(getUmurSize());
			var productid  	= doJava.dom("plan_product_id").value;
			var paymode	    = doJava.dom("plan_paymode").value;
			var URL_STRING  = groupType+"|"+umur_user+" "+getPrameterInsurance();
			
			if( paymode=='') { return false;}
			else
			{
				doJava.File   = "../class/class.edit.policy.php";
				doJava.Params = 
				{
					action	   : "hitung_premi_customer",
					urlstring  : URL_STRING,
					planid	   : planid, 	
					campaignid : campaignid,
					productid  : productid,
					paymode    : paymode,
					groupType  : groupType
				}
				
				var error = doJava.Post();
					doJava.dom('plan_premi').value = error.trim();
				
				//doJava.dom('plan_premi').disabled =true
			}
		}
		
		
	var getPremiByPlanMode = function(value){
			
			var campaignid 	= "<?php echo $db->escPost('campaignid');?>";
			var groupType 	= doJava.dom("cb_holder_holdertype").value; 	
			var umur_user  	= parseInt(getUmurSize());
			var productid  	= doJava.dom("plan_product_id").value;
			var paymode	    = value;
			var URL_STRING  = groupType+"|"+umur_user+" "+getPrameterInsurance();
			
			if( paymode=='') { return false;}
			else
			{
				doJava.File   = "../class/class.edit.policy.php";
				doJava.Params = 
				{
					action	   : "hitung_premi_customer",
					urlstring  : URL_STRING,
					planid	   : doJava.dom('plan_paymode').value, 	
					campaignid : campaignid,
					productid  : productid,
					paymode    : paymode,
					groupType  : groupType
				}
				
				var error = doJava.Post();
					doJava.dom('plan_premi').value = error.trim();
				
				//doJava.dom('plan_premi').disabled =true
			}
	
	}	
	
/* validate next polish **/
	
	UpdateCustomer = function(CustomerId){
		if( CustomerId!=''){
		
			var insuranceBox  = doJava.checkedValue('cbx_ins_folow');
			var benefBox 	  = doJava.checkedValue('benef_box');
			var holderIsPayer = doJava.dom('chekclist');
			var datas 		  = getTagNameInput()+" "+getTagNameSelect()+
								"&insuranceBox="+insuranceBox+"&main_cust_policy_number="+doJava.dom('main_cust_policy_number').value+
								"&benefBox="+benefBox+"&holder_age="+getUmurSize()+
								"&holderIsPayer="+(holderIsPayer.checked?1:0);
			
				doJava.File  = '../class/class.edit.policy.php';
				doJava.Params = {
					action 		:'update_policy',
					datas 		: datas,
					customerid 	: CustomerId,
					campaignid 	: CampaignId	
				} 
				
			
			if( validBenefiecery() ){	
				var error = doJava.Post();
					if( error!=0){
						alert("Success updating the policy..")
						doJava.dom('main_cust_policy_number').value=error.trim();
						doJava.dom('main_cust_policy_number').disabled=false;
						doJava.dom('main_cust_policy_number').style.borderColor='1px solid red';
					}
					else
						alert('Failed, no prefix number for this product, contact your system administrator');
			}
			else{
				alert('Input is not complete!');
			}
		}
	}	

		
/* validation field tabs 4 **/
		
		var showPayer = function(){
			var isCheck = doJava.dom('chekclist');
			doJava.File  = '../class/class.frm.tpl.php';
			doJava.Params= {
				action:'get_form_payers',
				customerid:CustomerId,
				payers:(isCheck.checked?1:0),
				payer_salutation :doJava.dom('payer_salutation').value.trim(),
				payer_first_name :doJava.dom('payer_first_name').value.trim(),
				payer_last_name :doJava.dom('payer_last_name').value.trim(),
				payer_gender :doJava.dom('payer_gender').value.trim(),
				payer_dob :doJava.dom('payer_dob').value.trim(),
				payer_address1:doJava.dom('payer_address1').value.trim(),
				payer_mobile_phone :doJava.dom('payer_mobile_phone').value.trim(),
				payer_city :doJava.dom('payer_city').value.trim(),
				payer_address2 :doJava.dom('payer_address2').value.trim(),
				payer_home_phone :doJava.dom('payer_home_phone').value.trim(),
				payer_zip_code :doJava.dom('payer_zip_code').value.trim(),
				payer_address3 :doJava.dom('payer_address3').value.trim(),
				payer_office_phone :doJava.dom('payer_office_phone').value.trim(),
				payer_province :doJava.dom('payer_province').value.trim(),
				payer_card_number :doJava.dom('payer_card_number').value.trim(),
				payer_bank :doJava.dom('payer_bank').value.trim(),
				payer_fax_number :doJava.dom('payer_fax_number').value.trim(),
				payer_expired_date:doJava.dom('payer_expired_date').value.trim(),
				payer_card_type :doJava.dom('payer_card_type').value.trim(),
				payer_email :doJava.dom('payer_email').value.trim(),
				//payer_card_prefix:doJava.dom('payer_card_prefix').value.trim()
			}
			doJava.Load('tabs-4');
			
		}

/* validation field tabs 4 **/

		var isNumber = function(obj){
			var Lstring;
			var Lconstant;
			var Rstring;
				Lstring = obj.value.length;
				Lconstant = (obj.value.length -1)
				Rstring = obj.value.substring(Lconstant,Lstring)
				
			if(isNaN(Rstring)){
				obj.value = obj.value.substring(0,obj.value.length-1);
			}
		} 

/* validation field tabs 4 **/		
	
		var isStrValue = function(obj){
			var Lstring;
			var Lconstant;
			var Rstring;
				Lstring = obj.value.length;
				Lconstant = (obj.value.length -1)
				Rstring = obj.value.substring(Lconstant,Lstring)
			if(!isNaN(Rstring)){
				obj.value = obj.value.substring(0,obj.value.length-1);
			}
		} 
		
/* validation field tabs 4 **/
		
		var validIputInsured = function(){
				var validInsurance= false;
				var inCheckBox = doJava.checkedValue('cbx_ins_folow');
				var cb_insurance_sp_holdertype 	= doJava.dom('cb_insurance_sp_holdertype');
				var cb_insurance_sp_idtype 		= doJava.dom('cb_insurance_sp_idtype');
				var txt_insurance_sp_idno 		= doJava.dom('txt_insurance_sp_idno');
				var cb_insurance_sp_relation 	= doJava.dom('cb_insurance_sp_relation');
				var cb_insurance_sp_salut 		= doJava.dom('cb_insurance_sp_salut');
				var txt_insurance_sp_firstname 	= doJava.dom('txt_insurance_sp_firstname');
				var txt_insurance_sp_lastname  	= doJava.dom('txt_insurance_sp_lastname');
				var txt_insurance_sp_gender 	= doJava.dom('txt_insurance_sp_gender');
				var txt_insurance_sp_dob 		= doJava.dom('txt_insurance_sp_dob');
				var txt_insurance_sp_age        = doJava.dom('txt_insurance_sp_age');
			
			if( inCheckBox.length >0 ){	
				var listBox = inCheckBox.split(',');
				
				for( var i in listBox ){
					if( listBox[i]=='0') 
					{
						cb_insurance_sp_holdertype.disabled = true;
						if( cb_insurance_sp_idtype.value=='' ) { alert('ID Type cannot be empty!'); return false;}
						else if( txt_insurance_sp_idno.value=='' ) { alert('ID No cannot be empty!'); return false;}
						else if( cb_insurance_sp_relation.value==''){ alert('Relation cannot be empty!'); return false;}
						else if( cb_insurance_sp_salut.value==''){alert('Title cannot be empty!'); return false;}
						else if( txt_insurance_sp_firstname.value==''){ alert('First Name cannot be empty!'); return false;}
						else if( txt_insurance_sp_gender.value==''){ alert('Gender cannot be empty!'); return false;}
						else if( txt_insurance_sp_dob.value==''){ alert('DOB cannot be empty!'); return false;}
						else if( txt_insurance_sp_age.value==''){ alert('Age cannot be empty!'); return false;}
						else{
							validInsurance=true;
						}
					}
					if( listBox[i]!=0){
						cb_insurance_sp_holdertype.disabled=true; 
						if( doJava.dom('cb_insurance_dp'+listBox[i]+'_holdertype').value ==''){ alert('Dependent '+listBox[i]+' :: Holder type cannot be empty!') }
						else if( doJava.dom('cb_insurance_dp'+listBox[i]+'_rel').value==''){ alert('Dependent '+listBox[i]+' :: Relation cannot be empty!')}
						else if( doJava.dom('cb_insurance_dp'+listBox[i]+'_salut').value==''){ alert('Dependent '+listBox[i]+' :: Title cannot be empty! ')}
						else if( doJava.dom('txt_insurance_dp'+listBox[i]+'_firstname').value==''){ alert('Dependent '+listBox[i]+' :: First Name cannot be empty!')}
						//else if( doJava.dom('txt_insurance_dp'+listBox[i]+'_lastname').value==''){ alert('Dependent '+listBox[i]+' :: Last Name is empty..!')}
						else if( doJava.dom('cb_insurance_dp'+listBox[i]+'_gender').value==''){ alert('Dependent_gender'+listBox[i]+' :: Gender cannot be empty!')}
						else if( doJava.dom('txt_insurance_dp'+listBox[i]+'_dob').value==''){ alert('Dependent_dob'+listBox[i]+' :: DOB cannot be empty!')}
						else if( doJava.dom('txt_insurance_dp'+listBox[i]+'_age').value==''){ alert('Dependent_age'+listBox[i]+' :: Age cannot be empty!')}
						else {
								validInsurance=true;
							}	
					}	
				}	
			}
			else { validInsurance=true; }
			
			
			return validInsurance;
		}
		
/* validation field tabs 4 **/
		
		var validBenefiecery = function(){
			var benefValue = doJava.checkedValue('benef_box');
				if( benefValue.length >0 ){
					var ListView = benefValue.split(',');
				
					for(var i in ListView)
					{
						if( doJava.dom('txt_benef'+ListView[i]+'_holdertype').value==''){ alert('Beneficiery '+ListView[i]+' :: Holder Type is empty');}
						else if( doJava.dom('txt_benef'+ListView[i]+'_rel').value==''){ alert('Beneficiery '+ListView[i]+' :: Relation is empty');}
						else if( doJava.dom('txt_benef'+ListView[i]+'_title').value==''){ alert('Beneficiery '+ListView[i]+' :: Title is empty');}
						else if( doJava.dom('txt_benef'+ListView[i]+'_first').value==''){ alert('Beneficiery '+ListView[i]+' :: First Name is empty');}
						else if( doJava.dom('txt_benef'+ListView[i]+'_lastname').value==''){ alert('Beneficiery '+ListView[i]+' :: Last Name is empty');}
						else
						{
							return true;
						}
					}
				}
				else{ return true; }
		}
		
	var getNexValidationCard = function(value){
		doJava.File = '../class/class.edit.policy.php';
		doJava.Params = {
			action : 'value_sec_num',
			number : value,
		}
		
		var error = doJava.Post();
		
		if( error==1) {
			CcCardValid = true;  
			doJava.dom('error_message_html').innerHTML= "<img src=../gambar/icon/accept.png>";
		}
		else{
			CcCardValid = false;  
			doJava.dom('error_message_html').innerHTML= "<img src=../gambar/icon/cancel.png>"; 
		}
	}		
		
    </script>
	<style>
		#page_info_header { color:#3c3c36;background-color:#eeeeee;height:20px;padding:3px;font-size:14px;font-weight:bold;border-bottom:2px solid #dddfff;margin-bottom:4px;}
		 table td{ font-size:12px; text-align:left;}
		 table p{font-size:12px;color:#4c4c47;}
		 table td .input{ border:1px solid #a5bb89;background-color:#fbfaf5;width:160px;height:20px;}
		 table td .input:hover{ border:1px solid #a5bb89;background-color:#e7d795}
		 table td select{ border:1px solid #a5bb89;background-color:#fbfaf5;}
		.header-text {text-align:right;font-weight:normal;}
		.sunah {color:#4c4c47;font-size:12px;font-family:Arial;}
		.wajib {color:#4c4c47;font-size:12px;font-family:Arial;}
		 h4{background-color:#eeeeee;color:red;padding:4px;cursor:pointer;width:120px;}
		 h4:hover{color:red;background-color:#d8f7f9;}
		 .age{width:60px;}
	</style>
	</head>
	<body style="overflow:auto;background-color:#eee;">
	
	<!-- start open info if was creted before ** -->
	<?php
		
		
		$BasicHolder = $getFunction -> getBasicHolder();
		$StartForms  = $getFunction -> getStartForm();
		$editPayers  = $getFunction -> getEditPayers();
		$getInsSp 	 = $getFunction -> getInsuranceSp();
		$editHolder  = $getFunction -> getHolderEdit();
		$PolicyHolder = $getFunction -> getDetailPolicy();	
		$Benefiecery  = $getFunction -> getBenefiecery();
		
	?>
	<!-- stop open info if was creted before ** -->
	
	<div id="print_pages" style="background-color:#FFFFFF;margin-left:150px;padding-bottom:25px;padding-top:25px;margin-right:150px;margin-top:0px;overflow:auto;border:1px solid #dddddd;">
	  <table border=0 align="center" width="75%" style="border:1px solid #eeeeee;">
		<TR>
		<TD Style="background-color:#61605e;color:#FFFFFF;font-size:18px;font-weight:bold;height:26px;text-align:center;padding:4px;">EDIT POLICY FORM </TD>
		</TR>
	  <tr>
		<td>
			<div id="page_info_panel" style="margin-top:1px;">
				<table border=0 width="80%" align="center" cellpadding="6px;">
					<tr>
						<td class="header-text sunah" nowrap>Policy Number</td>
						<td nowrap><input type="text" name="main_cust_policy_number" id="main_cust_policy_number" class="input" value="<?php echo $BasicHolder -> PolicyNumber; ?>" disabled></td>
						<td class="header-text sunah" nowrap>Input date</td>
						<td nowrap><input type="text" name="main_cust_policy_date" id="main_cust_policy_date" class="input" value="<?php echo ($BasicHolder -> PolicySalesDate?$db -> formatDateId($BasicHolder -> PolicySalesDate):$StartForms['inputDate']); ?>" disabled></td>
						<td class="header-text sunah" nowrap>Campaign Name</td>
						<td nowrap><input type="text" name="main_cust_policy_campaign" id="main_cust_policy_campaign" class="input" value="<?php echo $getFunction->getCampaignNumber(); ?>" disabled></td>
					</tr>
					<tr>
						<td class="header-text sunah">Telemarketer</td>
						<td><input type="text" name="main_cust_policy_user" id="main_cust_policy_user" class="input" value="<?php echo ($BasicHolder->CignaUser!=''?$BasicHolder->CignaUser:$db->getSession('username'));?>" disabled></td>
						<td class="header-text sunah" nowrap>Effective Date</td>
						<td><input type="text" name="main_cust_policy_efective" id="main_cust_policy_efective" class="input" value="<?php echo ($BasicHolder -> PolicyEffectiveDate?$db -> formatDateId($BasicHolder -> PolicyEffectiveDate):$StartForms['efectiveDate']); ?>" disabled></td>
						<td class="header-text sunah">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
			</div>
	</td>
	</tr>
		<TR>
			<TD style="background-color:#61605e;;color:#FFFFFF;text-align:left;height:24px;padding-left:4px;font-size:16px;font-weight:bold;">HOLDER</TD>
		
		</TR>
	
	<tr>
		<td>
			<div id="tabs-1" style="border:0px solid #dddddd;">
				<table width="100%" border=0 cellpadding="6px;">
					<tr>
						<td class="header-text wajib" nowrap>Holder Type</td>
						<td style="height:30px;"><?php $jpForm -> jpCombo('cb_holder_holdertype',NULL,$getFunction->getHolderType(),2,NULL,1); ?>
								<!--<select id="cb_holder_holdertype" name="cb_holder_holdertype" style="width:180px;" disabled>
									<?php //$getFunction->getHolderType(2); ?>
								</select>-->
								
						<input type="checkbox" name="chekclist" id="chekclist" <?php echo ($getFunction->IsHolder()?'checked':'');?>  value="<?php echo ($getFunction->IsHolder()?1:0);?> " style="text-align:left;margin:0px;border;1px solid #000;" disabled> &nbsp; Holder = Payer </td>
						<td class="header-text wajib" nowrap>First Name</td>
						<td style="height:30px;" nowrap><input type="text" class="input" onkeyup="isStrValue(this);" name="frm_holder_firstname" id="frm_holder_firstname" style="width:200px;" value="<?php echo $getFunction->holderEdit->InsuredFirstName; ?>"></td>
						<!--
							<td class="header-text sunah" style="display:none;">Last Name</td>
							<td style="height:30px;" nowrap><input type="text" style="display:none;" class="input" onkeyup="isStrValue(this);" name="frm_holder_lastname" id="frm_holder_lastname" style="width:200px;" value="<?php echo $getFunction->holderEdit->InsuredLastName; ?>"></td>
						-->
					</tr>
					<tr>
						<td class="header-text sunah">ID-Type</td>
						<td style="height:30px;"><?php $jpForm -> jpCombo('cb_holder_idtype',NULL, $getFunction -> getIdType(),$PolicyHolder -> result_get_value('IdentificationTypeId')); ?>
							<!--<select id="cb_holder_idtype" name="cb_holder_idtype" style="width:120px;">
									<?php //$getFunction->getIdType( ($BasicHolder->IdentificationTypeId?$BasicHolder->IdentificationTypeId:1) ); ?>
							</select>-->
						</td>
						<td class="header-text wajib">Relation</td>
						<td style="height:30px;"><?php $jpForm -> jpCombo('frm_holder_rel',NULL, $getFunction -> getRelation(),$PolicyHolder -> result_get_value('RelationshipTypeId')); ?>
							<!--<select id="frm_holder_rel" name="frm_holder_rel" style="width:'auto';" disabled>
								<?php //$getFunction->getRelation( (1) ); ?>
							</select>-->
						</td>
						<td class="header-text wajib">DOB</td>
						<td style="height:30px;">
							<input type="text" class="input" name="frm_holder_dob" id="frm_holder_dob" value="<?php echo $db ->formatDateId($getFunction->CustData->CustomerDOB); ?>" readonly>
							<img src="<?php echo $app->basePath();?>gambar/calendar.gif"> <span id="text_dob_size"></span>
						</td>
					</tr>
					<tr>
						<td class="header-text sunah">ID-No</td>
						<td style="height:30px;"><input type="text" class="input" onkeyup="isNumber(this);" name="frm_holder_idno" value="<?php echo $BasicHolder->InsuredIdentificationNum; ?>" id="frm_holder_idno"></td>
						<td class="header-text wajib">Gender</td>
						<td style="height:30px;"><?php $jpForm -> jpCombo('frm_holder_gender',NULL, $getFunction -> getGender(),$PolicyHolder -> result_get_value('GenderId')); ?>
							<!--<select id="frm_holder_gender" name="frm_holder_gender" style="width:120px;">
								<?php //$getFunction->getGender($BasicHolder->GenderId); ?>
							</select>-->
						</td>
						<td class="header-text wajib">Title</td>
						<td style="height:30px;"><?php $jpForm -> jpCombo('frm_holder_title',NULL, $getFunction->getSalutation(),$PolicyHolder -> result_get_value('SalutationId')); ?>
							<!--<select id="frm_holder_title" name="frm_holder_title" style="width:120px;">
								<?php //$getFunction->getSalutation($BasicHolder->SalutationId); ?>
							</select>-->
						</td>
					</tr>
					</table>
			</div>
			</td>
		</tr>	
	<!-- stop : tab HHolder -->
	<TR>
		<TD style="background-color:#61605e;color:#FFFFFF;text-align:left;height:24px;padding-left:4px;font-size:16px;font-weight:bold;">INSURANCE</TD>
		
		</TR>
	<!-- start: Insurance -->
	<tr>
		<td>
			<div id="tabs-2">
			   <table width="100%" align="center" style="border:0px dotted #dddddd;" cellpadding="6px;">
					<tr>
						<td style="text-align:right;height:30px;border:0px dotted #dddddd;"  valign="top">
							
							<!-- start here -->
						
							<table>
								<tr>
									<td class="header-top"><h4>SPOUSE</h4></td>
									<td><input type="checkbox" name="cbx_ins_folow" id="cbx_ins_folow" value="<?php echo $getInsSp[3]->InsuredId;?>" <?php echo ($getInsSp[3]->IndexRows==3?'checked':'');?> disabled></td>
								</tr>
								<tr>
								
									<td class="header-text sunah">Holder Type</td>
									<td><?php $jpForm -> jpCombo('cb_insurance_dp_holdertype_'.$getInsSp[3]->InsuredId,NULL, $getFunction->getHolderType(),3,NULL,1); ?>
										<!--<select id="cb_insurance_dp_holdertype_<?php //echo $getInsSp[3]->InsuredId;?>" name="cb_insurance_dp_holdertype_<?php //echo $getInsSp[3]->InsuredId;?>" disabled>
											<?php //$getFunction->getHolderType($getInsSp[3]->PremiumGroupId); ?>
										</select>-->
									</td>
								</tr>
									<tr>
									<td class="header-text <?php echo ($_REQUEST['callstatus']==402?"wajib":"sunah"); ?>">ID Type</td>
									<td><?php $jpForm -> jpCombo('cb_insurance_dp_idtype_'.$getInsSp[3]->InsuredId,NULL, $getFunction->getIdType(),$getInsSp[3]->IdentificationTypeId); ?>
										<!--<select id="cb_insurance_dp_idtype_<?php //echo $getInsSp[3]->InsuredId;?>" name="cb_insurance_dp_idtype_<?php //echo $getInsSp[3]->InsuredId;?>">
												<?php //$getFunction->getIdType($getInsSp[3]->IdentificationTypeId); ?>
										</select>-->
									</td>
								</tr>
									<tr>
									<td class="header-text <?php echo ($_REQUEST['callstatus']==402?"wajib":"sunah"); ?>">ID No</td>
									<td>
										<input type="text" name="txt_insurance_dp_idno_<?php echo $getInsSp[3]->InsuredId;?>" onkeyup="isNumber(this);" id="txt_insurance_dp_idno_<?php echo $getInsSp[3]->InsuredId;?>" class="input" 
										value="<?php echo $getInsSp[3]->InsuredIdentificationNum; ?>">
									</td>
								</tr>
								<tr>
									<td class="header-text sunah">Relation</td>
									<td><?php $jpForm -> jpCombo('cb_insurance_dp_rel_'.$getInsSp[3]->InsuredId,NULL, $getFunction->editRelation(),$getInsSp[3]->RelationshipTypeId); ?>
										<!--<select id="cb_insurance_dp_rel_<?php //echo $getInsSp[3]->InsuredId;?>" name="cb_insurance_dp_rel_<?php //echo $getInsSp[3]->InsuredId;?>" >
											<?php //$getFunction->editRelation($getInsSp[3]->RelationshipTypeId); ?>
										</select>-->
									</td>
								</tr>
								<tr>
									<td class="header-text sunah">Title</td>
									<td><?php $jpForm -> jpCombo('cb_insurance_dp_salut_'.$getInsSp[3]->InsuredId,NULL, $getFunction->getSalutation(),$getInsSp[3]->SalutationId); ?>
										<!--<select id="cb_insurance_dp_salut_<?php //echo $getInsSp[3]->InsuredId;?>" name="cb_insurance_dp_salut_<?php //echo $getInsSp[3]->InsuredId;?>">
											<?php //$getFunction->getSalutation($getInsSp[3]->SalutationId); ?>
										</select>-->
									</td>
								</tr>
								<tr>
									<td class="header-text sunah">First Name</td>
									<td><input type="text" id="txt_insurance_dp_firstname_<?php echo $getInsSp[3]->InsuredId;?>" onkeyup="isStrValue(this);" value="<?php echo $getInsSp[3]->InsuredFirstName; ?>" name="txt_insurance_dp_firstname_<?php echo $getInsSp[3]->InsuredId;?>" class="input "></td>
								</tr>
								<tr>
									<td class="header-text sunah">Last Name</td>
									<td><input type="text" name="txt_insurance_dp_lastname_<?php echo $getInsSp[3]->InsuredId;?>" onkeyup="isStrValue(this);" value="<?php echo $getInsSp[3]->InsuredLastName;?>" id="txt_insurance_dp_lastname_<?php echo $getInsSp[3]->InsuredId;?>" class="input "></td>
								</tr>
								<tr>
									<td class="header-text sunah">Gender</td>
									<td><?php $jpForm -> jpCombo('txt_insurance_dp_gender_'.$getInsSp[3]->InsuredId,NULL, $getFunction->getGender(),$getInsSp[3]->GenderId); ?>
										<!--<select name="txt_insurance_dp_gender_<?php //echo $getInsSp[3]->InsuredId;?>" id="txt_insurance_dp_gender_<?php //echo $getInsSp[3]->InsuredId;?>">
											<?php //$getFunction->getGender($getInsSp[3]->GenderId); ?>
										</select>-->
									</td>
								</tr>
								<tr>
									<td class="header-text sunah">DOB</td>
									<td>
										<input type="text" name="txt_insurance_dp_dob_<?php echo $getInsSp[3]->InsuredId;?>" value="<?php echo $db->formatDateId($getInsSp[3]->InsuredDOB); ?>" id="txt_insurance_dp_dob_<?php echo $getInsSp[3]->InsuredId;?>" onclick="getUmurSizeFunc(this.name,'txt_insurance_dp_age_<?php echo $getInsSp[3]->InsuredId;?>',3);" class="input ">
										<img src="<?php echo $app->basePath();?>gambar/calendar.gif">
									</td>
								</tr>
								<tr>
									<td class="header-text sunah">Age</td>
									<td>
										<input type="text" name="txt_insurance_dp_age_<?php echo $getInsSp[3]->InsuredId;?>" value="<?php echo $getInsSp[3]->InsuredAge;?>" id="txt_insurance_dp_age_<?php echo $getInsSp[3]->InsuredId;?>" class="input age" readonly>
									</td>
								</tr>
								
								
								<tr>
									<td></td>
									<td></td>
								</tr>
								
							</table>
						</td>
						
						<?
							$getInsDp = $getFunction->getInsuranceDp();
							$i = 1;
							for ( $i=1; $i<3; $i++){
							
							//print_r($getInsDp[$i]);
						?>
								<td style="height:30px;border:0px dotted #dddddd;" valign="top" >
									<table>
										<tr>
											<td><h4>DEPENDENT <?php echo $i; ?></h4></td>
											<td><input type="checkbox" name="cbx_ins_folow" id="cbx_ins_folow" value="<?php echo $getInsDp[$i]->InsuredId;?>" <?php echo ($getInsDp[$i]->PremiumGroupId?'checked':''); ?> disabled></td>
										</tr>
										<tr>
											<td class="header-text sunah">Holder Type</td>
											<td><?php $jpForm -> jpCombo('cb_insurance_dp_holdertype_'.$getInsDp[$i]->InsuredId,NULL, $getFunction->getHolderType(),1,NULL,1); ?>
												<!--<select id="cb_insurance_dp_holdertype_<?php //echo $getInsDp[$i]->InsuredId;?>" name="cb_insurance_dp_holdertype_<?php //echo $getInsDp[$i]->InsuredId;?>">
													<?php //$getFunction->getHolderType($getInsDp[$i]->PremiumGroupId); ?>
												</select>-->
											</td>
										</tr>
										<tr>
											<td class="header-text sunah">Relation</td>
											<td><?php $jpForm -> jpCombo('cb_insurance_dp_rel_'.$getInsDp[$i]->InsuredId,NULL, $getFunction->editRelation(),$getInsDp[$i]->RelationshipTypeId); ?>
												<!--<select id="cb_insurance_dp_rel_<?php //echo $getInsDp[$i]->InsuredId;?>" name="cb_insurance_dp_rel_<?php //echo $getInsDp[$i]->InsuredId;?>">
													<?php //$getFunction->editRelation($getInsDp[$i]->RelationshipTypeId); ?>
												</select>-->
											</td>
										</tr>
										<tr>
											<td class="header-text sunah">Title</td>
											<td><?php $jpForm -> jpCombo('cb_insurance_dp_salut_'.$getInsDp[$i]->InsuredId,NULL, $getFunction->getSalutation(),$getInsDp[$i]->SalutationId); ?>
												<!--<select id="cb_insurance_dp_salut_<?php //echo $getInsDp[$i]->InsuredId;?>" name="cb_insurance_dp_salut_<?php //echo $getInsDp[$i]->InsuredId;?>">
													<?php //$getFunction->getSalutation($getInsDp[$i]->SalutationId); ?>
												</select>-->
											</td>
										</tr>
										<tr>
											<td class="header-text sunah">First Name</td>
											<td><input type="text" name="txt_insurance_dp_firstname_<?php echo $getInsDp[$i]->InsuredId;?>" class="input" value="<?php echo $getInsDp[$i]->InsuredFirstName; ?>" onkeyup="isStrValue(this);" id="txt_insurance_dp_firstname_<?php echo $getInsDp[$i]->InsuredId;?>"></td>
										</tr>
										<tr>
											<td class="header-text sunah">Last Name</td>
											<td><input type="text" value="<?php echo $getInsDp[$i]->InsuredLastName; ?>"  name="txt_insurance_dp_lastname_<?php echo $getInsDp[$i]->InsuredId;?>" class="input" onkeyup="isStrValue(this);" id="txt_insurance_dp_lastname_<?php echo $getInsDp[$i]->InsuredId;?>"></td>
										</tr>
										<tr>
											<td class="header-text sunah">Gender</td>
											<td><?php $jpForm -> jpCombo('cb_insurance_dp_gender_'.$getInsDp[$i]->InsuredId,NULL, $getFunction->getGender(),$getInsDp[$i]->GenderId); ?>
												<!--<select id="cb_insurance_dp_gender_<?php //echo $getInsDp[$i]->InsuredId;?>" name="cb_insurance_dp_gender_<?php //echo $getInsDp[$i]->InsuredId;?>">
													<?php //$getFunction->getGender($getInsDp[$i]->GenderId); ?>
												</select>-->
											</td>
										</tr>
										<tr>
											<td class="header-text sunah">DOB</td>
											<td>
												<input type="text" value="<?php echo $db->formatDateId($getInsDp[$i]->InsuredDOB); ?>"  name="txt_insurance_dp_dob_<?php echo $getInsDp[$i]->InsuredId;?>" id="txt_insurance_dp_dob_<?php echo $getInsDp[$i]->InsuredId;?>" class="input" onclick="getUmurSizeFunc(this.name,'txt_insurance_dp_age_<?php echo $getInsDp[$i]->InsuredId;?>',1);">
												<img src="<?php echo $app->basePath();?>gambar/calendar.gif">
											</td>
										</tr>
										<tr>
											<td class="header-text sunah">Age</td>
											<td>
												<input type="text" value="<?php echo $getInsDp[$i]->InsuredAge; ?>"  name="txt_insurance_dp_age_<?php echo $getInsDp[$i]->InsuredId;?>" id="txt_insurance_dp_age_<?php echo $getInsDp[$i]->InsuredId;?>" class="input age" readonly>
											</td>
										</tr>
										<tr>
											<td></td>
											<td></td>
										</tr>
										
									</table>
								
								</td>
						<?
								if($i==3) break;
							}
						?>	
						
					</tr>
					<tr>
						<?php
							for ( $y=3; $y<5; $y++){
						?>
						<td style="height:30px;border:0px dotted #dddddd;" valign="top" >
									<table>
										<tr>
											<td><h4>DEPENDENT <?php echo $y; ?></h4></td>
											<td><input type="checkbox" name="cbx_ins_folow" id="cbx_ins_folow" value="<?php echo $getInsDp[$y]->InsuredId;?>" <?php echo ($getInsDp[$y]->PremiumGroupId?'checked':''); ?> disabled></td>
										</tr>
										<tr>
											<td class="header-text sunah">Holder Type</td>
											<td><?php $jpForm -> jpCombo('cb_insurance_dp_holdertype_'.$y.'_holdertype',NULL, $getFunction->getHolderType(),1,NULL,1); ?>
												<!--<select id="cb_insurance_dp<?php //echo $y; ?>_holdertype" name="cb_insurance_dp<?php //echo $y; ?>_holdertype">
													<?php //$getFunction->getHolderType($getInsDp[$y]->PremiumGroupId); ?>
												</select>-->
											</td>
										</tr>
										<tr>
											<td class="header-text sunah">Relation</td>
											<td><?php $jpForm -> jpCombo('cb_insurance_dp'.$y.'_rel',NULL, $getFunction->editRelation(),$getInsDp[$y]->RelationshipTypeId); ?>
												<!--<select id="cb_insurance_dp<?php //echo $y; ?>_rel" name="cb_insurance_dp<?php //echo $y; ?>_rel">
													<?php //$getFunction->editRelation($getInsDp[$y]->RelationshipTypeId); ?>
												</select>-->
											</td>
										</tr>
										<tr>
											<td class="header-text sunah">Title</td>
											<td><?php $jpForm -> jpCombo('cb_insurance_dp'.$y.'_salut',NULL, $getFunction->getSalutation(),$getInsDp[$y]->SalutationId); ?>
												<!--<select id="cb_insurance_dp<?php //echo $y; ?>_salut" name="cb_insurance_dp<?php //echo $y; ?>_salut">
													<?php //$getFunction->getSalutation($getInsDp[$y]->SalutationId); ?>
												</select>-->
											</td>
										</tr>
										<tr>
											<td class="header-text sunah">First Name</td>
											<td><input type="text" name="txt_insurance_dp<?php echo $y; ?>_firstname" class="input" 
											value="<?php echo $getInsDp[$y]->InsuredFirstName; ?>" onkeyup="isStrValue(this);" id="txt_insurance_dp<?php echo $y; ?>_firstname"></td>
										</tr>
										<tr>
											<td class="header-text sunah">Last Name</td>
											<td><input type="text" value="<?php echo $getInsDp[$y]->InsuredLastName; ?>"  name="txt_insurance_dp<?php echo $y; ?>_lastname" class="input" onkeyup="isStrValue(this);" id="txt_insurance_dp<?php echo $y; ?>_lastname"></td>
										</tr>
										<tr>
											<td class="header-text sunah">Gender</td>
											<td><?php $jpForm -> jpCombo('cb_insurance_dp'.$y.'_gender',NULL, $getFunction->getGender(),$getInsDp[$y]->GenderId); ?>
												<!--<select id="cb_insurance_dp<?php //echo $y; ?>_gender" name="cb_insurance_dp<?php //echo $y; ?>_gender">
													<?php //$getFunction->getGender($getInsDp[$y]->GenderId); ?>
												</select>-->
											</td>
										</tr>
										<tr>
											<td class="header-text sunah">DOB</td>
											<td>
												<input type="text" value="<?php echo $db->formatDateId($getInsDp[$y]->InsuredDOB); ?>"  name="txt_insurance_dp<?php echo $y; ?>_dob" id="txt_insurance_dp<?php echo $y; ?>_dob" class="input" onclick="getUmurSizeFunc(this.name,'txt_insurance_dp<?php echo $y; ?>_age',1);">
												<img src="<?php echo $app->basePath();?>gambar/calendar.gif">
											</td>
										</tr>
										<tr>
											<td class="header-text sunah">Age</td>
											<td>
												<input type="text" value="<?php echo $getInsDp[$y]->InsuredAge; ?>"  name="txt_insurance_dp<?php echo $y; ?>_age" id="txt_insurance_dp<?php echo $y; ?>_age" class="input age" readonly>
											</td>
										</tr>
										<tr>
											<td></td>
											<td></td>
										</tr>
										
									</table>
								
								</td>
						<?php
						}
						
						?>
						
						
					</tr>
					</table>
			</div>
		</td>
	</tr>	
	<TR>
		<TD style="background-color:#61605e;color:#FFFFFF;text-align:left;height:24px;padding-left:4px;font-size:16px;font-weight:bold;">PLAN</TD>
	</TR>
	<!-- start : tab3 -->
    <tr>
		<td>
				<div id="tabs-3">
					<table width="75%" align="center">
					<tr>
							<td class="header-text wajib">Product </td>
							<td style="height:30px;"><?php $jpForm -> jpCombo('plan_product_id',NULL, $getFunction->getProductByCampaign(),$getPlan->ProductId,null,1); ?>
								<!--<select name="plan_product_id" id="plan_product_id" onchange="getPlanByProduct(this.value);" disabled>
									<?php //$getFunction -> getProductByCampaign( $getPlan->ProductId); ?>
								</select>-->
							</td>
							<td class="header-text wajib">Plan</td>
							<td style="height:30px;" id="html_inner_plan">
								<?php //$jpForm -> jpCombo('plan_plan',NULL, $getPlan->ProductPlanName,$getPlan->ProductPlan,null,1); ?>
								<select name="plan_plan" id="plan_plan" disabled>
										<option value=""> -- Choose -- </option>
										<?php
											echo " <option value='".$getPlan->ProductPlan."' selected>".$getPlan->ProductPlanName."</option>";
										?>
								</select>
							</td>
							<td class="header-text wajib">Pay Mode</td>
							<td style="height:30px;"><?php $jpForm -> jpCombo('plan_paymode',NULL, $getFunction->getPayMode(),$getPlan->PayModeId,null,1); ?>
								<!--<select name="plan_paymode" id="plan_paymode" onchange="getPremiByPlanMode(this.value);" disabled>
										<?php //$getFunction->getPayMode($getPlan->PayModeId); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text wajib">Premi</td>
							<td style="height:30px;"><input type="text" name="plan_premi" id="plan_premi" class="input" value="<?php echo $getFunction->getEditPremium();?>" readonly></td>
							<td class="header-text wajib">Pay Type</td>
							<td style="height:30px;"><?php $jpForm -> jpCombo('plan_paytype',NULL, $getFunction->getPayType(),1,null,1); ?>
								<!--<select name="plan_paytype" id="plan_paytype" disabled >
										<?php //$getFunction->getPayType(1); ?>
									</select>-->
							</td>
							<td style="text-align:right;height:30px;">&nbsp;</td>
							<td style="height:30px;">&nbsp;</td>
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
		<script>
		$(function(){
			$("#payer_dob").datepicker({ dateFormat: 'dd-mm-yy',
				buttonImage: '../gambar/calendar.gif', 
				buttonImageOnly: true,
				changeMonth: true,
				changeYear: true
			});
		});
					</script>
		
		<div id="tabs-4">
			<table width="100%" align="center">	
					<tr>
						<td class="header-text wajib">Title</td>
						<td ><?php $jpForm -> jpCombo('payer_salutation',NULL, $getFunction->getSalutation(),$editPayers->SalutationId); ?>
							<!--<select name="payer_salutation" id="payer_salutation">
								<?php //$getFunction->getSalutation($editPayers->SalutationId); ?>
							</select>-->
						</td>
						<td class="header-text wajib" >First Name</td>
						<td><input type="text" name="payer_first_name" onkeyup="isStrValue(this);" id="payer_first_name" class="input" value="<?php echo $editPayers->PayerFirstName; ?>" ></td>
						<td class="header-text sunah">Last Name</td>
						<td><input type="text" name="payer_last_name" onkeyup="isStrValue(this);" id="payer_last_name" class="input" value="<?php echo ($BasicPayer->PayerLastName?$BasicPayer->PayerLastName:$_REQUEST['payer_last_name']); ?>" ></td>
					</tr>	
					<tr>
						<td class="header-text wajib">Gender</td>
						<td ><?php $jpForm -> jpCombo('payer_gender',NULL, $getFunction->getGender(),$editPayers->GenderId); ?>
							<!--<select name="payer_gender" id="payer_gender">
								<?php //$getFunction->getGender($editPayers->GenderId); ?>
							</select>-->
						</td>
						<td class="header-text wajib">DOB</td>
						<td><input type="text" name="payer_dob" id="payer_dob" class="input" onclick="getFuckJquery(this.name);"
								value="<?php echo $db -> formatDateId($editPayers->PayerDOB);?>">
								<img src="<?php echo $app->basePath();?>gambar/calendar.gif">
								</td>
						<td class="header-text wajib">Address</td>
						<td><input type="text" name="payer_address" id="payer_address1"  class="input" 
							value="<?php echo $editPayers->PayerAddressLine1;?>"></td>
					</tr>

					<tr>
						<td class="header-text wajib">ID - Type </td>
						<td ><?php $jpForm -> jpCombo('payer_holder_idtype',NULL, $getFunction->getIdType(),($BasicHolder->IdentificationTypeId?$BasicHolder->IdentificationTypeId:1)); ?>
							<!--<select id="payer_holder_idtype" name="payer_holder_idtype" style="width:120px;">
								<?php //$getFunction->getIdType( ($BasicHolder->IdentificationTypeId?$BasicHolder->IdentificationTypeId:1) ); ?>
							</select>-->
						</td>
						<td class="header-text wajib" >ID No</td>
						<td><input type="text" name="payer_idno" onkeyup="isNumber(this);" id="payer_idno" class="input" value="<?php echo $editPayers->PayerIdentificationNum;?>" ></td>
					</tr>
					
					<tr>
						<td class="header-text wajib">Mobile Phone</td>
						<td ><input type="text" name="payer_mobile_phone"  onkeyup="isNumber(this);" id="payer_mobile_phone" class="input" 
						value="<?php echo $editPayers->PayerMobilePhoneNum;?>"></td>
						<td class="header-text wajib">City</td>
						<td><input type="text" name="payer_city" id="payer_city" class="input" value="<?php echo $editPayers->PayerCity;?>"> </td>
						<td class="header-text wajib"></td>
						<td><input type="text" name="payer_address2" id="payer_address2" class="input" value="<?php echo $editPayers->PayerAddressLine2;?>"></td>
					</tr>	
					
					<!--
						[PayerAddressLine1] => GATOT SUBROTO VILLA TOMANG MAS
			[PayerAddressLine2] => I BLK G 9
			[PayerAddressLine3] => 
			[PayerAddressLine4] => 
					-->
					<tr>
						<td class="header-text wajib">Home Phone </td>
						<td ><input type="text" name="payer_home_phone" id="payer_home_phone" onkeyup="isNumber(this);" class="input" value="<?php echo $editPayers->PayerHomePhoneNum;?>"></td>
						<td class="header-text wajib">Zip</td>
						<td><input type="text" name="payer_zip_code" id="payer_zip_code" onkeyup="isNumber(this);" class="input" 
						value="<?php echo $editPayers->PayerZipCode;?>"></td>
						<td class="header-text wajib"></td>
						<td><input type="text" name="payer_address3" id="payer_address3"  class="input" value="<?php echo $editPayers->PayerAddressLine3;?>"></td>
					</tr>	
					<tr>
						<td class="header-text wajib">Office Phone </td>
						<td ><input type="text" name="payer_office_phone" onkeyup="isNumber(this);" id="payer_office_phone" class="input" value="<?php echo $editPayers->PayerWorkPhoneNum;?>"></td>
						<td class="header-text wajib">Province</td>
						<td ><?php $jpForm -> jpCombo('payer_province',NULL, $getFunction->getProvince(),$editPayers->ProvinceId); ?>
							<!--<select name="payer_province" id="payer_province">
								<option value=""> -- Choose --</option>
								<?php //$getFunction->getProvince($editPayers->ProvinceId); ?>
							</select>-->
						</td>
						<td class="header-text wajib"></td>
						<td><input type="text" name="payer_address4" id="payer_address4" class="input" value="<?php echo $editPayers->PayerAddressLine4;?>"></td>
					</tr>	
					<tr>
						<td class="header-text wajib" valign="top">Card Number</td>
						<td valign="top">
							<input type="text" maxlength="16" name="payer_card_number" id="payer_card_number" value="<?php echo $editPayers->PayerCreditCardNum;?>" onkeyup="getNexValidationCard(this.value);" onkeyup="isNumber(this);" class="input">
						<span id="error_message_html"></span></td>
						<td class="header-text sunah">Bank</td>
						<td ><?php $jpForm -> jpCombo('payer_bank',NULL, $getFunction->getBanking(),$editPayers->PayersBankId); ?>
							<!--<select name="payer_bank" id="payer_bank">
								<option value=""> -- Choose --</option>
								<?php //$getFunction->getBanking($getFunction->getBankId());?>
								
							</select>-->
						</td>
						<td class="header-text sunah">Fax Phone</td>
						<td><input type="text" name="payer_fax_number" onkeyup="isNumber(this);" id="payer_fax_number" class="input" 
								value="<?php echo $editPayers->PayerFaxNum;?>"></td>
					</tr>	
					<tr>
						<td class="header-text wajib">Expiration date</td>
						<td ><input type="text" name="payer_expired_date" id="payer_expired_date" class="input" value="<?php echo $editPayers->PayerCreditCardExpDate;?>"> (mm/yy)</td>
						<td class="header-text wajib">Card Type</td>
						<td ><?php $jpForm -> jpCombo('payer_card_type',NULL, $getFunction->getCardType(),$editPayers->CreditCardTypeId); ?>
							<!--<select name="payer_card_type" id="payer_card_type" >
								<?php //$getFunction -> getCardType($editPayers->CreditCardTypeId);?>
							</select>-->
						</td>
						<td class="header-text sunah">Email</td>
						<td><input type="text" name="payer_email" id="payer_email" class="input" value="<?php echo $editPayers->PayerEmail;?>"></td>
					</tr>	
				</table>
		</div>
	</td>
	</tr>	
	
	<TR>
			<TD style="background-color:#61605e;color:#FFFFFF;text-align:left;height:24px;padding-left:4px;font-size:16px;font-weight:bold;">BENEFICIARY</TD>
		</TR>
		
	<!-- stop : tab5 -->	
	<tr>
		<td>
			<div id="tabs-5" >
					<table width="75%">
						<tr><td>
							<table>
								<tr>
									<td><h4 >BENEFICIARY 1</h4> </td>
									<td><input type="checkbox" name="benef_box" id="benef_box" value="1"></td>
								</tr>
								<tr>
									<td class="header-text sunah">Holder Type</td>
									<td><?php $jpForm -> jpCombo('txt_benef1_holdertype',NULL, $getFunction->getHolderType(),1,NULL,1); ?>
										<!--<select id="txt_benef1_holdertype" name="txt_benef1_holdertype">
											<?php //$getFunction->getHolderType(); ?>
										</select>-->
									</td>
								</tr>
								<tr>
									<td class="header-text sunah">Relation</td>
									<td><?php $jpForm -> jpCombo('txt_benef1_rel',NULL, $getFunction -> getRelation()); ?>
										<!--<select id="txt_benef1_rel" name="txt_benef1_rel">
											<#?php $getFunction->getRelation(); ?>
										</select>-->
									</td>
								</tr>
								<tr>
									<td class="header-text sunah">Title</td>
									<td><?php $jpForm -> jpCombo('txt_benef1_title',NULL, $getFunction->getSalutation(),NULL); 
										/*<select id="txt_benef1_title" name="txt_benef1_title">
										 $getFunction->getSalutation(); 
										</select> */?>
									</td>
								</tr>
								<tr>
									<td class="header-text sunah" class="input">First Name</td>
									<td><input type="text" name="txt_benef1_first" onkeyup="isStrValue(this);" id="txt_benef1_first" class="input"></td>
								</tr>
								<tr>
									<td class="header-text sunah" class="input">Last Name</td>
									<td><input type="text" name="txt_benef1_lastname" onkeyup="isStrValue(this);" id="txt_benef1_lastname" class="input"></td>
								</tr>
								<tr>
									<td></td>
									<td></td>
								</tr>
								
							</table>
						</td>
						<td style="height:30px;">
							<table>
								<tr>
									<td><h4>BENEFICIARY 2</h4></td>
									<td> <input type="checkbox" name="benef_box" id="benef_box" value="2" ></td>
								</tr>
								<tr>
									<td class="header-text sunah">Holder Type</td>
									<td><?php $jpForm -> jpCombo('txt_benef2_holdertype',NULL, $getFunction->getHolderType(),1,NULL,1); ?>
										<!--<select id="txt_benef2_holdertype" name="txt_benef2_holdertype">
											<?php //$getFunction->getHolderType(); ?>
										</select>-->
									</td>
								</tr>
								<tr>
									<td class="header-text sunah">Relation</td>
									<td><?php $jpForm -> jpCombo('txt_benef2_rel',NULL, $getFunction -> getRelation()); ?>
										<!--<select id="txt_benef2_rel" name="txt_benef2_rel">
											<#?php $getFunction->getRelation(); ?>
										</select>-->
									</td>
								</tr>
								<tr>
									<td class="header-text sunah">Title</td>
									<td><?php $jpForm -> jpCombo('txt_benef2_title',NULL, $getFunction->getSalutation(),NULL); ?>
										<!--<select id="txt_benef2_title" name="txt_benef2_title">
											<?php //$getFunction->getSalutation(); ?>
										</select>-->
									</td>
								</tr>
								<tr>
									<td class="header-text sunah">First Name</td>
									<td><input type="text" name="txt_benef2_first" onkeyup="isStrValue(this);" id="txt_benef2_first" class="input"></td>
								</tr>
								<tr>
									<td class="header-text sunah" >Last Name</td>
									<td><input type="text" name="txt_benef2_lastname" onkeyup="isStrValue(this);" id="txt_benef2_lastname" class="input"></td>
								</tr>
								
								
								<tr>
									<td></td>
									<td></td>
								</tr>
								
							</table>
						
						</td>
						<td style="height:30px;">
							<table>
								<tr>
									<td><h4 >BENEFICIARY 3</h4></td>
									<td> <input type="checkbox" name="benef_box" id="benef_box" value="3" > </td>
								</tr>
								<tr>
									<td class="header-text sunah">Holder Type</td>
									<td><?php $jpForm -> jpCombo('txt_benef3_holdertype',NULL, $getFunction->getHolderType(),1,NULL,1); ?>
										<!--<select id="txt_benef3_holdertype" name="txt_benef3_holdertype">
											<?php //$getFunction->getHolderType(); ?>
										</select>-->
									</td>
								</tr>
								<tr>
									<td class="header-text sunah">Relation</td>
									<td><?php $jpForm -> jpCombo('txt_benef3_rel',NULL, $getFunction -> getRelation()); ?>
										<!--<select id="txt_benef3_rel" name="txt_benef3_rel">
											<?php //$getFunction->getRelation(); ?>
										</select>-->
									</td>
								</tr>
								<tr>
									<td class="header-text sunah">Title</td>
									<td><?php $jpForm -> jpCombo('txt_benef3_title',NULL, $getFunction->getSalutation(),NULL); ?>
										<!--<select id="txt_benef3_title" name="txt_benef3_title">
											<?php //$getFunction->getSalutation(); ?>
										</select>-->
									</td>
								</tr>
								<tr>
									<td class="header-text sunah">First Name</td>
									<td><input type="text" name="txt_benef3_first" onkeyup="isStrValue(this);" id="txt_benef3_first" class="input"></td>
								</tr>
								<tr>
									<td class="header-text sunah" >Last Name</td>
									<td><input type="text" name="txt_benef3_lastname" onkeyup="isStrValue(this);" id="txt_benef3_lastname" class="input"></td>
								</tr>
								
								
								<tr>
									<td></td>
									<td></td>
								</tr>
								
							</table>
						
						</td>
					</tr>
					<tr>
						<td style="text-align:right;height:30px;">
							<table>
								<tr>
									<td><h4 >BENEFICIARY 4</h4> </td>
									<td><input type="checkbox" name="benef_box" id="benef_box" value="4" ></td>
								</tr>
								<tr>
									<td class="header-text sunah">Holder Type</td>
									<td><?php $jpForm -> jpCombo('txt_benef4_holdertype',NULL, $getFunction->getHolderType(),1,NULL,1); ?>
										<!--<select id="txt_benef4_holdertype" name="txt_benef4_holdertype">
											<?php //$getFunction->getHolderType(); ?>
										</select>-->
									</td>
								</tr>
								<tr>
									<td class="header-text sunah">Relation</td>
									<td><?php $jpForm -> jpCombo('txt_benef4_rel',NULL, $getFunction -> getRelation()); ?>
										<!--<select id="txt_benef4_rel" name="txt_benef4_rel">
											<?php //$getFunction->getRelation(); ?>
										</select>-->
									</td>
								</tr>
								<tr>
									<td class="header-text sunah">Title</td>
									<td><?php $jpForm -> jpCombo('txt_benef4_title',NULL, $getFunction->getSalutation(),NULL); ?>
										<!--<select id="txt_benef3_title" name="txt_benef3_title">
											<?php //$getFunction->getSalutation(); ?>
										</select>-->
									</td>
								</tr>
								<tr>
									<td class="header-text sunah">First Name</td>
									<td><input type="text" name="txt_benef4_first" onkeyup="isStrValue(this);" id="txt_benef4_first" class="input"></td>
								</tr>
								<tr>
									<td class="header-text sunah" >Last Name</td>
									<td><input type="text" name="txt_benef4_lastname" onkeyup="isStrValue(this);" id="txt_benef4_lastname" class="input"></td>
								</tr>
								
								
								<tr>
									<td></td>
									<td></td>
								</tr>
								
							</table>
						</td>
						<td style="height:30px;">
							<table>
								<tr>
									<td><h4 >BENEFICIARY 5</h4> </td>
									<td><input type="checkbox" name="benef_box" id="benef_box" value="5" ></td>
								</tr>
								<tr>
									<td class="header-text sunah">Holder Type</td>
									<td><?php $jpForm -> jpCombo('txt_benef5_holdertype',NULL, $getFunction->getHolderType(),1,NULL,1); ?>
										<!--<select id="txt_benef4_holdertype" name="txt_benef4_holdertype">
											<?php //$getFunction->getHolderType(); ?>
										</select>-->
									</td>
								</tr>
								<tr>
									<td class="header-text sunah">Relation</td>
									<td><?php $jpForm -> jpCombo('txt_benef5_rel',NULL, $getFunction -> getRelation()); ?>
										<!--<select id="txt_benef4_rel" name="txt_benef4_rel">
											<?php //$getFunction->getRelation(); ?>
										</select>-->
									</td>
								</tr>
								<tr>
									<td class="header-text sunah">Title</td>
									<td><?php $jpForm -> jpCombo('txt_benef5_title',NULL, $getFunction->getSalutation(),NULL); ?>
										<!--<select id="txt_benef3_title" name="txt_benef3_title">
											<?php //$getFunction->getSalutation(); ?>
										</select>-->
									</td>
								</tr>
								<tr>
									<td class="header-text sunah">First Name</td>
									<td><input type="text" name="txt_benef5_first" id="txt_benef5_first" onkeyup="isStrValue(this);" class="input"></td>
								</tr>
								<tr>
									<td class="header-text sunah" >Last Name</td>
									<td><input type="text" name="txt_benef5_lastname" id="txt_benef5_lastname" onkeyup="isStrValue(this);"class="input" ></td>
								</tr>
								
								
								<tr>
									<td></td>
									<td></td>
								</tr>
								
							</table>
							
						</td>
						
					</tr>
					</table>
					
				</div>
				<!-- stop : tab4 -->
				
				
		</div>
	</td>
	</tr>
	</table>
				<div style="text-align:center;margin-top:20px;">	
					<input type="button" style="border:1px solid #dddddd;width:90px;color:red;font-weight:bold;" name="update_button" id="update_button" value="Update" onClick="UpdateCustomer(<?php echo $_REQUEST['customerid'];?>)">
					<input type="button" style="border:1px solid #dddddd;width:90px;color:red;font-weight:bold;" name="updatex_button" id="updatex_button" value="Exit"   onClick="javascript:window.close();">
				</div>	
	</div>
		

</body>
</html>