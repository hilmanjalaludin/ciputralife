<?php
class report_free_pa extends IndexExcel
{
	var $_xfn;
	function report_free_pa(){
		//$this -> _con = null;
		$this -> _xfn = 'FREE_PA_REPORT';
	}

	public function show_content_excel()
	{
		mysql::__construct();
		$this -> _Excel( $this -> _xfn );
		$this -> _Excel_Header();
		switch($_REQUEST['group_by'])
		{
			case 'campaign' 	: $this -> FreePAByCampaign(); break;
			//case 'Telesales'  	: $this -> ReferalByTelesales(); break; 
			default:
					echo "<h3>Sorry, This report must grouping by campaign!</h3>";
			break;
		}
	}
	
	private function FreePAByCampaign()
	{
		switch($_REQUEST['mode'])
			{
				case 'summary' : $this -> summaryFreePAByCampaign(); break;
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
	function write_header_by_campaign($CampaignId){
		$_conts = $this -> _query_campaign($CampaignId);
		echo "<h4>{$_conts -> result_get_value('CampaignName')}</h4>";
		echo "<table border=0 cellpadding=0 cellspacing=0 width='100%' style='border-collapse:collapse;' align='center'> 
					<tr class=xl152508 style='mso-height-source:userset;height:27.0pt'>
						<td nowrap class=xl602508 align=\"center\">Campaign Name</td>
						<td nowrap class=xl602508 align=\"center\">Customer Number</td>
						<td nowrap class=xl602508 align=\"center\">Customer Name</td>
						<td nowrap class=xl602508 align=\"center\">Agent ID</td>
						<td nowrap class=xl602508 align=\"center\">Agent Name</td>
						<td nowrap class=xl602508 align=\"center\">Referal Count</td>
					</tr>";
	}
	
	function write_footer(){
		$this -> _Excel_Footer();
	}
	/*function summaryFreePAByCampaign(){
		$start_date = $this -> getStartDate();
		$end_date = $this -> getEndDate();
		echo "<table border=0 cellpadding=0 cellspacing=0 width='100%' style='border-collapse:collapse;' align='center'> 
					<tr class=xl152508 style='mso-height-source:userset;height:27.0pt'>
						<td nowrap class=xl602508 align=\"center\">Campaign Name</td>
						<td nowrap class=xl602508 align=\"center\">Customer Number</td>
						<td nowrap class=xl602508 align=\"center\">Customer Name</td>
						<td nowrap class=xl602508 align=\"center\">Agent ID</td>
						<td nowrap class=xl602508 align=\"center\">Agent Name</td>
						<td nowrap class=xl602508 align=\"center\">Referal Count</td>
						&nbsp;
					</tr>";
		foreach( $this -> CampaignId() as $key => $CampId ) 
		{
			$sql  =" SELECT a.CustomerId, a.CustomerNumber, a.CustomerFirstName,d.CampaignName,c.init_name, 
				c.full_name, COUNT(b.ReferalId) AS jml
				from t_gn_customer a
				RIGHT JOIN t_gn_referal b ON a.CustomerId=b.ReferalCustomerId
				RIGHT JOIN tms_agent c ON c.UserId=b.ReferalSellerId
				RIGHT JOIN t_gn_campaign d ON d.CampaignId = a.CampaignId
				WHERE
					b.ReferalQAStatus = 1
					AND a.CampaignId = '$CampId'
					AND a.CustomerFreePA = 1
					AND DATE(b.ReferalApprovalTs) >= '".$start_date."'
					AND DATE(b.ReferalApprovalTs) <= '".$end_date."'
				GROUP BY a.CustomerId ";
					
			$qry = $this -> query($sql);				
			foreach($qry -> result_assoc() as $rows )
			{
				echo "<tr class=xl592508 height=26 style='mso-height-source:userset;height:22.0pt'>
						<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[CampaignName]."</td>
						<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[CustomerNumber]."</td>
						<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[CustomerFirstName]."</td>
						<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[init_name]."</td>
						<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[full_name]."</td>
						<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[jml]."</td>
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
					</tr>";
		$this -> _Excel_Footer();
	
	}*/
	function summaryFreePAByCampaign(){
		foreach( $this -> CampaignId() as $keys => $CampaignId )
			{	
				$this -> write_header_by_campaign($CampaignId);
				$this -> write_content_by_campaign($CampaignId);
				$this -> write_footer();
			}
	}
	function write_content_by_campaign($CampId=''){
		$start_date = $this -> getStartDate();
		$end_date = $this -> getEndDate();
		$sql  =" SELECT a.CustomerId, a.CustomerNumber, a.CustomerFirstName,d.CampaignName,c.init_name, 
				c.full_name, COUNT(b.ReferalId) AS jml
				from t_gn_customer a
				RIGHT JOIN t_gn_referal b ON a.CustomerId=b.ReferalCustomerId
				RIGHT JOIN tms_agent c ON c.UserId=b.ReferalSellerId
				RIGHT JOIN t_gn_campaign d ON d.CampaignId = a.CampaignId
				WHERE
					b.ReferalQAStatus = 1
					AND a.CampaignId = '$CampId'
					AND a.CustomerFreePA = 1
					AND DATE(b.ReferalApprovalTs) >= '".$start_date."'
					AND DATE(b.ReferalApprovalTs) <= '".$end_date."'
				GROUP BY a.CustomerId ";
					
			$qry = $this -> query($sql);				
			foreach($qry -> result_assoc() as $rows )
			{
				echo "<tr class=xl592508 height=26 style='mso-height-source:userset;height:22.0pt'>
						<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[CampaignName]."</td>
						<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[CustomerNumber]."</td>
						<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[CustomerFirstName]."</td>
						<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[init_name]."</td>
						<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[full_name]."</td>
						<td class=xl582508 class=xl582508 style='border-top:none;border-left:none' align=\"right\">".$rows[jml]."</td>
					</tr> ";
			}
			echo "<tr class=xl152508 style='mso-height-source:userset;height:15.0pt'>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
						<td nowrap class=xl602508 align=\"center\">&nbsp;</td>
					</tr>";
	}
}
?>