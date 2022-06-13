<!-- SOF --> 

<?php
define('sale','15,16'); //sale

class cmp_review extends index
{
	var $_con; // not definition
	var $_mod; // mode report 
	var $_grp; // group mode
	var $_cbl; // calback status
	var $_ctc; // contact status
	var $_not; //
	
/**
 ** report available only summary report group by HTML Telesales & HTML supervisor
 ** for available other report please open remark and then crate content 
 ** under spesific function to generate
 **/

 
/**
 ** @ aksesor of class 
 ** @ handle return <void>
 */
 
 function cmp_review()
 {
	$this -> _con  = null;
	$this -> _mod  = $this -> escPost('mode');
	$this -> _grp  = $this -> escPost('group_by');
	$this -> _cbl  = $this -> _with_code(self::_calback_status());
	$this -> _ctc  = $this -> _with_code(self::_contact_status());
	$this -> _not  = $this -> _with_code(self::_not_int_status());
	
 }
 
/**
 ** @ aksesor of class 
 ** @ handle return <void>
 */
 function _sizeByCallreason($CampaignId=0, $_within)
 {
	$start_date = $this -> getStartDate();
	$end_date = $this -> getEndDate();
	
	$_conds = array();
	$sql = " SELECT COUNT(DISTINCT a.CustomerId) AS cnt ,
			 WEEK(date(a.CallHistoryCreatedTs)) as tgl	
			 FROM t_gn_callhistory a
			 LEFT JOIN t_gn_customer b ON a.CustomerId=b.CustomerId
			 LEFT JOIN tms_agent d ON a.CreatedById=d.UserId
			 WHERE b.CampaignId='$CampaignId' 
			 AND DATE(a.CallHistoryCallDate)>='$start_date' 
			 AND DATE(a.CallHistoryCallDate)<='$end_date' 
			 AND DATE(a.CallHistoryCreatedTs)>='$start_date' 
			 AND DATE(a.CallHistoryCreatedTs)<='$end_date'
			 AND d.handling_type='".level_user_agent."'
			 AND a.CallReasonId IN($_within) 
			 GROUP BY tgl";
	$qry = $this -> query($sql);	
	foreach($qry -> result_assoc() as $rows ){
		$_conds[$rows['tgl']] = $rows['cnt'];
	}
	
	return $_conds;
}		


/**
 ** @ aksesor of class 
 ** @ handle return <void>
 */
 
 function _sizeDailyCallreason($CampaignId=0, $start_date=NULL, $end_date=NULL, $_within)
 {
	$_conds = array();
	$sql = " SELECT COUNT(DISTINCT a.CustomerId) AS cnt ,
			 DATE(a.CallHistoryCreatedTs) as tgl	
			 FROM t_gn_callhistory a
			 LEFT JOIN t_gn_customer b ON a.CustomerId=b.CustomerId
			 LEFT JOIN tms_agent d ON a.CreatedById=d.UserId
			 WHERE b.CampaignId='$CampaignId' 
			 AND DATE(a.CallHistoryCallDate)>='$start_date' 
			 AND DATE(a.CallHistoryCallDate)<='$end_date' 
			 AND DATE(a.CallHistoryCreatedTs)>='$start_date' 
			 AND DATE(a.CallHistoryCreatedTs)<='$end_date'
			 AND d.handling_type='".level_user_agent."'
			 AND a.CallReasonId IN($_within) 
			 GROUP BY tgl";
	$qry = $this -> query($sql);	
	foreach($qry -> result_assoc() as $rows ){
		$_conds[$rows['tgl']] = $rows['cnt'];
	}
	
	return $_conds;
}		

 /** %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% **********/
 
 function _Weekly_List()
 {
	$_Weekly_List = array();
	$start_date = $this -> getStartDate();
	$end_date = $this -> getEndDate();
	
	if( $start_date )
	{
		$sql = " SELECT week(a.CallHistoryCreatedTs) as Weekly 
					FROM  t_gn_callhistory a 
					WHERE  a.CallHistoryCreatedTs  
					BETWEEN date('$start_date') 
					AND date('$end_date') 
					group by Weekly ";
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows )
		{
			$_Weekly_List[$rows['Weekly']] = $rows['Weekly'];
			$key++;
		}	
	}
	
	return $_Weekly_List;
 }
 
/**
 ** @ aksesor of class 
 ** @ handle return <void>
 */

private function __week_days($date='')
{
	$sql = "select WEEKDAY('$date') as jumlah";
	$qry = $this -> query($sql);
	$wek = $qry -> result_get_value('jumlah');
	return ($wek==6?true:false);
	
} 
/**
 ** @ aksesor of class 
 ** @ handle return <void>
 */

 private function _with_code( $_code=array() )
 {
	return implode(",",$_code );
 }
 
/**
 ** @ aksesor of class 
 ** @ handle return <void>
 */
 
 function _not_int_status()
 {
	$_clbk  = array();
	$sql = " SELECT a.CallReasonId FROM t_lk_callreason a where a.CallReasonCategoryId=5 
			 AND a.CallReasonStatusFlag=1 ";
			 
	$qry = $this -> query($sql);
	foreach( $qry -> result_assoc() as $rows ){
		$_clbk[$rows['CallReasonId']] = $rows['CallReasonId'];
	}	
	return $_clbk;
}
  
/**
 ** @ aksesor of class 
 ** @ handle return <void>
 */
 
 function _calback_status()
 {
	$_clbk  = array();
	$sql = " select a.CallReasonId from t_lk_callreason a where a.CallReasonLater=1
			and a.CallReasonStatusFlag=1 ";
	$qry = $this -> query($sql);
	foreach( $qry -> result_assoc() as $rows ){
		$_clbk[$rows['CallReasonId']] = $rows['CallReasonId'];
	}	
	return $_clbk;
}
 
/**
 ** @ aksesor of class 
 ** @ handle return <void>
 */
 
 function _contact_status()
 {
	$_ctc  = array();
	$sql = " select a.CallReasonId from t_lk_callreason a where a.CallReasonContactedFlag=1
			 and a.CallReasonStatusFlag=1 ";
	$qry = $this -> query($sql);
	foreach( $qry -> result_assoc() as $rows  ){
		$_ctc[$rows['CallReasonId']] = $rows['CallReasonId'];
	}	
	return $_ctc;
 }
 
 /**
 ** get group select on navigation report
 ** return < obejct:Class >
 */
 
 function _Duration( $seconds=0 )
	{
		$sec = 0;
		$min = 0;
		$hour= 0;
		$sec = $seconds%60;
		$seconds = floor($seconds/60);
		
		if ($seconds){
			$min  = $seconds%60;
			$hour = floor($seconds/60);
		}
		
		if($seconds == 0 && $sec == 0)
			return sprintf("");
		else
			return sprintf("%01d.%02d", $hour, $min);
	}
	
/**
 ** get group select on navigation report
 ** return < obejct:Class >
 */
 
 private function getGroupSelect(){
		$Spvid = $this -> escPost('group_select');
		if($Spvid!=''){
			return $this -> Users -> getUsers($Spvid);
		}
	}

 private function getCampaignName(){
		$CmpId = $this -> escPost('campaign_name');
		if($CmpId!=''){
			return $this -> Users -> getUsers($CmpId);
		}
	}	
	
