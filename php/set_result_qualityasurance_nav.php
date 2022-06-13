<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
	$sql = "select * from t_lk_aprove_status a ";
	
	$NavPages -> setPage(10);			 
	$NavPages -> query($sql);
	$NavPages -> setWhere();
	//$NavPages -> OrderBy("a.ApproveId","ASC");
	
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
		
		var datas=
		{
			order_by : '<?php echo $db -> escPost('order_by');?>',
			type	 : '<?php echo $db -> escPost('type');?>'
		}
			extendsJQuery.totalPage = <?php echo $NavPages ->getTotPages(); ?>;
			extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
		
	/* assign navigation filter **/
		
		var navigation = {
			custnav : 'set_result_qualityasurance_nav.php',
			custlist : 'set_result_qualityasurance_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContentList();
		
		var cancelResult=function(){
			doJava.dom('span_top_nav').innerHTML='';
		}
		
		var addResult = function()
		{
			doJava.File = '../class/class.call.result.php' 
			doJava.Params ={ action:'tpl_add_quality' }	
			doJava.Load('span_top_nav');
		}
		
	/* edit category ****/
	
		var editResult = function()
		{
			var inResultCheck = doJava.checkedValue('chk_quality');
			var inArray = inResultCheck.split(',');
			
			if( inResultCheck!=''){	
			if( inArray.length>1){
				alert('Please Select One Rows');
			}
			else{
				doJava.File = '../class/class.call.result.php' 
				doJava.Params ={ 
					action:'tpl_edit_quality',
					resultid:inArray
				}	
				doJava.Load('span_top_nav');
			}
		  }
		  else { alert('Please select rows !'); }
		}
		
	/* * delete **/	
		
		var DeleteCategory = function(){
			
				var inResultCheck = doJava.checkedValue('chk_quality');
				if( inResultCheck!='')
				{
					doJava.File = '../class/class.call.result.php' 
					doJava.Params = {
						action:'delete_quality',
						resultid: inResultCheck
					}
					var error= doJava.eJson();
						if( error.result)
						{
							alert("Success, deleting the Quality result!");
							extendsJQuery.postContent();
						}
						else{ 
							alert("Failed, deleting the Quality result!"); 
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
			var inResultCheck = doJava.checkedValue('chk_quality');
			if( inResultCheck!='')
			{
				doJava.Params = {
					action:'disable_quality',
					resultid: inResultCheck
				}
				var error= doJava.eJson();
				if( error.result)
				{
					alert("Success disabling the Quality result!");
					extendsJQuery.postContent();
				}
				else{ 
					alert("Failed disabling the Quality result!"); 
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
			var inResultCheck = doJava.checkedValue('chk_quality');
			if( inResultCheck!=''){
				doJava.Params = {
					action:'enable_quality',
					resultid: inResultCheck
				}
				var error= doJava.eJson();
				if( error.result)
				{
					alert("Success Enabling the Quality result!");
					extendsJQuery.postContent();
				}
				else{ 
					alert("Failed Enabling the Quality result!"); 
					return false; 
				}
			}
			else{
				alert("Please select a row!")
			}
		}
		
		
		var UpdateQualityResult = function()
		{
			var quality_id 	= doJava.dom('quality_id').value;
			var quality_code 	= doJava.dom('quality_code').value;
			var quality_name 	= doJava.dom('quality_name').value;	 
			var quality_status	= doJava.dom('quality_status').value;
			var quality_eskalasi= doJava.dom('quality_eskalasi').value; 
			var quality_levels	 = doJava.dom('quality_level_eskalasi').value; 
			
			if( quality_code==''){ alert('Input Quality Code')}
			else if( quality_name==''){  alert('Input Quality Name') }
			else
			{
				if( confirm('Do you want to Update this Quality')){
						doJava.File = '../class/class.call.result.php' 
						doJava.Params = 
						{
							action:'update_quality',
							quality_id : quality_id,
							quality_code : quality_code,
							quality_name : quality_name,	 
							quality_status : quality_status,
							quality_eskalasi : quality_eskalasi,
							quality_levels : quality_levels
						}
				
						var error = doJava.eJson();
						if( error.result==1)
						{
							alert("Success saving the Quality result !");
							extendsJQuery.postContent();
						}
						else{ alert("Failed saving the Quality result!"); return true; }
				}
			}	
		}
		
	///////////////////////
	
		var SaveQualityResult = function()
		{	
			var quality_code 	 = doJava.dom('quality_code').value;
			var quality_name 	 = doJava.dom('quality_name').value;	 
			var quality_status	 = doJava.dom('quality_status').value;
			var quality_eskalasi = doJava.dom('quality_eskalasi').value; 
			var quality_levels	 = doJava.dom('quality_level_eskalasi').value; 
			
			
				if( quality_code==''){ alert('Input Quality Code')}
				else if( quality_name==''){  alert('Input Quality Name') }
				else{
					if( confirm('Do you want to save this Quality Result ?')){
						doJava.File = '../class/class.call.result.php' 
						doJava.Params = {
							action:'save_quality',
							quality_code : quality_code,
							quality_name : quality_name,	 
							quality_status : quality_status,
							quality_eskalasi : quality_eskalasi,
							quality_levels : quality_levels
						}
						
						var error = doJava.eJson();
							if( error.result==1){
								alert("Success saving the Quality result !");
								extendsJQuery.postContent();
							}
							else{ alert("Failed saving the Quality result!"); return true; }
					}
				}
		}
		
	</script>
	
	<!-- start : content -->
	
		<fieldset class="corner">
			<legend class="icon-callresult">&nbsp;&nbsp;Quality Asurance Result </legend>	
				<div id="toolbars"></div>
				<div id="span_top_nav"></div>
				<div class="content_table"></div>
				<div id="pager"></div>
				<div id="ViewCmp"></div>
		</fieldset>	
		
	<!-- stop : content -->
	
	
	