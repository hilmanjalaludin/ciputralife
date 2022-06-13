<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	
	
/* get last history ******/
	function getWAEmailStatus()
	{
		global $db;
		$sql = "select a.id from t_lk_wa_email a where a.FuShow =1 ";
		$qry = $db -> query($sql);
		$datas=array();
		if( $qry -> result_num_rows() > 0 )
		{
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[] = $rows['id']; 
			}
		}
		return implode(',',$datas);
	}	
	
		function getWAEmailStatusExclude()
	{
		global $db;
		$sql = "select a.id from t_lk_wa_email a where a.FuShow =0 ";
		$qry = $db -> query($sql);
		$datas=array();
		if( $qry -> result_num_rows() > 0 )
		{
			foreach( $qry -> result_assoc() as $rows )
			{
				$datas[] = $rows['id']; 
			}
		}
		return implode(',',$datas);
	}
	
	function getLastHistory($customerId='')
	{
		global $db;
		$sql = " select a.CallHistoryNotes, a.CallHistoryCreatedTs from t_gn_callhistory a where a.CustomerId =".$customerId."
				 order by a.CallHistoryCreatedTs DESC LIMIT 1 ";
				 	 
		$notes = $db -> valueSQl($sql);
		if( $notes!='') return $notes;
		else 
			return '-';
	}
	
	
