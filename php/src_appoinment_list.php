<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	
	
	function getLastHistory($customerId='')
	{
		global $db;
		$sql = " select a.CallHistoryNotes, a.CallHistoryCreatedTs from t_gn_callhistory a
			where a.CustomerId =".$customerId."
			order by a.CallHistoryCreatedTs DESC LIMIT 1 ";
		$notes = $db -> valueSQl($sql);
		if( $notes!='') return $notes;
		else 
			return '-';
	}
	
	
 /** set properties pages records **/
 
	$ListPages -> pages = $db -> escPost('v_page'); 
	$ListPages -> setPage(10);

 /** set  genral query SQL  **/
 
	$sql = "SELECT 
				distinct(a.CustomerId) as CustomerId, 
				a.CampaignId, 
				a.CustomerNumber, 
				a.CustomerFirstName, 
				a.CustomerLastName, 
				a.CustomerHomePhoneNum, 
				a.CustomerMobilePhoneNum,
				a.CustomerWorkPhoneNum,
				date_format(e.ApoinmentDate,'%d/%m/%Y %H:%i') as ApoinmentDate,
				IF( d.CallReasonCode is null ,'NEW',d.CallReasonDesc) as CallReasonCode,
				IF( a.CustomerUpdatedTs is null, '-',a.CustomerUpdatedTs) as CustomerUpdatedTs,
				IF(a.CustomerCity is null,'-',a.CustomerCity) as CustomerCity, 
				a.CustomerUploadedTs, 
				a.CustomerOfficeName, 
				c.CampaignNumber 
			FROM t_gn_customer a INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId 
				LEFT JOIN t_gn_campaign c on a.CampaignId=c.CampaignId 
				LEFT JOIN t_lk_callreason d on a.CallReasonId =d.CallReasonId 
				LEFT JOIN t_gn_appoinment e on a.CustomerId=e.CustomerId ";
	
	$ListPages -> query($sql);
		
 /** create set filter SQL if found **/	

	$filter =  " AND b.AssignAdmin is not null 
				 AND b.AssignMgr is not null 
				 AND b.AssignSpv is not null 
				 AND a.CallReasonId IN('".$db -> Entity -> CallBackWithIn()."')
				  AND e.ApoinmentDate > now()";
				 
	
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
		
    $ListPages -> setWhere($filter);
	
 /** create set Limit record **/	

	$ListPages -> setLimit();
	$ListPages -> result();
	// $ListPages -> echo_query();
	
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
		<th nowrap class="custom-grid th-middle">&nbsp;Last Call Status</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Call Try Again</th>
		<th nowrap class="custom-grid th-middle">&nbsp;Note</th>
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
				<input type="checkbox" value="<?php echo $row -> CustomerId."_".$row -> CampaignId; ?>" name="chk_cust_appoinment" id="chk_cust_appoinment"  ></td>
				<td class="content-middle"><?php  echo $no; ?></td>
				<td class="content-middle"><?php echo $row -> CustomerNumber; ?></td>
				<td class="content-middle"><?php echo $row -> CustomerFirstName; ?></td>
				<td class="content-middle"><?php echo $row -> CallReasonCode; ?></td>
				<td class="content-middle" style="color:red;font-size:12px;"><?php echo $row -> ApoinmentDate; ?></td>
				<td class="content-middle"><div class="wraptext"><?php echo getLastHistory( $row -> CustomerId );?></div></td>
				<td class="content-lasted"><?php echo $row -> CustomerUpdatedTs; ?></td>
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>


