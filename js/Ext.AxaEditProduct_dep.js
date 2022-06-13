/***
	update : 18-08-2015
	line : 364 - 390
***/
var sv ="1";
var uw = "2";
// define dependent field
 Ext.DOM._benefieceryEdit = {
	field :[
		 'EditBenefRelationshipTypeId', 
		 'EditBenefSalutationId', 
		 'EditBenefFirstName', 
		 'EditBenefLastName',
		 'EditBenefGenderId',
		 'EditBenefPercentage'
		],
		 
	chars : 'Be',
	code  : 0
 }	
 
 // define dependent field
 Ext.DOM._benefieceryAdd = {
	field :[
		 'AddBenefRelationshipTypeId', 
		 'AddBenefSalutationId', 
		 'AddBenefFirstName', 
		 'AddBenefLastName', 
		 'AddBenefGenderId', 
		 'AddBenefPercentage'
		],
		 
	chars : 'Be',
	code  : 0
 }
 
var Mandat_SurveyQuest = [];
var Mandat_SurveyAns = [];
var PayMethod = [];
var card_type;

 
/* @ Ext /EUI :	frame work ready function 
 * @ Will render on global definition 
 */
Ext.DOM.Insured = {
	field :
	{
		InsuredIdentificationTypeId : { keys : false, warn : 'ID Type', number: false , clear: true},
		InsuredIdentificationNum : { keys : false, warn : 'ID No ', number: false, clear: true },
		InsuredRelationshipTypeId : { keys : false, warn : 'Relation', number: false, clear: true },
		InsuredSalutationId : { keys : false, warn : 'Title', number: false, clear: true },
		InsuredFirstName : { keys : false, warn : 'First Name ', number: false, clear: true },
		InsuredLastName : { keys : false, warn : 'Last Name', number: false, clear: true },
		InsuredGenderId : { keys : false, warn : 'Gender', number: false, clear: true },
		InsuredDOB : { keys : false, warn : 'DOB', number: false, clear: true },
		InsuredAge : { keys : false, warn : 'Age', number: true, clear: true },
		InsuredPayMode : { keys : false, warn : 'Payment Mode', number: false, clear: false },
		InsuredPlanType : { keys : false, warn : 'Plan Type', number: false, clear: false },
		InsuredPremi : { keys : false, warn : 'Premi', number: false, clear: true }
	},
		
	chars : 'Ho',		
	code  : 2
 }	
 
/* @ Ext /EUI  : frame work ready function 
 * @ def	   : payer object 	 
 */
 
 Ext.DOM.Payer = 
 {
	'PayerSalutationId' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayerFirstName' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayerLastName' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayerGenderId' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayerDOB' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayerAddressLine1' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayerIdentificationTypeId' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayerIdentificationNum' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayerMobilePhoneNum' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayerCity' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayerAddressLine2' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayerHomePhoneNum' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayerZipCode' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayerAddressLine3' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayerOfficePhoneNum' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayerProvinceId' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayerAddressLine4' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayerCreditCardNum' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayersBankId' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayerFaxNum' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayerCreditCardExpDate' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'CreditCardTypeId' : { keys : false, warn : 'ID Type', number: false , clear: true },
	'PayerEmail': { keys : false, warn : 'ID Type', number: false , clear: true }
}

 
/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */
 
Ext.DOM.FormBenefieceryEdit = function( checkbox, p)
{
	if( checkbox.checked )
	{
		for(var i in Ext.DOM._benefieceryEdit.field ){
			Ext.Cmp(Ext.DOM._benefieceryEdit.field[i]+"_"+p).disabled(false);
		}
	}
	else
	{
		for(var i in Ext.DOM._benefieceryEdit.field ) 
		{
			Ext.Cmp(Ext.DOM._benefieceryEdit.field[i]+"_"+p).disabled(true);
			if( Ext.Cmp(Ext.DOM._benefieceryEdit.field[2]+"_"+p).empty()){
				Ext.Cmp(Ext.DOM._benefieceryEdit.field[i]+"_"+p).setValue('');
			}
		}
	}
};
 
/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */
 
Ext.DOM.FormBenefieceryAdd = function( checkbox, p)
{
	if( checkbox.checked )
	{
		for(var i in Ext.DOM._benefieceryAdd.field ){
			Ext.Cmp(Ext.DOM._benefieceryAdd.field[i]+"_"+p).disabled(false);
		}
	}
	else
	{
		for(var i in Ext.DOM._benefieceryAdd.field ) 
		{
			Ext.Cmp(Ext.DOM._benefieceryAdd.field[i]+"_"+p).disabled(true);
			if( Ext.Cmp(Ext.DOM._benefieceryAdd.field[2]+"_"+p).empty()){
				Ext.Cmp(Ext.DOM._benefieceryAdd.field[i]+"_"+p).setValue('');
			}
		}
	}
	
	Ext.DOM.PercentageAdd();
};
/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */
 
 Ext.DOM.PercentageAdd = function() 
 {
 	// var total = 4 - _box.length;
	var _box = Ext.Cmp('AddBenefeciery').getName(), 
		_tot = 0, _percent = 100, 
		_percent_personal = 0 , 
		_totalsChecked = 4 - _box.length;

		for(var frm = 0; frm < _box.length; frm++ ) {
			if( _box[frm].checked ) 
			  _totalsChecked++;
		}
		// alert(_totalsChecked);exit();
		
		_tot = 	parseInt( _percent ) / parseInt( _totalsChecked );
		for(var box = 0; box < _box.length; box++ ) 
		{
			if( _box[box].checked )
			{
				var position = _box[box].getAttribute('onclick').split(',')[1].replace(/\);/g, '');
				Ext.Cmp('AddBenefPercentage_' + position).setValue(_tot.toFixed(2)); 
			}	
		}		
 };

/* 
 * @ Ext /EUI :	frame work ready function 
 * @ Will render on global definition 
 */
 
