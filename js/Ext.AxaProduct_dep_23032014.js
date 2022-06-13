/* @ def 	:  define of Global dependent input if not followed !, Try on window object
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */
// define dependent field
 Ext.DOM._benefiecery = {
	field :[
		 'BenefRelationshipTypeId', 
		 'BenefSalutationId', 
		 'BenefFirstName', 
		 'BenefLastName', 
		 'BenefPercentage'
		],
		 
	chars : 'Be',
	code  : 0
 }	
 

// define holder field
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
		InsuredPayMode : { keys : false, warn : 'Payment Mode', number: false, clear: true },
		InsuredPlanType : { keys : false, warn : 'Plan Type', number: false, clear: true },
		InsuredPremi : { keys : false, warn : 'Premi', number: false, clear: true }
	},
		
	chars : 'Ho',		
	code  : 2
 }	
// define payers field
 Ext.DOM._PayersData = [
	'PayerSalutationId',
	'PayerFirstName',
	'PayerLastName',
	'PayerGenderId',
	'PayerDOB', 
	'PayerAddressLine1',
	'PayerIdentificationTypeId',
	'PayerIdentificationNum',
	'PayerMobilePhoneNum',
	'PayerCity',
	'PayerAddressLine2',
	'PayerHomePhoneNum',
	'PayerZipCode',
	'PayerAddressLine3',
	'PayerOfficePhoneNum',
	'PayerProvinceId',
	'PayerAddressLine4',
	'PayerCreditCardNum',
	'PayersBankId',
	'PayerFaxNum', 
	'PayerCreditCardExpDate', 
	'CreditCardTypeId', 
	'PayerEmail']
 
/* @ jquery :	fucked 
 * @ render on ready document
 * @ will ender by "tabs "   
 */
 
$(document).ready(function(){

/* @ jquery :	fucked 
 * @ render on ready document
 * @ will ender by "tabs "   
 */
 $("#tabs" ).tabs();
 
/*
 * @ get all data date picker srializer 
 * @ is simple get data asumsion 
 */
 $(".date").datepicker
 ({
    buttonImage		: '../gambar/calendar.gif',  buttonImageOnly : true, changeMonth : true, changeYear		: true, yearRange : '1945:2030', dateFormat : 'dd-mm-yy',
    onSelect  		: function(e)
   {
	 var _a = $(this).attr("id"), 
		 _b = _a.substring(0,2), 
		 _c = e.split('-'),
		 _d = _c[2]+'-'+_c[1]+'-'+_c[0];
	 
	 if( _c.length > 2 )
	 {			
		var JSnum = ( Ext.Ajax({  url : '../class/class.SaveAxa.php',  method :'GET', param :{ action:'_get_age', GroupPremi : Ext.Cmp("InsuredGroupPremi").getValue(), DOB : _d.trim()}}).json());
				
				Ext.Cmp('InsuredAge').setValue( JSnum.personal_age );
				Ext.Cmp("InsuredPremi").setValue(	
					Ext.Ajax({ 
						url 	: '../class/class.SaveAxa.php', method :'GET', 
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
				Ext.Cmp('InsuredAge').disabled(true);
				Ext.Cmp("InsuredPremi").disabled(false);
		//}
	 }}
  });
  
/* @ Ext 		: autoload   
 * @ render 	: on ready document
 * @ will ender by "tabs "   
 */
 
Ext.DOM.WindowDisabled = ( function(e){
 return rad = { 
 
/* @ Ext 		: autoload   
 * @ render 	: on ready document
 * @ will ender by "tabs "   
 */
	benefiecery :function(){
		for( var p =1; p<=e; p++) {  
		  for( var a in Ext.DOM._benefiecery.field ) {
			Ext.Cmp(Ext.DOM._benefiecery.field[a]+"_"+p).disabled(true);
			} 
		}
	},
	
/* @ Ext 		: autoload   
 * @ render 	: on ready document
 * @ will ender by "tabs "   
 */	
	Insured : function(){
		for( var i in Ext.DOM.Insured.field) {
			Ext.Cmp(i).disabled(false);
		} 
	}
	
  }});
  
// disabled first loqding 

 Ext.DOM.WindowDisabled(4).benefiecery();
 Ext.DOM.WindowDisabled(1).Insured();
 });

/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */

 
Ext.DOM.ClearInsured = function(options) {
	for( var i in Ext.DOM.Insured.field){
		if( Ext.DOM.Insured.field[i].clear ){
			Ext.Cmp(i).setValue('');	
		}
	} 
}
  
/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */
 
 Ext.document('document').ready( function(){ 
	Ext.Cmp('PayerIdentificationNum').listener({'onKeyup' : function( e ){ Ext.Set( e.currentTarget.id ).IsNumber();}});
	Ext.Cmp('PayerMobilePhoneNum').listener({ 'onKeyup' : function( e ){ Ext.Set( e.currentTarget.id ).IsNumber();}});
	Ext.Cmp('PayerIdentificationNum').listener({'onKeyup': function( e ){ Ext.Set( e.currentTarget.id ).IsNumber();}});
	Ext.Cmp('PayerHomePhoneNum').listener({'onKeyup': function( e ){ Ext.Set( e.currentTarget.id ).IsNumber(); }});
	Ext.Cmp('PayerOfficePhoneNum').listener({'onKeyup': function( e ){ Ext.Set( e.currentTarget.id ).IsNumber();}});
	Ext.Cmp('PayerCreditCardNum').listener({'onKeyup': function( e ){ Ext.Set( e.currentTarget.id ).IsNumber();}});
	Ext.Cmp('PayerFaxNum').listener({'onKeyup': function( e ){ Ext.Set( e.currentTarget.id ).IsNumber();}});
 });
  
/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */
 
Ext.DOM.PecahPolicy  = function(PecahPolis)
{
	if( PecahPolis ==1){
		Ext.Cmp('InsuredPolicyNumber').disabled(false); 
	}
	else{
		Ext.Cmp('InsuredPolicyNumber').disabled(true);	
	}
}
 
/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */

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
						url 	: '../class/class.SaveAxa.php', method :'GET', 
						param 	: { 
							action		: '_get_premi', 
							PlanTypeId  : Ext.Cmp('InsuredPlanType').getValue(),
							PersonalAge	: Ext.Cmp('InsuredAge').getValue(),	
							PayModeId	: Ext.Cmp('InsuredPayMode').getValue(),
							ProductId	: Ext.Cmp('ProductId').getValue(),
							GroupPremi 	: Ext.Cmp('InsuredGroupPremi').getValue(),
						}
					}).json()
			   );
			   
			 Ext.Cmp('InsuredPremi').setValue(JSnum.personal_premi);  
			 Ext.Cmp('InsuredPremi').disabled(true); 
	}	   
};

