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
$excelData = array(
	"CUSTOMERID" => "123456" , 
	"NAME" => "Ahmad Wahyudin" , 
	"DOB" => "05-12-1992" , 
	"EMAIL" => "ahmadwahyudin119@gmail.com" , 
	"EMAIL_P" => "ahmadwahyudin119@gmail.com" , 
	"ADDRESS" => "Jl Swadaya 1 No 26 , RT 004 /011 , Pejaten Timur , Pasar Minggu , JakSel 12510 " , 
	"ADDRESS_P" => "Jl Swadaya 1 No 26 , RT 004 /011 , Pejaten Timur , Pasar Minggu , JakSel 12510 " , 
	"CITY" => "Jakarta" , 
	"MOBILEPHONE" => "087887140109" , 
	"HOMEPHONE" => "089823238" , 
	"OFFICEPHONE" => "234234234" , 
	"CALLREASON" => "Connected" , 
	"CAMPAIGNNAME" => "CAMPAIGN" , 
	"REMARKS" => "Success Call" 
);

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
$row_size = 70000;
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
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.($i+2), $excelData['CUSTOMERID'],  
		PHPExcel_Cell_DataType::TYPE_STRING );
	$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+2), $excelData['NAME']);
	$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+2), $excelData['DOB']);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.($i+2), $excelData['EMAIL']);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+2), $excelData['EMAIL_P']);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+2), $excelData['ADDRESS']);
	$objPHPExcel->getActiveSheet()->setCellValue('G'.($i+2), $excelData['ADDRESS_P']);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.($i+2), $excelData['CITY']);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('I'.($i+2), 
		$excelData['MOBILEPHONE'], PHPExcel_Cell_DataType::TYPE_STRING );
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('J'.($i+2), 
		$excelData['HOMEPHONE'], PHPExcel_Cell_DataType::TYPE_STRING );
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('K'.($i+2), 
		$excelData['OFFICEPHONE'], PHPExcel_Cell_DataType::TYPE_STRING );
	$objPHPExcel->getActiveSheet()->setCellValue('L'.($i+2), $excelData['CALLREASON']);
	$objPHPExcel->getActiveSheet()->setCellValue('M'.($i+2), $excelData['CAMPAIGNNAME']);
	$objPHPExcel->getActiveSheet()->setCellValue('N'.($i+2), $excelData['REMARKS']);
} 

// Set worksheet title
$objPHPExcel->getActiveSheet()->setTitle($judul);

//save the file to the server (Excel5)
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(dirname(__FILE__).'/../GenerateTest/' . $fileName . '.xlsx');

echo "\nDONE GENERATING EXCEL FIILE => $fileName\n";
