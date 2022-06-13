<?php
class call_tracking extends index
{
	var $group_select;
	
	function call_tracking()
	{
		
	}
	
	
/*** **********************/
	
	function get_catgory_result()
	{
		$sql =" SELECT * FROM t_lk_callreasoncategory a 
				LEFT JOIN t_lk_callreason b on a.CallReasonCategoryId=b.CallReasonCategoryId
				WHERE b.CallReasonDesc is not null ";

		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows )
		{
			$datas[$rows['CallReasonCategoryId']] = $rows;
			
		}
		return $datas;
	}
	
/*** **********************/

	
	function get_count_category($categoryId)
	{
		$sql ="select count(a.CallReasonId) from t_lk_callreason a where a.CallReasonCategoryId='$categoryId'";
		$qry = $this -> query($sql);
		return $qry -> result_singgle_value();
	}
	
/** get campaign namae ***/
	
	function get_campaign_name($CampaignNumber=0)
	{
		$sql = "select a.CampaignName from t_gn_campaign a where a.CampaignNumber='$CampaignNumber'";
		$qry = $this -> query($sql);
		return $qry -> result_singgle_value();
	}
	
	
/** get campaign call reason ***/
	
	function get_call_reason($categoryId)
	{
		$sql ="select * from t_lk_callreason a where a.CallReasonCategoryId='$categoryId'";
		$qry = $this -> query($sql);
		foreach( $qry -> result_assoc() as $rows )
		{
			$datas[$rows['CallReasonId']] = $rows;
			
		}
		return $datas;
	}
	
/** get_size_callreason_by_date_CMP **/

	private function get_size_callreason_by_date_CMP($start_date='', $CallReasonId='',$CampaignNumber='')
	{
		$sql = " SELECT COUNT(a.CustomerId) AS jumlah
				 FROM t_gn_customer a
				 LEFT JOIN t_gn_assignment b ON a.CustomerId=b.CustomerId
				 LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId
				 WHERE DATE(a.CustomerUpdatedTs)='$start_date' 
				 AND c.CampaignNumber ='$CampaignNumber'
				 AND a.CallReasonId = '$CallReasonId'";

		$qry = $this -> query($sql);
		return $qry -> result_singgle_value();		 
	
	}	
	
/** get counter size every status/days **/

	private function get_size_callreason_by_date_AM($start_date='', $CallReasonId='',$ManagerId='')
	{
		$sql = " SELECT COUNT(a.CustomerId) AS jumlah
				 FROM t_gn_customer a
				 LEFT JOIN t_gn_assignment b ON a.CustomerId=b.CustomerId
				 WHERE DATE(a.CustomerUpdatedTs)='$start_date' 
				 AND b.AssignMgr='$ManagerId'
				 AND a.CallReasonId = '$CallReasonId'";

		$qry = $this -> query($sql);
		return $qry -> result_singgle_value();		 
	}
	
/* get_size_callreason_by_date_SPV ***/

	private function get_size_callreason_by_date_SPV($start_date='', $CallReasonId='',$SupervisorId='')
	{
		$sql = " SELECT COUNT(a.CustomerId) AS jumlah
				 FROM t_gn_customer a
				 LEFT JOIN t_gn_assignment b ON a.CustomerId=b.CustomerId
				 WHERE DATE(a.CustomerUpdatedTs)='$start_date' 
				 AND b.AssignSpv='$SupervisorId'
				 AND a.CallReasonId = '$CallReasonId'";

		$qry = $this -> query($sql);
		return $qry -> result_singgle_value();		
	
	}
	
/** get_size_callreason_by_date_TM ***/
	private function get_size_callreason_by_date_TM($start_date='', $CallReasonId='',$TelealesId='')
	{
		$sql = " SELECT COUNT(a.CustomerId) AS jumlah
				 FROM t_gn_customer a
				 LEFT JOIN t_gn_assignment b ON a.CustomerId=b.CustomerId
				 WHERE DATE(a.CustomerUpdatedTs)='$start_date' 
				 AND b.AssignSelerId='$TelealesId'
				 AND a.CallReasonId = '$CallReasonId'";

		$qry = $this -> query($sql);
		return $qry -> result_singgle_value();	
	
	}	
	
/** summary get_summary_size_callreason_by_campaign***/

	private function get_summary_size_callreason_by_campaign($CallReasonId='', $group_select='')
	{
		$start_date = $this -> formatDateEng($_REQUEST['start_date']); 
		$end_date   = $this -> formatDateEng($_REQUEST['end_date']);
		
		$sql = " SELECT count(a.CustomerId) as jumlah FROM t_gn_customer a 
				 LEFT JOIN t_gn_campaign b on a.CampaignId=b.CampaignId
				 WHERE date(a.CustomerUpdatedTs)>='$start_date' 
				 and date(a.CustomerUpdatedTs)<='$end_date' 	
				 and b.CampaignNumber='$group_select'
				 and a.CallReasonId = '$CallReasonId'";
		$tot = $this -> query($sql);
		return $tot -> result_singgle_value();		 
	}	
				 
/** summary get_summary_size_callreason_by_AM***/

	private function get_summary_size_callreason_by_AM($CallReasonId='',$group_select='')
	{
		$start_date = $this -> formatDateEng($_REQUEST['start_date']); 
		$end_date   = $this -> formatDateEng($_REQUEST['end_date']);
		
		$sql = "SELECT COUNT(a.CustomerId) AS jumlah
				FROM t_gn_customer a
				LEFT JOIN t_gn_assignment b ON a.CustomerId=b.CustomerId
				WHERE DATE(a.CustomerUpdatedTs)>='$start_date' 
				AND DATE(a.CustomerUpdatedTs)<='$end_date' 	
				AND b.AssignMgr='$group_select' 
				AND a.CallReasonId = '$CallReasonId' ";

		$tot = $this -> query($sql);
		return $tot -> result_singgle_value();		 
	}	

/** summary get_summary_size_callreason_by_SPV ***/

	private  function get_summary_size_callreason_by_SPV($CallReasonId='',$group_select='')
	{
		$start_date = $this -> formatDateEng($_REQUEST['start_date']); 
		$end_date   = $this -> formatDateEng($_REQUEST['end_date']);
		
		$sql = "SELECT COUNT(a.CustomerId) AS jumlah
				FROM t_gn_customer a
				LEFT JOIN t_gn_assignment b ON a.CustomerId=b.CustomerId
				WHERE DATE(a.CustomerUpdatedTs)>='$start_date' 
				AND DATE(a.CustomerUpdatedTs)<='$end_date' 	
				AND b.AssignSpv='$group_select' 
				AND a.CallReasonId = '$CallReasonId' ";

		$tot = $this -> query($sql);
		return $tot -> result_singgle_value();		 
	}		