/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */
 Ext.DOM.Percentage = function() 
 {
	var _box = Ext.Cmp('Benefeciery').getValue(), _tot = 0, _percent = 100, _percent_personal = 0 ;
	if( _box.length!=0 ) {
		_tot = 	parseInt( _percent ) / parseInt( _box.length );
		
		for( var a in _box ) {
			Ext.Cmp('BenefPercentage_' + _box[a]).setValue(_tot.toFixed(2)); 
		}
	}
 }; 
/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */
Ext.DOM.FormBenefiecery = function( checkbox, p)
{
	if( checkbox.checked )
	{
		for(var i in Ext.DOM._benefiecery.field ){
			Ext.Cmp(Ext.DOM._benefiecery.field[i]+"_"+p).disabled(false);
			Ext.Cmp(Ext.DOM._benefiecery.field[i]+"_"+p).setValue('');
		}
	}
	else
	{
		for(var i in Ext.DOM._benefiecery.field ) {
			Ext.Cmp(Ext.DOM._benefiecery.field[i]+"_"+p).disabled(true);
			Ext.Cmp(Ext.DOM._benefiecery.field[i]+"_"+p).setValue('');
		}
	}
	
	// calculation
	Ext.DOM.Percentage();
};


/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */
 Ext.DOM.CopyData=function( checkbox )
  {
	if( checkbox.checked )
	{
		Ext.Ajax({
			url    : '../class/class.SaveAxa.php',
			method : 'GET',
			param  : {
				action : '_get_payer_data', 
				CustomerId : Ext.Cmp('CustomerId').getValue()
			},
			ERROR : function(e){
				var ERR = JSON.parse(e.target.responseText), p = 0;
				if( ERR ) {
					for( var p in ERR){
						Ext.Cmp(p).setValue(ERR[p]);
					}
				}
			}
		}).post()
	}
	else
	{
		for(var p in Ext.DOM._PayersData )
			Ext.Cmp(Ext.DOM._PayersData[p]).setValue('')
	}
  }
  
/* @ def 	:  HolderPlanType 
 *
 * @ triger : Pecah Policy
 * @ params : jika terjadi pecah polis
 */
Ext.DOM.getSplitProduct = function(opts)
  {
	Ext.Ajax({
		url     : '../class/class.SaveAxa.php',
		method 	: 'GET',
		param 	: {
			action 	  : '_get_split',
			ProductId : opts.value
		},
		ERROR : function(e){
			var ERR = JSON.parse(e.target.responseText);
			if( ERR.success )
			{
				if( ERR.pecah.toUpperCase() =='ONE-TO-ONE')
				{
					Ext.Cmp('PecahPolicy').disabled(false);
					Ext.Cmp('PecahPolicy').setValue('0');
				}
				else{
					Ext.Cmp('PecahPolicy').disabled(true);
					Ext.Cmp('PecahPolicy').setValue('0');
				}
			}
			else{
				Ext.Cmp('PecahPolicy').disabled(true);
				Ext.Cmp('PecahPolicy').setValue('');
			}
		}
	}).post();
  };
  
