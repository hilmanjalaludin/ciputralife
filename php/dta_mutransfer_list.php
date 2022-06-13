<?

	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	
	function returnArray($datas=''){
		$i_a = explode(',',$datas);
		return implode("','",$i_a);
	}
	
	
	function statusInAgent(){
		global $db;
		$sql = " select a.CallReasonId from t_lk_callreason a 
					left join t_lk_callreasoncategory b
					on a.CallReasonCategoryId=b.CallReasonCategoryId
					where a.CallReasonCategoryId IN (2,5,6)";
		$qry = $db->execute($sql,__FILE__,__LINE__);
		while($row = $db->fetcharray($qry)){
			if( $row[0]!=1){
				$datas[] = $row[0];
			}
		}
		$datas = implode(",",$datas);
		return $datas;
	}	
	
	/** create object **/
	
	$ListPages -> pages = $db -> escPost('v_page'); 
	$ListPages -> setPage(15);
	
  /* create Sql Query for show list data **/
  
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
					e.UserId, e.full_name, e.id
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
						  AND ( a.CallReasonId NOT IN (".statusInAgent().") OR a.CallReasonId is null)
						 AND b.AssignBlock=0 
						 AND c.CampaignStatusFlag=1";			 
		}
		else if( $db->getSession('handling_type')==6){
			$filter  = " AND b.AssignAdmin='".$db->getSession('UserId')."'
						 AND b.AssignMgr is not null
						 AND b.AssignSpv is not null
						 AND b.AssignSelerId is not null
						 AND ( a.CallReasonId NOT IN (".statusInAgent().") OR a.CallReasonId is null)
						 AND b.AssignBlock=0 
						 AND c.CampaignStatusFlag=1 ";
		}		 	
		else if( $db->getSession('handling_type')==2){
			$filter  = " AND b.AssignAdmin is not null
						 AND b.AssignMgr='".$db->getSession('UserId')."'
						 AND b.AssignSpv is not null
						 AND b.AssignSelerId is not null  
						 AND ( a.CallReasonId NOT IN (".statusInAgent().") OR a.CallReasonId is null)
						 AND b.AssignBlock=0 
						 AND c.CampaignStatusFlag=1";
		}
		else if( $db->getSession('handling_type')==3){
			$filter  = " AND b.AssignAdmin is not null
						 AND b.AssignMgr is not null
						 AND b.AssignSpv ='".$db->getSession('UserId')."'
						 AND b.AssignSelerId is not null 
						  AND ( a.CallReasonId NOT IN (".statusInAgent().") OR a.CallReasonId is null)
						 AND b.AssignBlock=0 
						 AND c.CampaignStatusFlag=1";
		}
			
		if( $db->havepost('campaign_list_id') ):
			$filter.=" AND a.CampaignId IN('".returnArray($db->escPost('campaign_list_id'))."')";
		endif;
		
		if( $db -> havepost('campaign_onagent_id') ):
			if( $db -> getSession('handling_type')==1) $filter.=" AND b.AssignMgr IN('".returnArray($db->escPost('campaign_onagent_id'))."')";
			if( $db -> getSession('handling_type')==6) $filter.=" AND b.AssignMgr IN('".returnArray($db->escPost('campaign_onagent_id'))."')";
			if( $db -> getSession('handling_type')==2) $filter.=" AND b.AssignSpv IN('".returnArray($db->escPost('campaign_onagent_id'))."')";
			if( $db -> getSession('handling_type')==3) $filter.=" AND b.AssignSelerId IN('".returnArray($db->escPost('campaign_onagent_id'))."')";
		endif;
		
		
		if( $db->havepost('campaign_result_id') ):
			$status = explode(',', $_REQUEST['campaign_result_id']);
			if(in_array(1,$status)) { $filter.=" AND ( a.CallReasonId IN('".returnArray($db->escPost('campaign_result_id'))."') OR a.CallReasonId is null ) "; }
			else {
				$filter.=" AND a.CallReasonId IN('".returnArray($db->escPost('campaign_result_id'))."')";
			}
		endif;
		
		
	$ListPages -> setWhere($filter);
	// echo "<pre>";
	// echo $ListPages -> query;
	// echo "</pre>";
	
 /** set of limit string **/
	
	$ListPages -> setLimit();
	$ListPages -> result();
	SetNoCache();

?>

<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first">&nbsp;<a href="javascript:void(0);">#</a></th>	
		<th nowrap class="custom-grid th-middle" width="4%">&nbsp;No.</th>	
		<th nowrap class="custom-grid th-middle" width="7%">&nbsp;Campaign Id.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Customer Number.</th>        
        <th nowrap class="custom-grid th-middle">&nbsp;Customer Name.</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Customer City.</th>
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
				<td class="content-first"><input type="checkbox" value="<?php echo $row -> CustomerId; ?>" name="chk_cust_data" name="chk_cust_data" disabled></td>
				<td class="content-middle"><?php echo $no; ?></td>	
				<td class="content-middle"><?php echo $row -> CampaignNumber; ?></td>
				<td class="content-middle"><?php echo $row -> CustomerNumber; ?></td>
				<td class="content-middle"><?php echo $row -> CustomerFirstName; ?></td>
				<td class="content-middle"><?php echo $row -> CustomerCity; ?></td>
				<td class="content-middle"><?php echo $row -> id.'-'.$row -> full_name; ?></td>
				<td class="content-middle"><?php echo ($row -> CustomerUpdatedTs?$row -> CustomerUpdatedTs:'0000-00-00 00:00:00'); ?></td>
				<td class="content-lasted"><?php echo ($row -> CallReasonDesc?$row -> CallReasonDesc:'NEW'); ?></td>
				
			</tr>	
			
			
</tbody>
	<?php
		$no++;
		};
	?>
</table>