/** get_summary_size_callreason_by_TM **/
	
	
	private  function get_summary_size_callreason_by_TM($CallReasonId='',$group_select='', $SupervisorId='')
	{
		$start_date = $this -> formatDateEng($_REQUEST['start_date']); 
		$end_date   = $this -> formatDateEng($_REQUEST['end_date']);
		
		$sql = "SELECT COUNT(a.CustomerId) AS jumlah
				FROM t_gn_customer a
				LEFT JOIN t_gn_assignment b ON a.CustomerId=b.CustomerId
				WHERE DATE(a.CustomerUpdatedTs)>='$start_date' 
				AND DATE(a.CustomerUpdatedTs)<='$end_date' 	
				AND b.AssignSelerId='$group_select' 
				AND b.AssignSpv = '$SupervisorId'
				AND a.CallReasonId = '$CallReasonId' ";

		$tot = $this -> query($sql);
		return $tot -> result_singgle_value();		 
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
	
/** html content ***/
	
	public function show_content_html()
	{
		mysql::__construct();
		switch($_REQUEST['mode'])
		{
			case 'hourly' 	: $this -> hourly_call_tracking(); break;
			case 'daily' 	: $this -> daily_call_tracking(); break;
			case 'summary' 	: $this -> summary_call_tracking(); break;
		}
	}
	
/** hourly **/	
	
	function hourly_call_tracking()
	{
	
		echo "<h1 style=\"color:red;\">Sorry, Report Not Available!</h1>";
		exit(0);
		// mysql::__construct();
		// switch($_REQUEST['group_by'])
		// {
			// case 'campaign' 	: $this -> hourly_group_by_campaign(); 	break;
			// case 'manager' 		: $this -> hourly_group_by_manager(); 	break;
			// case 'supervisor'	: $this -> hourly_group_by_manager(); 	break;
		// }	
	}
	
	
/** daily **/
	
	function daily_call_tracking()
	{
		mysql::__construct();
		switch($_REQUEST['group_by'])
		{
			case 'campaign' 	: $this -> daily_group_by_campaign(); 		break;
			case 'manager' 		: $this -> daily_group_by_manager(); 		break;
			case 'supervisor'	: $this -> daily_group_by_supervisor(); 	break;
			case 'Telesales'	: $this -> daily_group_by_telesales(); 		break;
		}	
	}
	
	
		
/** by group **/
	
	function summary_call_tracking()
	{
		mysql::__construct();
		switch($_REQUEST['group_by'])
		{
			case 'campaign' 	: $this -> summary_group_by_campaign(); 	break;
			case 'manager' 		: $this -> summary_group_by_manager(); 		break;
			case 'supervisor'	: $this -> summary_group_by_supervisor(); 	break;
			case 'Telesales'	: $this -> summary_group_by_telesales(); 	break;
			
		}	
	}
	
/** daily_group_by_telesales ***/

	function daily_group_by_telesales()
	{
		foreach( $this -> get_agent_select() as $k => $TelesalesId )
		{
			$dataSize 	= 0;
			$start_date = $this -> formatDateEng($this -> escPost('start_date')); 
			$end_date   = $this -> formatDateEng($this -> escPost('end_date'));
			$Telesales  = $this -> Users -> getUsers($TelesalesId);
			
			
			echo "<h4> {$Telesales -> getUsername()} - {$Telesales -> getFullname()}</h4>";
			echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\">	
					<tr>
					<td rowspan=2 class=\"header fisrt\">Tanggal</td>
					<td rowspan=2 class=\"header middle\">Data Size</td>
					<td rowspan=2 class=\"header middle\">Data Utilize</td> ";
					foreach($this -> get_catgory_result() as $key => $rows ){
						echo "<td class=\"header middle\" colspan=\"".$this -> get_count_category($rows['CallReasonCategoryId'])."\">".ucwords(strtolower($rows['CallReasonCategoryName']))."</td>";
					}
			echo "	<td colspan=3 class=\"header lasted\">Call Attemp Iniated</td>
					</tr>
					<tr> ";
					foreach($this -> get_catgory_result() as $key => $rows ){
						foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row ){
							echo " <td class=\"header middle\">".ucwords(strtolower($row['CallReasonDesc']))."</td>";	
						}
					}
			echo "  <td class=\"header middle\">Total Call</td>
					<td class=\"header middle\">Connectced</td>
					<td class=\"header lasted\">Not Connected</td>
					</tr>";
			
			
	/*** datasize daily per AM **/
			
			$sql=" SELECT count(a.AssignId) as data_size from t_gn_assignment  a 
				   WHERE a.AssignSelerId = '".$Telesales -> getUserId()."'";
				   
			$qry = $this -> query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$dataSize = $rows['data_size']; 
			}
			
		
		/** utilize data calling atempt **/
		
			$sql = " SELECT DATE(a.start_time) AS tgl, COUNT(a.id) AS TotalCall, 
					 COUNT(DISTINCT a.assign_data) AS UtilizeData, 
					 SUM(IF(a.`status` IN(3004,3005),1,0)) AS CallConnected, 
					 SUM(IF(a.`status` NOT IN(3004,3005),1,0)) AS CallNotConnected
					 FROM cc_call_session a
					 LEFT JOIN t_gn_customer b on a.assign_data=b.CustomerId
					 LEFT JOIN t_gn_assignment c on b.CustomerId=c.CustomerId
					 WHERE c.AssignSelerId ='".$Telesales -> getUserId()."'
					 GROUP BY tgl ";

 
			
			//echo $sql;
			
			$qry = $this -> query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$UtilizeData[$rows['tgl']] = $rows['UtilizeData']; 
				$CallConnected[$rows['tgl']] = $rows['CallConnected']; 
				$CallNotConected[$rows['tgl']] = $rows['CallNotConnected'];
				$TotalCall[$rows['tgl']] = $rows['TotalCall']; 
			}
			/** definer data total iniated call ***/
			
			 $total_utilize_data = 0;
			 $total_call_data = 0;
			 $total_call_not_connected = 0;
			 $total_call_connected = 0;
			 
			while(true)
			{	
				$estart_date = $start_date;
				echo "  <tr>
							<td class=\"content first\" >".$this -> formatDateId($estart_date)."</td>
							<td class=\"content middle\" align=\"right\">".$dataSize."</td>
							<td class=\"content middle\" align=\"right\">".($UtilizeData[$estart_date]?$UtilizeData[$estart_date]:'&nbsp;')."</td>";
							foreach($this -> get_catgory_result() as $key => $rows ){
								foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row )
								{
									$current_size_data = $this -> get_size_callreason_by_date_TM($estart_date, $row['CallReasonId'],$Telesales -> getUserId());
									echo " <td class=\"content middle\" align=\"right\">".($current_size_data?$current_size_data:'&nbsp;')."</td>";	
								}
							}
							
				echo 	"<td class=\"content middle\" align=\"right\">".($TotalCall[$estart_date]?$TotalCall[$estart_date]:'&nbsp;')."</td>
							<td class=\"content middle\" align=\"right\">".($CallConnected[$estart_date]?$CallConnected[$estart_date]:'&nbsp;')."</td>
							<td class=\"content lasted\" align=\"right\">".($CallNotConected[$estart_date]?$CallNotConected[$estart_date]:'&nbsp;')."</td>
						</tr> ";
						
						$total_utilize_data+= $UtilizeData[$estart_date];
						$total_call_data+= $TotalCall[$estart_date];
						$total_call_not_connected+= $CallNotConected[$estart_date];
						$total_call_connected+= $CallConnected[$estart_date];
						
					if( $start_date == $end_date ) break;
						$start_date = $this ->nextdate($start_date);	
			}		
					
			echo " <tr>
						<td class=\"total fisrt\">Grand Total</td>
						<td class=\"total middle\" align=\"right\">{$dataSize}</td>
						<td class=\"total middle\" align=\"right\">{$total_utilize_data}</td>";
						
						/** get summary data ***/
						foreach($this -> get_catgory_result() as $key => $rows ){
							foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row ){
								$current_summary = $this -> get_summary_size_callreason_by_TM($row['CallReasonId'],$Telesales -> getUserId());
									echo " <td class=\"total middle\" align=\"right\">&nbsp;".($current_summary?$current_summary:'0')."</td>";	
							}
						}
			echo "<td class=\"total middle\" align=\"right\">{$total_call_data}</td>
				  <td class=\"total middle\" align=\"right\">{$total_call_connected}</td>
				  <td class=\"total lasted\" align=\"right\">{$total_call_not_connected}</td>
				</tr> 
				</table>";
		}
	}
	
	
	
