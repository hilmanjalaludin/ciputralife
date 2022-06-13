<?php

ini_set('memory_limit', '-1');

include_once("../fungsi/global.php");
require_once dirname(__FILE__) . "/../../class/MYSQLConnect.php";
require_once dirname(__FILE__) . "/../../class/class.list.table.php";
require_once dirname(__FILE__) . '/PHPExcel/IOFactory.php';
require_once dirname(__FILE__) . '/PHPExcel/IOFactory.php';
require_once dirname(__FILE__) . '/PHPExcel/IOFactory.php';
// include_once("../class/MYSQLConnect.php");
// include_once("../class/class.list.table.php");
// include_once('../PHPExcel_1.8.0_doc/Classes/PHPExcel/IOFactory.php');


$rawdata_type=1; // 1(default) => daily
global $argv, $argc;

if ($argc >= 2) {
	$rawdata_type = $argv[1];
}

if($rawdata_type == 1) {	// raw data daily
	$fileName = 'EXTRACT_DAILY_ACTIVE_'.date("Ymd");
	$judul = 'EXTRACT DAILY ACTIVE '.date("Ymd");
	$start_date = date("Y-m-d 01:00:00");
	$end_date = date("Y-m-d 23:00:00");
}else {						// MTD
	$fileName = 'EXTRACT_MTD_ACTIVE_'.date("Ymd");
	$judul = 'EXTRACT MTD ACTIVE '.date("Ymd");
	$start_date = date("Y-m-01 01:00:00");
	$end_date = date("Y-m-d 23:00:00");
	
	// $fileName = 'EXTRACT_MTD_ACTIVE_20171212';
	// $judul = 'EXTRACT MTD ACTIVE 20171212';
	// $start_date = date("Y-12-01 01:00:00");
	// $end_date = date("Y-12-12 23:00:00");
}

$styleArray = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FFFFFF')
        // 'size'  => 11, // jika diinginkan size custome
        // 'name'  => 'Verdana' // jika diinginkan font style custome
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
			->setCellValue('L1', 'CALLREASON')
			->setCellValue('M1', 'CAMPAIGNNAME')
			->setCellValue('N1', 'REMARKS')
			;
// highlight kolom header dengan bg biru gelap dan font putih
$ch = 'A';
$row_size = count($excelData);

// echo $row_size;
// exit();

$col_size = 14;
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
	$objPHPExcel->getActiveSheet()->setCellValue('L'.($i+2), $excelData[$i]['CALLREASON']);
	$objPHPExcel->getActiveSheet()->setCellValue('M'.($i+2), $excelData[$i]['CAMPAIGNNAME']);
	$objPHPExcel->getActiveSheet()->setCellValue('N'.($i+2), $excelData[$i]['REMARKS']);
} 

// Set worksheet title
$objPHPExcel->getActiveSheet()->setTitle($judul);

//save the file to the server (Excel5)
//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//$objWriter->save(dirname(__FILE__).'/../Generated/' . $fileName . '.xls');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(dirname(__FILE__).'/../Generated/' . $fileName . '.xlsx');
// $objWriter->save('/opt/enigma/webapps/ciputralife/report/Generated/' . $fileName . '.xlsx');

echo "\nDONE GENERATING EXCEL FIILE => $fileName\n";

function get_data() {
	global $db;
	global $ListPages;
	global $start_date;
	global $end_date;
	
	$sql = "SELECT 
		cs.CUSTOMERID,
		cs.CustomerFirstName as NAME,
		cs.CustomerDOB as DOB,
		cs.CustomerEmail as EMAIL,
		p.PayerEmail as EMAIL_P,
		-- concat(cs.CustomerAddressLine1,cs.CustomerAddressLine2,cs.CustomerAddressLine3,cs.CustomerAddressLine4) as ADDRESS,
		-- concat(p.PayerAddressLine1,p.PayerAddressLine2,p.PayerAddressLine3,p.PayerAddressLine4) as ADDRESS_P,
		concat(IFNULL(cs.CustomerAddressLine1,''),IFNULL(cs.CustomerAddressLine2,''),IFNULL(cs.CustomerAddressLine3,''),IFNULL(cs.CustomerAddressLine4,'')) as ADDRESS,
		concat(IFNULL(p.PayerAddressLine1,''),IFNULL(p.PayerAddressLine2,''),IFNULL(p.PayerAddressLine3,''),IFNULL(p.PayerAddressLine4,'')) as ADDRESS_P,
		p.PayerCity as CITY,
		convert(cs.CustomerMobilePhoneNum, char(20)) as MOBILEPHONE,
		convert(cs.CustomerHomePhoneNum, char(20)) as HOMEPHONE,
		convert(cs.CustomerWorkPhoneNum, char(20)) as OFFICEPHONE,
		ca.CallReasonDesc as CALLREASON,
		c.CAMPAIGNNAME,
		cs.Remark_1 as REMARKS
		from  t_gn_customer cs
		INNER JOIN t_lk_callreason ca on ca.CallReasonId=cs.CallReasonId
		left JOIN t_gn_payer p on p.CustomerId=cs.CustomerId
		INNER join t_gn_campaign c on c.CampaignId=cs.CampaignId
		where cs.CustomerUpdatedTs >= '".$start_date."'
		and cs.CustomerUpdatedTs <= '".$end_date."'
		-- and cs.CampaignId not in (1,2,3,19,22,23,24,25,26,27,28,29,30,44,64,65,89,101,105,131,132,177,20,21,33,34,35,36,37,38)
		and c.CampaignProductionFlag=1
		and c.CampaignStatusFlag = 1
		-- limit 100 
		";
		// echo $sql;
	$data = array();
	$res = $ListPages->execute($sql,__FILE__,__LINE__);
	while($rows = $ListPages->fetchassoc($res)){
		$data[] = $rows;
	}
		
	return $data;
}
