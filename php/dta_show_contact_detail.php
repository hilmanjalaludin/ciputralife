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
			left join tms_agent c on a.UploadBy=c.UserId ";
		
		$qry = $db->execute($sql,__FILE__,__LINE__);	
		while( $row = $db->fetchrow($qry) ){
			$datas[$row->ScriptId] = $row->ScriptUpload;
		}
		
		return "[".json_encode($datas)."]";	
	}
	
	
	function getFormLayout()
	{
		global $db;
		$sql = " SELECT a.FormLayout 
				 FROM t_gn_formlayout a 
				 LEFT JOIN t_gn_campaignproduct b on a.ProductId=b.ProductId
				 WHERE a.FormConds=1 
				 AND b.CampaignId='".$_REQUEST['campaignid']."' ";

		$qry = $db->execute($sql,__FILE__,__LINE__);	
		if( $qry && ($rows = @mysql_fetch_assoc($qry)))
		{
			return $rows['FormLayout'];
		}
	}
	

?>
<!--<script type="text/javascript" src="<?php //echo $app->basePath();?>/js/cti.js"></script>	-->

<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
<script>
	var V_SCRIPT	=<?php echo getScriptActive(); ?>;
	
 /* define  code call **/
	
	var V_INTWTHSPS  = 402;
	var V_INTWTHSLF  = 1;
	var V_CALLBACKL  = 310;
	var V_VERIFIED   = '<?php echo $_REQUEST['VerifiedStatus'];?>';
	var V_FORMPOLICY = '<?php echo getFormLayout(); ?>';

