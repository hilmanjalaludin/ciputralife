<?php
class agency_performance extends index{
	var $start_date;
	var $end_date;
	
	function agency_performance()
	{
		$this->start_date = $this -> formatDateEng($_REQUEST['start_date']);
		$this->end_date = $this -> formatDateEng($_REQUEST['end_date']);
	}
	
	
	function _Duration( $seconds=0 )
	{
		$sec = 0;
		$min = 0;
		$hour= 0;
		$sec = $seconds%60;
		$seconds = floor($seconds/60);
		
		if ($seconds){
			$min  = $seconds%60;
			$hour = floor($seconds/60);
		}
		
		if($seconds == 0 && $sec == 0)
			return sprintf("");
		else
			return sprintf("%01d.%02d", $hour,$min);
	}

	private function getCmpName($cmpId)
	{
		$sql = "select a.CampaignName from t_gn_campaign a where a.CampaignId = ".$cmpId;
		$qry = $this ->query($sql);
		$row = $qry -> result_assoc();
		
		return $row[0]['CampaignName'];
	}
	
	private function getSpvName($spvId)
	{
		$sql = "select a.full_name from tms_agent a where a.UserId = ".$spvId;
		$qry = $this ->query($sql);
		$row = $qry -> result_assoc();
		
		return $row[0]['full_name'];
	}
	
	private function headerBySpv($spv)
	{
		/*** Unactive 26-08-2015
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\">
				<tr>
					<td nowrap class=\"header first\" >".($spv?$this->getSpvName($spv):'Supervisor')."</td>
					<td nowrap class=\"header middle\">Solicited</td>
					<td nowrap class=\"header middle\">Contact</td>
					<td nowrap class=\"header middle\">Contact Rate %</td>
					<td nowrap class=\"header middle\">Attempt</td>
					<td nowrap class=\"header middle\">Attempt Ratio</td>
					<td nowrap class=\"header middle\">Sales Close Rate %</td>
					<td nowrap class=\"header middle\">Response Rate %</td>
					<td nowrap class=\"header middle\">PIF</td>
					<td nowrap class=\"header middle\">ANP</td>
					<td nowrap class=\"header middle\">Average Premium</td>
					<td nowrap class=\"header middle\">Contact per Hour</td>
					<td nowrap class=\"header middle\">Sales per Hour</td>
					<td nowrap class=\"header middle\">ANP per Hour</td>
					<td nowrap class=\"header middle\">Login Hours</td>
					<td nowrap class=\"header middle\">Talk Time Contacted</td>
					<!-- t d nowrap class=\"header middle\">Talk Time</td>
					<td nowrap class=\"header middle\">Wait Time</td>
					<td nowrap class=\"header lasted\">Wrap Time</t d -->
				</tr> ";
			END OF Unactive 26-08-2015 ***/
				
				echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\">
				<tr>
					<td nowrap class=\"header first\" >".($spv?$this->getSpvName($spv):'Supervisor')."</td>
					<td nowrap class=\"header middle\">Touch</td>
					<td nowrap class=\"header middle\">Contact</td>
					<td nowrap class=\"header middle\">Contact Rate %</td>
					<td nowrap class=\"header middle\">Attempt</td>
					<td nowrap class=\"header middle\">Attempt Ratio</td>
					<td nowrap class=\"header middle\">Conversion Rate%</td>
					<td nowrap class=\"header middle\">Response Rate %</td>
					<td nowrap class=\"header middle\">Case</td>
					<td nowrap class=\"header middle\">APE</td>
					<td nowrap class=\"header middle\">Average Premium</td>
				</tr> ";
	}
	
	// Rumus SPV ====================================================================================================================
	
	function getSolicited($cmp, $spv, $get=0)
	{
		/* 
			1 = Solicited;
			2 = NotSolicited;
			3 = Contact;
			4 = Terminate;
		*/
		//14,15,16,17,18,19,20,21,22,23,24,25,27,28,29,31,32,33,34,49,50,54,55,56,57,58,59,60,62,63,64,65,66,70,71,72,73,74,75
		$sql = " SELECT '0' AS NullValue,
					SUM(IF(a.CustomerUpdatedTs IS NOT NULL,1,0)) AS Solicited, 
					SUM(IF(a.CustomerUpdatedTs IS NULL,1,0)) AS NotSolicited, 
					SUM(IF(d.CallReasonId IN(7,8,9,10,11,12,13,14,15,16,17,20,21,22,23,24,25),1,0)) AS Contact, 
					SUM(IF(d.CallReasonTerminate IN(1),1,0)) AS Terminate
				FROM t_gn_customer a 
					LEFT JOIN tms_agent b ON a.SellerId=b.UserId
					LEFT JOIN t_gn_campaign c ON a.CampaignId=c.CampaignId
					LEFT JOIN t_lk_callreason d on a.CallReasonId=d.CallReasonId
				WHERE a.CampaignId = ".$cmp."
					AND b.spv_id = ".$spv."
					AND DATE(a.CustomerUpdatedTs) >='".$this->start_date."'
					AND DATE(a.CustomerUpdatedTs) <='".$this->end_date."'
				GROUP BY b.spv_id ";
		// echo "<pre>$sql</pre>";		
		$qry = $this -> query($sql);
		if($row = $qry -> result_rows()){
			return $row[0][$get];
		}
		else{
			return 0;
		}
	}
	
