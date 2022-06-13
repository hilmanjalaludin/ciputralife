<?php
error_reporting(1);

include("../sisipan/sessions.php");
include("../fungsi/global.php");
include("../class/MYSQLConnect.php");
include("../class/class.application.php");
include("../class/lib.form.php");
include('../sisipan/parameters.php');
include("../class/class_export_excel.php");

define('level_user_admin',1);
define('level_user_manager',2);
define('level_user_spv',3);
define('level_user_agent',4);
define('user_state_active',1);
define('user_state_unactive',0);

/** define status on cigna level **/

define('CONTACT_STATUS','6,11,14,15,16,17,18,19,20,21,22,23,24,25,27,28,29,30,31,32,33,34,49,50,54,55,56,57,58,59,60,62,63,64,65,66,70,71,72,73,74,75');
define('CONNECT_STATUS','14,15,16,17,18,19,20,21,22,23,24,25,27,28,29,30,31,32,33,34,49,50,54,55,56,57,58,59,60,62,63,64,65,66,70,71,72,73,74,75');
define('COMPLETE_STATUS','6,8,9,11,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,62,63,64,65,66,67,68,69,70,71,72');
define('NOTCOMPLETE_STATUS','1,2,3,4,5,6,7,8,9,10,11,12,14,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60');
define('NOTINTEREST_STATUS','17,18,19,20,21,22,23,24,25,34,71,75'); 
define('NOPICKUP_STATUS','1,3,4,7,9,47,48');
define('CALLBACK_STATUS','10,12,42,49,50'); 
define('INTEREST_STATUS','15,16');
define('MISSCUSTOMER_STATUS','10');
define('ANSWERMACHINE_STATUS','7');
define('ABANDONE_STATUS','0');
define('SIT_STATUS','0');

class IndexExcel extends mysql
{
	var $TM_List;
	var $QA_List;
	var $start_date;
	var $end_date;
	var $ReportType;
	var $mode;
	var $action;
	
	function IndexExcel()
	{
		parent::__construct();
		$this -> setLabel();
		$this -> content();
	}
		
	function getStatus($status='',$agent='',$start_date)
	{
		$datas = array();
		
		$sql = "SELECT sum(unix_timestamp(a.end_time)-unix_timestamp(a.start_time)) duration
						FROM cc_agent_activity_log a 
						WHERE agent ='$agent'
						AND date(a.start_time) >= '$start_date'
						AND date(a.start_time) <= '$start_date'";
		
		switch($status)
		{
			case 0://logout
				$where = "AND a.status = $status";
				break;
				
			case 1://ready
				$where = "AND a.status = '$status' AND reason='1'";
				break;
				
			case 2://not ready(AUX)
				$where = "AND a.status = '$status'";
				break;
			
			case 3://acw
				$where = "AND a.status > 0";
				break;
				
			case 4://busy
				$where = "AND a.status = $status";
				break;
				
			default:
				$where = "";
				break;
		}
		$sql.=$where;	
		$qry = $this ->query($sql);
		
		echo "<pre>";
		print_r($sql);
		echo "</pre>";
		
		if( $qry->result_num_rows() > 0 )
		{
		   foreach( $qry -> result_assoc() as $rows )
		   {
			 $datas[$rows['duration']] = $rows; 
		   }	
		}
		
		return $datas;
	}
	
/** ****************/
	
	protected function getCcAgentId($tms_agent_id)
	{
		$sql=" SELECT b.id as AgentId, a.id as Username, a.full_name as Fullname, a.spv_id as leader 
			   FROM tms_agent a left join cc_agent b on a.id=b.userid
			   WHERE a.UserId='$tms_agent_id' AND a.user_state='".user_state_active."' ";
		$qry = $this -> query($sql);
		if( $qry->result_num_rows() > 0 )
		{
		   return  $qry -> result_first_assoc();	
		}
	} 
	
/** ****************/
	
	protected function getSpvId($test='')
	{
		$datas = array();
		
		$sql=" select 
				b.UserId, b.id as username, b.full_name as fullname,
				a.name as UserLevel 
			from tms_agent_profile a 
			left join tms_agent b on a.id=b.profile_id
			where a.id='".level_user_spv."' AND b.user_state='".user_state_active."' ";
		
		$qry = $this -> query($sql);
		if( $qry->result_num_rows() > 0 )
		{
		   foreach( $qry -> result_assoc() as $rows )
		   {
			 $datas[$rows['UserId']] = $rows; 
		   }	
		}
		
		return $datas;
	} 
	
/** get agent by spv id **/

	protected function getAgentBySpvId($SpvId=0)
	{
		$sql =" select a.id as cc_id, b.id as username, b.full_name 
				from cc_agent a 
				left join tms_agent b on a.userid=b.id where b.user_state='".user_state_active."'
				and b.spv_id='$SpvId' 
				and b.profile_id='".level_user_agent."' ";
		
		$qry = $this -> query($sql);
		if( $qry->result_num_rows() > 0 )
		{
		   foreach( $qry -> result_assoc() as $rows )
		   {
			 $datas[$rows['cc_id']] = $rows; 
		   }	
		}
		
		return $datas;
	}
	

/** ambil post ****/
	
	protected function getParameterAgent()
	{
		if( $this ->havepost('list_user_tm') )
		{
			$result_agent = explode(',',$_REQUEST['list_user_tm']);
			
			if( count($result_agent) >0 )
			{
				return $result_agent;
			}
		}
	}

/** ambil post ****/	

	function content()
	{
		
		if( $this ->havepost('report_type') )
		{
			$new_name_file = $_REQUEST['report_type'];
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
 private function setLabel()
	{
		$report_type = array
		(
			'score_report'				=> 'Call Monitoring (Score) Reports'
		);
		
		$start_date_callmon  = str_replace("-","/",$this -> escPost('start_callmon'));
		$end_date_callmon  	 = str_replace("-","/",$this -> escPost('end_callmon'));
		$start_date_sell  	 = str_replace("-","/",$this -> escPost('start_sale'));
		$end_date_sell  	 = str_replace("-","/",$this -> escPost('end_sale'));
		$group_by    		 = ucfirst($this -> escPost('group_by')); 
		$labelReport  		 = $report_type[$this -> escPost('report_type')];
		//$ModeReport  		 = ucfirst($this -> escPost('mode'));
		$today		 		 = date("d/m/Y");
		
		echo "<div class=\"label_header\" style=\"margin-bottom:5px;padding-top:5px;padding-bottom:5px;border-bottom:1px solid #eee;width:'100%';\">
				<span class='top'>{$labelReport}</span><br/>".
				($this -> havepost('group_by')?" ":"")
				."
				<span class='middle'>Call Monitoring Date : {$start_date_callmon} - {$end_date_callmon}  </span> | 
				<span class='middle'>Selling Date : {$start_date_sell} - {$end_date_sell}  </span><br/>
				<span class='bottom'>Report Date :  $today </span>
				</div>\n\r";//<span class='middle'>Mode : {$ModeReport}</span><br/>
	}
	
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
new IndexExcel();
// END OF FILE
?>