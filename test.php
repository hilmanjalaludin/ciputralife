<?php
require(dirname(__FILE__).'/class/MYSQLConnect.php');

	$objSQL = new mysql();

	$sql = "select a.CallReasonId,
			if(b.CallReasonContactedFlag is null,2,b.CallReasonContactedFlag) as CallReasonContactedFlag,
			if(b.CallReasonDesc is null,\"Un Touch\",b.CallReasonDesc) as CallReasonDesc,
			c.CampaignNumber,c.CampaignName,count(a.CustomerId) as TOT 
			from t_gn_customer a 
			left join t_lk_callreason b on a.CallReasonId=b.CallReasonId 
			left join t_gn_campaign c on a.CampaignId=c.CampaignId 
			where 1=1 
			AND c.CampaignName like '%FPATEL%' 
			group by a.CallReasonId,a.CampaignId
			order by a.CallReasonId asc";
	
	$qry = $objSQL->execute($sql,__FILE__,__LINE__);
	while($row = $objSQL->fetchassoc($qry)){
		if($row['CallReasonContactedFlag']==0){
			$dataunCon1[$row['CallReasonDesc']][$row['CampaignNumber']] = $row['TOT'];
			$dataunCon2[$row['CampaignNumber']]	= $row['CampaignNumber'];
			$dataunCon3[$row['CallReasonDesc']]	= $row['CallReasonDesc'];
		}else if($row['CallReasonContactedFlag']==1){
			$dataCont1[$row['CallReasonDesc']][$row['CampaignNumber']] = $row['TOT'];
			$dataCont2[$row['CampaignNumber']]	= $row['CampaignNumber'];
			$dataCont3[$row['CallReasonDesc']]	= $row['CallReasonDesc'];
		}else{
			$dataNull1[$row['CallReasonDesc']][$row['CampaignNumber']] = $row['TOT'];
			$dataNull2[$row['CampaignNumber']]	= $row['CampaignNumber'];
			$dataNull3[$row['CallReasonDesc']]	= $row['CallReasonDesc'];
		}
	}
	
	//Uncontacted
	foreach($dataunCon3 as $key=>$val){ $headerS[] = $val; }
	foreach($dataunCon2 as $key=>$val){ $headerT[] = $val; }
	//Contacted
	foreach($dataCont3 as $key=>$val){ $header2S[] = $val; }
	foreach($dataCont2 as $key=>$val){ $header2T[] = $val; }
	//Contacted
	foreach($dataNull3 as $key=>$val){ $header3S[] = $val; }
	foreach($dataNull2 as $key=>$val){ $header3T[] = $val; }
	
	echo "<table border=\"1px\">";
	//******************************* Uncontacted *******************************//
	for($k=0;$k<sizeof($headerS);$k++){
		if($k==0){
			echo "<tr><td>Call Reason</td>";
			for($i=0;$i<sizeof($headerT);$i++){
				echo "<td>".$headerT[$i]."</td>";
			}
			echo "<td>TOTAL</td>";
			echo "</tr>";
		}else{
			echo "<tr><td>".$headerS[$k]."</td>";
			$sTotal = 0;
			for($i=0;$i<sizeof($headerT);$i++){
				echo "<td>".($dataunCon1[$headerS[$k]][$headerT[$i]]!=""?$dataunCon1[$headerS[$k]][$headerT[$i]]:"0")."</td>";
				$sTotal +=($dataunCon1[$headerS[$k]][$headerT[$i]]!=""?$dataunCon1[$headerS[$k]][$headerT[$i]]:"0");
			}
			echo "<td>".$sTotal."</td>";
			echo "</tr>";
			for($r=0;$r<sizeof($headerT);$r++){
				$jumlah[$r][$r]+=($dataunCon1[$headerS[$k]][$headerT[$r]]!=""?$dataunCon1[$headerS[$k]][$headerT[$r]]:"0");
				$grandTOT['unc'][$r]+=($dataunCon1[$headerS[$k]][$headerT[$r]]!=""?$dataunCon1[$headerS[$k]][$headerT[$r]]:"0");
			}
		}
	}

	echo "<tr><td>Total Un Contacted</td>";
		for($s=0;$s<sizeof($headerT);$s++){
			echo "<td>".($jumlah[$s][$s]!=""?$jumlah[$s][$s]:"0")."</td>";
			$jTotal += ($jumlah[$s][$s]!=""?$jumlah[$s][$s]:"0");
		}
		echo "<td>".$jTotal."</td>";
	//*************************************************************************//
	//******************************* Contacted *******************************//
	for($k=0;$k<sizeof($header2S);$k++){
		if($k==0){
			echo "<tr></tr>";
		}else{
			echo "<tr><td>".$header2S[$k]."</td>";
			$sTotal2 = 0;
			for($i=0;$i<sizeof($headerT);$i++){
				echo "<td>".($dataCont1[$header2S[$k]][$headerT[$i]]!=""?$dataCont1[$header2S[$k]][$headerT[$i]]:"0")."</td>";
				$sTotal2 +=($dataCont1[$header2S[$k]][$headerT[$i]]!=""?$dataCont1[$header2S[$k]][$headerT[$i]]:"0");
			}
			echo "<td>".$sTotal2."</td>";
			echo "</tr>";
			for($r=0;$r<sizeof($headerT);$r++){
				$jumlah2[$r][$r]+=($dataCont1[$header2S[$k]][$headerT[$r]]!=""?$dataCont1[$header2S[$k]][$headerT[$r]]:"0");
				$grandTOT['con'][$r]+=($dataCont1[$header2S[$k]][$headerT[$r]]!=""?$dataCont1[$header2S[$k]][$headerT[$r]]:"0");
			}
		}
	}
	echo "<tr><td>Total Contacted</td>";
		for($s=0;$s<sizeof($headerT);$s++){
			echo "<td>".($jumlah2[$s][$s]!=""?$jumlah2[$s][$s]:"0")."</td>";
			$jTotal2 +=($jumlah2[$s][$s]!=""?$jumlah2[$s][$s]:"0");
		}
		echo "<td>".$jTotal2."</td>";
	//*************************************************************************//
	//******************************* NULL *******************************//
	for($k=0;$k<=sizeof($header3S);$k++){
		if($k==0){
			echo "<tr></tr>";
		}else{
			echo "<tr><td>".$header3S[$k-1]."</td>";
			$sTotal3 = 0;
			for($i=0;$i<sizeof($headerT);$i++){
				echo "<td>".($dataNull1[$header3S[$k-1]][$headerT[$i]]!=""?$dataNull1[$header3S[$k-1]][$headerT[$i]]:"0")."</td>";
				$sTotal3 +=($dataNull1[$header3S[$k-1]][$headerT[$i]]!=""?$dataNull1[$header3S[$k-1]][$headerT[$i]]:"0");
			}
			echo "<td>".$sTotal3."</td>";
			echo "</tr>";
			for($r=0;$r<sizeof($headerT);$r++){
				$jumlah3[$r][$r]+=($dataNull1[$header3S[$k-1]][$headerT[$r]]!=""?$dataNull1[$header3S[$k-1]][$headerT[$r]]:"0");
				$grandTOT['unt'][$r]+=($dataCont1[$header2S[$k]][$headerT[$r]]!=""?$dataCont1[$header2S[$k]][$headerT[$r]]:"0");
			}
		}
	}

	echo "<tr><td>Total New</td>";
		for($s=0;$s<sizeof($headerT);$s++){
			echo "<td>".($jumlah3[$s][$s]!=""?$jumlah3[$s][$s]:"0")."</td>";
			$jTotal3 +=($jumlah3[$s][$s]!=""?$jumlah3[$s][$s]:"0");
		}
		echo "<td>".$jTotal3."</td>";
	//*************************************************************************//
	echo "</tr><tr><td>Grand Total</td>";
		$sumArray = array();
		foreach ($grandTOT as $k=>$subArray){
			foreach ($subArray as $id=>$value){
				$sumArray[$id]+=$value;
			}
		}
		for($s=0;$s<sizeof($headerT);$s++){
			echo "<td>".($sumArray[$s]!=""?$sumArray[$s]:"0")."</td>";
		}
		$GGTOT = $jTotal3+$jTotal2+$jTotal;
		echo "<td>".($GGTOT!=""?$GGTOT:"0")."</td>";
	echo "</tr></table>";
?>