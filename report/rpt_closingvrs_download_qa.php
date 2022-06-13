<?php
	require ("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("class_export_excel.php");
	
	$start_date 	= $_REQUEST['start_date'];
	$end_date  		= $_REQUEST['end_date'];
	$campaign		= $_REQUEST['Campaign'];
	$agt			= $_REQUEST['Agent'];
	$excel			= new excel();
	$excel -> xlsWriteHeader('DownloadClosingVRS(ClosingQA)(From'.$start_date.'To'.$end_date.')');
	
	
	$excel ->xlsWriteLabel(0,0,"No.");		
	$excel ->xlsWriteLabel(0,1,"Prospect Id");
	$excel ->xlsWriteLabel(0,2,"Premium");
	$excel ->xlsWriteLabel(0,3,"Call Date");
	$excel ->xlsWriteLabel(0,4,"Agent Name");
	$excel ->xlsWriteLabel(0,5,"Agent Id");
	$excel ->xlsWriteLabel(0,6,"Customer Name");
	$excel ->xlsWriteLabel(0,7,"Hp 1");
	$excel ->xlsWriteLabel(0,8,"Hp 2");
	$excel ->xlsWriteLabel(0,9,"Phone 1");
	$excel ->xlsWriteLabel(0,10,"Phone 2");
	$excel ->xlsWriteLabel(0,11,"Product");
	$excel ->xlsWriteLabel(0,12,"Remark");
		

	

	
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
						WHERE	cst.CallReasonId IN (37,38)
						AND date(clh.CallHistoryCallDate) >='$start_date' AND date(clh.CallHistoryCallDate) <='$end_date'";
 
			
		$query		= $db ->execute($sql,__FILE__,__LINE__); 
		
		
		$xlsRows = (($db -> start) + 1);
		while($row = $db ->fetchrow($query)){
		
				$excel->xlsWriteNumber($xlsRows,0,$xlsRows);
				$excel->xlsWriteLabel($xlsRows,1,$row -> Prospect_Id );
				$excel->xlsWriteLabel($xlsRows,2,$row -> Premium );
				$excel->xlsWriteLabel($xlsRows,3,$row -> call_date );
				$excel->xlsWriteLabel($xlsRows,4,$row -> Agent_Name );
				$excel->xlsWriteLabel($xlsRows,5,$row -> Agent_Id );
				$excel->xlsWriteLabel($xlsRows,6,$row -> Customer_Name );
				$excel->xlsWriteLabel($xlsRows,7,$row -> HP_1 );
				$excel->xlsWriteLabel($xlsRows,8,$row -> HP_2 );
				$excel->xlsWriteLabel($xlsRows,9,$row -> Phone_1 );
				$excel->xlsWriteLabel($xlsRows,10,$row -> Phone_2 );
				$excel->xlsWriteLabel($xlsRows,11,$row -> Product );
				$excel->xlsWriteLabel($xlsRows,12,$row -> Remark );
			$xlsRows++;
	};	

	$excel -> xlsClose();
	
	
	?>
