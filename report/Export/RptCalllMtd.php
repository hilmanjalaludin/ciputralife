<?php
ini_set('memory_limit', '-1');

include_once("../fungsi/global.php");
require_once dirname(__FILE__) . "/../../class/MYSQLConnect.php";
require_once dirname(__FILE__) . "/../../class/class.list.table.php";
require_once dirname(__FILE__) . '/PHPExcel/IOFactory.php';
require_once dirname(__FILE__) . '/PHPExcel/IOFactory.php';
// include_once("../class/MYSQLConnect.php");
// include_once("../class/class.list.table.php");
// include_once('../PHPExcel_1.8.0_doc/Classes/PHPExcel/IOFactory.php');


$fileName 	= 'REPORT_ALL_DATA_CALL_HISTORY_MTD_'.date("Ymd");
$judul 		= 'MTD '.date("Ymd");
$start_date = date("Y-m-01 01:00:00");
$end_date 	= date("Y-m-d 23:00:00");


$styleArray = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FFFFFF'),
        // 'size'  => 11, // jika diinginkan size custome
        'name'  => 'Verdana' // jika diinginkan font style custome
    ));
$bgArray = array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
             'rgb' => '004C99'
        ));
		
//prepare the records to be added on the excel file in an array
$excelData = get_data();

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Aseanindo")->setLastModifiedBy("Aseanindo")
	->setTitle($judul)->setSubject($judul)->setDescription($judul)->setKeywords($judul)
	->setCategory($judul);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Add column headers
$objPHPExcel->getActiveSheet()
			->setCellValue('A1', 'CUSTOMERID')
			->setCellValue('B1', 'NAME')
			->setCellValue('C1', 'DOB')
			->setCellValue('D1', 'EMAIL (CUSTOMER)')
			->setCellValue('E1', 'EMAIL (PAYER)')
			->setCellValue('F1', 'ADDRESS (CUSTOMER)')
			->setCellValue('G1', 'ADDRESS (PAYER)')
			->setCellValue('H1', 'CITY')
			->setCellValue('I1', 'MOBILEPHONE')
			->setCellValue('J1', 'HOMEPHONE')
			->setCellValue('K1', 'OFFICEPHONE')
			->setCellValue('L1', 'CAMPAIGNNAME')
			->setCellValue('M1', 'CAMPAIGNSTATUS')
			->setCellValue('N1', 'REMARKS')
			->setCellValue('O1', 'CALLREASON')
			->setCellValue('P1', 'TANGGAL_CALL')
			->setCellValue('Q1', 'TGL_UPDATE')
			->setCellValue('R1', 'TGL_UPLOAD')
			->setCellValue('S1', 'PRODUCT_CODE')

			;
// highlight kolom header dengan bg biru gelap dan font putih
$ch = 'A';
$row_size = count($excelData);
print_r($row_size);exit();
$col_size = 19;
for($i=0; $i<$col_size; $i++){
	$objPHPExcel->getActiveSheet()->getStyle($ch.'1')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle(($ch++).'1')->getFill()->applyFromArray($bgArray);
}

$objPHPExcel->getActiveSheet()
    ->getStyle('G2:G128')
    ->setQuotePrefix(true);

//Put each record in a new cell
for($i=0; $i<$row_size; $i++){
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.($i+2), $excelData[$i]['CUSTOMERID'],  
		PHPExcel_Cell_DataType::TYPE_STRING );
	$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+2), $excelData[$i]['NAME']);
	$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+2), $excelData[$i]['DOB']);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.($i+2), $excelData[$i]['EMAIL']);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+2), $excelData[$i]['EMAIL_P']);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+2), $excelData[$i]['ADDRESS']);
	$objPHPExcel->getActiveSheet()->setCellValue('G'.($i+2), $excelData[$i]['ADDRESS_P']);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.($i+2), $excelData[$i]['CITY']);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('I'.($i+2), 
		$excelData[$i]['MOBILEPHONE'], PHPExcel_Cell_DataType::TYPE_STRING );
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('J'.($i+2), 
		$excelData[$i]['HOMEPHONE'], PHPExcel_Cell_DataType::TYPE_STRING );
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('K'.($i+2), 
		$excelData[$i]['OFFICEPHONE'], PHPExcel_Cell_DataType::TYPE_STRING );
	$objPHPExcel->getActiveSheet()->setCellValue('L'.($i+2), $excelData[$i]['CAMPAIGNNAME']);
	$objPHPExcel->getActiveSheet()->setCellValue('M'.($i+2), $excelData[$i]['CAMPAIGNSTATUS']);
	$objPHPExcel->getActiveSheet()->setCellValue('N'.($i+2), $excelData[$i]['REMARKS']);
	$objPHPExcel->getActiveSheet()->setCellValue('O'.($i+2), $excelData[$i]['CALLREASON']);
	$objPHPExcel->getActiveSheet()->setCellValue('P'.($i+2), $excelData[$i]['TANGGAL_CALL']);
	$objPHPExcel->getActiveSheet()->setCellValue('Q'.($i+2), $excelData[$i]['TGL_UPDATE']);
	$objPHPExcel->getActiveSheet()->setCellValue('R'.($i+2), $excelData[$i]['TGL_UPLOAD']);
	$objPHPExcel->getActiveSheet()->setCellValue('S'.($i+2), $excelData[$i]['PRODUCT_CODE']);
} 