/**
 ** get second group select on navigation report
 ** return < array >
 */
 
 
 private function getTelesales()
	{
		$Telesales = explode(',',$_REQUEST['Telesales']);
		if( is_array($Telesales) ) return $Telesales;
	}
	
/**
 ** get start date interval 
 ** return < string >
 */
 	
 private function getStartDate()
 {
		return $start_date = $this -> formatDateEng($this -> escPost('start_date')); 
	}
	
/**
 ** get end date interval 
 ** return < string >
 */
 
 private function getEndDate(){
		return $end_date   = $this -> formatDateEng($this -> escPost('end_date'));
	}
	
/** 
 ** main content HTML group report 
 ** return < void >
 **/
	
 public function show_content_html()
	{
		mysql::__construct();	
		switch( $this -> _grp )
		{
			//case 'Telesales'  : $this -> PerfomanceByTelesales(); break; 
			//case 'supervisor' : $this -> PerfomanceBySupervisor(); break; 
			case 'campaign'   : $this -> ReviewByCampaign(); break; 
			default:
				echo "<h3>Sorry, You must filtering by Campaign!</h3>";
			break;
		}
	}

/** 
 ** main content HTML PerfomanceBySupervisor 
 ** return < void >
 **/

 
private function ReviewByCampaign()
{
	switch( $this -> _mod )
	{
		case 'weekly'  : $this -> _weeklyReviewByCampaign(); break; 
		case 'daily'   : $this -> _dailyReviewByCampaign(); break; 
		case 'summary' : $this -> _summaryReviewByCampaign(); break; 
		default: 
			echo "<h3>Sorry, You must filtering by Campaign!</h3>";
		break;
	}
}
	
/** 
 ** main content HTML PerfomanceBySupervisor 
 ** return < void >
 **/
	
 private function PerfomanceBySupervisor()
	{
		
		switch( $this -> _mod )
		{
			//case 'hourly'  : $this -> hourlyPerfomanceByTelesales(); break; 
			//case 'daily'   : $this -> dailyPerfomanceBySupervisor(); break; 
			//case 'summary' : $this -> summaryPerfomanceBySupervisor(); break; 
			
			default:
				echo "<h3>Sorry, You must filtering by Campaign!</h3>";
			break;
		}
	}	

/** 
 ** main content HTML PerfomanceByTelesales 
 ** return < void >
 **/
 
 private function PerfomanceByTelesales()
	{
		switch( $this -> _mod )
		{
			//case 'hourly'  : $this -> hourlyPerfomanceByTelesales(); break; 
			//case 'daily'   : $this -> dailyPerfomanceBySupervisor(); break; 
			//case 'summary' : $this -> summaryPerfomanceByTelesales(); break; 
			
			default:
				echo "<h3>Sorry, You must filtering by Campaign!</h3>";
			break;
		}
	}	

	
/** factory Model 
 ** get all campaign by send POST
 ** paramter
 **/
 
function _query_campaign($_CampaignId)
{
	$sql = " SELECT a.CampaignNumber, a.CampaignName FROM t_gn_campaign a WHERE a.CampaignId ='$_CampaignId'";
	$qry = $this -> query($sql);
	if( !$qry -> EOF() )
	{
		return $qry; 
	}
}	
	
	
/**
 ** get start date interval 
 ** return < string >
 */

private function _CampaignName()
{
	$_cmp = explode(',',$_REQUEST['CampaignName']);
	return $_cmp;
} 

/**
 ** _summaryReviewByCampaign get data from t_gn_customer
 ** with interval ( customerupdates ) with status
 ** break every Weekly 
**/	

