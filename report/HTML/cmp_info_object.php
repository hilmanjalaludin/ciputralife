<!-- SOF --> 

<?php
class cmp_info_object extends index
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
 ** @ handle return <void>a
 */
 
 function cmp_info_object()
 {
	$this -> _con  = null;
	$this -> _xfn  = 'REPORT_CAMPAIGN_INFO_OBJECT';
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

 /** 
	 ** get summarry allocation database on campaign
	 ** with spesifid campaign  
     **/	 
	 
function _summaryByDaily($CampaignId, $start_date, $end_date)
{	 

	$start_date = self::getStartDate();
	$end_date = self::getEndDate();
	$data = array();
	
	$sql = "SELECT 
				COUNT(a.CustomerId) AS tots, a.CampaignId AS idx, 
				SUM(IF((a.CallReasonId IS NOT NULL AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date' ) ,1,0)) AS Utilize,
				SUM(IF((a.CallReasonId IN(".MISSCUSTOMER_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) AS Misscustomers,
				SUM(IF((a.CallReasonId IN(".NOPICKUP_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) AS NoPickUp,
				SUM(IF((a.CallReasonId IN(".CONTACT_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) AS Contact,
				SUM(IF((a.CallReasonId IN(".CALLBACK_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) AS CallBack,
				SUM(IF((a.CallReasonId IN(".NOTINTEREST_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) AS NotInterest,
				SUM(IF((a.CallReasonId IN(".COMPLETE_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) as Complete
			    FROM t_gn_customer a  
				WHERE a.CampaignId IN('$CampaignId')
				GROUP BY idx ";
	//echo "<pre>Daily : <br>$sql</pre>";			
	$qry = $this -> query($sql);
	foreach($qry -> result_assoc() as $rows )
	{
			$data[$rows['idx']]['DataSize']= $rows['tots'];
			$data[$rows['idx']]['totSolicited'] = $rows['Utilize'];
			$data[$rows['idx']]['NoPickUp']= $rows['NoPickUp']; 
			$data[$rows['idx']]['SizeContact']= $rows['Contact']; 
			$data[$rows['idx']]['CallBack']= $rows['CallBack']; 
			$data[$rows['idx']]['Misscustomers']= $rows['Misscustomers'];  
			$data[$rows['idx']]['Complete']= $rows['Complete']; 
			$data[$rows['idx']]['NotInterest']= $rows['NotInterest'];
	}
	
	return $data;
}	
/**
 ** @ aksesor of class 
 ** @ handle return <void>
 */
 
 function _sizeByCallreason($CampaignId=0, $_within)
 {
	$start_date = $this -> getStartDate();
	$end_date = $this -> getEndDate();
	
	$_conds = 0;
	
	$sql = " SELECT COUNT(DISTINCT a.CustomerId) AS cnt 
			 FROM t_gn_callhistory a
			 LEFT JOIN t_gn_customer b ON a.CustomerId=b.CustomerId
			 LEFT JOIN t_lk_callreason c ON a.CallReasonId = c.CallReasonId
			 WHERE b.CampaignId='$CampaignId' 
			 AND DATE(a.CallHistoryCallDate)>='$start_date' 
			 AND DATE(a.CallHistoryCallDate)<='$end_date' 
			 AND DATE(a.CallHistoryCreatedTs)>='$start_date' 
			AND DATE(a.CallHistoryCreatedTs)<='$end_date'
			AND c.CallReasonId IN($_within) ";
			
    $qry = $this -> query($sql);			
	if( is_object($qry)){
		$_conds = $qry  -> result_singgle_value();
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
			return sprintf("%01d.%02d", $hour,$min);
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
 	
 private function getStartDate(){
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
		switch( $this -> _grp )
		{
			case 'campaign'   : $this -> ReviewByCampaign(); break; 
			default:
				echo "<h3>Sorry, You must filtering by Campaign!</h3>";
			break;
		}
		
		//$this -> _Excel_Footer();
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



/** get total Hour By CampaignId
 ** render by followup agent ID 
 **/
 
function _getHourByCampaignId($CampaignId='', $AgentId)
{	 
	
	$start_date = $this -> getStartDate();
	$end_date 	= $this -> getEndDate();
	$LoignHours = array();
	
	$sql = " SELECT hour(a.start_time) as HourTime,
				SUM( IF(a.`status` IN (1,2,3,4),(UNIX_TIMESTAMP(a.end_time)-UNIX_TIMESTAMP(a.start_time)),0)) as tots,
				SUM( IF(a.`status`=1,(UNIX_TIMESTAMP(a.end_time)-UNIX_TIMESTAMP(a.start_time)), 0)) as Ready,
				SUM( IF(a.`status`=2,(UNIX_TIMESTAMP(a.end_time)-UNIX_TIMESTAMP(a.start_time)), 0)) as NotReady,
				SUM( IF(a.`status`=3,(UNIX_TIMESTAMP(a.end_time)-UNIX_TIMESTAMP(a.start_time)), 0)) as ACW,
				SUM( IF(a.`status`=4,(UNIX_TIMESTAMP(a.end_time)-UNIX_TIMESTAMP(a.start_time)), 0)) as Busy
			FROM cc_agent_activity_log a 
			LEFT JOIN cc_agent b on a.agent=b.id
			LEFT JOIN tms_agent c on b.userid=c.id
			WHERE date(a.start_time)>='$start_date'
			AND date(a.start_time)<='$end_date'
			AND a.`status` IN(1,2,3,4)
			AND c.UserId IN('".implode("','",$AgentId)."')
			GROUP BY HourTime ";
				
		$qry  = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$LoignHours['TotLoginHour']+= $rows['tots'];
			$LoignHours['TotReady']+= $rows['Ready'];
			$LoignHours['TotNotReady']+= $rows['NotReady'];
			$LoignHours['TotACW']+= $rows['ACW'];
			$LoignHours['TotBusy']+= $rows['Busy'];
		}
		
	return $LoignHours;
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
					<td rowspan='2' nowrap class=\"header first\" align=\"center\">Campaign Name</td>
					<td colspan='20' nowrap class=\"header middle\" align=\"center\">Contact Performance</td>
					<td colspan='8' nowrap class=\"header middle\" align=\"center\">Sales & Metrics</td>
					<td colspan='6' nowrap class=\"header middle\" align=\"center\">Agents & Hours</td>
				</tr>
				<tr>
					<td nowrap class=\"header middle\" align=\"center\">Database</td>
					<td nowrap class=\"header middle\" align=\"center\">Solicited</td>
					<td nowrap class=\"header middle\" align=\"center\">Solicited Rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Attempt</td>
					<td nowrap class=\"header middle\" align=\"center\">Attempt Ratio</td>
					<td nowrap class=\"header middle\" align=\"center\">Attempt per Complete</td>
					<td nowrap class=\"header middle\" align=\"center\">Attempt per Contact</td>
					<td nowrap class=\"header middle\" align=\"center\">Atempt per Hour</td>
					<td nowrap class=\"header middle\" align=\"center\">Completes</td>
					<td nowrap class=\"header middle\" align=\"center\">Completes Penetration %</td>
					<td nowrap class=\"header middle\" align=\"center\">Completes per Hour</td>
					<td nowrap class=\"header middle\" align=\"center\">Callbacks</td>
					<td nowrap class=\"header middle\" align=\"center\">Callbacks per Hour</td>
					<td nowrap class=\"header middle\" align=\"center\">Callbacks Left %</td>
					<td nowrap class=\"header middle\" align=\"center\">Contact</td>
					<td nowrap class=\"header middle\" align=\"center\">Contacts per Complete %</td>
					<td nowrap class=\"header middle\" align=\"center\">Contact per Hour</td>
					<td nowrap class=\"header middle\" align=\"center\">Not Interest</td>
					<td nowrap class=\"header middle\" align=\"center\">Miss Customers</td>
					<td nowrap class=\"header middle\" align=\"center\">NPU</td>
					<td nowrap class=\"header middle\" align=\"center\">PIF</td>
					<td nowrap class=\"header middle\" align=\"center\">AARP</td>
					<td nowrap class=\"header middle\" align=\"center\">ANP</td>
					<td nowrap class=\"header middle\" align=\"center\">Average Premium</td>
					<td nowrap class=\"header middle\" align=\"center\">Contact Rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Sales Close Rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Response Rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Sales per Hour</td>
					<td nowrap class=\"header middle\" align=\"center\">Log In Hours</td>
					<td nowrap class=\"header middle\" align=\"center\">Agents</td>
					<td nowrap class=\"header middle\" align=\"center\">Average Worked Hours</td>
					<td nowrap class=\"header middle\" align=\"center\">Talk Time %</td>
					<td nowrap class=\"header middle\" align=\"center\">Wait Time %</td>
					<td nowrap class=\"header lasted\" align=\"center\">Wrap Time %</td>
				</tr>";
				

	/**
     ** get atempt call data **
     ** return data <size >
	 **/
	  
		$DataSize = array();
		$size_atempt = array();
		$Complete = array();
		$NoPickUp = array();
		$CallBack = array();
		$Misscustomers = array();
		$Agents = array();
		$totSolicited = array();
		$NotInterest = array(); 
		
	
	/** 
	 ** get summarry allocation database on campaign
	 ** with spesifid campaign  
     **/	 
	 
		$sql = "SELECT 
				COUNT(a.CustomerId) AS tots, a.CampaignId AS idx, 
				SUM(IF((a.CallReasonId IS NOT NULL AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date' ) ,1,0)) AS Utilize,
				SUM(IF((a.CallReasonId IN(".MISSCUSTOMER_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) AS Misscustomers,
				SUM(IF((a.CallReasonId IN(".NOPICKUP_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) AS NoPickUp,
				SUM(IF((a.CallReasonId IN(".CONTACT_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) AS Contact,
				SUM(IF((a.CallReasonId IN(".CALLBACK_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) AS CallBack,
				SUM(IF((a.CallReasonId IN(".NOTINTEREST_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) AS NotInterest,
				SUM(IF((a.CallReasonId IN(".COMPLETE_STATUS.") AND date(a.CustomerUpdatedTs)>='$start_date' AND date(a.CustomerUpdatedTs)<='$end_date'),1,0)) as Complete
			    FROM t_gn_customer a  
				WHERE a.CampaignId IN(".self::_with_code(self::_CampaignName()).")
				GROUP BY idx ";
		//echo "<pre>$sql</pre>";
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$DataSize[$rows['idx']]= $rows['tots'];
			$totSolicited[$rows['idx']] += $rows['Utilize'];
			$NoPickUp[$rows['idx']]= $rows['NoPickUp']; 
			$SizeContact[$rows['idx']]= $rows['Contact']; 
			$CallBack[$rows['idx']]= $rows['CallBack']; 
			$Misscustomers[$rows['idx']]= $rows['Misscustomers'];  
			$Complete[$rows['idx']]= $rows['Complete']; 
			$NotInterest[$rows['idx']]= $rows['NotInterest'];
		}
		
	
	/** 
	 ** query , Utilize, 
	 ** agents,  Atempt,  Abadone 
	 **/
	 
		$sql = "SELECT  
					COUNT(distinct a.CustomerId) as Utilize ,  
					COUNT(distinct a.CreatedById) as agents,
					COUNT(a.CustomerId) as Atempt,
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
			$NotInterest[$rows['IDX']]+= $rows['NotInterest'];
		}
		
	/**
     ** get Sales & Metrics **
     ** return data <size >
	 **/	 
	 
		$size_pif = array();
		$size_anp = array();
		
		$sql = " select  a.CampaignId as IDX,
					count(distinct b.PolicyNumber) as PIF, 
					SUM(IF(d.PayModeId=2,(c.Premi*12), c.Premi)) AS ANP
					from t_gn_customer a 
					left join t_gn_policyautogen b on a.CustomerId=b.CustomerId
					left join t_gn_policy c on b.PolicyNumber=c.PolicyNumber
					right join t_gn_productplan d on d.ProductPlanId=c.ProductPlanId
					where 1=1
					AND DATE(c.PolicySalesDate)>= '$start_date'
					AND DATE(c.PolicySalesDate)<= '$end_date'
					and b.PolicyNumber is not null
					and a.CallReasonId IN(".INTEREST_STATUS.")
					AND a.CampaignId IN(".self::_with_code(self::_CampaignName()).")
					group by IDX ";	
					
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$size_pif[$rows['IDX']]+= $rows['PIF'];
			$size_anp[$rows['IDX']]+= $rows['ANP'];
		}
	

	
	/** while true data looping date 
	 ** by date filter 
	 */	
	 
	 $LoginHours = array();
	 $total_pif = 0;
	 $total_solicited = 0;
	 $total_atempt = 0;
	 $total_callback = 0;
	 $total_complete = 0;
	 $total_contact = 0;
	 $total_nopickup = 0;
	 $total_login_hours = 0;
	 $total_login_ready = 0;
	 $total_login_notready = 0;
	 $total_login_busy = 0;
	 $total_login_agents = 0;
	 
	foreach( $this -> _CampaignName() as $k => $s_d ) 
	{	
		
	/** get agent followup this data 
	 ** regiter LOCAL Indicated
	 **/
		$TotalAgentLogin = 0; 
		$TotalAgentReady = 0; 
		$TotalAgentNotReady = 0;
		$TotalAgentBusy = 0;
		$AgentId = '';
		$_conts_init_names = $this -> _query_campaign($s_d);
		
		$sql = " SELECT  distinct a.CreatedById as UserId 
				 from t_gn_callhistory a 
				 left join t_gn_customer b on a.CustomerId=b.CustomerId
				 LEFT JOIN tms_agent c on a.CreatedById=c.UserId
				 where b.CampaignId ='$s_d'
				 and date(a.CallHistoryCreatedTs)>='$start_date'
				 and date(a.CallHistoryCreatedTs)<='$end_date'
				 and c.handling_type IN('".USER_TELESALES."') ";
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows ){
			$AgentId[] = $rows['UserId'];
		}	
		

		$LoginHours = self::_getHourByCampaignId($s_d, $AgentId);
		
	/** callculation on definition content 
	 ** look on before definition again
	 **/	
			
		$TotalAgentLogin	= $LoginHours['TotLoginHour'];
		$TotalAgentReady	= $LoginHours['TotReady'];
		$TotalAgentNotReady = $LoginHours['TotNotReady'];
		$TotalAgentBusy		= $LoginHours['TotBusy'];
		$PercentUtilize 	= ROUND((($totSolicited[$s_d]/$DataSize[$s_d]) * 100),2);
		$RatioAtempt 		= ROUND(($size_atempt[$s_d]/$totSolicited[$s_d]),2);
		$AttemptPerComplete	= ROUND(($size_atempt[$s_d]/$Complete[$s_d]),2);
		$AttemptPerContact	= ROUND(($size_atempt[$s_d]/$SizeContact[$s_d]),2);
		$AvgPresentation	= ROUND((($Complete[$s_d]/$DataSize[$s_d]) * 100),2);
		$CallbackLeft		= ROUND((($CallBack[$s_d]/$DataSize[$s_d]) * 100),2);
		$ContactPerComplete	= ROUND((($Complete[$s_d]/$SizeContact[$s_d]) * 100),2);
		$AARP				= ($size_anp[$s_d]/$size_pif[$s_d]);
		$AvgPremi			= ($AARP/12);
		$ContactRate		= ROUND((($SizeContact[$s_d] /$totSolicited[$s_d]) * 100),2);
		$SalesClose			= ROUND((($size_pif[$s_d]/$SizeContact[$s_d]) * 100),2);
		$ResponseRate		= ROUND((($size_pif[$s_d]/$DataSize[$s_d]) * 100),2);
		$AtemptPerHour 		= ROUND(($size_atempt[$s_d]/self::_Duration($TotalAgentLogin)),2);
		$CompletePerHour 	= ROUND(($Complete[$s_d]/self::_Duration($TotalAgentLogin)),2);
		$CallbacksPerHour 	= ROUND(($CallBack[$s_d]/self::_Duration($TotalAgentLogin)),2);
		$ContactPerHour 	= ROUND(($SizeContact[$s_d]/self::_Duration($TotalAgentLogin)),2);
		$SalesPerHour 		= ROUND(($size_pif[$s_d]/self::_Duration($TotalAgentLogin)),3);
		$avg_agent_worked	= ROUND((self::_Duration($TotalAgentLogin)/$Agents[$s_d]),2); 
		$avg_agent_ready	= ROUND((($TotalAgentReady/$TotalAgentLogin)*100),2);
		$avg_agent_notready = ROUND((($TotalAgentNotReady/$TotalAgentLogin)*100),2); 
		$avg_agent_busy		= ROUND((($TotalAgentBusy/$TotalAgentLogin)*100),2);
	/**
     ** resulting content to table
	 ** HTML Mode 
	 **/
	 
		$CampaignName = $_conts_init_names ->result_get_value('CampaignName');
		echo "<tr>
				<td height=26 class=\"content first\" nowrap align=\"right\">&nbsp;".($CampaignName?$CampaignName:'')."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($DataSize[$s_d]?$DataSize[$s_d]:0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($totSolicited[$s_d]?$totSolicited[$s_d]:0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($PercentUtilize?$PercentUtilize:0)." %</td>
				<td nowrap class=\"content middle\" align=\"right\">".($size_atempt[$s_d]?$size_atempt[$s_d]:0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($RatioAtempt?$RatioAtempt:0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($AttemptPerComplete?$AttemptPerComplete:0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($AttemptPerContact?$AttemptPerContact:0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($AtemptPerHour?$AtemptPerHour:0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($Complete[$s_d]?$Complete[$s_d]:0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($AvgPresentation?$AvgPresentation:0)." %</td>
				<td nowrap class=\"content middle\" align=\"right\">".($CompletePerHour?$CompletePerHour:0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($CallBack[$s_d]?$CallBack[$s_d]:0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($CallbacksPerHour?$CallbacksPerHour:0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($CallbackLeft?$CallbackLeft:0)." %</td>
				<td nowrap class=\"content middle\" align=\"right\">".($SizeContact[$s_d]?$SizeContact[$s_d]:0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($ContactPerComplete?$ContactPerComplete:0)." %</td>
				<td nowrap class=\"content middle\" align=\"right\">".($ContactPerHour?$ContactPerHour:0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($NotInterest[$s_d]?$NotInterest[$s_d]:0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($Misscustomers[$s_d]?$Misscustomers[$s_d]:0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($NoPickUp[$s_d]?$NoPickUp[$s_d]:0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($size_pif[$s_d]?$size_pif[$s_d]:0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".formatRupiah(($AARP?$AARP:0))."</td>
				<td nowrap class=\"content middle\" align=\"right\">".formatRupiah(($size_anp[$s_d]?$size_anp[$s_d]:0))."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($AvgPremi)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($ContactRate?$ContactRate:0)." %</td>
				<td nowrap class=\"content middle\" align=\"right\">".($SalesClose?$SalesClose:0)." %</td>
				<td nowrap class=\"content middle\" align=\"right\">".($ResponseRate?$ResponseRate:0)." %</td>
				<td nowrap class=\"content middle\" align=\"right\">".($SalesPerHour?$SalesPerHour:0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($TotalAgentLogin?toDuration($TotalAgentLogin):0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($Agents[$s_d]?$Agents[$s_d]:0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($avg_agent_worked?$avg_agent_worked:0)."</td>
				<td nowrap class=\"content middle\" align=\"right\">".($avg_agent_busy?$avg_agent_busy:0)." %</td>
				<td nowrap class=\"content middle\" align=\"right\">".($avg_agent_ready?$avg_agent_ready:0)." %</td>
				<td nowrap class=\"content lasted\" align=\"right\">".($avg_agent_notready?$avg_agent_notready:0)." %</td>
			</tr>";

			/** 
			 ** calculation footer 
			 ** for next step 
			 **/
					 
			$total_datasize += $DataSize[$s_d];
			$total_solicited += $totSolicited[$s_d];
			$total_atempt += $size_atempt[$s_d];
			$total_callback += $CallBack[$s_d];
			$total_complete += $Complete[$s_d];
			$total_contact += $SizeContact[$s_d];
			$total_nopickup += $Complete[$s_d];
			$total_not_interest += $NotInterest[$s_d];
			$total_miss_cust += $Misscustomers[$s_d];
			$total_npu += $NoPickUp[$s_d];
			$total_pif += $size_pif[$s_d];
			$total_anp += $size_anp[$s_d];
			$total_agent += $Agents[$s_d];	
			$total_login_hours += $TotalAgentLogin;
			$total_login_ready += $TotalAgentReady;
			$total_login_notready += $TotalAgentNotReady;
			$total_login_busy += $TotalAgentBusy;
			 
		}
		
		/** hitung rata-rata
		 ** solicted data dbase
		 **/
			$avg_call_utilize   	= ROUND((($total_solicited/$DataSize[$s_d])*100),2);
			$avg_call_atempt    	= ROUND((($total_atempt/$total_solicited)),2);
			$avg_attempt_complete	= ROUND(($total_atempt / $total_complete),2);
			$avg_attempt_contact	= ROUND(($total_atempt / $total_contact),2);
			$avg_complete_penetrate	= ROUND((($total_complete / $DataSize[$s_d]) * 100),2);
			$avg_callback_left		= ROUND((($total_callback / $DataSize[$s_d]) * 100),2);
			$avg_contact_complete	= ROUND((($total_complete / $total_contact) * 100),2);
			$avg_aarp				= ($total_anp / $total_pif);
			$avg_premium			= ($avg_aarp / 12);
			$avg_call_contact   	= ROUND((($total_contact/$total_solicited)*100),2);
			$avg_sales_close		= ROUND((($total_pif / $total_contact) * 100),2);
			$avg_response			= ROUND((($total_pif / $DataSize[$s_d]) * 100),2);
			$avg_call_complete  	= ROUND((($total_complete/$total_solicited)*100),2);
			$avg_call_pickup    	= ROUND((($total_nopickup/$total_solicited)*100),2);
			$avg_call_callback  	= ROUND((($total_callback/$total_solicited)*100),2);
			
			$avg_tots_agent_worked	= ROUND((self::_Duration($total_login_hours)/$total_agent),2); 
			$avg_tots_agent_ready 	= ROUND((($total_login_ready/$total_login_hours)*100),2);
			$avg_tots_agent_notready= ROUND((($total_login_notready/$total_login_hours)*100),2); 
			$avg_tots_agent_busy 	= ROUND((($total_login_busy/$total_login_hours)*100),2); 
			
			$avg_AtemptPerHour 		= ROUND(($total_atempt/self::_Duration($total_login_hours)),2);
			$avg_CompletePerHour 	= ROUND(($total_complete/self::_Duration($total_login_hours)),2);
			$avg_CallbacksPerHour 	= ROUND(($total_callback/self::_Duration($total_login_hours)),2);
			$avg_ContactPerHour 	= ROUND(($total_contact/self::_Duration($total_login_hours)),2);
			$avg_SalesPerHour 		= ROUND(($total_pif/self::_Duration($total_login_hours)),3);
		
			
			
		echo "<tr>
				<td nowrap class=\"total first\" align=\"center\">MTD</td>
				<td nowrap class=\"total middle\" align=\"right\">".($total_datasize?$total_datasize:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($total_solicited?$total_solicited:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_call_utilize?$avg_call_utilize:0)." %</td>
				<td nowrap class=\"total middle\" align=\"right\">".($total_atempt?$total_atempt:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_call_atempt?$avg_call_atempt:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_attempt_complete?$avg_attempt_complete:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_attempt_contact?$avg_attempt_contact:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_AtemptPerHour?$avg_AtemptPerHour:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($total_complete?$total_complete:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_complete_penetrate?$avg_complete_penetrate:0)." %</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_CompletePerHour?$avg_CompletePerHour:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($total_callback?$total_callback:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_CallbacksPerHour?$avg_CallbacksPerHour:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_callback_left?$avg_callback_left:0)." %</td>
				<td nowrap class=\"total middle\" align=\"right\">".($total_contact?$total_contact:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_contact_complete?$avg_contact_complete:0)." %</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_ContactPerHour?$avg_ContactPerHour:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($total_not_interest?$total_not_interest:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($total_miss_cust?$total_miss_cust:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($total_npu?$total_npu:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($total_pif?$total_pif:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".formatRupiah(($avg_aarp?$avg_aarp:0))."</td>
				<td nowrap class=\"total middle\" align=\"right\">".formatRupiah(($total_anp?$total_anp:0))."</td>
				<td nowrap class=\"total middle\" align=\"right\">".formatRupiah(($avg_premium?$avg_premium:0))."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_call_contact?$avg_call_contact:0)." %</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_sales_close?$avg_sales_close:0)." %</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_response?$avg_response:0)." %</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_SalesPerHour?$avg_SalesPerHour:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($total_login_hours?toDuration($total_login_hours):0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($total_agent?$total_agent:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_tots_agent_worked?$avg_tots_agent_worked:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_tots_agent_busy?$avg_tots_agent_busy:0)." %</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_tots_agent_ready?$avg_tots_agent_ready:0)." %</td>
				<td nowrap class=\"total lasted\" align=\"right\">".($avg_tots_agent_notready?$avg_tots_agent_notready:0)." %</td>
				</tr></table><br/>";	 
				
		//$this -> _Excel_Footer();	// show foter 			
}


/**
 ** _weeklyReviewByCampaign
 **/ 
 
 function _weeklyReviewByCampaign()
 {
	$start_date = $this -> getStartDate();
	$end_date = $this -> getEndDate();
	$DataSize = 0;
	
	foreach( $this -> _CampaignName() as $key => $CampaignId ) 
	{
		$_conts = $this -> _query_campaign($CampaignId);
		echo "<h4>{$_conts -> result_get_value('CampaignName')}</h4>
			 <table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\">
				<tr>
					<td rowspan='2' nowrap class=\"header first\" align=\"center\">Weekly</td>
					<td colspan='20' nowrap class=\"header middle\" align=\"center\">Contact Performance</td>
					<td colspan='8' nowrap class=\"header middle\" align=\"center\">Sales & Metrics</td>
					<td colspan='6' nowrap class=\"header middle\" align=\"center\">Agents & Hours</td>
				</tr>
				<tr>
					<td nowrap class=\"header middle\" align=\"center\">Database</td>
					<td nowrap class=\"header middle\" align=\"center\">Solicited</td>
					<td nowrap class=\"header middle\" align=\"center\">Solicited Rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Attempt</td>
					<td nowrap class=\"header middle\" align=\"center\">Attempt Ratio</td>
					<td nowrap class=\"header middle\" align=\"center\">Attempt per Complete</td>
					<td nowrap class=\"header middle\" align=\"center\">Attempt per Contact</td>
					<td nowrap class=\"header middle\" align=\"center\">Atempt per Hour</td>
					<td nowrap class=\"header middle\" align=\"center\">Completes</td>
					<td nowrap class=\"header middle\" align=\"center\">Completes Penetration %</td>
					<td nowrap class=\"header middle\" align=\"center\">Completes per Hour</td>
					<td nowrap class=\"header middle\" align=\"center\">Callbacks</td>
					<td nowrap class=\"header middle\" align=\"center\">Callbacks per Hour</td>
					<td nowrap class=\"header middle\" align=\"center\">Callbacks Left %</td>
					<td nowrap class=\"header middle\" align=\"center\">Contact</td>
					<td nowrap class=\"header middle\" align=\"center\">Contacts per Complete %</td>
					<td nowrap class=\"header middle\" align=\"center\">Contact per Hour</td>
					<td nowrap class=\"header middle\" align=\"center\">Not Interest</td>
					<td nowrap class=\"header middle\" align=\"center\">Miss Customers</td>
					<td nowrap class=\"header middle\" align=\"center\">NPU</td>
					<td nowrap class=\"header middle\" align=\"center\">PIF</td>
					<td nowrap class=\"header middle\" align=\"center\">AARP</td>
					<td nowrap class=\"header middle\" align=\"center\">ANP</td>
					<td nowrap class=\"header middle\" align=\"center\">Average Premium</td>
					<td nowrap class=\"header middle\" align=\"center\">Contact Rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Sales Close Rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Response Rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Sales per Hour</td>
					<td nowrap class=\"header middle\" align=\"center\">Log In Hours</td>
					<td nowrap class=\"header middle\" align=\"center\">Agents</td>
					<td nowrap class=\"header middle\" align=\"center\">Average Worked Hours</td>
					<td nowrap class=\"header middle\" align=\"center\">Talk Time %</td>
					<td nowrap class=\"header middle\" align=\"center\">Wait Time %</td>
					<td nowrap class=\"header lasted\" align=\"center\">Wrap Time %</td>
				</tr>";
					
	/** 
	 ** get summarry allocation database on campaign
	 ** with spesifid campaign  
     **/	 
		
		
		$sql = " SELECT COUNT(distinct a.CustomerId) as tots 
				 FROM t_gn_customer a 
				 left join t_gn_campaign b on a.CampaignId
				 WHERE a.CampaignId='$CampaignId'";
		
		$qry = $this -> query($sql);
		if( !$qry -> EOF() ){
			$DataSize = $qry -> result_get_value('tots');	
		}
		
	/** get agent followup this data 
	 ** regiter LOCAL Indicated
	 **/
	 
		$AgentId = array();
		
		$sql = " SELECT  distinct a.CreatedById as UserId 
				from t_gn_callhistory a 
				left join t_gn_customer b on a.CustomerId=b.CustomerId
				LEFT JOIN tms_agent c on a.CreatedById=c.UserId
				where b.CampaignId='$CampaignId'
				and date(a.CallHistoryCreatedTs)>='$start_date'
				and date(a.CallHistoryCreatedTs)<='$end_date'
				and c.handling_type IN('".USER_TELESALES."') ";
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows ){
			$AgentId[] = $rows['UserId'];
		}			
		
	/** login agent on campaignid
	 ** by periode definition $start_date to $end_date 
	 **/
	 
		$TotLoginHour = array();
		$TotReady = array();
		$TotNotReady = array();
		$TotACW = array();
		$TotBusy = array();
		
		$sql = " SELECT WEEK(a.start_time) as weekly,
				 SUM( IF(a.`status` IN (1,2,3,4),(UNIX_TIMESTAMP(a.end_time)-UNIX_TIMESTAMP(a.start_time)),0)) as tots,
				 SUM( IF(a.`status`=1,(UNIX_TIMESTAMP(a.end_time)-UNIX_TIMESTAMP(a.start_time)), 0)) as Ready,
				 SUM( IF(a.`status`=2,(UNIX_TIMESTAMP(a.end_time)-UNIX_TIMESTAMP(a.start_time)), 0)) as NotReady,
				 SUM( IF(a.`status`=3,(UNIX_TIMESTAMP(a.end_time)-UNIX_TIMESTAMP(a.start_time)), 0)) as ACW,
				 SUM( IF(a.`status`=4,(UNIX_TIMESTAMP(a.end_time)-UNIX_TIMESTAMP(a.start_time)), 0)) as Busy
			FROM cc_agent_activity_log a 
			LEFT JOIN cc_agent b on a.agent=b.id
			LEFT JOIN tms_agent c on b.userid=c.id
			WHERE date(a.start_time)>='$start_date'
			AND date(a.start_time)<='$end_date'
			AND a.`status` IN(1,2,3,4)
			AND c.UserId IN('".implode("','",$AgentId)."')
			GROUP BY weekly ";
		
		$qry  = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$TotLoginHour[$rows['weekly']]+= $rows['tots'];
			$TotReady[$rows['weekly']]+= $rows['Ready'];
			$TotNotReady[$rows['weekly']]	+= $rows['NotReady'];
			$TotACW[$rows['weekly']] += $rows['ACW'];
			$TotBusy[$rows['weekly']]	+= $rows['Busy'];
		}	
			
	/**
     ** get atempt call data **
     ** return data <size >
	 **/	 
	 
		$size_atempt = array();
		$Complete = array();
		$NoPickUp = array();
		$CallBack = array();
		$Misscustomers = array();
		$Agents = array();
		$totSolicited = array();
		
		$sql = "SELECT 
				count(distinct a.CustomerId) as Utilize , 
				COUNT(distinct a.CreatedById) as agents,
				COUNT(a.CustomerId) as Atempt, 
				SUM(IF(a.CallReasonId IN(".CONTACT_STATUS."),1,0)) AS Contact,
				SUM(IF(a.CallReasonId IN(".NOPICKUP_STATUS."),1,0)) as NoPickUp,
				SUM(IF(a.CallReasonId IN(".COMPLETE_STATUS."),1,0)) as Complete,
				SUM(IF(a.CallReasonId IN(".CALLBACK_STATUS."),1,0)) as Callback,
				SUM(IF(a.CallReasonId IN(".MISSCUSTOMER_STATUS."),1,0)) as Misscustomers,
				WEEK(a.CallHistoryCreatedTs) as tgl
				FROM t_gn_callhistory a
				LEFT JOIN t_gn_customer b on a.CustomerId=b.CustomerId
				LEFT JOIN t_lk_callreason c on a.CallReasonId = c.CallReasonId
				WHERE b.CampaignId='$CampaignId'
				AND DATE(a.CallHistoryCallDate)>='$start_date'
				AND DATE(a.CallHistoryCallDate)<='$end_date'
				AND date(a.CallHistoryCreatedTs)>='$start_date'
				AND date(a.CallHistoryCreatedTs)<='$end_date'
				GROUP BY tgl ";
		
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$totSolicited[$rows['tgl']]	 += $rows['Utilize'];
			$size_atempt[$rows['tgl']]	 += $rows['Atempt'];
			$SizeContact[$rows['tgl']]	 += $rows['Contact'];
			$Complete[$rows['tgl']]		 += $rows['Complete'];
			$NoPickUp[$rows['tgl']]		 += $rows['NoPickUp'];
			$CallBack[$rows['tgl']]		 += $rows['Callback'];
			$Misscustomers[$rows['tgl']] += $rows['Misscustomers'];
			$Agents[$rows['tgl']]		 += $rows['agents'];
		}
		
	/** 
	 ** get interest policy status 
	 ** get on customer data 
	 **/
		
		$NotInterest = array(); 
		$sql = " SELECT COUNT(a.CustomerId) as cnt,
				 SUM(IF(a.CallReasonId IN(".NOTINTEREST_STATUS."),1,0)) as NotInterest,
				 WEEK(a.CustomerUpdatedTs) as tgl
				 FROM t_gn_customer a 
				 WHERE a.CampaignId='$CampaignId'
				 GROUP BY tgl ";
		
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{	
			$NotInterest[$rows['tgl']]+= $rows['NotInterest'];
		}
	
	
	/**
     ** get Sales & Metrics **
     ** return data <size >
	 **/	 
	 
		$size_pif = array();
		$size_anp = array();
		$sql = "SELECT 
					COUNT(DISTINCT a.CustomerId) AS PIF,
					SUM(IF(e.PayModeId=2,(b.Premi*12), b.Premi)) AS ANP,
					WEEK(f.CallHistoryCreatedTs) as tgl
				FROM t_gn_policyautogen a
					LEFT JOIN t_gn_policy b ON a.PolicyNumber=b.PolicyNumber
					LEFT JOIN t_gn_customer c ON a.CustomerId=c.CustomerId
					LEFT JOIN t_gn_assignment d ON c.CustomerId=d.CustomerId
					LEFT JOIN t_gn_productplan e ON b.ProductPlanId=e.ProductPlanId
					LEFT JOIN t_gn_callhistory f ON c.CustomerId = f.CustomerId
				WHERE 1=1
					AND DATE(b.PolicySalesDate)>= '$start_date'
					AND DATE(b.PolicySalesDate)<= '$end_date'
					AND c.CampaignId ='$CampaignId'
					AND c.CallReasonId IN(".INTEREST_STATUS.")
				GROUP BY tgl ";
		
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$size_pif[$rows['tgl']]+= $rows['PIF'];
			$size_anp[$rows['tgl']]+= $rows['ANP'];
		}
	
		 
	/** while true data looping date 
	 ** by date filter 
	 */	
	 
	 $total_solicited = 0;
	 $total_atempt = 0;
	 $total_callback = 0;
	 $total_complete = 0;
	 $total_contact = 0;
	 $total_nopickup = 0;
	
	 foreach( self::_Weekly_List() as $k => $s_d ) 
	 {
		$PercentUtilize 	= ROUND((($totSolicited[$s_d]/$DataSize) * 100),2);
		$RatioAtempt 		= ROUND(($size_atempt[$s_d]/$totSolicited[$s_d]),2);
		$AttemptPerComplete	= ROUND(($size_atempt[$s_d] / $Complete[$s_d]),2);
		$AttemptPerContact	= ROUND(($size_atempt[$s_d] / $SizeContact[$s_d]),2);
		$AvgPresentation	= ROUND((($Complete[$s_d] / $DataSize) * 100),2);
		$CallbackLeft		= ROUND((($CallBack[$s_d] / $DataSize) * 100),2);
		$ContactPerComplete	= ROUND((($Complete[$s_d] / $SizeContact[$s_d]) * 100),2);
		$AARP				= ($size_anp[$s_d] / $size_pif[$s_d]);
		$AvgPremi			= ($AARP / 12);
		$ContactRate		= ROUND((($SizeContact[$s_d] / $totSolicited[$s_d]) * 100),2);
		$SalesClose			= ROUND((($size_pif[$s_d] / $SizeContact[$s_d]) * 100),2);
		$ResponseRate		= ROUND((($size_pif[$s_d] / $DataSize) * 100),2);
		
		$AtemptPerHour 		= ROUND(($size_atempt[$s_d]/self::_Duration($TotLoginHour[$s_d])),2);
		$CompletePerHour 	= ROUND(($Complete[$s_d]/self::_Duration($TotLoginHour[$s_d])),2);
		$CallbacksPerHour 	= ROUND(($CallBack[$s_d]/self::_Duration($TotLoginHour[$s_d])),2);
		$ContactPerHour 	= ROUND(($SizeContact[$s_d]/self::_Duration($TotLoginHour[$s_d])),2);
		$SalesPerHour 		= ROUND(($size_pif[$s_d]/self::_Duration($TotLoginHour[$s_d])),3);
		$avg_agent_worked	= ROUND((self::_Duration($TotLoginHour[$s_d])/$Agents[$s_d]),2); 
		
		$avg_agent_ready	= ROUND((($TotReady[$s_d]/$TotLoginHour[$s_d])*100),2);
		$avg_agent_notready = ROUND((($TotNotReady[$s_d]/$TotLoginHour[$s_d])*100),2); 
		$avg_agent_busy		= ROUND((($TotBusy[$s_d]/$TotLoginHour[$s_d])*100),2);
		
			echo "<tr>
					<td nowrap class=\"content first\" align=\"center\">Week ".($s_d+1)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($DataSize?$DataSize:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totSolicited[$s_d]?$totSolicited[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($PercentUtilize?$PercentUtilize:0)." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($size_atempt[$s_d]?$size_atempt[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($RatioAtempt?$RatioAtempt:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($AttemptPerComplete?$AttemptPerComplete:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($AttemptPerContact?$AttemptPerContact:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($AtemptPerHour?$AtemptPerHour:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($Complete[$s_d]?$Complete[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($AvgPresentation?$AvgPresentation:0)." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($CompletePerHour?$CompletePerHour:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($CallBack[$s_d]?$CallBack[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($CallbacksPerHour?$CallbacksPerHour:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($CallbackLeft?$CallbackLeft:0)." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($SizeContact[$s_d]?$SizeContact[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($ContactPerComplete?$ContactPerComplete:0)." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($ContactPerHour?$ContactPerHour:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($NotInterest[$s_d]?$NotInterest[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($Misscustomers[$s_d]?$Misscustomers[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($NoPickUp[$s_d]?$NoPickUp[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($size_pif[$s_d]?$size_pif[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".formatRupiah(($AARP?$AARP:0))."</td>
					<td nowrap class=\"content middle\" align=\"right\">".formatRupiah(($size_anp[$s_d]?$size_anp[$s_d]:0))."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($AvgPremi)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($ContactRate?$ContactRate:0)." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($SalesClose?$SalesClose:0)." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($ResponseRate?$ResponseRate:0)." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($SalesPerHour?$SalesPerHour:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".(toDuration($TotLoginHour[$s_d])?toDuration($TotLoginHour[$s_d]):0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($Agents[$s_d]?$Agents[$s_d]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($avg_agent_worked?$avg_agent_worked:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($avg_agent_busy?$avg_agent_busy:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($avg_agent_ready?$avg_agent_ready:0)."</td>
					<td nowrap class=\"content lasted\" align=\"right\">".($avg_agent_notready?$avg_agent_notready:0)."</td>
				  </tr>";
		
		
			/** 
			 ** calculation footer 
			 ** for next step 
			 **/
					 
			$total_datasize = $DataSize;
			$total_solicited += $totSolicited[$s_d];
			$total_atempt += $size_atempt[$s_d];
			$total_callback += $CallBack[$s_d];
			$total_complete += $Complete[$s_d];
			$total_contact += $SizeContact[$s_d];
			$total_nopickup += $Complete[$s_d];
			$total_not_interest += $NotInterest[$s_d];
			$total_miss_cust += $Misscustomers[$s_d];
			$total_npu += $NoPickUp[$s_d];
			$total_pif += $size_pif[$s_d];
			$total_anp += $size_anp[$s_d];
			$total_agent += $Agents[$s_d];
			
			$total_login_hours += $TotLoginHour[$s_d];
			$total_login_ready += $TotReady[$s_d];
			$total_login_notready += $TotNotReady[$s_d];
			$total_login_busy += $TotBusy[$s_d];
			 
		}
		
		/** hitung rata-rata
		 ** solicted data dbase
		**/
		
		$avg_call_utilize   	= ROUND((($total_solicited/$DataSize)*100),2);
		$avg_call_atempt    	= ROUND((($total_atempt/$total_solicited)),2);
		$avg_attempt_complete	= ROUND(($total_atempt / $total_complete),2);
		$avg_attempt_contact	= ROUND(($total_atempt / $total_contact),2);
		$avg_complete_penetrate	= ROUND((($total_complete / $DataSize) * 100),2);
		$avg_callback_left		= ROUND((($total_callback / $DataSize) * 100),2);
		$avg_contact_complete	= ROUND((($total_complete / $total_contact) * 100),2);
		$avg_aarp				= ($total_anp / $total_pif);
		$avg_premium			= ($avg_aarp / 12);
		$avg_call_contact   	= ROUND((($total_contact/$total_solicited)*100),2);
		$avg_sales_close		= ROUND((($total_pif / $total_contact) * 100),2);
		$avg_response			= ROUND((($total_pif / $DataSize) * 100),2);
		$avg_call_complete  	= ROUND((($total_complete/$total_solicited)*100),2);
		$avg_call_pickup    	= ROUND((($total_nopickup/$total_solicited)*100),2);
		$avg_call_callback  	= ROUND((($total_callback/$total_solicited)*100),2);
		
		$avg_tots_agent_worked	= ROUND((self::_Duration($total_login_hours)/$total_agent),2); 
		$avg_tots_agent_ready 	= ROUND((($total_login_ready/$total_login_hours)*100),2);
		$avg_tots_agent_notready= ROUND((($total_login_notready/$total_login_hours)*100),2); 
		$avg_tots_agent_busy 	= ROUND((($total_login_busy/$total_login_hours)*100),2); 
			
		$avg_AtemptPerHour 		= ROUND(($total_atempt/self::_Duration($total_login_hours)),2);
		$avg_CompletePerHour 	= ROUND(($total_complete/self::_Duration($total_login_hours)),2);
		$avg_CallbacksPerHour 	= ROUND(($total_callback/self::_Duration($total_login_hours)),2);
		$avg_ContactPerHour 	= ROUND(($total_contact/self::_Duration($total_login_hours)),2);
		$avg_SalesPerHour 		= ROUND(($total_pif/self::_Duration($total_login_hours)),3);
				
		echo "<tr>
				<td colspan='35' nowrap class=\"total first\" align=\"center\"></td>
			</tr></table><br/> ";
		}
		
	//$this -> _Excel_Footer();	// show foter 			
 }
	
/**
 ** summaryPerfomanceByCampaign
**/	

function _dailyReviewByCampaign()
{
	$DataSize = 0;
	$start_date = $this -> getStartDate();
	$end_date = $this -> getEndDate();
	foreach( $this -> _CampaignName() as $key => $CampaignId ) 
	{
		$_conts = $this -> _query_campaign($CampaignId);
		echo "<h4>{$_conts -> result_get_value('CampaignName')}</h4> 
			  <table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\">
				<tr>
					<td rowspan='2' nowrap class=\"header first\" align=\"center\">Date</td>
					<td colspan='20' nowrap class=\"header middle\" align=\"center\">Contact Performance</td>
					<td colspan='8' nowrap class=\"header middle\" align=\"center\">Sales & Metrics</td>
					<td colspan='6' nowrap class=\"header middle\" align=\"center\">Agents & Hours</td>
				</tr>
				<tr>
					<td nowrap class=\"header middle\" align=\"center\">Database</td>
					<td nowrap class=\"header middle\" align=\"center\">Solicited</td>
					<td nowrap class=\"header middle\" align=\"center\">Solicited Rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Attempt</td>
					<td nowrap class=\"header middle\" align=\"center\">Attempt Ratio</td>
					<td nowrap class=\"header middle\" align=\"center\">Attempt per Complete</td>
					<td nowrap class=\"header middle\" align=\"center\">Attempt per Contact</td>
					<td nowrap class=\"header middle\" align=\"center\">Atempt per Hour</td>
					<td nowrap class=\"header middle\" align=\"center\">Completes</td>
					<td nowrap class=\"header middle\" align=\"center\">Completes Penetration %</td>
					<td nowrap class=\"header middle\" align=\"center\">Completes per Hour</td>
					<td nowrap class=\"header middle\" align=\"center\">Callbacks</td>
					<td nowrap class=\"header middle\" align=\"center\">Callbacks per Hour</td>
					<td nowrap class=\"header middle\" align=\"center\">Callbacks Left %</td>
					<td nowrap class=\"header middle\" align=\"center\">Contact</td>
					<td nowrap class=\"header middle\" align=\"center\">Contacts per Complete %</td>
					<td nowrap class=\"header middle\" align=\"center\">Contact per Hour</td>
					<td nowrap class=\"header middle\" align=\"center\">Not Interest</td>
					<td nowrap class=\"header middle\" align=\"center\">Miss Customers</td>
					<td nowrap class=\"header middle\" align=\"center\">NPU</td>
					<td nowrap class=\"header middle\" align=\"center\">PIF</td>
					<td nowrap class=\"header middle\" align=\"center\">AARP</td>
					<td nowrap class=\"header middle\" align=\"center\">ANP</td>
					<td nowrap class=\"header middle\" align=\"center\">Average Premium</td>
					<td nowrap class=\"header middle\" align=\"center\">Contact Rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Sales Close Rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Response Rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Sales per Hour</td>
					<td nowrap class=\"header middle\" align=\"center\">Log In Hours</td>
					<td nowrap class=\"header middle\" align=\"center\">Agents</td>
					<td nowrap class=\"header middle\" align=\"center\">Average Worked Hours</td>
					<td nowrap class=\"header middle\" align=\"center\">Talk Time %</td>
					<td nowrap class=\"header middle\" align=\"center\">Wait Time %</td>
					<td nowrap class=\"header lasted\" align=\"center\">Wrap Time %</td>
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
			
	/** get agent followup this data 
	 ** regiter LOCAL Indicated
	 **/
	 
		$AgentId = array();
		
		$sql = " SELECT  distinct a.CreatedById as UserId 
				from t_gn_callhistory a 
				left join t_gn_customer b on a.CustomerId=b.CustomerId
				LEFT JOIN tms_agent c on a.CreatedById=c.UserId
				where b.CampaignId='$CampaignId'
				and date(a.CallHistoryCreatedTs)>='$start_date'
				and date(a.CallHistoryCreatedTs)<='$end_date'
				and c.handling_type IN('".USER_TELESALES."') ";
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows ){
			$AgentId[] = $rows['UserId'];
		}			
		
	/** login agent on campaignid
	 ** by periode definition $start_date to $end_date 
	 **/
	 
		$TotLoginHour = array();
		$TotReady = array();
		$TotNotReady = array();
		$TotACW = array();
		$TotBusy = array();
		
		$sql = " SELECT date(a.start_time) as tgl,
				 SUM( IF(a.`status` IN (1,2,3,4),(UNIX_TIMESTAMP(a.end_time)-UNIX_TIMESTAMP(a.start_time)),0)) as tots,
				 SUM( IF(a.`status`=1,(UNIX_TIMESTAMP(a.end_time)-UNIX_TIMESTAMP(a.start_time)), 0)) as Ready,
				 SUM( IF(a.`status`=2,(UNIX_TIMESTAMP(a.end_time)-UNIX_TIMESTAMP(a.start_time)), 0)) as NotReady,
				 SUM( IF(a.`status`=3,(UNIX_TIMESTAMP(a.end_time)-UNIX_TIMESTAMP(a.start_time)), 0)) as ACW,
				 SUM( IF(a.`status`=4,(UNIX_TIMESTAMP(a.end_time)-UNIX_TIMESTAMP(a.start_time)), 0)) as Busy
			FROM cc_agent_activity_log a 
			LEFT JOIN cc_agent b on a.agent=b.id
			LEFT JOIN tms_agent c on b.userid=c.id
			WHERE date(a.start_time)>='$start_date'
			AND date(a.start_time)<='$end_date'
			AND a.`status` IN(1,2,3,4)
			AND c.UserId IN('".implode("','",$AgentId)."')
			GROUP BY tgl ";
		
		$qry  = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$TotLoginHour[$rows['tgl']]+= $rows['tots'];
			$TotReady[$rows['tgl']]+= $rows['Ready'];
			$TotNotReady[$rows['tgl']]	+= $rows['NotReady'];
			$TotACW[$rows['tgl']] += $rows['ACW'];
			$TotBusy[$rows['tgl']]	+= $rows['Busy'];
		}	
			
			
	/**
     ** get atempt call data **
     ** return data <size >
	 **/	 
		$size_atempt = array();
		$Complete = array();
		$NoPickUp = array();
		$CallBack = array();
		$Misscustomers = array();
		$Agents = array();
		
		
		$sql = "SELECT 
				COUNT(distinct a.CustomerId) as Utilize, 
				COUNT(distinct a.CreatedById) as agents,
				COUNT(a.CustomerId) as Atempt, 
				SUM(IF(a.CallReasonId IN(".CONTACT_STATUS."),1,0)) AS Contact,
				SUM(IF(a.CallReasonId IN(".NOPICKUP_STATUS."),1,0)) as NoPickUp,
				SUM(IF(a.CallReasonId IN(".CALLBACK_STATUS."),1,0)) as Callback,
				SUM(IF(a.CallReasonId IN(".MISSCUSTOMER_STATUS."),1,0)) as Misscustomers,
				SUM(IF(a.CallReasonId IN(".COMPLETE_STATUS."),1,0)) as Complete,
				DATE(a.CallHistoryCreatedTs) as tgl
				FROM t_gn_callhistory a
				LEFT JOIN t_gn_customer b on a.CustomerId=b.CustomerId
				WHERE b.CampaignId='$CampaignId'
				AND DATE(a.CallHistoryCallDate)>='$start_date'
				AND DATE(a.CallHistoryCallDate)<='$end_date'
				AND date(a.CallHistoryCreatedTs)>='$start_date'
				AND date(a.CallHistoryCreatedTs)<='$end_date'
				GROUP BY tgl ";
		
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$totSolicited[$rows['tgl']]+= $rows['Utilize'];
			$size_atempt[$rows['tgl']]+= $rows['Atempt'];
			$Misscustomers[$rows['tgl']]+= $rows['Misscustomers'];
			$Agents[$rows['tgl']]+= $rows['agents'];
		}
		
	/** 
	 ** get interest policy status 
	 ** get on customer data 
	 **/
		
		$NotInterest = array(); 
		$sql = " SELECT COUNT(a.CustomerId) as cnt,
				 SUM(IF(a.CallReasonId IN(".NOTINTEREST_STATUS."),1,0)) as NotInterest,
				 DATE(a.CustomerUpdatedTs) as tgl
				 FROM t_gn_customer a 
				 WHERE a.CampaignId='$CampaignId'
				 GROUP BY tgl ";
		
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{	
			$NotInterest[$rows['tgl']]+= $rows['NotInterest'];
		}
		
	/**
     ** get Sales & Metrics **
     ** return data <size >
	 **/	 
		$size_pif = array();
		$size_anp = array();
		
		
		$sql = " select date(c.PolicySalesDate) as tgl ,
					count(distinct b.PolicyNumber) as PIF, 
					SUM(IF(d.PayModeId=2,(c.Premi*12), c.Premi)) AS ANP
					from t_gn_customer a 
					left join t_gn_policyautogen b on a.CustomerId=b.CustomerId
					left join t_gn_policy c on b.PolicyNumber=c.PolicyNumber
					right join t_gn_productplan d on d.ProductPlanId=c.ProductPlanId
					where a.CampaignId='$CampaignId'
					AND DATE(c.PolicySalesDate)>= '$start_date'
					AND DATE(c.PolicySalesDate)<= '$end_date'
					and b.PolicyNumber is not null
					and a.CallReasonId IN(".INTEREST_STATUS.")
					group by tgl ";	
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$size_pif[$rows['tgl']]+= $rows['PIF'];
			$size_anp[$rows['tgl']]+= $rows['ANP'];
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
	 $total_nopickup = 0;
	 $AARP=0;
	 while(true)
	 {
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
		$AttemptPerComplete	= ROUND(($size_atempt[$s_d]/$Complete[$s_d]),2);
		$AttemptPerContact	= ROUND(($size_atempt[$s_d]/$SizeContact[$s_d]),2);
		$AvgPresentation	= ROUND((($Complete[$s_d]/$DataSize) * 100),2);
		$CallbackLeft		= ROUND((($CallBack[$s_d]/$DataSize) * 100),2);
		$ContactPerComplete	= ROUND((($Complete[$s_d]/$SizeContact[$s_d] ) * 100),2);
		$AARP				= ($size_anp[$s_d]/$size_pif[$s_d]);
		$AvgPremi			= ($AARP/12);
		$ContactRate		= ROUND((($SizeContact[$s_d]/$totSolicited[$s_d]) * 100),2);
		$SalesClose			= ROUND((($size_pif[$s_d]/$SizeContact[$s_d]) * 100),2);
		$ResponseRate		= ROUND((($size_pif[$s_d]/$DataSize) * 100),2);
		$AtemptPerHour 		= ROUND(($size_atempt[$s_d]/self::_Duration($TotLoginHour[$s_d])),2);
		$CompletePerHour 	= ROUND(($Complete[$s_d]/self::_Duration($TotLoginHour[$s_d])),2);
		$CallbacksPerHour 	= ROUND(($CallBack[$s_d]/self::_Duration($TotLoginHour[$s_d])),2);
		$ContactPerHour 	= ROUND(($SizeContact[$s_d]/self::_Duration($TotLoginHour[$s_d])),2);
		$SalesPerHour 		= ROUND(($size_pif[$s_d]/self::_Duration($TotLoginHour[$s_d])),3);
		$avg_agent_worked	= ROUND((self::_Duration($TotLoginHour[$s_d])/$Agents[$s_d]),2); 
		$avg_agent_ready	= ROUND((($TotReady[$s_d]/$TotLoginHour[$s_d])*100),2);
		$avg_agent_notready = ROUND((($TotNotReady[$s_d]/$TotLoginHour[$s_d])*100),2); 
		$avg_agent_busy		= ROUND((($TotBusy[$s_d]/$TotLoginHour[$s_d])*100),2);
		
		
		$color 				= ($this -> __week_days($s_d)?'#dddeee':'');
		
				echo "<tr>
						<td height=26 class=\"content first\" nowrap>".$this -> formatDateId($s_d)."</td>
						<td class=\"content middle\" align=\"right\">".($DataSize?$DataSize:0)."</td>
						<td class=\"content middle\" align=\"right\">".($totSolicited[$s_d]?$totSolicited[$s_d]:0)."</td>
						<td class=\"content middle\" align=\"right\">".($PercentUtilize?$PercentUtilize:0)." %</td>
						<td class=\"content middle\" align=\"right\">".($size_atempt[$s_d]?$size_atempt[$s_d]:0)."</td>
						<td class=\"content middle\" align=\"right\">".($RatioAtempt?$RatioAtempt:0)."</td>
						<td class=\"content middle\" align=\"right\">".($AttemptPerComplete?$AttemptPerComplete:0)."</td>
						<td class=\"content middle\" align=\"right\">".($AttemptPerContact?$AttemptPerContact:0)."</td>
						<td class=\"content middle\" align=\"right\">".($AtemptPerHour?$AtemptPerHour:0)."</td>
						<td class=\"content middle\" align=\"right\">".($Complete[$s_d]?$Complete[$s_d]:0)."</td>
						<td class=\"content middle\" align=\"right\">".($AvgPresentation?$AvgPresentation:0)." %</td>
						<td class=\"content middle\" align=\"right\">".($CompletePerHour?$CompletePerHour:0)."</td>
						<td class=\"content middle\" align=\"right\">".($CallBack[$s_d]?$CallBack[$s_d]:0)."</td>
						<td class=\"content middle\" align=\"right\">".($CallbacksPerHour?$CallbacksPerHour:0)."</td>
						<td class=\"content middle\" align=\"right\">".($CallbackLeft?$CallbackLeft:0)." %</td>
						<td class=\"content middle\" align=\"right\">".($SizeContact[$s_d]?$SizeContact[$s_d]:0)."</td>
						<td class=\"content middle\" align=\"right\">".($ContactPerComplete?$ContactPerComplete:0)." %</td>
						<td class=\"content middle\" align=\"right\">".($ContactPerHour?$ContactPerHour:0)."</td>
						<td class=\"content middle\" align=\"right\">".($NotInterest[$s_d]?$NotInterest[$s_d]:0)."</td>
						<td class=\"content middle\" align=\"right\">".($Misscustomers[$s_d]?$Misscustomers[$s_d]:0)."</td>
						<td class=\"content middle\" align=\"right\">".($NoPickUp[$s_d]?$NoPickUp[$s_d]:0)."</td>
						<td class=\"content middle\" align=\"right\">".($size_pif[$s_d]?$size_pif[$s_d]:0)."</td>
						<td class=\"content middle\" align=\"right\">".($AARP?formatRupiah($AARP):0)."</td>
						<td class=\"content middle\" align=\"right\">".($size_anp[$s_d]?formatRupiah($size_anp[$s_d]):0)."</td>
						<td class=\"content middle\" align=\"right\">".formatRupiah($AvgPremi)."</td>
						<td class=\"content middle\" align=\"right\">".($ContactRate?$ContactRate:0)." %</td>
						<td class=\"content middle\" align=\"right\">".($SalesClose?$SalesClose:0)." %</td>
						<td class=\"content middle\" align=\"right\">".($ResponseRate?$ResponseRate:0)." %</td>
						<td class=\"content middle\" align=\"right\">".($SalesPerHour?$SalesPerHour:0)."</td>
						<td class=\"content middle\" align=\"right\">".(toDuration($TotLoginHour[$s_d])?toDuration($TotLoginHour[$s_d]):0)."</td>
						<td class=\"content middle\" align=\"right\">".($Agents[$s_d]?$Agents[$s_d]:0)."</td>
						<td class=\"content middle\" align=\"right\">".($avg_agent_worked?$avg_agent_worked:0)."</td>
						<td class=\"content middle\" align=\"right\">".($avg_agent_busy?$avg_agent_busy:0)."</td>
						<td class=\"content middle\" align=\"right\">".($avg_agent_ready?$avg_agent_ready:0)."</td>
						<td class=\"content lasted\" align=\"right\">".($avg_agent_notready?$avg_agent_notready:0)."</td>
				  </tr>";
					
			/** 
			 ** calculation footer 
			 ** for next step 
			 **/
				$total_atempt += $size_atempt[$s_d];
				$total_pif += $size_pif[$s_d];
				$total_anp += $size_anp[$s_d];
				$total_agent += $Agents[$s_d];
				$total_login_hours += $TotLoginHour[$s_d];
				$total_login_ready += $TotReady[$s_d];
				$total_login_notready += $TotNotReady[$s_d];
				$total_login_busy += $TotBusy[$s_d];
			
				if( $start_date == $end_date ) break;
					$start_date = $this -> nextDate($start_date);
					
			}
		
	/** distinct process ***/
	
		$DataSizeUtil = array();	
		$Complete = array();
		$NoPickUp = array();
		$CallBack = array();
		$Misscustomers = array();
		$Agents = array();
		$totSolicited = array();
		$NotInterest = array(); 
		
		/** hitung rata-rata
		 ** solicted data dbase
		**/
				$DataSizeUtil 			= self::_summaryByDaily($CampaignId);
				
				$avg_call_utilize   	= ROUND((($DataSizeUtil[$CampaignId]['totSolicited']/$DataSizeUtil[$CampaignId]['DataSize'])*100),2);
				$avg_call_atempt    	= ROUND((($total_atempt/$DataSizeUtil[$CampaignId]['totSolicited'])),2);
				$avg_attempt_complete	= ROUND(($total_atempt/$DataSizeUtil[$CampaignId]['Complete']),2);
				$avg_attempt_contact	= ROUND(($total_atempt/$DataSizeUtil[$CampaignId]['SizeContact']),2);
				$avg_complete_penetrate	= ROUND((($DataSizeUtil[$CampaignId]['Complete']/ $DataSizeUtil[$CampaignId]['DataSize']) * 100),2);
				$avg_callback_left		= ROUND((($DataSizeUtil[$CampaignId]['CallBack']/ $DataSizeUtil[$CampaignId]['DataSize']) * 100),2);
				$avg_contact_complete	= ROUND((($DataSizeUtil[$CampaignId]['Complete']/$DataSizeUtil[$CampaignId]['SizeContact']) * 100),2);
				
				$avg_aarp				= ($total_anp / $total_pif);
				$avg_premium			= ($avg_aarp / 12);
				$avg_call_contact   	= ROUND((($DataSizeUtil[$CampaignId]['SizeContact']/$DataSizeUtil[$CampaignId]['totSolicited'])*100),2);
				$avg_sales_close		= ROUND((($total_pif/$DataSizeUtil[$CampaignId]['SizeContact']) * 100),2);
				$avg_response			= ROUND((($total_pif/ $DataSizeUtil[$CampaignId]['DataSize']) * 100),2);
				$avg_call_complete  	= ROUND((($DataSizeUtil[$CampaignId]['Complete']/$DataSizeUtil[$CampaignId]['totSolicited'])*100),2);
				$avg_call_pickup    	= ROUND((($DataSizeUtil[$CampaignId]['NoPickUp']/$DataSizeUtil[$CampaignId]['totSolicited'])*100),2);
				$avg_call_callback  	= ROUND((($DataSizeUtil[$CampaignId]['CallBack']/$DataSizeUtil[$CampaignId]['totSolicited'])*100),2);
				
				$avg_tots_agent_worked	= ROUND((self::_Duration($total_login_hours)/$total_agent),2); 
				$avg_tots_agent_ready 	= ROUND((($total_login_ready/$total_login_hours)*100),2);
				$avg_tots_agent_notready= ROUND((($total_login_notready/$total_login_hours)*100),2); 
				$avg_tots_agent_busy 	= ROUND((($total_login_busy/$total_login_hours)*100),2); 
					
				$avg_AtemptPerHour 		= ROUND(($total_atempt/self::_Duration($total_login_hours)),2);
				$avg_CompletePerHour 	= ROUND(($DataSizeUtil[$CampaignId]['Complete']/self::_Duration($total_login_hours)),2);
				$avg_CallbacksPerHour 	= ROUND(($DataSizeUtil[$CampaignId]['CallBack']/self::_Duration($total_login_hours)),2);
				$avg_ContactPerHour 	= ROUND(($DataSizeUtil[$CampaignId]['SizeContact']/self::_Duration($total_login_hours)),2);
				$avg_SalesPerHour 		= ROUND(($total_pif/self::_Duration($total_login_hours)),3);
				
		echo "<tr>
				<td nowrap class=\"total first\" align=\"left\">MTD</td>
				<td nowrap class=\"total middle\" align=\"right\">".($DataSizeUtil[$CampaignId]['DataSize']?$DataSizeUtil[$CampaignId]['DataSize']:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($DataSizeUtil[$CampaignId]['totSolicited']?$DataSizeUtil[$CampaignId]['totSolicited']:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_call_utilize?$avg_call_utilize:0)." %</td>
				<td nowrap class=\"total middle\" align=\"right\">".($total_atempt?$total_atempt:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_call_atempt?$avg_call_atempt:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_attempt_complete?$avg_attempt_complete:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_attempt_contact?$avg_attempt_contact:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_AtemptPerHour?$avg_AtemptPerHour:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($DataSizeUtil[$CampaignId]['Complete']?$DataSizeUtil[$CampaignId]['Complete']:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_complete_penetrate?$avg_complete_penetrate:0)." %</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_CompletePerHour?$avg_CompletePerHour:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($DataSizeUtil[$CampaignId]['CallBack']?$DataSizeUtil[$CampaignId]['CallBack']:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_CallbacksPerHour?$avg_CallbacksPerHour:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_callback_left?$avg_callback_left:0)." %</td>
				<td nowrap class=\"total middle\" align=\"right\">".($DataSizeUtil[$CampaignId]['SizeContact']?$DataSizeUtil[$CampaignId]['SizeContact']:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_contact_complete?$avg_contact_complete:0)." %</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_ContactPerHour?$avg_ContactPerHour:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($DataSizeUtil[$CampaignId]['NotInterest']?$DataSizeUtil[$CampaignId]['NotInterest']:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($DataSizeUtil[$CampaignId]['Misscustomers']?$DataSizeUtil[$CampaignId]['Misscustomers']:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($DataSizeUtil[$CampaignId]['NoPickUp']?$DataSizeUtil[$CampaignId]['NoPickUp']:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($total_pif?$total_pif:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".formatRupiah(($avg_aarp?$avg_aarp:0))."</td>
				<td nowrap class=\"total middle\" align=\"right\">".formatRupiah(($total_anp?$total_anp:0))."</td>
				<td nowrap class=\"total middle\" align=\"right\">".formatRupiah(($avg_premium?$avg_premium:0))."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_call_contact?$avg_call_contact:0)." %</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_sales_close?$avg_sales_close:0)." %</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_response?$avg_response:0)." %</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_SalesPerHour?$avg_SalesPerHour:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($total_login_hours?toDuration($total_login_hours):0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($total_agent?$total_agent:0)."</td>
				<td nowrap class=\"total middle\"\"total middle\" align=\"right\">".($avg_tots_agent_worked?$avg_tots_agent_worked:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_tots_agent_busy?$avg_tots_agent_busy:0)."</td>
				<td nowrap class=\"total middle\" align=\"right\">".($avg_tots_agent_ready?$avg_tots_agent_ready:0)."</td>
				<td nowrap class=\"total lasted\" align=\"right\">".($avg_tots_agent_notready?$avg_tots_agent_notready:0)."</td>
				
			</tr></table><br/>";
		}
		
		//$this -> _Excel_Footer();	// show foter 		
		//$this -> view_filter();
		
	}
}
?>
<!--- EOF -->