Ext.DOM.CopyDataPayer2EditHolder = function(opt) {
	(opt.checked ? (
		Ext.Cmp('HolderFirstName').setValue(Ext.Cmp('PayerFirstName').getValue()),
		Ext.Cmp('HolderLastName').setValue(Ext.Cmp('PayerLastName').getValue()),
		Ext.Cmp('HolderGenderId').setValue(Ext.Cmp('PayerGenderId').getValue()),
		Ext.Cmp('HolderPOB').setValue(Ext.Cmp('PayerPOB').getValue()),
		Ext.Cmp('HolderDOB').setValue(Ext.Cmp('PayerDOB').getValue()),
		// / Ext.Cmp('HolderPosition').setValue(Ext.Cmp('PayerIdentificationTypeId').getValue()),
		// Ext.Cmp('HolderOccupation').setValue(Ext.Cmp('PayerIdentificationTypeId').getValue()),
		// Ext.Cmp('HolderIncome').setValue(Ext.Cmp('PayerIdentificationTypeId').getValue()),
		// Ext.Cmp('HolderCompany').setValue(Ext.Cmp('PayerIdentificationTypeId').getValue()),
		Ext.Cmp('HolderMobilePhoneNum').setValue(Ext.Cmp('PayerMobilePhoneNum').getValue()),
		Ext.Cmp('HolderMaritalStatus').setValue(Ext.Cmp('PayerMaritalStatus').getValue()),
		Ext.Cmp('HolderIdentificationTypeId').setValue(Ext.Cmp('PayerIdentificationTypeId').getValue()),
		Ext.Cmp('HolderIdentificationNum').setValue(Ext.Cmp('PayerIdentificationNum').getValue()),
		// Ext.Cmp('HolderRelationshipTypeId').setValue(Ext.Cmp('PayerIdentificationTypeId').getValue()),
		Ext.Cmp('HolderAddrType').setValue(Ext.Cmp('PayerAddrType').getValue()),
		Ext.Cmp('HolderAddressLine1').setValue(Ext.Cmp('PayerAddressLine1').getValue()),
		Ext.Cmp('HolderAddressLine2').setValue(Ext.Cmp('PayerAddressLine2').getValue()),
		Ext.Cmp('HolderAddressLine3').setValue(Ext.Cmp('PayerAddressLine3').getValue()),
		Ext.Cmp('HolderAddressLine4').setValue(Ext.Cmp('PayerAddressLine4').getValue()),
		Ext.Cmp('HolderProvinceId').setValue(Ext.Cmp('PayerProvinceId').getValue()),
		Ext.Cmp('HolderCity').setValue(Ext.Cmp('PayerCity').getValue()),
		// Ext.Cmp('HolderEmail').setValue(Ext.Cmp('PayerEmail').getValue()),
		Ext.Cmp('HoldersBankId').setValue(Ext.Cmp('PayersBankId').getValue()),
		// Ext.Cmp('HolderBankBranch').setValue(Ext.Cmp('PayerIdentificationTypeId').getValue()),
		Ext.Cmp('HolderCreditCardNum').setValue(Ext.Cmp('PayerCreditCardNum').getValue()),
		Ext.Cmp('HolderOfficePhoneNum').setValue(Ext.Cmp('PayerOfficePhoneNum').getValue()),
		Ext.Cmp('HolderCreditCardTypeId').setValue(Ext.Cmp('CreditCardTypeId').getValue()),
		Ext.Cmp('HolderZipCode').setValue(Ext.Cmp('PayerZipCode').getValue())
		
		
		// Ext.DOM.getPremi()
    ) : (
        Ext.Cmp('InsuredIdentificationTypeId').setValue(''),
        Ext.Cmp('HolderFirstName').setValue(''),
		Ext.Cmp('HolderLastName').setValue(''),
		Ext.Cmp('HolderGenderId').setValue(''),
		Ext.Cmp('HolderPOB').setValue(''),
		Ext.Cmp('HolderDOB').setValue(''),
		Ext.Cmp('HolderPosition').setValue(''),
		Ext.Cmp('HolderOccupation').setValue(''),
		Ext.Cmp('HolderIncome').setValue(''),
		Ext.Cmp('HolderCompany').setValue(''),
		Ext.Cmp('HolderMobilePhoneNum').setValue(''),
		Ext.Cmp('HolderMaritalStatus').setValue(''),
		Ext.Cmp('HolderIdentificationTypeId').setValue(''),
		Ext.Cmp('HolderIdentificationNum').setValue(''),
		Ext.Cmp('HolderRelationshipTypeId').setValue(''),
		Ext.Cmp('HolderAddrType').setValue(''),
		Ext.Cmp('HolderAddressLine1').setValue(''),
		Ext.Cmp('HolderAddressLine2').setValue(''),
		Ext.Cmp('HolderAddressLine3').setValue(''),
		Ext.Cmp('HolderAddressLine4').setValue(''),
		Ext.Cmp('HolderProvinceId').setValue(''),
		Ext.Cmp('HolderCity').setValue(''),
		// Ext.Cmp('HolderEmail').setValue(''),
		Ext.Cmp('HoldersBankId').setValue(''),
		Ext.Cmp('HolderBankBranch').setValue(''),
		Ext.Cmp('HolderCreditCardNum').setValue(''),
		Ext.Cmp('HolderOfficePhoneNum').setValue(''),
		Ext.Cmp('HolderCreditCardTypeId').setValue(''),
		Ext.Cmp('HolderZipCode').setValue(''),
		
        Ext.Cmp('HolderFirstName').disabled(false),
		Ext.Cmp('HolderLastName').disabled(false),
		Ext.Cmp('HolderGenderId').disabled(false),
		Ext.Cmp('HolderPOB').disabled(false),
		Ext.Cmp('HolderDOB').disabled(false),
		// Ext.Cmp('HolderPosition').disabled(false),
		// Ext.Cmp('HolderOccupation').disabled(false),
		// Ext.Cmp('HolderIncome').disabled(false),
		// Ext.Cmp('HolderCompany').disabled(false),
		Ext.Cmp('HolderMobilePhoneNum').disabled(false),
		Ext.Cmp('HolderMaritalStatus').disabled(false),
		Ext.Cmp('HolderIdentificationTypeId').disabled(false),
		Ext.Cmp('HolderIdentificationNum').disabled(false),
		// Ext.Cmp('HolderRelationshipTypeId').disabled(false),
		// Ext.Cmp('HolderAddrType').disabled(false),
		Ext.Cmp('HolderAddressLine1').disabled(false),
		Ext.Cmp('HolderAddressLine2').disabled(false),
		Ext.Cmp('HolderProvinceId').disabled(false),
		Ext.Cmp('HolderCity').disabled(false),
		// Ext.Cmp('HolderEmail').disabled(false),
		Ext.Cmp('HoldersBankId').disabled(false),
		// Ext.Cmp('HolderBankBranch').disabled(false),
		Ext.Cmp('HolderCreditCardNum').disabled(false),
		Ext.Cmp('HolderOfficePhoneNum').disabled(false),
		Ext.Cmp('HolderCreditCardTypeId').disabled(false),
		Ext.Cmp('HolderZipCode').disabled(false)
		
		// Ext.DOM.getPremi()
    ))
}

Ext.DOM.getPremi = function(opts) 
{
	if( Ext.Cmp('ProductId').empty() ){ Ext.Msg("Product ID is Empty").Info(); return false; }
	else if( Ext.Cmp('InsuredGroupPremi').empty() ){ Ext.Msg("Group Premi is Empty").Info(); return false; }
	else if( Ext.Cmp('InsuredAge').empty() ){ Ext.Msg("Age is Empty").Info(); return false; }
	else if( Ext.Cmp('InsuredAge').getValue()==0 ){ Ext.Msg("Age is Zero").Info(); return false; }
	else if( Ext.Cmp('InsuredPayMode').empty() ){ Ext.Msg("Payment Mode").Info(); return false; }
	else if( Ext.Cmp('InsuredPlanType').empty() ){ Ext.Msg("Product Plan").Info(); return false; }
	else
	{
		var JSnum =(	
					Ext.Ajax
					({ 
						url 	: '../class/class.SaveAxa.php', 
						method :'GET', 
						param 	: { 
							action		: '_get_premi', 
							PlanTypeId  : Ext.Cmp('InsuredPlanType').getValue(),
							PersonalAge	: Ext.Cmp('InsuredAge').getValue(),	
							PayModeId	: Ext.Cmp('InsuredPayMode').getValue(),
							ProductId	: Ext.Cmp('ProductId').getValue(),
							GroupPremi 	: Ext.Cmp('InsuredGroupPremi').getValue()
						}
					}).json()
			   );
			   
			Ext.Cmp('InsuredPremi').setValue(JSnum.personal_premi);  
			Ext.Cmp('InsuredPremi').disabled(true); 
	}	   
};


/* 
 * @ Ext /EUI :	frame work ready function 
 * @ Will render on global definition 
 */

Ext.DOM.OnUnloadWindow = function()
{
	if( window.opener )
	{
		window.opener.Transaction();
	}
}
 
Ext.DOM.CloseSelfWindow = function(WinName) {	
	if( window.opener )
	{
		window.opener.Transaction(); 
		window.close(WinName);
	}	
}

/* 
 * @ Ext /EUI :	frame work ready function 
 * @ Will render on global definition 
 */

Ext.DOM.ExtInsured=function()
{
var VAR_POST_DATA = [];
	VAR_POST_DATA['action'] = '_set_update_insured';
	VAR_POST_DATA['InsuredId'] = Ext.Cmp('InsuredId').getValue();
	VAR_POST_DATA['CustomerId'] = Ext.Cmp('CustomerId').getValue();
	VAR_POST_DATA['ProductId']  = Ext.Cmp('ProductId').getValue();
	
	if (!Ext.DOM._get_result_insured()) {
		return false;
	}
	
	Ext.Ajax
	({
		url 	: '../class/class.EditAxa.php', 
		method 	: 'POST',
		param 	: ( Ext.Join(new Array(VAR_POST_DATA, Ext.Serialize('form_edit_insured').getElement())).object() ),
		ERROR 	: function(e)
		{
		    var ERR = JSON.parse(e.target.responseText);
			if(ERR.success){
				Ext.Msg("Update Insured ").Success();
				Ext.DOM.CloseSelfWindow('WinEditInsured');
			}
			else{
				Ext.Msg("Update Insured ").Failed();
			}
		 
		}
	}).post();
}

