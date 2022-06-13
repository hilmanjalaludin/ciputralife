<?php
require("/opt/enigma/webapps/ciputralife/fungsi/global.php");
require("/opt/enigma/webapps/ciputralife/class/MYSQLConnect.php");
require("/opt/enigma/webapps/ciputralife/class/class.list.table.php");

$Modes	= $argv;
if($Modes[1]=="MTD"){
		$reporttype = "MTD";
		$start_date	= date("Y-m-")."01";
		// $end_date	= date("Y-m-d");
		$end_date	= "2017-12-22";
	}else if($Modes[1]=="tgl"){
		$start_date	= $Modes[2];
		$end_date	= $Modes[3];
	}else{
		$reporttype = "Daily";
		// $start_date	= date("Y-m-d");
		// $end_date	= date("Y-m-d");
		$start_date	= "2017-12-22";
		$end_date	= "2017-12-22";
	}

/** Error reporting */
// error_reporting(E_ALL);

/** Include path **/
ini_set('include_path', ini_get('include_path').';../Classes/');

/** PHPExcel */
include 'PHPExcel.php';

/** PHPExcel_Writer_Excel2007 */
include 'PHPExcel/Writer/Excel2007.php';

// Create new PHPExcel object
echo date('H:i:s') . " Create new PHPExcel object\n";
$objPHPExcel = new PHPExcel();

// Set properties
echo date('H:i:s') . " Set properties\n";
$objPHPExcel->getProperties()->setCreator("Aseanindo");
$objPHPExcel->getProperties()->setLastModifiedBy("Aseanindo");
$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
$objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");


// Add some data
echo date('H:i:s') . " Add some data\n";
var_dump("CEK DUL");
die();

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
$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Campaign Overview By History');
$objPHPExcel->getActiveSheet()->SetCellValue('A2', 'Date Range '.date('Y-m-d'));
$objPHPExcel->getActiveSheet()->SetCellValue('A3', 'Report Date '.date('Y-m-d'));

// Applying Style
$objPHPExcel->getActiveSheet()->getStyle('A1:A3')->applyFromArray($styleTitleFont);

// Report Header
$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No.');
$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Upload DATE');
$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Source DB');
$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'Campaign Type');
$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'Segmentasi');
$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Campaign Name');
$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'Campaign Number');
$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Total Data');
$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Cases');
$objPHPExcel->getActiveSheet()->SetCellValue('J5', 'APE');
$objPHPExcel->getActiveSheet()->SetCellValue('K5', 'Contact');
$objPHPExcel->getActiveSheet()->SetCellValue('L5', 'Attempt');
$objPHPExcel->getActiveSheet()->SetCellValue('M5', 'Not Touch');
$objPHPExcel->getActiveSheet()->SetCellValue('N5', 'Touch');
$objPHPExcel->getActiveSheet()->SetCellValue('O5', 'AVG APE/Cases');
$objPHPExcel->getActiveSheet()->SetCellValue('P5', 'Contact Rate');
$objPHPExcel->getActiveSheet()->SetCellValue('Q5', 'Response Rate');
$objPHPExcel->getActiveSheet()->SetCellValue('R5', 'Conversion Rate');
$objPHPExcel->getActiveSheet()->SetCellValue('S5', 'Attempt Ratio');

// Applying Style
$objPHPExcel->getActiveSheet()->getStyle('A5:S5')->applyFromArray($styleHeaderFont);

	$rowhours	= 0;
	$no			= 1;
	$jam_getok	= jaammm();
	$leads1		= leads();
	$solicited1 = solicited();
	$contact1	= contact();
	$termin		= terminated();
	$etem		= attempt();
	$tmr1		= tmr();
	$sales12	= sales();
	// echo "<pre>";
	// print_r($sales12);
	// echo "</pre>";
	$anp1		= anp();
	$uploaddates= getUploadDate();

	$exrow = 6; // coz the header start at row 5;
