<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../class/lib.form.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");
	
/* function report type **/

function report_type()
{
	return 
		$report_type = array
		(
			'quality_activity' => 'Quality Activity Report',
			'quality_detail'  => 'Quality Detail Report'
		);
}
		
		
	
/* function report type **/

function group_type()
{
	return 
		$report_type = array
		(
			'by_quality_campaign' => 'By Campaign',
			'by_quality_status' => 'By Status',
			'by_quality_agent'  => 'By Agent',
			'by_quality_qa' => 'By QA',
			'by_quality_date' => 'By Date'
		);
}
				
		
?>

<style>
	.xx001{width:190px;border:1px solid #dddddd;font-size:11px;height:100px;padding-left:2px;background-color:#FFFEEE;}
	.xx003{border:1px solid #dddddd;font-size:11px;height:22px;padding-left:2px;width:190px;background:url('../gambar/input_bg.png');}
	.xx002{border:1px solid #dddddd;font-size:11px;height:18px;padding-left:2px; background:url('../gambar/input_bg.png');}
	.xx004{border:1px solid #dddddd;font-size:11px;width:70px;height:18px;padding-left:2px; background:url('../gambar/input_bg.png');}
</style>
<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js?versi=1.0"></script>
<script type="text/javascript"  src="<?php echo $app->basePath();?>js/javaclass.js?versi=1.0"></script>
<script type="text/javascript">

$(function(){
	$('#toolbars').extToolbars({
		extUrl   :'<?php echo $app->basePath();?>gambar/icon',
		extTitle :[[''],['Show HTML'],['Show Excel']],
		extMenu  :[[''],['showHTML'],['showExcel']],
		extIcon  :[[''],['zoom.png'],['zoom.png']],
		extText  :true,
		extInput :false,
		extOption:[]
		});
			
	$('#start_date').datepicker({showOn: 'button', buttonImage: '<?php echo $app->basePath();?>gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
	$('#end_date').datepicker({showOn: 'button', buttonImage: '<?php echo $app->basePath();?>gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
});


/* filter form **/
doJava.getFormType = function(action,type_form,type_value)
{
	this.File = "../class/class.quality.report.php";
	this.Params ={
		action: action,
		type_form : type_form
	}
	this.Load(type_value);
}

/* filter form **/
doJava.Label = function(label_name)
{
	this.dom('label_name').innerHTML  = label_name;
}

/* get user tM ***/
doJava.getUserTM = function(type,values)
{
	this.File = "../class/class.quality.report.php";
	if( values!=''){
		this.Params = {
			action :'get_user_agent',
			spvid : values,
			type_form : type
		}
	}
	else{
		this.Params = {
			action :'get_user_agent',
			type_form : type 
		}
	}	
	
	this.Load('group_filter_tm');
}

/* getUserBySpv **/
doJava.getUserBySpv = function(combo)
{
	if( combo.value!=''){ this.getUserTM('check',combo.value); }
	else{ this.getUserTM('combo',combo.value); }	
}

/* getReportType ***/
doJava.getReportType = function(combo)
{
	this.groupFilter(combo);
	if( combo.value=='' ){
		this.dom('group_filter_by').selectedIndex= 0;
		this.dom('group_filter_value').selectedIndex= 0;
		this.dom('group_filter_by').value = 0;
		this.dom('group_filter_value').value = 0;
		this.dom('group_filter_by').disabled = true
		this.dom('group_filter_value').disabled = true;
	}
	else
	{	
		this.dom('group_filter_by').selectedIndex= 0;
		this.dom('group_filter_value').selectedIndex= 0;
		this.dom('group_filter_by').disabled = false;
		this.dom('group_filter_value').disabled = false;
	}
}

/* filtering group mode ***/
doJava.groupFilter = function(combo)
{
	if( combo.value=='by_quality_campaign'){
		this.getFormType('by_quality_campaign','check','group_filter_html');
		this.getUserTM('combo','');
		this.Label('Campaign Name');
	}
	else if( combo.value=='by_quality_status'){
		this.getFormType('by_quality_status','check','group_filter_html');
		this.getUserTM('combo','');
		this.Label('Status');
	}
	else if( combo.value=='by_quality_agent'){
		this.getFormType('by_quality_agent','combo','group_filter_html');
		this.getUserTM('combo','');
		this.Label('Supervisor');
	}
	else if( combo.value=='by_quality_date'){
		this.getFormType('by_quality_date','combo','group_filter_html');
		this.Label('Date');
		this.getUserTM('combo','');
	}
	else if(  combo.value=='by_quality_qa'){
		this.getFormType('by_quality_qa','check','group_filter_html');
		this.getUserTM('combo','');
		this.Label('User QA');
	}
	else{
		this.getFormType('by_quality_agent','combo','group_filter_html');
		this.getUserTM('combo','');
	}
}


/* mode report **/
doJava.ModeReport = function()
{
	var combo_mode = doJava.dom('group_filter_by'); 
	return combo_mode.options[combo_mode.selectedIndex].text;
}

/* showExcel **/


var showExcel =function()
{
	var report_type = doJava.dom('report_type').value;
	var group_filter_by = doJava.dom('group_filter_by').value;
	var ModeReport = doJava.ModeReport();
	
		switch(group_filter_by)
		{
			case 'by_quality_campaign': 
				var group_by = group_filter_by;
				var group_select = doJava.checkedValue('group_filter_value');
				var start_date = doJava.dom('start_date').value;
				var end_date  = doJava.dom('end_date').value;
				doJava.Params = {
					action : 'show_report', content : 'Excel',
					report_type  : report_type, group_by : group_by, 
					end_date : end_date, start_date : start_date,
					mode : ModeReport, group_select: group_select
				}
			break;
		
			case 'by_quality_status': 
				var group_by = group_filter_by;
				var group_select = doJava.checkedValue('group_filter_value');
				var start_date = doJava.dom('start_date').value;
				var end_date  = doJava.dom('end_date').value;
				doJava.Params = {
					action : 'show_report', content : 'Excel',
					report_type  : report_type, group_by : group_by, 
					end_date : end_date, start_date : start_date,
					mode : ModeReport, group_select: group_select
				}
			break;
			
			case 'by_quality_agent': 
				var group_by = group_filter_by;
				var group_select = doJava.checkedValue('group_user_tm');
				var start_date = doJava.dom('start_date').value;
				var end_date  = doJava.dom('end_date').value;
				doJava.Params = {
					action : 'show_report', content : 'Excel',
					report_type  : report_type, group_by : group_by, 
					end_date : end_date, start_date : start_date,
					mode : ModeReport, group_select: group_select
				}
			break;
			
			case 'by_quality_qa': 
				var group_by = group_filter_by;
				var group_select = doJava.checkedValue('group_filter_value');
				var start_date = doJava.dom('start_date').value;
				var end_date  = doJava.dom('end_date').value;
				doJava.Params = {
					action : 'show_report', content : 'Excel',
					report_type  : report_type, group_by : group_by, 
					end_date : end_date, start_date : start_date,
					mode : ModeReport, group_select: group_select
				}
			break;
			
			case 'by_quality_date': 
				var group_by = group_filter_by;
				var group_select = doJava.checkedValue('group_filter_value');
				var start_date = doJava.dom('start_date').value;
				var end_date  = doJava.dom('end_date').value;
				doJava.Params = {
					action : 'show_report', content : 'Excel',
					report_type  : report_type, group_by : group_by, 
					end_date : end_date, start_date : start_date,
					mode : ModeReport, group_select: group_select
				}
			break;
		}
		
	doJava.File ="../report/index.excel.php";	
	doJava.windowOpen();
}

/* show HTML **/
var showHTML =function()
{
	var report_type = doJava.dom('report_type').value;
	var group_filter_by = doJava.dom('group_filter_by').value;
	var ModeReport = doJava.ModeReport();
	
		switch(group_filter_by)
		{
			case 'by_quality_campaign': 
				var group_by = group_filter_by;
				var group_select = doJava.checkedValue('group_filter_value');
				var start_date = doJava.dom('start_date').value;
				var end_date  = doJava.dom('end_date').value;
				doJava.Params = {
					action : 'show_report', content : 'HTML',
					report_type  : report_type, group_by : group_by, 
					end_date : end_date, start_date : start_date,
					mode : ModeReport, group_select: group_select
				}
			break;
		
			case 'by_quality_status': 
				var group_by = group_filter_by;
				var group_select = doJava.checkedValue('group_filter_value');
				var start_date = doJava.dom('start_date').value;
				var end_date  = doJava.dom('end_date').value;
				doJava.Params = {
					action : 'show_report', content : 'HTML',
					report_type  : report_type, group_by : group_by, 
					end_date : end_date, start_date : start_date,
					mode : ModeReport, group_select: group_select
				}
			break;
			
			case 'by_quality_agent': 
				var group_by = group_filter_by;
				var group_select = doJava.checkedValue('group_user_tm');
				var start_date = doJava.dom('start_date').value;
				var end_date  = doJava.dom('end_date').value;
				doJava.Params = {
					action : 'show_report', content : 'HTML',
					report_type  : report_type, group_by : group_by, 
					end_date : end_date, start_date : start_date,
					mode : ModeReport, group_select: group_select
				}
			break;
			
			case 'by_quality_qa': 
				var group_by = group_filter_by;
				var group_select = doJava.checkedValue('group_filter_value');
				var start_date = doJava.dom('start_date').value;
				var end_date  = doJava.dom('end_date').value;
				doJava.Params = {
					action : 'show_report', content : 'HTML',
					report_type  : report_type, group_by : group_by, 
					end_date : end_date, start_date : start_date,
					mode : ModeReport, group_select: group_select
				}
			break;
			
			case 'by_quality_date': 
				var group_by = group_filter_by;
				var group_select = doJava.checkedValue('group_filter_value');
				var start_date = doJava.dom('start_date').value;
				var end_date  = doJava.dom('end_date').value;
				doJava.Params = {
					action : 'show_report', content : 'HTML',
					report_type  : report_type, group_by : group_by, 
					end_date : end_date, start_date : start_date,
					mode : ModeReport, group_select: group_select
				}
			break;
		}
		
	doJava.File ="../report/index.html.php";	
	doJava.windowOpen();
}


/* default disabled all combo panel **/

new(function(){
	doJava.dom('group_filter_by').disabled =true;
	doJava.dom('group_filter_value').disabled =true;
	doJava.dom('group_user_tm').disabled =true;
});

</script>
<fieldset class="corner">
	<legend class="icon-product"> &nbsp;&nbsp;&nbsp;Quality Report</legend>
		<div id="cmp_top_content" class="box-shadow" style="margin-left:7px;width:1100px;height:auto;overflow:auto;padding:9px;margin-bottom:10px;">
			<table cellpadding="4px">
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;">Report Type</td>
					<td><?php $jpForm->jpCombo('report_type','xx003', report_type(),NULL,'onchange="doJava.getReportType(this);"',0,0);?></td>
				</tr>	
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;">Group Filter By</td>
					<td><?php $jpForm->jpCombo('group_filter_by','xx002', group_type(),NULL,'onchange="doJava.groupFilter(this);"',NULL,0,0);?></td>
				</tr>	
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;" valign="top">
						<span id="label_name">Campaign Name</span>
					</td>
					<td> <span id="group_filter_html"><?php $jpForm->jpCombo('group_filter_value','xx002', array(), NULL, NULL, NULL,0,0);?></span></td>
				</tr>	
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;" valign="top">User TM</td>
					<td> <span id="group_filter_tm"><?php $jpForm->jpCombo('group_user_tm','xx002', array(), NULL, NULL, NULL,0,0);?></span></td>
				</tr>
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;">Interval</td>
					<td>
						<?php $jpForm->jpInput('start_date','xx004');?> &nbsp;-&nbsp;
						<?php $jpForm->jpInput('end_date','xx004');?>
					</td>
				</tr>
			</table>
		</div>
		<div id="toolbars"></div>
</fieldset>	


