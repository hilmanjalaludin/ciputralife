<?php

class call_table extends index
{
	var $start_date;
	var $end_date;
	var $ReportType;
	var $action;
	var $mode;
	function call_table()
	{
		
	}
	public function show_content_html()
	{
		switch($_REQUEST['mode'])
		{
			case 'hourly' 	: $this -> hourly_call_table(); break;
			case 'daily' 	: $this -> daily_call_table(); break;
			case 'summary' 	: $this -> summary_call_table(); break;
		}
	}
	
	
	
	private function hourly_call_table()
	{
		
		echo"<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\">
				<tr>
					<td rowspan=\"2\" class=\"header first\" align=\"center\">Interval Hour</td>
					<td colspan=\"3\" class=\"header middle\" align=\"center\">Inbound Calls</td>
					<td colspan=\"3\" class=\"header middle\" align=\"center\">Outbound Calls</td>
					<td colspan=\"2\" class=\"header middle\" align=\"center\">Talk Time</td>
					<td colspan=\"2\" class=\"header middle\" align=\"center\">ACW Time</td>
					<td colspan=\"2\" class=\"header middle\" align=\"center\">Handling Time</td>
					<td colspan=\"4\" class=\"header last\" align=\"center\">Activity Time</td>
				 </tr>
				 <tr>
					<td class=\"header middle\">Offered</td>
					<td class=\"header middle\">Answered</td>
					<td class=\"header middle\">Not Answered</td>
					<td class=\"header middle\">Initiated</td>
					<td class=\"header middle\">Connected</td>
					<td class=\"header middle\">Not Connected</td>
					<td class=\"header middle\">Inbound</td>
					<td class=\"header middle\">Outbound</td>
					<td class=\"header middle\">Inbound</td>
					<td class=\"header middle\">Outbound</td>
					<td class=\"header middle\">Inbound</td>
					<td class=\"header middle\">Outbound</td>
					<td class=\"header middle\">Logged In</td>
					<td class=\"header middle\">Available</td>
					<td class=\"header middle\">Busy</td>
					<td class=\"header middle\">Aux</td>
				  </tr>";
		foreach( $this -> getParameterAgent() as $k => $AgentId )
		{
			$agent_id 	= $this -> getCcAgentId($AgentId);
			$start_date = $this -> formatDateEng($_REQUEST['start_date']); 
			$end_date   = $this -> formatDateEng($_REQUEST['end_date']);
			
		/** definer total **/

			$total_summary_call = 0;
			$tot_summary_conected = 0;
			$tot_summary_not_conected = 0;
			$talk_summary_time = 0;
			
		/**  definer ******/
			
			$tot_call = '';
			$tot_conected ='';
			$tot_not_conected = '';
			$talk_time ='';
		
			echo "<tr bgcolor=\"#EEEEEE\">
					<td class=\"content agent first\" colspan=\"17\">&nbsp;".$agent_id['Fullname']."</td>
				</tr> ";
				
				
			while(true)
			{
			
				/** definer total **/

				$total_summary_call = 0;
				$tot_summary_conected = 0;
				$tot_summary_not_conected = 0;
				$talk_summary_time = 0;
				
			/**  definer ******/
				
				$tot_call = '';
				$tot_conected ='';
				$tot_not_conected = '';
				$talk_time ='';
				
				$estart_date = $start_date;
				echo "	<tr >
							<td class=\"content tanggal first\" colspan=\"17\">&nbsp;".$estart_date."</td>
						</tr> ";
						
						
				/** SQL QUERY ***/
				
					$sql = "select 
								date_format(a.start_time,'%H') as `hour`,
								count(a.id) as tot_call,  
								SUM(IF( a.`status` IN(3004,3005),1,0)) as tot_conected,
								SUM(IF( a.`status` NOT IN(3004,3005),1,0)) as tot_not_conected,
								SUM(IF( a.`status` IN(3004,3005),(UNIX_TIMESTAMP(a.end_time) - UNIX_TIMESTAMP(a.agent_time)),0)) as talk_time  
								
								from cc_call_session a  
								where a.agent_id = '$agent_id[AgentId]'
								and date(a.start_time)>='$estart_date'
								and date(a.start_time)<='$estart_date'
								group by `hour` ";
								
					
					$qry = $this ->query($sql);
					foreach( $qry -> result_assoc() as $rows )
					{
						$tot_call[$rows['hour']] = $rows['tot_call'];
						$tot_conected[$rows['hour']]= $rows['tot_conected'];
						$tot_not_conected[$rows['hour']]= $rows['tot_not_conected'];
						$talk_time[$rows['hour']]= $rows['talk_time'];
					}
			
					
				#  CS :: HOURLY 
				
					for ($i=7; $i<=20; $i++)
					{
						$s_i = (strlen($i)==1)?"0".$i:$i;			
						echo "<tr>
								<td class=\"content first\">&nbsp;{$s_i}:00-{$s_i}:59</td>
								<td class=\"content middle\">&nbsp;</td>
								<td class=\"content middle\">&nbsp;</td>
								<td class=\"content middle\">&nbsp;</td>
								<td class=\"content middle\">&nbsp;".($tot_call[$s_i]?$tot_call[$s_i]:'-')."</td>
								<td class=\"content middle\">&nbsp;".($tot_conected[$s_i]?$tot_conected[$s_i]:'-')."</td>
								<td class=\"content middle\">&nbsp;".($tot_not_conected[$s_i]?$tot_not_conected[$s_i]:'-')."</td>
								<td class=\"content middle\">&nbsp;</td>
								<td class=\"content middle\">&nbsp;".($talk_time[$s_i]?toDuration($talk_time[$s_i]):'-')."</td>
								<td class=\"content middle\">&nbsp;</td>
								<td class=\"content middle\">&nbsp;</td>
								<td class=\"content middle\">&nbsp;</td>
								<td class=\"content middle\">&nbsp;</td>
								<td class=\"content middle\">&nbsp;</td>
								<td class=\"content middle\">&nbsp;</td>
								<td class=\"content middle\">&nbsp;</td>
								<td class=\"content lasted\">&nbsp;</td>
						 </tr> ";		
					}
					
						echo "<tr>
								<td class=\"content subtotal first\">&nbsp;Sub Total</td>
								<td class=\"content subtotal middle\">&nbsp;</td>
								<td class=\"content subtotal middle\">&nbsp;</td>
								<td class=\"content subtotal middle\">&nbsp;</td>
								<td class=\"content subtotal middle\">&nbsp;</td>
								<td class=\"content subtotal middle\">&nbsp;</td>
								<td class=\"content subtotal middle\">&nbsp;</td>
								<td class=\"content subtotal middle\">&nbsp;</td>
								<td class=\"content subtotal middle\">&nbsp;</td>
								<td class=\"content subtotal middle\">&nbsp;</td>
								<td class=\"content subtotal middle\">&nbsp;</td>
								<td class=\"content subtotal middle\">&nbsp;</td>
								<td class=\"content subtotal middle\">&nbsp;</td>
								<td class=\"content subtotal middle\">&nbsp;</td>
								<td class=\"content subtotal middle\">&nbsp;</td>
								<td class=\"content subtotal middle\">&nbsp;</td>
								<td class=\"content subtotal lasted\">&nbsp;</td>
						 </tr> ";	
					
					if( $start_date == $end_date ) break;
						$start_date = $this ->nextdate($start_date);
						
				#  CE :: HOURLY 
			}
		}
	}
	
/** DAILY SECTION ****/
	
