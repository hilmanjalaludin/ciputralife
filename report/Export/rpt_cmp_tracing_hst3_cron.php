<?php
	include("../fungsi/global.php");
	require_once dirname(__FILE__) . "/../../class/MYSQLConnect.php";
	require_once dirname(__FILE__) . "/../../class/class.list.table.php"; 
	/** Include PHPExcel */
	require_once dirname(__FILE__) . '/PHPExcel.php';

	$Modes = $argv;

	function CreateReportTracing()
	{
		global $db;
		global $Modes;

		if($Modes[1]=="MTD"){
			$start_date	= date("Y-m-")."01 00:00:00";
			$end_date	= date("Y-m-d")." 23:59:00";
		}else if($Modes[1]=="tgl"){
			$start_date	= $Modes[2];
			$end_date	= $Modes[3];
		}else{
			$start_date	= date("Y-m-d")." 00:00:00";
			$end_date	= date("Y-m-d")." 23:59:00";
		}
		$today = date("Y-m-d");
		                                  
		$CampaignInfo	= array();
		$CasesAPE		= array();
		$summaryReason	= array();
		$ReasonCall		= array();
		$attempt 		= array();
		/*********************CAMPAIN INFO*******************/
		// if($this->havepost('Campaign'))
		// {
			$sql = "select
					si.AssignSpv as spvid,
					a.id as spv,
					a.full_name as spvname,
					count(si.CustomerId) as datasize
					from t_gn_assignment si
					inner join t_gn_customer cs on si.CustomerId=cs.CustomerId
					inner join tms_agent a on si.AssignSpv=a.UserId
					where 1=1
					and cs.CampaignId in (1)
					";
			// if($this->havepost('Agent') != "") 
			// {
			// 	$sql .= " and si.AssignSelerId IN (".$this->escPost('Agent').")";
			// }
			// if($this->havepost('Supervisor') != "") 
			// {
			// 	$sql .= " and si.AssignSpv IN (".$this->escPost('Supervisor').")";
			// }
			$sql .= " GROUP BY si.AssignSpv,a.id,a.full_name;";
			// echo "<pre>".$sql."</pre>";
			$qry = $db ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$CampaignInfo[$rows['spvid']]['spvid'] = $rows['spvid'];
				$CampaignInfo[$rows['spvid']]['spv'] = $rows['spv'];
				$CampaignInfo[$rows['spvid']]['spvname'] = $rows['spvname'];
				$CampaignInfo[$rows['spvid']]['datasize'] = $rows['datasize'];
			}
			/**********************************************************************/

			/*********************CASES APE*******************/
			$sql="SELECT h.spv_id as spvid,
					count(b.InsuredId) AS CASES,
					round(SUM(IF(d.PayModeId=2,c.Premi*12,c.Premi))) as APE,
					round(sum(if(d.PayModeId=1,c.Premi/12,c.Premi))) as PREMI
					FROM t_gn_customer a
					INNER JOIN t_gn_insured b ON a.CustomerId=b.CustomerId
					INNER JOIN t_gn_policy c ON b.PolicyId = c.PolicyId
					INNER JOIN t_gn_productplan d ON c.ProductPlanId = d.ProductPlanId
					INNER JOIN t_gn_product e ON d.ProductId=e.ProductId
					INNER JOIN t_gn_product_category f ON e.product_category_id=f.product_category_id
					INNER JOIN t_gn_uploadreport g ON a.UploadId = g.UploadId
					inner join tms_agent h on h.UserId=a.SellerId
					WHERE 1=1
					AND a.CampaignId IN (1)
					AND a.CallReasonId IN (15)
					AND a.CustomerUpdatedTs >='".$start_date."'
					AND a.CustomerUpdatedTs <= '".$end_date."'";

			// if($this->havepost('Agent') != "") 
			// {
			// 	$sql .= " and a.SellerId IN (".$this->escPost('Agent').")";
			// }
			// if($this->havepost('Supervisor') != "") 
			// {
			// 	$sql .= "and h.spv_id IN (".$this->escPost('Supervisor').")";
			// }

			$sql .=	" group by h.spv_id;";
			// echo "<pre>".$sql."</pre>";
			$qry = $db ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				// $data[$rows['UserId']]['CASES'][$rows['product_category_id']] = $rows['CASES'];
				$CasesAPE[$rows['spvid']]['APE'] += (int)$rows['APE'];
				$CasesAPE[$rows['spvid']]['PREMI'] += (int)$rows['PREMI'];
				$CasesAPE[$rows['spvid']]['CASES'] += (int)$rows['CASES'];
			}
			/**********************************************************************/

			/*********************SUMMARY REASON*******************/
			$sql = "SELECT si.AssignSpv as spvid,
					count(IF(b.CallReasonContactedFlag=0,si.CustomerId,null)) AS uncontacted,
					count(IF(b.CallReasonContactedFlag=1,si.CustomerId,null)) AS contacted
					FROM t_gn_customer a
					inner join t_gn_assignment si on si.CustomerId=a.CustomerId
					INNER JOIN t_lk_callreason b ON a.CallReasonId = b.CallReasonId
					inner join tms_agent d on d.UserId=si.AssignSelerId
					WHERE 1=1
					AND a.CampaignId IN (1)
					and b.CallReasonStatusFlag=1
					AND a.CustomerUpdatedTs >='".$start_date."'
					AND a.CustomerUpdatedTs <='".$end_date."'";

			// if($this->havepost('Agent') != "") 
			// {
			// 	$sql .= " and si.AssignSelerId IN (".$this->escPost('Agent').")";
			// }
			// if($this->havepost('Supervisor') != "")
			// {
			// 	$sql .= " and d.spv_id  IN (".$this->escPost('Supervisor').")";
			// }
			$sql .= " GROUP BY si.AssignSpv;";
			// echo "<pre>".$sql."</pre>";
			$qry = $db ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				// $data[$rows['CampaignId']]['contact'] += $rows['contacted'];
				// $data[$rows['CampaignId']]['uncontacted'] += $rows['uncontacted'];
				$summaryReason[$rows['spvid']]['contacted'] += (int)$rows['contacted'];
				$summaryReason[$rows['spvid']]['uncontacted'] += (int)$rows['uncontacted'];

			}
			/**********************************************************************/

			/*********************Reason Call*******************/
			$sql="select si.AssignSelerId as UserId,si.AssignSpv as spvid,
					COUNT(if(cs.CallReasonId in (1),si.CustomerId,NULL)) as BUSY,
					COUNT(if(cs.CallReasonId in (2),si.CustomerId,NULL)) as INVALID_NUMBER,
					COUNT(if(cs.CallReasonId in (3),si.CustomerId,NULL)) as NO_PICK,
					COUNT(if(cs.CallReasonId in (4),si.CustomerId,NULL)) as CALL_AGAIN,
					COUNT(if(cs.CallReasonId in (5),si.CustomerId,NULL)) as MISS_CUSTOMER,
					COUNT(if(cs.CallReasonId in (6),si.CustomerId,NULL)) as THINKING,
					COUNT(if(cs.CallReasonId in (7),si.CustomerId,NULL)) as MOVED,
					COUNT(if(cs.CallReasonId in (8),si.CustomerId,NULL)) as OVERAGE,
					COUNT(if(cs.CallReasonId in (9),si.CustomerId,NULL)) as DONOTCALL,
					COUNT(if(cs.CallReasonId in (10),si.CustomerId,NULL)) as NO_CARD,
					COUNT(if(cs.CallReasonId in (11),si.CustomerId,NULL)) as NOT_INTEREST,
					COUNT(if(cs.CallReasonId in (12),si.CustomerId,NULL)) as WRONG_PERSON,
					COUNT(if(cs.CallReasonId in (13),si.CustomerId,NULL)) as FOLLOWUP_EMAIL,
					COUNT(if(cs.CallReasonId in (14),si.CustomerId,NULL)) as FOLLOWUP_WA,
					COUNT(if(cs.CallReasonId in (15),si.CustomerId,NULL)) as SALES,
					COUNT(if(cs.CallReasonId in (17),si.CustomerId,NULL)) as Reject_WA,
					COUNT(if(cs.CallReasonId in (18),si.CustomerId,NULL)) as Reject_Email,
					COUNT(if(cs.CallReasonId in (19),si.CustomerId,NULL)) as Agree_Email,
					COUNT(if(cs.CallReasonId in (20),si.CustomerId,NULL)) as Agree_WA,
					COUNT(if(cs.CallReasonId in (21),si.CustomerId,NULL)) as Thinking_Email,
					COUNT(if(cs.CallReasonId in (22),si.CustomerId,NULL)) as Thinking_WA,
					COUNT(IF(cs.CallReasonId IS NOT NULL,si.CustomerId,NULL)) AS Touch

				from t_gn_customer cs
				inner join t_gn_assignment si on si.CustomerId=cs.CustomerId
				inner join tms_agent a on a.UserId=si.AssignSelerId
				inner join t_lk_callreason c on cs.CallReasonId = c.CallReasonId
				where a.handling_type=4
			and c.CallReasonStatusFlag=1
			AND cs.CampaignId IN (1)
			AND cs.CustomerUpdatedTs >='".$start_date."'
			AND cs.CustomerUpdatedTs <= '".$end_date."'";

			// if($this->havepost('Agent') != "")
			// {
			// 	$sql .= " and si.AssignSelerId IN (".$this->escPost('Agent').")";
			// }
			// if($this->havepost('Supervisor') != "")
			// {
			// 	$sql .= " and a.spv_id IN (".$this->escPost('Supervisor').")";
			// }

			$sql .=	" group by si.AssignSpv;";
			// echo "<pre>".$sql."</pre>";
			$qry = $db ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$ReasonCall[$rows['spvid']]['BUSY'] += (INT)$rows['BUSY'];
				$ReasonCall[$rows['spvid']]['INVALID_NUMBER'] += (INT)$rows['INVALID_NUMBER'];
				$ReasonCall[$rows['spvid']]['NO_PICK'] += (INT)$rows['NO_PICK'];
				$ReasonCall[$rows['spvid']]['CALL_AGAIN'] += (INT)$rows['CALL_AGAIN'];
				$ReasonCall[$rows['spvid']]['MISS_CUSTOMER'] += (INT)$rows['MISS_CUSTOMER'];
				$ReasonCall[$rows['spvid']]['THINKING'] += (INT)$rows['THINKING'];
				$ReasonCall[$rows['spvid']]['MOVED'] += (INT)$rows['MOVED'];
				$ReasonCall[$rows['spvid']]['OVERAGE'] += (INT)$rows['OVERAGE'];
				$ReasonCall[$rows['spvid']]['DONOTCALL'] += (INT)$rows['DONOTCALL'];
				$ReasonCall[$rows['spvid']]['NO_CARD'] += (INT)$rows['NO_CARD'];
				$ReasonCall[$rows['spvid']]['NOT_INTEREST'] += (INT)$rows['NOT_INTEREST'];
				$ReasonCall[$rows['spvid']]['WRONG_PERSON'] += (INT)$rows['WRONG_PERSON'];
				$ReasonCall[$rows['spvid']]['FOLLOWUP_EMAIL'] += (INT)$rows['FOLLOWUP_EMAIL'];
				$ReasonCall[$rows['spvid']]['FOLLOWUP_WA'] += (INT)$rows['FOLLOWUP_WA'];
				$ReasonCall[$rows['spvid']]['SALES'] += (INT)$rows['SALES'];
				$ReasonCall[$rows['spvid']]['Thinking_WA'] += (INT)$rows['Thinking_WA'];
				$ReasonCall[$rows['spvid']]['Thinking_Email'] += (INT)$rows['Thinking_Email'];
				$ReasonCall[$rows['spvid']]['Agree_WA'] += (INT)$rows['Agree_WA'];
				$ReasonCall[$rows['spvid']]['Agree_Email'] += (INT)$rows['Agree_Email'];
				$ReasonCall[$rows['spvid']]['Reject_Email'] += (INT)$rows['Reject_Email'];
				$ReasonCall[$rows['spvid']]['Reject_WA'] += (INT)$rows['Reject_WA'];
				$ReasonCall[$rows['spvid']]['Touch'] += (INT)$rows['Touch'];
			}
			/**********************************************************************/

			/*********************attempt*******************/
			$sql = "SELECT
					c.spv_id as spvid,
					COUNT(b.CallHistoryId) AS CallAttempt
					from t_gn_callhistory b
					inner join t_gn_customer a on a.CustomerId=b.CustomerId
					INNER JOIN tms_agent c ON b.CreatedById = c.UserId
					WHERE 1=1
					and c.handling_type=4
					AND a.CampaignId IN (1)
					and b.CallHistoryCallDate >= '".$start_date."'
					AND b.CallHistoryCallDate <='".$end_date."'";

			// if($this->havepost('Agent') != "")
			// {
			// 	$sql .= " and b.CreatedById IN (".$this->escPost('Agent').")";
			// }
			// if($this->havepost('Supervisor') != "") 
			// {
			// 	$sql .= " and c.spv_id IN (".$this->escPost('Supervisor').")";
			// }
			$sql .= " GROUP BY c.spv_id;";
			// echo "<pre>".$sql."</pre>";
			$qry = $db ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				// $data[$rows['CampaignId']] = $rows['CallAttempt'];
				$attempt[$rows['spvid']]['CallAttempt'] = $rows['CallAttempt'];

			}

			PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );
			// Create new PHPExcel object
			$objPHPExcel = new PHPExcel();

			// Set document properties
			$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
										 ->setLastModifiedBy("Maarten Balliauw")
										 ->setTitle("Office 2007 XLSX Test Document")
										 ->setSubject("Office 2007 XLSX Test Document")
										 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
										 ->setKeywords("office 2007 openxml php")
										 ->setCategory("Test result file");
			$styleAll = array(
				'fill'  => array(
			        'type' => PHPExcel_Style_Fill::FILL_SOLID,
			        'color' => array('rgb' => '0410FB'),
			    ),
			    'borders' => array(
			        'allborders' => array(
			            'style' => PHPExcel_Style_Border::BORDER_THIN,
			            'color' => array('rgb' => '000000'),
			        )
			    ),
			    'alignment' => array(
			        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			    ),
			    'font' => array(
			    	'bold'	=> true,
					'color' => array('rgb' => 'FFFFFF'),
				)
			);
			$styleFill = array(
			    'fill'  => array(
			        'type' => PHPExcel_Style_Fill::FILL_SOLID,
			        'color' => array('rgb' => '0410FB'),
			    ),
			    'borders' => array(
			        'allborders' => array(
			            'style' => PHPExcel_Style_Border::BORDER_THIN,
			            'color' => array('rgb' => '000000')
			        )
			    )
			);
			$styleJudul = array(
				'font' => array(
					'color' => array('rgb' => '3AB82D'),
					'size'	=> 21,
				)
			);
			$styleSubJudul = array(
				'font' => array(
					'color' => array('rgb' => '3AB82D'),
					'size'	=> 12,
				)
			);
			$styleBorder = array(
				'borders' => array(
			        'allborders' => array(
			            'style' => PHPExcel_Style_Border::BORDER_THIN,
			            'color' => array('rgb' => '000000')
			        )
			    )
			);

			$objPHPExcel->getActiveSheet()->getStyle("B4:Z4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->setActiveSheetIndex(0)
				//judul
				->mergeCells('A1:J1')
				->setCellValue('A1','Report Tracking Review History 3')
			
				//sub judul
				->mergeCells('A2:J2')
				->setCellValue('A2','"Interval Date : '.$start_date.' - '.$end_date.' | Report Date : '.$today.'"');
			
			// set title table
			$objPHPExcel->setActiveSheetIndex(0)
				->mergeCells('A3:A4')
				->setCellValue('A3','No.')

				->mergeCells('B3:B4')
				->setCellValue('B3','SPV ID')

				->mergeCells('C3:C4')
				->setCellValue('C3','SPV Name')

				->mergeCells('D3:D4')
				->setCellValue('D3','Data Size')

				->mergeCells('E3:E4')
				->setCellValue('E3','New Data')

				->mergeCells('F3:F4')
				->setCellValue('F3','Utilize (TBS)')

				//set merge cell Contacted
				->mergeCells('G3:X3')
				->setCellValue('G3','Contacted')

				->setCellValue('G4','Call Again')

				->setCellValue('H4','Miss Customer')

				->setCellValue('I4','Thinking')

				->setCellValue('J4','Already Moved')

				->setCellValue('K4','Deceased / Overage')

				->setCellValue('L4','Do Not Call')

				->setCellValue('M4','No Card')

				->setCellValue('N4','Not Interest')

				->setCellValue('O4','Wrong Person')

				->setCellValue('P4','Follow Up Email')

				->setCellValue('Q4','Follow Up WA')

				->setCellValue('R4','Sales / interested')

				->setCellValue('S4','Reject WA')

				->setCellValue('T4','Reject Email')

				->setCellValue('U4','Agree Email')

				->setCellValue('V4','Agree WA')

				->setCellValue('W4','Thinking Email')

				->setCellValue('X4','Thinking WA')

				//set merge cell No Contacted
				->mergeCells('Y3:AA3')
				->setCellValue('Y3','Not Contacted')

				->setCellValue('Y4','Busy')

				->setCellValue('Z4','Invalid Number')

				->setCellValue('AA4','No Pick Up')

				->mergeCells('AB3:AB4')
				->setCellValue('AB3','Cases')

				->mergeCells('AC3:AC4')
				->setCellValue('AC3','APE')

				->mergeCells('AD3:AD4')
				->setCellValue('AD3','Case Size')

				->mergeCells('AE3:AE4')
				->setCellValue('AE3','Attempt')

				->mergeCells('AF3:AF4')
				->setCellValue('AF3','Attempt Ratio')

				->mergeCells('AG3:AG4')
				->setCellValue('AG3','Contacted Rate')

				->mergeCells('AH3:AH4')
				->setCellValue('AH3','Uncontacted Rate')

				->mergeCells('AI3:AI4')
				->setCellValue('AI3','Response Rate')

				->mergeCells('AJ3:AJ4')
				->setCellValue('AJ3','Success Rate')

				->mergeCells('AK3:AK4')
				->setCellValue('AK3','Presentation Rate');

			// cell style Judul Report
			$objPHPExcel->getActiveSheet()->getStyle("A1")->applyFromArray($styleJudul);
			$objPHPExcel->getActiveSheet()->getStyle("A2")->applyFromArray($styleSubJudul);
			//set color, border title table
			$last_row = $objPHPExcel->getActiveSheet()->getHighestDataRow(); //value angka position
			$max_col  = $objPHPExcel->getActiveSheet()->getHighestDataColumn(); //value abjad position
			$objPHPExcel->getActiveSheet()->getStyle("A3:{$max_col}3")->applyFromArray($styleAll);
			$objPHPExcel->getActiveSheet()->getStyle("A4:{$max_col}4")->applyFromArray($styleAll);

			$coloms = 0; //start indexing coloms 0
			$rows 	= 5;
			$no 	=1;
			$abjadExcel = 'A';
			// $objPHPExcel->getActiveSheet()->getStyle('A5')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_GENERAL);
			// $objPHPExcel->getActiveSheet()->getColumnDimension('E3')->setAutoSize(true);
			// $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn(4)->setAutoSize(true);
			foreach($CampaignInfo as $up_id => $arr_val)
			{
				$datasize		= (isset($CampaignInfo[$up_id]['datasize'])?$CampaignInfo[$up_id]['datasize']:"0");
				$BUSY			= (isset($ReasonCall[$up_id]['BUSY'])?$ReasonCall[$up_id]['BUSY']:"0");
				$INVALID_NUMBER = (isset($ReasonCall[$up_id]['INVALID_NUMBER'])?$ReasonCall[$up_id]['INVALID_NUMBER']:"0");
				$NO_PICK 		= (isset($ReasonCall[$up_id]['NO_PICK'])?$ReasonCall[$up_id]['NO_PICK']:"0");
				$CALL_AGAIN		= (isset($ReasonCall[$up_id]['CALL_AGAIN'])?$ReasonCall[$up_id]['CALL_AGAIN']:"0");
				$MISS_CUSTOMER 	= (isset($ReasonCall[$up_id]['MISS_CUSTOMER'])?$ReasonCall[$up_id]['MISS_CUSTOMER']:"0");
				$THINKING 		= (isset($ReasonCall[$up_id]['THINKING'])?$ReasonCall[$up_id]['THINKING']:"0");
				$MOVED 			= (isset($ReasonCall[$up_id]['MOVED'])?$ReasonCall[$up_id]['MOVED']:"0");
				$OVERAGE 		= (isset($ReasonCall[$up_id]['OVERAGE'])?$ReasonCall[$up_id]['OVERAGE']:"0");
				$DONOTCALL 		= (isset($ReasonCall[$up_id]['DONOTCALL'])?$ReasonCall[$up_id]['DONOTCALL']:"0");
				$NO_CARD 		= (isset($ReasonCall[$up_id]['NO_CARD'])?$ReasonCall[$up_id]['NO_CARD']:"0");
				$NOT_INTEREST 	= (isset($ReasonCall[$up_id]['NOT_INTEREST'])?$ReasonCall[$up_id]['NOT_INTEREST']:"0");
				$WRONG_PERSON 	= (isset($ReasonCall[$up_id]['WRONG_PERSON'])?$ReasonCall[$up_id]['WRONG_PERSON']:"0");
				$FOLLOWUP_EMAIL = (isset($ReasonCall[$up_id]['FOLLOWUP_EMAIL'])?$ReasonCall[$up_id]['FOLLOWUP_EMAIL']:"0");
				$$Reject_WA		= (isset($ReasonCall[$up_id]['$Reject_WA'])?$ReasonCall[$up_id]['$Reject_WA']:"0");
				$$Reject_Email	= (isset($ReasonCall[$up_id]['$Reject_Email'])?$ReasonCall[$up_id]['$$Reject_Email']:"0");
				$$Agree_Email	= (isset($ReasonCall[$up_id]['$Agree_Email'])?$ReasonCall[$up_id]['$$Agree_Email']:"0");
				$$Agree_WA		= (isset($ReasonCall[$up_id]['$Agree_WA'])?$ReasonCall[$up_id]['$$Agree_WA']:"0");
				$FOLLOWUP_WA 	= (isset($ReasonCall[$up_id]['FOLLOWUP_WA'])?$ReasonCall[$up_id]['FOLLOWUP_WA']:"0");
				$Thinking_Email	= (isset($ReasonCall[$up_id]['Thinking_Email'])?$ReasonCall[$up_id]['Thinking_Email']:"0");
				$Thinking_WA	= (isset($ReasonCall[$up_id]['Thinking_WA'])?$ReasonCall[$up_id]['Thinking_WA']:"0");
				$SALES 			= (isset($ReasonCall[$up_id]['SALES'])?$ReasonCall[$up_id]['SALES']:"0");
				$utilize 		= $CALL_AGAIN+$MISS_CUSTOMER+$THINKING+$MOVED+$OVERAGE+$DONOTCALL+$NO_CARD+$NOT_INTEREST+$WRONG_PERSON+$FOLLOWUP_EMAIL+$FOLLOWUP_WA+$SALES+$Reject_WA+$Reject_Email+$Agree_Email+$Agree_WA+$Thinking_Email+$Thinking_WA+$BUSY+$INVALID_NUMBER+$NO_PICK;
				$untouch		= $datasize-$utilize;

				//$objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, $no);
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}","{$no}");

				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->getStyle("A{$rows}:{$max_col}{$rows}")->applyFromArray($styleBorder); // set border
				$objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, $arr_val['spv']);

				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, $arr_val['spvname']);

				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}",$arr_val['datasize']);
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, $arr_val['datasize']);

				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}","{$untouch}");
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, $untouch);

				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}","{$utilize}");
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, $utilize);

				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}",(isset($ReasonCall[$up_id]['CALL_AGAIN']) ? $ReasonCall[$up_id]['CALL_AGAIN'] : 0));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($ReasonCall[$up_id]['CALL_AGAIN'])?$ReasonCall[$up_id]['CALL_AGAIN']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($ReasonCall[$up_id]['MISS_CUSTOMER'])?$ReasonCall[$up_id]['MISS_CUSTOMER']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($ReasonCall[$up_id]['MISS_CUSTOMER'])?$ReasonCall[$up_id]['MISS_CUSTOMER']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($ReasonCall[$up_id]['THINKING'])?$ReasonCall[$up_id]['THINKING']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($ReasonCall[$up_id]['THINKING'])?$ReasonCall[$up_id]['THINKING']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($ReasonCall[$up_id]['MOVED'])?$ReasonCall[$up_id]['MOVED']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($ReasonCall[$up_id]['MOVED'])?$ReasonCall[$up_id]['MOVED']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($ReasonCall[$up_id]['OVERAGE'])?$ReasonCall[$up_id]['OVERAGE']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($ReasonCall[$up_id]['OVERAGE'])?$ReasonCall[$up_id]['OVERAGE']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($ReasonCall[$up_id]['DONOTCALL'])?$ReasonCall[$up_id]['DONOTCALL']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($ReasonCall[$up_id]['DONOTCALL'])?$ReasonCall[$up_id]['DONOTCALL']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($ReasonCall[$up_id]['NO_CARD'])?$ReasonCall[$up_id]['NO_CARD']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($ReasonCall[$up_id]['NO_CARD'])?$ReasonCall[$up_id]['NO_CARD']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($ReasonCall[$up_id]['NOT_INTEREST'])?$ReasonCall[$up_id]['NOT_INTEREST']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($ReasonCall[$up_id]['NOT_INTEREST'])?$ReasonCall[$up_id]['NOT_INTEREST']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($ReasonCall[$up_id]['WRONG_PERSON'])?$ReasonCall[$up_id]['WRONG_PERSON']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($ReasonCall[$up_id]['WRONG_PERSON'])?$ReasonCall[$up_id]['WRONG_PERSON']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($ReasonCall[$up_id]['FOLLOWUP_EMAIL'])?$ReasonCall[$up_id]['FOLLOWUP_EMAIL']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($ReasonCall[$up_id]['FOLLOWUP_EMAIL'])?$ReasonCall[$up_id]['FOLLOWUP_EMAIL']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($ReasonCall[$up_id]['FOLLOWUP_WA'])?$ReasonCall[$up_id]['FOLLOWUP_WA']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($ReasonCall[$up_id]['FOLLOWUP_WA'])?$ReasonCall[$up_id]['FOLLOWUP_WA']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($ReasonCall[$up_id]['SALES'])?$ReasonCall[$up_id]['SALES']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($ReasonCall[$up_id]['SALES'])?$ReasonCall[$up_id]['SALES']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($ReasonCall[$up_id]['Reject_WA'])?$ReasonCall[$up_id]['Reject_WA']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($ReasonCall[$up_id]['Reject_WA'])?$ReasonCall[$up_id]['Reject_WA']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($ReasonCall[$up_id]['Reject_Email'])?$ReasonCall[$up_id]['Reject_Email']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($ReasonCall[$up_id]['Reject_Email'])?$ReasonCall[$up_id]['Reject_Email']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($ReasonCall[$up_id]['Agree_Email'])?$ReasonCall[$up_id]['Agree_Email']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($ReasonCall[$up_id]['Agree_Email'])?$ReasonCall[$up_id]['Agree_Email']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($ReasonCall[$up_id]['Agree_WA'])?$ReasonCall[$up_id]['Agree_WA']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($ReasonCall[$up_id]['Agree_WA'])?$ReasonCall[$up_id]['Agree_WA']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($ReasonCall[$up_id]['Thinking_Email'])?$ReasonCall[$up_id]['Thinking_Email']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($ReasonCall[$up_id]['Thinking_Email'])?$ReasonCall[$up_id]['Thinking_Email']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($ReasonCall[$up_id]['Thinking_WA'])?$ReasonCall[$up_id]['Thinking_WA']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($ReasonCall[$up_id]['Thinking_WA'])?$ReasonCall[$up_id]['Thinking_WA']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", "{$BUSY}");
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, $BUSY);
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($ReasonCall[$up_id]['INVALID_NUMBER'])?$ReasonCall[$up_id]['INVALID_NUMBER']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($ReasonCall[$up_id]['INVALID_NUMBER'])?$ReasonCall[$up_id]['INVALID_NUMBER']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($ReasonCall[$up_id]['NO_PICK'])?$ReasonCall[$up_id]['NO_PICK']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($ReasonCall[$up_id]['NO_PICK'])?$ReasonCall[$up_id]['NO_PICK']:"0"));

				$APE 			= (isset($CasesAPE[$up_id]['APE']) ? $CasesAPE[$up_id]['APE']:"0" );
				$CASES 	 		= (isset($CasesAPE[$up_id]['CASES'])?$CasesAPE[$up_id]['CASES']:"0");
				$PREMI 	 		= (isset($CasesAPE[$up_id]['PREMI'])?$CasesAPE[$up_id]['PREMI']:"0");
				$contact 		= (isset($summaryReason[$up_id]['contacted'])?$summaryReason[$up_id]['contacted']:"0");
				$uncontact 		= (isset($summaryReason[$up_id]['uncontacted'])?$summaryReason[$up_id]['uncontacted']:"0");
				$touch			= (isset($ReasonCall[$up_id]['Touch'])?$ReasonCall[$up_id]['Touch']:"0");
				$attemp 		= (isset($attempt[$up_id]['CallAttempt'])?$attempt[$up_id]['CallAttempt']:"0");

				$presentation 	= $THINKING+$NOT_INTEREST+$NO_CARD+$SALES;
				$AVGAPE			= $APE/$CASES;
				$prensentationrate = ($presentation/$contact)*100;
				// $ContactRate	= $touch/$contact;
				$ContactRate	= ($contact/$touch)*100;
				// $unContactRate	= $touch/$uncontact;
				$unContactRate	= ($uncontact/$touch) * 100;
				
				$ResponseRate	= ($CASES/$utilize) * 100;
				$SuksesRate		= ($CASES/$contact) * 100;
				
				$ConversionRate	= ($CASES/$contact) * 100;
				$AttempRatio	= ($attemp/$utilize);

				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", "{$CASES}");
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, $CASES);
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($CasesAPE[$up_id]['APE'])?$CasesAPE[$up_id]['APE']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($CasesAPE[$up_id]['APE'])?$CasesAPE[$up_id]['APE']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($CasesAPE[$up_id]['PREMI'])?$CasesAPE[$up_id]['PREMI']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($CasesAPE[$up_id]['PREMI'])?$CasesAPE[$up_id]['PREMI']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", (isset($attempt[$up_id]['CallAttempt'])?$attempt[$up_id]['CallAttempt']:"0"));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, (isset($attempt[$up_id]['CallAttempt'])?$attempt[$up_id]['CallAttempt']:"0"));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", round($AttempRatio,2));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, round($AttempRatio,2));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", round($ContactRate,2));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, round($ContactRate,2));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", round($unContactRate,2));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, round($unContactRate,2));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", round($ResponseRate,2));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, round($ResponseRate,2));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", round($SuksesRate,2));
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, round($SuksesRate,2));
				
				$coloms++;
				$abjadExcel++;
				$objPHPExcel->getActiveSheet()->setCellValue("{$abjadExcel}{$rows}", round($prensentationrate,2));
				$rows++;
				// $objPHPExcel->getActiveSheet()->setCellValueExplicitByColumnAndRow($coloms, $rows, round($prensentationrate,2));

				//default
				$coloms = 0;
				$abjadExcel='A';


				$no++;
				$cases = 0;
				$tdatasize			+= $datasize;
				$tBUSY				+= $BUSY;
				$tINVALID_NUMBER 	+= $INVALID_NUMBER;
				$tNO_PICK 			+= $NO_PICK;
				$tCALL_AGAIN		+= $CALL_AGAIN;
				$tMISS_CUSTOMER 	+= $MISS_CUSTOMER;
				$tTHINKING 			+= $THINKING;
				$tMOVED 			+= $MOVED;
				$tOVERAGE 			+= $OVERAGE;
				$tDONOTCALL 		+= $DONOTCALL;
				$tNO_CARD 			+= $NO_CARD;
				$tNOT_INTEREST 		+= $NOT_INTEREST;
				$tWRONG_PERSON 		+= $WRONG_PERSON;
				$tFOLLOWUP_EMAIL 	+= $FOLLOWUP_EMAIL;
				$tReject_WA			+= $Reject_WA;
				$tReject_Email		+= $Reject_Email;
				$tAgree_Email		+= $Agree_Email;
				$tAgree_WA			+= $Agree_WA;
				$tFOLLOWUP_WA 		+= $FOLLOWUP_WA;
				$tThinking_Email	+= $Thinking_Email;
				$tThinking_WA		+= $Thinking_WA;
				$tSALES 			+= $SALES;
				$tutilize 			+= $utilize;
				$tuntouch			+= $untouch;
				$tAPE 				+= $APE;
				$tCASES 			+= $CASES;
				$tPREMI 			+= $PREMI;
				$tcontact 			+= $contact;
				$tuncontact 		+= $uncontact;
				$ttouch 			+= $touch;
				$tattemp 			+= $attemp;
				$tattempratio		= ($tattemp/$tutilize);
				$tContactRate		= ($tcontact/$ttouch)*100;
				$tunContactRate		= ($tuncontact/$ttouch)*100;
				$tResponseRate		= ($tCASES/$tutilize)*100;
				$tSuksesRate		= ($tCASES/$tcontact)*100;
				$tpresentation 		+= $presentation;
				$tprensentationrate	= ($tpresentation/$tcontact)*100;
				
			}

			//name out file & workshett, file name 'Maximum 31 characters'
			if($Modes[1]=="MTD"){ // MTD
				$outputFile = "R_C_TrackingHstMTD3_".date('Ymd');
			}else{ // DAILY
				$outputFile = "R_C_TrackingHstDAILY3_".date('Ymd');
			}

			// Rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle($outputFile);

			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save(dirname(__FILE__).'/../Generated/'.$outputFile.'.xls');
			echo "Done Generated Excel Success";
			exit;

		// }

	}

	CreateReportTracing();
		
 ?>