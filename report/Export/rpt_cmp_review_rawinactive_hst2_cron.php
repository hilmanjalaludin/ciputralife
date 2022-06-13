<?php
	require("/opt/enigma/webapps/ciputralife/fungsi/global.php");
	require("/opt/enigma/webapps/ciputralife/class/MYSQLConnect.php");
	require("/opt/enigma/webapps/ciputralife/class/class.list.table.php");
	
	$campaign		= explode(",",$_REQUEST['cmp']);
	$campaign1		= implode(",",$campaign);
	$Modes 			= $argv;
	
	// usage:
	// php /opt/enigma/webapps/ciputralife/report/Export/rpt_cmp_review_rawinactive_hst2_cron.php MTD
	// php /opt/enigma/webapps/ciputralife/report/Export/rpt_cmp_review_rawinactive_hst2_cron.php tgl 2017-12-22 2017-12-22
	
	if($Modes[1]=="MTD"){
		$start_date	= date("Y-m-")."01";
		$end_date	= date("Y-m-d");
	}else if($Modes[1]=="tgl"){
		$start_date	= $Modes[2];
		$end_date	= $Modes[3];
	}else{
		$start_date	= date("Y-m-d");
		$end_date	= date("Y-m-d");
	}
	
	/** Error reporting */
	// error_reporting(E_ALL);

	/** Include path **/
	ini_set('include_path', ini_get('include_path').';../Classes/');

	/** PHPExcel */
	include 'PHPExcel.php';

	/** PHPExcel_Writer_Excel2007 */
	include 'PHPExcel/Writer/Excel2007.php';
	echo "start";
