<?php
    ini_set('memory_limit', '-1');
 //    error_reporting(E_ALL);
	// ini_set('display_errors', 1);


    include_once("../fungsi/global.php");
    require_once dirname(__FILE__) . "/../../class/MYSQLConnect.php";
    require_once dirname(__FILE__) . "/../../class/class.list.table.php";


    $start_date = date("2018-08-01");
    $end_date   = date("2018-12-01");

    /** Include path **/
    ini_set('include_path', ini_get('include_path').';../Classes/');

    /** PHPExcel */
    include 'PHPExcel.php';

    /** PHPExcel_Writer_Excel2007 */
    include 'PHPExcel/Writer/Excel2007.php';
    
    echo "start\n";
    set_time_limit(0);

    function CreateReport() {

        global $db;
        global $ListPages;
        global $Modes;
        global $start_date;
        global $end_date;
        //prepare the records to be added on the excel file in an array

        echo date('H:i:s') . " Create new PHPExcel object\n";
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();


        // Set properties
        echo date('H:i:s') . " Set properties\n";
        $objPHPExcel->getProperties()->setCreator("Aseanindo");
        $objPHPExcel->getProperties()->setLastModifiedBy("Aseanindo");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Ciputralife document for Office 2007 XLSX, generated using PHP classes.");

        // Add some data
        echo date('H:i:s') . " Add some data\n";

        $styleHeaderFont = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => 'FFFFFF'),
                'size'  => 13
                // 'name'  => 'Verdana'
            ),
            'fill'  => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'ADD8E6')
                )
            );
            
        $styleTitleFont = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '004C99'),
                'size'  => 14
                // 'name'  => 'Verdana'
            ));

        // Report Title
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Report All Data Call MTD');
        $objPHPExcel->getActiveSheet()->SetCellValue('A2', 'Date Range '.$start_date. ' s/d ' .$end_date);
        $objPHPExcel->getActiveSheet()->SetCellValue('A3', 'Report Date '.date('Y-m-d'));

        // Applying Style
        $objPHPExcel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($styleTitleFont);

        // Report Header
        $objPHPExcel->getActiveSheet()->SetCellValue('A5', 'CUSTOMERID');
        $objPHPExcel->getActiveSheet()->SetCellValue('B5', 'NAME');
        $objPHPExcel->getActiveSheet()->SetCellValue('C5', 'DOB');
        $objPHPExcel->getActiveSheet()->SetCellValue('D5', 'EMAIL (CUSTOMER)');
        $objPHPExcel->getActiveSheet()->SetCellValue('E5', 'EMAIL (PAYER)');
        $objPHPExcel->getActiveSheet()->SetCellValue('F5', 'ADDRESS (CUSTOMER)');
        $objPHPExcel->getActiveSheet()->SetCellValue('G5', 'ADDRESS (PAYER)');
        $objPHPExcel->getActiveSheet()->SetCellValue('H5', 'CITY');
        $objPHPExcel->getActiveSheet()->SetCellValue('I5', 'MOBILEPHONE');
        $objPHPExcel->getActiveSheet()->SetCellValue('J5', 'HOMEPHONE');
        $objPHPExcel->getActiveSheet()->SetCellValue('K5', 'OFFICEPHONE');
        $objPHPExcel->getActiveSheet()->SetCellValue('L5', 'CAMPAIGNNAME');
        $objPHPExcel->getActiveSheet()->SetCellValue('M5', 'CAMPAIGNSTATUS');
        $objPHPExcel->getActiveSheet()->SetCellValue('N5', 'REMARKS');
        $objPHPExcel->getActiveSheet()->SetCellValue('O5', 'CALLREASON');
        $objPHPExcel->getActiveSheet()->SetCellValue('P5', 'TANGGAL_CALL');
        $objPHPExcel->getActiveSheet()->SetCellValue('Q5', 'TGL_UPDATE');
        $objPHPExcel->getActiveSheet()->SetCellValue('R5', 'TGL_UPLOAD');
        $objPHPExcel->getActiveSheet()->SetCellValue('S5', 'PRODUCT_CODE');
        $objPHPExcel->getActiveSheet()->SetCellValue('T5', 'TM_CODE');

        // Applying Style
        // $objPHPExcel->getActiveSheet()->getStyle('A5:S5')->applyFromArray($styleHeaderFont);
        $objPHPExcel->getActiveSheet()->getStyle('A5:T5')->applyFromArray($styleHeaderFont);
        
        $today = date("Y-m-d");
        
        set_time_limit(0);
        $ListPages -> pages = $db -> escPost('v_page'); 
        $ListPages -> setPage(10);
        
        $sql = "SELECT 
        			tgc.SellerId,
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
                    tgcall.CallHistoryCallDate >='".$start_date." 00-00-00' and tgcall.CallHistoryCallDate <= '".$end_date." 23-59-59' 
                ORDER BY tgcall.CallHistoryCallDate desc
            ";
        // echo $sql;
        set_time_limit(0);
        $data = array();
        $res = $ListPages->execute($sql,__FILE__,__LINE__);

        while($rows = $ListPages->fetchassoc($res)){
            $data[] = $rows;
        }   

        $exrow = 6; // coz the header start at row 5;
        foreach($data as $key => $data){
        	$code = get_agent($data['SellerId']);
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$exrow, $data['CUSTOMERID']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$exrow, $data['NAME']);
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$exrow, $data['DOB']);
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$exrow, $data['EMAIL']);
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$exrow, $data['EMAIL_P']);
            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$exrow, $data['ADDRESS']);
            $objPHPExcel->getActiveSheet()->SetCellValue('G'.$exrow, $data['ADDRESS_P']);
            
            $objPHPExcel->getActiveSheet()->SetCellValue('H'.$exrow,$data['CITY']);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('I'.$exrow,$data['MOBILEPHONE'], PHPExcel_Cell_DataType::TYPE_STRING );
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('j'.$exrow,$data['HOMEPHONE'], PHPExcel_Cell_DataType::TYPE_STRING );
            
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('K'.$exrow, $data['OFFICEPHONE'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->SetCellValue('L'.$exrow, $data['CAMPAIGNNAME']);
            $objPHPExcel->getActiveSheet()->SetCellValue('M'.$exrow, $data['CAMPAIGNSTATUS']);
            $objPHPExcel->getActiveSheet()->SetCellValue('N'.$exrow, $data['REMARKS']);
            $objPHPExcel->getActiveSheet()->SetCellValue('O'.$exrow, $data['CALLREASON']);
            $objPHPExcel->getActiveSheet()->SetCellValue('P'.$exrow, $data['TANGGAL_CALL']);
            $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$exrow, $data['TGL_UPDATE']);
            $objPHPExcel->getActiveSheet()->SetCellValue('R'.$exrow, $data['TGL_UPLOAD']);
            $objPHPExcel->getActiveSheet()->SetCellValue('S'.$exrow, $data['PRODUCT_CODE']);
            $objPHPExcel->getActiveSheet()->SetCellValue('T'.$exrow, $code);
        
            $exrow++;
        }

        // Rename sheet
        echo date('H:i:s') . " Rename sheet\n";
        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        
        $reporttype = "Daily";
        // Save Excel 2007 file
        echo date('H:i:s') . " Write to Excel2007 format\n";
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save("/var/www/html/development/ciputralife/report/Generated/Report_All_Data_Call_".$reporttype."-".date('Ymd').".xlsx");
        // Echo done
        echo date('H:i:s') . " Done writing file.\r\n";
    }

    function get_agent($seller_id)
    {
    	$datas = array();
    	$sql = "select * tms_agent ag
    		where ag.UserId = '".$seller_id."'";

    	$res = @mysql_query($sql);

    	while ($rows = mysql_fetch_assoc($res)) {
    	    $datas = $rows['id'];
    	}

    	return $datas;
    }

    CreateReport();
?>