function  _summaryReviewByCampaign()
{
	$start_date = $this -> getStartDate();
	$end_date = $this -> getEndDate();
	/**
     ** content header data 
     ** return data <size >
	 **/	
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\">
				<tr>
					<td rowspan=\"2\" nowrap class=\"header first\" align=\"left\">Campaign Name</td>
					<td colspan=\"29\" nowrap class=\"header first\" align=\"center\">Campaign Review</td>
				</tr>
				<tr>
					<td nowrap class=\"header first\" align=\"center\">Database</td>
					<td nowrap class=\"header middle\" align=\"center\">Solicited</td>
					<td nowrap class=\"header middle\" align=\"center\">Solicited Rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Attempt</td>
					<td nowrap class=\"header middle\" align=\"center\">Attempt Ratio</td>
					<td nowrap class=\"header middle\" align=\"center\">Connect</td>
					<td nowrap class=\"header middle\" align=\"center\">Connect rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Contact</td>
					<td nowrap class=\"header middle\" align=\"center\">Contact Rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Completes</td>
					<td nowrap class=\"header middle\" align=\"center\">Not Completes</td>
					<td nowrap class=\"header middle\" align=\"center\">Answer Machines</td>
					<td nowrap class=\"header middle\" align=\"center\">Answer Machines %</td>
					<td nowrap class=\"header middle\" align=\"center\">NPU</td>
					<td nowrap class=\"header middle\" align=\"center\">NPU %</td>
					<td nowrap class=\"header middle\" align=\"center\">Abandons</td>
					<td nowrap class=\"header middle\" align=\"center\">Abandons %</td>
					<td nowrap class=\"header middle\" align=\"center\">Callbacks</td>
					<td nowrap class=\"header middle\" align=\"center\">Callbacks Left %</td>
					<td nowrap class=\"header middle\" align=\"center\">Miss Customers</td>
					<td nowrap class=\"header middle\" align=\"center\">Miss Customers %</td>
					<td nowrap class=\"header middle\" align=\"center\">SIT</td>
					<td nowrap class=\"header middle\" align=\"center\">SIT Rate</td>
					<td nowrap class=\"header middle\" align=\"center\">Agents</td>
					<td nowrap class=\"header middle\" align=\"center\">Average<br>Call Length<br> - Interest</td>
					<td nowrap class=\"header middle\" align=\"center\">Average<br>Call Length<br> - Not Interest</td>
					<td nowrap class=\"header middle\" align=\"center\">Average<br>Call Length<br> - Callback</td>
					<td nowrap class=\"header middle\" align=\"center\">Average<br>Call Length<br> - Answer Machines</td>
					<td nowrap class=\"header lasted\" align=\"center\">Average<br>Call Length<br> - Wait</td>
				</tr>";
				
	/**
     ** get atempt call data **
     ** return data <size >
	 **/
	 
		$DataSize = array();
		$size_atempt = array();
		$CallIniated = array();
		$Complete = array();
		$NotComplete = array();
		$NoPickUp = array();
		$AnswerMachine = array();
		$CallBack = array();
		$Misscustomers = array();
		$Agents = array();
		$SIT = array();
		$Abadone = array();
		$totSolicited = array();
		$Interest_time = array();
		$Machine_time = array();
		$NotInterest_time = array();
		$Callback_time = array();
		$Interest = array();
		$NotInterest = array(); 
		
	
	/** 
	 ** get summarry allocation database on campaign
	 ** with spesifid campaign  
     **/	 
		$sql = " SELECT 
				 COUNT(a.CustomerId) AS tots, a.CampaignId AS idx, 
				 SUM(IF((a.CallReasonId IS NOT NULL AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) AS Utilize,
				 SUM(IF((a.CallReasonId IN(".ANSWERMACHINE_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date') ,1,0)) AS AnswerMachine,
				 SUM(IF((a.CallReasonId IN(".MISSCUSTOMER_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) AS Misscustomers,
				 SUM(IF((a.CallReasonId IN(".NOPICKUP_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) AS NoPickUp,
				 SUM(IF((a.CallReasonId IN(".CALLBACK_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) AS CallBack,
				 SUM(IF((a.CallReasonId IN(".NOTINTEREST_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) AS NotInterest,
				 SUM(IF((a.CallReasonId IN(".INTEREST_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) as Interest,
				 SUM(IF((a.CallReasonId IN(".CONNECT_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) as CallIniated,
				 SUM(IF((a.CallReasonId IN(".NOTCOMPLETE_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) as NotComplete,
				 SUM(IF((a.CallReasonId IN(".CONTACT_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) as SizeContact,
				 SUM(IF((a.CallReasonId IN(".COMPLETE_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) as Complete
			    FROM t_gn_customer a  
				WHERE a.CampaignId IN(".self::_with_code(self::_CampaignName()).")
				GROUP BY idx";
		
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$DataSize[$rows['idx']]= $rows['tots'];
			$totSolicited[$rows['idx']] += $rows['Utilize'];
			$NoPickUp[$rows['idx']]= $rows['NoPickUp']; 
			$CallIniated[$rows['idx']]= $rows['CallIniated']; 
			$SizeContact[$rows['idx']]= $rows['SizeContact']; 
			$CallBack[$rows['idx']]= $rows['CallBack']; 
			$AnswerMachine[$rows['idx']]= $rows['AnswerMachine']; 
			$Misscustomers[$rows['idx']]= $rows['Misscustomers'];  
			$NotComplete[$rows['idx']]= $rows['NotComplete']; 
			$Complete[$rows['idx']]= $rows['Complete']; 
			$Interest[$rows['idx']]= $rows['Interest'];
			$NotInterest[$rows['idx']]= $rows['NotInterest'];
		}
		
	
	/** 
	 ** query , Utilize, 
	 ** agents,  Atempt,  Abadone 
	 **/
	 
		$sql = "SELECT  COUNT(distinct a.CustomerId) as Utilize ,  COUNT(distinct a.CreatedById) as agents,
				COUNT(a.CustomerId) as Atempt, '0' as SIT, '0' as Abadone, 
				b.CampaignId as IDX
				FROM t_gn_callhistory a
				LEFT JOIN t_gn_customer b on a.CustomerId=b.CustomerId
				WHERE DATE(a.CallHistoryCallDate)>='$start_date' AND DATE(a.CallHistoryCallDate)<='$end_date'
				AND date(a.CallHistoryCreatedTs)>='$start_date' AND date(a.CallHistoryCreatedTs)<='$end_date'
				AND b.CampaignId IN(".self::_with_code(self::_CampaignName()).")
				GROUP BY IDX ";
		
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			
			$size_atempt[$rows['IDX']] += $rows['Atempt'];
			$Agents[$rows['IDX']] += $rows['agents'];
			$SIT[$rows['IDX']] += $rows['SIT'];
			$Abadone[$rows['IDX']] += $rows['Abadone'];
			$NotInterest[$rows['IDX']]+= $rows['NotInterest'];
			$Interest[$rows['IDX']]+= $rows['Interest'];		
		}
		
	/** 
	 ** get duration for interes data 
     ** then wil avg interest  	 
	 **/
		$sql = " SELECT 
					SUM(IF((b.`status` IN(3004,3005) AND a.CallReasonId IN(".ANSWERMACHINE_STATUS.")),(UNIX_TIMESTAMP(b.end_time)-UNIX_TIMESTAMP(b.agent_time)),0 )) as machine_talktime,
					SUM(IF((b.`status` IN(3004,3005) AND a.CallReasonId IN(".INTEREST_STATUS.")),(UNIX_TIMESTAMP(b.end_time)-UNIX_TIMESTAMP(b.agent_time)),0 )) as interest_talktime,
					SUM(IF((b.`status` IN(3004,3005) AND a.CallReasonId IN(".NOTINTEREST_STATUS.") ),(UNIX_TIMESTAMP(b.end_time)-UNIX_TIMESTAMP(b.agent_time)),0)) as not_interest_talktime,
					SUM(IF((b.`status` IN(3004,3005) AND a.CallReasonId IN(".CALLBACK_STATUS.") ),(UNIX_TIMESTAMP(b.end_time)-UNIX_TIMESTAMP(b.agent_time)),0)) as callback_talktime,
					a.CampaignId as idx
				 FROM t_gn_customer a 
				 LEFT JOIN cc_call_session b on a.CustomerId=b.assign_data
				 WHERE a.CallReasonId IN(".ANSWERMACHINE_STATUS.",".INTEREST_STATUS.",".NOTINTEREST_STATUS.",".CALLBACK_STATUS.")
				 AND a.CampaignId IN(".self::_with_code(self::_CampaignName()).")
				 AND DATE(b.start_time)>='$start_date'
				 AND DATE(b.start_time)<='$end_date'
				 GROUP BY idx ";
				 
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{	
			$Interest_time[$rows['idx']] += $rows['interest_talktime'];
			$Machine_time[$rows['idx']] += $rows['machine_talktime'];
			$NotInterest_time[$rows['idx']]+= $rows['not_interest_talktime'];
			$Callback_time[$rows['idx']]+= $rows['callback_talktime'];
		}
		 
	/** while true data looping date 
	 ** by date filter 
	 */	
	 
	 $total_solicited = 0;
	 $total_atempt = 0;
	 $total_callback = 0;
	 $total_complete = 0;
	 $total_call_iniated = 0;
	 $total_contact = 0;
	 $total_machine = 0;
	 $total_nopickup = 0;
	 $total_data_size = 0;
	 $total_miss_customers = 0;
	 $total_interest =0;
	 $total_not_interest =0;
	 $total_agents = 0;
	 
	foreach( $this -> _CampaignName() as $k => $s_d ) 
	{	
		$_conts_init_names  = $this -> _query_campaign($s_d);
		$PercentUtilize 	= ROUND((($totSolicited[$s_d]/$DataSize[$s_d]) * 100),2);
		$RatioAtempt 		= ROUND(($size_atempt[$s_d]/$totSolicited[$s_d]),2);
		$RatioConnect 		= ROUND((($CallIniated[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioContact 		= ROUND((($SizeContact[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioAnswerMachine = ROUND((($AnswerMachine[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioNoPickUp 		= ROUND((($NoPickUp[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioCallBack 		= ROUND((($CallBack[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioMisscustomer  = ROUND((($Misscustomers[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioAbadone		= ROUND((($Abadone[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioSIT			= ROUND((($Interest[$s_d]/$totSolicited[$s_d])*100),2);
		$AvgInterestTime    = ROUND((self::_Duration($Interest_time[$s_d])/$Interest[$s_d]),2);
		$AvgNotInterestTime = ROUND((self::_Duration($NotInterest_time[$s_d])/$NotInterest[$s_d]),2);
		$AvgCallBackTime    = ROUND((self::_Duration($Callback_time[$s_d])/$CallBack[$s_d]),2);
		$AvgMachineTime     = ROUND((self::_Duration($Machine_time[$s_d])/$AnswerMachine[$s_d]),2);
		$AvgWaitTime     	= 0.0;
		
		
		$CampaignName = $_conts_init_names ->result_get_value('CampaignName');
		
		echo "<tr>
				 <td nowrap class=\"content first\" align=\"left\">&nbsp;".($CampaignName?$CampaignName:'')."</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($DataSize[$s_d]?$DataSize[$s_d]:0)."</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($totSolicited[$s_d]?$totSolicited[$s_d]:0)."</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($PercentUtilize?$PercentUtilize:0)." %</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($size_atempt[$s_d]?$size_atempt[$s_d]:0)."</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($RatioAtempt?$RatioAtempt:0)."</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($CallIniated[$s_d]?$CallIniated[$s_d]:0)."</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($RatioConnect?$RatioConnect:0)."  %</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($SizeContact[$s_d]?$SizeContact[$s_d]:0)."</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($RatioContact?$RatioContact:0)."  %</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($Complete[$s_d]?$Complete[$s_d]:0)."</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($NotComplete[$s_d]?$NotComplete[$s_d]:0)."</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($AnswerMachine[$s_d]?$AnswerMachine[$s_d]:0)."</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($RatioAnswerMachine?$RatioAnswerMachine:0)."  %</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($NoPickUp[$s_d]?$NoPickUp[$s_d]:0)."</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($RatioNoPickUp?$RatioNoPickUp:0)." %</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($Abadone[$s_d]?$Abadone[$s_d]:0)."</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($RationAbadone?$RationAbadone:0)."</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($CallBack[$s_d]?$CallBack[$s_d]:0)."</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($RatioCallBack?$RatioCallBack:0)." %</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($Misscustomers[$s_d]?$Misscustomers[$s_d]:0)."</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($RatioMisscustomer?$RatioMisscustomer:0)." %</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($Interest[$s_d]?$Interest[$s_d]:0)."</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($RatioSIT?$RatioSIT:0)." %</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($Agents[$s_d]?$Agents[$s_d]:0)."</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($AvgInterestTime?$AvgInterestTime:0)."</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($AvgNotInterestTime?$AvgNotInterestTime:0)."</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($AvgCallBackTime?$AvgCallBackTime:0)."</td>
				 <td nowrap class=\"content middle\" align=\"right\">".($AvgMachineTime?$AvgMachineTime:0)."</td>
				<td nowrap class=\"content lasted\" align=\"right\">".($AvgWaitTime?$AvgWaitTime:0)."</td>
			 </tr>";

			/** 
			 ** calculation footer 
			 ** for next step 
			 **/
					 
			$total_solicited += $totSolicited[$s_d];
			$total_atempt += $size_atempt[$s_d];
			$total_callback += $CallBack[$s_d];
			$total_complete += $Complete[$s_d];
			$total_not_complete += $NotComplete[$s_d];
			$total_call_iniated += $CallIniated[$s_d];
			$total_contact += $SizeContact[$s_d];
			$total_machine += $AnswerMachine[$s_d];
			$total_nopickup += $NoPickUp[$s_d];	
			$total_data_size += $DataSize[$s_d];
			$total_miss_customers+= $Misscustomers[$s_d];
			$total_interest+=$Interest[$s_d];
			$total_not_interest+=$NotInterest[$s_d];
			$total_agents+=$Agents[$s_d];	
		}
		
		/** hitung rata-rata
		 ** solicted data dbase
		 **/
			$avg_call_utilize   = ROUND((($total_solicited/$total_data_size)*100),2);
			$avg_call_atempt    = ROUND((($total_atempt/$total_solicited)),2);
			$avg_call_iniated   = ROUND((($total_call_iniated/$total_solicited)*100),2);
			$avg_call_contact   = ROUND((($total_contact/$total_solicited)*100),2);
			$avg_call_complete  = ROUND((($total_complete/$total_solicited)*100),2);
			$avg_call_machine   = ROUND((($total_machine/$total_solicited)*100),2);
			$avg_call_pickup    = ROUND((($total_nopickup/$total_solicited)*100),2);
			$avg_call_callback  = ROUND((($total_callback/$total_solicited)*100),2);
			$avg_call_missed	= ROUND((($total_miss_customers/$total_solicited)*100),2); 
			$avg_call_interest	= ROUND((($total_interest/$total_solicited)*100),2); 
			$avg_call_nointerest= ROUND((($total_not_interest/$total_solicited)*100),2); 
				
		echo "<tr>
				<td nowrap class=\"total first\" align=\"left\">Summary</td>
				<td nowrap class=\"total middle\" align=\"right\">{$total_data_size}</td>
				<td nowrap class=\"total middle\" align=\"right\">{$total_solicited}</td>
				<td nowrap class=\"total middle\" align=\"right\">{$avg_call_utilize} %</td>
				<td nowrap class=\"total middle\" align=\"right\">{$total_atempt}</td>
				<td nowrap class=\"total middle\" align=\"right\">{$avg_call_atempt}</td>
				<td nowrap class=\"total middle\" align=\"right\">{$total_call_iniated}</td>
				<td nowrap class=\"total middle\" align=\"right\">{$avg_call_iniated} %</td>
				<td nowrap class=\"total middle\" align=\"right\">{$total_contact}</td>
				<td nowrap class=\"total middle\" align=\"right\">{$avg_call_contact} %</td>
				<td nowrap class=\"total middle\" align=\"right\">{$total_complete}</td>
				<td nowrap class=\"total middle\" align=\"right\">{$total_not_complete}</td>
				<td nowrap class=\"total middle\" align=\"right\">{$total_machine}</td>
				<td nowrap class=\"total middle\" align=\"right\">{$avg_call_machine} %</td>
				<td nowrap class=\"total middle\" align=\"right\">{$total_nopickup}</td>
				<td nowrap class=\"total middle\" align=\"right\">{$avg_call_pickup} %</td>
				<td nowrap class=\"total middle\" align=\"right\">0</td>
				<td nowrap class=\"total middle\" align=\"right\">0</td>
				<td nowrap class=\"total middle\" align=\"right\">{$total_callback}</td>
				<td nowrap class=\"total middle\" align=\"right\">{$avg_call_callback} %</td>
				<td nowrap class=\"total middle\" align=\"right\">{$total_miss_customers}</td>
				<td nowrap class=\"total middle\" align=\"right\">{$avg_call_missed}</td>
				<td nowrap class=\"total middle\" align=\"right\">{$total_interest}</td>
				<td nowrap class=\"total middle\" align=\"right\">{$avg_call_interest} %</td>
				<td nowrap class=\"total middle\" align=\"right\">{$total_agents}</td>
				<td nowrap class=\"total middle\" align=\"right\">&nbsp;</td>
				<td nowrap class=\"total middle\" align=\"right\">&nbsp;</td>
				<td nowrap class=\"total middle\" align=\"right\">&nbsp;</td>
				<td nowrap class=\"total middle\" align=\"right\">&nbsp;</td>
				<td nowrap class=\"total lasted\" align=\"right\">&nbsp;</td>
			</tr> </table><br>";
}
			

/**
 ** summaryPerfomanceByCampaign
 ** with interval Weekly mode
 ** break every Weekly 
**/	
 
 function _weeklyReviewByCampaign()
 {
	$start_date = $this -> getStartDate();
	$end_date = $this -> getEndDate();
	$DataSize = 0;
	
  foreach( $this -> _CampaignName() as $key => $CampaignId ) 
  {
	/**
     ** get atempt call data **
     ** return data <size >
	 **/	 
		$size_atempt = array();
		$CallIniated = array();
		$Complete = array();
		$NotComplete = array();
		$NoPickUp = array();
		$AnswerMachine = array();
		$CallBack = array();
		$Misscustomers = array();
		$Agents = array();
		$SIT = array();
		$Abadone = array();
		$totSolicited = array();
		$Interest_time = array();
		$Machine_time = array();
		$NotInterest_time = array();
		$Callback_time = array();
		$Interest = array();
		$NotInterest = array(); 
		
	/**
     ** content header data 
     ** return data <size >
	 **/	
	 
		$_conts = $this -> _query_campaign($CampaignId);
		echo "<h4>{$_conts -> result_get_value('CampaignName')}</h4>
			  <table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\">
				<tr>
					<td rowspan=\"2\" nowrap class=\"header first\" align=\"center\">Weekly</td>
					<td colspan=\"29\" nowrap class=\"header first\" align=\"center\">Campaign Review</td>
				</tr>
				<tr>
					<td nowrap class=\"header first\" align=\"center\">Database</td>
					<td nowrap class=\"header middle\" align=\"center\">Solicited</td>
					<td nowrap class=\"header middle\" align=\"center\">Solicited Rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Attempt</td>
					<td nowrap class=\"header middle\" align=\"center\">Attempt Ratio</td>
					<td nowrap class=\"header middle\" align=\"center\">Connect</td>
					<td nowrap class=\"header middle\" align=\"center\">Connect rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Contact</td>
					<td nowrap class=\"header middle\" align=\"center\">Contact Rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Completes</td>
					<td nowrap class=\"header middle\" align=\"center\">Not Completes</td>
					<td nowrap class=\"header middle\" align=\"center\">Answer Machines</td>
					<td nowrap class=\"header middle\" align=\"center\">Answer Machines %</td>
					<td nowrap class=\"header middle\" align=\"center\">NPU</td>
					<td nowrap class=\"header middle\" align=\"center\">NPU %</td>
					<td nowrap class=\"header middle\" align=\"center\">Abandons</td>
					<td nowrap class=\"header middle\" align=\"center\">Abandons %</td>
					<td nowrap class=\"header middle\" align=\"center\">Callbacks</td>
					<td nowrap class=\"header middle\" align=\"center\">Callbacks Left %</td>
					<td nowrap class=\"header middle\" align=\"center\">Miss Customers</td>
					<td nowrap class=\"header middle\" align=\"center\">Miss Customers %</td>
					<td nowrap class=\"header middle\" align=\"center\">SIT</td>
					<td nowrap class=\"header middle\" align=\"center\">SIT Rate</td>
					<td nowrap class=\"header middle\" align=\"center\">Agents</td>
					<td nowrap class=\"header middle\" align=\"center\">Average<br>Call Length<br> - Interest</td>
					<td nowrap class=\"header middle\" align=\"center\">Average<br>Call Length<br> - Not Interest</td>
					<td nowrap class=\"header middle\" align=\"center\">Average<br>Call Length<br> - Callback</td>
					<td nowrap class=\"header middle\" align=\"center\">Average<br>Call Length<br> - Answer Machines</td>
					<td nowrap class=\"header lasted\" align=\"center\">Average<br>Call Length<br> - Wait</td>
				</tr>";
				
	/** 
	 ** get summarry allocation database on campaign
	 ** with spesifid campaign  
     **/	 
	 
		$sql = " SELECT COUNT(distinct a.CustomerId) as tots FROM t_gn_customer a 
				 LEFT JOIN t_gn_campaign b on a.CampaignId WHERE a.CampaignId='$CampaignId'";
		$qry = $this -> query($sql);
		if( !$qry -> EOF() ){
			$DataSize = $qry -> result_get_value('tots');	
		}		
	/** 
	 ** get spesifik status on custoer
	 ** on interval weekly priode
	 **/
	

		$NoPickUp = $this -> _sizeByCallreason($CampaignId, NOPICKUP_STATUS);
		$CallIniated = $this -> _sizeByCallreason($CampaignId, CONNECT_STATUS);
		$SizeContact = $this -> _sizeByCallreason($CampaignId, CONTACT_STATUS);
		$CallBack = $this -> _sizeByCallreason($CampaignId, CALLBACK_STATUS);
		$AnswerMachine = $this -> _sizeByCallreason($CampaignId, ANSWERMACHINE_STATUS);
		$Misscustomers = $this -> _sizeByCallreason($CampaignId, MISSCUSTOMER_STATUS); 
		$NotComplete = $this -> _sizeByCallreason($CampaignId,NOTCOMPLETE_STATUS);
		$Complete = $this -> _sizeByCallreason($CampaignId, COMPLETE_STATUS);
	
	/** 
	 ** query , Utilize, 
	 ** agents,  Atempt,  Abadone 
	 **/
	 
		$sql = "SELECT  COUNT(distinct a.CustomerId) as Utilize ,  COUNT(distinct a.CreatedById) as agents,
				COUNT(a.CustomerId) as Atempt, '0' as SIT, '0' as Abadone, WEEK(a.CallHistoryCreatedTs) as tgl
				FROM t_gn_callhistory a
				LEFT JOIN t_gn_customer b on a.CustomerId=b.CustomerId
				WHERE b.CampaignId='$CampaignId'
				AND DATE(a.CallHistoryCallDate)>='$start_date' AND DATE(a.CallHistoryCallDate)<='$end_date'
				AND date(a.CallHistoryCreatedTs)>='$start_date' AND date(a.CallHistoryCreatedTs)<='$end_date'
				GROUP BY tgl ";
		
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows ){
			$totSolicited[$rows['tgl']] += $rows['Utilize'];
			$size_atempt[$rows['tgl']] += $rows['Atempt'];
			$Agents[$rows['tgl']] += $rows['agents'];
			$SIT[$rows['tgl']] += $rows['SIT'];
			$Abadone[$rows['tgl']] += $rows['Abadone'];
		}
		
	/** 
	 ** get interest policy status 
	 ** get on customer data 
	 **/
		$sql = " SELECT COUNT(a.CustomerId) as cnt,  
				 SUM(IF(a.CallReasonId IN(".INTEREST_STATUS."),1,0)) as Interest,
				 SUM(IF(a.CallReasonId IN(".NOTINTEREST_STATUS."),1,0)) as NotInterest, 
				 WEEK(a.CustomerUpdatedTs) as tgl
				 FROM t_gn_customer a 
				 WHERE a.CampaignId='$CampaignId'
				 GROUP BY tgl ";
		
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows ){	
			$NotInterest[$rows['tgl']]+= $rows['NotInterest'];
			$Interest[$rows['tgl']]+= $rows['Interest'];		 
		}
	
	/** 
	 ** get duration for interes data 
     ** then wil avg interest  	 
	 **/
		$sql = " SELECT 
					WEEK(b.start_time) as tgl,
					SUM(IF((b.`status` IN(3004,3005) AND a.CallReasonId IN(".ANSWERMACHINE_STATUS.")),(UNIX_TIMESTAMP(b.end_time)-UNIX_TIMESTAMP(b.agent_time)),0 )) as machine_talktime,
					SUM(IF((b.`status` IN(3004,3005) AND a.CallReasonId IN(".INTEREST_STATUS.")),(UNIX_TIMESTAMP(b.end_time)-UNIX_TIMESTAMP(b.agent_time)),0 )) as interest_talktime,
					SUM(IF((b.`status` IN(3004,3005) AND a.CallReasonId IN(".NOTINTEREST_STATUS.") ),(UNIX_TIMESTAMP(b.end_time)-UNIX_TIMESTAMP(b.agent_time)),0)) as not_interest_talktime,
					SUM(IF((b.`status` IN(3004,3005) AND a.CallReasonId IN(".CALLBACK_STATUS.") ),(UNIX_TIMESTAMP(b.end_time)-UNIX_TIMESTAMP(b.agent_time)),0)) as callback_talktime
				 FROM t_gn_customer a 
				 LEFT JOIN cc_call_session b on a.CustomerId=b.assign_data
				 WHERE a.CampaignId='$CampaignId'
				 AND a.CallReasonId IN(".ANSWERMACHINE_STATUS.",".INTEREST_STATUS.",".NOTINTEREST_STATUS.",".CALLBACK_STATUS.")
				 AND DATE(b.start_time)>='$start_date'
				 AND DATE(b.start_time)<='$end_date'
				 GROUP BY tgl ";
				 
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{	
			$Interest_time[$rows['tgl']] += $rows['interest_talktime'];
			$Machine_time[$rows['tgl']] += $rows['machine_talktime'];
			$NotInterest_time[$rows['tgl']]+= $rows['not_interest_talktime'];
			$Callback_time[$rows['tgl']]+= $rows['callback_talktime'];
		}
		 
	/** while true data looping date 
	 ** by date filter 
	 */	
	 
	 $total_solicited = 0;
	 $total_atempt = 0;
	 $total_callback = 0;
	 $total_complete = 0;
	 $total_call_iniated = 0;
	 $total_contact = 0;
	 $total_machine = 0;
	 $total_nopickup = 0;
	
	 foreach( self::_Weekly_List() as $k => $s_d ) 
	 {
		$PercentUtilize 	= ROUND((($totSolicited[$s_d]/$DataSize) * 100),2);
		$RatioAtempt 		= ROUND(($size_atempt[$s_d]/$totSolicited[$s_d]),2);
		$RatioConnect 		= ROUND((($CallIniated[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioContact 		= ROUND((($SizeContact[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioAnswerMachine = ROUND((($AnswerMachine[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioNoPickUp 		= ROUND((($NoPickUp[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioCallBack 		= ROUND((($CallBack[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioMisscustomer  = ROUND((($Misscustomers[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioAbadone		= ROUND((($Abadone[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioSIT			= ROUND((($Interest[$s_d]/$totSolicited[$s_d])*100),2);
		$AvgInterestTime    = ROUND((self::_Duration($Interest_time[$s_d])/$Interest[$s_d]),2);
		$AvgNotInterestTime = ROUND((self::_Duration($NotInterest_time[$s_d])/$NotInterest[$s_d]),2);
		$AvgCallBackTime    = ROUND((self::_Duration($Callback_time[$s_d])/$CallBack[$s_d]),2);
		$AvgMachineTime     = ROUND((self::_Duration($Machine_time[$s_d])/$AnswerMachine[$s_d]),2);
		$AvgWaitTime     	= 0.0;
		$color 				= ($this -> __week_days($s_d)?'#dddeee':'');
			
			echo "<tr>
					<td nowrap class=\"content first\" align=\"center\"> Week ".($s_d+1)." </td>
					<td nowrap class=\"content middle\" align=\"right\">".($DataSize?$DataSize:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totSolicited[$s_d]?$totSolicited[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($PercentUtilize?$PercentUtilize:0)." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($size_atempt[$s_d]?$size_atempt[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($RatioAtempt?$RatioAtempt:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($CallIniated[$s_d]?$CallIniated[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($RatioConnect?$RatioConnect:0)."  %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($SizeContact[$s_d]?$SizeContact[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($RatioContact?$RatioContact:0)."  %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($Complete[$s_d]?$Complete[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($NotComplete[$s_d]?$NotComplete[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($AnswerMachine[$s_d]?$AnswerMachine[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($RatioAnswerMachine?$RatioAnswerMachine:0)."  %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($NoPickUp[$s_d]?$NoPickUp[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($RatioNoPickUp?$RatioNoPickUp:0)." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($Abadone[$s_d]?$Abadone[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($RationAbadone?$RationAbadone:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($CallBack[$s_d]?$CallBack[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($RatioCallBack?$RatioCallBack:0)." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($Misscustomers[$s_d]?$Misscustomers[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($RatioMisscustomer?$RatioMisscustomer:0)." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($Interest[$s_d]?$Interest[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($RatioSIT?$RatioSIT:0)." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($Agents[$s_d]?$Agents[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($AvgInterestTime?$AvgInterestTime:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($AvgNotInterestTime?$AvgNotInterestTime:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($AvgCallBackTime?$AvgCallBackTime:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($AvgMachineTime?$AvgMachineTime:0)."</td>
					<td nowrap class=\"content lasted\" align=\"right\">".($AvgWaitTime?$AvgWaitTime:0)."</td>
				  </tr>";

			/** 
			 ** calculation footer 
			 ** for next step 
			 **/
					 
			$total_solicited += $totSolicited[$s_d];
			$total_atempt += $size_atempt[$s_d];
			$total_callback += $CallBack[$s_d];
			$total_complete += $Complete[$s_d];
			$total_call_iniated += $CallIniated[$s_d];
			$total_contact += $SizeContact[$s_d];
			$total_machine += $AnswerMachine[$s_d];
			$total_nopickup += $Complete[$s_d];	
		}
			/** hitung rata-rata
			 ** solicted data dbase
			 **/
				$avg_call_utilize   = ROUND((($total_solicited/$DataSize)*100),2);
				$avg_call_atempt    = ROUND((($total_atempt/$total_solicited)),2);
				$avg_call_iniated   = ROUND((($total_call_iniated/$total_solicited)*100),2);
				$avg_call_contact   = ROUND((($total_contact/$total_solicited)*100),2);
				$avg_call_complete  = ROUND((($total_complete/$total_solicited)*100),2);
				$avg_call_machine   = ROUND((($total_machine/$total_solicited)*100),2);
				$avg_call_pickup    = ROUND((($total_nopickup/$total_solicited)*100),2);
				$avg_call_callback  = ROUND((($total_callback/$total_solicited)*100),2);
				
			echo "<tr>
					<td nowrap class=\"total first\" align=\"right\" colspan=\"30\">&nbsp;</td>
				  </tr> </table><br>";
	}
 }
	
/**
 ** summaryPerfomanceByCampaign
 ** with interval daily mode
 ** break every days 
**/	

function _dailyReviewByCampaign()
{
	$DataSize = 0;
	$end_date = $this -> getEndDate();
	
	
	foreach( $this -> _CampaignName() as $key => $CampaignId ) 
	{
		$start_date = $this -> getStartDate();
		$_conts = $this -> _query_campaign($CampaignId);
		echo "<h4>{$_conts -> result_get_value('CampaignName')}</h4>
			  <table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\">
				<tr>
					<td rowspan=\"2\" nowrap class=\"header first\" align=\"center\">Date</td>
					<td colspan=\"29\" nowrap class=\"header first\" align=\"center\">Campaign Review</td>
				</tr>
				<tr>
					<td nowrap class=\"header first\" align=\"center\">Database</td>
					<td nowrap class=\"header middle\" align=\"center\">Solicited</td>
					<td nowrap class=\"header middle\" align=\"center\">Solicited Rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Attempt</td>
					<td nowrap class=\"header middle\" align=\"center\">Attempt Ratio</td>
					<td nowrap class=\"header middle\" align=\"center\">Connect</td>
					<td nowrap class=\"header middle\" align=\"center\">Connect rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Contact</td>
					<td nowrap class=\"header middle\" align=\"center\">Contact Rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Completes</td>
					<td nowrap class=\"header middle\" align=\"center\">Not Completes</td>
					<td nowrap class=\"header middle\" align=\"center\">Answer Machines</td>
					<td nowrap class=\"header middle\" align=\"center\">Answer Machines %</td>
					<td nowrap class=\"header middle\" align=\"center\">NPU</td>
					<td nowrap class=\"header middle\" align=\"center\">NPU %</td>
					<td nowrap class=\"header middle\" align=\"center\">Abandons</td>
					<td nowrap class=\"header middle\" align=\"center\">Abandons %</td>
					<td nowrap class=\"header middle\" align=\"center\">Callbacks</td>
					<td nowrap class=\"header middle\" align=\"center\">Callbacks Left %</td>
					<td nowrap class=\"header middle\" align=\"center\">Miss Customers</td>
					<td nowrap class=\"header middle\" align=\"center\">Miss Customers %</td>
					<td nowrap class=\"header middle\" align=\"center\">SIT</td>
					<td nowrap class=\"header middle\" align=\"center\">SIT Rate</td>
					<td nowrap class=\"header middle\" align=\"center\">Agents</td>
					<td nowrap class=\"header middle\" align=\"center\">Average<br>Call Length<br> - Interest</td>
					<td nowrap class=\"header middle\" align=\"center\">Average<br>Call Length<br> - Not Interest</td>
					<td nowrap class=\"header middle\" align=\"center\">Average<br>Call Length<br> - Callback</td>
					<td nowrap class=\"header middle\" align=\"center\">Average<br>Call Length<br> - Answer Machines</td>
					<td nowrap class=\"header lasted\" align=\"center\">Average<br>Call Length<br> - Wait</td>
				</tr>";
	/** 
	 ** get summarry allocation database on campaign
	 ** with spesifid campaign  
     **/	 
		
		$DataSize = 0;
		$sql = " SELECT COUNT(distinct a.CustomerId) as tots 
				 FROM t_gn_customer a 
				 left join t_gn_campaign b on a.CampaignId
				 WHERE a.CampaignId='$CampaignId'";
			 
		$qry = $this -> query($sql);
		if( !$qry -> EOF() ){
			$DataSize = $qry -> result_get_value('tots');	
		}		
			
	/**
     ** get atempt call data **
     ** return data <size >
	 **/	 
		$totSolicited = array();
		$size_atempt = array();
		$CallIniated = array();
		$Complete = array();
		$NotComplete = array();
		$NoPickUp = array();
		$AnswerMachine = array();
		$CallBack = array();
		$SizeContact = array();
		$Misscustomers = array();
		$Agents = array();
		$SIT = array();
		$Abadone = array();
		
		$sql = "SELECT 
				COUNT(distinct a.CustomerId) as Utilize, 
				COUNT(distinct a.CreatedById) as agents,
				COUNT(a.CallHistoryId) as Atempt, 
				SUM(IF(a.CallReasonId IN(".SIT_STATUS."),1,0)) as SIT,
				SUM(IF(a.CallReasonId IN(".ABANDONE_STATUS."),1,0)) as Abadone,
				DATE(a.CallHistoryCreatedTs) as tgl
				FROM t_gn_callhistory a
				LEFT JOIN t_gn_customer b on a.CustomerId=b.CustomerId
				LEFT JOIN tms_agent c on a.CreatedById=c.UserId
				WHERE b.CampaignId='$CampaignId'
				AND c.handling_type =4 
				AND DATE(a.CallHistoryCallDate)>='$start_date'
				AND DATE(a.CallHistoryCallDate)<='$end_date'
				AND date(a.CallHistoryCreatedTs)>='$start_date'
				AND date(a.CallHistoryCreatedTs)<='$end_date'
				GROUP BY tgl ";
	//echo $sql;		
		
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$totSolicited[$rows['tgl']]+= $rows['Utilize'];
			$size_atempt[$rows['tgl']]+= $rows['Atempt'];
			$Agents[$rows['tgl']]+= $rows['agents'];
			$SIT[$rows['tgl']]+= $rows['SIT'];
			$Abadone[$rows['tgl']]+= $rows['Abadone'];
		
		}
		
	/** 
	 ** get interest policy status 
	 ** get on customer data 
	 **/
		$Interest = array();
		$NotInterest = array(); 
		$sql = " SELECT COUNT(a.CustomerId) as cnt,  
				 SUM(IF(a.CallReasonId IN(".INTEREST_STATUS."),1,0)) as Interest,
				 SUM(IF(a.CallReasonId IN(".NOTINTEREST_STATUS."),1,0)) as NotInterest,
				 DATE(a.CustomerUpdatedTs) as tgl
				 FROM t_gn_customer a 
				 WHERE a.CampaignId='$CampaignId'
				 GROUP BY tgl ";
		
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{	
			$NotInterest[$rows['tgl']]+= $rows['NotInterest'];
			$Interest[$rows['tgl']]+= $rows['Interest'];		 
		}
	
	/** 
	 ** get duration for interes data 
     ** then wil avg interest  	 
	 **/
	 
		$Interest_time = array();
		$Machine_time = array();
		$NotInterest_time = array();
		$Callback_time = array();
		
		$sql = " SELECT 
					DATE(b.start_time) as tgl,
					SUM(IF((b.`status` IN(3004,3005) AND a.CallReasonId IN(".ANSWERMACHINE_STATUS.")),(UNIX_TIMESTAMP(b.end_time)-UNIX_TIMESTAMP(b.agent_time)),0 )) as machine_talktime,
					SUM(IF((b.`status` IN(3004,3005) AND a.CallReasonId IN(".INTEREST_STATUS.")),(UNIX_TIMESTAMP(b.end_time)-UNIX_TIMESTAMP(b.agent_time)),0 )) as interest_talktime,
					SUM(IF((b.`status` IN(3004,3005) AND a.CallReasonId IN(".NOTINTEREST_STATUS.") ),(UNIX_TIMESTAMP(b.end_time)-UNIX_TIMESTAMP(b.agent_time)),0)) as not_interest_talktime,
					SUM(IF((b.`status` IN(3004,3005) AND a.CallReasonId IN(".CALLBACK_STATUS.") ),(UNIX_TIMESTAMP(b.end_time)-UNIX_TIMESTAMP(b.agent_time)),0)) as callback_talktime
				 FROM t_gn_customer a 
				 LEFT JOIN cc_call_session b on a.CustomerId=b.assign_data
				 WHERE a.CampaignId='$CampaignId'
				 AND a.CallReasonId IN(".ANSWERMACHINE_STATUS.",".INTEREST_STATUS.",".NOTINTEREST_STATUS.",".CALLBACK_STATUS.")
				 AND DATE(b.start_time)>='$start_date'
				 AND DATE(b.start_time)<='$end_date'
				 GROUP BY tgl ";
				 
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{	
			$Interest_time[$rows['tgl']] += $rows['interest_talktime'];
			$Machine_time[$rows['tgl']] += $rows['machine_talktime'];
			$NotInterest_time[$rows['tgl']]+= $rows['not_interest_talktime'];
			$Callback_time[$rows['tgl']]+= $rows['callback_talktime'];
		}
		 
	/** while true data looping date 
	 ** by date filter 
	 */	
	 
	 $total_solicited = 0;
	 $total_atempt = 0;
	 $total_callback = 0;
	 $total_complete = 0;
	 $total_call_iniated = 0;
	 $total_contact = 0;
	 $total_machine = 0;
	 $total_nopickup = 0;
	 
	 while(true){
		$s_d = $start_date;
		
		$CallIniated 	= self::_sizeDailyCallreason($CampaignId,$s_d,$s_d,CONNECT_STATUS);
		$NoPickUp 	 	= self::_sizeDailyCallreason($CampaignId,$s_d,$s_d,NOPICKUP_STATUS);
		$SizeContact 	= self::_sizeDailyCallreason($CampaignId,$s_d,$s_d,CONTACT_STATUS);
		$CallBack 		= self::_sizeDailyCallreason($CampaignId,$s_d,$s_d,CALLBACK_STATUS);
		$AnswerMachine 	= self::_sizeDailyCallreason($CampaignId,$s_d,$s_d,ANSWERMACHINE_STATUS);
		$Misscustomers 	= self::_sizeDailyCallreason($CampaignId,$s_d,$s_d,MISSCUSTOMER_STATUS); 
		$NotComplete 	= self::_sizeDailyCallreason($CampaignId,$s_d,$s_d,NOTCOMPLETE_STATUS);
		$Complete 		= self::_sizeDailyCallreason($CampaignId,$s_d,$s_d,COMPLETE_STATUS);	
		
		/** rumus perhitungan ****/
		
		$PercentUtilize 	= ROUND((($totSolicited[$s_d]/$DataSize) * 100),2);
		$RatioAtempt 		= ROUND(($size_atempt[$s_d]/$totSolicited[$s_d]),2);
		$RatioConnect 		= ROUND((($CallIniated[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioContact 		= ROUND((($SizeContact[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioAnswerMachine = ROUND((($AnswerMachine[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioNoPickUp 		= ROUND((($NoPickUp[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioCallBack 		= ROUND((($CallBack[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioMisscustomer  = ROUND((($Misscustomers[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioAbadone		= ROUND((($Abadone[$s_d]/$totSolicited[$s_d])*100),2);
		$RatioSIT			= ROUND((($Interest[$s_d]/$totSolicited[$s_d])*100),2);
		$AvgInterestTime    = ROUND((self::_Duration($Interest_time[$s_d])/$Interest[$s_d]),2);
		$AvgNotInterestTime = ROUND((self::_Duration($NotInterest_time[$s_d])/$NotInterest[$s_d]),2);
		$AvgCallBackTime    = ROUND((self::_Duration($Callback_time[$s_d])/$CallBack[$s_d]),2);
		$AvgMachineTime     = ROUND((self::_Duration($Machine_time[$s_d])/$AnswerMachine[$s_d]),2);
		$AvgWaitTime     	= 0.0;
		$color 				= ($this -> __week_days($s_d)?'#dddeee':'');
		
				echo "<tr>
						<td nowrap class=\"content first\" align=\"center\" style=\"background-color:$color;\">".$this ->formatDateId($s_d)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($DataSize?$DataSize:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($totSolicited[$s_d]?$totSolicited[$s_d]:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($PercentUtilize?$PercentUtilize:0)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".($size_atempt[$s_d]?$size_atempt[$s_d]:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($RatioAtempt?$RatioAtempt:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($CallIniated[$s_d]?$CallIniated[$s_d]:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($RatioConnect?$RatioConnect:0)."  %</td>
						<td nowrap class=\"content middle\" align=\"right\">".($SizeContact[$s_d]?$SizeContact[$s_d]:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($RatioContact?$RatioContact:0)."  %</td>
						<td nowrap class=\"content middle\" align=\"right\">".($Complete[$s_d]?$Complete[$s_d]:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($NotComplete[$s_d]?$NotComplete[$s_d]:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($AnswerMachine[$s_d]?$AnswerMachine[$s_d]:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($RatioAnswerMachine?$RatioAnswerMachine:0)."  %</td>
						<td nowrap class=\"content middle\" align=\"right\">".($NoPickUp[$s_d]?$NoPickUp[$s_d]:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($RatioNoPickUp?$RatioNoPickUp:0)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".($Abadone[$s_d]?$Abadone[$s_d]:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($RationAbadone?$RationAbadone:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($CallBack[$s_d]?$CallBack[$s_d]:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($RatioCallBack?$RatioCallBack:0)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".($Misscustomers[$s_d]?$Misscustomers[$s_d]:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($RatioMisscustomer?$RatioMisscustomer:0)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".($Interest[$s_d]?$Interest[$s_d]:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($RatioSIT?$RatioSIT:0)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".($Agents[$s_d]?$Agents[$s_d]:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($AvgInterestTime?$AvgInterestTime:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($AvgNotInterestTime?$AvgNotInterestTime:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($AvgCallBackTime?$AvgCallBackTime:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($AvgMachineTime?$AvgMachineTime:0)."</td>
						<td nowrap class=\"content lasted\" align=\"right\">".($AvgWaitTime?$AvgWaitTime:0)."</td>
				</tr>";
					
				/** 
				 ** calculation footer 
				 ** for next step 
				 **/
					 
					$total_solicited += $totSolicited[$s_d];
					$total_atempt += $size_atempt[$s_d];
					$total_callback += $CallBack[$s_d];
					$total_complete += $Complete[$s_d];
					$total_call_iniated += $CallIniated[$s_d];
					$total_contact += $SizeContact[$s_d];
					$total_machine += $AnswerMachine[$s_d];
					$total_nopickup += $Complete[$s_d];
					
				  if( $start_date == $end_date ) break;
						$start_date = $this -> nextDate($start_date);
					
					
					
			}
			/** hitung rata-rata
			 ** solicted data dbase
			 **/
				$avg_call_utilize   = ROUND((($total_solicited/$DataSize)*100),2);
				$avg_call_atempt    = ROUND((($total_atempt/$total_solicited)),2);
				$avg_call_iniated   = ROUND((($total_call_iniated/$total_solicited)*100),2);
				$avg_call_contact   = ROUND((($total_contact/$total_solicited)*100),2);
				$avg_call_complete  = ROUND((($total_complete/$total_solicited)*100),2);
				$avg_call_machine   = ROUND((($total_machine/$total_solicited)*100),2);
				$avg_call_pickup    = ROUND((($total_nopickup/$total_solicited)*100),2);
				$avg_call_callback  = ROUND((($total_callback/$total_solicited)*100),2);
				
			echo "<tr>
					<td nowrap class=\"total first\" align=\"right\" colspan=\"30\">&nbsp;</td>
				  </tr> </table><br>";
		}
		
		//$this -> view_filter();
		
	}
}
?>
<!--- EOF -->