/** daily_group_by_supervisor ***/

	function daily_group_by_supervisor()
	{
		foreach( $this -> get_group_select() as $k => $SupervisorId )
		{
			$dataSize 	= 0;
			$start_date = $this -> formatDateEng($this -> escPost('start_date')); 
			$end_date   = $this -> formatDateEng($this -> escPost('end_date'));
			$Supervisor = $this -> Users -> getUsers($SupervisorId);
			
			
			echo "<h4> {$Supervisor -> getUsername()} - {$Supervisor -> getFullname()}</h4>";
			echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\">	
					<tr>
					<td rowspan=2 class=\"header fisrt\">Tanggal</td>
					<td rowspan=2 class=\"header middle\">Data Size</td>
					<td rowspan=2 class=\"header middle\">Data Utilize</td> ";
					foreach($this -> get_catgory_result() as $key => $rows ){
						echo "<td class=\"header middle\" colspan=\"".($this -> get_count_category($rows['CallReasonCategoryId'])+0)."\">".ucwords(strtolower($rows['CallReasonCategoryName']))."</td>";
					}
			echo "	<td colspan=3 class=\"header lasted\">Call Attemp Iniated</td>
					</tr>
					
					<tr> ";
					foreach($this -> get_catgory_result() as $key => $rows ){
						foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row ){
							
							echo " <td class=\"header middle\">".ucwords(strtolower($row['CallReasonDesc']))."</td>";
							
						}
					}
			echo "  <td class=\"header middle\">Total Call</td>
					<td class=\"header middle\">Connectced</td>
					<td class=\"header lasted\">Not Connected</td>
					</tr>";
			
			
	/*** datasize daily per SPV **/
			
			$sql=" SELECT COUNT(a.AssignId) as data_size from t_gn_assignment  a 
				   WHERE a.AssignSpv = '".$Supervisor -> getUserId()."'";
				   
			$qry = $this -> query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$dataSize = $rows['data_size']; 
			}
			
		
		/** utilize data calling atempt **/
		
			$sql = " SELECT DATE(a.start_time) AS tgl, COUNT(a.id) AS TotalCall, 
					 COUNT(DISTINCT a.assign_data) AS UtilizeData, 
					 SUM(IF(a.`status` IN(3004,3005),1,0)) AS CallConnected, 
					 SUM(IF(a.`status` NOT IN(3004,3005),1,0)) AS CallNotConnected
					 FROM cc_call_session a
					 LEFT JOIN t_gn_customer b on a.assign_data=b.CustomerId
					 LEFT JOIN t_gn_assignment c on b.CustomerId=c.CustomerId
					 WHERE c.AssignSpv ='".$Supervisor -> getUserId()."'
					 GROUP BY tgl ";
					 
			$qry = $this -> query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$UtilizeData[$rows['tgl']] = $rows['UtilizeData']; 
				$CallConnected[$rows['tgl']] = $rows['CallConnected']; 
				$CallNotConected[$rows['tgl']] = $rows['CallNotConnected'];
				$TotalCall[$rows['tgl']] = $rows['TotalCall']; 
			}
			/** definer data total iniated call ***/
			
			 $total_utilize_data = 0;
			 $total_call_data = 0;
			 $total_call_not_connected = 0;
			 $total_call_connected = 0;
			 
			while(true)
			{	
				$estart_date = $start_date;
				echo "  <tr>
							<td class=\"content first\" >".$this -> formatDateId($estart_date)."</td>
							<td class=\"content middle\" align=\"right\">".$dataSize."</td>
							<td class=\"content middle\" align=\"right\">".($UtilizeData[$estart_date]?$UtilizeData[$estart_date]:'&nbsp;')."</td>";
							foreach($this -> get_catgory_result() as $key => $rows ){
								foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row )
								{
									$current_size_data = $this -> get_size_callreason_by_date_SPV($estart_date, $row['CallReasonId'],$Supervisor -> getUserId());
									echo " <td class=\"content middle\" align=\"right\">".($current_size_data?$current_size_data:'&nbsp;')."</td>";	
									
								}
							}
							
				echo 	"<td class=\"content middle\" align=\"right\">".($TotalCall[$estart_date]?$TotalCall[$estart_date]:'&nbsp;')."</td>
							<td class=\"content middle\" align=\"right\">".($CallConnected[$estart_date]?$CallConnected[$estart_date]:'&nbsp;')."</td>
							<td class=\"content lasted\" align=\"right\">".($CallNotConected[$estart_date]?$CallNotConected[$estart_date]:'&nbsp;')."</td>
						</tr> ";
						
						$total_utilize_data+= $UtilizeData[$estart_date];
						$total_call_data+= $TotalCall[$estart_date];
						$total_call_not_connected+= $CallNotConected[$estart_date];
						$total_call_connected+= $CallConnected[$estart_date];
						
					if( $start_date == $end_date ) break;
						$start_date = $this ->nextdate($start_date);	
			}		
					
			echo " <tr>
						<td class=\"total fisrt\">Grand Total</td>
						<td class=\"total middle\" align=\"right\">{$dataSize}</td>
						<td class=\"total middle\" align=\"right\">{$total_utilize_data}</td>";
						
						/** get summary data ***/
						foreach($this -> get_catgory_result() as $key => $rows ){
							foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row ){
								$current_summary = $this -> get_summary_size_callreason_by_SPV($row['CallReasonId'],$Supervisor -> getUserId());
									echo " <td class=\"total middle\" align=\"right\">&nbsp;".($current_summary?$current_summary:'0')."</td>";	
							}
						}
			echo "<td class=\"total middle\" align=\"right\">{$total_call_data}</td>
				  <td class=\"total middle\" align=\"right\">{$total_call_connected}</td>
				  <td class=\"total lasted\" align=\"right\">{$total_call_not_connected}</td>
				</tr> 
				</table>";
		}
	}
	
