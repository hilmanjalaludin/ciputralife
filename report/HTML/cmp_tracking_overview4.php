<?php

if(!define('CardClose','10')) define('CardClose','10');

class cmp_tracking_overview4 extends index
{
	private $product_category;
	function cmp_tracking_overview4()
	{
		$this->start_date = $this -> formatDateEng($this -> escPost('start_date'));
		$this->end_date = $this -> formatDateEng($this -> escPost('end_date'));

		ini_set("memory_limit","1024M");
	}

	/* main content HTML **/
	function show_content_html()
	{
		mysql::__construct();
		$this->product_category = $this->getProductCategory();
		self::write_header_closed();
		self::write_content();
		// self::write_footer();
	}

	private function getProductCategory()
	{
		$ProductCategory = array();
		$sql = "SELECT a.product_category_id,a.product_category_code,
				b.ProductId,b.ProductCode
				FROM t_gn_product_category a
				INNER JOIN t_gn_product b ON a.product_category_id=b.product_category_id
				WHERE b.ProductStatusFlag = 1";
		$qry = $this ->query($sql);
		foreach($qry -> result_assoc() as $rows )
		{
			$ProductCategory[$rows['product_category_id']] = $rows['product_category_code'];

		}
		return $ProductCategory;
	}
	private function getCampaignInfo()
	{

		$data = array();
		if($this->havepost('Campaign'))
		{
			$sql = "select
					b.UserId as mgrid,
					b.id as mgr,
					b.full_name as mgrname,
					count(si.CustomerId) as datasize
					from t_gn_assignment si
					inner join t_gn_customer cs on si.CustomerId=cs.CustomerId
					inner join tms_agent a on si.AssignSelerId=a.UserId
					inner join tms_agent b on a.mgr_id=b.UserId
					where 1=1
					and cs.CampaignId in (".$this->escPost('Campaign').")
					";
			if($this->havepost('Agent') != "") {
				$sql .= " and si.AssignSelerId IN (".$this->escPost('Agent').")";
			}
			if($this->havepost('Supervisor') != "") {
				$sql .= " and a.spv_id IN (".$this->escPost('Supervisor').")";
			}
			$sql .= " GROUP BY b.UserId,b.id,b.full_name;";
			// echo "<pre>".$sql."</pre>";
			$qry = $this ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$data[$rows['mgrid']]['mgrid'] = $rows['mgrid'];
				$data[$rows['mgrid']]['mgr'] = $rows['mgr'];
				$data[$rows['mgrid']]['mgrname'] = $rows['mgrname'];
				$data[$rows['mgrid']]['datasize'] = $rows['datasize'];
				// $data[$rows['UserId']]['untouch'] = $rows['untouch'];
			}
		}