	function getAttempt($cmp, $spv)
	{
		$sql = " SELECT 
					COUNT(DISTINCT d.CallHistoryId) Attempt,
					COUNT(DISTINCT a.CustomerId) Lead,
					b.spv_id
				FROM t_gn_customer a 
					LEFT JOIN tms_agent b ON a.SellerId=b.UserId
					LEFT JOIN t_gn_campaign c ON a.CampaignId=c.CampaignId
					LEFT JOIN t_gn_callhistory d ON a.CustomerId = d.CustomerId
				WHERE a.CampaignId = ".$cmp."
					AND b.spv_id = ".$spv."
					AND DATE(a.CustomerUpdatedTs) >='".$this->start_date."'
					AND DATE(a.CustomerUpdatedTs) <='".$this->end_date."'
				GROUP BY b.spv_id ";
				
	
		$qry = $this -> query($sql);
		if($row = $qry -> result_assoc()){
			return $row[0]['Attempt'];
		}
		else{
			return 0;
		}
	}
	
	function getLead($cmp, $spv)
	{
		$sql = " SELECT 
					COUNT(DISTINCT a.CustomerId) Lead,
					b.spv_id
				FROM t_gn_customer a 
					LEFT JOIN tms_agent b ON a.SellerId=b.UserId
					LEFT JOIN t_gn_campaign c ON a.CampaignId=c.CampaignId
					LEFT JOIN t_gn_callhistory d ON a.CustomerId = d.CustomerId
				WHERE a.CampaignId = ".$cmp."
					AND b.spv_id = ".$spv."
					AND DATE(a.CustomerUpdatedTs) >='".$this->start_date."'
					AND DATE(a.CustomerUpdatedTs) <='".$this->end_date."'
				GROUP BY b.spv_id ";
				
	
		$qry = $this -> query($sql);
		if($row = $qry -> result_assoc()){
			return $row[0]['Lead'];
		}
		else{
			return 0;
		}
	}
	
	function getPIF($cmp, $spv)
	{
		$sql = " SELECT count(distinct a.CustomerId) as PIF,
				 d.AssignSpv
				 from t_gn_policyautogen a 
				 left join t_gn_policy b on a.PolicyNumber=b.PolicyNumber
				 left join t_gn_customer c on a.CustomerId=c.CustomerId
				 left join t_gn_assignment d on c.CustomerId=d.CustomerId
				 left join t_gn_productplan e on b.ProductPlanId=e.ProductPlanId
				 WHERE 
				 date(b.PolicySalesDate)>='".$this->start_date."'
				 AND date(b.PolicySalesDate)<='".$this->end_date."'
				 AND d.AssignSpv = ".$spv."	
				 AND c.CampaignId = ".$cmp."
				 AND c.CallReasonId IN(20,21)
				 group by d.AssignSpv ";
				
	
		$qry = $this -> query($sql);
		if($row = $qry -> result_assoc()){
			return $row[0]['PIF'];
		}
		else{
			return 0;
		}
	}
	
	function getANP($cmp, $spv)
	{
		$sql = " SELECT count(a.CustomerId), sum(b.Premi) as tmp,
				SUM( IF( e.PayModeId=2,(b.Premi*12), b.Premi)) as ANP,
				d.AssignSpv
				from t_gn_policyautogen a 
				left join t_gn_policy b on a.PolicyNumber=b.PolicyNumber
				left join t_gn_customer c on a.CustomerId=c.CustomerId
				left join t_gn_assignment d on c.CustomerId=d.CustomerId
				left join t_gn_productplan e on b.ProductPlanId=e.ProductPlanId
				WHERE 
				date(b.PolicySalesDate)>='".$this->start_date."'
				AND date(b.PolicySalesDate)<='".$this->end_date."'
				AND d.AssignSpv = ".$spv."
				AND c.CampaignId = ".$cmp."
				group by d.AssignSpv ";
		
		$qry = $this -> query($sql);
		if($row = $qry -> result_assoc()){
			return $row[0]['ANP'];
		}
		else{
			return 0;
		}
	}
	
	function setContactTalk($spv = array())
	{
		$spvId = implode(',',$spv);
		$datas = array();
		
		$sql = "SELECT 
					d.spv_id,
					SUM(IF(a.`status` IN ('3005','3004'),UNIX_TIMESTAMP(a.end_time) - UNIX_TIMESTAMP(a.agent_time),0)) Talk_Contact
				FROM cc_call_session a
					LEFT JOIN t_gn_customer b ON a.assign_data = b.CustomerId
					INNER JOIN t_gn_assignment c ON b.CustomerId = c.CustomerId
					LEFT JOIN tms_agent d ON b.SellerId = d.UserId
					LEFT JOIN t_lk_callreason e ON b.CallReasonId = e.CallReasonId
				WHERE a.start_time >= '".$this->start_date." 00:00:00' 
					AND a.start_time <= '".$this->end_date." 23:59:59' 
					AND a.`status` IN ('3005','3004')
					AND d.spv_id IN (".$spvId.")
					AND e.CallReasonId IN (7,8,9,10,11,12,13,14,15,16,17,20,21,22,23,24,25)
				GROUP BY d.spv_id";
		// echo "<pre>$sql</pre>";		
		$qry = $this -> query($sql);
		
		foreach($qry -> result_rows() as $row){
			$datas[$row[0]] = $row;
		}
		
		return $datas;
	}
	