foreach($uploaddates as $key => $value){

	$oLeads			= ($leads1[$value['CampaignNumber']] ? $leads1[$value['CampaignNumber']] :0);
	$oSolicited		= ($solicited1[$value['CampaignNumber']] ? $solicited1[$value['CampaignNumber']] :0);
	$oTerminLeads	= ($termin[$value['CampaignNumber']] ? $termin[$value['CampaignNumber']] :0);
	$oHours			= ($jam_getok[$value['CampaignNumber']] ? $jam_getok[$value['CampaignNumber']] :0);
	$oTMR			= ($tmr1[$value['CampaignNumber']] ? $tmr1[$value['CampaignNumber']] :0);
	$oSales			= ($sales12[$value['CampaignNumber']] ? $sales12[$value['CampaignNumber']] :0);
	$oANP			= ($anp1[$value['CampaignNumber']] ? $anp1[$value['CampaignNumber']] :0);

	$cases   	= getCases($value['CampaignId'],$value['UploadId'],"cases");
	$contact 	= getCases($value['CampaignId'],$value['UploadId'],"contacted");
	$nottouch	= getCases($value['CampaignId'],$value['UploadId'],"nottouch");
	$touch	 	= getCases($value['CampaignId'],$value['UploadId'],"touch");
	$perProd	= getCasesPerProduct($value['CampaignId'],$value['UploadId']);
	$oCases		= ($cases ? $cases :0);
	$oContact	= ($contact ? $contact :0);
	$oTouch 	= ($touch ? $touch :0);
	
	//update 10062017 rumus NOT TOUCH
	$oNotTouch	= ($value['total_data']-$oTouch);
	$oAttempt	= ($etem[$value['UploadId']] ? $etem[$value['UploadId']] :0);
	
	/** NGITUNG **/
	$LeadsRemain 	= ($leads1[$value['CampaignNumber']] ? ($leads1[$value['CampaignNumber']] - $termin[$value['CampaignNumber']]) :0);
	$LeadsAllocate	= ($leads1[$value['CampaignNumber']] ? ($leads1[$value['CampaignNumber']] / $tmr1[$value['CampaignNumber']]) :0);
	$AvgPremium		= ($anp1[$value['CampaignNumber']] ? (($anp1[$value['CampaignNumber']] / $sales12[$value['CampaignNumber']]) / 12) :0);
	$ContactPersen	= ($contact1[$value['CampaignNumber']] ? (($contact1[$value['CampaignNumber']] / $solicited1[$value['CampaignNumber']]) * 100) :0);
	$CPH			= ($contact1[$value['CampaignNumber']] ? ($contact1[$value['CampaignNumber']] / $jam_getok[$value['CampaignNumber']]) :0);
	$SCR			= ($sales12[$value['CampaignNumber']] ? (($sales12[$value['CampaignNumber']] / $contact1[$value['CampaignNumber']]) * 100) :0);
	$SPH			= ($sales12[$value['CampaignNumber']] ? ($sales12[$value['CampaignNumber']] / $jam_getok[$value['CampaignNumber']]) :0);
	$AnpPh			= ($anp1[$value['CampaignNumber']] ? ($anp1[$value['CampaignNumber']] / $jam_getok[$value['CampaignNumber']]) :0);
	$AnpPerTMR		= ($anp1[$value['CampaignNumber']] ? ($anp1[$value['CampaignNumber']] / $tmr1[$value['CampaignNumber']]) :0);
	$SalesPerTMR	= ($sales12[$value['CampaignNumber']] ? ($sales12[$value['CampaignNumber']]/$tmr1[$value['CampaignNumber']]) :0);
	$AARP			= ($anp1[$value['CampaignNumber']] ? $anp1[$value['CampaignNumber']] / $sales12[$value['CampaignNumber']] :0);
	
	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$exrow, $no);
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$exrow, $value['upload_date']);
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$exrow, '');
	$objPHPExcel->getActiveSheet()->SetCellValue('D'.$exrow, '');
	$objPHPExcel->getActiveSheet()->SetCellValue('E'.$exrow, $value['Segmentasi']);
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$exrow, $value['CampaignName']);
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$exrow, $value['CampaignNumber']);
	$objPHPExcel->getActiveSheet()->SetCellValue('H'.$exrow, $value['total_data']);
	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$exrow, number_format($oCases));
	$objPHPExcel->getActiveSheet()->SetCellValue('J'.$exrow, number_format($oANP));
	$objPHPExcel->getActiveSheet()->SetCellValue('K'.$exrow, number_format($oContact));
	$objPHPExcel->getActiveSheet()->SetCellValue('L'.$exrow, number_format($oAttempt));
	$objPHPExcel->getActiveSheet()->SetCellValue('M'.$exrow, number_format($oNotTouch));
	$objPHPExcel->getActiveSheet()->SetCellValue('N'.$exrow, number_format($oTouch));
	$objPHPExcel->getActiveSheet()->SetCellValue('O'.$exrow, number_format($oANP/$oCases));
	$objPHPExcel->getActiveSheet()->SetCellValue('P'.$exrow, ROUND(($oContact/$oTouch)*100,2)."%");
	$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$exrow, ROUND(($oCases/$oTouch)*100,2)."%");
	$objPHPExcel->getActiveSheet()->SetCellValue('R'.$exrow, ROUND(($oCases/$oContact)*100,2)."%");
	$objPHPExcel->getActiveSheet()->SetCellValue('S'.$exrow, ROUND(($oAttempt/$oTouch),2));
	
	$exrow++;
	$no++;
	$sTotalData	+= $value['total_data'];
	$sCases		+= $oCases;
	$sANP 		+= $oANP;
	$sContact	+= $oContact;
	$sAttempt 	+= $oAttempt;
	$sNotTouch 	+= $oNotTouch;
	$sTouch		+= $oTouch;
	$tAVGNBI	+= ($oANP/$oCases);
	$tContactRate  = ($sContact/$sTouch);
	$tResponseRate = ($sCases/$sTouch);
	$tConversiRate = ($sCases/$sContact);
	$tAttemptRatio = ($sAttempt/$sTouch);
}
	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$exrow, '');
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$exrow, '');
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$exrow, '');
	$objPHPExcel->getActiveSheet()->SetCellValue('D'.$exrow, '');
	$objPHPExcel->getActiveSheet()->SetCellValue('E'.$exrow, '');
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$exrow, '');
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$exrow, 'Subtotal');
	$objPHPExcel->getActiveSheet()->SetCellValue('H'.$exrow, number_format($sTotalData));
	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$exrow, number_format($sCases));
	$objPHPExcel->getActiveSheet()->SetCellValue('J'.$exrow, number_format($sANP));
	$objPHPExcel->getActiveSheet()->SetCellValue('K'.$exrow, number_format($sContact));
	$objPHPExcel->getActiveSheet()->SetCellValue('L'.$exrow, number_format($sAttempt));
	$objPHPExcel->getActiveSheet()->SetCellValue('M'.$exrow, number_format($sNotTouch));
	$objPHPExcel->getActiveSheet()->SetCellValue('N'.$exrow, number_format($sTouch));
	$objPHPExcel->getActiveSheet()->SetCellValue('O'.$exrow, number_format($tAVGNBI));
	$objPHPExcel->getActiveSheet()->SetCellValue('P'.$exrow, ROUND(($tContactRate)*100,2)."%");
	$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$exrow, ROUND(($tResponseRate)*100,2)."%");
	$objPHPExcel->getActiveSheet()->SetCellValue('R'.$exrow, ROUND(($tConversiRate)*100,2)."%");
	$objPHPExcel->getActiveSheet()->SetCellValue('S'.$exrow, ROUND(($tAttemptRatio),2));
	
	$objPHPExcel->getActiveSheet()->getStyle('A'.$exrow.':S'.$exrow)->applyFromArray($styleHeaderFont);

