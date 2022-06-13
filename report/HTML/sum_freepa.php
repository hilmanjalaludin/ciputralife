<?php 

if(!define('CardClose','10')) define('CardClose','10');

class sum_freepa extends index
{
	// private $product_category;
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
		//$this->product_category = $this->getProductCategory();
		self::write_header_closed();
		self::write_content();
		self::write_footer();
	}
	
	private function write_header_closed()
	{
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" >
				<tr>
					<td class=\"header first\" nowrap align=\"center\">No</td>
					<td class=\"header middle\" nowrap align=\"center\">Prospect ID</td>
					<td class=\"header middle\" nowrap align=\"center\">Policy</td>
					<td class=\"header middle\" nowrap align=\"center\">Name</td>
					<td class=\"header middle\" nowrap align=\"center\">Email</td>
					<td class=\"header middle\" nowrap align=\"center\">Phone</td>
					<td class=\"header middle\" nowrap align=\"center\">Birthdate</td>
					<td class=\"header middle\" nowrap align=\"center\">Gender</td>
					<td class=\"header middle\" nowrap align=\"center\">Age</td>
					<td class=\"header middle\" nowrap align=\"center\">Phone</td>";
					
		echo "		<td class=\"header middle\" nowrap align=\"center\">Expired</td>
					<td class=\"header middle\" nowrap align=\"center\">No. Induk</td>
					<td class=\"header middle\" nowrap align=\"center\">No. Polis</td>
					<td class=\"header middle\" nowrap align=\"center\">Created</td>
					<td class=\"header middle\" nowrap align=\"center\">Updated</td>
					<td class=\"header middle\" nowrap align=\"center\">Remark 1</td>
					<td class=\"header middle\" nowrap align=\"center\">Alamat</td>
					<td class=\"header middle\" nowrap align=\"center\">Provinsi</td>
					<td class=\"header middle\" nowrap align=\"center\">Survey</td>";

		echo "		<td class=\"header middle\" nowrap align=\"center\">TFA Code</td>
					<td class=\"header middle\" nowrap align=\"center\">TFA Name</td>
					<td class=\"header middle\" nowrap align=\"center\">SPV Code</td>
					<td class=\"header middle\" nowrap align=\"center\">SPV Name</td>
					<td class=\"header middle\" nowrap align=\"center\">AM Code</td>
					<td class=\"header middle\" nowrap align=\"center\">AM Name</td>
					<td class=\"header middle\" nowrap align=\"center\">Mgr Code</td>
					<td class=\"header middle\" nowrap align=\"center\">Mgr Name</td>
					<td class=\"header middle\" nowrap align=\"center\">Campaign</td>		
				</tr>";
	}
	
	private function write_content()
	{
		// $CampaignInfo = $this->getCampaignInfo();
		//$Result = $this->getCampaignInfo();
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