/* @ def 	:  _get_result_spouse 
 *
 * @ triger : _get_result_spouse Policy
 * @ params : jika terjadi pecah polis
 */
Ext.DOM._get_result_payers = function()
{
	//if( Ext.Cmp('PayerSalutationId').empty() ){ alert('PayerSalutationId'); next_process =0;  }
	if(Ext.Cmp('PayerFirstName').empty()){ alert('PayerFirstName'); next_process =0; }
	//else if(Ext.Cmp('PayerLastName').empty()){ alert('PayerLastName'); next_process =0; }
	else if(Ext.Cmp('PayerGenderId').empty()){ alert('PayerGenderId'); next_process =0; }
	else if(Ext.Cmp('PayerDOB').empty()){ alert('PayerDOB'); next_process =0; }
	else if(Ext.Cmp('PayerAddressLine1').empty()){ alert('PayerAddressLine1'); next_process =0; }
	//else if(Ext.Cmp('PayerIdentificationTypeId').empty()){ alert('PayerIdentificationTypeId'); next_process =0; }
	//else if(Ext.Cmp('PayerIdentificationNum').empty()){ alert('PayerIdentificationNum'); next_process =0; }
	else if(Ext.Cmp('PayerMobilePhoneNum').empty() && Ext.Cmp('PayerHomePhoneNum').empty() && Ext.Cmp('PayerOfficePhoneNum').empty()){  alert('PayerMobilePhoneNum'); next_process =0; }
	else if(Ext.Cmp('PayerCity').empty()){  alert('PayerCity'); next_process =0;  }
	//else if(Ext.Cmp('PayerAddressLine2').empty()){  alert('PayerAddressLine2'); next_process =0; }
	//else if(Ext.Cmp('PayerHomePhoneNum').empty()){  alert('PayerHomePhoneNum'); next_process =0; }
	else if(Ext.Cmp('PayerZipCode').empty()){ alert('PayerZipCode'); next_process =0; }
	//else if(Ext.Cmp('PayerAddressLine3').empty()){ alert('PayerAddressLine3'); next_process =0; }
	//else if(Ext.Cmp('PayerOfficePhoneNum').empty()){ alert('PayerOfficePhoneNum'); next_process =0;  }
	else if(Ext.Cmp('PayerProvinceId').empty()){ alert('PayerProvinceId'); next_process =0; }
	//else if(Ext.Cmp('PayerAddressLine4').empty()){ alert('PayerAddressLine4'); next_process =0; }
	else if(Ext.Cmp('PayerCreditCardNum').empty()){ alert('PayerCreditCardNum'); next_process =0; }
	else if(Ext.Cmp('PayersBankId').empty()){ alert('PayersBankId'); next_process =0; }
	//else if(Ext.Cmp('PayerFaxNum').empty()){ alert('PayerFaxNum '); next_process =0;}
	else if(Ext.Cmp('PayerCreditCardExpDate').empty()){ alert('Expiration Date'); next_process =0; }
	//else if(Ext.Cmp('CreditCardTypeId').empty()){ alert('CreditCardTypeId '); next_process =0; }
	//else if(Ext.Cmp('PayerEmail').empty()){ alert('PayerEmail');  next_process =0; }
	else{ next_process =1;  }
	
	return next_process;
 }
/* @ def 	:  SavePolis 
 *
 * @ triger : SavePolis Policy
 * @ params : jika terjadi pecah polis
 */
Ext.DOM.SavePolis = function()
{
	Ext.Ajax
		({
			url    : '../class/class.SaveAxa.php',
			method : 'POST',
			param  : ( Ext.Join
						 ( 
							new Array
							( 
								Ext.Serialize('form_data_payer').getElement(),
								Ext.Serialize('form_data_insured').getElement(),
								(Ext.Cmp('Spouse').Checked() ? Ext.Serialize('form_data_spouse').getElement() : new Array()),
								(Ext.Cmp('Dependent').Checked() ? Ext.Serialize('form_data_dependent').getElement() : new Array()),
								(Ext.Cmp('Benefeciery').Checked() ? Ext.Serialize('form_data_benefiecery').getElement() : new Array())
							)
						 ).object()
					  ),
			ERROR : function( e ){
				var ERR = JSON.parse(e.target.responseText), message ='';
				if( ERR.success==1) {
					for( var a in ERR.polis ) { message += ERR.polis[a]+"\n";  }	
					alert("Sucess, Create Polis , with number polis :\n" + message );
					return false;
				}
				else if( ERR.success==2 ){
					for( var a in ERR.polis ){ message += ERR.polis[a]+"\n"; }	
					alert("Info, Polis alerdy exist , with number polis :\n" + message );
					return false;
				}
				else {
					alert("Failed, Create Polis. please try again !")
					return false;
				}
			}	
			
		}).post();
};

