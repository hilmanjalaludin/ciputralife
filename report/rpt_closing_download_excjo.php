<?php
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require 'Export/PHPExcel.php';
	require 'Export/PHPExcel/IOFactory.php';
	$connect = new mysql();
	
	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	$prodid = $_REQUEST['prodid'];
	$Campaign = $_REQUEST['Campaign'];
	$exported = $_REQUEST['exported'];
	$query = $_REQUEST['query'];
	$query_check = $_REQUEST['query_check'];
	$qcstatus = explode (',',$_REQUEST['QCStat']);
	$qcstatusname = $db->Entity->ReasonLabelQuality();
	$SelectQCStatus = array();
	
	$objPHPExcel = new PHPExcel();
	
	if ($prodid) {
		$productName = getProductName($prodid, $connect);
	}
	else{
		$productName = 'All active product';
	}
	function getProductName($prodid, $dbase)
	{
		$sql = "select pr.ProductName from t_gn_product pr
				where pr.ProductCode='$prodid' ";
		$te = $dbase->query($sql);
		$te2 = $fte->result_object();

		return $te2[0]->ProductName;
	}
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('A1', 'No.');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('B1', 'formnumber');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('C1', 'insuredname');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('D1', 'insureddOB');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('E1', 'sex');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('F1', 'address1');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('G1', 'address2');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('H1', 'city');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('I1', 'rtandrw');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('J1', 'zipcode');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('K1', 'phone1');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('L1', 'phone2');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('M1', 'extphone2');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('N1', 'mphone');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('O1', 'maritalstatus');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('P1', 'children');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('Q1', 'facode');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('R1', 'activationdate');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('S1', 'branchcode');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('T1', 'csname');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('U1', 'accholdername');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('V1', 'accnumber');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('W1', 'accbranch');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('X1', 'program');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('Y1', 'email');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('Z1', 'asken');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AA1', 'region');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AB1', 'namalg');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AC1', 'idstaffbank');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AD1', 'operid');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AE1', 'sellerid');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AF1', 'SPVID');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AG1', 'AMID');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AH1', 'TSMID');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AI1', 'productidd');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AJ1', 'expdate');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AK1', 'expcard');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AL1', 'period');
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('AM1', 'premiumamout');
	
	$file_name = $prodid.'_'.date('Y-m-d-his').'.xls';
	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename='.$file_name);
	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
?>