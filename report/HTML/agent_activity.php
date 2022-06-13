<?php
class agent_activity extends index
{
	
	
	function agent_activity()
	{
		// default on parent
		//echo "<title>xxx</title>";
		//$this -> styleCss();
	}
	
	
/** html content ***/
	
	public function show_content_html()
	{
		switch($_REQUEST['mode'])
		{
			case 'hourly' 	: $this -> hourly_agent_activity(); break;
			case 'daily' 	: $this -> daily_agent_activity(); break;
			case 'summary' 	: $this -> summary_agent_activity(); break;
		}
	}
	
	function setQuery($agent_id='',$start_date='',$status='')
	{
		$sql = "Select 
					a.agent, 
					date(a.start_time) as tgl,
					sum(unix_timestamp(a.end_time)-unix_timestamp(a.start_time)) as duration 
					from cc_agent_activity_log a 
					where a.agent='$agent_id'";
		switch($status)
		{
			case 1://ready
				$sql .= "AND a.status = '$status' AND reason='1'";
				break;
				
			case 2://not ready(AUX)
				$sql .= "AND a.status = '$status'";
				break;
			
			case 3://acw
				$sql .= "AND a.status > 0";
				break;
				
			case 4://busy
				$sql .= "AND a.status = $status";
				break;
				
			default:
				$where = "";
				break;
		}
		
		$sql .=		"AND DATE(a.start_time) >= '$start_date'
					AND DATE(a.start_time) <= '$start_date'
					group by tgl";	
		return $sql;
	}
	
	function setQueryHour($agent_id='',$start_date='',$status='')
	{
		$sql = "Select 
					a.agent, 
					date_format(a.start_time,'%H') as `hour`,
					sum(unix_timestamp(a.end_time)-unix_timestamp(a.start_time)) as duration 
					from cc_agent_activity_log a 
					where a.agent='$agent_id'";
		switch($status)
		{
			case 1://ready
				$sql .= "AND a.status = '$status' AND reason='1'";
				break;
				
			case 2://not ready(AUX)
				$sql .= "AND a.status = '$status'";
				break;
			
			case 3://acw
				$sql .= "AND a.status > 0";
				break;
				
			case 4://busy
				$sql .= "AND a.status = $status";
				break;
				
			default:
				$where = "";
				break;
		}
		
		$sql .=		"AND DATE(a.start_time) >= '$start_date'
					AND DATE(a.start_time) <= '$start_date'
					group by `hour`";	

		return $sql;
	}
	
	function setQuerySummary($agent_id='',$start_date='',$end_date='',$status='')
	{
		$sql = "Select 
					a.agent,
					b.name,
					sum(unix_timestamp(a.end_time)-unix_timestamp(a.start_time)) as duration 
					from cc_agent_activity_log a 
					left join cc_agent b on a.agent=b.id 
					where a.agent='$agent_id'";
		switch($status)
		{
			case 1://ready
				$sql .= "AND a.status = '$status' AND reason='1'";
				break;
				
			case 2://not ready(AUX)
				$sql .= "AND a.status = '$status'";
				break;
			
			case 3://acw
				$sql .= "AND a.status > 0";
				break;
				
			case 4://busy
				$sql .= "AND a.status = $status";
				break;
				
			default:
				$where = "";
				break;
		}
		
		$sql .=		"AND DATE(a.start_time) >= '$start_date'
					 AND DATE(a.end_time)   <= '$end_date'	
					 group by a.agent";
		return $sql;
	}
	

	
/** create footer table **/

	private function writeFooterTable()
	{
	
		echo "	
			</table> ";	
	}
		
	
/** hourly ***/
	