/** daily_group_by_manager ***/

	function daily_group_by_manager()
	{
		foreach( $this -> get_group_select() as $k => $ManagerId )
		{
			$dataSize 	= 0;
			$start_date = $this -> formatDateEng($this -> escPost('start_date')); 
			$end_date   = $this -> formatDateEng($this -> escPost('end_date'));
			$Manager 	= $this -> Users -> getUsers($ManagerId);
			
			
			echo "<h4> {$Manager -> getUsername()} - {$Manager -> getFullname()}</h4>";
			echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\">	
					<tr>
					<td rowspan=2 class=\"header fisrt\">Tanggal</td>
					<td rowspan=2 class=\"header middle\">Data Size</td>
					<td rowspan=2 class=\"header middle\">Data Utilize</td> ";
					foreach($this -> get_catgory_result() as $key => $rows ){
						echo "<td class=\"header middle\" colspan=\"".$this -> get_count_category($rows['CallReasonCategoryId'])."\">".ucwords(strtolower($rows['CallReasonCategoryName']))."</td>";
					}
			echo "	<td colspan=3 class=\"header lasted\">Call Attemp Iniated</td>
					</tr>
					<tr> ";
					foreach($this -> get_catgory_result() as $key => $rows ){
						foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row ){
							echo " <td class=\"header middle\">".ucwords(strtolower($row['CallReasonDesc']))."</td>";	
						}
					}
			echo "  <td class=\"header middle\">Total Call</td>
					<td class=\"header middle\">Connectced</td>
					<td class=\"header lasted\">Not Connected</td>
					</tr>";
			
			
	/*** datasize daily per AM **/
			
			$sql=" SELECT count(a.AssignId) as data_size from t_gn_assignment  a 
				   WHERE a.AssignMgr = '".$Manager -> getUserId()."'";
				   
			$qry = $this -> query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$dataSize = $rows['data_size']; 
			}
			
		
		/** utilize data calling atempt **/
		
			$sql = " SELECT DATE(a.start_time) AS tgl, COUNT(a.id) AS TotalCall, 
					 COUNT(DISTINCT a.assign_data) AS UtilizeData, 
					 SUM(IF(a.`status` IN(3004,3005),1,0)) AS CallConnected, 
					 SUM(IF(a.`status` NOT IN(3004,3005),1,0)) AS CallNotConnected
					 FROM cc_call_session a
					 LEFT JOIN t_gn_customer b on a.assign_data=b.CustomerId
					 LEFT JOIN t_gn_assignment c on b.CustomerId=c.CustomerId
					 WHERE c.AssignMgr ='".$Manager -> getUserId()."'
					 GROUP BY tgl ";
					 
			$qry = $this -> query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$UtilizeData[$rows['tgl']] = $rows['UtilizeData']; 
				$CallConnected[$rows['tgl']] = $rows['CallConnected']; 
				$CallNotConected[$rows['tgl']] = $rows['CallNotConnected'];
				$TotalCall[$rows['tgl']] = $rows['TotalCall']; 
			}
			/** definer data total iniated call ***/
			
			 $total_utilize_data = 0;
			 $total_call_data = 0;
			 $total_call_not_connected = 0;
			 $total_call_connected = 0;
			 
			while(true)
			{	
				$estart_date = $start_date;
				echo "  <tr>
							<td class=\"content first\" >".$this -> formatDateId($estart_date)."</td>
							<td class=\"content middle\" align=\"right\">".$dataSize."</td>
							<td class=\"content middle\" align=\"right\">".($UtilizeData[$estart_date]?$UtilizeData[$estart_date]:'&nbsp;')."</td>";
							foreach($this -> get_catgory_result() as $key => $rows ){
								foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row )
								{
									$current_size_data = $this -> get_size_callreason_by_date_AM($estart_date, $row['CallReasonId'],$Manager -> getUserId());
									echo " <td class=\"content middle\" align=\"right\">".($current_size_data?$current_size_data:'&nbsp;')."</td>";	
								}
							}
							
				echo 	"<td class=\"content middle\" align=\"right\">".($TotalCall[$estart_date]?$TotalCall[$estart_date]:'&nbsp;')."</td>
							<td class=\"content middle\" align=\"right\">".($CallConnected[$estart_date]?$CallConnected[$estart_date]:'&nbsp;')."</td>
							<td class=\"content lasted\" align=\"right\">".($CallNotConected[$estart_date]?$CallNotConected[$estart_date]:'&nbsp;')."</td>
						</tr> ";
						
						$total_utilize_data+= $UtilizeData[$estart_date];
						$total_call_data+= $TotalCall[$estart_date];
						$total_call_not_connected+= $CallNotConected[$estart_date];
						$total_call_connected+= $CallConnected[$estart_date];
						
					if( $start_date == $end_date ) break;
						$start_date = $this ->nextdate($start_date);	
			}		
					
			echo " <tr>
						<td class=\"total fisrt\">Grand Total</td>
						<td class=\"total middle\" align=\"right\">{$dataSize}</td>
						<td class=\"total middle\" align=\"right\">{$total_utilize_data}</td>";
						
						/** get summary data ***/
						foreach($this -> get_catgory_result() as $key => $rows ){
							foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row ){
								$current_summary = $this -> get_summary_size_callreason_by_AM($row['CallReasonId'],$Manager -> getUserId());
									echo " <td class=\"total middle\" align=\"right\">&nbsp;".($current_summary?$current_summary:'0')."</td>";	
							}
						}
			echo "<td class=\"total middle\" align=\"right\">{$total_call_data}</td>
				  <td class=\"total middle\" align=\"right\">{$total_call_connected}</td>
				  <td class=\"total lasted\" align=\"right\">{$total_call_not_connected}</td>
				</tr> 
				</table>";
		}
	}
	
	