/* define info customers **/
	
	var CustomerId = '<?php echo $db->escPost('customerid'); ?>';
	var CampaignId = '<?php echo $db->escPost('campaignid'); ?>';
	var InitPhp	   = '../class/tpl.contact.detail.php?';
	var phoneCall  = {
						initPhone  :  false,
						initType   : '', 
						initNumber : '',
						InitLater  : false,
						initCreate : false,
						initCall   : false,
						initStatus : 0,
						initChoose : false
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
	
/* on top content **/
	
	var getDefaultContact = function(){
		$(function(){
			$('#contact_default_info').load(InitPhp+"action=default_contact&CustomerId="+CustomerId+"&CampaignId="+CampaignId);
		});
	}
	
 /* Home **/
 
	var getHomeContact = function(){
		$(function(){
			$('#contact_home').load(InitPhp+"action=home_contact&CustomerId="+CustomerId+"&CampaignId="+CampaignId);
		});
	}
	
/* Office **/
	
	var getOfficeContact = function(){
		$(function(){
			$('#contact_office').load(InitPhp+"action=office_contact&CustomerId="+CustomerId+"&CampaignId="+CampaignId);
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
			$('#contact_cust_history').load(InitPhp+"action=history_contact&CustomerId="+CustomerId+"&CampaignId="+CampaignId);
		});
	}

/* action get Open call **/

	
	var getContactReason = function(){
		$(function(){
			$('#contact_reason_call').load(InitPhp+"action=reason_contact&CustomerId="+CustomerId+"&CampaignId="+CampaignId+"&VerifiedStatus="+V_VERIFIED);
		});

	}
	
	var getDOB = function(){
		$(function(){
			$('#contact_reason_call').load(InitPhp+"action=reason_contact&CustomerId="+CustomerId+"&CampaignId="+CampaignId);
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
	
	
	var CreatePolicy = function(conds){
		
		if( conds && phoneCall.initStatus!=''){
			phoneCall.initCreate = true;
			var xScreen = 1024;
			var yScreen = 968;
			doJava.Params ={
				action		:'create_policy',
				campaignid 	: CampaignId,
				customerid 	: CustomerId,
				callstatus  : phoneCall.initStatus
			}
			
		
			doJava.winew.winconfig={
					location	: V_FORMPOLICY+'?'+doJava.ArrVal(),
					width		: xScreen,
					height		: yScreen,
					windowName	: 'windowName',
					resizable	: false, 
					menubar		: false, 
					scrollbars	: true, 
					status		: false, 
					toolbar		: false
			};
			
			doJava.winew.open();  
		}
		else{ 
			var callStatus = doJava.checkedValue('call_status');
				$(function(){
					$('#contact_reason_text').load(InitPhp+'action=call_reason_text&call_status='+callStatus);
				})
				phoneCall.initStatus='';
				phoneCall.initCreate = false; 
		}
	}
	
/* action get Open toolbar **/	
	
	$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Exit']],
				extMenu  :[['Exit']],
				extIcon  :[['arrow_left.png']],
				extText  :true,
				extInput :true,
				extOption:[{
						render : 6,
						type   : 'combo',
						header : 'Script ',
						id     : 'v_result_script', 	
						name   : 'v_result_script',
						triger : 'showPdfList',
						store  : V_SCRIPT
					}]
	});
	
	var showPdfList = function(){
		var v_result_script = doJava.dom('v_result_script').value;
		
		if( v_result_script!=''){
			var windowX = window.open('window.script.php?scriptid='+v_result_script,"myWindowPdf","height=600,width=800,menubar=no,status=no");
			windowX.close();
			windowX=window.open('window.script.php?scriptid='+v_result_script,"myWindowPdf","height=600,width=800,menubar=no,status=no");
		}
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
	
	function getEvent(code){
		if( code )
		{
			doJava.File = '../class/class.call.result.php';
			doJava.Params = {
				action : 'event_result',
				reason_code : code
			}
			var message = doJava.eJson();
			if( message.result==1 ) return true;
			else{
				return false;
			}
		}
	}
	
	function getCallLater(code){
		if( code )
		{
			doJava.File = '../class/class.call.result.php';
			doJava.Params = {
				action : 'event_callback',
				reason_code : code
			}
			var message = doJava.eJson();
			if( message.result==1 ) return true;
			else{
				return false;
			}
		}
	}
	
/* action get Open call **/
	
	
	function getActionNext(triger_value)
	{
		if( getCallLater(triger_value) ){
			doJava.dom('date_call_later').disabled =false;
			doJava.dom('hour_call_later').disabled =false;
			doJava.dom('minute_call_later').disabled =false;
			doJava.dom('create_policy').disabled=true;
			doJava.dom('create_policy').checked=false;
			phoneCall.initCreate=false;
		}
		else{
			doJava.dom('date_call_later').disabled =true;
			doJava.dom('hour_call_later').disabled =true;
			doJava.dom('minute_call_later').disabled =true;
			
			if( getEvent(triger_value) ){
				doJava.dom('create_policy').disabled =false;
				phoneCall.initStatus = triger_value;
				phoneCall.initCreate=true;
				//doJava.dom('call_status_contacted').disabled=true;
				//doJava.dom('call_status_nocontacted').disabled=true;
				//doJava.dom('call_primary_phone').disabled=true;
				doJava.dom('call_result').disabled=false;
				doJava.dom('create_policy').checked=false;
				
			}
			else{
				//doJava.dom('call_primary_phone').disabled=false;
				phoneCall.initCreate=false;
				//doJava.dom('call_status_contacted').disabled=false;
				//doJava.dom('call_status_nocontacted').disabled=false;
				doJava.dom('create_policy').checked=false;
				doJava.dom('create_policy').disabled=true;
				doJava.dom('call_result').disabled=true;
			}
		}
	}
	
/* action get Open call **/
	
	var getCallReasontext = function(){ //callStatus, cond){
		//if( cond ){
			$(function(){
				//doJava.dom('create_policy').checked=false;
				//doJava.dom('create_policy').disabled=true;
				$('#contact_reason_text').load(InitPhp+'action=call_reason_text');//&call_status='+callStatus);
			})
		//}
	}

	/* action get Open call **/
	
	var hangupCustomer = function(){	
		if( phoneCall.initCall ){
			phoneCall.initCall = false;
			document.ctiapplet.callHangup()
		}
		else{
			alert("No call activity!");
		}
	}
	
	
	var callToNumber = function (PhoneNumber){ 	
		document.ctiapplet.callDialCustomer('', PhoneNumber,CustomerId,CustomerId);	
		phoneCall.initCall = true;
	}


/* Dial to customer */

	var dialCustomer=function(){
		if(getLastTime()){	
			if(phoneCall.initPhone)
			{
				phoneCall.initChoose = true;
				callToNumber(phoneCall.initNumber);
				setTimeout(function(){
					var call_contact = document.getElementsByName('call_status');
						for(var i = 0; i< call_contact.length; i++)
						{
							call_contact[i].disabled = false;
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
			if( error ==1) return true;
			else return false;
		}
		else{ 
			return false;
		}	
 }	 
	
 /* function sava call  **/
 
	var saveCallAction = function()
	{
		var CallResult = doJava.dom('call_result').value;
		var CallRemarks = doJava.dom('call_remarks').value;
		var FormCreate = doJava.dom('create_policy').checked;
		var CallLaterDate = doJava.dom('date_call_later').value;
		var CallLaterHour = doJava.dom('hour_call_later').value;
		var CallLaterSec  = doJava.dom('minute_call_later').value;
		
			doJava.File = '../class/class.save.activitycall.php';
			doJava.Params = {
				action:'save_activity_call',
				campaignid : CampaignId,
				customerid : CustomerId,
				callnumber : phoneCall.initNumber,
				callresult : CallResult,
				callremarks : CallRemarks,
				formCreate : FormCreate,
				calllaterdate : CallLaterDate,
				calllaterhour : CallLaterHour,
				calllatersec : CallLaterSec,
				verifiedStatus : V_VERIFIED
			}
		if( CallResult!='' )
		{		
			var error = doJava.Post();
			  if( error ==1){ 
				 alert('Success saving call activity..');
				 getContactHistory();
				 getContactReason();
			  }
			  else{ alert('Failed saving call activity..');}
		}
		else{
			alert('Call Result Can\'t be empty !'); return false;
		}	
	}
	
	var Exit = function(){
			class_active.Active();
			$('#main_content').load('src_customer_bucket_nav.php');
	}
/* cancel activity **/

	var CancelActivity = function(){
		if( phoneCall.initChoose){
			alert('Failed, please save the status!')
		}
		else
		{
			class_active.Active();
			$('#main_content').load('src_customer_show_nav.php');
		}
	}
	
	
	
/* save activity **/
	
	var saveActivity = function(){
		alert(CallResult);
		var CallResult = parseInt(doJava.dom('call_result').value);
		
		/* 
			check apakah ada window create polish ?
			jika NO exec Ini...	
		**/
		if(CallResult==4){
			var date_call_later		= doJava.dom('date_call_later').value;
			var hour_call_later		= doJava.dom('hour_call_later').value;
			var minute_call_later	= doJava.dom('minute_call_later').value;
			
			if(date_call_later=="" && hour_call_later=="" && minute_call_later==""){
				alert("Informasi Call Again Harus diIsi!");
				return false;
			}
		}
		 if( doJava.winew.winHwnd.closed==undefined){
			//alert(phoneCall.initCreate);
			if(phoneCall.initCreate)
			{
				if( isCreateForm()){ 
					saveCallAction(); 
					phoneCall.initChoose=false; 
					return true;
				}
				else{ 
					alert('You have Not created the policy yet!'); 
					return false;
				}	
			}
			else{ 
				//alert(isProudForm());
				if( isProudForm())
				{
					alert('Please Select Call Result !');
					return false;
				}
				else{
					saveCallAction(); 
					phoneCall.initChoose = false;
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
			
			var error = doJava.Post();
			if( error!=''){
				doJava.dom('txt_old_value').value = error;	
			}
			else
				doJava.dom('txt_old_value').value = '';	
		}	
	}
	
/* action get Open call **/
	
	$(function(){
		getDefaultContact();
		getHomeContact();
		getOfficeContact();		
		getContactHistory();
		getContactReason();
	});
	
</script>

<div id="toolbars"></div>
<div class="contact_detail">
	<table width="100%" border=0>
		<tr>
			<td  width="80%" valign="top">
				<div id="contact_default_info" class="box-shadow box-left-top" style="margin-bottom:8px;"></div>
				<div class="box-shadow">
					<table width="99%" style="margin-bottom:8px;"  align="center">
						<tr>
							<td id="contact_home" width="50%" valign="top" style="background-color:#FFFFFF;"></td>
							<td id="contact_office" width="50%" valign="top" style="background-color:#FFFFFF;"></td>
						</tr>	
					</table>
				</div>
				<div class="box-shadow">
					<table width="99%" align="center">
						<tr>
							<td id="contact_cust_history" width="50%"></td>
						</tr>	
					</table>
				</div>
			</td>
			<!--<td  width="40%" rowspan="2" valign="top">
				<div class="box-shadow box-right-top" id="contact_reason_call"></div>
			</td>-->
		</tr>
	</table>
	<div id="change_request_dialog" >
</div>