/*
 * @ get all data date picker srializer 
 * @ is simple get data asumsion 
 */

Ext.DOM._get_result_benef = function(benefid=false) {
	next_process = 0;
	
    if(!benefid){
		// alert(benefid); return false;
		if(Ext.Cmp('AddBenefRelationshipTypeId_1').empty() &&
			Ext.Cmp('AddBenefSalutationId_1').empty() &&
			Ext.Cmp('AddBenefFirstName_1').empty() &&
			// Ext.Cmp('AddBenefLastName_1').empty() &&
			Ext.Cmp('AddBenefGenderId_1').empty() &&
			// Ext.Cmp('AddBenefDOB_1').empty() || 
			(Ext.Cmp("AddBenefDOB_1").getValue()=='00-00-0000' || Ext.Cmp("AddBenefDOB_1").getValue()=='0000-00-00') &&
			Ext.Cmp('AddBenefPercentage_1').empty()){
				alert('at least 1 benef is added');
				next_process = 0;
			}
		
		else if (Ext.Cmp('AddBenefRelationshipTypeId_1').empty()) {
			alert('Invalid Benefiecery Relation');
			next_process = 0;
		}
		else if (Ext.Cmp('AddBenefSalutationId_1').empty()) {
			alert('Invalid Benefiecery Title');
			next_process = 0;
		}
		else if (Ext.Cmp('AddBenefFirstName_1').empty()) {
			alert('Invalid Benefiecery First Name');
			next_process = 0;
		}
		// else if (Ext.Cmp('AddBenefLastName_1').empty()) {
			// alert('Invalid Benefiecery Last Name');
			// next_process = 0;
		// }
		else if (Ext.Cmp('AddBenefGenderId_1').empty()) {
			alert('Invalid Benefiecery Gender');
			next_process = 0;
		}
		// else if (Ext.Cmp('AddBenefDOB_1').empty()) {
			// alert('Invalid Benefiecery DOB');
			// next_process = 0;
		// }
		else if (Ext.Cmp("AddBenefDOB_1").getValue()=='00-00-0000' || Ext.Cmp("AddBenefDOB_1").getValue()=='0000-00-00') {
			alert('Invalid Holder DOB');
			next_process = 0;
		}
		else if (Ext.Cmp('AddBenefPercentage_1').empty()) {
			alert('Invalid Benefiecery Percentage');
			next_process = 0;
		}else {
			next_process = 1;
		}
	}else
	{
		// alert("EditBenefRelationshipTypeId_"+benefid);
		if (Ext.Cmp('EditBenefRelationshipTypeId_'+benefid).empty()) {
			alert('Invalid Benefiecery Relation');
			next_process = 0;
		}
		else if (Ext.Cmp('EditBenefSalutationId_'+benefid).empty()) {
			alert('Invalid Benefiecery Title');
			next_process = 0;
		}
		else if (Ext.Cmp('EditBenefFirstName_'+benefid).empty()) {
			alert('Invalid Benefiecery First Name');
			next_process = 0;
		}
		// else if (Ext.Cmp('EditBenefLastName_'+benefid).empty()) {
			// alert('Invalid Benefiecery Last Name');
			// next_process = 0;
		// }
		else if (Ext.Cmp('EditBenefGenderId_'+benefid).empty()) {
			alert('Invalid Benefiecery Gender');
			next_process = 0;
		}
		// else if (Ext.Cmp('EditBenefDOB_'+benefid).empty()) {
			// alert('Invalid Benefiecery DOB');
			// next_process = 0;
		// }
		else if (Ext.Cmp("EditBenefDOB_"+benefid).getValue()=='00-00-0000' || Ext.Cmp("EditBenefDOB_"+benefid).getValue()=='0000-00-00') {
			alert('Invalid Holder DOB');
			next_process = 0;
		}
		else if (Ext.Cmp('EditBenefPercentage_'+benefid).empty()) {
			alert('Invalid Benefiecery Percentage');
			next_process = 0;
		}else {
			next_process = 1;
		}
	}
    return next_process;
}
 
Ext.DOM.ExtAddBenefiecery = function()
{
	var VAR_POST_DATA = [];
		VAR_POST_DATA['Add'] = Ext.Cmp('AddBenefeciery').getValue();
		VAR_POST_DATA['action'] = '_set_add_benefiecery';
		VAR_POST_DATA['InsuredId'] = Ext.Cmp('InsuredId').getValue();
		VAR_POST_DATA['CustomerId'] = Ext.Cmp('CustomerId').getValue();
	
	// if (!Ext.DOM._get_result_benef(false)) {
		// return false;
	// } 
	
	Ext.Ajax
	({
		url 	: '../class/class.EditAxa.php', 
		method 	: 'POST',
		param 	: ( Ext.Join( new Array( VAR_POST_DATA, (Ext.Cmp('AddBenefeciery').getChecked() ? Ext.Serialize('form_add_benefiecery').getElement():new Array()))).object() ),
		ERROR 	: function(e)
		{
			var ERR = JSON.parse(e.target.responseText);
			if(ERR.success){
				Ext.Msg("Add Benefiecery").Success();
				Ext.DOM.CloseSelfWindow('WinEditInsured');
			}
			else{
				Ext.Msg("Add Benefiecery ").Failed();
			}
		}
	}).post();
}


/* 
 * @ Ext /EUI :	frame work ready function 
 * @ Will render on global definition 
 */

Ext.DOM.ExtUpdateBenefiecery = function(benefid)
{
var VAR_POST_DATA = [];
	VAR_POST_DATA['action'] = '_set_update_benefiecery';
	VAR_POST_DATA['Edit'] = Ext.Cmp('EditBenefeciery').getValue();
	VAR_POST_DATA['InsuredId'] = Ext.Cmp('InsuredId').getValue();
	VAR_POST_DATA['CustomerId'] = Ext.Cmp('CustomerId').getValue();
	
	if (!Ext.DOM._get_result_benef(benefid)) {
		return false;
	}
	
	Ext.Ajax
	({
			url 	: '../class/class.EditAxa.php', 
			method 	: 'POST',
			param 	: ( Ext.Join( new Array( VAR_POST_DATA, (Ext.Cmp('EditBenefeciery').getChecked() ? Ext.Serialize('form_edit_benefiecery').getElement():new Array()))).object() ),
			ERROR 	: function(e)
			{
			  var ERR = JSON.parse(e.target.responseText);
				if(ERR.success){
					Ext.Msg("Update Benefiecery").Success();
					Ext.DOM.CloseSelfWindow('WinEditInsured');
				}
				else{
					Ext.Msg("Update Benefiecery").Failed();
				}
			 
			}
	}).post();
}

Ext.DOM.Ismail = function(){
	var email = Ext.Cmp('PayerEmail').getValue();
	var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if(!regex.test(email)){
		Ext.Msg("Email Not Valid").Info();
		return false;
	}else{
		return true;
	}
}

