<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
	$sql = " select a.CallReasonId from t_lk_callreason a
					left join t_lk_callreasoncategory b
					on a.CallReasonCategoryId=b.CallReasonCategoryId";
					
	
	$NavPages -> setPage(10);			 
	$NavPages -> query($sql);
	
    $NavPages -> setWhere();
	$NavPages -> OrderBy(" a.CallReasonCategoryId");
	
	
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
				extTitle :[['Enable'],['Disable'] ,['Add Call Result'],['Edit Call Result'],['Cancel'],['Search']],
				extMenu  :[['enableResult'],['disableResult'],['addResult'],['editResult'],['cancelResult'],['searchResult']],
				extIcon  :[['accept.png'],['cancel.png'], ['add.png'],['calendar_edit.png'],['cancel.png'], ['zoom.png']],
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
			custnav:'set_call_result_nav.php',
			custlist:'set_call_result_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,'')
		extendsJQuery.postContentList();
		
		
		var searchResult = function(){
			alert(doJava.dom('v_result').value);
			
		}
		var cancelResult=function(){
			doJava.dom('span_top_nav').innerHTML='';
		}
		
		var addResult = function(){
			doJava.File = '../class/class.call.result.php' 
			doJava.Params ={ action:'tpl_add' }	
			doJava.Load('span_top_nav');
		}
		
		var editResult = function(){
			var inResultCheck = doJava.checkedValue('chk_result');
			var inArray = inResultCheck.split(',');
		 if( inResultCheck!=''){	
			if( inArray.length>1){
				alert('Please Select One Rows');
			}
			else{
				doJava.File = '../class/class.call.result.php' 
				doJava.Params ={ 
					action:'tpl_edit',
					resultid:inArray
				}	
				doJava.Load('span_top_nav');
			}
		  }
		  else { alert('Please select rows !'); }
		}
		
		var disableResult=function()
		{
			doJava.File = '../class/class.call.result.php' 
			var inResultCheck = doJava.checkedValue('chk_result');
			if( inResultCheck!=''){
				doJava.Params = {
					action:'disable_result',
					resultid: inResultCheck
				}
				var error = doJava.Post();
				if( error==1)
				{
					alert("Success disabling the call result!");
					extendsJQuery.postContent();
				}
				else{ 
					alert("Failed disabling the call result!"); 
					return false; 
				}
			}
			else{
				alert("Please select a row!")
			}
		}
		
		var enableResult=function()
		{
			doJava.File = '../class/class.call.result.php' 
			var inResultCheck = doJava.checkedValue('chk_result');
			if( inResultCheck!=''){
				doJava.Params = {
					action:'enable_result',
					resultid: inResultCheck
				}
				var error = doJava.Post();
				if( error==1)
				{
					alert("Success Enabling the call result!");
					extendsJQuery.postContent();
				}
				else{ 
					alert("Failed Enabling the call result!"); 
					return false; 
				}
			}
			else{
				alert("Please select a row!")
			}
		}
		
		
		var UpdateCallResult = function(){
		
			var result_code_value = doJava.dom('result_code');
			var result_name_value = doJava.dom('result_name'); 
			var result_category_value = doJava.dom('result_category');
			var resultid = doJava.dom('resultid');
			var result_event = doJava.dom('result_event');
			var result_remind = doJava.dom('result_remind');
			var result_followup = doJava.dom('result_followup');
			var result_order = doJava.dom('result_order');
			
				if( result_code_value.value==''){ result_code_value.focus();}
				else if( result_name_value.value==''){ result_name_value.focus();}
				else if( result_category_value.value==''){ result_category_value.focus();}
				else if( result_event.value==''){ result_event.focus();}
				else if( result_remind.value==''){ result_remind.focus();}
				else{
					if( confirm('Do you want to Update this Result')){
						doJava.File = '../class/class.call.result.php' 
						doJava.Params = {
							action:'update_result',
							result_order : result_order.value,
							result_code: result_code_value.value,
							result_name: result_name_value.value,
							result_category: result_category_value.value,
							resultid:resultid.value,
							result_event:result_event.value,
							result_remind:result_remind.value,
							result_followup:result_followup.value
						}
				
						var error = doJava.Post();
							if( error==1){
								alert("Success Update the call result!");
								extendsJQuery.postContent();
							}
							else{ alert("Failed Update the call result !"); return false; }
					}
				}
		}
		
		var deleteResult = function(){
			
				var inResultCheck = doJava.checkedValue('chk_result');
				if( inResultCheck!=''){
					doJava.File = '../class/class.call.result.php' 
					doJava.Params = {
						action:'delete_result',
						resultid: inResultCheck
					}
					var error = doJava.Post();
						if( error==1)
						{
							alert("Success deleting the call result!");
							extendsJQuery.postContent();
						}
						else{ 
							alert("Failed deleting the call result!"); 
							return false; 
						}
				}
				else{
					alert("Please select a row!")
				}
		}
		var saveResult=function()
		{
			var result_code_value = doJava.dom('result_code');
			var result_name_value = doJava.dom('result_name'); 
			var result_category_value = doJava.dom('result_category');
			var result_event_value = doJava.dom('result_event');
			var result_remind_value = doJava.dom('result_remind');
			var result_followup_value = doJava.dom('result_followup');
			var result_order = doJava.dom('result_order');
			
			if( result_code_value.value==''){ result_code_value.focus();}
				else if( result_name_value.value==''){ result_name_value.focus();}
				else if( result_category_value.value==''){ result_category_value.focus();}
				else if( result_event.value==''){ result_event.focus();}
				else if( result_remind.value==''){ result_remind.focus();}
				else{
					if( confirm('Do you want to save this Result')){
						doJava.File = '../class/class.call.result.php' 
						doJava.Params = {
							action:'save_result',
							result_order : result_order.value,
							result_code: result_code_value.value,
							result_name: result_name_value.value,
							result_category: result_category_value.value,
							result_event: result_event_value.value,
							result_remind: result_remind_value.value,
							result_followup: result_followup_value.value
						}
					//	doJava.MsgBox();
						var error = doJava.Post();
							if( error==1){
								alert("Success saving the call result !");
								extendsJQuery.postContent();
							}
							else{ alert("Failed saving the call result!"); return true; }
					}
				}
		}
		
	</script>
	
	<!-- start : content -->
	
		<fieldset class="corner">
			<legend class="icon-callresult">&nbsp;&nbsp;Call Result </legend>	
				<div id="toolbars"></div>
				<div id="span_top_nav"></div>
				<div class="content_table"></div>
				<div id="pager"></div>
				<div id="ViewCmp"></div>
		</fieldset>	
		
	<!-- stop : content -->
	
	
	