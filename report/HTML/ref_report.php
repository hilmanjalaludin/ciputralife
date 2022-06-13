<?php
//include(dirname(__FILE__)."/class_export_excel.php");
//modified by Fajar 24-01-2014 (add function campaign select) 
class ref_report extends index
{

	function ref_report()
	{
		$this->start_date = $this -> formatDateEng($_REQUEST['start_date']);
		$this->end_date = $this -> formatDateEng($_REQUEST['end_date']);
	}
	
	function write_footer()
	{
		
		echo "<tr >
						  <td class=\"total first\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
					</tr> ";
		?>
			</table>
			<div>
			</body>
			</html>
		<?php
	}
	
		
/** get group_select **/

	private function get_group_select()
	{
		return explode(",", $this -> escPost('group_select'));
	}	
	

 private function get_campaign_select()
	{
		return explode(",", $this -> escPost('CampaignName'));
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
	
	function _query_campaign($_CampaignId)
	{
		$sql = " SELECT a.CampaignNumber, a.CampaignName FROM t_gn_campaign a WHERE a.CampaignId ='$_CampaignId'";
		$qry = $this -> query($sql);
		if( !$qry -> EOF() )
		{
			return $qry; 
		}
	}
/* super visor ***/

	private function write_header_by_campaign($Parameters='')
	{ 
		$_conts = $this -> _query_campaign($Parameters);
		echo "<h4>{$_conts -> result_get_value('CampaignName')}</h4>";
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" >
				<tr>
					<td class=\"header first\" nowrap>Customer CIF</td>
					<td class=\"header middle\" nowrap>Customer Name</td>
					<td class=\"header middle\" nowrap>Campaign Number</td>
					<td class=\"header middle\" nowrap>Campaign Name</td>
					<td class=\"header middle\" nowrap>Referal Name</td>
					<td class=\"header middle\" nowrap>Referal Phone 1</td>
					<td class=\"header middle\" nowrap>Referal Phone 2</td>
					<td class=\"header middle\" nowrap>Referal Phone 3</td>
					<td class=\"header middle\" nowrap>Customer DOB</td>
					<td class=\"header middle\" nowrap>Customer Relasi</td>
					<td class=\"header middle\" nowrap>Agent ID</td>
					<td class=\"header middle\" nowrap>Agent Name</td>
					<td class=\"header lasted\" nowrap>Created Date</td>
				</tr> "; 
	}
		
		
/* main content HTML **/
		
	
	function show_content_html()
	{
		mysql::__construct();
		switch($_REQUEST['group_by'])
		{
			case 'campaign' 	: $this -> ReferalReportByCampaign(); 	break;
			default:
					echo "<h3>Sorry, This report must grouping by campaign!</h3>";
			break;
			
		}
	}
	private function ReferalReportByCampaign(){
		switch($_REQUEST['mode'])
			{
				case 'all' : $this -> AllReferalReportByCampaign(); break;
				//case 'less3' : $this -> LessReferalReportByCampaign(); break;
				//case 'more3' : $this -> MoreReferalReportByCampaign(); break;
				default:
					echo "<h3>Please select mode !</h3>";
				break;
			}
	}
	
	function AllReferalReportByCampaign(){
		
		foreach( $this -> get_campaign_select() as $keys => $CampaignId )
		{	$this -> write_header_by_campaign($CampaignId);
			$this -> All_write_content_by_campaign($CampaignId);
			$this -> write_footer();
		}
	}
	function LessReferalReportByCampaign(){
		foreach( $this -> get_campaign_select() as $keys => $CampaignId )
		{ 
			$this -> write_header_by_campaign($CampaignId);
			$this -> Less_write_content_by_campaign($CampaignId);
			$this -> write_footer();
		}
	}
	function MoreReferalReportByCampaign(){
		foreach( $this -> get_campaign_select() as $keys => $CampaignId )
		{	
			$this -> write_header_by_campaign($CampaignId);
			$this -> More_write_content_by_campaign($CampaignId);
			$this -> write_footer();
		}
		
	}
	



/** create d write_content_by_campaign **/
	
	function All_write_content_by_campaign($campaignid)
	{
		$start_date = $this -> getStartDate();
		$end_date = $this -> getEndDate();
		$sql  =" SELECT
					b.CustomerNumber,b.NumberCIF, b.CustomerFirstName as CustomerName,
					c.CampaignNumber, c.CampaignName,
					a.ReferalName, DATE(a.ReferalCreateTs)as datecreate,
					a.ReferalPhone1, a.ReferalPhone2, a.ReferalPhone3, a.ReferalDOB, a.ReferalRelasi,
					d.id as AgentId, d.full_name as Agent
				FROM t_gn_referal a
					LEFT JOIN t_gn_customer b ON a.ReferalCustomerId=b.CustomerId
					LEFT JOIN t_gn_campaign c ON b.CampaignId = c.CampaignId
					LEFT JOIN tms_agent d ON a.ReferalSellerId = d.UserId
				WHERE 
					c.CampaignId = '$campaignid'
					AND a.ReferalQAStatus = 1
					AND DATE(a.ReferalApprovalTs) >= '".$start_date."'
					AND DATE(a.ReferalApprovalTs) <= '".$end_date."' ";//IF(a.ReferalQAStatus=1,'Approve',IF(a.ReferalQAStatus=0,'Reject','New')) as Status
					
		$qry = $this -> query($sql);				
		foreach($qry -> result_assoc() as $rows )
		{
			echo "<tr >
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[NumberCIF]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerName]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CampaignNumber]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CampaignName]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[ReferalName]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[ReferalPhone1]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[ReferalPhone2]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[ReferalPhone3]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[ReferalDOB]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[ReferalRelasi]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[AgentId]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[Agent]."</td>
					  <td class=\"content lasted\" nowrap>&nbsp;".date('d/m/Y',strtotime($rows[datecreate]))."</td>
				</tr> ";//<td class=\"content lasted\" nowrap>&nbsp;".$rows[Status]."</td>
		}	
	}
	
	function Less_write_content_by_campaign($CampId=''){
		$CustmId = $this -> getLessReferalCustomerId($CampId);
		//print_r ($CustmId);
		for ($i=0; $i < count($CustmId);$i++){
			$sql = "SELECT
					b.CustomerNumber,b.NumberCIF, b.CustomerFirstName as CustomerName,
					c.CampaignNumber, c.CampaignName,
					a.ReferalName, DATE(a.ReferalCreateTs)as datecreate,
					a.ReferalPhone1, a.ReferalPhone2, a.ReferalPhone3, a.ReferalDOB, a.ReferalRelasi,
					d.id as AgentId, d.full_name as Agent
				FROM t_gn_referal a
					LEFT JOIN t_gn_customer b ON a.ReferalCustomerId=b.CustomerId
					LEFT JOIN t_gn_campaign c ON b.CampaignId = c.CampaignId
					LEFT JOIN tms_agent d ON a.ReferalSellerId = d.UserId
				WHERE
				a.ReferalCustomerId = '".$CustmId[$i]."'";
				$qry = $this -> query($sql);
				foreach($qry -> result_assoc() as $rows )
				{
					$data = $rows[CustomerNumber];
					if (count($data)!=0){
					//print_r($data);
						echo "<tr >
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[NumberCIF]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerName]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[CampaignNumber]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[CampaignName]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[ReferalName]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[ReferalPhone1]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[ReferalPhone2]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[ReferalPhone3]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[ReferalDOB]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[ReferalRelasi]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[AgentId]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[Agent]."</td>
								  <td class=\"content lasted\" nowrap>&nbsp;".date('d/m/Y',strtotime($rows[datecreate]))."</td>
							</tr> ";
					}
					
				}
			
		}
		
	}
	
	function More_write_content_by_campaign($CampId=''){
		$CustmId = $this -> getMoreReferalCustomerId($CampId);
		//print_r ($CustmId);
		for ($i=0; $i < count($CustmId);$i++){
			$sql = "SELECT
					b.CustomerNumber,b.NumberCIF, b.CustomerFirstName as CustomerName,
					c.CampaignNumber, c.CampaignName,
					a.ReferalName, DATE(a.ReferalCreateTs)as datecreate,
					a.ReferalPhone1, a.ReferalPhone2, a.ReferalPhone3, a.ReferalDOB, a.ReferalRelasi,
					d.id as AgentId, d.full_name as Agent
				FROM t_gn_referal a
					LEFT JOIN t_gn_customer b ON a.ReferalCustomerId=b.CustomerId
					LEFT JOIN t_gn_campaign c ON b.CampaignId = c.CampaignId
					LEFT JOIN tms_agent d ON a.ReferalSellerId = d.UserId
				WHERE
				a.ReferalCustomerId = '".$CustmId[$i]."'";
				$qry = $this -> query($sql);
				foreach($qry -> result_assoc() as $rows )
				{
					$data = $rows[CustomerNumber];
					if (count($data)!=0){
					//print_r($data);
						echo "<tr >
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[NumberCIF]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerName]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[CampaignNumber]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[CampaignName]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[ReferalName]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[ReferalPhone1]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[ReferalPhone2]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[ReferalPhone3]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[ReferalDOB]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[ReferalRelasi]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[AgentId]."</td>
								  <td class=\"content middle\" nowrap>&nbsp;".$rows[Agent]."</td>
								  <td class=\"content lasted\" nowrap>&nbsp;".date('d/m/Y',strtotime($rows[datecreate]))."</td>
							</tr> ";
					}
					
				}
			
		}
		
	}
	
	function getLessReferalCustomerId($Campid){
		$start_date = $this -> getStartDate();
		$end_date = $this -> getEndDate();
		$sql  =" SELECT a.ReferalCustomerId from t_gn_referal a
					RIGHT JOIN t_gn_customer b ON a.ReferalCustomerId = b.CustomerId
					WHERE a.ReferalQAStatus = 1
						AND b.CampaignId = ".$Campid."
						AND DATE(a.ReferalApprovalTs) >= '".$start_date."'
						AND DATE(a.ReferalApprovalTs) <= '".$end_date."'
						GROUP BY a.ReferalCustomerId
					HAVING COUNT( a.ReferalCustomerId ) < 3 ";
			$qry = $this -> query($sql);
			$i=0;		
			foreach($qry -> result_assoc() as $rows )
			{
				$CustmId[$i] = $rows[ReferalCustomerId];
				$i++;
			}
		return $CustmId;
	}

	function getMoreReferalCustomerId($Campid){
		$start_date = $this -> getStartDate();
		$end_date = $this -> getEndDate();
		$sql  =" SELECT a.ReferalCustomerId from t_gn_referal a
					RIGHT JOIN t_gn_customer b ON a.ReferalCustomerId = b.CustomerId
					WHERE a.ReferalQAStatus = 1
						AND b.CampaignId = ".$Campid."
						AND DATE(a.ReferalApprovalTs) >= '".$start_date."'
						AND DATE(a.ReferalApprovalTs) <= '".$end_date."'
						GROUP BY a.ReferalCustomerId
					HAVING COUNT( a.ReferalCustomerId ) >= 3 ";
			$qry = $this -> query($sql);
			$i=0;		
			foreach($qry -> result_assoc() as $rows )
			{
				$CustmId[$i] = $rows[ReferalCustomerId];
				$i++;
			}
		return $CustmId;
	}	
	
	
}
?>