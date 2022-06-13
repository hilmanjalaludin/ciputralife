<?php

include(dirname(__FILE__).'/../sisipan/sessions.php');
include(dirname(__FILE__).'/../fungsi/global.php');
include(dirname(__FILE__).'/../class/MYSQLConnect.php');
include(dirname(__FILE__).'/../class/class.application.php');
include(dirname(__FILE__).'/../class/lib.form.php');
include(dirname(__FILE__).'/../sisipan/parameters.php');

define('level_user_admin',1);
define('level_user_manager',2);
define('level_user_spv',3);
define('level_user_agent',4);
define('user_state_active',1);
define('user_state_unactive',0);

class index extends mysql
{
	var $TM_List;
	var $QA_List;
	var $start_date;
	var $end_date;
	var $start_date_callmon;
	var $end_date_callmon;
	var $start_date_sell;
	var $end_date_sell;
	var $ReportType;
	var $mode;
	var $group;
	var $action;
	
	function index()
	{
		parent::__construct();
		$this -> header();	
		$this -> setLabel();
		$this -> content();
		$this -> footer();
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
		
		$sql.= $where;	
		$qry = $this ->query($sql);
		
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
	
	function CampaignName($CampaignId){
		$sql = " SELECT a.CampaignName
				FROM t_gn_campaign a
				WHERE a.CampaignId IN ('$CampaignId') AND a.CampaignStatusFlag=1 ";
		$qry = $this -> query($sql);
		if( $qry -> EOF() ) return null;
		else{
			return $qry ->result_get_value('CampaignName');
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
	
/** **/
	
	function styleCss(){ ?>
		<style>
			table.grid{}
			td.header { background-color:#2182bf;font-family:Arial;font-weight:bold;color:#f1f5f8;font-size:12px;padding:5px;} 
			td.sub { background-color:#eeeeee;font-family:Arial;font-weight:bold;color:#000000;font-size:12px;padding:5px;} 
			td.subtot { background-color:#ef9b9b;font-family:Arial;font-weight:bold;color:#000000;font-size:12px;padding:5px;} 
			td.content { padding:2px;height:24px;font-family:Arial;font-weight:normal;color:#456376;font-size:12px;background-color:#f9fbfd;} 
			td.first {border-left:1px solid #dddddd;border-top:1px solid #dddddd;border-bottom:0px solid #dddddd;}
			td.middle {border-left:1px solid #dddddd;border-bottom:0px solid #dddddd;border-top:1px solid #dddddd;}
			td.lasted {border-left:1px solid #dddddd; border-bottom:0px solid #dddddd; border-right:1px solid #dddddd; border-top:1px solid #dddddd;}
			td.agent{font-family:Arial;font-weight:normal;font-size:12px;padding-top:5px;padding-bottom:5px;border-left:0px solid #dddddd; 
					border-bottom:0px solid #dddddd; border-right:0px solid #dddddd; border-top:0px solid #dddddd;
					background-color:#fcfeff;padding-left:2px;color:#06456d;font-weight:bold;}
			h1.agent{font-style:inherit; font-family:Trebuchet MS;color:blue;font-size:14px;color:#2182bf;}
			
			td.total{
						padding:2px;font-family:Arial;font-weight:normal;font-size:12px;padding-top:5px;padding-bottom:5px;border-left:0px solid #dddddd; 
					border-bottom:1px solid #dddddd; border-top:1px solid #dddddd;  
					border-right:1px solid #dddddd; border-top:1px solid #dddddd;
					background-color:#2182bf;padding-left:2px;color:#f1f5f8;font-weight:bold;}
			span.top{color:#306407;font-family:Trebuchet MS;font-size:28px;line-height:40px;}
			span.middle{color:#306407;font-family:Trebuchet MS;font-size:14px;line-height:18px;}
			span.bottom{color:#306407;font-family:Trebuchet MS;font-size:12px;line-height:18px;}
			td.subtotal{ font-family:Arial;font-weight:bold;color:#3c8a08;height:30px;background-color:#FFFCCC;}
			td.tanggal{ font-weight:bold;color:#FF4321;height:22px;background-color:#FFFFFF;height:30px;}
			h3{color:#306407;font-family:Trebuchet MS;font-size:14px;}
			h4{color:#FF4321;font-family:Trebuchet MS;font-size:14px;}
		</style>	
			
	<?php }
	
	function content()
	{
		if( $this ->havepost('report_type') )
		{
			$new_name_file = $_REQUEST['report_type'];
			if( !empty($new_name_file))
			{
				include(dirname(__FILE__).'/HTML/'.$new_name_file.'.php');
				$object = new $new_name_file();
				switch($_REQUEST['content'])
				{
					default : $object -> show_content_html();  break;
					case 'HTML' : $object -> show_content_html();  break;
				}
			}
		}
	}
	
/** set label html **/
	
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
	
	
	function header()
	{
		global $Themes;
		
		echo "
			<html>
				<head>
					<title>{$Themes -> V_WEB_TITLE} - Reporting </title>
					<meta http-equiv=Content-Type content=\"text/html; charset=windows-1252\">
					<meta name=ProgId content=\"Excel.Sheet\">
					<meta name=Generator content=\"Microsoft Excel 12\">\n\r";
			
			// cs : css
			$this -> styleCss();	
			// ce : css

		echo "
				</head>
				<body>\n\r";	
			
	}
	
	
	function footer()
	{
		echo "</body>
				</html>";
	}
	
}	
new index();
?>