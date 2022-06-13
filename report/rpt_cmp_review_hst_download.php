<?php
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	
	$start_date 	= $_REQUEST['start_date'];
	$end_date  		= $_REQUEST['end_date'];
	$cignasystem	= $_REQUEST['cignasystem'];
	$campaign		= explode(",",$_REQUEST['cmp']);
	$campaign1		= implode(",",$campaign);
	$today			= date("Y-m-d");
	// echo $today;
	/** FUNGSI EXCEL **/
	header("Content-type: application/vnd-ms-excel");
	$name		="CiputraCampaignreviewHistory";
	$file		=".xls";
	$sdate		=$start_date;
	//$filename 	= $name.$sdate."To".$end_date.$file;
	$filename 	= $name.$today.$file;
	
	header("Content-Disposition: attachment; filename=".($filename));
	
	set_time_limit(500000);
	$ListPages -> pages = $db -> escPost('v_page'); 
	$ListPages -> setPage(10);
	
	/***** Campaign Selected Plus Datasize *****/
	$sqlCampaign = "select a.CampaignId, a.CampaignNumber, a.CampaignName, count(b.CustomerId) as DataSize
					from t_gn_campaign a
					left join t_gn_customer b ON a.CampaignId = b.CampaignId
					where a.CampaignId in (".$campaign1.")
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
	$sql = "SELECT ch.CallReasonId,
				c.CallReasonCode,ca.CampaignId,
				if(c.CallReasonContactedFlag is null,2,c.CallReasonContactedFlag) as CallReasonContactedFlag,
				if(c.CallReasonDesc is null,\"Un Touch\",c.CallReasonDesc) as CallReasonDesc,
				ca.CampaignNumber,ca.CampaignName,count(DISTINCT ch.CustomerId) as TOT
				FROM t_gn_callhistory ch
				INNER JOIN t_gn_customer cs on cs.CustomerId=ch.CustomerId
				INNER JOIN t_gn_campaign ca on ca.CampaignId=cs.CampaignId
				INNER JOIN t_lk_callreason c on c.CallReasonId=ch.CallReasonId
				where 
				ch.CallHistoryId =
				(select max(subch.CallHistoryId) from t_gn_callhistory subch where
				subch.CallHistoryCallDate >= '".$_REQUEST['start_date']." 00:00:00' 
				and subch.CallHistoryCallDate <= '".$_REQUEST['end_date']." 23:50:00'
				and subch.CustomerId = ch.CustomerId)";
		if($_REQUEST['cmp']){
			$sql.=" and cs.CampaignId in (".$campaign1.")";
		}
		$sql.=" group by ch.CallReasonId,cs.CampaignId
				order by ch.CallReasonId ASC;";
	// echo $sql;
	$qry = $ListPages->execute($sql,__FILE__,__LINE__);
	while($row = $ListPages->fetchassoc($qry)){
		if($row['CallReasonContactedFlag']==1){
			$DataContact[$row['CallReasonDesc']][$row['CampaignId']]['Total'] +=$row['TOT'];
			$DataContact[$row['CallReasonDesc']][$row['CampaignId']]['CampaignName'] = $row['CampaignName'];
			$DataContact[$row['CallReasonDesc']][$row['CampaignId']]['CallReasonCode'] = $row['CallReasonCode'];
		}else if($row['CallReasonContactedFlag']==0){
			if($row['CallReasonId']==3 || $row['CallReasonId']==1){
				$DataUnContact['No Pick Up'][$row['CampaignId']]['Total'] +=$row['TOT'];
				$DataUnContact['No Pick Up'][$row['CampaignId']]['CampaignName'] = $row['CampaignName'];
				$DataUnContact['No Pick Up'][$row['CampaignId']]['CallReasonCode'] = '202';
			}else if($row['CallReasonId']<>3 && $row['CallReasonId']<>1){
				$DataUnContact[$row['CallReasonDesc']][$row['CampaignId']]['Total'] +=$row['TOT'];
				$DataUnContact[$row['CallReasonDesc']][$row['CampaignId']]['CampaignName'] = $row['CampaignName'];
				$DataUnContact[$row['CallReasonDesc']][$row['CampaignId']]['CallReasonCode'] = $row['CallReasonCode'];
			}
		}
		
		/*if($row['CallReasonContactedFlag']==0){
			if($row['CallReasonId']==3 || $row['CallReasonId']==1){
				$dataunCon1['No Pick Up'][$row['CampaignName']] += $row['TOT'];
				$dataunCon2[$row['CampaignName']]	= $row['CampaignName'];
				$dataunCon3['No Pick Up']	= $row['CallReasonDesc'];
				$dataunCon4[$row['202']]	= $row['CallReasonCode'];
				$Touched[$row['CampaignNumber']] +=$row['TOT'];
			}else{
				$dataunCon1[$row['CallReasonDesc']][$row['CampaignName']] = $row['TOT'];
				$dataunCon2[$row['CampaignName']]	= $row['CampaignName'];
				$dataunCon3[$row['CallReasonDesc']]	= $row['CallReasonDesc'];
				$dataunCon4[$row['CallReasonCode']]	= $row['CallReasonCode'];
				$Touched[$row['CampaignNumber']] +=$row['TOT'];
			}
		}else if($row['CallReasonContactedFlag']==1){
			$dataCont1[$row['CallReasonDesc']][$row['CampaignName']] = $row['TOT'];
			$dataCont2[$row['CampaignName']]	= $row['CampaignName'];
			$dataCont3[$row['CallReasonDesc']]	= $row['CallReasonDesc'];
			$dataCont4[$row['CallReasonCode']]	= $row['CallReasonCode'];
			$Touched[$row['CampaignNumber']] +=$row['TOT'];
		}*/
	}

	/*$sqlquery = "select b.CampaignNumber, count(*) as tot, b.CampaignName 
				from t_gn_customer a
				left join t_gn_campaign b ON a.CampaignId = b.CampaignId
				where true ".($_REQUEST['cmp']?" and a.CampaignId in (".$campaign1.")":"")."
				group by b.CampaignNumber";
				
	$qrydataa = mysql_query($sqlquery);
	while($row = mysql_fetch_assoc($qrydataa)){
		$rowseed[$row['CampaignNumber']]['tot'] = $row['tot'];
		$rowseed[$row['CampaignNumber']]['CampaignName'] = $row['CampaignName'];
	}

	foreach($rowseed as $CampaignNumber => $data){
		
		$dataNull1['New'][$data['CampaignName']] = $data['tot']-$Touched[$CampaignNumber];
		$dataNull2[$data['CampaignName']] = $data['CampaignName'];
		$dataNull3['New'] = 'New';
		$dataNull4['New'] = 'New';
	}*/
	
	/*$sqlnew = "SELECT 'New' as CallReasonId,
				'New' as CallReasonCode,
				IF(b.CallReasonContactedFlag IS NULL,2,b.CallReasonContactedFlag) AS CallReasonContactedFlag,
				IF(b.CallReasonDesc IS NULL,'New',b.CallReasonDesc) AS CallReasonDesc,
				c.CampaignNumber,c.CampaignName, COUNT(a.CustomerId) AS TOT
				FROM t_gn_customer a
				LEFT JOIN t_lk_callreason b ON a.CallReasonId=b.CallReasonId
				LEFT JOIN t_gn_campaign c ON a.CampaignId=c.CampaignId
				WHERE 1=1";
				if($_REQUEST['cmp']){
					$sqlnew.=" AND c.CampaignId In (".$campaign1.")";
				}
				$sqlnew.=" AND a.CallReasonId is NULL
				GROUP BY a.CallReasonId,a.CampaignId
				ORDER BY a.CallReasonId ASC;";

	$qrynew = $ListPages->execute($sqlnew,__FILE__,__LINE__);
	while($rownew = $ListPages->fetchassoc($qrynew)){
			$dataNull1[$rownew['CallReasonDesc']][$rownew['CampaignName']] = $rownew['TOT'];
			$dataNull2[$rownew['CampaignName']] = $rownew['CampaignName'];
			$dataNull3[$rownew['CallReasonDesc']['CallReasonCode']] = $rownew['CallReasonDesc'];
			$dataNull4[$rownew['CallReasonCode']] = $rownew['CallReasonCode'];
	}*/

	//Uncontacted
	// foreach($dataunCon3 as $key=>$val){ $headerS[] = $val; }
	// foreach($dataunCon2 as $key=>$val){ $headerT[] = $val; }
	// foreach($dataunCon4 as $key=>$val){ $headerC[] = $val; }
	//Contacted
	// foreach($dataCont3 as $key=>$val){ $header2S[] = $val; }
	// foreach($dataCont2 as $key=>$val){ $header2T[] = $val; }
	// foreach($dataCont4 as $key=>$val){ $header2C[] = $val; }
	//Contacted
	// foreach($dataNull3 as $key=>$val){ $header3S[] = $val; }
	// foreach($dataNull2 as $key=>$val){ $header3T[] = $val; }
	// foreach($dataNull4 as $key=>$val){ $header3C[] = $val; }
	// print_r($headerS);
	
	
	echo "<th> &nbsp;&nbsp;&nbsp;Report Campaign Review History <br/></th>";
	echo "<th> &nbsp;&nbsp;&nbsp;Date Range  &nbsp;: $start_date To $end_date<br/></th>";
	echo "<th> &nbsp;&nbsp;&nbsp;Report Date : $today </th>";
	echo "<table width=\"99%\" class=\"custom-grid\" cellspacing=\"0\">";
	
	/********* HEADER *********/
	echo "<TABLE width=\"99%\" border=\"0\" align=\"center\">
			<DIV id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
			<THEAD><tr height=\"30\">
				<th rowspan=\"2\" nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">Call Reason</th>
				<th colspan=\"".sizeof($CampaignList)."\" nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">Campaign Name</th>
				<th rowspan=\"2\" nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">TOTAL</th></tr>
			<TR height=\"30\">";
			foreach($CampaignList as $Id => $Campaign){
				echo "<th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">".$Campaign['Nama']."</th>";
			}
	echo "</TR></THEAD></DIV>";
	
	/***** Contact & Uncontact List *****/
	foreach($CallReasonList as $Reason => $CallReason){
		foreach($CallReason as $CallReasonDesc => $CallReasonDetail){
			/***** Uncontacted *****/
			if($Reason=="UnContact"){
				echo "<DIV id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
					 <TR height=\"30\">
						<td nowrap style=\"text-align: center\" class=\"content-middle\">".$CallReasonDesc." [".$CallReasonDetail['Code']."]</td>";
						foreach($CampaignList as $CampaignId => $CampaignName){
							echo "<td nowrap style=\"text-align: center\" class=\"content-middle\">".($DataUnContact[$CallReasonDesc][$CampaignId]['Total']?$DataUnContact[$CallReasonDesc][$CampaignId]['Total']:0)."</td>";
							$TotalUncontactedPerReason[$CallReasonDesc] += ($DataUnContact[$CallReasonDesc][$CampaignId]['Total']?$DataUnContact[$CallReasonDesc][$CampaignId]['Total']:0);
							$TotalUncontactedPerCampaign[$CampaignId] += ($DataUnContact[$CallReasonDesc][$CampaignId]['Total']?$DataUnContact[$CallReasonDesc][$CampaignId]['Total']:0);
						}
						echo "<td nowrap style=\"text-align: center\" class=\"content-middle\">".$TotalUncontactedPerReason[$CallReasonDesc]."</td>";
				echo "</TR><DIV>";
			}
		}
	}
		/***** Total Uncontacted *****/
		echo "<DIV id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
			 <TR height=\"30\">
				<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">Total Uncontacted</td>";
				foreach($CampaignList as $CampaignId => $CampaignName){
					echo "<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">".$TotalUncontactedPerCampaign[$CampaignId]."</td>";
					$GTotalUncontacted += $TotalUncontactedPerCampaign[$CampaignId];
					$TotalTouchCampaign[$CampaignId] += $TotalUncontactedPerCampaign[$CampaignId];
				}
				echo "<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">".$GTotalUncontacted."</td>";
			echo "</TR><DIV>";
			
	foreach($CallReasonList as $Reason => $CallReason){
		foreach($CallReason as $CallReasonDesc => $CallReasonDetail){
			/***** Contacted *****/
			if($Reason=="Contact"){
				echo "<DIV id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
					 <TR height=\"30\">
						<td nowrap style=\"text-align: center\" class=\"content-middle\">".$CallReasonDesc." [".$CallReasonDetail['Code']."]</td>";
						foreach($CampaignList as $CampaignId => $CampaignName){
							echo "<td nowrap style=\"text-align: center\" class=\"content-middle\">".($DataContact[$CallReasonDesc][$CampaignId]['Total']?$DataContact[$CallReasonDesc][$CampaignId]['Total']:0)."</td>";
							$TotalContactedPerReason[$CallReasonDesc] += ($DataContact[$CallReasonDesc][$CampaignId]['Total']?$DataContact[$CallReasonDesc][$CampaignId]['Total']:0);
							$TotalContactedPerCampaign[$CampaignId] += ($DataContact[$CallReasonDesc][$CampaignId]['Total']?$DataContact[$CallReasonDesc][$CampaignId]['Total']:0);
						}
						echo "<td nowrap style=\"text-align: center\" class=\"content-middle\">".$TotalContactedPerReason[$CallReasonDesc]."</td>";
				echo "</TR><DIV>";
			}
		}
	}
		/***** Total Contacted *****/
		echo "<DIV id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
			 <TR height=\"30\">
				<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">Total Contacted</td>";
				foreach($CampaignList as $CampaignId => $CampaignName){
					echo "<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">".$TotalContactedPerCampaign[$CampaignId]."</td>";
					$GTotalContacted += $TotalContactedPerCampaign[$CampaignId];
					$TotalTouchCampaign[$CampaignId] += $TotalContactedPerCampaign[$CampaignId];
				}
				echo "<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">".$GTotalContacted."</td>";
			echo "</TR><DIV>";
			
		echo "<tr><td>&nbsp;</td></tr>";
		
		/***** Total Touch PerCampaign *****/
		echo "<DIV id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
			 <TR height=\"30\">
				<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">Total Touch</td>";
				foreach($CampaignList as $CampaignId => $CampaignName){
					echo "<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">".$TotalTouchCampaign[$CampaignId]."</td>";
					$GTotalTouchCampaign += $TotalTouchCampaign[$CampaignId];
				}
				echo "<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">".($GTotalContacted+$GTotalUncontacted)."</td>";
			echo "</TR><DIV>";
			
		/***** Total unTouch PerCampaign *****/
		echo "<DIV id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
			 <TR height=\"30\">
				<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">Total unTouch</td>";
				foreach($CampaignList as $CampaignId => $CampaignName){
					echo "<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">".($CampaignName['DataSize']-$TotalTouchCampaign[$CampaignId])."</td>";
					$GTotalunTouchCampaign += ($CampaignName['DataSize']-$TotalTouchCampaign[$CampaignId]);
				}
				echo "<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">".$GTotalunTouchCampaign."</td>";
			echo "</TR><DIV>";
		
		/***** Total Data PerCampaign *****/
		echo "<DIV id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
			 <TR height=\"30\">
				<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">Data Size</td>";
				foreach($CampaignList as $CampaignId => $CampaignName){
					echo "<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">".$CampaignName['DataSize']."</td>";
					$GTotalDataCampaign += $CampaignName['DataSize'];
				}
				echo "<td bgcolor=\"#3366FF\" nowrap style=\"text-align: center\" class=\"content-middle\">".($GTotalDataCampaign)."</td>";
			echo "</TR><DIV>";
	
	echo "</tr></table>";
	/****************************************************************************====>*/
	//******************************* Uncontacted *******************************//
?>