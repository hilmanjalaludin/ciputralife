<?php 

if(!define('CardClose','10')) define('CardClose','10');

class freepa_detail extends index
{
	private $product_category;
	function freepa_detail()
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
			$sql = "SELECT 
					i.InsuredId as FPAId,
					concat(i.InsuredFirstName,' ',i.InsuredLastName) as customername,
					date_format(i.InsuredDOB, '%d-%m-%Y') as DOB,
					p.PayerEmail as  Email,
					p.PayerMobilePhoneNum as HP,
					p.PayerAddressLine1 as Alamat,
					pr.Province as provinsi,
					(select ans.answer_value from t_gn_multians_survey ans 
						where ans.customer_id=i.CustomerId and ans.quest_have_ans=1 
						and ans.answer_value <> '' and ans.answer_value is not null 
					and ans.insured_id <> 0 limit 1) as Survey,    -- diganti dengan yg ini
					a.init_name as agentcode,
					a.full_name as agentname,
					b.init_name as spvcode,
					b.full_name as spvname,
					e.init_name as amcode,
					e.full_name as amname,
					c.init_name as mgrcode,
					c.full_name as mgrname
				from t_gn_insured i
				inner join t_gn_payer p on p.CustomerId=i.CustomerId
				LEFT join t_lk_province pr on pr.ProvinceId=p.ProvinceId
				left JOIN t_gn_policyautogen d ON i.CustomerId = d.CustomerId
				left join t_gn_product f on d.ProductId = f.ProductId
				INNER JOIN tms_agent a on i.CreatedById=a.UserId
				left join tms_agent b on a.spv_id=b.UserId
				left join tms_agent e on a.manager_id=e.UserId #manager
				LEFT join tms_agent c on a.mgr_id=c.UserId #am
				where f.ProductId=2
				and i.InsuredCreatedTs >= '".$this->start_date." 00:00:00'
				and i.InsuredCreatedTs <= '".$this->end_date." 23:00:00'
					";
		} else if($this->escPost('Option') == 0){
			$sql = "select 
					fp.FPAId, fp.name as customername, date_format(fp.dob, '%d-%m-%Y') as DOB,
					fp.email as  Email, fp.phone as HP, p.PayerAddressLine1 as Alamat, pr.Province as provinsi,

					(select ans.answer_value from t_gn_multians_survey ans 
					where ans.customer_id=fp.CustomerId and ans.quest_have_ans=1 
					and ans.answer_value <> '' and ans.answer_value is not null 
					and ans.insured_id <> 0 limit 1
					) as Survey,                                                             -- diganti dengan yg ini

					a.init_name as agentcode,
					a.full_name as agentname,
					b.init_name as spvcode,
					b.full_name as spvname,
					e.init_name as amcode,
					e.full_name as amname,
					c.init_name as mgrcode,
					c.full_name as mgrname

					from t_gn_fpa_registered fp
					-- left JOIN t_gn_multians_survey ans on ans.customer_id=fp.CustomerId -- ga perlu dijoin lg 
					left join t_gn_payer p on p.CustomerId=fp.CustomerId
					left join t_lk_province pr on pr.ProvinceId=p.ProvinceId
					left join t_gn_assignment ass on ass.CustomerId=fp.CustomerId
					left join tms_agent a on ass.AssignSelerId=a.UserId
					left join tms_agent b on ass.AssignSpv=b.UserId
					left join tms_agent e on ass.AssignMgr=e.UserId #manager
					left join tms_agent c on ass.AssignManager=c.UserId  #am
			where 1=1#ans.quest_have_ans=1
				#and f.ProductId=2
				#and cs.CallReasonId=15
				and fp.register_status=2
				and fp.createdts >= '".$this->start_date." 00:00:00'
				and fp.createdts <= '".$this->end_date." 23:00:00'
					";
		}
		
		if($this->havepost('Agent') != "") {
			$sql .= "  and a.UserId IN (".$this->escPost('Agent').")"; 
		}
		if($this->havepost('Supervisor') != "") {
			$sql .= " and a.spv_id IN (".$this->escPost('Supervisor').")"; 
		}
		if($this->escPost('Option') == 1) {
			$sql .= " group by i.InsuredId;";
		} else if($this->escPost('Option') == 0) {
			$sql .= " group by fp.CustomerId;";
		}
			$qry = $this ->query($sql);
			// echo "<pre>".$sql."</pre>";
			foreach($qry -> result_assoc() as $rows )
			{
				
				$data[$rows['FPAId']]['customername'] = $rows['customername'];
				$data[$rows['FPAId']]['DOB'] = $rows['DOB'];
				$data[$rows['FPAId']]['Email'] = $rows['Email'];
				$data[$rows['FPAId']]['HP'] = $rows['HP'];
				$data[$rows['FPAId']]['Alamat'] = $rows['Alamat'];
				$data[$rows['FPAId']]['provinsi'] = $rows['provinsi'];
				$data[$rows['FPAId']]['Survey'] = $rows['Survey'];
				$data[$rows['FPAId']]['agentcode'] = $rows['agentcode'];
				$data[$rows['FPAId']]['agentname'] = $rows['agentname'];
				$data[$rows['FPAId']]['spvcode'] = $rows['spvcode'];
				$data[$rows['FPAId']]['spvname'] = $rows['spvname'];
				$data[$rows['FPAId']]['amcode'] = $rows['amcode'];
				$data[$rows['FPAId']]['amname'] = $rows['amname'];
				$data[$rows['FPAId']]['mgrcode'] = $rows['mgrcode'];
				$data[$rows['FPAId']]['mgrname'] = $rows['mgrname'];
			}
		return $data;
	}

	private function write_header_closed()
	{
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" >
				<tr>
					<td class=\"header first\" nowrap align=\"center\">No</td>
					<td class=\"header middle\" nowrap align=\"center\">CUSTOMER NAME</td>
					<td class=\"header middle\" nowrap align=\"center\">DOB</td>
					<td class=\"header middle\" nowrap align=\"center\">EMAIL</td>
					<td class=\"header middle\" nowrap align=\"center\">MOBILE NO</td>
					<td class=\"header middle\" nowrap align=\"center\">ALAMAT</td>
					<td class=\"header middle\" nowrap align=\"center\">PROVINSI</td>
					<td class=\"header middle\" nowrap align=\"center\">SURVEY</td>
					<td class=\"header middle\" nowrap align=\"center\">TFA CODE</td>
					<td class=\"header middle\" nowrap align=\"center\">TFA NAME</td>
					<td class=\"header middle\" nowrap align=\"center\">SPV CODE</td>
					<td class=\"header middle\" nowrap align=\"center\">SPV NAME</td>
					<td class=\"header middle\" nowrap align=\"center\">AM CODE</td>
					<td class=\"header middle\" nowrap align=\"center\">AM NAME</td>
					<td class=\"header middle\" nowrap align=\"center\">MGR CODE</td>
					<td class=\"header middle\" nowrap align=\"center\">MGR NAME</td>
				</tr>";
	}
	
	private function write_content()
	{
		// $CampaignInfo = $this->getCampaignInfo();
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
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['customername']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['DOB']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['Email']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['HP']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['Alamat']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['provinsi']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['Survey']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['agentcode']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['agentname']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['spvcode']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['spvname']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['amcode']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['amname']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['mgrcode']."</td>
					<td rowspan=\" ".$rowspan." \" class=\"content middle\" nowrap>".$arr_val['mgrname']."</td>
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