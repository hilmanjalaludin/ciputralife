<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../class/lib.form.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");
?>

<script type="text/javascript" src="<?php echo $app -> basePath();?>pustaka/jquery/plugins/extToolbars.js?versi=1.0"></script>
<script type="text/javascript">	
	var ClassReport = "../class/class.report.nav.php";
	var IndexReport = "../report/index.report.php";
	
	$(function(){
		$('#toolbars').extToolbars({
			extUrl   :'../gambar/icon',
			extTitle :[['Show HTML'],['Show Excel']],
			extMenu  :[['Ext.DOM.HTML'],['Ext.DOM.EXCEL']],
			extIcon  :[['zoom.png'],['zoom.png']],
			extText  :true,
			extInput :false,
			extOption:[{
						render : 0,
						type   : 'combo',
						header : 'Report Type',
						id     : 'report_type', 
						name   : 'report_type',
						store  : [],
						triger : '',
						width  : 250
					}]
		});
		
		$('#start_date').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
		$('#end_date').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
	});
	
	Ext.document('document').ready(function() {
		Ext.Cmp('group_filter').listener({
			'onChange': function(e) {
				Ext.DOM.getGroupFilter();
			}
		});
	});
	
	Ext.DOM.getGroupFilter = function()
	{
		var mode; 
		
		switch(Ext.Cmp('group_filter').getValue())
		{
			case 'campaign'   : mode = 'cmp'; break;
			case 'supervisor' : mode = 'spv'; break;
			case 'telesales'  : mode = 'tso'; break;
			default			  : mode = 'default';
		}
		
		Ext.DOM.loadCMP(mode);
		Ext.DOM.loadSPV(mode);
		Ext.DOM.loadTSO(mode);
	}
	
	Ext.DOM.loadCMP = function(a)
	{
		Ext.Ajax({
			url: ClassReport,
			method: 'GET',
			param: {
				action: 'load_cmp',
				mode: a
			}
		}).load('content_campaign');
	}
	
	Ext.DOM.loadSPV = function(a)
	{
		Ext.Ajax({
			url: ClassReport,
			method: 'GET',
			param: {
				action: 'load_spv',
				mode: a
			}
		}).load('content_supervisor');
	}
	
	Ext.DOM.loadTSO = function(a)
	{
		Ext.Ajax({
			url: ClassReport,
			method: 'GET',
			param: {
				action: 'load_tso',
				mode: a
			}
		}).load('content_telesales');
	}
	
	Ext.DOM.groupFilterContent = function()
	{
		var SPV_ID = Ext.Cmp('group_filter_spv').getValue();
		Ext.Ajax({
			url: ClassReport,
			method: 'GET',
			param: {
				action: 'load_tso_by_spv',
				spv_id: SPV_ID
			}
		}).load('content_telesales');
	}
	
	Ext.DOM.HTML = function(){
		var GroupFilter = Ext.Cmp('group_filter').getValue();
		var Campaign	= Ext.Cmp('group_filter_cmp').getValue();
		var Supervisor 	= Ext.Cmp('group_filter_spv').getValue();
		var Telesales 	= Ext.Cmp('group_filter_tso').getValue();
		var StartDate 	= Ext.Cmp('start_date').getValue();
		var EndDate 	= Ext.Cmp('end_date').getValue();
		var Mode 		= Ext.Cmp('mode').getValue();
		
		Ext.Window({
            url: IndexReport,
            param: {
                content		: 'HTML',
				report_type	: 'agent_performance',
				group_by	: GroupFilter,
				campaign	: Campaign,
				supervisor	: Supervisor,
				telesales	: Telesales,
				start_date	: StartDate,
				end_date	: EndDate,
				mode		: Mode
            }
        }).newtab();
	}
	
	Ext.DOM.EXCEL = function(){
		var GroupFilter = Ext.Cmp('group_filter').getValue();
		var Campaign	= Ext.Cmp('group_filter_cmp').getValue();
		var Supervisor 	= Ext.Cmp('group_filter_spv').getValue();
		var Telesales 	= Ext.Cmp('group_filter_tso').getValue();
		var StartDate 	= Ext.Cmp('start_date').getValue();
		var EndDate 	= Ext.Cmp('end_date').getValue();
		var Mode 		= Ext.Cmp('mode').getValue();
		
		Ext.Window({
            url: IndexReport,
            param: {
                content		: 'EXCEL',
				report_type	: 'agent_performance',
				group_by	: GroupFilter,
				campaign	: Campaign,
				supervisor	: Supervisor,
				telesales	: Telesales,
				start_date	: StartDate,
				end_date	: EndDate,
				mode		: Mode
            }
        }).newtab();
	}
	
</script>
<style>
	.xx001{width:190px;border:1px solid #dddddd;font-size:11px;height:100px;padding-left:2px;background-color:#FFFEEE;}
	.xx003{border:1px solid #dddddd;font-size:11px;height:22px;padding-left:2px;width:190px;background:url('../gambar/input_bg.png');}
	.xx002{width:75px;border:1px solid #dddddd;font-size:11px;height:18px;padding-left:2px; background:url('../gambar/input_bg.png');}
	.xx004{border:1px solid #dddddd;font-size:11px;height:18px;padding-left:2px; background:url('../gambar/input_bg.png');}
</style>
<fieldset class="corner">
	<legend class="icon-product"> &nbsp;&nbsp;&nbsp;Report Agent Performance</legend>
		<div id="cmp_top_content" class="box-shadow" style="margin-left:7px;width:1100px;height:auto;overflow:auto;padding:9px;margin-bottom:10px;">
			<table cellpadding="4px">
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;">Group Filter By</td>
					<td><?php $jpForm->jpCombo('group_filter','xx004', array('campaign'=>'Campaign Name','supervisor'=>'Supervisor','telesales'=>'Telesales'));?></td>
				</tr>
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;" valign="top"><span id="label_segment1_cigna">Campaign Name</span></td>
					<td> <span id="content_campaign"> <?php $jpForm->jpCombo('group_filter_cmp','xx002',array(),NULL);?></span> </td>
				</tr>
				
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;" valign="top"><span id="label_segment2_cigna">Supervisor</span></td>
					<td> <span id="content_supervisor"> <?php $jpForm->jpCombo('group_filter_spv','xx002',array(),NULL);?></span> </td>
				</tr>
			
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;" valign="top"><span id="label_segment3_cigna">Telesales</span></td>
					<td> <span id="content_telesales"><?php $jpForm->jpCombo('group_filter_tso','xx002',array(),NULL);?></span></td>
				</tr>
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;">Interval</td>
					<td>
						<?php $jpForm->jpInput('start_date','xx002');?> &nbsp;-&nbsp;
						<?php $jpForm->jpInput('end_date','xx002');?>
					</td>
				</tr>
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;"><span id="label_segment4_cigna">Mode</span></td>
					<td><span id="content_segment4"><?php $jpForm->jpCombo('mode','xx004', array('daily'=>'Daily','summary'=>'Summary'),NULL,NULL,0,0);?></span></td>
				</tr>
			</table>
		</div>
		<div id="toolbars"></div>
</fieldset>