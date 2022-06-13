<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
	$sql = "select b.ProductId, b.ProductCode, b.ProductName 
				from t_gn_productplan a 
				left join t_gn_product b on a.ProductId=b.ProductId
				group by a.ProductId ";
				
	
	$NavPages -> setPage(10);			 
	$NavPages -> query($sql);
	
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
				extTitle :[['Enable'],['Disable'],['Show Product Plan'],['Edit Product Plan'],['Cancel']],
				extMenu  :[['enableProduct'],['disableProduct'],['editBenefit'],['searchBenefit'],['cancelBenefit']],
				extIcon  :[['accept.png'],['cancel.png'],['calendar_edit.png'],['note_edit.png'],['cancel.png']],
				extText  :true,
				extInput :false,
				extOption:[{
						render:1,
						type:'text',
						id:'v_benefit', 	
						name:'v_benefit',
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
			custnav:'mgt_viewplan_nav.php',
			custlist:'mgt_viewplan_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,'')
		extendsJQuery.postContentList();
		
		var enableProduct=function(){
			var product = doJava.checkedValue('chk_benfit');
			
			if (product!=''){
				doJava.File = '../class/class.product.plan.php' 
				doJava.Params ={
					action  : 'enable_product',
					product : product
				}
				var error = doJava.Post();
				if( error==1)
				{
					alert("Succeeded, Enable Product !");
					extendsJQuery.postContent();
				}
				else{ 
					alert("Failed, Enable Product !"); 
					return false; 
				}
			}
			else{
				alert('No Product Selected');
				return false;
			}
			
		}
		
		var disableProduct=function(){
			var product = doJava.checkedValue('chk_benfit');
			
			if (product!=''){
				doJava.File = '../class/class.product.plan.php' 
				doJava.Params ={
					action  : 'disable_product',
					product : product
				}
				var error = doJava.Post();
				if( error==1)
				{
					alert("Succeeded, Enable Product !");
					extendsJQuery.postContent();
				}
				else{ 
					alert("Failed, Enable Product !"); 
					return false; 
				}
			}
			else{
				alert('No Product Selected');
				return false;
			}
		}
		
		var cancelBenefit=function(){
			doJava.File = '../class/class.product.plan.php' 
			doJava.dom('span_top_nav').innerHTML='';
		}
		
		var searchBenefit = function(){
			doJava.File = '../class/class.product.plan.php' 
			doJava.Params ={ action:'get_list_product' }	
			doJava.Load('span_top_nav');
		}
		
		var getListFilterPlan = function(object)
		{
			doJava.File = '../class/class.product.plan.php' 
			if( object.value!=''){
				doJava.Params = {
					action:'get_list_plan',
					productid:object.value
				}
				
				doJava.Load('html_cb_plan');
				
				get_list_age(object.value);
				doJava.dom('edit_premium_group').innerHTML='';
			}	
		
		}
		
		var editBenefit = function()
		{
			doJava.File = '../class/class.product.plan.php' 
			var chk_benfit = doJava.checkedValue('chk_benfit');
			var chk_benfit_value = chk_benfit.split(',');
			
			if( chk_benfit!='')
			{
				if( chk_benfit_value.length > 1){
					alert('Please select a row!');
				}
				else{
					$('#main_content').load('mgt_detail_plan.php?productid='+chk_benfit_value);
				}
			}
			else{
				alert("Please select a row!");
			}	
		}
		
		var showEditPremium =function()
		{
			doJava.File = '../class/class.product.plan.php' 
			var product_id = doJava.dom('filter_product_id').value;
			var grooup_id = doJava.dom('filter_group_id').value;
			var paymode_id = doJava.dom('filter_paymode_id').value;
			var filter_age_id = doJava.dom('filter_age_id').value;
			
			var planid = doJava.dom('filter_plan_id').value;
				if( product_id!=''){
					doJava.Params = {
						action		:'view_edit_content',
						product_id	: product_id,
						grooup_id 	: grooup_id,
						paymode_id	: paymode_id,
						planid 		: planid,
						ageid		:filter_age_id
					}
					
					doJava.Load('edit_premium_group');
				}
				
				
		}
		
		var updatePremi = function(planId)
		{
			doJava.File = '../class/class.product.plan.php' 
			if( planId!=''){
				var NewPremi = doJava.dom('new_premium_value').value;
				if( NewPremi !=''){
					doJava.Params  ={
						action:'update_premi',
						newpremi: NewPremi,
						planId:planId
					}
				var error = doJava.Post();
					if( error==1 ){
						alert("Success updating the premium!");
						
								var product_id = doJava.dom('filter_product_id').value;
								var grooup_id = doJava.dom('filter_group_id').value;
								var paymode_id = doJava.dom('filter_paymode_id').value;
								var filter_age_id = doJava.dom('filter_age_id').value;
								
								var planid = doJava.dom('filter_plan_id').value;
									if( product_id!=''){
										doJava.Params = {
											action		:'view_edit_content',
											product_id	: product_id,
											grooup_id 	: grooup_id,
											paymode_id	: paymode_id,
											planid 		: planid,
											ageid		:filter_age_id
										}
										
										doJava.Load('edit_premium_group');
									}
					}
					else{
						alert("Failed updating the premium!");
					}
				}
				else{
					alert('Please insert the premi!');
				}	
			}
		}
		
		var get_list_age = function(productid)
		{
			doJava.File = '../class/class.product.plan.php' 
			doJava.Params ={
				action:'get_list_age',
				productid:productid
			}
			doJava.Load('html_cb_age');
		}
		
		var updateBenfit = function()
		{
			doJava.File = '../class/class.product.plan.php' 	
			var benefit_item_product = doJava.dom('cb_benefit_product_id'); 
			var benefit_item_desc  = doJava.dom('benefit_description');
			var benefit_item_plan  = doJava.dom('cb_benefit_plan');
			var benefit_item_value  = doJava.dom('benefit_product');
			var benefit_status    =  doJava.dom('benefit_status');
			var rows_id			 = doJava.dom('rows_id');

				if( benefit_item_product.value==''){
					alert('Info, Product ID is empty');
					benefit_item_product.focus();
					return false;
				}
				else if(benefit_item_desc.value==''){
					alert('Info, Benefit Description is empty');
					benefit_item_desc.focus();
					return false;
				}
				else if(benefit_item_plan.value==''){
					alert('Info, Product Plan is empty');
					benefit_item_plan.focus();
					return false;
				}
				else if(benefit_item_value.value==''){
					alert('Info, Product Benefit is empty');
					benefit_item_value.focus();
					return false;
				}
				else if( benefit_status.value==''){
					alert('Info, Benefit Status is empty');
					benefit_status.focus();
					return false;
				}
				else{
					if( confirm('Do you want to save this benefit?'))
					{
						doJava.Method = "POST";
						doJava.Params = {
							action:'update_benefit',
							benefit_item_product : benefit_item_product.value,
							benefit_item_desc : benefit_item_desc.value,
							benefit_item_plan : benefit_item_plan.value,
							benefit_item_value : benefit_item_value.value,
							benefit_status: benefit_status.value,
							rowsid:rows_id.value
						}
						var error = doJava.Post();
						if( error==1){
							alert("Success updating product benefit!");
							extendsJQuery.postContent();
						}else
							alert("Failed updating product benefit!");
					}
				}
		}
		
		var saveBenefit= function()
		{
			doJava.File = '../class/class.product.plan.php' 
			var benefit_item_product = doJava.dom('cb_benefit_product_id'); 
			var benefit_item_desc  = doJava.dom('benefit_description');
			var benefit_item_plan  = doJava.dom('cb_benefit_plan');
			var benefit_item_value  = doJava.dom('benefit_product');
			var benefit_status    =  doJava.dom('benefit_status');

				if( benefit_item_product.value==''){
					alert('Info, Product ID is empty');
					benefit_item_product.focus();
					return false;
				}
				else if(benefit_item_desc.value==''){
					alert('Info, Benefit description is empty');
					benefit_item_desc.focus();
					return false;
				}
				else if(benefit_item_plan.value==''){
					alert('Info, Product Plan is empty');
					benefit_item_plan.focus();
					return false;
				}
				else if(benefit_item_value.value==''){
					alert('Info, Product Benefit is empty');
					benefit_item_value.focus();
					return false;
				}
				else if( benefit_status.value==''){
					alert('Info, Benefit Status is empty');
					benefit_status.focus();
					return false;
				}
				else{
					if( confirm('Do you want to save this benefit?'))
					{
						doJava.Method = "POST";
						doJava.Params = {
							action:'save_benefit',
							benefit_item_product : benefit_item_product.value,
							benefit_item_desc : benefit_item_desc.value,
							benefit_item_plan : benefit_item_plan.value,
							benefit_item_value : benefit_item_value.value,
							benefit_status: benefit_status.value
						}
						var error = doJava.Post();
						if( error==1){
							alert("Success saving the product benefit!");
							extendsJQuery.postContent();
						}else
							alert("Failed saving the product benefit!");
					}
				}
			
		}
		
		
		
	</script>
	
	<!-- start : content -->
	
		<fieldset class="corner" >
			<legend class="icon-benefit">&nbsp;&nbsp;View Product Plan  </legend>	
				<div id="toolbars"></div>
				<div id="span_top_nav"></div>
				<div id="span_edit_nav"></div>
				<div class="content_table" ></div>
				<div id="pager" ></div>
				
		</fieldset>	
		
	<!-- stop : content -->
	
	
	