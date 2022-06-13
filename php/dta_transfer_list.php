<?php

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	
	
	/** create object **/
	
	$ListPages -> pages = $db -> escPost('v_page'); 
	$ListPages -> setPage(15);
	
  /* create Sql Query for show list data **/
  
	
	function getShowFollowup()
	{
		global $db;
		$sql = "select a.CallReasonId from t_lk_callreason a where a.BucketFollowupShow =0 ";
		$qry = $db -> query($sql);
		if( $qry -> result_num_rows() > 0 )
		{
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[$rows['CallReasonId']] = $rows['CallReasonId']; 
			}
		}
		return implode(',',array_keys($datas));
	}
	
	$sql = " select a.CustomerId,
					a.CampaignId,
					a.CustomerNumber,
					a.CustomerFirstName,
					a.CustomerLastName,
					IF(a.CustomerCity is null,'-',a.CustomerCity) 
					as CustomerCity,
					a.CustomerUploadedTs,
					a.CustomerOfficeName,
					c.CampaignNumber,
					d.CallReasonDesc,
					a.CustomerUpdatedTs,
					e.UserId, e.full_name, e.id,
					DATE_FORMAT(a.CustomerDOB,'%Y-%m-%d') AS CustomerDOB, 
					DATE_FORMAT(FROM_DAYS(DATEDIFF(CURRENT_DATE,a.CustomerDOB)),'%y') AS Age,
					IF(a.GenderId=1,'M',IF(a.GenderId=2,'F','-')) as gnd
			FROM t_gn_customer a 
			INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId
			LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId
			LEFT JOIN t_lk_callreason d on a.CallReasonId=d.CallReasonId
			left join tms_agent e on b.AssignSelerId=e.UserId ";
			
	$ListPages -> query($sql);
	
	/* if found filter label **/	
	
	$filter = "";
		
		if( $db->getSession('handling_type')==1){
				
				$filter  = " AND b.AssignMgr is not null
							 AND b.AssignSpv is not null
							 AND b.AssignSelerId is not null
							 AND (d.CallReasonId NOT IN(".getShowFollowup().") OR d.CallReasonId is null)							 
							 AND b.AssignBlock=0 
							 AND c.CampaignStatusFlag=1";
			}		 
			else if( $db->getSession('handling_type')==2){
				$filter  = " AND b.AssignAdmin is not null
							 AND b.AssignMgr='".$db->getSession('UserId')."'
							 AND (d.CallReasonId NOT IN(".getShowFollowup().") OR d.CallReasonId is null)
							 AND b.AssignSpv is not null
							 AND b.AssignSelerId is not null  
							 and b.AssignBlock=0 
							 and c.CampaignStatusFlag=1";
			}
			else if( $db->getSession('handling_type')==3){
				$filter  = " AND b.AssignAdmin is not null
							 AND b.AssignMgr is not null
							 AND b.AssignSpv ='".$db->getSession('UserId')."'
							 AND (d.CallReasonId NOT IN(".getShowFollowup().") OR d.CallReasonId is null)
							 AND b.AssignSelerId is not null 
							 AND b.AssignBlock=0 
							 AND c.CampaignStatusFlag=1";
			}
			
		if( $db->havepost('campaignId')):
			$filter.=" AND a.CampaignId='".$db->escPost('campaignId')."' ";
		endif;
		
		if( $db->havepost('filteruser') ):
			$filter.=" AND b.AssignSelerId='".$db->escPost('filteruser')."' ";
		endif;
		
		if( $db->havepost('callresult') ){
			if ($db->escPost('callresult') != "new")
				$filter.=" AND a.CallReasonId ='".$db->escPost('callresult')."' ";
			else
			$filter.=" AND a.CallReasonId is null ";
		}
		
		
		if( $db->havepost('CustomerId') ):
			$filter.=" AND a.CustomerNumber='".$db->escPost('CustomerId')."' ";
		endif;
		
		if( $db->havepost('CustomerName') ):
			$filter.=" AND a.CustomerFirstName LIKE '%".$db->escPost('CustomerName')."%' ";
		endif;
				
		if( $db->havepost('city') ):
			$filter.=" AND a.CustomerCity LIKE '%".$db->escPost('city')."%' ";
		endif;
		
		if( $db->havepost('age') ):
			$filter.=" AND DATE_FORMAT(FROM_DAYS(DATEDIFF(CURRENT_DATE,a.CustomerDOB)),'%y')  ='".$db->escPost('age')."' ";
		endif;
		
		if( $db->havepost('gender') ):
			$filter.=" AND GenderId  ='".$db->escPost('gender')."' ";
		endif;
		
		
	$ListPages -> setWhere($filter);

 /** set of limit string **/
	
	$ListPages -> setLimit();
	//echo $ListPages ->query;
	$ListPages -> result();
	SetNoCache();

?>

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first">&nbsp;<a href="javascript:void(0);" onclick="doJava.checkedAll('chk_cust_data');">#</a></th>	
		<th nowrap class="custom-grid th-middle" width="4%">&nbsp;No.</th>	
		<th nowrap class="custom-grid th-middle" width="7%">&nbsp;Campaign Id.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Customer Number.</th>        
        <th nowrap class="custom-grid th-middle">&nbsp;Customer Name.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Customer City.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;DOB.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Customer Age.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Gender</th>
		<th nowrap class="custom-grid th-middle">&nbsp;User ID.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Last Call Date.</th>
		<th nowrap class="custom-grid th-lasted">&nbsp;Last Call Result.</th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
	?>
			<tr class="onselect">
				<td class="content-first"><input type="checkbox" value="<?php echo $row -> CustomerId; ?>" name="chk_cust_data" name="chk_cust_data"></td>
				<td class="content-middle"><?php echo $no; ?></td>	
				<td class="content-middle"><?php echo $row -> CampaignNumber; ?></td>
				<td class="content-middle"><?php echo $row -> CustomerNumber; ?></td>
				<td class="content-middle"><?php echo $row -> CustomerFirstName; ?></td>
				<td class="content-middle"><?php echo $row -> CustomerCity; ?></td>
				<td class="content-middle"><?php echo $row -> CustomerDOB; ?></td>
				<td class="content-middle"><?php echo $row -> Age; ?></td>
				<td class="content-middle"><?php echo $row -> gnd; ?></td>
				<td class="content-middle"><?php echo $row -> id.'-'.$row -> full_name; ?></td>
				<td class="content-middle"><?php echo $db->Date->date_time_indonesia($row -> CustomerUpdatedTs); ?></td>
				<td class="content-lasted"><?php echo $row -> CallReasonDesc; ?></td>
				
			</tr>	
			
			
</tbody>
	<?php
		$no++;
		};
	?>
</table>



