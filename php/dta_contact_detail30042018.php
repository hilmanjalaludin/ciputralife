<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	function getScriptActive(){
		global $db;
		
		$sql = " select 
					a.ScriptId,
					a.ScriptUpload
			from t_gn_productscript a
			left join t_gn_product b on a.ProductId=b.ProductId
			left join tms_agent c on a.UploadBy=c.UserId
			where ScriptFlagStatus=1 ";
		
		$qry = $db->execute($sql,__FILE__,__LINE__);	
		while( $row = $db->fetchrow($qry) ){
			$datas[$row->ScriptId] = $row->ScriptUpload;
		}
		
		return "[".json_encode($datas)."]";	
	}
	
	
	function getFormLayout()
	{
		global $db;
		$sql = " SELECT a.FormLayout , a.FormEditLayout
				 FROM t_gn_formlayout a 
				 LEFT JOIN t_gn_campaignproduct b on a.ProductId=b.ProductId
				 WHERE a.FormConds=1 
				 AND b.CampaignId='".$db -> escPost('CampaignId')."' ";
		//echo $sql;
		
		$qry = $db->execute($sql,__FILE__,__LINE__);	
		if( $qry && ($rows = @mysql_fetch_assoc($qry)))
		{	
			if($_REQUEST['VerifiedStatus']!='') return $rows['FormEditLayout'];
			else{
				return $rows['FormLayout'];
			}
		}
	}

?>
<!--<script type="text/javascript" src="<+?php echo $app->basePath();?>/js/cti.js"></script>	-->

<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
<script>
	var V_SCRIPT	=<?php echo getScriptActive(); ?>;
	var countGrid = 0;
 /* define  code call **/
	
	var V_VERIFIED      = '<?php echo $db->escPost('VerifiedStatus');?>';
	//var V_FORMPOLICY   =  '<?php echo getFormLayout(); ?>';
	var V_FORMPOLICY    =  'form.axa.product.php';
	var V_FORMEDIT   	=  'form.axa.qa.product.php';
	var CustomerId      = '<?php echo $db->escPost('CustomerId'); ?>';
	var CampaignId      = '<?php echo $db->escPost('CampaignId'); ?>';
	var CallReasonId    = '<?php echo $db->escPost('CallReasonId'); ?>';
	var CustomerName    = '<?php echo $db->escPost('CustomerName');?>';
	var CustomerAddress = '<?php echo $db->escPost('CustomerAddress');?>';
	var hiddenresult    = '<?php echo $db->escPost('CallReasonId'); ?>';
					
	var V_INTWTHSPS  = 402;
	var V_INTWTHSLF  = 1;
	var V_CALLBACKL  = 310;
	
/* define info customers **/
	var InitPhp	     = '../class/tpl.contact.detail.php?';
	var phoneCall    = {
						initPhone  : false,
						initType   : '', 
						initNumber : '',
						InitLater  : false,
						initCreate : false,
						initCall   : false,
						initStatus : 0,
						initChoose : false,
						CustomerId : CustomerId,
						initSave   : false,
						NextDatas  : false,
						initReferal: false,
						callIsRun  : false
					 }
	
 /* disabled right click && Refresh  **/
	
	// document.oncontextmenu=new Function("return false");
	// window.onkeypress=function(e){
		// var winEvent = e; 
		// if( winEvent.keyCode==116 )
		// {
			// return false;	
		// }
	// }
/* recording suspend data */
	
	
	var getFileRecording = function(){
			$(function(){
				doJava.Params = { 
					action:'get_recording',
					customerid:CustomerId
				}
				$('#recording_file').load(InitPhp+'?'+doJava.ArrVal() );
			});
	}
	
	var playRecording = function(filename){
		$(function(){
				doJava.Params = { 
					action:'play_recording',
					rec_id:filename,
					customerid:CustomerId
				}
				$('#recording_play').load(InitPhp+'?'+doJava.ArrVal() );
		});
	}
	
/* on top content **/
	
	var getDefaultContact = function(){
		$(function(){
			$('#contact_default_info').load(InitPhp+"action=default_contact&customerid="+CustomerId+"&campaignid="+CampaignId);
		});
	}
	
 /* Home **/
 
	var getHomeContact = function(){
		$(function(){
			$('#contact_home').load(InitPhp+"action=home_contact&customerid="+CustomerId+"&campaignid="+CampaignId);
		});
	}
	
