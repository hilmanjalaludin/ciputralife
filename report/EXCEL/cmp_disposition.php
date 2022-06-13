<!-- SOF --> 

<?php
define('sale','15,16'); //sale

class cmp_disposition extends IndexExcel
{
	var $_con;
	var $_xfn;
	
/**
 ** report available only summary report group by HTML Telesales & HTML supervisor
 ** for available other report please open remark and then crate content 
 ** under spesific function to generate
 **/

 
/**
 ** @ aksesor of class 
 ** @ handle return <void>
 */
 
 function cmp_disposition()
 {
		$this -> _con = null;
		$this -> _xfn = 'REPORT_CAMPAIGN_DISPOSITION';
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
 
 
 private function getSupervisor()
	{
		$Supervisor = explode(',',$_REQUEST['Supervisor']);
		if( is_array($Supervisor) ) return $Supervisor;
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
	
 public function show_content_excel()
	{
		
		mysql::__construct();
		$this -> _Excel( $this -> _xfn );
		$this -> _Excel_Header();
		
		switch($_REQUEST['group_by'])
		{
			case 'Telesales'  : $this -> PerfomanceByTelesales(); break; 
			case 'supervisor' : $this -> PerfomanceBySupervisor(); break; 
			case 'campaign'   : $this -> PerfomanceByCampaign(); break; 
			default:
				echo "<h3>Sorry, You must filtering by Campaign!</h3>";
			break;
		}
		
		$this -> _Excel_Footer();
	}

/** 
 ** main content HTML PerfomanceBySupervisor 
 ** return < void >
 **/
 
private function PerfomanceByCampaign()
{
	switch($_REQUEST['mode'])
		{
			case 'summary' : $this -> summaryPerfomanceByCampaign(); break; 
			
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
		switch($_REQUEST['mode'])
		{
			case 'summary' : $this -> summaryPerfomanceBySupervisor(); break; 
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
		switch($_REQUEST['mode'])
		{
			case 'summary' : $this -> summaryPerfomanceByTelesales(); break; 
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
 ** summaryPerfomanceByCampaign
**/	

function summaryPerfomanceByCampaign()
{
	$start_date = $this -> getStartDate();
	$end_date = $this -> getEndDate();
	foreach( $this -> _CampaignName() as $key => $CampaignId ) 
	{
		$_conts = $this -> _query_campaign($CampaignId);
		echo "<h4>{$_conts -> result_get_value('CampaignName')}</h4>";
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\">
					<tr>
						<td rowspan='2' nowrap class=\"header middle\" align=\"center\">TMR</td>
						<td rowspan='2' nowrap class=\"header middle\" align=\"center\">Solicited</td>
						<td colspan='7' nowrap class=\"header middle\" align=\"center\">Wrong Party Non Human</td>
						<td colspan='4' nowrap class=\"header middle\" align=\"center\">Human Contact</td>
						<td colspan='10' nowrap class=\"header middle\" align=\"center\">Unsuccessful + Successful Outcomes</td>
					</tr>
					<tr>
						<td nowrap class=\"header middle\" align=\"center\">No Answer</td>
						<td nowrap class=\"header middle\" align=\"center\">Fax</td>
						<td nowrap class=\"header middle\" align=\"center\">Answer Machine</td>
						<td nowrap class=\"header middle\" align=\"center\">Busy</td>
						<td nowrap class=\"header middle\" align=\"center\">Invalid Number</td>
						<td nowrap class=\"header middle\" align=\"center\">Total</td>
						<td nowrap class=\"header middle\" align=\"center\">%</td>
						<td nowrap class=\"header middle\" align=\"center\">Wrong Number</td>
						<td nowrap class=\"header middle\" align=\"center\">Call Back</td>
						<td nowrap class=\"header middle\" align=\"center\">Total</td>
						<td nowrap class=\"header middle\" align=\"center\">%</td>
						<td nowrap class=\"header middle\" align=\"center\">Contacts</td>
						<td nowrap class=\"header middle\" align=\"center\">Contact Rate %</td>
						<td nowrap class=\"header middle\" align=\"center\">Sales Close Rate %</td>
						<td nowrap class=\"header middle\" align=\"center\">No Interest</td>
						<td nowrap class=\"header middle\" align=\"center\">Thinking</td>
						<td nowrap class=\"header middle\" align=\"center\">Interest</td>
						<td nowrap class=\"header middle\" align=\"center\">Sale - Product HIP</td>
						<td nowrap class=\"header middle\" align=\"center\">Sale - Product PA</td>
						<td nowrap class=\"header middle\" align=\"center\">Sale - product TL</td>
						<td nowrap class=\"header lasted\" align=\"center\">Sales Close Rate %</td>
					</tr>";
					
	///////////////////////////////////////		
		
		$SizeData = array();
		$sql = " select count(a.CustomerId) as cnt, b.UserId, b.id as Username, b.full_name ,
					 SUM( IF( a.CustomerUpdatedTs is not null, 1,0)) as Solicited
					 from t_gn_customer a left join tms_agent b on a.SellerId=b.UserId
					 where a.CampaignId='$CampaignId'
					 AND date(a.CustomerUpdatedTs) >= '$start_date'
					 AND date(a.CustomerUpdatedTs) <= '$end_date'
					 group by b.UserId  ";
					 
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows ){
				$SizeData[$rows['UserId']] += $rows['Solicited'];	
			}		
			
	//////////////////////////////////////////
		$SizeProductType = array();
		
		$sql = "SELECT
					COUNT(distinct a.CustomerId) as cnt,
					e.ProductTypeId , a.SellerId 
				FROM t_gn_customer a 
				LEFT join t_gn_campaign b on a.CampaignId=b.CampaignId
				LEFT join t_gn_campaignproduct c on b.CampaignId=c.CampaignId
				LEFT join t_gn_product d on c.ProductId=d.ProductId
				LEFT JOIN t_lk_producttype e on d.ProductTypeId=e.ProductTypeId
				WHERE a.CallReasonId IN(15,16)
				AND a.CampaignId='$CampaignId'
				AND date(a.CustomerUpdatedTs) >= '$start_date'
				AND date(a.CustomerUpdatedTs) <= '$end_date'
				GROUP BY a.SellerId ";
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows ){
			$SizeProductType[$rows['SellerId']][$rows['ProductTypeId']]+= $rows['cnt'];
		}
		
		// print_r($SizeProductType);
	///////////////////////////////////////	
		
		$totSolicited = array();
		$totAnswer = array();
		$totFaxnumber = array();
		$totNotInterest = array();
		$totInterest = array();
		$totMachine = array();
		$totBusy = array();
		$totInvalid = array();
		$totCallback = array();
		$totNotInterest = array();
		$totThinking = array();
		
		
		
		$sql = " SELECT count(a.CustomerId), b.UserId, b.full_name ,
				 SUM( IF( a.CallReasonId IN(2,3,4,5),1,0)) as NoAnswer,
				 SUM( IF( a.CallReasonId IN(68,9),1,0)) as Fax,
				 SUM( IF( a.CallReasonId IN(7),1,0)) as Machine,
				 SUM( IF( a.CallReasonId IN(39),1,0)) as Busy,
				 SUM( IF( a.CallReasonId IN(8,69),1,0)) as Invalid,
				 SUM( IF( a.CallReasonId IN(8,69),1,0)) as Wrong,
				 SUM( IF( a.CallReasonId IN(49,50),1,0)) as Callback,
				 SUM( IF( a.CallReasonId IN(17,18,19,20,21,22,23),1,0)) as NotInterest,
				 SUM( IF( a.CallReasonId IN(14),1,0)) as Thinking,
				 SUM( IF( a.CallReasonId IN(15,16),1,0)) as Interest
			FROM t_gn_customer a left join tms_agent b on a.SellerId=b.UserId
			WHERE a.CampaignId='$CampaignId'
			AND date(a.CustomerUpdatedTs) >= '$start_date'
			AND date(a.CustomerUpdatedTs) <= '$end_date'
			GROUP by b.UserId  ";
		
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows ){
			$totSolicited[$rows['UserId']] += $rows[Solicited];
			$totAnswer[$rows['UserId']] += $rows[NoAnswer];
			$totFaxnumber[$rows['UserId']]+= $rows[Fax];
			$totMachine[$rows['UserId']] += $rows[Machine];
			$totBusy[$rows['UserId']] += $rows[Busy];
			$totInvalid[$rows['UserId']] += $rows[Invalid];
			$totWrong[$rows['UserId']] += $rows[Wrong];
			$totCallback[$rows['UserId']] += $rows[Callback];
			$totNotInterest[$rows['UserId']] += $rows[NotInterest];
			$totThinking[$rows['UserId']]+= $rows[Thinking];
			$totInterest[$rows['UserId']]+= $rows[Interest];
		}		
		
		
		foreach( $SizeData as $key => $SolicitedData )
		{
		
		//RUMUS
		$totWrongParty = ($totAnswer[$key] + $totFaxnumber[$key] + $totMachine[$key] + $totBusy[$key]);
		$percentWrongParty = round((($totWrongParty / $SolicitedData) * 100),2);
		$totHumanContact = ($totWrong[$key] + $totCallback[$key]);
		$percentHumanContact = round((($totHumanContact / $SolicitedData) * 100),2);
		$totContact = ($totNotInterest[$key] + $totThinking[$key] + 0);
		$percentContactRate = round((($totContact / $SolicitedData) * 100),2);
		$percentCloseRate =  round((($totInterest[$key]/ $totContact) * 100),2);
	//	$totInterest = 0;
		$totSaleHIP = 0;
		$totSalePA = 0;
		$totSaleTL = 0;
		$percentCloseRate2 = 0;
		
			$UserTM = $this -> Users -> getUsers($key);
			echo "<tr>
					<td nowrap class=\"content middle\" align=\"left\">{$UserTM ->getUserName()} - {$UserTM ->getFullname()}</td>
					<td nowrap class=\"content middle\" align=\"right\">".$SolicitedData."</td>
					<td nowrap class=\"content middle\" align=\"right\">".$totAnswer[$key]."</td>
					<td nowrap class=\"content middle\" align=\"right\">".$totFaxnumber[$key]."</td>
					<td nowrap class=\"content middle\" align=\"right\">".$totMachine[$key]."</td>
					<td nowrap class=\"content middle\" align=\"right\">".$totBusy[$key]."&nbsp;</td>
					<td nowrap class=\"content middle\" align=\"right\">".$totInvalid[$key]."</td>
					<td nowrap class=\"content middle\" align=\"right\">".$totWrongParty."</td>
					<td nowrap class=\"content middle\" align=\"right\">".$percentWrongParty." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".$totWrong[$key]."&nbsp;</td>
					<td nowrap class=\"content middle\" align=\"right\">".$totCallback[$key]."&nbsp;</td>
					<td nowrap class=\"content middle\" align=\"right\">".$totHumanContact."</td>
					<td nowrap class=\"content middle\" align=\"right\">".$percentHumanContact." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".$totContact."</td>
					<td nowrap class=\"content middle\" align=\"right\">".$percentContactRate." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".$percentCloseRate." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".$totNotInterest[$key]."</td>
					<td nowrap class=\"content middle\" align=\"right\">".$totThinking[$key]."</td>
					<td nowrap class=\"content middle\" align=\"right\">".$totInterest[$key]."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($SizeProductType[$key][1]?$SizeProductType[$key][1]:0)."</td> 
					<td nowrap class=\"content middle\" align=\"right\">".($SizeProductType[$key][2]?$SizeProductType[$key][2]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($SizeProductType[$key][3]?$SizeProductType[$key][3]:0)."</td>
					<td nowrap class=\"content lasted\" align=\"right\">".$percentCloseRate2." %</td>
				</tr>";
				
				// KALKULASI
					$kalSolicited += $SolicitedData;
					$kalAnswer += $totAnswer[$key];
					$kalFax += $totFaxnumber[$key];
					$kalMachine += $totMachine[$key];
					$kalBusy += $totBusy[$key];
					$kalInvalid += $totInvalid[$key];
					$kalWrongParty += $totWrongParty;
					$kalWrong += $totWrong[$key];
					$kalCallback += $totCallback[$key];
					$kalHumanContact += $totHumanContact;
					$kalContact += $totContact;
					$kalNotInterest += $totNotInterest[$key];
					$kalThinking += $totThinking[$key];
					$kalInterest += $totInterest[$key];
					$kalSaleHIP += $SizeProductType[$key][1];
					$kalSalePA += $SizeProductType[$key][2];
					$kalSaleTL += $SizeProductType[$key][3];
					
			}
			
				// KALKULASI PERSEN
					$kalPercentWrongParty += ROUND((($kalWrongParty / $kalSolicited) * 100),2);
					$kalPercentHumanContact += ROUND((($kalHumanContact / $kalSolicited) * 100),2);
					$kalPercentContactRate += ROUND((($kalContact / $kalSolicited) * 100),2);
					$kalPercentSalesClose += ROUND( (($kalInterest / $kalContact) * 100),2);
					$kalPercentSalesClose2 += ROUND( ((($kalSaleHIP + $kalSalePA + $kalSaleTL) / $kalContact)*100), 2);
			
			echo "<tr>
							<td nowrap class=\"total first\" align=\"center\">MTD (Distinct by Prospect ID)</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalSolicited."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalAnswer."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalFax."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalMachine."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalBusy."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalInvalid."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalWrongParty."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalPercentWrongParty." %</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalWrong."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalCallback."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalHumanContact."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalPercentHumanContact." %</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalContact."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalPercentContactRate." %</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalPercentSalesClose." %</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalNotInterest."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalThinking."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalInterest."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalSaleHIP."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalSalePA."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalSaleTL."</td>
							<td nowrap class=\"total lasted\" align=\"right\">".$kalPercentSalesClose2." %</td>
						</tr> </table><br>";
		}
		
		$this -> view_filter();
		
	}
	
	
/******************************************************* 
 ** summary group by telesales based on function 
 ** return < void >
 *******************************************************
 **/
 
 private function summaryPerfomanceBySupervisor()
 {
	$start_date = $this -> getStartDate();
	$end_date = $this -> getEndDate();
	
	foreach( $this -> _CampaignName() as $key => $CampaignId ) 
	{
		$_conts = $this -> _query_campaign($CampaignId);
		echo "<h4>{$_conts -> result_get_value('CampaignName')}</h4>";
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\">
					<tr>
						<td rowspan='2' nowrap class=\"header middle\" align=\"center\">Supervisor</td>
						<td rowspan='2' nowrap class=\"header middle\" align=\"center\">Solicited</td>
						<td colspan='7' nowrap class=\"header middle\" align=\"center\">Wrong Party Non Human</td>
						<td colspan='4' nowrap class=\"header middle\" align=\"center\">Human Contact</td>
						<td colspan='10' nowrap class=\"header middle\" align=\"center\">Unsuccessful + Successful Outcomes</td>
					</tr>
					<tr>
						<td nowrap class=\"header middle\" align=\"center\">No Answer</td>
						<td nowrap class=\"header middle\" align=\"center\">Fax</td>
						<td nowrap class=\"header middle\" align=\"center\">Answer Machine</td>
						<td nowrap class=\"header middle\" align=\"center\">Busy</td>
						<td nowrap class=\"header middle\" align=\"center\">Invalid Number</td>
						<td nowrap class=\"header middle\" align=\"center\">Total</td>
						<td nowrap class=\"header middle\" align=\"center\">%</td>
						<td nowrap class=\"header middle\" align=\"center\">Wrong Number</td>
						<td nowrap class=\"header middle\" align=\"center\">Call Back</td>
						<td nowrap class=\"header middle\" align=\"center\">Total</td>
						<td nowrap class=\"header middle\" align=\"center\">%</td>
						<td nowrap class=\"header middle\" align=\"center\">Contacts</td>
						<td nowrap class=\"header middle\" align=\"center\">Contact Rate %</td>
						<td nowrap class=\"header middle\" align=\"center\">Sales Close Rate %</td>
						<td nowrap class=\"header middle\" align=\"center\">No Interest</td>
						<td nowrap class=\"header middle\" align=\"center\">Thinking</td>
						<td nowrap class=\"header middle\" align=\"center\">Interest</td>
						<td nowrap class=\"header middle\" align=\"center\">Sale - Product HIP</td>
						<td nowrap class=\"header middle\" align=\"center\">Sale - Product PA</td>
						<td nowrap class=\"header middle\" align=\"center\">Sale - product TL</td>
						<td nowrap class=\"header lasted\" align=\"center\">Sales Close Rate %</td>
					</tr>";
					
					
	/** get size data utilize every Supervisor 
	 ** on campaign Group filter	
	 **/
	 
		$SizeData = array();
		$sql = " select count(a.CustomerId) as cnt, b.spv_id as SpvId, 
					 SUM( IF( a.CustomerUpdatedTs is not null, 1,0)) as Solicited
					 from t_gn_customer a left join tms_agent b on a.SellerId=b.UserId
					 where a.CampaignId='$CampaignId'
					 AND date(a.CustomerUpdatedTs) >= '$start_date'
					 AND date(a.CustomerUpdatedTs) <= '$end_date'
					 group by SpvId ";
				 
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows ){
				$SizeData[$rows['SpvId']] += $rows['Solicited'];	
			}		
			
	/** get size data utilize every Supervisor 
	 ** on campaign Group filter	
	 **/
	 
		$SizeProductType = array();
		$sql = "SELECT
					COUNT(distinct a.CustomerId) as cnt,
					e.ProductTypeId , f.spv_id as SpvId
				FROM t_gn_customer a 
				LEFT join t_gn_campaign b on a.CampaignId=b.CampaignId
				LEFT join t_gn_campaignproduct c on b.CampaignId=c.CampaignId
				LEFT join t_gn_product d on c.ProductId=d.ProductId
				LEFT JOIN t_lk_producttype e on d.ProductTypeId=e.ProductTypeId
				LEFT JOIN tms_agent f on a.SellerId=f.UserId
				WHERE a.CallReasonId IN(15,16)
				AND a.CampaignId='$CampaignId'
				AND date(a.CustomerUpdatedTs) >= '$start_date'
				AND date(a.CustomerUpdatedTs) <= '$end_date'
				GROUP BY SpvId ";
		
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows ){
			$SizeProductType[$rows['SpvId']][$rows['ProductTypeId']]+= $rows['cnt'];
		}
		
	/** get size data by satatus definition 
	 ** utilize every Supervisor 
	 ** on campaign Group filter	
	 **/
		
		$totSolicited = array();
		$totAnswer = array();
		$totFaxnumber = array();
		$totNotInterest = array();
		$totInterest = array();
		$totMachine = array();
		$totBusy = array();
		$totInvalid = array();
		$totCallback = array();
		$totNotInterest = array();
		$totThinking = array();
		
		$sql = " SELECT count(a.CustomerId), b.spv_id as SpvId,
				 SUM( IF( a.CallReasonId IN(2,3,4,5),1,0)) as NoAnswer,
				 SUM( IF( a.CallReasonId IN(68,9),1,0)) as Fax,
				 SUM( IF( a.CallReasonId IN(7),1,0)) as Machine,
				 SUM( IF( a.CallReasonId IN(39),1,0)) as Busy,
				 SUM( IF( a.CallReasonId IN(8,69),1,0)) as Invalid,
				 SUM( IF( a.CallReasonId IN(8,69),1,0)) as Wrong,
				 SUM( IF( a.CallReasonId IN(49,50),1,0)) as Callback,
				 SUM( IF( a.CallReasonId IN(17,18,19,20,21,22,23),1,0)) as NotInterest,
				 SUM( IF( a.CallReasonId IN(14),1,0)) as Thinking,
				 SUM( IF( a.CallReasonId IN(15,16),1,0)) as Interest
			FROM t_gn_customer a 
				LEFT JOIN tms_agent b on a.SellerId=b.UserId
				WHERE a.CampaignId='$CampaignId'
				AND date(a.CustomerUpdatedTs) >= '$start_date'
				AND date(a.CustomerUpdatedTs) <= '$end_date'
				GROUP by SpvId ";
		
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows ){
			$totSolicited[$rows['SpvId']] += $rows[Solicited];
			$totAnswer[$rows['SpvId']] += $rows[NoAnswer];
			$totFaxnumber[$rows['SpvId']]+= $rows[Fax];
			$totMachine[$rows['SpvId']] += $rows[Machine];
			$totBusy[$rows['SpvId']] += $rows[Busy];
			$totInvalid[$rows['SpvId']] += $rows[Invalid];
			$totWrong[$rows['SpvId']] += $rows[Wrong];
			$totCallback[$rows['SpvId']] += $rows[Callback];
			$totNotInterest[$rows['SpvId']] += $rows[NotInterest];
			$totThinking[$rows['SpvId']]+= $rows[Thinking];
			$totInterest[$rows['SpvId']]+= $rows[Interest];
		}		
		
		
		$kalSolicited =0;
		$kalAnswer =0;
		$kalFax =0;
		$kalMachine =0;
		$kalBusy =0;
		$kalInvalid =0;
		$kalWrongParty =0;
		$kalWrong =0;
		$kalCallback =0;
		$kalHumanContact =0;
		$kalContact =0;
		$kalNotInterest =0;
		$kalThinking =0;
		$kalInterest =0;
		$kalSaleHIP =0;
		$kalSalePA =0;
		$kalSaleTL =0;
	/**
     ** showing content by spv index looping data 
	 ** next by agent 
     **/	 
		foreach( $this -> getSupervisor() as $_keys => $SpvId )
		{
			$totWrongParty = ($totAnswer[$SpvId] + $totFaxnumber[$SpvId] + $totMachine[$SpvId] + $totBusy[$SpvId]);
			$percentWrongParty = round((($totWrongParty / $SizeData[$SpvId]) * 100),2);
			$totHumanContact = ($totWrong[$SpvId] + $totCallback[$SpvId]);
			$percentHumanContact = round((($totHumanContact / $SizeData[$SpvId]) * 100),2);
			$totContact = ($totNotInterest[$SpvId] + $totThinking[$SpvId] + 0);
			$percentContactRate = round((($totContact / $SizeData[$SpvId]) * 100),2);
			$percentCloseRate =  round((($totInterest[$SpvId]/ $totContact) * 100),2);
			$totSaleHIP = 0;
			$totSalePA = 0;
			$totSaleTL = 0;
			$percentCloseRate2 = 0;
		
			$UserSPV = $this -> Users -> getUsers($SpvId);
			echo "<tr>
					<td nowrap class=\"content middle\" align=\"left\">{$UserSPV ->getUserName()} - {$UserSPV ->getFullname()}</td>
					<td nowrap class=\"content middle\" align=\"right\">".($SizeData[$SpvId]?$SizeData[$SpvId]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totAnswer[$SpvId]?$totAnswer[$SpvId]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totFaxnumber[$SpvId]?$totFaxnumber[$SpvId]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totMachine[$SpvId]?$totMachine[$SpvId]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totBusy[$SpvId]?$totBusy[$SpvId]:0)."&nbsp;</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totInvalid[$SpvId]?$totInvalid[$SpvId]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totWrongParty?$totWrongParty:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($percentWrongParty?$percentWrongParty:0)." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totWrong[$SpvId]?$totWrong[$SpvId]:0)."&nbsp;</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totCallback[$SpvId]?$totCallback[$SpvId]:0)."&nbsp;</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totHumanContact?$totHumanContact:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($percentHumanContact?$percentHumanContact:0)." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totContact?$totContact:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($percentContactRate?$percentContactRate:0)." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($percentCloseRate?$percentCloseRate:0)." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totNotInterest[$SpvId]?$totNotInterest[$SpvId]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totThinking[$SpvId]?$totThinking[$SpvId]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totInterest[$SpvId]?$totInterest[$SpvId]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($SizeProductType[$SpvId][1]?$SizeProductType[$SpvId][1]:0)."</td> 
					<td nowrap class=\"content middle\" align=\"right\">".($SizeProductType[$SpvId][2]?$SizeProductType[$SpvId][2]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($SizeProductType[$SpvId][3]?$SizeProductType[$SpvId][3]:0)."</td>
					<td nowrap class=\"content lasted\" align=\"right\">".($percentCloseRate2?$percentCloseRate2:0)." %</td>
				</tr>";
				
				// KALKULASI
					$kalSolicited += $SizeData[$SpvId];
					$kalAnswer += $totAnswer[$SpvId];
					$kalFax += $totFaxnumber[$SpvId];
					$kalMachine += $totMachine[$SpvId];
					$kalBusy += $totBusy[$SpvId];
					$kalInvalid += $totInvalid[$SpvId];
					$kalWrongParty += $totWrongParty;
					$kalWrong += $totWrong[$SpvId];
					$kalCallback += $totCallback[$SpvId];
					$kalHumanContact += $totHumanContact;
					$kalContact += $totContact;
					$kalNotInterest += $totNotInterest[$SpvId];
					$kalThinking += $totThinking[$SpvId];
					$kalInterest += $totInterest[$SpvId];
					$kalSaleHIP += $SizeProductType[$SpvId][1];
					$kalSalePA += $SizeProductType[$SpvId][2];
					$kalSaleTL += $SizeProductType[$SpvId][3];
					
			}
			
				// KALKULASI PERSEN
					$kalPercentWrongParty += ROUND((($kalWrongParty / $kalSolicited) * 100),2);
					$kalPercentHumanContact += ROUND((($kalHumanContact / $kalSolicited) * 100),2);
					$kalPercentContactRate += ROUND((($kalContact / $kalSolicited) * 100),2);
					$kalPercentSalesClose += ROUND( (($kalInterest / $kalContact) * 100),2);
					$kalPercentSalesClose2 += ROUND( ((($kalSaleHIP + $kalSalePA + $kalSaleTL) / $kalContact)*100), 2);
			
			echo "<tr>
							<td nowrap class=\"total first\" align=\"center\">MTD (Distinct by Prospect ID)</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalSolicited."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalAnswer."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalFax."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalMachine."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalBusy."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalInvalid."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalWrongParty."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalPercentWrongParty." %</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalWrong."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalCallback."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalHumanContact."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalPercentHumanContact." %</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalContact."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalPercentContactRate." %</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalPercentSalesClose." %</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalNotInterest."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalThinking."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalInterest."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalSaleHIP."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalSalePA."</td>
							<td nowrap class=\"total middle\" align=\"right\">".$kalSaleTL."</td>
							<td nowrap class=\"total lasted\" align=\"right\">".$kalPercentSalesClose2." %</td>
						</tr> </table><br>";
		}
		
		$this -> view_filter();
}	
	
/** 
 ** summary group by telesales based on function 
 ** return < void >
 **/
function summaryPerfomanceByTelesales()
{
	$start_date = $this -> getStartDate();
	$end_date = $this -> getEndDate();
	
	/** detail spv send by paramter POST **/
	 $_spv = $this -> Users -> getUsers( $this -> escPost('Supervisor') );
	
	echo "<h3>{$_spv -> getUsername()} - {$_spv -> getFullname()}</h3>";
	
	
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\">
					<tr>
						<td rowspan='2' nowrap class=\"header middle\" align=\"center\">TM</td>
						<td rowspan='2' nowrap class=\"header middle\" align=\"center\">Solicited</td>
						<td colspan='7' nowrap class=\"header middle\" align=\"center\">Wrong Party Non Human</td>
						<td colspan='4' nowrap class=\"header middle\" align=\"center\">Human Contact</td>
						<td colspan='10' nowrap class=\"header middle\" align=\"center\">Unsuccessful + Successful Outcomes</td>
					</tr>
					<tr>
						<td nowrap class=\"header middle\" align=\"center\">No Answer</td>
						<td nowrap class=\"header middle\" align=\"center\">Fax</td>
						<td nowrap class=\"header middle\" align=\"center\">Answer Machine</td>
						<td nowrap class=\"header middle\" align=\"center\">Busy</td>
						<td nowrap class=\"header middle\" align=\"center\">Invalid Number</td>
						<td nowrap class=\"header middle\" align=\"center\">Total</td>
						<td nowrap class=\"header middle\" align=\"center\">%</td>
						<td nowrap class=\"header middle\" align=\"center\">Wrong Number</td>
						<td nowrap class=\"header middle\" align=\"center\">Call Back</td>
						<td nowrap class=\"header middle\" align=\"center\">Total</td>
						<td nowrap class=\"header middle\" align=\"center\">%</td>
						<td nowrap class=\"header middle\" align=\"center\">Contacts</td>
						<td nowrap class=\"header middle\" align=\"center\">Contact Rate %</td>
						<td nowrap class=\"header middle\" align=\"center\">Sales Close Rate %</td>
						<td nowrap class=\"header middle\" align=\"center\">No Interest</td>
						<td nowrap class=\"header middle\" align=\"center\">Thinking</td>
						<td nowrap class=\"header middle\" align=\"center\">Interest</td>
						<td nowrap class=\"header middle\" align=\"center\">Sale - Product HIP</td>
						<td nowrap class=\"header middle\" align=\"center\">Sale - Product PA</td>
						<td nowrap class=\"header middle\" align=\"center\">Sale - product TL</td>
						<td nowrap class=\"header lasted\" align=\"center\">Sales Close Rate %</td>
					</tr>";
					
	foreach( $this -> _CampaignName() as $key => $CampaignId ) 
	{
		$_conts = $this -> _query_campaign($CampaignId);
		
		echo "<tr>
				<td class=\"sub first\"  colspan=\"23\">{$_conts -> result_get_value('CampaignName')}</td>
			</tr>";
		
	/** get size data utilize every Supervisor 
	 ** on campaign Group filter	
	 **/
	 
		$SizeData = array();
		$sql = " select count(a.CustomerId) as cnt, b.UserId as UserId, 
					 SUM( IF( a.CustomerUpdatedTs is not null, 1,0)) as Solicited
					 from t_gn_customer a 
					 LEFT JOIN tms_agent b on a.SellerId=b.UserId
					 where a.CampaignId='$CampaignId'
					 AND date(a.CustomerUpdatedTs) >= '$start_date'
					 AND date(a.CustomerUpdatedTs) <= '$end_date'
					 group by UserId ";
				 
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows ){
				$SizeData[$rows['UserId']] += $rows['Solicited'];	
			}		
			
	/** get size data utilize every Supervisor 
	 ** on campaign Group filter	
	 **/
	 
		$SizeProductType = array();
		$sql = "SELECT
					COUNT(distinct a.CustomerId) as cnt,
					e.ProductTypeId , f.UserId as UserId
				FROM t_gn_customer a 
				LEFT join t_gn_campaign b on a.CampaignId=b.CampaignId
				LEFT join t_gn_campaignproduct c on b.CampaignId=c.CampaignId
				LEFT join t_gn_product d on c.ProductId=d.ProductId
				LEFT JOIN t_lk_producttype e on d.ProductTypeId=e.ProductTypeId
				LEFT JOIN tms_agent f on a.SellerId=f.UserId
				WHERE a.CallReasonId IN(15,16)
				AND a.CampaignId='$CampaignId'
				AND date(a.CustomerUpdatedTs) >= '$start_date'
				AND date(a.CustomerUpdatedTs) <= '$end_date'
				GROUP BY UserId ";
		
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows ){
			$SizeProductType[$rows['UserId']][$rows['ProductTypeId']]+= $rows['cnt'];
		}
		
	/** get size data by satatus definition 
	 ** utilize every Supervisor 
	 ** on campaign Group filter	
	 **/
		
		$totSolicited = array();
		$totAnswer = array();
		$totFaxnumber = array();
		$totNotInterest = array();
		$totInterest = array();
		$totMachine = array();
		$totBusy = array();
		$totInvalid = array();
		$totCallback = array();
		$totNotInterest = array();
		$totThinking = array();
		
		$sql = " SELECT count(a.CustomerId), b.UserId as UserId,
				 SUM( IF( a.CallReasonId IN(2,3,4,5),1,0)) as NoAnswer,
				 SUM( IF( a.CallReasonId IN(68,9),1,0)) as Fax,
				 SUM( IF( a.CallReasonId IN(7),1,0)) as Machine,
				 SUM( IF( a.CallReasonId IN(39),1,0)) as Busy,
				 SUM( IF( a.CallReasonId IN(8,69),1,0)) as Invalid,
				 SUM( IF( a.CallReasonId IN(8,69),1,0)) as Wrong,
				 SUM( IF( a.CallReasonId IN(49,50),1,0)) as Callback,
				 SUM( IF( a.CallReasonId IN(17,18,19,20,21,22,23),1,0)) as NotInterest,
				 SUM( IF( a.CallReasonId IN(14),1,0)) as Thinking,
				 SUM( IF( a.CallReasonId IN(15,16),1,0)) as Interest
			FROM t_gn_customer a 
				LEFT JOIN tms_agent b on a.SellerId=b.UserId
				WHERE a.CampaignId='$CampaignId'
				AND date(a.CustomerUpdatedTs) >= '$start_date'
				AND date(a.CustomerUpdatedTs) <= '$end_date'
				GROUP by UserId ";
		
		$qry = $this -> query($sql);
		foreach($qry -> result_assoc() as $rows ){
			$totSolicited[$rows['UserId']] += $rows[Solicited];
			$totAnswer[$rows['UserId']] += $rows[NoAnswer];
			$totFaxnumber[$rows['UserId']]+= $rows[Fax];
			$totMachine[$rows['UserId']] += $rows[Machine];
			$totBusy[$rows['UserId']] += $rows[Busy];
			$totInvalid[$rows['UserId']] += $rows[Invalid];
			$totWrong[$rows['UserId']] += $rows[Wrong];
			$totCallback[$rows['UserId']] += $rows[Callback];
			$totNotInterest[$rows['UserId']] += $rows[NotInterest];
			$totThinking[$rows['UserId']]+= $rows[Thinking];
			$totInterest[$rows['UserId']]+= $rows[Interest];
		}		
		
		
		$kalSolicited =0;
		$kalAnswer =0;
		$kalFax =0;
		$kalMachine =0;
		$kalBusy =0;
		$kalInvalid =0;
		$kalWrongParty =0;
		$kalWrong =0;
		$kalCallback =0;
		$kalHumanContact =0;
		$kalContact =0;
		$kalNotInterest =0;
		$kalThinking =0;
		$kalInterest =0;
		$kalSaleHIP =0;
		$kalSalePA =0;
		$kalSaleTL =0;
	/**
     ** showing content by spv index looping data 
	 ** next by agent 
     **/	 
	
		foreach( self::getTelesales() as $s_k => $UserId )
		{
			$totWrongParty = ($totAnswer[$UserId] + $totFaxnumber[$UserId] + $totMachine[$UserId] + $totBusy[$UserId]);
			$percentWrongParty = round((($totWrongParty / $SizeData[$UserId]) * 100),2);
			$totHumanContact = ($totWrong[$UserId] + $totCallback[$UserId]);
			$percentHumanContact = round((($totHumanContact / $SizeData[$UserId]) * 100),2);
			$totContact = ($totNotInterest[$UserId] + $totThinking[$UserId] + 0);
			$percentContactRate = round((($totContact / $SizeData[$UserId]) * 100),2);
			$percentCloseRate =  round((($totInterest[$UserId]/ $totContact) * 100),2);
			$totSaleHIP = 0;
			$totSalePA = 0;
			$totSaleTL = 0;
			$percentCloseRate2 = 0;
			
			$UserTM = $this -> Users -> getUsers($UserId);
			echo "<tr>
					<td nowrap class=\"content middle\" align=\"left\">{$UserTM ->getUserName()} - {$UserTM ->getFullname()}</td>
					<td nowrap class=\"content middle\" align=\"right\">".($SizeData[$UserId]?$SizeData[$UserId]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totAnswer[$UserId]?$totAnswer[$UserId]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totFaxnumber[$UserId]?$totFaxnumber[$UserId]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totMachine[$UserId]?$totMachine[$UserId]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totBusy[$UserId]?$totBusy[$UserId]:0)."&nbsp;</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totInvalid[$UserId]?$totInvalid[$UserId]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totWrongParty?$totWrongParty:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($percentWrongParty?$percentWrongParty:0)." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totWrong[$UserId]?$totWrong[$UserId]:0)."&nbsp;</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totCallback[$UserId]?$totCallback[$UserId]:0)."&nbsp;</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totHumanContact?$totHumanContact:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($percentHumanContact?$percentHumanContact:0)." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totContact?$totContact:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($percentContactRate?$percentContactRate:0)." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($percentCloseRate?$percentCloseRate:0)." %</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totNotInterest[$UserId]?$totNotInterest[$UserId]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totThinking[$UserId]?$totThinking[$UserId]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($totInterest[$UserId]?$totInterest[$UserId]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($SizeProductType[$UserId][1]?$SizeProductType[$UserId][1]:0)."</td> 
					<td nowrap class=\"content middle\" align=\"right\">".($SizeProductType[$UserId][2]?$SizeProductType[$UserId][2]:0)."</td>
					<td nowrap class=\"content middle\" align=\"right\">".($SizeProductType[$UserId][3]?$SizeProductType[$UserId][3]:0)."</td>
					<td nowrap class=\"content lasted\" align=\"right\">".($percentCloseRate2?$percentCloseRate2:0)." %</td>
				</tr>";
					
		/** calculation every rows 
		 ** sub division 
		 **/
			$kalSolicited += $SizeData[$UserId];
			$kalAnswer += $totAnswer[$UserId];
			$kalFax += $totFaxnumber[$UserId];
			$kalMachine += $totMachine[$UserId];
			$kalBusy += $totBusy[$UserId];
			$kalInvalid += $totInvalid[$UserId];
			$kalWrongParty += $totWrongParty;
			$kalWrong += $totWrong[$UserId];
			$kalCallback += $totCallback[$UserId];
			$kalHumanContact += $totHumanContact;
			$kalContact += $totContact;
			$kalNotInterest += $totNotInterest[$UserId];
			$kalThinking += $totThinking[$UserId];
			$kalInterest += $totInterest[$UserId];
			$kalSaleHIP += $SizeProductType[$UserId][1];
			$kalSalePA += $SizeProductType[$UserId][2];
			$kalSaleTL += $SizeProductType[$UserId][3];
						
		}
		