	function setLoginHours($spv = array())
	{
		$spvId = implode(',',$spv);
		$datas = array();
		
		$sql = "SELECT 
				c.spv_id,
				SUM(IF(a.`status` IN (1,2,3,4),(UNIX_TIMESTAMP(a.end_time)- UNIX_TIMESTAMP(a.start_time)),0)) AS tots, 
				SUM(IF(a.`status`=1,(UNIX_TIMESTAMP(a.end_time)- UNIX_TIMESTAMP(a.start_time)), 0)) AS Ready, 
				SUM(IF(a.`status`=2,(UNIX_TIMESTAMP(a.end_time)- UNIX_TIMESTAMP(a.start_time)), 0)) AS NotReady, 
				SUM(IF(a.`status`=3,(UNIX_TIMESTAMP(a.end_time)- UNIX_TIMESTAMP(a.start_time)), 0)) AS ACW, 
				SUM(IF(a.`status`=4,(UNIX_TIMESTAMP(a.end_time)- UNIX_TIMESTAMP(a.start_time)), 0)) AS Busy
				FROM cc_agent_activity_log a
				LEFT JOIN cc_agent b ON a.agent=b.id
				LEFT JOIN tms_agent c ON b.userid=c.id
				WHERE a.start_time >='".$this->start_date." 00:00:00' 
				AND a.start_time <='".$this->end_date." 23:59:59' 
				AND a.`status` IN(1,2,3,4) 
				AND c.spv_id in (".$spvId.")
				GROUP BY c.spv_id";
		$qry = $this -> query($sql);
		
		foreach($qry -> result_rows() as $row){
			$datas[$row[0]] = $row;
		}
		
		return $datas;
	}
	
	// End of Rumus SPV =============================================================================================================
	
	private function PerfomanceBySupervisor()
	{
		switch($_REQUEST['mode'])
		{
			case 'summary' : $this -> PerformSpvSummary(); break; 
		}
	}
	