/* Office **/
	
	var getOfficeContact = function(){
		$(function(){
			$('#contact_office').load(InitPhp+"action=office_contact&customerid="+CustomerId+"&campaignid="+CampaignId);
		});
	}

	var getRemark = function(){
		$(function(){
			$('#remarks').load(InitPhp+"action=remarks&customerid="+CustomerId+"&campaignid="+CampaignId);
		});
    }
	var getXsell = function(){
		$(function(){
			$('#xsellinfo').load(InitPhp+"action=xsellinfo&customerid="+CustomerId+"&campaignid="+CampaignId);
		});
    }
	/* Script **/
	var getScript = function(){
		$(function(){
			//$('#contact_office').load(InitPhp+"action=office_contact&customerid="+CustomerId+"&campaignid="+CampaignId);
		});
	}
	
/* action get Open call **/
	
    
	var getContactHistory = function(){
		$(function(){
			$('#contact_cust_history').load(InitPhp+"action=history_contact&customerid="+CustomerId+"&campaignid="+CampaignId);
		});
	}

/* action get Open call **/

	
	var getContactReason = function(){
		$(function(){
			$('#contact_reason_call').load(InitPhp+"action=reason_contact&customerid="+CustomerId+"&campaignid="+CampaignId+"&VerifiedStatus="+V_VERIFIED);
		});

	}
	
	var getDOB = function(){
		$(function(){
			$('#contact_reason_call').load(InitPhp+"action=reason_contact&customerid="+CustomerId+"&campaignid="+CampaignId);
		});

	}
	
 /* 
   @ get call last time periodik 
   @ update Date in 2012-12-13
   **/
	
	var getLastTime = function(){
		doJava.File = '../class/class.last.call.php';
		doJava.Params = { action:'get_last_call'}
		var error = doJava.Post(); 
			if( error ==1) 
				return true;
			else 
				return false;
	}
	
/* action get Open toolbar **/	
	
	
	var CreatePolicy = function(conds)
	{
		if( V_VERIFIED!='' )
		{
			phoneCall.initStatus = Ext.Cmp('call_result').getValue();
			
			if( conds && phoneCall.initStatus!='')
			{
				phoneCall.initCreate = true;
				Ext.Window
				({
					url 	: V_FORMEDIT,
					width 	: (Ext.DOM.window.screen.availWidth-200),
					height 	: Ext.DOM.window.screen.availHeight,
					name 	: 'windowPolicy',
					param   : {
						action		:'edit_policy',
						campaignid 	: CampaignId,
						customerid 	: CustomerId,
						verified	: V_VERIFIED,
						callstatus  : phoneCall.initStatus
					}
				}).popup();
			}
		}
		else{
			phoneCall.initStatus = Ext.Cmp('call_result').getValue();
			if( conds && phoneCall.initStatus!=''){
				phoneCall.initCreate = true;
				
				Ext.Window 
				({
					url 	: V_FORMPOLICY,
					name 	: 'windowPolicy',
					width 	: (Ext.DOM.window.screen.availWidth-200),
					height 	: Ext.DOM.window.screen.availHeight,
					param 	: {
						action		:'create_policy',
						campaignid 	: CampaignId,
						customerid 	: CustomerId,
						callstatus  : phoneCall.initStatus
					}
				}).popup();
			}
			else
			{ 
				var callStatus = doJava.checkedValue('call_status');
					//$(function(){
					//	$('#contact_reason_text').load(InitPhp+'action=call_reason_text&call_status='+callStatus);
					//})
					phoneCall.initStatus='';
					phoneCall.initCreate = false; 
			}
		}	
	}
	
