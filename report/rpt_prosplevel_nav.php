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
	
	function getCignaSystem1() {
		if ($this -> getSession('handling_type') == 1) :
			$sql = "select 
								a.CignaSystemCode as CCode,a.CignaSystem as CSystem
						from t_lk_cignasystem a";

			$qry = $this -> execute($sql, __FILE__, __LINE__);
			while ($row = $this -> fetchrow($qry)) :
				$result[] = $row;
			endwhile;
		endif;
		return array($result);
	}
	
}

if (!is_object($bInit)) :
	$aInit = new NavClosing();
endif;

$datasCignaSystem = $aInit -> getCignaSystem();
$datasCignaSystem1 = $aInit -> getCignaSystem1();

			

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
			var cignasystem = $('#CignaSystem').val();
				if(start_date !=0){
					if( confirm('Do you want to show this Report..?')){
						var url  = "../report/rpt_prosplevel_show.php?action=ShowReport&start_date="+start_date+"&end_date="+end_date+"&cignasystem="+cignasystem;
							window.open(url);
					}else 
						return false;
				}else
					alert("Please KeyIn The Interval Date Value");
				
				return;
		}
		
		function getIframe(){
			var iframe_window_txt = doJava.dom('iframe_window_txt');
				doJava.dom('loading_ajax').style.display="none";
				iframe_window_txt.src='../DownLoadReport/prospectlevel/'
		}
		
		function GetReport($CignaSystemCode){
			var start_date = $('#start_date').val(); 
			var end_date = $('#end_date').val();
			var cignasystem = $('#CignaSystem').val();
			
				if(start_date !=0){
				
					if( confirm('Do you want to Download this Report..?')){
						doJava.dom('loading_ajax').style.display="block";
						doJava.File = "../report/rpt_prosplevel_download.php";
						doJava.Params = {
							action		:'ShowReport',
							start_date	: start_date,
							end_date	: end_date,
							cignasystem : cignasystem
						}
						
						var error = doJava.Post();
						//alert(error);
						if( error==1){
							setTimeout("getIframe();",1000);
						}
						else{
							doJava.dom('loading_ajax').style.display="none";
						}
						
					}else {
						return false;
					}	
				}
				else
					alert("Please KeyIn The Interval Date Value");
				
				return;
			}
	
	</script>

	
	<fieldset class="corner">
		<legend class="icon-product"> &nbsp;&nbsp;&nbsp;Form Report Prospect Level</legend>
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
								&nbsp; To &nbsp;
								<input type="text" id="end_date" name="end_date" size="9" value="<?=$date_end; ?>"></td>
							</tr>
							<tr>
								<!--<th> * Campaign Type</th>
								<td> 
									<select name="status" id="status">
									<option value=""> -- Choose -- </option>
									<?php foreach($aInit -> getType() as $key => $val) : ?>
										<option value="<?php echo $key; ?>"><?php echo $val; ?></option>
									<?php endforeach; ?>
									</select>
								</td>-->
							</tr>
							<tr>
								<th>Cigna System</th>
								<td>
									<select style="width:200px" name="CignaSystem" id="CignaSystem">
											<option value=""> -- Choose -- </option>
											<option value="">  All </option>
										<?php foreach($datasCignaSystem as $value ): ?>
											<option value="<?php echo $value -> CCode; ?>"><?php echo $value -> CSystem; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
						</table>
						<div id="loading_ajax" style="display:none;color:red;"><img src="../gambar/loading.gif"> Loading...</div>
					</div>
				</table>
				<a href="javascript:void(0);" style="float:right;margin-right:50px;margin-bottom:20px;" class="sbutton" onclick="GetReport();"><span>&nbsp;Download Report</span></a>	
				<a href="javascript:void(0);" style="float:right;margin-right:50px;margin-bottom:20px;" class="sbutton" onclick="ShowReport();"><span>Show Report</span></a>
			</div>
			</div>
			<fieldset style="border:1px solid #dddddd;">
				<legend style="font-size:14px;"><font color=red> Download Prospect Level txt Files </legend>
					<div class="box-shadow" style="margin-top:2px;width:99%;padding:3px;text-align:center;">
						<iframe id="iframe_window_txt" style="width:99%;border:1px solid #dddddd;padding:3px;" height="400px;"></iframe>
					</div>
			</fieldset>		
	</fieldset>
