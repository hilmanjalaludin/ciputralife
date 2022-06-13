<?php

require ("../sisipan/sessions.php");
require ("../fungsi/global.php");
require ("../class/MYSQLConnect.php");
require ("../class/class.application.php");

class NavClosing extends mysql {

	function construct() {
		parent::__construct();
	}

	function getType() {
		$datas = array(0 => 'Normal', 1 => 'Treatment');
		return $datas;
	}

	function getCampaign() {
		$datas = array();
		if ($this -> getSession('handling_type') == 1) :
			$sql = "select 
								concat (a.CampaignNumber,' - ',a.CampaignName) as cmpno_name,a.CampaignId as CmpId,a.CampaignNumber as CmpNo
						from t_gn_campaign a where a.CampaignStatusFlag = 1 order by CampaignNumber";

			$qry = $this -> execute($sql, __FILE__, __LINE__);
			while ($row = $this -> fetchrow($qry)) :
				$datas[] = $row;
			endwhile;
		endif;
		return $datas;
	}
	function getListAgent() {
		$datas = array();
		if ($this -> getSession('handling_type') == 1) :
			$sql = "select a.id as userid, a. full_name as fullname, concat(a.id,' - ',a.full_name) as name
						from tms_agent a order by full_name";

			$qry = $this -> execute($sql, __FILE__, __LINE__);
			while ($row = $this -> fetchrow($qry)) :
				$datas[] = $row;
			endwhile;
		endif;
		return $datas;
	}
	
	
}

if (!is_object($bInit)) :
	$aInit = new NavClosing();
endif;

$dataCampaign = $aInit -> getCampaign();
$dataListAgent = $aInit -> getListAgent();

			

?> 
	<script "type="text/javascript" src="<?php echo $app -> basePath(); ?>js/javaclass.js"></script>
  	<script type="text/javascript">
		$(function() {
			$('.corner').corner();

		});
		var html;
		var CignaSystemCode ;
		var $__construct = function() {
			CignaSystemCode = doJava.checkedValue('CignaSystem');
		}
	</script>
	<script>
		function ShowReport($CignaSystemCode){
			var start_date = $('#start_date').val(); 
			var end_date = $('#end_date').val();
			var Campaign = $('#Campaign').val();
			var Agent = $('#Agent').val();
			var rptver = $('#rptver').val();
				if (start_date !=0 & end_date !=0 & rptver !=''){
					if( confirm('Do you want to show this Report..?')){
						if (rptver =='clsbyqa'){
							var url  = "../report/rpt_listclosing_show_qa.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&Campaign="+Campaign+"&Agent="+Agent;
							window.open(url);
						}if (rptver =='clsbyagt'){
							var url  = "../report/rpt_listclosing_show_agt.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&Campaign="+Campaign+"&Agent="+Agent;
							window.open(url);
						}else
						return;
					}else 
					return false;
				}else
					alert("Please Fill in All Required Parameters");
				return;
		}
		function GetReport($CignaSystemCode){
			var start_date = $('#start_date').val(); 
			var end_date = $('#end_date').val();
			var Campaign = $('#Campaign').val();
			var Agent = $('#Agent').val();
			var rptver = $('#rptver').val();
				if (start_date !=0 & end_date !=0 & rptver !=''){
					if( confirm('Do you want to show this Report..?')){
						if (rptver =='clsbyqa'){
							var url  = "../report/rpt_listclosing_download_qa.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&Campaign="+Campaign+"&Agent="+Agent;
							window.open(url);
						}if (rptver =='clsbyagt'){
							var url  = "../report/rpt_listclosing_download_agt.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&Campaign="+Campaign+"&Agent="+Agent;
							window.open(url);
						}else
						return;
					}else 
					return false;
				}else
					alert("Please Fill in All Required Parameters");
				return;
		}
	
	</script>


	<fieldset class="corner">
		<legend class="icon-product"> &nbsp;&nbsp;&nbsp;Form Report List Closing</legend>
			<div id="rpt_top_content" class="box-shadow" style="width:99%;height:auto;overflow:auto;">
				<table width="99%" border="0" align="center">
					<tr>
						<td valign="top">	
					<div class="sub_main_content">
						<table cellpadding="5px" align="left">
							<tr>
								<th><font color=red> * Interval</th>
								<td width="263"> <script type="text/javascript" src="../pustaka/jquery/jquery-ui-1.7.2/ui/ui.datepicker.js"></script>
									<script type="text/javascript">
										$(function() {
											$("#start_date").datepicker({
												showOn : 'button',
												buttonImage : '../gambar/calendar.gif',
												buttonImageOnly : true,
												dateFormat : 'yy-mm-dd'
											});
											$("#end_date").datepicker({
												showOn : 'button',
												buttonImage : '../gambar/calendar.gif',
												buttonImageOnly : true,
												dateFormat : 'yy-mm-dd'
											});
										});
									</script>
								<input type="text" id="start_date" name="start_date" size="9" value="<?=$date_start; ?>">
								&nbsp&nbsp&nbsp; To &nbsp&nbsp&nbsp;
								<input type="text" id="end_date" name="end_date" size="9" value="<?=$date_end; ?>"></td>
							</tr>
							<tr>
								<th> Campaign</th>
								<td>
									<select style="width:200px" name="Campaign" id="Campaign">
										<option value=""> -- Choose -- </option>
										<option value=""> All </option>
										<?php foreach($dataCampaign as $value ): ?>
											<option value="<?php echo $value -> CmpNo; ?>"><?php echo $value -> cmpno_name; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
							<tr>
								<th> Agent</th>
								<td>
									<select style="width:200px" name="Agent" id="Agent">
										<option value=""> -- Choose -- </option>
										<option value=""> All </option>
										<?php foreach($dataListAgent as $value ): ?>
											<option value="<?php echo $value -> userid; ?>"><?php echo $value -> name; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
							<tr>
								<th><font color=red> * Report Version</th>
								<td> 
									<select style="width:220px" name="rptver" id="rptver">
									<option value=""> -- Choose -- </option>
									<option value="clsbyagt">By Agent</option>
									<option value="clsbyqa">By Quality Assurance</option>
									</select>
								</td>
							</tr>
							</tr>
						</table>
					</div>
				</table>
				<a href="javascript:void(0);" style="float:right;margin-right:50px;margin-bottom:20px;" class="sbutton" onclick="GetReport();"><span>&nbsp;Download Report</span></a>	
				<a href="javascript:void(0);" style="float:right;margin-right:50px;margin-bottom:20px;" class="sbutton" onclick="ShowReport();"><span>Show Report</span></a>
			</div>
			</div>
	</fieldset>
