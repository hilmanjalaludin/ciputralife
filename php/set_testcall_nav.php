<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	
	SetNoCache();
	$filter ='';
	
	$sql = " select 
	a.*, b.ProviderName, b.ProviderCode , c.full_name 
from tms_testcall_report a 
left join tms_misdn_provider b on a.ProviderId=b.ProviderId
left join tms_agent c on a.CallByUser=c.UserId ";
					
					
	
	$NavPages -> setPage(20);			 
	$NavPages -> query($sql);
    $NavPages -> setWhere();
	
	
	/** user group **/
	
	function userGroup(){
		global $db;
		$datas= array();
			$sql = "select * from tms_agent_profile ";
			$qry = $db->execute($sql,__FILE__,__LINE__);
			while( $row = $db->fetchrow($qry)):
				$datas[$row->UserId] = $row->name; 
			endwhile;
			
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
		jQuery(function(){
			jQuery('.toolbars').corner();
			jQuery('.corner').corner();
			jQuery('#toolbars').extToolbars({
				extUrl  :'../gambar/icon',
				extTitle:[['Export Report Call'],['Option Call']],
				extMenu :[['exportExtension'],['CallOption']],
				extIcon :[['page_white_excel.png'],['page_edit.png']],
				extText :true,
				extInput:true,
				extOption:[{
						render:8,
						type:'text',
						id:'v_cmp_user', 	
						name:'v_cmp_user',
						value:'<?php echo $db->escPost('UserId');?>',
						width:120
					}]
			});
		});
		
	
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
		
		var initClass  = '../class/class.calltest.system.php'
		var navigation = {
			custnav:'set_testcall_nav.php',
			custlist:'set_testcall_list.php'
		}
		
		
/* ***************# Section #*************************************************************************/		
/* assign show list content **/
		
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContentList();
		

	
/* ***************# Section #*************************************************************************/
	

/* ***************# Section #*************************************************************************/	
	
	var CallOption = function(){
	
		doJava.File = initClass;
		doJava.Params = { action:'call_option_tpl'}
		doJava.Load('tpl_header');
	}
	
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
		
		var CallTestNumber = doJava.dom('isdn_type_call').value;
		var CallNumber = doJava.dom('call_to_number').value;
			if( CallNumber!='')
			{
				phoneCall.initNumber = CallNumber;
				document.ctiapplet.callDialCustomer('',CallNumber,CallTestNumber,CallTestNumber);	
			}
			else{
				alert("Please input number!");
			}	
		  
	}
	
	var HangupTest = function(){
		doJava.File = initClass;
		var CallTestNumber = doJava.dom('isdn_type_call').value;
		var CallNumber = doJava.dom('call_to_number').value;
			if( CallNumber!='')
			{
				document.ctiapplet.callHangup();
			}
			else{
				alert("Please input number!");
			}	
		  
	}
	
	var SaveActivityTest = function(){
		doJava.File = initClass;
		var CallTestNumber = doJava.dom('isdn_type_call').value;
		var CallNumber = doJava.dom('call_to_number').value;
		var ProviderType = doJava.dom('provider_type').value;
		var Notes = doJava.dom('notes').value;
			doJava.Params = {
				action:'save_activity_test',
				CallTestNumber :CallTestNumber,
				CallNumber :CallNumber,
				ProviderType:ProviderType,
				Notes:Notes
			}
			
			var error = doJava.Post();
				if( error==1){
					alert('Success, Save call activity test call ');
					extendsJQuery.postContent();
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
	
	var exportExtension = function(){
		doJava.File = initClass;
			doJava.Params = { action:'tpl_report' }
			$('#tpl_header').load(initClass+'?'+doJava.ArrVal());
	}

	</script>
	<fieldset class="corner" style="background-color:white;">
		<legend class="icon-userapplication">&nbsp;&nbsp;Call Test Management </legend>
			<div id="toolbars" class="toolbars"></div>
			
			<div id="tpl_header"></div>
			<div class="content_table"></div>
			<div id="pager"></div>
			<div id="UserTpl"></div>
	</fieldset>	
	