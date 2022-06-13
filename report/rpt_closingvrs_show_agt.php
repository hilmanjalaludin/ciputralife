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
						prp.ProductPlanPremium AS Premium,
						clh.CallHistoryCallDate AS call_date,
						agt.full_name AS Agent_Name,
						agt.id AS Agent_Id,
						ins.InsuredFirstName AS Customer_Name,
						cst.CustomerMobilePhoneNum AS HP_1,
						'' AS HP_2,
						cst.CustomerHomePhoneNum AS Phone_1,
						'' AS Phone_2,
						prd.ProductCode AS Product,
						clh.CallHistoryNotes AS Remark
						FROM
						t_gn_customer AS cst
						LEFT JOIN t_gn_insured AS ins ON ins.CustomerId = cst.CustomerId
						LEFT JOIN t_gn_policy AS plc ON plc.PolicyId = ins.PolicyId
						LEFT JOIN t_gn_productplan AS prp ON prp.ProductPlanId = plc.ProductPlanId
						LEFT JOIN t_gn_campaign AS cmp ON cst.CampaignId = cmp.CampaignId
						LEFT JOIN t_gn_product AS prd ON prd.ProductId = prp.ProductId
						LEFT JOIN t_gn_callhistory AS clh ON clh.CustomerId = cst.CustomerId AND clh.CallHistoryCallDate = cst.CustomerUpdatedTs
						LEFT JOIN tms_agent AS agt ON agt.UserId = cst.SellerId
						WHERE	cst.CallReasonId IN (16,17,39,40,41,42)
						AND date(clh.CallHistoryCallDate) >='$start_date' AND date(clh.CallHistoryCallDate) <='$end_date'";
						
 
			//print_r($_REQUEST);
			/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
		$ListPages -> query($sql);
		$ListPages -> result();
	
	SetNoCache();

?>			
<fieldset class="corner">
<legend style="color: teal;"> &nbsp;&nbsp;&nbsp;Preview Report Closing VRS (ClosingAgent)&nbsp;&nbsp;&nbsp;
</legend>
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
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Premium</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Call Date</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Agent Name</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Agent Id</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Customer Name</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Hp 1</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Hp 2</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Phone 1</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Phone 2</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Product</th>
		<th nowrap bgcolor="teal" class="custom-grid th-middle" style="color:#AFA;text-align:center;">&nbsp;Remark</th>
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
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Premium ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> call_date ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Agent_Name ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Agent_Id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Customer_Name ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Hp_1 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Hp_2 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Phone_1 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Phone_2 ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Product ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Remark ; ?></td>
			</tr>	
		</div>
</tbody>
	<?php
		$no++;
		};
	?>
</table>


