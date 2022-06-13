<?
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	
	$start_date 	= $_REQUEST['start_date'];
	$end_date  		= $_REQUEST['end_date'];
	$cignasystem	= $_REQUEST['cignasystem'];
	$campaign		= $_REQUEST['cmp'];
	$today = date("Y-m-d");
	
	set_time_limit(500000);

		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(10);
		
		//Fungsi Database Uploaded
		function database_uploaded($campaign,$start_date="")
		{
			global $db;	
			$hours=array();
			//return $hours;
			$sql =" select count(a.CustomerId) as dataupload
					from t_gn_customer a
					left join t_gn_campaign b on a.CampaignId=b.CampaignId
					where b.CampaignNumber = $campaign";
					//return $sql;
			if($start_date != ""){
				$sql.=" AND CustomerUpdatedTs >= '$start_date 00:00:00' AND CustomerUpdatedTs <= '$start_date 23:00:00'";
			}
			$qry=mysql_query($sql);
			$row = mysql_fetch_array($qry);
			/*while ($row = mysql_fetch_array($qry))
			{
				$hours[$row['CampaignNumber']] = $row['Hours'];
			}*/
			return $row['dataupload'];
			//return $sql;
		}
		
		//Fungsi Solicited
		function solicited($campaign,$start_date="")
		{
			global $db;	
			$leads=array();
			$sql =" select count(a.CustomerId) as solicited
					from t_gn_customer a
					left join t_gn_campaign b on a.CampaignId=b.CampaignId
					where a.CallReasonId is not null and
					b.CampaignNumber = $campaign";
			if($start_date != ""){
				$sql.=" AND CustomerUpdatedTs >= '$start_date 00:00:00' AND CustomerUpdatedTs <= '$start_date 23:00:00'";
			}
			$qry=mysql_query($sql);
			$row = mysql_fetch_array($qry);
			// while ($row = mysql_fetch_array($qry))
			// {
				// $leads[$row['CampaignNumber']] = $row['Leads'];
			// }
			return $row['solicited'];
		}
		
		//Fungsi Attempt
		function attempting($campaign,$start_date="")
		{
			global $db;	
			$contact=array();
			$sql1 ="select count(b.CallHistoryId) as Attempt
					from t_gn_customer a
					left join t_gn_callhistory b on a.CustomerId=b.CustomerId
					left join t_gn_campaign c on a.CampaignId=c.CampaignId
					where a.CallReasonId is not null and
					c.CampaignNumber = $campaign";
			if($start_date != ""){
				$sql.=" AND CustomerUpdatedTs >= '$start_date 00:00:00' AND CustomerUpdatedTs <= '$start_date 23:00:00'";
			}
			$qry=mysql_query($sql1);
			$row = mysql_fetch_array($qry);
			// while ($row = mysql_fetch_array($qry))
			// {
				// $contact[$row['CampaignNumber']] = $row['Contact'];
			// }
			return $row['Attempt'];
		}
		
		//Fungsi Compelete
		function compeleted($campaign,$start_date="")
		{
			global $db;
			$terminated=array();
			$sql ="	select count(a.CustomerId) as completed
					from t_gn_customer a
					left join t_lk_callreason b on a.CallReasonId=b.CallReasonId
					left join t_gn_campaign c on a.CampaignId=c.CampaignId
					where b.CallReasonCategoryId in (4,3,5) and b.CallReasonStatusFlag = 1 and
					c.CampaignNumber = $campaign";
			if($start_date != ""){
				$sql.=" AND CustomerUpdatedTs >= '$start_date 00:00:00' AND CustomerUpdatedTs <= '$start_date 23:00:00'";
			}
			$qry=mysql_query($sql);
			$row = mysql_fetch_array($qry);
			// while ($row = mysql_fetch_array($qry))
			// {
				// $terminated[$row['CampaignNumber']] = $row['Terminate'];
			// }
			return $row['completed'];
		}
		
		//Fungsi TEC
		function tec($campaign,$start_date="")
		{
			global $db;	
			$tmr = array();
			$sql ="select count(a.CustomerId) as tec
					from t_gn_customer a
					left join t_lk_callreason b on a.CallReasonId=b.CallReasonId
					left join t_gn_campaign c on a.CampaignId=c.CampaignId
					where b.CallReasonCategoryId in (2,3,4,5) and b.CallReasonStatusFlag = 1 and
					c.CampaignNumber = $campaign";
			if($start_date != ""){
				$sql.=" AND CustomerUpdatedTs >= '$start_date 00:00:00' AND CustomerUpdatedTs <= '$start_date 23:00:00'";
			}
			$qry=mysql_query($sql);
			$row = mysql_fetch_array($qry);
			// while ($row = mysql_fetch_array($qry))
			// {
				// $terminated[$row['CampaignNumber']] = $row['Terminate'];
			// }
			return $row['tec'];
		}
		
		//Fungsi Sales
		function sales()
		{
			global $db;	
			$start_date 	= $_REQUEST['start_date'];
			$end_date  		= $_REQUEST['end_date'];
			$sales = array();
			$sql = " select
					ca.CampaignNumber,
					ca.CampaignId,
					sum(IF(c.CallReasonQue=1,1,0)) as Sales
				from t_gn_customer c
				left join t_gn_campaign ca on c.CampaignId = ca.CampaignId
				left join t_lk_callreason cr on c.CallReasonId = cr.CallReasonId
				group by ca.CampaignNumber";
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$sales[$row['CampaignNumber']] = $row['Sales'];
			}	
			return $sales;
		}
		
		//Fungsi ANP
		function anp()
		{
			global $db;	
			$start_date 	= $_REQUEST['start_date'];
			$end_date  		= $_REQUEST['end_date'];
			
			$anp = array();
			$sql = "SELECT  cmp.CampaignId,
						cmp.CampaignNumber AS CampaignNumber,
						agt.UserId AS agtuid,
						agt.id AS agentid,
						agt.full_name AS Agent_Name,
						spv.id AS spvid,
						spv.UserId AS spvuid,
						spv.full_name AS spv_name,
						sum(if(prp.PremiumGroupId=2,1,0)) as main,
						sum(if(prp.PremiumGroupId=3,1,0)) as sp,
						sum(if(prp.PremiumGroupId=1,1,0)) as dp,
						sum(if(prp.PayModeId=2,prp.ProductPlanPremium*12,0)+if(prp.PayModeId=1,prp.ProductPlanPremium,0)) as allpremium
						FROM
						t_gn_customer AS cst
						LEFT JOIN t_gn_insured AS ins ON ins.CustomerId = cst.CustomerId
						LEFT JOIN t_gn_policy AS plc ON plc.PolicyId = ins.PolicyId
						LEFT JOIN t_gn_productplan AS prp ON prp.ProductPlanId = plc.ProductPlanId
						LEFT JOIN t_gn_campaign AS cmp ON cst.CampaignId = cmp.CampaignId
						LEFT JOIN t_gn_product AS prd ON prd.ProductId = prp.ProductId
						LEFT JOIN t_gn_callhistory AS clh ON clh.CustomerId = cst.CustomerId AND clh.CallHistoryCallDate = cst.CustomerUpdatedTs
						LEFT JOIN tms_agent AS agt ON agt.UserId = cst.SellerId
						LEFT JOIN tms_agent AS spv ON spv.UserId = agt.spv_id
						LEFT JOIN t_lk_callreason crs ON crs.CallReasonId = cst.CallReasonId
						WHERE	cst.CallReasonQue=1
						Group By cmp.CampaignNumber ";
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$anp[$row['CampaignNumber']] = $row['allpremium'];
			}	
			return $anp;
		}
		
		//Fungsi Date Range
		function dateRange( $first, $last, $step = '+1 day', $format = 'Y/m/d' )
		{
			$dates = array();
			$current = strtotime( $first );
			$last = strtotime( $last );

			while( $current <= $last ) {

				$dates[] = date( $format, $current );
				$current = strtotime( $step, $current );
			}

			return $dates;
		}
		
		//Date with Value Function
		function dateVals( $first, $last, $step = '+1 day', $format = 'Y-m-d',$campaign )
		{
			$datVals = array();
			$dateFormat = 'Y-m-d';
			$current = strtotime( $first );
			$last = strtotime( $last );

			while( $current <= $last ) {

				$dates = array(date( $format, $current ) => array(
							database_uploaded($campaign,date( $dateFormat, $current )),
							solicited($campaign,date( $dateFormat, $current )),
							number_format((solicited($campaign)/database_uploaded($campaign))*100,2)."%",
							attempting($campaign,date( $dateFormat, $current )),
							number_format(attempting($campaign)/solicited($campaign),2),
							number_format(attempting($campaign)/compeleted($campaign),2),
							number_format(attempting($campaign)/tec($campaign),2),
							"MTD9",
							compeleted($campaign,date( $dateFormat, $current )),
							number_format((compeleted($campaign)/database_uploaded($campaign))*100,2)."%",
							"MTD12",
							"MTD13",
							"MTD14",
							"MTD15",
							tec($campaign,date( $dateFormat, $current )),
							"MTD17",
							"MTD18",
							"MTD19",
							"MTD20",
							"MTD21"
						)
							);
				$current = strtotime( $step, $current );
				$datVals+=$dates;
			}

			return $datVals;
		}
		
		//Query Index				
		$sql = 	"select distinct
					'ASEANINDO' as Sponsor,
					pr.ProductCode as ProductId,
					cg.CampaignGroupCode as CampaignSplit,
					cp.CampaignNumber as CampaignId,
					cp.CampaignName as CampaignName,
					Date(cp.CampaignStartDate) as CampaignStartDate,
					Date(cp.CampaignEndDate) as CampaignEndDate
				from t_gn_campaign cp
					left join t_gn_campaignproduct cmp on cp.CampaignId = cmp.CampaignId
					LEFT JOIN t_gn_product pr ON cmp.ProductId = pr.ProductId
					LEFT JOIN t_gn_campaigngroup cg ON pr.CampaignGroupId = cg.CampaignGroupId
				where 1=1 ";	
				if($_REQUEST['cmp'])
				{
					$sql.=" AND CampaignNumber='".$_REQUEST['cmp']."'";
				}
					$sql.=" group by cp.CampaignNumber ";
						
			//print_r($_REQUEST);
			/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
		$ListPages -> query($sql,$sql1);
		$ListPages -> result();
		$dateRanges = dateRange( $start_date, $end_date, $step = '+1 day', $format = 'Y/m/d' );
		$dateValss = dateVals( $start_date, $end_date, $step = '+1 day', $format = 'd-M',$campaign );
	SetNoCache();
	// echo "Database Uploaded => ".database_uploaded($campaign)."<br>";
	// echo "Solicited => ".solicited($campaign)."<br>";
	// echo "Solicited Rate % => ".number_format((solicited($campaign)/database_uploaded($campaign))*100,2)."%<br>";
	// echo "Total Attempt => ".attempting($campaign)."<br>";
	// echo "Attempt Ratio => ".number_format(attempting($campaign)/solicited($campaign),2)."<br>";
	// echo "Attempt per Complete => ".number_format(attempting($campaign)/compeleted($campaign),2)."<br>";
	// echo "Attempt per Contact => ".number_format(attempting($campaign)/tec($campaign),2)."<br>";
	// echo "Completes => ".compeleted($campaign)."<br>";
	// echo "Complete Penetration = > ".number_format((compeleted($campaign)/database_uploaded($campaign))*100,2)."%<br>";
	// echo "TEC/Contact => ".tec($campaign)."<br>";
	//print_r(sales());
	