Ext.DOM._get_result_payers = function() {
    if (Ext.Cmp('PayerFirstName').empty()) {
        alert('PayerFirstName');
        next_process = 0;
    } else if (Ext.Cmp('PayerGenderId').empty()) {
        alert('PayerGenderId');
        next_process = 0;
    } 
    else if (Ext.Cmp('PayerPOB').empty()) {
        alert('PayerPOB');
        next_process = 0;
    }
	// else if (Ext.Cmp('PayerAddrType').empty()) {
        // alert('PayerAddrType');
        // next_process = 0;
    // }
	else if (Ext.Cmp('PayerCertificateStatus').empty()) {
        alert('Invalid Certificate Type');
        next_process = 0;
    }
	else if (Ext.Cmp('PayerCertificateStatus').getValue()=='2' && Ext.Cmp('PayerEmail').empty()) {
        alert('Invalid Payer Email');
        next_process = 0;
    } 
    else if (Ext.Cmp('PayerAddressLine1').empty()) {
        alert('PayerAddressLine1');
        next_process = 0;
    } else if (Ext.Cmp('PayerMobilePhoneNum').empty() && Ext.Cmp('PayerHomePhoneNum').empty() && Ext.Cmp('PayerOfficePhoneNum').empty()) {
        alert('PayerMobilePhoneNum');
        next_process = 0;
    } else if (Ext.Cmp('PayerCity').getValue()=='--Choose--') {
        alert('City is Empty');
        next_process = 0;
	}
    else if (Ext.Cmp('PayerZipCode').empty()) {
        alert('PayerZipCode');
        next_process = 0;
    } 
    else if (Ext.Cmp('PayerProvinceId').empty()) {
        alert('PayerProvinceId');
        next_process = 0;
    } 
	else if (Ext.Cmp('PayerIdentificationTypeId').getValue()!='' && Ext.Cmp('PayerIdentificationNum').empty()) {
        alert('Invalid Identification No');
        next_process = 0;
    }
	else if (Ext.Cmp('PayerIdentificationTypeId').getValue()=='' && !Ext.Cmp('PayerIdentificationNum').empty()) {
        alert('Invalid Identification Type');
        next_process = 0;
    }
	else if (  Ext.Cmp('PayerZipCode').getValue().length < 5 ) {
        alert('Invalid ZipCode');
        next_process = 0;
    }
	else if (Ext.Cmp("PayerDOB").getValue()=='00-00-0000' || Ext.Cmp("PayerDOB").getValue()=='0000-00-00') {
        alert('Invalid Payer DOB');
        next_process = 0;
    }
	else if (Ext.Cmp("PayerDOB").empty()) {
        alert('PayerDOB');
        next_process = 0;
    }
	else {
        next_process = 1;
    }
    return next_process;
}


Ext.DOM._get_result_insured = function() {
    next_process = 0;
    for (var i in Ext.DOM.Insured.field) {
        if (Ext.Cmp(i).empty() && Ext.DOM.Insured.field[i].keys) {
            Ext.Msg(Ext.DOM.Insured.field[i].warn).Info();
            next_process = 0
            return false;
        } else {
            next_process = 1;
        }
    }
	
	if (Ext.Cmp('InsuredPlanType').empty()) {
        alert('Invalid Insured Plan');
        next_process = 0;
    }
	else if (Ext.Cmp('InsuredPayMode').empty()) {
        alert('Invalid Insured PayMode');
        next_process = 0;
    }
	else if (Ext.Cmp('InsuredGroupPremi').empty()) {
        alert('Invalid Insured GroupPremi');
        next_process = 0;
    }
	else if (Ext.Cmp('InsuredGenderId').empty()) {
        alert('Invalid Insured Gender');
        next_process = 0;
    }
	else if (Ext.Cmp('InsuredPOB').empty()) {
        alert('Invalid Insured POB');
        next_process = 0;
    }
	else if (Ext.Cmp('InsuredSalutationId').empty()) {
        alert('Invalid Insured Salutation Id');
        next_process = 0;
    }
	else if (Ext.Cmp('InsuredFirstName').empty()) {
        alert('Invalid Insured FirstName');
        next_process = 0;
    }
	else if (Ext.Cmp("InsuredDOB").getValue()=='00-00-0000' || Ext.Cmp("InsuredDOB").getValue()=='0000-00-00') {
        alert('Invalid Insured DOB');
        next_process = 0;
    }
	else if (Ext.Cmp('InsuredIdentificationTypeId').getValue()!='' && Ext.Cmp('InsuredIdentificationNum').empty()) {
        alert('Invalid Identification No');
        next_process = 0;
    }
	else if (Ext.Cmp('InsuredIdentificationTypeId').getValue()=='' && !Ext.Cmp('InsuredIdentificationNum').empty()) {
        alert('Invalid Identification Type');
        next_process = 0;
    }

    return next_process;
}

Ext.DOM._get_result_holder = function() {
	
	if (Ext.Cmp('HolderFirstName').empty()) {
        alert('HolderFirstName');
        next_process = 0;
    }
	else if (Ext.Cmp('HolderGenderId').empty()) {
        alert('HolderGenderId');
        next_process = 0;
    }
	else if (Ext.Cmp('HolderPOB').empty()) {
        alert('HolderPOB');
        next_process = 0;
    }
	else if (Ext.Cmp('HolderDOB').empty()) {
        alert('HolderDOB');
        next_process = 0;
    }
	else if (Ext.Cmp("HolderDOB").getValue()=='00-00-0000' || Ext.Cmp("HolderDOB").getValue()=='0000-00-00') {
        alert('Invalid Holder DOB');
        next_process = 0;
    }
	// else if (Ext.Cmp('HolderPosition').empty()) {
        // alert('HolderPosition');
        // next_process = 0;
    // }
	// else if (Ext.Cmp('PayerMobilePhoneNum').empty() && Ext.Cmp('PayerHomePhoneNum').empty() && Ext.Cmp('PayerOfficePhoneNum').empty()) {
        // alert('PayerMobilePhoneNum');
        // next_process = 0;
    // }
	// else if (Ext.Cmp('HolderOccupation').empty()) {
        // alert('HolderOccupation');
        // next_process = 0;
    // }
	// else if (Ext.Cmp('HolderIncome').empty()) {
        // alert('HolderIncome');
        // next_process = 0;
    // }
	// else if (Ext.Cmp('HolderCompany').empty()) {
        // alert('HolderCompany');
        // next_process = 0;
    // }
	else if (Ext.Cmp('HolderMobilePhoneNum').empty()) {
        alert('HolderMobilePhoneNum');
        next_process = 0;
    }
	// else if (Ext.Cmp('HolderMaritalStatus').empty()) {
        // alert('HolderMaritalStatus');
        // next_process = 0;
    // }
	// else if (Ext.Cmp('HolderIdentificationTypeId').empty()) {
        // alert('HolderIdentificationTypeId');
        // next_process = 0;
    // }
	// else if (Ext.Cmp('HolderIdentificationNum').empty()) {
        // alert('HolderIdentificationNum');
        // next_process = 0;
    // }
	// else if (Ext.Cmp('HolderRelationshipTypeId').empty()) {
        // alert('HolderRelationshipTypeId');
        // next_process = 0;
    // }
	// else if (Ext.Cmp('HolderAddrType').empty()) {
        // alert('HolderAddrType');
        // next_process = 0;
    // }
	else if (Ext.Cmp('HolderAddressLine1').empty()) {
        alert('HolderAddressLine1');
        next_process = 0;
    }
	else if (Ext.Cmp('HolderProvinceId').empty()) {
        alert('HolderProvinceId');
        next_process = 0;
    }
	else if (Ext.Cmp('HolderCity').empty()) {
        alert('HolderCity');
        next_process = 0;
    }
	// else if (Ext.Cmp('HoldersBankId').empty()) {
        // alert('HoldersBankId');
        // next_process = 0;
    // }
	// else if (Ext.Cmp('HolderBankBranch').empty()) {
        // alert('HolderBankBranch');
        // next_process = 0;
    // }
	// else if (Ext.Cmp('HolderCreditCardNum').empty()) {
        // alert('HolderCreditCardNum');
        // next_process = 0;
    // }
	else if (Ext.Cmp('HolderZipCode').empty()) {
        alert('HolderZipCode');
        next_process = 0;
    }
	else if (  Ext.Cmp('HolderZipCode').getValue().length < 5 ) {
        alert('Invalid ZipCode');
        next_process = 0;
    }
	else {
        next_process = 1;
    }
    return next_process;
}

/* 
 * @ Ext /EUI :	frame work ready function 
 * @ Will render on global definition 
 */
