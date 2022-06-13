<?php
	require(dirname(__FILE__)."/../sisipan/sessions.php");
	require(dirname(__FILE__)."/../fungsi/global.php");
	require(dirname(__FILE__)."/../class/MYSQLConnect.php");
	require(dirname(__FILE__)."/../class/class.application.php");
	require(dirname(__FILE__)."/../class/class.getfunction.php");
	require(dirname(__FILE__)."/../sisipan/parameters.php");
	require(dirname(__FILE__)."/../plugin/lib.form.php");
	
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- start Link : css --> 
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta content="utf-8" http-equiv="encoding">
	<title>Create Policy </title>
	<link type="text/css" rel="stylesheet" href="<?php echo $app->basePath();?>gaya/gaya_utama.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo $app->basePath();?>gaya/other.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo $app->basePath();?>pustaka/jquery/jquery-ui-themes-1.7.2/themes/<?php echo $Themes->V_UI_THEMES;?>/ui.all.css" />	
	<link type="text/css" rel="stylesheet" media="all" href="<?php echo $app->basePath();?>gaya/chat.css" />
    <link type="text/css" rel="stylesheet" media="all" href="<?php echo $app->basePath();?>gaya/screen.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo $app->basePath();?>gaya/custom.css" />
 
 <!-- stop Link : css -->
	
 <!-- start Link : Javascript -->
	
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/jquery-1.3.2.js"></script>    
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/jquery-ui-1.7.2/ui/jquery-ui.js"></script>
    <script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/jquery-ui-1.7.2/external/bgiframe/jquery.bgiframe.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
	
	<script>
		
		var CcCardValid 	= false;
		var openTabs1 		= false;
		var openTabs2 		= false
		var CustomerId 		= '<?php echo $db -> escPost('customerid'); ?>';
		var CampaignId 		= '<?php echo $db -> escPost('campaignid'); ?>';
		var interestStatus 	= '<?php echo $db -> escPost('callstatus'); ?>'; 
		var CallStatus 		= '<?php echo $db -> escPost('callstatus'); ?>'; 
		var initGroup 		= false;
		var	iniatedUmur 	= '';
		var crosscek 		= false;
		var FormPayer		= false;
		var flagsp	 		= false;
		var flagdp	 		= false;

		$(document).ready(function() {
			$("#tabs" ).tabs();
			
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
		
			
		$( "#tabs" ).tabs( "option", "disabled", [1,2, 3,4]);
		var isCheck = doJava.dom('chekclist');
		doJava.File  = '../class/class.frm.tpl.php';
			doJava.Params= {
				action:'get_form_payers',
				customerid:CustomerId,
				payers:(isCheck.checked?1:0)
			}
		$('#tabs-4').load(doJava.File+'?'+doJava.ArrVal());
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

		
/* get by tag name **/

	var getTagNameInput = function()
	{
		var frmEl= document.getElementsByTagName('input');
		var frmString = '';
			for(var i=0; i< frmEl.length; i++)
			{
				frmString = frmString+"&"+frmEl[i].name+"="+frmEl[i].value;  
			}
		return frmString.substring(1,frmString.length);
	}

/* get by tag select **/
	
	var getTagNameSelect = function(){
		var frmEl = document.getElementsByTagName('select');
		var frmString = '';
			for(var i=0; i<frmEl.length; i++){
				frmString +="&"+frmEl[i].name+"="+frmEl[i].value;  
			}
		return frmString;
	}


/* validation field tabs 4 **/
	var InterestWithSpouse = function()
	{
		doJava.File = '../class/class.frm.policy.php';
		doJava.Params = {
			action :'interest_with_spouse',
			call_status_id : CallStatus, 
		} 
		
		var error_status = doJava.eJson();
		if( error_status.result){ return true; }
		else{ return false; }	
	}
		
	
						
/* validation field tabs 4 **/
		
	var getUmurSize = function()
		{
			if( iniatedUmur!=''){
				var iniatedUmurx = iniatedUmur.trim().split(" ");
				return iniatedUmurx[0];
			}
			else{
				return false;
			}
	}
		
/* validation field tabs 4 **/
		
		var getPlanByProduct = function(object)
		{
			var select_id= doJava.SelArrVal(object.name);
				doJava.File = '../class/class.frm.policy.php';
					doJava.Params = {
						action	: 'get_plan_customer',
						productid : select_id
				}
				var error = doJava.Post();
					doJava.dom('html_inner_plan').innerHTML= error;
		}
		
/* validation field tabs 4 **/
		
		var AjaxGetSizeAge = function(date,init){
				doJava.File = '../class/class.frm.policy.php';
				doJava.Params = {
					action	: 'hitung_dob_customer',
					user_dob : date,
					CampaignId : CampaignId,
					init:init
				}
			
			var JsonData = doJava.eJson();
				if( JsonData.result){ 
					return JsonData.umur_size; 
				}
				else{
					alert(JsonData.message);
					return JsonData.complete;
				}
		}
		
/* validation field tabs 4 **/
		
		var toRenderValue = function(date,name,init)
		{	
			if( name =='frm_holder_dob'){
				doJava.File = '../class/class.frm.policy.php';
				doJava.Params = {
					action	: 'hitung_dob_customer',
					user_dob : date,
					CampaignId : CampaignId,
					init:init
				}
				
				var JsonData = doJava.eJson();
				if( JsonData.result){ 
					doJava.dom('text_dob_size').value= JsonData.umur_size; 
				}
				else{
					alert(JsonData.message);
					doJava.dom('text_dob_size').value= JsonData.complete; 
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
			var isCheckbox 				= doJava.dom('chekclist');
			var text_dob_size			= doJava.dom('text_dob_size');
			
			if(cb_holder_holdertype.value=='' ){ cb_holder_holdertype.style.borderColor='red'; alert('Holder Type cannot be empty!'); return false;}
			else if( frm_holder_firstname.value==''){ frm_holder_firstname.style.borderColor='red'; alert('First Name cannot be empty!');return false;}
			//else if( frm_holder_rel.value==''){ frm_holder_rel.style.borderColor='red'; alert('Relation cannot be empty!'); return false;}
			//else if( cb_holder_idtype.value==''){ alert('ID-Type cannot be empty!'); cb_holder_idtype.style.borderColor='red'; return false;}
			//else if( frm_holder_gender.value==''){ alert('Gender cannot be empty!'); frm_holder_gender.style.borderColor='red'; return false; }
			else if( frm_holder_dob.value==''){ alert('Please select DOB!');  frm_holder_dob.style.borderColor='red'; return false;}			
			//else if( frm_holder_title.value==''){ alert('Title cannot be empty!'); frm_holder_title.style.borderColor='red'; return false;}
			else if( text_dob_size.value==0){ alert('Age Not valid!'); frm_holder_dob.focus(); return false; }
			else { 
				if(frm_holder_idno.value==''){ 
					alert('ID-No cannot be empty!'); return false; 
				}	
				else{
					return true;
				}
			}
		}
		
/* validation field tabs 4 **/
		
		var getValueTabs2 = function()
		{
			var cb_insurance_sp_holdertype 	= doJava.dom('cb_insurance_sp_holdertype');
			var cb_insurance_sp_idtype 		= doJava.dom('cb_insurance_sp_idtype');
			var txt_insurance_sp_idno 		= doJava.dom('txt_insurance_sp_idno');
			var cb_insurance_sp_relation 	= doJava.dom('cb_insurance_sp_relation');
			var cb_insurance_sp_salut 		= doJava.dom('cb_insurance_sp_salut');
			var txt_insurance_sp_firstname 	= doJava.dom('txt_insurance_sp_firstname');
			var txt_insurance_sp_lastname  	= doJava.dom('txt_insurance_sp_lastname');
			var txt_insurance_sp_gender 	= doJava.dom('txt_insurance_sp_gender');
			var txt_insurance_sp_dob 		= doJava.dom('txt_insurance_sp_dob');
			var cbx_ins_folow				= doJava.dom('cbx_ins_folow');
			var MaxLengthCheck 				= doJava.checkedValue('cbx_ins_folow');
			var ValLengthCheck 				= MaxLengthCheck.split(',');
			
				if( (cbx_ins_folow.checked==true) && InterestWithSpouse() ){
					if(openTabs2)
					{	
						//if( cb_insurance_sp_idtype.value==''){ cb_insurance_sp_idtype.focus(); alert('ID Type cannot be empty!');  return false;}
						//else if( txt_insurance_sp_idno.value==''){ txt_insurance_sp_idno.focus(); alert('ID No cannot be empty!'); return false;}
						//else { 
								return true;
						//}
					}	
				}
				else if( (cbx_ins_folow.checked==false) && InterestWithSpouse() ){
					if( MaxLengthCheck!=''){
						return true;
					}
					else{
						if(openTabs2){
							alert("Please fill Spouse Or Dependent ");
							return false;
						}
					}	
				}
				else if( (cbx_ins_folow.checked==false) && !InterestWithSpouse()){
					doJava.optDisabled('cbx_ins_folow');
					return true;
				}
				
			
		} 
		
/* validation field tabs 4 **/
		
		var getValueTabs3 = function()
		{

			var plan_product_id = doJava.dom('plan_product_id');
			var plan_plan 		= doJava.dom('plan_plan');
			var plan_paymode 	= doJava.dom('plan_paymode');
			var plan_paytype 	= doJava.dom('plan_paytype');
			var plan_premi1     = doJava.dom('plan_premi1');
			var plan_premi2     = doJava.dom('plan_premi2');
			var plan_premi3     = doJava.dom('plan_premi3');
			var plan_premi4     = doJava.dom('plan_premi4');
			
			if( plan_product_id.value =='') {plan_product_id.focus(); return false}
			else if( plan_plan.value =='') {plan_plan.focus(); return false}
			else if( plan_paymode.value =='') {plan_paymode.focus(); return false}
			else if( plan_paytype.value =='') {plan_paytype.focus(); return false}
			else{
				return true;
			}
		} 
		
/* validation field tabs 4 **/
		
		var clearTextAge = function(string)
		{
			var ArrString = string.split(' ');
				return ArrString[0].trim();
		}
		
/* validation field tabs 4 **/
		
		var getPrameterInsurance = function()
		{
			var inCheckBoxList = doJava.checkedValue('cbx_ins_folow');
			var insText = inCheckBoxList.split(',');
			var holder_type = new Array();
			var holder_Age  = '';
				
			if( inCheckBoxList.length >0 ){
				for( var i in insText )
				{
					if( insText[i]=='0')
					{
						holder_Age+= "~"+doJava.dom('cb_insurance_sp_holdertype').value+"|"+parseInt(doJava.dom('txt_insurance_sp_age').value);
					}
					else
					{
						holder_Age+= "~"+doJava.dom('cb_insurance_dp'+insText[i]+'_holdertype').value+"|"+parseInt(doJava.dom('txt_insurance_dp'+insText[i]+'_age').value);
					}	
				}
				return holder_Age;
			}	
			
		}  	


		
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
				var payer_address4 = doJava.dom('payer_address4');
				var payer_office_phone = doJava.dom('payer_office_phone');
				var payer_province = doJava.dom('payer_province');
				//var payer_card_prefix = doJava.dom('payer_card_prefix');
				var payer_card_number = doJava.dom('payer_card_number');
				var payer_bank =doJava.dom('payer_bank');
				var payer_fax_number = doJava.dom('payer_fax_number');
				var payer_expired_date= doJava.dom('payer_expired_date');
				var payer_card_type = doJava.dom('payer_card_type');
				var payer_email = doJava.dom('payer_email');
				var payer_holder_idtype=doJava.dom('payer_holder_idtype');
				var payer_idno=doJava.dom('payer_idno');
				var payer_expired_date_split = (payer_expired_date.value?payer_expired_date.value.split('/'):'');
				
				if(openTabs1){				
					//if( payer_salutation.value==''){alert('Payer Title cannot be empty!'); return false;}
					if( payer_mobile_phone.value==''&&payer_home_phone.value==''&&payer_office_phone.value==''){ alert('Please Input One Of Phone Number !'); return false;}
					else if( payer_first_name.value==''){alert('Payer First Name cannot be empty!'); return false;}
					else if( payer_gender.value==''){ alert('Payer Gender cannot be empty!'); return false;}
					else if( payer_dob.value==''){alert('Payer DOB cannot be empty!'); return false;}
					//else if( payer_holder_idtype.value==''){alert('ID Type cannot Be Empty!');return false} 
					//else if( payer_idno.value==''){alert('ID Number cannot Be Empty!'); return false}
					else if( payer_address1.value==''){ alert('Payer Address cannot be empty!'); return false;}
					//else if( payer_mobile_phone.value==''){ alert('Mobile Phone cannot be empty!'); return false;}
					else if( payer_city.value==''){ alert('Payer City cannot be empty!'); return false;}
					//else if( payer_home_phone.value==''){ alert('Payer Home Phone cannot be empty!'); return false;}
					else if( payer_zip_code.value==''){ alert('Payer Zip Code cannot be empty!'); return false;}
					//else if( payer_office_phone.value==''){ alert('Office Phone cannot be empty!'); return false;}
					else if( payer_province.value==''){ alert('Payer Province cannot be empty!'); return false;}
					else if( payer_province.value=='100'){ alert('Payer Province Not Available!'); return false;}
					else if( payer_card_number.value==''){alert('Payer Card Number cannot be empty!'); return false;}
					else if( payer_expired_date.value==''){ alert('Payer Expiration Date cannot be empty!'); return false;}
					else if( payer_card_type.value==''){ alert('Payer Card Type cannot be empty!'); return false;}
					else if(payer_expired_date.value.indexOf("/") < 0){ alert('Payer Expiration Date is not valid'); return false;}
					else if(payer_expired_date.value.length !=5 ){ alert('Payer Expiration Date is less than 5'); return false;}
					else if((parseInt(payer_expired_date_split[0])>12) ){ alert('Month Not Available!'); return false;}
					else if(payer_card_number.value.length <16){  alert('Payer Card Number is less than 16'); return false; }
					else{
						
						if( CcCardValid){ return true; }
						else { 
							alert('Card Number is not valid!')
							return false; 
						}
					}
				}
				else{
					return false;
				}	

		}
		
/* validate next polish **/
		
		var getPremiByPlan=function(planid)
		{
			var campaignid 	= "<?php echo $db->escPost('campaignid');?>";
			var groupType 	= doJava.dom("cb_holder_holdertype").value; 	
			var umur_user  	= doJava.dom("text_dob_size").value;
			var productid  	= doJava.SelArrVal("plan_product_id");
			var paymode	    = doJava.dom("plan_paymode").value;
			var URL_STRING  = groupType+"|"+umur_user+" "+getPrameterInsurance();
				
			doJava.File   = "../class/class.frm.policy.php";
			doJava.Params = {
					action	   : "hitung_premi_customer",
					urlstring  : URL_STRING,
					planid	   : planid, 	
					campaignid : campaignid,
					productid  : productid,
					paymode    : paymode,
					groupType  : groupType
				}
				
				var error_size_data = doJava.eJson();
					doJava.getFormBenefit();
					doJava.dom('callculation_premi').innerHTML= error_size_data.content;
		}
	
/* get premi by plan mode **/
	
	var getPremiByPlanMode = function(value)
	{
		var campaignid 	= CampaignId;
		var groupType 	= doJava.dom("cb_holder_holdertype").value; 	
		var umur_user  	= parseInt(doJava.dom("text_dob_size").value);
		var productid  	= doJava.SelArrVal("plan_product_id");
		var paymode	    = value;
		var URL_STRING  = groupType+"|"+umur_user+" "+getPrameterInsurance();
		var plan_id     = doJava.dom('plan_plan').value;
			
			doJava.File   = "../class/class.frm.policy.php";
			doJava.Params = {
					action : "hitung_premi_customer",
					urlstring : URL_STRING, planid : plan_id, 	
					campaignid : campaignid, productid : productid,
					paymode : paymode, groupType : groupType
				}
				
			var error_size_data = doJava.eJson();
			doJava.dom('callculation_premi').innerHTML= error_size_data.content;
	}	
	
/* validate next polish **/
	
		var NextPolicy = function()
		{
			if(getValueTabs1()){
				$( "#tabs" ).tabs( "option", "disabled", [2, 3,4]);
				if(getValueTabs2())
				{ 
					
				   if( validIputInsured() )
				   {	
						$( "#tabs" ).tabs( "option", "disabled", [3,4]);

						if( getValueTabs3())
							{
								$( "#tabs" ).tabs( "option", "disabled", [4]);
									
									if( getValueTabs4() )
									{ 
										$( "#tabs" ).tabs( "option", "disabled", []);	
											crosscek = true; 
									}
									else{ $( "#tabs" ).tabs( "option", "disabled", [4]);}
								
							}
							else{ $( "#tabs" ).tabs( "option", "disabled", [3,4]); }
					}
					else{
						crosscek = false; 
					}
				}
				else{$( "#tabs" ).tabs( "option", "disabled", [2, 3,4]);}
			}
			else{ $( "#tabs" ).tabs( "option", "disabled", [1,2, 3,4]);}
		}
		
/* validation field tabs 4 **/
	
		var saveCreatePolish = function()
		{
			var insuranceBox  = doJava.checkedValue('cbx_ins_folow');
			var benefBox 	  = doJava.checkedValue('benef_box');
			var ProductId	  = doJava.SelArrVal('plan_product_id');
			var holder_age    = parseInt(doJava.dom('text_dob_size').value);
			var holderIsPayer = doJava.dom('chekclist');
			var datas 		  = getTagNameInput()+" "+getTagNameSelect()+
								"&insuranceBox="+insuranceBox+"&ProductId="+ProductId+
								"&benefBox="+benefBox+"&holderIsPayer="+(holderIsPayer.checked?1:0);
			
				doJava.File  = '../class/class.frm.policy.php';
				doJava.Params = {
					action :'save_create_policy',
					holder_age : holder_age,
					datas : datas,
					customerid : CustomerId,
					campaignid : CampaignId	
				} 
				
				
			
			if( crosscek && validBenefiecery() )
			{	
				var policy_number_html = doJava.dom('policy_number_html');
				var error_datas = doJava.eJson();
				if( error_datas.result==1)
				{
					alert("Success, Create Policy..");
					policy_number_html.innerHTML = "";
					for( var policyId in error_datas.policy)
					{
						error_datas.policy[policyId];
						policy_number_html.innerHTML+="<input type='text'  style='padding:2px;border:1px solid red;margin-bottom:3px;' name='main_cust_policy_number' id='main_cust_policy_number' value='"+error_datas.policy[policyId]+"' disabled><br>"; 
					}
					
					window.close();
					window.opener.focus();
					
				}
				else if(error_datas.result==2){
					alert("Success, Policy Number Alerdy Exist ("+error_datas.policy+")");
					window.close();
					window.opener.focus();
				}
				else
				{
					policy_number_html.innerHTML ="<input type='text' style='padding:2px;border:1px solid red;' name='main_cust_policy_number' id='main_cust_policy_number' value=''>"; 
					alert('Failed, No Prefix number for this product, Contact your System administrator');
				}	
			}
			else{
				alert('Input Not Complete!');
			}
			
		}

/* validation field tabs 4 **/

	var openNewTab =function(){ openTabs2=true; }
		
/* validation field tabs 4 **/
		
		var showPayer = function()
		{
			openTabs1 =true;
			
			var isCheck = doJava.dom('chekclist');
			doJava.File  = '../class/class.frm.tpl.php';
			doJava.Params= 
			{
				action:'get_form_payers',
				customerid:CustomerId,
				payers:(isCheck.checked?1:0),
				frm_payer:FormPayer,
				payer_salutation :doJava.dom('payer_salutation').value.trim(),
				payer_first_name :doJava.dom('frm_holder_firstname').value.trim(),
				payer_last_name :doJava.dom('payer_last_name').value.trim(),
				payer_gender :doJava.dom('frm_holder_gender').value.trim(),
				payer_dob :doJava.dom('payer_dob').value.trim(),
				payer_frm_holder_dob:doJava.dom('frm_holder_dob').value.trim(),
				payer_address1:doJava.dom('payer_address1').value.trim(),
				payer_mobile_phone :doJava.dom('payer_mobile_phone').value.trim(),
				payer_city :doJava.dom('payer_city').value.trim(),
				payer_address2 :doJava.dom('payer_address2').value.trim(),
				payer_home_phone :doJava.dom('payer_home_phone').value.trim(),
				payer_zip_code :doJava.dom('payer_zip_code').value.trim(),
				payer_address3 :doJava.dom('payer_address3').value.trim(),
				payer_address4 :doJava.dom('payer_address4').value.trim(),
				payer_office_phone :doJava.dom('payer_office_phone').value.trim(),
				payer_province :doJava.dom('payer_province').value.trim(),
				payer_card_number :doJava.dom('payer_card_number').value.trim(),
				payer_bank :doJava.dom('payer_bank').value.trim(),
				payer_fax_number :doJava.dom('payer_fax_number').value.trim(),
				payer_expired_date:doJava.dom('payer_expired_date').value.trim(),
				payer_card_type :doJava.dom('payer_card_type').value.trim(),
				payer_email :doJava.dom('payer_email').value.trim(),
				payer_holder_idtype:(isCheck.checked?doJava.dom('cb_holder_idtype').value:doJava.dom('payer_holder_idtype').value.trim()),
				payer_idno:(isCheck.checked?doJava.dom('frm_holder_idno').value:doJava.dom('payer_idno').value.trim()),
				
			}
			//$('#tabs-4').load(doJava.File+'?'+doJava.ArrVal());
			doJava.Load('tabs-4');
			if(!FormPayer)
			{
				FormPayer = true;
			}
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
			if(!isNaN(Rstring) && Rstring != ' '){
				obj.value = obj.value.substring(0,obj.value.length-1);
			}
		} 
		
/* validation field tabs 4 **/
		
		var validIputInsured = function()
		{
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
						//if( cb_insurance_sp_idtype.value=='' ) { alert('ID Type cannot be empty!'); return false;}
						//else if( txt_insurance_sp_idno.value=='' ) { alert('ID No cannot be empty!'); return false;}
						if( cb_insurance_sp_relation.value==''){ alert('Relation cannot be empty!'); return false;}
						//else if( cb_insurance_sp_salut.value==''){alert('Title cannot be empty!'); return false;}
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
						//else if( doJava.dom('cb_insurance_dp'+listBox[i]+'_salut').value==''){ alert('Dependent '+listBox[i]+' :: Title cannot be empty! ')}
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
		
		var validBenefiecery = function()
		{
			var benefValue = doJava.checkedValue('benef_box');
				if( benefValue.length > 0 ){
					var ListView = benefValue.split(',');
				
					for(var i in ListView)
					{
						if( doJava.dom('txt_benef'+ListView[i]+'_holdertype').value==''){ alert('Beneficiery '+ListView[i]+' :: Holder Type is empty');}
						else if( doJava.dom('txt_benef'+ListView[i]+'_rel').value==''){ alert('Beneficiery '+ListView[i]+' :: Relation is empty');}
						//else if( doJava.dom('txt_benef'+ListView[i]+'_title').value==''){ alert('Beneficiery '+ListView[i]+' :: Title is empty');}
						else if( doJava.dom('txt_benef'+ListView[i]+'_first').value==''){ alert('Beneficiery '+ListView[i]+' :: First Name is empty');}
						else if( doJava.dom('txt_benef'+ListView[i]+'_lastname').value==''){ alert('Beneficiery '+ListView[i]+' :: Last Name is empty');}
						else if( doJava.dom('txt_benef'+ListView[i]+'_persen').value==''){ alert('Beneficiery '+ListView[i]+' :: Percentage is empty');}
						//else if( doJava.dom('txt_benef'+ListView[i]+'_persen').value > '100'){ alert('Beneficiery '+ListView[i]+' :: Percentage is Greater');}
						else
						{
							return true;
						}
					}
				}
				else{ return true; }

		}
	
/* validation data ***/
	
	var getNexValidationCard = function(value)
	{
		doJava.File = '../class/class.frm.policy.php';
		doJava.Params = {
			action : 'value_sec_num',
			number : value,
		}
		
		var error = doJava.eJson();
		if( error.result ){
			CcCardValid = true;  
			doJava.dom('error_message_html').innerHTML= "<img src=../gambar/icon/accept.png>";
		}
		else{
			CcCardValid = false;  
			doJava.dom('error_message_html').innerHTML= "<img src=../gambar/icon/cancel.png>"; 
		}
	}
	
	function showFormBenefit()
	{
		var plan_name = doJava.dom('plan_plan').value;
		var product_id = doJava.dom('plan_product_id').value;
		//alert(plan_name);
		if( product_id!='' && plan_name!='' ){
			doJava.File  = '../class/class.frm.tpl.php';
			doJava.Params = {
				action		:'get_form_plan',
				productid	: product_id,
				planbenef	: plan_name
			}

			var error_size_data = doJava.Post();
			doJava.dom('show_plan').innerHTML = error_size_data;
			//alert(error_size_data);
		}
		
		/*var select_id= doJava.SelArrVal(object.name);
				doJava.File = '../class/class.frm.tpl.php';
					doJava.Params = {
						action	: 'get_form_plan',
						productid : select_id
				}
				var error = doJava.Post();
				doJava.dom('show_plan').innerHTML = error;*/
	}
/* set mandatory ****/
	
	var setMandatory = function(object)
	{
		if( object ){
			doJava.dom('frm_holder_idno').style.borderColor='red';
		}
	}	
	 
/* get form benefit **/
	
	doJava.getFormBenefit = function()
	{
		var plan_name = doJava.dom('plan_plan').value;
		var product_id = doJava.dom('plan_product_id').value;
		doJava.File  = '../class/class.frm.tpl.php';
			doJava.Params = {
				action		:'get_form_plan',
				productid	: product_id,
				planbenef	: plan_name
			}
			
			doJava.Load('product_benefit');
	}
	
	var CheckList = function(obj)
	{
		var CheckVal = doJava.checkedValue('cbx_ins_folow');
		var insText  = CheckVal.split(',');
		//alert("This is Object "+obj);
		//alert(CheckVal.length+" = "+insText.length);
		if(obj==0){
			if(!flagsp){
				doJava.dom("cb_insurance_sp_idtype").disabled 		= false;
				doJava.dom("txt_insurance_sp_idno").disabled  		= false;
				doJava.dom("cb_insurance_sp_relation").disabled  	= false;
				doJava.dom("cb_insurance_sp_salut").disabled  		= false;
				doJava.dom("txt_insurance_sp_firstname").disabled  	= false;
				doJava.dom("txt_insurance_sp_lastname").disabled  	= false;
				doJava.dom("txt_insurance_sp_gender").disabled  	= false;
				doJava.dom("txt_insurance_sp_dob").disabled  		= false;
				flagsp = true;
			}else{
				doJava.dom("cb_insurance_sp_idtype").disabled 		= true;
				doJava.dom("txt_insurance_sp_idno").value 			= '';
				doJava.dom("txt_insurance_sp_idno").disabled  		= true;
				doJava.dom("cb_insurance_sp_relation").disabled  	= true;
				doJava.dom("cb_insurance_sp_salut").disabled  		= true;
				doJava.dom("txt_insurance_sp_firstname").value 		= '';
				doJava.dom("txt_insurance_sp_firstname").disabled  	= true;
				doJava.dom("txt_insurance_sp_lastname").value 		= '';
				doJava.dom("txt_insurance_sp_lastname").disabled  	= true;
				doJava.dom("txt_insurance_sp_gender").disabled  	= true;
				doJava.dom("txt_insurance_sp_dob").value  			= '';
				doJava.dom("txt_insurance_sp_dob").disabled  		= true;
				doJava.dom("txt_insurance_sp_age").value	  		= '';
				flagsp = false;
			}
		}
		else{
			for (var i=1;i<=4;i++){
				doJava.dom("cb_insurance_dp"+i+"_rel").disabled  		= true;
				doJava.dom("cb_insurance_dp"+i+"_salut").disabled  		= true;
				doJava.dom("txt_insurance_dp"+i+"_firstname").value 	= '';
				doJava.dom("txt_insurance_dp"+i+"_firstname").disabled  = true;
				doJava.dom("txt_insurance_dp"+i+"_lastname").value 		= '';
				doJava.dom("txt_insurance_dp"+i+"_lastname").disabled  	= true;
				doJava.dom("cb_insurance_dp"+i+"_gender").disabled  	= true;
				doJava.dom("txt_insurance_dp"+i+"_dob").value  			= '';
				doJava.dom("txt_insurance_dp"+i+"_dob").disabled  		= true;
				doJava.dom("txt_insurance_dp"+i+"_age").value	  		= '';
			}
			
			if( CheckVal.length > 0 ){
				for (var i=0;i<insText.length;i++){
					//alert("insText["+i+"] = "+insText[i]);
					if (insText[i]==0){
						i++;
					}
					doJava.dom("cb_insurance_dp"+insText[i]+"_rel").disabled  		= false;
					doJava.dom("cb_insurance_dp"+insText[i]+"_salut").disabled  	= false;
					doJava.dom("txt_insurance_dp"+insText[i]+"_firstname").disabled = false;
					doJava.dom("txt_insurance_dp"+insText[i]+"_lastname").disabled  = false;
					doJava.dom("cb_insurance_dp"+insText[i]+"_gender").disabled  	= false;
					doJava.dom("txt_insurance_dp"+insText[i]+"_dob").disabled  		= false;
				}
			}
			
		}
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
	<body onload="setMandatory();">
	
	<!-- start open info if was creted before ** -->
	<?php
		
		
		$BasicHolder = $getFunction -> getBasicHolder();
		$StartForms  = $getFunction -> getStartForm();
	?>
	<!-- stop open info if was creted before ** -->
	
	  <div id="page_info_header" style="margin:0;margin-top:-2px;"><center>DATA INFORMATION</center></div>
	  <div id="page_info_panel" style="margin-top:1px;">
		<table border=0 width="99%" align="center" cellpadding="2px;">
			<tr>
				<td class="header-text sunah">Policy Number</td>
				<td><span id="policy_number_html"><?php $jpForm -> jpInput('main_cust_policy_number','input',$BasicHolder -> PolicyNumber,NULL,1);?></span></td>
				<td class="header-text sunah">Input date</td>
				<td><?php $jpForm -> jpInput('main_cust_policy_date','input',($BasicHolder -> CustomerUpdatedTs?$db -> formatDateId($BasicHolder -> CustomerUpdatedTs):$db -> formatDateId($StartForms['inputDate'])),NULL,1);?> </td>
				<td class="header-text sunah">Campaign Name</td>
				<td><?php $jpForm -> jpInput('main_cust_policy_campaign','input',$getFunction->getCampaignName(),NULL,1);?></td>
			</tr>
			<tr>
				<td class="header-text sunah">Telemarketer</td>
				<td><?php $jpForm -> jpInput('main_cust_policy_user','input',($BasicHolder->CignaUser!=''?$BasicHolder->CignaUser:$db->getSession('username')),NULL,1);?></td>
				<td class="header-text sunah">Effective Date</td>
				<td> <?php $jpForm -> jpInput('main_cust_policy_efective','input',($BasicHolder -> PolicyEffectiveDate?$db -> formatDateId($BasicHolder -> PolicyEffectiveDate):$StartForms['efectiveDate']),NULL,1);?></td>
				<td class="header-text sunah">&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</div>
	
	<!-- start : tab HHolder -->
	
	<div id="tabs" style="margin-top:10px;margin-left:6px;margin-right:6px;">
		<ul>
			<li><a href="#tabs-1"> HOLDER</a></li>
			<li><a href="#tabs-2" onclick="openNewTab();"> INSURED</a></li>
			<li><a href="#tabs-3"> PLAN</a></li>
			<li><a href="#tabs-4" onclick="showPayer();"> PAYER AND ADDRESS INFO</a></li>
			<li><a href="#tabs-5"> BENEFICIARY</a></li>
		</ul>
		
    <div id="tabs-1">
			<table width="99%" border=0>
			<tr><td class="header-text wajib">Holder Type</td>
				<td style="height:30px;"><?php $jpForm -> jpCombo('cb_holder_holdertype',NULL,$getFunction->getHolderType(),'2',NULL,1); ?>
				<input type="checkbox" name="chekclist" id="chekclist" onchange="setMandatory(this.checked);" style="text-align:left;margin:0px;border;1px solid #000;" checked=true disabled=true> &nbsp; Holder = Payer </td>
				<td class="header-text wajib">First Name</td>
				<!--<td style="height:30px;"><input type="text" class="input" onkeyup="isStrValue(this);" name="frm_holder_firstname" id="frm_holder_firstname" style="width:200px;" value="<?php //echo $getFunction->CustData->CustomerFirstName; ?>" disabled></td>-->
				<td style="height:30px;"><input type="text" class="input" name="frm_holder_firstname" id="frm_holder_firstname" style="width:200px;" value="<?php echo $getFunction->CustData->CustomerFirstName; ?>"></td>
				<td class="header-text sunah" style="display:none;">Last Name</td>
				<td style="height:30px;"><input type="text" style="display:none;" class="input" onkeyup="isStrValue(this);" name="frm_holder_lastname" id="frm_holder_lastname" style="width:200px;" value="<?php echo $getFunction->CustData->CustomerLastName; ?>"></td>
			</tr>
			<tr>
				<td class="header-text wajib">ID-Type</td>
				<td style="height:30px;"><?php $jpForm -> jpCombo('cb_holder_idtype',NULL, $getFunction -> getIdType(),$getFunction ->CustData ->IdentificationTypeId ); ?> </td>
				<td class="header-text wajib">Relation</td>
				<td style="height:30px;"><?php $jpForm -> jpCombo('frm_holder_rel',NULL, $getFunction -> getRelation(),$getFunction ->CustData ->RelationId); ?> </td>
				<td class="header-text wajib">DOB</td>
				<td style="height:30px;"><input type="text" class="input" name="frm_holder_dob" id="frm_holder_dob" value="<?php echo $db ->formatDateId($getFunction->CustData->CustomerDOB); ?>" readonly>
					<img src="<?php echo $app->basePath();?>gambar/calendar.gif"> <input type="text" id="text_dob_size" name="text_dob_size" class="input_box age" value="0">
				</td>
			</tr>
			<tr>
				<td class="header-text wajib">ID-No</td>
				<td style="height:30px;">
					<input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" class="input" onkeyup="isNumber(this);" name="frm_holder_idno" value="<?php echo $BasicHolder->InsuredIdentificationNum; ?>" id="frm_holder_idno">
				</td>
				<td class="header-text wajib">Gender</td>
				<td style="height:30px;"><?php $jpForm -> jpCombo('frm_holder_gender',NULL,$getFunction->getGender(),$getFunction ->CustData ->GenderId); ?> </td>
				<td class="header-text wajib">Title</td>
				<td style="height:30px;"><?php $jpForm -> jpCombo('frm_holder_title',NULL,$getFunction->getSalutation(),$getFunction ->CustData ->SalutationId); ?></td>
			</tr>
			</table>
	</div>
	<!-- stop : tab HHolder -->
	
	<!-- start: Insurance -->
	<div id="tabs-2" onclick="openNewTab();" style="height:430px;overflow:auto;">
       <table width="99%" align="center" style="border:1px dotted #dddddd;">
			<tr>
				<td style="text-align:right;height:30px;border:1px dotted #dddddd;"  valign="top">
					<table>
						<tr>
							<td class="header-top"><h4>SPOUSE</h4></td>
							<td><input type="checkbox" name="cbx_ins_folow" id="cbx_ins_folow" value="0" onclick="CheckList(this.value);"></td>
						</tr>
						<tr>
							<td class="header-text sunah">Holder Type</td>
							<td><?php $jpForm -> jpCombo('cb_insurance_sp_holdertype',NULL, $getFunction->getHolderType(),3,NULL,1); ?></td>
						</tr>
						<tr>
							<td class="header-text <?php echo ($_REQUEST['callstatus']==402?"wajib":"sunah"); ?>">ID Type</td>
							<td><?php $jpForm -> jpCombo('cb_insurance_sp_idtype',NULL, $getFunction -> getIdType(),0,NULL,1); ?> </td>
						</tr>
						<tr>
							<td class="header-text <?php echo ($_REQUEST['callstatus']==402?"wajib":"sunah"); ?>">ID No</td>
							<td>
								<input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled name="txt_insurance_sp_idno" onkeyup="isNumber(this);" id="txt_insurance_sp_idno" class="input ">
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Relation</td>
							<td><?php $jpForm -> jpCombo('cb_insurance_sp_relation',NULL, $getFunction -> getRelation(),0,NULL,1); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">Title</td>
							<td><?php $jpForm -> jpCombo('cb_insurance_sp_salut',NULL, $getFunction->getSalutation(),0,NULL,1); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">First Name</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled id="txt_insurance_sp_firstname" onkeyup="isStrValue(this);" name="txt_insurance_sp_firstname" class="input "></td>
						</tr>
						<tr>
							<td class="header-text sunah">Last Name</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  onpaste="return false;" oncopy="return false;" oncut="return false;"  name="txt_insurance_sp_lastname" onkeyup="isStrValue(this);" id="txt_insurance_sp_lastname" class="input "></td>
						</tr>
						<tr>
							<td class="header-text sunah">Gender</td>
							<td><?php $jpForm -> jpCombo('txt_insurance_sp_gender',NULL,$getFunction->getGender(),0,NULL,1); ?></td>
						</tr>
						<tr>
							<td class="header-text sunah">DOB</td>
							<td>
								<input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_insurance_sp_dob" id="txt_insurance_sp_dob"  class="input ">
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Age</td>
							<td>
								<input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;"  disabled name="txt_insurance_sp_age" id="txt_insurance_sp_age" class="input age" readonly>
							</td>
						</tr>
						
						
						<tr>
							<td></td>
							<td></td>
						</tr>
						
					</table>
				</td>
				<td style="height:30px;border:1px dotted #dddddd;" valign="top" >
					<table>
						<tr>
							<td><h4>DEPENDENT 1</h4></td>
							<td><input type="checkbox" name="cbx_ins_folow" id="cbx_ins_folow" value="1" onclick="CheckList(this.value);"></td>
						</tr>
						<tr>
							<td class="header-text sunah">Holder Type</td>
							<td><?php $jpForm -> jpCombo('cb_insurance_dp1_holdertype',NULL, $getFunction->getHolderType(),1,NULL,1); ?>
								<!--<select id="cb_insurance_dp1_holdertype" name="cb_insurance_dp1_holdertype" disabled>
									<#?php $getFunction->getHolderType(1); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Relation</td>
							<td><?php $jpForm -> jpCombo('cb_insurance_dp1_rel',NULL, $getFunction -> getRelation(),0,NULL,1); ?>
								<!--<select id="cb_insurance_dp1_rel" name="cb_insurance_dp1_rel">
									<#?php $getFunction->getRelation(1); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Title</td>
							<td><?php $jpForm -> jpCombo('cb_insurance_dp1_salut',NULL, $getFunction->getSalutation(),0,NULL,1); ?>
								<!--<select id="cb_insurance_dp1_salut" name="cb_insurance_dp1_salut">
									<#?php $getFunction->getSalutation(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">First Name</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_insurance_dp1_firstname" class="input" onkeyup="isStrValue(this);" id="txt_insurance_dp1_firstname"></td>
						</tr>
						<tr>
							<td class="header-text sunah">Last Name</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_insurance_dp1_lastname" class="input" onkeyup="isStrValue(this);" id="txt_insurance_dp1_lastname"></td>
						</tr>
						<tr>
							<td class="header-text sunah">Gender</td>
							<td><?php $jpForm -> jpCombo('cb_insurance_dp1_gender',NULL,$getFunction->getGender(),0,NULL,1); ?>
								<!--<select id="cb_insurance_dp1_gender" name="cb_insurance_dp1_gender">
									<#?php $getFunction->getGender(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">DOB</td>
							<td>
								<input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_insurance_dp1_dob" id="txt_insurance_dp1_dob" class="input">
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Age</td>
							<td>
								<input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_insurance_dp1_age" id="txt_insurance_dp1_age" class="input age" readonly>
							</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
						</tr>
						
					</table>
				
				</td>
				<td style="height:30px;border:1px dotted #dddddd;" valign="top" >
					<table>
						<tr>
							<td><h4>DEPENDENT 2</h4></td>
							<td><input type="checkbox" name="cbx_ins_folow" id="cbx_ins_folow" value="2" onclick="CheckList(this.value);"></td>
						</tr>
						<tr>
							<td class="header-text sunah">Holder Type</td>
							<td><?php $jpForm -> jpCombo('cb_insurance_dp2_holdertype',NULL, $getFunction->getHolderType(),1,NULL,1); ?>
								<!--<select id="cb_insurance_dp2_holdertype" name="cb_insurance_dp2_holdertype" disabled>
									<#?php $getFunction->getHolderType(1); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Relation</td>
							<td><?php $jpForm -> jpCombo('cb_insurance_dp2_rel',NULL, $getFunction -> getRelation(),0,NULL,1); ?>
								<!--<select id="cb_insurance_dp2_rel" name="cb_insurance_dp2_rel">
									<#?php $getFunction->getRelation(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Title</td>
							<td><?php $jpForm -> jpCombo('cb_insurance_dp2_salut',NULL, $getFunction->getSalutation(),0,NULL,1); ?>
								<!--<select id="cb_insurance_dp2_salut" name="cb_insurance_dp2_salut">
									<#?php $getFunction->getSalutation(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">First Name</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_insurance_dp2_firstname" onkeyup="isStrValue(this);" id="txt_insurance_dp2_firstname" class="input "></td>
						</tr>
						<tr>
							<td class="header-text sunah">Last Name</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_insurance_dp2_lastname" onkeyup="isStrValue(this);" id="txt_insurance_dp2_lastname" class="input "></td>
						</tr>
						<tr>
							<td class="header-text sunah">Gender</td>
							<td><?php $jpForm -> jpCombo('cb_insurance_dp2_gender',NULL,$getFunction->getGender(),0,NULL,1); ?>
								<!--<select id="cb_insurance_dp2_gender" name="cb_insurance_dp2_gender">
									<#?php $getFunction->getGender(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">DOB</td>
							<td>
								<input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_insurance_dp2_dob" id="txt_insurance_dp2_dob" class="input" onclick="getUmurSizeFunc(this.name,'txt_insurance_dp2_age',1);">
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Age</td>
							<td>
								<input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_insurance_dp2_age" id="txt_insurance_dp2_age" class="input age" readonly>
							</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
						</tr>
						
					</table>
				
				</td>
			</tr>
			<tr>
				<td  style="text-align:right;height:30px;border:1px dotted #dddddd;" valign="top" >
					<table>
						<tr>
							<td><h4>DEPENDENT 3</h4></td>
							<td><input type="checkbox" name="cbx_ins_folow" id="cbx_ins_folow" value="3" onclick="CheckList(this.value);"></td>
						</tr>
						<tr>
							<td class="header-text sunah">Holder Type</td>
							<td><?php $jpForm -> jpCombo('cb_insurance_dp3_holdertype',NULL, $getFunction->getHolderType(),1,NULL,1); ?>
								<!--<select id="cb_insurance_dp3_holdertype" name="cb_insurance_dp3_holdertype" disabled>
									<#?php $getFunction->getHolderType(1); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Relation</td>
							<td><?php $jpForm -> jpCombo('cb_insurance_dp3_rel',NULL, $getFunction -> getRelation(),0,NULL,1); ?>
								<!--<select id="cb_insurance_dp3_rel" name="cb_insurance_dp3_rel">
									<#?php $getFunction->getRelation(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Title</td>
							<td><?php $jpForm -> jpCombo('cb_insurance_dp3_salut',NULL, $getFunction->getSalutation(),0,NULL,1); ?>
								<!--<select id="cb_insurance_dp3_salut" name="cb_insurance_dp3_salut">
									<#?php $getFunction->getSalutation(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">First Name</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;"  disabled name="txt_insurance_dp3_firstname" onkeyup="isStrValue(this);" id="txt_insurance_dp3_firstname" class="input "></td>
						</tr>
						<tr>
							<td class="header-text sunah">Last Name</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_insurance_dp3_lastname" onkeyup="isStrValue(this);" id="txt_insurance_dp3_lastname" class="input "></td>
						</tr>
						<tr>
							<td class="header-text sunah">Gender</td>
							<td><?php $jpForm -> jpCombo('cb_insurance_dp3_gender',NULL,$getFunction->getGender(),0,NULL,1); ?>
								<!--<select id="cb_insurance_dp3_gender" name="cb_insurance_dp3_gender">
									<#?php $getFunction->getGender(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">DOB</td>
							<td>
								<input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_insurance_dp3_dob" id="txt_insurance_dp3_dob" class="input" onclick="getUmurSizeFunc(this.name,'txt_insurance_dp3_age',1);" >
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Age</td>
							<td>
								<input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_insurance_dp3_age" id="txt_insurance_dp3_age" class="input age" readonly>
							</td>
						</tr>
						<tr>
							<td></td>
							<td></td>
						</tr>
						
					</table>
				</td>
				<td valign="top" style="height:30px;border:1px dotted #dddddd;">
					<table>
						<tr>
							<td><h4>DEPENDENT 4</h4></td>
							<td><input type="checkbox" name="cbx_ins_folow" id="cbx_ins_folow" value="4" onclick="CheckList(this.value);"></td>
						</tr>
						<tr>
							<td class="header-text sunah">Holder Type</td>
							<td><?php $jpForm -> jpCombo('cb_insurance_dp4_holdertype',NULL, $getFunction->getHolderType(),1,NULL,1); ?>
								<!--<select id="cb_insurance_dp4_holdertype" name="cb_insurance_dp4_holdertype" disabled>
									<#?php $getFunction->getHolderType(1); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Relation</td>
							<td><?php $jpForm -> jpCombo('cb_insurance_dp4_rel',NULL, $getFunction -> getRelation(),0,NULL,1); ?>
								<!--<select id="cb_insurance_dp4_rel" name="cb_insurance_dp4_rel">
									<#?php $getFunction->getRelation(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Title</td>
							<td><?php $jpForm -> jpCombo('cb_insurance_dp4_salut',NULL, $getFunction->getSalutation(),0,NULL,1); ?>
								<!--<select id="cb_insurance_dp4_salut" name="cb_insurance_dp4_salut">
									<#?php $getFunction -> getSalutation(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">First Name</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;"  disabled name="txt_insurance_dp4_firstname"  onkeyup="isStrValue(this);" id="txt_insurance_dp4_firstname" class="input "></td>
						</tr>
						<tr>
							<td class="header-text sunah">Last Name</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_insurance_dp4_lastname" onkeyup="isStrValue(this);" id="txt_insurance_dp4_lastname" class="input "></td>
						</tr>
						<tr>
							<td class="header-text sunah">Gender</td>
							<td><?php $jpForm -> jpCombo('cb_insurance_dp4_gender',NULL,$getFunction->getGender(),0,NULL,1); ?>
								<!--<select id="cb_insurance_dp4_gender" name="cb_insurance_dp4_gender">
									<#?php $getFunction->getGender(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">DOB</td>
							<td>
								<input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_insurance_dp4_dob" id="txt_insurance_dp4_dob" class="input" onclick="getUmurSizeFunc(this.name,'txt_insurance_dp4_age',1);">
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Age</td>
							<td>
								<input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;"  disabled name="txt_insurance_dp4_age" id="txt_insurance_dp4_age" class="input age" readonly>
							</td>
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
	
	<!-- start : tab3 -->
    
	<div id="tabs-3">
		<table width="100%" align="center" border=0>
			<tr>
				<td class="header-text wajib" valign="top">Product</td>
				<td style="height:30px;" ><?php $jpForm -> jpMultiple('plan_product_id', 'multiple',$getFunction -> getProductByCampaign(),NULL,'onchange="getPlanByProduct(this);"');?> </td>
				<td class="header-text wajib" valign="bottom">Plan</td>
				<td style="height:30px;" id="html_inner_plan" valign="bottom"> <?php $jpForm -> jpCombo('plan_plan', NULL, NULL, NULL,'onchange="showFormBenefit(this);"');?></td>
				<td class="header-text wajib" valign="bottom">Pay Mode</td>
				<td style="height:30px;" valign="bottom"> <?php $jpForm -> jpCombo('plan_paymode', NULL, $getFunction->getPayMode(), NULL,'onchange="getPremiByPlanMode(this.value);"');?></td>
				
			</tr>
			<tr>
				<td class="header-text wajib">Pay Type</td>
				<td style="height:30px;"><?php $jpForm -> jpCombo('plan_paytype', NULL, $getFunction->getPayType(),'1');?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="height:30px;" align="right" colspan="2"><div id="callculation_premi"></div></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<!--<div id="show_plan" style="height:200px;" align="right"></div>-->
		<div id="show_plan" style="height:120px;" align="right"></div>
	</div>
	
	<!-- stop : tab3 -->
	
	<!-- start : tab4 -->
	
	
	<div id="tabs-4"> </div>
	
	
	<!-- stop : tab5 -->	
	<div id="tabs-5" style="height:430px;overflow:auto;">
			<table width="99%">
				<tr><td>
					<table>
						<tr>
							<td><h4 >BENEFICIARY 1</h4> </td>
							<td><input type="checkbox" name="benef_box" id="benef_box" value="1" disabled></td>
						</tr>
						<tr>
							<td class="header-text sunah">Holder Type</td>
							<td><?php $jpForm -> jpCombo('txt_benef1_holdertype',NULL, $getFunction->getHolderType(),1,NULL,1); ?>
								<!--<select id="txt_benef1_holdertype" name="txt_benef1_holdertype">
									<#?php $getFunction->getHolderType(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Relation</td>
							<td><?php $jpForm -> jpCombo('txt_benef1_rel',NULL, $getFunction -> getRelation(),0,NULL,1); ?>
								<!--<select id="txt_benef1_rel" name="txt_benef1_rel">
									<#?php $getFunction->getRelation(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Title</td>
							<td><?php $jpForm -> jpCombo('txt_benef1_title',NULL, $getFunction->getSalutation(),0,NULL,1); ?>
								<!--<select id="txt_benef1_title" name="txt_benef1_title">
									<#?php $getFunction->getSalutation(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah" class="input">First Name</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_benef1_first" onkeyup="isStrValue(this);" id="txt_benef1_first" class="input"></td>
						</tr>
						<tr>
							<td class="header-text sunah" class="input">Last Name</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;"  disabled name="txt_benef1_lastname" onkeyup="isStrValue(this);" id="txt_benef1_lastname" class="input"></td>
						</tr>
						<tr>
							<td class="header-text sunah" class="input">Percentage</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_benef1_persen" onkeyup="isNumber(this);" id="txt_benef1_persen" class="input" style="width:40px;">&nbsp;%</td>
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
							<td> <input type="checkbox" name="benef_box" id="benef_box" value="2" disabled></td>
						</tr>
						<tr>
							<td class="header-text sunah">Holder Type</td>
							<td><?php $jpForm -> jpCombo('txt_benef2_holdertype',NULL, $getFunction->getHolderType(),1,NULL,1); ?>
								<!--<select id="txt_benef2_holdertype" name="txt_benef2_holdertype">
									<#?php $getFunction->getHolderType(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Relation</td>
							<td><?php $jpForm -> jpCombo('txt_benef2_rel',NULL, $getFunction -> getRelation(),0,NULL,1); ?>
								<!--<select id="txt_benef2_rel" name="txt_benef2_rel">
									<#?php $getFunction->getRelation(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Title</td>
							<td><?php $jpForm -> jpCombo('txt_benef2_title',NULL, $getFunction->getSalutation(),0,NULL,1); ?>
								<!--<select id="txt_benef2_title" name="txt_benef2_title">
									<#?php $getFunction->getSalutation(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">First Name</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;"  disabled name="txt_benef2_first" onkeyup="isStrValue(this);" id="txt_benef2_first" class="input"></td>
						</tr>
						<tr>
							<td class="header-text sunah" >Last Name</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;"  disabled name="txt_benef2_lastname" onkeyup="isStrValue(this);" id="txt_benef2_lastname" class="input"></td>
						</tr>
						<tr>
							<td class="header-text sunah" class="input">Percentage</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_benef2_persen" onkeyup="isNumber(this);" id="txt_benef2_persen" class="input" style="width:40px;">&nbsp;%</td>
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
							<td> <input type="checkbox" name="benef_box" id="benef_box" value="3" disabled> </td>
						</tr>
						<tr>
							<td class="header-text sunah">Holder Type</td>
							<td><?php $jpForm -> jpCombo('txt_benef3_holdertype',NULL, $getFunction->getHolderType(),1,NULL,1); ?>
								<!--<select id="txt_benef3_holdertype" name="txt_benef3_holdertype">
									<#?php $getFunction->getHolderType(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Relation</td>
							<td><?php $jpForm -> jpCombo('txt_benef3_rel',NULL, $getFunction -> getRelation(),0,NULL,1); ?>
								<!--<select id="txt_benef3_rel" name="txt_benef3_rel">
									<#?php $getFunction->getRelation(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Title</td>
							<td><?php $jpForm -> jpCombo('txt_benef3_title',NULL, $getFunction->getSalutation(),0,NULL,1); ?>
								<!--<select id="txt_benef3_title" name="txt_benef3_title">
									<#?php $getFunction->getSalutation(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">First Name</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;"  disabled name="txt_benef3_first" onkeyup="isStrValue(this);" id="txt_benef3_first" class="input"></td>
						</tr>
						<tr>
							<td class="header-text sunah" >Last Name</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_benef3_lastname" onkeyup="isStrValue(this);" id="txt_benef3_lastname" class="input"></td>
						</tr>
						<tr>
							<td class="header-text sunah" class="input">Percentage</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;"  disabled name="txt_benef3_persen" onkeyup="isNumber(this);" id="txt_benef3_persen" class="input" style="width:40px;">&nbsp;%</td>
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
							<td><input type="checkbox" name="benef_box" id="benef_box" value="4" disabled></td>
						</tr>
						<tr>
							<td class="header-text sunah">Holder Type</td>
							<td><?php $jpForm -> jpCombo('txt_benef4_holdertype',NULL, $getFunction->getHolderType(),1,NULL,1); ?>
								<!--<select id="txt_benef4_holdertype" name="txt_benef4_holdertype">
									<#?php $getFunction->getHolderType(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Relation</td>
							<td><?php $jpForm -> jpCombo('txt_benef4_rel',NULL, $getFunction -> getRelation(),0,NULL,1); ?>
								<!--<select id="txt_benef4_rel" name="txt_benef4_rel">
									<#?php $getFunction->getRelation(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Title</td>
							<td><?php $jpForm -> jpCombo('txt_benef4_title',NULL, $getFunction->getSalutation(),0,NULL,1); ?>
								<!--<select id="txt_benef4_title" name="txt_benef4_title">
									<#?php $getFunction->getSalutation(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">First Name</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_benef4_first" onkeyup="isNumber(this);" id="txt_benef4_first" class="input"></td>
						</tr>
						<tr>
							<td class="header-text sunah" >Last Name</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;"  disabled name="txt_benef4_lastname" onkeyup="isStrValue(this);" id="txt_benef4_lastname" class="input"></td>
						</tr>
						<tr>
							<td class="header-text sunah" class="input">Percentage</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_benef4_persen" onkeyup="isStrValue(this);" id="txt_benef4_persen" class="input" style="width:40px;">&nbsp;%</td>
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
							<td><input type="checkbox" name="benef_box" id="benef_box" value="5" disabled></td>
						</tr>
						<tr>
							<td class="header-text sunah">Holder Type</td>
							<td><?php $jpForm -> jpCombo('txt_benef5_holdertype',NULL, $getFunction->getHolderType(),1,NULL,1); ?>
								<!--<select id="txt_benef5_holdertype" name="txt_benef5_holdertype">
									<#?php $getFunction->getHolderType(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Relation</td>
							<td><?php $jpForm -> jpCombo('txt_benef5_rel',NULL, $getFunction -> getRelation(),0,NULL,1); ?>
								<!--<select id="txt_benef5_rel" name="txt_benef5_rel">
									<#?php $getFunction->getRelation(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">Title</td>
							<td><?php $jpForm -> jpCombo('txt_benef5_title',NULL, $getFunction->getSalutation(),0,NULL,1); ?>
								<!--<select id="txt_benef5_title" name="txt_benef5_title">
									<#?php $getFunction->getSalutation(); ?>
								</select>-->
							</td>
						</tr>
						<tr>
							<td class="header-text sunah">First Name</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_benef5_first" id="txt_benef5_first" onkeyup="isStrValue(this);" class="input"></td>
						</tr>
						<tr>
							<td class="header-text sunah" >Last Name</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_benef5_lastname" id="txt_benef5_lastname" onkeyup="isStrValue(this);"class="input" ></td>
						</tr>
						<tr>
							<td class="header-text sunah" class="input">Percentage</td>
							<td><input type="text" onpaste="return false;" oncopy="return false;" oncut="return false;" disabled  name="txt_benef5_persen" onkeyup="isNumber(this);" id="txt_benef5_persen" class="input" style="width:40px;">&nbsp;%</td>
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

<div> 
<div style="float:right;">	
	<a href="javascript:void(0);" class="sbutton" onclick="javascript:NextPolicy();" style="margin:4px;"><span>&nbsp;Next</span></a> &nbsp;
	<a href="javascript:void(0);" class="sbutton" onclick="javascript:doJava.winew.winClose();" style="margin:4px;"><span>&nbsp;Exit</span></a> &nbsp;
	<a href="javascript:void(0);" class="sbutton" onclick="javascript:saveCreatePolish();" style="margin:4px;"><span>&nbsp;Save</span></a> &nbsp;
</div>	
	
</body>
</html>