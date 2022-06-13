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
		/*return 
			$report_type = array(
				// 'cmp_overview' 			=> 'Campaign Overview',
				'agency_performance' 	=> 'Agent Performance',
				'performance_by_hour'  	=> 'Performance By Hour',
				'cmp_info_object'  		=> 'Campaign Information & Objective',
				'lead_activity'  		=> 'Lead Activity',
				'cmp_disposition'  		=> 'Campaign Disposition',
				'cmp_review'  			=> 'Campaign Review',
				'ref_report'  			=> 'Referal Report',
				//'report_free_pa'		=> 'Free PA Report' //ditambahkan oleh Fajar 27-01-2014
			);*/
		$report_type = array('agency_performance' 	=> 'Agent Performance'); 
		
		return $report_type;
	}

?>

<script type="text/javascript"  src="<?php echo $app -> basePath();?>pustaka/jquery/plugins/extToolbars.js?versi=1.0"></script>
<script type="text/javascript"  src="<?php echo $app -> basePath();?>js/javaclass.js?versi=1.0"></script>
<script type="text/javascript"  src="<?php echo $app -> basePath();?>js/Cigna_calltracking.js?time=<?php echo time();?>"></script>
<style>
	.xx001{width:190px;border:1px solid #dddddd;font-size:11px;height:100px;padding-left:2px;background-color:#FFFEEE;}
	.xx003{border:1px solid #dddddd;font-size:11px;height:22px;padding-left:2px;width:190px;background:url('../gambar/input_bg.png');}
	.xx002{width:75px;border:1px solid #dddddd;font-size:11px;height:18px;padding-left:2px; background:url('../gambar/input_bg.png');}
	.xx004{border:1px solid #dddddd;font-size:11px;height:18px;padding-left:2px; background:url('../gambar/input_bg.png');}
</style>

<fieldset class="corner">
	<legend class="icon-product"> &nbsp;&nbsp;&nbsp;Call Tracking Report</legend>
		<div id="cmp_top_content" class="box-shadow" style="margin-left:7px;width:1100px;height:auto;overflow:auto;padding:9px;margin-bottom:10px;">
			<table cellpadding="4px">
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;">Report Type</td>
					<td><?php $jpForm->jpCombo('report_type_cigna','xx004', report_type(),NULL,'onchange="__CIGNA.ReportType(this);"',0,0);?></td>
				</tr>	
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;">Group Filter By</td>
					<td><?php $jpForm->jpCombo('group_filter_by_cigna','xx004', array('campaign'=>'Campaign Name','supervisor'=>'Supervisor','Telesales'=>'Telesales'),NULL,'onchange="__CIGNA.GroupFilter(this);"',NULL,0,0);?></td>
				</tr>		
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;" valign="top"><span id="label_segment1_cigna">Campaign Name</span></td>
					<td> <span id="content_segment1"> <?php $jpForm->jpCombo('group_filter_segment1','xx002',array(),NULL);?></span> </td>
				</tr>
				
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;" valign="top"><span id="label_segment2_cigna">Supervisor</span></td>
					<td> <span id="content_segment2"> <?php $jpForm->jpCombo('group_filter_segment2','xx002',array(),NULL,'onchange="__CIGNA.GroupFilterBySPV(this);"');?></span> </td>
				</tr>
			
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;" valign="top"><span id="label_segment3_cigna">Telesales</span></td>
					<td> <span id="content_segment3"><?php $jpForm->jpCombo('group_filter_segment3','xx002',array());?></span></td>
				</tr>
				
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;">Interval</td>
					<td>
						<?php $jpForm->jpInput('start_date_cigna','xx002');?> &nbsp;-&nbsp;
						<?php $jpForm->jpInput('end_date_cigna','xx002');?>
					</td>
				</tr>
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;"><span id="label_segment4_cigna">Mode</span></td>
					<td><span id="content_segment4"><?php $jpForm->jpCombo('mode_cigna','xx004', array('summary'=>'Summary'),NULL,NULL,0,0);?></span></td>
				</tr>	
				
			</table>
		</div>
		<div id="toolbars"></div>
</fieldset>	