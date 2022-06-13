<!-- SOF --> 

<?php
define('sale','15,16'); //sale

class lead_activity extends index
{
	var $_con;
/**
 ** report available only summary report group by HTML Telesales & HTML supervisor
 ** for available other report please open remark and then crate content 
 ** under spesific function to generate
 **/

 
/**
 ** @ aksesor of class 
 ** @ handle return <void>
 */
 
 function lead_activity(){
		$this ->_con&= null;
	}

	
private function _lead_database($CampaignId, $number)
{
	$start_date = $this -> getStartDate();
	$end_date = $this -> getEndDate();
	
	$sql = "SELECT DISTINCT 
				a.CustomerId, 
				COUNT(a.CallHistoryId) AS jum
			FROM t_gn_callhistory a
				LEFT JOIN t_gn_customer b ON a.CustomerId=b.CustomerId
				LEFT JOIN tms_agent c on a.CreatedById=c.UserId
			WHERE 
				b.CampaignId='$CampaignId'
				AND DATE(a.CallHistoryCreatedTs)>='$start_date'
				AND DATE(a.CallHistoryCreatedTs)<='$end_date'
				AND c.handling_type=4
			GROUP BY a.CustomerId
			HAVING jum =$number ";

	$qry = $this -> query($sql);
	foreach($qry  -> result_assoc() as $rows ){
		$count+=1;
	}	
	
	return $count;
}	

private function _lead_contact($CampaignId, $number)
{
	$start_date = $this -> getStartDate();
	$end_date = $this -> getEndDate();
	
	$sql = "SELECT DISTINCT 
				a.CustomerId, 
				COUNT(a.CallHistoryId) AS jum, 
				SUM(IF(a.CallReasonId IN (14,15,16,17,18,19,20,21,22,23,24,25,27,28,29,31,32,33,34,49,50,54,55,56,57,58,59,60,62,63,64,65,66,70,71,72,73,74,75),1,0)) AS Contact
			FROM t_gn_callhistory a
				LEFT JOIN t_gn_customer b ON a.CustomerId=b.CustomerId
			WHERE 
				b.CampaignId='$CampaignId'
				AND DATE(a.CallHistoryCreatedTs)>='$start_date'
				AND DATE(a.CallHistoryCreatedTs)<='$end_date'
			GROUP BY a.CustomerId
			HAVING jum =$number ";
	$qry = $this -> query($sql);
	foreach($qry  -> result_assoc() as $rows ){
		$count+=$rows['Contact'];
	}	
	
	return $count;
}	


/**
 ** get premy by Closing data 
 ** return currency && Colisng status
 **/ 
 
private function _lead_sale($CampaignId, $number)
{
	$start_date = $this -> getStartDate();
	$end_date = $this -> getEndDate();
	$count_datas = array();
	
	$sql = " SELECT DISTINCT a.CustomerId, 
				COUNT(a.CallHistoryId) AS jum, 
				SUM(distinct IF(a.CallReasonId IN (14,15,16,17,18,19,20,21,22,23,24,25,27,28,29,31,32,33,34,49,50,54,55,56,57,58,59,60,62,63,64,65,66,70,71,72,73,74,75),1,0)) as contact,
				SUM(distinct IF(b.CallReasonId IN (15,16),1,0)) AS PIF,
				SUM(IF(f.PayModeId=2,(e.Premi*12), e.Premi)) AS ANP
				FROM t_gn_callhistory a
			 LEFT JOIN t_gn_customer b ON a.CustomerId=b.CustomerId
			 LEFT JOIN tms_agent c on a.CreatedById=c.UserId
			 LEFT JOIN t_gn_policyautogen d on b.CustomerId=d.CustomerId
			 LEFT JOIN t_gn_policy e on d.PolicyNumber=e.PolicyNumber
			 LEFT JOIN t_gn_productplan f ON e.ProductPlanId=f.ProductPlanId
			 WHERE b.CampaignId='$CampaignId' 
			 AND DATE(a.CallHistoryCreatedTs)>='$start_date'
			 AND DATE(a.CallHistoryCreatedTs)<='$end_date'
			 AND c.handling_type=4
			 GROUP BY a.CustomerId
			 HAVING jum =$number ";
			 
	$qry = $this -> query($sql);
	foreach($qry  -> result_assoc() as $rows ){
		$count_datas['interest'] +=$rows['PIF'];
		$count_datas['anp'] +=$rows['ANP'];
		$count_datas['contact'] +=$rows['contact'];
	}	
	
	return $count_datas;
}	


/**
 ** get start date interval 
 ** return < string >
 */
 