// Rename sheet
echo date('H:i:s') . " Rename sheet\n";
$objPHPExcel->getActiveSheet()->setTitle('Simple');

		
// Save Excel 2007 file
echo date('H:i:s') . " Write to Excel2007 format\n";
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save("/opt/enigma/webapps/ciputralife/report/Generated/ReportCampaignOverviewHistory_".$reporttype."-".date('Ymd').".xlsx");
// $objWriter->save(str_replace('.php', '.xlsx', __FILE__));

// Echo done
echo date('H:i:s') . " Done writing file.\r\n";

function getCases($CampaignId,$UploadId,$cat=""){
	global $start_date;
	global $end_date;
	
	switch($cat){
	case "cases":
		$sql = "SELECT COUNT(DISTINCT tcst.CustomerId) as Cases FROM t_gn_campaign tcmp
			LEFT JOIN t_gn_customer tcst ON tcmp.CampaignId = tcst.CampaignId
			LEFT JOIN t_lk_callreason tcll ON tcll.CallReasonId = tcst.CallReasonId

			left join t_gn_insured tgin on tcst.CustomerId = tgin.CustomerId
			left join t_gn_policy tgpc on tgin.PolicyId = tgpc.PolicyId
			left join t_gn_productplan tgpl on tgpc.ProductPlanId = tgpl.ProductPlanId
			left join t_gn_product tgpd on tgpl.ProductId = tgpd.ProductId
			left join t_gn_uploadreport tgup on tgup.UploadId = tcst.UploadId

			WHERE 1=1
			AND tcst.CampaignId = ".$CampaignId."
			AND tcst.CallReasonId = 15 AND tcst.CallReasonQue = 1
			AND tcst.CustomerUpdatedTs >= '".$start_date." 00:00:00'
			AND tcst.CustomerUpdatedTs <= '".$end_date." 23:00:00'";
		break;
	case "contacted":
		$sql = "select count(distinct ch.CustomerId) as Cases
				from t_gn_callhistory ch
				inner join t_gn_customer cs on cs.CustomerId=ch.CustomerId
				inner join t_lk_callreason ca on ca.CallReasonId=ch.CallReasonId
				where ch.CallHistoryId =
					(select max(subch.CallHistoryId) from t_gn_callhistory subch where
						subch.CallHistoryCallDate >= '".$start_date." 00:00:00'
						and subch.CallHistoryCallDate <= '".$end_date." 23:50:00'
						and subch.CustomerId = ch.CustomerId
						AND cs.CampaignId = ".$CampaignId."
						and ca.CallReasonContactedFlag = 1
					);
				";
		break;
	case "nottouch":
		$sql = "SELECT COUNT(DISTINCT tcst.CustomerId) as Cases FROM t_gn_campaign tcmp
		LEFT JOIN t_gn_customer tcst ON tcmp.CampaignId = tcst.CampaignId
		LEFT JOIN t_lk_callreason tcll ON tcll.CallReasonId = tcst.CallReasonId

		left join t_gn_insured tgin on tcst.CustomerId = tgin.CustomerId
		left join t_gn_policy tgpc on tgin.PolicyId = tgpc.PolicyId
		left join t_gn_productplan tgpl on tgpc.ProductPlanId = tgpl.ProductPlanId
		left join t_gn_product tgpd on tgpl.ProductId = tgpd.ProductId
		left join t_gn_uploadreport tgup on tgup.UploadId = tcst.UploadId

		WHERE 1=1
		AND tcst.CampaignId = ".$CampaignId."
		AND tcst.CallReasonId IS NULL";
		break;
	case "touch":
		$sql = "select count(distinct ch.CustomerId) as Cases
			from t_gn_callhistory ch
			inner join t_gn_customer cs on cs.CustomerId=ch.CustomerId
			where ch.CallHistoryCallDate >= '".$start_date." 00:00:00'
			and ch.CallHistoryCallDate <= '".$end_date." 23:00:00'
			AND cs.CampaignId = ".$CampaignId."
			";
		break;
	}

	$qry = mysql_query($sql);
	$row = mysql_fetch_array($qry);
	return $row[0];
}