?>
<fieldset class="corner">
<legend class="icon-product" style="color: teal;"> &nbsp;&nbsp;&nbsp;Campaign Information & Objectives &nbsp;&nbsp;&nbsp;</legend>
<legend style="color: #637dde;">
	<?php
		if($row = $db ->fetchrow($ListPages->result))
			{
		echo "  
						<th>&nbsp;Product ID: ".$row->ProductId." </th> ||
						<th>&nbsp;Campaign Split : ".$row->CampaignSplit." </th> ||
						<th>&nbsp;Campaign ID : ".$row->CampaignId." </th> ||
						<th>&nbsp;Campaign Name : ".$row->CampaignName." </th><br/>
						<th>&nbsp;Sponsor : ASEANINDO</th> ||
						<th>&nbsp;Launch Date : ".$row->CampaignStartDate." </th> ||
						<th>&nbsp;Close Date : ".$row->CampaignEndDate." </th> ||
						<th>&nbsp;Report Date : ".$today." </th><br/></br>
						<th>&nbsp;Date Range  &nbsp;: ".$start_date." To ".$end_date."</th>
					 ";
	}
	else
	{
		echo "  
						<th>&nbsp;Product ID: ".$row->ProductId." </th> ||
						<th>&nbsp;Campaign Split : ".$row->CampaignSplit." </th> ||
						<th>&nbsp;Campaign ID : ".$row->CampaignId." </th> ||
						<th>&nbsp;Campaign Name : ".$row->CampaignName." </th><br/>
						<th>&nbsp;Sponsor : ASEANINDO</th> ||
						<th>&nbsp;Launch Date : ".$row->LaunchDate." </th> ||
						<th>&nbsp;Close Date : ".$row->CloseDate." </th> ||
						<th>&nbsp;Report Date : ".$today." </th><br/></br>
						<th>&nbsp;Date Range  &nbsp;: ".$start_date." To ".$end_date."</th>
					 ";
	}
	 ?>