	private function summary_agent_activity()
	{
		echo "<table class=\"grid\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
				<tr>
					<td class=\"header first\"  rowspan=\"2\">Agent Name</td>
					<td class=\"header middle\" rowspan=\"2\">Logged In Time</td>
					<td class=\"header middle\" rowspan=\"2\">Available Time</td>
					<td class=\"header middle\" rowspan=\"2\">AUX Time</td>
					<td class=\"header middle\" colspan='4'>Inbound Activity</td>
					<td class=\"header middle\" colspan='4'>Outbound Activity</td>
					<td class=\"header middle\" rowspan=\"2\">Internal Call</td>
					<td class=\"header lasted\" rowspan=\"2\">Internal External</td>
				</tr>
				<tr>
					<td class=\"header middle\">Answer Calls</td>
					<td class=\"header middle\">Talk Time</td>
					<td class=\"header middle\">ACW Time</td>
					<td class=\"header middle\">Handling Time</td>
					<td class=\"header middle\">Connected Calls</td>
					<td class=\"header middle\">Talk Time</td>
					<td class=\"header middle\">ACW Time</td>
					<td class=\"header middle\">Handling Time</td>
				</tr> ";
				
		$grandtotal_login = 0;
		$grandtotal_ready = 0;
		$grandtotal_aux	  = 0;
		
		foreach( $this -> getParameterAgent() as $k => $AgentId )
		{
			$agent_id 	= $this -> getCcAgentId($AgentId);
			$spv		= $this -> getAgentBySpvId($agent_id['leader']);
			$start_date = $this -> formatDateEng($_REQUEST['start_date']); 
			$end_date   = $this -> formatDateEng($_REQUEST['end_date']);
			
			/** definer total **/
			
			$total_login = 0;
			$total_ready = 0;
			$total_aux	 = 0;
			
			/**  definer ******/
			
			$tot_login 	= '';
			$tot_ready 	= '';
			$tot_aux 	= '';
			
			//////////////////////////////////////////////////////////////////////////////////////////
			
			$sql  = $this -> setQuerySummary($agent_id[AgentId],$start_date,$end_date,'');
			$qry  = $this -> query($sql);
			
			
			
			foreach( $qry -> result_assoc() as $rows )
			{
				$tot_login = $rows['duration'];
			}
			
			//////////////////////////////////////////////////////////////////////////////////////////
			
			$sql1 = $this -> setQuerySummary($agent_id[AgentId],$start_date,$end_date,1);
			$qry1 = $this -> query($sql1);
			
			
			
			foreach( $qry1 -> result_assoc() as $rows )
			{
				$tot_ready = $rows['duration'];
			}
			
			////////////////////////////////////////////////////////////////////////////////////////////////
			
			$sql2 = $this -> setQuerySummary($agent_id[AgentId],$start_date,$end_date,2);
			$qry2 = $this -> query($sql2);
			
			
			
			foreach( $qry2 -> result_assoc() as $rows )
			{
				$tot_aux = $rows['duration'];
			}
			
			$tm = $agent_id['Fullname'];
			echo "<tr>
						<td class=\"content first\">".$tm."</td>
						<td class=\"content middle\">".$tot_login[$tm]."</td>
						<td class=\"content middle\">".$tot_ready[$tm]."</td>
						<td class=\"content middle\">".$tot_aux[$tm]."</td>
						<td class=\"content middle\">-</td>
						<td class=\"content middle\">-</td>
						<td class=\"content middle\">-</td>
						<td class=\"content middle\">-</td>
						<td class=\"content middle\">-</td>
						<td class=\"content middle\">-</td>
						<td class=\"content middle\">-</td>
						<td class=\"content middle\">-</td>
						<td class=\"content middle\">-</td>
						<td class=\"content lasted\">-</td>
					 </tr>";
			/////////////////////////////////////////////////////////////////////////////////////////////////
		}
		$this ->writeFooterTable();
	}
	
/** daily ***/
	
