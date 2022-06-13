<?php
//include(dirname(__FILE__)."/class_export_excel.php");

class report_closing extends index
{

	function report_closing()
	{
		//$this-> excel = new excel();
	}
	
	function write_footer()
	{
		?>
			</table>
			<div>
			</body>
			</html>
		<?php
	}
	
		
/** get group_select **/

	private function get_group_select()
	{
		return explode(",", $this -> escPost('group_select'));
	}	
	
/** get filtering agentid **/
	
	private function get_agent_select()
	{
		return explode(",",$this -> escPost('list_user_tm'));
	}
	
	private function write_header_by_telesales($Parameters='')
	{ 
		echo "<h4><u>{$Parameters -> getUsername()} - {$Parameters -> getFullname()}</u></h4>";
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" >
				<tr>
				  <td class=\"header first\" nowrap>Card Type Desc</td>
				  <td class=\"header middle\" nowrap>Fin Code</td>
				  <td class=\"header middle\" nowrap>Customer Name</td>
				  <td class=\"header middle\" nowrap>Customer DOB</td>
				  <td class=\"header middle\" nowrap>Gender</td>
				  <td class=\"header middle\" nowrap>Customer Address Line 1</td>
				  <td class=\"header middle\" nowrap>Customer Address Line 2</td>
				  <td class=\"header middle\" nowrap>Customer Address Line 3</td>
				  <td class=\"header middle\" nowrap>Customer Address Line 4</td>
				  <td class=\"header middle\" nowrap>CustomerCity</td>
				  <td class=\"header middle\" nowrap>Customer Zip Code</td>
				  <td class=\"header middle\" nowrap>Policy Sales Date</td>
				  <td class=\"header middle\" nowrap>Agent User Name</td>
				  <td class=\"header middle\" nowrap>Agent Full Name</td>
				  <td class=\"header middle\" nowrap>Spv User Name</td>
				  <td class=\"header lasted\" nowrap>Spv Full Name</td>
			</tr> "; 
	}
	
/* super visor ***/

	private function write_header_by_supervisor($Parameters='')
	{ 
		echo "<h4><u>{$Parameters -> getUsername()} - {$Parameters -> getFullname()}</u></h4>";
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" >
				<tr>
				  <td class=\"header first\" nowrap>Card Type Desc</td>
				  <td class=\"header middle\" nowrap>Fin Code</td>
				  <td class=\"header middle\" nowrap>Customer Name</td>
				  <td class=\"header middle\" nowrap>Customer DOB</td>
				  <td class=\"header middle\" nowrap>Gender</td>
				  <td class=\"header middle\" nowrap>Customer Address Line 1</td>
				  <td class=\"header middle\" nowrap>Customer Address Line 2</td>
				  <td class=\"header middle\" nowrap>Customer Address Line 3</td>
				  <td class=\"header middle\" nowrap>Customer Address Line 4</td>
				  <td class=\"header middle\" nowrap>CustomerCity</td>
				  <td class=\"header middle\" nowrap>Customer Zip Code</td>
				  <td class=\"header middle\" nowrap>Policy Sales Date</td>
				  <td class=\"header middle\" nowrap>Agent User Name</td>
				  <td class=\"header middle\" nowrap>Agent Full Name</td>
				  <td class=\"header middle\" nowrap>Spv User Name</td>
				  <td class=\"header lasted\" nowrap>Spv Full Name</td>
			</tr> "; 
	}
	
/* super visor ***/

	private function write_header_by_manager($Parameters='')
	{ 
		echo "<h4><u>{$Parameters -> getUsername()} - {$Parameters -> getFullname()}</u></h4>";
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" >
				<tr>
				  <td class=\"header first\" nowrap>Card Type Desc</td>
				  <td class=\"header middle\" nowrap>Fin Code</td>
				  <td class=\"header middle\" nowrap>Customer Name</td>
				  <td class=\"header middle\" nowrap>Customer DOB</td>
				  <td class=\"header middle\" nowrap>Gender</td>
				  <td class=\"header middle\" nowrap>Customer Address Line 1</td>
				  <td class=\"header middle\" nowrap>Customer Address Line 2</td>
				  <td class=\"header middle\" nowrap>Customer Address Line 3</td>
				  <td class=\"header middle\" nowrap>Customer Address Line 4</td>
				  <td class=\"header middle\" nowrap>CustomerCity</td>
				  <td class=\"header middle\" nowrap>Customer Zip Code</td>
				  <td class=\"header middle\" nowrap>Policy Sales Date</td>
				  <td class=\"header middle\" nowrap>Agent User Name</td>
				  <td class=\"header middle\" nowrap>Agent Full Name</td>
				  <td class=\"header middle\" nowrap>Spv User Name</td>
				  <td class=\"header lasted\" nowrap>Spv Full Name</td>
			</tr> "; 
	}
	
/* super visor ***/

