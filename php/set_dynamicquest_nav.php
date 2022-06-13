<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.nav.table.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");


SetNoCache();

$sql = "SELECT a.questioner_id FROM t_gn_questioner a 
		INNER JOIN t_gn_product b ON a.product_id=b.ProductId
		INNER JOIN t_lk_questioner_type c ON a.questioner_type = c.quest_type_id";

$filter = '';
if( $db->havepost('product_filter')){
	$filter =" AND b.ProductId = ".$db->escPost('product_filter');
}
$NavPages -> setPage(15);

$NavPages -> query($sql);

$NavPages -> setWhere($filter);
// $NavPages -> GroupBy('a.`CampaignId`');
//$NavPages ->echo_query();


?>

<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
<script type="text/javascript">
var Product =(Ext.Ajax({ 
				url 	: '../class/class.questioner.php', 
				method :'GET', 
				param 	: { 
				action	: 'get_active_product'
						}
				}).json() );
var datas = {
	product_filter	: '<?php echo $db->escPost('product_filter');?>',	
	order_by 		: '<?php echo $db -> escPost('order_by');?>',
	type	 		: '<?php echo $db -> escPost('type');?>'
};

var navigation = 
{
	custnav	 : 'set_dynamicquest_nav.php',
	custlist : 'set_dynamicquest_list.php'
}

extendsJQuery.totalPage   = <?php echo $NavPages -> getTotPages(); ?>;
extendsJQuery.totalRecord = <?php echo $NavPages -> getTotRows(); ?>;

extendsJQuery.construct(navigation,datas);
extendsJQuery.postContentList();


var Download = function(){	
	var arr_cbx_list = doJava.checkedValue('CampaignId').split(',');
	
};

/* function searching customers **/
	
var Searchquestion = function()
{
	var product_filter = doJava.dom('product_filter').value;
		datas = {
			product_filter 	: product_filter
		}
		
	extendsJQuery.construct(navigation,datas)
	extendsJQuery.postContent()
};

var AddQuestioner = function(){
	// var cmpId = Ext.Cmp('IdCampaing').getValue(); 
	// datas = {
		// CampaingId : cmpId
	// }

	// extendsJQuery.construct(navigation,datas)
	// extendsJQuery.postContent();
	// alert('cek');
	var dialog = Ext.Window({
            url: 'frm.questioner.php',
            width: parseInt(Ext.DOM.screen.availWidth - 300),
            height: parseInt(Ext.DOM.screen.availHeight - 200),
            name: 'WinAddQuestioner',
            param: {
                action: 'ShowAdd'
            }
        });
        dialog.popup();
};

var PreviewQuestioner = function(){

	var arrCallRows  = doJava.checkedValue('questioner');
	var arrCountRows = arrCallRows.split(','); 
		if( arrCallRows!='')
		{	
			if( arrCountRows.length == 1 )
			{
				var dialog = Ext.Window({
					url: '../class/class.questioner.php',
					width: parseInt(Ext.DOM.screen.availWidth - 300),
					height: parseInt(Ext.DOM.screen.availHeight - 200),
					name: 'WinAPriviewQuestioner',
					param: {
						action: 'preview_questioner',
						quest	: arrCallRows
					}
				});
				dialog.popup();
				
			}
			else
			{
				alert("Select One Questioner !")
				return false;
			}
			
		}
		else
		{
			alert("No Questioner Select !");
			return false;
		}

	

};
var enablequest = function()
{
	var arrCallRows  = doJava.checkedValue('questioner');
	var arrCountRows = arrCallRows.split(','); 
		if( arrCallRows!='')
		{	
			if( arrCountRows.length == 1 )
			{
				
				// arrCallRows = arrCountRows[0].split('_'); 
				Ext.Ajax
				({
					url 	: '../class/class.questioner.php', 
					method 	: 'GET',
					param 	: {
							action	: 'enable_questioner',
							quest	: arrCallRows
						},
					ERROR 	: function(e)
					{
						var ERR = JSON.parse(e.target.responseText);
						if(ERR.status==1){
							alert(ERR.msg);
							Searchquestion();
						}
						else{
							alert(ERR.msg);
						}
					 
					}
				}).post(); 
				// alert(arrCallRows[1]);
			}
			else
			{
				alert("Select One Questioner !")
				return false;
			}
			
		}
		else
		{
			alert("No Questioner Select !");
			return false;
		}
};

var disablequest = function()
{
	var arrCallRows  = doJava.checkedValue('questioner');
	var arrCountRows = arrCallRows.split(','); 
		if( arrCallRows!='')
		{	
			if( arrCountRows.length == 1 )
			{
				
				// arrCallRows = arrCountRows[0].split('_'); 
				Ext.Ajax
				({
					url 	: '../class/class.questioner.php', 
					method 	: 'GET',
					param 	: {
							action	: 'disable_questioner',
							quest	: arrCallRows
						},
					ERROR 	: function(e)
					{
						var ERR = JSON.parse(e.target.responseText);
						if(ERR.status==1){
							Ext.Msg("Disable Questioner").Success();
							Searchquestion();
						}
						else{
							Ext.Msg("Disable Questioner").Failed();
						}
					 
					}
				}).post(); 
				// alert(arrCallRows[1]);
			}
			else
			{
				alert("Select One Questioner !")
				return false;
			}
			
		}
		else
		{
			alert("No Questioner Select !");
			return false;
		}
};

$('#toolbars').extToolbars({
	extUrl   :'../gambar/icon',
	extTitle :[['Enable'],['Disable'],['Add'],['Preview'],['Search']],
	extMenu  :[['enablequest'],['disablequest'],['AddQuestioner'],['PreviewQuestioner'],['Searchquestion']],
	extIcon  :[['accept.png'],['cancel.png'],['add.png'],['accept.png'],['zoom.png']],
	extText  :true,
	extInput :true,
	extOption: [{
				 render	: 4,
						header	: 'Filter ',
						type	: 'combo',
						id		: 'product_filter', 	
						name	: 'product_filter',
						value	: '<?php echo $db->escPost('product_filter');?>',
						store	: [Product],
						triger	: '',
						width	: 200
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
	<legend class="icon-menulist">&nbsp;&nbsp;Questioner</legend>	
	<div id="span_top_nav"></div>
	<div id="toolbars" class="toolbars"></div>
	<div id="customer_panel" class="box-shadow">
		<div class="content_table" style="background-color:#FFFFFF;"></div>
		<div id="pager"></div>
	</div>
	
</fieldset>