/* action get Open toolbar **/	
	
	$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				//extTitle :[['Add Referal'],['Change Request'],['Help']],
				extTitle :[['(Phone) Additional Request'],['Add Referal']],
				//extMenu  :[['AddReferal'],['UserChangeList'],['']],
				extMenu  :[['validAddPhone'],['AddReferal']],
				extIcon  :[['monitor_edit.png'],['user_suit.png']],
				//extIcon  :[['user_suit.png'],['monitor_edit.png'],['help.png']],
				extText  :true
				/*extInput :true,
				extOption:[{
						render : 1,
						type   : 'combo',
						header : 'Script ',
						id     : 'v_result_script', 	
						name   : 'v_result_script',
						triger : 'showPdfList',
						store  : V_SCRIPT
					}]*/
	});
	
	var validAddPhone = function()
	{
		if(phoneCall.initChoose)
		{
			UserChangeList();
		}
		else{
			alert("Can\'t add phone before call first!");
			return false;
		}
	}
	
	var addGrid = function(){
		var ref_name  	= doJava.dom('Ref_Name').value;
		var ref_phone1  = doJava.dom('Ref_phone1').value;
		var ref_phone2  = doJava.dom('Ref_phone2').value;
		var ref_phone3  = doJava.dom('Ref_phone3').value;
		
		doJava.File = '../class/tpl.contact.detail.php';
		
		doJava.Params = {
			action  	: 'add_grid',
			ref_name 	: ref_name,
			ref_phone1 	: ref_phone1,
			ref_phone2	: ref_phone2,
			ref_phone3	: ref_phone3,
		}
		var tbl = doJava.Post();
		doJava.dom('content_grid').innerHTML = tbl;
	}
	
	var AddReferal = function(){
		if(phoneCall.initReferal){
			var xScreen = ($(window).width());
			var yScreen = ($(window).height());
			doJava.Params ={
				CampaignId 	: CampaignId,
				CustomerId 	: CustomerId,
			}
					
			doJava.winew.winconfig={
				location : 'frm.window.referal.php?'+doJava.ArrVal(),
				width : xScreen,
				height : yScreen,
				windowName : 'windowReferal',
				resizable : false, 
				menubar	: false, 
				scrollbars	: true, 
				status : false, 
				toolbar: false
			};
					
			doJava.winew.open();  
		}
		else{
			alert('Please call first before add referal !');
			return false;
		}		
	}
	
	var showPdfList = function(){
		var v_result_script = doJava.dom('v_result_script').value;
		
		//if( v_result_script!=''){
			var windowX = window.open('window.script.php?scriptid='+v_result_script,"myWindowPdf","height=600,width=800,menubar=no,status=no");
			windowX.close();
			windowX=window.open('window.script.php?scriptid='+v_result_script,"myWindowPdf","height=600,width=800,menubar=no,status=no");
		//}
	}
	
/*  set Call Number **/
	
	var setCallNumber = function(phoneNumber){
		if( phoneNumber!=''){
			phoneCall.initPhone = true;
			phoneCall.initNumber = phoneNumber;
		}
		else{
			phoneCall.initPhone = false;
			phoneCall.initNumber = '';
		}
	}
	
/* event trigger data ****/
	
	doJava.getEvent = function(code)
	{
		if( code )
		{
			this.File = '../class/class.call.result.php';
			this.Params = {
				action : 'event_result',
				reason_code : code
			}
			var message = doJava.eJson();
			return message;
		}
	}
	
	
/* action get Open call **/
	
	
	function getActionNext(triger_value)
	{
		var CallEvent= doJava.getEvent(triger_value);
		
		if( CallEvent.result)
		{	
			if( (parseInt(CallEvent.callLater)==1) && (parseInt(CallEvent.callSales)==0) )
			{
				doJava.dom('date_call_later').disabled =false;
				doJava.dom('hour_call_later').disabled =false;
				doJava.dom('minute_call_later').disabled =false;
				doJava.dom('create_policy').disabled=true;
				doJava.dom('create_policy').checked=false;
				phoneCall.initCreate=false;
			}
			else if( (parseInt(CallEvent.callLater)==0) && (parseInt(CallEvent.callSales)==1) )
			{
				doJava.dom('date_call_later').disabled =true;
				doJava.dom('hour_call_later').disabled =true;
				doJava.dom('minute_call_later').disabled =true
				doJava.dom('create_policy').disabled =false;
				phoneCall.initStatus = triger_value;
				phoneCall.initCreate=true;
				doJava.dom('call_primary_phone').disabled=true;
				doJava.dom('call_result').disabled=false;
				doJava.dom('create_policy').checked=false;
			}
			else if( (parseInt((CallEvent.callLater))==1) && (parseInt(CallEvent.callSales)==1) )
			{
				doJava.dom('date_call_later').disabled =false;
				doJava.dom('hour_call_later').disabled =false;
				doJava.dom('minute_call_later').disabled =false
				doJava.dom('create_policy').disabled =false;
				phoneCall.initStatus = triger_value;
				phoneCall.initCreate=true;
				doJava.dom('call_primary_phone').disabled=true;
				doJava.dom('call_result').disabled=false;
				doJava.dom('create_policy').checked=false;
			}
			else if( (parseInt(CallEvent.callLater)==0) && (parseInt(CallEvent.callSales)==0))
			{
				doJava.dom('call_primary_phone').disabled=false;
				doJava.dom('date_call_later').disabled =true;
				doJava.dom('hour_call_later').disabled =true;
				doJava.dom('minute_call_later').disabled =true;
				doJava.dom('create_policy').disabled=true;
				doJava.dom('create_policy').checked=false;
				phoneCall.initCreate=false;
			}
			else
			{
				doJava.dom('call_primary_phone').disabled=false;
				phoneCall.initCreate=false;
				doJava.dom('create_policy').checked=false;
				doJava.dom('create_policy').disabled=true;
				doJava.dom('call_result').disabled=false;
			}
		}
		else
		{
			doJava.dom('call_primary_phone').disabled=false;
			phoneCall.initCreate=false;
			doJava.dom('create_policy').checked=false;
			doJava.dom('create_policy').disabled=true;
			doJava.dom('call_result').disabled=false;
		}
	}
	


	/* action get Open call **/
	
	var hangupCustomer = function(){	
		phoneCall.callIsRun = false;
		if( phoneCall.initCall ){
			phoneCall.initCall = false;
			document.ctiapplet.callHangup()
		}
		else{
			alert("No call activity!");
		}
	}
	
	
	var callToNumber = function (PhoneNumber)
	{ 
		console.log(phoneCall.initSave);
		if( phoneCall.callIsRun == false){
			if(phoneCall.initSave==false )
		{
			if( document.ctiapplet.getAgentStatus()==AGENT_READY){
				console.log('call to '+ PhoneNumber +'is run . phoneCall.initSave=true');
				document.ctiapplet.callDialCustomer('', PhoneNumber,CustomerId,CustomerId);	
				phoneCall.initCall = true;
				phoneCall.initSave = true;
				phoneCall.callIsRun = true;
			}
			else{
				alert('Please set Ready!'); return;
			}
		}
		else{
			alert('Please Save Status!');
		}	
	  }
	  else{
		alert('Call is run.');
	  }
		
	}