	private function daily_agent_activity()
	{
		echo "<table class=\"grid\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
				
				<tr>
					<td class=\"header first\"  rowspan=\"2\">Summary Date</td>
					<td class=\"header middle\" rowspan=\"2\">Logged In Time</td>
					<td class=\"header middle\" rowspan=\"2\">Available Time</td>
					<td class=\"header middle\" rowspan=\"2\">AUX Time</td>
					<td class=\"header middle\" colspan='4'>Inbound Activity</td>
					<td class=\"header middle\" colspan='4'>Outbound Activity</td>
					<td class=\"header middle\" rowspan=\"2\">Internal Call</td>
					<td class=\"header lasted\" rowspan=\"2\">Internal External</td>
				</tr>
				<tr>
					<td class=\"header middle\">Answer Calls</td>
					<td class=\"header middle\">Talk Time</td>
					<td class=\"header middle\">ACW Time</td>
					<td class=\"header middle\">Handling Time</td>
					<td class=\"header middle\">Connected Calls</td>
					<td class=\"header middle\">Talk Time</td>
					<td class=\"header middle\">ACW Time</td>
					<td class=\"header middle\">Handling Time</td>
				</tr>";
		$grandtotal_login = 0;
		$grandtotal_ready = 0;
		$grandtotal_aux	 = 0;	

		
		
		foreach( $this -> getParameterAgent() as $k => $AgentId )
		{
			$agent_id 	= $this -> getCcAgentId($AgentId);
			$spv		= $this -> getAgentBySpvId($agent_id['leader']);
			$start_date = $this -> formatDateEng($_REQUEST['start_date']); 
			$end_date   = $this -> formatDateEng($_REQUEST['end_date']);
			
			echo "<tr>
					<td colspan=\"14\" class=\"agent\">Agent : ".$agent_id['Fullname']."</td>
				</tr>";
					
			/** definer total **/
			
			$total_login = 0;
			$total_ready = 0;
			$total_aux	 = 0;
			
			/**  definer ******/
			
			$tot_login 	= '';
			$tot_ready 	= '';
			$tot_aux 	= '';
			
			while(true)
			{
				// duration Login
				$sql  = $this -> setQuery($agent_id[AgentId],$start_date,'');
				$qry  = $this -> query($sql);
				foreach( $qry -> result_assoc() as $rows )
				{
					$tot_login[$rows['tgl']] = $rows['duration'];
				}
				
				//duration Ready
				$sql1 = $this -> setQuery($agent_id[AgentId],$start_date,1);
				$qry1 = $this -> query($sql1);
				foreach( $qry1 -> result_assoc() as $rows )
				{
					$tot_ready[$rows['tgl']] = $rows['duration'];
				}
				
				//duration AUX
				$sql2 = $this -> setQuery($agent_id[AgentId],$start_date,2);
				$qry2 = $this -> query($sql2);
				foreach( $qry2 -> result_assoc() as $rows )
				{
					$tot_aux[$rows['tgl']] = $rows['duration'];
				}
				
				$estart_date = $start_date;
				
				echo "<tr>
						<td class=\"content first\">".$estart_date."</td>
						<td class=\"content middle\">".(toDuration($tot_login[$estart_date])?toDuration($tot_login[$estart_date]):'-')."</td>
						<td class=\"content middle\">".(toDuration($tot_ready[$estart_date])?toDuration($tot_ready[$estart_date]):'-')."</td>
						<td class=\"content middle\">".(toDuration($tot_aux[$estart_date])?toDuration($tot_aux[$estart_date]):'-')."</td>
						<td class=\"content middle\">-</td>
						<td class=\"content middle\">-</td>
						<td class=\"content middle\">-</td>
						<td class=\"content middle\">-</td>
						<td class=\"content middle\">-</td>
						<td class=\"content middle\">-</td>
						<td class=\"content middle\">-</td>
						<td class=\"content middle\">-</td>
						<td class=\"content middle\">-</td>
						<td class=\"content lasted\">-</td>
					</tr>";
				 
				 $total_login += $tot_login[$estart_date];
				 $total_ready += $tot_ready[$estart_date];
				 $total_aux	  += $tot_aux[$estart_date];
				 
				if( $start_date == $end_date ) break;
					$start_date = $this ->nextdate($start_date);
			}
			
	/** subtotal total **/
			
			echo "<tr>
					<td class=\"content subtotal first\">Sub Total</td>
					<td class=\"content subtotal middle\">".(toDuration($total_login)?toDuration($total_login):'0')."</td>
					<td class=\"content subtotal middle\">".(toDuration($total_ready)?toDuration($total_ready):'0')."</td>
					<td class=\"content subtotal middle\">".(toDuration($total_aux)?toDuration($total_aux):'0')."</td>
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
				</tr>";
				
			$grandtotal_login += $total_login;
			$grandtotal_ready += $total_ready;
			$grandtotal_aux	 += $total_aux;	
		}
		
	/** grand total **/
	
		echo "<tr>
					<td class=\"total\">Grand Total</td>
					<td class=\"total\">".(toDuration($grandtotal_login)?toDuration($grandtotal_login):'0')."</td>
					<td class=\"total\">".(toDuration($grandtotal_ready)?toDuration($grandtotal_ready):'0')."</td>
					<td class=\"total\">".(toDuration($grandtotal_aux)?toDuration($grandtotal_aux):'0')."</td>
					<td class=\"total\">&nbsp;</td>
					<td class=\"total\">&nbsp;</td>
					<td class=\"total\">&nbsp;</td>
					<td class=\"total\">&nbsp;</td>
					<td class=\"total\">&nbsp;</td>
					<td class=\"total\">&nbsp;</td>
					<td class=\"total\">&nbsp;</td>
					<td class=\"total\">&nbsp;</td>
					<td class=\"total\">&nbsp;</td>
					<td class=\"total\">&nbsp;</td>
				 </tr>";
		$this ->writeFooterTable();
		
	}	

/** Summary ***/
	