function getUploadDate(){
	$sql = "SELECT DISTINCT(tgur.UploadDateTs) AS upload_date,\"\" AS source_db,\"\" AS campaig_type,
				tlct.Category AS Segmentasi,tgcm.CampaignId,tgcm.CampaignNumber,tgcm.CampaignName,
				COUNT(DISTINCT tgcs.CustomerId) AS `total_data`,tgur.UploadId
				FROM t_gn_campaign tgcm
				LEFT JOIN t_gn_customer tgcs ON tgcs.CampaignId = tgcm.CampaignId
				LEFT JOIN t_gn_uploadreport tgur ON tgur.UploadId = tgcs.UploadId
				LEFT JOIN t_lk_category tlct ON tlct.CategoryId = tgcm.CategoryId
				WHERE 1=1 AND tgcs.UploadId is not null
				AND tgcm.CampaignStatusFlag = 1
				GROUP BY tgur.UploadId";
		$qry = mysql_query($sql);
		while ($row = mysql_fetch_assoc($qry)){
				$UploadId[$row['UploadId']] = $row;
			}
	return $UploadId;
}

function getCasesPerProduct($CampaignId,$prodId){
	global $start_date;
	global $end_date;
	
			$sql = "Select tgpd.ProductCode,count(tgcs.CustomerId) as jumlah,
					tgcs.CustomerFirstName,tgin.InsuredId,tgin.PolicyId,tgpc.ProductPlanId,tgpd.ProductCode,tgpd.product_category_id
					from t_gn_customer tgcs
					left join t_gn_insured tgin on tgcs.CustomerId = tgin.CustomerId
					left join t_gn_policy tgpc on tgin.PolicyId = tgpc.PolicyId
					left join t_gn_productplan tgpl on tgpc.ProductPlanId = tgpl.ProductPlanId
					left join t_gn_product tgpd on tgpl.ProductId = tgpd.ProductId

					LEFT JOIN t_gn_uploadreport tgur ON tgur.UploadId = tgcs.UploadId
					left join t_gn_campaign tcmpg ON tcmpg.CampaignId = tgcs.CampaignId
					where tgcs.CallReasonId in (20,21) And tgin.InsuredId is not null

					AND DATE(tgcs.CustomerUpdatedTs) >= '".$start_date." 00:00:00'
					AND DATE(tgcs.CustomerUpdatedTs) <= '".$end_date." 23:59:00'
					and tcmpg.CampaignStatusFlag = 1
					group by tgpd.ProductCode";
			$qry = mysql_query($sql);
			while ($row = mysql_fetch_array($qry)){
				$oPerProduct[$row['product_category_id']][$row['ProductCode']] = $row['jumlah'];
			}
			return $oPerProduct;
		}

