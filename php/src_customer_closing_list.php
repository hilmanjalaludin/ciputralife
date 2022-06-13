<?php
	require("../sisipan/sessions.php");
	require("../fungsi/global.php");
	require("../class/MYSQLConnect.php");
	require("../class/class.list.table.php");
	require("../class/class.application.php");
	require("../sisipan/parameters.php");
	require("../class/lib.form.php");
	
	
/** get last notes ***/
	
	function getLastHistory($customerId='')
	{
		global $db;
		$sql = " select a.CallHistoryNotes, a.CallHistoryCreatedTs from t_gn_callhistory a
				 where a.CustomerId =".$customerId." order by a.CallHistoryCreatedTs DESC LIMIT 1 ";
				 
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
			a.CustomerId,  a.CallReasonId, a.CampaignId, 
			date_format(a.CustomerUpdatedTs,'%d/%m/%Y') as CustomerUpdatedTs,
			date_format(a.CustomerRejectedDate,'%d/%m/%Y') as CustomerRejectedDate,
			a.CustomerId, a.CustomerFirstName, e.Gender, a.CustomerDOB, c.CardTypeDesc, g.*
		FROM t_gn_customer a
		INNER JOIN t_gn_assignment b on a.CustomerId=b.CustomerId 
		LEFT JOIN t_lk_gender e ON a.GenderId=e.GenderId
		LEFT JOIN t_lk_cardtype c ON a.CardTypeId=c.CardTypeId
		LEFT JOIN t_gn_campaign d on a.CampaignId=d.CampaignId 
		LEFT JOIN t_lk_callreason f on a.CallReasonId = f.CallReasonId
		left join t_lk_aprove_status g on a.CallReasonQue=g.ApproveId ";
	
	$ListPages -> query($sql);
		
 /** create set filter SQL if found **/	

	$filter =  " AND b.AssignAdmin IS NOT NULL 
				 AND b.AssignMgr IS NOT NULL 
				 AND b.AssignSpv IS NOT NULL
				 AND f.CallReasonId IN('".$db -> Entity -> SaleWithIn()."') 
				 AND b.AssignBlock=0 
				 and d.CampaignStatusFlag=1";
				 
	
	if( $db->getSession('handling_type')==3 )			 
		$filter.=" AND b.AssignSpv ='".$db -> getSession('UserId')."' 
				   AND a.CallReasonQue = 12";
		
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
    
	
 /** create set Limit record **/	
	
	$ListPages -> setWhere($filter);
	$ListPages -> OrderBy($db-> escPost('order_by'),$db -> escPost('type'));
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
		<th nowrap class="custom-grid th-first">&nbsp;<b style='color:#608ba9;'>#</b></th>	
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<b style='color:#608ba9;'>No</b></th>			
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CustomerFirstName');"><b style='color:#608ba9;'>Cust Name</b></span></th>   
        <th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('g.AproveName');"><b style='color:#608ba9;'>Verify Status</b></span></th>
		<th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CustomerRejectedDate');"><b style='color:#608ba9;'>Verify Date</b></span></th>
		<th nowrap class="custom-grid th-lasted">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('a.CustomerUpdatedTs');"><b style='color:#608ba9;'>Last Call Date</b></span></th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result))
		{
			$call_content = $row -> CustomerId."_".$row -> CampaignId."_".$row -> CallReasonId."_".$row -> ApproveId;
			// $QualityId = $db -> Entity -> getEskalasiStatus(USER_QUALITY,USER_TELESALES);	
			$QualityId = $db -> Entity -> getEskalasiStatus(USER_QUALITY);	
	?>
			<tr class="onselect">
				<?php //<td class="content-first">$jpForm -> jpCheck('chk_cust_call',NULL,$call_content,NULL,false,(in_array($row -> ApproveId,array_keys($QualityId))?0:1) ); </td>?>
				<td class="content-first"><?php $jpForm -> jpCheck('chk_cust_call',NULL,$call_content,NULL,false,(in_array($row -> ApproveId,array_keys($QualityId))?0:1));?> </td>
				<td class="content-middle"><?php  echo $no; ?></td>
				<td class="content-middle" nowrap><?php echo $row -> CustomerFirstName; ?></td>
				<td class="content-middle" style='color:green;text-align:center;'><?php echo ($row -> AproveName?$row -> AproveName:'New Closing'); ?></td>
				<td class="content-middle" style='color:green;text-align:center;'><?php echo ($row -> CustomerRejectedDate?$row -> CustomerRejectedDate:'-'); ?></td>
				<td class="content-lasted" style='color:green;text-align:center;'><?php echo $row -> CustomerUpdatedTs; ?></td>
			</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>


