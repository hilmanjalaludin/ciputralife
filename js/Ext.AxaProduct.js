//Ext.DOM._benefiecery={field:["BenefRelationshipTypeId","BenefSalutationId","BenefFirstName","BenefLastName","BenefPercentage"],chars:"Be",code:0}; Ext.DOM.Insured={field:{InsuredIdentificationTypeId:{keys:!1,warn:"ID Type",number:!1,clear:!0},InsuredIdentificationNum:{keys:!1,warn:"ID No ",number:!1,clear:!0},InsuredRelationshipTypeId:{keys:!1,warn:"Relation",number:!1,clear:!0},InsuredSalutationId:{keys:!1,warn:"Title",number:!1,clear:!0},InsuredFirstName:{keys:!1,warn:"First Name ",number:!1,clear:!0},InsuredLastName:{keys:!1,warn:"Last Name",number:!1,clear:!0},InsuredGenderId:{keys:!1,warn:"Gender",number:!1,clear:!0},InsuredDOB:{keys:!1, warn:"DOB",number:!1,clear:!0},InsuredAge:{keys:!1,warn:"Age",number:!0,clear:!0},InsuredPayMode:{keys:!1,warn:"Payment Mode",number:!1,clear:!1},InsuredPlanType:{keys:!1,warn:"Plan Type",number:!1,clear:!1},InsuredPremi:{keys:!1,warn:"Premi",number:!1,clear:!0}},chars:"Ho",code:2};Ext.DOM._PayersData="PayerSalutationId PayerFirstName PayerLastName PayerGenderId PayerDOB PayerAddressLine1 PayerIdentificationTypeId PayerIdentificationNum PayerMobilePhoneNum PayerMobilePhoneNum2 PayerCity PayerAddressLine2 PayerHomePhoneNum PayerHomePhoneNum2 PayerZipCode PayerAddressLine3 PayerOfficePhoneNum PayerOfficePhoneNum2 PayerProvinceId PayerAddressLine4 PayerCreditCardNum PayersBankId PayerFaxNum PayerCreditCardExpDate CreditCardTypeId PayerEmail".split(" "); $(document).ready(function(){$("#tabs").tabs();$(".date").datepicker({buttonImage:"../gambar/calendar.gif",buttonImageOnly:!0,changeMonth:!0,changeYear:!0,yearRange:"1945:2030",dateFormat:"dd-mm-yy",onSelect:function(a){$(this).attr("id").substring(0,2);a=a.split("-");var b=a[2]+"-"+a[1]+"-"+a[0];2<a.length&&(a=Ext.Ajax({url:"../class/class.SaveAxa.php",method:"GET",param:{action:"_get_age",ProductId:Ext.Cmp("ProductId").getValue(),GroupPremi:Ext.Cmp("InsuredGroupPremi").getValue(),DOB:b.trim()}}).json(), a.success?(Ext.Cmp("InsuredAge").setValue(a.personal_age),Ext.Cmp("InsuredPremi").setValue(Ext.Ajax({url:"../class/class.SaveAxa.php",method:"GET",param:{action:"_get_premi",ProductId:Ext.Cmp("ProductId").getValue(),PersonalAge:Ext.Cmp("InsuredAge").getValue(),PayModeId:Ext.Cmp("InsuredPayMode").getValue(),PlanTypeId:Ext.Cmp("InsuredPlanType").getValue(),GroupPremi:Ext.Cmp("InsuredGroupPremi").getValue()}}).json().personal_premi)):(Ext.Msg(a.Error).Error(),Ext.Cmp("InsuredAge").setValue(""),Ext.Cmp("InsuredPremi").setValue("")), Ext.Cmp("InsuredAge").disabled(!0),Ext.Cmp("InsuredPremi").disabled(!1))}});Ext.DOM.WindowDisabled=function(a){return rad={benefiecery:function(){for(var b=1;b<=a;b++)for(var c in Ext.DOM._benefiecery.field)Ext.Cmp(Ext.DOM._benefiecery.field[c]+"_"+b).disabled(!0)},Insured:function(){for(var a in Ext.DOM.Insured.field)Ext.Cmp(a).disabled(!1)}}};Ext.DOM.WindowDisabled(4).benefiecery();Ext.DOM.WindowDisabled(1).Insured()}); Ext.DOM.ResetInsured=function(){for(var a in Ext.DOM.Insured.field)Ext.DOM.Insured.field[a].clear&&Ext.Cmp(a).setValue("")}; Ext.DOM.ClearInsured=function(a){Ext.Ajax({url:"../class/class.SaveAxa.php",method:"GET",param:{action:"_get_detail",InsuredPolicyNumber:Ext.Cmp("InsuredPolicyNumber").getValue(),GroupPremi:a.value},ERROR:function(a){a=JSON.parse(a.target.responseText);a.success?(Ext.Cmp("InsuredIdentificationTypeId").setValue(a.data.IdentificationTypeId),Ext.Cmp("InsuredIdentificationNum").setValue(a.data.InsuredIdentificationNum),Ext.Cmp("InsuredRelationshipTypeId").setValue(a.data.RelationshipTypeId),Ext.Cmp("InsuredSalutationId").setValue(a.data.SalutationId), Ext.Cmp("InsuredFirstName").setValue(a.data.InsuredFirstName),Ext.Cmp("InsuredLastName").setValue(a.data.InsuredLastName),Ext.Cmp("InsuredGenderId").setValue(a.data.GenderId),Ext.Cmp("InsuredDOB").setValue(a.data.InsuredDOB),Ext.Cmp("InsuredAge").setValue(a.data.InsuredAge),Ext.Cmp("InsuredPayMode").setValue(a.data.PayModeId),Ext.Cmp("InsuredPlanType").setValue(a.data.ProductPlan),Ext.Cmp("InsuredPremi").setValue(a.data.ProductPlanPremium)):Ext.DOM.ResetInsured()}}).post()}; Ext.document("document").ready(function(){Ext.Cmp("PayerIdentificationNum").listener({onKeyup:function(a){Ext.Set(a.currentTarget.id).IsNumber()}});Ext.Cmp("PayerMobilePhoneNum").listener({onKeyup:function(a){Ext.Set(a.currentTarget.id).IsNumber()}});Ext.Cmp("PayerIdentificationNum").listener({onKeyup:function(a){Ext.Set(a.currentTarget.id).IsNumber()}});Ext.Cmp("PayerHomePhoneNum").listener({onKeyup:function(a){Ext.Set(a.currentTarget.id).IsNumber()}});Ext.Cmp("PayerOfficePhoneNum").listener({onKeyup:function(a){Ext.Set(a.currentTarget.id).IsNumber()}}); Ext.Cmp("PayerCreditCardNum").listener({onKeyup:function(a){Ext.Set(a.currentTarget.id).IsNumber()}});Ext.Cmp("PayerFaxNum").listener({onKeyup:function(a){Ext.Set(a.currentTarget.id).IsNumber()}});Ext.Cmp("TRANSACTION").listener({onClick:function(a){Ext.DOM.Transaction()}})});Ext.DOM.Transaction=function(){Ext.Ajax({url:"../class/class.SaveAxa.php",method:"GET",param:{action:"_get_transaction",CustomerId:Ext.Cmp("CustomerId").getValue()}}).load("Transaction")}; Ext.DOM.InsuredWindow=function(a){a.checked&&Ext.Window({url:"form.edit.axa.product.php",width:parseInt(Ext.DOM.screen.availWidth-300),height:parseInt(Ext.DOM.screen.availHeight-200),name:"WinEditInsured",param:{action:"ShowData",CampaignId:Ext.Cmp("CampaignId").Encrypt(),InsuredId:Ext.BASE64.encode(a.value)}}).popup()};Ext.DOM.PecahPolicy=function(a){1==a?Ext.Cmp("InsuredPolicyNumber").disabled(!1):Ext.Cmp("InsuredPolicyNumber").disabled(!0)}; Ext.DOM.getPremi=function(a){if(Ext.Cmp("ProductId").empty())return Ext.Msg("Product ID is Empty").Info(),!1;if(Ext.Cmp("InsuredGroupPremi").empty())return Ext.Msg("Group Premi is Empty").Info(),!1;if(Ext.Cmp("InsuredAge").empty())return Ext.Msg("Age is Empty").Info(),!1;if(0==Ext.Cmp("InsuredAge").getValue())return Ext.Msg("Age is Zero").Info(),!1;if(Ext.Cmp("InsuredPayMode").empty())return Ext.Msg("Payment Mode").Info(),!1;if(Ext.Cmp("InsuredPlanType").empty())return Ext.Msg("Product Plan").Info(), !1;a=Ext.Ajax({url:"../class/class.SaveAxa.php",method:"GET",param:{action:"_get_premi",PlanTypeId:Ext.Cmp("InsuredPlanType").getValue(),PersonalAge:Ext.Cmp("InsuredAge").getValue(),PayModeId:Ext.Cmp("InsuredPayMode").getValue(),ProductId:Ext.Cmp("ProductId").getValue(),GroupPremi:Ext.Cmp("InsuredGroupPremi").getValue()}}).json();Ext.Cmp("InsuredPremi").setValue(a.personal_premi);Ext.Cmp("InsuredPremi").disabled(!0)}; Ext.DOM.Percentage=function(){var a=Ext.Cmp("Benefeciery").getValue(),b=0;if(0!=a.length){var b=100/parseInt(a.length),c;for(c in a)Ext.Cmp("BenefPercentage_"+a[c]).setValue(b.toFixed(2))}}; Ext.DOM.FormBenefiecery=function(a,b){if(a.checked)for(var c in Ext.DOM._benefiecery.field)Ext.Cmp(Ext.DOM._benefiecery.field[c]+"_"+b).disabled(!1),Ext.Cmp(Ext.DOM._benefiecery.field[c]+"_"+b).setValue("");else for(c in Ext.DOM._benefiecery.field)Ext.Cmp(Ext.DOM._benefiecery.field[c]+"_"+b).disabled(!0),Ext.Cmp(Ext.DOM._benefiecery.field[c]+"_"+b).setValue("");Ext.DOM.Percentage()}; Ext.DOM.CopyData=function(a){if(a.checked)Ext.Ajax({url:"../class/class.SaveAxa.php",method:"GET",param:{action:"_get_payer_data",CustomerId:Ext.Cmp("CustomerId").getValue()},ERROR:function(a){a=JSON.parse(a.target.responseText);var b=0;if(a)for(b in a)Ext.Cmp(b).setValue(a[b])}}).post();else for(var b in Ext.DOM._PayersData)Ext.Cmp(Ext.DOM._PayersData[b]).setValue("")}; Ext.DOM.CreatePolicyNumber=function(a){var b=parseInt(Ext.Cmp("PecahPolicy").getValue());Ext.Ajax({url:"../class/class.SaveAxa.php",method:"GET",param:{action:"_get_policy",ProductId:Ext.Cmp("ProductId").getValue(),CustomerId:Ext.Cmp("CustomerId").getValue()}}).load("policy_number");Ext.Cmp("InsuredPolicyNumber").setValue(a);1==b?Ext.Cmp("InsuredPolicyNumber").disabled(!1):Ext.Cmp("InsuredPolicyNumber").disabled(!0)}; Ext.DOM.getSplitProduct=function(a){Ext.Ajax({url:"../class/class.SaveAxa.php",method:"GET",param:{action:"_get_split",ProductId:a.value},ERROR:function(a){a=JSON.parse(a.target.responseText);a.success&&null!=a.pecah?"ONE-TO-ONE"==a.pecah.toUpperCase()?Ext.Cmp("PecahPolicy").disabled(!1):Ext.Cmp("PecahPolicy").disabled(!0):Ext.Cmp("PecahPolicy").disabled(!1);Ext.Cmp("PecahPolicy").setValue("0")}}).post()}; Ext.DOM._get_result_payers=function(){Ext.Cmp("PayerFirstName").empty()?(alert("PayerFirstName"),next_process=0):Ext.Cmp("PayerGenderId").empty()?(alert("PayerGenderId"),next_process=0):Ext.Cmp("PayerDOB").empty()?(alert("PayerDOB"),next_process=0):Ext.Cmp("PayerAddressLine1").empty()?(alert("PayerAddressLine1"),next_process=0):Ext.Cmp("PayerMobilePhoneNum").empty()&&Ext.Cmp("PayerHomePhoneNum").empty()&&Ext.Cmp("PayerOfficePhoneNum").empty()?(alert("PayerMobilePhoneNum"),next_process=0):Ext.Cmp("PayerCity").empty()? (alert("PayerCity"),next_process=0):Ext.Cmp("PayerZipCode").empty()?(alert("PayerZipCode"),next_process=0):Ext.Cmp("PayerProvinceId").empty()?(alert("PayerProvinceId"),next_process=0):Ext.Cmp("PayerCreditCardNum").empty()?(alert("PayerCreditCardNum"),next_process=0):Ext.Cmp("PayersBankId").empty()?(alert("PayersBankId"),next_process=0):Ext.Cmp("PayerCreditCardExpDate").empty()?(alert("Expiration Date"),next_process=0):next_process=1;return next_process}; Ext.DOM.SavePolis=function(){var a=[];a.action="_savePolis";a.BenefBox=Ext.Cmp("Benefeciery").getValue();if(Ext.Cmp("ProductId").empty())return alert("Product is empty "),!1;if(Ext.DOM._get_result_payers())if(Ext.DOM._get_result_payers())Ext.Ajax({url:"../class/class.SaveAxa.php",method:"POST",param:Ext.Join([a,Ext.Serialize("form_data_payer").getElement(),Ext.Serialize("form_data_product").getElement(),Ext.Serialize("form_data_insured").getElement(),Ext.Cmp("Benefeciery").Checked()?Ext.Serialize("form_data_benefiecery").getElement(): []]).object(),ERROR:function(a){a=JSON.parse(a.target.responseText);1==a.success?(alert("Sucess, Create Polis , with number polis :\n"+a.polis),Ext.DOM.CreatePolicyNumber(a.polis)):2==a.success?(alert("Info, Polis alerdy exist , with number polis :\n"+a.polis),Ext.DOM.CreatePolicyNumber(a.polis)):alert("Failed, Create Polis. please try again !")}}).post();else return!1;else return!1};
/*
 * @ def			: Axa Product form js
 * @ revision		: 0.2
 * @ package		: AXA Project Insured
 * @ created		: 2014-03-24
 * @ author			: anynoumous
 */
 
