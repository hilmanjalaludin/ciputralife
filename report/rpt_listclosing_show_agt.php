<?
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	
	
	$start_date = $_REQUEST['start_date'];
	$end_date  = $_REQUEST['end_date'];
	$campaign		= $_REQUEST['Campaign'];
	$agt		= $_REQUEST['Agent'];
	
	
	
	//
		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(10);
		
		
		$sql = " SELECT DISTINCT cst.CustomerNumber AS Prospect_Id,
						plc.policyNumber AS policyno,
						cst.CustomerFirstName AS Customer_Name,
						cst.CustomerAddressLine1 AS Address_1,
						cst.CustomerAddressLine2 AS Address_2,
						cst.CustomerAddressLine3 AS Address_3,
						cst.CustomerAddressLine4 AS Address_4,
						ins.InsuredFirstName AS Holder_Name,
						ins.InsuredDOB AS DOB,
						prp.ProductPlanPremium AS Premium,
						prd.ProductCode AS Product_Id,
						cmp.CampaignNumber AS Campaign_Id,
						cst.CustomerMobilePhoneNum AS Mobile_Phone,
						'' AS Mobile_Phone2,
						'' AS `AS Mobile_Phone_Req`,
						cst.CustomerHomePhoneNum AS Home_Phone,
						'' AS Home_Phone2,
						'' AS Home_Phone_Req,
						cst.CustomerWorkPhoneNum AS Office_Phone,
						'' AS Office_Phone2,
						'' AS Office_Phone_Req,
						clh.CallHistoryNotes AS Remark,
						agt.id AS Agent_Id,
						agt.full_name AS Agent_Name,
						clh.CallHistoryCallDate AS Call_Date,
						crs.CallReasonId AS callreasonid,
						crs.CallReasonDesc AS callreasondesc,
						(cmp.CampaignId)
						FROM
						t_gn_customer AS cst
						LEFT JOIN t_gn_insured AS ins ON ins.CustomerId = cst.CustomerId
						LEFT JOIN t_gn_policy AS plc ON plc.PolicyId = ins.PolicyId
						LEFT JOIN t_gn_productplan AS prp ON prp.ProductPlanId = plc.ProductPlanId
						LEFT JOIN t_gn_campaign AS cmp ON cst.CampaignId = cmp.CampaignId
						LEFT JOIN t_gn_product AS prd ON prd.ProductId = prp.ProductId
						LEFT JOIN t_gn_callhistory AS clh ON clh.CustomerId = cst.CustomerId AND date(clh.CallHistoryCallDate) = date(cst.CustomerUpdatedTs)
						LEFT JOIN tms_agent AS agt ON agt.UserId = cst.SellerId
						LEFT JOIN t_lk_callreason crs ON crs.CallReasonId = cst.CallReasonId
						WHERE	cst.CallReasonId IN (16,17,39,40,41,42)
						AND date(clh.CallHistoryCallDate) >='$start_date' AND date(clh.CallHistoryCallDate) <='$end_date'
						AND (cmp.CampaignNumber) like '%$campaign%'
						AND (agt.id) like '%$agt%'
						GROUP BY cst.CustomerNumber
						ORDER BY cst.CustomerId ";
 
			//print_r($_REQUEST);
			// echo "<pre>";
			// echo $sql;
			// echo "</pre>";
		$ListPages -> query($sql);
		$ListPages -> result();
	
	SetNoCache();

?>			
<fieldset class="corner">
<legend class="icon-product" style="color: teal;"> &nbsp;&nbsp;&nbsp;Preview Report List Closing (ClosingAgent) &nbsp;&nbsp;&nbsp;</legend>
<legend style="color: #637dde;">
<?php
		echo "<th> &nbsp;&nbsp;&nbsp;Interval : $start_date To $end_date</th>";
?>
</legend>
<table width="99%" class="custom-grid" cellspacing="0" border="black">
<thead>
	<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
	<table width="99%" border="0" align="center">
	<tr height="30">
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;No.</th>		
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Prospect Id</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Policy No</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Customer Name</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Address 1</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Address 2</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Address 3</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Address 4</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Holder Name</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;DOB</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Premium</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Product Id</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Campaign Id</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Mobile Phone</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Mobile Phone2</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Mobile Phone_Req</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Home Phone</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Home Phone2</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Home Phone_Req</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Office Phone</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Office Phone2</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Office Phone Req</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Remark</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Call Date</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Call Reason ID</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Call Reason Desc</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Agent Id</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Agent_Name</th>
		</tr>
	</div>
</thead>
</fieldset>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
	?>
			<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
			<tr>
				<td style="text-align: center" class="content-first"><?php echo $no; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Prospect_Id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> policyno ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Customer_Name ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Address_1 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Address_2 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Address_3 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Address_4 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Holder_Name ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> DOB ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Premium ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Product_Id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Campaign_Id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Mobile_Phone ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Mobile_Phone2 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Mobile_Phone_Req ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Home_Phone ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Home_Phone2 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Home_Phone_Req ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Office_Phone ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Office_Phone2 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Office_Phone_Req ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Remark ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Call_Date ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> callreasonid ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> callreasondesc ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Agent_Id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Agent_Name ; ?></td>
			</tr>	
		</div>
</tbody>
	<?php
		$no++;
		};
	?>
</table>


