<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.nav.table.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");


SetNoCache();
/** setup sql syntax *******/

  $sql = " SELECT a.CampaignTypeId FROM t_lk_campaigntype a ";
  
 /** setup navigation page ****/
 
  $NavPages -> setPage(15);			 
  $NavPages -> query($sql);

 /** set filter ***************/
 $filter = '';
 if( $db -> havepost('keywords'))
 {
	$filter.= " AND 
				(
					a.CampaignTypeCode LIKE '%".$_REQUEST['keywords']."%' OR  
					a.CampaignTypeDesc LIKE '%".$_REQUEST['keywords']."%'
				) ";
 }
 
 $NavPages -> setWhere($filter);	
?>
<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js?versi=1.0"></script>
<script type="text/javascript"  src="<?php echo $app->basePath();?>js/extendsJQuery.js?versi=1.0"></script>
<script type="text/javascript"  src="<?php echo $app->basePath();?>js/javaclass.js?versi=1.0"></script>
<script type="text/javascript">
	
/* assign show list content **/
	
	var datas = 
	{
		keywords : '<?php echo $_REQUEST['keywords'];?>',
		order_by : '<?php echo $_REQUEST['order_by'];?>',
		type 	 : '<?php echo $_REQUEST['type'];?>'
	}
	
/* exttollbars **************/

	$('#toolbars').extToolbars({
		extUrl   :'../gambar/icon',
		extTitle :[['Enable'],['Disable'] ,['Add'],['Edit'],['Delete'],['Cancel'],[],['Search']],
		extMenu  :[['enableWork'],['disableWork'],['addWork'],['editWork'],['deleteWork'],['cancelWork'],[],['searchWork']],
		extIcon  :[['accept.png'],['cancel.png'], ['add.png'],['calendar_edit.png'],['delete.png'],['cancel.png'],[],['zoom.png']],
		extText  :true,
		extInput :true,
		extOption: [{
					 render:6,
					 type:'text',
					 id:'v_result', 	
					 name:'v_result',
					 value: datas.keywords,
					 width:200
					}]
	});
	
/* assign show list content **/
	
	var navigation = 
	{
		custnav	 : 'campaign_type_nav.php',
		custlist : 'campaign_type_list.php'
	}
	
	extendsJQuery.totalPage   = <?php echo $NavPages -> getTotPages(); ?>;
	extendsJQuery.totalRecord = <?php echo $NavPages -> getTotRows(); ?>;

/* assign show list content **/
	
	var searchWork = function(){
		var keywords = doJava.dom('v_result').value;
		var datas = {
			keywords : keywords
		}
		extendsJQuery.construct(navigation,datas);
		extendsJQuery.postContent();
	}

/* assign show list content **/

	extendsJQuery.construct(navigation,datas);
	extendsJQuery.postContentList();

/* UpdateData save result ***/

 var UpdateData = function()
	{
		var code =  doJava.dom('CampaignTypeCode').value;
		var desc =  doJava.dom('CampaignTypeDesc').value;
		var CampaignTypeId =  doJava.dom('CampaignTypeid').value;

		if( code =='' ) { alert('Please input Campaign Type Code!'); return false;}
		else if( desc =='' ) { alert('Please input Campaign Type Description!'); return false;}
		else
		{
			doJava.File = '../class/class.campaign.type.php';
			doJava.Params = { 
				action : 'update_campaign_type',
				CampaignTypeId : CampaignTypeId,
				code : code,
				desc : desc,
			}
			var error = doJava.eJson();
			// alert('CampaignTypeCode: '+error.CampaignTypeCode+' CampaignTypeDesc: '+error.CampaignTypeDesc);
			// return false;
			if( error.result ){
				alert('Success, Update Campaign Type!');
				extendsJQuery.postContent();	
			}
			else{
				alert('Failed, Update Campaign Type!');
			}
		}
	}		