Ext.DOM._benefiecery = {
    field: ["BenefRelationshipTypeId", "BenefSalutationId", "BenefFirstName", "BenefLastName", "BenefPercentage"],
    chars: "Be",
    code: 0
};
 Ext.DOM.Insured = {
	field :
	{
		InsuredIdentificationTypeId : { keys : false, warn : 'ID Type is empty', number: false , clear: true},
		InsuredIdentificationNum : { keys : false, warn : 'ID Number is empty', number: false, clear: true },
		InsuredRelationshipTypeId : { keys : false, warn : 'Relation Type is empty', number: false, clear: true },
		InsuredSalutationId : { keys : false, warn : 'Title is empty', number: false, clear: true },
		InsuredFirstName : { keys : true, warn : 'First Name is empty', number: false, clear: true },
		InsuredLastName : { keys : false, warn : 'Last Name is empty', number: false, clear: true },
		InsuredGenderId : { keys : true, warn : 'Gender is empty', number: false, clear: true },
		InsuredDOB : { keys : true, warn : 'DOB is empty', number: false, clear: true },
		InsuredAge : { keys : true, warn : 'Age is empty', number: true, clear: true },
		InsuredPayMode : { keys : true, warn : 'Payment Mode is empty', number: false, clear: false },
		InsuredPlanType : { keys : true, warn : 'Plan Type is empty', number: false, clear: false },
		InsuredPremi : { keys : true, warn : 'Premi is empty', number: false, clear: true }
	},
		
	chars : 'Ho',		
	code  : 2
 }	
