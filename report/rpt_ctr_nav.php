<?php

require ("../sisipan/sessions.php");
require ("../fungsi/global.php");
require ("../class/MYSQLConnect.php");
require ("../plugin/lib.form.php");
require ("../class/class.application.php");

class NavClosing extends mysql {

	function construct() {
		parent::__construct();
		
	}

	function getType() {
		$datas = array(0 => 'Normal', 1 => 'Treatment');
		return $datas;
	}

	function getCignaSystem() {
		$datas = array();
		if ($this -> getSession('handling_type') == 1) :
			$sql = "select 
								a.CignaSystemCode as CCode,a.CignaSystem as CSystem
						from t_lk_cignasystem a";

			$qry = $this -> execute($sql, __FILE__, __LINE__);
			while ($row = $this -> fetchrow($qry)) :
				$datas[] = $row;
			endwhile;
		endif;
		return $datas;
	}
	
	function getCmpByStatus()
	{
		echo "OK";
	}
	
	function getcmp() 
	{
		$datas = array();
		if ($this -> getSession('handling_type') >= 0) :
		
			$sql = "SELECT
						cp.CampaignId as CampId,
						cp.CampaignName as CampName
					FROM t_gn_campaign cp
					where cp.CampaignStatusFlag=1
					order by CampId";

			$qry = $this -> execute($sql, __FILE__, __LINE__);
			while ($row = $this -> fetchassoc($qry)) :
				$result[$row['CampId']] = $row['CampName'];
			endwhile;
			
		endif;
		return $result;
	}
	
	function getspv() {
		$datas = array();
		if ($this -> getSession('handling_type') > 0) :
		
			$sql = "select a.UserId as spvid, concat(a.id,' - ',a.full_name) as spvname
					from tms_agent a
					where a.spv_id = a.UserId order by a.full_name";

			$qry = $this -> execute($sql, __FILE__, __LINE__);
			while ($row = $this -> fetchrow($qry)) :
				$result[] = $row;
			endwhile;
		endif;
		return $result;
	}
	
	function getagt() {
		$datas = array();
		if ($this -> getSession('handling_type') > 0 ) :
		
			$sql = "select a.UserId as agentid, concat(a.id,' - ',a.full_name) as agentname
					from tms_agent a
					where a.handling_type = '4' order by a.full_name";

			$qry = $this -> execute($sql, __FILE__, __LINE__);
			while ($row = $this -> fetchrow($qry)) :
				$result[] = $row;
			endwhile;
		endif;
		return $result;
	}
	
	// function getproduct() {
		// $datas = array();
		// if ($this -> getSession('handling_type') > 0 ) :
		
			// $sql = "select a.CampaignId as cmpid, a.CampaignNumber as cmpno, concat(a.CampaignNumber,' - ',a.CampaignName) as cmpnum
						// from t_gn_campaign a
						// order by a.CampaignNumber";

			// $qry = $this -> execute($sql, __FILE__, __LINE__);
			// while ($row = $this -> fetchrow($qry)) :
				// $result[] = $row;
			// endwhile;
		// endif;
		// return $result;
	// }
	
}
//var_dump ($result);


if (!is_object($bInit)) :
	$aInit = new NavClosing();
endif;
$datasCignaSystem = $aInit -> getCignaSystem();
$datascmp = $aInit -> getcmp();
// $datascmp2 = $aInit -> getcmp2();
// $datascmp = $aInit -> getcmp3();
$datasspv = $aInit -> getspv();
$datasagt = $aInit -> getagt();

			

