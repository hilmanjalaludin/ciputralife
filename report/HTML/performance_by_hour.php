<!-- SOF --> 
<?php

/** author < omens >
 ** project < CIGNA Insured >
 ** report available only summary report group by HTML Telesales & HTML supervisor
 ** for available other report please open remark and then crate content 
 ** under spesific function to generate
 **/

class performance_by_hour extends index
{
	var $_con;

/**
 ** @ aksesor of class 
 ** @ handle return <void>
 */
 
 function performance_by_hour(){
		$this ->_con&= null;
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
 ** toduration by (.) sparator 
 ** with format H.m
 **/

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
 ** filtering Handle On the footer 
 ** return < @void >
 */
 
function view_filter()
{
	$CampaignId = implode("','",explode(',',$this -> escPost('CampaignName')));
	echo "<h3> Notes : Filter By Campaign </h3>";
	$sql = " SELECT *
			FROM t_gn_campaign a
			WHERE a.CampaignId IN('$CampaignId') 
			AND a.CampaignStatusFlag=1  ";
	$qry = $this ->query($sql);
	
	echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\">
		<tr> 
			<td class=\"header first\">Campaign Number</td>
			<td class=\"header lasted\">Campaign name</td></tr>";
	foreach($qry -> result_assoc() as $rows )
	{
		echo "<tr> 
				<td class=\"content first\" align=\"center\">{$rows[CampaignNumber]}</td>
				<td class=\"content lasted\" align=\"left\">{$rows[CampaignName]}</td>
			</tr>	
			";
	}	
		echo "<tr> 
				<td class=\"header first\" colspan=\"2\">&nbsp;</td>
			</tr>
			</table>";
}
	
/**
 ** get group select on navigation report
 ** return < obejct:Class >
 */
 
 private function getGroupSelect()
 {
		$Spvid = $this -> escPost('group_select');
		if($Spvid!=''){
			return $this -> Users -> getUsers($Spvid);
		}
	}
	
/**
 ** get group select on navigation report
 ** return < obejct:Class >
 */
 
 private function getCampaignName()
 {
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

private function _CampaignName()
{
	$_cmp = explode(',',$_REQUEST['CampaignName']);
	return $_cmp;
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
		mysql::__construct();
		switch($_REQUEST['group_by'])
		{
			// case 'Telesales'  : $this -> PerfomanceByTelesales(); break; 
			// case 'supervisor' : $this -> PerfomanceBySupervisor(); break; 
			case 'campaign'   : $this -> PerfomanceByCampaign(); break; 
			
			default:
				echo "<h3>Sorry, Report Under maintenace</h3>";
			break;
		}
	}

/** 
 ** main content HTML PerfomanceBySupervisor 
 ** return < void >
 **/

 
private function PerfomanceByCampaign()
{
	switch($_REQUEST['mode'])
		{
			//case 'hourly'  : $this -> hourlyPerfomanceByTelesales(); break; 
			//case 'daily'   : $this -> dailyPerfomanceBySupervisor(); break; 
			case 'summary'    : $this -> summaryPerfomanceByCampaign(); break; 
			default:
				echo "<h3>Sorry, ".ucfirst($_REQUEST['mode'])." Mode Report Not Available </h3>";
			break;
		}

}
	
/** 
 ** main content HTML PerfomanceBySupervisor 
 ** return < void >
 **/
	
 private function PerfomanceBySupervisor()
	{
		
		switch($_REQUEST['mode'])
		{
			//case 'hourly'  : $this -> hourlyPerfomanceByTelesales(); break; 
			//case 'daily'   : $this -> dailyPerfomanceBySupervisor(); break; 
			//case 'summary' : $this -> summaryPerfomanceBySupervisor(); break; 
			default:
				echo "<h3>Sorry, ".ucfirst($_REQUEST['mode'])." Mode Report Not Available </h3>";
			break;
		}
	}	

/** 
 ** main content HTML PerfomanceByTelesales 
 ** return < void >
 **/
 
 private function PerfomanceByTelesales()
	{
		switch($_REQUEST['mode'])
		{
			//case 'hourly'  : $this -> hourlyPerfomanceByTelesales(); break; 
			//case 'daily'   : $this -> dailyPerfomanceBySupervisor(); break; 
			//case 'summary' : $this -> summaryPerfomanceByTelesales(); break; 
			
			default:
				echo "<h3>Sorry, ".ucfirst($_REQUEST['mode'])." Mode Report Not Available </h3>";
			break;
		}
	}	

/**
 ** Performance By Hour Report Only 
 ** Not Dedicated for for all report type
 ** Mode 
 ** return < void >
 **/	

function summaryPerfomanceByCampaign()
{
	$start_date = $this -> getStartDate();
	$end_date = $this -> getEndDate();
	
	foreach( $this -> _CampaignName() as $key => $CampaignId ) 
	{
		$_conts = $this -> _query_campaign($CampaignId);
		echo "<h4>{$_conts -> result_get_value('CampaignName')}</h4>";
	
	 
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
		
	/** Utilize call
	 ** regiter LOCAL var < $Utilize > 
	 **/
	
		$Utilize = array();
		$sql = " SELECT count( distinct a.CustomerId) as tots, hour(a.CallHistoryCreatedTs) as HourTime
				 FROM t_gn_callhistory a 
				 LEFT JOIN t_gn_customer b ON a.CustomerId = b.CustomerId
				 WHERE b.CampaignId='$CampaignId' 
				 AND date(a.CallHistoryCreatedTs)>='$start_date'
				 AND date(a.CallHistoryCreatedTs)<='$end_date'
				 GROUP BY HourTime ";
				 
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows ){
			$Utilize[$rows['HourTime']] += $rows['tots'];
		}	
		
	/** Frequensi call
	 ** regiter LOCAL var < $FreqCall > 
	 ** get all Freq Call Customer, 
	 ** By Agent Handle every Houry 
	 **/
	 
		$FreqCall = array();
		$sql = " SELECT count(a.CustomerId) as tots, hour(a.CallHistoryCreatedTs) as HourTime
				 FROM t_gn_callhistory a 
				 LEFT JOIN t_gn_customer b ON a.CustomerId = b.CustomerId
				 WHERE b.CampaignId='$CampaignId' 
				 AND date(a.CallHistoryCreatedTs)>='$start_date'
				 AND date(a.CallHistoryCreatedTs)<='$end_date'
				 group by HourTime ";

		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows ){
			$FreqCall[$rows['HourTime']] += $rows['tots'];
		}

	/**
	 ** get Call Reason status group Contacted  
	 ** within set interval & CampaignId On Customer 
	 ** Every Hourly $Contact < @ARRAY >  
     **/	 
		
		$Contact = array();
		$sql = " SELECT count(distinct(a.CustomerId)) as tots, hour(a.CallHistoryCreatedTs) as HourTime 
				 FROM t_gn_callhistory a
				 LEFT JOIN t_gn_customer b ON a.CustomerId = b.CustomerId
				 WHERE b.CampaignId='$CampaignId' 
				 AND date(a.CallHistoryCreatedTs)>='$start_date'
				 AND date(a.CallHistoryCreatedTs)<='$end_date'
				 AND b.CallReasonId IN(14,15,16,17,18,19,20,21,22,23,24,25,27,28,29,31,32,33,34,49,50,54,55,56,57,58,59,60,62,63,64,65,66,70,71,72,73,74,75)
				 group by HourTime";
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows ){
			$Contact[$rows['HourTime']] += $rows['tots'];
		}
		
	/**
	 ** get Call Reason status group Selling & Premi Total Every Policy  
	 ** within set interval & CampaignId On Customer 
	 ** Every Hourly $SalesData & $AnpPremi < @ARRAY >  
     **/
	
		$SalesData = array();
		$AnpPremi  = array();
		$sql = " SELECT 
					COUNT(DISTINCT(a.CustomerId)) AS tots, 
					SUM( IF(f.PayModeId=2,(e.Premi*12),e.Premi)) Premi,
					HOUR(a.CallHistoryCreatedTs) as HourTime
				FROM t_gn_callhistory a
				LEFT JOIN t_gn_customer b ON a.CustomerId = b.CustomerId
				LEFT JOIN tms_agent c on a.CreatedById=c.UserId
				LEFT JOIN t_gn_policyautogen d on a.CustomerId=d.CustomerId
				LEFT JOIN t_gn_policy e on d.PolicyNumber=e.PolicyNumber
				LEFT JOIN t_gn_productplan f on e.ProductPlanId=f.ProductPlanId
				WHERE b.CampaignId='$CampaignId' 
				AND DATE(a.CallHistoryCreatedTs)>='$start_date' 
				AND DATE(a.CallHistoryCreatedTs)<='$end_date' 
				AND c.handling_type IN(".USER_TELESALES.")
				AND a.CallReasonId IN('".$this ->Entity->SaleWithIn()."')
				GROUP BY HourTime ";
		
		
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows ){
			$SalesData[$rows['HourTime']]+= $rows['tots'];
			$AnpPremi[$rows['HourTime']] += $rows['Premi'];
		}
		
