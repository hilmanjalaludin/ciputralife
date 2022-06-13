<?
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	
	
	$start_date = $_REQUEST['start_date'];
	$end_date  = $_REQUEST['end_date'];
	$cignasystem		= $_REQUEST['cignasystem'];
	
	set_time_limit(500000);
//
		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(10);
		
		function asgn(){
		global $db;
		
		$sql =" SELECT cmp.campaignnumber AS cmpnum,
				count(cst.CustomerId) AS supply
				FROM t_gn_customer cst
				LEFT JOIN t_gn_assignment asg ON asg.customerid = cst.customerid
				LEFT JOIN t_gn_campaign cmp ON cmp.CampaignId = cst.CampaignId
				WHERE (date(if((cmp.CampaignExtendedDate is null or date(cmp.CampaignExtendedDate) < date(cmp.CampaignEndDate)) ,cmp.CampaignEndDate,cmp.CampaignExtendedDate)) >= '$start_date'
				OR date(if((cmp.CampaignExtendedDate is null or date(cmp.CampaignExtendedDate) < date(cmp.CampaignEndDate)) ,cmp.CampaignEndDate,cmp.CampaignExtendedDate)) >= '$end_date')
				AND asg.AssignBlock = 0
				GROUP BY cmp.CampaignNumber ";
		$qry=mysql_query($sql);
		while ($row = mysql_fetch_array($qry)){
			$followup[$row['cmpnum']] += $row['supply'];
		}
		
		return $followup;
	
	}
		
		
		
		
		
		$sql = " SELECT DISTINCT c.CustomerNumber AS prospect_id,
							ca.CampaignName AS campaign_name,
							ca.CampaignNumber AS campaign_id,
							pa.PayerCreditCardNum AS acc_number,
							c.CustomerNumber AS cif_number,
							pa.PayerCreditCardExpDate AS ref_number,
							c.CustomerFirstName AS `name`,
							c.CustomerDOB AS dob,
							pr.ProductCode AS product_id,
							pr.ProductName AS product_name,
							cr.CallReasonCode AS call_result,
							crc.CallReasonCategoryCode AS call_type,
							ta.id AS seller_id,
							c.CustomerUpdatedTs AS call_date1,
							c.CustomerUploadedTs AS Upload_date,
							spv.full_name AS SPV_Id,
							ta.id AS Agent_Id,
							DATE_FORMAT((p.PolicySalesDate),'%Y-%m-%d %H:%i') AS call_date,
							ch.CallHistoryNotes AS Remarks,
							g.GenderShortCode AS sex,
							ch.CallHistoryNotes AS Remarks_1,
							'' AS Remarks_2,
							'' AS Remarks_3,
							p.PolicyNumber AS Policy_No,
							'' AS Policy_ref,
							p.PolicyEffectiveDate AS policy_date,
							cg.CampaignGroupName AS Campaign_Cigna,
							cr.CallReasonDesc AS Description,
							pp.ProductPlanPremium AS Premium,
							pm.PayModeCode AS Payment_Frequent,
							'N' AS Camp_Type1,
							ca.CampaignStartDate AS Campaign_Initial_Date,
							ca.CampaignStartDate AS Campaign_Start_Date,
							ca.CampaignEndDate AS Campaign_End_Date,
							ca.CampaignExtendedDate AS Extend_Date,
							spo.SponsorCode AS Sponsor_Id,
							DATE_FORMAT(ca.CampaignStartDate, '%m') AS Month_Of_Campaign,
							DATE_FORMAT(ca.CampaignStartDate, '%Y') AS year_Of_campaign,
							ct.CampaignTypeCode AS Camp_Type,
							1 AS Build_Type,
							ca.CampaignDataFileName AS Source_Campaign_ID,
							rur.ReUploadReason AS Reupload_Reason,
							ca.CampaignDataFileName AS Source_Campaign_ID,
							cs.CignaSystemCode
							FROM
							t_gn_customer AS c
							LEFT JOIN t_gn_assignment AS asg ON asg.CustomerId = c.CustomerId
							LEFT JOIN t_gn_campaign AS ca ON c.CampaignId = ca.CampaignId 
							LEFT JOIN t_lk_cignasystem AS cs ON ca.CignaSystemId = cs.CignaSystemId
							LEFT JOIN t_gn_insured AS i ON c.CustomerId = i.CustomerId AND i.PremiumGroupId = 2
							LEFT JOIN t_gn_policy AS p ON i.PolicyId = p.PolicyId
							LEFT JOIN t_gn_productplan AS pp ON p.ProductPlanId = pp.ProductPlanId
							LEFT JOIN t_gn_campaignproduct AS cpr ON cpr.CampaignId = ca.CampaignId 
							LEFT JOIN t_gn_product AS pr ON cpr.ProductId = pr.ProductId 
							LEFT JOIN t_gn_campaigngroup AS cg ON pr.CampaignGroupId = cg.CampaignGroupId 
							LEFT JOIN t_gn_payer AS pa ON c.CustomerId = pa.CustomerId
							LEFT JOIN t_lk_salutation AS pas ON pa.SalutationId = pas.SalutationId
							LEFT JOIN t_lk_gender AS pag ON pa.GenderId = pag.GenderId
							LEFT JOIN t_lk_province AS pap ON pa.ProvinceId = pap.ProvinceId
							LEFT JOIN t_lk_paymenttype AS pt ON pa.PaymentTypeId = pt.PaymentTypeId
							LEFT JOIN t_lk_creditcardtype AS cct ON pa.CreditCardTypeId = cct.CreditCardTypeId
							LEFT JOIN t_lk_validccprefix AS vcp ON pa.ValidCCPrefixId = vcp.ValidCCPrefixId
							LEFT JOIN t_lk_bank AS b ON vcp.BankId = b.BankId
							LEFT JOIN t_lk_paymode AS pm ON pp.PayModeId = pm.PayModeId
							LEFT JOIN tms_agent AS ta ON c.SellerId = ta.UserId
							LEFT JOIN t_lk_premiumgroup AS pg ON i.PremiumGroupId = pg.PremiumGroupId
							LEFT JOIN t_lk_salutation AS s ON i.SalutationId = s.SalutationId
							LEFT JOIN t_lk_gender AS g ON i.GenderId = g.GenderId
							LEFT JOIN t_lk_relationshiptype AS rt ON i.RelationshipTypeId = rt.RelationshipTypeId
							LEFT JOIN t_gn_callhistory AS ch ON c.CustomerId = ch.CustomerId AND c.CallReasonId = ch.CallReasonId
							LEFT JOIN t_lk_callreason AS cr ON ch.CallReasonId = cr.CallReasonId
							LEFT JOIN t_lk_campaigntype AS ct ON ca.CampaignTypeId = ct.CampaignTypeId
							LEFT JOIN t_lk_callreasoncategory AS crc ON cr.CallReasonCategoryId = crc.CallReasonCategoryId
							LEFT JOIN t_lk_reuploadreason AS rur ON ca.ReUploadReasonId = rur.ReUploadReasonId
							LEFT JOIN t_lk_sponsor AS spo ON c.SponsorId = spo.SponsorId
							LEFT JOIN t_lk_buildtype AS bt ON ca.BuildTypeId = bt.BuildTypeId
							LEFT JOIN tms_agent AS spv ON spv.UserId = ta.spv_id
							WHERE (date(if((ca.CampaignExtendedDate is null or date(ca.CampaignExtendedDate) < date(ca.CampaignEndDate)) ,ca.CampaignEndDate,ca.CampaignExtendedDate)) >= '$start_date'
							OR date(if((ca.CampaignExtendedDate is null or date(ca.CampaignExtendedDate) < date(ca.CampaignEndDate)) ,ca.CampaignEndDate,ca.CampaignExtendedDate)) >= '$end_date')
							AND asg.AssignBlock = 0
							GROUP BY c.CustomerNumber
							order by c.CustomerNumber DESC limit 100 ";
 
			//print_r($_REQUEST);
			
			// echo "<pre>";
			// echo $sql;
			// echo "</pre>";
		$ListPages -> query($sql);
		$ListPages -> result();
	
	SetNoCache();

