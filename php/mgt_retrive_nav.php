<?php

require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.nav.table.php");
	require("../class/class.application.php");
	require('../sisipan/parameters.php');
?>

<script>
	doJava.File = "../class/class.reload.data.php";
	
	var getListCampaign = function()
	{
		doJava.Params = {
			action:"get_list_campaign"
		}
		doJava.Load("html_list_campaign");
	}

	var getListResult = function(campaignid)
	{
		doJava.Params = {
			action:'get_list_result',
			campaignid : campaignid.value
		}
		doJava.Load("html_list_result");
		
		
		if( campaignid.value!=''){
			//getMode('show');
		}
		else{
			//getMode('empty');	
		}	
	}

	
	var show = function(){
		var CampaignId = doJava.SelArrVal('campaign_list_id');
		var CallResult = doJava.SelArrVal('result_list_id'); 
		doJava.File ='../class/class.reload.data.php';
		doJava.Params = {
			action:"get_list_table",
			CampaignId:CampaignId,
			CallResult:CallResult
		}
		
		window.open(doJava.File+'?'+doJava.ArrVal());
		
	} 
	var excel = function(){
		var CampaignId = doJava.SelArrVal('campaign_list_id');
		var CallResult = doJava.SelArrVal('result_list_id'); 
		doJava.File ='../class/class.reload.excel.php';
		
		
		doJava.Params = {
			action:"get_list_excel",
			CampaignId:CampaignId,
			CallResult:CallResult
		}
		
		window.open(doJava.File+'?'+doJava.ArrVal());
		
	} 
	
getListCampaign();
</script>
<style>
	.header-text{font-size:12px;font-family:Arial;color:red;font-weight:bold;}
	.legend{color:blue;font-size:14px;}
	.fieldset{ border:1px solid #dddddd;}
	.select{ border:1px solid #dddddd;color:green;font-size:12px;
		background-color:#fffccc;
		font-family:Arial;
	}
	get_list_table
</style>
<fieldset class="fieldset">
	<legend class="legend"> Reload Data </legend>
	<div id="campaign_list" style="margin-bottom:3px;text-align:left;background-color:#FFFFFF;" class="box-shadow">
						<table border=0 cellpadding="4px;" width="30%">
							<tr>
								<td class="header-text" nowrap valign="top" width="10%">* Campaign List</td>
								<td id="html_list_campaign">
									<select name="campaign_list_id" id="campaign_list_id" class="select" style="color:green;width:auto;" multiple="true">
									<select>
									
								</td>
							</tr>
							<tr>
								<td class="header-text" nowrap valign="top">* Call Result </td>
								<td id="html_list_result">
									<select name="result_list_id" id="result_list_id" style="width:300px;" class="select">
									<select>
								</td>
							</tr>	
							<tr>
								<td class="header-text" nowrap valign="top"></td>
								<td id="html_list_result">
								<a href="javascript:void(0);" class="sbutton" onclick="show();" style="margin-right:4px;"><span>&nbsp;Show</span></a> 
								<a href="javascript:void(0);" class="sbutton" onclick="excel();" style="margin-left:4px;"><span>&nbsp;Excel</span></a>
								</td>
							</tr>	
						</table>
					</div>
</fieldset>	