	private function write_header_by_campaign($Parameters='')
	{ 
		echo "<h4><u>{$Parameters -> getUsername()} - {$Parameters -> getFullname()}</u></h4>";
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" >
				<tr>
				  <td class=\"header first\" nowrap>Card Type Desc</td>
				  <td class=\"header middle\" nowrap>Fin Code</td>
				  <td class=\"header middle\" nowrap>Customer Name</td>
				  <td class=\"header middle\" nowrap>Customer DOB</td>
				  <td class=\"header middle\" nowrap>Gender</td>
				  <td class=\"header middle\" nowrap>Customer Address Line 1</td>
				  <td class=\"header middle\" nowrap>Customer Address Line 2</td>
				  <td class=\"header middle\" nowrap>Customer Address Line 3</td>
				  <td class=\"header middle\" nowrap>Customer Address Line 4</td>
				  <td class=\"header middle\" nowrap>CustomerCity</td>
				  <td class=\"header middle\" nowrap>Customer Zip Code</td>
				  <td class=\"header middle\" nowrap>Policy Sales Date</td>
				  <td class=\"header middle\" nowrap>Agent User Name</td>
				  <td class=\"header middle\" nowrap>Agent Full Name</td>
				  <td class=\"header middle\" nowrap>Spv User Name</td>
				  <td class=\"header lasted\" nowrap>Spv Full Name</td>
			</tr> "; 
	}
		
		
/* main content HTML **/
		
	
	function show_content_html()
	{
		mysql::__construct();
		switch($_REQUEST['group_by'])
		{
			case 'campaign' 	: $this -> closing_group_by_campaign(); 	break;
			case 'manager' 		: $this -> closing_group_by_manager(); 		break;
			case 'supervisor'	: $this -> closing_group_by_supervisor(); 	break;
			case 'Telesales'	: $this -> closing_group_by_telesales(); 	break;
			
		}
	}
	
/* closing_group_by_campaign ***/
	
	function closing_group_by_campaign()
	{
		foreach( $this -> get_group_select() as $keys => $CampaignId )
		{	
			$this -> write_header_by_campaign($CampaignId);
			$this -> write_content_by_campaign($CampaignId);
			$this -> write_footer();
		}
	}
	
/* closing_group_by_manager ***/
	
	function closing_group_by_manager()
	{
		foreach( $this -> get_group_select() as $keys => $ManagerId )
		{	
			$this -> write_header_by_manager($this -> Users -> getUsers($ManagerId));
			$this -> write_content_by_manager($this -> Users -> getUsers($ManagerId));
			$this -> write_footer();
		}
	}

/* closing_group_by_supervisor ***/
	
	function closing_group_by_supervisor()
	{
		foreach( $this -> get_group_select() as $keys => $Supervisor )
		{	
			$this -> write_header_by_supervisor($this -> Users -> getUsers($SupervisorId));
			$this -> write_content_by_supervisor($this -> Users -> getUsers($SupervisorId));
			$this -> write_footer();
		}
	
	}

/* closing_group_by_campaign ***/
	
	function closing_group_by_telesales()
	{
		foreach( $this -> get_agent_select() as $keys => $TelesalesId )
		{	
			$this -> write_header_by_telesales( $this -> Users -> getUsers($TelesalesId));
			$this -> write_content_by_telesales($this -> Users -> getUsers($TelesalesId));
			$this -> write_footer();
		}
	}	
	
	
/** cretae d write_content_by_campaign **/
	
	function write_content_by_campaign()
	{
		$sql  =" select b.CustomerCardType,
					b.CustomerNumber, b.CustomerFirstName,
					b.CustomerDOB, c.GenderShortCode,
					b.CustomerAddressLine1,
					b.CustomerAddressLine2,
					b.CustomerAddressLine3,
					b.CustomerAddressLine4,
					b.CustomerCity,
					b.CustomerZipCode,
					d.PolicySalesDate,
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
					
		$qry = $this -> query($sql);				
		foreach($qry -> result_assoc() as $rows )
		{
			echo "<tr >
					  <td class=\"content first\" nowrap>&nbsp;".$rows[CustomerCardType]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerNumber]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerFirstName]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".date('d/m/Y',strtotime($rows[CustomerDOB]))."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[GenderShortCode]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerAddressLine1]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerAddressLine2]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerAddressLine3]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerAddressLine4]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerCity]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerZipCode]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[PolicySalesDate]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[AgentUserName]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[AgentFullName]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[SpvUserName]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[SpvFullName]."</td>
				</tr> ";
		}

		echo "<tr >
					  <td class=\"total first\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
				</tr> ";	
	}
	
	
