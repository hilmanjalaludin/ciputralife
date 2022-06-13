<?php

class free_pa_report extends index
{
	function free_pa_report()
	{
		$this->start_date = $this -> formatDateEng($_REQUEST['start_date']);
		$this->end_date = $this -> formatDateEng($_REQUEST['end_date']);
	}
	
	function show_content_html()
	{
		mysql::__construct();
		switch($_REQUEST['group_by'])
		{
			case 'campaign' 	: $this -> closing_group_by_campaign(); 	break;
			//case 'manager' 		: $this -> closing_group_by_manager(); 		break;
			//case 'supervisor'	: $this -> closing_group_by_supervisor(); 	break;
			//case 'Telesales'	: $this -> closing_group_by_telesales(); 	break;
			
		}
	}
	
	/* closing_group_by_campaign ***/
	
	function closing_group_by_campaign()
	{
		$this -> write_header_by_campaign();
		foreach( $this -> get_campaign_select() as $keys => $CampaignId )
		{	
			$this -> write_content_by_campaign($CampaignId);
		}
		$this -> write_footer();
	}
	private function write_header_by_campaign()
	{ 
		//echo "<h4><u>{$Parameters -> getUsername()} - {$Parameters -> getFullname()}</u></h4>";
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" >
				<tr>
					<td class=\"header middle\" nowrap>Campaign Name</td>
					<td class=\"header middle\" nowrap>Customer Number</td>
					<td class=\"header middle\" nowrap>Customer Name</td>
					<td class=\"header middle\" nowrap>Referal Name</td>
					<td class=\"header middle\" nowrap>Referal Phone 1</td>
					<td class=\"header middle\" nowrap>Referal Phone 2</td>
					<td class=\"header middle\" nowrap>Referal Phone 3</td>
					<td class=\"header middle\" nowrap>Agent ID</td>
					<td class=\"header middle\" nowrap>Agent Name</td>
					<td class=\"header middle\" nowrap>Created Date</td>
					<td class=\"header lasted\" nowrap>Status</td>
				</tr> "; 
	}
}
?>