?> 
	<script type="text/javascript" src="<?php echo $app -> basePath(); ?>js/javaclass.js"></script>
  	<script type="text/javascript">
		</script>
	<script>
		$('document').ready(function() {
			$('.corner').corner();
			$('#start_date').datepicker({showOn: 'button', buttonImage: '<?php echo $app->basePath();?>gambar/calendar.gif', buttonImageOnly: true, dateFormat:'yy-mm-dd',readonly:true});
			$('#end_date').datepicker({showOn: 'button', buttonImage: '<?php echo $app->basePath();?>gambar/calendar.gif', buttonImageOnly: true, dateFormat:'yy-mm-dd',readonly:true});
		});
		
		var html;
		var CignaSystemCode ;
		var $__construct = function() {
			CignaSystemCode = doJava.checkedValue('CignaSystem');
		}
	
		
		
		function ShowReport($CignaSystemCode){
			var start_date = $('#start_date').val(); 
			var end_date = $('#end_date').val();
			var cignasystem = $('#CignaSystem').val();
			var cmptype = $('#cmptype').val();
			var rpttype = $('#rpttype').val();
			var agt = $('#agt').val();
			var spv = $('#spv').val();
			var cmp = doJava.checkedValue('campaign');
			if(cmp=='')
					{
						alert("Please Choose Campaign");
						return false;
					}
				if(start_date !=0 & end_date !=0){
					/*if(rpttype =='cmp.campaignnumber' & rptver=='clsbyqa')  {
					var url  = "../report/rpt_ctr_showcmp_qa.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp+"&spv="+spv+"&agt="+agt;
							window.open(url);
							
					}*/
					if(rpttype =='cmp.campaignnumber')  {
					var url  = "../report/rpt_ctr_showcmp_agt.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp+"&spv="+spv+"&agt="+agt;
							window.open(url,'addressbar=no');
							
					}
					if(rpttype =='cmp.overview')  {
					var url  = "../report/rpt_cmp_overview_show.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp;
							window.open(url,'addressbar=no');
							
					}
					if(rpttype =='cmp.overviewhst')  {
					var url  = "../report/rpt_cmp_overview_hst.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp;
							window.open(url,'addressbar=no');
							
					}
					if(rpttype =='cmp.overviewhst2')  {
					var url  = "../report/rpt_cmp_overview_hst2.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp;
							window.open(url,'addressbar=no');
							
					}
					if(rpttype =='cmp.review')  {
					var url  = "../report/rpt_cmp_review_show.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp;
							window.open(url,'addressbar=no');
							
					}if(rpttype =='cmp.reviewhst')  {
					var url  = "../report/rpt_cmp_review_hst.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp;
							window.open(url,'addressbar=no');
							
					}if(rpttype =='cmp.reviewhst2')  {
					var url  = "../report/rpt_cmp_review_hst2.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp;
							window.open(url,'addressbar=no');
							
					}
					if(rpttype =='spv.UserId')  {
					var url  = "../report/rpt_ctr_showspv_agt.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp+"&spv="+spv+"&agt="+agt;
							window.open(url);
							
					}
					if(rpttype =='agt.UserId')  {
					var url  = "../report/rpt_ctr_showagt_agt.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp+"&spv="+spv+"&agt="+agt;
							window.open(url);
							
					}
					if(rpttype =='cmp.cio') {
					var url  = "../report/rpt_ctr_cio_show.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp+"&spv="+spv+"&agt="+agt;
							window.open(url);
					}
					if(rpttype =='')  {
					alert("Please Fill in REPORT TYPE Parameters");		
					}
					return;
				}else
					alert("Please Fill in ALL Required Parameters");
				
				return;
		
		}
		
		function GetReport($CignaSystemCode){
			var start_date = $('#start_date').val(); 
			var end_date = $('#end_date').val();
			var cignasystem = $('#CignaSystem').val();
			var cmptype = $('#cmptype').val();
			var rpttype = $('#rpttype').val();
			var agt = $('#agt').val();
			var spv = $('#spv').val();
			var cmp = doJava.checkedValue('campaign');
			if(cmp=='')
					{
						alert("Please Choose Campaign");
						return false;
					}
				if(start_date !=0 & end_date !=0){
					/*if(rpttype =='cmp.campaignnumber' & rptver=='clsbyqa')  {
					var url  = "../report/rpt_ctr_downloadcmp_qa.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp+"&spv="+spv+"&agt="+agt;
							window.open(url);
							
					}*/
					if(rpttype =='cmp.campaignnumber')  {
					var url  = "../report/rpt_ctr_downloadcmp_agt.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp+"&spv="+spv+"&agt="+agt;
							window.open(url);
							
					}
					if(rpttype =='cmp.overview')  {
					var url  = "../report/rpt_cmp_overview_download.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp;
							window.open(url,'addressbar=no');
							
					}
					if(rpttype =='cmp.overviewhst')  {
					var url  = "../report/rpt_cmp_overview_hst_download.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp;
							window.open(url,'addressbar=no');
							
					}
					if(rpttype =='cmp.overviewhst2')  {
					var url  = "../report/rpt_cmp_overview_hst2_download.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp;
							window.open(url,'addressbar=no');
							
					}
					if(rpttype =='cmp.review')  {
					var url  = "../report/rpt_cmp_review_download.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp;
							window.open(url,'addressbar=no');
						// alert("asd");
					}
					if(rpttype =='cmp.reviewhst')  {
					var url  = "../report/rpt_cmp_review_hst_download.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp;
							window.open(url,'addressbar=no');
							
					}if(rpttype =='cmp.reviewhst2')  {
					var url  = "../report/rpt_cmp_review_hst2_download.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp;
							window.open(url,'addressbar=no');
							
					}
					if(rpttype =='spv.UserId')  {
					var url  = "../report/rpt_ctr_downloadspv_agt.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp+"&spv="+spv+"&agt="+agt;
							window.open(url);
							
					}
					if(rpttype =='agt.UserId')  {
					var url  = "../report/rpt_ctr_downloadagt_agt.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&rpttype="+rpttype+"&cmp="+cmp+"&spv="+spv+"&agt="+agt;
							window.open(url);
							
					}
					if(rpttype =='')  {
					alert("Please Fill in REPORT TYPE Parameters");		
					}
					return;
				}else
					alert("Please Fill in ALL Required Parameters");
				
				return;
			}
			
		var getCmpByStatus = function(opts){
			var status = opts.value;
			if(status != ''){
				doJava.File = "../class/class.rpt_callmon.php";
				doJava.Params= { 
					action:'get_cmp_by_status',
					status: status 
				}
				doJava.Load('group_campaign');
			}
			else{
				doJava.dom('group_campaign').innerHTML =" <select name=\"campaign\" id=\"campaign\" class=\"xx004\"><option value=\"\">--Choose--</option></select>";
				return false;
			}
			// try{
				// console.log(opts.value)
			// }
			// catch(e){
				// console.log(e);
			// }
		}
		
	
	</script>
