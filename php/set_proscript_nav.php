<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
	$sql = " select 
				b.ProductCode,
				b.ProductName,
				c.id,
				c.full_name,
				a.ScriptFileName,
				a.ScriptUpload,
				a.UploadDate
			from t_gn_productscript a
			left join t_gn_product b on a.ProductId=b.ProductId
			left join tms_agent c on a.UploadBy=c.UserId ";
					
	
	$NavPages -> setPage(10);			 
	$NavPages -> query($sql);
    
	//$filter = " and a.ScriptFlagStatus=1";
	
	//$NavPages -> setWhere($filter);
	
	
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
				extTitle :[['Enable'],['Disable'] ,['Add Script'],['Delete Script'],['Cancel'],['Search']],
				extMenu  :[['enableResult'],['disableResult'],['addResult'],['deleteResult'],['cancelResult'],['searchResult']],
				extIcon  :[['accept.png'],['cancel.png'], ['add.png'],['delete.png'],['cancel.png'], ['zoom.png']],
				extText  :true,
				extInput :true,
				extOption: [{
						render:5,
						type:'text',
						id:'v_product_prefix', 	
						name:'v_product_prefix',
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
			custnav:'set_proscript_nav.php',
			custlist:'set_proscript_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,'')
		extendsJQuery.postContentList();
		
		
		var searchResult = function(){
			alert(doJava.dom('v_result').value);
			
		}
		var cancelResult=function(){
			doJava.File = '../class/class.pro.script.php' 
			doJava.dom('span_top_nav').innerHTML='';
		}
		
		var addResult = function(){
			doJava.File = '../class/class.pro.script.php' 
			doJava.Params ={ action:'tpl_add' }	
			doJava.Load('span_top_nav');
		}
		
		
		var disableResult = function(){
			doJava.File = '../class/class.pro.script.php' 
			var inResultCheck = doJava.checkedValue('chk_result');
				if( inResultCheck!='')
				{
					doJava.Params = 
					{
						action:'disable_prefix',
						resultid: inResultCheck
					}
					var error = doJava.Post();
						if( error==1)
						{
							alert("Succeeded, Disable Script  !");
							extendsJQuery.postContent();
						}
						else{ 
							alert("Failed, Disable Script !"); 
							return false; 
						}
				}
				else{
					alert("Please select Rows !")
				}
		}
		
		var enableResult = function()
		{
			doJava.File = '../class/class.pro.script.php' 
			var inResultCheck = doJava.checkedValue('chk_result');
				if( inResultCheck!='')
				{
					doJava.Params = 
					{
						action:'enable_prefix',
						resultid: inResultCheck
					}
					var error = doJava.Post();
						if( error==1)
						{
							alert("Succeeded, Enable Script  !");
							extendsJQuery.postContent();
						}
						else{ 
							alert("Failed, Enable Script !"); 
							return false; 
						}
				}
				else{
					alert("Please select Rows !")
				}
		}
		
		var editResult = function()
		{
			doJava.File = '../class/class.pro.script.php' 
			doJava.Params ={ action:'tpl_edit' }	
			doJava.Load('span_top_nav');
		}
		
		var deleteResult = function()
		{
				doJava.File = '../class/class.pro.script.php' 
				var inResultCheck = doJava.checkedValue('chk_result');
				if( inResultCheck!=''){
					doJava.Params = 
					{
						action:'delete_result',
						resultid: inResultCheck
					}
					var error = doJava.Post();
						if( error==1)
						{
							alert("Succeeded, Delete Script !");
							extendsJQuery.postContent();
						}
						else{ 
							alert("Failed, Delete Script !"); 
							return false; 
						}
				}
				else{
					alert("Please select Rows !")
				}
		}
		/* if click aggree confirmation before **/
	
	var actionUpload =function()
	{
		
	}		
	
 /* if click prosess button by user **/
 
	var proses=function()
	{
		Ext.Ajax({
			url: '../class/class.pro.script.php',
			method:'POST',
			param :{
				act_file_name 	 : Ext.Cmp('fileToupload').getValue(),
				action 			 : 'upload_script',
				product_id 		 : Ext.Cmp('product_id').getValue(),
				script_init_name : Ext.Cmp('script_init_name').getValue()
			},
			complete:function(e){
				alert(e.target.responseText)
				extendsJQuery.postContent();
			}
		}).upload();
	}	
	</script>
	
	<!-- start : content -->
	
		<fieldset class="corner">
		<!--<#?php
			echo $NavPages->query;
		 ?>-->
			<legend class="icon-callresult">&nbsp;&nbsp;Product Script Upload </legend>	
				<div id="toolbars"></div>
				<div id="span_top_nav"></div>
				<div class="content_table"></div>
				<div id="pager"></div>
				<div id="ViewCmp"></div>
		</fieldset>	
		
	<!-- stop : content -->
	
	
	