<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	
	class spvActivity extends mysql{
	


	/** 
		get Script Guide for user interface 
	 **/
	 
		function getScriptActive(){
			$sql = " select 
						a.ScriptId,
						a.ScriptUpload
				from t_gn_productscript a
				left join t_gn_product b on a.ProductId=b.ProductId
				left join tms_agent c on a.UploadBy=c.UserId ";
			
			$qry = $this->execute($sql,__FILE__,__LINE__);	
			while( $row = $this->fetchrow($qry) ){
				$datas[$row->ScriptId] = $row->ScriptUpload;
			}
			
			return "[".json_encode($datas)."]";	
		}
		
	  /** 
		@  get Call result Code bypass from Custid '
	  
	  **/
	 
		function getCallResultCode(){
			$sql = "SELECT  d.CallReasonCode
					FROM t_gn_customer a INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId 
						LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId 
						LEFT join t_lk_callreason d on a.CallReasonId =d.CallReasonId
						left join tms_agent e on a.SellerId=e.UserId
					WHERE a.CustomerId='".$this->escPost('CustomerId')."'";
					
				
			$codec = $this -> fetchval($sql,__FILE__,__LINE__);
			if( $codec ) : return $codec;
			else : return null;
			endif;
		}
	}

	/** create object **/
		
		$spvActivity = new spvActivity(true); 
	

?>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
<script>
	
 /* define  code call **/
 
	var V_SCRIPT	= <?php echo $spvActivity -> getScriptActive();?>;
	var V_INIT_CODE ='<?php echo $spvActivity -> getCallResultCode(); ?>';
	
