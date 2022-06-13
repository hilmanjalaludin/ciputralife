<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
	
	SetNoCache();
	
	$NavPages -> setPage(10);	
	
	$sql = "select 
				a.CustomerId, 
				a.CampaignId, 
				b.CampaignName,
				a.CustomerNumber, 
				a.CustomerFirstName,  
				a.CustomerDOB,
				d.GenderShortCode,
				a.CustomerCity, 				
				f.full_name as tso,
				a.CustomerAddressLine1

			from t_gn_customer a
			left join t_gn_campaign b on a.CampaignId = b.CampaignId
			left join t_lk_gender d on a.GenderId = d.GenderId
			left join t_gn_assignment e on a.CustomerId = e.CustomerId
			left join tms_agent f on e.AssignSelerId = f.UserId";
	
	
	
	if( $db ->havepost('campaign')) 
		$filter.= " and a.CampaignId = '".$db ->escPost('campaign')."'";
		
	if( $db ->havepost('cust_name')) 
		$filter.= " and a.CustomerFirstName LIKE '%".$db ->escPost('cust_name')."%'";
				 
	$NavPages -> query($sql);
	$NavPages -> setWhere($filter);
	$NavPages -> GroupBy('a.CustomerId');
	$NavPages -> Result();
	
?>	
	
<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js?versi=1.0"></script>
<script type="text/javascript"  src="<?php echo $app->basePath();?>js/extendsJQuery.js?versi=1.0"></script>
<script type="text/javascript"  src="<?php echo $app->basePath();?>js/javaclass.js?versi=1.0"></script>
<script type="text/javascript">
	
	
	
	var datas  	= {}
		 
	extendsJQuery.totalPage   = <?php echo $NavPages ->getTotPages(); ?>;
	extendsJQuery.totalRecord = <?php echo $NavPages ->getTotRows(); ?>;
		
		datas = 
	{
		
		cust_name 	: '<?php echo $db -> escPost('cust_name');?>',
		campaign	: '<?php echo $db -> escPost('campaign');?>'
	}
	
	var navigation = {
		custnav:'dta_delete_nav.php',
		custlist:'dta_delete_list.php'
	}
	
	extendsJQuery.construct(navigation,datas)
	extendsJQuery.postContentList();
		
		
		
	var defaultPanel = function()
	{
		if( doJava.destroy() )
		{
			doJava.File = '../class/class.delete.php'
			doJava.Method = 'POST',
			doJava.Params = 
			{
				action		:'tpl_onready',
				campaign 	: datas.campaign,
				cust_name 	: datas.cust_name
			}
			doJava.Load('span_top_nav');	
		}
	}
	
	doJava.onReady(
		evt=function(){ 
		 defaultPanel();
		},
	  evt()
	)
	
	
	var removeCustomer = function()
		{
			var chk_menu = doJava.checkedValue('chk_menu');
			if( chk_menu!='' )
			{
				doJava.File = '../class/class.delete.php' 
				doJava.Params ={ 
					action:'remove_customer', 
					customer_id : chk_menu	
				}		
				var error_message = doJava.eJson();
					if( error_message.result )
					{
						alert('Success, Delete Customer');
						extendsJQuery.postContent();
					}
					else{
						alert('Failed, Customer');
						return false;
					}
			}
			else{
				alert('Please select a rows !')
			}
		
		}
		
		
/**		var validremove = function(){
		var chk_menu = doJava.dom('chk_menu');		
		if (chk_menu==''){
			alert("Please Select a rows !!");
			Ext.Cmp('chk_menu').setFocus();
			return false;
		}
		else
		{		
			return true;
		}
}
**/
	
	
	
	var searchCustomer = function(){
		var campaign  	 = doJava.dom('campaign').value; 
		
				
		doJava.File = '../class/class.delete.php'
		datas = {
			campaign	: campaign,
		}
		//alert(campaign);
		extendsJQuery.construct(navigation,datas)
		extendsJQuery.postContent()
	}
	
	
	
	
	
	var resetSeacrh = function(){
		if( doJava.destroy() ){
			doJava.init = [
							['campaign'],
						  ]
			doJava.setValue('');
			searchCustomer();
		}
	}
	
	$(function(){
		
		$('#toolbars').extToolbars({
			extUrl   :'../gambar/icon',
			extTitle :[['Search'],['Clear'],['Remove Customer']],
			extMenu  :[['searchCustomer'],['resetSeacrh'],['removeCustomer']],
			extIcon  :[['zoom.png'], ['cancel.png'], ['cross.png']],
			extText  :true,
			extInput :false,
		});
		
		$('#cust_dob').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
	});
	
	
</script>

<fieldset class="corner">
	<legend class="icon-customers">&nbsp;&nbsp;Delete Data </legend>	
	
	<div id="span_top_nav"></div>
	<div id="toolbars"></div>
	<div id="recording_panel" class="box-shadow">
		<div class="content_table" ></div>
		<div id="pager"></div>
	</div>
				
</fieldset>	