?>			
<fieldset class="corner">
<legend class="icon-product" style="color: #3366FF;"> &nbsp;&nbsp;&nbsp;Preview Report Prospect Level with Header &nbsp;&nbsp;&nbsp;</legend>
<legend class="icon-product" style="color: #3366FF;"> &nbsp;&nbsp;&nbsp;THIS PREVIEW ONLY SHOW 100 LAST DATA &nbsp;&nbsp;&nbsp;</legend>
<legend style="color: red;">
<?php
		echo "<th> &nbsp;&nbsp;&nbsp;Interval : $start_date To $end_date</th>";
?>
</legend>
<table width="99%" class="custom-grid" cellspacing="0">
<thead>
	<div id="rpt_top_content" class="box-shadow" style="width:99%px;height:auto;overflow:auto;">
	<table width="99%" border="0" align="center">
	<tr height="30">
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;no.</th>		
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;prospect_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;campaign_name</th>        
        <th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;campaign_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;acc_number</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;cif_number</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;ref_number</th>        
        <th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;name</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;dob</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;product_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;product_name</th>        
        <th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;call_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;calltype</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;call_tso</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;spv_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;agent_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;remarks</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;sex</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;remark1</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;remark2</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;remark3</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;policyno</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;policy_ref</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;policy_date</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;campaign_core</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;description</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;payment_frequent</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;camptype</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;initial_date</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;start_date</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;end_date</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;extend_date</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;total_prospect</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;sponsor_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;month_of_campaign</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;year_of_campaign</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;flag_of_reupload</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;campaign_type</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;filename</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;build_type</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;source_campaign_id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;reupload_reason</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;calldate</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;callstamp</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;uploaddate</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;uploadstamp</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Premium</th>
	</tr>
	</div>
</thead>
</fieldset>	
<tbody>
	<?php
		$rowcmp = 0;
		
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
		
		$rowcmp = asgn();
	?>
			<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
			<tr>
				<td style="text-align: center" class="content-first"><?php echo $no; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> prospect_id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> campaign_name ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> campaign_id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> acc_number ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> cif_number ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> ref_number ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> name ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> dob ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> product_id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> product_name ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php /* echo $row -> call_Id ; */?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> call_type ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php /* echo $row -> call_tso ; */?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> SPV_Id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Agent_Id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Remarks ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> sex ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Remarks_1 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Remarks_2 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Remarks_3 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Policy_No ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Policy_ref ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> policy_date ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Campaign_Cigna ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Description ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Payment_Frequent ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Camp_Type1 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Campaign_Initial_Date ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Campaign_Start_Date ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Campaign_End_Date ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Extend_Date ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $rowcmp[$row->campaign_id] ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Sponsor_Id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Month_Of_Campaign ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> year_Of_campaign ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php /* echo $row -> flag_of_reupload ; */ ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Camp_Type ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php /* echo $row -> filename ; */ ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Build_Type ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Source_Campaign_ID ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Reupload_Reason ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> call_date ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php /* echo $row -> callstamp ; */ ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Upload_date ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php /* echo $row -> uploadstamp ; */ ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Premium ; ?></td>
			</tr>	
		</div>
</tbody>
	<?php
		$no++;
		};
	?>
</table>


