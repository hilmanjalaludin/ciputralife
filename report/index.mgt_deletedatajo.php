<?php 
require("../fungsi/global.php");
require("../class/MYSQLConnect.php");
require("../class/class.list.table.php");
require 'Export/PHPExcel.php';
require 'Export/PHPExcel/IOFactory.php';
$connect = new mysql();

$objPHPExcel = new PHPExcel();

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValueExplicit('A1', 'pros_prospect_Id');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('B1', 'pros_campaign_Id');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('C1', 'pros_name');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('D1', 'pros_dob');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('E1', 'pros_haddress1');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('F1', 'pros_haddress2');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('G1', 'pros_haddress3');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('H1', 'pros_haddress4');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('I1', 'pros_hcity');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('J1', 'pros_hphone1');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('K1', 'pros_hphone2');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('L1', 'pros_mphone');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('M1', 'pros_mphone1');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('N1', 'pros_mphone2');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('O1', 'pros_Product_Id_master');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('P1', 'pros_call_Id');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('Q1', 'pros_Call_group');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('R1', 'pros_Calldate');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('S1', 'pros_agent_Id');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('T1', 'pros_spv_Id');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('U1', 'pros_remark1');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('V1', 'pros_remark2');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('w1', 'pros_remark3');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('x1', 'pros_remark4');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('Y1', 'pros_remark5');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('z1', 'pros_Accnumber');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('AA1', 'pros_cifnumber');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('AB1', 'pros_Refnumber');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('AC1', 'pros_camp_name');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('AD1', 'pros_Inititaldate');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('AE1', 'pros_uploaddate');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('AF1', 'pros_totalprosp');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('AG1', 'pros_policy_Id');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('AH1', 'pros_Product_Id');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('AI1', 'pros_input');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('AJ1', 'pros_effdt');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('AK1', 'pros_premium');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('AL1', 'pros_nbi');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('AM1', 'acctnum');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('AN1', 'ccexpdate');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('AO1', 'pros_mail');

