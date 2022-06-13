<?php
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");

	$connect = new mysql();
	
	$start_date 	= formatDateEng($_REQUEST['start_date']);
	$end_date  		= formatDateEng($_REQUEST['end_date']);
	//$selling_start_date = formatDateEng($_REQUEST['selling_start_date']);
	//$selling_end_date = formatDateEng($_REQUEST['selling_end_date']);
	$report_type	= $_REQUEST['report_type'];
	$am 			= $_REQUEST['Am'];
	$spv			= $_REQUEST['Supervisor'];
	$tm				= $_REQUEST['Agent'];
	//$today = date("Y-m-d");
	
		set_time_limit(500000);
		$ListPages -> pages = $db -> escPost('v_page');
		$ListPages -> setPage(10); 		
		
		//Query Index				
		if($report_type != null){

			$sql = "SELECT
					fp.FPAId, fp.CustomerId as ProspectId, pa.PolicyNumber as Policy, fp.name as Name, 
					fp.email as  Email, fp.phone as Phone, fp.dob as Birthdate, d.Gender as Gender,
					ins.InsuredAge as Age, fp.expired as Expired, fp.no_induk as NoInduk, 
					fp.no_polis as NoPolis, fp.createdts as Created, tgc.CustomerUpdatedTs as Updated, tgc.Remark_1 as Remark1,
					CONCAT(p.PayerAddressLine1, ' ', p.PayerAddressLine2, ' ',p.PayerAddressLine3, ' ', p.PayerAddressLine4) as Alamat, 
					pr.Province as Provinsi,

					(select ans.answer_value from t_gn_multians_survey ans where ans.customer_id=fp.CustomerId and ans.quest_have_ans=1 
					 and ans.answer_value <> '' and ans.answer_value is not null and ans.insured_id <> 0 limit 1) as Survey,

					a.init_name as TFACode, a.full_name as TFAName, b.init_name as SpvCode, b.full_name as SpvName, e.init_name as AmCode,
					e.full_name as AmName, c.init_name as MgrCode, c.full_name as MgrName, tg.CampaignName as Campaign

					FROM t_gn_fpa_registered fp
		
					LEFT JOIN t_gn_payer p on p.CustomerId=fp.CustomerId
					LEFT JOIN t_lk_province pr on pr.ProvinceId=p.ProvinceId
					LEFT JOIN t_gn_assignment ass on ass.CustomerId=fp.CustomerId
					LEFT JOIN tms_agent a on ass.AssignSelerId=a.UserId
					LEFT JOIN tms_agent b on ass.AssignSpv=b.UserId
					LEFT JOIN tms_agent e on ass.AssignMgr=e.UserId #manager
					LEFT JOIN tms_agent c on ass.AssignManager=c.UserId  #am
					INNER JOIN t_gn_customer tgc on tgc.CustomerId = fp.CustomerId
					INNER JOIN t_gn_campaign tg on tg.CampaignId = tgc.CampaignId
					INNER JOIN t_gn_policyautogen pa on pa.CustomerId = fp.CustomerId
					INNER JOIN t_gn_insured ins on ins.CustomerId = fp.CustomerId
					INNER JOIN t_lk_gender d on d.GenderId = ins.GenderId
					INNER JOIN t_gn_policy pc on pc.PolicyNumber = pa.PolicyNumber
					where 1=1 AND fp.register_status=2";

				if($_REQUEST['start_date'] and $_REQUEST['end_date'])
					{
						$sql.=" AND fp.createdts >= '".$start_date." 00:00:00'
								AND fp.createdts <= '".$end_date." 23:00:00'";
					}
				// if($_REQUEST['selling_start_date'] and $_REQUEST['selling_end_date'])
				// 	{
				// 		$sql.=" AND pc.PolicySalesDate >= '".$selling_start_date." 00:00:00'
				// 				AND pc.PolicySalesDate <= '".$selling_end_date." 23:00:00'";
				// 	}
				if($_REQUEST['Am'])
					{
						$sql.=" AND ass.AssignMgr in (".$am.")";
					} 
				if($_REQUEST['Supervisor'])
					{
						$sql.=" AND ass.AssignSpv in (".$spv.")";
					}
				if($_REQUEST['Agent'])
					{
						$sql.=" AND ass.AssignSelerId in (".$tm.")";
					}
				$sql.=" GROUP BY fp.CustomerId";
				// echo $sql;
			$qry = $ListPages->execute($sql,__FILE__,__LINE__);
		} 
		
			//var_dump($data);
			//echo $data['ProspectId'];
			// $row[$rowdata['ProspectId']]['ProspectId'] = $rowdata['ProspectId'];
			// $row[$rowdata['ProspectId']]['Policy'] = $rowdata['Policy'];
			// $row[$rowdata['ProspectId']]['Name'] = $rowdata['Name'];
			// $row[$rowdata['ProspectId']]['Name'] = $rowdata['Name'];
			// $row[$rowdata['ProspectId']]['Email']= $rowdata['Email'];
			// $row[$rowdata['ProspectId']]['Phone'] = $rowdata['Phone'];
			// $row[$rowdata['ProspectId']]['Birthdate'] = $rowdata['Birthdate'];
			// $row[$rowdata['ProspectId']]['Gender'] = $rowdata['Name'];
			// $row[$rowdata['ProspectId']]['Age'] = $rowdata['Age'];
			// $row[$rowdata['ProspectId']]['Expired'] = $rowdata['Expired'];
			// $row[$rowdata['ProspectId']]['No_induk'] = $rowdata['No_induk'];
			// $row[$rowdata['ProspectId']]['No_Polis'] = $rowdata['No_Polis'];
			// $row[$rowdata['ProspectId']]['Source'] = $rowdata['Source'];
			// $row[$rowdata['ProspectId']]['Created'] = $rowdata['Created'];
			// $row[$rowdata['ProspectId']]['Updated'] = $rowdata['Updated'];
			// $row[$rowdata['ProspectId']]['Remark1'] = $rowdata['Remark1'];
			// $row[$rowdata['ProspectId']]['Alamat'] = $rowdata['Alamat'];
			// $row[$rowdata['ProspectId']]['Provinsi'] = $rowdata['Province'];
			// $row[$rowdata['ProspectId']]['Survey'] = $rowdata['Survey'];
			// $row[$rowdata['ProspectId']]['TFACode'] = $rowdata['TFACode'];
			// $row[$rowdata['ProspectId']]['TFAName'] = $rowdata['TFAName'];
			// $row[$rowdata['ProspectId']]['SpvCode'] = $rowdata['SpvCode'];
			// $row[$rowdata['ProspectId']]['SpvName'] = $rowdata['SpvName'];
			// $row[$rowdata['ProspectId']]['AmCode'] = $rowdata['AmCode'];
			// $row[$rowdata['ProspectId']]['AmName'] = $rowdata['AmName'];
			// $row[$rowdata['ProspectId']]['MgrCode'] = $rowdata['MgrCode'];
			// $row[$rowdata['ProspectId']]['MgrName'] = $rowdata['MgrName'];
			// $row[$rowdata['ProspectId']]['Campaign'] = $rowdata['Campaign'];
		