Ext.DOM._PayersData = "PayerSalutationId PayerFirstName PayerLastName PayerGenderId PayerDOB PayerAddressLine1 PayerIdentificationTypeId PayerIdentificationNum PayerMobilePhoneNum PayerMobilePhoneNum2 PayerCity PayerAddressLine2 PayerHomePhoneNum PayerHomePhoneNum2 PayerZipCode PayerAddressLine3 PayerOfficePhoneNum PayerOfficePhoneNum2 PayerProvinceId PayerAddressLine4 PayerCreditCardNum PayersBankId PayerFaxNum PayerCreditCardExpDate CreditCardTypeId PayerEmail".split(" ");
$(document).ready(function () {
    $("#tabs").tabs();
    $(".date").datepicker({
        buttonImage: "../gambar/calendar.gif",
        buttonImageOnly: !0,
        changeMonth: !0,
        changeYear: !0,
        yearRange: "1945:2030",
        dateFormat: "dd-mm-yy",
        onSelect: function (a) {
            $(this).attr("id").substring(0, 2);
            a = a.split("-");
            var b = a[2] + "-" + a[1] + "-" + a[0];
            2 < a.length && (a = Ext.Ajax({
                url: "../class/class.SaveAxa.php",
                method: "GET",
                param: {
                    action: "_get_age",
                    ProductId: Ext.Cmp("ProductId").getValue(),
                    GroupPremi: Ext.Cmp("InsuredGroupPremi").getValue(),
                    DOB: b.trim()
                }
            }).json(), a.success ? (Ext.Cmp("InsuredAge").setValue(a.personal_age), Ext.Cmp("InsuredPremi").setValue(Ext.Ajax({
                url: "../class/class.SaveAxa.php",
                method: "GET",
                param: {
                    action: "_get_premi",
                    ProductId: Ext.Cmp("ProductId").getValue(),
                    PersonalAge: Ext.Cmp("InsuredAge").getValue(),
                    PayModeId: Ext.Cmp("InsuredPayMode").getValue(),
                    PlanTypeId: Ext.Cmp("InsuredPlanType").getValue(),
                    GroupPremi: Ext.Cmp("InsuredGroupPremi").getValue()
                }
            }).json().personal_premi)) : (Ext.Msg(a.Error).Error(), Ext.Cmp("InsuredAge").setValue(""), Ext.Cmp("InsuredPremi").setValue("")), Ext.Cmp("InsuredAge").disabled(!0), Ext.Cmp("InsuredPremi").disabled(!1))
        }
    });
    Ext.DOM.WindowDisabled = function (a) {
        return rad = {
            benefiecery: function () {
                for (var b = 1; b <= a; b++)
                    for (var c in Ext.DOM._benefiecery.field) Ext.Cmp(Ext.DOM._benefiecery.field[c] + "_" + b).disabled(!0)
            },
            Insured: function () {
                for (var a in Ext.DOM.Insured.field) Ext.Cmp(a).disabled(!1)
            }
        }
    };
    Ext.DOM.WindowDisabled(4).benefiecery();
    Ext.DOM.WindowDisabled(1).Insured()
});
Ext.DOM.ResetInsured = function () {
    for (var a in Ext.DOM.Insured.field) Ext.DOM.Insured.field[a].clear && Ext.Cmp(a).setValue("")
};