Ext.DOM.UpdatePayer = function(){

var VAR_POST_DATA = [];
	VAR_POST_DATA['action'] = '_set_update_payers';
	VAR_POST_DATA['CustomerId'] = Ext.Cmp('CustomerId').getValue();
	VAR_POST_DATA['ProductId'] = Ext.Cmp('ProductId').getValue();
	
	if (!Ext.DOM._get_result_payers()) {
		return false;
	} 
	
	Ext.Ajax
	({
			url 	: '../class/class.EditAxa.php', 
			method 	: 'POST',
			param 	: Ext.Join([VAR_POST_DATA, Ext.Serialize('form_edit_payers').getElement()]).object(),
			ERROR 	: function(e)
			{
			  var ERR = JSON.parse(e.target.responseText);
				if(ERR.success){
					Ext.Msg("Update Payer").Success();
					Ext.DOM.CloseSelfWindow('WinEditInsured');
				}
				else{
					Ext.Msg("Update Payer").Failed();
				}
			 
			}
	}).post();
	
}

Ext.DOM.UpdateHolder = function(){

var VAR_POST_DATA = [];
	VAR_POST_DATA['action'] = '_set_update_holder';
	VAR_POST_DATA['CustomerId'] = Ext.Cmp('CustomerId').getValue();
	VAR_POST_DATA['ProductId'] = Ext.Cmp('ProductId').getValue();
	
	if (!Ext.DOM._get_result_holder()) {
		return false;
	} 
	
	Ext.Ajax
	({
			url 	: '../class/class.EditAxa.php', 
			method 	: 'POST',
			param 	: Ext.Join([VAR_POST_DATA, Ext.Serialize('form_data_holder').getElement()]).object(),
			ERROR 	: function(e)
			{
			  var ERR = JSON.parse(e.target.responseText);
				if(ERR.success){
					Ext.Msg("Update Holder").Success();
					Ext.DOM.CloseSelfWindow('WinEditInsured');
				}
				else{
					Ext.Msg("Update Holder").Failed();
				}
			 
			}
	}).post();
	
}
//UpdateSurvey

/* 
 * @ Ext /EUI :	frame work ready function 
 * @ Will render on global definition 
 */
Ext.DOM.UpdateSurvey = function()
{

if ( Ext.DOM.ValidSurvey() ) {
	alert("Please answer survey (red box area)");
	return false;
} 
else
{
	var VAR_POST_DATA = [];
		VAR_POST_DATA['action'] = '_set_update_survey';
		VAR_POST_DATA['CustomerId'] = Ext.Cmp('CustomerId').getValue();
		VAR_POST_DATA['ProductId'] = Ext.Cmp('ProductId').getValue();
		VAR_POST_DATA['InsuredId'] = Ext.Cmp('InsuredId').getValue();
		VAR_POST_DATA['questioner_type'] = sv;
		
		Ext.Ajax
		({
				url 	: '../class/class.EditAxa.php', 
				method 	: 'POST',
				param 	: Ext.Join([VAR_POST_DATA, Ext.Serialize('form_edit_survey').getElement()]).object(),
				ERROR 	: function(e)
				{
				  var ERR = JSON.parse(e.target.responseText);
					if(ERR.success){
						Ext.Msg("Update Survey").Success();
						Ext.DOM.CloseSelfWindow('WinEditSurvey');
					}
					else{
						Ext.Msg("Update Survey").Failed();
					}
				 
				}
		}).post();
}
}

/*** validasi survey **/
Ext.DOM.ValidSurvey = function()
{
	var red_mark=0;
	// console.log(Mandat_SurveyQuest);
	// console.log(Mandat_SurveyAns);
	for(var i in Mandat_SurveyQuest )
	{
			if(typeof Mandat_SurveyAns[i] === 'undefined') 
			{
				// Ext.Cmp(Ext.DOM._benefieceryAdd.field[2]+"_"+p).empty()
				// alert('survey_'+Mandat_SurveyQuest[i]);
				// alert(Ext.Cmp('survey_'+Mandat_SurveyQuest[i]).getValue());
				// alert(Ext.Cmp('survey_'+Mandat_SurveyQuest[i]).getValue());
				if(  Ext.Cmp('survey_'+Mandat_SurveyQuest[i]).getValue()=="" )
				{
					// alert("Please answer question");
					$("#ans_valid_"+Mandat_SurveyQuest[i]).css({"border":"2px solid #FF0000"});
					red_mark++;
				}
				else
				{
					$("#ans_valid_"+Mandat_SurveyQuest[i]).css({"border":"0px solid #000000"});
				}
			}
			else 
			{
				var checkans="";
				for(var j in Mandat_SurveyAns[i] )
				{
					checkans = checkans + Ext.Cmp('survey_'+Mandat_SurveyQuest[i]+'_'+Mandat_SurveyAns[i][j]).getValue();	
				}
				if ( checkans =="" )
				{
					// alert("Please answer question");
					$("#ans_valid_"+Mandat_SurveyQuest[i]).css({"border":"2px solid #FF0000"});
					red_mark++;
				}
				else
				{
					$("#ans_valid_"+Mandat_SurveyQuest[i]).css({"border":"0px solid #000000"});
				}
			}
			// alert(typeof Mandat_SurveyAns[i]);
			
	}
	// alert(red_mark);
	if(red_mark>0)
	{
		
		return true;
	}
	else
	{
		return false;
	}
}

/*** validasi rule ***/
 Ext.DOM.ValidRule = function ()
 {
	var callback = false;
	var question_valid = new Array();
	var valid_answer =(Ext.Ajax({ 
				url 	: '../class/class.SaveAxa.php', 
				method :'GET', 
				param 	: { 
					action	: '_getValidAnswer',
					ProductId : Ext.Cmp('ProductId').getValue()
					}
				}).json() );
	var except_question =(Ext.Ajax({ 
				url 	: '../class/class.SaveAxa.php', 
				method :'GET', 
				param 	: { 
					action	: '_getExceptQuestion',
					ProductId : Ext.Cmp('ProductId').getValue()
					}
				}).json() );
	// console.log(except_question);
	for ( var i in valid_answer.question )
	{
		
		// console.log("Question ke " );
		// console.log(i);
		// var questionid = valid_answer.question[i];
		
		// console.log(except_question[i]);
		// console.log("jawaban valid untuk pertanyaan "+i);
		
		// console.log(valid_answer.answer[i]);
		
		var a = valid_answer.answer[i];
		// console.log( (a));
		var all_ans = Ext.Cmp('survey_'+i).getValue();
		
		// console.log("Jawaban dari pertanyaan "+ i);
		// console.log( (all_ans) );
		
		// console.log("type variabel Jawaban dari pertanyaan "+ i);
		// console.log( typeof(all_ans) );
		
		// console.log("Panjang objek jawaban dari pertanyaan " + i);
		// console.log( typeof(all_ans) );
		if(all_ans.length==1)
		{
			var ans = String(all_ans);
		// console.log( a.indexOf( ans ) );
			if( a.indexOf(ans) == -1 )
			{
				// question_valid[i]=false;
				 if(typeof except_question[i] !='undefined')
				 {
					// console.log('survey_'+except_question[i]);
					// console.log(Ext.Cmp('survey_'+except_question[i]).getValue());
					var ex_select_user = Ext.Cmp('survey_'+except_question[i]).getValue();
					// console.log(ex_select_user.length);
					if(ex_select_user.length > 0 )
					{
						var b = valid_answer.answer[except_question[i]];
						for ( var s in ex_select_user )
						{
							// console.log( String(ex_select_user[s]) );
							// console.log( typeof ( String(ex_select_user[s]) ) );
							// console.log( b.indexOf(String(ex_select_user[s])) );
							if(b.indexOf(String(ex_select_user[s])) == -1)
							{
								
								callback=true;
								break;
									// console.log(callback);
							}
							else
							{
								callback=false;
								// console.log(callback);
							}
							
						}
					}
					else
					{
						callback=true;
						break;
					}
					
				 }
				 else
				 {
					callback=true;
					break;
				 }
			}
			else
			{
				// console.log(true);
				// question_valid[i]=true;
				// console.log(i);
				// question_valid.push={i:[true]};
				callback=false;
				// break;
			}
		// alert(valid_answer.question[i]);
		// for ( var j in valid_answer.answer[i] )
		// {
			// console.log(valid_answer.answer[i][j]);
		// }
		// alert('survey_'+
		}
		else
		{
			for ( var multi in all_ans )
			{
				
				if(a.indexOf(String(all_ans[multi])) == -1)
				{
					
					callback=true;
					break;
						// console.log(callback);
				}
				else
				{
					callback=false;
					// console.log(callback);
				}
				
			}
		}
	}
// console.log(question_valid);
	// for ( var q in question_valid )
	// {
		// if(question_valid[q] == false)
		// {
			// callback=true;
			// break;
		// }
		// else 
		// {
			// callback=false;
		// }
	// }
	// alert(Ext.Cmp('survey_44').getValue());
	return callback;
 }
 
 /** validasi uw kosong **/