<?php

	$contact_performances = array(
		"Contact Performance" => array(
				"MTD"
			),
		"Database" => array(
				database_uploaded($campaign)
			),
		"Solicited" => array(
				solicited($campaign)
			),
		"Solicited Rate %" => array(
				number_format((solicited($campaign)/database_uploaded($campaign))*100,2)."%"
			),
		"Attempt" => array(
				attempting($campaign)
			),
		"Attempt Ratio" => array(
				number_format(attempting($campaign)/solicited($campaign),2)
			),
		"Attempt per Complete" => array(
				number_format(attempting($campaign)/compeleted($campaign),2)
			),
		"Attempt per Contact" => array(
				number_format(attempting($campaign)/tec($campaign),2)
			),
		"Atempt per Hour" => array(
				"MTD9"
			),
		"Completes" => array(
				compeleted($campaign)
			),
		"Completes Penetration %" => array(
				number_format((compeleted($campaign)/database_uploaded($campaign))*100,2)."%"
			),
		"Completes per Hour" => array(
				"MTD12"
			),
		"Callbacks" => array(
				"MTD13"
			),
		"Callbacks per Hour" => array(
				"MTD14"
			),
		"Callbacks Left %" => array(
				"MTD15"
			),
		"Contact" => array(
				tec($campaign)
			),
		"Contacts per Complete %" => array(
				"MTD17"
			),
		"Contact per Hour" => array(
				"MTD18"
			),
		"Not Interest" => array(
				"MTD19"
			),
		"Miss Customers" => array(
				"MTD20"
			),
		"NPU" => array(
				"MTD21"
			)
	);

	echo "<br><table border=\"1\" width=\"99%\" class=\"custom-grid\" cellspacing=\"0\">";
	$i=0;
	foreach($contact_performances as $key => $val){
		echo "<tr>";
		echo "<th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:left;\">".$key."</th>
			  <th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:left;\">".$val[0]."</th>";
		foreach($dateValss as $key => $val){
			if($i==0){
				echo "<td>".$key."</td>";
			}else{
				echo "<td>".$val[$i-1]."</td>";
			}
		}
		echo "</tr>";
		$i++;
	}
	echo "</table>";

