<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
	$sql = " SELECT a.*, b.CollCategoryName  
			 FROM coll_subcategory_collmon a  
			 LEFT JOIN coll_category_collmon  b ON a.CategoryId=b.CollCategoryId ";

	
	$NavPages -> setPage(10);			 
	$NavPages -> query($sql);
	$NavPages -> setWhere();
	$NavPages -> OrderBy("a.SubCategoryId","ASC");
	
	/** user group **/
	
	
?>
	<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
	<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
	<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
  	<script type="text/javascript">
		
		$(function(){
			$('#toolbars').extToolbars({
				extUrl   :'../gambar/icon',
				extTitle :[['Enable'],['Disable'] ,['Add'],['Edit'],['Cancel']],
				extMenu  :[['enableCollmon'],['disableCollmon'],['addCollmon'],['editCollmon'],['cancelCollmon']],
				extIcon  :[['accept.png'],['cancel.png'], ['add.png'],['calendar_edit.png'],['cancel.png']],
				extText  :true,
				extInput :false,
				extOption: [{
						render:15,
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
			custnav : 'set_collmon_nav.php',
			custlist : 'set_collmon_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,'')
		extendsJQuery.postContentList();
		
		var cancelCollmon=function(){
			doJava.dom('span_top_nav').innerHTML='';
		}
		
		var addCollmon = function(){
			doJava.File = '../class/class.collmon.setup.php' 
			doJava.Params ={ action:'tpl_add_collmon' }	
			doJava.Load('span_top_nav');
		}
		
	/* edit category ****/
	
		var editCollmon = function()
		{
			var inResultCheck = doJava.checkedValue('chk_category');
			var inArray = inResultCheck.split(',');
			
			if( inResultCheck!=''){	
			if( inArray.length>1){
				alert('Please Select One Rows');
			}
			else{
				doJava.File = '../class/class.collmon.setup.php'
				//alert(inArray);
				doJava.Params ={ 
					action:'tpl_edit_collmon',
					collmonid:inArray
				}
				doJava.Load('span_top_nav');
			}
		  }
		  else { alert('Please select rows !'); }
		}
		
	/* * delete **/	
		
		var DeleteCollmon = function(){
			
				var inResultCheck = doJava.checkedValue('chk_category');
				if( inResultCheck!='')
				{
					doJava.File = '../class/class.collmon.setup.php' 
					doJava.Params = {
						action:'delete_collmon',
						collmonid: inResultCheck
					}
					var error= doJava.eJson();
						if( error)
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
	
		var disableCollmon=function()
		{
			doJava.File = '../class/class.collmon.setup.php' 
			var inResultCheck = doJava.checkedValue('chk_category');
			if( inResultCheck!='')
			{
				doJava.Params = {
					action:'disable_collmon',
					resultid: inResultCheck
				}
				var error= doJava.eJson();
				if( error.result==1)
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
	
		var enableCollmon=function()
		{
			doJava.File = '../class/class.collmon.setup.php'  
			var inResultCheck = doJava.checkedValue('chk_category');
			if( inResultCheck!=''){
				doJava.Params = {
					action:'enable_collmon',
					resultid: inResultCheck
				}
				var error= doJava.eJson();
				//alert(error.result);
				if( error.result==1)
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
		
		
		
		var UpdateCollmon = function()
		{
			var category_collmon	= doJava.dom('category_collmon').value;
			var sub_category		= doJava.dom('sub_category').value;
			var min_number			= doJava.dom('min_number').value;
			var max_number			= doJava.dom('max_number').value;
			var step_number			= doJava.dom('step_number').value;
			var collmonid			= doJava.dom('collmonid').value;
			
			if( category_collmon==''){ alert('Input Category Code')}
			else if( sub_category==''){  alert('Input Category Name') }
			else
			{
				if( confirm('Do you want to Update this Category')){
						doJava.File = '../class/class.collmon.setup.php' 
						doJava.Params = 
						{
							action			:'update_collmon',
							collmonid		: collmonid,
							category_collmon: category_collmon,
							sub_category	: sub_category,
							min_number		: min_number,
							max_number		: max_number,
							step_number		: step_number	
						}
						
						var error = doJava.eJson();
						alert(error);
						if( error==1)
						{
							alert("Success saving the Category result !");
							extendsJQuery.postContent();
						}
						else{ alert("Failed saving the Category result!"); return true; }
				}
			}	
		}
	///////////////////////

		var saveCollmonSetup = function()
		{
			var category_collmon	= doJava.dom('category_collmon').value;
			var sub_category		= doJava.dom('sub_category').value;
			var min_number			= doJava.dom('min_number').value;
			var max_number			= doJava.dom('max_number').value;
			var step_number			= doJava.dom('step_number').value;
			
			doJava.File = '../class/class.collmon.setup.php'
			//alert(category_collmon)
			doJava.Params = {
				action			: 'save_collmon',
				category_collmon: category_collmon,
				sub_category	: sub_category,
				min_number		: min_number,
				max_number		: max_number,
				step_number		: step_number				
			}
			var error = doJava.Post();
			if( error==1)
			{
				alert("Success saving the Collmon Setup !");
				extendsJQuery.postContent();
			}
			else{ alert("Failed saving the Collmon Setup!"); return true; }
		}
	
	/////////////////////// 
		
	</script>
	
	<!-- start : content -->
	
		<fieldset class="corner">
			<legend class="icon-callresult">&nbsp;&nbsp;Collmon Setting </legend>	
				<div id="toolbars"></div>
				<div id="span_top_nav"></div>
				<div class="content_table"></div>
				<div id="pager"></div>
				<div id="ViewCmp"></div>
		</fieldset>	
		
	<!-- stop : content -->
	
	
	