	private function daily_call_table()
	{
		echo"<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\">
				<tr>
					<td rowspan=\"2\" class=\"header first\" align=\"center\">Summary date</td>
					<td colspan=\"3\" class=\"header middle\" align=\"center\">Inbound Calls</td>
					<td colspan=\"3\" class=\"header middle\" align=\"center\">Outbound Calls</td>
					<td colspan=\"2\" class=\"header middle\" align=\"center\">Talk Time</td>
					<td colspan=\"2\" class=\"header middle\" align=\"center\">ACW Time</td>
					<td colspan=\"2\" class=\"header middle\" align=\"center\">Handling Time</td>
					<td colspan=\"4\" class=\"header last\" align=\"center\">Activity Time</td>
				 </tr>
				 <tr>
					<td class=\"header middle\">Offered</td>
					<td class=\"header middle\">Answered</td>
					<td class=\"header middle\">Not Answered</td>
					<td class=\"header middle\">Initiated</td>
					<td class=\"header middle\">Connected</td>
					<td class=\"header middle\">Not Connected</td>
					<td class=\"header middle\">Inbound</td>
					<td class=\"header middle\">Outbound</td>
					<td class=\"header middle\">Inbound</td>
					<td class=\"header middle\">Outbound</td>
					<td class=\"header middle\">Inbound</td>
					<td class=\"header middle\">Outbound</td>
					<td class=\"header middle\">Logged In</td>
					<td class=\"header middle\">Available</td>
					<td class=\"header middle\">Busy</td>
					<td class=\"header middle\">Aux</td>
				  </tr>";
		
		foreach( $this -> getParameterAgent() as $k => $AgentId )
		{
			$agent_id 	= $this -> getCcAgentId($AgentId);
			$start_date = $this -> formatDateEng($_REQUEST['start_date']); 
			$end_date   = $this -> formatDateEng($_REQUEST['end_date']);
			
		/** definer total **/

			$total_summary_call = 0;
			$tot_summary_conected = 0;
			$tot_summary_not_conected = 0;
			$talk_summary_time = 0;
			
		/**  definer ******/
			
			$tot_call = '';
			$tot_conected ='';
			$tot_not_conected = '';
			$talk_time ='';
		
			echo "<tr bgcolor=\"#EEEEEE\">
					<td class=\"content agent first\" colspan=\"17\">&nbsp;".$agent_id['Fullname']."</td>
				</tr> ";
					 
		/** sql aing **/
		
			$sql ="
					select 
					date(a.start_time) as tgl,
					count(a.id) as tot_call,  
					SUM(IF( a.`status` IN(3004,3005),1,0)) as tot_conected,
					SUM(IF( a.`status` NOT IN(3004,3005),1,0)) as tot_not_conected,
					SUM(IF( a.`status` IN(3004,3005),(UNIX_TIMESTAMP(a.end_time) - UNIX_TIMESTAMP(a.agent_time)),0)) as talk_time  
					
					from cc_call_session a 
					where a.agent_id = '".$agent_id['AgentId']."'
					group by tgl";
			
			$qry = $this ->query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
				$tot_call[$rows['tgl']] = $rows['tot_call'];
				$tot_conected[$rows['tgl']]= $rows['tot_conected'];
				$tot_not_conected[$rows['tgl']]= $rows['tot_not_conected'];
				$talk_time[$rows['tgl']]= $rows['talk_time'];
			}

			while(true)
			{
				$estart_date = $start_date;
				echo "<tr>
						<td class=\"content first\">&nbsp;".$estart_date."</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content middle\">&nbsp;".($tot_call[$estart_date]?$tot_call[$estart_date]:'-')."</td>
						<td class=\"content middle\">&nbsp;".($tot_conected[$estart_date]?$tot_conected[$estart_date]:'-')."</td>
						<td class=\"content middle\">&nbsp;".($tot_not_conected[$estart_date]?$tot_not_conected[$estart_date]:'-')."</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content middle\">&nbsp;".($talk_time[$estart_date]?toDuration($talk_time[$estart_date]):'-')."</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content lasted\">&nbsp;</td>
					 </tr> ";
					 
				$total_summary_call += $tot_call[$estart_date];
				$tot_summary_conected += $tot_conected[$estart_date];
				$tot_summary_not_conected += $tot_not_conected[$estart_date];
				$talk_summary_time += $talk_time[$estart_date];
			
				if( $start_date == $end_date ) break;
					$start_date = $this ->nextdate($start_date);
			}
			echo "<tr bgcolor=\"#EEEEEE\">
						<td class=\"content subtotal first\">&nbsp;Sub total</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;".$total_summary_call."</td>
						<td class=\"content subtotal middle\">&nbsp;".$tot_summary_conected."</td>
						<td class=\"content subtotal middle\">&nbsp;".$tot_summary_not_conected."</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;".toDuration($talk_summary_time)."</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal lasted\">&nbsp;</td>
					 </tr> 
					 <tr>
						<td colspan=\"17\">&nbsp;</td>
					</tr>";	
		
		}
		//$this -> writeFooterTable();
		
	}
	
	#SUMMARY SECTION
	
	private function summary_call_table()
	{
		echo"<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\">
				<tr>
					<td rowspan=\"2\" class=\"header first\" align=\"center\">Agent Name</td>
					<td rowspan=\"2\" class=\"header first\" align=\"center\">Agent Code</td>
					<td colspan=\"3\" class=\"header middle\" align=\"center\">Inbound Calls</td>
					<td colspan=\"3\" class=\"header middle\" align=\"center\">Outbound Calls</td>
					<td colspan=\"2\" class=\"header middle\" align=\"center\">Talk Time</td>
					<td colspan=\"2\" class=\"header middle\" align=\"center\">ACW Time</td>
					<td colspan=\"2\" class=\"header middle\" align=\"center\">Handling Time</td>
					<td colspan=\"4\" class=\"header last\" align=\"center\">Activity Time</td>
				 </tr>
				 <tr>
					<td class=\"header middle\">Offered</td>
					<td class=\"header middle\">Answered</td>
					<td class=\"header middle\">Not Answered</td>
					<td class=\"header middle\">Initiated</td>
					<td class=\"header middle\">Connected</td>
					<td class=\"header middle\">Not Connected</td>
					<td class=\"header middle\">Inbound</td>
					<td class=\"header middle\">Outbound</td>
					<td class=\"header middle\">Inbound</td>
					<td class=\"header middle\">Outbound</td>
					<td class=\"header middle\">Inbound</td>
					<td class=\"header middle\">Outbound</td>
					<td class=\"header middle\">Logged In</td>
					<td class=\"header middle\">Available</td>
					<td class=\"header middle\">Busy</td>
					<td class=\"header middle\">Aux</td>
				  </tr>";
				  
		foreach( $this -> getSpvId() as $k => $spv )
		{
			
			echo "<tr> <td class=\"content agent first\" colspan=\"18\">&nbsp;". $spv['fullname'] ."</td> </tr> ";
					
			$start_date = $this -> formatDateEng($_REQUEST['start_date']); 
			$end_date   = $this -> formatDateEng($_REQUEST['end_date']);
			
			
			/** definer total **/

			$total_summary_call = 0;
			$tot_summary_conected = 0;
			$tot_summary_not_conected = 0;
			$talk_summary_time = 0; 
				
			/**  definer ******/
				
			$agent_name = '';
			$tot_call = '';
			$tot_conected ='';
			$tot_not_conected = '';
			$talk_time ='';
							
			/** set sql ***/
			
			$sql =" SELECT 
					a.agent_id as agent,
					count(a.id) as tot_call,  
					SUM(IF( a.`status` IN(3004,3005),1,0)) as tot_conected,
					SUM(IF( a.`status` NOT IN(3004,3005),1,0)) as tot_not_conected,
					SUM(IF( a.`status` IN(3004,3005),(UNIX_TIMESTAMP(a.end_time) - UNIX_TIMESTAMP(a.agent_time)),0)) as talk_time  
					FROM cc_call_session a 
					WHERE 1=1
					and date(a.start_time)>='$start_date'
					and date(a.start_time)<='$end_date'
					GROUP BY agent";
				
			$qry = $this ->query($sql);
			foreach( $qry -> result_assoc() as $rows )
			{
					$tot_call[$rows['agent']] = $rows['tot_call'];
					$tot_conected[$rows['agent']]= $rows['tot_conected'];
					$tot_not_conected[$rows['agent']]= $rows['tot_not_conected'];
					$talk_time[$rows['agent']]= $rows['talk_time'];
			}	
			
			/** list data by cc-agent-id **/
			
			foreach( $this -> getAgentBySpvId($spv['UserId']) as $rows )
			{
					$cc_id = $rows['cc_id'];
					
					echo "<tr>
						<td class=\"content first\">&nbsp;{$rows['full_name']}</td>
						<td class=\"content middle\">&nbsp;{$rows['username']}</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content middle\">&nbsp;".($tot_call[$cc_id]?$tot_call[$cc_id]:'-')."</td>
						<td class=\"content middle\">&nbsp;".($tot_conected[$cc_id]?$tot_conected[$cc_id]:'-')."</td>
						<td class=\"content middle\">&nbsp;".($tot_not_conected[$cc_id]?$tot_not_conected[$cc_id]:'-')."</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content middle\">&nbsp;".($talk_time[$cc_id]?toDuration($talk_time[$cc_id]):'-')."</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content middle\">&nbsp;</td>
						<td class=\"content lasted\">&nbsp;</td>
					 </tr> ";
					 
				$total_summary_call += $tot_call[$cc_id];
				$tot_summary_conected += $tot_conected[$cc_id];
				$tot_summary_not_conected += $tot_not_conected[$cc_id];
				$talk_summary_time += $talk_time[$cc_id];
			}
			
			echo "<tr bgcolor=\"#EEEEEE\">
						<td class=\"content subtotal first\">&nbsp;Sub total</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;".$total_summary_call."</td>
						<td class=\"content subtotal middle\">&nbsp;".$tot_summary_conected."</td>
						<td class=\"content subtotal middle\">&nbsp;".$tot_summary_not_conected."</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;".toDuration($talk_summary_time)."</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal middle\">&nbsp;</td>
						<td class=\"content subtotal lasted\">&nbsp;</td>
					 </tr> 
					 <tr>
						<td colspan=\"17\">&nbsp;</td>
					</tr>";	
		} 
		
		
	}
}
?>