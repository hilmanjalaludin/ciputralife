<?
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	
	
	$start_date = $_REQUEST['start_date'];
	$end_date  = $_REQUEST['end_date'];
	$camptype	= $_REQUEST['camptype'];
	
	
	
	//
		$ListPages -> pages = $db -> escPost('v_page'); 
		$ListPages -> setPage(10);
		
		
		$sql = " SELECT
						cst.CustomerNumber AS prospect_id,
						pr.ProductCode AS Product_Id,
						cmp.CampaignNumber AS Campaign_ID,
						ag2.full_name AS SPV_Name,
						ag1.id AS Agent_Id,
						crs.CallReasonCode AS Call_Id,
						ch.CallHistoryCallDate AS Call_Date,
						ch.CallHistoryNotes AS Remark,
						if(crc.CallReasonCategoryCode = 'PIC','INT',crc.CallReasonCategoryCode) AS Call_Type,
						if(crs.CallReasonCode = '401','Interested',if(crs.CallReasonCode='402','Interested With Spouse',crs.CallReasonDesc)) AS Description
						FROM
						t_gn_callhistory AS ch
						LEFT JOIN t_gn_customer AS cst ON cst.CustomerId = ch.CustomerId
						LEFT JOIN t_gn_campaignproduct cmpr ON cmpr.CampaignId = cst.CampaignId
						LEFT JOIN t_gn_campaign AS cmp ON cmp.CampaignId = cmpr.CampaignId
						LEFT JOIN t_gn_product AS pr ON pr.ProductId = cmpr.ProductId AND cmpr.CampaignId = cmp.CampaignId
						LEFT JOIN t_lk_callreason AS crs ON crs.CallReasonId = ch.CallReasonId
						LEFT JOIN t_lk_callreasoncategory AS crc ON crc.CallReasonCategoryId = crs.CallReasonCategoryId
						LEFT JOIN tms_agent AS ag1 ON ag1.UserId = cst.SellerId
						LEFT JOIN tms_agent AS ag2 ON ag2.UserId = ag1.spv_id
						WHERE date( ch.CallHistoryCallDate) >='$start_date' AND date( ch.CallHistoryCallDate) <='$end_date'
						GROUP BY ch.CallHistoryCallDate
						ORDER BY ch.CallHistoryCallDate ";
						
						/*
						LEFT JOIN t_lk_cignasystem sys ON sys.CignaSystemId = cmp.CignaSystemId
						WHERE date( ch.CallHistoryCallDate) >='$start_date' AND date( ch.CallHistoryCallDate) <='$end_date'
						AND (sys.CignaSystemCode)like '%".$camptype."%'
						AND (sys.CignaSystemCode)like '%".$camptype."%'
						GROUP BY ch.CallHistoryCallDate
						ORDER BY ch.CallHistoryCallDate ";
						*/
						
 
			//print_r($_REQUEST);
			/*echo "<pre>";
			echo $sql;
			echo "</pre>";*/
		$ListPages -> query($sql);
		$ListPages -> result();
	
	SetNoCache();

?>			
<fieldset class="corner">
<legend class="icon-product" style="color: #3366FF;"> &nbsp;&nbsp;&nbsp;Preview Call Tracking &nbsp;&nbsp;&nbsp;</legend>
<legend style="color: red;">
<?php
		echo "<th> &nbsp;&nbsp;&nbsp;Interval : $start_date To $end_date</th>";
?>
</legend>
<table width="99%" class="custom-grid">
<thead>
	<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
	<table width="99%" border="0" align="center">
	<tr height="30">
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;No.</th>		
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Prospect Id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Product Id</th>        
    <th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Campaign Id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;SPV Name</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Agent Id</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Call Id</th>        
    <th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Call Date</th>
    <th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Start Call</th>
    <th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;End Call</th>
    <th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Duration</th>
    <th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Dest Phone No</th>
    <th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Call Date</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Remark</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Call type</th>
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Description</th>        
		<th nowrap bgcolor="#3366FF" class="custom-grid th-middle" style="color:#FFFFFF;text-align:center;">&nbsp;Attempt</th>        
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
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> prospect_id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Product_Id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Campaign_ID ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> SPV_Name ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Agent_Id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Call_Id ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Call_Date ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Remark ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Call_Type ; ?></td>
				<td nowrap style="text-align: center" class="content-middle"><?php echo $row -> Description ; ?></td>
			</tr>	
		</div>
</tbody>
	<?php
		$no++;
		};
	?>
</table>