/** summary_group_by_telesales **/
	
	function summary_group_by_telesales()
	{
		$dataSize 	 = array();
		$start_date  = $this -> formatDateEng($this -> escPost('start_date')); 
		$end_date    = $this -> formatDateEng($this -> escPost('end_date'));
		$Supervior   = $this -> Users -> getUsers($this -> escPost('group_select'));
		
		
		echo "<div style='color:#074e76;font-weight:bold;font-family:Arial;margin-bottom:5px;'>{$Supervior -> getUserName()} - {$Supervior -> getFullname()}</u></div>";
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\">	
					<tr>
					<td rowspan=2 class=\"header fisrt\">Telesales</td>
					<td rowspan=2 class=\"header middle\">Data Size</td>
					<td rowspan=2 class=\"header middle\">Data Utilize</td> ";
					foreach($this -> get_catgory_result() as $key => $rows ){
						echo "<td class=\"header middle\" colspan=\"".$this -> get_count_category($rows['CallReasonCategoryId'])."\">".$rows['CallReasonCategoryName']."</td>";
					}
			echo "	<td colspan=3 class=\"header lasted\">Call Attemp Iniated</td>
					</tr>
					<tr> ";
					foreach($this -> get_catgory_result() as $key => $rows ){
						foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row ){
							echo " <td class=\"header middle\">".$row['CallReasonDesc']."</td>";	
						}
					}
			echo "  <td class=\"header middle\">Total Call</td>
					<td class=\"header middle\">Connectced</td>
					<td class=\"header lasted\">Not Connected</td>
					</tr>";	
					
					
		/* get total datasize per am **/
		
			$sql = "SELECT count(b.CampaignId) as data_size, a.AssignSelerId 
					FROM t_gn_assignment a 
					LEFT JOIN t_gn_customer b on a.CustomerId=b.CustomerId 
					LEFT JOIN t_gn_campaign c on b.CampaignId=c.CampaignId 
					WHERE a.AssignSpv = '".$Supervior->getUserId()."'
					GROUP BY a.AssignSelerId ";
					
			$qry = $this -> query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$dataSize[$rows['AssignSelerId']]+= $rows['data_size']; 
			}		
			
		/** utilize data pertiap AM **/
			
			$sql = " SELECT 
						d.AssignSelerId as TelealesId,	
						COUNT(a.id) AS TotalCall, 
						COUNT(DISTINCT a.assign_data) AS UtilizeData, 
						SUM(IF(a.`status` IN(3004,3005),1,0)) AS CallConnected, 
						SUM(IF(a.`status` NOT IN(3004,3005),1,0)) AS CallNotConnected
						FROM cc_call_session a
						INNER JOIN t_gn_customer b ON a.assign_data=b.CustomerId
						LEFT JOIN t_gn_campaign c ON b.CampaignId=c.CampaignId
						LEFT JOIN t_gn_assignment d on b.CustomerId=d.CustomerId
						WHERE date(a.start_time)>='$start_date'
						AND date(a.start_time)<='$end_date'
						AND d.AssignSpv = '".$Supervior->getUserId()."'
						GROUP BY TelealesId ";
						
			$qry = $this -> query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$UtilizeData[$rows['TelealesId']] += $rows['UtilizeData']; 
				$CallConnected[$rows['TelealesId']] += $rows['CallConnected']; 
				$CallNotConected[$rows['TelealesId']] += $rows['CallNotConnected'];
				$TotalCall[$rows['TelealesId']] += $rows['TotalCall']; 
			}	

		/* definer total in integer  by am summary ***/
		
			$total_utilize_data = 0;
			$total_call_data = 0;
			$total_call_not_connected = 0;
			$total_call_connected = 0;
			$total_data_size = 0; 
			
			foreach( $this -> get_agent_select() as $k => $TelealesId )
			{
				$Telesales= $this -> Users -> getUsers($TelealesId);
				echo " <tr>
						<td class=\"content first\">{$Telesales-> getUserName()} - {$Telesales-> getFullname()}</td>
						<td class=\"content middle\" align=\"right\">".($dataSize[$TelealesId]?$dataSize[$TelealesId]:'-')."</td>
						<td class=\"content middle\" align=\"right\">".($UtilizeData[$TelealesId]?$UtilizeData[$TelealesId]:'-')."</td>";
						foreach($this -> get_catgory_result() as $key => $rows ){
							foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row ){
								$current_size_data[$rows['CallReasonCategoryId']][$row['CallReasonId']] = $this -> get_summary_size_callreason_by_TM($row['CallReasonId'], $TelealesId, $Supervior->getUserId());
								$total_size_reason[$rows['CallReasonCategoryId']][$row['CallReasonId']]+= $this -> get_summary_size_callreason_by_TM($row['CallReasonId'], $TelealesId, $Supervior->getUserId());
								echo " <td class=\"content middle\" align=\"right\">".($current_size_data[$rows['CallReasonCategoryId']][$row['CallReasonId']]?$current_size_data[$rows['CallReasonCategoryId']][$row['CallReasonId']]:'-')."</td>";	
							}
						}
							
				echo 	"<td class=\"content middle\" align=\"right\">".($TotalCall[$TelealesId]?$TotalCall[$TelealesId]:'-')."</td>
							<td class=\"content middle\" align=\"right\">".($CallConnected[$TelealesId]?$CallConnected[$TelealesId]:'-')."</td>
							<td class=\"content lasted\" align=\"right\">".($CallNotConected[$TelealesId]?$CallNotConected[$TelealesId]:'-')."</td>
						</tr> ";
						
						$total_utilize_data+= $UtilizeData[$TelealesId];
						$total_call_data+= $TotalCall[$TelealesId];
						$total_call_not_connected+= $CallNotConected[$TelealesId];
						$total_call_connected+= $CallConnected[$TelealesId];
						$total_data_size += $dataSize[$TelealesId];
			
			}	
			
			
		
		/** start footer table ***********************/
	
			echo " <tr>
					<td class=\"total fisrt\">Grand Total</td>
					<td class=\"total middle\" align=\"right\">{$total_data_size}</td>
					<td class=\"total middle\" align=\"right\">{$total_utilize_data}</td>";
					
					/** get summary data ***/
					foreach($this -> get_catgory_result() as $key => $rows ){
						foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row ){
							$total_rows_reason = $total_size_reason[$rows['CallReasonCategoryId']][$row['CallReasonId']];
							echo " <td class=\"total middle\" align=\"right\">&nbsp;".($total_rows_reason ?$total_rows_reason :'0')."</td>";	
						}
					}
			echo "	<td class=\"total middle\" align=\"right\">{$total_call_data}</td>
					<td class=\"total middle\" align=\"right\">{$total_call_connected}</td>
					<td class=\"total lasted\" align=\"right\">{$total_call_not_connected}</td>
				  </tr> 
				  </table>";
	
	}
	