function jaammm(){
			global $db;
			global $start_date;
	global $end_date;
	
			$jaammm=array();
			$sql ="  SELECT
						ca.CampaignId, ca.CampaignNumber,
						SUM((ac.EndCallTs - ac.StartCallTs)/3600) AS Hours
					FROM t_gn_activitycall AS ac
						LEFT JOIN t_gn_customer c ON c.CustomerId=ac.CustomerId
						LEFT JOIN t_gn_campaign ca ON ca.CampaignId=c.CampaignId
					WHERE 1=1
						AND c.CustomerUpdatedTs >= '".$start_date." 00:00:00'
						AND c.CustomerUpdatedTs <= '".$end_date." 23:00:00'
					GROUP BY ca.CampaignNumber";

			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$jaammm[$row['CampaignNumber']] = $row['Hours'];
			}
			return $jaammm;
		}

function leads(){
			global $db;
			global $start_date;
	global $end_date;
	
			$leads=array();
			$sql =" SELECT
						ca.CampaignNumber, ca.CampaignId,
						COUNT(DISTINCT c.CustomerId) AS Leads
					FROM t_gn_customer c
						LEFT JOIN t_gn_campaign ca ON c.CampaignId = ca.CampaignId
					WHERE 1=1
						AND ca.CampaignNumber IS NOT NULL
						AND ca.CampaignId IS NOT NULL
						AND DATE(c.CustomerUpdatedTs) >= '".$start_date." 00:00:00'
						AND DATE(c.CustomerUpdatedTs) <= '".$end_date." 23:50:00'
					GROUP BY ca.CampaignNumber;";

			// echo "<pre>".$sql."</pre>";
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$leads[$row['CampaignNumber']] = $row['Leads'];
			}
			return $leads;
		}

		//Fungsi Solicited
function solicited(){
			global $db;
			global $start_date;
	global $end_date;
	
			$solicited=array();
			$sql =" SELECT
						b.CampaignNumber,
						SUM(IF(a.CustomerUpdatedTs IS NOT NULL, 1,0)) AS Solicited
					FROM t_gn_customer a
						LEFT JOIN t_gn_campaign b ON a.CampaignId = b.CampaignId
					WHERE 1=1
						AND DATE(a.CustomerUpdatedTs) >= '".$start_date." 00:00:00'
						AND DATE(a.CustomerUpdatedTs) <= '".$end_date." 23:50:00'
					GROUP BY b.CampaignNumber;";
			// echo "<pre>".$sql."</pre>";
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$solicited[$row['CampaignNumber']] = $row['Solicited'];
			}
			return $solicited;
		}

		//Fungsi Contact
