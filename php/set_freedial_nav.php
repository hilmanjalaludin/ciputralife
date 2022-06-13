<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	

	SetNoCache();
	$filter ='';
	
	$sql = " SELECT 
			 a.*, b.ProviderName, b.ProviderCode , c.full_name 
			 FROM tms_testcall_report a 
			 LEFT JOIN tms_misdn_provider b on a.ProviderId=b.ProviderId
			 LEFT JOIN tms_agent c on a.CallByUser=c.UserId ";
				
	$NavPages -> setPage(20);			 
	$NavPages -> query($sql);
    $NavPages -> setWhere();
	
/**** user group *****/
	
	function userGroup()
	{
		GLOBAL $db;
		$datas= array();
		$sql = " SELECT * FROM tms_agent_profile ";
		$qry = $db -> execute($sql,__FILE__,__LINE__);
		while( $row = $db->fetchrow($qry))
		{
			$datas[$row -> UserId] = $row -> name; 
		}
		return $datas;	
	
	}	
	
?>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>js/javaclass.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>js/upload.js"></script>
  	<script type="text/javascript">
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
					 
		$(function(){
			$('#toolbars').extToolbars({
				extUrl  :'../gambar/icon',
				extTitle:[['Export Report Call'],['Option Call'],['Direct Call']],
				extMenu :[['exportExtension'],['CallOption'],['DirectCall']],
				extIcon :[['page_white_excel.png'],['page_edit.png'],['telephone_go.png']],
				extText :true,
				extInput:false,
				extOption:[]
			});
		});
		//CallOption();
	
	var saveExtension = function(){
		var ext_number  = doJava.dom('ext_number').value;
		var ext_pbx = doJava.dom('ext_pbx').value;
		var ext_desc  = doJava.dom('ext_desc').value;
		var ext_type  = doJava.dom('ext_type').value;
		var ext_status  = doJava.dom('ext_status').value;
		var ext_location  = doJava.dom('ext_location').value;
			doJava.File = initClass;
		if( ext_pbx!='' && ext_number!=''){
			doJava.Params = {
				action : 'add_extension_exe',
				ext_number : doJava.dom('ext_number').value,
				ext_pbx : doJava.dom('ext_pbx').value,
				ext_desc : doJava.dom('ext_desc').value,
				ext_type : doJava.dom('ext_type').value,
				ext_status : doJava.dom('ext_status').value,
				ext_location : doJava.dom('ext_location').value
			}
			
			var error= doJava.Post();
			
			if( error==1){
				alert("Success, Save Extension!");
				extendsJQuery.construct(navigation,'')
				extendsJQuery.postContentList();
			}
			else{
				alert("Failed, Save Extension!");
			}
		}
		
	}
	
	

/* ***************# Section #*************************************************************************/

				
		var datas={ UserId:'<?php echo $db->escPost('UserId');?>'}
		extendsJQuery.totalPage = <?php echo $NavPages ->getTotPages(); ?>;
		extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
		
		
/* ***************# Section #*************************************************************************/			
/* assign navigation filter **/
		
		var initClass  = '../class/class.freedial.system.php'
		var navigation = {
			custnav:'set_freedial_nav.php',
			custlist:'set_freedial_list.php'
		}
			
		
		
/* ***************# Section #*************************************************************************/		
/* assign show list content **/
		
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContentList();
		

/*open direct call **/
	var DirectCall = function(){
		doJava.File = "../class/class.freedial.system.php";
		doJava.Params = {
			action:'direct_call_tpl'
		}
		doJava.Load('tpl_header');
	}

	
/* ***************# Section #*************************************************************************/
	var CallOption = function(){
	
		doJava.File = initClass;
		doJava.Params = { action:'call_option_tpl'}
		doJava.Load('tpl_header');
	}
		
	doJava.onReady(
			evt=function(){ 
			  CallOption();
			},
		  evt()
		)
/* ***************# Section #*************************************************************************/	
	
	var getCallType = function(opt){
		doJava.File = initClass;
		doJava.Params = { 
			action:'isdn_type_tpl',
			provider:opt
		}
		
		doJava.Load('isdn_type');
	}
	
	var SetNumberCall = function(Tonumber){
	
		if( Tonumber!=''){
			doJava.dom('call_to_number').value = Tonumber;
		}
		else
			alert('Please Select Number!');
			return;
	}
	
	var DialTest = function(){
		doJava.File = initClass;
		
		//var CallTestNumber = doJava.dom('isdn_type_call').value;
		var CallNumber = doJava.dom('call_to_number').value;
			if( CallNumber!='')
			{
				phoneCall.initNumber = CallNumber;
				document.ctiapplet.callDialCustomer('',CallNumber,CallNumber,CallNumber);
				SaveActivityTest();
			}
			else{
				alert("Please input number!");
			}	
		  
	}
	
	var HangupTest = function(){
		doJava.File = initClass;
		//var CallTestNumber = doJava.dom('isdn_type_call').value;
		var CallNumber = doJava.dom('call_to_number').value;
			if( CallNumber!='')
			{
				document.ctiapplet.callHangup();
				UpdateActivityTest();
			}
			else{
				alert("Please input number!");
			}	
		  
	}
	
	var UpdateActivityTest = function(){
		doJava.File = initClass;
		//var CallTestNumber = doJava.dom('isdn_type_call').value;
		var CallNumber = doJava.dom('call_to_number').value;
		//var ProviderType = doJava.dom('provider_type').value;
		//var Notes = doJava.dom('notes').value;
			doJava.Params = {
				action:'update_end_date',
				//CallTestNumber :CallTestNumber,
				CallNumber :CallNumber
				//ProviderType:ProviderType,
				//Notes:Notes
			}
			
			var error = doJava.Post();
				if( error==1){
					//alert('Success, Save call activity test call ');
					extendsJQuery.postContentList();
				}
				else
					alert('Failed, Save call activity test call ');
	}
	
	var SaveActivityTest = function(){
		doJava.File = initClass;
		//var CallTestNumber = doJava.dom('isdn_type_call').value;
		var CallNumber = doJava.dom('call_to_number').value;
		//var ProviderType = doJava.dom('provider_type').value;
		//var Notes = doJava.dom('notes').value;
			doJava.Params = {
				action:'save_activity_test',
				//CallTestNumber :CallTestNumber,
				CallNumber :CallNumber
				//ProviderType:ProviderType,
				//Notes:Notes
			}
			
			var error = doJava.Post();
				if( error==1){
					extendsJQuery.postContentList();
				}
				else
					alert('Failed, Save call activity test call ');
	}
	
