<?php

require ("../sisipan/sessions.php");
require ("../fungsi/global.php");
require ("../class/MYSQLConnect.php");
require ("../class/class.application.php");
require ("../class/lib.form.php");

class NavClosing extends mysql {

	function construct() {
		parent::__construct();
	}

	function getType() {
		$datas = array(0 => 'Normal', 1 => 'Treatment');
		return $datas;
	}

	function getProdid() {

		$sql = "select pr.ProductCode, pr.ProductName,pr.ProductStatusFlag 
				from t_gn_product pr
				where pr.ProductStatusFlag=1";

		$qry = $this -> execute($sql, __FILE__, __LINE__);
		while ($row = $this -> fetchrow($qry)) :
			$datas[$row->ProductCode] = $row->ProductName;
		endwhile;

		return $datas;
	}

	function getCampaign() {
		$sql = "select cm.CampaignNumber, cm.CampaignName 
				from t_gn_campaign cm
				where cm.CampaignEndDate >= now() - interval 1 year";

		$qry = $this -> execute($sql, __FILE__, __LINE__);
		while ($row = $this -> fetchrow($qry)) :
			$datas[$row->CampaignNumber] = $row->CampaignNumber.' - '.$row->CampaignName;
		endwhile;

		return $datas;
	}

	function getcs() {
		$datas = array();
		if ($this -> getSession('handling_type') == 1) :
			$sql = "select a.csCode as CCode,a.cs as CSystem
						from t_lk_cs a";

			$qry = $this -> execute($sql, __FILE__, __LINE__);
			while ($row = $this -> fetchrow($qry)) :
				$datas[] = $row;
			endwhile;
		endif;
		return $datas;
	}
	
	function getQCstatus()
	{
		return $this->Entity->ReasonLabelQuality();
	}
}

if (!is_object($bInit)) :
	$aInit = new NavClosing();
endif;

$datascs = $aInit -> getcs();
$form = new jpForm();			

