<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	
	
/* get last history ******/
	
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
	

 /** set properties pages records **/
	
	$ListPages -> pages = $db -> escPost('v_page'); 
	$ListPages -> setPage(10);
	
	
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
				a.CustomerUploadedTs, 
				a.CustomerOfficeName, 
				c.CampaignNumber,
				c.CampaignName  
			FROM t_gn_customer a INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId 
				LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId 
				LEFT JOIN t_lk_callreason f on a.CallReasonId = f.CallReasonId ";
	
	$ListPages -> query($sql);
		
 /** create set filter SQL if found **/	

	$filter =  " AND b.AssignAdmin is not null 
				 AND b.AssignMgr is not null 
				 AND b.AssignSpv is not null  
				 AND ( f.CallReasonId NOT IN(".getClsoingStatus().") OR f.CallReasonId is null)
				 AND b.AssignBlock=0 
				 AND c.CampaignStatusFlag=1";
				 
	
	if( $db->getSession('handling_type')==3 )			 
		$filter.=" AND b.AssignSpv ='".$db -> getSession('UserId')."' ";
		
	if( $db->getSession('handling_type')==4)
		$filter.=" AND b.AssignSelerId = '".$db->getSession('UserId')."'";
				 	
	if( $db->havepost('cust_name') ) 
		$filter.=" AND a.CustomerFirstName LIKE '%".$db->escPost('cust_name')."%'"; 
	
	if( $db->havepost('cust_number') ) 
		$filter.=" AND a.CustomerNumber LIKE '%".$db->escPost('cust_number')."%'"; 
		
	if( $db->havepost('call_result') )
		$filter.=" AND a.CallReasonId =".$db->escPost('call_result');	
	
	if( $db->havepost('campaign_id') )
		$filter.=" AND a.CampaignId =".$db->escPost('campaign_id');	
		
	
	if( $db -> havepost('call_status'))
		$filter.=" AND a.CallReasonId LIKE '%".$db->escPost('call_status')."%'"; 
	
	if( $db -> havepost('cust_fine_code'))
		$filter.=" AND a.NumberCIF LIKE '%".$db->escPost('cust_fine_code')."%'"; 
		
    
	
	
 /** create set Limit record **/	
 
	$ListPages -> setWhere($filter);
	$ListPages -> OrderBy($db-> escPost('order_by'),$db -> escPost('type'));
	$ListPages -> setLimit();
	//$ListPages -> echo_query();
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
        <th nowrap class="custom-grid th-middle" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('f.CallReasonCode');"><b style='color:#608ba9;'>Last Call Status</b></span></th>
		<th nowrap class="custom-grid th-lasted" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CustomerUpdatedTs');"><b style='color:#608ba9;'>Last Call Date</b></span></th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
	?>
			<tr class="onselect">
				<td class="content-first" width='5%'><input type="checkbox" value="<?php echo $row -> CustomerId."_".$row -> CampaignId."_".$row -> CallReasonId; ?>" name="chk_cust_call" name="chk_cust_call" <?php echo ($db->getSession('handling_type')!=4?'disabled':'');?> ></td>
				<td class="content-middle" width='5%'><?php  echo $no; ?></td>
				<td class="content-middle" width='20%' nowrap style="color:green;font-weight:bold;"><?php echo $row -> CampaignName; ?></td>
				<td class="content-middle" width='40%' nowrap style="color:green;font-weight:bold;"><?php echo $row -> CustomerFirstName; ?></td>
				<td class="content-middle" width='15%' style="color:green;"><?php echo $row -> CallReasonCode; ?></td>
				<td class="content-lasted" width='15%'><?php echo $row -> CustomerUpdatedTs; ?></td>
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>