/** summary_group_by_SPV **/

	function summary_group_by_supervisor()
	{
		$dataSize = array();
		$start_date = $this -> formatDateEng($_REQUEST['start_date']); 
		$end_date   = $this -> formatDateEng($_REQUEST['end_date']);
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\">	
					<tr>
					<td rowspan=2 class=\"header fisrt\">Supervisor</td>
					<td rowspan=2 class=\"header middle\">Data Size</td>
					<td rowspan=2 class=\"header middle\">Data Utilize</td> ";
					foreach($this -> get_catgory_result() as $key => $rows ){
						echo "<td class=\"header middle\" colspan=\"".$this -> get_count_category($rows['CallReasonCategoryId'])."\">".$rows['CallReasonCategoryName']."</td>";
					}
			echo "	<td colspan=3 class=\"header lasted\">Call Attemp Iniated</td>
					</tr>
					<tr> ";
					foreach($this -> get_catgory_result() as $key => $rows ){
						foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row ){
							echo " <td class=\"header middle\">".$row['CallReasonDesc']."</td>";	
						}
					}
			echo "  <td class=\"header middle\">Total Call</td>
					<td class=\"header middle\">Connectced</td>
					<td class=\"header lasted\">Not Connected</td>
					</tr>";	
					
					
		/* get total datasize per am **/
		
			$sql = "SELECT count(b.CampaignId) as data_size, a.AssignSpv 
					FROM t_gn_assignment a 
					LEFT JOIN t_gn_customer b on a.CustomerId=b.CustomerId 
					LEFT JOIN t_gn_campaign c on b.CampaignId=c.CampaignId 
					GROUP BY a.AssignSpv ";
					
			$qry = $this -> query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$dataSize[$rows['AssignSpv']]+= $rows['data_size']; 
			}		
			
		/** utilize data pertiap AM **/
			
			$sql = " SELECT 
						d.AssignSpv as SupervisorId,	
						COUNT(a.id) AS TotalCall, 
						COUNT(DISTINCT a.assign_data) AS UtilizeData, 
						SUM(IF(a.`status` IN(3004,3005),1,0)) AS CallConnected, 
						SUM(IF(a.`status` NOT IN(3004,3005),1,0)) AS CallNotConnected
						FROM cc_call_session a
						INNER JOIN t_gn_customer b ON a.assign_data=b.CustomerId
						LEFT JOIN t_gn_campaign c ON b.CampaignId=c.CampaignId
						LEFT JOIN t_gn_assignment d on b.CustomerId=d.CustomerId
						WHERE date(a.start_time)>='$start_date'
						AND date(a.start_time)<='$end_date'
						GROUP BY SupervisorId ";
						
			$qry = $this -> query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$UtilizeData[$rows['SupervisorId']] += $rows['UtilizeData']; 
				$CallConnected[$rows['SupervisorId']] += $rows['CallConnected']; 
				$CallNotConected[$rows['SupervisorId']] += $rows['CallNotConnected'];
				$TotalCall[$rows['SupervisorId']] += $rows['TotalCall']; 
			}	

		/* definer total in integer  by am summary ***/
		
			$total_utilize_data = 0;
			$total_call_data = 0;
			$total_call_not_connected = 0;
			$total_call_connected = 0;
			$total_data_size = 0; 
			
			foreach( $this -> get_group_select() as $k => $SupervisorId )
			{
				$Supervisor= $this -> Users -> getUsers($SupervisorId);
				echo " <tr>
						<td class=\"content first\">{$Supervisor-> getUserName()} - {$Supervisor-> getFullname()}</td>
						<td class=\"content middle\" align=\"right\">".($dataSize[$SupervisorId]?$dataSize[$SupervisorId]:'-')."</td>
						<td class=\"content middle\" align=\"right\">".($UtilizeData[$SupervisorId]?$UtilizeData[$SupervisorId]:'-')."</td>";
						foreach($this -> get_catgory_result() as $key => $rows ){
							foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row ){
								$current_size_data[$rows['CallReasonCategoryId']][$row['CallReasonId']] = $this -> get_summary_size_callreason_by_SPV($row['CallReasonId'],$SupervisorId);
								$total_size_reason[$rows['CallReasonCategoryId']][$row['CallReasonId']]+= $this -> get_summary_size_callreason_by_SPV($row['CallReasonId'],$SupervisorId);
								echo " <td class=\"content middle\" align=\"right\">".($current_size_data[$rows['CallReasonCategoryId']][$row['CallReasonId']]?$current_size_data[$rows['CallReasonCategoryId']][$row['CallReasonId']]:'-')."</td>";	
							}
						}
							
				echo 	"<td class=\"content middle\" align=\"right\">".($TotalCall[$SupervisorId]?$TotalCall[$SupervisorId]:'-')."</td>
							<td class=\"content middle\" align=\"right\">".($CallConnected[$SupervisorId]?$CallConnected[$SupervisorId]:'-')."</td>
							<td class=\"content lasted\" align=\"right\">".($CallNotConected[$SupervisorId]?$CallNotConected[$SupervisorId]:'-')."</td>
						</tr> ";
						
						$total_utilize_data+= $UtilizeData[$SupervisorId];
						$total_call_data+= $TotalCall[$SupervisorId];
						$total_call_not_connected+= $CallNotConected[$SupervisorId];
						$total_call_connected+= $CallConnected[$SupervisorId];
						$total_data_size += $dataSize[$SupervisorId];
			
			}	
			
			
		
		/** start footer table ***********************/
	
			echo " <tr>
					<td class=\"total fisrt\">Grand Total</td>
					<td class=\"total middle\" align=\"right\">{$total_data_size}</td>
					<td class=\"total middle\" align=\"right\">{$total_utilize_data}</td>";
					
					/** get summary data ***/
					foreach($this -> get_catgory_result() as $key => $rows ){
						foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row ){
							$total_rows_reason = $total_size_reason[$rows['CallReasonCategoryId']][$row['CallReasonId']];
							echo " <td class=\"total middle\" align=\"right\">&nbsp;".($total_rows_reason ?$total_rows_reason :'0')."</td>";	
						}
					}
			echo "	<td class=\"total middle\" align=\"right\">{$total_call_data}</td>
					<td class=\"total middle\" align=\"right\">{$total_call_connected}</td>
					<td class=\"total lasted\" align=\"right\">{$total_call_not_connected}</td>
				  </tr> 
				  </table>";
	}	
	
/** summary_group_by_manager **/
	
	function summary_group_by_manager()
	{
		
		$dataSize = array();
		$start_date = $this -> formatDateEng($_REQUEST['start_date']); 
		$end_date   = $this -> formatDateEng($_REQUEST['end_date']);
		echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\">	
					<tr>
					<td rowspan=2 class=\"header fisrt\">Account Manager</td>
					<td rowspan=2 class=\"header middle\">Data Size</td>
					<td rowspan=2 class=\"header middle\">Data Utilize</td> ";
					foreach($this -> get_catgory_result() as $key => $rows ){
						echo "<td class=\"header middle\" colspan=\"".$this -> get_count_category($rows['CallReasonCategoryId'])."\">".$rows['CallReasonCategoryName']."</td>";
					}
			echo "	<td colspan=3 class=\"header lasted\">Call Attemp Iniated</td>
					</tr>
					<tr> ";
					foreach($this -> get_catgory_result() as $key => $rows ){
						foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row ){
							echo " <td class=\"header middle\">".$row['CallReasonDesc']."</td>";	
						}
					}
			echo "  <td class=\"header middle\">Total Call</td>
					<td class=\"header middle\">Connectced</td>
					<td class=\"header lasted\">Not Connected</td>
					</tr>";	
					
					
		/* get total datasize per am **/
		
			$sql = "SELECT count(b.CampaignId) as data_size, a.AssignMgr 
					FROM t_gn_assignment a 
					LEFT JOIN t_gn_customer b on a.CustomerId=b.CustomerId 
					LEFT JOIN t_gn_campaign c on b.CampaignId=c.CampaignId 
					GROUP BY a.AssignMgr ";
					
			$qry = $this -> query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$dataSize[$rows['AssignMgr']]+= $rows['data_size']; 
			}		
			
		/** utilize data pertiap AM **/
			
			$sql = " SELECT 
						d.AssignMgr as ManagerId,	
						COUNT(a.id) AS TotalCall, 
						COUNT(DISTINCT a.assign_data) AS UtilizeData, 
						SUM(IF(a.`status` IN(3004,3005),1,0)) AS CallConnected, 
						SUM(IF(a.`status` NOT IN(3004,3005),1,0)) AS CallNotConnected
						FROM cc_call_session a
						INNER JOIN t_gn_customer b ON a.assign_data=b.CustomerId
						LEFT JOIN t_gn_campaign c ON b.CampaignId=c.CampaignId
						LEFT JOIN t_gn_assignment d on b.CustomerId=d.CustomerId
						WHERE date(a.start_time)>='$start_date'
						AND date(a.start_time)<='$end_date'
						GROUP BY ManagerId ";
						
			$qry = $this -> query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$UtilizeData[$rows['ManagerId']] += $rows['UtilizeData']; 
				$CallConnected[$rows['ManagerId']] += $rows['CallConnected']; 
				$CallNotConected[$rows['ManagerId']] += $rows['CallNotConnected'];
				$TotalCall[$rows['ManagerId']] += $rows['TotalCall']; 
			}	

		/* definer total in integer  by am summary ***/
		
			$total_utilize_data = 0;
			$total_call_data = 0;
			$total_call_not_connected = 0;
			$total_call_connected = 0;
			$total_data_size = 0; 
			
			foreach( $this -> get_group_select() as $k => $ManagerId )
			{
				$Manager = $this -> Users -> getUsers($ManagerId);
				echo " <tr>
						<td class=\"content first\">{$Manager-> getUserName()} - {$Manager-> getFullname()}</td>
						<td class=\"content middle\" align=\"right\">".($dataSize[$ManagerId]?$dataSize[$ManagerId]:'-')."</td>
						<td class=\"content middle\" align=\"right\">".($UtilizeData[$ManagerId]?$UtilizeData[$ManagerId]:'-')."</td>";
						foreach($this -> get_catgory_result() as $key => $rows ){
							foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row ){
								$current_size_data[$rows['CallReasonCategoryId']][$row['CallReasonId']] = $this -> get_summary_size_callreason_by_AM($row['CallReasonId'],$ManagerId);
								$total_size_reason[$rows['CallReasonCategoryId']][$row['CallReasonId']]+= $this -> get_summary_size_callreason_by_AM($row['CallReasonId'],$ManagerId);
								echo " <td class=\"content middle\" align=\"right\">".($current_size_data[$rows['CallReasonCategoryId']][$row['CallReasonId']]?$current_size_data[$rows['CallReasonCategoryId']][$row['CallReasonId']]:'-')."</td>";	
							}
						}
							
				echo 	"<td class=\"content middle\" align=\"right\">".($TotalCall[$ManagerId]?$TotalCall[$ManagerId]:'-')."</td>
							<td class=\"content middle\" align=\"right\">".($CallConnected[$ManagerId]?$CallConnected[$ManagerId]:'-')."</td>
							<td class=\"content lasted\" align=\"right\">".($CallNotConected[$ManagerId]?$CallNotConected[$ManagerId]:'-')."</td>
						</tr> ";
						
						$total_utilize_data+= $UtilizeData[$ManagerId];
						$total_call_data+= $TotalCall[$ManagerId];
						$total_call_not_connected+= $CallNotConected[$ManagerId];
						$total_call_connected+= $CallConnected[$ManagerId];
						$total_data_size += $dataSize[$ManagerId];
			
			}	
			
			
		
		/** start footer table ***********************/
	
			echo " <tr>
					<td class=\"total fisrt\">Grand Total</td>
					<td class=\"total middle\" align=\"right\">{$total_data_size}</td>
					<td class=\"total middle\" align=\"right\">{$total_utilize_data}</td>";
					
					/** get summary data ***/
					foreach($this -> get_catgory_result() as $key => $rows ){
						foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row ){
							$total_rows_reason = $total_size_reason[$rows['CallReasonCategoryId']][$row['CallReasonId']];
							echo " <td class=\"total middle\" align=\"right\">&nbsp;".($total_rows_reason ?$total_rows_reason :'0')."</td>";	
						}
					}
			echo "	<td class=\"total middle\" align=\"right\">{$total_call_data}</td>
					<td class=\"total middle\" align=\"right\">{$total_call_connected}</td>
					<td class=\"total lasted\" align=\"right\">{$total_call_not_connected}</td>
				  </tr> 
				  </table>";
	}	
	