function contact(){
			global $db;
			global $start_date;
	global $end_date;
	
			$contact=array();
			$sql =" SELECT
						#ca.CampaignNumber,
						up.UploadId,
						ca.CampaignId,
						COUNT(DISTINCT IF(a.CallReasonContactedFlag = 1,c.CustomerId,0)) AS Contact
					FROM t_gn_customer c
						LEFT JOIN t_gn_campaign ca ON c.CampaignId = ca.CampaignId
						LEFT JOIN t_lk_callreason a ON c.CallReasonId = a.CallReasonId
						LEFT JOIN t_gn_uploadreport up ON c.UploadId = up.UploadId
					WHERE 1=1
						AND DATE(c.CustomerUpdatedTs) >= '".$start_date." 00:00:00'
						AND DATE(c.CustomerUpdatedTs) <= '".$end_date." 23:50:00'
					GROUP BY ca.CampaignNumber ";
			// echo "<pre>".$sql."</pre>";
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				// $contact[$row['CampaignNumber']] = $row['Contact'];
				$contact[$row['UploadId']] = $row['Contact'];
			}
			return $contact;
		}

		//Fungsi Terminated Leads
function terminated(){
			global $db;
			global $start_date;
	global $end_date;
	
			$terminated=array();
			$sql = "SELECT
						ca.CampaignNumber, ca.CampaignId,
						COUNT(DISTINCT IF(a.CallReasonTerminate = 1,c.CustomerId,0)) AS Terminate
					FROM t_gn_customer c
						LEFT JOIN t_gn_campaign ca ON c.CampaignId = ca.CampaignId
						LEFT JOIN t_lk_callreason a ON c.CallReasonId = a.CallReasonId
					WHERE 1=1
						AND DATE(c.CustomerUpdatedTs) >= '".$start_date." 00:00:00'
						AND DATE(c.CustomerUpdatedTs) <= '".$end_date." 23:50:00'
					GROUP BY ca.CampaignNumber";
			// echo "<pre>".$sql."</pre>";
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry))
			{
				$terminated[$row['CampaignNumber']] = $row['Terminate'];
			}
			return $terminated;
		}

		//Fungsi TMR
		function tmr(){
			global $db;
			global $start_date;
	global $end_date;
	
			$tmr = array();
			$sql = "SELECT
						c.CampaignNumber AS CampaignNumber,
						c.CampaignId AS CampaignId,
						COUNT(DISTINCT a.AssignSelerId) AS tmr
					FROM t_gn_assignment a
						LEFT JOIN t_gn_customer b ON a.CustomerId = b.CustomerId
						LEFT JOIN t_gn_campaign c ON b.CampaignId = c.CampaignId
					WHERE
						a.AssignSelerId IS NOT NULL
						AND DATE(b.CustomerUpdatedTs) >= '".$start_date." 00:00:00'
						AND DATE(b.CustomerUpdatedTs) <= '".$end_date." 23:50:00'
					GROUP BY c.CampaignNumber";
			//echo $sql;
			$qry=mysql_query($sql);
			while($row = mysql_fetch_array($qry))
			{
				$tmr[$row['CampaignNumber']] = $row['tmr'];
			}
			return $tmr;
		}

		//Fungsi Attempt d.CampaignNumber,
		function attempt(){
			global $db;
			global $start_date;
	global $end_date;
	
			$attempt = array();
			$sql = "SELECT DISTINCT

						up.UploadId,
						COUNT(a.CallHistoryId) AS Attempt
					FROM t_gn_callhistory a
						LEFT JOIN t_gn_customer b ON a.CustomerId=b.CustomerId
						LEFT JOIN t_gn_uploadreport up ON b.UploadId=up.UploadId
						LEFT JOIN tms_agent c on a.CreatedById=c.UserId
						LEFT JOIN t_gn_campaign d ON b.CampaignId = d.CampaignId
					WHERE 1=1
						AND DATE(a.CallHistoryCreatedTs) >= '".$start_date." 00:00:00'
						AND DATE(a.CallHistoryCreatedTs) <= '".$end_date." 23:50:00'
					GROUP BY up.UploadId";
			//echo $sql;
			$qry=mysql_query($sql);
			while($row = mysql_fetch_array($qry))
			{
				$attempt[$row['UploadId']] = $row['Attempt'];
			}
			return $attempt;
		}

		//Fungsi Sales
		function sales(){
			global $db;
			global $start_date;
			global $end_date;
			$sales = array();
			$sql = " SELECT
						f.CampaignId,
						f.CampaignNumber, COUNT(DISTINCT a.CustomerId) AS Sales
					FROM t_gn_policyautogen a
						LEFT JOIN t_gn_policy b ON a.PolicyNumber=b.PolicyNumber
						LEFT JOIN t_gn_customer c ON a.CustomerId=c.CustomerId
						LEFT JOIN t_gn_assignment d ON c.CustomerId=d.CustomerId
						LEFT JOIN t_gn_productplan e ON b.ProductPlanId=e.ProductPlanId
						LEFT JOIN t_gn_campaign f ON c.CampaignId = f.CampaignId
					WHERE
						DATE(b.PolicySalesDate)>='".$start_date." 00:00:00' AND
						DATE(b.PolicySalesDate)<='".$end_date." 23:54:00' AND
						c.CallReasonId IN(15,16)
					GROUP BY f.CampaignNumber ";

			$qry=mysql_query($sql);
			while ($row = mysql_fetch_assoc($qry))
			{
				$sales[$row['CampaignNumber']] = $row['Sales'];
			}
			return $sales;
		}

		//Fungsi ANP
		function anp(){
			global $db;
			global $start_date;
	global $end_date;
	
			$anp = array();
			$sql = "SELECT
						f.CampaignId, f.CampaignNumber,
						SUM(b.Premi) AS tmp, SUM(IF(e.PayModeId=2,(b.Premi*12), b.Premi)) AS ANP
					FROM t_gn_policyautogen a
						LEFT JOIN t_gn_policy b ON a.PolicyNumber=b.PolicyNumber
						LEFT JOIN t_gn_customer c ON a.CustomerId=c.CustomerId
						LEFT JOIN t_gn_assignment d ON c.CustomerId=d.CustomerId
						LEFT JOIN t_gn_productplan e ON b.ProductPlanId=e.ProductPlanId
						LEFT JOIN t_gn_product tgpd on e.ProductId = tgpd.ProductId
						LEFT JOIN t_gn_campaign f ON c.CampaignId = f.CampaignId
					WHERE
						DATE(b.PolicySalesDate)>='".$start_date." 00:00:00' AND
						DATE(b.PolicySalesDate)<='".$end_date." 23:50:00' AND
						c.CallReasonId IN(15) AND c.CallReasonQue = 1
					GROUP BY f.CampaignNumber ";

			$qry=mysql_query($sql);
			while ($row = mysql_fetch_assoc($qry))
			{
				$anp[$row['CampaignNumber']] = $row['ANP'];
			}
			return $anp;
		}

		//New Function added per 2nd Mei 2014
		function getProductCode($i){
			$sql = "select a.ProductCode from t_gn_product a where ProductStatusFlag = $i";
			$qry=mysql_query($sql);
			$k=0;
			while ($row = mysql_fetch_assoc($qry)){
				$ProductCode[$k] = $row['ProductCode'];
				$k++;
			}
			return $ProductCode;
		}

		function getProductCategory(){
			$sql = "SELECT a.product_category_id,a.product_category_code,
					b.ProductId,b.ProductCode
					FROM t_gn_product_category a
					INNER JOIN t_gn_product b ON a.product_category_id=b.product_category_id
					WHERE b.ProductStatusFlag = 1";
			$qry=mysql_query($sql);
			while ($row = mysql_fetch_array($qry)){
				$ProductCategory['category'][$row['product_category_id']] = $row['product_category_code'];
				$ProductCategory['product'][$row['product_category_id']][$row['ProductId']] = $row['ProductCode'];

			}
			return $ProductCategory;
		}