 private function _lead_Index($CampaignId){
	$start_date = $this -> getStartDate();
	$end_date = $this -> getEndDate();
	
	$sql = "select distinct a.CustomerId, count(a.CallHistoryId)  as jum  
			from t_gn_callhistory a 
			left join t_gn_customer b on a.CustomerId=b.CustomerId
			where b.CampaignId='$CampaignId'
			and date(a.CallHistoryCreatedTs)>='$start_date'
			and date(a.CallHistoryCreatedTs)<='$end_date'
			group by a.CustomerId
			order by jum asc";
	$qry = $this -> query($sql);
	foreach($qry  -> result_assoc() as $rows ){
		$datas[$rows['jum']] = $rows['jum'];
	}	
	
	return $datas;
 }
 
 
 
 	
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
			//case 'Telesales'  : $this -> PerfomanceByTelesales(); break; 
			//case 'supervisor' : $this -> PerfomanceBySupervisor(); break; 
			case 'campaign'   : $this -> PerfomanceByCampaign(); break; 
			
			default:
				echo "<h3>Sorry, This report must grouping by Campaign!</h3>";
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
			case 'summary' : $this -> summaryPerfomanceByCampaign(); break; 
			
			default:
				echo "<h3>Sorry, This report must grouping by Campaign!</h3>";
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
 ** summaryPerfomanceByCampaign
**/	

function summaryPerfomanceByCampaign()
{
	$start_date = $this -> getStartDate();
	$end_date = $this -> getEndDate();
	foreach( $this -> _CampaignName() as $key => $CampaignId ) 
	{
		//print_r();
		
		$_conts = $this -> _query_campaign($CampaignId);
		echo "<h4>{$_conts -> result_get_value('CampaignName')}</h4>";
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\">
					<tr>
						<td nowrap class=\"header first\" align=\"center\">Number of <br>Attempt<br>(per Prospect)</td>
						<td nowrap class=\"header middle\" align=\"center\">Database</td>
						<td nowrap class=\"header middle\" align=\"center\">Contact</td>
						<td nowrap class=\"header middle\" align=\"center\">PIF</td>
						<td nowrap class=\"header middle\" align=\"center\">ANP</td>
						<td nowrap class=\"header middle\" align=\"center\">Contact Rate %</td>
						<td nowrap class=\"header middle\" align=\"center\">Sales Close Rate %</td>
						<td nowrap class=\"header lasted\" align=\"center\">Response Rate %</td>
					</tr>";
		
	 /**
	  ** definition content list 
	  **/
	  
		$kalAttempt =0;
		$kalDatabase = 0;
		$kalContact = 0;
		$kalPIF = 0;
		$kalANP= 0;
					
		
		$SizeData = self::_lead_Index($CampaignId);
		
		foreach( $SizeData as $key => $SolicitedData )
		{
				
			$SalesData = self::_lead_sale($CampaignId,$key);
			$DatabaseData = self::_lead_database($CampaignId,$key);
			
			/** perhitungan RUMUS data **/
			
			$percentContactRate = ROUND( (($SalesData['contact']/$DatabaseData)*100),2);
			$percentSalesClose = ROUND( (($SalesData['interest']/$SalesData['contact'])*100),2);
			$percentResponse = ROUND( (($SalesData['interest']/$DatabaseData)*100),2);
			
				$UserTM = $this -> Users -> getUsers($key);
				echo "<tr> 
						<td nowrap class=\"content first\" align=\"center\">".$key."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($DatabaseData?$DatabaseData:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($SalesData['contact']?$SalesData['contact']:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($SalesData['interest']?$SalesData['interest']:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($SalesData['anp']?formatRupiah($SalesData['anp']):0)."</td> 
						<td nowrap class=\"content middle\" align=\"right\">".$percentContactRate." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".$percentSalesClose." %</td>
						<td nowrap class=\"content lasted\" align=\"right\">".$percentResponse." %</td>
						</tr>";
					
					
				$kalAttempt += $DatabaseData;
				$kalDatabase += $DatabaseData;
				$kalContact += $SalesData['contact'];
				$kalPIF += $SalesData['interest'];
				$kalANP += $SalesData['anp'];
			}
				
					// KALKULASI PERSEN
					$kalPercentContactRate += ROUND((($kalContact / $kalDatabase)*100),2);
					$kalPercentSalesClose += ROUND( (($kalPIF / $kalContact)*100),2);
					$kalPercentResponse += ROUND((($kalPIF / $kalDatabase)*100),2);
					
				
				echo "<tr>
						<td nowrap class=\"total first\" align=\"center\">Total</td>
						<td nowrap class=\"total middle\" align=\"right\">".$kalDatabase."</td>
						<td nowrap class=\"total middle\" align=\"right\">".$kalContact."</td>
						<td nowrap class=\"total middle\" align=\"right\">".$kalPIF."</td>
						<td nowrap class=\"total middle\" align=\"right\">".formatRupiah($kalANP,2)."</td>
						<td nowrap class=\"total middle\" align=\"right\">".$kalPercentContactRate." %</td>
						<td nowrap class=\"total middle\" align=\"right\">".$kalPercentSalesClose." %</td>
						<td nowrap class=\"total lasted\" align=\"right\">".$kalPercentResponse." %</td>
					</tr> </table><br>";
			}
			
	}
	
}
?>
<!--- EOF -->