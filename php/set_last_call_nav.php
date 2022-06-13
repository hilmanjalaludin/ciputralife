<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
	$sql = " SELECT 
					LastCallId 
				FROM t_gn_lastcall a left join tms_agent b on a.LasCallCreateBy=b.UserId";
	
	$NavPages -> setPage(10);			 
	$NavPages -> query($sql);
    $NavPages -> setWhere();
	
	
	/** user group **/
	
	
?>
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
  	<script type="text/javascript">
		
		$(function(){
		
			// $('.corner').corner();
			// $('#toolbars').corner();
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Enable'],['Disable'],['Edit'],['Add'],['Delete'],['Cancel'],['Search']],
				extMenu  :[['enableLastCall'],['disableLastCall'],['editLastCall'],['addResult'],['deleteResult'],['cancelResult'],['searchResult']],
				extIcon  :[['accept.png'],['lock.png'], ['clock_edit.png'],['add.png'],['delete.png'],['cancel.png'], ['zoom.png']],
				extText  :true,
				extInput :true,
				extOption: [{
						render:6,
						type:'text',
						id:'v_result', 	
						name:'v_result',
						value:'',
						width:200
					}]
			});
			
		});
		
		var datas={}
			extendsJQuery.totalPage = <?php echo $NavPages ->getTotPages(); ?>;
			extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
		
	/* assign navigation filter **/
		
		var navigation = {
			custnav:'set_last_call_nav.php',
			custlist:'set_last_call_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,'')
		extendsJQuery.postContentList();
		
		doJava.File = '../class/class.last.call.php' 
		
		
		var searchResult = function(){
			alert(doJava.dom('v_result').value);
			
		}
		var cancelResult=function(){
			doJava.dom('span_top_nav').innerHTML='';
		}
		
		var addResult = function(){
			$('#span_top_nav').load(doJava.File+'?action=tpl_add');
		}
		
/* *************************************** */
/* *************************************** */	
		
		var enableLastCall = function(){
			var inResultCheck = doJava.checkedValue('chk_lastcall');
				if( inResultCheck!=''){
					doJava.Params = {
						action:'enable_last_call',
						resultid: inResultCheck
					}
					var error = doJava.Post();
						if( error==1)
						{
							alert("Succeeded, Enable Last Call !");
							extendsJQuery.postContent();
						}
						else{ 
							alert("Failed, Enable Last Call !"); 
							return false; 
						}
				}
				else{
					alert("Please select Rows !")
				}
		}
		
	var editLastCall = function(){
		var inResultCheck = doJava.checkedValue('chk_lastcall');
			if( inResultCheck!=''){
				$('#span_top_nav').load(doJava.File+'?action=tpl_edit&resultid='+inResultCheck);
			}
			else
				alert("Please select Rows !")
	}	
		
/* *************************************** */
/* *************************************** */
	
		var disableLastCall = function(){
			var inResultCheck = doJava.checkedValue('chk_lastcall');
				if( inResultCheck!=''){
					doJava.Params = {
						action:'disable_last_call',
						resultid: inResultCheck
					}
					var error = doJava.Post();
						if( error==1)
						{
							alert("Succeeded, Disable Last Call !");
							extendsJQuery.postContent();
						}
						else{ 
							alert("Failed, Disable Last Call !"); 
							return false; 
						}
				}
				else{
					alert("Please select Rows !")
				}
		}
		
		
/* *************************************** */
/* *************************************** */
	
		var deleteResult = function(){
			
				var inResultCheck = doJava.checkedValue('chk_lastcall');
				if( inResultCheck!=''){
					doJava.Params = {
						action:'delete_last_call',
						resultid: inResultCheck
					}
					var error = doJava.Post();
						if( error==1)
						{
							alert("Succeeded, Delete Last Call !");
							extendsJQuery.postContent();
						}
						else{ 
							alert("Failed, Last Call !"); 
							return false; 
						}
				}
				else{
					alert("Please select Rows !")
				}
		}
		
		
/* *************************************** */
/* *************************************** */

		var saveResult=function(){
			var last_call_start_date 		= doJava.dom('last_call_start_date').value;
			var last_call_end_date 			= doJava.dom('last_call_end_date').value; 
			var last_call_hour_start_time 	= doJava.dom('last_call_hour_start_time').value;
			var last_call_minute_start_time = doJava.dom('last_call_minute_start_time').value;
			var last_call_hour_end_time 	= doJava.dom('last_call_hour_end_time').value;
			var last_call_minute_end_time 	= doJava.dom('last_call_minute_end_time').value;
			var last_call_reason 			= doJava.dom('last_call_reason').value;
			var last_call_status			= doJava.dom('last_call_status').value;
			
			if( (last_call_start_date!='') 
				&& (last_call_end_date!='')
				&& (last_call_hour_start_time!='') 
				&& (last_call_minute_start_time!='')
				&& (last_call_hour_end_time!='')
				&& (last_call_minute_end_time!='')
				&& (last_call_reason!='')
				&& (last_call_status!='') )
			{
				doJava.Params = {
					action:'save_last_call',
					last_call_start_date:last_call_start_date,	
					last_call_end_date : last_call_end_date,
					last_call_hour_start_time: last_call_hour_start_time,
					last_call_minute_start_time: last_call_minute_start_time,
					last_call_hour_end_time: last_call_hour_end_time,
					last_call_minute_end_time: last_call_minute_end_time,
					last_call_reason: last_call_reason,
					last_call_status: last_call_status
				}
				
				if(confirm('Do you want to save this Last Call?'))
				{
					var error = doJava.Post();
						if( error ==1)
						{
							alert("Succeeded, Saving Last Call");
							extendsJQuery.postContent();	
						}
						else { alert("Failed, Saving Last Call");}
				}
				else { return false; }	
				
			}else { alert('Input Not Complete!') }
		}
		
/* *************************************** */
/* *************************************** */
	
		var UpdateLastCall = function(){
			var last_call_start_date 		= doJava.dom('last_call_start_date').value;
			var last_call_end_date 			= doJava.dom('last_call_end_date').value; 
			var last_call_hour_start_time 	= doJava.dom('last_call_hour_start_time').value;
			var last_call_minute_start_time = doJava.dom('last_call_minute_start_time').value;
			var last_call_hour_end_time 	= doJava.dom('last_call_hour_end_time').value;
			var last_call_minute_end_time 	= doJava.dom('last_call_minute_end_time').value;
			var last_call_reason 			= doJava.dom('last_call_reason').value;
			var last_call_status			= doJava.dom('last_call_status').value;
			var last_call_editid			= doJava.dom('edit_lastid').value;
			
			if( (last_call_start_date!='') 
				&& (last_call_end_date!='')
				&& (last_call_hour_start_time!='') 
				&& (last_call_minute_start_time!='')
				&& (last_call_hour_end_time!='')
				&& (last_call_minute_end_time!='')
				&& (last_call_reason!='')
				&& (last_call_status!='') )
			{
				doJava.Params = {
					action:'update_last_call',
					last_call_start_date:last_call_start_date,	
					last_call_end_date : last_call_end_date,
					last_call_hour_start_time: last_call_hour_start_time,
					last_call_minute_start_time: last_call_minute_start_time,
					last_call_hour_end_time: last_call_hour_end_time,
					last_call_minute_end_time: last_call_minute_end_time,
					last_call_reason: last_call_reason,
					last_call_status: last_call_status,
					last_call_editid: last_call_editid
				}
				
				if(confirm('Do you want to Update this Last Call?'))
				{
					var error = doJava.Post();
						if( error ==1)
						{
							alert("Succeeded, Updating Last Call");
							extendsJQuery.postContent();	
						}
						else { alert("Failed, Update Last Call");}
				}
				else { return false; }	
				
			}else { alert('Input Not Complete!') }
		}
		
	</script>
	
	<!-- start : content -->
	
		<fieldset class="corner">
			<legend class="icon-callresult">&nbsp;&nbsp;Last Call </legend>	
				<div id="toolbars"></div>
				<div id="span_top_nav"></div>
				<div class="content_table"></div>
				<div id="pager"></div>
				<div id="ViewCmp"></div>
		</fieldset>	
		
	<!-- stop : content -->
	
	
	