function CreateReport(){
	global $db;
	global $ListPages;
	global $Modes;
	global $start_date;
	global $end_date;
	
	// Create new PHPExcel object
	
	echo date('H:i:s') . " Create new PHPExcel object\n";
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
		'font'	=> array(
			'bold'  => true,
			'color' => array('rgb' => 'FFFFFF'),
			'size'  => 13
			// 'name'  => 'Verdana'
		),
		'fill'	=> array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb' => 'ADD8E6')
			)
		);
		
	$styleTitleFont = array(
		'font'	=> array(
			'bold'  => true,
			'color' => array('rgb' => '004C99'),
			'size'  => 14
			// 'name'  => 'Verdana'
		));

	// Report Title
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'RAW Data Active Campaign');
	$objPHPExcel->getActiveSheet()->SetCellValue('A2', 'Date Range '.$start_date. ' s/d ' .$end_date);
	$objPHPExcel->getActiveSheet()->SetCellValue('A3', 'Report Date '.date('Y-m-d'));

	// Applying Style
	$objPHPExcel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($styleTitleFont);

	// Report Header
	$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'Name');
	$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'DOB');
	$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Email');
	$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'PayerEmail');
	$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Address');
	$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'City');
	$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Payer Address');
	$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Mobile Phone');
	$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Home Phone');
	$objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Office Phone');
	$objPHPExcel->getActiveSheet()->SetCellValue('K5', 'Call Reason');
	$objPHPExcel->getActiveSheet()->SetCellValue('L5', 'Campaign');
	$objPHPExcel->getActiveSheet()->SetCellValue('M5', 'Remarks');
	
	// Applying Style
	$objPHPExcel->getActiveSheet()->getStyle('A5:S5')->applyFromArray($styleHeaderFont);
	
	$today = date("Y-m-d");
	
	set_time_limit(500000);
	$ListPages -> pages = $db -> escPost('v_page'); 
	$ListPages -> setPage(10);

	/***** Campaign Selected Plus Datasize *****/
	$sqlCampaign = "select a.CampaignId, a.CampaignNumber, a.CampaignName, count(b.CustomerId) as DataSize
					from t_gn_campaign a
					left join t_gn_customer b ON a.CampaignId = b.CampaignId
					where a.CampaignStatusFlag = 1
					group by a.CampaignId Order by a.CampaignId ASC";
	$qryCampaign = $ListPages->execute($sqlCampaign,__FILE__,__LINE__);
	while($rowCampaign = $ListPages->fetchassoc($qryCampaign)){
		$CampaignList[$rowCampaign['CampaignId']]['Nama'] = $rowCampaign['CampaignName'];
		$CampaignList[$rowCampaign['CampaignId']]['DataSize'] = $rowCampaign['DataSize'];
	}
	
	/***** Contact / Uncontact Call Reason Query *****/
	$sqlCallReason = "select a.CallReasonContactedFlag, a.CallReasonId, a.CallReasonCode, a.CallReasonDesc from t_lk_callreason a Order by a.CallReasonId ASC";
	$qryCallReason = $ListPages->execute($sqlCallReason,__FILE__,__LINE__);
	while($rowCallReason = $ListPages->fetchassoc($qryCallReason)){
		if($rowCallReason['CallReasonContactedFlag']==1){
			$CallReasonList['Contact'][$rowCallReason['CallReasonDesc']]['Id'] = $rowCallReason['CallReasonId'];
			$CallReasonList['Contact'][$rowCallReason['CallReasonDesc']]['Code'] = $rowCallReason['CallReasonCode'];
			$CallReasonList['Contact'][$rowCallReason['CallReasonDesc']]['Flag'] = $rowCallReason['CallReasonContactedFlag'];
		}else if($rowCallReason['CallReasonContactedFlag']==0){
			if($rowCallReason['CallReasonId']==3 || $rowCallReason['CallReasonId']==1){
				$CallReasonList['UnContact']['No Pick Up']['Id'] = 3;
				$CallReasonList['UnContact']['No Pick Up']['Code'] = 202;
				$CallReasonList['UnContact']['No Pick Up']['Flag'] = 0;
			}else if($rowCallReason['CallReasonId']<>3 && $rowCallReason['CallReasonId']<>1){
				$CallReasonList['UnContact'][$rowCallReason['CallReasonDesc']]['Id'] = $rowCallReason['CallReasonId'];
				$CallReasonList['UnContact'][$rowCallReason['CallReasonDesc']]['Code'] = $rowCallReason['CallReasonCode'];
				$CallReasonList['UnContact'][$rowCallReason['CallReasonDesc']]['Flag'] = $rowCallReason['CallReasonContactedFlag'];
			}
		}
	}
	
	/***** Contact / Uncontact Data Query *****/
	$sqlkey = "SELECT a.CustomerId,
				(SELECT MAX(ax.CallHistoryId)
					FROM t_gn_callhistory ax
					LEFT JOIN t_lk_callreason b ON ax.CallReasonId = b.CallReasonId
					WHERE b.CallReasonCategoryId = 3 AND ax.CustomerId = a.CustomerId
						AND ax.CallHistoryCallDate >= '".$start_date." 00:00:00'
						AND ax.CallHistoryCallDate <= '".$end_date." 23:59:00') as `Contact`,
				(SELECT MAX(az.CallHistoryId)
					FROM t_gn_callhistory az
					LEFT JOIN t_lk_callreason b ON az.CallReasonId = b.CallReasonId
					WHERE b.CallReasonCategoryId IN (1,2) AND az.CustomerId = a.CustomerId
					AND az.CallHistoryCallDate >= '".$start_date." 00:00:00'
					AND az.CallHistoryCallDate <= '".$end_date." 23:59:00') AS `unContact`,
				b.CampaignId
			FROM t_gn_callhistory a
			LEFT JOIN t_gn_customer b ON a.CustomerId = b.CustomerId
			LEFT JOIN t_gn_campaign c ON b.CampaignId = c.CampaignId
			WHERE TRUE AND c.CampaignStatusFlag = 0
			AND a.CallHistoryCallDate >= '".$start_date." 00:00:00'
			AND a.CallHistoryCallDate <= '".$end_date." 23:59:00'
			GROUP BY a.CustomerId;";

	$qrykey = $ListPages->execute($sqlkey,__FILE__,__LINE__);
	while($rowkey = $ListPages->fetchassoc($qrykey)){
		$Key[] = ($rowkey['Contact']?$rowkey['Contact']:$rowkey['unContact']);
		$rowkey['Contact']?$TotalContact +=1:$TotalUnContact +=1;
		$Touched[$rowkey['CampaignId']] +=1;
	}

	$i=1;
	foreach($Key as $k=>$CallHistoryId){
		$sql = "select 
			#e.CampaignName, count(a.CallHistoryId) as Dead
			b.CustomerFirstName, b.CustomerDOB, b.CustomerEmail, c.PayerEmail,
			concat(b.CustomerAddressLine1,' ',b.CustomerAddressLine2,' ',b.CustomerAddressLine3,' ',b.CustomerAddressLine4) as 'Customer Address', b.CustomerCity,
			concat(c.PayerAddressLine1,' ',c.PayerAddressLine2,' ',c.PayerAddressLine3,' ',c.PayerAddressLine4,' ',c.PayerCity) as 'Payer Address',
			b.CustomerMobilePhoneNum, b.CustomerHomePhoneNum, b.CustomerWorkPhoneNum, d.CallReasonDesc, e.CampaignName, a.CallHistoryNotes
			from t_gn_callhistory a
			left join t_gn_customer b ON a.CustomerId = b.CustomerId
			left join t_gn_payer c ON b.CustomerId = c.CustomerId
			left join t_lk_callreason d on a.CallReasonId = d.CallReasonId
			left join t_gn_campaign e on b.CampaignId = e.CampaignId
			where 1=1
			and e.CampaignStatusFlag = 0
			and a.CallHistoryId = ".$CallHistoryId;
			// group by b.CampaignId ";
	
		$qry = $ListPages->execute($sql,__FILE__,__LINE__);
		$row = $ListPages->fetchassoc($qry);
		$rowRaw[$i] = $row;

		$i++;
	}
	
	// echo $sql;
	// echo "<pre>";
	// foreach($rowRaw as $key => $data){
		// echo $key. " ; ";
		// print_r($data);
		// echo "\n";
	// }
	// echo "</pre>";
	
	$exrow = 6; // coz the header start at row 5;
	foreach($rowRaw as $key => $data){
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$exrow, $data['CustomerFirstName']);
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$exrow, $data['CustomerDOB']);
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$exrow, $data['CustomerEmail']);
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$exrow, $data['PayerEmail']);
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$exrow, $data['Customer Address']);
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$exrow, $data['CustomerCity']);
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$exrow, $data['Payer Address']);
		
		$objPHPExcel->getActiveSheet()->setCellValueExplicit('H'.$exrow,$data['CustomerMobilePhoneNum'], PHPExcel_Cell_DataType::TYPE_STRING );
		$objPHPExcel->getActiveSheet()->setCellValueExplicit('I'.$exrow,$data['CustomerHomePhoneNum'], PHPExcel_Cell_DataType::TYPE_STRING );
		$objPHPExcel->getActiveSheet()->setCellValueExplicit('j'.$exrow,$data['CustomerWorkPhoneNum'], PHPExcel_Cell_DataType::TYPE_STRING );
		
		$objPHPExcel->getActiveSheet()->SetCellValue('K'.$exrow, $data['CallReasonDesc']);
		$objPHPExcel->getActiveSheet()->SetCellValue('L'.$exrow, $data['CampaignName']);
		$objPHPExcel->getActiveSheet()->SetCellValue('M'.$exrow, $data['CallHistoryNotes']);
	
	$exrow++;
	}
	
	// Rename sheet
	echo date('H:i:s') . " Rename sheet\n";
	$objPHPExcel->getActiveSheet()->setTitle('Simple');

	// Save Excel 2007 file
	echo date('H:i:s') . " Write to Excel2007 format\n";
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	$objWriter->save("/opt/enigma/webapps/ciputralife/report/Generated/RAWData_CampaignInactive2_".$reporttype."-".date('Ymd').".xlsx");
	// $objWriter->save(str_replace('.php', '.xlsx', __FILE__));

	// Echo done
	echo date('H:i:s') . " Done writing file.\r\n";

}

	CreateReport();

	
?>