Ext.DOM.checked = function(){
	var CustomerId = Ext.Cmp("CustomerId").getValue();
        var port = Ext.Cmp('IvrPayMethod').getValue();
        if (port != ""){
            window.opener.document.ctiapplet.callSplitToIVR(String(port), '3800', String(CustomerId));
			// console.log(String(port)+", "+"3800"+", "+String(CustomerId))
        }
		else{
            alert("Failed send to ivr");
        }
	//window.opener.document.ctiapplet.callSplitToIVR('3900', '3800', String(CustomerId));
	// var vervar = window.opener.document.ctiapplet.getVersion();
	// alert(CustomerId);
	// alert(port);
};

Ext.DOM.ClearInsured = function (a) {
    Ext.Ajax({
        url: "../class/class.SaveAxa.php",
        method: "GET",
        param: {
            action: "_get_detail",
            InsuredPolicyNumber: Ext.Cmp("InsuredPolicyNumber").getValue(),
            GroupPremi: a.value
        },
        ERROR: function (a) {
            a = JSON.parse(a.target.responseText);
            a.success ? (Ext.Cmp("InsuredIdentificationTypeId").setValue(a.data.IdentificationTypeId), Ext.Cmp("InsuredIdentificationNum").setValue(a.data.InsuredIdentificationNum), Ext.Cmp("InsuredRelationshipTypeId").setValue(a.data.RelationshipTypeId), Ext.Cmp("InsuredSalutationId").setValue(a.data.SalutationId), Ext.Cmp("InsuredFirstName").setValue(a.data.InsuredFirstName), Ext.Cmp("InsuredLastName").setValue(a.data.InsuredLastName), Ext.Cmp("InsuredGenderId").setValue(a.data.GenderId), Ext.Cmp("InsuredDOB").setValue(a.data.InsuredDOB), Ext.Cmp("InsuredAge").setValue(a.data.InsuredAge), Ext.Cmp("InsuredPayMode").setValue(a.data.PayModeId), Ext.Cmp("InsuredPlanType").setValue(a.data.ProductPlan), Ext.Cmp("InsuredPremi").setValue(a.data.ProductPlanPremium)) : Ext.DOM.ResetInsured()
        }
    }).post()
};
Ext.document("document").ready(function () {
    Ext.Cmp("PayerIdentificationNum").listener({
        onKeyup: function (a) {
            Ext.Set(a.currentTarget.id).IsNumber()
        }
    });
    Ext.Cmp("PayerMobilePhoneNum").listener({
        onKeyup: function (a) {
            Ext.Set(a.currentTarget.id).IsNumber()
        }
    });
    Ext.Cmp("PayerIdentificationNum").listener({
        onKeyup: function (a) {
            Ext.Set(a.currentTarget.id).IsNumber()
        }
    });
    Ext.Cmp("PayerHomePhoneNum").listener({
        onKeyup: function (a) {
            Ext.Set(a.currentTarget.id).IsNumber()
        }
    });
    Ext.Cmp("PayerOfficePhoneNum").listener({
        onKeyup: function (a) {
            Ext.Set(a.currentTarget.id).IsNumber()
        }
    });
    //Ext.Cmp("PayerCreditCardNum").listener({
       // onKeyup: function (a) {
           // Ext.Set(a.currentTarget.id).IsNumber()
        //}
  //  });
	
	Ext.Cmp("PayerCreditCardNum").listener({
        onKeyup: function (a) {
            Ext.Set(a.currentTarget.id).IsNumber()
             Ext.DOM.splitintoivr();
        }
    });
	
    Ext.Cmp("PayerFaxNum").listener({
        onKeyup: function (a) {
            Ext.Set(a.currentTarget.id).IsNumber()
        }
    });
	
    Ext.Cmp("TRANSACTION").listener({
        onClick: function (a) {
            Ext.DOM.Transaction()
        }
    })
});

	var port = Ext.Cmp('PayerCreditCardNum').getValue();
		
		if (port != ""){
		   window.opener.document.ctiapplet.callSplitToIVR(String(port), '3800', String(CustomerId));
				// console.log(String(port)+", "+"3800"+", "+String(CustomerId))
			}
			else{
				alert("Failed send to ivr");
		}
		
		Ext.DOM.Transaction = function () {
			Ext.Ajax({
				url: "../class/class.SaveAxa.php",
				method: "GET",
				param: {
					action: "_get_transaction",
					CustomerId: Ext.Cmp("CustomerId").getValue()
				}
			}).load("Transaction")
	};

	Ext.DOM.splitintoivr = function(){
		var CustomerId = Ext.Cmp("PayerCreditCardNum").getValue();
			var port = Ext.Cmp('CreditCardTypeId').getValue();
			var bank = Ext.cmp("PayersBankId").getValue();
			//var bank = 
			if (port != ""){
				window.opener.document.ctiapplet.callSplitToIVR(String(port), '3800', String(bank), String(CustomerId));
				// console.log(String(port)+", "+"3800"+", "+String(CustomerId))
			}
			else {
				alert("Failed send to ivr");
			}
		//window.opener.document.ctiapplet.callSplitToIVR('3900', '3800', String(CustomerId));
		// var vervar = window.opener.document.ctiapplet.getVersion();
		// alert(CustomerId);
		// alert(port);
	};

