<?php
class agent_performance extends indexReport
{
	var $mode;
	var $content;
	var $report_type;
	var $start_date;
	var $end_date;
	
	function agent_performance()
	{
		$this->content 		= $this->escPost('content');
		$this->report_type 	= $this->escPost('report_type');
		$this->start_date 	= $this -> formatDateEng($this->escPost('start_date'));
		$this->end_date 	= $this -> formatDateEng($this->escPost('end_date'));
		$this->mode 		= $this->escPost('mode');
	}
	
	function show_content()
	{
		switch($this->content)
		{
			case 'HTML'  : $this->content_report_html(); break;
			case 'EXCEL' : $this->content_report_excel(); break;
			default : echo "Please, select content report!"; break;
		}
	}
	
	function content_report_html()
	{
		$this->header_excel();
		$this->header_report();
		switch($this->mode)
		{
			case 'daily' 	: $this->content_report_daily(); break;
			case 'summary'  : $this->content_report_summary(); break;
			default			: echo "This mode isn\'t available!"; break;
		}
		$this->footer_excel();
	}
	
	function content_report_excel()
	{
		$this->download_excel();
		$this->header_excel();
		$this->header_report();
		switch($this->mode)
		{
			case 'daily' 	: $this->content_report_daily(); break;
			case 'summary'  : $this->content_report_summary(); break;
			default			: echo "This mode isn\'t available!"; break;
		}
		$this->footer_excel();
	}
	
	function content_report_daily()
	{
		for($idx=1;$idx<=10;$idx++)
		{
	?>
		 <tr height=20 style='height:15.0pt'>
		  <td height=20 class=xl6522301 style='height:15.0pt;border-top:none' nowrap><?php echo $idx; ?></td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>content_report_daily</td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>&nbsp;</td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>&nbsp;</td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>&nbsp;</td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>&nbsp;</td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>&nbsp;</td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>&nbsp;</td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>&nbsp;</td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>&nbsp;</td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>&nbsp;</td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>&nbsp;</td>
		 </tr>
	<?php
		}
	}
	
	function content_report_summary()
	{
		for($idx=1;$idx<=10;$idx++)
		{
	?>
		 <tr height=20 style='height:15.0pt'>
		  <td height=20 class=xl6522301 style='height:15.0pt;border-top:none' nowrap><?php echo $idx; ?></td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>content_report_summary</td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>&nbsp;</td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>&nbsp;</td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>&nbsp;</td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>&nbsp;</td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>&nbsp;</td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>&nbsp;</td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>&nbsp;</td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>&nbsp;</td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>&nbsp;</td>
		  <td class=xl6622301 style='border-top:none;border-left:none' nowrap>&nbsp;</td>
		 </tr>
	<?php
		}
	}
	
	function download_excel()
	{
		$filename = $this->report_type.'_'.date('Ymd').'_'.date('His').'.xls';
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Cache-Control: private");
		header("Pragma: no-cache");
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=$filename");
	}
	
