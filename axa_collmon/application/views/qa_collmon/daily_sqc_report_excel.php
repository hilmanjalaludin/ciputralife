<?php 

//variable misterius
$remarks_form_id= 12;
/**
** 12 di dapat dari tabel coll_group_collmon
**/
$header_color = '2f3cd7';
$header_static=array(
	'PolicyNumber'=>"POLICY",
	'EFFDate'=>"EFF. DATE",
	'rec_duration'=>"DURASI",
	'InsuredFirstName'=>"NAME",
	'InsuredDOB'=>"DOB",
	'Premi'=>"PREMIUM",
	'ProductName'=>"PRODUCT",
	'CampaignName'=>"CAMPAIGN",
	'prospect'=>"PROSPECT",
	'PayerEmail'=>"EMAIL",
	'waktu_analisis'=>"WAKTU ANALIS",
	'status_report'=>"STATUS",
	'TM'=>"AGENT",
	'eff_date_comp'=>"EFF. DATE COMPLETE",
	'spv'=>"SPV",
	'mgr'=>"AM",
	'qc'=>"QC",
	'PhoneNum'=>"NO. TELP."
);
$score_static_header =array(
	'static_note'=>"NOTE",
	'static_status_system'=>"Status ALL System & Rec. Result",
	'static_plan'=>"Plan (Polis/TT)"
);

$this->appexcel->setActiveSheetIndex(0);
$this->appexcel->getActiveSheet()->setCellValue('A1', 
	"Report ".$form['header']['name']
);

$this->appexcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
$this->appexcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$this->appexcel->getActiveSheet()->setCellValue('A2', 
	"Call Monitoring Date : ".(isset($filter['colmon_date'])?
	$filter['colmon_date']['start']." s/d ".$filter['colmon_date']['end']:"-" )
);
$this->appexcel->getActiveSheet()->setCellValue('A3',
	"Selling Date : ".
	(isset($filter['selling_date'])?
	$filter['selling_date']['start']." s/d ".$filter['selling_date']['end']:"-" )
);
$this->appexcel->getActiveSheet()->setCellValue('A4',
	"Report Date : ".
	date('d/m/Y H:i:s')
);


$start_table = 5;
$sub_row= $start_table +1;
$content_result = $sub_row +1;
$cols_excel=1;
$merge_head= count($header_static);
$this->appexcel
->getActiveSheet()
->setCellValueByColumnAndRow(0,$start_table,
	"No."
);

$this->appexcel->getActiveSheet()->mergeCellsByColumnAndRow(
	0,5,0,6
);
$style_header=array(
	'alignment' => array(
        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
    ),
	'fill' => array(
       'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
             'rgb' => $header_color
        )
    ),
	'font'  => array(
		'bold' => true,
		'color' => array('rgb' => 'ffffff')
	)
);

$this->appexcel
->getActiveSheet()
->getStyle('A5')
->applyFromArray(
	$style_header
);
foreach($header_static as $cols=>$cols_name)
{
	$this->appexcel
	->getActiveSheet()
	->setCellValueByColumnAndRow($cols_excel,$start_table,
		$cols_name
	);
	$this->appexcel->getActiveSheet()->mergeCellsByColumnAndRow(
		$cols_excel,5,$cols_excel,6
	);
	$this->appexcel
	->getActiveSheet()
	->getStyle(
		$this->appexcel->GetCellByColumnAndRow($cols_excel,5,$cols_excel,6)
	)
	->applyFromArray(
		$style_header
	);
	$cols_excel++;
	
}