/* ***************# Section #*************************************************************************/
	
	var Clear =function(){
		doJava.File = initClass;
		doJava.Params = { action:'clear' }
		doJava.Load('tpl_header');
	}
	
	var ExcelReport = function(){
		doJava.File = initClass;
		var start_date = doJava.dom('start_date').value; 
		var end_date = doJava.dom('end_date').value;
		var ProviderType = doJava.SelArrVal('provider_type');
			
			doJava.Params = {
				action:'export_excel_exe',
				start_date: start_date,
				end_date : end_date,
				ProviderType:ProviderType
			}
			
		if( confirm('Do you want export to excel ?')){	
			window.open(initClass+'?'+doJava.ArrVal())	
		}
			
	}
	
/* SetCallNumber **/

	var SetCallNumber = function(opts){
		var text_string_number = '';
		var text_call_number = doJava.dom('call_to_number_id');
		if( opts.value!='' ){
			text_call_number.value += opts.value;
			text_call_number.focus();
		}
	}	
	
/* ButtonClear **/

	var ButtonClear = function(){
		var text_call_number = doJava.dom('call_to_number_id');
		if( text_call_number.value!='' ){
			text_call_number.value = text_call_number.value.substring(0,(text_call_number.value.length-1));
			text_call_number.focus();
		}	
	}	
	
	
/* ValidCallNumber **/

	var ValidCallNumber = function(textarea){
		if( (textarea.value!='#')){
			if(isNaN(textarea.value)){
				textarea.value = textarea.value.substring(0,(textarea.value.length-1));
			}
			else{
				textarea.value = textarea.value;
			}
		}	
		else{
			textarea.value = textarea.value;
		}
	}
	
/* SAVE DATA TO fredial sent ***/ 	
	doJava.SaveFreedial = function(DialToNumber)
	{
		var CallSessionKey = document.ctiapplet.getCallSessionKey();
		var CallerNumber = phoneCall.initNumber;
		
		if( CallSessionKey !='' ){
			this.File ="../class/class.freedial.system.php";
			this.Params = {
				action :'save_free_dial',
				CallerNumber : CallerNumber,
				CallSessionKey : CallSessionKey
			}
			return this.eJson();
		}
		else
			return false;
	}
	
/* ButtonDial **/

	var ButtonDial = function()
	{
		var DialToNumber = doJava.dom('call_to_number_id').value;
			if( DialToNumber!='' ){
				phoneCall.initNumber = DialToNumber;
				if( !phoneCall.initCall){
					if( document.ctiapplet.getAgentStatus()==AGENT_READY){
						phoneCall.initCall = true;
						document.ctiapplet.callDialCustomer('',DialToNumber,'free_dial','free_dial');
						var error = doJava.SaveFreedial();
						if( error.result )
						{
							doJava.CallSessionKey = error.CallSessionKey;
							extendsJQuery.postContentList();
						}
					}
					else{
						alert('Please set Ready Status!'); return false;
					}
				}	
			}
			else{
				alert('No Number !'); return false;
			}	
	}
	
/* hangup **/

	var ButtonHangup = function()
	{
		if( phoneCall.initCall){
			document.ctiapplet.callHangup();
			phoneCall.initCall=false;
			if( doJava.CallSessionKey !='' ){
				doJava.File ="../class/class.freedial.system.php";
				doJava.Params = 
				{
					action :'update_free_dial',
					CallerNumber : phoneCall.initNumber,
					CallSessionKey : doJava.CallSessionKey
				}
				
				var error = doJava.eJson();
				if( error.result ){
					doJava.CallSessionKey = '';
					extendsJQuery.postContentList();
				}
			}
		}
	}
	
/* export ***/	
	
	var exportExtension = function(){
		doJava.File = initClass;
			doJava.Params = { action:'tpl_report' }
			$('#tpl_header').load(initClass+'?'+doJava.ArrVal());
	}
	
	DirectCall();
	
	</script>
	<fieldset class="corner" style="background-color:white;">
		<legend class="icon-userapplication">&nbsp;&nbsp;Free Dial </legend>
			<div id="toolbars" class="toolbars"></div>
			<div id="tpl_header" class=""></div>
			<div class="content_table"></div>
			<div id="pager"></div>
			<div id="UserTpl"></div>
	</fieldset>	
	