	/** 
	 ** get login Hour Agent & 
	 ** then in follower Campaign 
	 **/
	 
		$TotLoginHour = array();
		$TotReady = array();
		$TotNotReady = array();
		$TotACW = array();
		$TotBusy = array();
		
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
			$TotLoginHour[$rows['HourTime']]+= $rows['tots'];
			$TotReady[$rows['HourTime']]+= $rows['Ready'];
			$TotNotReady[$rows['HourTime']]	+= $rows['NotReady'];
			$TotACW[$rows['HourTime']] += $rows['ACW'];
			$TotBusy[$rows['HourTime']]	+= $rows['Busy'];
		}
		
	/** Get Talktime with Call Duration Indicator 
	 ** render data from cc_callsession
	 ** with status < 3304=terminate, 3005=agent terminate > 
	 ** Not <abandone > $TalkTime & $WaitTime < @Array >
	 **/
	
		$TalkTime = array();
		$WaitTime = array();
		
		$sql = " SELECT 
					HOUR(a.start_time) as HourTime,
					SUM(IF( a.`status` IN(3004,3005), UNIX_TIMESTAMP(a.end_time) - UNIX_TIMESTAMP(a.agent_time), 0)) as TalkTime,
					SUM(IF( a.`status` IN(3004,3005), UNIX_TIMESTAMP(a.agent_time) - UNIX_TIMESTAMP(a.start_time), 0)) as WaitTime  
				FROM cc_call_session a 
				LEFT JOIN t_gn_customer b on a.assign_data=b.CustomerId
				WHERE date(a.start_time)>='$start_date'
				AND date(a.start_time)<='$end_date'
				AND b.CampaignId='$CampaignId'
				group by HourTime ";
		
		$qry  = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$TalkTime[$rows['HourTime']]+= $rows['TalkTime'];
			$WaitTime[$rows['HourTime']]+= $rows['WaitTime'];
		}
		
	
	/**
	 ** Show Content On the table View HTML
	 ** Mode Text Only 
     **/	 
	
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\">
				<tr>
					<td nowrap class=\"header first\" align=\"center\">Daily Average<br>Period</td>
					<td nowrap class=\"header middle\" align=\"center\">Solicited</td>
					<td nowrap class=\"header middle\" align=\"center\">Attempt</td>
					<td nowrap class=\"header middle\" align=\"center\">Contact</td>
					<td nowrap class=\"header middle\" align=\"center\">Contact<br>Rate %</td>
					<td nowrap class=\"header middle\" align=\"center\">Contact per<br>Hour</td>
					<td nowrap class=\"header middle\" align=\"center\">Sales Close<br>Rate % </td>
					<td nowrap class=\"header middle\" align=\"center\">Sales per<br>Hour</td>
					<td nowrap class=\"header middle\" align=\"center\">Sales per<br>Contact</td>
					<td nowrap class=\"header middle\" align=\"center\">Sales</td>
					<td nowrap class=\"header middle\" align=\"center\">ANP</td>
					<td nowrap class=\"header middle\" align=\"center\">Login Hours</td>
					<td nowrap class=\"header middle\" align=\"center\">Talk Time</td>
					<td nowrap class=\"header middle\" align=\"center\">Wait Time</td>
					<td nowrap class=\"header lasted\" align=\"center\">Wrap Time</td>
				</tr> ";
					
			$avg_contact = 0;
			$avg_sales= 0; 
			$percent_sales = 0;
			
			for( $s_i=7; $s_i<=21; $s_i++)
			{
				$s_h = (strlen($s_i)==1)?"0".$s_i:$s_i;
				$const_hours = (INT)$this -> _Duration($TotLoginHour[$s_i]);
				$avg_contact = round(($Contact[$s_i]/$Utilize[$s_i])*100);
				$avg_sales = round( ($SalesData[$s_i]/$Contact[$s_i]),2);	
				$percent_sales = round((($SalesData[$s_i]/$Contact[$s_i])*100),2);
				$sale_hours = round(($SalesData[$s_i]/$const_hours),2);
				$contact_hours = round(($Contact[$s_i]/$const_hours),2);
				
				echo "<tr> 
						<td nowrap class=\"content first\" align=\"center\">".$s_h.":00 - ".$s_h.":59</td>
						<td nowrap class=\"content middle\" align=\"right\">".($Utilize[$s_i]?$Utilize[$s_i]:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($FreqCall[$s_i]?$FreqCall[$s_i]:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($Contact[$s_i]?$Contact[$s_i]:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".$avg_contact." %</td> 
						<td nowrap class=\"content middle\" align=\"right\">".($contact_hours?$contact_hours:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($percent_sales?$percent_sales:0)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".($sale_hours?$sale_hours:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($avg_sales?$avg_sales:0)."</td>
						<td nowrap class=\"content middle\" align=\"center\">".($SalesData[$s_i]?$SalesData[$s_i]:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($AnpPremi[$s_i]?formatRupiah($AnpPremi[$s_i]):0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($TotLoginHour[$s_i]?toDuration($TotLoginHour[$s_i]):0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($TotBusy[$s_i]?toDuration($TotBusy[$s_i]):0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($TotReady[$s_i]?toDuration($TotReady[$s_i]):0)."</td>
						<td nowrap class=\"content lasted\" align=\"right\">".($TotNotReady[$s_i]?toDuration($TotNotReady[$s_i]):'0')."</td>
					</tr> ";
				}	
					
			echo "<tr>
					<td nowrap class=\"total first\" align=\"center\" colspan=\"15\"></td>
				</tr> </table><br>";
		}
		
		$this -> view_filter();
		
	}

}
?>
<!--- EOF -->