foreach($header_collmon as $group_id=>$group_name)
{
	$colspan=1;
	$rowspan=2;
	$strmerge="";
	
	$this->appexcel
	->getActiveSheet()
	->setCellValueByColumnAndRow($cols_excel,
		$start_table,
		$group_name
	);	
	if(isset($form['sub_category'][$group_id]))
	{
		$colspan = count($form['sub_category'][$group_id]);
		$rowspan=1;
		$cols_sub=$cols_excel;
		foreach($form['sub_category'][$group_id] as $sub_group_id=>$sub_group_name)
		{
			$this->appexcel
			->getActiveSheet()
			->setCellValueByColumnAndRow($cols_sub,
				$sub_row,
				$sub_group_name
			);
			$this->appexcel
			->getActiveSheet()
			->getStyle(
				$this->appexcel->GetCellByColumnAndRow($cols_sub,$sub_row)
			)
			->applyFromArray(
				$style_header
			);
			$cols_sub++;
			$merge_head++;
		}
		$this->appexcel->getActiveSheet()->mergeCellsByColumnAndRow(
			$cols_excel,$start_table,$cols_excel+$colspan -1,$start_table
		);
		$this->appexcel
		->getActiveSheet()
		->getStyle(
			$this->appexcel->GetCellByColumnAndRow($cols_excel,$start_table,$cols_excel+$colspan -1,$start_table)
		)
		->applyFromArray(
			$style_header
		);
	}
	else
	{
		$this->appexcel->getActiveSheet()->mergeCellsByColumnAndRow(
			$cols_excel,5,$cols_excel,6
		);
		$this->appexcel
		->getActiveSheet()
		->getStyle(
			$this->appexcel->GetCellByColumnAndRow($cols_excel,$start_table,$cols_excel+$colspan -1,$start_table)
		)
		->applyFromArray(
			$style_header
		);
		$merge_head++;
	}
	
	$cols_excel= $cols_excel + $colspan;
	if(isset($form['add_remark_category'][$group_id]) and $form['add_remark_category'][$group_id]==1)
	{
		$this->appexcel
		->getActiveSheet()
		->setCellValueByColumnAndRow($cols_excel,
			$start_table,
			"Remaks ".$group_name
		);
		$this->appexcel->getActiveSheet()->mergeCellsByColumnAndRow($cols_excel,$start_table,$cols_excel,$start_table+1);
		$this->appexcel
		->getActiveSheet()
		->getStyle(
			$this->appexcel->GetCellByColumnAndRow($cols_excel,$start_table)
		)
		->applyFromArray(
			$style_header
		);
		$cols_excel = $cols_excel + 1;
		$merge_head++;
	}	
}

$this->appexcel
->getActiveSheet()
->setCellValueByColumnAndRow($cols_excel,$start_table,
	"NO. CC & EXP. CARD / NO. SAVING"
);
$this->appexcel->getActiveSheet()->mergeCellsByColumnAndRow(
	$cols_excel,5,$cols_excel,6
);
$this->appexcel
->getActiveSheet()
->getStyle(
	$this->appexcel->GetCellByColumnAndRow($cols_excel,5,$cols_excel,6)
)
->applyFromArray(
	$style_header
);
$cols_excel++;
$merge_head++;

foreach(array(1,2,3,4) as $indx=>$val)
{
	$this->appexcel->getActiveSheet()->mergeCellsByColumnAndRow(
		0,$val,$merge_head,$val
	);
}

foreach($score_static_header as $index_static=>$name_static)
{
	$this->appexcel
	->getActiveSheet()
	->setCellValueByColumnAndRow($cols_excel,$start_table,
		$name_static
	);
	$this->appexcel->getActiveSheet()->mergeCellsByColumnAndRow(
		$cols_excel,5,$cols_excel,6
	);
	$this->appexcel
	->getActiveSheet()
	->getStyle(
		$this->appexcel->GetCellByColumnAndRow($cols_excel,5,$cols_excel,6)
	)
	->applyFromArray(
		$style_header
	);
	$cols_excel++;
}