/* Dial to customer */

	var dialCustomer=function(){
		doJava.dom('call_result').disabled= false;
		if( hiddenresult == 4){
							doJava.dom('date_call_later').disabled =false;
							doJava.dom('hour_call_later').disabled =false;
							doJava.dom('minute_call_later').disabled =false;
						}
		if(getLastTime()){	
			if(phoneCall.initPhone)
			{
				phoneCall.initChoose = true;
				phoneCall.initReferal = true;
				callToNumber(phoneCall.initNumber);
				setTimeout(function(){
					if( V_VERIFIED!='' )
					{
						doJava.dom('call_result').disabled= true;
					}else{
						doJava.dom('call_result').disabled= false;
					}
				},5000);
			}
			else{ alert('Please select the phone number!'); return false; }
	    }
		else{ alert("You enter the end time work today. Please contact your administrator..!"); return false; }
	}


	
	var isProudForm = function ()
	{
		doJava.File = '../class/class.save.activitycall.php';
			doJava.Params = {
				action:'isvalidPolicy',
				customerid : CustomerId
			}
		var error = doJava.Post();
		if( error ==1) return true;
		else return false;
	 }	 
	
/* valid Form **/
	
 var isCreateForm = function (){
	doJava.File = '../class/class.save.activitycall.php';
		doJava.Params = {
			action:'isvalidPolicy',
			customerid : CustomerId
		}
		
		phoneCall.initCreate 
		
		if( phoneCall.initCreate ){
			var error = doJava.Post();
			if( error ==1){
				return true;
			}
			else{
				return false;
			}
		}
		else{ 
			return false;
		}	
	}	 
 
	var getHirarkiCallStatus = function(CallReasonId)
	{	
		return true;
		
		if( CallReasonId!='')
		{
			doJava.File = '../class/class.save.activitycall.php';
			doJava.Params = {
				action:'get_hirarki_status',
				CustomerId:CustomerId,
				CallReasonId:CallReasonId
			}
			
			var response = doJava.eJson();

			if( response.success){ return true}
			else{
				return false;
			}
		}
		else
			return false;
	}
 /* function sava call  **/
 
	var saveCallAction = function()
	{
		var CallResult = doJava.dom('call_result').value;
		var CallRemarks = doJava.dom('call_remarks').value;
		//var CallRemarks2 = doJava.dom('call_remarks2').value;
		//var CallRemarks3 = doJava.dom('call_remarks3').value;
		//var CallRemarks4 = doJava.dom('call_remarks4').value;
		//var CallRemarks5 = doJava.dom('call_remarks5').value;
		var FormCreate = doJava.dom('create_policy').checked;
		var CallLaterDate = doJava.dom('date_call_later').value;
		var CallLaterHour = doJava.dom('hour_call_later').value;
		var CallLaterSec  = doJava.dom('minute_call_later').value;
		
		
			if( CallResult!='' )
			{
				if(V_VERIFIED != ''){
					doJava.File = '../class/class.save.activitycall.php';
					doJava.Params = {
						action:'save_activity_call',
						campaignid : CampaignId,
						customerid : CustomerId,
						callnumber : phoneCall.initNumber,
						callresult : CallResult,
						callremarks : CallRemarks,
						//callremarks2 : CallRemarks2,
						//callremarks3 : CallRemarks3,
						//callremarks4 : CallRemarks4,
						//callremarks5 : CallRemarks5,
						formCreate : FormCreate,
						calllaterdate : CallLaterDate,
						calllaterhour : CallLaterHour,
						calllatersec : CallLaterSec,
						verifiedStatus : V_VERIFIED
					}
					
					var error = doJava.Post();
					if( error ==1)
					{ 
						stop_database('stop_counter_endtime'); // save acw duration timer 	
						alert('Success saving call activity..');
						phoneCall.initSave = false;
						phoneCall.initReferal = false;
						phoneCall.NextDatas = true;
						getContactHistory();
						getContactReason();
					}
					else{ alert('Failed saving call activity..');}
				}
				else{
					if (!getHirarkiCallStatus(CallResult)) {
						alert("Can\'t change call status");
					}
					else
					{
						doJava.File = '../class/class.save.activitycall.php';
						doJava.Params = {
							action:'save_activity_call',
							campaignid : CampaignId,
							customerid : CustomerId,
							callnumber : phoneCall.initNumber,
							callresult : CallResult,
							callremarks : CallRemarks,
							//callremarks2 : CallRemarks2,
							//callremarks3 : CallRemarks3,
							//callremarks4 : CallRemarks4,
							//callremarks5 : CallRemarks5,
							formCreate : FormCreate,
							calllaterdate : CallLaterDate,
							calllaterhour : CallLaterHour,
							calllatersec : CallLaterSec,
							verifiedStatus : V_VERIFIED
						}
						
						var error = doJava.Post();
						if( error ==1)
						{ 
							stop_database('stop_counter_endtime'); // save acw duration timer 	
							alert('Success saving call activity..');
							phoneCall.initSave = false;
							phoneCall.initReferal = false;
							phoneCall.NextDatas = true;
							getContactHistory();
							getContactReason();
						}
						else{ alert('Failed saving call activity..');}
					}
				}
			}
			else{ alert('Call Result Can\'t be empty !'); return false; }
	}
	

