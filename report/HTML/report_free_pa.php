<?php
class report_free_pa extends index
{
	function report_free_pa()
	{
		$this->start_date = $this -> formatDateEng($_REQUEST['start_date']);
		$this->end_date = $this -> formatDateEng($_REQUEST['end_date']);
	}
	
	private function get_campaign_select()
	{
		return explode(",", $this -> escPost('CampaignName'));
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
	function write_footer()
	{
		
		echo "<tr >
						  <td class=\"total first\" nowrap>&nbsp;</td>
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
	
	/* main content HTML **/
			
	function show_content_html()
	{
		mysql::__construct();
		switch($_REQUEST['group_by'])
		{
			case 'campaign' 	: $this -> free_group_by_campaign(); 	break;
			//case 'manager' 		: $this -> closing_group_by_manager(); 		break;
			//case 'supervisor'	: $this -> closing_group_by_supervisor(); 	break;
			//case 'Telesales'	: $this -> closing_group_by_telesales(); 	break;
			default:
					echo "<h3>Sorry, This report must grouping by campaign!</h3>";
			break;
			
		}
	}
	
	private function free_group_by_campaign(){
		switch($_REQUEST['mode'])
			{
				case 'summary' : $this -> summaryFreeByCampaign(); break;
				default:
					echo "<h3>Please select mode !</h3>";
				break;
			}
	}
	private function summaryFreeByCampaign(){
		
		foreach( $this -> get_campaign_select() as $keys => $CampaignId )
		{	
			$this -> write_header_by_campaign($CampaignId);
			$this -> write_content_by_campaign($CampaignId);
			$this -> write_footer();
		}
	}
	
	function write_header_by_campaign($Parameters=''){
		$_conts = $this -> _query_campaign($Parameters);
		echo "<h4>{$_conts -> result_get_value('CampaignName')}</h4>";
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" >
				<tr>
					<td class=\"header middle\" nowrap>Customer Number</td>
					<td class=\"header middle\" nowrap>Customer Name</td>
					<td class=\"header middle\" nowrap>Agent ID</td>
					<td class=\"header middle\" nowrap>Agent Name</td>
					<td class=\"header lasted\" nowrap>Referal Count</td>
				</tr> "; 
	}
	
	function write_content_by_campaign($CampId){
	$sql  =" SELECT a.CustomerNumber, a.CustomerFirstName,c.init_name, 
				c.full_name, COUNT(b.ReferalId) AS jml
				from t_gn_customer a
				RIGHT JOIN t_gn_referal b ON a.CustomerId=b.ReferalCustomerId
				RIGHT JOIN tms_agent c ON c.UserId=b.ReferalSellerId
				RIGHT JOIN t_gn_campaign d ON d.CampaignId = a.CampaignId
				WHERE
					b.ReferalQAStatus = 1
					AND a.CampaignId = '$CampId'
					AND a.CustomerFreePA = 1
					AND DATE(b.ReferalApprovalTs) >= '".$this->start_date."'
					AND DATE(b.ReferalApprovalTs) <= '".$this->end_date."'
				GROUP BY a.CustomerId ";
				
					
		$qry = $this -> query($sql);				
		foreach($qry -> result_assoc() as $rows )
		{
			echo "<tr >  
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerNumber]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerFirstName]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[init_name]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[full_name]."</td>
					  <td class=\"content lasted\" nowrap>&nbsp;".$rows[jml]."</td>
				</tr> ";
		}
	}
}

?>