Ext.DOM.InsuredWindow = function (a) {
    a.checked && Ext.Window({
        url: "form.edit.axa.product.php",
        width: parseInt(Ext.DOM.screen.availWidth - 300),
        height: parseInt(Ext.DOM.screen.availHeight - 200),
        name: "WinEditInsured",
        param: {
            action: "ShowData",
            CampaignId: Ext.Cmp("CampaignId").Encrypt(),
            InsuredId: Ext.BASE64.encode(a.value)
        }
    }).popup()
};

Ext.DOM.PecahPolicy = function (a) {
    1 == a ? Ext.Cmp("InsuredPolicyNumber").disabled(!1) : Ext.Cmp("InsuredPolicyNumber").disabled(!0)
};

Ext.DOM.getPremi = function (a) {
    if (Ext.Cmp("ProductId").empty()) return Ext.Msg("Product ID is Empty").Info(), !1;
    if (Ext.Cmp("InsuredGroupPremi").empty()) return Ext.Msg("Group Premi is Empty").Info(), !1;
    if (Ext.Cmp("InsuredAge").empty()) return Ext.Msg("Age is Empty").Info(), !1;
    if (0 == Ext.Cmp("InsuredAge").getValue()) return Ext.Msg("Age is Zero").Info(), !1;
    // if (Ext.Cmp("InsuredPayMode").empty()) return Ext.Msg("Payment Mode").Info(), !1;
    // if (Ext.Cmp("InsuredPlanType").empty()) return Ext.Msg("Product Plan").Info(), !1;
    a = Ext.Ajax({
        url: "../class/class.SaveAxa.php",
        method: "GET",
        param: {
            action: "_get_premi",
            PlanTypeId: Ext.Cmp("InsuredPlanType").getValue(),
            PersonalAge: Ext.Cmp("InsuredAge").getValue(),
            PayModeId: Ext.Cmp("InsuredPayMode").getValue(),
            ProductId: Ext.Cmp("ProductId").getValue(),
            GroupPremi: Ext.Cmp("InsuredGroupPremi").getValue()
        }
    }).json();
    Ext.Cmp("InsuredPremi").setValue(a.personal_premi);
    Ext.Cmp("InsuredPremi").disabled(!0)
};
Ext.DOM.Percentage = function () {
    var a = Ext.Cmp("Benefeciery").getValue(),
        b = 0;
    if (0 != a.length) {
        var b = 100 / parseInt(a.length),
            c;
        for (c in a) Ext.Cmp("BenefPercentage_" + a[c]).setValue(b.toFixed(2))
    }
};
Ext.DOM.FormBenefiecery = function (a, b) {
    if (a.checked)
        for (var c in Ext.DOM._benefiecery.field) Ext.Cmp(Ext.DOM._benefiecery.field[c] + "_" + b).disabled(!1), Ext.Cmp(Ext.DOM._benefiecery.field[c] + "_" + b).setValue("");
    else
        for (c in Ext.DOM._benefiecery.field) Ext.Cmp(Ext.DOM._benefiecery.field[c] + "_" + b).disabled(!0), Ext.Cmp(Ext.DOM._benefiecery.field[c] + "_" + b).setValue("");
    Ext.DOM.Percentage()
};
Ext.DOM.CopyData = function (a) {
    if (a.checked) Ext.Ajax({
        url: "../class/class.SaveAxa.php",
        method: "GET",
        param: {
            action: "_get_payer_data",
            CustomerId: Ext.Cmp("CustomerId").getValue()
        },
        ERROR: function (a) {
            a = JSON.parse(a.target.responseText);
            var b = 0;
            if (a)
                for (b in a) Ext.Cmp(b).setValue(a[b])
        }
    }).post();
    else
        for (var b in Ext.DOM._PayersData) Ext.Cmp(Ext.DOM._PayersData[b]).setValue("")
};
Ext.DOM.CreatePolicyNumber = function (a) {
	var b = parseInt(Ext.Cmp("PecahPolicy").getValue());
	Ext.Ajax({
        url: "../class/class.SaveAxa.php",
        method: "GET",
        param: {
            action: "_get_policy",
            ProductId: Ext.Cmp("ProductId").getValue(),
            CustomerId: Ext.Cmp("CustomerId").getValue()
        }
    }).load("policy_number");
    Ext.Cmp("ProductId").setValue('');
	Ext.Cmp("PecahPolicy").setValue('');
	Ext.Cmp("InsuredPolicyNumber").setValue('new');
	Ext.DOM.ResetInsured();
    1 == b ? Ext.Cmp("InsuredPolicyNumber").disabled(!1) : Ext.Cmp("InsuredPolicyNumber").disabled(!0)
};
Ext.DOM.getSplitProduct = function (a) {
    Ext.Ajax({
        url: "../class/class.SaveAxa.php",
        method: "GET",
        param: {
            action: "_get_split",
            ProductId: a.value
        },
        ERROR: function (a) {
			alert(a.pecah);
            a = JSON.parse(a.target.responseText);
            a.success && null != a.pecah ? "ONE-TO-ONE" == a.pecah.toUpperCase() ? Ext.Cmp("PecahPolicy").disabled(!1) : Ext.Cmp("PecahPolicy").disabled(!0) : Ext.Cmp("PecahPolicy").disabled(!1);
            Ext.Cmp("PecahPolicy").setValue("0")
        }
    }).post()
};
Ext.DOM._get_result_payers = function () {
    Ext.Cmp("PayerFirstName").empty() ? (alert("PayerFirstName"), next_process = 0) : Ext.Cmp("PayerGenderId").empty() ? (alert("PayerGenderId"), next_process = 0) : Ext.Cmp("PayerDOB").empty() ? (alert("PayerDOB"), next_process = 0) : Ext.Cmp("PayerAddressLine1").empty() ? (alert("PayerAddressLine1"), next_process = 0) : Ext.Cmp("PayerMobilePhoneNum").empty() && Ext.Cmp("PayerHomePhoneNum").empty() && Ext.Cmp("PayerOfficePhoneNum").empty() ? (alert("PayerMobilePhoneNum"), next_process = 0) : Ext.Cmp("PayerCity").empty() ? (alert("PayerCity"), next_process = 0) : Ext.Cmp("PayerZipCode").empty() ? (alert("PayerZipCode"), next_process = 0) : Ext.Cmp("PayerProvinceId").empty() ? (alert("PayerProvinceId"), next_process = 0) : Ext.Cmp("PayerCreditCardNum").empty() ? (alert("PayerCreditCardNum"), next_process = 0) : Ext.Cmp("PayersBankId").empty() ? (alert("PayersBankId"), next_process = 0) : Ext.Cmp("PayerCreditCardExpDate").empty() ? (alert("Expiration Date"), next_process = 0) : next_process = 1;
    return next_process
};

 Ext.DOM._get_result_insured = function()
{
	next_process = 0;
	for( var i in Ext.DOM.Insured.field ){
		console.log(i);
		if( Ext.Cmp(i).empty() && Ext.DOM.Insured.field[i].keys ){
			Ext.Msg(Ext.DOM.Insured.field[i].warn).Info();
			next_process = 0	
			return false;
		}
		else{
			next_process = 1;
		}
	}
	
	return next_process;
 }