	private function PerformSpvSummary()
	{
		$spv = explode(",",$_REQUEST['Supervisor']);
		$cmp = explode(",",$_REQUEST['CampaignName']);
		
		$LogHours = $this->setLoginHours($spv);
		$ContactTalk = $this->setContactTalk($spv);
		$this->headerBySpv();
		
		foreach($cmp as $key => $campaign){
			$camp = $campaign;
			
			echo"<tr>
					<td nowrap class=\"sub first\" colspan=\"16\">&nbsp;".$this->getCmpName($campaign)."</td>
				</tr>";
				
				/* NILAI DASAR */
				$solicited = 0;
				$contact = 0;
				$TalkContact = 0;
				$ContactRate = 0;
				$Attempt = 0;
				$AttemptRatio = 0;
				$SalesClose = 0;
				$ResponseRate = 0;
				$PIF = 0;
				$ANP = 0;
				$AvPremium = 0;
				$ContactPerHour = 0;
				$SalesPerHour = 0;
				$AnpPerHour = 0;
				$Login = 0;
				$Talk = 0;
				$Wait = 0;
				$Wrap = 0;
				
			foreach($spv as $key1 => $user){
			
				$contact_per_hours = round(($this->getSolicited($camp,$user,3) / $this ->_Duration($LogHours[$user][1])),2);
				$sales_per_hours = round(($this->getPIF($camp,$user)/ $this ->_Duration($LogHours[$user][1])),3);
				$anp_per_hours =  round(($this->getANP($camp,$user)/ $this ->_Duration($LogHours[$user][1]))); 
				$avg_premi = ($this->getANP($camp,$user)?($this->getANP($camp,$user)/$this->getPIF($camp,$user))/12:0);
				
				/**Unactive 26-08-2015
					echo"<tr>
						<td nowrap class=\"content first\">".$this->getSpvName($user)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".$this->getSolicited($camp,$user,1)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".$this->getSolicited($camp,$user,3)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".round((($this->getSolicited($camp,$user,3)?($this->getSolicited($camp,$user,3)/$this->getSolicited($camp,$user,1)):0)*100),2)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".$this->getAttempt($camp,$user,1)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".round(($this->getAttempt($camp,$user,1)/$this->getLead($camp,$user,1)*100),2)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".round((($this->getPIF($camp,$user)?($this->getPIF($camp,$user)/$this->getSolicited($camp,$user,3)):0)*100),2)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".round((($this->getPIF($camp,$user)?($this->getPIF($camp,$user)/$this->getSolicited($camp,$user,1)):0)*100),2)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".$this->getPIF($camp,$user)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".formatRupiah($this->getANP($camp,$user))."</td>
						<td nowrap class=\"content middle\" align=\"right\">".formatRupiah($avg_premi)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($contact_per_hours?$contact_per_hours:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($sales_per_hours?$sales_per_hours:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".formatRupiah($anp_per_hours)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".toDuration((($LogHours[$user][1])?($LogHours[$user][1]):0))."&nbsp;</td>
						<td nowrap class=\"content middle\" align=\"right\">".toDuration((($ContactTalk[$user][1])?($ContactTalk[$user][1]):0))."&nbsp;</td>
						<!-- t d nowrap class=\"content middle\" align=\"right\">//toDuration((($LogHours[$user][5])?($LogHours[$user][5]):0))&nbsp;</td>
						<td nowrap class=\"content middle\" align=\"right\">//toDuration(($LogHours[$user][2])?($LogHours[$user][2]):0)&nbsp;</td>
						<td nowrap class=\"content lasted\" align=\"right\">//toDuration((($LogHours[$user][3])?($LogHours[$user][3]):0))&nbsp;</t d -->
					</tr> ";
					END Unactive 26-08-2015****/
					echo"<tr>
						<td nowrap class=\"content first\">".$this->getSpvName($user)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".$this->getSolicited($camp,$user,1)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".$this->getSolicited($camp,$user,3)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".round((($this->getSolicited($camp,$user,3)?($this->getSolicited($camp,$user,3)/$this->getSolicited($camp,$user,1)):0)*100),2)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".$this->getAttempt($camp,$user,1)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".round(($this->getAttempt($camp,$user,1)/$this->getLead($camp,$user,1)*100),2)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".round((($this->getPIF($camp,$user)?($this->getPIF($camp,$user)/$this->getSolicited($camp,$user,3)):0)*100),2)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".round((($this->getPIF($camp,$user)?($this->getPIF($camp,$user)/$this->getSolicited($camp,$user,1)):0)*100),2)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".$this->getPIF($camp,$user)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".formatRupiah($this->getANP($camp,$user))."</td>
						<td nowrap class=\"content middle\" align=\"right\">".formatRupiah($avg_premi)."</td>
						
					</tr> ";
					
				/* NILAI SUMMARY */
				$solicited += $this->getSolicited($camp,$user,1);
				$contact += $this->getSolicited($camp,$user,3);
				$TalkContact += $ContactTalk[$user][1];
				$ContactRate += round((($this->getSolicited($camp,$user,3)?($this->getSolicited($camp,$user,3)/$this->getSolicited($camp,$user,1)):0)*100),2);
				$Attempt += $this->getAttempt($camp,$user,1);
				$AttemptRatio += round(($this->getAttempt($camp,$user,1)/$this->getLead($camp,$user,1)*100),2);
				$SalesClose += round((($this->getPIF($camp,$user)?($this->getPIF($camp,$user)/$this->getSolicited($camp,$user,3)):0)*100),2);
				$ResponseRate += round((($this->getPIF($camp,$user)?($this->getPIF($camp,$user)/$this->getSolicited($camp,$user,1)):0)*100),2);
				$PIF += $this->getPIF($camp,$user);
				$ANP += $this->getANP($camp,$user);
				$AvPremium += round(($this->getANP($camp,$user)?($this->getANP($camp,$user)/$this->getPIF($camp,$user))/12:0),0);
				$ContactPerHour += ($contact_per_hours?$contact_per_hours:0);
				$SalesPerHour += ($sales_per_hours?$sales_per_hours:0);
				$AnpPerHour += formatRupiah($anp_per_hours);
				$Login += $LogHours[$user][1];
				$Talk += $LogHours[$user][5];
				$Wait += $LogHours[$user][2];
				$Wrap += $LogHours[$user][3];
				
			}
			/**Unactive 26-08-2015
			echo "<tr>
					<td nowrap class=\"total first\">Subtotal</td>
					<td nowrap class=\"total middle\" align=\"right\">".$solicited."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$contact."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$ContactRate." %</td>
					<td nowrap class=\"total middle\" align=\"right\">".$Attempt."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$AttemptRatio." %</td>
					<td nowrap class=\"total middle\" align=\"right\">".$SalesClose." %</td>
					<td nowrap class=\"total middle\" align=\"right\">".$ResponseRate." %</td>
					<td nowrap class=\"total middle\" align=\"right\">".$PIF."</td>
					<td nowrap class=\"total middle\" align=\"right\">".formatRupiah(($ANP?$ANP:0))."</td>
					<td nowrap class=\"total middle\" align=\"right\">".formatRupiah($AvPremium)."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$ContactPerHour."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$SalesPerHour."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$AnpPerHour."</td>
					<td nowrap class=\"total middle\" align=\"right\">".toDuration($Login?$Login:0)."&nbsp;</td>
					<td nowrap class=\"total middle\" align=\"right\">".toDuration($TalkContact?$TalkContact:0)."</td>
					<!-- t d nowrap class=\"total middle\" align=\"right\">toDuration($Talk?$Talk:0)&nbsp;</td>
					<td nowrap class=\"total middle\" align=\"right\">toDuration($Wait?$Wait:0)&nbsp;</td>
					<td nowrap class=\"total lasted\" align=\"right\">toDuration($Wrap?$Wrap:0)&nbsp;</t d -->
				</tr>";
			END of Unactive 26-08-2015***/
			echo "<tr>
					<td nowrap class=\"total first\">Subtotal</td>
					<td nowrap class=\"total middle\" align=\"right\">".$solicited."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$contact."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$ContactRate." %</td>
					<td nowrap class=\"total middle\" align=\"right\">".$Attempt."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$AttemptRatio." %</td>
					<td nowrap class=\"total middle\" align=\"right\">".$SalesClose." %</td>
					<td nowrap class=\"total middle\" align=\"right\">".$ResponseRate." %</td>
					<td nowrap class=\"total middle\" align=\"right\">".$PIF."</td>
					<td nowrap class=\"total middle\" align=\"right\">".formatRupiah(($ANP?$ANP:0))."</td>
					<td nowrap class=\"total middle\" align=\"right\">".formatRupiah($AvPremium)."</td>
				</tr>";
		}
		
		
		echo "</table><br>";
	}
	