/** get ccc ***/
	
	function summary_group_by_campaign()
	{
			$dataSize = array();
			$start_date = $this -> formatDateEng($_REQUEST['start_date']); 
			$end_date   = $this -> formatDateEng($_REQUEST['end_date']);
			
			echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\">	
					<tr>
					<td rowspan=2 class=\"header fisrt\">Campaign</td>
					<td rowspan=2 class=\"header middle\">Data Size</td>
					<td rowspan=2 class=\"header middle\">Data Utilize</td> ";
					foreach($this -> get_catgory_result() as $key => $rows ){
						echo "<td class=\"header middle\" colspan=\"".$this -> get_count_category($rows['CallReasonCategoryId'])."\">".$rows['CallReasonCategoryName']."</td>";
					}
			echo "	<td colspan=3 class=\"header lasted\">Call Attemp Iniated</td>
					</tr>
					<tr> ";
					foreach($this -> get_catgory_result() as $key => $rows ){
						foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row ){
							echo " <td class=\"header middle\">".$row['CallReasonDesc']."</td>";	
						}
					}
			echo "  <td class=\"header middle\">Total Call</td>
					<td class=\"header middle\">Connectced</td>
					<td class=\"header lasted\">Not Connected</td>
					</tr>";
		
		/* datasize **/
		
			$sql = "SELECT count(b.CampaignId) as data_size, c.CampaignNumber 
				    FROM t_gn_assignment a 
					LEFT JOIN t_gn_customer b on a.CustomerId=b.CustomerId 
					LEFT JOIN t_gn_campaign c on b.CampaignId=c.CampaignId 
					GROUP BY c.CampaignNumber ";
					
			$qry = $this -> query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$dataSize[$rows['CampaignNumber']]+= $rows['data_size']; 
			}
			
			
		/** utilize data **/
		
			$sql = " SELECT c.CampaignNumber as CampaignNumber, 
					 COUNT(a.id) AS TotalCall, 
					 COUNT(DISTINCT a.assign_data) AS UtilizeData, 
					 SUM(IF(a.`status` IN(3004,3005),1,0)) AS CallConnected, 
					 SUM(IF(a.`status` NOT IN(3004,3005),1,0)) AS CallNotConnected
					 FROM cc_call_session a
					 INNER JOIN t_gn_customer b ON a.assign_data=b.CustomerId
					 LEFT JOIN t_gn_campaign c ON b.CampaignId=c.CampaignId
					 WHERE date(a.start_time)>='$start_date'
					 AND date(a.start_time)<='$end_date'
					 GROUP BY CampaignNumber ";
					 
			$qry = $this -> query($sql);
			foreach($qry -> result_assoc() as $rows )
			{
				$UtilizeData[$rows['CampaignNumber']] += $rows['UtilizeData']; 
				$CallConnected[$rows['CampaignNumber']] += $rows['CallConnected']; 
				$CallNotConected[$rows['CampaignNumber']] += $rows['CallNotConnected'];
				$TotalCall[$rows['CampaignNumber']] += $rows['TotalCall']; 
			}		
						
			
			$total_utilize_data = 0;
			$total_call_data = 0;
			$total_call_not_connected = 0;
			$total_call_connected = 0;
			$total_data_size = 0; 
			foreach( $this -> get_group_select() as $k => $CamapignNumber )
			{
				echo " <tr>
						<td class=\"content first\" >".$this -> get_campaign_name($CamapignNumber)."</td>
						<td class=\"content middle\" align=\"right\">".($dataSize[$CamapignNumber]?$dataSize[$CamapignNumber]:'-')."</td>
						<td class=\"content middle\" align=\"right\">".($UtilizeData[$CamapignNumber]?$UtilizeData[$CamapignNumber]:'-')."</td>";
						foreach($this -> get_catgory_result() as $key => $rows ){
							foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row ){
								$current_size_data[$rows['CallReasonCategoryId']][$row['CallReasonId']] = $this -> get_summary_size_callreason_by_campaign($row['CallReasonId'],$CamapignNumber);
								$total_size_reason[$rows['CallReasonCategoryId']][$row['CallReasonId']]+= $this -> get_summary_size_callreason_by_campaign($row['CallReasonId'],$CamapignNumber);
								echo " <td class=\"content middle\" align=\"right\">".($current_size_data[$rows['CallReasonCategoryId']][$row['CallReasonId']]?$current_size_data[$rows['CallReasonCategoryId']][$row['CallReasonId']]:'-')."</td>";	
							}
						}
							
				echo 	"<td class=\"content middle\" align=\"right\">".($TotalCall[$CamapignNumber]?$TotalCall[$CamapignNumber]:'-')."</td>
							<td class=\"content middle\" align=\"right\">".($CallConnected[$CamapignNumber]?$CallConnected[$CamapignNumber]:'-')."</td>
							<td class=\"content lasted\" align=\"right\">".($CallNotConected[$CamapignNumber]?$CallNotConected[$CamapignNumber]:'-')."</td>
						</tr> ";
						
						$total_utilize_data+= $UtilizeData[$CamapignNumber];
						$total_call_data+= $TotalCall[$CamapignNumber];
						$total_call_not_connected+= $CallNotConected[$CamapignNumber];
						$total_call_connected+= $CallConnected[$CamapignNumber];
						$total_data_size += $dataSize[$CamapignNumber];
			
			}	
			
			//print_r($total_size_reason);

	
		/** start footer table ***********************/
	
			echo " <tr>
					<td class=\"total fisrt\">Grand Total</td>
					<td class=\"total middle\" align=\"right\">{$total_data_size}</td>
					<td class=\"total middle\" align=\"right\">{$total_utilize_data}</td>";
					
					/** get summary data ***/
					foreach($this -> get_catgory_result() as $key => $rows ){
						foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row ){
							$total_rows_reason = $total_size_reason[$rows['CallReasonCategoryId']][$row['CallReasonId']];
							echo " <td class=\"total middle\" align=\"right\">&nbsp;".($total_rows_reason ?$total_rows_reason :'0')."</td>";	
						}
					}
			echo "	<td class=\"total middle\" align=\"right\">{$total_call_data}</td>
					<td class=\"total middle\" align=\"right\">{$total_call_connected}</td>
					<td class=\"total lasted\" align=\"right\">{$total_call_not_connected}</td>
				  </tr> 
				  </table>";
	
	}
	