/* add save result ***/
 var saveResult = function()
	{
		
		var code =  doJava.dom('CampaignTypeCode').value;
		var desc =  doJava.dom('CampaignTypeDesc').value;
		// var status =  doJava.dom('campaign_type_status').value;
		
		// if( status =='' ) { alert('Please select status!'); return false;}
		if( code =='' ) { alert('Please input Campaign Type Code!'); return false;}
		else if( desc =='' ) { alert('Please input Campaign Type Description!'); return false;}
		else
		{
			doJava.File = '../class/class.campaign.type.php';
			doJava.Params = { 
				action : 'insert_campaign_type',
				code : code,
				desc : desc,
				// status : status, 
			}
			
			var error = doJava.eJson();
			if( error.result ){
				alert('Success, Add Campaign Type!');
				extendsJQuery.postContent();	
			}
			else{
				alert('Failed, Add Campaign Type!');
			}
		}
	}	
/* deleted work ********************/

var deleteWork = function()
	{
		doJava.File = '../class/class.campaign.type.php';
		var arr_cbx_list = doJava.checkedValue('CampaignTypeId').split(',');
		doJava.Params = {
			action :'delete_work_area',
			CampaignTypeid : arr_cbx_list[0] 
		}
		var result = doJava.eJson();
		if( result.result ){
			alert('Success, Deleted Rows!');
			extendsJQuery.postContent();	
		}
		else
			alert('Failed, Deleted Rows!');
	}
	
/* enabel work ********************/

var enableWork = function()
	{
		doJava.File = '../class/class.campaign.type.php';
		var arr_cbx_list = doJava.checkedValue('CampaignTypeId').split(',');
		doJava.Params = {
			action :'enable_work_area',
			CampaignTypeid : arr_cbx_list[0] 
		}
		var result = doJava.eJson();
		if( result.result ){
			alert('Success, Enabling Type!');
			extendsJQuery.postContent();	
		}
		else
			alert('Failed, Disabling Type!');
	}
	
/* editWork **/

	var editWork = function(){
		var arr_cbx_list = doJava.checkedValue('CampaignTypeId').split(',');
		if( arr_cbx_list.length==1){
			doJava.File = '../class/class.campaign.type.php';
			doJava.Params = 
			{
				action :'edit_work_area',
				CampaignTypeid : arr_cbx_list[0] 
			}
			// var t =doJava.Post();
			// alert(t);
			// return false;
			doJava.Load('span_top_nav');
		}
		else{
			alert('please select a rows !')
		}
	}
	
/* disbaled work ***/
	
	var disableWork = function()
	{
		doJava.File = '../class/class.campaign.type.php';
		var arr_cbx_list = doJava.checkedValue('CampaignTypeId').split(',');
		doJava.Params = {
			action :'disable_work_area',
			CampaignTypeid : arr_cbx_list[0] 
		}
		var result = doJava.eJson();
		if( result.result ){
			alert('Success, Disabling Type!');
			extendsJQuery.postContent();	
		}
		else
			alert('Failed, Disabling Type!');

	}
	
	var addWork = function()
	{
		doJava.File = '../class/class.campaign.type.php';
		doJava.Params ={ action: 'add_campaign_type' }
		doJava.Load('span_top_nav');
	}
	
/* cancelWork *************/	
	var cancelWork = function()
	{
		doJava.File = '../class/class.campaign.type.php';
		doJava.Params ={ action: 'clear_work_area' }
		doJava.Load('span_top_nav');
	}	
</script>

<!-- ///////////////////////////////////////////////////////////////////////////-->
<!-- ///////////////////////////////////////////////////////////////////////////-->
<!-- start : content -->

	<fieldset class="corner">
		<legend class="icon-customers">&nbsp;&nbsp;Setting Campaign Type </legend>	
			<div id="toolbars"></div>
			<div id="span_top_nav" style="margin:5px;"></div>
			<div id="customer_panel" class="box-shadow">
				<div class="content_table" style="background-color:#FFFFFF;"></div>
				<div id="pager"></div>
			</div>
	</fieldset>	
		
<!-- stop : content -->
<!-- ///////////////////////////////////////////////////////////////////////////-->
<!-- ///////////////////////////////////////////////////////////////////////////-->