	public function show_content_html()
	{
		// echo "AAAA";
		mysql::__construct();
		
		switch($_REQUEST['group_by'])
		{
			case 'Telesales'  : $this -> PerfomanceByTelesales(); break; 
			case 'supervisor' : $this -> PerfomanceBySupervisor(); break; 
			case 'campaign'   : echo "<h3>Sorry, this report only grouping by Supervisor and Telesales !</h3>"; break; 
			
			default:
				echo "<h3>Sorry, Report Under maintenace</h3>";
			break;
		}
	}
	
	// Rumus By Telesales =============================================================================================================
	
	function getSolicitedTmr($cmp, $tmr, $get=0)
	{
		/* 
			1 = Solicited;
			2 = NotSolicited;
			3 = Contact;
			4 = Terminate;
		*/
					
		$sql = " 
				SELECT '0' AS NullValue,
				SUM(IF(a.CustomerUpdatedTs IS NOT NULL,1,0)) AS Solicited, 
				SUM(IF(a.CustomerUpdatedTs IS NULL,1,0)) AS NotSolicited, 
				SUM(IF(d.CallReasonId IN(7,8,9,10,11,12,13,14,15,16,17,20,21,22,23,24,25),1,0)) AS Contact, 
				SUM(IF(d.CallReasonTerminate IN(1),1,0)) AS Terminate

				FROM t_gn_customer a 
				LEFT JOIN tms_agent b ON a.SellerId=b.UserId
				LEFT JOIN t_gn_campaign c ON a.CampaignId=c.CampaignId
				LEFT JOIN t_lk_callreason d on a.CallReasonId=d.CallReasonId

				WHERE 1=1 
				AND a.CampaignId = ".$cmp."
				AND b.UserId = ".$tmr."
				AND a.CustomerUpdatedTs >='".$this->start_date." 00:00:00'
				AND a.CustomerUpdatedTs <='".$this->end_date." 23:59:59'
				GROUP BY b.UserId ";
		// echo "<pre>$sql</pre>";
		$qry = $this -> query($sql);
		
		if($row = $qry -> result_rows()){
			return $row[0][$get];
		}
		else{
			return 0;
		}
	}
	
	function getAttempttmr($cmp, $tmr)
	{
		$sql = " SELECT 
					COUNT(DISTINCT d.CallHistoryId) Attempt,
					b.UserId
				FROM t_gn_customer a 
					LEFT JOIN tms_agent b ON a.SellerId=b.UserId
					LEFT JOIN t_gn_campaign c ON a.CampaignId=c.CampaignId
					LEFT JOIN t_gn_callhistory d ON a.CustomerId = d.CustomerId
				 WHERE 1=1 
					AND a.CampaignId = ".$cmp."
					AND b.UserId = ".$tmr."
					AND d.CallHistoryCallDate >='".$this->start_date." 00:00:00'
					AND d.CallHistoryCallDate <='".$this->end_date." 23:59:59'
				GROUP BY b.UserId ";
				
				
			
		$qry = $this -> query($sql);
		
		if($row = $qry -> result_assoc()){
			return $row[0]['Attempt'];
		}
		else{
			return 0;
		}
	}
	
	function getLeadtmr($cmp, $tmr)
	{
		$sql = " SELECT 
					COUNT(DISTINCT a.CustomerId) Lead,
					b.UserId
				FROM t_gn_customer a 
					LEFT JOIN tms_agent b ON a.SellerId=b.UserId
					LEFT JOIN t_gn_campaign c ON a.CampaignId=c.CampaignId
					LEFT JOIN t_gn_callhistory d ON a.CustomerId = d.CustomerId
				 WHERE 1=1 
					AND a.CampaignId = ".$cmp."
					AND b.UserId = ".$tmr."
					AND a.CustomerUpdatedTs >='".$this->start_date." 00:00:00'
					AND a.CustomerUpdatedTs <='".$this->end_date." 23:59:59'
				GROUP BY b.UserId ";
				
				
			
		$qry = $this -> query($sql);
		
		if($row = $qry -> result_assoc()){
			return $row[0]['Lead'];
		}
		else{
			return 0;
		}
	}
	