		return $data;
	}

	private function getReasonCall()
	{

		$data = array();
		if($this->havepost('Campaign'))
		{
			$sql="select a.mgr_id as mgrid,
							COUNT(if(cs.CallReasonId in (1),si.CustomerId,NULL)) as BUSY,
							COUNT(if(cs.CallReasonId in (2),si.CustomerId,NULL)) as INVALID_NUMBER,
							COUNT(if(cs.CallReasonId in (3),si.CustomerId,NULL)) as NO_PICK,
							COUNT(if(cs.CallReasonId in (4),si.CustomerId,NULL)) as CALL_AGAIN,
							COUNT(if(cs.CallReasonId in (5),si.CustomerId,NULL)) as MISS_CUSTOMER,
							COUNT(if(cs.CallReasonId in (6),si.CustomerId,NULL)) as THINKING,
							COUNT(if(cs.CallReasonId in (7),si.CustomerId,NULL)) as MOVED,
							COUNT(if(cs.CallReasonId in (8),si.CustomerId,NULL)) as OVERAGE,
							COUNT(if(cs.CallReasonId in (9),si.CustomerId,NULL)) as DONOTCALL,
							COUNT(if(cs.CallReasonId in (10),si.CustomerId,NULL)) as NO_CARD,
							COUNT(if(cs.CallReasonId in (11),si.CustomerId,NULL)) as NOT_INTEREST,
							COUNT(if(cs.CallReasonId in (12),si.CustomerId,NULL)) as WRONG_PERSON,
							COUNT(if(cs.CallReasonId in (13),si.CustomerId,NULL)) as FOLLOWUP_EMAIL,
							COUNT(if(cs.CallReasonId in (14),si.CustomerId,NULL)) as FOLLOWUP_WA,
							COUNT(if(cs.CallReasonId in (15),si.CustomerId,NULL)) as SALES,
							COUNT(if(cs.CallReasonId in (17),si.CustomerId,NULL)) as Reject_WA,
							COUNT(if(cs.CallReasonId in (18),si.CustomerId,NULL)) as Reject_Email,
							COUNT(if(cs.CallReasonId in (19),si.CustomerId,NULL)) as Agree_Email,
							COUNT(if(cs.CallReasonId in (20),si.CustomerId,NULL)) as Agree_WA,
							COUNT(if(cs.CallReasonId in (21),si.CustomerId,NULL)) as Thinking_Email,
							COUNT(if(cs.CallReasonId in (22),si.CustomerId,NULL)) as Thinking_WA,
							COUNT(IF(cs.CallReasonId IS NOT NULL,si.CustomerId,NULL)) AS Touch

						from t_gn_customer cs
						inner join t_gn_assignment si on si.CustomerId=cs.CustomerId
						inner join tms_agent a on a.UserId=si.AssignSelerId
						inner join t_lk_callreason c on cs.CallReasonId = c.CallReasonId
						where a.handling_type=4
					and c.CallReasonStatusFlag=1
					AND cs.CampaignId IN (".$this->escPost('Campaign').")
					AND cs.CustomerUpdatedTs >='".$this->start_date." 00:00:00'
					AND cs.CustomerUpdatedTs <= '".$this->end_date." 23:00:00'";

			if($this->havepost('Agent') != "") {
				$sql .= " and si.AssignSelerId IN (".$this->escPost('Agent').")";
			}
			if($this->havepost('Supervisor') != "") {
				$sql .= " and a.spv_id IN (".$this->escPost('Supervisor').")";
			}

			$sql .=	" group by a.mgr_id;";
			 echo "<pre>".$sql."</pre>";
			$qry = $this ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$data[$rows['mgrid']]['BUSY'] += (INT)$rows['BUSY'];
				$data[$rows['mgrid']]['INVALID_NUMBER'] += (INT)$rows['INVALID_NUMBER'];
				$data[$rows['mgrid']]['NO_PICK'] += (INT)$rows['NO_PICK'];
				$data[$rows['mgrid']]['CALL_AGAIN'] += (INT)$rows['CALL_AGAIN'];
				$data[$rows['mgrid']]['MISS_CUSTOMER'] += (INT)$rows['MISS_CUSTOMER'];
				$data[$rows['mgrid']]['THINKING'] += (INT)$rows['THINKING'];
				$data[$rows['mgrid']]['MOVED'] += (INT)$rows['MOVED'];
				$data[$rows['mgrid']]['OVERAGE'] += (INT)$rows['OVERAGE'];
				$data[$rows['mgrid']]['DONOTCALL'] += (INT)$rows['DONOTCALL'];
				$data[$rows['mgrid']]['NO_CARD'] += (INT)$rows['NO_CARD'];
				$data[$rows['mgrid']]['NOT_INTEREST'] += (INT)$rows['NOT_INTEREST'];
				$data[$rows['mgrid']]['WRONG_PERSON'] += (INT)$rows['WRONG_PERSON'];
				$data[$rows['mgrid']]['FOLLOWUP_EMAIL'] += (INT)$rows['FOLLOWUP_EMAIL'];
				$data[$rows['mgrid']]['FOLLOWUP_WA'] += (INT)$rows['FOLLOWUP_WA'];
				$data[$rows['mgrid']]['SALES'] += (INT)$rows['SALES'];
				$data[$rows['mgrid']]['Thinking_WA'] += (INT)$rows['Thinking_WA'];
				$data[$rows['mgrid']]['Thinking_Email'] += (INT)$rows['Thinking_Email'];
				$data[$rows['mgrid']]['Agree_WA'] += (INT)$rows['Agree_WA'];
				$data[$rows['mgrid']]['Agree_Email'] += (INT)$rows['Agree_Email'];
				$data[$rows['mgrid']]['Reject_Email'] += (INT)$rows['Reject_Email'];
				$data[$rows['mgrid']]['Reject_WA'] += (INT)$rows['Reject_WA'];
				$data[$rows['mgrid']]['Touch'] += (INT)$rows['Touch'];
			}
		}

		return $data;
	}

	private function getCasesAPE()
	{

		$data = array();
		if($this->havepost('Campaign'))
		{
			$sql="SELECT h.mgr_id as mgrid,
					count(b.InsuredId) AS CASES,
					round(SUM(IF(d.PayModeId=2,c.Premi*12,c.Premi))) as APE,
					round(sum(if(d.PayModeId=1,c.Premi/12,c.Premi))) as PREMI
					FROM t_gn_customer a
					INNER JOIN t_gn_insured b ON a.CustomerId=b.CustomerId
					INNER JOIN t_gn_policy c ON b.PolicyId = c.PolicyId
					INNER JOIN t_gn_productplan d ON c.ProductPlanId = d.ProductPlanId
					INNER JOIN t_gn_product e ON d.ProductId=e.ProductId
					INNER JOIN t_gn_product_category f ON e.product_category_id=f.product_category_id
					INNER JOIN t_gn_uploadreport g ON a.UploadId = g.UploadId
					inner join tms_agent h on h.UserId=a.SellerId
					WHERE 1=1
					AND a.CampaignId IN (".$this->escPost('Campaign').")
					AND a.CallReasonId IN (15)
					AND a.CustomerUpdatedTs >='".$this->start_date." 00:00:00'
					AND a.CustomerUpdatedTs <= '".$this->end_date." 23:00:00'";

			if($this->havepost('Agent') != "") {
				$sql .= " and a.SellerId IN (".$this->escPost('Agent').")";
			}
			if($this->havepost('Supervisor') != "") {
				$sql .= "and h.spv_id IN (".$this->escPost('Supervisor').")";
			}

			$sql .=	" group by h.mgr_id;";
			// echo "<pre>".$sql."</pre>";
			$qry = $this ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				// $data[$rows['UserId']]['CASES'][$rows['product_category_id']] = $rows['CASES'];
				$data[$rows['mgrid']]['APE'] += (int)$rows['APE'];
				$data[$rows['mgrid']]['PREMI'] += (int)$rows['PREMI'];
				$data[$rows['mgrid']]['CASES'] += (int)$rows['CASES'];
			}
		}

		return $data;
	}

	private function getAttempt()
	{

		$data = array();
		if($this->havepost('Campaign'))
		{
			$sql = "SELECT
					c.mgr_id as mgrid,
					COUNT(b.CallHistoryId) AS CallAttempt
					from t_gn_callhistory b
					inner join t_gn_customer a on a.CustomerId=b.CustomerId
					INNER JOIN tms_agent c ON b.CreatedById = c.UserId
					WHERE 1=1
					and c.handling_type=4
					AND a.CampaignId IN (".$this->escPost('Campaign').")
					and b.CallHistoryCallDate >= '".$this->start_date." 00:00:00'
					AND b.CallHistoryCallDate <='".$this->end_date." 23:00:00'";

			if($this->havepost('Agent') != "") {
				$sql .= " and b.CreatedById IN (".$this->escPost('Agent').")";
			}
			if($this->havepost('Supervisor') != "") {
				$sql .= " and c.spv_id IN (".$this->escPost('Supervisor').")";
			}
			$sql .= " GROUP BY c.mgr_id;";
			// echo "<pre>".$sql."</pre>";
			$qry = $this ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				// $data[$rows['CampaignId']] = $rows['CallAttempt'];
				$data[$rows['mgrid']]['CallAttempt'] = $rows['CallAttempt'];

			}
		}

		return $data;
	}

	private function getReason()
	{

		$data = array();
		if($this->havepost('Campaign'))
		{
			$sql = "SELECT d.mgr_id as mgrid,
					count(IF(b.CallReasonContactedFlag=0,si.CustomerId,null)) AS uncontacted,
					count(IF(b.CallReasonContactedFlag=1,si.CustomerId,null)) AS contacted
					FROM t_gn_customer a
					inner join t_gn_assignment si on si.CustomerId=a.CustomerId
					INNER JOIN t_lk_callreason b ON a.CallReasonId = b.CallReasonId
					inner join tms_agent d on d.UserId=si.AssignSelerId
					WHERE 1=1
					AND a.CampaignId IN (".$this->escPost('Campaign').")
					and b.CallReasonStatusFlag=1
					AND a.CustomerUpdatedTs >='".$this->start_date." 00:00:00'
					AND a.CustomerUpdatedTs <='".$this->end_date." 23:00:00'";

			if($this->havepost('Agent') != "") {
				$sql .= " and si.AssignSelerId IN (".$this->escPost('Agent').")";
			}
			if($this->havepost('Supervisor') != "") {
				$sql .= " and d.spv_id  IN (".$this->escPost('Supervisor').")";
			}
			$sql .= " GROUP BY d.mgr_id;";
			// echo "<pre>".$sql."</pre>";
			$qry = $this ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				// $data[$rows['CampaignId']]['contact'] += $rows['contacted'];
				// $data[$rows['CampaignId']]['uncontacted'] += $rows['uncontacted'];
				$data[$rows['mgrid']]['contacted'] += (int)$rows['contacted'];
				$data[$rows['mgrid']]['uncontacted'] += (int)$rows['uncontacted'];

			}
		}

		return $data;
	}
	private function write_header_closed()
	{
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" >
				<tr>
					<td rowspan=\"2\" class=\"header first\" nowrap align=\"center\">No.</td>
					<td rowspan=\"2\" class=\"header first\" nowrap align=\"center\">MGR ID</td>
					<td rowspan=\"2\" class=\"header middle\" nowrap align=\"center\">MGR Name</td>
					<td rowspan=\"2\" class=\"header middle\" nowrap align=\"center\">Datasize</td>
					<td rowspan=\"2\" class=\"header middle\" nowrap align=\"center\">New Data</td>
					<td rowspan=\"2\" class=\"header middle\" nowrap align=\"center\">Utilize (TBS)</td>
					<td colspan=\"18\" class=\"header middle\" nowrap align=\"center\">Contacted</td>
					<td colspan=\"3\" class=\"header middle\" nowrap align=\"center\">Not Contacted</td>
					<td rowspan=\"2\" class=\"header middle\" nowrap align=\"center\">Cases</td>
					<td rowspan=\"2\" class=\"header middle\" nowrap align=\"center\">APE</td>
					<td rowspan=\"2\" class=\"header middle\" nowrap align=\"center\">Case Size</td>
					<td rowspan=\"2\" class=\"header middle\" nowrap align=\"center\">Attempt</td>
					<td rowspan=\"2\" class=\"header middle\" nowrap align=\"center\">Attempt Ratio</td>
					<td rowspan=\"2\" class=\"header middle\" nowrap align=\"center\">Contacted Rate</td>
					<td rowspan=\"2\" class=\"header middle\" nowrap align=\"center\">Uncontacted Rate</td>
					<td rowspan=\"2\" class=\"header middle\" nowrap align=\"center\">Response Rate</td>
					<td rowspan=\"2\" class=\"header middle\" nowrap align=\"center\">Success Rate</td>
					<td rowspan=\"2\" class=\"header middle\" nowrap align=\"center\">Presentation Rate</td>
				</tr>
				<tr>
					<td class=\"header middle\" nowrap align=\"center\">Call Again</td>
					<td class=\"header middle\" nowrap align=\"center\">Miss Customer</td>
					<td class=\"header middle\" nowrap align=\"center\">Thinking</td>
					<td class=\"header middle\" nowrap align=\"center\">Already Moved</td>
					<td class=\"header middle\" nowrap align=\"center\">Deceased / Overage</td>
					<td class=\"header middle\" nowrap align=\"center\">Do Not Call</td>
					<td class=\"header middle\" nowrap align=\"center\">No Card</td>
					<td class=\"header middle\" nowrap align=\"center\">Not Interest</td>
					<td class=\"header middle\" nowrap align=\"center\">Wrong Person</td>
					<td class=\"header middle\" nowrap align=\"center\">Follow Up Email</td>
					<td class=\"header middle\" nowrap align=\"center\">Follow Up WA</td>
					<td class=\"header middle\" nowrap align=\"center\">Sales / interested</td>
					<td class=\"header middle\" nowrap align=\"center\">Reject WA</td>
					<td class=\"header middle\" nowrap align=\"center\">Reject Email</td>
					<td class=\"header middle\" nowrap align=\"center\">Agree Email</td>
					<td class=\"header middle\" nowrap align=\"center\">Agree WA</td>
					<td class=\"header middle\" nowrap align=\"center\">Thinking Email</td>
					<td class=\"header middle\" nowrap align=\"center\">Thinking WA</td>
					<td class=\"header middle\" nowrap align=\"center\">Busy</td>
					<td class=\"header middle\" nowrap align=\"center\">Invalid Number</td>
					<td class=\"header middle\" nowrap align=\"center\">No Pick Up</td>
				</tr>";
	}

	private function write_content()
	{
		$CampaignInfo = $this->getCampaignInfo();
		$CasesAPE = $this->getCasesAPE();
		$summaryReason = $this->getReason();
		$ReasonCall = $this->getReasonCall();
		$attempt = $this->getAttempt();
		// echo "<pre>";
		// print_r($CampaignInfo);
		// echo "</pre>";
		// echo "<pre>";
		// print_r($CasesAPE);
		// echo "</pre>";
		$no=1;
		foreach($CampaignInfo as $up_id => $arr_val)
		{
			$datasize		= (isset($CampaignInfo[$up_id]['datasize'])?$CampaignInfo[$up_id]['datasize']:"0");
			$BUSY			= (isset($ReasonCall[$up_id]['BUSY'])?$ReasonCall[$up_id]['BUSY']:"0");
			$INVALID_NUMBER = (isset($ReasonCall[$up_id]['INVALID_NUMBER'])?$ReasonCall[$up_id]['INVALID_NUMBER']:"0");
			$NO_PICK 		= (isset($ReasonCall[$up_id]['NO_PICK'])?$ReasonCall[$up_id]['NO_PICK']:"0");
			$CALL_AGAIN		= (isset($ReasonCall[$up_id]['CALL_AGAIN'])?$ReasonCall[$up_id]['CALL_AGAIN']:"0");
			$MISS_CUSTOMER 	= (isset($ReasonCall[$up_id]['MISS_CUSTOMER'])?$ReasonCall[$up_id]['MISS_CUSTOMER']:"0");
			$THINKING 		= (isset($ReasonCall[$up_id]['THINKING'])?$ReasonCall[$up_id]['THINKING']:"0");
			$MOVED 			= (isset($ReasonCall[$up_id]['MOVED'])?$ReasonCall[$up_id]['MOVED']:"0");
			$OVERAGE 		= (isset($ReasonCall[$up_id]['OVERAGE'])?$ReasonCall[$up_id]['OVERAGE']:"0");
			$DONOTCALL 		= (isset($ReasonCall[$up_id]['DONOTCALL'])?$ReasonCall[$up_id]['DONOTCALL']:"0");
			$NO_CARD 		= (isset($ReasonCall[$up_id]['NO_CARD'])?$ReasonCall[$up_id]['NO_CARD']:"0");
			$NOT_INTEREST 	= (isset($ReasonCall[$up_id]['NOT_INTEREST'])?$ReasonCall[$up_id]['NOT_INTEREST']:"0");
			$WRONG_PERSON 	= (isset($ReasonCall[$up_id]['WRONG_PERSON'])?$ReasonCall[$up_id]['WRONG_PERSON']:"0");
			$FOLLOWUP_EMAIL = (isset($ReasonCall[$up_id]['FOLLOWUP_EMAIL'])?$ReasonCall[$up_id]['FOLLOWUP_EMAIL']:"0");
			$$Reject_WA		= (isset($ReasonCall[$up_id]['$Reject_WA'])?$ReasonCall[$up_id]['$Reject_WA']:"0");
			$$Reject_Email	= (isset($ReasonCall[$up_id]['$Reject_Email'])?$ReasonCall[$up_id]['$$Reject_Email']:"0");
			$$Agree_Email	= (isset($ReasonCall[$up_id]['$Agree_Email'])?$ReasonCall[$up_id]['$$Agree_Email']:"0");
			$$Agree_WA		= (isset($ReasonCall[$up_id]['$Agree_WA'])?$ReasonCall[$up_id]['$$Agree_WA']:"0");
			$FOLLOWUP_WA 	= (isset($ReasonCall[$up_id]['FOLLOWUP_WA'])?$ReasonCall[$up_id]['FOLLOWUP_WA']:"0");
			$Thinking_Email	= (isset($ReasonCall[$up_id]['Thinking_Email'])?$ReasonCall[$up_id]['Thinking_Email']:"0");
			$Thinking_WA	= (isset($ReasonCall[$up_id]['Thinking_WA'])?$ReasonCall[$up_id]['Thinking_WA']:"0");
			$SALES 			= (isset($ReasonCall[$up_id]['SALES'])?$ReasonCall[$up_id]['SALES']:"0");
			$utilize 		= $CALL_AGAIN+$MISS_CUSTOMER+$THINKING+$MOVED+$OVERAGE+$DONOTCALL+$NO_CARD+$NOT_INTEREST+$WRONG_PERSON+$FOLLOWUP_EMAIL+$FOLLOWUP_WA+$SALES+$Reject_WA+$Reject_Email+$Agree_Email+$Agree_WA+$Thinking_Email+$Thinking_WA+$BUSY+$INVALID_NUMBER+$NO_PICK;
			$untouch		= $datasize-$utilize;
			echo "<tr>
					<td rowspan=\" ".$rowspan." \" class=\"content first\" nowrap>".$no."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['mgr']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['mgrname']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['datasize']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".$untouch."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".$utilize."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($ReasonCall[$up_id]['CALL_AGAIN'])?$ReasonCall[$up_id]['CALL_AGAIN']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($ReasonCall[$up_id]['MISS_CUSTOMER'])?$ReasonCall[$up_id]['MISS_CUSTOMER']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($ReasonCall[$up_id]['THINKING'])?$ReasonCall[$up_id]['THINKING']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($ReasonCall[$up_id]['MOVED'])?$ReasonCall[$up_id]['MOVED']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($ReasonCall[$up_id]['OVERAGE'])?$ReasonCall[$up_id]['OVERAGE']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($ReasonCall[$up_id]['DONOTCALL'])?$ReasonCall[$up_id]['DONOTCALL']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($ReasonCall[$up_id]['NO_CARD'])?$ReasonCall[$up_id]['NO_CARD']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($ReasonCall[$up_id]['NOT_INTEREST'])?$ReasonCall[$up_id]['NOT_INTEREST']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($ReasonCall[$up_id]['WRONG_PERSON'])?$ReasonCall[$up_id]['WRONG_PERSON']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($ReasonCall[$up_id]['FOLLOWUP_EMAIL'])?$ReasonCall[$up_id]['FOLLOWUP_EMAIL']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($ReasonCall[$up_id]['FOLLOWUP_WA'])?$ReasonCall[$up_id]['FOLLOWUP_WA']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($ReasonCall[$up_id]['SALES'])?$ReasonCall[$up_id]['SALES']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($ReasonCall[$up_id]['Reject_WA'])?$ReasonCall[$up_id]['Reject_WA']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($ReasonCall[$up_id]['Reject_Email'])?$ReasonCall[$up_id]['Reject_Email']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($ReasonCall[$up_id]['Agree_Email'])?$ReasonCall[$up_id]['Agree_Email']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($ReasonCall[$up_id]['Agree_WA'])?$ReasonCall[$up_id]['Agree_WA']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($ReasonCall[$up_id]['Thinking_Email'])?$ReasonCall[$up_id]['Thinking_Email']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($ReasonCall[$up_id]['Thinking_WA'])?$ReasonCall[$up_id]['Thinking_WA']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".$BUSY."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($ReasonCall[$up_id]['INVALID_NUMBER'])?$ReasonCall[$up_id]['INVALID_NUMBER']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($ReasonCall[$up_id]['NO_PICK'])?$ReasonCall[$up_id]['NO_PICK']:"0")."</td>

					";

					$APE 			= (isset($CasesAPE[$up_id]['APE'])?$CasesAPE[$up_id]['APE']:"0");
					$CASES 	 		= (isset($CasesAPE[$up_id]['CASES'])?$CasesAPE[$up_id]['CASES']:"0");
					$PREMI 	 		= (isset($CasesAPE[$up_id]['PREMI'])?$CasesAPE[$up_id]['PREMI']:"0");
					$contact 		= (isset($summaryReason[$up_id]['contacted'])?$summaryReason[$up_id]['contacted']:"0");
					$uncontact 		= (isset($summaryReason[$up_id]['uncontacted'])?$summaryReason[$up_id]['uncontacted']:"0");
					$touch			= (isset($ReasonCall[$up_id]['Touch'])?$ReasonCall[$up_id]['Touch']:"0");
					$attemp 		= (isset($attempt[$up_id]['CallAttempt'])?$attempt[$up_id]['CallAttempt']:"0");

					$presentation 	= $THINKING+$NOT_INTEREST+$NO_CARD+$SALES;
					$AVGAPE			= $APE/$CASES;
					$prensentationrate = ($presentation/$contact)*100;
					// $ContactRate	= $touch/$contact;
					$ContactRate	= ($contact/$touch)*100;
					// $unContactRate	= $touch/$uncontact;
					$unContactRate	= ($uncontact/$touch) * 100;
					
					$ResponseRate	= ($CASES/$utilize) * 100;
					$SuksesRate		= ($CASES/$contact) * 100;
					
					$ConversionRate	= ($CASES/$contact) * 100;
					$AttempRatio	= ($attemp/$utilize);
					// echo ".."
					// echo "cases =".$CASES."<br>";
					// echo "datasize =".$datasize;
			echo "	<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".$CASES."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($CasesAPE[$up_id]['APE'])?$CasesAPE[$up_id]['APE']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($CasesAPE[$up_id]['PREMI'])?$CasesAPE[$up_id]['PREMI']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".(isset($attempt[$up_id]['CallAttempt'])?$attempt[$up_id]['CallAttempt']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content lasted\" align=\"right\" nowrap>".round($AttempRatio,2)."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".round($ContactRate,2)." %</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".round($unContactRate,2)." %</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".round($ResponseRate,2)." %</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".round($SuksesRate,2)." %</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" align=\"right\" nowrap>".round($prensentationrate,2)." %</td>
					";

			echo "</tr>";
			$no++;
			$cases = 0;
			$tdatasize			+= $datasize;
			$tBUSY				+= $BUSY;
			$tINVALID_NUMBER 	+= $INVALID_NUMBER;
			$tNO_PICK 			+= $NO_PICK;
			$tCALL_AGAIN		+= $CALL_AGAIN;
			$tMISS_CUSTOMER 	+= $MISS_CUSTOMER;
			$tTHINKING 			+= $THINKING;
			$tMOVED 			+= $MOVED;
			$tOVERAGE 			+= $OVERAGE;
			$tDONOTCALL 		+= $DONOTCALL;
			$tNO_CARD 			+= $NO_CARD;
			$tNOT_INTEREST 		+= $NOT_INTEREST;
			$tWRONG_PERSON 		+= $WRONG_PERSON;
			$tFOLLOWUP_EMAIL 	+= $FOLLOWUP_EMAIL;
			$tReject_WA			+= $Reject_WA;
			$tReject_Email		+= $Reject_Email;
			$tAgree_Email		+= $Agree_Email;
			$tAgree_WA			+= $Agree_WA;
			$tFOLLOWUP_WA 		+= $FOLLOWUP_WA;
			$tThinking_Email	+= $Thinking_Email;
			$tThinking_WA		+= $Thinking_WA;
			$tSALES 			+= $SALES;
			$tutilize 			+= $utilize;
			$tuntouch			+= $untouch;
			$tAPE 				+= $APE;
			$tCASES 			+= $CASES;
			$tPREMI 			+= $PREMI;
			$tcontact 			+= $contact;
			$tuncontact 		+= $uncontact;
			$ttouch 			+= $touch;
			$tattemp 			+= $attemp;
			$tattempratio		= ($tattemp/$tutilize);
			$tContactRate		= ($tcontact/$ttouch)*100;
			$tunContactRate		= ($tuncontact/$ttouch)*100;
			$tResponseRate		= ($tCASES/$tutilize)*100;
			$tSuksesRate		= ($tCASES/$tcontact)*100;
			$tpresentation 		+= $presentation;
			$tprensentationrate	= ($tpresentation/$tcontact)*100;
		}

		echo "	<tr>
				<td colspan=\"3\" class=\"header middle\" nowrap align=\"center\">Total</td>
				<td class=\"header middle\" nowrap align=\"center\">$tdatasize</td>
				<td class=\"header middle\" nowrap align=\"center\">$tuntouch</td>
				<td class=\"header middle\" nowrap align=\"center\">$tutilize</td>
				<td class=\"header middle\" nowrap align=\"center\">$tCALL_AGAIN</td>
				<td class=\"header middle\" nowrap align=\"center\">$tMISS_CUSTOMER</td>
				<td class=\"header middle\" nowrap align=\"center\">$tTHINKING</td>
				<td class=\"header middle\" nowrap align=\"center\">$tMOVED</td>
				<td class=\"header middle\" nowrap align=\"center\">$tOVERAGE</td>
				<td class=\"header middle\" nowrap align=\"center\">$tDONOTCALL</td>
				<td class=\"header middle\" nowrap align=\"center\">$tNO_CARD</td>
				<td class=\"header middle\" nowrap align=\"center\">$tNOT_INTEREST</td>
				<td class=\"header middle\" nowrap align=\"center\">$tWRONG_PERSON</td>
				<td class=\"header middle\" nowrap align=\"center\">$tFOLLOWUP_EMAIL</td>
				<td class=\"header middle\" nowrap align=\"center\">$tFOLLOWUP_WA</td>
				<td class=\"header middle\" nowrap align=\"center\">$tSALES</td>
				<td class=\"header middle\" nowrap align=\"center\">$tReject_WA</td>
				<td class=\"header middle\" nowrap align=\"center\">$tReject_Email</td>
				<td class=\"header middle\" nowrap align=\"center\">$tAgree_Email</td>
				<td class=\"header middle\" nowrap align=\"center\">$tAgree_WA</td>
				<td class=\"header middle\" nowrap align=\"center\">$tThinking_Email</td>
				<td class=\"header middle\" nowrap align=\"center\">$tThinking_WA</td>
				<td class=\"header middle\" nowrap align=\"center\">$tBUSY</td>
				<td class=\"header middle\" nowrap align=\"center\">$tINVALID_NUMBER</td>
				<td class=\"header middle\" nowrap align=\"center\">$tNO_PICK</td>
				<td class=\"header middle\" nowrap align=\"center\">$tCASES</td>
				<td class=\"header middle\" nowrap align=\"center\">$tAPE</td>
				<td class=\"header middle\" nowrap align=\"center\">$tPREMI</td>
				<td class=\"header middle\" nowrap align=\"center\">$tattemp</td>
				<td class=\"header middle\" nowrap align=\"center\">".round($tattempratio,2)." </td>
				<td class=\"header middle\" nowrap align=\"center\">".round($tContactRate,2)." %</td>
				<td class=\"header middle\" nowrap align=\"center\">".round($tunContactRate,2)." %</td>
				<td class=\"header middle\" nowrap align=\"center\">".round($tResponseRate,2)." %</td>
				<td class=\"header middle\" nowrap align=\"center\">".round($tSuksesRate,2)." %</td>
				<td class=\"header middle\" nowrap align=\"center\">".round($tprensentationrate,2)." %</td>
				</tr></table> ";

		// echo "<br>".$tCASES."<br>";
		// echo $tcontact;
	}

	function write_footer()
	{
		echo "	<tr>
					<td class=\"total first\" nowrap></td>
					<td class=\"total middle\" nowrap></td>
				</tr></table> ";
	}
}