$campaingid = $_REQUEST['CampaingId'];
//$sql = "call sp_deletion_toExcel(".$campaingid.")";
$sql = "SELECT DISTINCT
	a.CustomerNumber AS 'pros_prospect_Id',
	d.CampaignNumber AS 'pros_campaign_Id',
	a.CustomerFirstName AS 'pros_name',
	DATE_FORMAT(a.CustomerDOB,  '%d-%m-%Y' ) AS 'pros_dob',
	r.PayerAddressLine1 AS 'pros_haddress1',
	'NULL' AS 'pros_haddress2',
	r.PayerAddressLine3 AS 'pros_haddress3',
	r.PayerAddressLine4 AS 'pros_haddress4',
	r.PayerCity AS 'pros_hcity',
	a.CustomerHomePhoneNum AS 'pros_hphone1',
	a.CustomerHomePhoneNum2 AS 'pros_hphone2',
	a.CustomerMobilePhoneNum AS 'pros_mphone',
	a.CustomerMobilePhoneNum2 AS 'pros_mphone1',
	a.CustomerWorkPhoneNum AS 'pros_mphone2',
	m.ProductCode AS 'pros_Product_Id_master',
	h.CallReasonCode AS 'pros_call_Id',
	i.CallReasonCategoryCode AS 'pros_Call_group',
	a.CustomerUpdatedTs AS 'pros_Calldate',
	n.id AS'pros_agent_Id',
	o.id AS 'pros_spv_Id',
	r.PayerAddressLine2 AS 'pros_remark1',
	(SELECT DISTINCT ss.CallHistoryNotes FROM t_gn_callhistory ss
	WHERE  a.CustomerId = ss.CustomerId
	ORDER BY ss.CallHistoryCallDate DESC
	LIMIT 1 ) AS 'pros_remark2',
	'NULL'AS 'pros_remark3',
	'NULL'AS 'pros_remark4',
	'NULL'AS 'pros_remark5',
	'NULL'AS 'pros_Accnumber',
	'NULL'AS 'pros_cifnumber',
	r.PayerAddressLine2 AS 'pros_Refnumber',
	d.CampaignName AS 'pros_camp_name',
	d.CampaignEndDate AS 'pros_Initialdate',
	d.CampaignStartDate AS 'pros_uploaddate',
	'null'AS 'pros_totalprosp',
	p.PolicyNumber AS 'pros_policy_Id',
	m.ProductCode AS 'pros_Product_Id',
	p.PolicyEffectiveDate AS 'pros_input',
	p.PolicyEffectiveDate AS 'pros_effdt',
	p.Premi AS 'pros_premium',
	IF(q.PayModeId= 2,12*p.Premi,p.Premi) AS 'pros_nbi',
	r.PayerCreditCardNum AS 'acctnum',
	r.PayerCreditCardExpDate AS 'ccexpdate',
	r.PayerEmail AS 'posemail'

	FROM t_gn_customer a
	LEFT JOIN t_gn_campaign d ON a.CampaignId = d.CampaignId
	LEFT JOIN t_gn_policyautogen f ON a.CustomerId = f.CustomerId
	LEFT JOIN t_gn_product g ON f.ProductId = g.ProductId
	LEFT JOIN t_lk_callreason h ON a.CallReasonId = h.CallReasonId
	LEFT JOIN t_lk_callreasoncategory i ON h.CallReasonCategoryId = i.CallReasonCategoryId
	LEFT JOIN tms_agent n ON a.SellerId = n.UserId
	LEFT JOIN tms_agent o ON n.spv_id = o.UserId
	LEFT JOIN t_gn_policy p ON f.PolicyNumber = p.PolicyNumber
	LEFT JOIN t_gn_productplan q ON p.ProductPlanId = q.ProductPlanId
	LEFT JOIN t_gn_product m ON q.ProductId = m.ProductId
	LEFT JOIN t_gn_payer r ON a.CustomerId = r.CustomerId
	WHERE d.CampaignId =".$campaingid;
		$qry = $connect->query($sql);
		$baris =2;
		foreach($qry-> result_assoc() as $rows )
		{
			
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$baris, $rows["pros_prospect_Id"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$baris,$rows["pros_campaign_Id"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$baris,$rows["pros_name"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.$baris,$rows["pros_dob"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('E'.$baris,$rows["pros_haddress1"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('F'.$baris,$rows["pros_haddress2"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('G'.$baris,$rows["pros_haddress3"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('H'.$baris,$rows["pros_haddress4"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('I'.$baris,$rows["pros_hcity"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('J'.$baris,$rows["pros_hphone1"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('K'.$baris,$rows["pros_hphone2"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('L'.$baris,$rows["pros_mphone"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('M'.$baris,$rows["pros_mphone1"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('N'.$baris,$rows["pros_mphone2"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('O'.$baris,$rows["pros_Product_Id_master"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('P'.$baris,$rows["pros_call_Id"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('Q'.$baris,$rows["pros_Call_group"],PHPExcel_Cell_DataType::TYPE_STRING);			
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('R'.$baris,$rows["pros_Calldate"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('S'.$baris,$rows["pros_agent_Id"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('T'.$baris,$rows["pros_spv_Id"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('U'.$baris,$rows["pros_remark1"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('V'.$baris,$rows["pros_remark2"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('W'.$baris,$rows["pros_remark3"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('X'.$baris,$rows["pros_remark4"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('Y'.$baris,$rows["pros_remark5"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('Z'.$baris,$rows["pros_Accnumber"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('AA'.$baris,$rows["pros_cifnumber"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('AB'.$baris,$rows["pros_Refnumber"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('AC'.$baris,$rows["pros_camp_name"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('AD'.$baris,$rows["pros_Initialdate"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('AE'.$baris,$rows["pros_uploaddate"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('AF'.$baris,$rows["pros_totalprosp"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('AG'.$baris,$rows["pros_policy_id"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('AH'.$baris,$rows["pros_Product_Id"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('AI'.$baris,$rows["pros_input"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('AJ'.$baris,$rows["pros_effdt"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('AK'.$baris,$rows["pros_premium"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('AL'.$baris,$rows["pros_nbi"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('AM'.$baris,$rows["acctnum"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('AN'.$baris,$rows["ccexpdate"],PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('AO'.$baris,$rows["posemail"],PHPExcel_Cell_DataType::TYPE_STRING);
			
			$baris++;
		}


$file_name = "Customer_Deletion_".date('Ymd')."_".date('His').".xls";
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename='.$file_name);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

?>