		// KALKULASI PERSEN
		$kalPercentWrongParty += ROUND((($kalWrongParty / $kalSolicited) * 100),2);
		$kalPercentHumanContact += ROUND((($kalHumanContact / $kalSolicited) * 100),2);
		$kalPercentContactRate += ROUND((($kalContact / $kalSolicited) * 100),2);
		$kalPercentSalesClose += ROUND( (($kalInterest / $kalContact) * 100),2);
		$kalPercentSalesClose2 += ROUND( ((($kalSaleHIP + $kalSalePA + $kalSaleTL) / $kalContact)*100), 2);
			echo "<tr>
					<td nowrap class=\"total first\" align=\"center\">MTD (Distinct by Prospect ID)</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalSolicited."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalAnswer."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalFax."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalMachine."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalBusy."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalInvalid."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalWrongParty."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalPercentWrongParty." %</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalWrong."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalCallback."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalHumanContact."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalPercentHumanContact." %</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalContact."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalPercentContactRate." %</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalPercentSalesClose." %</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalNotInterest."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalThinking."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalInterest."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalSaleHIP."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalSalePA."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$kalSaleTL."</td>
					<td nowrap class=\"total lasted\" align=\"right\">".$kalPercentSalesClose2." %</td>
				</tr> ";
					
				
		}
		echo "</table><br>";
		$this -> view_filter();
	}
}
?>
<!--- EOF -->