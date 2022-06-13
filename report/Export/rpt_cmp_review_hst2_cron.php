<?php
	require("/opt/enigma/webapps/ciputralife/fungsi/global.php");
	require("/opt/enigma/webapps/ciputralife/class/MYSQLConnect.php");
	require("/opt/enigma/webapps/ciputralife/class/class.list.table.php");
	
	$campaign		= explode(",",$_REQUEST['cmp']);
	$campaign1		= implode(",",$campaign);
	$Modes 			= $argv;
	
function CreateReport(){
	global $db;
	global $ListPages;
	global $Modes;
	
	if($Modes[1]=="MTD"){
		$start_date	= date("Y-m-")."01";
		$end_date	= date("Y-m-d");
	}else if($Modes[1]=="tgl"){
		$start_date	= $Modes[2];
		$end_date	= $Modes[3];
	}else{
		$start_date	= date("Y-m-d");
		$end_date	= date("Y-m-d");
	}
	
	$today = date("Y-m-d");
	
	set_time_limit(500000);
	$ListPages -> pages = $db -> escPost('v_page'); 
	$ListPages -> setPage(10);

	/***** Campaign Selected Plus Datasize *****/
	$sqlCampaign = "select a.CampaignId, a.CampaignNumber, a.CampaignName, count(b.CustomerId) as DataSize
					from t_gn_campaign a
					left join t_gn_customer b ON a.CampaignId = b.CampaignId
					where a.CampaignStatusFlag = 1
					group by a.CampaignId Order by a.CampaignId ASC";
	$qryCampaign = $ListPages->execute($sqlCampaign,__FILE__,__LINE__);
	while($rowCampaign = $ListPages->fetchassoc($qryCampaign)){
		$CampaignList[$rowCampaign['CampaignId']]['Nama'] = $rowCampaign['CampaignName'];
		$CampaignList[$rowCampaign['CampaignId']]['DataSize'] = $rowCampaign['DataSize'];
	}
	
	/***** Contact / Uncontact Call Reason Query *****/
	$sqlCallReason = "select a.CallReasonContactedFlag, a.CallReasonId, a.CallReasonCode, a.CallReasonDesc from t_lk_callreason a Order by a.CallReasonId ASC";
	$qryCallReason = $ListPages->execute($sqlCallReason,__FILE__,__LINE__);
	while($rowCallReason = $ListPages->fetchassoc($qryCallReason)){
		if($rowCallReason['CallReasonContactedFlag']==1){
			$CallReasonList['Contact'][$rowCallReason['CallReasonDesc']]['Id'] = $rowCallReason['CallReasonId'];
			$CallReasonList['Contact'][$rowCallReason['CallReasonDesc']]['Code'] = $rowCallReason['CallReasonCode'];
			$CallReasonList['Contact'][$rowCallReason['CallReasonDesc']]['Flag'] = $rowCallReason['CallReasonContactedFlag'];
		}else if($rowCallReason['CallReasonContactedFlag']==0){
			if($rowCallReason['CallReasonId']==3 || $rowCallReason['CallReasonId']==1){
				$CallReasonList['UnContact']['No Pick Up']['Id'] = 3;
				$CallReasonList['UnContact']['No Pick Up']['Code'] = 202;
				$CallReasonList['UnContact']['No Pick Up']['Flag'] = 0;
			}else if($rowCallReason['CallReasonId']<>3 && $rowCallReason['CallReasonId']<>1){
				$CallReasonList['UnContact'][$rowCallReason['CallReasonDesc']]['Id'] = $rowCallReason['CallReasonId'];
				$CallReasonList['UnContact'][$rowCallReason['CallReasonDesc']]['Code'] = $rowCallReason['CallReasonCode'];
				$CallReasonList['UnContact'][$rowCallReason['CallReasonDesc']]['Flag'] = $rowCallReason['CallReasonContactedFlag'];
			}
		}
	}
	
	/***** Contact / Uncontact Data Query *****/
	$sqlkey = "SELECT a.CustomerId,
				(SELECT MAX(ax.CallHistoryId)
					FROM t_gn_callhistory ax
					LEFT JOIN t_lk_callreason b ON ax.CallReasonId = b.CallReasonId
					WHERE b.CallReasonCategoryId = 3 AND ax.CustomerId = a.CustomerId
						AND ax.CallHistoryCallDate >= '".$start_date." 00:00:00'
						AND ax.CallHistoryCallDate <= '".$end_date." 23:59:00') as `Contact`,
				(SELECT MAX(az.CallHistoryId)
					FROM t_gn_callhistory az
					LEFT JOIN t_lk_callreason b ON az.CallReasonId = b.CallReasonId
					WHERE b.CallReasonCategoryId IN (1,2) AND az.CustomerId = a.CustomerId
					AND az.CallHistoryCallDate >= '".$start_date." 00:00:00'
					AND az.CallHistoryCallDate <= '".$end_date." 23:59:00') AS `unContact`,
				b.CampaignId
			FROM t_gn_callhistory a
			LEFT JOIN t_gn_customer b ON a.CustomerId = b.CustomerId
			LEFT JOIN t_gn_campaign c ON b.CampaignId = c.CampaignId
			WHERE TRUE AND c.CampaignStatusFlag = 1
			AND a.CallHistoryCallDate >= '".$start_date." 00:00:00'
			AND a.CallHistoryCallDate <= '".$end_date." 23:59:00'
			GROUP BY a.CustomerId;";

	$qrykey = $ListPages->execute($sqlkey,__FILE__,__LINE__);
	while($rowkey = $ListPages->fetchassoc($qrykey)){
		$Key[] = ($rowkey['Contact']?$rowkey['Contact']:$rowkey['unContact']);
		$rowkey['Contact']?$TotalContact +=1:$TotalUnContact +=1;
		$Touched[$rowkey['CampaignId']] +=1;
	}
	
	foreach($Key as $k=>$CallHistoryId){
		$sql =	"select cr.CallReasonId, cr.CallReasonCode, cr.CallReasonContactedFlag, cr.CallReasonDesc,
				cp.CampaignNumber,cp.CampaignName, cp.CampaignId
			from t_gn_callhistory his
			left join t_gn_customer cust on his.CustomerId = cust.CustomerId
			left join t_lk_callreason cr on his.CallReasonId = cr.CallReasonId
			left join t_gn_campaign cp on cust.CampaignId = cp.CampaignId
			where his.CallHistoryId = ".$CallHistoryId;
	
		$qry = $ListPages->execute($sql,__FILE__,__LINE__);
		$row = $ListPages->fetchassoc($qry);
		if($row['CallReasonContactedFlag']==1){
			$DataContact[$row['CallReasonDesc']][$row['CampaignId']]['Total'] +=1;
			$DataContact[$row['CallReasonDesc']][$row['CampaignId']]['CampaignName'] = $row['CampaignName'];
			$DataContact[$row['CallReasonDesc']][$row['CampaignId']]['CallReasonCode'] = $row['CallReasonCode'];
		}else if($row['CallReasonContactedFlag']==0){
			if($row['CallReasonId']==3 || $row['CallReasonId']==1){
				$DataUnContact['No Pick Up'][$row['CampaignId']]['Total'] +=1;
				$DataUnContact['No Pick Up'][$row['CampaignId']]['CampaignName'] = $row['CampaignName'];
				$DataUnContact['No Pick Up'][$row['CampaignId']]['CallReasonCode'] = '202';
			}else if($row['CallReasonId']<>3 && $row['CallReasonId']<>1){
				$DataUnContact[$row['CallReasonDesc']][$row['CampaignId']]['Total'] +=1;
				$DataUnContact[$row['CallReasonDesc']][$row['CampaignId']]['CampaignName'] = $row['CampaignName'];
				$DataUnContact[$row['CallReasonDesc']][$row['CampaignId']]['CallReasonCode'] = $row['CallReasonCode'];
			}
		}
	}
	
	$reports = "<th> &nbsp;&nbsp;&nbsp;Report Campaign Review History 2 &nbsp;<br/></th>";
	$reports .= "<th> &nbsp;&nbsp;&nbsp;Date Range  &nbsp;: $start_date To $end_date<br/></th>";
	$reports .= "<th> &nbsp;&nbsp;&nbsp;Report Date : $today </th>";
	$reports .= "<table width=\"99%\" class=\"custom-grid\" cellspacing=\"0\">";
	
	/********* HEADER *********/
	$reports .= "<TABLE width=\"99%\" border=\"0\" align=\"center\">
			<DIV id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
			<THEAD><tr height=\"30\">
				<th rowspan=\"2\" nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">Call Reason</th>
				<th colspan=\"".sizeof($CampaignList)."\" nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">Campaign Name</th>
				<th rowspan=\"2\" nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">TOTAL</th></tr>
			<TR height=\"30\">";
			foreach($CampaignList as $Id => $Campaign){
				$reports .= "<th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">".$Campaign['Nama']."</th>";
			}
	$reports .= "</TR></THEAD></DIV>";
	
	/***** Contact & Uncontact List *****/
	foreach($CallReasonList as $Reason => $CallReason){
		foreach($CallReason as $CallReasonDesc => $CallReasonDetail){
			/***** Uncontacted *****/
			if($Reason=="UnContact"){
				$reports .= "<DIV id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
					 <TR height=\"30\">
						<td nowrap style=\"text-align: center\" class=\"content-middle\">".$CallReasonDesc." [".$CallReasonDetail['Code']."]</td>";
						foreach($CampaignList as $CampaignId => $CampaignName){
							$reports .= "<td nowrap style=\"text-align: center\" class=\"content-middle\">".($DataUnContact[$CallReasonDesc][$CampaignId]['Total']?$DataUnContact[$CallReasonDesc][$CampaignId]['Total']:0)."</td>";
							$TotalUncontactedPerReason[$CallReasonDesc] += ($DataUnContact[$CallReasonDesc][$CampaignId]['Total']?$DataUnContact[$CallReasonDesc][$CampaignId]['Total']:0);
							$TotalUncontactedPerCampaign[$CampaignId] += ($DataUnContact[$CallReasonDesc][$CampaignId]['Total']?$DataUnContact[$CallReasonDesc][$CampaignId]['Total']:0);
						}
						$reports .= "<td nowrap style=\"text-align: center\" class=\"content-middle\">".$TotalUncontactedPerReason[$CallReasonDesc]."</td>";
				$reports .= "</TR><DIV>";
			}
		}
	}
		/***** Total Uncontacted *****/
		$reports .= "<DIV id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
			 <TR height=\"30\">
				<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">Total Uncontacted</td>";
				foreach($CampaignList as $CampaignId => $CampaignName){
					$reports .= "<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">".$TotalUncontactedPerCampaign[$CampaignId]."</td>";
					$GTotalUncontacted += $TotalUncontactedPerCampaign[$CampaignId];
					$TotalTouchCampaign[$CampaignId] += $TotalUncontactedPerCampaign[$CampaignId];
				}
				$reports .= "<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">".$GTotalUncontacted."</td>";
			$reports .= "</TR><DIV>";
			
	foreach($CallReasonList as $Reason => $CallReason){
		foreach($CallReason as $CallReasonDesc => $CallReasonDetail){
			/***** Contacted *****/
			if($Reason=="Contact"){
				$reports .= "<DIV id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
					 <TR height=\"30\">
						<td nowrap style=\"text-align: center\" class=\"content-middle\">".$CallReasonDesc." [".$CallReasonDetail['Code']."]</td>";
						foreach($CampaignList as $CampaignId => $CampaignName){
							$reports .= "<td nowrap style=\"text-align: center\" class=\"content-middle\">".($DataContact[$CallReasonDesc][$CampaignId]['Total']?$DataContact[$CallReasonDesc][$CampaignId]['Total']:0)."</td>";
							$TotalContactedPerReason[$CallReasonDesc] += ($DataContact[$CallReasonDesc][$CampaignId]['Total']?$DataContact[$CallReasonDesc][$CampaignId]['Total']:0);
							$TotalContactedPerCampaign[$CampaignId] += ($DataContact[$CallReasonDesc][$CampaignId]['Total']?$DataContact[$CallReasonDesc][$CampaignId]['Total']:0);
						}
						$reports .= "<td nowrap style=\"text-align: center\" class=\"content-middle\">".$TotalContactedPerReason[$CallReasonDesc]."</td>";
				$reports .= "</TR><DIV>";
			}
		}
	}
		/***** Total Contacted *****/
		$reports .= "<DIV id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
			 <TR height=\"30\">
				<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">Total Contacted</td>";
				foreach($CampaignList as $CampaignId => $CampaignName){
					$reports .= "<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">".$TotalContactedPerCampaign[$CampaignId]."</td>";
					$GTotalContacted += $TotalContactedPerCampaign[$CampaignId];
					$TotalTouchCampaign[$CampaignId] += $TotalContactedPerCampaign[$CampaignId];
				}
				$reports .= "<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">".$GTotalContacted."</td>";
			$reports .= "</TR><DIV>";
			
		$reports .= "<tr><td>&nbsp;</td></tr>";
		
		/***** Total Touch PerCampaign *****/
		$reports .= "<DIV id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
			 <TR height=\"30\">
				<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">Total Touch</td>";
				foreach($CampaignList as $CampaignId => $CampaignName){
					$reports .= "<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">".$TotalTouchCampaign[$CampaignId]."</td>";
					$GTotalTouchCampaign += $TotalTouchCampaign[$CampaignId];
				}
				$reports .= "<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">".($GTotalContacted+$GTotalUncontacted)."</td>";
			$reports .= "</TR><DIV>";
			
		/***** Total unTouch PerCampaign *****/
		$reports .= "<DIV id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
			 <TR height=\"30\">
				<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">Total unTouch</td>";
				foreach($CampaignList as $CampaignId => $CampaignName){
					$reports .= "<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">".($CampaignName['DataSize']-$TotalTouchCampaign[$CampaignId])."</td>";
					$GTotalunTouchCampaign += ($CampaignName['DataSize']-$TotalTouchCampaign[$CampaignId]);
				}
				$reports .= "<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">".$GTotalunTouchCampaign."</td>";
			$reports .= "</TR><DIV>";
		
		/***** Total Data PerCampaign *****/
		$reports .= "<DIV id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
			 <TR height=\"30\">
				<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">Data Size</td>";
				foreach($CampaignList as $CampaignId => $CampaignName){
					$reports .= "<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">".$CampaignName['DataSize']."</td>";
					$GTotalDataCampaign += $CampaignName['DataSize'];
				}
				$reports .= "<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">".($GTotalDataCampaign)."</td>";
			$reports .= "</TR><DIV>";
	
	$reports .= "</tr></table>";
	
	return $reports;
}

// CreateReport();
// echo dirname(__FILE__);
	if($Modes[1]=="MTD"){
		$Reporttype = "MTD";
	}else{
		$Reporttype = "Daily";
	}
	// $fp = fopen(dirname(__FILE__).'/Generated/Report_CampaignReview_History2_'.$Reporttype.'_'.date("Ymd").'.xls', "w");
	$fp = fopen('/opt/enigma/webapps/ciputralife/report/Generated/Report_CampaignReviewHistory2_'.$Reporttype.'_'.date("Ymd").'.xls', "w");
	fwrite($fp, CreateReport());
	fclose($fp);
?>