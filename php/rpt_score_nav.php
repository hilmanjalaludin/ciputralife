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
			$report_type = array(
				'score_report'				=> 'Call Monitoring (Score) Report'
			);
	}

?>

<script type="text/javascript"  src="<?php echo $app -> basePath();?>pustaka/jquery/plugins/extToolbars.js?versi=1.0"></script>
<script type="text/javascript"  src="<?php echo $app -> basePath();?>js/javaclass.js?versi=1.0"></script>
<script type="text/javascript"  src="<?php echo $app -> basePath();?>js/axa_scoring.js?time=<?php echo time();?>"></script>
<style>
	.xx001{width:190px;border:1px solid #dddddd;font-size:11px;height:100px;padding-left:2px;background-color:#FFFEEE;}
	.xx003{border:1px solid #dddddd;font-size:11px;height:22px;padding-left:2px;width:190px;background:url('../gambar/input_bg.png');}
	.xx002{width:75px;border:1px solid #dddddd;font-size:11px;height:18px;padding-left:2px; background:url('../gambar/input_bg.png');}
	.xx004{border:1px solid #dddddd;font-size:11px;height:18px;padding-left:2px; background:url('../gambar/input_bg.png');}
</style>

<fieldset class="corner">
	<legend class="icon-product"> &nbsp;&nbsp;&nbsp;Score Report</legend>
		<div id="cmp_top_content" class="box-shadow" style="margin-left:7px;width:1100px;height:auto;overflow:auto;padding:9px;margin-bottom:10px;">
			<table cellpadding="4px">
				<tr>
					<td style="font-size:12px;padding-right:12px;font-family:Arial;color:red;text-align:right;"> *Report Type</td>
					<td><?php $jpForm->jpCombo('report_type_cigna','xx004', report_type(),NULL,'onchange="__CIGNA.ReportType(this);"',0,0);?></td>
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