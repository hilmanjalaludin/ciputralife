<!-- SOF --> 

<?php

class ref_report extends IndexExcel
{
	var $_xfn;
	function ref_report(){
		//$this -> _con = null;
		$this -> _xfn = 'REPORT_REFERAL';
	}

	public function show_content_excel()
	{
		mysql::__construct();
		$this -> _Excel( $this -> _xfn );
		$this -> _Excel_Header();
		switch($_REQUEST['group_by'])
		{
			case 'campaign' 	: $this -> ReferalByCampaign(); break;
			//case 'Telesales'  	: $this -> ReferalByTelesales(); break; 
			default:
					echo "<h3>Sorry, This report must grouping by campaign!</h3>";
			break;
		}
	}
	private function ReferalByCampaign()
	{
		switch($_REQUEST['mode'])
			{
				case 'all' : $this -> AllReferalReportByCampaign(); break;
				case 'less3' : $this -> LessReferalReportByCampaign(); break;
				case 'more3' : $this -> MoreReferalReportByCampaign(); break;
				default:
					echo "<h3>Please select mode !</h3>";
				break;
			}
	}
	private function getStartDate(){
		return $start_date = $this -> formatDateEng($this -> escPost('start_date')); 
	}
	private function getEndDate(){
		return $end_date   = $this -> formatDateEng($this -> escPost('end_date'));
	}	
	private function CampaignId(){
		$Camp_id = explode (',',$_REQUEST['CampaignName']);
	return $Camp_id;
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
	
	
	function AllReferalReportByCampaign(){
		foreach( $this -> CampaignId() as $keys => $CampaignId )
		{	$this -> write_header_by_campaign($CampaignId);
			$this -> All_write_content_by_campaign($CampaignId);
			$this -> write_footer();
		}
	}
	
	function LessReferalReportByCampaign(){
		foreach( $this -> CampaignId() as $keys => $CampaignId )
		{	$this -> write_header_by_campaign($CampaignId);
			$this -> Less_write_content_by_campaign($CampaignId);
			$this -> write_footer();
		}
	}
	function MoreReferalReportByCampaign(){
		foreach( $this -> CampaignId() as $keys => $CampaignId )
		{	
			$this -> write_header_by_campaign($CampaignId);
			$this -> More_write_content_by_campaign($CampaignId);
			$this -> write_footer();
		}
		
	}
	
	function write_header_by_campaign($CampaignId){
		$_conts = $this -> _query_campaign($CampaignId);
		echo "<h4>{$_conts -> result_get_value('CampaignName')}</h4>";
		echo "<table border=0 cellpadding=0 cellspacing=0 width='100%' style='border-collapse:collapse;' align='center'> 
					<tr class=xl152508 style='mso-height-source:userset;height:27.0pt'>
						<td nowrap class=xl602508 align=\"center\">Number CIF</td>
						<td nowrap class=xl602508 align=\"center\">Customer Name</td>
						<td nowrap class=xl602508 align=\"center\">Campaign Number</td>
						<td nowrap class=xl602508 align=\"center\">Campaign Name</td>
						<td nowrap class=xl602508 align=\"center\">Referal Name</td>
						<td nowrap class=xl602508 align=\"center\">Referal Phone 1</td>
						<td nowrap class=xl602508 align=\"center\">Referal Phone 2</td>
						<td nowrap class=xl602508 align=\"center\">Referal Phone 3</td>
						<td nowrap class=xl602508 align=\"center\">Customer DOB</td>
						<td nowrap class=xl602508 align=\"center\">Customer Relasi</td>
						<td nowrap class=xl602508 align=\"center\">Agent ID</td>
						<td nowrap class=xl602508 align=\"center\">Agent Name</td>
						<td nowrap class=xl602508 align=\"center\">Created Date</td>
					</tr>";
	}
	function write_footer(){
		$this -> _Excel_Footer();
	}
	
	function All_write_content_by_campaign($CampId=''){
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
					c.CampaignId = '$CampId'
					AND a.ReferalQAStatus = 1
					AND DATE(a.ReferalApprovalTs) >= '".$start_date."'
					AND DATE(a.ReferalApprovalTs) <= '".$end_date."' ";
		$qry = $this -> query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				echo "<tr class=xl592508 height=26 style='mso-height-source:userset;height:22.0pt'>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[NumberCIF]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[CustomerName]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[CampaignNumber]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[CampaignName]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[ReferalName]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[ReferalPhone1]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[ReferalPhone2]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[ReferalPhone3]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[ReferalDOB]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[ReferalRelasi]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[AgentId]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[Agent]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".date('d/m/Y',strtotime($rows[ReferalCreateTs]))."</td>
				</tr> ";
			}
			echo "<tr class=xl152508 style='mso-height-source:userset;height:15.0pt'>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
					</tr>";
	}
	
	function Less_write_content_by_campaign($CampId=''){
		$CustmId = $this -> getLessReferalCustomerId($CampId);
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
					echo "<tr class=xl592508 height=26 style='mso-height-source:userset;height:22.0pt'>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[NumberCIF]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[CustomerName]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[CampaignNumber]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[CampaignName]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[ReferalName]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[ReferalPhone1]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[ReferalPhone2]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[ReferalPhone3]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[ReferalDOB]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[ReferalRelasi]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[AgentId]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[Agent]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".date('d/m/Y',strtotime($rows[datecreate]))."</td>
				</tr> ";
				}
		}
		echo "<tr class=xl152508 style='mso-height-source:userset;height:15.0pt'>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
					</tr>";
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
					echo "<tr class=xl592508 height=26 style='mso-height-source:userset;height:22.0pt'>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[NumberCIF]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[CustomerName]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[CampaignNumber]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[CampaignName]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[ReferalName]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[ReferalPhone1]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[ReferalPhone2]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[ReferalPhone3]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[ReferalDOB]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[ReferalRelasi]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[AgentId]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[Agent]."</td>
					<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".date('d/m/Y',strtotime($rows[datecreate]))."</td>
				</tr> ";
				}
		}
		echo "<tr class=xl152508 style='mso-height-source:userset;height:15.0pt'>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
					</tr>";
		
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
<!--- EOF -->