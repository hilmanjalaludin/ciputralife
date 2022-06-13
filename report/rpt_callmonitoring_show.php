<?php
include("../model/m_rpt_callmonitoring.php");


	if( $M_RPT_CALLMON-> ReportType=='html'){
		showHTML();
	}
	else if( $M_RPT_CALLMON-> ReportType=='excel'){
		showExcel();
	}
	

 /** generate report call mon by html **/
 

	function ShowHTML()
	{
		global $M_RPT_CALLMON;
		
			   $M_RPT_CALLMON -> Header();
			   $M_RPT_CALLMON -> Content();
			   $M_RPT_CALLMON -> Footer();
	}
	
	
 /** generate report call mon by excel **/

		
	function showExcel(){
		global $M_RPT_CALLMON;
			
			
			ExcelModus();
			
			  $M_RPT_CALLMON -> Header();
			  $M_RPT_CALLMON -> Content();
			  $M_RPT_CALLMON -> Footer();
		
	}
	
 /** generate report call mon by excel **/

	
	function ExcelModus(){
			$xlsName = 'call_mon_'.date('dmY_His').'.xls';
			header("Content-Type: application/vnd.ms-excel");
			header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Cache-Control: public");
			header("Content-Disposition: attachment; filename=$xlsName");
			header("Expires: 0");
			header("Pragma: no-cache");
	}	
		
?>	

