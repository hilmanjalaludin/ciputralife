<?php
require(dirname(__FILE__)."/../sisipan/sessions.php");
require(dirname(__FILE__)."/../fungsi/global.php");
require(dirname(__FILE__)."/../class/MYSQLConnect.php");
require(dirname(__FILE__)."/../class/class.application.php");
require(dirname(__FILE__)."/../class/lib.form.php");
require(dirname(__FILE__)."/../sisipan/parameters.php");

$report_type = array (
			// 'cmp_tracking_overview' => 'Campaign Overview',
	'sum_fpa' => 'Report Campaign'
);

class rpt_sum_fpa extends mysql {
	function getspv() {
		$datas = array();
		if($this -> getSession('handling_type')==USER_SUPERVISOR){
			$sql = "select a.UserId as spvid, concat(a.id,' - ',a.full_name) as spvname
					from tms_agent a
					where a.UserId = ".$this -> getSession('UserId')." and a.user_state = 1 order by a.full_name";

			$qry = $this -> execute($sql, __FILE__, __LINE__);
			$i = 1;
			while ($row = $this -> fetchassoc($qry))
			{
				$result[$row['spvid']] = $row['spvname'];
				$i++;
			}
		}else if ($this -> getSession('handling_type') > 0){

			$sql = "select a.UserId as spvid, concat(a.id,' - ',a.full_name) as spvname
					from tms_agent a
					where a.spv_id = a.UserId and a.user_state = 1 order by a.full_name";

			$qry = $this -> execute($sql, __FILE__, __LINE__);
			$i = 1;
			while ($row = $this -> fetchassoc($qry))
			{
				$result[$row['spvid']] = $row['spvname'];
				$i++;
			}
		}
		return $result;
	}
}
$aInit = new rpt_sum_fpa();

?>
<script type="text/javascript"  src="<?php echo $app->basePath();?>pustaka/jquery/plugins/extToolbars.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>pustaka/jquery/plugins/aqPaging.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/extendsJQuery.js"></script>
<script type="text/javascript" src="<?php echo $app->basePath();?>js/javaclass.js"></script>
<script type="text/javascript">

Cigna_report.prototype.YUCKFOU = function(option){
	this.SegmentLabels(object = {
		labelText:'Telesales ', 
		labelId:'label_segment3_cigna', 
		source:{
			id:'content_segment3',
			type:'box',
			params:'get_filter_tm',
			value:option.value
		}	
	});
}

var ShowHTML  = function ()
{
	var report_type = Ext.Cmp('report_type').getValue();
	//alert(report_type);exit();
	if(report_type == 0){
		alert('Please set Report type');
		return false;
	} 
	
	var dialog = Ext.Window({
            url: '../report/rpt_sum_freepa_view.php',
            name: 'WinRptCampTrack',
            param: {
                report_type: report_type,
                start_date: Ext.Cmp('start_date').getValue(),
                end_date: Ext.Cmp('end_date').getValue(),
                selling_start_date: Ext.Cmp('selling_start_date').getValue(),
                selling_end_date: Ext.Cmp('selling_end_date').getValue(),
                Agent : Ext.Cmp('select_tele').getValue(),
                Supervisor : Ext.Cmp('group_filter_segment2').getValue()
            }
        });
        dialog.newtab();
}

var ShowExcel=function ()
{
	// alert(Ext.Cmp('report_type').getValue() + 'Sorry under constraction :(');
	var dialog = Ext.Window({
            url: '../report/rpt_sum_freepa_download.php',
            name: 'WinRptCampTrack',
            param: {
                report_type: Ext.Cmp('report_type').getValue(),
                start_date: Ext.Cmp('start_date').getValue(),
                end_date: Ext.Cmp('end_date').getValue(),
                selling_start_date: Ext.Cmp('selling_start_date').getValue(),
                selling_end_date: Ext.Cmp('selling_end_date').getValue(),
                Agent : Ext.Cmp('select_tele').getValue(),
                Supervisor : Ext.Cmp('group_filter_segment2').getValue()
            }
        });
        dialog.newtab();
}
$(function(){
	$('#toolbars').extToolbars({
		extUrl   :'../gambar/icon',
		extTitle :[['Show HTML'],['Show EXCEL']],
		extMenu  :[['ShowHTML'],['ShowExcel']],
		extIcon  :[['zoom.png'],['zoom.png']],
		extText  :true,
		extInput :false,
		extOption: [{}]
	});
	$('#start_date').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
	$('#end_date').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
	$('#selling_start_date').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});
	$('#selling_end_date').datepicker({showOn: 'button', buttonImage: '../gambar/calendar.gif', buttonImageOnly: true, dateFormat:'dd-mm-yy',readonly:true});

});
</script>
<style>
	.xx001{width:190px;border:1px solid #dddddd;font-size:11px;height:100px;padding-left:2px;background-color:#FFFEEE;}
	.xx003{border:1px solid #dddddd;font-size:11px;height:22px;padding-left:2px;width:190px;background:url('../gambar/input_bg.png');}
	.xx002{width:75px;border:1px solid #dddddd;font-size:11px;height:18px;padding-left:2px; background:url('../gambar/input_bg.png');}
	.xx004{border:1px solid #dddddd;font-size:11px;height:18px;padding-left:2px; background:url('../gambar/input_bg.png');}
</style>
<fieldset class="corner" style="background-color:#FFFFFF;">
	<legend class="icon-menulist">&nbsp;&nbsp;Report Campaign</legend>
	<div id="span_top_nav">
		<table cellpadding="4px">
			<tr>
				<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;"> * Report Type</td>
				<td><?php $jpForm->jpCombo('report_type','xx004', $report_type,NULL,'onchange="ShowSpv()"',0,0);?></td>
			</tr>
			<tr>
				<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;">Selling Date</td>
				<td>
					<?php $jpForm->jpInput('selling_start_date','xx002');?> &nbsp;-&nbsp;
					<?php $jpForm->jpInput('selling_end_date','xx002');?>
				</td>
			</tr>
			<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;" valign="top"><span id="label_segment2_cigna">Supervisor</span></td>
					<td> <span id="content_segment2"> <?php $jpForm->jpCombo('group_filter_segment2','xx002',$aInit->getspv(),NULL,'onchange="__CIGNA.YUCKFOU(this);"');?></span> </td>
				</tr>
			<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;" valign="top"><span id="label_segment3_cigna">Telesales</span></td>
					<td> <span id="content_segment3"><?php $jpForm->jpCombo('group_filter_segment3','xx002',array());?></span></td>
				</tr>
			<tr>
				<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;">&nbsp;</td>
			</tr>
		</table>
	</div>
	<div id="toolbars" class="toolbars"></div>

</fieldset>