/*** daliy by campaign ***/
	
	function daily_group_by_campaign()
	{
		foreach( $this -> get_group_select() as $k => $group_select )
		{
			echo "<h4>Campaign <u>{$this -> get_campaign_name($group_select)} </u></h4>";
			
			$dataSize =0;
			$start_date = $this -> formatDateEng($_REQUEST['start_date']); 
			$end_date   = $this -> formatDateEng($_REQUEST['end_date']);
			
		/** define array ***/
		
			$UtilizeData= array();
			$CallConnected= array(); 
			$CallNotConected= array();
			$TotalCall= array(); 
			
			
			echo "<table class=\"grid\" cellpadding=\"0\" cellspacing=\"0\" width=\"90%\">	
					<tr>
					<td rowspan=2 class=\"header fisrt\">Tanggal</td>
					<td rowspan=2 class=\"header middle\">Data Size</td>
					<td rowspan=2 class=\"header middle\">Data Utilize</td> ";
					foreach($this -> get_catgory_result() as $key => $rows ){
						echo "<td class=\"header middle\" colspan=\"".$this -> get_count_category($rows['CallReasonCategoryId'])."\">".$rows['CallReasonCategoryName']."</td>";
					}
			echo "	<td colspan=3 class=\"header lasted\">Call Attemp Iniated</td>
					</tr>
					<tr> ";
					foreach($this -> get_catgory_result() as $key => $rows ){
						foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row ){
							echo " <td class=\"header middle\">".$row['CallReasonDesc']."</td>";	
						}
					}
			echo "  <td class=\"header middle\">Total Call</td>
					<td class=\"header middle\">Connectced</td>
					<td class=\"header lasted\">Not Connected</td>
					</tr>";
					
				
			/* datasize **/
				
				$sql = " SELECT count(a.AssignId) as data_size from t_gn_assignment  a 
						 left join t_gn_customer b on a.CustomerId=b.CustomerId
						 left join t_gn_campaign c on b.CampaignId=c.CampaignId 
						 WHERE c.CampaignNumber ='$group_select'";
						 
				$qry = $this -> query($sql);
				foreach($qry -> result_assoc() as $rows )
				{
					$dataSize += $rows['data_size']; 
				}

			
			/** utilize data **/
			
				$sql = "SELECT 
						DATE(a.start_time) as tgl,
						COUNT(a.id) as TotalCall,
						COUNT(distinct a.assign_data) as UtilizeData,
						SUM(IF(a.`status` IN(3004,3005),1,0)) as CallConnected,
						SUM(IF(a.`status` NOT IN(3004,3005),1,0)) as CallNotConnected
						from cc_call_session a 
						INNER JOIN t_gn_customer b on a.assign_data=b.CustomerId
						left join t_gn_campaign c on b.CampaignId=c.CampaignId
						WHERE c.CampaignNumber ='$group_select'
						group by tgl  ";
						
				$qry = $this -> query($sql);
				foreach($qry -> result_assoc() as $rows )
				{
					$UtilizeData[$rows['tgl']] += $rows['UtilizeData']; 
					$CallConnected[$rows['tgl']] += $rows['CallConnected']; 
					$CallNotConected[$rows['tgl']] += $rows['CallNotConnected'];
					$TotalCall[$rows['tgl']] += $rows['TotalCall']; 
				}		
						
		  /** definer data total iniated call ***/
			
			 $total_utilize_data = 0;
			 $total_call_data = 0;
			 $total_call_not_connected = 0;
			 $total_call_connected = 0;
			
			while(true)
			{	
				$estart_date = $start_date;
				echo "  <tr>
							<td class=\"content first\" >".$this -> formatDateId($estart_date)."</td>
							<td class=\"content middle\" align=\"right\">".$dataSize."</td>
							<td class=\"content middle\" align=\"right\">".($UtilizeData[$estart_date]?$UtilizeData[$estart_date]:'&nbsp;')."</td>";
							foreach($this -> get_catgory_result() as $key => $rows ){
								foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row )
								{
									$current_size_data = $this -> get_size_callreason_by_date_CMP($estart_date,$row['CallReasonId'],$group_select);
									echo " <td class=\"content middle\" align=\"right\">".($current_size_data?$current_size_data:'&nbsp;')."</td>";	
								}
							}
							
				echo 	"<td class=\"content middle\" align=\"right\">".($TotalCall[$estart_date]?$TotalCall[$estart_date]:'&nbsp;')."</td>
							<td class=\"content middle\" align=\"right\">".($CallConnected[$estart_date]?$CallConnected[$estart_date]:'&nbsp;')."</td>
							<td class=\"content lasted\" align=\"right\">".($CallNotConected[$estart_date]?$CallNotConected[$estart_date]:'&nbsp;')."</td>
						</tr> ";
						
						$total_utilize_data+= $UtilizeData[$estart_date];
						$total_call_data+= $TotalCall[$estart_date];
						$total_call_not_connected+= $CallNotConected[$estart_date];
						$total_call_connected+= $CallConnected[$estart_date];
						
					if( $start_date == $end_date ) break;
						$start_date = $this ->nextdate($start_date);	
			}		
					
			echo " <tr>
						<td class=\"total fisrt\">Grand Total</td>
						<td class=\"total middle\" align=\"right\">{$dataSize}</td>
						<td class=\"total middle\" align=\"right\">{$total_utilize_data}</td>";
						
						/** get summary data ***/
						foreach($this -> get_catgory_result() as $key => $rows ){
							foreach($this -> get_call_reason($rows['CallReasonCategoryId']) as $k => $row ){
								$current_summary = $this -> get_summary_size_callreason_by_campaign($row['CallReasonId'], $group_select);
									echo " <td class=\"total middle\" align=\"right\">&nbsp;".($current_summary?$current_summary:'0')."</td>";	
							}
						}
				echo "<td class=\"total middle\" align=\"right\">{$total_call_data}</td>
					  <td class=\"total middle\" align=\"right\">{$total_call_connected}</td>
					  <td class=\"total lasted\" align=\"right\">{$total_call_not_connected}</td>
					</tr> 
					</table>";
		}			
	
	}
	
}
?>