?> 
	<script "type="text/javascript" src="<?php echo $app -> basePath(); ?>js/javaclass.js"></script>
  	<script type="text/javascript">
		$(function() {
			$('.corner').corner();

		});
		var html;
		var csCode ;
		var $__construct = function() {
			csCode = doJava.checkedValue('cs');
		}
	</script>
	<script>
		function ShowReport($csCode){
			var start_date = $('#start_date').val(); 
			var end_date = $('#end_date').val();
			var prodid= $('#prodid').val();
			var query= $('#using_query').val();
			var Campaign = doJava.checkedValue('select_camp');
			var QCStat = doJava.checkedValue('select_qc_status');
			var exported = doJava.checkedValue('exported');
			var query_check = doJava.checkedValue('using_query_check');
			rptver	='clsbyagt';
				var url  = "../report/rpt_closing_show_exc.php?action=ShowReport&start_date="+start_date+
				"&end_date="+end_date+
				"&prodid="+prodid+
				"&Campaign="+Campaign+
				"&QCStat="+QCStat+
				"&exported="+exported+
				"&query="+query+
				"&query_check="+query_check
				;
					window.open(url);
				return;
		}
				
		function getIframe(){
			var iframe_window_txt = doJava.dom('iframe_window_txt');
				doJava.dom('loading_ajax').style.display="none";
				iframe_window_txt.src='../DownLoadReport/closing/'
		}
		
		function doChange () {
			var val=doJava.checkedValue('using_query_check');
			if (val) {
				$('#using_query').attr('disabled',false);
			} else{
				$('#using_query').attr('disabled','disabled');
			}
		}

		function GetReport($csCode){
			var start_date = $('#start_date').val(); 
			var end_date = $('#end_date').val();
			var prodid= $('#prodid').val();
			var query= $('#using_query').val();
			var Campaign = doJava.checkedValue('select_camp');
			var QCStat = doJava.checkedValue('select_qc_status');
			var exported = doJava.checkedValue('exported');
			var query_check = doJava.checkedValue('using_query_check');
			// rptver	='clsbyagt';
			// 	var url  = "../report/rpt_closing_download_agt.php?action=ShowReport&start_date="+start_date+
			// 	"&end_date="+end_date+
			// 	"&prodid="+prodid+
			// 	"&Campaign="+Campaign+
			// 	"&exported="+exported+
			// 	"&query="+query+
			// 	"&query_check="+query_check
			// 	;
			// 		window.open(url);
			// 	return;


						doJava.File = "../report/rpt_closing_download_excel.php";
						doJava.Params = {
							start_date	: start_date,
							end_date	: end_date,
							prodid      : prodid,
							Campaign     : Campaign,
							QCStat     : QCStat,
							exported     : exported,
							query     : query,
							query_check     : query_check,
						}
						var url = doJava.File +'?'+ doJava.ArrVal();
						window.open(url);
						// doJava.dom('loading_ajax').style.display="none";
						// setTimeout("getIframe();",1000);
						// }
						
				// 	}
				// 	else if(rptver ==''){
				// 		alert("Please Fill Report Version");
				// 		return false;
				// 	}
				// }
				// else
				// 	alert("Please Fill in All Required Parameters");
				
				return;
			}
	</script>

	<fieldset class="corner">
		<legend class="icon-product"> &nbsp;&nbsp;&nbsp;Closing Excel</legend>
			<div id="rpt_top_content" class="box-shadow" style="width:99%;height:auto;overflow:auto;">
				<table width="99%" border="0" align="center">
					
					<tr>
						<td>
							<table align="top" height="100%" width="50%" border="0">
									<tr>
										<th width="10%">   Product </th>
										<td><?php $jpForm->jpCombo('prodid','select', $aInit->getProdid()); ?></td>
										</td>
									</tr>

									<tr>
										<th>   Date</th>
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
										<td><?php $jpForm->jpListcombo('select_camp','All Campaign', $aInit->getCampaign()); ?></td>
									</tr>
									<tr>
										<th> QC Status</th>
										<td><?php $jpForm->jpListcombo('select_qc_status','Status', $aInit->getQCstatus()); ?></td>
									</tr>

									<tr>
										<td > <?php $jpForm->jpCheck('exported','Exported', '1'); ?></td>
									</tr>
<!--
									<tr>
										<td colspan="2"><?php $jpForm->jpCheck('using_query_check','Using Query', '1', "onclick='doChange();'"); ?></td>
									</tr>

									<tr>
										<td colspan="2"><textarea id="using_query" name="using_query"  
										rows="10" cols="100" disabled/></td>
									</tr>
-->
									<tr>
										<td><a href="javascript:void(0);"  class="sbutton" onclick="ShowReport();"><span>&nbsp;Show </span></a></td>
										<td><a href="javascript:void(0);"  class="sbutton" onclick="GetReport();"><span>&nbsp;Download Excel</span></a>	</td>
									</tr>
									
								</table>
						</td>
						<!--
						<td width="0%" style="display:none;">
							<fieldset  style="border:1px solid #dddddd;">
								<legend style="font-size:14px;"><font color=red>Download Closing txt Files </legend>
									<table>
									<tr>
										<td><input type="button" value="refresh" onclick="getIframe();"/></td>
										<td><div id="loading_ajax" style="display:none;color:red;"><img src="../gambar/loading.gif"> Loading...</div></td>
										
									</tr>
									<tr>
									<div class="box-shadow" style="margin-top:2px;width:99%;padding:3px;text-align:center;">
										<iframe id="iframe_window_txt" style="width:99%;border:1px solid #dddddd;padding:3px;" height="400px;"></iframe>
									</div>
									</tr>
									</table>
							</fieldset>
						</td>
						-->
					</tr>
						
						
					</div>
				</table>
				
			</div>
			</div>
				
	</fieldset>