	function getPIFtmr($cmp, $tmr)
	{
		$sql = " SELECT count(distinct a.CustomerId) as PIF,
				 d.AssignSelerId
				 from t_gn_policyautogen a 
				 left join t_gn_policy b on a.PolicyNumber=b.PolicyNumber
				 left join t_gn_customer c on a.CustomerId=c.CustomerId
				 left join t_gn_assignment d on c.CustomerId=d.CustomerId
				 left join t_gn_productplan e on b.ProductPlanId=e.ProductPlanId
				 WHERE b.PolicySalesDate >='".$this->start_date." 00:00:00'
				 AND b.PolicySalesDate <='".$this->end_date." 23:59:59'
				 AND d.AssignSelerId = ".$tmr."	
				 AND c.CampaignId = ".$cmp."
				 AND c.CallReasonId IN(20,21)
				 group by d.AssignSelerId ";
				
				
			
		$qry = $this -> query($sql);
		
		if($row = $qry -> result_assoc()){
			return $row[0]['PIF'];
		}
		else{
			return 0;
		}
	}
	
	function getANPtmr($cmp, $tmr)
	{
		$sql = " SELECT COUNT(a.CustomerId), sum(b.Premi) as tmp,
				SUM( IF( e.PayModeId=2,(b.Premi*12), b.Premi)) as ANP,
				d.AssignSelerId
				from t_gn_policyautogen a 
				left join t_gn_policy b on a.PolicyNumber=b.PolicyNumber
				left join t_gn_customer c on a.CustomerId=c.CustomerId
				left join t_gn_assignment d on c.CustomerId=d.CustomerId
				left join t_gn_productplan e on b.ProductPlanId=e.ProductPlanId
				WHERE b.PolicySalesDate >='".$this->start_date." 00:00:00'
				AND b.PolicySalesDate <='".$this->end_date." 23:59:59'
				AND d.AssignSelerId = ".$tmr."
				AND c.CampaignId = ".$cmp."
				group by d.AssignSelerId ";
				
		$qry = $this -> query($sql);
		
		if($row = $qry -> result_assoc()){
			return $row[0]['ANP'];
		}
		else{
			return 0;
		}
	}
	
	function setContactTalkTmr($tmr = array())
	{	
		$datas = array();
		$tmrId = implode(',',$tmr);
		$sql = "SELECT 
					d.UserId,
					SUM(IF(a.`status` IN ('3005','3004'),UNIX_TIMESTAMP(a.end_time) - UNIX_TIMESTAMP(a.agent_time),0)) Talk_Contact
				FROM cc_call_session a
					LEFT JOIN t_gn_customer b ON a.assign_data = b.CustomerId
					INNER JOIN t_gn_assignment c ON b.CustomerId = c.CustomerId
					LEFT JOIN tms_agent d ON b.SellerId = d.UserId
					LEFT JOIN t_lk_callreason e ON b.CallReasonId = e.CallReasonId
				WHERE 
					d.UserId IN (".$tmrId.") 
					AND a.start_time >='".$this->start_date." 00:00:00' 
					AND a.start_time <='".$this->end_date." 23:59:59' 
					AND a.`status` IN ('3005','3004')
					AND e.CallReasonId IN (7,8,9,10,11,12,13,14,15,16,17,20,21,22,23,24,25)
				GROUP BY d.UserId ";
		// echo "<pre>$sql</pre>";		
		$qry = $this -> query($sql);
		foreach($qry -> result_rows() as $row){
			$datas[$row[0]] = $row;
		}
		
		return $datas;
	}
	
	function setLoginHoursTmr($tmr = array())
	{	
		$datas = array();
		$tmrId = implode(',',$tmr);
		$sql = "SELECT c.UserId,
					SUM(IF(a.`status` IN (1,2,3,4),(UNIX_TIMESTAMP(a.end_time)- UNIX_TIMESTAMP(a.start_time)),0)) AS tots, 
					SUM(IF(a.`status`=1,(UNIX_TIMESTAMP(a.end_time)- UNIX_TIMESTAMP(a.start_time)), 0)) AS Ready, 
					SUM(IF(a.`status`=2,(UNIX_TIMESTAMP(a.end_time)- UNIX_TIMESTAMP(a.start_time)), 0)) AS NotReady, 
					SUM(IF(a.`status`=3,(UNIX_TIMESTAMP(a.end_time)- UNIX_TIMESTAMP(a.start_time)), 0)) AS ACW, 
					SUM(IF(a.`status`=4,(UNIX_TIMESTAMP(a.end_time)- UNIX_TIMESTAMP(a.start_time)), 0)) AS Busy
				FROM cc_agent_activity_log a
				LEFT JOIN cc_agent b ON a.agent=b.id
				left join tms_agent c on b.userid=c.id
				WHERE c.UserId IN (".$tmrId.") 
				AND a.start_time >='".$this->start_date." 00:00:00' 
				AND a.start_time <='".$this->end_date." 23:59:59' 
				AND a.`status` IN(1,2,3,4) 
				GROUP BY c.UserId ";
		//print "<pre>$sql</pre>";
		
		$qry = $this -> query($sql);
		foreach($qry -> result_rows() as $row){
			$datas[$row[0]] = $row;
		}
		
		return $datas;
	}
	// End of rumus Telesales =========================================================================================================
	
	private function PerfomanceByTelesales()
	{
		switch($_REQUEST['mode'])
		{
			case 'summary' : $this -> PerformTMSummary(); break; 
		}
		
	}
	
