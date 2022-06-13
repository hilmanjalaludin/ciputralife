<?php

require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");
//require(dirname(__FILE__)."/../plugin/lib.form.php");

	
/*
 * @ package	: Set Product 
 *
 * @ def 		: add master campaign  sifatnyan local doang 
 * @ return 	: local class	
 */
	
class NavCampaign extends mysql
{

/*
 * @ package	: construct
 *
 * @ def 		: add master campaign  sifatnyan local doang 
 * @ return 	: local class	
 */	
 
function construct()
{
	parent::__construct();
}

/*
 * @ package	: getCoreCampaign
 *
 * @ def 		: add master campaign  sifatnyan local doang 
 * @ return 	: local class	
 */		
 
function getCoreCampaign()
{
	$datas = array();
	$sql = " select  a.CampaignGroupId as cmpGroup, a.CampaignGroupCode as cmpCode,  a.CampaignGroupName as cmpName, a.CampaignGroupStatusFlag as cmpStatus
			from t_gn_campaigngroup a where a.CampaignGroupStatusFlag=1";
	$qry = $this -> query($sql); 
	foreach( $qry -> result_assoc() as $rows )
	{
		$datas[$rows['cmpGroup']] = $rows['cmpName'];
	}	
	return $datas;
}


/*
 * @ package	: getStatus
 *
 * @ def 		: add master campaign  sifatnyan local doang 
 * @ return 	: local class	
 */		
 
function getStatus()
{
	$datas = array(0=>'Not Active',1=>'Active');
	return $datas;
}  

/*
 * @ package	: getPayMode
 *
 * @ def 		: add master campaign  sifatnyan local doang 
 * @ return 	: local class	
 */		
 
function getPayMode()
{
	$datas = array();
	$sql   = "select a.PayModeId, a.PayMode from t_lk_paymode a order by a.PayModeId ASC ";
	$qry   = $this -> query($sql);
	foreach( $qry -> result_assoc() as $rows )
	{
		$datas[$rows['PayModeId']] = $rows['PayMode'];
	}
	return $datas;
}	
		
/*
 * @ package	: _get_premi_group
 *
 * @ def 		: add master campaign  sifatnyan local doang 
 * @ return 	: local class	
 */	

function _get_premi_group()
	{
		$datas = array();
		$sql = " select 
					a.PremiumGroupId, a.PremiumGroupDesc 
				from t_lk_premiumgroup a order by a.PremiumGroupOrder ASC ";
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows ){
			$datas[$rows['PremiumGroupId']] = $rows['PremiumGroupDesc'];
		}
		
		return $datas;
	}
	
/*
 * @ package    : ProductCategoryId
 *
 * @ def 		: add master campaign  sifatnyan local doang 
 * @ return 	: local class	
 */	
 
function ProductCategoryId()
{
	$sql = " select * from t_lk_category a ";
	$qry = $this -> query($sql);
	foreach( $qry -> result_assoc() as $rows )
	{
		$datas[$rows['CategoryId']] = $rows['Category'];
	}
	return $datas;
}

/*
 * @ package	: ProductTypeId
 *
 * @ def 		: add master campaign  sifatnyan local doang 
 * @ return 	: local class	
 */	
 
function ProductTypeId()
{
	$sql = " select * from t_lk_producttype a ";
	$qry = $this -> query($sql);
	foreach( $qry -> result_assoc() as $rows )
	{
		$datas[$rows['ProductTypeId']] = $rows['ProductType'];
	}
	return $datas;
}

/*
 * @ package	: ProductTypeId
 *
 * @ def 		: add master campaign  sifatnyan local doang 
 * @ return 	: local class	
 */	
 

}
	
	if(!is_object($aInit ) ) $aInit = new NavCampaign();
	
	$datasCmp = $aInit -> getCoreCampaign();
	
?> 
<script type="text/javascript">

/* @ def 		: globals init 
 *
 * @ params 	: test,
 * @ author		: omens
 */
 
	var html; 
	var prod_id; 
	var payMode;
	var cmp_core;
	var cmp_name;
	var cmp_status;
	var pDatas;

/* @ def 		: ShowThisForm 
 *
 * @ params 	: test,
 * @ author		: omens
 */
 
var ShowThisForm = function(object)
{	
        var statusgender;
        if($('#slcgender').is(':checked')){
            statusgender = 1;
           
        }else{
            statusgender = 0;
        }
        if( object.checked) 
	{
	   
            
	/* show content of data input / age **/
            
		Ext.Ajax({
			url		: '../class/class.product.core.php',
			method  : 'GET',
			param 	: {
				action		: 'get_line_age',
				ProductPlan : Ext.Cmp('text_product_plan').getValue(),
				RangeAge	: Ext.Cmp('text_product_age').getValue(),
				PayMode 	: Ext.Cmp('PayMode').getValue(),
				PremiGroup	: Ext.Cmp('PremiGroup').getValue(),
                                Statusgender    : statusgender
			}
		}).load("html_div_agerange");
		
	/* show content of data input / grid **/
	
		Ext.Ajax({
			url		: '../class/class.product.core.php',
			method  : 'GET',
			param 	: {
				action		: 'get_line_content',
				ProductPlan : Ext.Cmp('text_product_plan').getValue(),
				RangeAge	: Ext.Cmp('text_product_age').getValue(),
				PayMode 	: Ext.Cmp('PayMode').getValue(),
				PremiGroup	: Ext.Cmp('PremiGroup').getValue(),
                                Statusgender    : statusgender
			}
		}).load("html_div_contents");
            
		
	}
	else{
		Ext.Cmp('html_div_agerange').setText("");	
		Ext.Cmp('html_div_contents').setText("");	
	}
}

