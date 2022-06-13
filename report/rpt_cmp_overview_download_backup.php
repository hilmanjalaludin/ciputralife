<?
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	
	$start_date 	= $_REQUEST['start_date'];
	$end_date  		= $_REQUEST['end_date'];
	$cignasystem	= $_REQUEST['cignasystem'];
	$campaign		= explode(",",$_REQUEST['cmp']);
	$campaign1		= implode("','",$campaign);
	$today = date("Y-m-d");
	
	/** FUNGSI EXCEL **/
	header("Content-type: application/vnd-ms-excel");
	$name		="CignaCampaignOverview";
	$file		=".xls";
	$sdate		=$start_date;
	$filename 	= $name.$sdate."To".$end_date.$file;
	
	header("Content-Disposition: attachment; filename=".($filename));
	
	set_time_limit(500000);

		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(10);
		
		//Fungsi Hours
		function jaammm()
		{
			global $db;	
			$jaammm=array();
			$sql ="  SELECT
						ca.CampaignId, ca.CampaignNumber, 
						SUM((ac.EndCallTs - ac.StartCallTs)/3600) AS Hours
					FROM t_gn_activitycall AS ac
						LEFT JOIN t_gn_customer c ON c.CustomerId=ac.CustomerId
						LEFT JOIN t_gn_campaign ca ON ca.CampaignId=c.CampaignId
					WHERE 1=1
						AND DATE(c.CustomerUpdatedTs) >= '".$_REQUEST['start_date']."'
						AND DATE(c.CustomerUpdatedTs) <= '".$_REQUEST['end_date']."'
					GROUP BY ca.CampaignNumber";
			
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$jaammm[$row['CampaignNumber']] = $row['Hours'];
			}	
			return $jaammm;
		}
		
		//Fungsi Leads
		function leads()
		{
			global $db;	
			$leads=array();
			$sql =" SELECT
						ca.CampaignNumber, ca.CampaignId, 
						COUNT(DISTINCT c.CustomerId) AS Leads
					FROM t_gn_customer c
						LEFT JOIN t_gn_campaign ca ON c.CampaignId = ca.CampaignId
					WHERE 1=1
						AND ca.CampaignNumber IS NOT NULL
						AND ca.CampaignId IS NOT NULL
					GROUP BY ca.CampaignNumber ";
					
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$leads[$row['CampaignNumber']] = $row['Leads'];
			}	
			return $leads;
		}
		
		//Fungsi Solicited
		function solicited()
		{
			global $db;	
			$solicited=array();
			$sql =" SELECT 
						b.CampaignNumber, 
						SUM(IF(a.CustomerUpdatedTs IS NOT NULL, 1,0)) AS Solicited
					FROM t_gn_customer a
						LEFT JOIN t_gn_campaign b ON a.CampaignId = b.CampaignId
					WHERE 1=1 
						AND DATE(a.CustomerUpdatedTs) >= '".$_REQUEST['start_date']."' 
						AND DATE(a.CustomerUpdatedTs) <= '".$_REQUEST['end_date']."'
					GROUP BY b.CampaignNumber ";
					
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$solicited[$row['CampaignNumber']] = $row['Solicited'];
			}	
			return $solicited;
		}
		
		//Fungsi Contact
		function contact()
		{
			global $db;	
			$contact=array();
			$sql =" SELECT
						ca.CampaignNumber, ca.CampaignId, 
						COUNT(DISTINCT IF(a.CallReasonContactedFlag = 1,c.CustomerId,0)) AS Contact
					FROM t_gn_customer c
						LEFT JOIN t_gn_campaign ca ON c.CampaignId = ca.CampaignId
						LEFT JOIN t_lk_callreason a ON c.CallReasonId = a.CallReasonId
					WHERE 1=1 
						AND DATE(c.CustomerUpdatedTs) >= '".$_REQUEST['start_date']."' 
						AND DATE(c.CustomerUpdatedTs) <= '".$_REQUEST['end_date']."'
					GROUP BY ca.CampaignNumber ";
			//echo $sql;
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$contact[$row['CampaignNumber']] = $row['Contact'];
			}
			return $contact;
		}
		
		
		//Fungsi Terminated Leads
		function terminated()
		{
			global $db;	
			$terminated=array();
			$sql = "SELECT 
						ca.CampaignNumber, ca.CampaignId, 
						COUNT(DISTINCT IF(a.CallReasonTerminate = 1,c.CustomerId,0)) AS Terminate
					FROM t_gn_customer c
						LEFT JOIN t_gn_campaign ca ON c.CampaignId = ca.CampaignId
						LEFT JOIN t_lk_callreason a ON c.CallReasonId = a.CallReasonId
					WHERE 1=1
						AND DATE(c.CustomerUpdatedTs) >= '".$_REQUEST['start_date']."' 
						AND DATE(c.CustomerUpdatedTs) <= '".$_REQUEST['end_date']."'
					GROUP BY ca.CampaignNumber";
			
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$terminated[$row['CampaignNumber']] = $row['Terminate'];
			}
			return $terminated;
		}
		
		//Fungsi TMR
		function tmr()
		{
			global $db;	
			$tmr = array();
			$sql = "SELECT 
						c.CampaignNumber AS CampaignNumber, 
						c.CampaignId AS CampaignId, 
						COUNT(DISTINCT a.AssignSelerId) AS tmr
					FROM t_gn_assignment a
						LEFT JOIN t_gn_customer b ON a.CustomerId = b.CustomerId
						LEFT JOIN t_gn_campaign c ON b.CampaignId = c.CampaignId
					WHERE 
						a.AssignSelerId IS NOT NULL 
						AND DATE(b.CustomerUpdatedTs) >= '".$_REQUEST['start_date']."' 
						AND DATE(b.CustomerUpdatedTs) <= '".$_REQUEST['end_date']."'
					GROUP BY c.CampaignNumber";
			//echo $sql;
			$qry=mysql_query($sql);
			while($row = mysql_fetch_array($qry))
			{
				$tmr[$row['CampaignNumber']] = $row['tmr'];
			}
			return $tmr;
		}
		
		//Fungsi Attempt
		function attempt()
		{
			global $db;	
			$attempt = array();
			$sql = "SELECT DISTINCT 
						d.CampaignNumber,
						COUNT(a.CallHistoryId) AS Attempt
					FROM t_gn_callhistory a
						LEFT JOIN t_gn_customer b ON a.CustomerId=b.CustomerId
						LEFT JOIN tms_agent c on a.CreatedById=c.UserId
						LEFT JOIN t_gn_campaign d ON b.CampaignId = d.CampaignId
					WHERE 1=1
						AND DATE(a.CallHistoryCreatedTs)>= '".$_REQUEST['start_date']."' 
						AND DATE(a.CallHistoryCreatedTs)<= '".$_REQUEST['end_date']."'
					GROUP BY d.CampaignNumber";
			//echo $sql;
			$qry=mysql_query($sql);
			while($row = mysql_fetch_array($qry))
			{
				$attempt[$row['CampaignNumber']] = $row['Attempt'];
			}
			return $attempt;
		}
		
		//Fungsi Sales
		function sales()
		{
			global $db;	
			$sales = array();
			$sql = " SELECT 
						f.CampaignId,
						f.CampaignNumber, COUNT(DISTINCT a.CustomerId) AS Sales
					FROM t_gn_policyautogen a
						LEFT JOIN t_gn_policy b ON a.PolicyNumber=b.PolicyNumber
						LEFT JOIN t_gn_customer c ON a.CustomerId=c.CustomerId
						LEFT JOIN t_gn_assignment d ON c.CustomerId=d.CustomerId
						LEFT JOIN t_gn_productplan e ON b.ProductPlanId=e.ProductPlanId
						LEFT JOIN t_gn_campaign f ON c.CampaignId = f.CampaignId
					WHERE 
						DATE(b.PolicySalesDate)>='".$_REQUEST['start_date']."' AND 
						DATE(b.PolicySalesDate)<='".$_REQUEST['end_date']."' AND 
						c.CallReasonId IN(15,16)
					GROUP BY f.CampaignNumber ";
			
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
			$anp = array();
			$sql = "SELECT 
						f.CampaignId, f.CampaignNumber,
						SUM(b.Premi) AS tmp, SUM(IF(e.PayModeId=2,(b.Premi*12), b.Premi)) AS ANP
					FROM t_gn_policyautogen a
						LEFT JOIN t_gn_policy b ON a.PolicyNumber=b.PolicyNumber
						LEFT JOIN t_gn_customer c ON a.CustomerId=c.CustomerId
						LEFT JOIN t_gn_assignment d ON c.CustomerId=d.CustomerId
						LEFT JOIN t_gn_productplan e ON b.ProductPlanId=e.ProductPlanId
						LEFT JOIN t_gn_campaign f ON c.CampaignId = f.CampaignId
					WHERE 
						DATE(b.PolicySalesDate)>='".$_REQUEST['start_date']."' AND 
						DATE(b.PolicySalesDate)<='".$_REQUEST['end_date']."' AND 
						c.CallReasonId IN(15,16)
					GROUP BY f.CampaignNumber ";
			
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$anp[$row['CampaignNumber']] = $row['ANP'];
			}	
			return $anp;
		}
		
		//Query Index				
		$sql = "SELECT DISTINCT
					'ASEANINDO' AS Sponsor, pr.ProductCode,
					cg.CampaignGroupCode, cp.CampaignId,
					cp.CampaignNumber, cp.CampaignName, 
					DATE(cp.CampaignStartDate) AS CampaignStartDate, 
					DATE(cp.CampaignEndDate) AS CampaignEndDate
				FROM t_gn_campaign cp
					LEFT JOIN t_gn_campaignproduct cmp ON cp.CampaignId = cmp.CampaignId
					LEFT JOIN t_gn_product pr ON cmp.ProductId = pr.ProductId
					LEFT JOIN t_gn_campaigngroup cg ON pr.CampaignGroupId = cg.CampaignGroupId
				WHERE 1=1";	
				if($_REQUEST['cmp'])
				{
					$sql.=" AND cp.CampaignId In ('".$campaign1."')";
				}
					$sql.=" group by CampaignNumber ";
		
		$ListPages -> query($sql,$sql1);
		$ListPages -> result();
			
	SetNoCache();