// Sales Matrics Table
	$sales_metrics = array(
		"Sales & Metrics" => array ("N/A"),
		"PIF" => array ("N/A"),
		"AARP" => array ("N/A"),
		"ANP" => array ("N/A"),
		"Average Premium" => array ("N/A"),
		"Contact Rate %" => array ("N/A"),
		"Sales Close Rate %" => array ("N/A"),
		"Response Rate %" => array ("N/A"),
		"Sales per Hour" => array ("N/A")
	);
	
	$MTD2 = array(
		1=>"N/A","N/A","N/A","N/A","N/A","N/A","N/A","N/A","N/A"
	);

echo "<br><table border=\"1\" width=\"99%\" class=\"custom-grid\" cellspacing=\"0\">";
	$i=0;
	foreach($sales_metrics as $key => $val){
		echo "<tr>";
		echo "<th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:left;\">".$key."</th>
			  <th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:left;\">".$val[0]."</th>";
		foreach($dateValss as $key => $val){
			if($i==0){
				echo "<td>".$key."</td>";
			}else{
				echo "<td>".$val[$i-1]."</td>";
			}
		}
		echo "</tr>";
		$i++;
	}
echo "</table>";

// Agent & Hours Table
	$agent_hour = array(
		"Agent & Hours" => array("N/A"),
		"Log In Hours" => array("N/A"),
		"Agents" => array("N/A"),
		"Average Worked Hours" => array("N/A"),
		"Talk Time %" => array("N/A"),
		"Wait Time %" => array("N/A"),
		"Wrap Time %" => array("N/A")
	);

echo "<br><table border=\"1\" width=\"99%\" class=\"custom-grid\" cellspacing=\"0\">";
	$i=0;
	foreach($agent_hour as $key => $val){
		echo "<tr>";
		echo "<th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:left;\">".$key."</th>
			  <th nowrap bgcolor=\"#3366FF\" class=\"custom-grid th-middle\" style=\"color:#FFFFFF;text-align:left;\">".$val[0]."</th>";
		foreach($dateValss as $key => $val){
			if($i==0){
				echo "<td>".$key."</td>";
			}else{
				echo "<td>".$val[$i-1]."</td>";
			}
		}
		echo "</tr>";
		$i++;
	}
echo "</table>";
?>