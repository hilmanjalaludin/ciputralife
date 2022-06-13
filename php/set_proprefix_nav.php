<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
	$sql = " select b.ProductCode 
				 from t_gn_productprefixnumber a
				left join t_gn_product b on a.ProductId=b.ProductId";
	$NavPages -> setPage(10);			 
	$NavPages -> query($sql);
    $NavPages -> setWhere();
	//select * from t_gn_productprefixnumber a left join t_gn_formlayout b on a.ProductId=b.ProductId
	
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
				extTitle :[['Enable'],['Disable'] ,['Add Prefix'],['Form Product'],['Cancel'],['Search']],
				extMenu  :[['enableResult'],['disableResult'],['addResult'],['FormProduct'],['cancelResult'],['searchResult']],
				extIcon  :[['accept.png'],['cancel.png'], ['add.png'],['layout.png'],['cancel.png'],['zoom.png']],
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
			custnav:'set_proprefix_nav.php',
			custlist:'set_proprefix_list.php'
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
			doJava.File = '../class/class.pro.prefix.php' 
			doJava.Params ={ action:'tpl_add' }	
			doJava.Load('span_top_nav');
		}
		
		
		var FormProduct = function()
		{
			var inResultCheck = doJava.checkedValue('chk_result');
			var arrinResultCheck = inResultCheck.split(',');
			if (inResultCheck!='')
			{
				if( arrinResultCheck.length==1 )
				{
					doJava.File = '../class/class.pro.prefix.php' 
					doJava.Params = {
							action :'get_tpl_form',
							prefix_id : inResultCheck
						}
					doJava.Load('span_top_nav');	
				}
				else{
					alert('Please select one rows !')
				}
			}
			else{
				alert('No Customer Selected !')
			}
		}
		
		
		var saveForm = function()
		{
			var ProductId = doJava.dom('product_id').value;
			var FormProductId = doJava.dom('new_form').value;
			if( confirm('Do you want to save this form ?'))
			{
				doJava.File = '../class/class.pro.prefix.php' 
				doJava.Params = {
						action :'save_tpl_form',
						ProductId : ProductId,  
						FormProductId: FormProductId
					}
				var message_error = doJava.eJson();
				//alert(message_error.result);
				//return false;
					if( message_error.result )
					{
						alert('Success, Save form product !'); 
						$('#main_content').load('set_proprefix_nav.php');						
					}
					else{
						alert('Failed, Save form product !'); 
						return false;
					}
			}
		}
		
		
		var disableResult = function(){
			var inResultCheck = doJava.checkedValue('chk_result');
				if( inResultCheck!=''){
				doJava.File = '../class/class.pro.prefix.php' 
		
		
					doJava.Params = {
						action:'disable_prefix',
						resultid: inResultCheck
					}
					var error = doJava.Post();
						if( error==1)
						{
							alert("Succeeded, Disable Prefix  !");
							extendsJQuery.postContent();
						}
						else{ 
							alert("Failed, Disable Prefix !"); 
							return false; 
						}
				}
				else{
					alert("Please select Rows !")
				}
		}
		
		var enableResult = function(){
			var inResultCheck = doJava.checkedValue('chk_result');
				if( inResultCheck!=''){
				doJava.File = '../class/class.pro.prefix.php' 
					doJava.Params = {
						action:'enable_prefix',
						resultid: inResultCheck
					}
					var error = doJava.Post();
					//alert(error);
						if( error==1)
						{
							alert("Succeeded, Enable Prefix  !");
							extendsJQuery.postContent();
						}
						else{ 
							alert("Failed, Enable Prefix !"); 
							return false; 
						}
				}
				else{
					alert("Please select Rows !")
				}
		}
		
		var editResult = function(){
		doJava.File = '../class/class.pro.prefix.php' 
		
		
			doJava.Params ={ action:'tpl_edit' }	
			doJava.Load('span_top_nav');
		}
		
		var deleteResult = function(){
		
				var inResultCheck = doJava.checkedValue('chk_result');
				if( inResultCheck!=''){
				doJava.File = '../class/class.pro.prefix.php' 
					doJava.Params = {
						action:'delete_result',
						resultid: inResultCheck
					}
					var error = doJava.Post();
						if( error==1)
						{
							alert("Succeeded, Delete Prefix !");
							extendsJQuery.postContent();
						}
						else{ 
							alert("Failed, Delete Prefix !"); 
							return false; 
						}
				}
				else{
					alert("Please select Rows !")
				}
		}
		var savePrefix = function(){
			var result_head_value = doJava.dom('product_id');
			var result_prefix_value = doJava.dom('result_name'); 
			var result_code_value = doJava.dom('result_code');
			var result_name_value = doJava.dom('result_name'); 
			var result_category_value = doJava.dom('status_active');
				
				if( result_head_value.value=='' ){ result_head_value.focus(); }
				else if( result_prefix_value.value==''){ result_prefix_value.focus();}
				else if( result_code_value.value==''){ result_code_value.focus();}
				else if( result_name_value.value==''){ result_name_value.focus();}
				else if( result_category_value.value==''){ result_category_value.focus();}
				else{
					if( confirm('Do you want to save this Prefix')){
					doJava.File = '../class/class.pro.prefix.php' 
						doJava.Params = {
							action:'save_prefix',
							prefix_head: result_head_value.value,
							prefix_prefix: result_prefix_value.value,
							prefix_code: result_code_value.value,
							prefix_name: result_name_value.value,
							prefix_status: result_category_value.value
						}
					//	doJava.MsgBox();
						var error = doJava.Post();
							if( error==1){
								alert("Succeeded, Save Prefix !");
								extendsJQuery.postContent();
							}
							else{ alert("Failed, Save Prefix !"); return false; }
					}
				}
		}
		
	</script>
	
	<!-- start : content -->
	
		<fieldset class="corner">
			<legend class="icon-callresult">&nbsp;&nbsp;Product Prefix </legend>	
				<div id="toolbars"></div>
				<div id="span_top_nav"></div>
				<div class="content_table"></div>
				<div id="pager"></div>
				<div id="ViewCmp"></div>
		</fieldset>	
		
	<!-- stop : content -->
	
	
	