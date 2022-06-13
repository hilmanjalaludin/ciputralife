<?php
if(!define('CardClose','10')) define('CardClose','10');

class cmp_tracking_overview extends index
{
	private $product_category;
	
	function cmp_tracking_overview()
	{
		$this->_Excel("Campaign_Tracking_Overview");
		$this->start_date = $this -> formatDateEng($this -> escPost('start_date'));
		$this->end_date = $this -> formatDateEng($this -> escPost('end_date'));
		
		ini_set("memory_limit","1024M");
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
			$sql = "SELECT c.UploadId,DATE_FORMAT(c.UploadDateTs,'%Y%m%d') AS UploadDate,
					b.CampaignNumber,
					b.CampaignName,
					COUNT(a.CustomerId) AS 'Database',
					SUM(IF(a.CallReasonId IS NOT NULL,1,0)) AS Touch,
					SUM(IF(a.CallReasonId IS NULL,1,0)) AS Untouch
					FROM t_gn_customer a 
					INNER JOIN t_gn_campaign b ON a.CampaignId=b.CampaignId
					INNER JOIN t_gn_uploadreport c ON a.UploadId = c.UploadId
					WHERE 1=1 
					AND a.CampaignId IN (".$this->escPost('Campaign').")
					AND DATE(c.UploadDateTs) >='".$this->start_date."'
					AND DATE(c.UploadDateTs) <= '".$this->end_date."'
					GROUP BY c.UploadId";
			
			$qry = $this ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$data[$rows['UploadId']]['uploaddate'] = $rows['UploadDate'];
				$data[$rows['UploadId']]['CampaignNumber'] = $rows['CampaignNumber'];
				$data[$rows['UploadId']]['CampaignName'] = $rows['CampaignName'];
				$data[$rows['UploadId']]['Database'] = $rows['Database'];
				$data[$rows['UploadId']]['Touch'] = $rows['Touch'];
				$data[$rows['UploadId']]['Untouch'] = $rows['Untouch'];
				
			}
		}
		
		return $data;
	}
	
	private function getCasesAPE()
	{
		
		$data = array();
		if($this->havepost('Campaign'))
		{
			$sql="SELECT g.UploadId,g.UploadDateTs,
					e.product_category_id,
					count(b.InsuredId) AS CASES,
					SUM(IF(d.PayModeId=2,c.Premi*12,c.Premi)) as APE
					FROM t_gn_customer a
					INNER JOIN t_gn_insured b ON a.CustomerId=b.CustomerId
					INNER JOIN t_gn_policy c ON b.PolicyId = c.PolicyId
					INNER JOIN t_gn_productplan d ON c.ProductPlanId = d.ProductPlanId
					INNER JOIN t_gn_product e ON d.ProductId=e.ProductId
					INNER JOIN t_gn_product_category f ON e.product_category_id=f.product_category_id
					INNER JOIN t_gn_uploadreport g ON a.UploadId = g.UploadId
					WHERE 1=1
					AND a.CampaignId IN (".$this->escPost('Campaign').")
					AND a.CallReasonId IN (20,21)
					AND DATE(g.UploadDateTs) >='".$this->start_date."'
					AND DATE(g.UploadDateTs) <= '".$this->end_date."'
					group by g.UploadId,e.product_category_id";
			// echo $sql;
			$qry = $this ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$data[$rows['UploadId']]['CASES'][$rows['product_category_id']] = $rows['CASES'];				
				$data[$rows['UploadId']]['APE'] += (int) $rows['APE'];			
			}
		}
		
		return $data;
	}
	
	private function getAttempt()
	{
		
		$data = array();
		if($this->havepost('Campaign'))
		{
			$sql = "SELECT d.UploadId,d.UploadDateTs,
					COUNT(b.CallHistoryId) AS CallAttempt 
					FROM t_gn_customer a 
					INNER JOIN t_gn_callhistory b ON a.CustomerId = b.CustomerId
					INNER JOIN tms_agent c ON b.CreatedById = c.UserId
					INNER JOIN t_gn_uploadreport d ON a.UploadId = d.UploadId
					WHERE 1=1
					AND a.CampaignId IN (".$this->escPost('Campaign').")
					AND DATE(d.UploadDateTs) >='".$this->start_date."'
					AND DATE(d.UploadDateTs) <='".$this->end_date."'
					AND c.handling_type = 4
					GROUP BY d.UploadId";
			// echo $sql;
			$qry = $this ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$data[$rows['UploadId']] = $rows['CallAttempt'];				
				
			}
		}
		
		return $data;
	}
	
	private function getReason()
	{
		
		$data = array();
		if($this->havepost('Campaign'))
		{
			$sql = "SELECT c.UploadId,c.UploadDateTs,
					b.CallReasonContactedFlag,a.CallReasonId,
					SUM(IF(b.CallReasonContactedFlag=0,1,0)) AS uncontacted,
					SUM(IF(b.CallReasonContactedFlag=1,1,0)) AS contacted
					FROM t_gn_customer a 
					INNER JOIN t_lk_callreason b ON a.CallReasonId = b.CallReasonId
					INNER JOIN t_gn_uploadreport c ON a.UploadId = c.UploadId
					WHERE 1=1
					AND a.CampaignId IN (".$this->escPost('Campaign').")
					AND DATE(c.UploadDateTs) >='".$this->start_date."'
					AND DATE(c.UploadDateTs) <='".$this->end_date."'
					GROUP BY c.UploadId, a.CallReasonId";
			// echo $sql;
			$qry = $this ->query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$data[$rows['UploadId']]['contact'] += $rows['contacted'];				
				$data[$rows['UploadId']]['uncontacted'] += $rows['uncontacted'];
				if($rows['CallReasonId']==CardClose)
				{
					$data[$rows['UploadId']]['CardClose'] += $rows['contacted'];
				}
			}
		}
		
		return $data;
	}
	
	/* main content EXCEL **/
	function show_content_excel()
	{
		mysql::__construct();
		$this->product_category = $this->getProductCategory();
		self::write_header_closed();
		self::write_content();
		self::write_footer();
	}
	
	function write_footer()
	{
		$this -> _Excel_Footer();
		
	}
	
	private function write_header_closed()
	{
		$this -> _Excel_Header();
		echo "Campaign Overview Report<br />";
		echo "Call Monitoring Date : " .$this->start_date. " - ".$this->end_date."<br />";
		echo "Report Date : ".date("d/m/Y")."<br />";
		
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" >
				<tr>
					<td class=\"xl602508\" nowrap align=\"center\">No</td>
					<td class=\"xl602508\" nowrap align=\"center\">UploadDate</td>
					<td class=\"xl602508\" nowrap align=\"center\">Campaign_id</td>
					<td class=\"xl602508\" nowrap align=\"center\">Campaign Name</td>
					<td class=\"xl602508\" nowrap align=\"center\">Database</td>";
					foreach($this->product_category as $product_category_id => $value)
					{
						echo "<td class=\"xl602508\" nowrap align=\"center\"> CASES ".$value."</td>";
					}
		echo "		<td class=\"xl602508\" nowrap align=\"center\">APE</td>
					<td class=\"xl602508\" nowrap align=\"center\">Contact</td>
					<td class=\"xl602508\" nowrap align=\"center\">Attempt</td>
					<td class=\"xl602508\" nowrap align=\"center\">Untouch</td>
					<td class=\"xl602508\" nowrap align=\"center\">Uncontact</td>
					<td class=\"xl602508\" nowrap align=\"center\">Touch</td>
					<td class=\"xl602508\" nowrap align=\"center\">CardClose</td>
				</tr>";
	}
	private function write_content()
	{
		$CampaignInfo = $this->getCampaignInfo();
		$CasesAPE = $this->getCasesAPE();
		$summaryReason = $this->getReason();
		$attempt = $this->getAttempt();
		
		$no=1;
		foreach($CampaignInfo as $up_id => $arr_val)
		{
			echo "<tr>
					<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$no."</td>
					<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$arr_val['uploaddate']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$arr_val['CampaignNumber']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"xl582508\" nowrap>".$arr_val['CampaignName']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"xl582508\" align=\"right\" nowrap>".$arr_val['Database']."</td>";
					foreach($this->product_category as $product_category_id => $value)
					{
						echo "<td rowspan=\" ".$rowspan." \" class=\"xl582508\" align=\"right\" nowrap>".(isset($CasesAPE[$up_id]['CASES'][$product_category_id])?$CasesAPE[$up_id]['CASES'][$product_category_id]:"0")."</td>";
					}
			echo "	<td rowspan=\" ".$rowspan." \" class=\"xl582508\" align=\"right\" nowrap>".(isset($CasesAPE[$up_id]['APE'])?$CasesAPE[$up_id]['APE']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"xl582508\" align=\"right\" nowrap>".(isset($summaryReason[$up_id]['contact'])?$summaryReason[$up_id]['contact']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"xl582508\" align=\"right\" nowrap>".(isset($attempt[$up_id])?$attempt[$up_id]:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"xl582508\" align=\"right\" nowrap>".$arr_val['Untouch']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"xl582508\" align=\"right\" nowrap>".(isset($summaryReason[$up_id]['uncontacted'])?$summaryReason[$up_id]['uncontacted']:"0")."</td>
					<td rowspan=\" ".$rowspan." \" class=\"xl582508\" align=\"right\" nowrap>".$arr_val['Touch']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"xl582508\" align=\"right\" nowrap>".(isset($summaryReason[$up_id]['CardClose'])?$summaryReason[$up_id]['CardClose']:"0")."</td>
				</tr>";
			$no++;
		}
	}
	
}

?>