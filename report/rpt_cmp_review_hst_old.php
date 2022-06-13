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
	set_time_limit(500000);
	$ListPages -> pages = $db -> escPost('v_page'); 
	$ListPages -> setPage(10);
	// $objSQL = new mysql();
	
	$sql = "SELECT ch.CallReasonId,
				c.CallReasonCode,
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
				and subch.CallHistoryCallDate <= '".$_REQUEST['end_date']." 23:00:00'
				and subch.CustomerId = ch.CustomerId)
			";
		if($_REQUEST['cmp']){
			$sql.=" and cs.CampaignId in (".$campaign1.")";
		}
		$sql.=" group by ch.CallReasonId,cs.CampaignId
				order by ch.CallReasonId ASC;";
	// echo "<pre>".$sql."</pre>";
	// $ListPages -> query($sql,$sql1);
	// echo $ListPages -> getSQL();
	
	$qry = $ListPages->execute($sql,__FILE__,__LINE__);
	while($row = $ListPages->fetchassoc($qry)){
		if($row['CallReasonContactedFlag']==0){
			if($row['CallReasonId']==3 || $row['CallReasonId']==1){
				$dataunCon1['No Pick Up'][$row['CampaignName']] += $row['TOT'];
				$dataunCon2[$row['CampaignName']]	= $row['CampaignName'];
				$dataunCon3['No Pick Up']	= $row['CallReasonDesc'];
				$dataunCon4[$row['202']]	= $row['CallReasonCode'];
			}else{
				$dataunCon1[$row['CallReasonDesc']][$row['CampaignName']] = $row['TOT'];
				$dataunCon2[$row['CampaignName']]	= $row['CampaignName'];
				$dataunCon3[$row['CallReasonDesc']]	= $row['CallReasonDesc'];
				$dataunCon4[$row['CallReasonCode']]	= $row['CallReasonCode'];
			}
		}else if($row['CallReasonContactedFlag']==1){
			$dataCont1[$row['CallReasonDesc']][$row['CampaignName']] = $row['TOT'];
			$dataCont2[$row['CampaignName']]	= $row['CampaignName'];
			$dataCont3[$row['CallReasonDesc']]	= $row['CallReasonDesc'];
			$dataCont4[$row['CallReasonCode']]	= $row['CallReasonCode'];
		// }else{
			// $dataNull1[$row['CallReasonDesc']][$row['CampaignName']] = $row['TOT'];
			// $dataNull2[$row['CampaignName']] = $row['CampaignName'];
			// $dataNull3[$row['CallReasonDesc']['CallReasonCode']] = $row['CallReasonDesc'];
			// $dataNull4[$row['CallReasonCode']] = $row['CallReasonCode'];
		}
	}
	
	$sqlnew = "SELECT 'New' as CallReasonId,
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
	// echo "<pre>".$sqlnew."</pre>";
	$qrynew = $ListPages->execute($sqlnew,__FILE__,__LINE__);
	while($rownew = $ListPages->fetchassoc($qrynew)){
			$dataNull1[$rownew['CallReasonDesc']][$rownew['CampaignName']] = $rownew['TOT'];
			$dataNull2[$rownew['CampaignName']] = $rownew['CampaignName'];
			$dataNull3[$rownew['CallReasonDesc']['CallReasonCode']] = $rownew['CallReasonDesc'];
			$dataNull4[$rownew['CallReasonCode']] = $rownew['CallReasonCode'];
	}
	// echo "<pre>";
	// print_r($dataNull1);
	// print_r($dataNull2);
	// print_r($dataNull3);
	// print_r($dataNull4);
	// echo "</pre>";
	//Uncontacted
	foreach($dataunCon3 as $key=>$val){ $headerS[] = $val; }
	foreach($dataunCon2 as $key=>$val){ $headerT[] = $val; }
	foreach($dataunCon4 as $key=>$val){ $headerC[] = $val; }
	//Contacted
	foreach($dataCont3 as $key=>$val){ $header2S[] = $val; }
	foreach($dataCont2 as $key=>$val){ $header2T[] = $val; }
	foreach($dataCont4 as $key=>$val){ $header2C[] = $val; }
	//Contacted
	foreach($dataNull3 as $key=>$val){ $header3S[] = $val; }
	foreach($dataNull2 as $key=>$val){ $header3T[] = $val; }
	foreach($dataNull4 as $key=>$val){ $header3C[] = $val; }
	// print_r($headerS);
	
	
	
	echo "<th> &nbsp;&nbsp;&nbsp;Date Range  &nbsp;: $start_date To $end_date<br/></th>";
	echo "<th> &nbsp;&nbsp;&nbsp;Report Date : $today </th>";
	echo "<table width=\"99%\" class=\"custom-grid\" cellspacing=\"0\">";
	//******************************* Uncontacted *******************************//
	for($k=0;$k<=sizeof($headerS);$k++){
		if($k==0){
			echo "<table width=\"99%\" border=\"0\" align=\"center\">
				  <div id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
				  <thead>
				  <tr height=\"30\">
					<th rowspan=\"2\" nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">Call Reason</th>
					<th colspan=\"".sizeof($headerT)."\" nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">CAMPAIGN NAME</th>
					<th rowspan=\"2\" nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">TOTAL</th></tr>
				  <tr height=\"30\">";
			for($i=0;$i<sizeof($headerT);$i++){
				echo "<th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">".$headerT[$i]."</th>";
			}
			//echo "<th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">TOTAL</th>";
			echo "</tr></thead></div>";
		}else{
			echo "<div id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
				  <tr height=\"30\">";
			echo "<td nowrap style=\"text-align: center\" class=\"content-middle\">".$headerS[$k-1]." [".$headerC[$k-1]."]</td>";
			$sTotal = 0;
			for($i=0;$i<sizeof($headerT);$i++){
				echo "<td nowrap style=\"text-align: center\" class=\"content-middle\">".($dataunCon1[$headerS[$k-1]][$headerT[$i]]!=""?$dataunCon1[$headerS[$k-1]][$headerT[$i]]:"0")."</td>";
				$sTotal +=($dataunCon1[$headerS[$k-1]][$headerT[$i]]!=""?$dataunCon1[$headerS[$k-1]][$headerT[$i]]:"0");
			}
			echo "<td nowrap style=\"text-align: center\" class=\"content-middle\">".$sTotal."</td>";
			echo "</tr></div>";
			for($r=0;$r<sizeof($headerT);$r++){
				$jumlah[$r][$r]+=($dataunCon1[$headerS[$k-1]][$headerT[$r]]!=""?$dataunCon1[$headerS[$k-1]][$headerT[$r]]:"0");
				$grandTOT['unc'][$r]+=($dataunCon1[$headerS[$k-1]][$headerT[$r]]!=""?$dataunCon1[$headerS[$k-1]][$headerT[$r]]:"0");
			}
		}
	}

	echo "<tr height=\"30\">
		  <th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">Total Un Contacted</th>";
		for($s=0;$s<sizeof($headerT);$s++){
			echo "<th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">".($jumlah[$s][$s]!=""?$jumlah[$s][$s]:"0")."</th>";
			$jTotal += ($jumlah[$s][$s]!=""?$jumlah[$s][$s]:"0");
		}
		echo "<th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">".$jTotal."</th>";
	//*************************************************************************//
	//******************************* Contacted *******************************//
	for($k=0;$k<=sizeof($header2S);$k++){
		if($k==0){
			echo "<tr></tr>";
		}else{
			echo "<div id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
				  <tr height=\"30\">";
			echo "<td nowrap style=\"text-align: center\" class=\"content-middle\">".$header2S[$k-1]." [".$header2C[$k-1]."]</td>";
			$sTotal2 = 0;
			for($i=0;$i<sizeof($headerT);$i++){
				echo "<td nowrap style=\"text-align: center\" class=\"content-middle\">".($dataCont1[$header2S[$k-1]][$headerT[$i]]!=""?$dataCont1[$header2S[$k-1]][$headerT[$i]]:"0")."</td>";
				$sTotal2 +=($dataCont1[$header2S[$k-1]][$headerT[$i]]!=""?$dataCont1[$header2S[$k-1]][$headerT[$i]]:"0");
			}
			echo "<td nowrap style=\"text-align: center\" class=\"content-middle\">".$sTotal2."</td>";
			echo "</tr></div>";
			for($r=0;$r<sizeof($headerT);$r++){
				$jumlah2[$r][$r]+=($dataCont1[$header2S[$k-1]][$headerT[$r]]!=""?$dataCont1[$header2S[$k-1]][$headerT[$r]]:"0");
				$grandTOT['con'][$r]+=($dataCont1[$header2S[$k-1]][$headerT[$r]]!=""?$dataCont1[$header2S[$k-1]][$headerT[$r]]:"0");
			}
		}
	}
	echo "<thead>
		  <div id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
		  <tr height=\"30\">
		  <th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">Total Contacted</th>";
		for($s=0;$s<sizeof($headerT);$s++){
			echo "<th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">".($jumlah2[$s][$s]!=""?$jumlah2[$s][$s]:"0")."</th>";
			$jTotal2 +=($jumlah2[$s][$s]!=""?$jumlah2[$s][$s]:"0");
		}
		echo "<th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">".$jTotal2."</th>";
		echo "</tr></div></thead>";
	//*************************************************************************//
	//******************************* NULL *******************************//
	for($k=0;$k<=sizeof($header3S);$k++){
		if($k==0){
			echo "<tr></tr>";
		}else{
			echo "<div id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
				  <tr height=\"30\">";
			echo "<td nowrap style=\"text-align: center\" class=\"content-middle\">NEW [NEW]</td>";
			$sTotal3 = 0;
			for($i=0;$i<sizeof($headerT);$i++){
				echo "<td nowrap style=\"text-align: center\" class=\"content-middle\">".($dataNull1[$header3S[$k-1]][$headerT[$i]]!=""?$dataNull1[$header3S[$k-1]][$headerT[$i]]:"0")."</td>";
				$sTotal3 +=($dataNull1[$header3S[$k-1]][$headerT[$i]]!=""?$dataNull1[$header3S[$k-1]][$headerT[$i]]:"0");
			}
			echo "<td nowrap style=\"text-align: center\" class=\"content-middle\">".$sTotal3."</td>";
			echo "</tr>";
			for($r=0;$r<sizeof($headerT);$r++){
				$jumlah3[$r][$r]+=($dataNull1[$header3S[$k-1]][$headerT[$r]]!=""?$dataNull1[$header3S[$k-1]][$headerT[$r]]:"0");
				$grandTOT['unt'][$r]+=($dataNull1[$header3S[$k-1]][$headerT[$r]]!=""?$dataNull1[$header3S[$k-1]][$headerT[$r]]:"0");
			}
		}
	}

	echo "<thead>
		  <div id=\"rpt_top_content\" class=\"box-shadow\" style=\"width:1115px;height:auto;overflow:auto;\">
		  <tr height=\"30\">
		  <th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">Total New</th>";
		for($s=0;$s<sizeof($headerT);$s++){
			echo "<th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">".($jumlah3[$s][$s]!=""?$jumlah3[$s][$s]:"0")."</th>";
			$jTotal3 +=($jumlah3[$s][$s]!=""?$jumlah3[$s][$s]:"0");
		}
		echo "<th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">".$jTotal3."</th>";
		echo "</tr></div></thead>";
	//*************************************************************************//
	echo "<tr><th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">Grand Total</th>";
		$sumArray = array();
		foreach ($grandTOT as $k=>$subArray){
			foreach ($subArray as $id=>$value){
				$sumArray[$id]+=$value;
			}
		}
		
		for($s=0;$s<sizeof($headerT);$s++){
			echo "<th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">".($sumArray[$s]!=""?$sumArray[$s]:"0")."</th>";
		}
		// $GGTOT = $jTotal3+$jTotal2+$jTotal;
		$GGTOT = $jTotal3+$jTotal2+$jTotal;
		echo "<th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:center;\">".($GGTOT!=""?$GGTOT:"0")."</th>";
	echo "</tr></table>";
?>