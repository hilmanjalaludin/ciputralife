<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.application.php");
	require("../class/class.main.menu.php");
	require("../class/class.cti.php");
	require('../sisipan/parameters.php');
	
	
	
	SetNoCache();
	
	$username     	= $db -> getSession("username");
	$user_profile 	= $db -> getSession("user_profile");
	$user_group   	= $db -> getSession("user_group");
	$user_menu    	= $db -> getSession("menu");
	$login_date   	= $db -> getSession("login_date");
	$pass		  	= $db -> getSession("pass");
	$handling_type  = $db -> getSession("handling_type");
	
	
	function getAgentScript(){
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
	
		
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	
	<meta name="author" content="<?php echo $Themes->V_WEB_AUTHOR; ?>" />
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta content="utf-8" http-equiv="encoding">
	
    <meta http-equiv="Content-Style-Type" content="text/css">
    <meta http-equiv="Content-Script-Type" content="text/javascript">
    <title><?php echo $Themes->V_WEB_TITLE; ?></title>
 
 <!-- start Link : css --> 
	
	<link type="text/css" rel="stylesheet" href="<?php echo $app->basePath();?>gaya/gaya_utama.css" />
    <link type="text/css" rel="stylesheet" href="<?php echo $app->basePath();?>gaya/other.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo $app->basePath();?>pustaka/jquery/jquery-ui-themes-1.7.2/themes/<?php echo $Themes->V_UI_THEMES;?>/ui.all.css" />	
	<link type="text/css" rel="stylesheet" media="all" href="<?php echo $app->basePath();?>gaya/chat.css" />
    <link type="text/css" rel="stylesheet" media="all" href="<?php echo $app->basePath();?>gaya/screen.css" />
	<link type="text/css" rel="stylesheet" href="<?php echo $app->basePath();?>gaya/custom.css" />
 
 <!-- stop Link : css -->
	
 <!-- start Link : Javascript -->
	
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/date_format.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/jquery-1.3.2.js?time=<?php echo time();?>"></script>    
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/jquery-ui-1.7.2/ui/jquery-ui.js?time=<?php echo time();?>"></script>
    <script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/jquery.form.js"></script>
    <script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/jquery-ui-1.7.2/external/bgiframe/jquery.bgiframe.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/jquery.slidingmessage.min.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/myPlugin.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/jqueryRounded.js?time=<?php echo time();?>"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/jquery.purr.js?time=<?php echo time();?>"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/EUI_1.0.2.js?time=<?php echo time();?>"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/init.js?time=<?php echo time();?>"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/CallCounter.js?time=<?php echo time();?>"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/chat.js?time=<?php echo time();?>"></script>
    <script type="text/javascript" src="<?php echo $app->basePath();?>js/jquery.media.js?time=<?php echo time();?>"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js?time=<?php echo time();?>"></script>
	
	
 <!-- stop Link : Javascript -->   
	
   
<!-- CTI SETUP -->     
	<script language="JavaScript" type="text/javascript" src="<?php echo $app->basePath();?>js/cti/centerback.js"></script> 
	<script language="JavaScript">
<!--
	var VSCRIPT	=<?php echo getAgentScript(); ?>;
	var destNo;
	var callPasswd;
	var callTAC;
	var isMSIE = (navigator.appName=="Microsoft Internet Explorer");
	var USER_SYSTEM_LEVEL  = '<?php echo $_SESSION['handling_type'];?>';
	var promptDestWin=undefined;
	
	
	function prepareCTIClient(){
		if (document.ctiapplet.getAgentStatus()==AGENT_NULL){
			document.ctiapplet.setAgentSkill(1);
			document.ctiapplet.ctiConnect();
			document.ctiapplet.setAgentEventHandler('OnAgentEventHandler');
			document.ctiapplet.setCallEventHandler ('OnCallEventHandler');			
			document.ctiapplet.setOtherMediaEventHandler('OnOtherMediaEventHandler');
		}
		
		if(isMSIE)
			document.ctiapplet.style.display = 'none';
	}
	
	function dialOut(destNo){	
		callTAC = "<?php echo $db->getSession('pbxTAC'); ?>";
		if (destNo.indexOf(callTAC) == 0)		
			destNo = destNo.substring(callTAC.length);
			document.ctiapplet.callDial(callTAC, destNo, "1234");
	}

	
	function timeStamp(){
		var now = new Date();		
		var stamp = now.getFullYear()+'/'+(now.getMonth()+1)+'/'+now.getDate()+
		            ' '+ now.getHours()+':'+now.getMinutes()+':'+now.getSeconds();
		return stamp;
	}	
	
	function disableAgentButton(val){
		document.getElementById("idFrmAgent").btnReady.disabled = (val & CBTNREADY);
		document.frmAgent.btnReady.disabled 	= (val & CBTNREADY);
		document.frmAgent.btnAUX.disabled 		= (val & CBTNAUX);		
	}
	
	function disableCallButton(val){		
		callBtnState |= val;
		document.frmAgent.btnHold.disabled 		= (callBtnState & CBTNHOLD);		
		document.frmAgent.btnHangup.disabled 	= (callBtnState & CBTNHANGUP);		
	}
	
	function enableCallButton(val){		
		callBtnState &= ~val;				
		if(!onCall)
			document.frmAgent.btnDial.disabled 	= (callBtnState & CBTNDIAL);		
		document.frmAgent.btnHold.disabled 		= (callBtnState & CBTNHOLD);		
		document.frmAgent.btnHangup.disabled 	= (callBtnState & CBTNHANGUP);		
	}
	
	function disableAllButton(){
		disableCallButton(CBTNALL);
	}	
	
	function OnAgentEventHandler(agentstatus){
		switch(agentstatus){
			case AGENT_LOGIN:
				document.getElementById("AgentStatus").innerHTML = '" Login';
				disableAgentButton(0);
				disableCallButton(CBTNDIAL);
			break;
				
			case AGENT_READY:
				document.getElementById("AgentStatus").innerHTML = '" Ready';
				disableAgentButton(CBTNREADY);
				disableCallButton(CBTNDIAL);
			break;
				
			case AGENT_NOTREADY:	
				var selAuxreason = document.getElementById('auxReason');
				if( selAuxreason.options[selAuxreason.selectedIndex].text!=''){
					if( selAuxreason.options[selAuxreason.selectedIndex].value!=''){
						document.getElementById("AgentStatus").innerHTML = ' Not Ready [ '+selAuxreason.options[selAuxreason.selectedIndex].text+' ]';
					}
					else{
						document.getElementById("AgentStatus").innerHTML = ' Not Ready ';
					}
				}	
				else
					document.getElementById("AgentStatus").innerHTML = '" Not Ready ';
					
				disableAgentButton(CBTNAUX);
				disableCallButton(CBTNDIAL);
			break;
				
			case AGENT_ACW:				
				document.getElementById("AgentStatus").innerHTML = '" Acw ';				
				disableAgentButton(CBTNACW);
				disableCallButton(CBTNDIAL);
			break;
				
			case AGENT_OUTBOUND:
				disableAgentButton(CBTNOUTBOUND);
				enableCallButton(CBTNDIAL);
			break;
				
			case AGENT_PREDICTIVE:
				//disableAgentButton(CBTNPREDICTIVE);
			break;
			
			case AGENT_BUSY:				
					document.getElementById("AgentStatus").innerHTML = '" Busy';
			break;
			
			default:				
				if(warned==0){
						alert('Login Telephony anda ditolak karena kemungkinanan anda sudah login ditempat lain\n'+
							  'atau sudah ada yang login di PC ini');
					  warned=1;
					  document.getElementById("AgentStatus").innerHTML = '" Reject';
				}
			  break;
		}
	}
	
	function OnCallEventHandler(callstatus){	
		CounterFunction(USER_SYSTEM_LEVEL,callstatus);
		switch(callstatus){
			case CALLSTATUS_IDLE:
				onHold = false;
				onCall = false;
				disableCallButton(CBTNHANGUP|CBTNHOLD|CBTNTRANSFER|CBTNCONFERENCE);
				//stoped_timer(); // add stop_timer counter ( omens )
				document.getElementById("idCallStatus").innerHTML = "Idle";
				btns = document.getElementsByName("btnHold");
				btns[0].innerHTML = "Hold";
			break;
				
			case CALLSTATUS_ALERTING:
				onCall = true;
				enableCallButton(CBTNHANGUP);
				var direction = document.ctiapplet.getCallDirection();
				if(direction == 1){	
					document.getElementById("idCallStatus").innerHTML = "Call from "+document.ctiapplet.getCallerId();
				}
			break;
			
			case CALLSTATUS_ANSWERED:
				alert(callstatus)
				onCall = true;					
				enableCallButton(CBTNHANGUP);
				document.getElementById("idCallStatus").innerHTML = " Call to "; //+document.ctiapplet.getCallerId()+ " connected";
			break;
				
			case CALLSTATUS_SERVICEINITIATED:
				onCall = true;
				document.getElementById("idCallStatus").innerHTML = "Phone offhook";
			break;
			
			case CALLSTATUS_ORIGINATING:
				onCall = true;
				document.getElementById("idCallStatus").innerHTML = "Call to - "+doJava.setMasking(phoneCall.initNumber)+" ";
				enableCallButton(CBTNHANGUP);
			break;
			
			case CALLSTATUS_CONNECTED:
				onCall = true;
				onHold = false;
				enableCallButton(CBTNHANGUP|CBTNHOLD|CBTNTRANSFER|CBTNCONFERENCE);
				if (document.ctiapplet.getCallDirection() == 1)
					document.getElementById("idCallStatus").innerHTML = "Call from "+doJava.setMasking(phoneCall.initNumber)+ " connected";
				else
					document.getElementById("idCallStatus").innerHTML = " Connected to - "+ doJava.setMasking(phoneCall.initNumber) + "";
					//start_timer(); // start_time
				btns = document.getElementsByName("btnHold");
				btns[0].innerHTML = "Hold";
			break;
			
			case CALLSTATUS_HELD:
				onHold = true;
				document.getElementById("idCallStatus").innerHTML = "Call with "+doJava.setMasking(phoneCall.initNumber)+ " on hold";
				btns = document.getElementsByName("btnHold");
				btns[0].innerHTML = "Reconnect";
			break;
		}
	}
	
	function OnOtherMediaEventHandler(media, eventid){
		switch(media){
			case EMAIL_MEDIA:
				if (parent.frames[0] != 'undefined'){  		
					parent.frames[0].cti_notification(1, document.ctiapplet.getMediaId());
				}		  	
			break;
		}
	}
	
	function onButtonHoldClick(){
		if (onHold){
			document.ctiapplet.callRetrieve();
		}else{
			document.ctiapplet.callHold();
		}
	}
	
	function setLabelStatus(v_status_agent){
		if( v_status_agent!='')
		{
		
			var selAuxreason = document.getElementById('auxReason');
				document.getElementById('idCallStatus').innerHTML = selAuxreason.options[v_status_agent].text
				if( selAuxreason.options[v_status_agent].value!=''){
					document.ctiapplet.agentSetNotReady(v_status_agent);
				}
				else{
					document.ctiapplet.agentSetNotReady(0);
				}
			return false;
		}
		else
			return;
	}
	
	var ShowScriptList = function(){
			var openscript = doJava.dom('openscript').value;
			//if( openscript!=''){
				//var windowX = window.open('window.script.php?scriptid='+openscript,"myWindowPdf","height=900,width=850,menubar=no,status=no");
				//windowX.close();
				windowX=window.open('window.script.php?scriptid='+openscript,"_blank","height=900,width=850,menubar=no,status=no");
			//}
	}
	
	function setLabelReady(){
		document.getElementById('idCallStatus').innerHTML="Idle"
		document.ctiapplet.agentSetReady();
		return false;
		
	}
	
	//set ticket number for current or last call session
	function ctiSetTicketNumber(n){
		document.ctiapplet.setAssignmentId(n);
	}


-->
</script>	
<script type="text/javascript">
	var USER_SYSTEM_LEVEL  = '<?php echo $_SESSION['handling_type'];?>';
	var USER_LEVEL_ADMIN   = 1; 
	var USER_LEVEL_MANAGER = 2;
	var USER_LEVEL_SPV 	   = 3;
	var USER_LEVEL_AGENT   = 4;
	var USER_LEVEL_QUALITY = 5;
	var USER_LEVEL_QUALITY_R = 10;
	var USER_LEVEL_ROOT = 9;
	
	var V_STATUS_STORE 		= <?php echo $MainMenu -> getAuxReason(); ?>;
	var EXT_TITLE 	   		= [];
	var EXT_MENU 	   		= [];
	var EXT_ICON 	   		= [];
	var EXT_OPTION     		= [];
		
	
	// var class_active = new ActiveWarning([
										// {id:'src_menu', name:'src_customer_show_nav.php'},
										// {id:'bucket', name:'src_customer_bucket_nav.php'},
										// {id:'cust_closing', name:'src_customer_closing_nav.php'},
										// {id:'app_menu', name:'src_appoinment_nav.php'}
										// ]);
										
	// var class_active = new ActiveWarning([
										// {id:'bucket', name:'src_customer_bucket_nav.php'},
										// {id:'cust_closing', name:'src_customer_closing_nav.php'},
										// {id:'app_menu', name:'src_appoinment_nav.php'}
										// ]);
	
	var class_active = new ActiveWarning(function(){
		var v = [];
		var arr = <?php echo $MainMenu->getAllMenu();?>;
		if( typeof(arr)=='object'){
			return arr;
		}
		else{
			return null;
		}
	}());
	
	$(window).bind('beforeunload', function(e) { 
		doJava.File = "sys_logout.php"
		doJava.Param ={ action:'logout'}
		if(confirm("Do You want to Close from this session")){
			doJava.Post(); return true;
		}
		else
			return false;
	});

	$(document).ready(function(){
	
		function createLayout(){					   
			var h = $(window).height();
			var m = $(window).height();
			var left_width = $('#accordion').width();
			var w = $(window).width();
			var chatHeight = $(window).height();
			
			   h = h-90;
			   m = m-90;	
			  
			$('body').css({overflow : 'hidden'});	
			
			autoW = w-(left_width+20);
			chatHeight = chatHeight-(h-70);
			
			$('#main_content').css({height : m,"overflow-y":'auto',"background" : "url(<?php echo $app->basePath();?>gambar/gradient_orange.png) repeat-x 0 bottom",
								   "overflow-x" : "hidden", "padding" : "4px",'width':autoW});
			
			$('#left_menu').css({height : m,overflow : 'hidden',width:210});	
			$('.chat').css({height:chatHeight});
		}
		
		createLayout();   				   
		$(window).resize(function(){ createLayout(); });
		

/* store toolbar o the bottom panel **/

		switch(parseInt(USER_SYSTEM_LEVEL))
		{
			case USER_LEVEL_ROOT:
				 EXT_TITLE  = [[],[],[],[],[],[],['Set Ready'],['Chat With'],['Home']];
				 EXT_MENU   = [[],[],[],[],[],[],['setLabelReady'],['ChatWith'],['class_active.Home']];
				 EXT_ICON   = [[],[],[],[],[],[],['group_go.png'],['group_add.png'],['house_go.png']];
				 EXT_OPTION = [{render:5, type:'combo', triger:'setLabelStatus', store:V_STATUS_STORE, header:'', name:'auxReason', id:'auxReason', width:120, value:''},
							   {render:2, type:'label', id:'AgentStatus', name:'AgentStatus', label:'Agent Status', width:50 },
							   {render:3, type:'label', id:'idCallStatus', name:'idCallStatus', label:'', width:200 },
							   {render:4,type:'label', id:'time_counter', name:'time_counter', label:'-', width:200},
							   {render:0, type:'label', id:'Script', name:'Script', label:'Show Script :', width:50 },
							   {render:1, type:'combo', triger : 'ShowScriptList',id:'openscript', store:VSCRIPT, header:'', name:'openscript', width:120, value:''}];
			break;
			
			case USER_LEVEL_ADMIN: 
				 EXT_TITLE  = [[],[],[],[],[],[],['Set Ready'],['Chat With'],['Home']];
				 EXT_MENU   = [[],[],[],[],[],[],['setLabelReady'],['ChatWith'],['class_active.Home']];
				 EXT_ICON   = [[],[],[],[],[],[],['group_go.png'],['group_add.png'],['house_go.png']];
				 EXT_OPTION = [{render:5, type:'combo', triger:'setLabelStatus', store:V_STATUS_STORE, header:'', name:'auxReason', id:'auxReason', width:120, value:''},
							   {render:2, type:'label', id:'AgentStatus', name:'AgentStatus', label:'Agent Status', width:50 },
							   {render:3, type:'label', id:'idCallStatus', name:'idCallStatus', label:'', width:200 },
							   {render:4,type:'label', id:'time_counter', name:'time_counter', label:'-', width:200},
							   {render:0, type:'label', id:'Script', name:'Script', label:'Show Script :', width:50 },
							   {render:1, type:'combo', triger : 'ShowScriptList',id:'openscript', store:VSCRIPT, header:'', name:'openscript', width:120, value:''}];
			break; 
			
			case USER_LEVEL_MANAGER: 
				 EXT_TITLE  = [[],[],[],[],[],[],['Set Ready'],['Chat With'],['Home']];
				 EXT_MENU   = [[],[],[],[],[],[],['setLabelReady'],['ChatWith'],['class_active.Home']];
				 EXT_ICON   = [[],[],[],[],[],[],['group_go.png'],['group_add.png'],['house_go.png']];
				 EXT_OPTION = [{render:5, type:'combo', triger:'setLabelStatus', store:V_STATUS_STORE, header:'', name:'auxReason', id:'auxReason', width:120, value:''},
							   {render:2, type:'label', id:'AgentStatus', name:'AgentStatus', label:'Agent Status', width:50 },
							   {render:3, type:'label', id:'idCallStatus', name:'idCallStatus', label:'', width:200 },
							   {render:4,type:'label', id:'time_counter', name:'time_counter', label:'-', width:200},
							   {render:0, type:'label', id:'Script', name:'Script', label:'Show Script :', width:50 },
							   {render:1, type:'combo', triger : 'ShowScriptList',id:'openscript', store:VSCRIPT, header:'', name:'openscript', width:120, value:''}];
			break;
			
			case USER_LEVEL_SPV: 
				 EXT_TITLE  = [[],[],[],[],[],[],[],['Set Ready'],['Chat With'],['Home']];
				 EXT_MENU   = [[],[],[],[],[],[],[],['setLabelReady'],['ChatWith'],['class_active.Home']];
				 EXT_ICON   = [[],[],[],[],[],[],[],['group_go.png'],['group_add.png'],['house_go.png']];
				 EXT_OPTION = [{render:6, type:'combo', triger:'setLabelStatus', store:V_STATUS_STORE, header:'', name:'auxReason', id:'auxReason', width:120, value:''},
							   {render:2, type:'label', id:'AgentStatus', name:'AgentStatus', label:'Agent Status', width:50 },
							   {render:3, type:'label', id:'idCallStatus', name:'idCallStatus', label:'', width:200 },
							   {render:4,type:'label', id:'time_counter', name:'time_counter', label:'-', width:200},
							   {render:5,type:'label', id:'lebel_counter', name:'lebel_counter', label:'-"', width:200},
							   {render:0, type:'label', id:'Script', name:'Script', label:'Show Script :', width:50 },
							   {render:1, type:'combo', triger : 'ShowScriptList',id:'openscript', store:VSCRIPT, header:'', name:'openscript', width:120, value:''}];
			break;
			
			case USER_LEVEL_AGENT: 
				 EXT_TITLE  = [[],[],[],[],[],[],['Set Ready'],['Home']];
				 EXT_MENU   = [[],[],[],[],[],[],['setLabelReady'],['class_active.Home']];
				 EXT_ICON   = [[],[],[],[],[],[],['group_go.png'],['house_go.png']];
				 EXT_OPTION = [{render:3, type:'combo', triger:'setLabelStatus', store:V_STATUS_STORE, header:'', name:'auxReason', id:'auxReason', width:120, value:''},
							   {render:2, type:'label', id:'AgentStatus', name:'AgentStatus', label:'Agent Status', width:50 },
							   {render:4, type:'label', id:'idCallStatus', name:'idCallStatus', label:'-"', width:200},
							   {render:6,type:'label', id:'time_counter', name:'time_counter', label:'-"', width:200},
							   {render:5,type:'label', id:'lebel_counter', name:'lebel_counter', label:'-"', width:200},
							   {render:0, type:'label', id:'Script', name:'Script', label:'Show Script :', width:50 },
							   {render:1, type:'combo', triger : 'ShowScriptList',id:'openscript', store:VSCRIPT, header:'', name:'openscript', width:120, value:''}
								];
			break;
			
			case USER_LEVEL_QUALITY: 
				 EXT_TITLE  = [[],[],[],[],[],[],['Set Ready'],['Chat With'],['Home']];
				 EXT_MENU   = [[],[],[],[],[],[],['setLabelReady'],['ChatWith'],['class_active.Home']];
				 EXT_ICON   = [[],[],[],[],[],[],['group_go.png'],['group_add.png'],['house_go.png']];
				 EXT_OPTION = [{render:5, type:'combo', triger:'setLabelStatus', store:V_STATUS_STORE, header:'', name:'auxReason', id:'auxReason', width:120, value:''},
							   {render:2, type:'label', id:'AgentStatus', name:'AgentStatus', label:'Agent Status', width:50 },
							   {render:3, type:'label', id:'idCallStatus', name:'idCallStatus', label:'', width:200 },
							   {render:4,type:'label', id:'time_counter', name:'time_counter', label:'-', width:200},
							   {render:0, type:'label', id:'Script', name:'Script', label:'Show Script :', width:50 },
							   {render:1, type:'combo', triger : 'ShowScriptList',id:'openscript', store:VSCRIPT, header:'', name:'openscript', width:120, value:''}];
			break;

			case USER_LEVEL_QUALITY_R: 
				 EXT_TITLE  = [[],[],[],[],[],[],['Set Ready'],['Chat With'],['Home']];
				 EXT_MENU   = [[],[],[],[],[],[],['setLabelReady'],['ChatWith'],['class_active.Home']];
				 EXT_ICON   = [[],[],[],[],[],[],['group_go.png'],['group_add.png'],['house_go.png']];
				 EXT_OPTION = [{render:5, type:'combo', triger:'setLabelStatus', store:V_STATUS_STORE, header:'', name:'auxReason', id:'auxReason', width:120, value:''},
							   {render:2, type:'label', id:'AgentStatus', name:'AgentStatus', label:'Agent Status', width:50 },
							   {render:3, type:'label', id:'idCallStatus', name:'idCallStatus', label:'', width:200 },
							   {render:4,type:'label', id:'time_counter', name:'time_counter', label:'-', width:200},
							   {render:0, type:'label', id:'Script', name:'Script', label:'Show Script :', width:50 },
							   {render:1, type:'combo', triger : 'ShowScriptList',id:'openscript', store:VSCRIPT, header:'', name:'openscript', width:120, value:''}];
			break;
			
		}
	

/* Ext toolbars Bottom nvigation jQuery ***/
	
		$('#toolbars').extToolbars
			({
				extUrl    : '../gambar/icon',
				extTitle  : EXT_TITLE,
				extMenu   : EXT_MENU,
				extIcon   : EXT_ICON,
				extText   : true,
				extInput  : true,
				extOption : EXT_OPTION
			});
	});
	
/* clear data if call later *****/
	
	var ClearData = function (messageid)
	{
		$.get("getCallLater.php",{act : "update-messages",messageid :messageid});	
		getMessage();
	}
	
/*  broadcast Messages  **/
	
	var getMessage = function()
	{	
	    var options = {
				id: 'message_from_top',
				position: 'top',
				size: 11,
				backgroundColor: '#ffffff',
				delay: 3000,
				speed: 500,
				fontSize: '12px',
				htmlBody:{
					imgUrl:'../gambar/icon',
					
					title:{
						icon:'information.png',
						text:'Message Box ',
						id:'message-title'
					},
					
					content:{
						text:'Hello world',
						cssBody:'box-shadow',
						id:'message-body',
						hiddenid:''
					},
					
					close:{
						icon:'cancel.png',
						css:'test'
					}
				}
			};
			
		
		new ( function(){
			var HTML = '', Json = [];
			var divContent = doJava.dom('content-msg');
			var xmlGet = null;
				xmlGet = new XMLHttpRequest();
				xmlGet.open('POST','getCallLater.php?act=get-broadcast-mesage',false);
				xmlGet.send();
				Json = JSON.parse(xmlGet.responseText);
				
				if( Json.pesan.result==1 )
				{
					var i = 0;
					for(var a in Json.pesan)
					{
						if(i==0) HTML = "<span style='color:blue;'><b style='font-size:12px;color:red;'>"+Json.pesan[a].from+",</b> says : </span>";
						if( Json.pesan[a].datetime!=undefined )
						{
							HTML += " <br><span style='color:#030c7b;'>[ "+Json.pesan[a].datetime+" ] "+
									" </span><br>"+Json.pesan[a].message+"&nbsp; ( <a href='javascript:void(0);' onclick='javascript:ClearData("+Json.pesan[a].msgid+");'>Clear</a> )<br>";
						}
						i++;
					}
						
					options.htmlBody.content.text =HTML;
					options.htmlBody.content.hiddenid =	0;
					if( i > 0 ) $.showMessage('', options);	
				}
				else{
					if( divContent!=null ) divContent.innerHTML='';
				}	
		});	
		
		return false;
	}
	

/* ------------------------------------*/		
/* Push broadcast Messages **/
/* ------------------------------------*/	
  var V_SESSION_HANDLING = '<?php echo $db->getSession('handling_type');?>';
	
	function GetCallLaterContent(CustomerId,CampaignId){
		if( CustomerId!='' && CampaignId!=''){
			if( V_SESSION_HANDLING == 3){
				doJava.Params = {
					CustomerId : CustomerId,
					CampaignId : CampaignId
				}
				
				$('main_content').load('dta_spv_detail.php.php?'+doJava.ArrVal());
			}
			else if( V_SESSION_HANDLING==4){
				doJava.Params = {
					customerid : CustomerId,
					campaignid : CampaignId
				}
				
				$('main_content').load('dta_contact_detail.php?'+doJava.ArrVal());
			}
		}	
	}
	
	var callLaterPush = function ()
	{
		$.getJSON('getCallLater.php',{act : 'select'}, function(data) {
			 	if(data.show >= 1){
					var not = $(this).attr('href');
					var link = '<a href="javascript:void(0);" id="reminderId"'
							+'onclick="$(\'#main_content\').load(\'dta_contact_detail.php?CustomerId='+data.customer+'&CampaignId='+data.campaignid+'\');" '
							+'>'+data.customername+'</a>';			
					var notice = '<div class="notice">'
								  + '<div class="notice-body">' 
									  + '<img src="<?php echo $app->basePath();?>gambar/info.png" alt="" />'
									  + '<h3>Reminder Call For : <br>'+link+'</h3>'
									  + '<p><span>With Status  : '+data.status+'</span></p>'
								  + '</div>'
								  + '<div class="notice-bottom">'
								  + '</div>'
							  + '</div>';
							  
						$( notice ).purr(
							{
								usingTransparentPNG: true,
								isSticky: true
							}
						);
						$.get("getCallLater.php",{act : "update",CustomerId : data.customer, calllaterdate : data.tryagain});	
						return false;
				}
			});
		}
		
/* reminder call verifed from queue ***/

	var VerifiedReminder = function ()
	{
		$.getJSON('getCallLater.php',{act : 'get_verified'}, function(data) {
			 	if(data.show >= 1){
					//alert(data.query);
					var not = $(this).attr('href');
					var link = '<a href="javascript:void(0);" id="VerifiedId" onclick="javascript:extendsJQuery.verifiedContent('+data.CustomerId+','+data.CampaignId+','+data.VerifiedStatus+');">'+data.CustomerFirstName+'</a>';			
					var notice = '<div class="notice">'
								  + '<div class="notice-body">' 
									  + '<img src="<?php echo $app->basePath();?>gambar/info.png" alt="" />'
									  + '<h3>You have pending Closing </h3>'
									  + '<h3>'+link+'</h3>'								  
								  + '</div>'
								  + '<div class="notice-bottom">'
								  + '</div>'
							  + '</div>';
							  
						$( notice ).purr(
							{
								usingTransparentPNG: true,
								isSticky: true
							}
						);
						$.get("getCallLater.php",{act : "update_verifed",VerifiedId : data.VerifiedId});	
						return false;
				}
			});
		}	
		

/* ------------------------------------*/		
/* Push interval Message  **/
/* ------------------------------------*/	
	
	var dataPush = function ()
	{
		$('.chat').load('../class/class.user.chat.php');
		
		/*di remark, karena tidak tahu peruntukkannya dan khawatir membebani server karena push data*/
		// if( USER_SYSTEM_LEVEL==4 ){
			// VerifiedReminder();
		// }	
	}
	
	var dataCallback = function ()
	{
		if( USER_SYSTEM_LEVEL==4 ){
			callLaterPush();
		}	
	}
	
	setInterval('dataPush();',7000);
	setInterval('dataCallback();',8500);
	setInterval('getMessage();',20000);
</script>
<style>
		#accordion { border:0px solid #000;width:200px;}
		#panel_user{border:1px solid #000000;}
		p span {
			font-size:11px;
		}
		.px {
			font-size:9px;
			font-family:Trebuchet Ms;
			color:#000;
		}
		#purr-container {
			position: fixed;
			top: 0;
			right: 0;
		}
		
		.notice {
			position: relative;
			width: 324px;
		}
		.notice .close	{position: absolute; top: 12px; right: 12px; display: block; width: 18px; height: 17px; text-indent: -9999px; background: url(../gambar/purrClose.png) no-repeat 0 10px;}
		
		.notice-body {
			min-height: 50px;
			padding: 22px 22px 0 22px;
			background: url(../gambar/purrTop.png) no-repeat left top;
			color: #f9f9f9;
		}
			.notice-body img	{width: 50px; margin: 0 10px 0 0; float: left;}
			.notice-body h3	{margin: 0; font-size: 1.1em;}
			.notice-body p		{margin: 5px 0 0 60px; font-size: 0.8em; line-height: 1.4em;}
		
		.notice-bottom {
			height: 22px;
			background: url(../gambar/purrBottom.png) no-repeat left top;
		}
		#toolbars select{font-size:12px;}
		#notification{position:absolute;z-index:99;}
		h3 a {font-size:12px;color:#FFF}
		h3 a:hover {font-size:14px;color:#FFF}
		.msg_icon{float:left;padding-right:0px;margin-left:-1200;}
		#msg_view {position:absolute; left:250px;width:225px;height:228px;}
		#msg_txt {top:10px;width:170px;height:170px;border:0px dotted #666;position:absolute;margin:10px;left:12px; overflow:auto;
		font-family: 'Comic Sans MS',Textile,cursive;font-size:14px;color:#999;}
</style>
</head>


<?php
if( $db -> getSession('handling_type')!='' ){
	echo '<body onLoad="prepareCTIClient();disableAllButton();" onUnload="document.ctiapplet.ctiDisconnect();">';
}else{
	echo "<body>";
}
?>

<div id="pass" title="Change Password" style="display:none;">
  	<fieldset class="change_password" style="border:0px;">
		<table border="0">
  		  <tr>
  		    <td><label for="curr_password">Current Password</label></td>
  		    <td><input type="password" name="curr_password" id="curr_password" class="text ui-widget-content ui-corner-all" /></td>
  		  </tr>
  		  <tr>
  		    <td><label for="new_password">New Password</label></td>
  		    <td><input type="password" name="new_password" id="new_password" value="" class="text ui-widget-content ui-corner-all" /></td>
  		  </tr>
  		  <tr>
  		    <td><label for="re_new_password">Re-type New Pass.</label></td>
  		    <td><input type="password" name="re_new_password" id="re_new_password" value="" class="text ui-widget-content ui-corner-all" /></td>
  		  </tr>
  		</table>
  	</fieldset>
  	<!--</form>-->
  </div> 
  
  <div id="password_confirm" title="Change Password"></div>
<div id="notification"></div>
	<noscript>
		<span style="font:20px bold Tahoma, Verdana, Arial;color:red;text-align:center;">
			You have to enable JavaScript at your browser before using this page correctly :)
		</span>
	</noscript>
    
	<div id="loading" style="display:none">&nbsp;<img src="<?php echo $app->basePath();?>gambar/loading.gif">Loading..</div>
	<div id="wrapper">
	
		<!-- HEADER -->
		<div id="header">
		
		 <!-- start : right info -->
			
			<div class="top_info" style="border:0px solid #000;width:660px;">
				<div class="top_info_right">
					<p>
          				Profile : <b><?php echo $db->valueSQL("SELECT name FROM tms_agent_profile WHERE id = '".$user_profile."'"); ?></b><br />
						Last updated : <?php echo date("l"); ?>, <?php echo date("j F Y, H:i:s"); ?>		
          			</p>
				</div>
				<div class="top_info_left" style="margin-top:-2px;padding:left:2px;border:0px solid blue;width:310px;font-size:11px;">
					<?php if($handling_type !=4){?>
                    <div class="msg_icon" style="cursor:pointer;margin-right:6px;"><img class="msg_n" src="../gambar/msg.png" /></div>&nbsp;	
                    <?php }?>
                    <b><?php echo $db->valueSQL("SELECT full_name FROM tms_agent WHERE id = '".$username."'"); ?>,</b> you are logged in<br />
						<p style="margin-left:12px;"> &nbsp;on <?php echo $login_date; ?></p>
          			
				</div>
			</div>
			
		<!-- stop : right info -->	
			
			<!-- start : logo -->
			
			<div class="top_logo" style="border:0px solid #000;width:600px;z-index:999;">
				<div class="top_info_left" style="border:0px solid #000;width:600px;">
				   <h1 style="font-size:25px;margin-top:-4px;">
					 <a href="#" title="<?php echo $Themes -> V_WEB_TITLE; ?>" style="color:#041A63;"> 
						<?php echo $Themes -> V_LOGO_DARK; ?>
						<span class="<?php echo $Themes -> V_UI_THEMES; ?>" style="color:#041A63;"> <?php echo $Themes -> V_LOGO_ORANGE; ?></span></a>
				   </h1>                 
			  	</div>
			</div>
		</div>
		
        <div>	
			
			<!-- start :: LEFT MENU -->
			
				<div id="left_menu" >  <?php $MainMenu -> iniateMenuUser();?> </div>
			
			<!-- endof :: LEFT MENU -->

			
			<!-- start : MAIN CONTENT -->
				<div id="main_content"> </div>	
			<!-- endof : Main Content -->
			
        </div>
		
       </div>
	</div>
	<div id="logout" style="display:none;" title="Logout from this session?">
  	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;">
    </span>You will logged-out from this session.<br />Are you sure?</p>
  </div>

  
  <div id="foot" style="border:1px solid #dddddd;height:35px;overflow:hidden;"><span>
<?php 
if($db -> getSession('handling_type')!=''): 
	$sesCTI = new CTI( $db -> getSession('username') );  
	$sesCTI -> includeCTI(); ?>
	
<!-- start : form required CTI --->

		<div id="toolbars" style="padding-right:10px;font-size:12px;border:1px solid #ddd;height:41px;text-align:right;margin-top:-8px;margin-left:1px;margin-right:-0px;"></div>
		<form name="frmAgent" id="idFrmAgent" >
			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" >
				<tr><td valign="top" nowrap class="small">
				<input type="button" name="btnReady" id="btnReady" onClick="document.ctiapplet.agentSetReady();return false;" value="Ready"/>
				<input type="button" name="btnAUX"   onClick="document.ctiapplet.agentSetNotReady(document.frmAgent.auxReason.value);return false;" value="Not Ready"/>
				
				<select name="auxReason" style="font-size:11px; width:75px; border:1px solid #ddd;height:22px;background-color:#eee;">
					<option value="0">Break</option>
					<option value="1">Solat</option>
				</select>
			</td>
			<td width=70 align="center" nowrap class="small">
				<b><div id="idAgentStatus" style="border:0px solid red;position:absolute;width:100;margin-top:-10px;margin-left:-30">&nbsp;Agent Status</div></b>
			</td>
			<td width=150 align="center" nowrap class="small">			
				<!--<div id="idCallStatus" style="border:0px solid red;position:absolute;width:100;margin-top:-10px;margin-left:-30">&nbsp; Call Status</div> -->
			</td><td valign="top">
				<button name="btnHold" id="btnHold"     
					onClick="onButtonHoldClick();return false;" style="display:none;">Hold</button>			
				<input type="button" name="btnHangup"  id="btnHangup"  style="display:none;" onClick="document.ctiapplet.callHangup();return false;" value="Hangup"/>
			</td></tr>
				<tr><td colspan=4>
		<?php if( $sesCTI -> getTelphone() ) : ?>	
			<applet 
			name="ctiapplet" 
			code="centerBackAgent.class"
			archive="<?php echo $app->basePath();?>js/cti/centerBackAgentApplet.jar" width="215" height="55"  MAYSCRIPT
			onLoad="document.ctiapplet.setAgentSkill(1);document.ctiapplet.ctiConnect();">
			<param name="CTIHost"  value="<?php echo $db -> getSession('ctiIp'); ?>"/>
			<param name="CTIPort"  value="<?php echo $db -> getSession('ctiUdpPort');?>"/>
			<param name="agentId"  value="<?php echo $db -> getSession('agentId');?>"/>
			<param name="agentLogin" value="<?php echo $db -> getSession('agentLogin');?>"/>
			<param name="agentName"  value="<?php echo $db -> getSession('agentName');?>"/>        
			<param name="agentGroup" value="<?php echo $db -> getSession('agentGroup');?>"/>
			<param name="agentLevel" value="<?php echo $db -> getSession('agentLevel');?>"/>
			<param name="agentExt"   value="<?php echo $db -> getSession('agentExt');?>"/>        
			<param name="agentPbxGroup" value="<?php echo $db -> getSession('agentPbxGroup'); ?>"/>
			<param name="debugLevel" value="10"/>

			  alt="Your browser understands the &lt;APPLET&gt; tag but isn't running the applet, for some reason."
			  Your browser is completely ignoring the &lt;APPLET&gt; tag!
		</applet>
			</td>  		
			</tr>
		
		<input name="destNo" type="hidden" />
		<input name="passwd" type="hidden" />
		<input name="callAction" type="hidden" />
		<?php endif; ?>
		</table>
  	</form>
	<?php endif; ?> 
	<!-- stop : form required CTI --->
  </span>
  
  </div>
</body>
</html>