?>	
<!DOCTYPE html>
<html>
<head>
	<title>Gross Daily Report</title>
	<style>
			table.grid{}
			td.header { background-color:#2182bf;font-family:Arial;font-weight:bold;color:#f1f5f8;font-size:12px;padding:5px;}
			td.sub { background-color:#eeeeee;font-family:Arial;font-weight:bold;color:#000000;font-size:12px;padding:5px;}
			td.subtot { background-color:#ef9b9b;font-family:Arial;font-weight:bold;color:#000000;font-size:12px;padding:5px;}
			td.content { padding:2px;height:24px;font-family:Arial;font-weight:normal;color:#456376;font-size:12px;background-color:#f9fbfd;}
			td.first {border-left:1px solid #dddddd;border-top:1px solid #dddddd;border-bottom:0px solid #dddddd;}
			td.middle {border-left:1px solid #dddddd;border-bottom:0px solid #dddddd;border-top:1px solid #dddddd;}
			td.lasted {border-left:1px solid #dddddd; border-bottom:0px solid #dddddd; border-right:1px solid #dddddd; border-top:1px solid #dddddd;}
			td.agent{font-family:Arial;font-weight:normal;font-size:12px;padding-top:5px;padding-bottom:5px;border-left:0px solid #dddddd;
					border-bottom:0px solid #dddddd; border-right:0px solid #dddddd; border-top:0px solid #dddddd;
					background-color:#fcfeff;padding-left:2px;color:#06456d;font-weight:bold;}
			h1.agent{font-style:inherit; font-family:Trebuchet MS;color:blue;font-size:14px;color:#2182bf;}

			td.total{
						padding:2px;font-family:Arial;font-weight:normal;font-size:12px;padding-top:5px;padding-bottom:5px;border-left:0px solid #dddddd;
					border-bottom:1px solid #dddddd; border-top:1px solid #dddddd;
					border-right:1px solid #dddddd; border-top:1px solid #dddddd;
					background-color:#2182bf;padding-left:2px;color:#f1f5f8;font-weight:bold;}
			span.top{color:#306407;font-family:Trebuchet MS;font-size:28px;line-height:40px;}
			span.middle{color:#306407;font-family:Trebuchet MS;font-size:14px;line-height:18px;}
			span.bottom{color:#306407;font-family:Trebuchet MS;font-size:12px;line-height:18px;}
			td.subtotal{ font-family:Arial;font-weight:bold;color:#3c8a08;height:30px;background-color:#FFFCCC;}
			td.tanggal{ font-weight:bold;color:#FF4321;height:22px;background-color:#FFFFFF;height:30px;}
			h3{color:#306407;font-family:Trebuchet MS;font-size:14px;}
			h4{color:#FF4321;font-family:Trebuchet MS;font-size:14px;}
		</style>
</head>
<body>
	<fieldset class="corner">
		<legend class="icon-product" style="color: teal;"> &nbsp;&nbsp;&nbsp;Gross Daily Report &nbsp;&nbsp;&nbsp;</legend>
		<legend style="color: #637dde;">
			<?php
				echo "<br/>";
				echo "<th> &nbsp;&nbsp;&nbsp;Selling Start Date &nbsp;: $selling_start_date </th>";
				echo "<th> &nbsp;&nbsp;&nbsp;- </th>";
				echo "<th> &nbsp;&nbsp;&nbsp;Selling End Date  &nbsp;: $selling_end_date</th>";
				echo "</br>";
				echo "<br/>";
			?>
		</legend>
		<?php 
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" >
				<tr>
					<td class=\"header first\" nowrap align=\"center\">No</td>
					<td class=\"header first\" nowrap align=\"center\">SPV Name</td>
					<td class=\"header first\" nowrap align=\"center\">SPV ID</td>
					<td class=\"header middle\" nowrap align=\"center\">TFS Name</td>
					<td class=\"header middle\" nowrap align=\"center\">TFS ID</td>
					<td class=\"header middle\" nowrap align=\"center\">Campaign Name</td>
					<td class=\"header middle\" nowrap align=\"center\">Policy ID</td>
					<td class=\"header first\" nowrap align=\"center\">Sales Date</td>
					<td class=\"header first\" nowrap align=\"center\">Customer Name</td>
					<td class=\"header first\" nowrap align=\"center\">DOB</td>
					<td class=\"header first\" nowrap align=\"center\">Premium</td>
					<td class=\"header first\" nowrap align=\"center\">Status</td>
					<td class=\"header first\" nowrap align=\"center\">Status Extract</td>
					<td class=\"header first\" nowrap align=\"center\">Last Update</td>
					<td class=\"header first\" nowrap align=\"center\">Last Update BY</td>
				</tr>";
		?>
			<tbody>
				<div id="rpt_top_content" class="box-shadow" style="width:1115px;height:auto;overflow:auto;">
				<?php

					$no = 1;
						while($data = $ListPages->fetchassoc($qry)){
							$DOB 	 = date('Y-m-d', strtotime($data['Birthdate']));
							$Expired = date('Y-m-d', strtotime($data['Expired']));
							$Created = date('Y-m-d', strtotime($data['Created']));
							$Updated = date('Y-m-d', strtotime($data['Updated']));

					echo "<tr>
							<td rowspan=\" ".$rowspan." \" class=\"content first\" nowrap align=\"center\">".$no."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['ProspectId']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['Policy']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['Name']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['Email']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['Phone']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$DOB."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['Gender']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['Age']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$Expired."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['NoInduk']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['NoPolis']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap></td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['Created']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['Updated']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['Remark1']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['Alamat']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['Provinsi']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['Survey']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['TFACode']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['TFAName']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['SpvCode']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['SpvName']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['AmCode']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['AmName']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['MgrCode']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['MgrName']."</td>
							<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap align=\"center\">".$data['Campaign']."</td>
						</tr>";
						$no++;
						}
					?>
				</div>
			</tbody>
		</table>
	</fieldset>
</body>
</html>