/* define info customers **/
	
	var CustomerId = '<?php echo $db->escPost('CustomerId');?>';
	var CampaignId = '<?php echo $db->escPost('CampaignId');?>';
	var InitPhp	   = '../class/tpl.contact.spv.php?';
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
	
	document.oncontextmenu=new Function("return false");
	window.onkeypress=function(e){
		var winEvent = e; 
		if( winEvent.keyCode==116 )
		{
			return false;	
		}
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
	

/* action get Open call **/
	
	var getContactHistory = function(){
		$(function(){
			$('#contact_cust_history').load(InitPhp+"action=history_contact&customerid="+CustomerId+"&campaignid="+CampaignId);
		});
	}

/* action get Open call **/

	
	var getContactReason = function(){
		$(function(){
			$('#contact_reason_call').load(InitPhp+"action=reason_contact&customerid="+CustomerId+"&campaignid="+CampaignId);
		});

	}
	
/* action get Open toolbar **/	
	
	
	var CreatePolicy = function(conds){
		if( conds ){
			phoneCall.initCreate = true;
			var xScreen = ($(window).width());
			var yScreen = ($(window).height());
			doJava.Params ={
				action		:'create_policy',
				campaignid 	: CampaignId,
				customerid 	: CustomerId,
				callstatus  : phoneCall.initStatus
			}
			
			doJava.winew.winconfig={
					location	: 'frm.edit.policy.php?'+doJava.ArrVal(),
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
		else{ phoneCall.initCreate = false; }
	}
	
/* action get Open toolbar **/	
	
	$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Change Request'],['Help']],
				extMenu  :[['UserChangeList'],['']],
				extIcon  :[['monitor_edit.png'],['help.png']],
				extText  :true,
				extInput :true,
				extOption:[{
						render : 1,
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
		
			doJava.winew.winconfig={
					location	: 'window.script.php?scriptid='+v_result_script,
					width		: 800,
					height		: 600,
					windowName	: 'windowName1',
					resizable	: false, 
					menubar		: false, 
					scrollbars	: true, 
					status		: false, 
					toolbar		: false
			};
			doJava.winew.open();  
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
	
	
/* action get Open call **/
	
	var getCallReasontext = function(callStatus, cond){
		if( cond ){
			if( callStatus!=3){
				doJava.dom('edit_policy').disabled=false;
				doJava.dom('edit_policy').checked=false;
				doJava.dom('date_call_later').value='';
				doJava.dom('hour_call_later').value='';
				doJava.dom('minute_call_later').value='';
				doJava.dom('date_call_later').disabled=true;
				doJava.dom('hour_call_later').disabled=true;
				doJava.dom('minute_call_later').disabled=true;
			}
			else{
				doJava.dom('date_call_later').disabled=false;
				doJava.dom('hour_call_later').disabled=false;
				doJava.dom('minute_call_later').disabled=false;
				doJava.dom('edit_policy').checked=false;
				doJava.dom('edit_policy').disabled=true;
				
			}
		}
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
	
	
	var callToNumber = function (PhoneNumber) { 	
		document.ctiapplet.callDialCustomer('', PhoneNumber,CustomerId,CustomerId);	
		phoneCall.initCall = true;
	}

/* action get Open call **/
	
	var dialCustomer=function(){
		if( phoneCall.initPhone )
		{
			phoneCall.initChoose = true;
			callToNumber(phoneCall.initNumber);
			setTimeout(function(){
				var call_contact = document.getElementsByName('call_status');
					for(var i = 0; i< call_contact.length; i++)
					{
						call_contact[i].disabled = false;
					}				
				
			},1000);
		}
		else {
			alert('Please select the phone number!');
		}
	}

	
	var isProudForm = function (){
		doJava.File = '../class/class.save.spvactivity.php';
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
	doJava.File = '../class/class.save.spvactivity.php';
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
 
	var saveYesValid = function(CallResult){
	
		var CallRemarks = doJava.dom('call_remarks').value;
			doJava.File = '../class/class.save.spvactivity.php';
			doJava.Params = {
				action:'save_confirm_valid',
				campaignid : CampaignId,
				callnumber : phoneCall.initNumber,
				customerid : CustomerId,
				callresult : CallResult,
				callremarks: CallRemarks,
				codec : V_INIT_CODE
			}
			
			if( CallRemarks	!='')
			{
				var error = doJava.Post();
				if( error ==1){ 
					alert('Success saving call activity..');
					getContactHistory();
				}
				else{ alert('Failed saving call activity..');}
			}
			else{
				alert('Notes Can Not Be Empty!');
				return false;
			}	
	}
	
	var saveNoValid = function(CallResult){
	
		var CallRemarks = doJava.dom('call_remarks').value;
		var CallLaterDate = doJava.dom('date_call_later').value;
		var CallLaterHour = doJava.dom('hour_call_later').value;
		var CallLaterSec  = doJava.dom('minute_call_later').value;
		
			doJava.File = '../class/class.save.spvactivity.php';
			doJava.Params = {
				action:'save_confirm_novalid',
				campaignid : CampaignId,
				customerid : CustomerId,
				callnumber : phoneCall.initNumber,
				callresult : CallResult,
				callremarks : CallRemarks
			}
				
			var error = doJava.Post();
			  if( error ==1){ 
				 alert('Success, saving call activity..');
				 getContactHistory();
			  }
			  else{ alert('Failed, saving call activity..');}
	}
	
	
	var saveAppoinment = function(CallResult){
		var CallRemarks = doJava.dom('call_remarks').value;
		var CallLaterDate = doJava.dom('date_call_later').value;
		var CallLaterHour = doJava.dom('hour_call_later').value;
		var CallLaterSec  = doJava.dom('minute_call_later').value;
		
			doJava.File = '../class/class.save.spvactivity.php';
			doJava.Params = {
				action:'save_confirm_callback',
				campaignid : CampaignId,
				customerid : CustomerId,
				callnumber : phoneCall.initNumber,
				callresult : CallResult,
				callremarks : CallRemarks,
				calllaterdate : CallLaterDate,
				calllaterhour : CallLaterHour,
				calllatersec : CallLaterSec
			}
				
			var error = doJava.Post();
			  if( error ==1){ 
				 alert('Success, saving call activity..');
				 getContactHistory();
			  }
			  else{ alert('Failed , saving call activity..');}
	}
	
	/* function history sava call  **/
 
	var HistoryCall = function(){
		var CallResult = doJava.dom('call_result').value;
		var CallRemarks = doJava.dom('call_remarks').value;
		var FormCreate = doJava.dom('create_policy').checked;
		var CallLaterDate = doJava.dom('date_call_later').value;
		var CallLaterHour = doJava.dom('hour_call_later').value;
		var CallLaterSec  = doJava.dom('minute_call_later').value;
		
			doJava.File = '../class/class.save.spvactivity.php';
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
				calllatersec : CallLaterSec
			}
				
			var error = doJava.Post();
			  if( error ==1){ 
				 alert('Success saving call activity..');
				 getContactHistory();
				 getContactReason();
			  }
			  else{ alert('Failed saving call activity..');}
	}
	
/* cancel activity **/

	var CancelActivity = function(){
		$('#main_content').load('dta_spv_nav.php');
	}
	
/* save activity **/
	
	var saveActivity = function(){
	
	var CallResult = parseInt(doJava.checkedValue('call_status'));
		switch(CallResult){
			case 1 : saveYesValid(CallResult);   break;
			case 2 : saveNoValid(CallResult);   break;
			case 3 : saveAppoinment(CallResult); break;
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
			<td  width="60%" valign="top">
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
			<td  width="40%" rowspan="2" valign="top">
				<div class="box-shadow box-right-top" id="contact_reason_call"></div>
				
			</td>
		</tr>
	</table>
	<div id="change_request_dialog" >
</div>