	private function hourly_agent_activity()
	{
		echo "<table class=\"grid\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
				
				<tr>
					<td class=\"header first\"  rowspan=\"2\">Summary Date</td>
					<td class=\"header middle\" rowspan=\"2\">Logged In Time</td>
					<td class=\"header middle\" rowspan=\"2\">Available Time</td>
					<td class=\"header middle\" rowspan=\"2\">AUX Time</td>
					<td class=\"header middle\" colspan='4'>Inbound Activity</td>
					<td class=\"header middle\" colspan='4'>Outbound Activity</td>
					<td class=\"header middle\" rowspan=\"2\">Internal Call</td>
					<td class=\"header lasted\" rowspan=\"2\">Internal External</td>
				</tr>
				<tr>
					<td class=\"header middle\">Answer Calls</td>
					<td class=\"header middle\">Talk Time</td>
					<td class=\"header middle\">ACW Time</td>
					<td class=\"header middle\">Handling Time</td>
					<td class=\"header middle\">Connected Calls</td>
					<td class=\"header middle\">Talk Time</td>
					<td class=\"header middle\">ACW Time</td>
					<td class=\"header middle\">Handling Time</td>
				</tr>";
				
		$grandtotal_login = 0;
		$grandtotal_ready = 0;
		$grandtotal_aux	 = 0;		
		
		foreach( $this -> getParameterAgent() as $k => $AgentId )
		{
			$agent_id 	= $this -> getCcAgentId($AgentId);
			$start_date = $this -> formatDateEng($_REQUEST['start_date']); 
			$end_date   = $this -> formatDateEng($_REQUEST['end_date']);
			
			
			
			echo "	<tr><td colspan=\"14\" class=\"agent\">&nbsp;</td></tr>
					<tr><td colspan=\"14\" class=\"agent\">Agent : ".$agent_id['Fullname']."</td></tr>";
					
					
			while(true)
			{
				
				/**  definer ******/
				$tot_login 	= '';
				$tot_ready 	= '';
				$tot_aux 	= '';
				
				/** definer total **/
			
				$total_login = 0;
				$total_ready = 0;
				$total_aux	 = 0;
				
				$estart_date = $start_date;
				$sql  = $this -> setQueryHour($agent_id[AgentId],$start_date,'');
				$sql1 = $this -> setQueryHour($agent_id[AgentId],$start_date,1);
				$sql2 = $this -> setQueryHour($agent_id[AgentId],$start_date,2);
				
				$qry = $this ->query($sql);
				$qry1 = $this ->query($sql1);
				$qry2 = $this ->query($sql2);
				
				foreach( $qry -> result_assoc() as $rows )
				{
					$tot_login[$rows['hour']] = $rows['duration'];
				}
				
				foreach( $qry1 -> result_assoc() as $rows )
				{
					$tot_ready[$rows['hour']] = $rows['duration'];
				}
				
				foreach( $qry2 -> result_assoc() as $rows )
				{
					$tot_aux[$rows['hour']] = $rows['duration'];
				}
								
				
				echo "
					<tr>
						<td class=\"content tanggal first \" colspan=\"14\">".$estart_date."</td>
					</tr>";
					
				for ($i=7; $i<=21; $i++) 
				{
						$s_i = (strlen($i)==1)?"0".$i:$i;	
						
						
						echo "<tr>
							<td class=\"content first\">&nbsp;{$s_i}:00-{$s_i}:59</td>
							<td class=\"content middle\">&nbsp;".(toDuration($tot_login[$s_i])?toDuration($tot_login[$s_i]):'-')."</td>
							<td class=\"content middle\">&nbsp;".(toDuration($tot_ready[$s_i])?toDuration($tot_ready[$s_i]):'-')."</td>
							<td class=\"content middle\">&nbsp;".(toDuration($tot_aux[$s_i])?toDuration($tot_aux[$s_i]):'-')."</td>
							<td class=\"content middle\">-</td>
							<td class=\"content middle\">-</td>
							<td class=\"content middle\">-</td>
							<td class=\"content middle\">-</td>
							<td class=\"content middle\">-</td>
							<td class=\"content middle\">-</td>
							<td class=\"content middle\">-</td>
							<td class=\"content middle\">-</td>
							<td class=\"content middle\">-</td>
							<td class=\"content lasted\">-</td>
						 </tr> ";
						 
						 $total_login += $tot_login[$s_i];
						 $total_ready += $tot_ready[$s_i];
						 $total_aux	  += $tot_aux[$s_i];
				}
				echo "<tr>
					<td class=\"content subtotal first\">Sub Total</td>
					<td class=\"content subtotal middle\">".(toDuration($total_login)?toDuration($total_login):'-')."</td>
					<td class=\"content subtotal middle\">".(toDuration($total_ready)?toDuration($total_ready):'-')."</td>
					<td class=\"content subtotal middle\">".(toDuration($total_aux)?toDuration($total_aux):'-')."</td>
					<td class=\"content subtotal middle\">-</td>
					<td class=\"content subtotal middle\">-</td>
					<td class=\"content subtotal middle\">-</td>
					<td class=\"content subtotal middle\">-</td>
					<td class=\"content subtotal middle\">-</td>
					<td class=\"content subtotal middle\">-</td>
					<td class=\"content subtotal middle\">-</td>
					<td class=\"content subtotal middle\">-</td>
					<td class=\"content subtotal middle\">-</td>
					<td class=\"content subtotal lasted\">-</td>
				 </tr>";
				$grandtotal_login += $total_login;
				$grandtotal_ready += $total_ready;
				$grandtotal_aux	 += $total_aux;	
				
				if( $start_date == $end_date ) break;
					$start_date = $this ->nextdate($start_date);
			}
			
		}
		echo "<tr>
					<td class=\"total\">Grand Total</td>
					<td class=\"total\">".(toDuration($grandtotal_login)?toDuration($grandtotal_login):'00:00:00')."</td>
					<td class=\"total\">".(toDuration($grandtotal_ready)?toDuration($grandtotal_ready):'00:00:00')."</td>
					<td class=\"total\">".(toDuration($grandtotal_aux)?toDuration($grandtotal_aux):'00:00:00')."</td>
					<td class=\"total\">&nbsp;</td>
					<td class=\"total\">&nbsp;</td>
					<td class=\"total\">&nbsp;</td>
					<td class=\"total\">&nbsp;</td>
					<td class=\"total\">&nbsp;</td>
					<td class=\"total\">&nbsp;</td>
					<td class=\"total\">&nbsp;</td>
					<td class=\"total\">&nbsp;</td>
					<td class=\"total\">&nbsp;</td>
					<td class=\"total\">&nbsp;</td>
				 </tr>";
		$this ->writeFooterTable();
	}		
	
	
	function show_content_excel(){
		echo "hello world excel";
	}
}
?>