<?php
	require ("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	//require("class.list.table.php");
	require("class_export_excel.php");
	
	$start_date 	= $_REQUEST['start_date'];
	$end_date  		= $_REQUEST['end_date'];
	$campaign		= $_REQUEST['Campaign'];
	$agt			= $_REQUEST['Agent'];
	$excel			= new excel();
	$excel -> xlsWriteHeader('DownloadListClosing(From'.$start_date.'To'.$end_date.')');
	
	
	$excel ->xlsWriteLabel(0,0,"No.");		
	$excel ->xlsWriteLabel(0,1,"Prospect Id");
	$excel ->xlsWriteLabel(0,2,"Customer Name");
	$excel ->xlsWriteLabel(0,3,"Address 1");
	$excel ->xlsWriteLabel(0,4,"Address 2");
	$excel ->xlsWriteLabel(0,5,"Address 3");
	$excel ->xlsWriteLabel(0,6,"Address 4");
	$excel ->xlsWriteLabel(0,7,"Holder Name");
	$excel ->xlsWriteLabel(0,8,"DOB");
	$excel ->xlsWriteLabel(0,9,"Premium");
	$excel ->xlsWriteLabel(0,10,"Product Id");
	$excel ->xlsWriteLabel(0,11,"Campaign Id");
	$excel ->xlsWriteLabel(0,12,"Mobile Phone");
	$excel ->xlsWriteLabel(0,13,"Mobile Phone2");
	$excel ->xlsWriteLabel(0,14,"Mobile Phone Req");
	$excel ->xlsWriteLabel(0,15,"Home Phone");
	$excel ->xlsWriteLabel(0,16,"Home Phone2");
	$excel ->xlsWriteLabel(0,17,"Home Phone_Req");
	$excel ->xlsWriteLabel(0,18,"Office Phone");
	$excel ->xlsWriteLabel(0,19,"Office Phone2");
	$excel ->xlsWriteLabel(0,20,"Office Phone Req");
	$excel ->xlsWriteLabel(0,21,"Remark");
	$excel ->xlsWriteLabel(0,22,"Call Date");
	$excel ->xlsWriteLabel(0,23,"Agent Id");
	$excel ->xlsWriteLabel(0,24,"Agent Name");
		

	// Fungsi header dengan mengirimkan raw data excel
	//header("Content-type: application/vnd-ms-excel");
	///$name		="ClosingList";
	//$file		=".xls";
///	$sdate		=$start_date;
//	$filename 	= $name."-".$sdate."to".$end_date.$file;
//	// Mendefinisikan nama file ekspor "hasil-export.xls"z
	//header("Content-Disposition: attachment; filename=".($filename));

 

	
			$sql = " SELECT DISTINCT cst.CustomerNumber AS Prospect_Id,
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
						(cmp.CampaignId)
						FROM
						t_gn_customer AS cst
						LEFT JOIN t_gn_insured AS ins ON ins.CustomerId = cst.CustomerId
						LEFT JOIN t_gn_policy AS plc ON plc.PolicyId = ins.PolicyId
						LEFT JOIN t_gn_productplan AS prp ON prp.ProductPlanId = plc.ProductPlanId
						LEFT JOIN t_gn_campaign AS cmp ON cst.CampaignId = cmp.CampaignId
						LEFT JOIN t_gn_product AS prd ON prd.ProductId = prp.ProductId
						LEFT JOIN t_gn_callhistory AS clh ON clh.CustomerId = cst.CustomerId AND clh.CallHistoryCallDate = cst.CustomerUpdatedTs
						LEFT JOIN tms_agent AS agt ON agt.UserId = cst.SellerId
						WHERE	cst.CallReasonId IN (37,38)
						AND date(clh.CallHistoryCallDate) >='$start_date' AND date(clh.CallHistoryCallDate) <='$end_date'
						AND (cmp.CampaignId) like '%$campaign%'
						AND (agt.id) like '%$agt%'
						ORDER BY cst.customerid";
 
			
		$query		= $db ->execute($sql,__FILE__,__LINE__); 
		
		//$xlsRows = 1;
		$xlsRows = (($db -> start) + 1);
		while($row = $db ->fetchrow($query)){
		
				$excel->xlsWriteNumber($xlsRows,0,$xlsRows);
				$excel->xlsWriteLabel($xlsRows,1,$row -> Prospect_Id );
				$excel->xlsWriteLabel($xlsRows,2,$row -> Customer_Name );
				$excel->xlsWriteLabel($xlsRows,3,$row -> Address_1 );
				$excel->xlsWriteLabel($xlsRows,4,$row -> Address_2 );
				$excel->xlsWriteLabel($xlsRows,5,$row -> Address_3 );
				$excel->xlsWriteLabel($xlsRows,6,$row -> Address_4 );
				$excel->xlsWriteLabel($xlsRows,7,$row -> Holder_Name );
				$excel->xlsWriteLabel($xlsRows,8,$row -> DOB );
				$excel->xlsWriteLabel($xlsRows,9,$row -> Premium );
				$excel->xlsWriteLabel($xlsRows,10,$row -> Product_Id );
				$excel->xlsWriteLabel($xlsRows,11,$row -> Campaign_Id );
				$excel->xlsWriteLabel($xlsRows,12,$row -> Mobile_Phone );
				$excel->xlsWriteLabel($xlsRows,13,$row -> Mobile_Phone2 );
				$excel->xlsWriteLabel($xlsRows,14,$row -> Mobile_Phone_Req );
				$excel->xlsWriteLabel($xlsRows,15,$row -> Home_Phone );
				$excel->xlsWriteLabel($xlsRows,16,$row -> Home_Phone2 );
				$excel->xlsWriteLabel($xlsRows,17,$row -> Home_Phone_Req );
				$excel->xlsWriteLabel($xlsRows,18,$row -> Office_Phone );
				$excel->xlsWriteLabel($xlsRows,19,$row -> Office_Phone2 );
				$excel->xlsWriteLabel($xlsRows,20,$row -> Office_Phone_Req );
				$excel->xlsWriteLabel($xlsRows,21,$row -> Remark );
				$excel->xlsWriteLabel($xlsRows,22,$row -> Call_Date );
				$excel->xlsWriteNumber($xlsRows,23,$row -> Agent_Id );
				$excel->xlsWriteLabel($xlsRows,24,$row -> Agent_Name );
			$xlsRows++;
	};	

	$excel -> xlsClose();
	
	
	?>
