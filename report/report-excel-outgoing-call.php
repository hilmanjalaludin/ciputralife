<?php

class outCallExcel extends v_index{

	function __construct(){ parent::__construct();}
	
	function destruct(){}
	
	function Excel(){ $this -> Daily();}
	
	function Daily(){
		$agentid = explode(",", $_REQUEST['agent']);
		$excel = new excel();
		$excel -> xlsWriteHeader("ReportOutgoingCall");
		
	/** title page **/	
	
		$xlsRows = 0;
		$excel -> xlsWriteLabel($xlsRows,0,"Report - Outgoing Call");
		$xlsRows = $xlsRows+1;
		$excel -> xlsWriteLabel($xlsRows,0,"Periode : ".$this ->to_value_date($_REQUEST['start_date'])." - ".$this ->to_value_date($_REQUEST['end_date']));
		
		
		$xlsRows = $xlsRows+1;
		
	/** Daily Report **/
	
		foreach($agentid as $key => $agent ){
		
	/* pre define variabel **/
		
			$totOffered 			= 0;
			$totIvrTerm 			= 0;
			$totQueueTerm 			= 0;
			$totQueued 				= 0;
			$totAbandon 			= 0;
			$totAgentAbandon 		= 0;
			$totAgentConnected  	= 0;
			$totAgentConnected20s 	= 0;
			$totAgentTalkTimeAvg  	= 0;
			
			
	/* get parameter post data **/		
			
			$start_date = $_REQUEST['start_date'];
			$end_date   = $_REQUEST['end_date'];
			
			$xlsRows = $xlsRows+1;
			$excel -> xlsWriteLabel($xlsRows,0,"Deskoll [ ".$this ->getInitName($agent)." ]");
			$xlsRows = $xlsRows+1;
			$excel -> xlsWriteLabel($xlsRows,0,"Interval");
			$excel -> xlsWriteLabel($xlsRows,1,"Total Outgoing Calls");
			$excel -> xlsWriteLabel($xlsRows,2,"Connected");
			$excel -> xlsWriteLabel($xlsRows,3,"Unconnected");
			$excel -> xlsWriteLabel($xlsRows,4,"AVG Talktime");
				
				$sql = "SELECT date_format(a.start_time,'%d-%m-%Y') tgl,
						count(id) tot,
						SUM(IF((a.status IN (3004, 3005)),1,0)) AS tot_con,
						SUM(IF((a.status NOT IN (3004, 3005)),1,0)) AS tot_abd,
						SUM(IF((a.status IN (3004, 3005) AND ((UNIX_TIMESTAMP(agent_time)-UNIX_TIMESTAMP(a.start_time))<20)),1,0)) AS tot_con20s
						FROM enigmacollectdb.cc_call_session a
						WHERE
						(UNIX_TIMESTAMP(a.start_time) between UNIX_TIMESTAMP('".$_REQUEST['start_date']." 00:00:00') AND UNIX_TIMESTAMP('".$_REQUEST['end_date']." 23:59:59'))
						AND a.direction = 2
						AND agent_id='".$agent."' 
						AND a.d_number =''
						GROUP BY date(a.start_time)";
						
				
				$query  = $this ->execute($sql,__FILE__,__LINE__);
				while( $row =  $this ->fetchrow($query) ):
					$offeredCall[ $row ->tgl] = $row ->tot;
					$abandonedCall[ $row ->tgl] = $row ->tot_abd;		
					$agentConnected[ $row ->tgl] = $row ->tot_con;
					$agentConnected20s[ $row ->tgl] = $row ->tot_con20s;	
				endwhile;
				
	/* AVG talktime */
									
				$sql = "SELECT date_format(a.start_time,'%d-%m-%Y') tgl, round(avg(unix_timestamp(a.end_time)-unix_timestamp(a.agent_time))) avg_talk "
						." FROM enigmacollectdb.cc_call_session a "
						." WHERE DATE(a.start_time)>= '".$start_date."' "
						."  AND DATE(a.start_time)<= '".$end_date."' "
						."  AND a.agent_time IS NOT NULL "
						."  AND a.agent_time <> '0000-00-00 00:00:00' "
						."  AND a.end_time IS NOT null "
						."  AND direction = 2 "
						."  AND status = 3005 "
						."  AND agent_id='".$agent."' "
						." GROUP BY date_format(a.start_time,'%Y-%m-%d')";
									
				$query =  $this->execute($sql,__FILE__,__LINE__);
				while($row = $this ->fetchrow($query)):
					$agentTalkTimeAvg[$row->tgl] = $row->avg_talk;
				endwhile;
				
								
				
					
	/** start row content **/
			
				$xlsRows = $xlsRows+1;
				while(true){
					$sdates = explode("-", $start_date);
					$estart_date = $sdates[2]."-".$sdates[1]."-".$sdates[0];
					
					$excel -> xlsWriteLabel($xlsRows,0,$estart_date);
					$excel -> xlsWriteNumber($xlsRows,1,$offeredCall[$estart_date]);
					$excel -> xlsWriteNumber($xlsRows,2,$agentConnected[$estart_date]);
					$excel -> xlsWriteNumber($xlsRows,3,$abandonedCall[$estart_date]);
					$excel -> xlsWriteLabel($xlsRows,4,$agentTalkTimeAvg[$estart_date]);
			
						$totOffered	+= $offeredCall[$estart_date];
						$totIvrTerm	+= $ivrTermCall[$estart_date];
						$totQueued	+= $queuedCall[$estart_date];
						$totQueueTerm += $queuedTermCall[$estart_date];
						$totAbandon	+= $abandonedCall[$estart_date];
						$totAgentAbandon += $agentAbandonCall[$estart_date];
						$totAgentConnected	+= $agentConnected[$estart_date];
						$totAgentConnected20s += $agentConnected20s[$estart_date];
						$totAgentTalkTimeAvg +=$agentTalkTimeAvg[$estart_date];
						
					if ($start_date == $end_date) break;
						$start_date = $this -> nextDate($start_date);
						
					$xlsRows+=1;
					
				}
		
		/** start row content of bottom **/
		
				$xlsRows = $xlsRows+1;
				$excel -> xlsWriteLabel($xlsRows,0,"Summary");
				$excel -> xlsWriteNumber($xlsRows,1,$totOffered);
				$excel -> xlsWriteNumber($xlsRows,2,$totAgentConnected);
				$excel -> xlsWriteNumber($xlsRows,3,$totAbandon);
				$excel -> xlsWriteLabel($xlsRows,4,$totAgentTalkTimeAvg);
				
			 $xlsRows=$xlsRows+1;
			}
			
		/* close from object excel**/
		
			$excel->xlsClose();
	}

}

if(!is_object($outCallExcel)) :
	$outCallExcel = new outCallExcel();
	$outCallExcel ->excel();
endif;
 
 

?>