/* cancel activity **/

	var CancelActivity = function(){
		if( phoneCall.initChoose){
			alert('Failed, please save the status!')
		}
		else
		{
			class_active.Active();
			if( V_VERIFIED!='' ){
				$('#main_content').load('src_customer_closing_nav.php');
			}
			else{
				//$('#main_content').load('src_customer_precall.php');
				$('#main_content').load('src_customer_bucket_nav.php');
			}
		}
	}
	
	
/* NextCustomers **/
var NextCustomers = function()
{
	if(phoneCall.NextDatas)
	{
		if(!phoneCall.initSave){
			
			new(function(){
				doJava.File = "../class/class.user.precall.php";
				doJava.Params = { 
					action:'get_start_call_data',
					CampaignId : CampaignId,
					CallReasonId : CallReasonId, 
					CustomerName : CustomerName,
					CustomerAddress : CustomerAddress
				}
					
				var Customers = doJava.eJson();
				if( Customers.valid )
				{
					class_active.NotActive(); 
					doJava.File = "dta_contact_detail.php";
					doJava.Params = { 
						action:'show_detail_data',
						CustomerId : Customers.CustomerId,
						CampaignId : Customers.CampaignId,
						CallReasonId :	Customers.CallReasonId,
						CustomerName : Customers.CustomerName,
						CustomerAddress : Customers.CustomerAddress
					}
					extendsJQuery.Content()
				}
				else{
					alert("No Customer(s) \nPlease Choose Other Campaign Or Other Call Status !");
					return false;
				}
			});
		}
		else{
			alert('Please Save Call Activity Before Next Customers !');
			return false;
		}
	}
	else{
		alert('Please FollowUp this Customer Before Next Customers !');
		return false;
	}	
}	
	
	
/* save activity **/
	
	var saveActivity = function()
	{
		var CallResult = parseInt(doJava.dom('call_result').value);
		if(CallResult==4){
			var date_call_later		= doJava.dom('date_call_later').value;
			var hour_call_later		= doJava.dom('hour_call_later').value;
			var minute_call_later	= doJava.dom('minute_call_later').value;
			
			if(date_call_later=="" || hour_call_later=="" || minute_call_later==""){
				alert("Informasi Call Again Harus diIsi!");
				return false;
			}
		}
		if( doJava.winew.winHwnd.closed==undefined){
			if(phoneCall.initCreate){
				if( isCreateForm()){ 
					saveCallAction(); 
					phoneCall.initChoose=false;
					return true;
					//CancelActivity();
				}
				else{ 
					alert('You have Not created the policy yet!'); 
					return false;
				}	
			}
			else
			{ 
				if( isProudForm())
				{
					alert('This Customer Already Have Policy,\n Please Select Interest Call Result!');
					return false;
				}
				else{
					saveCallAction(); 
					phoneCall.initChoose = false;
					//CancelActivity();
				}
			}		
				
		}
		else{
			if( !doJava.winew.winHwnd.closed){ 
				alert('Please close your Create Policy window..!'); return false;
			}
			else{ 
				if(phoneCall.initCreate)
				{
					if( isCreateForm())
					{ 
						saveCallAction(); 
						phoneCall.initChoose=false; 
						return
						//CancelActivity();
					}
					else{ 
						alert('You have Not created the policy yet!'); 
						return false;
					}
				}else{
					if( isProudForm() ){
						alert('Please Select Call Result 401 Or 402 !');
						return false;
					}
					else{
						saveCallAction(); 
						phoneCall.initChoose = false;
						//CancelActivity();
					}
				}	
			}
				
		}
	}
	
 /** 
	@ get phone Number Type to change 
	@ on privileges agent TM 
**/
 
	var getPhoneNumber = function(phoneType){
		if( phoneType!='')
		{
			doJava.File = "../class/tpl.contact.detail.php"
			doJava.Params = {
				action : 'get_phone_type',
				phone_type : phoneType,
				customerid : CustomerId
			}
			/*
			var error = doJava.Post();
			
			if( error!=''){
				doJava.dom('txt_old_value').value = error;	
			}
			else
				doJava.dom('txt_old_value').value = '';	
			*/
		}	
	}
	
	/* action get Open call **/
	
	var getCallReasontext = function(){ //callStatus, cond){
		$(function(){
			$('#contact_reason_text').load(InitPhp+'action=call_reason_text');//&call_status='+callStatus);
		})
	}
	
	var getFileRecording = function(){
		if( V_VERIFIED!='' )
		{	
			$(function(){
				$('#recording_file').load(InitPhp+'action=get_recording&customerid='+CustomerId );
			});
		}
	}
	
	var playRecording = function(filename){
		if( V_VERIFIED!='' )
		{
			$(function(){
				$('#recording_play').load(InitPhp+'action=play_recording&customerid='+CustomerId+'&rec_id='+filename );
			});
		}
	}
