<?php
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");

	$connect = new mysql();
	
	$start_date 	= formatDateEng($_REQUEST['start_date']);
	$end_date  		= formatDateEng($_REQUEST['end_date']);
	$selling_start_date = formatDateEng($_REQUEST['selling_start_date']);
	$selling_end_date = formatDateEng($_REQUEST['selling_end_date']);
	$report_type	= $_REQUEST['report_type'];
	$spv			= $_REQUEST['Supervisor'];
	$tm				= $_REQUEST['Agent'];

	header("Content-Type: application/vnd.ms-excel");
	$name		= "ReportSummaryFreePA";
	$file		= ".xls";
	$sdate		= $start_date;
	$filename 	= $name.$sdate."To".$end_date.$file;
	
	header("Pragma: no-cache");
	header("Expires: 0");
	header("Content-Disposition: attachment; filename=".($filename));
	
	set_time_limit(500000);
	$ListPages -> pages = $db -> escPost('v_page'); 		
		
		//Query Index				
		if($report_type != null){

			$sql = "SELECT a.CustomerId as ProspectId, pa.PolicyNumber as Policy, a.CustomerFirstName as Name, b.email as Email, 
					b.phone as Phone, b.dob as Birthdate, c.Gender as Gender, ins.InsuredAge as Age, b.expired as Expired, 
					b.no_induk as No_induk, b.no_polis as No_Polis, b.createdts as Created, a.CustomerUpdatedTs as Updated, 
					a.Remark_1 as Remark1, a.CustomerAddressLine1 as Alamat, pr.Province as Province, f.init_name as TFACode, 
					f.full_name as TFAName, g.init_name as SpvCode, g.full_name as SpvName, h.init_name as AmCode, h.full_name as AmName, 
					i.init_name as MgrCode, i.full_name as MgrName, j.CampaignName as Campaign
						FROM t_gn_customer a 
					inner join t_gn_fpa_registered b on b.CustomerId = a.CustomerId 
					inner join t_gn_assignment e on e.CustomerId = a.CustomerId 
					inner join tms_agent f on f.UserId = a.SellerId 
					inner join tms_agent g on g.UserId = e.AssignSpv 
					inner join tms_agent h on h.UserId = e.AssignMgr 
					left join tms_agent i on i.UserId = e.AssignManager
					inner join t_gn_campaign j on j.CampaignId = a.CampaignId 
					inner join t_gn_policyautogen pa on pa.CustomerId = a.CustomerId
					inner join t_gn_policy pc on pc.PolicyNumber = pa.PolicyNumber
					inner join t_gn_insured ins on ins.CustomerId = a.CustomerId
					inner join t_lk_gender c on c.GenderId = ins.GenderId
					inner join t_gn_payer d on d.CustomerId = a.CustomerId
					inner join t_lk_province pr on pr.ProvinceId = d.ProvinceId 
					where 1=1";
				if($_REQUEST['start_date'] and $_REQUEST['end_date'])
					{
						$sql.=" AND a.CustomerRejectedDate >= '".$start_date." 00:00:00'
								AND a.CustomerRejectedDate <= '".$end_date." 23:00:00'";
					}
				if($_REQUEST['selling_start_date'] and $_REQUEST['selling_end_date'])
					{
						$sql.=" AND pc.PolicySalesDate >= '".$selling_start_date." 00:00:00'
								AND pc.PolicySalesDate <= '".$selling_end_date." 23:00:00'";
					}
				if($_REQUEST['AM'])
					{
						$sql.=" AND e.AssignMgr in ('".$am."')";
					} 
				if($_REQUEST['Supervisor'])
					{
						$sql.=" AND e.AssignSpv in ('".$spv."')";
					}
				if($_REQUEST['Agent'])
					{
						$sql.=" AND e.AssignSelerId in ('".$tm."')";
					}
				$sql.=" GROUP BY a.CustomerId";
				//echo $sql;
			$qry = $ListPages->execute($sql,__FILE__,__LINE__);
		} 

?>			
<fieldset class="corner">
		<legend class="icon-product" style="color: teal;"> &nbsp;&nbsp;&nbsp;Summary FreePA &nbsp;&nbsp;&nbsp;</legend> <br>
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
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;" >&nbsp;No.</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Prospect ID</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Policy</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Name</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Email</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Phone</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Birthdate</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Gender</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Age</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Expired</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;No. Induk</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;No. Polis</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Source</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Created</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Updated</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Remark1</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Alamat</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Provinsi</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Survey</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;TFA Code</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;TFA Name</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;SPV Code</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;SPV Name</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;AM Code</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;AM Name</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Mgr Code</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Mgr Name</th>
					<th bgcolor="#3366FF"  style="color:#FFFFFF;text-align:center;">&nbsp;Campaign</th>
				</tr>
			</thead>	
			<tbody>
				<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
					<tr>
					<?php 
					    $no = 1;
						while($data = $ListPages->fetchassoc($qry)){
							$Expired = date('Y-m-d', strtotime($data['Expired']));
							$Created = date('Y-m-d', strtotime($data['Created']));
							$Updated = date('Y-m-d', strtotime($data['Updated']));
					
					?>
						<td style="text-align: center" class="content-first"><?php echo $no; ?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['ProspectId']; ?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['Policy']; ?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['Name']; ?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['Email']; ?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['Phone']; ?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['Birthdate']?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['Gender']; ?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['Age'];?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $Expired;?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['No_induk'];?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['No_Polis'];?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['Source'];?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $Created;?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $Updated;?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['Remark1'];?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['Alamat'];?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['Province'];?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['Survey'];?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['TFACode'];?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['TFAName'];?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['SpvCode'];?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['SpvName'];?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['AmCode'];?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['AmName'];?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['MgrCode'];?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['MgrName'];?></td>
						<td nowrap style="text-align: center" class="content-middle"><?php echo $data['Campaign'];?></td>	
					</tr>
					<?php
						$no++;
						}
					?>
				</div>
			</tbody>
		</table>
	</fieldset>