$no=0;
foreach($policy as $cus_id => $insuredgroup)
{
	$no++;
	$rowspan= count($policy[$cus_id]);
	$samecustid= $no;
	
	foreach($insuredgroup as $insuredid => $cols_result){
		$cols_excel=0;
		$this->appexcel->getActiveSheet()
		->getStyle(
			$this->appexcel->GetCellByColumnAndRow(
				$cols_excel,$content_result,$cols_excel,($content_result+$rowspan-1)
			)
		)
		->getAlignment()
		->applyFromArray(
			array(
			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
		);
		if($samecustid==$no){
			$this->appexcel
			->getActiveSheet()
			->setCellValueByColumnAndRow($cols_excel,
				$content_result,
				$no
			);
			if($rowspan>1)
			{
				$this->appexcel->getActiveSheet()->mergeCellsByColumnAndRow(
					$cols_excel,$content_result,$cols_excel,($content_result+$rowspan-1)
				);
			}
		}
		foreach($header_static as $cols=>$cols_name)
		{
			$cols_excel++;
			$this->appexcel
			->getActiveSheet()
			->setCellValueByColumnAndRow($cols_excel,
				$content_result,
				$cols_result[$cols]
			);
		}
		if($samecustid==$no){
			
			foreach($header_collmon as $group_id=>$group_name)
			{
				if(isset($form['sub_category'][$group_id]))
				{
					foreach($form['sub_category'][$group_id] as $sub_group_id=>$sub_group_name)
					{
						$answer = (isset($col_result[$cus_id][$group_id][$sub_group_id])?
							$col_result[$cus_id][$group_id][$sub_group_id] :
							"-"
						);
						$cols_excel++;
						$this->appexcel
						->getActiveSheet()
						->setCellValueByColumnAndRow($cols_excel,
							$content_result,
							$answer
						);
						if($rowspan>1)
						{
							$this->appexcel->getActiveSheet()->mergeCellsByColumnAndRow(
								$cols_excel,$content_result,$cols_excel,($content_result+$rowspan-1)
							);
						}
					}
				}
				if(isset($form['add_remark_category'][$group_id]) and $form['add_remark_category'][$group_id]==1)
				{
					$cols_excel++;
					$remaks=(isset($remarks_group[$cus_id][$group_id])?$remarks_group[$cus_id][$group_id]:"-");
					$this->appexcel
					->getActiveSheet()
					->setCellValueByColumnAndRow($cols_excel,
						$content_result,
						$remaks
					);
					if($rowspan>1)
					{
						$this->appexcel->getActiveSheet()->mergeCellsByColumnAndRow(
							$cols_excel,$content_result,$cols_excel,($content_result+$rowspan-1)
						);
					}
				}
				if(isset($score_result[$cus_id][$group_id]))
				{
					$cols_excel++;
					$this->appexcel
					->getActiveSheet()
					->setCellValueByColumnAndRow($cols_excel,
						$content_result,
						$score_result[$cus_id][$group_id]
					);
					if($rowspan>1)
					{
						$this->appexcel->getActiveSheet()->mergeCellsByColumnAndRow(
							$cols_excel,$content_result,$cols_excel,($content_result+$rowspan-1)
						);
					}
				}
				/**
				** tanem dikit broh
				** prosesnya ada yg kelewat
				** waktu udah abis jadi biar cepet ^_^ :*
				**/
				if($remarks_form_id===$group_id)
				{
					for($i=1;$i<2;$i++)
					{
						$remaks=(isset($remarks_form[$cus_id][$i])?$remarks_form[$cus_id][$i]:"-");
						$cols_excel++;
						$this->appexcel
						->getActiveSheet()
						->setCellValueByColumnAndRow($cols_excel,
							$content_result,
							$remaks
						);
						if($rowspan>1)
						{
							$this->appexcel->getActiveSheet()->mergeCellsByColumnAndRow(
								$cols_excel,$content_result,$cols_excel,($content_result+$rowspan-1)
							);
						}
					}
					
				}
				////akhir tanem dikit =.=
			}
			$cols_excel++;
			$this->appexcel
			->getActiveSheet()
			->setCellValueByColumnAndRow($cols_excel,
				$content_result,
				$cols_result['PayerCreditCardNum']
			);
			if($rowspan>1)
			{
				$this->appexcel->getActiveSheet()->mergeCellsByColumnAndRow(
					$cols_excel,$content_result,$cols_excel,($content_result+$rowspan-1)
				);
			}
			foreach($score_static_header as $index_static=>$name_static)
			{
				$input=(isset($static_input[$cus_id][$index_static])?$static_input[$cus_id][$index_static]:"-");
				
				$cols_excel++;
				$this->appexcel
				->getActiveSheet()
				->setCellValueByColumnAndRow($cols_excel,
					$content_result,
					$input
				);
				if($rowspan>1)
				{
					$this->appexcel->getActiveSheet()->mergeCellsByColumnAndRow(
						$cols_excel,$content_result,$cols_excel,($content_result+$rowspan-1)
					);
				}

			}
		}
		$content_result++;
		$samecustid++;
	}
	
}



$filename="daily_sqc_report_".date('Ymdhis').".xls"; //save our workbook as this file name
header('Content-Type: application/vnd.ms-excel'); //mime type
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($this->appexcel, 'Excel5');
$objWriter->save('php://output');