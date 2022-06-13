<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
	$sql = "select a.ProductPlanBenefitId from t_gn_productplanbenefit a 
				left join t_gn_product b on a.ProductId=b.ProductId 
				LEFT JOIN t_gn_campaignproduct f on b.ProductId=f.ProductId
				left join t_gn_campaign c on f.CampaignId=c.CampaignId
				left join t_gn_productplan d on a.ProductPlan=d.ProductPlan 
				left join t_lk_producttype e on b.ProductTypeId=e.ProductTypeId ";
	
	$NavPages -> setPage(15);			 
	$NavPages -> query($sql);
	$NavPages -> setWhere();
	$NavPages -> GroupBy("a.ProductPlanBenefitId");
	$NavPages -> OrderBy("a.ProductPlanBenefitId");

	
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
				extTitle :[['Add Product Benefit'],['Edit Product Benefit'],['Delete'],['Cancel']],
				extMenu  :[['addBenefit'],['editBenefit'],['DeleteBenefit'],['cancelBenefit']],
				extIcon  :[['add.png'],['calendar_edit.png'],['delete.png'],['cancel.png']],
				extText  :true,
				extInput :false,
				extOption:[{
						render:2,
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
			custnav:'set_benefit_nav.php',
			custlist:'set_benefit_list.php'
		}
		
	/* assign show list content **/
		
		extendsJQuery.construct(navigation,'')
		extendsJQuery.postContentList();
		
	/* assign show list content **/
	
		var DeleteBenefit = function()
		{
			var chk_benfit = doJava.checkedValue('chk_benfit');
			if( chk_benfit!='' )
			{
				doJava.File = '../class/class.benefit.php' 
				doJava.Params ={ 
					action:'delete_benefit', 
					benefit_id : chk_benfit	
				}		
				var error_message = doJava.eJson();
					if( error_message.result )
					{
						alert('Success, Delete Product Benefit');
						extendsJQuery.postContent();
					}
					else{
						alert('Failed, Delete Product Benefit');
						return false;
					}
			}
			else{
				alert('Please select a rows !')
			}
		
		}
		
	// **************************************** //	
	
		var cancelBenefit=function()
		{
			doJava.File = '../class/class.benefit.php' 
			doJava.dom('span_top_nav').innerHTML='';
		}
		
	// **************************************** //	
		
		var addBenefit = function()
		{
			doJava.File = '../class/class.benefit.php' 
			doJava.Params ={ action:'tpl_add' }	
			doJava.Load('span_top_nav');
		}
		
	// **************************************** //	
		
		var editBenefit = function()
		{	
			var chk_benfit = doJava.checkedValue('chk_benfit');
			var chk_benfit_value = chk_benfit.split(',');
			doJava.File = '../class/class.benefit.php' 
			if( chk_benfit!='')
			{
				if( chk_benfit_value.length > 1){
					alert('Please select a row!');
				}
				else{
				
					doJava.Params ={ action:'tpl_edit',rowsid:chk_benfit_value[0]}	
					doJava.Load('span_top_nav');
				}
			}
			else{
				alert("Please select a row!");
			}	
		}
		
	// **************************************** //
	
		var getPlanByProduct = function(value)
		{
			doJava.File = '../class/class.benefit.php' 
			doJava.Params = {
					action:'get_cb_product_plan',
					productid:value
				}
				doJava.Load('div_product_plan');
		}
		
	// **************************************** //	
	
		var updateBenfit = function()
		{	
			var benefit_item_product = doJava.dom('cb_benefit_product_id'); 
			var benefit_item_desc  = doJava.dom('benefit_description');
			var benefit_item_plan  = doJava.dom('cb_benefit_plan');
			var benefit_item_value  = doJava.dom('benefit_product');
			var benefit_status    =  doJava.dom('benefit_status');
			var rows_id			 = doJava.dom('rows_id');
			
				doJava.File = '../class/class.benefit.php' 	
				
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
				
				else if(benefit_item_value.value==''){
					alert('Info, Benefit product is empty');
					benefit_item_value.focus();
					return false;
				}
				else if( benefit_status.value==''){
					alert('Info, Select Benefit status ');
					benefit_status.focus();
					return false;
				}
				else{
					if( confirm('Do you want to save this Benfit?'))
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
							alert("Success updating the product benefit!");
							extendsJQuery.postContent();
						}else
							alert("Failed updating the product benefit!");
					}
				}
		}
		
	// **************************************** //	
		var saveBenefit= function()
		{	
			var benefit_item_product = doJava.dom('cb_benefit_product_id'); 
			var benefit_item_desc  = doJava.dom('benefit_description');
			var benefit_item_plan  = doJava.dom('cb_benefit_plan');
			var benefit_item_value  = doJava.dom('benefit_product');
			var benefit_status    =  doJava.dom('benefit_status');
				doJava.File = '../class/class.benefit.php' 
				
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
				
				else if(benefit_item_value.value==''){
					alert('Info, Product Benefit is empty');
					benefit_item_value.focus();
					return false;
				}
				else if( benefit_status.value==''){
					alert('Info, Select Benefit Status ');
					benefit_status.focus();
					return false;
				}
				else{
					if( confirm('Do you want to save this product benefit ?'))
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
			<legend class="icon-benefit">&nbsp;&nbsp;Product Benefit </legend>	
				<div id="toolbars"></div>
				<div id="span_top_nav"></div>
				<div class="content_table" ></div>
				<div id="pager" ></div>
		</fieldset>	
		
	<!-- stop : content -->
	
	
	