?>			
<fieldset class="corner">
<legend class="icon-product" style="color: teal;"> &nbsp;&nbsp;&nbsp;Preview Campaign Overview &nbsp;&nbsp;&nbsp;</legend>
<legend style="color: #637dde;">
<?php
		echo "<th> &nbsp;&nbsp;&nbsp;Date Range  &nbsp;: $start_date To $end_date<br/></th>";
		echo "<th> &nbsp;&nbsp;&nbsp;Report Date : $today </th>";
?>
</legend>
<table width="99%" class="custom-grid" cellspacing="0" >
<thead>
	<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
	<table width="99%" border="0" align="center">
	<tr height="30">
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;No.</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Sponsor</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Product ID</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Campaign Split</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Campaign ID</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Campaign Name</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Start Date</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;End Date</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Leads</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Solicited</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Contacts</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Terminated Leads</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Attempt</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Leads Remaining</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Hours</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;TMR's (FTE)</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Leads Allocated per TMR</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Sales</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;ANP</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Average Premium</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Contact %</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;CPH</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;SCR</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;SPH</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;ANP PH</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;ANP per TMR</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Sales per TMR</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;AARP</th>
	</tr>
	</div>
</thead>
</fieldset>	
<tbody>
	<?php
		
		$rowhours = 0;
		$no = (($ListPages -> start) + 1);
		
		$jam_getok = array();
		$jam_getok = jaammm();
		//var_dump($jam_getok);
		
		$leads1 = array();
		$leads1=leads();
		//var_dump($leads1);
		
		$solicited1 = array();
		$solicited1 = solicited();
		//var_dump($leads1);
		
		$contact1 = array();
		$contact1=contact();
		//var_dump($contact1);
		
		$termin = array();
		$termin=terminated();
		//var_dump($termin);
		
		$etem = array();
		$etem=attempt();
		//var_dump($etem);
		
		$tmr1 = array();
		$tmr1=tmr();
		//var_dump($tmr1);
		
		$sales12 = array();
		$sales12 = sales();
		//var_dump($sales12);
		
		$anp1 = array();
		$anp1 = anp();
		//var_dump($anp1);
		
		//var_dump($end_date);
		
		while($row = $db ->fetchrow($ListPages->result))
		{
			/** OUTPUT **/
			$oLeads			= ($leads1[$row ->CampaignNumber] ? $leads1[$row ->CampaignNumber] :0);
			$oSolicited		= ($solicited1[$row ->CampaignNumber] ? $solicited1[$row ->CampaignNumber] :0);
			$oContact		= ($contact1[$row ->CampaignNumber] ? $contact1[$row ->CampaignNumber] :0);
			$oTerminLeads	= ($termin[$row ->CampaignNumber] ? $termin[$row ->CampaignNumber] :0);
			$oAttempt		= ($etem[$row ->CampaignNumber] ? $etem[$row ->CampaignNumber] :0);
			$oHours			= ($jam_getok[$row ->CampaignNumber] ? $jam_getok[$row ->CampaignNumber] :0);
			$oTMR			= ($tmr1[$row->CampaignNumber] ? $tmr1[$row->CampaignNumber] :0);
			$oSales			= ($sales12[$row ->CampaignNumber] ? $sales12[$row ->CampaignNumber] :0);
			$oANP			= ($anp1[$row->CampaignNumber] ? $anp1[$row->CampaignNumber] :0);
		
			/** NGITUNG **/
			$LeadsRemain 	= ($leads1[$row ->CampaignNumber] ? ($leads1[$row ->CampaignNumber] - $termin[$row ->CampaignNumber]) :0);
			$LeadsAllocate	= ($leads1[$row ->CampaignNumber] ? ($leads1[$row ->CampaignNumber] / $tmr1[$row->CampaignNumber]) :0);
			$AvgPremium		= ($anp1[$row->CampaignNumber] ? (($anp1[$row->CampaignNumber] / $sales12[$row ->CampaignNumber]) / 12) :0);
			$ContactPersen	= ($contact1[$row ->CampaignNumber] ? (($contact1[$row ->CampaignNumber] / $solicited1[$row ->CampaignNumber]) * 100) :0);
			$CPH			= ($contact1[$row ->CampaignNumber] ? ($contact1[$row ->CampaignNumber] / $jam_getok[$row ->CampaignNumber]) :0);
			$SCR			= ($sales12[$row ->CampaignNumber] ? (($sales12[$row ->CampaignNumber] / $contact1[$row ->CampaignNumber]) * 100) :0);
			$SPH			= ($sales12[$row ->CampaignNumber] ? ($sales12[$row ->CampaignNumber] / $jam_getok[$row ->CampaignNumber]) :0);
			$AnpPh			= ($anp1[$row->CampaignNumber] ? ($anp1[$row->CampaignNumber] / $jam_getok[$row->CampaignNumber]) :0);
			$AnpPerTMR		= ($anp1[$row->CampaignNumber] ? ($anp1[$row->CampaignNumber] / $tmr1[$row->CampaignNumber]) :0);
			$SalesPerTMR	= ($sales12[$row ->CampaignNumber] ? ($sales12[$row ->CampaignNumber]/$tmr1[$row->CampaignNumber]) :0);
			$AARP			= ($anp1[$row->CampaignNumber] ? $anp1[$row->CampaignNumber] / $sales12[$row ->CampaignNumber] :0);
			
	?>
			<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
			<tr>
				<td style="text-align: center" class="content-first"><?php echo $no; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Sponsor ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ProductCode ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->CampaignGroupCode ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->CampaignNumber ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->CampaignName ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->CampaignStartDate ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->CampaignEndDate ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($oLeads) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($oSolicited) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($oContact) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($oTerminLeads) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($oAttempt) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($LeadsRemain) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo toDuration($oHours) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($oTMR) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($LeadsAllocate) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo number_format($oSales) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo formatRupiah($oANP) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo formatRupiah($AvgPremium) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ROUND($ContactPersen,2) ; ?> %</td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ROUND($CPH,2) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ROUND($SCR,2) ; ?> %</td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ROUND($SPH,2) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo formatRupiah($AnpPh) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo formatRupiah($AnpPerTMR) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo ROUND($SalesPerTMR,2) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo formatRupiah($AARP) ; ?></td>
			</tr>	
	<?php
		/** KALKULASI **/
		$no++;
		$aLeads 		+= $oLeads;
		$aSolicited 	+= $oSolicited;
		$aContact 		+= $oContact;
		$aTerminLeads 	+= $oTerminLeads;
		$aAttempt 		+= $oAttempt;
		$aHours 		+= $oHours;
		$aTMR 			+= $oTMR;
		$aSales 		+= $oSales;
		$aANP 			+= $oANP;
		};
		
		/* KALKULASI HITUNG */
		$aLeadsRemain += ($aLeads -  $aTerminLeads);
		$aLeadAllocate += ($aLeads / $aTMR);
		$aAvgPremium += (($aANP / $aSales) / 12);
		$aContactPersen += (($aContact / $aSolicited) * 100);
		$aCPH += ($aContact / $aHours);
		$aSCR += (($aSales / $aContact) * 100);
		$aSPH += ($aSales / $aHours);
		$aANPperPH += ($aANP / $aHours);
		$aANPperTMR += ($aANP / $aTMR);
		$aSalesperTMR += ($aSales / $aTMR);
		$aAARP += ($aANP / $aSales);
	?>
		
	<tr height="30">
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;" colspan="8">&nbsp;Subtotal</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo number_format($aLeads) ; ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo number_format($aSolicited) ; ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo number_format($aContact) ; ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo number_format($aTerminLeads) ; ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo number_format($aAttempt) ;  ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo number_format($aLeadsRemain) ; ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo toDuration($aHours); ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo number_format($aTMR) ; ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo number_format($aLeadAllocate); ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo number_format($aSales); ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo formatRupiah($aANP); ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo formatRupiah($aAvgPremium); ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo ROUND($aContactPersen,2); ?> %</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo ROUND($aCPH,2); ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo ROUND($aSCR,2); ?> %</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo ROUND($aSPH,4); ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo formatRupiah($aANPperPH) ; ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo formatRupiah($aANPperTMR) ; ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo ROUND($aSalesperTMR,2); ?></th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;"><?php echo formatRupiah($aAARP); ?></th>
	</tr>
	</div>
	</tbody>
</table>


