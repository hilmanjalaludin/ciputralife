<?php
error_reporting(1);

include("../sisipan/sessions.php");
include("../fungsi/global.php");
include("../class/MYSQLConnect.php");
include("../class/class.application.php");
include("../class/lib.form.php");
include('../sisipan/parameters.php');
// include("../class/class_export_excel.php");

class IndexExcel extends mysql
{
	var $start_date;
	var $end_date;
	
	function IndexExcel()
	{
		parent::__construct();
		$this -> content();
	}
	
	/** ambil post ****/	

	function content()
	{
		
		if( $this ->havepost('report_type') )
		{
			$new_name_file = $_REQUEST['report_type'];
			echo $new_name_file;
			if( !empty($new_name_file))
			{
				include(dirname(__FILE__).'/EXCEL/'.$new_name_file.'.php');
				$object = new $new_name_file();
				switch($_REQUEST['content'])
				{
					default : $object -> show_content_excel();  break;
					case 'Excel' : $object -> show_content_excel();  break;
				}
				self::_Excel_EOF();
			}
		}
	}
	
	/** 
 ** create _Excel execute to download file of 
 ** pages heder position 
 ** save on your constructtor
 **/
 
 public function _Excel($_header_names = null )
 {
	if( $_header_names !=NULL )
	{
		$xlsName = $_header_names.'_'.date('Ymd').'_'.date('His').'.xls';
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Cache-Control: private");
		header("Pragma: no-cache");
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=$xlsName");		
	}
 }
 
 /** create Header _excel 
 ** heder position 
 ** save on your constructtor
 **/
 
 public function _Excel_Header()
 { ?>
		<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" 
			xmlns="http://www.w3.org/TR/REC-html40">
			<head>
			<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
			<meta name=ProgId content=Excel.Sheet>
			<meta name=Generator content="Microsoft Excel 12">
			<style id="report_1382947428_2508_Styles">
			<!--table
				{mso-displayed-decimal-separator:"\.";
				mso-displayed-thousand-separator:"\,";}
			.xh3{ text-align:left;}	
			.xl152508
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
			.xl582508
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
				mso-number-format:"\@";
				text-align:general;
				vertical-align:middle;
				border:.5pt solid #A5A5A5;
				background:#F2F2F2;
				mso-pattern:black none;
				white-space:nowrap;}
			.xl582509
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
				mso-number-format:"\@";
				text-align:general;
				vertical-align:middle;
				border:.5pt solid #A5A5A5;
				background:#eeeeee;
				mso-pattern:black none;
				white-space:nowrap;}
			.xl592508
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
			.xl602508
				{padding-top:1px;
				padding-right:1px;
				padding-left:1px;
				mso-ignore:padding;
				color:black;
				font-size:11.0pt;
				font-weight:700;
				font-style:normal;
				text-decoration:none;
				font-family:Calibri, sans-serif;
				mso-font-charset:0;
				mso-number-format:"\@";
				text-align:general;
				vertical-align:middle;
				border:.5pt solid #A5A5A5;
				background:#538ED5;
				mso-pattern:black none;
				white-space:nowrap;}
			.xl602509
				{padding-top:1px;
				padding-right:1px;
				padding-left:1px;
				mso-ignore:padding;
				color:black;
				font-size:11.0pt;
				font-weight:700;
				font-style:normal;
				text-decoration:none;
				font-family:Calibri, sans-serif;
				mso-font-charset:0;
				mso-number-format:"\@";
				text-align:general;
				vertical-align:middle;
				border:.5pt solid #A5A5A5;
				background:#eeeeee;
				mso-pattern:black none;
				white-space:nowrap;}
			.xl612508
				{padding-top:1px;
				padding-right:1px;
				padding-left:1px;
				mso-ignore:padding;
				color:#17375D;
				font-size:11.0pt;
				font-weight:700;
				font-style:normal;
				text-decoration:underline;
				text-underline-style:single;
				font-family:Calibri, sans-serif;
				mso-font-charset:0;
				mso-number-format:General;
				text-align:left;
				vertical-align:middle;
				border-top:none;
				border-right:none;
				border-bottom:.5pt solid #A5A5A5;
				border-left:none;
				mso-background-source:auto;
				mso-pattern:auto;
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
			<div id="REGISTER_REPORT_1382947428_2508" align=center x:publishsource="Excel">
		<?php
	}
	
	
/** 
 ** create footer 
 ** return < void >
 **/	
 
protected function _Excel_Footer()
{
 ?>
	<![if supportMisalignedColumns]>
	<tr height=0 style='display:none'>
		<td width=101 style='width:76pt'></td>
		<td width=150 style='width:113pt'></td>
		<td width=108 style='width:81pt'></td>
		<td width=98 style='width:74pt'></td>
		<td width=53 style='width:40pt'></td>
		<td width=189 style='width:142pt'></td>
		<td width=162 style='width:122pt'></td>
		<td width=162 style='width:122pt'></td>
		<td width=162 style='width:122pt'></td>
		<td width=92 style='width:69pt'></td>
		<td width=126 style='width:95pt'></td>
		<td width=130 style='width:98pt'></td>
		<td width=116 style='width:87pt'></td>
		<td width=111 style='width:83pt'></td>
		<td width=101 style='width:76pt'></td>
		<td width=96 style='width:72pt'></td>
		</tr>
	<![endif]>
	</table>
	
<?php }

/** 
 ** create footer EOF < end of html tag >
 ** return < void > then render download excel data
 **/	
 
protected function _Excel_EOF(){ ?>
	</div>
	<!----------------------------->
	<!--END OF OUTPUT FROM EXCEL PUBLISH AS WEB PAGE WIZARD-->
	<!----------------------------->
	</body>
	</html>
<?php }
}
?>