	function header_excel()
	{
	?>
		<html xmlns:o="urn:schemas-microsoft-com:office:office"
		xmlns:x="urn:schemas-microsoft-com:office:excel"
		xmlns="http://www.w3.org/TR/REC-html40">

		<head>
		<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
		<meta name=ProgId content=Excel.Sheet>
		<meta name=Generator content="Microsoft Excel 12">
		<?php
			if($this->content != 'EXCEL')
			{
			?>
				<title>Report Agent Performance AXA</title>
						<link rel="shortcut icon" type="image/x-icon" href="../gambar/enigma.ico">
			<?php
			}
		?>
		<link rel=File-List href="sample%20report_files/filelist.xml">
		<style id="Book1_22301_Styles">
		<!--table
			{mso-displayed-decimal-separator:"\.";
			mso-displayed-thousand-separator:"\,";}
		.xl1522303
			{padding-top:1px;
			padding-right:1px;
			padding-left:1px;
			mso-ignore:padding;
			color:black;
			font-size:11.0pt;
			font-weight:400;
			font-style:normal;
			text-decoration:none;
			font-family:Calibri, sans-serif;
			mso-font-charset:0;
			mso-number-format:General;
			text-align:general;
			vertical-align:middle;
			mso-background-source:auto;
			mso-pattern:auto;
			white-space:nowrap;}
		.xl1522301
			{padding-top:1px;
			padding-right:1px;
			padding-left:1px;
			mso-ignore:padding;
			color:black;
			font-size:11.0pt;
			font-weight:400;
			font-style:normal;
			text-decoration:none;
			font-family:Calibri, sans-serif;
			mso-font-charset:0;
			mso-number-format:General;
			text-align:general;
			vertical-align:bottom;
			mso-background-source:auto;
			mso-pattern:auto;
			white-space:nowrap;}
		.xl6322301
			{padding-top:1px;
			padding-right:1px;
			padding-left:1px;
			mso-ignore:padding;
			color:#5A5A5A;
			font-size:18.0pt;
			font-weight:700;
			font-style:normal;
			text-decoration:none;
			font-family:Calibri, sans-serif;
			mso-font-charset:0;
			mso-number-format:General;
			text-align:general;
			vertical-align:bottom;
			mso-background-source:auto;
			mso-pattern:auto;
			white-space:nowrap;}
		.xl6422301
			{padding-top:1px;
			padding-right:1px;
			padding-left:1px;
			mso-ignore:padding;
			color:#5A5A5A;
			font-size:11.0pt;
			font-weight:400;
			font-style:normal;
			text-decoration:none;
			font-family:Calibri, sans-serif;
			mso-font-charset:0;
			mso-number-format:General;
			text-align:general;
			vertical-align:top;
			mso-background-source:auto;
			mso-pattern:auto;
			white-space:nowrap;}
		.xl6522301
			{padding-top:1px;
			padding-right:1px;
			padding-left:1px;
			mso-ignore:padding;
			color:black;
			font-size:11.0pt;
			font-weight:400;
			font-style:normal;
			text-decoration:none;
			font-family:Calibri, sans-serif;
			mso-font-charset:0;
			mso-number-format:General;
			text-align:center;
			vertical-align:bottom;
			border:.5pt solid windowtext;
			mso-background-source:auto;
			mso-pattern:auto;
			white-space:nowrap;}
		.xl6622301
			{padding-top:1px;
			padding-right:1px;
			padding-left:1px;
			mso-ignore:padding;
			color:black;
			font-size:11.0pt;
			font-weight:400;
			font-style:normal;
			text-decoration:none;
			font-family:Calibri, sans-serif;
			mso-font-charset:0;
			mso-number-format:General;
			text-align:left;
			vertical-align:bottom;
			border:.5pt solid windowtext;
			mso-background-source:auto;
			mso-pattern:auto;
			white-space:nowrap;}
		.xl6722301
			{padding-top:1px;
			padding-right:1px;
			padding-left:1px;
			mso-ignore:padding;
			color:white;
			font-size:11.0pt;
			font-weight:400;
			font-style:normal;
			text-decoration:none;
			font-family:Calibri, sans-serif;
			mso-font-charset:0;
			mso-number-format:General;
			text-align:center;
			vertical-align:bottom;
			border:.5pt solid windowtext;
			background:#5A5A5A;
			mso-pattern:black none;
			white-space:nowrap;}
		-->
		</style>
		</head>

		<body>
		<!--[if !excel]>&nbsp;&nbsp;<![endif]-->
		<!--The following information was generated by Microsoft Office Excel's Publish
		as Web Page wizard.-->
		<!--If the same item is republished from Excel, all information between the DIV
		tags will be replaced.-->
		<!----------------------------->
		<!--START OF OUTPUT FROM EXCEL PUBLISH AS WEB PAGE WIZARD -->
		<!----------------------------->

		<div id="Book1_22301" align=center x:publishsource="Excel">

		<table border=0 cellpadding=0 cellspacing=0 width=1093 style='border-collapse:
		 collapse;table-layout:auto;width:820pt'>
		 
		 <tr height=31 style='height:23.25pt'>
		 <?php
			if($this->content == 'EXCEL')
			{
			?>
				<td height=20 class=xl6322301 style='height:20pt;width:30pt;' nowrap>Report Agent Performance By Telesales</td>
				<td class=xl1522301></td>
			<?php
			}
			else{
			?>
				<td class=xl1522303 align="center" style='height:20pt;width:30pt;' rowspan=2 nowrap><img src="../gambar/axa_logo.png" style='width:27pt' align="middle" /></td>
				<td height=20 class=xl6322301>Report Agent Performance By Telesales</td>
			<?php
			}
		 ?>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		 </tr>
		 <tr height=20 style='height:10.0pt'>
		  <td height=20 class=xl6422301 colspan=4 style='height:20.0pt'>Interval Date :
		  17/04/2014 s/d 17/04/2014</td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		 </tr>
		 <tr height=20 style='height:15.0pt'>
		  <td height=20 class=xl1522301 style='height:15.0pt'></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		  <td class=xl1522301></td>
		 </tr>
	<?php
	}
	
	function header_report()
	{
	?>
		<tr height=20 style='height:15.0pt'>
		  <td height=20 class=xl6722301 style='height:15.0pt'>No</td>
		  <td class=xl6722301 style='border-left:none'>Nama</td>
		  <td class=xl6722301 style='border-left:none'>Header 1</td>
		  <td class=xl6722301 style='border-left:none'>Header 2</td>
		  <td class=xl6722301 style='border-left:none'>Header 3</td>
		  <td class=xl6722301 style='border-left:none'>Header 4</td>
		  <td class=xl6722301 style='border-left:none'>Header 5</td>
		  <td class=xl6722301 style='border-left:none'>Header 6</td>
		  <td class=xl6722301 style='border-left:none'>Header 7</td>
		  <td class=xl6722301 style='border-left:none'>Header 8</td>
		  <td class=xl6722301 style='border-left:none'>Header 9</td>
		  <td class=xl6722301 style='border-left:none'>Header 10</td>
		 </tr>
	<?php
	}
	
	function footer_excel()
	{
	?>
		<![if supportMisalignedColumns]>
		 <tr height=0 style='display:none'>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td></td>
		 </tr>
		 <![endif]>
		</table>

		</div>


		<!----------------------------->
		<!--END OF OUTPUT FROM EXCEL PUBLISH AS WEB PAGE WIZARD-->
		<!----------------------------->
		</body>

		</html>
	<?php
	}
}
?>