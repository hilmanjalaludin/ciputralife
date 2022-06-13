<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
	$sql = "select * from t_lk_callreasoncategory a ";
	
	$NavPages -> setPage(10);			 
	$NavPages -> query($sql);
	$NavPages -> setWhere();
	$NavPages -> OrderBy("a.CallReasonCategoryOrder","ASC");
	
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
				extTitle :[['Enable'],['Disable'] ,['Add'],['Edit'],['Delete'],['Cancel']],
				extMenu  :[['enableResult'],['disableResult'],['addResult'],['editResult'],['DeleteCategory'],['cancelResult']],
				extIcon  :[['accept.png'],['cancel.png'], ['add.png'],['calendar_edit.png'],['delete.png'],['cancel.png']],
				extText  :true,
				extInput :false,
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
			custnav : 'set_result_category_nav.php',
			custlist : 'set_result_category_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,'')
		extendsJQuery.postContentList();
		
		var cancelResult=function(){
			doJava.dom('span_top_nav').innerHTML='';
		}
		
		var addResult = function(){
			doJava.File = '../class/class.call.result.php' 
			doJava.Params ={ action:'tpl_add_category' }	
			doJava.Load('span_top_nav');
		}
		
	/* edit category ****/
	
		var editResult = function()
		{
			var inResultCheck = doJava.checkedValue('chk_category');
			var inArray = inResultCheck.split(',');
			
			if( inResultCheck!=''){	
			if( inArray.length>1){
				alert('Please Select One Rows');
			}
			else{
				doJava.File = '../class/class.call.result.php' 
				doJava.Params ={ 
					action:'tpl_edit_category',
					resultid:inArray
				}	
				doJava.Load('span_top_nav');
			}
		  }
		  else { alert('Please select rows !'); }
		}
		
	/* * delete **/	
		
		var DeleteCategory = function(){
			
				var inResultCheck = doJava.checkedValue('chk_category');
				if( inResultCheck!='')
				{
					doJava.File = '../class/class.call.result.php' 
					doJava.Params = {
						action:'delete_category',
						resultid: inResultCheck
					}
					var error= doJava.eJson();
						if( error.result)
						{
							alert("Success, deleting the Category result!");
							extendsJQuery.postContent();
						}
						else{ 
							alert("Failed, deleting the Category result!"); 
							return false; 
						}
				}
				else{
					alert("Please select a row!")
				}
		}
	
	/* disabled **/
	
		var disableResult=function()
		{
			doJava.File = '../class/class.call.result.php' 
			var inResultCheck = doJava.checkedValue('chk_category');
			if( inResultCheck!='')
			{
				doJava.Params = {
					action:'disable_category',
					resultid: inResultCheck
				}
				var error= doJava.eJson();
				if( error.result)
				{
					alert("Success disabling the Category result!");
					extendsJQuery.postContent();
				}
				else{ 
					alert("Failed disabling the Category result!"); 
					return false; 
				}
			}
			else{
				alert("Please select a row!")
			}
		}
		
	/* enable **/
	
		var enableResult=function()
		{
			doJava.File = '../class/class.call.result.php' 
			var inResultCheck = doJava.checkedValue('chk_category');
			if( inResultCheck!=''){
				doJava.Params = {
					action:'enable_category',
					resultid: inResultCheck
				}
				var error= doJava.eJson();
				if( error.result)
				{
					alert("Success Enabling the Category result!");
					extendsJQuery.postContent();
				}
				else{ 
					alert("Failed Enabling the Category result!"); 
					return false; 
				}
			}
			else{
				alert("Please select a row!")
			}
		}
		
		
		var UpdateCatgoryResult = function()
		{
			var category_id 	= doJava.dom('category_id').value;
			var category_code 	= doJava.dom('category_code').value;
			var category_name 	= doJava.dom('category_name').value;	 
			var category_status	= doJava.dom('category_status').value;
			var category_order 	= doJava.dom('category_order').value;
			
			if( category_code==''){ alert('Input Category Code')}
			else if( category_name==''){  alert('Input Category Name') }
			else
			{
				if( confirm('Do you want to Update this Category')){
						doJava.File = '../class/class.call.result.php' 
						doJava.Params = 
						{
							action:'update_category',
							category_id : category_id,
							category_code : category_code,
							category_name : category_name,	 
							category_status : category_status,
							category_order 	: category_order
						}
				
						var error = doJava.eJson();
						if( error.result==1)
						{
							alert("Success saving the Category result !");
							extendsJQuery.postContent();
						}
						else{ alert("Failed saving the Category result!"); return true; }
				}
			}	
		}
		
	///////////////////////
	
		var SaveCatgoryResult = function()
		{	
			var category_code 	= doJava.dom('category_code').value;
			var category_name 	= doJava.dom('category_name').value;	 
			var category_status	= doJava.dom('category_status').value;
			var category_order 	= doJava.dom('category_order').value;
			
				if( category_code==''){ alert('Input Category Code')}
				else if( category_name==''){  alert('Input Category Name') }
				else{
					if( confirm('Do you want to save this Category Result ?')){
						doJava.File = '../class/class.call.result.php' 
						doJava.Params = {
							action:'save_category',
							category_code : category_code,
							category_name : category_name,	 
							category_status : category_status,
							category_order 	: category_order
						}
						
						var error = doJava.eJson();
							if( error.result==1){
								alert("Success saving the Category result !");
								extendsJQuery.postContent();
							}
							else{ alert("Failed saving the Category result!"); return true; }
					}
				}
		}
		
	</script>
	
	<!-- start : content -->
	
		<fieldset class="corner">
			<legend class="icon-callresult">&nbsp;&nbsp;Call Category Result </legend>	
				<div id="toolbars"></div>
				<div id="span_top_nav"></div>
				<div class="content_table"></div>
				<div id="pager"></div>
				<div id="ViewCmp"></div>
		</fieldset>	
		
	<!-- stop : content -->
	
	
	