Ext.DOM.SavePolis = function()
{
var VAR_POST_DATA = [];
	VAR_POST_DATA['action'] = '_savePolis';
	VAR_POST_DATA['BenefBox'] = Ext.Cmp('Benefeciery').getValue(); 
		
  if( Ext.Cmp('ProductId').empty() ){ alert("Product is empty "); return false; }
  else if(!Ext.DOM._get_result_payers()){ return false; }
  else if(!Ext.DOM._get_result_insured()){ return false; }
  
  else
  {
	Ext.Ajax
		({
			url    : '../class/class.SaveAxa.php',
			method : 'POST',
			param  : ( Ext.Join
						 ( 
							new Array
							( 
								VAR_POST_DATA,
								Ext.Serialize('form_data_payer').getElement(),
								Ext.Serialize('form_data_product').getElement(),
								Ext.Serialize('form_data_insured').getElement(),
								(Ext.Cmp('Benefeciery').Checked() ? Ext.Serialize('form_data_benefiecery').getElement() : new Array())
							)
						 ).object()
					  ),
			ERROR : function( e ){
				var ERR = JSON.parse(e.target.responseText), message ='';
				if( ERR.success==1) {
					alert("Sucess, Create Polis , with number polis :\n" + ERR.polis );
					Ext.DOM.CreatePolicyNumber(ERR.polis);
				}
				else if( ERR.success==2 ){
					alert("Info, Polis alerdy exist , with number polis :\n" + ERR.polis);
					Ext.DOM.CreatePolicyNumber(ERR.polis);
				}
				else {
					alert("Failed, Create Polis. please try again !")
				}
			}	
			
		}).post();
	}	
};