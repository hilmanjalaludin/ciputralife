<?
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	
	$start_date 	= $_REQUEST['start_date'];
	$end_date  		= $_REQUEST['end_date'];
	$cignasystem	= $_REQUEST['cignasystem'];
	
	$today = date("Y-m-d");
	
	
	header("Content-type: application/vnd-ms-excel");
	$name		="CIGNA CAMPAIGN OVRVIEW";
	$file		=".xls";
	$sdate		=$start_date;
	$filename 	= $name.$sdate."To".$end_date.$file;
	
	header("Content-Disposition: attachment; filename=".($filename));
	
	set_time_limit(500000);

		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(10);
		
		function jam($stime,$etime)
		{
			
			global $db;
			
			$hours=array();
			//return $hours;
			$sql =" select
					ca.CampaignNumber,ca.CampaignId,
					count(distinct c.SellerId) as Agent,
					round(sum(ac.EndCallTs - ac.StartCallTs)/3600,2) as Hours
					from t_gn_activitycall as ac
					inner join t_gn_customer c on c.CustomerId=ac.CustomerId
					inner join t_gn_campaign ca on ca.CampaignId=c.CampaignId
					where date(ac.StartCallTs) >= '".$stime."'
					and date(ac.StartCallTs) <= '".$etime."'
					group by ca.CampaignId
					order by ca.CampaignId ";
					//return $sql;
			$qry=mysql_query($sql);
			//return mysql_num_rows($qry);
			while ($row = mysql_fetch_array($qry))
			{
				$hours[$row['CampaignNumber']] = $row['Hours'];
			}
				
			return $hours;
		}
		
		function leads()
		{
			global $db;	
			$leads=array();
			$sql ="  select
						ca.CampaignNumber, ca.CampaignName, count(distinct c.CustomerId) as Leads
					from t_gn_customer c
						left join t_gn_campaign ca on c.CampaignId = ca.CampaignId
					group by ca.CampaignNumber";
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$leads[$row['CampaignNumber']] = $row['Leads'];
			}
				
			return $leads;
		}
		
		
		
		
		
		function contact()
		{
			global $db;	
			$contact=array();
			$sql1 ="select cp.CampaignNumber, cp.CampaignName, ch.CallHistoryId, count(ch.CallReasonId) as Contact, ch.CallHistoryCallDate
						from t_gn_callhistory ch
						left join t_gn_customer c on ch.CustomerId = c.CustomerId
						left join t_lk_callreason cr on ch.CallReasonId = cr.CallReasonId
						left join t_gn_campaign cp on c.CampaignId = cp.CampaignId
					where ch.CallReasonId in (11,13,14,15,16,17,18,19,20,21,22,23,24,25,27,28,29,30,31,32,33,34)
					and date(ch.CallHistoryCallDate) >= '2013-11-18'
					and date(ch.CallHistoryCallDate) <= '2013-11-18'
					group by cp.CampaignId ";
			$qry=mysql_query($sql1);
			while ($row = mysql_fetch_array($qry))
			{
				$contact[$row['CampaignNumber']] = $row['Contact'];
			}
				
			return $contact;
		}
		
		
		
		
		
		function terminated()
		{
			
			global $db;
			
			$terminated=array();
			
			$sql ="select cp.CampaignNumber, cp.CampaignName, ch.CallHistoryId, count(ch.CallReasonId) as TerminatedLeads, ch.CallHistoryCallDate
						from t_gn_callhistory ch
						left join t_gn_customer c on ch.CustomerId = c.CustomerId
						left join t_lk_callreason cr on ch.CallReasonId = cr.CallReasonId
						left join t_gn_campaign cp on c.CampaignId = cp.CampaignId
					where ch.CallReasonId in (11,13,14,15,16,17,18,19,20,21,22,23,24,25,27,28,29,30,31,32,33,34)
					and date(ch.CallHistoryCallDate) >= '2013-11-18'
					and date(ch.CallHistoryCallDate) <= '2013-11-18'
					group by cp.CampaignId ";
			
			$qry=mysql_query($sql);
			
			while ($row = mysql_fetch_array($qry))
			{
				$terminated[$row['CampaignNumber']] = $row['TerminatedLeads'];
			}
				
			return $terminated;
		}
		
				$sql = "SELECT Distinct 		
							(select SponsorSourceCode FROM t_lk_sponsor) as sponsor,
							pr.ProductCode as ProductId,
							cg.CampaignGroupCode as CampaignSplit,
							ca.CampaignNumber as CampaignId,
							ca.CampaignName as CampaignName,
							Date(ca.CampaignStartDate) as StartDate,
							Date(ca.CampaignEndDate) as EndDate,
							count(distinct c.CustomerId) as Leads,
							sum(IF(c.CallReasonId in (11,13,14,15,16,17,18,19,20,21,22,23,24,25,27,28,29,30,31,32,33,34),1,0)) as Contact,
							sum(IF(c.CallReasonId in (15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36),1,0)) as TerminatedLeads,
							round((sum(IF(c.CallReasonId in (11,12,13,14,15,16,17,18,19,20,21,22,23,24,27,28,29,30,31,32,33,34),1,0)) / sum(IF(c.CallReasonId in (15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36),1,0))),2) as LeadsRemaining,
							round((select MAX(ActivityDateTs) - MIN(ActivityDateTs) FROM tms_agent_activity),2) as Hours,
							count(distinct ta.id) as TMR,
							round((count(c.CustomerId) / count(distinct ta.id)) * 100,2) as LeadsTMR,
							count(p.PolicyId) as Sales,
							sum(if(pm.PayModeCode='M',pp.ProductPlanPremium*12,pp.ProductPlanPremium)) as ANP,
							round((sum(if(pm.PayModeCode='M',pp.ProductPlanPremium*12,pp.ProductPlanPremium)) / count(p.PolicyId)) /12,2) as AveragePremium,
							round((sum(IF(c.CallReasonId in (11,12,13,14,15,16,17,18,19,20,21,22,23,24,27,28,29,30,31,32,33,34),1,0)) / count(c.CustomerId)) * 100,2) as ContactPersen,
							(sum(IF(c.CallReasonId in (11,12,13,14,15,16,17,18,19,20,21,22,23,24,27,28,29,30,31,32,33,34),1,0)) / (select MAX(ActivityDateTs) - MIN(ActivityDateTs) FROM tms_agent_activity)) as CPH,
							round((count(p.PolicyId) / sum(IF(c.CallReasonId in (11,12,13,14,15,16,17,18,19,20,21,22,23,24,27,28,29,30,31,32,33,34),1,0))) * 100,2) as SCR,
							'' as SPH,
							#round(count(distinct p.PolicyId),)  as SPH,
							sum(if(pm.PayModeCode='M',pp.ProductPlanPremium*12,pp.ProductPlanPremium)) as AnpPh,
							round((sum(if(pm.PayModeCode='M',pp.ProductPlanPremium*12,pp.ProductPlanPremium)) / count(distinct ta.id)) * 100,2) as AnpPerTmr,
							round((count(p.PolicyId) / count(distinct ta.id)),2) as SalesPerTMR,
							round((sum(if(pm.PayModeCode='M',pp.ProductPlanPremium*12,pp.ProductPlanPremium)) / count(p.PolicyId)) * 100,2) as AARP,
							p.PolicySalesDate
					   
						FROM t_gn_customer c
							
							LEFT JOIN t_gn_campaign ca ON c.CampaignId = ca.CampaignId
							LEFT JOIN t_lk_cignasystem cs ON ca.CignaSystemId = cs.CignaSystemId
							LEFT JOIN t_gn_insured i ON c.CustomerId = i.CustomerId
							LEFT JOIN t_gn_policy p ON i.PolicyId = p.PolicyId
							LEFT JOIN t_gn_productplan pp ON p.ProductPlanId = pp.ProductPlanId
							LEFT JOIN t_gn_product pr ON pp.ProductId = pr.ProductId
							LEFT JOIN t_gn_campaigngroup cg ON pr.CampaignGroupId = cg.CampaignGroupId
							LEFT JOIN t_gn_payer pa ON c.CustomerId = pa.CustomerId
							LEFT JOIN t_lk_salutation pas ON pa.SalutationId = pas.SalutationId
							LEFT JOIN t_lk_gender pag ON pa.GenderId = pag.GenderId
							LEFT JOIN t_lk_province pap ON pa.ProvinceId = pap.ProvinceId
							LEFT JOIN t_lk_paymenttype pt ON pa.PaymentTypeId = pt.PaymentTypeId
							LEFT JOIN t_lk_creditcardtype cct ON pa.CreditCardTypeId = cct.CreditCardTypeId
							LEFT JOIN t_lk_validccprefix vcp ON pa.ValidCCPrefixId = vcp.ValidCCPrefixId
							LEFT JOIN t_lk_bank b ON vcp.BankId = b.BankId
							LEFT JOIN t_lk_paymode pm ON pp.PayModeId = pm.PayModeId
							LEFT JOIN tms_agent ta ON i.CreatedById = ta.UserId
							LEFT JOIN t_lk_premiumgroup pg ON i.PremiumGroupId = pg.PremiumGroupId
							LEFT JOIN t_lk_salutation s ON i.SalutationId = s.SalutationId
							LEFT JOIN t_lk_gender g ON i.GenderId = g.GenderId
							LEFT JOIN t_lk_relationshiptype rt ON i.RelationshipTypeId = rt.RelationshipTypeId
							LEFT JOIN t_gn_callhistory ch ON c.CustomerId = ch.CustomerId AND c.CallReasonId = ch.CallReasonId
							LEFT JOIN t_lk_callreason cr ON ch.CallReasonId = cr.CallReasonId
							LEFT JOIN t_lk_campaigntype ct ON ca.CampaignTypeId = ct.CampaignTypeId
							LEFT JOIN t_gn_beneficiary bnf ON bnf.CustomerId = c.customerid
							LEFT JOIN t_gn_assignment asg ON asg.CustomerId = c.customerid
						WHERE pr.ProductId is not null
						And Date(p.PolicySalesDate) >= '".$start_date."'
						And Date(p.PolicySalesDate) <= '".$end_date."'
						group by ca.CampaignNumber";
						
			//print_r($_REQUEST);
			echo "<pre>";
			echo $sql;
			echo "</pre>";
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
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Contacts</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Terminated Leads</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Leads Remaining</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Hours</th><!---->
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
		//var_dump($ListPages->result);
		$hh = array();
		$hh=jam($start_date,$end_date);
		
		$leads1 = array();
		$leads1=leads();
		//var_dump($leads1);
		
		$contact1 = array();
		$contact1=contact();
		var_dump($contact1);
		while($row = $db ->fetchrow($ListPages->result)){
	?>
			<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
			<tr>
				<td style="text-align: center" class="content-first"><?php echo $no; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->sponsor ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ProductId ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->CampaignSplit ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->CampaignId ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->CampaignName ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->StartDate ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->EndDate ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $leads1[$row ->CampaignId] ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $contact1[$row ->CampaignId] ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->TerminatedLeads ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->LeadsRemaining ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $hh[$row->CampaignId]; ?></td><!---->
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->TMR ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->LeadsTMR ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->Sales ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ANP ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->AveragePremium ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->ContactPersen ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo round($row ->Contact/$hh[$row->CampaignId],2) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->SCR ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo round($row ->Sales/$hh[$row->CampaignId],2) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo round($row ->ANP/$hh[$row->CampaignId],2) ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->AnpPerTmr ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->SalesPerTMR ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row ->AARP ; ?></td>
			</tr>	
		</div>
</tbody>
	<?php
		$no++;
		};
	?>
</table>