// Set worksheet title
$objPHPExcel->getActiveSheet()->setTitle($judul);

//save the file to the server (Excel5)
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save(dirname(__FILE__).'/../Generated/' . $fileName . '.xls');

echo "\nDONE GENERATING EXCEL FIILE => $fileName\n";

function create_report() {
	global $db;
	global $ListPages;
	// global $start_date;
	// global $end_date;
	$start_date	= date("Y-m-")."01";
	$end_date	= date("Y-m-d");
	$today = date("Y-m-d");
	set_time_limit(500000);
	$ListPages -> pages = $db -> escPost('v_page'); 
	$ListPages -> setPage(10);
	
	$sql = "SELECT 
				tgc.CustomerId AS CUSTOMERID,
				tgc.CustomerFirstName AS NAME,
				tgc.CustomerDOB AS DOB, 
				tgc.CustomerEmail AS EMAIL, 
				tpy.PayerEmail AS EMAIL_P, 
				tgc.CustomerAddressLine1 AS ADDRESS, 
				tpy.PayerAddressLine1 AS ADDRESS_P, 
				tgc.CustomerCity AS CITY, 
				tgc.CustomerMobilePhoneNum AS MOBILEPHONE,
				tgc.CustomerHomePhoneNum AS HOMEPHONE,
				tgc.CustomerWorkPhoneNum AS OFFICEPHONE,
				tgp.CampaignName AS CAMPAIGNNAME,
				IF(tgp.CampaignStatusFlag = 1, 'Aktif', 'Tidak Aktif') AS CAMPAIGN_STATUS,
				tgc.Remark_1 AS REMARKS,
				tcall.CallReasonDesc AS CALLREASON,
				tgcall.CallHistoryCallDate AS TANGGAL_CALL,
				tgc.CustomerUpdatedTs AS TGL_UPDATE,
				tgc.CustomerUploadedTs AS TGL_UPLOAD,
				tprod.ProductCode AS PRODUCT_CODE
			FROM t_gn_callhistory tgcall
			INNER JOIN t_gn_customer tgc ON tgc.CustomerId=tgcall.CustomerId
			LEFT JOIN t_gn_payer tpy ON tgc.CustomerId=tpy.CustomerId
			INNER JOIN t_gn_uploadreport tup ON tgc.UploadId=tup.UploadId
			INNER JOIN t_gn_campaign tgp ON tgc.CampaignId=tgp.CampaignId
			LEFT JOIN t_lk_callreason tcall ON tgcall.CallReasonId=tcall.CallReasonId
			INNER JOIN t_gn_campaignproduct tcprod ON tgc.CampaignId=tcprod.CampaignId
			INNER JOIN t_gn_product tprod ON tprod.ProductId=tcprod.ProductId
			WHERE 
				tgcall.CallHistoryCallDate >='2018-08-09 00:00:00' 
		";
			#and tgcall.CallHistoryCallDate <= '".$end_date."' 
		// $reports .= "<th> &nbsp;&nbsp;&nbsp;Report Campaign Review <br/></th>";
		$reports .= "<th> &nbsp;&nbsp;&nbsp;Date Range  &nbsp;: $start_date To $end_date<br/></th>";
		$reports .= "<th> &nbsp;&nbsp;&nbsp;Report Date : $today </th>";
		$reports .= "<table width=\"99%\" class=\"custom-grid\" cellspacing=\"0\">";
	//echo $sql;
	//set_time_limit(500000);
	$data = array();
	$res = $ListPages->execute($sql,__FILE__,__LINE__);

	// while($rows = $ListPages->fetchassoc($res)){
	// 	$data[] = $rows;
	// }
		
	return $data;
}