Ext.DOM.UWEmpty = function()
{
	var empty = false;
	var question_uw =(Ext.Ajax({ 
				url 	: '../class/class.SaveAxa.php', 
				method :'GET', 
				param 	: { 
					action	: '_getUWQuestion',
					ProductId : Ext.Cmp('ProductId').getValue()
					}
				}).json() );
				// console.log(question_uw);
	for ( var i in question_uw )
	{
		// console.log(Ext.Cmp('survey_'+i).empty());
		// console.log('survey_'+i);
		if(Ext.Cmp('survey_'+i).empty())
		{
			empty = true;
		}
		else{
			empty = false;
			break;
		}
			
	}
	return empty;
	
}
 
//UpdateUnderwriting

/* 
 * @ Ext /EUI :	frame work ready function 
 * @ Will render on global definition 
 */
Ext.DOM.SendFPA = function(){
	
var x 			= document.getElementById('loading_pa');


x.style.visibility = 'visible';

if( Ext.Cmp("QCStatus").getValue() != 1 ) {
	x.style.visibility = 'hidden';
	alert("Please Save Your Status To Approve Before Continue");
	return false;
}

if( Ext.Cmp("StatusInsured").getValue() != 1 ) {
  x.style.visibility = 'hidden';
  alert("Status Insured Not Approve!!")
  return false;
}
	
if( Ext.Cmp("pa_no_induk").getValue() != '' ) {
	x.style.visibility = 'hidden';
	Ext.Msg("PA already registered").Info();
	return false;
}

var VAR_POST_DATA = [];
	VAR_POST_DATA['action'] = '_send_fpa';
	VAR_POST_DATA['CustomerId'] = Ext.Cmp('CustomerId').getValue();
	VAR_POST_DATA['ProductId'] = Ext.Cmp('ProductId').getValue();
	
	Ext.Ajax
	({
			url 	: '../class/class.EditAxa.php', 
			method 	: 'POST',
			param 	: Ext.Join([VAR_POST_DATA, Ext.Serialize('form_edit_fpa').getElement()]).object(),
			ERROR 	: function(e)
			{
			  var ERR = JSON.parse(e.target.responseText);
				if(ERR.success){
					var msg_str = '';
					// msg_str += "\nstatus => " + ERR.status;
					switch( parseInt(ERR.register_status) ) {
						case 2:
							msg_str += "\nBerhasil menerbitkan polis";
							// msg_str += "\nno_induk => " + ERR.no_induk;
							// msg_str += "\nno_polis => " + ERR.no_polis;
							// msg_str += "\nexpired => " + ERR.expired;
							
							Ext.Cmp("pa_core_status").setValue("Sudah terdaftar");
							Ext.Cmp("pa_no_induk").setValue(ERR.no_induk);
							Ext.Cmp("pa_no_polis").setValue(ERR.no_polis);
							Ext.Cmp("pa_expired").setValue(ERR.expired);
							break;
						case 3:
							msg_str += "\nGagal menerbitkan polis. Umur tidak masuk criteria";
							break;
						case 4:
							msg_str += "\nGagal menerbitkan polis. Email sudah pernah dipakai sebelumnya";
							break;
					}
	
					x.style.visibility = 'hidden';
					Ext.Msg(msg_str).Info();
					// Ext.DOM.CloseSelfWindow('WinEditInsured');
				}
				else{
					x.style.visibility = 'hidden';
					Ext.Msg("Send PA, "+ERR.error).Failed();
				}
			}
	}).post();
	
} 
 
 
Ext.DOM.UpdateUW = function(){

if ( Ext.DOM.ValidSurvey() ) {
	alert("Please answer underwriting (red box area)");
	return false;
} 
else if (  Ext.DOM.ValidRule() ) {
	alert("Rule is not Valid, please reject/cancel this policy !");
	return false;
}
else if ( Ext.DOM.UWEmpty() ) {
	alert("Underwriting is empty");
	return false;
}
else{
	var VAR_POST_DATA = [];
		VAR_POST_DATA['action'] = '_set_update_survey';
		VAR_POST_DATA['CustomerId'] = Ext.Cmp('CustomerId').getValue();
		VAR_POST_DATA['ProductId'] = Ext.Cmp('ProductId').getValue();
		VAR_POST_DATA['InsuredId'] = Ext.Cmp('InsuredId').getValue();
		VAR_POST_DATA['questioner_type'] = uw;
		// alert(VAR_POST_DATA['InsuredId']);
		Ext.Ajax
		({
				url 	: '../class/class.EditAxa.php', 
				method 	: 'POST',
				param 	: Ext.Join([VAR_POST_DATA, Ext.Serialize('form_edit_uw').getElement()]).object(),
				ERROR 	: function(e)
				{
				  var ERR = JSON.parse(e.target.responseText);
					if(ERR.success){
						Ext.Msg("Update Underwriting").Success();
						Ext.DOM.CloseSelfWindow('WinEditSurvey');
					}
					else{
						Ext.Msg("Update Underwriting").Failed();
					}
				 
				}
		}).post();
}


}
	
/* 
 * @ Ext /EUI :	frame work ready function 
 * @ Will render on global definition 
 */

Ext.DOM.is_valid_payer = function(){
	
}

Ext.DOM.tst = function(){
	Ext.Cmp('PayerAge').setValue('55');
	return false;
}

Ext.DOM.getAgePayer = function()
{
	var PayerDOB = Ext.Cmp('PayerDOB').getValue();
		 _c = PayerDOB.split('-'),
		 _d = _c[2] + '-' + _c[1] + '-' + _c[0];
	if( Ext.Cmp('ProductId').getValue() != '' )
	{
		var JSnum = (
			Ext.Ajax({
				url: '../class/class.SaveAxa.php',
				method: 'GET',
				param: {
					action: '_get_age_payer',
					ProductId: Ext.Cmp("ProductId").getValue(),
					GroupPremi: "2",
					DOB: _d.trim()
				}
			}).json()
		);
		return JSnum;
	}
	else
	{
		alert('Please, choose product!');
		return false;
	}
		
}

// var saveUpdate = function(){
// 		var CustomerId = Ext.Cmp('CustomerId').getValue();
// 		var cc_card    = Ext.cmp('CreditCardNo').getValue();
		
// 		if( cc_card == ''){
// 			alert('Please select status !');
// 			return false;
// 		}
// 		else {
// 			doJava.File = '../class/class.SaveAxa.php';
// 			doJava.Params = {
// 				action 	: '_update_cc',
// 				CustomerId : CustomerId,
// 				CreditCardNo : cc_card
// 			}
// 			var error = doJava.eJson();
// 			if( error.result ==1)
// 			{
// 				alert("Success, Save Verified! ");
// 				return true;
// 			}
// 			else{ 
// 				alert("Failed, Save Verified! "); 
// 				return false;
// 			} 
// 		}
// 	}
	/**
	* function for update card number 
	* param : credit card, customer_id
	* method : post
	* author : didi ganteng
	*/
	Ext.DOM.UpdateCC = function()
	{
		var CustomerId = Ext.Cmp('CustomerId').getValue();
		var cc_card    = Ext.Cmp('CreditCardNo').getValue();
		var number 	   = /^\s*\d*\s*$/;

		if(cc_card == '') {
			alert('Please insert cc card no');
			return false;
		} else if(cc_card.length > 16) {
			alert('Please insert creadit card number 16 digits');
			return false;
		} else if(!cc_card.match(number)) {
			alert('Credit card is number only');
			return false;
		} else if(cc_card.match(/\s/g)) {
			alert('Please Check Credit card number');
			return false;
		} else {
			$.ajax({ 
				url    : '../class/class.SaveAxa.php', 
				type   : "POST",
				data   : { 
					action		: '_update_cc',
					CustomerId : CustomerId,
					CreditCardNo : cc_card
				},  
				success: function(data) {
				    var data = JSON.parse(data); 
					if (typeof(data) == 'object' && data.result == 1) {
					   	alert('Update creadit card number success !!!');
					   	return true;
					}
				}, error : function(error) {
					if(error.result == 0) {
						alert('Failed update creadit card number');
						return  false;
					}
				}
			});
		}
	}


