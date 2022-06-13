<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../class/lib.form.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");


function getProductCategory()
{
	global $db;
	$product_category=array();
	$sql = " SELECT a.product_category_id,a.product_category_code FROM t_gn_product_category a ";
	
	$qry = $db->execute($sql,__FILE__,__LINE__);	
	while( $row = $db->fetchrow($qry) ){
		$product_category[$row->product_category_id] = $row->product_category_code;
	}
	
	return $product_category;
}
?>
<script type="text/javascript"  src="<?php echo $app -> basePath();?>pustaka/jquery/plugins/extToolbars.js?versi=1.0"></script>
<script type="text/javascript"  src="<?php echo $app -> basePath();?>js/javaclass.js?versi=1.0"></script>
<script type="text/javascript" >

$(function(){

	Ext.DOM.ShowHtml=function()
	{
		var VAR_POST_DATA = [];
		VAR_POST_DATA['page'] = 'dailysqcreport';
		VAR_POST_DATA['action'] = 'showhtml';
		var dialog = Ext.Window({
            url: '../class/class.redirect.php',
            name: 'Show Daily SQC Report',
            param: (Ext.Join(
						new Array(
							VAR_POST_DATA,
							Ext.Serialize('filterreport').getElement()
						)
				).object()
			)
        });

        dialog.newtab();
	}
	
	Ext.DOM.ShowExcel=function()
	{
		var VAR_POST_DATA = [];
		VAR_POST_DATA['page'] = 'dailysqcreport';
		VAR_POST_DATA['action'] = 'showexcel';
		var dialog = Ext.Window({
            url: '../class/class.redirect.php',
            name: 'Show Daily SQC Report',
            param: (Ext.Join(
						new Array(
							VAR_POST_DATA,
							Ext.Serialize('filterreport').getElement()
						)
				).object()
			)
        });

        dialog.newtab();
	}
	$('#toolbars').extToolbars({
		extUrl   :'../gambar/icon',
		extTitle :[['Show HTML'],['Show Excel']],
		extMenu  :[['Ext.DOM.ShowHtml'],['Ext.DOM.ShowExcel']],
		extIcon  :[['zoom.png'],['zoom.png']],
		extText  :true,
		extInput :false,
		extOption:[{
					render : 0,
					type   : 'combo',
					header : 'Report Type ',
					id     : 'report_type', 	
					name   : 'report_type',
					store  : [],
					triger : '',
					width  : 250
				}]
		});
		
		$('#start_date_callmon').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
		$('#end_date_callmon').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
		
		$('#start_date_sale').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
		$('#end_date_sale').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
	});
</script>
<style>
	.xx001{width:190px;border:1px solid #dddddd;font-size:11px;height:100px;padding-left:2px;background-color:#FFFEEE;}
	.xx003{border:1px solid #dddddd;font-size:11px;height:22px;padding-left:2px;width:190px;background:url('../gambar/input_bg.png');}
	.xx002{width:75px;border:1px solid #dddddd;font-size:11px;height:18px;padding-left:2px; background:url('../gambar/input_bg.png');}
	.xx004{border:1px solid #dddddd;font-size:11px;height:18px;padding-left:2px; background:url('../gambar/input_bg.png');}
</style>
<form name="filterreport">
<fieldset class="corner">
	<legend class="icon-product"> &nbsp;&nbsp;&nbsp;Score Report</legend>
		<div id="cmp_top_content" class="box-shadow" style="margin-left:7px;width:1100px;height:auto;overflow:auto;padding:9px;margin-bottom:10px;">
			<table cellpadding="4px">
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;"> *Product Group</td>
					<td><?php $jpForm->jpCombo('product_group','xx004', getProductCategory());?></td>
				</tr>	
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;">Call Monitoring Date</td>
					<td>
						<?php $jpForm->jpInput('start_date_callmon','xx002');?> &nbsp;-&nbsp;
						<?php $jpForm->jpInput('end_date_callmon','xx002');?>
					</td>
				</tr>
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;">Selling Date</td>
					<td>
						<?php $jpForm->jpInput('start_date_sale','xx002');?> &nbsp;-&nbsp;
						<?php $jpForm->jpInput('end_date_sale','xx002');?>
					</td>
				</tr>
			</table>
		</div>
		<div id="toolbars"></div>
</fieldset>
</form>