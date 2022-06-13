<?php 

if(!define('CardClose','10')) define('CardClose','10');

class free_fpa extends index
{
	private $product_category;
	function free_fpa()
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
		if($this->escPost('Option') == 1)
		{
			$sql = "select 
					cs.SellerId,c.id as mgrid,c.full_name as mgr_name,
					b.id as spvid,b.full_name as spv_name,a.id as agentid,a.full_name as agentname,
					count(cs.CustomerId) as cases
					from t_gn_customer cs
					left JOIN t_gn_policyautogen d ON cs.CustomerId = d.CustomerId
					left join t_gn_product f on d.ProductId = f.ProductId
					inner join tms_agent a on cs.SellerId=a.UserId
					inner join tms_agent b on a.spv_id=b.UserId
					inner join tms_agent c on a.mgr_id=c.UserId

					where cs.CallReasonId=15
					and f.ProductId=2
					and cs.CustomerUpdatedTs >= '".$this->start_date." 00:00:00'
					and cs.CustomerUpdatedTs <= '".$this->end_date." 23:00:00'
					";
		} else if($this->escPost('Option') == 0){
			$sql = "select 
					cs.SellerId,c.id as mgrid,c.full_name as mgr_name,
					b.id as spvid,b.full_name as spv_name,a.id as agentid,a.full_name as agentname,
					count(cs.CustomerId) as cases
					from t_gn_customer cs
					left JOIN t_gn_policyautogen d ON cs.CustomerId = d.CustomerId
					left join t_gn_product f on d.ProductId = f.ProductId
					inner join tms_agent a on cs.SellerId=a.UserId
					inner join tms_agent b on a.spv_id=b.UserId
					inner join tms_agent c on a.mgr_id=c.UserId

					where cs.CallReasonId=15
					and f.ProductId=2
					and cs.CallReasonQue=1
					and cs.CustomerUpdatedTs >= '".$this->start_date." 00:00:00'
					and cs.CustomerUpdatedTs <= '".$this->end_date." 23:00:00'
					";
		}
		
		if($this->havepost('Agent') != "") {
			$sql .= " and cs.SellerId IN (".$this->escPost('Agent').")"; 
		}
		if($this->havepost('Supervisor') != "") {
			$sql .= " and a.spv_id IN (".$this->escPost('Supervisor').")"; 
		}
		$sql .= " group by cs.SellerId,a.id,a.full_name,b.id,b.full_name,c.id,c.full_name;";
			$qry = $this ->query($sql);
			//echo "<pre>".$sql."</pre>";
			foreach($qry -> result_assoc() as $rows )
			{
				
				$data[$rows['SellerId']]['mgrid'] = $rows['mgrid'];
				$data[$rows['SellerId']]['mgr_name'] = $rows['mgr_name'];
				$data[$rows['SellerId']]['spvid'] = $rows['spvid'];
				$data[$rows['SellerId']]['spv_name'] = $rows['spv_name'];
				$data[$rows['SellerId']]['agentid'] = $rows['agentid'];
				$data[$rows['SellerId']]['agentname'] = $rows['agentname'];
				$data[$rows['SellerId']]['cases'] = $rows['cases'];
			}
		return $data;
	}	

	private function write_header_closed()
	{
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" >
				<tr>
					<td class=\"header first\" nowrap align=\"center\">No</td>
					<td class=\"header middle\" nowrap align=\"center\">MGR CODE</td>
					<td class=\"header middle\" nowrap align=\"center\">MGR NAME</td>
					<td class=\"header middle\" nowrap align=\"center\">SPV CODE</td>
					<td class=\"header middle\" nowrap align=\"center\">SPV NAME</td>
					<td class=\"header middle\" nowrap align=\"center\">AGENT CODE</td>";
					
		echo "		<td class=\"header middle\" nowrap align=\"center\">AGENT NAME</td>
					<td class=\"header middle\" nowrap align=\"center\">CASES</td>
				</tr>";
	}
	
	private function write_content()
	{
		$Result = $this->getCampaignInfo();
		// echo "<pre>";
		// print_r($CampaignInfo);
		// echo "</pre>";
		// echo "<pre>";
		// print_r($CasesAPE);
		// echo "</pre>";
		$no=1;
		foreach($Result as $up_id => $arr_val)
		{
			echo "<tr>
					<td rowspan=\" ".$rowspan." \" class=\"content first\" nowrap>".$no."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['mgrid']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['mgr_name']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['spvid']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['spv_name']."</td>";
					
			echo "	<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['agentid']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['agentname']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['cases']."</td>
				</tr>";
			$no++;
	
		}
	}
	
	function write_footer()
	{
		echo "	<tr>
					<td class=\"total first\" nowrap></td>
					<td class=\"total middle\" nowrap></td>
				</tr></table> ";
	}
}