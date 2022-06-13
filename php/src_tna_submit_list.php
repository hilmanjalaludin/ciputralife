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
 
	$sql = 
		"
		SELECT DISTINCT 
			tgf.FuCustId, 
			tgf.*,
			tps.AproveName,
			tgc.CustomerUpdatedTs
		FROM t_gn_followup tgf
	    INNER JOIN t_gn_customer tgc    ON tgf.FuCustId = tgc.CustomerId
	    LEFT JOIN t_lk_aprove_status tps ON tgf.FuQAStatus = tps.ApproveId
		";
	$ListPages -> query($sql);
		
 /** create set filter SQL if found **/	
	if( $db->havepost('cust_name') ) 
		$filter.=" AND tgf.FuName LIKE '%".$db->escPost('cust_name')."%'"; 
    
 /** create set Limit record **/	
	$filter .= "AND tgf.FuType = '2'";   
	$ListPages -> setWhere($filter);
	$ListPages -> OrderBy($db-> escPost('order_by'),$db -> escPost('type'));
	$ListPages -> GroupBy('tgc.CustomerId');
	$ListPages -> setLimit();
	$ListPages -> result();
	//$ListPages -> echo_query();
	// SetNoCache();//
	
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
		<th nowrap class="custom-grid th-middle" align='left'>&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('tgf.FuName');"><b style='color:#608ba9;'>Cust Name</b></span></th>   
        <th nowrap class="custom-grid th-middle">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('tps.AproveName');"><b style='color:#608ba9;'>Verify Status</b></span></th>
		<th nowrap class="custom-grid th-lasted">&nbsp;<span class="header_order" onclick="extendsJQuery.orderBy('tgc.CustomerUpdatedTs');"><b style='color:#608ba9;'>Last Call Date</b></span></th>
	</tr>
</thead>	
<tbody>
	<?php
		$no = (($ListPages -> start) + 1);
		while($row = $db ->fetchrow($ListPages->result))
		{
			$call_content = $row -> CustomerId."_".$row -> CampaignId."_".$row -> CallReasonId."_".$row -> ApproveId;
			$QualityId = $db -> Entity -> getEskalasiStatus(USER_QUALITY);	
	?>
		<tr class="onselect">
			<td class="content-first">
				<?php $jpForm -> jpCheck('chk_cust_call',NULL,$call_content,NULL,false,(in_array($row -> ApproveId,array_keys($QualityId))?0:1));?> </td>
			<td class="content-middle"><?php  echo $no; ?></td>
			<td class="content-middle" nowrap><?php echo $row -> FuName; ?></td>
			<td class="content-middle" style='color:green;text-align:center;'><?php echo ($row -> AproveName?$row -> AproveName:'New Closing'); ?></td>
			<td class="content-lasted" style='color:green;text-align:center;'><?php echo $row -> CustomerUpdatedTs; ?></td>
		</tr>	
</tbody>
	<?php
		$no++;
		};
	?>
</table>