/** get status closing ****/
	
	function getClsoingStatus()
	{
		global $db;
		$sql = "select a.CallReasonId from t_lk_callreason a where a.CallReasonEvent =1 ";
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

 /** set properties pages records **/
	
	$ListPages -> pages = $db -> escPost('v_page'); 
	$ListPages -> setPage(15);
	
	
	
 /** set  genral query SQL  **/
 
	$sql = "SELECT 
				a.CustomerId, 
				a.CallReasonId,
				a.CampaignId, 
				a.CustomerNumber, 
				a.CustomerFirstName, 
				a.CustomerLastName, 
				a.CustomerHomePhoneNum, 
				a.CustomerMobilePhoneNum,
				a.CustomerWorkPhoneNum,
				IF( f.CallReasonCode is null ,'NEW',f.CallReasonDesc) as CallReasonCode,
				IF( a.CustomerUpdatedTs is null, '-',DATE_FORMAT(a.CustomerUpdatedTs,'%d-%m-%Y %H:%i') ) as CustomerUpdatedTs,
				IF(a.CustomerCity is null,'-',a.CustomerCity) as CustomerCity,
				DATE_FORMAT(a.CustomerDOB,'%Y-%m-%d') as CustomerDOB,
				IF(a.GenderId=1,'MALE',IF(a.GenderId=2,'FEMALE','-')) as Gender,
				a.CustomerUploadedTs, 
				a.CustomerOfficeName, 
				d.CampaignNumber,
				d.CampaignName,
				a.CustomerId, a.CustomerFirstName, a.GenderId, a.CustomerDOB, c.id as agent, g.id as spv, h.id as am,
				i.Desc
			FROM t_gn_customer a INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId 
				LEFT JOIN t_gn_campaign d on a.CampaignId=d.CampaignId 
				LEFT JOIN t_lk_callreason f on a.CallReasonId = f.CallReasonId 
				LEFT JOIN tms_agent c ON b.AssignSelerId = c.UserId
				LEFT JOIN tms_agent g ON b.AssignSpv = g.UserId
				LEFT JOIN tms_agent h ON b.AssignMgr = h.UserId
				LEFT JOIN t_lk_wa_email i ON a.wa_email_status = i.id";
	
	$ListPages -> query($sql);
		
 /** create set filter SQL if found **/	

	$filter =  " AND b.AssignAdmin is not null 
				 AND b.AssignMgr is not null 
				 AND b.AssignSpv is not null  
				 AND b.AssignBlock=0 
				 AND d.CampaignStatusFlag=1
				 AND a.wa_email_status  NOT IN (". getWAEmailStatusExclude(). ")
				 AND (f.CallReasonId NOT IN(".getShowFollowup().") OR f.CallReasonId is null)
				 AND a.IsForm in (0,1)  AND a.IsForm !=2";
				 // AND a.CallAgainAttempt<6";
				 // AND (f.CallReasonId NOT IN (20,21) OR f.CallReasonId is null)"
				 // AND (f.CallReasonId NOT IN(".getClsoingStatus().") OR f.CallReasonId is null)
	
	if( $db->getSession('handling_type')==3 )			 
		$filter.=" AND b.AssignSpv ='".$db -> getSession('UserId')."' ";
		
	if( $db->getSession('handling_type')==4)
		$filter.=" AND b.AssignSelerId = '".$db->getSession('UserId')."'";
				 	
	if( $db->havepost('user_id') ) 
	{
		if($db->escPost('user_id') == 'new')
		{
			$filter.=" AND b.AssignSelerId is null"; 
		}
		else{
			$filter.=" AND c.UserId = '".$db->escPost('user_id')."'"; 
		}
	}
	
	if( $db->havepost('cust_name') ) 
		$filter.=" AND a.CustomerFirstName LIKE '%".$db->escPost('cust_name')."%'"; 
	
	if( $db->havepost('cust_number') ) 
		$filter.=" AND a.CustomerNumber LIKE '%".$db->escPost('cust_number')."%'"; 
		
	if( $db->havepost('call_result') )
		$filter.=" AND a.CallReasonId =".$db->escPost('call_result');	
	
	if( $db->havepost('campaign_id') )
		$filter.=" AND a.CampaignId =".$db->escPost('campaign_id');	
	
	if( $db->havepost('gender')) 
		$filter.=" AND a.GenderId = '".$db->escPost('gender')."'"; 
	
	if( $db -> havepost('cust_dob'))
		$filter.=" AND a.CustomerDOB LIKE '%".$db->escPost('cust_dob')."%'"; 
	
	if( $db->havepost('city')) 
		$filter.=" AND a.CustomerCity LIKE '%".$db->escPost('city')."%'"; 
	
	if( $db -> havepost('call_status'))
		$filter.=" AND a.CallReasonId LIKE '%".$db->escPost('call_status')."%'"; 
	
	if( $db -> havepost('cust_fine_code'))
		$filter.=" AND a.NumberCIF LIKE '%".$db->escPost('cust_fine_code')."%'"; 
		
    $filter.=" ORDER BY a.CustomerUpdatedTs DESC";
	
	
 /** create set Limit record **/	
 
	$ListPages -> setWhere($filter);
	$ListPages -> OrderBy($db-> escPost('order_by'),$db -> escPost('type'));
	$ListPages -> setLimit();
	// $ListPages -> echo_query();
	$ListPages -> result();
	
	SetNoCache();
	
?>			
<style>
	.wraptext{color:green;font-size:11px;padding:3px;width:200px;}
	.wraptext:hover{color:blue;}
</style>
<table width="100%" class="custom-grid" cellspacing="0">
<thead>
	<tr height="20"> 
		<th nowrap class="custom-grid th-first">&nbsp;#</th>	
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<b style='color:#608ba9;'>No</b></th>			
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('c.CampaignName');"><b style='color:#608ba9;'>Campaign</b></span></th>
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CustomerFirstName');"><b style='color:#608ba9;'>Cust Name</b></span></th>
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CustomerCity');"><b style='color:#608ba9;'>Cust City</b></span></th>
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CustomerDOB');"><b style='color:#608ba9;'>Cust DOB</b></span></th>
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.GenderId');"><b style='color:#608ba9;'>Gender</b></span></th>
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('c.id');"><b style='color:#608ba9;'>Agent</b></span></th>
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('g.id');"><b style='color:#608ba9;'>SPV</b></span></th>
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('h.id');"><b style='color:#608ba9;'>AM</b></span></th>
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('f.CallReasonCode');"><b style='color:#608ba9;'>Last Call Status</b></span></th>
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('i.Desc');"><b style='color:#608ba9;'>QA Status</b></span></th>
		<th nowrap class="custom-grid th-lasted" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CustomerUpdatedTs');"><b style='color:#608ba9;'>Last Call Date</b></span></th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
	?>
			<tr class="onselect">
				<!--<td class="content-first" width='5%'><input type="checkbox" value="<?php echo $row -> CustomerId."_".$row -> CampaignId."_".$row -> CallReasonId; ?>" name="chk_cust_call" name="chk_cust_call" <?php echo ($db->getSession('handling_type')!=4?'disabled':'');?> ></td>-->
				<td class="content-first" width='20'><input type="checkbox" value="<?php echo $row -> CustomerId."_".$row -> CampaignId."_".$row -> CallReasonId; ?>" name="chk_cust_call" name="chk_cust_call" <?php echo ($db->getSession('handling_type')!=4?'disabled':'');?> ></td>
				<td class="content-middle" width='10'><?php  echo $no; ?></td>
				<td class="content-middle" width='70' nowrap style="color:green;font-weight:bold;"><?php echo $row -> CampaignName; ?></td>
				<td class="content-middle" width='150' nowrap style="color:green;font-weight:bold;"><?php echo $row -> CustomerFirstName; ?></td>
				<td class="content-middle" width='15' nowrap style="color:green;font-weight:bold;"><?php echo $row -> CustomerCity; ?></td>
				<td class="content-middle" width='10' nowrap style="color:green;font-weight:bold;"><?php echo $row -> CustomerDOB; ?></td>
				<td class="content-middle" width='10' nowrap style="color:green;font-weight:bold;"><?php echo $row -> Gender; ?></td>
				<td class="content-middle" width='30' nowrap style="color:green;font-weight:bold;"><?php echo ($row -> agent?$row -> agent:'-'); ?></td>
				<td class="content-middle" width='30' nowrap style="color:green;font-weight:bold;"><?php echo ($row -> spv?$row -> spv:'-'); ?></td>
				<td class="content-middle" width='30' nowrap style="color:green;font-weight:bold;"><?php echo ($row -> am?$row -> am:'-'); ?></td>
				<td class="content-middle" width='15' style="color:green;"><?php echo $row -> CallReasonCode; ?></td>
				<td class="content-middle" width='15' style="color:green;"><?php echo $row -> Desc; ?></td>
				<td class="content-lasted" width='30'><?php echo $row -> CustomerUpdatedTs; ?></td>
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>