/* action get Open call **/
	
	$(function(){
		
		getDefaultContact();
		getHomeContact();
		getOfficeContact();
		getRemark();
		getXsell();		
		getContactHistory();
		getContactReason();
		getFileRecording();
		
	});
	
	
	
</script>

<div id="toolbars"></div>
<div class="contact_detail">
	<table width="100%" border=0>
		<tr>
			<td  width="80%" valign="top">
				<!--<div id="recording_file"></div>-->
				<!--<div id="recording_play"></div>-->
				<div id="contact_default_info" class="box-shadow box-left-top" style="margin-bottom:8px;"></div>
				<div class="box-shadow">
					<table width="99%" style="margin-bottom:8px;"  align="center">
						<tr>
							<td id="contact_home" width="50%" valign="top" style="background-color:#FFFFFF;"></td>
							<td id="contact_office" width="50%" valign="top" style="background-color:#FFFFFF;"></td>
						</tr>	
					</table>
				</div>
				<div id="xsellinfo" class="box-shadow"></div>
				<div id="remarks" class="box-shadow"></div>
				<div class="box-shadow">
					<table width="99%" align="center">
						<tr>
							<td id="contact_cust_history" width="50%"></td>
						</tr>	
					</table>
				</div>
			</td>
			<td  width="20%" rowspan="2" valign="top">
				<div class="box-shadow box-right-top" id="contact_reason_call"></div>
			</td>
		</tr>
	</table>
	<div id="addRef">
	<div id="change_request_dialog" >
</div>
