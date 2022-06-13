<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	
	
	
	function getLastHistory($customerId=''){
		global $db;
		$sql = " select a.CallHistoryNotes, a.CallHistoryCreatedTs from t_gn_callhistory a
			where a.CustomerId =".$customerId."
			order by a.CallHistoryCreatedTs DESC LIMIT 1 ";
		$notes = $db -> valueSQl($sql);
		if( $notes!='') return $notes;
		else 
			return '-';
	}
	
	$status = array(16,17);
	
 /** set properties pages records **/
 
	$ListPages -> pages = $db -> escPost('v_page'); 
	$ListPages -> IFpage('campaign_id');
	
	
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
				a.CustomerRejectedDate,
				IF( d.CallReasonCode is null ,'NEW',d.CallReasonDesc) as CallReasonCode,
				IF( a.CustomerUpdatedTs is null, '-',DATE_FORMAT(a.CustomerUpdatedTs,'%d-%m-%Y %H:%i') ) as CustomerUpdatedTs,
				IF(a.CustomerCity is null,'-',a.CustomerCity) as CustomerCity, 
				a.CustomerUploadedTs, 
				a.CustomerOfficeName, 
				c.CampaignNumber, 
				concat( c.CampaignNumber) as Campaign,
				concat( e.id,' - ',e.full_name) as agentName
			FROM t_gn_customer a INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId 
				LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId 
				LEFT join t_lk_callreason d on a.CallReasonId =d.CallReasonId
				left join tms_agent e on a.SellerId=e.UserId ";
	
	$ListPages -> query($sql);
		
 /** create set filter SQL if found **/	

	$filter =  " AND b.AssignAdmin is not null 
				 AND b.AssignMgr is not null 
				 AND b.AssignSpv is not null  
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
		
	
	if( $db->havepost('campaign_id') )
		$filter.=" AND a.CampaignId =".$db->escPost('campaign_id');	
		
     if( $db->havepost('call_result')){ 
		$filter.=" AND a.CallReasonId ='".$db->escPost('call_result')."'"; 
		$filter.="   AND ( a.CallReasonId IN(39,40)) ";		
	 }
	 else{
		$filter.="   AND ( a.CallReasonId IN(39,40)) ";
	 }
	
	$ListPages -> setWhere($filter);
	
 /** create set Limit record **/	
	$ListPages -> OrderBy("a.CustomerRejectedDate","DESC");
	$ListPages -> setLimit();
	
	
	//echo $ListPage->query;
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
		<th nowrap class="custom-grid th-middle">&nbsp;No</th>			
		<th nowrap class="custom-grid th-middle">&nbsp;Cust Number</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Cust Name</th>        
        <th nowrap class="custom-grid th-middle">&nbsp;Home Phone</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Mobile Phone</th>
		<!--<th nowrap class="custom-grid th-middle">&nbsp;Office Phone</th>-->
        <th nowrap class="custom-grid th-middle">&nbsp;Last Call Status</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Agent Name</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Note</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Days</th>
		<th nowrap class="custom-grid th-lasted">&nbsp;Last Call Date</th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result)){
	?>
			<tr class="onselect">
				<td class="content-first">
				<input type="checkbox" value="<?php echo $row -> CustomerId."_".$row -> CampaignId."_".$row -> CallReasonId; ?>" name="chk_cust_call" name="chk_cust_call"></td>
				<td class="content-middle"><?php  echo $no; ?></td>
				<td class="content-middle"><?php echo $row -> CustomerNumber; ?></td>
				<td class="content-middle" nowrap><?php echo $row -> CustomerFirstName; ?></td>
				<td class="content-middle"><?php echo $app -> setMaskText( $row -> CustomerHomePhoneNum ); ?></td>
				<td class="content-middle"><?php echo $app -> setMaskText( $row -> CustomerMobilePhoneNum ); ?></td>
				<!--<td class="content-middle"><?php echo $app -> setMaskText( $row -> CustomerWorkPhoneNum ); ?></td>-->
				<td class="content-middle"><?php echo $row -> CallReasonCode; ?></td>
				<td class="content-middle"><?php echo $row -> agentName; ?></td>
				<td class="content-middle"><div class="wraptext"><?php echo getLastHistory( $row -> CustomerId );?></div></td>
				<td class="content-middle"><?php echo $row->CustomerRejectedDate;?></td>
				<td class="content-lasted"><?php echo $row -> CustomerUpdatedTs; ?></td>
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>