/* 
 * @ Ext /EUI :	frame work ready function 
 * @ Will render on global definition 
 */
 
$(document).ready(function(){

	// $("#btn_sh").click(function(e) {
	//     $("#myform").show();
	//     e.preventDefault();
	// });

	Mandat_SurveyQuest =(Ext.Ajax({ 
					url 	: '../class/class.SaveAxa.php', 
					method :'GET', 
					param 	: { 
					action	: '_get_mandat_quest',
					ProductId: Ext.Cmp('ProductId').getValue(),
							}
					}).json() );
					
	Mandat_SurveyAns =(Ext.Ajax({ 
					url 	: '../class/class.SaveAxa.php', 
					method :'GET', 
					param 	: { 
					action	: '_get_mandat_ans',
					ProductId: Ext.Cmp('ProductId').getValue(),
							}
					}).json() );
	PayMethod =( Ext.Ajax({ 
					url 	: '../class/class.SaveAxa.php', 
					method :'GET', 
					param 	: { action	: '_json_pay_method'}
				}).json() );
	card_type = Ext.Cmp("CreditCardTypeId").getValue();
	$("#tabs" ).tabs();
	$("#tabs ul" ).tabs().append("<li onclick=\"javascript:CloseSelfWindow('WinEditInsured');\" style=\"margin-top:2px;cursor:pointer;border:0px solid #ffffff;padding:1px;float:right;margin-right:10px;\" title=\"Exit\">"+
	  "<img src=\"../gambar/icon/cancel.png\"></li>");
	
	var HeightWindow = parseInt($(window).height()-150);
	if( HeightWindow )
	{
		$("#tabs-2").css({height : HeightWindow+"px","overflow":'auto'});
		$("#tabs-5").css({height : HeightWindow+"px","overflow":'auto'});
		$("#tabs-6").css({height : HeightWindow+"px","overflow":'auto'});
		$("#tabs-7").css({height : HeightWindow+"px","overflow":'auto'});
		$("#tabs-9").css({height : HeightWindow+"px","overflow":'auto'});
		$("#tabs-10").css({height : HeightWindow+"px","overflow":'auto'});
	}
/*
 * @ get all data date picker srializer 
 * @ is simple get data asumsion 
 */
 
 $(".date").datepicker
 ({
    buttonImage : '../gambar/calendar.gif',  buttonImageOnly : true, changeMonth : true, changeYear		: true, yearRange : '1945:2030', dateFormat : 'dd-mm-yy',
    onSelect : function(e)
   {
	var _a = $(this).attr("id"),  _b = _a.substring(0,2),  _c = e.split('-'), _d = _c[2]+'-'+_c[1]+'-'+_c[0];
	if( _c.length > 2 )
	{			
		if(_b != 'Ad')
		{
			if(_b != 'Ed')
			{
				var JSnum = ( Ext.Ajax({url:'../class/class.SaveAxa.php', method :'GET', param :{action :'_get_age', ProductId:Ext.Cmp("ProductId").getValue(), GroupPremi :Ext.Cmp("InsuredGroupPremi").getValue(),DOB: _d.trim()}}).json());
				if( JSnum.success )
				{
					Ext.Cmp('InsuredAge').setValue( JSnum.personal_age );
					Ext.Cmp("InsuredPremi").setValue(	
								Ext.Ajax({ 
									url 	: '../class/class.SaveAxa.php', 
									method :'GET', 
									param 	: { 
										action 		: '_get_premi', 
										ProductId	: Ext.Cmp("ProductId").getValue(),
										PersonalAge	: Ext.Cmp("InsuredAge").getValue(),
										PayModeId	: Ext.Cmp("InsuredPayMode").getValue(),
										PlanTypeId  : Ext.Cmp("InsuredPlanType").getValue(),
										GroupPremi  : Ext.Cmp("InsuredGroupPremi").getValue()
									}
								}).json().personal_premi
							);	
				}
				else
				{
					Ext.Msg(JSnum.Error).Error();
					Ext.Cmp('InsuredAge').setValue('');
					Ext.Cmp("InsuredPremi").setValue('')
				}
						
				Ext.Cmp('InsuredAge').disabled(true);
				Ext.Cmp("InsuredPremi").disabled(false);
			}
		}
	}
   }

  });
  
  Ext.Cmp('PayerEmail').listener({
    	'onChange':function(e){
    		//var email = $(this).value();
    		if(!Ext.DOM.Ismail()){
    			Ext.Cmp('PayerEmail').setFocus(true,10);
    			Ext.Cmp('PayerEmail').setValue('');
    		};
    	}
    });  
	
  Ext.Cmp('InsuredGroupPremi').listener({
    	'onChange':function(e){
    		if (parseInt(Ext.Cmp('InsuredGroupPremi').getValue()) == 2) {
				Ext.Cmp("CopyDataInsured").disabled(false);
				// Ext.Cmp('InsuredRelationshipTypeId').setValue(79);
				// Ext.Cmp('InsuredRelationshipTypeId').disabled(true);
			} else {
				Ext.Cmp("CopyDataInsured").setUnchecked();
				Ext.Cmp("CopyDataInsured").disabled(true);
				Ext.Cmp('InsuredRelationshipTypeId').setValue("");
				Ext.Cmp('InsuredRelationshipTypeId').disabled(false);
			}
    	}
    });
	
	Ext.Cmp('PayerDOB').listener({
        'onKeyup': function(e) {
            var DOB = Ext.Cmp('PayerDOB').getValue();
			var patt = new RegExp(/^\d{2}-\d{2}-\d{4}/);
			var res = patt.test(DOB);
			if(res)
			{
				var JSnum = Ext.DOM.getAgePayer();
				
				if(JSnum.success==1)
				{
					
					Ext.Cmp('PayerAge').setValue(JSnum.personal_age);
				}
				else{
					Ext.Cmp('PayerAge').setValue(JSnum.personal_age);
				}
				
			}
			else
			{
				Ext.Cmp('PayerAge').setValue('0');
			}
			
        }
    });
	
	Ext.DOM.CopyDataInsured = function(opt) {
	(opt.checked ? (
        Ext.Cmp('InsuredIdentificationTypeId').setValue(Ext.Cmp('PayerIdentificationTypeId').getValue()),
        Ext.Cmp('InsuredIdentificationTypeId').setValue(Ext.Cmp('PayerIdentificationTypeId').getValue()),
        Ext.Cmp('InsuredIdentificationNum').setValue(Ext.Cmp('PayerIdentificationNum').getValue()),
        Ext.Cmp('InsuredSalutationId').setValue(Ext.Cmp('PayerSalutationId').getValue()),
        Ext.Cmp('InsuredFirstName').setValue(Ext.Cmp('PayerFirstName').getValue()),
        Ext.Cmp('InsuredLastName').setValue(Ext.Cmp('PayerLastName').getValue()),
        Ext.Cmp('InsuredGenderId').setValue(Ext.Cmp('PayerGenderId').getValue()),
        Ext.Cmp('InsuredPOB').setValue(Ext.Cmp('PayerPOB').getValue()),
        Ext.Cmp('InsuredDOB').setValue(Ext.Cmp('PayerDOB').getValue()),
		Ext.Cmp('InsuredAge').setValue((Ext.Cmp('PayerAge').getValue()?Ext.Cmp('PayerAge').getValue():'')),
		
		Ext.Cmp('InsuredIdentificationTypeId').disabled(true),
        Ext.Cmp('InsuredIdentificationTypeId').disabled(true),
        Ext.Cmp('InsuredIdentificationNum').disabled(true),
        Ext.Cmp('InsuredSalutationId').disabled(true),
        Ext.Cmp('InsuredFirstName').disabled(true),
        Ext.Cmp('InsuredLastName').disabled(true),
        Ext.Cmp('InsuredGenderId').disabled(true),
        Ext.Cmp('InsuredPOB').disabled(true),
        Ext.Cmp('InsuredDOB').disabled(true),
		Ext.Cmp('InsuredAge').disabled(true),
		
		Ext.DOM.getPremi()
    ) : (
        Ext.Cmp('InsuredIdentificationTypeId').setValue(''),
        Ext.Cmp('InsuredIdentificationTypeId').setValue(''),
        Ext.Cmp('InsuredIdentificationNum').setValue(''),
        Ext.Cmp('InsuredSalutationId').setValue(''),
        Ext.Cmp('InsuredFirstName').setValue(''),
        Ext.Cmp('InsuredLastName').setValue(''),
        Ext.Cmp('InsuredGenderId').setValue(''),
        Ext.Cmp('InsuredPOB').setValue(''),
        Ext.Cmp('InsuredDOB').setValue(''),
        Ext.Cmp('InsuredAge').setValue(''),
		
		Ext.Cmp('InsuredIdentificationTypeId').disabled(false),
        Ext.Cmp('InsuredIdentificationTypeId').disabled(false),
        Ext.Cmp('InsuredIdentificationNum').disabled(false),
        Ext.Cmp('InsuredSalutationId').disabled(false),
        Ext.Cmp('InsuredFirstName').disabled(false),
        Ext.Cmp('InsuredLastName').disabled(false),
        Ext.Cmp('InsuredGenderId').disabled(false),
        Ext.Cmp('InsuredPOB').disabled(false),
        Ext.Cmp('InsuredDOB').disabled(false),
		Ext.Cmp('InsuredAge').disabled(false),
		
		Ext.DOM.getPremi()
    ))
}
  
  Ext.Cmp('PayerPaymentType').listener({
        'onChange': function(e) {
			var PayType = Ext.Cmp('PayerPaymentType').getValue();
			for (var i in PayMethod.form) {
				if(PayType==i)
				{
					$( "#"+PayMethod.form[i] ).show();
				}
				else
				{
					$( "#"+PayMethod.form[i] ).hide();
				}
			}
			Ext.Ajax({
				url: '../class/class.SaveAxa.php',
				method: 'GET',
				param: {
					action: '_card_type_pay',
					Pay_Type :PayType
				}
			}).load("dyn_card_type");
			Ext.Cmp("CreditCardTypeId").setValue(card_type);
        }
    });
});