<div>

	<fieldset class="corner">
		<legend class="icon-product"> &nbsp;&nbsp;&nbsp;Form Report Call Tracking</legend>
			
			<div id="rpt_top_content" class="box-shadow" style="width:99%;height:auto;overflow:auto;">
				<table width="99%" border="0" align="center">
					<tr>
						<td valign="top">	
					<div class="sub_main_content">
						<table cellpadding="5px" align="left">
							<tr>
								<th><font color=red> * Interval</th>
								<td width="230"> 
								<input type="text" id="start_date" name="start_date" size="9" value="<?=$date_start; ?>">
								&nbsp&nbsp&nbsp; To &nbsp&nbsp&nbsp;
								<input type="text" id="end_date" name="end_date" size="9" value="<?=$date_end; ?>"></td>
							</tr>
							<tr>
								<th><font color=red> * Report Type</th>
								<td> 
									<select style="width:220px" name="rpttype" id="rpttype">
									<option value=""> -- Choose -- </option>
									<option value="cmp.review">Campaign review</option>
									<option value="cmp.reviewhst">Campaign Review History</option>
									<option value="cmp.reviewhst2">Campaign Review History2</option>
									<option value="cmp.overview">Campaign Overview</option>
									<option value="cmp.overviewhst">Campaign Overview History</option>
									<option value="cmp.overviewhst2">Campaign Overview History2</option>
									</select>
							<tr>
							<tr>
								<th><font color=red> * Campaign Status</th>
								<td><?php $jpForm->jpCombo('mode_cigna','xx004', array('1'=>'Active','0'=>'Inactive'),NULL,'onchange="getCmpByStatus(this);"');?></td>
							</tr>
								<th><font color=red> * Campaign Name</th>
								<td><span id="group_campaign">
								<?php //$jpForm -> jpListcombo('campaign', $label = 'Campaign',$aInit -> getcmp(),$values = NULL, $js = NULL,$attr = false, $dis=0); ?>
								<?php $jpForm -> jpCombo('campaign', '',array(),$values = NULL, $js = NULL,$dis=0); ?>
								</span></td>
							</tr>
							
						</table>
					</div>
				</table>
				<a href="javascript:void(0);" style="float:right;margin-right:50px;margin-bottom:20px;" class="sbutton" onclick="GetReport();"><span>&nbsp;Download Report</span></a>	
				<a href="javascript:void(0);" style="float:right;margin-right:50px;margin-bottom:20px;" class="sbutton" onclick="ShowReport();"><span>Show Report</span></a>
			</div>
			</div>
	</fieldset>
</div>