/* @ def 		: get_credit_shield 
 *
 * @ params 	: test,
 * @ author		: omens
 */		
 
var get_credit_shield = function() {

	var ERROR = ( Ext.Ajax({
			url		: '../class/class.product.core.php',
			method 	: 'GET',
			param 	: {
				action:'get_credit_shield',
				product_type : Ext.Cmp('product_type').getValue()
			}
		}).json()
	);

	if( ERROR.result ) return true;
	else
		return false;
}


/* @ def 		: setInsert 
 *
 * @ params 	: test,
 * @ author		: omens
 */		
 
var setInsert = function(Type, opts)
{
  var 
	PayMode 	= Ext.Cmp('PayMode').getValue(),
	GroupPremi  = Ext.Cmp('PremiGroup').getValue(),
	ProductAge  = Ext.Cmp('text_product_age').getValue(),
	ProductPlan = Ext.Cmp('text_product_plan').getValue();
	
  /* get position start age to insert grid **/
  
  if( Type.toUpperCase() =='START')
  {
	 var i = 0;
		while( i < PayMode.length )
		{
			if(GroupPremi.length > 0 )
			{
				var g = 0;
				while( g < GroupPremi.length ) 
				{
					var p = [];
						p = opts.name.split('_');
					Ext.Cmp("start_age_"+ PayMode[i] +"_"+ GroupPremi[g]+"_"+p[p.length-1]+"").setValue(opts.value);
					g++;
				}
			}
			else
			{
				var p = [];
					p = opts.name.split('_');
				Ext.Cmp("start_age_"+ PayMode[i] +"_"+ p[p.length-1] +"").setValue(opts.value);
			}
			i++;
		}
  }
 
 // step2 
 
 if( Type.toUpperCase() =='END')  
 {
	var i = 0;
	while( i < PayMode.length )
	{
		if(GroupPremi.length > 0 ) 
		{
			var g = 0;
			while( g < GroupPremi.length ) 
			{
				var p = [];
					p = opts.name.split('_');
				Ext.Cmp("end_age_"+ PayMode[i] +"_"+ GroupPremi[g]+"_"+p[p.length-1]+"").setValue(opts.value);
				g++;
			}
		}
		else
		{
			var p = [];
				p = opts.name.split('_');
			Ext.Cmp("end_age_"+ PayMode[i] +"_"+ p[p.length-1] +"").setValue(opts.value);		
		}
		
		i++;
	}	
 }
 
}

/* @ def 		: saveProduct
 *
 * @ event 		: triger save 
 * @ params		: test
 */
 
var saveProduct = function()
{
	var form_age_range  = Ext.Serialize("form_age_range").getElement();
	var form_grid_content = Ext.Serialize("form_grid_content").getElement();
	
	var form_post_action = Ext.Join( new Array(form_age_range, form_grid_content ) ).http();
	var statusgender;
        if($('#slcgender').is(':checked')){
            statusgender = 1;
           
        }else{
            statusgender = 0;
        }
	Ext.Ajax({
		url		: '../class/class.product.core.php',
		method 	: 'POST',
		param   : {
			action			: 'save_product',
			post_data 	 	: form_post_action,
			CreditShield 	: (get_credit_shield()?1:0),
			ProductType 	: Ext.Cmp('product_type').getValue(),
			ProductId 	 	: Ext.Cmp('text_product_id').getValue(),
			ProductName  	: encodeURIComponent(Ext.Cmp('text_product_name').getValue()),
			ProductCores	: Ext.Cmp('select_cmp_core').getValue(),
			ProductStatus 	: Ext.Cmp('status').getValue(),	
			PayMode 		: Ext.Cmp('PayMode').getValue(),
			GroupPremi 		: Ext.Cmp('PremiGroup').getValue(),
			ProductPlan 	: Ext.Cmp('text_product_plan').getValue(),
			RangeAge 		: Ext.Cmp('text_product_age').getValue(),
                        StatusGender    : statusgender
		},
		ERROR :function(e) {
			var ERR = JSON.parse(e.target.responseText);
			if( ERR.success ){
				alert("Success, Add Prodcut !");
				return;
			}
			else{
				alert("Failed, Add prodcut.\nPlease check your field !");
				return;
			}
		}
	}).post();
}
	
/* @ def 		: saveProduct
 *
 * @ event 		: triger save 
 * @ params		: test
 */
 