/** cretae d write_content_by_Manager **/
	function write_content_by_manager()
	{
		$sql  =" select b.CustomerCardType,
					b.CustomerNumber, b.CustomerFirstName,
					b.CustomerDOB, c.GenderShortCode,
					b.CustomerAddressLine1,
					b.CustomerAddressLine2,
					b.CustomerAddressLine3,
					b.CustomerAddressLine4,
					b.CustomerCity,
					b.CustomerZipCode,
					d.PolicySalesDate,
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
					
		$qry = $this -> query($sql);				
		foreach($qry -> result_assoc() as $rows )
		{
			echo "<tr >
					  <td class=\"content first\" nowrap>&nbsp;".$rows[CustomerCardType]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerNumber]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerFirstName]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".date('d/m/Y',strtotime($rows[CustomerDOB]))."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[GenderShortCode]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerAddressLine1]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerAddressLine2]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerAddressLine3]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerAddressLine4]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerCity]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerZipCode]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[PolicySalesDate]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[AgentUserName]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[AgentFullName]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[SpvUserName]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[SpvFullName]."</td>
				</tr> ";
		}

		echo "<tr >
					  <td class=\"total first\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
				</tr> ";	
	}
	
	
		
/** cretae d write_content_by_supervisor **/
	function write_content_by_supervisor()
	{
		$sql  =" select b.CustomerCardType,
					b.CustomerNumber, b.CustomerFirstName,
					b.CustomerDOB, c.GenderShortCode,
					b.CustomerAddressLine1,
					b.CustomerAddressLine2,
					b.CustomerAddressLine3,
					b.CustomerAddressLine4,
					b.CustomerCity,
					b.CustomerZipCode,
					d.PolicySalesDate,
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
					
		$qry = $this -> query($sql);				
		foreach($qry -> result_assoc() as $rows )
		{
			echo "<tr >
					  <td class=\"content first\" nowrap>&nbsp;".$rows[CustomerCardType]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerNumber]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerFirstName]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".date('d/m/Y',strtotime($rows[CustomerDOB]))."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[GenderShortCode]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerAddressLine1]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerAddressLine2]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerAddressLine3]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerAddressLine4]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerCity]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerZipCode]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[PolicySalesDate]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[AgentUserName]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[AgentFullName]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[SpvUserName]."</td>
					  <td class=\"content middle\" nowrap>&nbsp;".$rows[SpvFullName]."</td>
				</tr> ";
		}

		echo "<tr >
					  <td class=\"total first\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
					  <td class=\"total middle\" nowrap>&nbsp;</td>
				</tr> ";	
	}
	
	
/** cretae d write_content_by_telesales **/
function write_content_by_telesales($Teleales)
	{
		$sql  =" select b.CustomerCardType,
						b.CustomerNumber, b.CustomerFirstName,
						b.CustomerDOB, c.GenderShortCode,
						b.CustomerAddressLine1,
						b.CustomerAddressLine2,
						b.CustomerAddressLine3,
						b.CustomerAddressLine4,
						b.CustomerCity,
						b.CustomerZipCode,
						d.PolicySalesDate,
						f.id as AgentUserName,
						f.full_name as AgentFullName,
						g.id as SpvUserName,
						g.full_name as SpvFullName
						from t_gn_policyautogen a left join t_gn_customer b on a.CustomerId=b.CustomerId
						left join t_lk_gender c on b.GenderId=c.GenderId
						left join t_gn_policy d on a.PolicyNumber=d.PolicyNumber
						left join t_gn_assignment e on b.CustomerId=e.CustomerId
						left join tms_agent f on e.AssignSelerId=f.UserId
						left join tms_agent g on e.AssignSpv=g.UserId 
						WHERE f.UserId='".$Teleales-> getUserId()."'";
			
			$qry = $this -> query($sql);				
			foreach($qry -> result_assoc() as $rows )
			{
				echo "<tr >
						  <td class=\"content first\" nowrap>&nbsp;".$rows[CustomerCardType]."</td>
						  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerNumber]."</td>
						  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerFirstName]."</td>
						  <td class=\"content middle\" nowrap>&nbsp;".date('d/m/Y',strtotime($rows[CustomerDOB]))."</td>
						  <td class=\"content middle\" nowrap>&nbsp;".$rows[GenderShortCode]."</td>
						  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerAddressLine1]."</td>
						  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerAddressLine2]."</td>
						  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerAddressLine3]."</td>
						  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerAddressLine4]."</td>
						  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerCity]."</td>
						  <td class=\"content middle\" nowrap>&nbsp;".$rows[CustomerZipCode]."</td>
						  <td class=\"content middle\" nowrap>&nbsp;".$rows[PolicySalesDate]."</td>
						  <td class=\"content middle\" nowrap>&nbsp;".$rows[AgentUserName]."</td>
						  <td class=\"content middle\" nowrap>&nbsp;".$rows[AgentFullName]."</td>
						  <td class=\"content middle\" nowrap>&nbsp;".$rows[SpvUserName]."</td>
						  <td class=\"content middle\" nowrap>&nbsp;".$rows[SpvFullName]."</td>
					</tr> ";
			}

			echo "<tr >
						  <td class=\"total first\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
						  <td class=\"total middle\" nowrap>&nbsp;</td>
					</tr> ";	
	}
	
	
	
}
?>