Ext.DOM.splitintoivr = function(){
	var CustomerId = Ext.Cmp("PayerCreditCardNum").getValue();
        var port = Ext.Cmp('CreditCardTypeId').getValue();
        var bank = Ext.Cmp("PayersBankId").getValue();
        if (port != ""){
            window.opener.document.ctiapplet.callSplitToIVR(String(port), '3800', String(bank), String(CustomerId));
			// console.log(String(port)+", "+"3800"+", "+String(CustomerId))
        }
		//
	//window.opener.document.ctiapplet.callSplitToIVR('3900', '3800', String(CustomerId));
	// var vervar = window.opener.document.ctiapplet.getVersion();
	// alert(CustomerId);
	// alert(port);
};

 	Ext.DOM.loadIvrBank = function ()
	{
		var IvrPayMethode = Ext.Cmp('IvrPayMethod').getValue();
		// if( IvrPayMethode != '')
		// {
			Ext.Ajax({
				url: '../class/class.SaveAxa.php',
				method: 'GET',
				param: {
					action: '_load_ivr_bank',
					IvrPayMethode :PayMethod.ivr[IvrPayMethode]
				}
			}).load("ivr_bank");
		// }
		
	};

Ext.DOM.check_digit_valid = function()
{
	var ivrpay = Ext.Cmp('IvrPayMethod').getValue();
	Ext.Cmp("ivr_list").setText("");
	if(ivrpay != "")
	{
		// var jsNum = (
			// Ext.Ajax({
				// url: '../class/class.SaveAxa.php',
				// method: 'GET',
				// param: {
					// action: '_check_digit',
					// CustomerId: Ext.Cmp("CustomerId").getValue(),
					// PayMethodId: PayMethod.ivr[ivrpay]
				// }
			// }).json()
		// );
		// Ext.Cmp('digit_message_html').setText(jsNum.img);
		// Ext.Ajax({
			// url: '../class/class.SaveAxa.php',
			// method: 'GET',
			// param: {
				// action: '_check_digit',
				// CustomerId: Ext.Cmp("CustomerId").getValue(),
				// PayMethodId: PayMethod.ivr[ivrpay]
			// }
		// }).load("ivr_list");
		if (ivrpay == "3900"){
			// var NumCard = (
							// Ext.Ajax({
								// url: '../class/class.SaveAxa.php',
								// method: 'GET',
								// param: {
									// action: '_get_card_numbercc',
									// CustomerId: Ext.Cmp("CustomerId").getValue(),
									// PayMethodId: PayMethod.ivr[ivrpay]
								// }
							// }).json()
						// );
			Ext.Ajax({
				url: '../class/class.SaveAxa.php',
				method: 'GET',
				param: {
					action: 'show_card_numbercc',
					CustomerId: Ext.Cmp("CustomerId").getValue(),
					PayMethodId: PayMethod.ivr[ivrpay]
				}
			}).load("ivr_list");
			//_insert_log();

			$("#myform").show();
			$("#btn_show").show();
		}else if (ivrpay == "3901"){
			
			// var SavingCard = (
							// Ext.Ajax({
								// url: '../class/class.SaveAxa.php',
								// method: 'GET',
								// param: {
									// action: '_get_card_numbersaving',
									// CustomerId: Ext.Cmp("CustomerId").getValue(),
									// PayMethodId: PayMethod.ivr[ivrpay]
								// }
							// }).json()
						// );
			Ext.Ajax({
				url: '../class/class.SaveAxa.php',
				method: 'GET',
				param: {
					action: 'show_card_numbersaving',
					CustomerId: Ext.Cmp("CustomerId").getValue(),
					PayMethodId: PayMethod.ivr[ivrpay]
				}
			}).load("ivr_list");
		}
		//console.log($('#digit_arr').length);
		// if($('#digit_arr').length != 0){
			// alert("digit_arr was found");
		// }
		
		// console.log(Ext.Cmp('digit_arr').getValue());
		
	}
	else
	{
		alert("Please choose IVR Payment Methode");
	}
	
	// console.log(PayMethod.ivr[ivrpay]);
	// console.log(ivrpay);
};

Ext.DOM.UpdateStatusIns = function()
{
	var StatusId = Ext.Cmp('StatusInsured').getValue();
	var InsuredId = Ext.Cmp('InsuredId').getValue();
	var SendFPA = document.getElementById("SENDTOCORE");
	
	if(StatusId)
	{
		var JSnum =(	
			Ext.Ajax
			({ 
				url 	: '../class/class.SaveAxa.php', 
				method :'GET', 
				param 	: { 
					action		: '_update_qc',
					StatusId	: StatusId,
					InsuredId 	: InsuredId
				}
			}).json()
	   );
	   
	   if(JSnum.result)
	   {
			alert('Update status QC, success!');
			Ext.DOM.OnUnloadWindow();
			window.location.hash = 'tabs-12';
			window.location.reload();
			
	   }
	   else{
			alert('Update status QC, failed!');
			return false;
	   }
	}
	else{
		alert('Please choose status!');
		return false;
	}
}