var setProduct = function(options)
{
	try
	{
		var pay_mode_disabled = Ext.Cmp('PayMode').getName();
		if(get_credit_shield())
		{
			Ext.Cmp('text_product_plan').disabled(true);
			Ext.Cmp('text_product_age').disabled(true);
			
			for( var i in pay_mode_disabled ) 
			{
				pay_mode_disabled[i].disabled=true;
			}	
		}
		else
		{
			Ext.Cmp('text_product_plan').disabled(false);
			Ext.Cmp('text_product_age').disabled(false);
			
			for( i in pay_mode_disabled )
			{
				pay_mode_disabled[i].disabled=false;
			}	
		}
	}
	catch(e){
		console.log(e);
	}	
}
		
</script>
<fieldset class="corner">
<legend class="icon-product"> &nbsp;&nbsp;&nbsp;Add Product</legend>
	<div id="cmp_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
		<table width="99%" border="0" align="center">
			<tr>
				<td valign="top">	
				<!-- start : left tpl -->	
				<div class="sub_main_content">
					<table cellpadding="5px" align="left" border=0>
						<tr>
							<th class="content_th_top" style="color:red;font-weight:normal;"> * Product Type </th>
							<td><?php $db -> DBForm -> jpCombo('product_type',NULL,  $aInit-> ProductTypeId(),NULL,'onchange="setProduct(this);"'); ?></td>
						</tr>
						<tr>
							<th class="content_th_top" style="color:red;font-weight:normal;"> * Product ID</th>
							<td> <?php $db -> DBForm -> jpInput("text_product_id",null,null); ?> ( 30 )</td>
						</tr>
						<tr>
								<th class="content_th_top" style="color:red;font-weight:normal;"> * Product Paymode</th>
								<td> 
									<?php 
									$paymode = $aInit -> getPayMode();
									foreach( $paymode as $key => $label) { ?>	
										<?php $db -> DBForm -> jpCheck('PayMode', $label, $key); ?> <br/>
									<?php } ?>
								</td>
							</tr>
							<tr>
								<th class="content_th_top" style="color:red;font-weight:normal;"> * Campaign Core</th>
								<td><?php $db -> DBForm -> jpCombo('select_cmp_core',NULL, $datasCmp ); ?></td>
							</tr>
							
							<tr>
								<th class="content_th_top" style="color:red;font-weight:normal;" valign="top"> * Premi Group</th>
								<td>
									<?php 
										$_get_premi_group = $aInit -> _get_premi_group();
										foreach( $_get_premi_group as $key => $label) { ?>	
											<?php $db -> DBForm -> jpCheck('PremiGroup', $label, $key); ?> <br/>
									<?php } ?>
								</td>
							</tr>
							
							<tr>
								<th class="content_th_top" style="color:red;font-weight:normal;"> * Product Plan </th>
								<td> <input type="text" id="text_product_plan" name="text_product_plan" value="" style="width:85px;"> ( Plan ) </td>
							</tr>
							<tr>
								<th class="content_th_top" style="color:red;font-weight:normal;"> * Age Period</th>
								<td> <input type="text" id="text_product_age" value="" name="text_product_age" style="width:85px;"> </td>
							</tr>
						</table>
				</div>
				<!-- stop : left tpl -->	
						</td>
						<td valign="top">
					<!-- start : right tpl -->	
						<div class="sub_main_content">
						<table cellpadding="5px" align="left" border=0>
							<tr>
								<th style="color:red;font-weight:normal;"> * Product Name</th>
								<td><?php $db -> DBForm -> jpInput("text_product_name",null); ?> ( 30 ) </td>
							</tr>
							<tr>
								<th style="color:red;font-weight:normal;"> * Product Status</th>
								<td> <?php $db -> DBForm -> jpCombo("status",null, $aInit -> getStatus()); ?></td>
							</tr>
							<tr>
								<th style="color:red;font-weight:normal;">* Beneficiary</th>
								<td><?php $db -> DBForm -> jpCombo("beneficiary",null, array('0'=>'NO','1'=>'YES')); ?></td>
							</tr>
                                                        <tr>
                                                            <th style="color:red;font-weight:normal;"> Gender</th>
                                                            <td><input type='checkbox' id='slcgender' />Gender</td>
                                                        </tr>
							<tr>
								<th style="color:red;font-weight:normal;">&nbsp;</th>
								<td style="color:red;font-weight:normal;"> <?php $db -> DBForm -> jpCheck('show_form_data','<span style="color:red;">Show form</span>',NULL,'onClick="ShowThisForm(this);"');?></td>
							</tr>
							
						</table>
					</div>
					</td>	
					<tr>
						<td colspan="2">
							<form name="form_age_range"> 
								<div id="html_div_agerange"></div>
							</form>	
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<form name="form_grid_content"> 
								<div id="html_div_contents"></div>
							</form>	
						</td>
					</tr>
				</table>
				<a href="javascript:void(0);" style="float:right;margin-right:50px;margin-bottom:20px;" class="sbutton" onclick="saveProduct();"><span>&nbsp;Save</span></a>	
			</div>
			
			
				
	</fieldset>