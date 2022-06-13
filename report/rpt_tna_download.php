<?php
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");

	$connect = new mysql();
	
	$start_date  = $_REQUEST['start_date'];
	$end_date  	 = $_REQUEST['end_date'];
	$cignasystem = $_REQUEST['cignasystem'];
	$today 		 = date("Y-m-d");

	header("Content-type: application/vnd-ms-excel");
	$name		= "ReportFollowTNA";
	$file		= ".xls";
	$sdate		= $start_date;
	$filename 	= $name.$sdate."To".$end_date.$file;
	header("Content-Disposition: attachment; filename=".($filename));
	
	set_time_limit(500000);
		//$ListPages -> pages = $db -> escPost('v_page'); 		
		
		//Query Index				
		$sql = "
			SELECT DISTINCT 
						tgf.FuCustId, 
						tgf.*, 
						ta.init_name, 
						tgh.CallHistoryCallDate, 
						tkb.BankName ,
						ta.full_name, 
						tps.ApproveId,
						tkc.CallReasonDesc as CallStatus
				FROM  t_gn_followup tgf
				   INNER JOIN t_gn_customer tgc ON tgc.CustomerId = tgf.FuCustId
				   LEFT JOIN t_lk_callreason tkc ON tgc.CallReasonId = tkc.CallReasonId
				   INNER JOIN t_gn_callhistory tgh ON tgh.CustomerId = tgc.CustomerId
				   INNER JOIN tms_agent ta ON ta.UserId = tgh.CreatedById
				   INNER JOIN t_lk_aprove_status tps ON tps.ApproveId = tgf.FuQAStatus
				   LEFT JOIN t_lk_bank tkb ON tkb.BankId = tgf.FuBank
				WHERE tgf.FuType = '2' AND tps.ApproveId = '17' AND tps.AproveCode = '513'
				GROUP BY tgc.CustomerId
			";
		//echo $sql;
		$benfRestObj = $connect->query($sql);
	SetNoCache();

?>			
<fieldset class="corner">
	<legend class="icon-product" style="color: teal;"> &nbsp;&nbsp;&nbsp;Follow TNA &nbsp;&nbsp;&nbsp;</legend> <br>
		<legend style="color: #637dde;">
			<?php
				echo "<th> &nbsp;&nbsp;&nbsp;Start Date &nbsp;: $start_date<br/></th>";
				echo "<th> &nbsp;&nbsp;&nbsp;End Date  &nbsp;: $end_date<br/></th>";
				echo "</br>";
			?>
		</legend>
		<table width="99%" class="" border="1">
			<thead>
				<tr height="30">
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;No.</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Call Status</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Nama TM</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;TM Code</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Call Date</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Nama</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;DOB</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;HP</th>
					<th bgcolor="#3366FF" colspan="7"  style="color:#FFFFFF;text-align:center;">&nbsp;Remark</th>
					<!--<th bgcolor="#3366FF" colspan="2"  style="color:#FFFFFF;text-align:center;">&nbsp;Assign</th>
					<th bgcolor="#3366FF" rowspan="2"  style="color:#FFFFFF;text-align:center;">&nbsp;Status</th>
					<th bgcolor="#3366FF" colspan="2"  style="color:#FFFFFF;text-align:center;">&nbsp;Submitted</th>
					<th bgcolor="#3366FF" colspan="2"  style="color:#FFFFFF;text-align:center;">&nbsp;Issued</th>
					<th bgcolor="#3366FF" colspan="2"  style="color:#FFFFFF;text-align:center;">&nbsp;Pending</th>-->
				</tr>
				<tr>
					<th bgcolor="#3366FF" colspan="8"></th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">Alamat</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">Telephone</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">Bank</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">Note 1</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">Note 2</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">Note 3</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">Note 4</th>
					<!--<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">FA</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">Date</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">Cases</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">APE</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">Cases</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">APE</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">Cases</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">APE</th>-->
				</tr>
			</thead>	
			<tbody>
				<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
					<tr>
					<?php 
					    $no = 1;
						foreach ($benfRestObj ->result_object() as $key => $value) { ?>
						<td style="text-align: center" class="content-first"><?php echo $no; ?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $value -> CallStatus; ?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $value -> full_name; ?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $value -> init_name; ?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $value -> CallHistoryCallDate; ?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $value -> FuName; ?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $value -> FuDOB; ?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $value -> FuMobile; ?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $value -> FuAddress?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $value -> FuPhone?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $value -> BankName?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $value -> FuNotes1?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $value -> FuNotes2?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $value -> FuNotes3?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $value -> FuNotes4?></td>
						<!--<td nowrap style="text-align: center" class="content-middle"> Blank </td>
						<td nowrap style="text-align: center" class="content-middle"> Blank </td>
						<td nowrap style="text-align: center" class="content-middle"> Blank </td>
						<td nowrap style="text-align: center" class="content-middle"> Blank </td>
						<td nowrap style="text-align: center" class="content-middle"> Blank </td>
						<td nowrap style="text-align: center" class="content-middle"> Blank </td>
						<td nowrap style="text-align: center" class="content-middle"> Blank </td>
						<td nowrap style="text-align: center" class="content-middle"> Blank </td>
						<td nowrap style="text-align: center" class="content-middle"> Blank </td> -->
					</tr>
					<?php
						$no++;
						}
					?>
				</div>
			</tbody>
		</table>
	</fieldset>