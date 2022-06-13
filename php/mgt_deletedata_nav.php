<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.nav.table.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");


SetNoCache();

$sql = "SELECT a.`CampaignId`,a.CampaignName,IF(COUNT(*) = 1,0,COUNT(*)) AS datasize FROM t_gn_campaign a LEFT JOIN t_gn_customer ON a.`CampaignId` = t_gn_customer.`CampaignId`";

$filter = '';
if( $db->havepost('CampaingId')){
    $cmpid = $db->escPost('CampaingId');
	$filter =" AND a.CampaignId = ".$db->escPost('CampaingId');
}
$NavPages -> setPage(15);

$NavPages -> query($sql);

$NavPages -> setWhere($filter);
$NavPages -> GroupBy('a.`CampaignId`');
//$NavPages ->echo_query();


?>

<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
<!--<script type="text/javascript" src="<?php //echo $app->basePath();?>js/sackAjax.js"></script>-->
<!--<script type="text/javascript" src="<?php //echo $app->basePath();?>js/autocompletes.js"></script>-->
<script type="text/javascript">
var kelas	= '../class/class.mgt_deletedata.php';
var datas = 
{
	CampaingId : '<?php echo $cmpid;?>'	
	
};

var navigation = 
{
	custnav	 : 'mgt_deletedata_nav.php',
	custlist : 'mgt_deletedata_list.php'
}

extendsJQuery.totalPage   = <?php echo $NavPages -> getTotPages(); ?>;
extendsJQuery.totalRecord = <?php echo $NavPages -> getTotRows(); ?>;

extendsJQuery.construct(navigation,datas);
extendsJQuery.postContentList();

var defaultPanel = function()
{
	
	doJava.File = kelas;
	
	if( doJava.destroy() ){
		doJava.Method = 'POST',
		doJava.Params = { 
			action :'tpl_onready', 
			CampaignId : datas.CampaignId 
			
		}
		doJava.Load('span_top_nav');	
	}
}; 

doJava.onReady(
		evt=function(){ 
		  defaultPanel();
		},
	  evt()
);

var Download = function(){	
	var arr_cbx_list = doJava.checkedValue('CampaignId').split(',');
	/*
	doJava.File = '../report/index.mgt_deletedata.php';
	doJava.Params = {
		content 		: 'EXCEL', report_type : report_type, 
		CampaignId      : arr_cbx_list[0]
		//mode  			: Mode
	}
	*/
	var url = "../report/index.mgt_deletedatajo.php?CampaingId="+ arr_cbx_list[0];
	window.open(url);
	//window.open(doJava.getWindowUrl());
	
};

var Delete = function(){
	var jawab = confirm("Do you want delete this ??");
	if (jawab) {
		doJava.File = '../class/class.mgt_deletedata.php';
		var arr_cbx_list = doJava.checkedValue('CampaignId').split(',');
			doJava.Params = {
				action: 'Delete',
				CampaingId: arr_cbx_list[0]
			};
			
			var error = doJava.eJson();
		if(error.result){
			alert('Success, Deleted Rows!' + error.id);
			extendsJQuery.postContent();	
		}else{
			alert('Failed, Deleted Rows!');
		}
	}
};

var searchcampaign = function(){
	var cmpId = Ext.Cmp('IdCampaing').getValue(); 
	datas = {
		CampaingId : cmpId
	}

	extendsJQuery.construct(navigation,datas)
	extendsJQuery.postContent();
};

$('#toolbars').extToolbars({
	extUrl   :'../gambar/icon',
	extTitle :[['search'],['Download'],['Delete']],
	extMenu  :[['searchcampaign'],['Download'],['Delete']],
	extIcon  :[['find.png'],['accept.png'],['delete.png']],
	extText  :true,
	extInput :true,
	extOption: [{
				 render:3,
				 type:'text',
				 id:'v_result', 	
				 name:'v_result',
				 value: datas.campaingid,
				 width:200
				}]
});



</script>
<style>
	.select { border:1px solid #dddddd;font-size:11px;background:url('../gambar/input_bg.png');height:22px;}
	.input_text {background:url('../gambar/input_bg.png');font-family:Arial;color:red;font-weight:bold;border:1px solid #dddddd;width:215px;
	font-size:11px;height:20px;background-color:#fffccc;}
	.input_autocomplete {font-family:Arial;color:red;font-weight:bold;border:1px solid #dddddd;width:250px;
	font-size:11px;height:20px;background-color:#fffccc;}	
	.text_header { text-align:right;color:#000;font-size:12px;}
	.select_multiple { border:1px solid #dddddd;height:10px;font-size:11px;background-color:#fffccc;width:200px;}
	.drop_dwn { border:1px solid #dddddd;font-size:11px;height:22px;background-color:#fffccc;width:225px;}
</style>
<fieldset class="corner" style="background-color:#FFFFFF;">
	<legend class="icon-menulist">&nbsp;&nbsp;Deletion Data</legend>	
		
	<div id="span_top_nav" style="margin:5px;"></div>
	<div id="toolbars" class="toolbars"></div>
	<div id="customer_panel" class="box-shadow">
		<div class="content_table" style="background-color:#FFFFFF;"></div>
		<div id="pager"></div>
	</div>
	
</fieldset>