	private function PerformTMSummary()
	{
		$tmr = explode(",",$_REQUEST['Telesales']);
		$cmp = explode(",",$_REQUEST['CampaignName']);
		
		$LogHours = $this->setLoginHoursTmr($tmr);
		// echo "<pre>";
		// print_r($LogHours);
		// echo "</pre>";
		$ContactTalk = $this->setContactTalkTmr($tmr);
		$this->headerBySpv($_REQUEST['Supervisor']);
		foreach($cmp as $key => $campaign){
			$camp = $campaign;
			
			echo"<tr>
					<td nowrap class=\"sub first\" colspan=\"16\">&nbsp;".$this->getCmpName($campaign)."</td>
				</tr>";
			
			/* NILAI DASAR */
			$solicitedTmr = 0;
			$contactTmr = 0;
			$ContactRateTmr = 0;
			$AtteptTmr = 0;
			$AttemptRatioTmr = 0;
			$SalesCloseTmr = 0;
			$ResponseRateTmr = 0;
			$PIFTmr = 0;
			$ANPTmr = 0;
			$AvPremiumTmr = 0;
			$ContactPerHourTmr = 0;
			$SalesPerHourTmr = 0;
			$AnpPerHourTmr = 0;
			$LoginTmr = 0;
			$TalkContactTmr = 0;
			$TalkTmr = 0;
			$WaitTmr = 0;
			$WrapTmr = 0;
			$no = 0;
			
			foreach($tmr as $key1 => $user){
				
				$contact_per_hours = round(($this->getSolicitedTmr($camp,$user,3) / $this ->_Duration($LogHours[$user][1])),2);
				$sales_per_hours = round(($this->getPIFtmr($camp,$user)/ $this ->_Duration($LogHours[$user][1])),2);
				$anp_per_hours =  round(($this->getANPtmr($camp,$user)/ $this ->_Duration($LogHours[$user][1]))); 
				/**Unactive 26-08-2015
				echo"<tr>
						<td nowrap class=\"content first\">".$this->getSpvName($user)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".$this->getSolicitedTmr($camp,$user,1)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".$this->getSolicitedTmr($camp,$user,3)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".round((($this->getSolicitedTmr($camp,$user,3)?($this->getSolicitedTmr($camp,$user,3)/$this->getSolicitedTmr($camp,$user,1)):0)*100),2)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".$this->getAttempttmr($camp,$user)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".round(($this->getAttempttmr($camp,$user)/$this->getLeadtmr($camp,$user)*100),2)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".round((($this->getPIFtmr($camp,$user)?($this->getPIFtmr($camp,$user)/$this->getSolicitedTmr($camp,$user,3)):0)*100),2)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".round((($this->getPIFtmr($camp,$user)?($this->getPIFtmr($camp,$user)/$this->getSolicitedTmr($camp,$user,1)):0)*100),2)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".$this->getPIFtmr($camp,$user)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".formatRupiah($this->getANPtmr($camp,$user))."</td>
						<td nowrap class=\"content middle\" align=\"right\">".formatRupiah(round(($this->getANPtmr($camp,$user)?($this->getANPtmr($camp,$user)/$this->getPIFtmr($camp,$user))/12:0),0))."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($contact_per_hours?$contact_per_hours:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".($sales_per_hours?$sales_per_hours:0)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".formatRupiah(($anp_per_hours?$anp_per_hours:0))."</td>
						<td nowrap class=\"content middle\" align=\"right\">".toDuration($LogHours[$user][1] ? $LogHours[$user][1] :0)."&nbsp;</td>
						<td nowrap class=\"content middle\" align=\"right\">".toDuration($ContactTalk[$user][1] ? $ContactTalk[$user][1] :0)."&nbsp;</td>
						<!-- t d nowrap class=\"content middle\" align=\"right\">toDuration($LogHours[$user][5] ? $LogHours[$user][5] :0)&nbsp;</td>
						<td nowrap class=\"content middle\" align=\"right\">toDuration($LogHours[$user][2] ? $LogHours[$user][2] :0)&nbsp;</td>
						<td nowrap class=\"content lasted\" align=\"right\">toDuration($LogHours[$user][3] ? $LogHours[$user][3] :0)&nbsp;</t d -->
					</tr> ";
			END of Unactive 26-08-2015 ***/
			echo"<tr>
						<td nowrap class=\"content first\">".$this->getSpvName($user)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".$this->getSolicitedTmr($camp,$user,1)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".$this->getSolicitedTmr($camp,$user,3)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".round((($this->getSolicitedTmr($camp,$user,3)?($this->getSolicitedTmr($camp,$user,3)/$this->getSolicitedTmr($camp,$user,1)):0)*100),2)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".$this->getAttempttmr($camp,$user)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".round(($this->getAttempttmr($camp,$user)/$this->getLeadtmr($camp,$user)*100),2)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".round((($this->getPIFtmr($camp,$user)?($this->getPIFtmr($camp,$user)/$this->getSolicitedTmr($camp,$user,3)):0)*100),2)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".round((($this->getPIFtmr($camp,$user)?($this->getPIFtmr($camp,$user)/$this->getSolicitedTmr($camp,$user,1)):0)*100),2)." %</td>
						<td nowrap class=\"content middle\" align=\"right\">".$this->getPIFtmr($camp,$user)."</td>
						<td nowrap class=\"content middle\" align=\"right\">".formatRupiah($this->getANPtmr($camp,$user))."</td>
						<td nowrap class=\"content middle\" align=\"right\">".formatRupiah(round(($this->getANPtmr($camp,$user)?($this->getANPtmr($camp,$user)/$this->getPIFtmr($camp,$user))/12:0),0))."</td>
					</tr> ";
			
				/* NILAI SUMMARY */
				$solicitedTmr += $this->getSolicitedTmr($camp,$user,1);
				$contactTmr += $this->getSolicitedTmr($camp,$user,3);
				$TalkContactTmr += $ContactTalk[$user][1];
				$ContactRateTmr += round((($this->getSolicitedTmr($camp,$user,3)?($this->getSolicitedTmr($camp,$user,3)/$this->getSolicitedTmr($camp,$user,1)):0)*100),2);
				$AtteptTmr += $this->getAttempttmr($camp,$user);
				$AttemptRatioTmr += round(($this->getAttempttmr($camp,$user)/$this->getLeadtmr($camp,$user)*100),2);
				$SalesCloseTmr += round((($this->getPIFtmr($camp,$user)?($this->getPIFtmr($camp,$user)/$this->getSolicitedTmr($camp,$user,3)):0)*100),2);
				$ResponseRateTmr += round((($this->getPIFtmr($camp,$user)?($this->getPIFtmr($camp,$user)/$this->getSolicitedTmr($camp,$user,1)):0)*100),2);
				$PIFTmr += $this->getPIFtmr($camp,$user);
				$ANPTmr += $this->getANPtmr($camp,$user);
				$AvPremiumTmr += round(($this->getANPtmr($camp,$user)?($this->getANPtmr($camp,$user)/$this->getPIFtmr($camp,$user))/12:0),0);
				$ContactPerHourTmr += ($contact_per_hours?$contact_per_hours:0);
				$SalesPerHourTmr += ($sales_per_hours?$sales_per_hours:0);
				$AnpPerHourTmr += formatRupiah($anp_per_hours);
				$LoginTmr += $LogHours[$user][1];
				$TalkTmr += $LogHours[$user][5];
				$WaitTmr += $LogHours[$user][2];
				$WrapTmr += $LogHours[$user][3];
				$no++;
			}
			/**Unactive 26-08-2015
			echo "<tr>
					<td nowrap class=\"total first\">Subtotal</td>
					<td nowrap class=\"total middle\" align=\"right\">".$solicitedTmr."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$contactTmr."</td>
					<td nowrap class=\"total middle\" align=\"right\">".round( (($contactTmr / $solicitedTmr)*100) ,2)." %</td>
					<td nowrap class=\"total middle\" align=\"right\">".$AtteptTmr."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$AttemptRatioTmr." %</td>
					<td nowrap class=\"total middle\" align=\"right\">".$SalesCloseTmr." %</td>
					<td nowrap class=\"total middle\" align=\"right\">".$ResponseRateTmr." %</td>
					<td nowrap class=\"total middle\" align=\"right\">".$PIFTmr."</td>
					<td nowrap class=\"total middle\" align=\"right\">".formatRupiah($ANPTmr)."</td>
					<td nowrap class=\"total middle\" align=\"right\">".formatRupiah($AvPremiumTmr)."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$ContactPerHourTmr."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$SalesPerHourTmr."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$AnpPerHourTmr."</td>
					<td nowrap class=\"total middle\" align=\"right\">".toDuration($LoginTmr?$LoginTmr:0)."&nbsp;</td>
					<td nowrap class=\"total middle\" align=\"right\">".toDuration($TalkContactTmr?$TalkContactTmr:0)."</td>
					<!-- t d nowrap class=\"total middle\" align=\"right\">toDuration($TalkTmr?$TalkTmr:0)&nbsp;</td>
					<td nowrap class=\"total middle\" align=\"right\">toDuration($WaitTmr?$WaitTmr:0)&nbsp;</td>
					<td nowrap class=\"total lasted\" align=\"right\">toDuration($WrapTmr?$WrapTmr:0)&nbsp;</t d -->
				</tr>";
			END of Unactive 26-08-2015***/
			
			echo "<tr>
					<td nowrap class=\"total first\">Subtotal</td>
					<td nowrap class=\"total middle\" align=\"right\">".$solicitedTmr."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$contactTmr."</td>
					<td nowrap class=\"total middle\" align=\"right\">".round( (($contactTmr / $solicitedTmr)*100) ,2)." %</td>
					<td nowrap class=\"total middle\" align=\"right\">".$AtteptTmr."</td>
					<td nowrap class=\"total middle\" align=\"right\">".$AttemptRatioTmr." %</td>
					<td nowrap class=\"total middle\" align=\"right\">".$SalesCloseTmr." %</td>
					<td nowrap class=\"total middle\" align=\"right\">".$ResponseRateTmr." %</td>
					<td nowrap class=\"total middle\" align=\"right\">".$PIFTmr."</td>
					<td nowrap class=\"total middle\" align=\"right\">".formatRupiah($ANPTmr)."</td>
					<td nowrap class=\"total middle\" align=\"right\">".formatRupiah($AvPremiumTmr)."</td>
				</tr>";
		}
		echo "</table><br>";
	}
	
	
}
?>