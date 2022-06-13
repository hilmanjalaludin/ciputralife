<?php
class report_closing extends IndexExcel
{
	var $excel;
	
	function report_closing()
	{
		$this-> excel = new excel();
	}
	
	
	function show_content_excel()
	{
		$sql  =" select b.CustomerCardType, 
					b.CustomerNumber, 
					b.CustomerFirstName, 
					date_format(b.CustomerDOB,'%d/%m/%Y') as CustomerDOB, 
					c.GenderShortCode,
					b.CustomerAddressLine1,
					b.CustomerAddressLine2,
					b.CustomerAddressLine3,
					b.CustomerAddressLine4,
					b.CustomerCity,
					b.CustomerZipCode,
					date_format(d.PolicySalesDate,'%d/%m/%Y %H:%i%:%s') as PolicySalesDate,
					f.id as AgentUserName,
					f.full_name as AgentFullName,
					g.id as SpvUserName,
					g.full_name as SpvFullName
					from t_gn_policyautogen a left join t_gn_customer b on a.CustomerId=b.CustomerId
					left join t_lk_gender c on b.GenderId=c.GenderId
					left join t_gn_policy d on a.PolicyNumber=d.PolicyNumber
					left join t_gn_assignment e on b.CustomerId=e.CustomerId
					left join tms_agent f on e.AssignSelerId=f.UserId
					left join tms_agent g on e.AssignSpv=g.UserId ";
					
		
				
	/* excel */
		$this-> excel-> xlsWriteHeader('AJMI_REGISTER_REPORT_'.time());
		$xlsRows = 0;
			$this-> excel-> xlsWriteLabel($xlsRows,0,'Card Type Desc');
			$this-> excel-> xlsWriteLabel($xlsRows,1,'Fine Code');
			$this-> excel-> xlsWriteLabel($xlsRows,2,'Customer Name');
			$this-> excel-> xlsWriteLabel($xlsRows,3,'Customer DOB');
			$this-> excel-> xlsWriteLabel($xlsRows,4,'Gender');
			$this-> excel-> xlsWriteLabel($xlsRows,5,'Customer Address Line 1');
			$this-> excel-> xlsWriteLabel($xlsRows,6,'Customer Address Line 2');
			$this-> excel-> xlsWriteLabel($xlsRows,7,'Customer Address Line 3');
			$this-> excel-> xlsWriteLabel($xlsRows,8,'Customer Address Line 4');
			$this-> excel-> xlsWriteLabel($xlsRows,9,'CustomerCity');
			$this-> excel-> xlsWriteLabel($xlsRows,10,'Customer Zip Code');
			$this-> excel-> xlsWriteLabel($xlsRows,11,'Policy Sales Date');
			$this-> excel-> xlsWriteLabel($xlsRows,12,'Agent User Name');
			$this-> excel-> xlsWriteLabel($xlsRows,13,'Agent Full Name');
			$this-> excel-> xlsWriteLabel($xlsRows,14,'Spv User Name');
			$this-> excel-> xlsWriteLabel($xlsRows,15,'Spv Full Name');
		
		$xlsRows = $xlsRows+1;
		$qry = $this -> query($sql);				
		foreach($qry -> result_assoc() as $rows )
		{
			$this-> excel-> xlsWriteLabel($xlsRows,0,$rows['CustomerCardType']);
			$this-> excel-> xlsWriteLabel($xlsRows,1,$rows['CustomerNumber']);
			$this-> excel-> xlsWriteLabel($xlsRows,2,$rows['CustomerFirstName']);
			$this-> excel-> xlsWriteLabel($xlsRows,3,$rows['CustomerDOB']);
			$this-> excel-> xlsWriteLabel($xlsRows,4,$rows['GenderShortCode']);
			$this-> excel-> xlsWriteLabel($xlsRows,5,$rows['CustomerAddressLine1']);
			$this-> excel-> xlsWriteLabel($xlsRows,6,$rows['CustomerAddressLine2']);
			$this-> excel-> xlsWriteLabel($xlsRows,7,$rows['CustomerAddressLine3']);
			$this-> excel-> xlsWriteLabel($xlsRows,8,$rows['CustomerAddressLine4']);
			$this-> excel-> xlsWriteLabel($xlsRows,9,$rows['CustomerCity']);
			$this-> excel-> xlsWriteLabel($xlsRows,10,$rows['CustomerZipCode']);
			$this-> excel-> xlsWriteLabel($xlsRows,11,$rows['PolicySalesDate']);
			$this-> excel-> xlsWriteLabel($xlsRows,12,$rows['AgentUserName']);
			$this-> excel-> xlsWriteLabel($xlsRows,13,$rows['AgentFullName']);
			$this-> excel-> xlsWriteLabel($xlsRows,14,$rows['SpvUserName']);
			$this-> excel-> xlsWriteLabel($xlsRows,15,$rows['SpvFullName']);
			$xlsRows += 1;
		}	
		
		$this-> excel-> xlsClose();
	}
	
}
?>